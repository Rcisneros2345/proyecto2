<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.33
- laravel/framework (LARAVEL) - v10
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v10

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v10 rules ===

## Laravel 10

- Use the `search-docs` tool to get version-specific documentation.
- Middleware typically live in `app/Http/Middleware/` and service providers in `app/Providers/`.
- Laravel 10 has a `bootstrap/app.php` file that creates the application instance and binds kernel contracts, but does not use it for application configuration like Laravel 11:
    - Middleware registration is in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule registration is in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`
- When using Eloquent model casts, you must use `protected $casts = [];` and not the `casts()` method. The `casts()` method isn't available on models in Laravel 10.

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== phpunit/core rules ===

## PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
</laravel-boost-guidelines>

---

# Reglas del proyecto (equipo de agentes)

> El bloque de arriba lo genera Laravel Boost y no se edita a mano: `php artisan boost:update`
> lo regenera. Todo lo de aquí abajo es del equipo y sí se mantiene a mano.

## Qué es este proyecto

Aplicación Laravel 10 / PHP 8.3 que fusiona tres sistemas:

| Dominio | Qué hay | Piezas clave |
|---|---|---|
| **Checador** | Empleados, dispositivos ZKTeco, huellas, asistencias | `ZktecoService`, `EmployeeDeviceSyncService`, `SyncDeviceJob`, `SyncEmployeeToDeviceJob`, `Device`, `Fingerprint`, `Pivots\DeviceEmployee`, `DeviceSync(Item)` |
| **Academia** | Ciclos, planes, cursos, grupos, alumnos, profesores, horarios, kárdex | `CicloActualService`, `HorarioResolver`, `KardexCalculator`, `PersonaContratosResolver`, modelos en `App\Models\Academia\*` |
| **RBAC / plataforma** | Módulos, permisos, grupos de permisos, navegación, preferencias | `PermissionResolver`, `RequireModulePermission`, `Module`, `Permission`, `PermissionGroup`, `NavigationItem`, `AdminLayoutComposer` |

Fuentes de datos:

- **MySQL** — base operativa de la aplicación.
- **Firebird** — fuente de verdad de empleados y del catálogo académico. **SOLO LECTURA.**
  Se lee vía `FirebirdReader` y las estrategias de `app/Services/SyncStrategies/`
  (`FullSyncStrategy`, `CatalogSmartSync`, `CycleDirectSync`, `CustomSyncStrategy`).
- **Dispositivos ZKTeco** — hardware real, multi-dispositivo.

## Carga de contexto (lazy loading)

CRÍTICO: cuando veas una referencia tipo `@.ai/guidelines/07-no-romper.md`, léela con tu
herramienta de lectura **solo cuando la tarea lo requiera**. Al cargarla, su contenido es
instrucción obligatoria.

| Cuando trabajes en… | Lee primero |
|---|---|
| Cualquier tarea | `@.ai/guidelines/00-reglas.md` |
| **Antes de tocar rutas, vistas, firmas o columnas** | `@.ai/guidelines/07-no-romper.md` |
| **Después de cambiar algo** (obligatorio) | `@.ai/guidelines/10-impacto.md` |
| Decidir entre alternativas | `@.ai/guidelines/11-decisiones.md` |
| Estructura, capas, servicios | `@.ai/guidelines/01-arquitectura.md` |
| Consultas, índices, migraciones | `@.ai/guidelines/02-mysql.md` |
| Firebird y estrategias de sync | `@.ai/guidelines/03-firebird.md` |
| Dispositivos, huellas, asistencias | `@.ai/guidelines/04-zkteco-sync.md` |
| Blade, CSS, componentes | `@.ai/guidelines/05-ui.md` + `docs/ui/UI_DESIGN_PRINCIPLES.md` |
| Pruebas | `@.ai/guidelines/06-testing.md` |
| Permisos, módulos, menú | `@.ai/guidelines/12-rbac.md` |
| Basura y temporales | `@.ai/guidelines/08-limpieza.md` |
| Protocolo entre agentes | `@.ai/guidelines/09-protocolo-agentes.md` |

## Comandos del proyecto

```bash
php artisan test --compact                      # suite completa
php artisan test --compact --filter=NombreTest  # una prueba
vendor/bin/pint --dirty                         # formato (Boost manda: no uses --test)

