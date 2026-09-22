# Plan de continuación — después de las auditorías

Revisión del proyecto actualizado, 2026-09-19. Ejecuté los scripts contra tu código real;
lo que sigue está respaldado por salidas reales, no por lectura en diagonal.

---

## 1. Dónde estás realmente

Lo que las auditorías lograron:

| Documento | Tamaño | Qué aporta |
|---|---|---|
| `auditoria-completa-2026-09-19.md` | 49 KB | Inventario: 182 rutas, 29 controllers, 40 modelos, 15 servicios, 33 tablas, 38 pruebas |
| `auditoria-bases-datos-2026-09-19.md` | 33 KB | Esquema, mapeo Firebird→MySQL, índices, FKs, transformaciones |
| `auditoria-ui-ux-2026-09-19.md` | 13 KB | Sistema de diseño, 16 problemas priorizados, calificación 7.5/10 |
| `.ai/baseline/routes.json` | — | 182 rutas, 181 con nombre. **Baseline válido** (antes era un volcado de error) |

Eso está bien hecho. Pero hay que ser claro sobre **qué tipo de trabajo es**: son
inventarios. Describen lo que existe. No comprueban que lo que existe **funcione**. Y esa
diferencia acaba de costarte el hallazgo más grave del proyecto, que ninguna de las tres
auditorías vio.

---

## 2. Lo que encontré y las auditorías no

### 2.1 CRÍTICO — Hay rutas en producción conectadas a métodos vacíos que fingen éxito

`app/Http/Controllers/FingerprintController.php` tiene 75 líneas y **siete métodos, todos
stub**:

```php
public function copyFingerprint(Employee $employee, Fingerprint $fingerprint, Request $request): JsonResponse
{
    return response()->json(['status' => 'copied']);   // no copia nada
}
```

Lo mismo con `assignFingerprint` ('assigned'), `deleteFingerprint` ('deleted'),
`uploadFingerprints` / `uploadFingerprintsOnDevice` ('uploaded'), `removeFromDevice`
('removed'). Y `fingerprints()` devuelve `view('employee.fingerprints')`, una vista que **no
existe** (las vistas están en `employees/`).

`app/Http/Controllers/DeviceSyncController.php` tiene el mismo patrón en tres métodos:

```php
public function setTime(Device $device, Request $request): JsonResponse
{
    // ... existing logic from DeviceController::setTime
    return response()->json(['status' => 'time set']);
}
```

Igual `clearAttendance()` ('cleared') y `restore()` ('restored'), más el `queueSync()`
privado, vacío.

**Todas esas rutas existen y están enrutadas** (`routes/web.php:184-189, 207-208):
`devices.set-time`, `devices.clear-attendance`, `devices.restore`,
`employees.copy-fingerprint`, `employees.delete-fingerprint`,
`employees.upload-fingerprints`, `employees.remove`.

Consecuencia real: el usuario pulsa "sincronizar hora", "borrar huella", "copiar huella" o
"quitar del dispositivo", **la interfaz le dice que salió bien, y no pasó nada en el
equipo**. Peor que un error: un error se ve. Esto no.

El comentario `// ... existing logic from DeviceController::setTime` delata el origen: fue
un refactor de extracción de controllers que se quedó a medio camino — se crearon los
métodos nuevos, se movieron las rutas, y la lógica nunca se trajo. `DeviceController`
conserva 391 líneas con la implementación real de otras operaciones, así que el código
fuente probablemente sigue en el historial de git.

**Por qué las auditorías no lo vieron:** un inventario lee firmas, no cuerpos. La auditoría
completa incluso anota como positivo "ZktecoService con reintentos adaptativos robustos" —
cierto, pero esos controllers ni siquiera llaman a ZktecoService.

Esto es la fase 1. Antes que UI, antes que índices, antes que todo.

### 2.2 Dos hallazgos de la auditoría que son falsos

Verifícalos tú mismo; los cito para que no gastes trabajo en ellos:

| Hallazgo de la auditoría | Realidad |
|---|---|
| "API routes `api/academia/*` sin auth:sanctum ni permisos" | `routes/web.php:126` — están dentro de `auth` **y** `module_permission:academia,view`. Son rutas web para AJAX, no API de sanctum. No hay hueco |
| "Áreas y Puestos sin permisos RBAC en sus rutas" | `routes/web.php:228-232` — tienen `->middleware('admin')`. No es un hueco de seguridad; es una inconsistencia de criterio (admin en vez de módulo), que es otra cosa |

Regla que conviene fijar desde hoy: **un hallazgo de auditoría no es un hecho hasta que se
verifica en el código**. Antes de abrir tarea, se comprueba. Si no, el equipo trabaja sobre
ruido y pierde confianza en sus propios documentos.

### 2.3 Los scripts de verificación tenían dos fallos y ya los corregí

Ejecutándolos contra tu proyecto salió esto:

**a) Falsos positivos por `$this->route()`.** `verificar_referencias.php` reportaba 13
referencias rotas; 11 eran `$this->route('ciclo')` dentro de FormRequests, que es el
parámetro de ruta del Request, no el helper `route()`. Con el parche: **de 13 a 2**, y las 2
son reales:

