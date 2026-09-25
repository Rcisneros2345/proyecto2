<?php

/**
 * comparar_rutas.php — Detecta regresiones del contrato público.
 *
 *   php scripts/comparar_rutas.php
 *
 * Compara .ai/baseline/routes.json contra el estado actual y reporta rutas
 * desaparecidas, renombradas, con URI/método/parámetro distinto, y claves de permiso
 * (module_permission:x,y) que dejaron de aplicarse.
 *
 * Salida 0 = sin regresiones · 1 = regresiones · 2 = no se pudo comparar.
 */
$raiz = dirname(__DIR__);
chdir($raiz);

$rutaBaseline = $argv[1] ?? '.ai/baseline/routes.json';

if (! is_file($rutaBaseline)) {
    fwrite(STDERR, "ERROR: no existe el baseline '$rutaBaseline'.\n");
    fwrite(STDERR, "Ejecuta primero: php scripts/baseline.php\n");
    exit(2);
}

function leerJson(string $texto): mixed
{
    // Tolera BOM y UTF-16 (PowerShell escribe así al redirigir con >).
    if (str_starts_with($texto, "\xFF\xFE") || str_starts_with($texto, "\xFE\xFF")) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-16');
    }
    $texto = preg_replace('/^\xEF\xBB\xBF/', '', $texto);

    return json_decode($texto, true);
}

$antes = leerJson((string) file_get_contents($rutaBaseline));
if (! is_array($antes)) {
    fwrite(STDERR, "ERROR: el baseline no es JSON válido (¿se generó con una redirección de PowerShell?).\n");
    fwrite(STDERR, "Regenéralo con: php scripts/baseline.php\n");
    exit(2);
}

$salida = [];
$codigo = 0;
exec('php -d xdebug.mode=off artisan route:list --json 2>&1', $salida, $codigo);
$rawOutput = implode("\n", $salida);
$pos = strpos($rawOutput, '[');
if ($pos !== false) {
    $rawOutput = substr($rawOutput, $pos);
}
$ahora = leerJson($rawOutput);

if ($codigo !== 0 || ! is_array($ahora)) {
    fwrite(STDERR, "ERROR: 'php artisan route:list --json' falló. Primeras líneas:\n\n");
    fwrite(STDERR, implode("\n", array_slice($salida, 0, 12))."\n\n");
    fwrite(STDERR, "BLOQUEO: si la app no puede listar sus rutas, no se puede verificar nada.\n");
    exit(2);
}

function parametros(string $uri): array
{
    preg_match_all('/\{([a-zA-Z0-9_]+)\??\}/', $uri, $m);

    return $m[1] ?? [];
}

/**
 * Claves module_permission de una ruta.
 *
 * `route:list --json` devuelve la clase resuelta (RequireModulePermission:mod,accion),
 * no el alias declarado (module_permission:mod,accion). Se aceptan las dos formas: si solo
 * buscas el alias, esta comprobacion encuentra cero permisos y pasa en falso.
 */
