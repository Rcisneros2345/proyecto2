# Buenas Prácticas para Cambiar Código Sin Romper Nada (Implementer)

Objetivo: que corregir un bug o agregar algo no introduzca uno nuevo en otro lado. Esto no reemplaza a QA — es lo que hace que el trabajo de QA encuentre menos sorpresas.

## Antes de tocar una línea

1. **Entender el "blast radius" antes de cambiar código compartido.** Si la función/clase/componente que vas a tocar se usa en más de un lugar, buscar todos los usos reales antes de cambiar su comportamiento o su firma. Un cambio que "arregla" un caso puede romper otro que dependía del comportamiento anterior (aunque fuera un bug).
2. **Reproducir el problema antes de arreglarlo**, si es un bug. Si no podés reproducirlo, el fix es una suposición, no una corrección.
3. **Confirmar el patrón existente del proyecto** (leyendo `memory/CONVENTIONS.md` y código vecino) antes de escribir algo nuevo con un estilo distinto. Consistencia > preferencia personal.

## Al hacer el cambio

4. **Diffs chicos y de un solo propósito.** Un cambio que arregla un bug Y refactoriza Y reformatea es imposible de revisar bien y difícil de revertir si algo sale mal. Separar: primero el fix, después (si hace falta) el refactor, en cambios distintos.
5. **No mezclar refactor con fix.** Si mientras arreglás algo ves código feo al lado, anotalo en `memory/KNOWN_ISSUES.md` (`#deuda-tecnica`) en vez de tocarlo en el mismo cambio.
6. **Preferir extender antes que modificar comportamiento existente**, cuando ambas opciones resuelven el problema. Agregar un caso nuevo a una función rompe menos que cambiar el comportamiento de un caso que ya funcionaba.
7. **Compatibilidad hacia atrás por default.** Si el cambio toca algo que otras partes del sistema (u otros clientes de una API) consumen, mantener la forma vieja funcionando (aunque sea marcada como deprecated) salvo que el usuario autorice explícitamente un cambio disruptivo.
8. **Defensive checks en los bordes, no en todos lados.** Validar datos que vienen de afuera (input de usuario, respuesta de API externa, fila de base de datos que puede ser null) en el punto de entrada. No hace falta blindar cada función interna contra datos que el propio sistema ya garantizó.
9. **No silenciar errores para que "pase".** Un `try/catch` vacío o un `null` devuelto para evitar que algo explote esconde el problema en vez de arreglarlo — casi siempre reaparece después, más difícil de rastrear.

## Antes de dar por terminado

10. **Revisar el diff completo vos mismo**, no solo el fragmento que cambiaste a propósito — a veces un IDE/herramienta toca formato de líneas que no pediste.
11. **Buscar los tests que cubren lo que tocaste** y correrlos. Si no hay tests para esa parte, es una señal (no bloqueante) de que convendría agregar al menos uno que cubra el bug corregido — así no vuelve a aparecer sin que nadie lo note.
12. **Pensar en el caso vecino, no solo en el caso que reportaron.** Si el bug era "falla con fecha vacía", chequear también fecha inválida, fecha futura, fecha en otro formato — el mismo tipo de entrada rota de formas parecidas.
13. **Si el cambio es reversible fácilmente (commit chico, sin migración destructiva), preferirlo** sobre uno "perfecto" pero grande y difícil de deshacer si algo sale mal.

## Señales de alarma (parar y avisar, no seguir)

- El cambio "mínimo" terminó tocando más de 5-6 archivos sin relación directa entre sí → el alcance real es mayor al estimado en Fase A, volver a triage.
- Tuviste que desactivar o comentar un test para que pase → no está arreglado, está escondido.
- No podés explicar en una frase por qué el bug pasaba → todavía no entendiste la causa raíz, seguís en Fase B (Descubrimiento).
