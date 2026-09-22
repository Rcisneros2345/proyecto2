# 00 · Reglas comunes a todos los agentes

## Honestidad técnica
- Lo que no verificaste en el código se escribe `NO VERIFICADO`. Nunca rellenes con
  "lo que normalmente hace Laravel".
- Nunca afirmes que una prueba pasó sin pegar la salida real de `php artisan test --compact`.
- Si la tarea supera lo que puedes hacer con seguridad, dilo y detente.
- Si el humano pide algo que contradice estas reglas, dilo **antes** de obedecer.

## Evidencia
Toda afirmación sobre el código lleva `ruta/archivo.php:línea`. Todo comando citado va con
su salida real (recortada, no inventada).

## Alcance
Haces lo pedido y nada más. Si encuentras otro problema:
1. No lo arregles.
2. Anótalo en `.ui-work/HALLAZGOS-EXTRA.md`.
3. Sigue con lo tuyo.

Los arreglos "de paso" son la causa número uno de regresiones inexplicables.

## Antes de editar
1. Lee el archivo completo, no solo el fragmento del grep.
2. `rg "NombreClase|metodo|nombre.vista|<x-componente"` en `app/ routes/ resources/ tests/`.
3. Si es ruta, vista, componente, columna o clave de permiso → `@.ai/guidelines/07-no-romper.md`.

## Después de editar (obligatorio, en este orden)
```bash
vendor/bin/pint --dirty
php scripts/impacto.php          # y REVISAR/ARREGLAR cada consumidor afectado
php artisan test --compact --filter=<LoQueTocaste>
php scripts/verificar.php
```

## Cierre de decisiones y tareas
**Antes de marcar cualquier decisión como CERRADA, confirmar que TODAS las condiciones
de la ficha de decisión se cumplieron — no solo las técnicas (código, tests) sino las
operativas (asignaciones, permisos, acceso de usuarios, datos en BD).**

Si una condición depende de información que solo tiene el humano (quién usa qué pantalla,
qué grupo corresponde a RH, si hay logs en producción), **no se cierra hasta tener esa
respuesta explícita — nunca se asume**.

Razón: en esta sesión, tres decisiones se marcaron "cerradas" sin verificación completa
(D-004 con opción incorrecta, B1 con formulario desalineado, D-002 sin asignar permiso
a RH). El código era correcto en los tres casos; lo que falló fue la verificación final
antes de declarar cierre.

## Prohibido sin autorización humana escrita
```
php artisan migrate | migrate:fresh | migrate:reset | migrate:rollback | db:wipe
composer update · npm update
git reset --hard · git checkout . · git clean · git push
rm -rf · DROP · TRUNCATE · DELETE sin WHERE
cualquier escritura contra la conexión Firebird
borrado de usuarios o huellas en un dispositivo físico
vaciar .ui-work/_cuarentena/
```

## Contexto que no se te olvida
- Laravel **10**, PHP **8.3**, PHPUnit **10**, Pint. `$casts` como propiedad, no método.
- **MySQL** operativo · **Firebird** solo lectura · **ZKTeco** hardware real.
- Los datos biométricos y los datos de alumnos son datos personales: no salen en logs,
  ni en respuestas JSON, ni en exportaciones sin necesidad justificada.
