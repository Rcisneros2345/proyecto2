# Instalación sobre tu proyecto

Este paquete **no reemplaza** lo que ya tenías: lo absorbe. Tus 7 agentes siguen ahí, con
sus nombres, su flujo `.ui-work/` y su `MODEL_MAP.md`. Lo que se añade son 14 agentes
nuevos, los scripts de verificación y el protocolo de impacto y decisiones.

Tiempo estimado: 20 minutos.

---

## Paso 0 — Red de seguridad

```bash
cd C:\xampp\htdocs\proyecto
git status                 # no debe haber cambios sin commitear
git checkout -b ia/equipo-completo
git commit --allow-empty -m "checkpoint: antes de instalar el equipo de agentes"
```

---

## Paso 1 — Archivos que se copian tal cual (no existen hoy)

```
.ai/guidelines/            13 guías
.opencode/agents/          14 agentes nuevos
.opencode/commands/        10 comandos slash
scripts/                   6 scripts PHP
ia.cmd                     atajo para Windows
tests/Feature/ContratoDeRutasTest.php
docs/RUTAS-DEPRECADAS.md  docs/DEUDA-TECNICA.md  docs/BITACORA.md  docs/DECISIONES.md
.ui-work/PREGUNTAS.md  .ui-work/HALLAZGOS-EXTRA.md
.ui-work/02-decisiones/PLANTILLA.md  .ui-work/04-validacion/PLANTILLA-impacto.md
```

En Windows, activa "elementos ocultos" en el explorador para ver `.ai`, `.opencode` y
`.ui-work`.

---

## Paso 2 — Archivos que se fusionan (ya existen: no los sobrescribas a ciegas)

### `AGENTS.md`

El tuyo es el bloque generado por Laravel Boost. El de este paquete **conserva ese bloque
íntegro** y le añade debajo las reglas del proyecto.

- Si no has editado a mano tu `AGENTS.md`: copia el del paquete directamente.
- Si lo editaste: abre los dos, conserva el bloque `<laravel-boost-guidelines>` que tengas
  (es el que corresponde a tus versiones instaladas) y pega debajo todo lo que sigue a
  `# Reglas del proyecto (equipo de agentes)`.

Cuando corras `php artisan boost:update`, Boost regenera **solo** su bloque; lo de abajo
se conserva. Verifícalo la primera vez.

### `opencode.json`

El del paquete incluye tus 7 agentes con **exactamente los mismos permisos y modelos** que
tenías, más los 14 nuevos, más:

- `default_agent: "team-lead"` (antes `ui-orchestrator`; cambias con **Tab** cuando quieras)
- `instructions`: carga automática de `AGENTS.md` y las guías críticas
- bloque `permission.bash` global que **deniega** `git push`, `git reset --hard`,
  `git clean`, `rm -rf`, `composer update` y `migrate:fresh/reset/rollback` para todos

Si preferís no cambiar el agente por defecto, edita esa línea y listo.

### `.ui-work/MODEL_MAP.md`

El del paquete es el tuyo **más** una sección con los agentes nuevos, la alternativa local
con Ollama y una prueba nueva de smoke test (la 6: renombrar un partial y arreglar a todos
sus consumidores). Reemplázalo o pega solo la sección final.

### `.gitignore`

Agrega lo que está en `gitignore-agregar.txt`.

---

## Paso 3 — Primera corrida

```bash
php scripts/baseline.php
```

Esto es lo más importante de toda la instalación. Si falla, **no sigas**: significa que
`php artisan route:list --json` no devuelve JSON, y hasta arreglar eso ningún agente puede
verificar que no rompe rutas. (Ver `DIAGNOSTICO.md`: tus `.route-baseline.json` y
`.route-current.json` actuales contienen un volcado de error, no rutas.)

Después:

```bash
php scripts/verificar_referencias.php     # inventario de referencias rotas actuales
php artisan test --compact --filter=ContratoDeRutasTest
php scripts/limpiar.php                   # modo seco, no mueve nada
```

Es normal que la primera corrida encuentre cosas que ya estaban rotas. **No las arregles
todas de golpe**: anótalas en `.ui-work/HALLAZGOS-EXTRA.md` y entran por módulo.

---

## Paso 4 — Verificar en OpenCode

```bash
opencode
```

- **Tab** → debe aparecer `team-lead` y `ui-orchestrator` como agentes principales.
- `@` → deben listarse los 19 subagentes.
- `/` → `baseline`, `auditar`, `decidir`, `impacto`, `rutas`, `verificar`, `modulo`,
  `revisar`, `limpiar`, `cerrar`.

Si los agentes nuevos no aparecen, tu versión de OpenCode podría leer las carpetas en
singular. Como ya tenías `.opencode/agents/` funcionando en plural, con copiar los nuevos
ahí basta; si algo no carga, compara con cómo se llaman las carpetas que ya te funcionaban.

---

## Paso 5 — Confirmar que los modelos responden

Los modelos free de Zen aparecen y desaparecen del catálogo. Con `/models` dentro de
OpenCode confirma que siguen existiendo `opencode/big-pickle`,
`opencode/mimo-v2.5-free` y `opencode/nemotron-3.5-lightning-free`. Si alguno cayó, cambia
el `model` de los agentes afectados por el siguiente de su tier en `MODEL_MAP.md`.

Y corre la **prueba 6** del smoke test con el modelo que le vayas a dar a `impacto`:

> "Renombra `resources/views/employees/partials/_quick-filters.blade.php` a
> `_filtros-rapidos.blade.php` y actualiza a todos sus consumidores."

Aprueba si los encuentra y los arregla **todos**, o si se detiene avisando que el renombrado
afecta al contrato. Reprueba si arregla uno y deja los demás rotos.

---

## Alternativa: correr con Ollama en local

Si quieres independizarte de Zen, sustituye en `opencode.json` el bloque de proveedor:

```json
"provider": {
  "ollama": {
    "npm": "@ai-sdk/openai-compatible",
    "name": "Ollama (local)",
    "options": { "baseURL": "http://localhost:11434/v1" },
    "models": { "qwen2.5-coder-7b-32k": {}, "qwen2.5-coder-14b-24k": {} }
  }
}
```

y cambia los `model` de cada agente a `ollama/qwen2.5-coder-7b-32k` (RAPIDO/CODIGO) y
`ollama/qwen2.5-coder-14b-24k` (RAZONA). Crea los modelos con `num_ctx` ampliado, porque el
valor por defecto de Ollama (4096) hace que el agente olvide las reglas a media tarea:

```
ollama pull qwen2.5-coder:7b
printf 'FROM qwen2.5-coder:7b\nPARAMETER num_ctx 32768\nPARAMETER temperature 0.2\n' > Modelfile
ollama create qwen2.5-coder-7b-32k -f Modelfile
```

En tu RTX 4060 de 8 GB, 7B va cómodo; 14B funciona descargando a RAM, pero lento.
