# 11 · Cómo se toman las decisiones

> La IA no decide sola lo que no le toca, y el humano no piensa desde cero cada vez.

## Quién decide qué

| Tipo de decisión | Decide |
|---|---|
| Reversible, bajo riesgo, dentro del alcance aprobado (nombre de variable, orden de un bloque, prueba extra) | el agente, y lo menciona en una línea |
| Hay dos caminos razonables (extraer servicio vs. parchear; índice ahora vs. después) | **el humano**, con opciones del agente `decisiones` |
| Toca contrato público: ruta, vista, componente, columna, clave de permiso | **el humano**, siempre |
| Irreversible: borrar datos, migración destructiva, retirar una ruta, limpiar log de un equipo | **el humano**, con respaldo verificado antes |
| Afecta a personas: quién puede entrar, quién puede checar | **el humano**, siempre |

## Formato de opciones (lo que debes entregarle al humano)

Nunca "¿qué hago?". Siempre:

```markdown
## El problema en una frase
(con archivo:línea)

## Qué está en juego
Qué le pasa hoy a un usuario real si no se hace nada.

## Opción A — <nombre>
- Qué implica · Cuesta (archivos y líneas aprox.) · Gana · Riesgo (qué se rompe) ·
  Reversible (cómo se deshace)

## Opción B — …
## Opción C — No hacerlo ahora, solo detectar y avisar

## Recomendación
Cuál y **por qué**, en dos frases.

## Qué necesito de ti
Una sola pregunta concreta.

## Si nos arrepentimos
El camino de vuelta de cada opción.
```

## Reglas

1. **Entre 2 y 4 opciones reales.** Nada de opciones de relleno puestas para que otra brille.
2. **Incluye siempre "no hacerlo todavía"** cuando sea legítima: muchas veces es la correcta.
3. **Cifra el costo** en archivos y líneas, no en adjetivos.
4. **Describe el riesgo con lo que se rompe**, no con "riesgo medio".
5. Si una opción es **irreversible**, dilo en mayúsculas.
6. Registra la decisión en `.ui-work/02-decisiones/D-NNN-<tema>.md` con quién decidió y
   cuándo, e indéxala en `docs/DECISIONES.md`. Una decisión sin registro se vuelve a
   discutir en tres semanas.
7. Antes de abrir una decisión, revisa si ya se decidió: `.ui-work/02-decisiones/`,
   `docs/`, `auditoria_completa.md`. Reabrir lo cerrado sin motivo nuevo cuesta tiempo.
