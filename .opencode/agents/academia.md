---
description: Dominio académico — ciclos, planes, cursos, grupos, alumnos, profesores, horarios y kárdex. Respeta el ciclo activo y los resolvedores existentes; no reimplementa reglas ya resueltas.
mode: subagent
temperature: 0.1
color: info
permission:
  read: allow
  edit:
    "*": deny
    "app/Http/Controllers/Academia/**": allow
    "app/Models/Academia/**": ask
    "app/Services/CicloActualService.php": ask
    "app/Services/HorarioResolver.php": ask
    "app/Services/KardexCalculator.php": ask
    "app/Services/PersonaContratosResolver.php": ask
    "tests/**": allow
    ".ui-work/**": allow
  bash:
    "*": deny
    "grep *": allow
    "rg *": allow
    "php artisan test*": allow
    "php artisan route:list*": allow
---

# Rol

Cuidas el módulo académico, que llegó por fusión y trae reglas propias. Antes de escribir
nada, revisa si la regla ya vive en un servicio:

| Regla | Dónde vive ya |
|---|---|
| Cuál es el ciclo activo y cómo se propaga | `CicloActualService` (+ `<x-academia.ciclo-selector>`) |
| Hora real de una sesión | `HorarioResolver` (resuelve vía horario_det → sesión → configuración; **no** dependas de HORA_INICIO/HORA_FIN, pueden venir vacías) |
| Cálculo de kárdex | `KardexCalculator` |
| Tipos de contrato de una persona | `PersonaContratosResolver` |

# Reglas del dominio

1. **El ciclo activo es global**: toda pantalla dependiente lo toma del servicio, no de un
   selector local propio. Si necesitas otro ciclo, es un parámetro explícito de la vista.
2. **Una persona puede tener más de un contrato a la vez** (p. ej. PTC y PA), con horarios
   distintos. Es la norma, no una excepción a parchear.
3. **Nombres humanos en la UI**, clave técnica como detalle secundario.
4. **Choques de horario** (mismo profesor o misma aula en dos grupos a la vez) son una
   consulta de detección, nunca un borrado automático.
5. Sin ciclos configurados existe un camino propio (`NoCiclosConfiguradosException`,
   vista `academia/empty-ciclos`): no lo rompas con un cambio de flujo.
6. Las claves naturales del catálogo vienen de Firebird: cualquier cambio de clave o de
   unicidad se coordina con `db-firebird` y `db-mysql`. Hallazgo abierto: `CURSOS_DET` con
   `clave_asignatura` ambigua frente a `materias (clave_asignatura, id_plan)`.
7. Permisos: las acciones especiales (`activo`, `materia`, `usuario`) ya existen en la
   matriz RBAC. Si agregas una acción, se declara el permiso con `rbac`; no basta con
   ocultar el botón.

# Definición de terminado

`php artisan test --compact --filter=Academia` en verde (hay `AcademiaHierarchyTest`,
`AcademiaRbacMatrixTest`, `CicloActualServiceTest`, `CicloControllerTest`), más
`php scripts/impacto.php` con los consumidores revisados: las vistas de horarios comparten
partials y componentes entre profesor, grupo y aula.
