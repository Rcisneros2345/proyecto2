<?php

/**
 * impacto.php — ¿Qué más queda afectado por lo que acabo de cambiar?
 *
 *   php scripts/impacto.php                      # cambios contra HEAD (working tree)
 *   php scripts/impacto.php --since=HEAD~3       # cambios desde otro punto
 *   php scripts/impacto.php resources/views/employees/index.blade.php  # archivos concretos
 *
 * Toma los archivos modificados, deduce qué símbolos exportan (vista, componente,
 * controller, modelo, servicio, job, permiso, asset) y busca en TODO el proyecto quién
 * los consume. Esa lista es la que hay que revisar y arreglar antes de cerrar el cambio.
 *
 * Salida 0 siempre (es informativo). Con --strict devuelve 1 si hay consumidores.
 */
$raiz = dirname(__DIR__);
chdir($raiz);

$args = array_slice($argv, 1);
$strict = in_array('--strict', $args, true);
$since = null;
$manual = [];

foreach ($args as $a) {
    if (str_starts_with($a, '--since=')) {
        $since = substr($a, 8);
    } elseif ($a !== '--strict') {
        $manual[] = str_replace('\\', '/', $a);
    }
}

/* ------------------------------------------------- 1) Archivos cambiados */
$cambiados = [];

if ($manual) {
    $cambiados = $manual;
} else {
    $cmd = $since ? "git diff --name-only $since 2>&1" : 'git diff --name-only HEAD 2>&1';
    $out = [];
    exec($cmd, $out);
    foreach ($out as $l) {
        $l = trim($l);
        if ($l !== '' && ! str_contains($l, 'fatal:')) {
            $cambiados[] = $l;
        }
    }
    // Archivos nuevos sin rastrear
    $out2 = [];
    exec('git ls-files --others --exclude-standard 2>&1', $out2);
    foreach ($out2 as $l) {
        $l = trim($l);
        if ($l !== '' && preg_match('/\.(php|blade\.php|css|js)$/', $l)) {
            $cambiados[] = $l;
        }
    }
}

$cambiados = array_values(array_unique(array_filter($cambiados, fn ($f) => is_file($f))));

if (! $cambiados) {
    echo "No hay archivos modificados que analizar.\n";
    echo "Usa --since=<rev> o pasa rutas de archivo explícitas.\n";
    exit(0);
}

echo "== Análisis de impacto ==\n";
echo 'Archivos cambiados: '.count($cambiados)."\n";
foreach ($cambiados as $c) {
    echo "  · $c\n";
}
echo "\n";

/* --------------------------------- 2) Qué símbolos expone cada archivo */
/** @var array<string, array{tipo:string, origen:string, agujas:array<int,string>, nota:string}> */
$objetivos = [];

function agregarObjetivo(array &$objetivos, string $clave, string $tipo, string $origen, array $agujas, string $nota = ''): void
{
    $objetivos[$clave] = ['tipo' => $tipo, 'origen' => $origen, 'agujas' => $agujas, 'nota' => $nota];
}

