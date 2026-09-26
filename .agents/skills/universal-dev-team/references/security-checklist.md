# Checklist de Seguridad (Security Engineer)

Usar la sección que corresponda al tipo de cambio. No hace falta pasar por todas si la tarea es acotada — pero la sección relevante es obligatoria, no opcional.

Referencia rápida a OWASP Top 10 — las secciones de abajo cubren, en este orden: control de acceso roto (Autorización), fallos criptográficos (Autenticación), inyección (Validación de entrada), diseño inseguro (todas), mala configuración de seguridad (Infra/Despliegue), componentes vulnerables (Dependencias), fallos de identificación (Autenticación), fallos de integridad de software/datos (Dependencias + Datos sensibles), fallos de logging/monitoreo (Datos sensibles), SSRF (Validación de entrada, si el proyecto hace requests salientes con input de usuario).

Cualquier hallazgo de esta checklist en un cambio a producción es, por definición, riesgo **Alto** según la tabla de "Niveles de riesgo y aprobación" del `SKILL.md` — dispara Tier 3 de verificación.

## Autenticación

- ¿Las contraseñas se hashean con un algoritmo moderno (bcrypt/argon2), nunca en texto plano ni con MD5/SHA1 sin salt?
- ¿Hay límite de intentos de login (rate limiting / lockout)?
- ¿Los tokens de sesión/API se generan con suficiente entropía y expiran?
- ¿El flujo de "olvidé mi contraseña" invalida el token después de un solo uso y expira en poco tiempo?
- ¿Se cierra la sesión realmente (invalidación server-side), no solo se borra el token del cliente?

## Autorización

- ¿Cada endpoint/acción verifica que el usuario autenticado tiene permiso sobre *ese recurso específico*, no solo que está logueado? (evitar IDOR: que el usuario A pueda editar el recurso del usuario B cambiando un ID en la URL)
- ¿Los roles/permisos se verifican en el backend, no solo se ocultan en el frontend?
- ¿Las rutas administrativas están protegidas por middleware/guard, no por "nadie va a adivinar la URL"?

## Validación y sanitización de entrada

- ¿Toda entrada de usuario se valida en el backend (tipo, longitud, formato), no solo en el frontend?
- ¿Las consultas a base de datos usan parámetros/prepared statements, nunca concatenación de strings con input del usuario (SQL injection)?
- ¿El output que se renderiza en HTML escapa contenido generado por usuarios (XSS)? Cuidado especial con campos "rich text" o markdown.
- ¿Los uploads de archivos validan tipo real (no solo extensión), tamaño máximo, y se guardan fuera de la carpeta ejecutable cuando el stack lo permite?
- ¿Hay protección CSRF en formularios/acciones que cambian estado, si el framework no la da por defecto?

## Datos sensibles

- ¿Se identificó qué datos son sensibles (contraseñas, datos personales, datos financieros, datos de salud) antes de decidir cómo se guardan/loguean?
- ¿Los logs de la aplicación evitan imprimir contraseñas, tokens o datos personales completos?
- ¿Las claves/API keys/secretos viven en variables de entorno o un vault, nunca hardcodeadas en el código ni commiteadas al repositorio?
- ¿La comunicación con servicios externos y con el cliente usa HTTPS/TLS?

## Rate limiting y abuso de endpoints

No es solo el login: cualquier endpoint costoso o automatizable sin límite es explotable ("Unrestricted Resource Consumption", top 10 API).

- ¿Los endpoints que envían email/SMS, generan reportes, exportan datos o hacen requests salientes tienen rate limit, no solo el login?
- ¿Hay límite de tamaño en payloads/uploads para evitar abuso de recursos (memoria/disco)?
- ¿Un usuario autenticado puede automatizar una acción legítima (ej. crear cientos de recursos por minuto) de forma dañina para otros usuarios o el sistema?

## Security headers

- ¿Respuestas HTTP incluyen `Content-Security-Policy`, `X-Content-Type-Options: nosniff`, `X-Frame-Options`/`frame-ancestors`?
- ¿`Strict-Transport-Security` (HSTS) está activo en producción (HTTPS siempre, sin fallback a HTTP)?
- ¿Cookies de sesión tienen `HttpOnly`, `Secure` y `SameSite` apropiados?

## Integridad de dependencias (supply chain)

- ¿`composer.lock` / `package-lock.json` están commiteados y el build/deploy instala con el lockfile (`composer install --no-dev`, `npm ci`), no resolviendo versiones nuevas en cada deploy?
- ¿Se verifica el origen de paquetes poco conocidos antes de agregarlos (mantenedor real, no un paquete typosquatted)?

## Dependencias

- ¿Se está agregando una dependencia nueva? Si sí: ¿tiene mantenimiento activo, sin vulnerabilidades conocidas críticas/altas sin parchear?
- ¿El proyecto tiene forma de auditar dependencias (`composer audit`, `npm audit`, `pip-audit`, etc.)? Si existe, correrla cuando se toquen dependencias.

## Logging y alertas (no solo "qué no loguear")

- ¿Las acciones sensibles (login fallido repetido, cambio de permisos/rol, borrado de datos) quedan registradas con quién/cuándo, no solo las exitosas?
- ¿Hay alguna forma de enterarse si algo falla en producción (alerta, log agregado, monitoreo), o solo se sabe si el usuario reporta? Ver `references/observability-checklist.md` si la tarea es de DevOps.

## PHP / Laravel — errores específicos del framework

- ¿Los modelos Eloquent usan `$fillable` (allowlist) en vez de `$guarded = []`, para evitar mass assignment (que un request arbitrario pise `is_admin`, `role`, `id`, etc. vía `create()`/`update()` masivo)?
- ¿`.env` está en `.gitignore` y no se commiteó nunca una versión real (solo `.env.example` con nombres de variable, sin valores)?
- ¿Rutas de desarrollo (`Telescope`, `Horizon`, `_debugbar`, `/phpinfo`) están deshabilitadas o protegidas por auth en producción?
- ¿Blade escapa por defecto (`{{ }}`) y solo se usa `{!! !!}` cuando el contenido ya fue sanitizado explícitamente?

## Infra/Despliegue (coordinar con DevOps)

- ¿Los entornos de staging/producción no exponen herramientas de debug (`APP_DEBUG=true`, stack traces detallados) al usuario final?
- ¿CORS está configurado de forma específica, no `*` abierto, cuando hay credenciales/cookies involucradas?
- ¿Las variables de entorno de producción no se filtran a repos públicos ni a logs?

### Apache

- ¿`ServerTokens Prod` y `ServerSignature Off` para no anunciar versión exacta del servidor?
- ¿Listado de directorios deshabilitado (`Options -Indexes`)?
- ¿`.htaccess`/config de vhost bloquea acceso directo a `.env`, `.git`, `storage/`, `vendor/`?

### Docker

- ¿El contenedor corre como usuario no-root (`USER` explícito en el Dockerfile), no como root por default?
- ¿`.dockerignore` excluye `.env`, claves, `.git`, para que no terminen dentro de la imagen?
- ¿La imagen de producción usa un tag de versión fijo, no `latest`, para builds reproducibles?
- ¿El socket de Docker (`/var/run/docker.sock`) no está montado dentro de contenedores que no lo necesitan?

## Al cerrar la tarea

Registrar en `memory/SECURITY_NOTES.md` (sin secretos reales, solo referencias):
- qué se revisó de esta lista,
- qué quedó pendiente o fue aceptado como riesgo conocido (y por qué),
- próxima revisión sugerida si aplica.
