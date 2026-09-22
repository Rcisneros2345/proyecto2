<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Academia\AttendanceCaptureAssignment;
use App\Models\Academia\Grupo;
use App\Models\Academia\HorarioDet;
use App\Models\Academia\Ciclo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AttendanceCaptureAuthorization
{
    public function assignmentsFor(User $user): Collection
    {
        return AttendanceCaptureAssignment::query()
            ->where('user_id', $user->id)
            ->where('active', true)
            ->orderBy('nivel')
            ->orderBy('id_campus')
            ->get();
    }

    public function canCaptureSchedule(User $user, HorarioDet $horario): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $grupo = Grupo::query()
            ->where('codigo_grupo', $horario->codigo_grupo)
            ->where('inicial', $horario->inicial)
            ->where('final', $horario->final)
            ->where('periodo', $horario->periodo)
            ->first();

        if (! $grupo) {
            return false;
        }

        return $this->assignmentsForCycle($user, $horario->inicial, $horario->final, $horario->periodo)
            ->contains(fn (AttendanceCaptureAssignment $assignment): bool => ($assignment->nivel === null || $assignment->nivel === $grupo->nivel)
                && ($assignment->id_campus === null || (string) $assignment->id_campus === (string) $horario->id_campus)
            );
    }

    public function canCaptureFilter(User $user, string $nivel, ?string $sede, int $inicial, int $final, int $periodo): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->assignmentsForCycle($user, $inicial, $final, $periodo)
            ->contains(fn (AttendanceCaptureAssignment $assignment): bool => ($assignment->nivel === null || $assignment->nivel === $nivel)
                && ($sede === null || $assignment->id_campus === null || (string) $assignment->id_campus === (string) $sede)
            );
    }

    public function canUseCycle(User $user, Ciclo $ciclo): bool
    {
        return $user->isAdmin() || $this->assignmentsForCycle($user, $ciclo->inicial, $ciclo->final, $ciclo->periodo)->isNotEmpty();
    }

    /** @return Collection<int, string> */
    public function allowedLevels(User $user, Ciclo $ciclo): Collection
    {
        if ($user->isAdmin()) {
            return collect();
        }

        return $this->assignmentsForCycle($user, $ciclo->inicial, $ciclo->final, $ciclo->periodo)
            ->pluck('nivel')->filter()->unique()->values();
    }

    /**
     * Restrict a catalog query to the user's active level/campus assignments.
     * A null level or campus remains a wildcard for that dimension.
     */
    public function restrictLevelCampusQuery(Builder $query, User $user, Ciclo $ciclo, string $levelColumn = 'nivel', string $campusColumn = 'id_campus'): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $assignments = $this->assignmentsForCycle($user, $ciclo->inicial, $ciclo->final, $ciclo->periodo);
        if ($assignments->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $scope) use ($assignments, $levelColumn, $campusColumn): void {
            foreach ($assignments as $assignment) {
                $scope->orWhere(function (Builder $match) use ($assignment, $levelColumn, $campusColumn): void {
                    if ($assignment->nivel !== null) {
                        $match->where($levelColumn, $assignment->nivel);
                    }
                    if ($assignment->id_campus !== null) {
                        $match->where($campusColumn, $assignment->id_campus);
                    }
                });
            }
        });
    }

    public function restrictLevelQuery(Builder $query, User $user, Ciclo $ciclo, string $levelColumn = 'nivel'): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $assignments = $this->assignmentsForCycle($user, $ciclo->inicial, $ciclo->final, $ciclo->periodo);
        if ($assignments->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        $levels = $assignments->pluck('nivel')->filter()->unique();
        if ($levels->isEmpty()) {
            return $query;
        }

        return $query->whereIn($levelColumn, $levels->all());
    }

    public function filterGrid(User $user, array $grid, int $inicial, int $final, int $periodo): array
    {
        if ($user->isAdmin()) {
            return $grid;
        }

        return array_values(array_filter($grid, fn (array $row): bool => $this->canCaptureRow($user, $row, $inicial, $final, $periodo)
        ));
    }

    /** @return Collection<int, AttendanceCaptureAssignment> */
    private function assignmentsForCycle(User $user, int $inicial, int $final, int $periodo): Collection
    {
        return $this->assignmentsFor($user)->filter(fn (AttendanceCaptureAssignment $assignment): bool => ($assignment->inicial === null || $assignment->inicial === $inicial)
            && ($assignment->final === null || $assignment->final === $final)
            && ($assignment->periodo === null || $assignment->periodo === $periodo)
        );
    }

    private function canCaptureRow(User $user, array $row, int $inicial, int $final, int $periodo): bool
    {
        return $this->assignmentsForCycle($user, $inicial, $final, $periodo)
            ->contains(fn (AttendanceCaptureAssignment $assignment): bool => ($assignment->nivel === null || $assignment->nivel === ($row['NIVEL'] ?? null))
                && ($assignment->id_campus === null || (string) $assignment->id_campus === (string) ($row['ID_CAMPUS'] ?? null))
            );
    }
}
