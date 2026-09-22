<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Red de seguridad del contrato público de rutas.
 *
 * Compara las rutas actuales contra .ai/baseline/routes.json y falla si alguna ruta con
 * nombre desapareció, cambió de URI o perdió su exigencia de permiso de módulo.
 *
 * Cuando un cambio de ruta sea intencional y esté autorizado:
 *   1) documéntalo en docs/RUTAS-DEPRECADAS.md
 *   2) regenera el baseline: php scripts/baseline.php
 */
class ContratoDeRutasTest extends TestCase
{
    /** @return array<int, array<string, mixed>> */
    private function baseline(): array
    {
        $ruta = base_path('.ai/baseline/routes.json');

        if (! is_file($ruta)) {
            $this->markTestSkipped('Sin baseline de rutas. Ejecuta: php scripts/baseline.php');
        }

        $contenido = (string) file_get_contents($ruta);
        $datos = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $contenido), true);

        if (! is_array($datos)) {
            $this->markTestSkipped('El baseline no es JSON válido. Regenéralo con php scripts/baseline.php');
        }

        return $datos;
    }

    /** @return array<string, array{uri: string, permisos: array<int, string>}> */
    private function rutasActuales(): array
    {
        $actuales = [];

        foreach (Route::getRoutes() as $ruta) {
            $nombre = $ruta->getName();
            if ($nombre === null || $nombre === '') {
                continue;
            }

            $permisos = $this->clavesDePermiso($ruta->gatherMiddleware());

            $actuales[$nombre] = ['uri' => $ruta->uri(), 'permisos' => $permisos];
        }

        return $actuales;
    }

    /**
     * Claves module_permission de una lista de middleware.
     *
     * Las rutas declaran el alias (module_permission:mod,accion) pero
     * `route:list --json` guarda en el baseline la clase ya resuelta
     * (RequireModulePermission:mod,accion). Hay que aceptar ambas: comparando solo el alias,
     * el baseline siempre sale vacio y esta prueba pasa sin comprobar nada.
     *
     * @param  array<int, mixed>  $middleware
     * @return array<int, string>
     */
    private function clavesDePermiso(array $middleware): array
    {
        $out = [];
        foreach ($middleware as $m) {
            if (! is_string($m)) {
                continue;
            }
            if (str_starts_with($m, 'module_permission:')) {
                $out[] = substr($m, strlen('module_permission:'));
            } elseif (preg_match('/RequireModulePermission:(.+)$/', $m, $mm)) {
                $out[] = $mm[1];
            }
        }
        sort($out);

        return array_values(array_unique($out));
    }

    /** @return array<int, string> */
    private function permisosBaseline(array $r): array
    {
        $mw = $r['middleware'] ?? [];

        return $this->clavesDePermiso(is_array($mw) ? $mw : [$mw]);
    }

    public function test_ninguna_ruta_con_nombre_desaparecio(): void
    {
        $actuales = $this->rutasActuales();
        $faltantes = [];

        foreach ($this->baseline() as $r) {
            $nombre = $r['name'] ?? null;
            $uri = (string) ($r['uri'] ?? '');
            if (! $nombre || str_starts_with(ltrim($uri, '/'), '_')) {
                continue;
            }
            if (! isset($actuales[$nombre])) {
                $faltantes[] = sprintf('%s [%s %s]', $nombre, $r['method'] ?? '?', $r['uri'] ?? '?');
            }
        }

        $this->assertSame([], $faltantes, "Rutas del contrato que desaparecieron:\n- ".implode("\n- ", $faltantes));
    }

    public function test_las_rutas_conservan_su_uri(): void
    {
        $actuales = $this->rutasActuales();
        $cambios = [];

        foreach ($this->baseline() as $r) {
            $nombre = $r['name'] ?? null;
            if (! $nombre || ! isset($actuales[$nombre])) {
                continue;
            }
            $antes = ltrim((string) ($r['uri'] ?? ''), '/');
            $ahora = ltrim($actuales[$nombre]['uri'], '/');
            if ($antes !== $ahora) {
                $cambios[] = "$nombre: '$antes' -> '$ahora'";
            }
        }

        $this->assertSame([], $cambios, "Rutas que cambiaron de URI:\n- ".implode("\n- ", $cambios));
    }

    public function test_ninguna_ruta_perdio_su_permiso_de_modulo(): void
    {
        $actuales = $this->rutasActuales();
        $abiertas = [];

        foreach ($this->baseline() as $r) {
            $nombre = $r['name'] ?? null;
            if (! $nombre || ! isset($actuales[$nombre])) {
                continue;
            }
            $perdidos = array_diff($this->permisosBaseline($r), $actuales[$nombre]['permisos']);
            if ($perdidos) {
                $abiertas[] = "$nombre ya no exige [".implode(', ', $perdidos).']';
            }
        }

        $this->assertSame([], $abiertas, "Rutas que quedaron más abiertas que en el baseline:\n- ".implode("\n- ", $abiertas));
    }

    public function test_toda_ruta_de_la_aplicacion_tiene_nombre(): void
    {
        $sinNombre = [];

        foreach (Route::getRoutes() as $ruta) {
            $uri = $ruta->uri();

            if (str_starts_with($uri, '_') || str_starts_with($uri, 'sanctum')
                || str_starts_with($uri, 'telescope') || str_starts_with($uri, 'horizon')
                || str_starts_with($uri, 'livewire') || $uri === 'up') {
                continue;
            }

            if (! $ruta->getName()) {
                $sinNombre[] = implode('|', $ruta->methods()).' '.$uri;
            }
        }

        $this->assertSame([], $sinNombre, "Rutas sin ->name() (obligatorio en este proyecto):\n- ".implode("\n- ", $sinNombre));
    }
}
