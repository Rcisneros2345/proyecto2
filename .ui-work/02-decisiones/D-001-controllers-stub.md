---
decision: D-001
modulo: DISPOSITIVOS / HUELLAS
fecha: 2026-09-19
estado: PROPUESTA
decidido_por: —
---

# D-001 · Métodos stub enrutados que devuelven éxito falso

## El problema en una frase

Diez métodos de controller enrutados no hacen nada y responden éxito:
`FingerprintController.php:18,27,36,45,53,61,71` (los siete métodos del archivo) y
`DeviceSyncController.php:198,207,216,225` (`setTime`, `clearAttendance`, `restore`,
`queueSync`).

```php
public function copyFingerprint(Employee $employee, Fingerprint $fingerprint, Request $request): JsonResponse
{
    return response()->json(['status' => 'copied']);   // no copia nada
}

public function setTime(Device $device, Request $request): JsonResponse
{
    // ... existing logic from DeviceController::setTime
    return response()->json(['status' => 'time set']);
}
```

Rutas afectadas (`routes/web.php:184-189, 207-208`): `devices.set-time`,
`devices.clear-attendance`, `devices.restore`, `employees.copy-fingerprint`,
`employees.delete-fingerprint`, `employees.upload-fingerprints`, `employees.remove`.

Además `FingerprintController::fingerprints()` devuelve `view('employee.fingerprints')`, que
no existe (las vistas están en `employees/`), y ninguna ruta apunta a ese método.

## Qué está en juego

El operador pulsa "sincronizar hora" o "borrar huella", la interfaz confirma que salió bien,
y el equipo no cambió. Nadie revisa un equipo que "ya se sincronizó". El daño no se ve el día
que ocurre: se ve semanas después, cuando alguien no puede checar y el sistema dice que todo
está en orden.

El comentario `// ... existing logic from DeviceController::X` indica que fue un refactor de
extracción de controllers que quedó a medias: se crearon los métodos, se movieron las rutas,
y la lógica nunca se trajo.

## Opciones

### Opción A — Deshabilitar las rutas hoy, recuperar la lógica después
- **Qué implica:** comentar o proteger las 7 rutas afectadas y ocultar sus botones en la UI,
  con un mensaje claro ("temporalmente no disponible"). Después, método por método, traer la
  implementación desde el historial de git.
- **Cuesta:** ~1 hora para el corte; 1-2 días para la recuperación completa.
- **Gana:** el sistema deja de mentir hoy mismo.
- **Riesgo:** BAJO — se pierde funcionalidad que hoy **ya no existe**; solo desaparece la
  ilusión de que existe.
- **Reversible:** sí, trivial.

### Opción B — Recuperar la lógica del historial de git ahora, sin cortar nada
- **Qué implica:** `git log -S "setTime" -- app/Http/Controllers/DeviceController.php` para
  localizar la versión que sí la tenía, y trasladarla a los controllers nuevos.
- **Cuesta:** 1-2 días, más pruebas con doble del gateway.
- **Gana:** recupera la funcionalidad sin ventana de indisponibilidad.
- **Riesgo:** MEDIO — el código antiguo puede no encajar con la estructura actual
  (`DeviceSync`/`DeviceSyncItem`, jobs, locks) y arrastrar deuda vieja. Mientras dura, la UI
  sigue mintiendo.
- **Reversible:** sí.

### Opción C — Reimplementar sobre `ZktecoService`, sin mirar el historial
- **Qué implica:** escribir de nuevo cada operación usando el servicio actual, con bitácora,
  lock por dispositivo, timeout y reintentos.
- **Cuesta:** 3-5 días con pruebas.
- **Gana:** queda consistente con el resto del módulo y con las reglas actuales.
- **Riesgo:** MEDIO-ALTO — reescribir operaciones sobre hardware sin la referencia previa
  puede perder casos límite que el código viejo ya resolvía.
- **Reversible:** sí, pero caro.

## Recomendación

**A ahora, y luego B con revisión.** Lo urgente no es recuperar la función: es que la
interfaz deje de afirmar cosas falsas, y eso cuesta una hora. Con el corte hecho, la
recuperación se puede hacer con calma, método por método, cada uno con su prueba.

C solo para los métodos cuyo código histórico no aparezca o esté claramente obsoleto.

## Qué necesito de ti

Una respuesta: ¿corto las 7 rutas hoy (A) o prefieres aguantar la mentira unos días mientras
se recupera la lógica (B)?

Y una segunda, para dimensionar: ¿alguien usa hoy "sincronizar hora", "limpiar asistencias"
o "copiar huella" en la operación diaria? Si nadie las usa, A es obvia.

## Si nos arrepentimos

A: se revierte el commit y vuelven las rutas.
B y C: `git revert`; no hay migraciones involucradas.

## Nota de método

Busca el resto del patrón antes de cerrar esta decisión:

```bash
rg -n "existing logic from" app/
rg -n "return response\(\)->json\(\['status'" app/Http/Controllers/
```

Si aparecen más, entran en esta misma decisión, no en otra.
