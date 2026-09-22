# 09 · Protocolo entre agentes

Los subagentes **no comparten memoria**: cada uno arranca en frío y solo sabe lo que está
escrito. Por eso la comunicación es por archivos, con formato fijo.

## Carpetas

```
.ui-work/
├── ESTADO.md                 tablero (lo mantiene team-lead / ui-orchestrator)
├── MODEL_MAP.md              enrutado de modelos (manda sobre la memoria del agente)
├── MODEL_LOG.md              modelos caídos y escaladas
├── PREGUNTAS.md              lo que requiere decisión humana
├── HALLAZGOS-EXTRA.md        lo encontrado fuera de alcance (NO se arregla)
├── 00-auditoria/<modulo>.md
├── 01-propuestas/<modulo>.md
├── 02-decisiones/D-NNN-<tema>.md
├── 03-implementacion/<modulo>.md
├── 04-validacion/{impacto|qa|revision}-<modulo>.md
├── 05-reportes/limpieza-<fase>.md
└── _cuarentena/<fecha>/
```

## Encabezado obligatorio de cada archivo

```markdown
---
agente: backend
modulo: DISPOSITIVOS
fecha: 2026-09-18
entrada: [02-decisiones/D-014.md]
estado: COMPLETADO      # COMPLETADO | BLOQUEADO | REQUIERE-DECISION
siguiente: impacto, qa
---
```

## Cierre obligatorio (handoff)

```markdown
## Entrega
**Hecho:** ...
**Archivos tocados:** archivo:línea
**Comandos y salida real:** ...
**Contrato público:** sin cambios | detalle
**Impacto:** N consumidores detectados, N revisados, N arreglados
**Riesgos abiertos:** ...
**Para el siguiente agente (`qa`):** prueba X, Y, Z
**Requiere decisión humana:** no | sí → PREGUNTAS.md#N
```

## Marcadores comunes

`[GAP: BACKEND]` · `[BLOQUEADO: ROUTE SAFETY]` · `[REVISIÓN MANUAL]` · `[REQUIERE DECISIÓN]`

## Convivencia

1. Un agente nunca edita el archivo de otro: escribe el suyo y referencia.
2. Si contradices a otro agente, cítalo textual y explica por qué.
3. Si necesitas algo de otro, escribe la petición y termina tu turno. `ui-*` no toca
   backend; `backend` no rediseña pantallas; nadie toca rutas salvo con dictamen.
4. Solo el humano cierra un `[REQUIERE DECISIÓN]`.
5. Nada de "ya está listo" sin la salida real del comando.
6. Se invoca a otro agente **por nombre, sin `@`** (el `@` es del humano).
