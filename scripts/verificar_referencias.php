<?php

/**
 * verificar_referencias.php — Caza enlaces rotos y referencias cruzadas incompletas.
 *
 *   php scripts/verificar_referencias.php
 *
 * Revisa en app/, routes/, resources/, tests/, database/seeders/:
 *   route('x')                 -> ¿existe esa ruta con nombre?
 *   view('x') / View::make     -> ¿existe la vista?
 *
 *   @extends @include @includeIf @includeWhen @includeUnless @includeFirst @each
 *   <x-componente>             -> ¿existe la vista del componente o su clase?
 *   asset('ruta')              -> ¿existe el archivo en public/?
 *   module_permission:mod,acc  -> ¿esa clave aparece en los seeders de permisos?
 *   href="/url/a/mano"         -> advertencia: debería ser route()
 *
 * Salida 0 = limpio · 1 = referencias rotas · 2 = no se pudo verificar.
 */
$raiz = dirname(__DIR__);
chdir($raiz);

/* ------------------------------------------------------- rutas disponibles */
/**
 * Extrae las claves module_permission de una lista de middleware.
 *
 * Ojo: `php artisan route:list --json` NO devuelve el alias declarado en las rutas
 * (`module_permission:academia,view`), sino la clase ya resuelta
 * (`App\Http\Middleware\RequireModulePermission:academia,view`). Si solo buscas el alias,
 * encuentras cero permisos y la verificación pasa en falso.
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

function leerJson(string $texto): mixed
{
    if (str_starts_with($texto, "\xFF\xFE") || str_starts_with($texto, "\xFE\xFF")) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-16');
    }

    return json_decode(preg_replace('/^\xEF\xBB\xBF/', '', $texto), true);
}

$salida = [];
$codigo = 0;
exec('php artisan route:list --json 2>&1', $salida, $codigo);
$rutas = leerJson(implode("\n", $salida));

if (! is_array($rutas)) {
    $fallback = '.ai/baseline/routes.json';
    if (is_file($fallback)) {
        $rutas = leerJson((string) file_get_contents($fallback));
        fwrite(STDERR, "AVISO: artisan no respondió; se usa el baseline de rutas.\n");
    }
}

if (! is_array($rutas)) {
    fwrite(STDERR, "ERROR: no hay lista de rutas. Ejecuta php scripts/baseline.php\n");
    exit(2);
}

$nombresRuta = [];
$permisosRuta = [];
foreach ($rutas as $r) {
    if (! empty($r['name'])) {
        $nombresRuta[$r['name']] = true;
    }
    foreach (clavesDePermiso($r['middleware'] ?? []) as $clave) {
        $permisosRuta[$clave] = $r['name'] ?? ($r['uri'] ?? '?');
    }
}

/* ----------------------------------------------------------- utilidades */
function archivosEn(string $dir, string $patron): array
{
    if (! is_dir($dir)) {
        return [];
    }
    $out = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        if ($f->isFile() && preg_match($patron, $f->getFilename())) {
            $out[] = str_replace('\\', '/', $f->getPathname());
        }
    }
    sort($out);

    return $out;
}

function linea(string $c, int $offset): int
{
    return substr_count(substr($c, 0, $offset), "\n") + 1;
}

function vistaExiste(string $nombre): bool
{
    $p = str_replace('.', '/', $nombre);

    return is_file("resources/views/$p.blade.php") || is_file("resources/views/$p.php");
}

function componenteExiste(string $nombre): bool
{
    $p = str_replace('.', '/', $nombre);
    if (is_file("resources/views/components/$p.blade.php")
        || is_file("resources/views/components/$p/index.blade.php")) {
        return true;
    }
    $partes = array_map(
        fn ($seg) => str_replace(' ', '', ucwords(str_replace('-', ' ', $seg))),
        explode('/', $p)
    );

    return is_file('app/View/Components/'.implode('/', $partes).'.php');
}

$archivos = array_merge(
    archivosEn('resources/views', '/\.blade\.php$/'),
    archivosEn('app', '/\.php$/'),
    archivosEn('routes', '/\.php$/'),
    archivosEn('tests', '/\.php$/'),
    archivosEn('database/seeders', '/\.php$/')
);

$errores = [];
$avisos = [];
$dinamicas = 0;

