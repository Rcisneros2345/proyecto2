# Checklist de Observabilidad (DevOps / Infra)

Objetivo: que un fallo en producción se detecte porque alguien lo vio en un log/alerta, no porque un usuario se quejó. Usar cuando la tarea toca despliegue, configuración de entorno o cuando `security-checklist.md` señala un hallazgo de logging/alertas.

## Logs

- ¿Los logs de aplicación están estructurados (JSON o formato consistente) y no son solo `echo`/`console.log` sueltos?
- ¿Cada request/job relevante tiene un identificador (correlation ID) que permite seguirlo de punta a punta, especialmente en la sincronización entre bases de datos?
- ¿Los niveles de log están bien usados (`error` para lo que rompe algo, `warning` para lo raro pero no fatal, `info`/`debug` para el resto) — no todo como `error`, ni todo como `info`?
- ¿Los logs de producción no exponen contraseñas, tokens, ni datos personales completos? (ver también `security-checklist.md` → Datos sensibles)
- ¿Hay una retención definida (cuánto tiempo se guardan los logs) en vez de crecer indefinidamente o perderse al reiniciar el contenedor?

## Alertas

- ¿Hay alguna señal automática (alerta, notificación, dashboard revisado) cuando la tasa de errores sube, un job programado falla, o un disco/cola se llena — o la única forma de enterarse es que el usuario reporte?
- ¿Los errores no manejados (excepciones sin capturar) llegan a algún lado visible (log agregado, servicio de error tracking), no solo a la salida estándar del contenedor que nadie mira?
- Para procesos programados/colas (ej. el job de sincronización Firebird↔MySQL): ¿se sabe si corrió y si falló, o se asume que "si no hay queja, funcionó"?

## Monitoreo básico

- ¿Hay un healthcheck simple (endpoint o comando) que confirme que la app y sus dependencias críticas (DB, cola) están arriba?
- ¿Se sabe, sin entrar a adivinar, cómo ver el estado actual del servidor/contenedor (uptime, uso de disco/memoria) cuando algo se reporta lento?

## Al cerrar la tarea

Registrar en `memory/PROJECT.md` (sección Infra) o `memory/DB_SCHEMA_NOTES.md` si aplica a la sincronización:
- qué logging/alertas existen hoy y dónde se ven,
- qué quedó sin cobertura de observabilidad y se aceptó como riesgo conocido.
