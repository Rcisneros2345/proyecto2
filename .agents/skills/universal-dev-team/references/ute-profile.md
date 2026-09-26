# Perfil UTE / PHP-Laravel-Firebird-MySQL

Carga este documento solo si el proyecto pertenece al ecosistema UTE o muestra claramente este stack.

## Stack de referencia
- PHP / Laravel
- Blade + Bootstrap + JavaScript
- MySQL como almacenamiento principal cuando la aplicación así lo defina
- Firebird 2.5 para sistemas legados cuando el proyecto lo documente
- Apache / PHP-FPM / Linux cuando la tarea sea de infraestructura

## Roles que suelen activarse
- Laravel Lead: rutas, controllers, middleware, policies, services, jobs, events, models, Blade.
- Database Architect: Firebird/MySQL, índices, integridad, migraciones y rendimiento.
- Sync Specialist: sincronizaciones idempotentes y auditables entre Firebird y MySQL.
- UI Specialist: Blade, Bootstrap, dark mode, responsive y JavaScript.
- Security/DevOps: auth, permisos, PHP-FPM, Apache, SSL, Linux, despliegue.

## Reglas de datos
- Tratar Firebird como solo lectura salvo autorización explícita del proyecto.
- No inferir que ausencia de registros equivale a DELETE/Baja sin una regla documentada.
- Para sincronizaciones, identificar primero claves técnicas/naturales y el alcance exacto (por ciclo, grupo, alumno, dispositivo u otra unidad del sistema).
- Buscar idempotencia y clasificar resultados como INSERT/UPDATE/UNCHANGED/DELETE cuando corresponda.

## Reglas UI
- Respetar componentes/layout existentes.
- Si el proyecto usa Bootstrap, no introducir Tailwind sin instrucción expresa.
- Separar cambios visuales de lógica de negocio.

## Regla de memoria
Registrar en `memory/DB_SCHEMA_NOTES.md` solo relaciones o restricciones que no sean obvias por migraciones/modelos. Registrar decisiones reutilizables en `DECISIONS.md`; no guardar conversaciones completas.