/* ------------------------------------------------------------- escaneo */
foreach ($archivos as $archivo) {
    $c = (string) file_get_contents($archivo);
    $esBlade = str_ends_with($archivo, '.blade.php');

    // (?<!->) evita $this->route('ciclo') de los FormRequest: ahi route() es el parametro
    // de ruta del Request, no el helper. Ese falso positivo ensuciaba el reporte entero.
    if (preg_match_all('/(?<!->)\broute\(\s*([\'"])([A-Za-z0-9_.\-]+)\1/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($m as $h) {
            if (! isset($nombresRuta[$h[2][0]])) {
                $errores[] = sprintf("%s:%d  route('%s') NO EXISTE", $archivo, linea($c, $h[0][1]), $h[2][0]);
            }
        }
    }
    $dinamicas += preg_match_all('/(?<!->)\broute\(\s*\$/', $c);

    if (preg_match_all('/\b(?:view|View::make|Route::view)\(\s*([\'"])([A-Za-z0-9_.\-\/]+)\1/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($m as $h) {
            $n = $h[2][0];
            if (! vistaExiste($n)) {
                $errores[] = sprintf("%s:%d  view('%s') NO EXISTE (esperada en resources/views/%s.blade.php)",
                    $archivo, linea($c, $h[0][1]), $n, str_replace('.', '/', $n));
            }
        }
    }

    if ($esBlade) {
        if (preg_match_all('/@(extends|include|includeIf|includeFirst|each)\(\s*([\'"])([A-Za-z0-9_.\-\/]+)\2/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($m as $h) {
                if (! vistaExiste($h[3][0])) {
                    $errores[] = sprintf("%s:%d  @%s('%s') NO EXISTE", $archivo, linea($c, $h[0][1]), $h[1][0], $h[3][0]);
                }
            }
        }
        if (preg_match_all('/@include(?:When|Unless)\(\s*[^,]+,\s*([\'"])([A-Za-z0-9_.\-\/]+)\1/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($m as $h) {
                if (! vistaExiste($h[2][0])) {
                    $errores[] = sprintf("%s:%d  @includeWhen/@includeUnless('%s') NO EXISTE", $archivo, linea($c, $h[0][1]), $h[2][0]);
                }
            }
        }
        if (preg_match_all('/<x-([A-Za-z0-9_.\-\/]+)/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($m as $h) {
                $n = rtrim($h[1][0], '.-/');
                if ($n === '' || str_starts_with($n, 'slot')) {
                    continue;
                }
                if (! componenteExiste($n)) {
                    $avisos[] = sprintf('%s:%d  <x-%s> sin vista ni clase encontrada (¿de un paquete? verifícalo)',
                        $archivo, linea($c, $h[0][1]), $n);
                }
            }
        }
        if (preg_match_all('/(?:href|action)\s*=\s*"(\/[^"{}\s]*)"/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            foreach ($m as $h) {
                $u = $h[1][0];
                if ($u === '/' || str_starts_with($u, '//')) {
                    continue;
                }
                $avisos[] = sprintf('%s:%d  URL escrita a mano "%s" — usa route(\'nombre\')',
                    $archivo, linea($c, $h[0][1]), $u);
            }
        }
    }

    if (preg_match_all('/\basset\(\s*([\'"])([^\'"$]+)\1/', $c, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($m as $h) {
            $ruta = ltrim($h[2][0], '/');
            if ($ruta === '' || str_contains($ruta, '://')) {
                continue;
            }
            if (! file_exists("public/$ruta")) {
                $errores[] = sprintf("%s:%d  asset('%s') NO EXISTE en public/", $archivo, linea($c, $h[0][1]), $h[2][0]);
            }
        }
    }
}

/* --------------------------- claves de permiso sin sembrar (heurístico) */
$seeders = '';
foreach (archivosEn('database/seeders', '/\.php$/') as $s) {
    $seeders .= (string) file_get_contents($s);
}
$permisosSinSembrar = [];
if ($seeders !== '') {
    foreach ($permisosRuta as $clave => $ruta) {
        [$modulo, $accion] = array_pad(explode(',', $clave, 2), 2, '');
        if (! str_contains($seeders, "'$modulo'") && ! str_contains($seeders, "\"$modulo\"")) {
            $permisosSinSembrar[] = "$clave (ruta: $ruta) — el módulo '$modulo' no aparece en los seeders";
        } elseif ($accion !== '' && ! str_contains($seeders, "'$accion'") && ! str_contains($seeders, "\"$accion\"")) {
            $permisosSinSembrar[] = "$clave (ruta: $ruta) — la acción '$accion' no aparece en los seeders";
        }
    }
}

/* ---------------------------------------------- vistas sin referencias */
$global = '';
foreach ($archivos as $a) {
    $global .= (string) file_get_contents($a);
}
$huerfanas = [];
foreach (archivosEn('resources/views', '/\.blade\.php$/') as $v) {
    $rel = substr($v, strlen('resources/views/'), -strlen('.blade.php'));
    if (str_starts_with($rel, 'components/') || str_starts_with($rel, 'layouts/') || str_starts_with($rel, 'errors/')) {
        continue;
    }
    $punteado = str_replace('/', '.', $rel);
    $base = basename($rel);
    if (! str_contains($global, "'$punteado'") && ! str_contains($global, "\"$punteado\"")
        && ! str_contains($global, "'$rel'") && ! str_contains($global, $base)) {
        $huerfanas[] = $v;
    }
}

/* ------------------------------------------------------------- reporte */
echo "== Verificación de referencias cruzadas ==\n";
printf("Archivos: %d  |  Rutas con nombre: %d  |  route() dinámicas no verificables: %d\n\n",
    count($archivos), count($nombresRuta), $dinamicas);

if ($errores) {
    echo 'REFERENCIAS ROTAS ('.count($errores).") — BLOQUEAN EL COMMIT\n";
    foreach ($errores as $e) {
        echo "  [X] $e\n";
    }
    echo "\n";
}

if ($permisosSinSembrar) {
    echo 'Permisos de ruta sin rastro en seeders ('.count($permisosSinSembrar).") — revísalos con el agente rbac\n";
    foreach (array_slice($permisosSinSembrar, 0, 25) as $p) {
        echo "  [!] $p\n";
    }
    echo "\n";
}

if ($avisos) {
    echo 'Advertencias ('.count($avisos).")\n";
    foreach (array_slice($avisos, 0, 40) as $a) {
        echo "  [!] $a\n";
    }
    if (count($avisos) > 40) {
        echo '  ... y '.(count($avisos) - 40)." más\n";
    }
    echo "\n";
}

if ($huerfanas) {
    echo 'Vistas sin referencias encontradas ('.count($huerfanas).") — no las borres sin verificar\n";
    foreach (array_slice($huerfanas, 0, 20) as $h) {
        echo "  [?] $h\n";
    }
    echo "\n";
}

if ($errores) {
    exit(1);
}

echo "Sin referencias rotas.\n";
exit(0);
