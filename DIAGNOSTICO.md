# Diagnóstico del proyecto (revisión del 2026-09-18)

Lo que encontré al abrir el repositorio, ordenado por lo que más te afecta. No cambié nada:
esto es material para que decidas, no tareas ya ejecutadas.

---

## 1. El guardián de rutas llevaba tiempo sin guardar nada

`.route-baseline.json` y `.route-current.json` **no contienen rutas**. Contienen, en UTF-16
con BOM, el volcado de un error:

```
ReflectionException
Class "ModuleController" does not exist
at vendor\laravel\framework\src\Illuminate\Foundation\Console\RouteListCommand.php:225
```

Dos problemas encadenados:

1. **La redirección de PowerShell escribe UTF-16.** `php artisan route:list > archivo`
   produce un archivo que `json_decode()` no puede leer. Aunque el comando hubiera
   funcionado, la comparación habría fallado.
2. **Se guardó la salida sin comprobar que fuera JSON.** Si artisan falla, el archivo guarda
   el error y el "baseline" queda envenenado sin que nadie se entere. Tu agente
   `route-safety` compara contra eso.

`ModuleController` sí existe hoy y está correctamente importado en `routes/web.php:28`, así
que el error es viejo — probablemente de cuando `list_routes.bat` corría contra
`C:\xampp\htdocs\laravelrelojnew`, que es otro directorio.

**Qué hacer:** correr `php scripts/baseline.php`. Valida que la salida sea JSON antes de
guardar, escribe UTF-8 y, si artisan falla, se detiene con un mensaje claro en vez de
guardar basura. Primer paso de la instalación, antes que cualquier otra cosa.

---

## 2. Dos scripts de la raíz apuntan a sitios equivocados

| Archivo | Contenido | Problema |
|---|---|---|
| `list_routes.bat` | `cd C:\xampp\htdocs\laravelrelojnew` | Ruta de otro proyecto (el anterior a la fusión) |
| `runtest.cmd` | `php vendor\bin\phpunit.php --version` | El binario es `vendor\bin\phpunit`, sin `.php` |

Están rastreados por git, así que ningún agente los va a tocar: quedan marcados
`[REVISIÓN MANUAL]`. La decisión de corregirlos o retirarlos es tuya. Con
`php scripts/verificar.php` ya no hacen falta.

---

## 3. Archivos sueltos en la raíz

- **`diseño`** (sin extensión, 17 KB) — es una especificación real y valiosa: "UX sistema
  completo", con frontmatter y plan por oleadas para las ~82 vistas. **No es basura.**
  Debería vivir en `docs/ui/UX-sistema-completo.md` para que los agentes de UI la
  encuentren; hoy, sin extensión y en la raíz, nadie la lee.
- **`query`** — 6 bytes con la palabra `mysql`. Candidato a cuarentena.
- **`OPTIONAL-opencode.jsonc`** — apunta a un `default_agent: "team-lead"` que no existía.
  Ahora sí existe: ese archivo ya no hace falta.

---

## 4. Dos fuentes de verdad que se citan y no existen

`route-safety` cita `docs/ui/UI_ROUTE_MAP.md` y `janitor` cita `docs/ui/UI_COMPONENTS.md`
como criterio de decisión. Ninguno de los dos existe; solo está
`docs/ui/UI_DESIGN_PRINCIPLES.md`. La auditoría del 2026-09-16 ya lo señalaba.

Mientras no existan, esos dos agentes deciden con un criterio vacío. Están en
`docs/DEUDA-TECNICA.md` como DT-07 y el agente `documentador` los tiene asignados.

---

## 5. Riesgos técnicos abiertos que ya conocías

De `auditoria_completa.md`, incorporados a `docs/DEUDA-TECNICA.md` (DT-01 a DT-06) y
repartidos entre los agentes que corresponden:

| ID | Riesgo | Agente responsable |
|---|---|---|
| DT-01 | `sync_all` corre el ciclo antes que los catálogos → falla por FK en base vacía | `db-firebird` |
| DT-02 | `syncCatalogTableChunked()` carga todo MySQL en memoria | `db-mysql` + `db-firebird` |
| DT-03 | Claves naturales sin normalizar vs. `utf8mb4_unicode_ci` → duplicados y omisiones silenciosas | `db-mysql` |
| DT-04 | `executePending()` sin claim atómico; borra jobs por `LIKE` del payload | `db-firebird` |
| DT-05 | `CURSOS_DET` con `clave_asignatura` ambigua frente a `materias` | `academia` + `db-mysql` |
| DT-06 | Paginación no universal | `backend` |

DT-03 y DT-04 son los que pueden corromper datos en silencio. Yo empezaría por ahí, pero
con la opción "solo detectar y avisar" primero: cuesta poco y detiene el daño mientras se
decide el arreglo de fondo. Eso es exactamente lo que el agente `decisiones` te va a
plantear cuando abras el módulo.

---

## 6. Nota de seguridad sobre el zip

El archivo que subiste incluye `.env` con credenciales reales (está bien ignorado en git,
pero viajó dentro del comprimido). Si ese zip salió de tu máquina hacia algún otro lado,
conviene rotar la contraseña de base de datos y `APP_KEY`. Para compartir el proyecto en el
futuro:

```bash
git archive --format=zip HEAD -o proyecto.zip
```

Eso exporta solo lo versionado: sin `.env`, sin `vendor/`, sin `node_modules/`.

---

## Orden que yo seguiría

1. `php scripts/baseline.php` — hasta que esto funcione, nada más importa.
2. `php scripts/verificar_referencias.php` — foto del estado real de las referencias.
3. Mover `diseño` a `docs/ui/` y decidir sobre `list_routes.bat` / `runtest.cmd`.
4. `/modulo AUTH_RBAC` completo, de punta a punta, para rodar el pipeline en un módulo con
   buena cobertura de pruebas.
5. Después DT-04 y DT-03, que son los que corrompen datos.

No empieces por sincronización: es el módulo más crítico y el que más contexto necesita.
