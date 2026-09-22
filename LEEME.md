# Continuación después de las auditorías — 2026-09-19

Revisé tu proyecto actualizado y ejecuté los scripts contra el código real (instalé PHP para
poder correrlos, así que lo que hay aquí está respaldado por salidas reales).

## Qué leer, en este orden

| Archivo | Qué es |
|---|---|
| **`PLAN-DE-CONTINUACION.md`** | El plan completo: dónde estás, qué encontré, las 5 fases en orden, cadencia y métricas |
| **`PARCHES.md`** | Dos fallos de los scripts que ya corregí, con el antes y el después medido |
| **`PLAN-UI.md`** | Cómo mejorar el diseño: activar tu spec olvidada + oleadas + quick wins |
| `docs/DEUDA-TECNICA.md` | Las tres auditorías consolidadas en una sola tabla verificada, con responsable |
| `.ui-work/02-decisiones/` | Tres fichas listas para que elijas: D-001 stubs, D-002 permisos, D-003 UI |

## Qué copiar al proyecto

```cmd
copy parches\scripts\*.php scripts\
copy parches\tests\Feature\ContratoDeRutasTest.php tests\Feature\
xcopy /E /I docs docs
xcopy /E /I .ui-work .ui-work
php scripts/baseline.php
```

`parches/scripts/` trae `baseline.php`, `comparar_rutas.php`, `verificar_referencias.php`
corregidos y `permisos.php`, que es nuevo.

## Los tres números de hoy

```
Referencias rotas reales     2      (eran 13 con falsos positivos)
Rutas con permiso detectadas 120    (el script veía 0: la verificación pasaba en falso)
Métodos stub enrutados       10     ← esto es lo urgente
```

## Lo más importante en una frase

Hay diez métodos de controller enrutados que devuelven éxito sin hacer nada: copiar huella,
borrar huella, subir huellas, quitar del dispositivo, sincronizar hora, limpiar asistencias
y restaurar equipo. La interfaz confirma que salió bien y el equipo no cambió. Ninguna de las
tres auditorías lo detectó, porque un inventario lee firmas, no cuerpos.

Empieza por ahí: `.ui-work/02-decisiones/D-001-controllers-stub.md`.