foreach ($cambiados as $archivo) {
    $f = str_replace('\\', '/', $archivo);

    // --- Vistas y componentes Blade
    if (str_ends_with($f, '.blade.php') && str_starts_with($f, 'resources/views/')) {
        $rel = substr($f, strlen('resources/views/'), -strlen('.blade.php'));
        $punteado = str_replace('/', '.', $rel);

        if (str_starts_with($rel, 'components/')) {
            $comp = substr($rel, strlen('components/'));
            $compPunteado = str_replace('/', '.', $comp);
            $compGuion = str_replace('/', '-', $comp);
            agregarObjetivo($objetivos, "componente <x-$compPunteado>", 'componente', $f,
                ["<x-$compPunteado", "<x-$compGuion", "x-$compPunteado"],
                'Revisa atributos y slots: un consumidor que pasa otra variable se rompe en silencio.');
        } else {
            $agujas = ["'$punteado'", "\"$punteado\"", "'$rel'"];
            $base = basename($rel);
            if (str_starts_with($base, '_')) {
                // partial: muchas veces se incluye por nombre corto dentro de su carpeta
                $agujas[] = $base;
            }
            agregarObjetivo($objetivos, "vista $punteado", 'vista', $f, $agujas,
                'Busca @include/@extends/view()/Route::view y las variables que espera.');
        }

        continue;
    }

    // --- Clases PHP de la aplicación
    if (str_ends_with($f, '.php') && str_starts_with($f, 'app/')) {
        $clase = basename($f, '.php');
        $tipo = 'clase';
        $nota = '';
        if (str_contains($f, 'app/Http/Controllers/')) {
            $tipo = 'controller';
            $nota = 'Verifica rutas que lo apuntan, redirecciones ->route() y pruebas Feature.';
        } elseif (str_contains($f, 'app/Models/')) {
            $tipo = 'modelo';
            $nota = 'Verifica $fillable/$casts, vistas que formatean sus campos, factories y seeders.';
        } elseif (str_contains($f, 'app/Services/')) {
            $tipo = 'servicio';
            $nota = 'Verifica TODOS los llamadores: controllers, jobs, comandos y otros servicios.';
        } elseif (str_contains($f, 'app/Jobs/')) {
            $tipo = 'job';
            $nota = 'Verifica quién lo despacha y qué vista muestra su progreso o resultado.';
        } elseif (str_contains($f, 'app/Http/Middleware/')) {
            $tipo = 'middleware';
            $nota = 'Verifica Kernel.php, los grupos de rutas que lo usan y la matriz RBAC.';
        } elseif (str_contains($f, 'app/Http/Requests/')) {
            $tipo = 'form request';
            $nota = 'Verifica el controller que lo usa y las vistas del formulario.';
        } elseif (str_contains($f, 'app/Policies/')) {
            $tipo = 'policy';
            $nota = 'Verifica AuthServiceProvider, controllers con authorize() y pruebas de permisos.';
        } elseif (str_contains($f, 'app/Events/') || str_contains($f, 'app/Listeners/')) {
            $tipo = 'evento';
            $nota = 'Verifica listeners, broadcasting y el JS/Blade que escucha el evento.';
        }
        agregarObjetivo($objetivos, "$tipo $clase", $tipo, $f, [$clase], $nota);

        // Métodos públicos: útiles para servicios y controllers
        $cont = (string) file_get_contents($f);
        if (preg_match_all('/public function ([a-zA-Z0-9_]+)\s*\(/', $cont, $m)) {
            foreach (array_unique($m[1]) as $metodo) {
                if (in_array($metodo, ['__construct', '__invoke', 'boot', 'up', 'down', 'handle', 'rules', 'authorize'], true)) {
                    continue;
                }
                agregarObjetivo($objetivos, "$clase::$metodo()", 'método', $f, ["$metodo("],
                    'Coincidencias por nombre de método: descarta homónimos manualmente.');
            }
        }

        continue;
    }

    // --- Migraciones: tabla afectada
    if (str_starts_with($f, 'database/migrations/')) {
        $cont = (string) file_get_contents($f);
        if (preg_match_all('/Schema::(?:create|table)\(\s*[\'"]([a-z0-9_]+)[\'"]/', $cont, $m)) {
            foreach (array_unique($m[1]) as $tabla) {
                agregarObjetivo($objetivos, "tabla $tabla", 'tabla', $f, ["'$tabla'", "\"$tabla\""],
                    'Verifica modelo, $fillable, seeders, factories, queries y vistas que muestran sus columnas.');
            }
        }
        // Columnas eliminadas o renombradas: lo más peligroso
        if (preg_match_all('/dropColumn\(\s*\[?\s*[\'"]([a-z0-9_]+)[\'"]/', $cont, $m2)) {
            foreach (array_unique($m2[1]) as $col) {
                agregarObjetivo($objetivos, "columna eliminada $col", 'columna', $f, ["'$col'", "->$col", "\$$col"],
                    'ALTO RIESGO: cualquier uso restante rompe en ejecución, no en compilación.');
            }
        }

        continue;
    }

    // --- Rutas
    if (str_starts_with($f, 'routes/')) {
        agregarObjetivo($objetivos, 'archivo de rutas '.basename($f), 'rutas', $f, [],
            'Corre php scripts/comparar_rutas.php: el contrato público puede haber cambiado.');

        continue;
    }

    // --- CSS / JS
    if (preg_match('/\.(css|js)$/', $f)) {
        agregarObjetivo($objetivos, 'asset '.basename($f), 'asset', $f, [basename($f)],
            'Verifica @vite, @push(\'scripts\'), asset() y las vistas que lo cargan.');

        continue;
    }

    // --- Seeders y config
    if (str_starts_with($f, 'database/seeders/') || str_starts_with($f, 'config/')) {
        agregarObjetivo($objetivos, basename($f), 'configuración', $f, [basename($f, '.php')],
            'Verifica quién lee esta configuración o depende de estos datos sembrados.');
    }
}

