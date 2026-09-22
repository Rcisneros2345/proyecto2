# UI_DESIGN_PRINCIPLES

Criterio de diseño para el sistema. Lo lee `ui-designer` antes de cada propuesta y
`ui-implementer` cuando duda. Complementa a `UI_DESIGN_SYSTEM.md` (que define los
tokens); aquí está el **porqué**, allí el **qué**.

---

## 1. Jerarquía: tres niveles, no diez

Todo contenido de una página cae en uno de tres niveles:

- **Primario** — lo que el usuario ve en los primeros 2 segundos. Como máximo 3 cosas.
- **Secundario** — lo que busca activamente cuando lo necesita.
- **Terciario** — lo que consulta rara vez. No ocupa espacio en el primer pantallazo.

La jerarquía se construye con, en este orden de fuerza: **posición > tamaño > peso >
color**. El color es el recurso más débil y el más abusado.

Una sola acción primaria por página. Si hay dos botones naranjas compitiendo, no hay
acción primaria.

---

## 2. Retícula y simetría

- 12 columnas. Nada se coloca "a ojo".
- **Ancho máximo de contenido**: el contenido se centra en un contenedor acotado.
  Una tabla estirada a 2560px es ilegible: el ojo pierde la fila.
- Texto de lectura: entre 45 y 75 caracteres por línea. Más, y se pierde el renglón.
- **Simetría por nivel**: elementos del mismo nivel jerárquico comparten ancho y alto.
  4 KPIs = 4 cuartos idénticos. Si tienes 5 métricas, o promocionas una a destacada o
  buscas una retícula que dé exacto. Nunca "3 arriba y 2 abajo centradas".
- **Un solo eje de alineación por bloque.** Los títulos, labels y textos de un bloque
  comparten borde izquierdo. Los números van a la derecha en tablas (para comparar
  magnitudes de un vistazo). Las fechas, formato único en todo el sistema.
- Alturas iguales en elementos contiguos. Cards de distinta altura en una fila rompen
  la lectura horizontal.

---

## 3. Espaciado: proximidad = relación

Escala fija (múltiplos de 4 u 8). Cero valores arbitrarios.

Regla que resuelve el 80% de los problemas de "se ve desordenado":

> El espacio **dentro** de un grupo debe ser siempre menor que el espacio **entre** grupos.

Si un label está tan lejos de su input como del input siguiente, el usuario no sabe
a cuál pertenece. Eso no se arregla con líneas ni con cajas, se arregla con espacio.

Un label pertenece a su campo: pégalo. Dos secciones distintas: sepáralas.

---

## 4. Color

**Reparto aproximado 60 / 30 / 10**: 60% fondo neutro, 30% superficies y bordes,
10% acento.

**Naranja = marca y acción.** Solo para: acción primaria, estado activo, selección,
indicador principal, enlace relevante. Nunca como fondo de grandes superficies. Una
interfaz naranja entera no comunica nada porque todo pesa igual.

**El color semántico es un sistema aparte del color de marca.** Éxito, aviso, error e
información tienen sus propios tokens. Si tu marca es naranja, el "aviso" no puede ser
naranja: se confundiría con una acción.

**El color nunca es el único portador de significado.** Un badge de estado lleva texto.
Un punto rojo lleva etiqueta. Un 8% de los hombres tiene deficiencia de visión del
color, y además el usuario con prisa no distingue tonos parecidos.

**Contraste mínimo (WCAG AA)**: 4.5:1 para texto normal, 3:1 para texto grande (≥18.66px
o ≥14px en negrita) y para bordes de controles e iconos con significado.

El texto gris claro sobre fondo blanco es el fallo de contraste más común en
administrativos. "Texto secundario" no significa "texto ilegible".

---

## 5. Light y Dark: superficies, no inversión

Dark Mode no es invertir colores.

- En claro, la elevación se sugiere con **sombra**. En oscuro, con **superficie más
  clara**: cuanto más elevado, más claro el fondo. Las sombras casi no se ven en oscuro.
- Nunca blanco puro sobre negro puro: produce halo y fatiga. Usa neutros ligeramente
  desaturados.
- El acento suele necesitar **más luminosidad en oscuro** para mantener el contraste.
  No uses el mismo hex en los dos temas: usa un token semántico que resuelva a valores
  distintos.
- Prohibido: `#fff` suelto, `bg-light`, `table-light`, y cualquier clase de color de
  framework que no pase por tokens.

---

## 6. Densidad

Un ERP no es una landing. El usuario frecuente quiere **ver más en menos espacio**;
el usuario ocasional quiere respirar.

- Páginas de entrada y configuración: densidad cómoda.
- Listados operativos que se usan 40 veces al día: densidad alta, filas compactas.
- Decide la densidad por página y escríbelo en la propuesta. No la dejes al azar.

---