```
[X] resources/views/welcome.blade.php:28  route('register') NO EXISTE
[X] app/Http/Controllers/FingerprintController.php:74  view('employee.fingerprints') NO EXISTE
```

**b) La verificación de permisos no verificaba nada.** `route:list --json` no devuelve el
alias que declaras (`module_permission:academia,view`), devuelve la clase ya resuelta
(`App\Http\Middleware\RequireModulePermission:academia,view`). Mis scripts y la prueba de
contrato buscaban solo el alias, encontraban **cero permisos** y pasaban en verde sin
comprobar nada. Con el parche, el baseline detecta **120 rutas con permiso y 28 claves
distintas**.

Los cuatro archivos corregidos están en `parches/`. Cópialos encima antes de seguir; sin
eso, media red de seguridad está apagada.

### 2.4 Un script nuevo: cobertura de autorización

Agregué `scripts/permisos.php`. Salida real de tu proyecto:

```
Rutas: 182 · MODULO: 101 · ADMIN: 63 · AUTH sin permiso: 13 · PUBLICA: 5
```

Las 5 públicas son de paquetes de desarrollo (`_ignition/*`, `_boost/*`) más
`sanctum/csrf-cookie`: desaparecen en producción porque `deploy.sh:49` ya usa
`composer install --no-dev`. Correcto, nada que hacer.

La **zona gris** son 13 rutas donde cualquier usuario con sesión entra sin permiso de
módulo. Quitando `login`, `logout`, `dashboard` y `api/user` (que deben ser así), quedan
**siete que sí hay que decidir**:

```
devices.check-status        POST  devices/{device}/check-status
employees.sobrantes         GET   employees/sobrantes
employees.sobrantes.data    GET   employees/sobrantes/data
firebird.index              GET   firebird
firebird.sync               GET   firebird/sync/{sync}
firebird.status             GET   firebird/{sync}/status
dashboard.kpisJson          GET   kpis/json
operations.notifications    GET   notifications
```

`employees.sobrantes.data` y `firebird.*` exponen datos operativos (empleados del checador,
estado de sincronizaciones) a cualquiera con cuenta.

---

## 3. Qué haría yo, en orden

La regla que ordena todo: **primero lo que miente, luego lo que se pierde, luego lo que
molesta, al final lo que se ve feo.**

### Fase 0 · Cerrar el ciclo de auditoría (hoy, ~1 hora)

Las auditorías se quedaron sin cerrar: `.ui-work/ESTADO.md` todavía dice
`fase_actual: validado, secciones: employees=validado` — el estado de septiembre 18, sin
mención de las tres auditorías nuevas. `docs/DEUDA-TECNICA.md` sigue con mis siete entradas
de plantilla, sin ninguno de los hallazgos nuevos. `docs/DECISIONES.md` está vacío.

Una auditoría que no se convierte en decisiones ni en estado no sirve para la siguiente
sesión: la IA vuelve a auditar lo mismo.

