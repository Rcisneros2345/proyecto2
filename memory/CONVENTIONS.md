# Project Conventions

Registra únicamente convenciones confirmadas por el repositorio (no inventadas).

## Naming
- **Modelos:** Singular PascalCase (`Employee`, `Device`, `Attendance`, `Area`, `Puesto`, `Incidencia`, `Profesor`, `Alumno`).
- **Tablas:** Plural snake_case (`devices`, `employees`, `attendances`, `device_employee`, `incidencias`, `horarios_laborales`).
- **Controladores:** PascalCase con sufijo `Controller` en `app/Http/Controllers/`.
- **Servicios:** PascalCase con sufijo `Service` en `app/Services/`.
- **Migraciones:** Formato estándar Laravel `YYYY_MM_DD_HHMMSS_<accion>_<tabla>_table.php`.

## Arquitectura
- **Capa de Servicios:** Lógica de negocio pesada (sincronizaciones ZKTeco, sincronización Firebird, cálculo de puntualidad) reside en `app/Services/`, manteniendo controladores delgados.
- **Colas y Tareas:** Procesamiento asíncrono para operaciones de red y sincronización en `app/Jobs/`.
- **Autorización:** Manejo de permisos mediante `app/Policies/` y sistema dinámico de permisos (`permissions`, `permission_groups`, `modules`).

## Tests
- **Comando principal:** `php artisan test` (o `vendor\bin\phpunit`).
- **Filtro específico:** `php artisan test --filter=<NombreDeClaseOTest>`
- **Requisito obligatorio:** MySQL local (XAMPP) en ejecución con la base de datos `rh_reloj_testing` creada. NUNCA ejecutar pruebas contra la base de datos principal (`rh-reloj`).

## Base de Datos
- **MySQL:** Gestor relacional principal. Integridad referencial enforced con foreign keys e índices de rendimiento en campos de búsqueda (`status`, `recorded_at`, `employee_id`).
- **Firebird 2.5:** Conexión de solo lectura. No realizar operaciones INSERT, UPDATE o DELETE sobre Firebird.
- **Transaccionalidad:** Envolver operaciones multi-tabla en `DB::transaction()` para garantizar atomicidad.

## UI / Frontend
- **Framework CSS:** Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 (no introducir Tailwind CSS).
- **Compilación de Assets:** Vite 4 (`npm run dev` para desarrollo, `npm run build` para producción).
- **Layout Base:** `resources/views/layouts/admin.blade.php`.
- **Manejo de Tema:** Soporte de tema Claro / Oscuro / Sistema sincronizado con `data-theme` y persistido en `localStorage['dash-theme']`.
- **Feedback al usuario:**
  - Toasts mediante `window.dashToast({ type, title, message })` o `window.showToast(type, message)`.
  - Modales de confirmación con `window.dashConfirm(opts)` o atributo `data-confirm` en formularios destructivos.
  - Paleta de comandos accesible con atajo `Ctrl+K`.

## Git
- Commits atómicos y descriptivos.
- Prohibido el uso de `git reset --hard` o `git clean -fd` sin autorización explícita.