php scripts/baseline.php          # congela el contrato: rutas, vistas, componentes, assets
php scripts/comparar_rutas.php    # ¿desapareció o cambió una ruta?
php scripts/verificar_referencias.php  # route()/view()/@include/<x-comp>/asset() rotos
php scripts/impacto.php           # ★ qué más se ve afectado por lo que acabas de cambiar
php scripts/verificar.php         # puerta de calidad completa
php scripts/limpiar.php           # limpieza a cuarentena (modo seco por defecto)
```

En Windows hay atajo: `ia.cmd baseline | impacto | verificar | limpiar`.

> **No uses `php artisan route:list > archivo`** desde PowerShell: escribe UTF-16 con BOM
> y el JSON queda ilegible. Usa siempre `php scripts/baseline.php`, que escribe UTF-8 y
> valida que la salida sea JSON real antes de guardarla.

## Las 12 reglas irrompibles

1. **No modificar lo que no entiendes.** Antes de editar: modelo, relaciones, controller,
   request, policy, middleware de módulo, ruta, vista y pruebas asociadas.
2. **No inventar reglas de negocio.** Duda → `.ui-work/PREGUNTAS.md` y se pregunta.
3. **No borrar** código, archivos, rutas ni columnas sin las cuatro búsquedas de evidencia
   de `@.ai/guidelines/07-no-romper.md`.
4. **No romper el contrato público**: nombres de ruta, URIs, métodos, parámetros, nombres
   de vista y de componente, nombres de columna, claves de permiso (`module_permission:x,y`).
5. **Cambios pequeños y verificables.** Un cambio = un propósito.
6. **Antes de un cambio grande**: problema → archivos afectados → riesgo → opciones →
   espera decisión humana. Ver `@.ai/guidelines/11-decisiones.md`.
7. **Después de CADA cambio**: `php scripts/impacto.php` y arreglar lo que quedó afectado.
   Un cambio que rompe a sus consumidores no está terminado, está a medias.
8. **No duplicar lógica.** Ya existen `CicloActualService`, `HorarioResolver`,
   `KardexCalculator`, `PermissionResolver`, `SobranteService`, `EmployeeDeviceSyncService`,
   `FirebirdReader`, `PersonaContratosResolver`. Búscalos antes de escribir uno nuevo.
9. **Firebird es SOLO LECTURA.** Ninguna escritura, nunca, ni "solo para probar".
10. **Nada destructivo sin autorización**: `migrate:fresh`, `migrate:rollback`, `db:wipe`,
    `DROP`, `TRUNCATE`, `DELETE` sin `WHERE`, `rm -rf`, `git reset --hard`, `git clean`,
    `git push`, `composer update`, y borrar usuarios o huellas en un dispositivo físico.
11. **Toda ruta lleva `->name()`** y todo enlace usa `route('nombre')`.
12. **Una tarea no termina porque el código corra.** Termina cuando `php scripts/verificar.php`
    está en verde, hay prueba nueva, y `impacto` no deja consumidores rotos.

## Proceso

```
CHECKPOINT GIT → AUDITAR → OPCIONES (decides tú) → IMPLEMENTAR
→ IMPACTO (revisar y arreglar lo afectado) → PROBAR → RUTAS → REVISAR
→ LIMPIAR → DOCUMENTAR → COMMIT
```

Módulos, en orden de trabajo sugerido:

`AUTH_RBAC` → `EMPLEADOS` → `DISPOSITIVOS` → `HUELLAS` → `ASISTENCIAS` → `INCIDENCIAS`
→ `FIREBIRD_SYNC` → `ACADEMIA_CATALOGOS` → `ACADEMIA_HORARIOS` → `ACADEMIA_KARDEX`
→ `OPERACIONES` → `NAVEGACION_UI`

## Equipo

Orquestador general: `team-lead` (Tab para cambiar de agente principal).
El flujo de UI existente sigue vivo con `ui-orchestrator`.

| Agente | Rol | Escribe |
|---|---|---|
| `team-lead` | Orquesta todo el proyecto, exige evidencia | no |
| `decisiones` | Convierte problemas en opciones comparadas para que elijas | solo `.ui-work/02-decisiones/` |
| `auditor` | Inventario y trazado end-to-end (backend + vistas) | solo reportes |
| `arquitecto` | Estructura, duplicación, acoplamiento | solo reportes |
| `backend` | Controllers, services, requests, jobs, comandos | sí |
| `db-mysql` | Esquema, índices, migraciones, N+1 | sí |
| `db-firebird` | Lectura y estrategias de sincronización | sí (lado MySQL) |
| `zkteco` | Dispositivos, huellas, asistencias | sí |
| `academia` | Ciclos, horarios, kárdex, inscripciones | sí |
| `rbac` | Módulos, permisos, grupos, menú | sí |
| `impacto` | ★ Tras cada cambio: quién más se ve afectado y lo arregla | sí, acotado |
| `route-safety` | Dictamen de rutas y contrato público | no |
| `ui-orchestrator` | Orquesta el ciclo de UI (existente) | solo `.ui-work/` |
| `ui-auditor` | Inventario de UI | solo `.ui-work/00-auditoria/` |
| `ui-designer` | Propuestas de rediseño | solo `.ui-work/01-propuestas/` |
| `ui-implementer` | Aplica propuestas aprobadas | sí, solo presentación |
| `code-fixer` | Diagnostica y corrige errores | sí, acotado |
| `qa` | Rompe lo que hicieron los demás, escribe pruebas | solo `tests/` |
| `revisor` | Veto final sobre el diff | no |
| `janitor` | Basura, huérfanos, cuarentena | solo mover a cuarentena |
| `documentador` | Bitácora, decisiones, docs | solo `docs/` y `.ui-work/` |

Para invocar a otro agente desde un agente, se menciona **por nombre, sin `@`**.
El `@` es solo para tu invocación manual.