```bash
# 1. Aplicar los parches
copy parches\scripts\*.php scripts\
copy parches\tests\Feature\ContratoDeRutasTest.php tests\Feature\

# 2. Regenerar el baseline con detección de permisos correcta
php scripts/baseline.php
php scripts/permisos.php > .ui-work/00-auditoria/permisos-2026-09-19.txt

# 3. Foto real de referencias
php scripts/verificar_referencias.php

# 4. Confirmar que la prueba de contrato ya comprueba permisos
php artisan test --compact --filter=ContratoDeRutasTest
```

Luego, con el agente `documentador`:

```
@documentador consolida las tres auditorías de .ui-work/00-auditoria/ en
docs/DEUDA-TECNICA.md: una fila por hallazgo verificado, con archivo:línea, severidad y
agente responsable. Marca como DESCARTADOS los dos falsos (api/academia y areas/puestos)
citando por qué. Actualiza .ui-work/ESTADO.md al estado real.
```

Y archiva: `auditoria-completa-2026-09-18.md` (51 KB) quedó superada por la del 19.
Muévela a `.ui-work/_cuarentena/` o bórrala. Dos inventarios del mismo sistema con un día
de diferencia es ruido que la próxima sesión de IA va a leer entero.

También sobran: `.ui-work/parse_routes.php` y `.ui-work/routes_parsed.txt` (herramienta
desechable que la IA se fabricó), y `.route-baseline.json` / `.route-current.json` en la
raíz, que siguen en UTF-16 con el volcado de error. `scripts/baseline.php` los reemplazó.

### Fase 1 · Los stubs (esta semana) — P0

```
@decisiones ¿qué hacemos con los métodos stub de FingerprintController y
DeviceSyncController que están enrutados y devuelven éxito falso? Tienes el detalle en
.ui-work/02-decisiones/D-001-controllers-stub.md
```

Ya te dejé esa ficha escrita con tres opciones (recuperar del historial de git, deshabilitar
las rutas, o reimplementar sobre `ZktecoService`). Mi recomendación está ahí: **primero
deshabilitar, luego recuperar**, porque hoy lo urgente es que la UI deje de mentir.

Después, por cada método:

```
@zkteco recupera la implementación de DeviceController::setTime del historial de git
(git log -S "setTime" --oneline -- app/Http/Controllers/DeviceController.php) y llévala a
DeviceSyncController::setTime. Registra bitácora en DeviceSync/DeviceSyncItem, lock por
dispositivo, timeout. Luego corre php scripts/impacto.php y añade prueba Feature con doble
del gateway.
```

Verificación de que quedó bien: una prueba que **falle** si el método vuelve a ser un stub.
Basta con afirmar que el gateway recibió la llamada, no solo que la respuesta es 200.

Al terminar la fase, una pregunta para ti: ¿hay más refactors de extracción a medias? Búscalo
con `rg -n "existing logic from" app/` — es la huella exacta de este patrón.

### Fase 2 · Autorización de la zona gris (esta semana) — P1

```
@rbac toma las 7 rutas de la zona gris de scripts/permisos.php (excluye login, logout,
dashboard, api/user). Para cada una propón: (a) module_permission, (b) auth a propósito y
documentado, o (c) admin. Preséntalo con `decisiones`, no lo apliques solo.
```

Mi lectura rápida: `firebird.*` y `employees.sobrantes*` deberían ir a
`module_permission:dispositivos,view` o a un módulo `sincronizacion`; `kpis/json` y
`notifications` probablemente sean correctos a nivel auth, pero debe quedar **escrito** que
es deliberado. Y si `areas`/`puestos` van a seguir en `admin`, que también quede escrito:
la incoherencia documentada no es deuda, la incoherencia silenciosa sí.

Cada cambio, con su fila en la matriz RBAC de pruebas (con permiso 200 · sin permiso 403 ·
sin sesión redirige).

### Fase 3 · Datos que se pierden en silencio (próximas 2 semanas) — P1

De la auditoría de bases de datos, dos riesgos que corrompen sin avisar:

