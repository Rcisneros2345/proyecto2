# Detección de Stack (sin leer el repo completo)

Objetivo: llenar `memory/PROJECT.md` en minutos, leyendo manifiestos y configs puntuales, no código de negocio.

## Paso 1 — Identificar lenguaje/gestor de dependencias

Buscar (existencia, no contenido completo) en la raíz:

| Archivo presente | Ecosistema probable |
|---|---|
| `composer.json` | PHP (Laravel, Symfony, etc.) |
| `package.json` | Node/JS/TS (revisar `dependencies` para framework: Express, Next, Nest, React, Vue...) |
| `requirements.txt` / `pyproject.toml` / `Pipfile` | Python (Django, Flask, FastAPI...) |
| `go.mod` | Go |
| `pom.xml` / `build.gradle` | Java/Kotlin |
| `Gemfile` | Ruby (Rails probable) |
| `Cargo.toml` | Rust |
| `*.csproj` / `*.sln` | .NET |
| `pubspec.yaml` | Dart/Flutter |

Si hay varios (monorepo, backend + frontend separados), documentar cada uno por separado en `PROJECT.md`.

## Paso 2 — Identificar framework y versión

Leer solo la sección relevante del manifiesto (no todo el árbol de dependencias):
- `composer.json` → `require.laravel/framework`, `require.symfony/*`.
- `package.json` → `dependencies`/`devDependencies` para `next`, `react`, `vue`, `express`, `@nestjs/core`.
- `pyproject.toml`/`requirements.txt` → `django`, `flask`, `fastapi`.

## Paso 3 — Identificar base(s) de datos

No asumir. Buscar en, en este orden:
1. Archivos de config de entorno (`.env.example`, `config/database.*`, `settings.py`, `application.yml`) — nunca leer `.env` real si contiene secretos, solo `.env.example` o el nombre de las variables.
2. Carpeta de migraciones (`migrations/`, `database/migrations/`) para confirmar el motor.
3. Si hay más de un motor (ej. Firebird + MySQL, o Postgres + Redis), anotar ambos y para qué se usa cada uno — esto es crítico para Database Architect y no debe reinvestigarse cada vez.

## Paso 4 — Identificar sistema de pruebas

Buscar `phpunit.xml`, `pytest.ini`/`pyproject.toml [tool.pytest]`, `jest.config.*`, `vitest.config.*`, carpeta `tests/` o `spec/`. Anotar el comando real para correr pruebas (ej. `php artisan test`, `pytest`, `npm test`) en `CONVENTIONS.md` bajo "Tests" para no tener que redescubrirlo.

## Paso 5 — Identificar CI/CD e infraestructura

Buscar `.github/workflows/`, `.gitlab-ci.yml`, `Dockerfile`, `docker-compose.yml`, `Procfile`. Si existen, anotar en `PROJECT.md` que hay pipeline/infra definida, sin necesidad de leer cada paso salvo que la tarea sea de DevOps.

## Qué escribir en PROJECT.md

```
## Stack
- Lenguaje/Framework: <ej. PHP 8.2 / Laravel 10.50.3>
- Gestor de dependencias: <ej. Composer>
- Frontend (si aplica): <ej. Blade + Alpine.js>

## Arquitectura
- Patrón: <ej. MVC estándar de Laravel, con Service classes en app/Services>
- Particularidades: <ej. multi-tenant, colas, eventos>

## Bases de datos / integraciones externas
- <ej. MySQL como principal, Firebird 2.5 como sistema legado con sincronización vía job programado>
- <ej. integración con API de terceros X para pagos>
```

No hace falta más detalle que este para empezar a trabajar eficientemente — el resto se descubre bajo demanda por tarea.
