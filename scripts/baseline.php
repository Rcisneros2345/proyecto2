<?php

/**
 * baseline.php — Congela el "estado bueno conocido" del proyecto.
 *
 *   php scripts/baseline.php
 *
 * Genera .ai/baseline/ con el contrato público contra el que se compara después.
 *
 * Por qué existe en PHP y no como redirección de shell: en PowerShell,
 * `php artisan route:list --json > archivo` escribe UTF-16 con BOM y el JSON queda
 * ilegible para json_decode(). Además, si artisan falla, el archivo guarda el volcado
 * del error y la comparación posterior no compara nada. Aquí se valida antes de guardar.
 */
$raiz = dirname(__DIR__);
chdir($raiz);

$dir = '.ai/baseline';
if (! is_dir($dir) && ! @mkdir($dir, 0777, true) && ! is_dir($dir)) {
    fwrite(STDERR, "ERROR: no se pudo crear $dir\n");
    exit(2);
}

echo '== Baseline — '.date('Y-m-d H:i:s')." ==\n";

/* ------------------------------------------------------------------ 1) Rutas */
$salida = [];
$codigo = 0;
exec('php -d xdebug.mode=off artisan route:list --json 2>&1', $salida, $codigo);
$json = implode("\n", $salida);

// Quita un posible BOM al inicio y cualquier salida previa a '['
$json = preg_replace('/^\xEF\xBB\xBF/', '', $json);
$pos = strpos($json, '[');
if ($pos !== false) {
    $json = substr($json, $pos);
}

$rutas = json_decode($json, true);

if ($codigo !== 0 || ! is_array($rutas)) {
    file_put_contents("$dir/routes.error.txt", $json);
    fwrite(STDERR, "\nERROR: 'php artisan route:list --json' no devolvió JSON válido.\n");
    fwrite(STDERR, "Salida guardada en $dir/routes.error.txt. Primeras líneas:\n\n");
    fwrite(STDERR, implode("\n", array_slice($salida, 0, 12))."\n\n");
    fwrite(STDERR, "Esto es un BLOQUEO: si la aplicación no puede listar sus rutas,\n");
    fwrite(STDERR, "ningún agente puede verificar que no las rompe. Arréglalo primero.\n");
    exit(1);
}

$conNombre = 0;
$sinNombre = [];
foreach ($rutas as $r) {
    if (! empty($r['name'])) {
        $conNombre++;
    } else {
        $sinNombre[] = ($r['method'] ?? '?').' '.($r['uri'] ?? '?');
    }
}

file_put_contents("$dir/routes.json", json_encode($rutas, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
@unlink("$dir/routes.error.txt");
printf("  [ok] rutas: %d (%d con nombre, %d sin nombre) -> %s/routes.json\n",
    count($rutas), $conNombre, count($sinNombre), $dir);

if ($sinNombre) {
    file_put_contents("$dir/routes-sin-nombre.txt", implode("\n", $sinNombre)."\n");
    printf("  [!]  %d rutas sin ->name(): ver %s/routes-sin-nombre.txt\n", count($sinNombre), $dir);
}

/**
 * Claves module_permission de una lista de middleware.
 *
 * `route:list --json` devuelve la clase resuelta
 * (App\Http\Middleware\RequireModulePermission:academia,view), no el alias declarado
 * (module_permission:academia,view). Si solo buscas el alias, cuentas cero permisos y la
 * verificacion pasa en falso.
 */
function clavesDePermiso(mixed $middleware): array
{
    $mw = is_array($middleware) ? $middleware : [$middleware];
    $out = [];
    foreach ($mw as $m) {
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

/* ------------------------------------------------- 2) Permisos usados en rutas */
$permisos = [];
foreach ($rutas as $r) {
    foreach (clavesDePermiso($r['middleware'] ?? []) as $clave) {
        $permisos[$clave] = true;
    }
}
ksort($permisos);
file_put_contents("$dir/permisos.txt", implode("\n", array_keys($permisos))."\n");
printf("  [ok] claves de permiso en rutas: %d -> %s/permisos.txt\n", count($permisos), $dir);

/* ------------------------------------------------------ 3) Vistas y componentes */
function listar(string $dirBase, string $patron): array
{
    if (! is_dir($dirBase)) {
        return [];
    }
    $out = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirBase, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile() && preg_match($patron, $f->getFilename())) {
            $out[] = str_replace('\\', '/', $f->getPathname());
        }
    }
    sort($out);

    return $out;
}

$vistas = listar('resources/views', '/\.blade\.php$/');
file_put_contents("$dir/views.txt", implode("\n", $vistas)."\n");
printf("  [ok] vistas Blade: %d -> %s/views.txt\n", count($vistas), $dir);

$componentes = array_values(array_filter($vistas, fn ($v) => str_contains($v, 'resources/views/components/')));
file_put_contents("$dir/componentes.txt", implode("\n", $componentes)."\n");
printf("  [ok] componentes: %d -> %s/componentes.txt\n", count($componentes), $dir);

/* ------------------------------------------------------------------ 4) Assets */
$assets = listar('public', '/\.(js|css|png|jpg|jpeg|svg|ico|woff2?)$/');
$assets = array_values(array_filter($assets, fn ($a) => ! str_contains($a, 'public/build/')));
file_put_contents("$dir/assets.txt", implode("\n", $assets)."\n");
printf("  [ok] assets públicos: %d -> %s/assets.txt\n", count($assets), $dir);

/* --------------------------------------------------------------------- 5) Git */
$commit = trim((string) shell_exec('git rev-parse --short HEAD 2>&1'));
$rama = trim((string) shell_exec('git rev-parse --abbrev-ref HEAD 2>&1'));
file_put_contents("$dir/git.txt", "commit: $commit\nrama: $rama\nfecha: ".date('c')."\n");
echo "  [ok] punto git: $commit ($rama)\n";

echo "\nBaseline listo. Antes de tocar nada:\n";
echo "  git add . && git commit -m \"checkpoint antes de <MODULO>\"\n";