1. **Claves naturales sin normalizar** frente a `utf8mb4_unicode_ci` + `INSERT IGNORE`: la
   fila omitida no deja rastro. La auditoría lo lista como positivo ("idempotente vía
   INSERT IGNORE"); es idempotente **y** silencioso a la vez. El arreglo mínimo: convertir
   cada omisión por conflicto en un `FirebirdSyncItem` con estado y clave completa. Barato y
   te da visibilidad antes de decidir el arreglo de fondo.
2. **`executePending()` sin claim atómico** y borrado de jobs por `LIKE` del payload:
   doble ejecución posible. Igual: `decisiones` primero.

Y un tercero que sí es nuevo de esta auditoría: **`ALUMNOS_KARDEX` está excluido del sync
directo**. Si la pantalla de kárdex muestra datos, ¿de dónde salen? Pregunta para ti, no
para la IA.

### Fase 4 · UI (en paralelo, es independiente) — P2

Ver `PLAN-UI.md`. En corto: las 5 recomendaciones de prioridad alta de tu propia auditoría
son todas de esfuerzo bajo y se pueden hacer en una sesión, con impacto inmediato.

### Fase 5 · Limpieza estructural (cuando lo anterior esté en verde) — P3

- Las 5 tablas huérfanas (`empleados_cfghorarios*`, `empleados_horarios`,
  `profesores_horarios*`): se sincronizan desde Firebird y nadie las lee. Antes de retirarlas,
  decide si el módulo de horarios laborales las va a necesitar. Esto es `decisiones`, no
  `janitor`.
- `welcome.blade.php`: única vista sin referencias, y con un `route('register')` inexistente.
  Es la vista por defecto de Laravel. A cuarentena.
- `@if(false)` en `layouts/admin.blade.php:46` — 165 líneas de menú muerto en un archivo de
  460. Una tercera parte del layout es código que nunca se ejecuta.

---

## 4. Cadencia que sostiene esto

Un ciclo por módulo, no más de uno abierto a la vez:

```
lunes      /baseline + /auditar <MODULO>        (si no está auditado)
martes     /decidir  → eliges tú                 ← esta es la parte que no se delega
mié-jue    implementación + /impacto             ← y esta es la que se salta y cuesta
viernes    /verificar + /revisar + /cerrar
```

Tres métricas que sí dicen algo, medibles con los scripts:

| Métrica | Hoy | Cómo se mide |
|---|---|---|
| Referencias rotas | 2 | `php scripts/verificar_referencias.php` |
| Rutas en zona gris | 13 (7 a decidir) | `php scripts/permisos.php` |
| Métodos stub enrutados | 10 | `rg -n "existing logic from" app/` |

Si estos tres números bajan cada semana, el proyecto mejora. Si sube el número de documentos
de auditoría pero no bajan estos, se está generando papel.

---

## 5. Lo que aprendería de esta ronda

Tres cosas para ajustar el proceso, no el código:

**1. Auditar no es verificar.** Los inventarios describen; los scripts comprueban. Pide
siempre las dos cosas: el documento **y** la salida del comando. La regla ya está en
`.ai/guidelines/00-reglas.md` ("nunca afirmes que algo pasó sin pegar la salida real"), y
aquí no se aplicó: ninguna de las tres auditorías incluye una sola ejecución de
`verificar_referencias.php` o `impacto.php`.

**2. Un inventario no detecta código que miente.** Para eso hace falta la pregunta contraria:
"¿qué endpoint devuelve éxito sin hacer nada?". Añádela como paso fijo del agente `auditor`:

> Para cada método de controller enrutado, comprueba que el cuerpo haga algo real:
> que llame a un servicio, toque un modelo o despache un job. Un método que solo devuelve
> `response()->json([...])` con un literal es un stub: repórtalo como CRÍTICO.

**3. Tres auditorías de 95 KB en dos días es demasiado documento y poca decisión.** El
resultado de auditar debe ser una lista corta de decisiones tomadas, no un PDF mental de
50 KB que nadie relee. La proporción sana es al revés: mucho verificar, poco describir.

---

## 6. Los tres siguientes comandos

```bash
# 1
copy parches\scripts\*.php scripts\ && copy parches\tests\Feature\*.php tests\Feature\
php scripts/baseline.php && php scripts/permisos.php && php scripts/verificar_referencias.php

# 2  (en opencode)
@decisiones lee .ui-work/02-decisiones/D-001-controllers-stub.md, verifica los stubs en el
código y dime cuál de las tres opciones recomiendas con evidencia

# 3
/modulo DISPOSITIVOS
```
