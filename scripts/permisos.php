<?php

/**
 * permisos.php — Mapa de cobertura de autorización, ruta por ruta.
 *
 *   php scripts/permisos.php              # resumen + rutas sin permiso de módulo
 *   php scripts/permisos.php --todas      # incluye también las que sí tienen permiso
 *   php scripts/permisos.php --csv        # salida para pegar en una hoja
 *
 * Clasifica cada ruta por su capa de autorización real:
 *   PUBLICA        sin auth (revisar siempre)
 *   AUTH           autenticado pero sin permiso de módulo  ← la zona gris
 *   ADMIN          protegida por EnsureAdmin
 *   MODULO         con module_permission:<modulo>,<accion>
 *
 * Lee .ai/baseline/routes.json, así que ejecuta antes php scripts/baseline.php.
 */
$raiz = dirname(__DIR__);
chdir($raiz);

$args = array_slice($argv, 1);
$todas = in_array('--todas', $args, true);
$csv = in_array('--csv', $args, true);

$archivo = '.ai/baseline/routes.json';
if (! is_file($archivo)) {
    fwrite(STDERR, "ERROR: falta $archivo. Ejecuta: php scripts/baseline.php\n");
    exit(2);
}

$rutas = json_decode(preg_replace('/^\xEF\xBB\xBF/', '', (string) file_get_contents($archivo)), true);
if (! is_array($rutas)) {
    fwrite(STDERR, "ERROR: el baseline no es JSON válido. Regenéralo con php scripts/baseline.php\n");
    exit(2);
}

/**
 * `route:list --json` guarda la clase resuelta (RequireModulePermission:mod,accion),
 * no el alias declarado (module_permission:mod,accion). Aceptamos las dos formas.
 */
function clavesDePermiso(mixed $middleware): array
{
    $out = [];
    foreach ((array) $middleware as $m) {
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

$filas = [];
$conteo = ['PUBLICA' => 0, 'AUTH' => 0, 'ADMIN' => 0, 'MODULO' => 0];
$modulos = [];

foreach ($rutas as $r) {
    $mw = (array) ($r['middleware'] ?? []);
    $permisos = clavesDePermiso($mw);
    $auth = false;
    $admin = false;

    foreach ($mw as $m) {
        if (! is_string($m)) {
            continue;
        }
        if (str_contains($m, 'Authenticate') || $m === 'auth') {
            $auth = true;
        }
        if (str_contains($m, 'EnsureAdmin') || $m === 'admin') {
            $admin = true;
        }
    }

    if ($permisos) {
        $capa = 'MODULO';
        foreach ($permisos as $p) {
            $modulos[explode(',', $p)[0]][] = $r['name'] ?? $r['uri'];
        }
    } elseif ($admin) {
        $capa = 'ADMIN';
    } elseif ($auth) {
        $capa = 'AUTH';
    } else {
        $capa = 'PUBLICA';
    }

    $conteo[$capa]++;
    $filas[] = [
        'capa' => $capa,
        'nombre' => $r['name'] ?? '(sin nombre)',
        'metodo' => $r['method'] ?? '',
        'uri' => $r['uri'] ?? '',
        'permisos' => implode(' + ', $permisos),
        'accion' => $r['action'] ?? '',
    ];
}

if ($csv) {
    echo "capa,nombre,metodo,uri,permisos,accion\n";
    foreach ($filas as $f) {
        if (! $todas && $f['capa'] === 'MODULO') {
            continue;
        }
        echo implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', $v).'"', $f))."\n";
    }
    exit(0);
}

echo "== Cobertura de autorización ==\n";
printf("Rutas: %d   ·   MODULO: %d   ·   ADMIN: %d   ·   AUTH sin permiso: %d   ·   PUBLICA: %d\n\n",
    count($rutas), $conteo['MODULO'], $conteo['ADMIN'], $conteo['AUTH'], $conteo['PUBLICA']);

$publicas = array_values(array_filter($filas, fn ($f) => $f['capa'] === 'PUBLICA'));
if ($publicas) {
    echo "── PÚBLICAS (sin autenticación) — revisa cada una\n";
    foreach ($publicas as $f) {
        printf("  %-38s %-10s %s\n", $f['nombre'], $f['metodo'], $f['uri']);
    }
    echo "  Nota: las rutas _ignition/* y _boost/* vienen de paquetes de desarrollo.\n";
    echo "  Desaparecen en producción si el despliegue usa composer install --no-dev.\n\n";
}

$grises = array_values(array_filter($filas, fn ($f) => $f['capa'] === 'AUTH'));
if ($grises) {
    echo '── ZONA GRIS: autenticado pero SIN permiso de módulo ('.count($grises).")\n";
    echo "   Cualquier usuario con sesión entra, tenga o no permiso del módulo.\n";
    foreach ($grises as $f) {
        printf("  %-38s %-10s %s\n", $f['nombre'], $f['metodo'], $f['uri']);
    }
    echo "\n";
}

echo '── Protegidas por EnsureAdmin ('.$conteo['ADMIN'].")\n";
echo "   Decisión de diseño válida, pero no es lo mismo que un permiso de módulo:\n";
echo "   un operador con permiso no entra, y un admin entra aunque no tenga el permiso.\n\n";

if ($modulos) {
    echo "── Rutas por módulo con permiso declarado\n";
    ksort($modulos);
    foreach ($modulos as $mod => $rs) {
        printf("  %-28s %d rutas\n", $mod, count($rs));
    }
    echo "\n";
}

if ($todas) {
    echo "── Detalle completo\n";
    foreach ($filas as $f) {
        printf("  [%-7s] %-38s %-28s %s\n", $f['capa'], $f['nombre'], $f['permisos'], $f['uri']);
    }
}

echo "Siguiente paso: por cada ruta en ZONA GRIS decide una de tres cosas —\n";
echo "  (a) darle module_permission, (b) dejarla a nivel auth a propósito y documentarlo,\n";
echo "  (c) moverla a admin. Registra la decisión en .ui-work/02-decisiones/.\n";