## 7. Cards: cada una justifica su existencia

Una card sirve para que el usuario pueda **entender, comparar, detectar, decidir o
actuar**. Si no hace ninguna de las cinco, sobra.

Un número solo no es información. Añade lo que **ya exista** en los datos:
etiqueta clara · unidad · período · tendencia · comparación · estado ·
fecha de actualización.

**No inventes datos que el backend no tenga.** Si no hay histórico, no hay tendencia;
di eso en la propuesta en vez de dibujar una flechita falsa.

No conviertas todo en cards. Distingue: card de contenido · KPI · panel · agrupador ·
sección. Una tabla dentro de una card dentro de un panel es ruido, no estructura.

---

## 8. Gráficas: responden preguntas

Antes de dibujar, responde las diez:

1. ¿Qué pregunta responde?  2. ¿Qué decisión soporta?  3. ¿Es el tipo correcto?
4. ¿Los ejes se entienden?  5. ¿Tiene unidades?  6. ¿Tiene período?
7. ¿Necesita comparación?  8. ¿Hace falta leyenda?  9. ¿Debe mostrar valores?
10. ¿Debe permitir interacción?

Guía rápida de tipo:
- **Evolución en el tiempo** → línea
- **Comparación entre categorías** → barra (horizontal si las etiquetas son largas)
- **Composición del total** → barra apilada, casi nunca tarta (el ojo compara mal
  ángulos; con más de 4 segmentos es inútil)
- **Relación entre dos variables** → dispersión
- **Un solo número** → un número grande, no una gráfica

Prohibido: 3D, degradados decorativos, gráficas para rellenar hueco, ejes truncados que
exageran diferencias. Si no hay datos suficientes, muestra la información de otra forma.

---

## 9. Estados: la página no está terminada sin ellos

Toda vista con datos define **seis** estados, no uno:

vacío inicial · cargando · con datos · error · sin resultados de filtro · sin permisos

El estado vacío es una oportunidad: explica qué es esto y ofrece la acción para empezar.
"No hay datos" no ayuda a nadie.

El estado de carga debe ser del mismo tipo para la misma operación en todo el sistema.
Skeleton para contenido que va a aparecer con forma conocida; spinner para esperas cortas
e indeterminadas; estado de carga en el propio botón para acciones.

---

## 10. Formularios

- Una columna. Los formularios de dos columnas duplican los errores de salto de campo.
  Excepción: campos naturalmente cortos y emparejados (código postal / ciudad).
- Label **siempre visible y asociado**. El placeholder no es un label: desaparece al
  escribir y deja al usuario sin saber qué estaba rellenando.
- Agrupa por significado, con títulos de sección.
- El error va **junto al campo**, dice **qué pasó y cómo arreglarlo**, y aparece cuando
  el usuario sale del campo (no en cada tecla).
- Marca lo opcional en vez de lo obligatorio si casi todo es obligatorio.
- Acción primaria a la izquierda del bloque de botones o alineada con los campos;
  destructiva separada y con confirmación.
- Nunca deshabilites el botón de envío sin decir qué falta.

---

## 11. Tablas

- Cabecera fija si la tabla scrollea.
- Números a la derecha, tabulares (mismo ancho de dígito) para poder comparar.
- Ordena por la columna que el usuario necesita primero, no por ID.
- Acciones de fila: máximo 2 visibles, el resto en menú. Si hay 6 iconos por fila,
  el usuario no encuentra ninguno.
- **Solo las capacidades necesarias.** Una tabla de 12 registros no necesita búsqueda,
  paginación, selección ni exportación. Una operativa de 40.000 sí.
- Responsive: scroll horizontal con columnas clave fijas, o columnas prioritarias, o
  conversión a cards. Nunca "reducir la fuente hasta que quepa".

---

## 12. Lo que no hacemos

Animaciones largas · degradados decorativos · glassmorphism · sombras fuertes ·
esquinas muy redondeadas en contenedores de datos · iconos sin etiqueta en acciones
importantes · más de dos familias tipográficas · scroll infinito en administrativos ·
modales encima de modales · rojo para algo que no es destructivo o error.

---

## 13. Lista de comprobación antes de cerrar una página

- [ ] Se entiende en 5 segundos qué es y qué se hace aquí
- [ ] Hay exactamente una acción primaria
- [ ] Todo alineado a la retícula; elementos del mismo nivel, mismo tamaño
- [ ] Ancho de contenido acotado y centrado
- [ ] Espaciado por escala; dentro < entre
- [ ] Contraste AA verificado en Light y en Dark
- [ ] Los 6 estados resueltos
- [ ] Navegable solo con teclado; foco siempre visible
- [ ] Probada en móvil, tablet y desktop
- [ ] Cero tokens inventados, cero componentes duplicados
- [ ] Cada card y cada gráfica justifica su sitio
