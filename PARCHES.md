# Parches a los scripts (aplicar antes de continuar)

Ejecuté los scripts contra tu proyecto real y aparecieron dos fallos. Los archivos
corregidos están en `parches/`. Cópialos encima de los tuyos:

```cmd
copy parches\scripts\*.php scripts\
copy parches\tests\Feature\ContratoDeRutasTest.php tests\Feature\
php scripts/baseline.php
```

---

## 1. Falsos positivos por `$this->route()`

`verificar_referencias.php` reportaba 13 referencias rotas. Once eran esto:

```php
// app/Http/Requests/CicloFormRequest.php:21
fn ($attr, $value, $fail) => !$this->route('ciclo') || $this->route('ciclo')->inicial !== $value
```

Ahí `route()` es el método del Request que devuelve el parámetro de ruta, no el helper
global. La expresión regular ahora lleva `(?<!->)`:

```php
'/(?<!->)\broute\(\s*([\'"])([A-Za-z0-9_.\-]+)\1/'
```

**Resultado en tu proyecto: de 13 referencias rotas a 2**, y las 2 son reales:

```
[X] resources/views/welcome.blade.php:28  route('register') NO EXISTE
[X] app/Http/Controllers/FingerprintController.php:74  view('employee.fingerprints') NO EXISTE
```

Un verificador con 85% de falsos positivos es peor que ninguno: enseña a ignorar la salida.

## 2. La comprobación de permisos no comprobaba nada

Las rutas declaran el alias:

```php
->middleware('module_permission:academia,view')
```

pero `php artisan route:list --json` guarda la clase ya resuelta:

```json
"middleware": ["web", "App\\Http\\Middleware\\Authenticate",
               "App\\Http\\Middleware\\RequireModulePermission:academia,view"]
```

Mis scripts y `ContratoDeRutasTest` buscaban solo el prefijo `module_permission:`.
Encontraban **cero** permisos, así que la comparación `array_diff([], [...])` salía vacía y
todo pasaba en verde sin verificar nada. El peor tipo de fallo: silencioso y tranquilizador.

Los cuatro archivos ahora aceptan las dos formas:

```php
if (str_starts_with($m, 'module_permission:')) {
    $out[] = substr($m, strlen('module_permission:'));
} elseif (preg_match('/RequireModulePermission:(.+)$/', $m, $mm)) {
    $out[] = $mm[1];
}
```

**Resultado en tu proyecto: de 0 a 120 rutas con permiso detectadas, 28 claves distintas.**
La prueba `test_ninguna_ruta_perdio_su_permiso_de_modulo` ahora sí protege.

Archivos tocados: `baseline.php`, `comparar_rutas.php`, `verificar_referencias.php`,
`tests/Feature/ContratoDeRutasTest.php`.

---

## 3. Script nuevo: `scripts/permisos.php`

Mapa de cobertura de autorización, ruta por ruta. Salida real de tu proyecto:

```
Rutas: 182 · MODULO: 101 · ADMIN: 63 · AUTH sin permiso: 13 · PUBLICA: 5
```

- `--todas` incluye también las que sí tienen permiso.
- `--csv` para pegar en una hoja y repartir decisiones.

Lo importante es la **zona gris**: rutas donde cualquier usuario con sesión entra sin permiso
de módulo. Quitando login/logout/dashboard/api-user, quedan siete que hay que decidir, entre
ellas `employees.sobrantes.data` y `firebird.*`, que exponen datos operativos.

Las 5 públicas son `_ignition/*`, `_boost/*` y `sanctum/csrf-cookie`: vienen de paquetes de
desarrollo y desaparecen en producción, porque `deploy.sh:49` ya usa
`composer install --no-dev`. Nada que corregir ahí.

---

## Verificación de que los parches quedaron bien

```bash
php -l scripts/verificar_referencias.php     # sin errores de sintaxis
php scripts/baseline.php                     # debe reportar claves de permiso > 0
php scripts/permisos.php                     # MODULO debe dar ~101, no 0
php scripts/verificar_referencias.php        # debe dar 2 referencias rotas, no 13
php artisan test --compact --filter=ContratoDeRutasTest
```

Si `baseline.php` sigue diciendo 0 claves de permiso, el parche no se copió.
