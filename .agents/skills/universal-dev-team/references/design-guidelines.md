# Guía de Diseño Web (UI / Frontend Specialist)

Tendencias vigentes en 2026 + reglas atemporales que no cambian con la moda. Usar las tendencias como vocabulario visual, no como excusa para romper usabilidad — si el proyecto ya tiene un sistema de diseño (`memory/CONVENTIONS.md` → sección UI), ese sistema manda por sobre cualquier tendencia de esta guía.

## Color

- **Paletas saturadas con contraste estratégico** reemplazaron a los pasteles apagados de años anteriores: 1-2 colores dominantes saturados + una base neutra, en vez de todo el sitio en un solo tono. El contraste alto en botones/CTAs mejora la tasa de click medible, no es solo estética.
- **Neutros "sin blanquear"** (off-white cálido, grises con tinte) están reemplazando al blanco puro como fondo base — se siente menos clínico.
- **Gradientes tipo "mesh"** (múltiples puntos de color difuminados, no un degradé lineal simple) para fondos y hero sections, con moderación — uno por página, no en cada sección.
- **Accesibilidad de color desde el diseño, no como revisión posterior**: definir los tokens de color con el contraste ya resuelto (herramientas tipo Radix Colors o Leonardo Color generan escalas que cumplen WCAG por construcción). El estándar de contraste AA de WCAG 2.1/2.2 (4.5:1 texto normal, 3:1 texto grande) sigue siendo el piso mínimo a cumplir siempre, tendencia de moda o no.
- **Dark mode ya no es opcional**: se espera que cualquier interfaz nueva soporte modo oscuro desde el diseño (tokens de color con variante dark), no como un tema pegado al final.

## Tipografía

- **Tipografía fluida con `clamp()`**: en vez de tamaños fijos que saltan en breakpoints, usar `font-size: clamp(min, preferido-en-vw, max)` para que el texto escale suave entre pantallas.
- **Tipografía grande y expresiva en headings** (kinetic/bold typography) para hero sections y landing pages, combinada con texto de cuerpo conservador y legible — el contraste de escala es lo que genera jerarquía, no usar 5 tamaños intermedios.
- Regla atemporal: nunca menos de 16px para texto de cuerpo en mobile, line-height 1.4–1.6 para bloques de texto largo.

## Layout

- **Bento grids**: composición modular tipo "lunchbox japonés" — bloques de distinto tamaño en una grilla, cada uno con su propia mini-historia (una métrica, una feature, una cita). Rompe la monotonía del scroll lineal y es el patrón dominante en landing pages de producto/SaaS en 2026.
- **Formas orgánicas/blob** y bordes asimétricos como contrapeso a la grilla estricta, usando `clip-path` o SVG (cuidar performance con `will-change` en animaciones).
- **Glassmorphism 2.0**: paneles semitransparentes con blur y profundidad de capa (no el efecto plano de vidrio de hace unos años — ahora con sombras y bordes más definidos para dar sensación de profundidad real).
- Regla atemporal: la grilla sigue siendo la base incluso en layouts "orgánicos" — el desorden visual intencional se construye sobre una estructura de grid subyacente, no al azar.

## Interacción y movimiento

- **Micro-interacciones con propósito**: feedback visual chico (hover, focus, estados de carga) en vez de animación decorativa sin función. Cada animación debe comunicar algo (que la acción se registró, que algo está cargando), no solo verse bien.
- Respetar `prefers-reduced-motion` siempre — usuarios con sensibilidad al movimiento no deben recibir animación forzada.

## Tablas y dashboards (datos, no solo estética)

- **Densidad de información controlada**: en una tabla con muchas filas, usar padding vertical compacto y una fila de altura consistente; alternar color de fila (zebra striping) sutil solo si hay más de ~8 filas visibles a la vez — con menos, agrega ruido sin ayudar a escanear.
- **Encabezado fijo (`sticky`)** en tablas largas para que las columnas sigan siendo legibles al hacer scroll.
- **Alinear números a la derecha, texto a la izquierda** — permite comparar magnitudes de un vistazo en columnas numéricas.
- **Estados vacíos y de carga diseñados**, no solo una tabla en blanco — un dashboard sin datos debe explicar qué hacer, no verse roto.
- **Mobile**: una tabla ancha no se achica, se transforma — colapsar a tarjetas apiladas (cada fila se convierte en una card con label:valor) o permitir scroll horizontal contenido, nunca dejar que rompa el layout de la página.
- Para gráficos: preferir 1-2 tipos de visualización por dashboard usados consistentemente (barras para comparación, líneas para tendencia en el tiempo) antes que variedad decorativa — la consistencia ayuda más a leer datos que la variedad visual.

## Reglas atemporales (no cambian con la tendencia del año)

- **Jerarquía visual clara**: el elemento más importante de la pantalla debe ser obvio en los primeros 2 segundos, sin depender de leer texto.
- **Contraste y legibilidad por sobre estética**: si una tendencia (gradiente sutil, texto sobre imagen) compromete la legibilidad, ganarle a la tendencia.
- **Responsive real, no solo "no se rompe"**: probar en al menos un tamaño mobile chico y uno de escritorio ancho, no asumir que Chrome DevTools a un tamaño intermedio alcanza.
- **Accesibilidad de teclado**: todo elemento interactivo debe ser alcanzable y operable con Tab/Enter, con un estado de foco visible — no solo con mouse/touch.

## Al cerrar una tarea de UI

Registrar en `memory/CONVENTIONS.md` (sección UI) cualquier decisión de paleta, tipografía o patrón de layout adoptada, para que la próxima tarea de UI la reutilice en vez de reinventar el sistema visual del proyecto.
