# 10 · Protocolo de impacto (obligatorio tras cada cambio)

> Si desarrollas algo, revisas y arreglas lo que ese algo afecta. Un cambio que funciona en
> su pantalla pero deja rotos a sus consumidores **no está terminado**.

## Los cuatro pasos

### 1. Qué cambió
```bash
git diff --name-only HEAD
git diff HEAD
```

### 2. Quién depende de eso
```bash
php scripts/impacto.php
```

El script deduce los símbolos públicos de lo que tocaste y busca sus consumidores en
`app/ routes/ resources/ tests/ config/ database/`. Su salida es una lista de
`archivo:línea`, no una opinión.

**Lo que el script NO puede ver** (búscalo tú):
- `view($variable)`, `@include($vista)`, nombres construidos por concatenación.
- Nombres de clase resueltos por contenedor o por string.
- URLs armadas en JavaScript.
- Consumidores fuera del repo: reportes externos, integraciones, favoritos del usuario.

### 3. Revisa cada consumidor de verdad
Por cada uno: ¿sigue recibiendo lo que espera? ¿existe el campo o método que usa? ¿cambió
el orden, el formato, el valor por defecto? ¿su prueba lo cubre?

Abre el archivo. "Parece que sí" no cuenta.

### 4. Arregla y verifica
```bash
php scripts/verificar_referencias.php
php scripts/comparar_rutas.php
php artisan test --compact
```

## Tabla rápida: qué revisar según lo que tocaste

| Tocaste… | Revisa también |
|---|---|
| Vista | `view('x')`, `@include`, `@extends`, `Route::view`, pruebas que la esperan |
| Componente `<x-y>` | todos sus usos **y** los atributos/slots que cada uno pasa |
| Partial `_algo` | cada `@include`, con las variables que cada llamador define |
| Controller | rutas que lo apuntan, `->route()` de redirección, pruebas Feature |
| Método público de Service | todos los llamadores: controllers, jobs, comandos, otros services |
| Modelo / `$casts` | vistas que formatean el campo, exportaciones, factories, seeders |
| Columna | `$fillable`, queries, vistas, seeders, pruebas, migraciones posteriores |
| Job | quién lo despacha, la vista que muestra su progreso, reintentos en cola |
| Evento (`SyncProgressUpdated`) | listeners, broadcasting, el JS/Blade que lo escucha |
| Clave de permiso | rutas, seeders, `NavigationItem`, `PermissionResolver`, matriz RBAC, caché |
| CSS/JS | `@vite`, `@push('scripts')`, `asset()`, vistas que lo cargan |
| Ruta | **todo lo anterior** + `comparar_rutas.php` |

## Límites

- Arreglas lo que **tu** cambio rompió. Lo que ya estaba roto va a `HALLAZGOS-EXTRA.md`.
- Si un consumidor exige tocar rutas → `[BLOQUEADO: ROUTE SAFETY]`.
- Si exige lógica de negocio fuera de tu ámbito → `[GAP: BACKEND]` con el detalle exacto.
- **Si aparecen más de 10 consumidores afectados: para.** El cambio estaba mal dimensionado;
  vuelve a `decisiones` y replantéalo.

## Reporte

`.ui-work/04-validacion/impacto-<modulo>-<fecha>.md` con: cambio original, tabla de
consumidores (afectado sí/no y acción), lo arreglado, lo escalado y la verificación final.
