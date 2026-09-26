# Checklist de Verificación por Tipo de Cambio (QA / Test Engineer)

## Regla general

Ejecutar primero las pruebas más cercanas al cambio (unitarias del módulo tocado); ampliar a la suite completa solo si el costo en tiempo/tokens es razonable para el tamaño del cambio. Nunca declarar "probablemente funciona" como equivalente a "se verificó".

Esta checklist se aplica con la profundidad que indique el Tier asignado en Fase A del `SKILL.md` (Tier 1 Superficial / Tier 2 Estándar / Tier 3 Profunda) — no todas las secciones de abajo aplican siempre; las de seguridad y base de datos son obligatorias en Tier 3.

## Bug fix

- Reproducir el bug original antes de aplicar el fix (si es posible), para confirmar que el fix realmente lo resuelve.
- Escribir o correr una prueba que hubiera fallado antes del fix y pase después.
- Revisar que el fix no rompa el comportamiento correcto en casos vecinos (edge cases cercanos al bug).

## Feature nueva

- Camino feliz (happy path) probado end-to-end del flujo nuevo.
- Al menos un caso de entrada inválida/vacía/límite.
- Si la feature interactúa con datos existentes, probar con datos representativos, no solo con datos vacíos.

## Cambios de seguridad (auth/permisos/entrada de usuario)

- Probar el camino correcto (usuario autorizado puede hacer la acción).
- Probar el camino de rechazo (usuario no autorizado NO puede hacer la acción, incluso manipulando IDs/parámetros).
- Probar con entrada maliciosa básica (inyección, payloads XSS simples) si el cambio toca input/output no confiable.
- Si el endpoint es nuevo o costoso (envía email, exporta, genera reportes), confirmar que el rate limiting definido en `security-checklist.md` realmente corta el abuso (no solo que existe en el código).

## Cambios de base de datos/migraciones

- Migración corre limpia hacia adelante y (si aplica) hacia atrás.
- Datos existentes no quedan corruptos ni huérfanos tras el cambio.
- Si había una query lenta, medir antes/después para confirmar la mejora real.

## UI/Frontend

- El cambio se ve correcto en al menos un tamaño de pantalla móvil y uno de escritorio.
- Estados de carga, vacío y error se comportan razonablemente, no solo el estado "feliz" con datos.
- Accesibilidad básica: labels en inputs, contraste razonable, navegación por teclado no rota.

## Antes de cerrar cualquier tarea

- Revisar el diff final completo: sin archivos temporales, sin código comentado de debug, sin cambios accidentales fuera de alcance.
- Confirmar que no se dejaron credenciales, tokens o datos de prueba sensibles en el código.
