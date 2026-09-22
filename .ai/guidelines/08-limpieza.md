# 08 · Limpieza

## Principio

**Nunca se borra: se mueve a `.ui-work/_cuarentena/<AAAA-MM-DD>/`**, conservando la ruta
original. El humano vacía la cuarentena cuando quiera. Ningún agente usa `rm` ni `git clean`.

Y jamás se toca un archivo **rastreado por git**: si parece basura, se **reporta**.

```bash
php scripts/limpiar.php            # modo seco
php scripts/limpiar.php --apply    # mueve a cuarentena
```

## Qué se busca

**Archivos:** `*.bak *.old *.orig *.rej *.tmp *~ *_v2.php *_nuevo.* *-copia.*`,
`test.php prueba.php temp.php borrar.php untitled*`, `routes_output.txt debug.log dump.sql`.

**En el código:** `dd()`, `dump()`, `var_dump()`, `print_r()`, `ray()`, `console.log()`,
`debugger`, `TODO/FIXME/HACK` viejos, `use` sin utilizar, código comentado "por si acaso",
`markTestSkipped()` olvidados.

**Huérfanos:** componentes, vistas, CSS y JS sin consumidores — con las cinco búsquedas de
verificación, incluida la de referencias dinámicas.

## Nunca tocar

`.env*`, `storage/`, `vendor/`, `node_modules/`, `public/build/`, `.git/`,
`database/migrations/`, `tests/`, `deploy.sh`.

## Higiene del repositorio

- Artefactos de trabajo de la IA: `.ui-work/` o `docs/`. Nunca sueltos en la raíz ni en `app/`.
- `.gitignore` debe cubrir `.ui-work/_cuarentena/`, `.ai/baseline/`, `routes_output.txt`.
- Al cerrar un módulo, `git status` no debe mostrar archivos no rastreados inesperados.
