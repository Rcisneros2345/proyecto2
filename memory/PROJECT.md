# Project Memory

## Stack
- **Lenguaje / Runtime:** PHP ^8.1
- **Framework Principal:** Laravel 10.x (`laravel/framework: ^10.0`)
- **Gestores de Dependencias:** Composer (backend PHP) y npm (assets frontend)
- **Frontend:** Laravel Blade + Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 + Vite 4.x + Axios
- **Integración Hardware:** ZKTeco Biometrics (`coding-libs/zkteco-php: ^0.0.35`) para relojes checadores biométricos (UDP/IP)
- **Herramientas de Desarrollo:** PHPUnit 10.0, Laravel Pint, Laravel Sail, Laravel Tinker, Spatie Laravel Ignition

## Arquitectura
- **Tipo de Aplicación:** Sistema de Recursos Humanos, Catálogo Central de Empleados, Control de Asistencias y Sincronización Biométrica (Proyecto UTE / rh-reloj).
- **Patrón:** MVC extendido de Laravel:
  - Controladores en `app/Http/Controllers/`
  - Modelos Eloquent en `app/Models/`
  - Capa de servicios de negocio en `app/Services/`
  - Tareas en segundo plano y colas en `app/Jobs/`
  - Políticas de autorización en `app/Policies/`
  - DTOs / Clases de datos en `app/Data/` y Enums en `app/Enums/`
  - Vistas organizadas modularmente en Blade bajo `resources/views/` con layout administrativo `layouts/admin.blade.php`.
- **Navegación y Permisos:** Sistema de permisos dinámico con soporte de módulos, grupos de permisos y catálogo de navegación en BD (`navigation_items`).
- **Procesamiento Asíncrono:** Soporte para colas y workers mediante Supervisor (`deploy.sh` soporta `USE_SUPERVISOR=1`).

## Bases de datos / Integraciones externas
- **MySQL (Principal):**
  - Motor de almacenamiento transaccional de la aplicación (`rh-reloj` en producción, configurable vía `DB_DATABASE`).
  - Entorno de testing configurado en `phpunit.xml` sobre MySQL (`rh_reloj_testing`), buscando paridad estricta con producción.
  - Tablas para empleados, huellas, dispositivos biométricos, asistencias, incidencias, usuarios, auditoría y catálogos espejeados.
- **Firebird 2.5 (Sistema Legado Institucional UTE):**
  - Conexión configurada en `config/database.php` bajo la clave `firebird` (vía PDO con DSN `FIREBIRD_DSN`).
  - Rol de solo lectura para extracción y sincronización periódica de datos académicos y de personal institucional (`ciclos`, `materias`, `profesores`, `alumnos`, `horarios`).
- **Dispositivos Biométricos ZKTeco:**
  - Comunicación directa en red con terminales de huella dactilar para sincronizar usuarios, huellas y descargar marcas de asistencia.

## CI/CD e Infraestructura
- **CI/CD Remoto:** No configurado en repositorio (sin directorio `.github/` ni `.gitlab-ci.yml`).
- **Despliegue:** Automatizado mediante script local/servidor `deploy.sh`:
  - Verificación de preflight (`APP_ENV=production`, conectividad a Supervisor).
  - Git pull fast-forward.
  - Composer install (`--no-dev --optimize-autoloader`).
  - Modo mantenimiento con página de renderizado 503.
  - Detención y reinicio controlado de workers de colas (`supervisorctl` / `queue:restart`).
  - Respaldo previo con `mysqldump`.
  - Ejecución de migraciones (`php artisan migrate --force`).
  - Optimización de caché de rutas, vistas y configuración (`optimize`, `event:cache`).

## Reglas permanentes
- No inventar patrones si el repositorio ya tiene uno funcional.
- Mantener cambios pequeños y verificables.
- Tratar Firebird estrictamente como base de datos de SOLO LECTURA.
- No ejecutar migraciones destructivas ni resets en la base de datos sin autorización explícita del usuario.
- Las pruebas automatizadas deben ejecutarse contra `rh_reloj_testing`, nunca contra la base de datos principal de desarrollo o producción.
- Seguridad no es opcional en cambios de autenticación, permisos, entrada de datos o manejo de datos sensibles biométricos y personales.
