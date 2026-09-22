# 04 · Dispositivos ZKTeco

Un error aquí deja a alguien sin poder checar, o borra huellas que hay que recapturar
persona por persona.

## Reglas por operación

| Operación | Regla |
|---|---|
| Subir empleado | Idempotente por `device_uid`; nunca reasignar un uid en uso |
| Copiar huellas | El empleado debe existir en el destino antes de copiar |
| Eliminar usuario o huella | **Confirmación humana explícita**, uno por uno, con respaldo previo |
| Sobrantes | Decisión manual por fila: borrar, ignorar o resincronizar |
| Info del equipo | Cachear; nunca dentro de un bucle de vista |
| Sincronizar hora | Registrar el desfase previo |
| Descargar asistencias | Incremental, sin duplicar; no limpiar el log del equipo sin respaldo verificado |

## No negociable

1. **Bitácora**: `DeviceSync` + un `DeviceSyncItem` por empleado/huella, con estado y mensaje.
   Es lo que permite reanudar y decir **qué empleado** falló.
2. **Idempotencia** y **reanudación** desde el último ítem en `ok`.
3. **Timeout, `$tries`, `$backoff`** en todo lo que hable con un equipo; siempre en cola.
4. **Lock por dispositivo**: dos sincronizaciones simultáneas al mismo equipo se excluyen.
5. **Alcance explícito**: nunca "todos los dispositivos" por defecto.
6. El payload de `SyncProgressUpdated` es contrato con la UI: si lo cambias, corre
   `php scripts/impacto.php` y ajusta el panel de progreso y sus pruebas.

## Pruebas

Contra doble del gateway, nunca contra hardware. Casos mínimos: equipo sin respuesta,
corte a mitad, reejecución, uid duplicado, empleado de baja aún cargado, dos syncs
simultáneas. Referencias: `ZktecoSyncTest`, `SyncQueueTest`, `DeviceSyncControllerTest`.
