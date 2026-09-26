# Security Notes

Nunca guardar aquí contraseñas, tokens, claves API ni datos personales reales — solo referencias a dónde viven y cómo se rotan.

## 2026-09-26 — Auditoría Inicial de Arquitectura y Configuración
- **Qué se revisó:** Configuración de autenticación, autorización, protección CSRF, manejo de variables de entorno y conexión con bases de datos y hardware biométrico.
- **Hallazgos:**
  - **Autenticación y Tokens:** Laravel Sanctum instalado (`laravel/sanctum: ^3.2`) gestiona tokens y sesiones web con protección de cookies segura.
  - **Autorización y Roles:** Estructura de roles en tabla `users` asegurada por constraint a nivel base de datos (`2026_09_05_202614_add_check_constraint_role_to_users_table.php`), complementada con sistema de permisos granulares (`permissions`, `permission_groups`, `modules`).
  - **Protección CSRF:** Implementada de forma global en solicitudes Blade (`@csrf`) y en peticiones AJAX (`X-CSRF-TOKEN` inyectado automáticamente en `resources/js/app.js` y `resources/js/bootstrap.js`).
  - **Datos Biométricos y Personales:** La tabla `fingerprints` y `employees` contienen datos sensibles. Existe una migración previa de encriptación (`2024_01_01_000008_encrypt_sensitive_data.php`). Cualquier consulta o exportación debe respetar el principio de mínimo privilegio.
  - **Aislamiento de Firebird 2.5:** Conexión configurada en `config/database.php`. Debe mantenerse estrictamente como lectura (`SELECT`) para evitar corromper el sistema institucional legacy UTE.
  - **Secretos:** Almacenados exclusivamente en `.env` (nunca versionados en Git).
- **Riesgos aceptados conscientemente:**
  - Comunicación con hardware ZKTeco a través de la red local sin cifrado nativo de protocolo ZK (limitación del firmware del fabricante). Mitigación: Relojes checadores deben estar en una VLAN o subred privada no accesible desde el exterior.
- **Próxima revisión sugerida:**
  - Antes de publicar cualquier endpoint nuevo de exportación de datos biométricos, API pública o cambio en el sistema de permisos.