if (! $objetivos) {
    echo "No se dedujo ningún símbolo público de los archivos cambiados.\n";
    exit(0);
}

/* ------------------------------------------- 3) Barrido del proyecto */
function archivosProyecto(): array
{
    $dirs = ['app', 'routes', 'resources', 'tests', 'config', 'database/seeders', 'database/factories'];
    $out = [];
    foreach ($dirs as $d) {
        if (! is_dir($d)) {
            continue;
        }
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if ($f->isFile() && preg_match('/\.(php|js|css)$/', $f->getFilename())) {
                $out[] = str_replace('\\', '/', $f->getPathname());
            }
        }
    }
    sort($out);

    return $out;
}

$todos = archivosProyecto();
$consumidores = [];   // clave => [ "archivo:linea" => texto ]

foreach ($todos as $archivo) {
    $contenido = (string) file_get_contents($archivo);
    $lineas = null;

    foreach ($objetivos as $clave => $obj) {
        if ($archivo === $obj['origen']) {
            continue;   // no se cuenta a sí mismo
        }
        foreach ($obj['agujas'] as $aguja) {
            if ($aguja === '' || ! str_contains($contenido, $aguja)) {
                continue;
            }
            $lineas ??= explode("\n", $contenido);
            foreach ($lineas as $i => $linea) {
                if (str_contains($linea, $aguja)) {
                    $consumidores[$clave][$archivo.':'.($i + 1)] = trim($linea);
                    if (count($consumidores[$clave] ?? []) >= 25) {
                        break 2;
                    }
                }
            }
        }
    }
}

/* -------------------------------------------------------- 4) Reporte */
$totalConsumidores = 0;
$sinConsumidores = [];

foreach ($objetivos as $clave => $obj) {
    $hits = $consumidores[$clave] ?? [];
    if (! $hits) {
        $sinConsumidores[] = $clave;

        continue;
    }
    $totalConsumidores += count($hits);

    echo "── $clave\n";
    echo "   origen: {$obj['origen']}\n";
    if ($obj['nota']) {
        echo "   ojo:    {$obj['nota']}\n";
    }
    echo '   consumidores ('.count($hits)."):\n";
    foreach ($hits as $donde => $texto) {
        $texto = mb_substr(preg_replace('/\s+/', ' ', $texto), 0, 110);
        echo "     · $donde   $texto\n";
    }
    echo "\n";
}

if ($sinConsumidores) {
    echo "── Sin consumidores detectados\n";
    foreach ($sinConsumidores as $s) {
        echo "     · $s\n";
    }
    echo "   (puede ser correcto, o puede significar referencia dinámica: view(\$x), \"x-\".\$tipo)\n\n";
}

echo "═══════════════════════════════════════════\n";
printf("Objetivos analizados: %d   ·   Referencias encontradas: %d\n", count($objetivos), $totalConsumidores);
echo "\nQué hacer ahora:\n";
echo "  1. Abre CADA consumidor de la lista y comprueba que sigue recibiendo lo que espera.\n";
echo "  2. Arregla los que tu cambio rompió (mínimo, sin ampliar alcance).\n";
echo "  3. php scripts/verificar_referencias.php  y  php scripts/comparar_rutas.php\n";
echo "  4. php artisan test --compact  (y añade prueba para el consumidor que se rompió)\n";
echo "  5. Si salen más de 10 consumidores afectados, PARA y replantea el cambio.\n";
echo "\nLas referencias dinámicas no se detectan aquí: busca a mano view(\$var),\n";
echo "@include(\$vista), nombres de clase o de componente construidos por concatenación.\n";

exit($strict && $totalConsumidores > 0 ? 1 : 0);
