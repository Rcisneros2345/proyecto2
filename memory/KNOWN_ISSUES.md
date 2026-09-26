# Known Issues

Formato recomendado:
## ISSUE-ID — #tag Título
- Síntoma:
- Causa conocida:
- Archivos:
- Workaround:
- Estado: Abierto | En progreso | Resuelto (fecha)
- Cómo verificar:

Regla de poda: issue "Resuelto" se mantiene completo solo 5 tareas después del cierre. Luego se condensa a:
`RESUELTO — <título> — <fecha> — ref commit/PR si aplica`

---

## ISSUE-01 — #deuda-tecnica #infra Paridad de pruebas requiere MySQL local activo
- Síntoma: `php artisan test` falla si el servicio de MySQL local no está en ejecución o si la base de datos de pruebas no existe.
- Causa conocida: `phpunit.xml` define intencionalmente `DB_CONNECTION=mysql` apuntando a `rh_reloj_testing` para garantizar paridad exacta de comportamiento, tipos y migraciones con producción, en lugar de usar SQLite en memoria.
- Archivos: `phpunit.xml`
- Workaround: Iniciar el servidor MySQL de XAMPP y crear la base de datos `rh_reloj_testing` (`CREATE DATABASE IF NOT EXISTS rh_reloj_testing;`) antes de correr la suite.
- Estado: Abierto (comportamiento documentado por diseño)
- Cómo verificar: Ejecutar `php artisan test` con MySQL activo.

## ISSUE-02 — #seguridad #infra Dependencia de conectividad de red para dispositivos ZKTeco
- Síntoma: Fallos de timeout o excepción de socket al intentar sincronizar relojes biométricos si no están encendidos o no son alcanzables en la subred.
- Causa conocida: La librería `coding-libs/zkteco-php` realiza conexiones directas por socket UDP/IP a las IPs registradas en la tabla `devices`.
- Archivos: `app/Services/` y jobs relacionados con `DeviceSync`.
- Workaround: Manejo de excepciones en los jobs de sincronización y registro del error en `device_syncs` y logs de Laravel.
- Estado: Abierto (inherente a hardware de red)
- Cómo verificar: Comprobar ping a la IP del dispositivo antes de iniciar tareas de sincronización masiva.