function permisos(array $r): array
{
    $mw = $r['middleware'] ?? [];
    $mw = is_array($mw) ? $mw : [$mw];
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

function indexar(array $rutas): array
{
    $porNombre = [];
    $sinNombre = [];
    foreach ($rutas as $r) {
        $firma = [
            'uri' => $r['uri'] ?? '',
            'method' => $r['method'] ?? '',
            'action' => $r['action'] ?? '',
            'params' => parametros($r['uri'] ?? ''),
            'permisos' => permisos($r),
        ];
        if (! empty($r['name'])) {
            $porNombre[$r['name']] = $firma;
        } else {
            $sinNombre[($r['method'] ?? '').' '.($r['uri'] ?? '')] = $firma;
        }
    }

    return [$porNombre, $sinNombre];
}

[$aN, $aS] = indexar($antes);
[$bN, $bS] = indexar($ahora);

$bloqueos = [];
$avisos = [];
$nuevas = [];

foreach ($aN as $nombre => $a) {
    if (! isset($bN[$nombre])) {
        $pista = '';
        foreach ($bN as $n2 => $b2) {
            if ($b2['uri'] === $a['uri'] && $b2['method'] === $a['method']) {
                $pista = " (¿renombrada a '$n2'?)";
                break;
            }
        }
        $bloqueos[] = "RUTA DESAPARECIDA: '$nombre' [{$a['method']} {$a['uri']}]$pista";

        continue;
    }

    $b = $bN[$nombre];

    if ($a['uri'] !== $b['uri']) {
        $bloqueos[] = "URI CAMBIADA en '$nombre': '{$a['uri']}' -> '{$b['uri']}'";
    }
    if ($a['method'] !== $b['method']) {
        $bloqueos[] = "MÉTODO CAMBIADO en '$nombre': '{$a['method']}' -> '{$b['method']}'";
    }
    if ($a['params'] !== $b['params']) {
        $bloqueos[] = "PARÁMETROS CAMBIADOS en '$nombre': [".implode(', ', $a['params'])
            .'] -> ['.implode(', ', $b['params']).']';
    }
    $perdidos = array_diff($a['permisos'], $b['permisos']);
    $ganados = array_diff($b['permisos'], $a['permisos']);
    if ($perdidos) {
        $bloqueos[] = "PERMISO RETIRADO en '$nombre': ya no exige [".implode(', ', $perdidos)
            .'] — la ruta quedó más abierta que antes';
    }
    if ($ganados) {
        $avisos[] = "Permiso añadido en '$nombre': ahora exige [".implode(', ', $ganados)
            .'] — ¿quién accedía antes y ahora no?';
    }
    if ($a['action'] !== $b['action']) {
        $avisos[] = "Acción distinta en '$nombre': '{$a['action']}' -> '{$b['action']}' (ok si es refactor intencional)";
    }
}

foreach ($aS as $clave => $a) {
    if (! isset($bS[$clave])) {
        $existe = false;
        foreach ($bN as $b2) {
            if ($b2['uri'] === $a['uri'] && $b2['method'] === $a['method']) {
                $existe = true;
                break;
            }
        }
        if (! $existe) {
            $avisos[] = "Ruta sin nombre desaparecida: $clave";
        }
    }
}

foreach ($bN as $nombre => $b) {
    if (! isset($aN[$nombre])) {
        $nuevas[] = "$nombre [{$b['method']} {$b['uri']}]";
    }
}

foreach ($bS as $clave => $b) {
    if (! isset($aS[$clave])) {
        $avisos[] = "Ruta NUEVA SIN ->name(): $clave (toda ruta debe tener nombre)";
    }
}

echo "== Comparación de rutas ==\n";
printf("Baseline: %d rutas   |   Actual: %d rutas\n\n", count($antes), count($ahora));

if ($bloqueos) {
    echo 'REGRESIONES DEL CONTRATO ('.count($bloqueos).") — BLOQUEAN EL COMMIT\n";
    foreach ($bloqueos as $b) {
        echo "  [X] $b\n";
    }
    echo "\n";
}

if ($avisos) {
    echo 'Advertencias ('.count($avisos).")\n";
    foreach ($avisos as $a) {
        echo "  [!] $a\n";
    }
    echo "\n";
}

if ($nuevas) {
    echo 'Rutas nuevas ('.count($nuevas).")\n";
    foreach ($nuevas as $n) {
        echo "  [+] $n\n";
    }
    echo "\n";
}

if (! $bloqueos) {
    echo "Contrato de rutas INTACTO.\n";
    exit(0);
}

echo "Corrige las regresiones antes de continuar.\n";
echo "Si el cambio es intencional y está autorizado:\n";
echo "  1) documenta en docs/RUTAS-DEPRECADAS.md (vieja -> nueva, con redirección)\n";
echo "  2) regenera el baseline: php scripts/baseline.php\n";
exit(1);
