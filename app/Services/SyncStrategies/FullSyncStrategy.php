<?php

declare(strict_types=1);

namespace App\Services\SyncStrategies;

use App\Models\FirebirdSync;
use App\Services\FirebirdReader;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class FullSyncStrategy implements SyncStrategyInterface
{
    public function execute(
        FirebirdReader $firebirdReader,
        FirebirdSync $sync,
        ?string $ciclo,
        bool $deleteOrphans,
        array $tables,
        bool $skipExisting,
        callable $progressCallback
    ): array {
        $log = [];
        $errors = [];
        $totals = ['created' => 0, 'updated' => 0, 'deleted' => 0, 'processed' => 0, 'total' => 0];

        // Si no hay ciclo, intentar obtener el último de horarios_det
        if (! $ciclo) {
            $ciclo = $this->getLatestCiclo(DB::connection()->getPdo());
            $log[] = ['tipo' => 'info', 'msg' => "Ciclo detectado automáticamente: {$ciclo}"];
        }

        // Los catálogos son prerequisitos de las FKs que usa el sync de ciclo.
        $catalogSync = new CatalogSmartSync;
        $result = $catalogSync->execute($firebirdReader, $sync, null, $deleteOrphans, [], $skipExisting, $progressCallback);
        $log = array_merge($log, $result['log']);
        $errors = array_merge($errors, $result['errors']);
        $totals['created'] += $result['created'];
        $totals['updated'] += $result['updated'];
        $totals['deleted'] += $result['deleted'];
        $totals['processed'] += $result['processed'];

        if ($ciclo) {
            $cycleSync = new CycleDirectSync;
            $result = $cycleSync->execute($firebirdReader, $sync, $ciclo, $deleteOrphans, [], $skipExisting, $progressCallback);
            $log = array_merge($log, $result['log']);
            $errors = array_merge($errors, $result['errors']);
            $totals['created'] += $result['created'];
            $totals['updated'] += $result['updated'];
            $totals['deleted'] += $result['deleted'];
            $totals['processed'] += $result['processed'];
        } else {
            $log[] = ['tipo' => 'skip', 'msg' => 'No hay ciclo disponible, saltando sync_ciclo'];
        }

        // Post-sync: poblar horarios_det.origen_horario desde profesores.origen_horario
        // Clave del clasificador PTC/PA: ORIGEN_HORARIO = 'HD' → PTC, resto → PA
        $log[] = ['tipo' => 'info', 'msg' => '--- Post-sync: propagando origen_horario a horarios_det ---'];
        $postResult = $this->propagateOrigenHorario($mysql, $ciclo);
        $log = array_merge($log, $postResult['log']);
        $errors = array_merge($errors, $postResult['errors']);

        return [
            'created' => $totals['created'],
            'updated' => $totals['updated'],
            'deleted' => $totals['deleted'],
            'processed' => $totals['processed'],
            'total' => $totals['total'],
            'log' => $log,
            'errors' => $errors,
        ];
    }

    protected function getLatestCiclo(PDO $mysql): ?string
    {
        try {
            $stmt = $mysql->query('SELECT INICIAL, FINAL, PERIODO FROM horarios_det ORDER BY INICIAL DESC, FINAL DESC, PERIODO DESC LIMIT 1');
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return "{$row['INICIAL']}-{$row['FINAL']}-{$row['PERIODO']}";
            }
        } catch (Throwable) {
            // ignore
        }

        return null;
    }

    /**
     * Propaga profesores.origen_horario → horarios_det.origen_horario
     * Implementa el clasificador PTC/PA: HD = PTC, resto = PA
     */
    protected function propagateOrigenHorario(PDO $mysql, ?string $ciclo = null): array
    {
        $log = [];
        $errors = [];

        try {
            $where = $ciclo ? $this->cicloToWhere($ciclo) : '';
            $params = $ciclo ? $this->parseCiclo($ciclo) : [];

            $sql = '
                UPDATE `horarios_det` hd
                JOIN `profesores` p ON hd.clave_profesor = p.clave_profesor
                SET hd.origen_horario = p.origen_horario
                WHERE hd.origen_horario IS NULL OR hd.origen_horario <> p.origen_horario
            ';
            if ($where) {
                $sql .= " AND {$where}";
            }

            $stmt = $mysql->prepare($sql);
            $stmt->execute($params);
            $affected = $stmt->rowCount();

            $log[] = ['tipo' => 'ok', 'msg' => "horarios_det.origen_horario actualizado: {$affected} filas".($ciclo ? " para ciclo {$ciclo}" : '')];

            // Log distribución PTC/PA
            $distSql = '
                SELECT hd.origen_horario, COUNT(*) as cnt
                FROM horarios_det hd
            ';
            if ($where) {
                $distSql .= " WHERE {$where}";
            }
            $distSql .= ' GROUP BY hd.origen_horario';
            $stmt = $mysql->prepare($distSql);
            $stmt->execute($params);
            $dist = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dist as $row) {
                $origen = $row['origen_horario'] ?? null;
                $origenDisplay = $origen ?? 'NULL';
                $label = $origenDisplay === 'HD' ? 'PTC' : 'PA';
                $log[] = ['tipo' => 'info', 'msg' => "  {$label} ({$origenDisplay}): {$row['cnt']} clases"];
            }

        } catch (Throwable $e) {
            $errors[] = 'propagateOrigenHorario: '.$e->getMessage();
            $log[] = ['tipo' => 'error', 'msg' => 'propagateOrigenHorario: ERROR - '.$e->getMessage()];
        }

        return compact('log', 'errors');
    }

    protected function cicloToWhere(string $ciclo): string
    {
        [$I, $F, $P] = $this->parseCiclo($ciclo);

        return 'hd.INICIAL = ? AND hd.FINAL = ? AND hd.PERIODO = ?';
    }

    protected function parseCiclo(string $ciclo): array
    {
        $parts = explode('-', $ciclo);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException("Ciclo inválido: {$ciclo}. Formato: INICIAL-FINAL-PERIODO");
        }

        return [(int) $parts[0], (int) $parts[1], (int) $parts[2]];
    }
}
