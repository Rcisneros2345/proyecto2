<?php

/**
 * verificar.php — Puerta de calidad. Ningún módulo se cierra con esto en rojo.
 *
 *   php scripts/verificar.php
 *   php scripts/verificar.php --rapido    (sin la suite completa de pruebas)
 */
$raiz = dirname(__DIR__);
chdir($raiz);

$rapido = in_array('--rapido', array_slice($argv, 1), true);
$fallas = 0;

function paso(string $t): void
{
    echo "\n──────── $t\n";
}
function ok(string $t): void
{
    echo "  [ok] $t\n";
}
function mal(string $t): void
{
    global $fallas;
    $fallas++;
    echo "  [FALLA] $t\n";
}
function correr(string $cmd): array
{
    $out = [];
    $code = 0;
    exec($cmd.' 2>&1', $out, $code);

    return [$code, $out];
}

echo "═══ Verificación de regresión ═══\n";

paso('1/7 Rutas resolubles');
[$c, $o] = correr('php artisan route:list --json');
$json = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', implode("\n", $o)), true);
if ($c === 0 && is_array($json)) {
    ok(count($json).' rutas listadas');
} else {
    mal("'php artisan route:list' falla — la app no puede resolver todas sus rutas");
    echo '       '.implode("\n       ", array_slice($o, 0, 8))."\n";
}

paso('2/7 Contrato de rutas');
if (is_file('.ai/baseline/routes.json')) {
    [$c, $o] = correr('php scripts/comparar_rutas.php');
    echo '  '.implode("\n  ", $o)."\n";
    $c === 0 ? ok('contrato intacto') : mal('regresión en el contrato de rutas');
} else {
    mal('no hay baseline — ejecuta php scripts/baseline.php');
}

paso('3/7 Referencias cruzadas');
[$c, $o] = correr('php scripts/verificar_referencias.php');
echo '  '.implode("\n  ", $o)."\n";
$c === 0 ? ok('sin referencias rotas') : mal('hay referencias rotas');

paso('4/7 Impacto de los cambios');
[$c, $o] = correr('php scripts/impacto.php');
$resumen = array_values(array_filter($o, fn ($l) => str_contains($l, 'Referencias encontradas')
    || str_contains($l, 'Archivos cambiados')));
if ($resumen) {
    foreach ($resumen as $r) {
        echo "  $r\n";
    }
    ok('revisa la salida completa con: php scripts/impacto.php');
} else {
    ok('sin cambios pendientes que analizar');
}

paso('5/7 Estilo (Pint)');
if (is_file('vendor/bin/pint')) {
    [$c, $o] = correr('vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'pint --test');
    $c === 0 ? ok('estilo correcto') : mal('archivos sin formatear — corre vendor/bin/pint --dirty');
} else {
    echo "  [--] Pint no instalado\n";
}

paso('6/7 Depuración y basura en el diff');
[, $diff] = correr('git diff -U0 HEAD');
$sucio = [];
foreach ($diff as $l) {
    if (str_starts_with($l, '+') && ! str_starts_with($l, '+++')
        && preg_match('/\bdd\(|\bdump\(|var_dump\(|print_r\(|\bray\(|console\.log\(|debugger/', $l)) {
        $sucio[] = trim($l);
    }
}
if ($sucio) {
    mal('hay depuración en los cambios:');
    foreach (array_slice($sucio, 0, 10) as $s) {
        echo "       $s\n";
    }
} else {
    ok('sin dd/dump/console.log en el diff');
}

[, $st] = correr('git status --porcelain');
$basura = array_values(array_filter($st, fn ($l) => str_starts_with($l, '??')
    && preg_match('/\.(bak|old|orig|rej|tmp|swp)$|_backup|_old|_copy|_v2|_nuevo|-copia|routes_output\.txt/', $l)));
if ($basura) {
    mal('archivos basura sin rastrear: '.count($basura).' (php scripts/limpiar.php)');
    foreach (array_slice($basura, 0, 8) as $b) {
        echo "       $b\n";
    }
} else {
    ok('sin archivos basura evidentes');
}

paso('7/7 Pruebas');
if ($rapido) {
    echo "  [--] omitidas por --rapido\n";
} else {
    [$c, $o] = correr('php artisan test --compact');
    $linea = '';
    foreach ($o as $l) {
        if (str_contains($l, 'Tests:')) {
            $linea = trim($l);
        }
    }
    if ($c === 0) {
        ok($linea ?: 'suite en verde');
    } else {
        mal('pruebas en rojo');
        echo '       '.implode("\n       ", array_slice($o, -20))."\n";
    }
}

echo "\n═══════════════════════════════════════════\n";
if ($fallas === 0) {
    echo "VERIFICACIÓN EN VERDE. Puedes continuar.\n";
    exit(0);
}
echo "VERIFICACIÓN EN ROJO: $fallas problema(s). No se cierra el módulo ni se hace commit.\n";
exit(1);
