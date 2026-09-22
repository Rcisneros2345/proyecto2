<?php

/**
 * limpiar.php — Limpieza SEGURA de lo que deja la IA.
 *
 *   php scripts/limpiar.php            modo seco: solo muestra qué haría
 *   php scripts/limpiar.php --apply    mueve a .ui-work/_cuarentena/<AAAA-MM-DD>/
 *
 * Nunca borra. Nunca toca archivos rastreados por git. Nunca usa rm.
 */
$raiz = dirname(__DIR__);
chdir($raiz);

$aplicar = in_array('--apply', array_slice($argv, 1), true);
$cuarentena = '.ui-work/_cuarentena/'.date('Y-m-d');

$patrones = [
    '/\.(bak|old|orig|rej|tmp|swp)$/i',
    '/~$/',
    '/_backup\./i', '/_old\./i', '/_copy\./i', '/_v2\.php$/i', '/_nuevo\./i', '/-copia\./i',
    '/^(test|prueba|temp|borrar|asdf)\.php$/i',
    '/^untitled/i',
    '/^(debug\.log|output\.txt|routes_output\.txt|dump\.sql)$/i',
];

$excluir = ['/vendor/', '/node_modules/', '/.git/', '/storage/', '/public/build/', '/.ui-work/_cuarentena/'];

echo $aplicar
    ? "═══ LIMPIEZA — se moverá a $cuarentena ═══\n\n"
    : "═══ LIMPIEZA — MODO SECO (no se mueve nada) ═══\nPara aplicar: php scripts/limpiar.php --apply\n\n";

$rastreados = [];
$out = [];
exec('git ls-files 2>&1', $out);
foreach ($out as $l) {
    $rastreados[str_replace('\\', '/', trim($l))] = true;
}

$candidatos = [];
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('.', FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($it as $f) {
    if (! $f->isFile()) {
        continue;
    }
    $ruta = str_replace('\\', '/', ltrim($f->getPathname(), './'));
    foreach ($excluir as $ex) {
        if (str_contains("/$ruta", $ex)) {
            continue 2;
        }
    }
    foreach ($patrones as $p) {
        if (preg_match($p, $f->getFilename())) {
            $candidatos[] = $ruta;
            break;
        }
    }
}

echo "── Archivos temporales y copias\n";
$movidos = 0;
$protegidos = 0;
foreach ($candidatos as $ruta) {
    if (isset($rastreados[$ruta])) {
        $protegidos++;
        echo "  [RASTREADO — NO SE TOCA] $ruta   <- decide tú a mano\n";

        continue;
    }
    if ($aplicar) {
        $destino = $cuarentena.'/'.dirname($ruta);
        if (! is_dir($destino)) {
            @mkdir($destino, 0777, true);
        }
        if (@rename($ruta, $destino.'/'.basename($ruta))) {
            $movidos++;
            echo "  [movido] $ruta\n";
        } else {
            echo "  [ERROR al mover] $ruta\n";
        }
    } else {
        $movidos++;
        echo "  [se movería] $ruta\n";
    }
}
if (! $candidatos) {
    echo "  ninguno\n";
}

echo "\n── Depuración dentro del código (NO se toca, se reporta)\n";
$hallazgos = 0;
foreach (['app', 'resources', 'routes'] as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    $it2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it2 as $f) {
        if (! $f->isFile() || ! preg_match('/\.(php|js)$/', $f->getFilename())) {
            continue;
        }
        $lineas = file($f->getPathname(), FILE_IGNORE_NEW_LINES);
        foreach ($lineas as $i => $l) {
            if (preg_match('/\bdd\(|\bdump\(|var_dump\(|print_r\(|\bray\(|console\.log\(|debugger/', $l)) {
                if ($hallazgos < 30) {
                    printf("  %s:%d  %s\n", str_replace('\\', '/', $f->getPathname()), $i + 1, trim(mb_substr($l, 0, 100)));
                }
                $hallazgos++;
            }
        }
    }
}
if ($hallazgos === 0) {
    echo "  ninguna\n";
} elseif ($hallazgos > 30) {
    echo '  ... y '.($hallazgos - 30)." más\n";
}

echo "\n── Archivos no rastreados en el repositorio\n";
$out = [];
exec('git status --porcelain 2>&1', $out);
$sueltos = array_values(array_filter($out, fn ($l) => str_starts_with($l, '??')));
foreach (array_slice($sueltos, 0, 20) as $s) {
    echo "  $s\n";
}
if (! $sueltos) {
    echo "  ninguno\n";
}

echo "\n═══════════════════════════════════════════\n";
printf("Candidatos: %d   ·   Protegidos por git: %d   ·   Depuración encontrada: %d\n",
    $movidos, $protegidos, $hallazgos);
if ($aplicar) {
    echo "Lo movido está en $cuarentena (recuperable con git o moviéndolo de vuelta).\n";
    echo "Vacía la cuarentena tú mismo cuando lo confirmes. Ningún agente debe hacerlo.\n";
} else {
    echo "Modo seco. Revisa la lista y ejecuta con --apply si estás de acuerdo.\n";
}
