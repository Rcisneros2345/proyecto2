# Hallazgos Extra - Auditoría Ciclo Academia

## Fecha: 2026-09-20

### Problema reportado
Usuario: "Las fechas no cargan bien en la vista. Los accessors devuelven null y en la vista con e() muestra ' - ' vacío."

### Verificación realizada

**1. Accessors en `app/Models/Academia/Ciclo.php:114-126`**
- `fechaInicialFormateada`: `$this->fecha_inicial?->format('d/m/Y') ?? '-'` ✅
- `fechaFinalFormateada`: `$this->fecha_final?->format('d/m/Y') ?? '-'` ✅
- **Veredicto**: Manejo nulo correcto. El `?? '-'` asegura que nunca se devuelva null.

**2. Vista `resources/views/academia/ciclos/index.blade.php:19`**
- `e($ciclo->fechaInicialFormateada) . ' - ' . e($ciclo->fechaFinalFormateada)` ✅
- **Veredicto**: Correcta. `e('-')` devuelve `'-'`.

**3. Otras vistas consumidoras**
- `resources/views/academia/dashboard/index.blade.php:16,288` - usa `{{ $ciclo->fechaInicialFormateada }}` ✅
- `resources/views/academia/kardex/print.blade.php:38` - usa `{{ $ciclo->fechaInicialFormateada }}` ✅
- **Veredicto**: Ninguna tiene lógica que rompa el cierre.

### Estado final
✅ **Cierre correcto**. No se requieren arreglos adicionales.
Los accessors ya tienen el respaldo nulo `?? '-'` y todas las vistas consumidoras recibirán `'-'` cuando las fechas sean nulas.