# `style.css` · Diseño de la página

Hoja de estilos de `index.php`. Da a la página un aspecto de plano técnico (cianotipia): fondo azul de Prusia con cuadrícula, texto blanco y detalles en cian y ámbar.

## Resumen

| Dato | Valor |
|---|---|
| Servidor | W-N06 (`10.6.1.10`) |
| Ruta | `/var/www/w-n06/public/style.css` |
| Propietario | `www-data:www-data` (permisos `644`) |
| Se carga desde | `index.php`, con `<link rel="stylesheet" href="style.css">` |
| Alcance | Solo CSS: no modifica el PHP ni el HTML |

## Instalación

```bash
sudo nano /var/www/w-n06/public/style.css
sudo chown www-data:www-data /var/www/w-n06/public/style.css
sudo chmod 644 /var/www/w-n06/public/style.css
```

Comprobar que Apache lo sirve:

```bash
curl -kI https://localhost/style.css
```

Debe devolver `HTTP/1.1 200 OK` y `Content-Type: text/css`.

## Paleta y fuentes

Los colores y fuentes están definidos como variables en `:root`, así que se cambian en un solo sitio:

```css
:root {
  --ink:    #082447;   /* fondo azul de Prusia */
  --sheet:  #0e3769;   /* cabecera de la tabla */
  --chalk:  #eaf3ff;   /* texto */
  --cyan:   #7fd4ff;   /* luz del escáner */
  --amber:  #ffcf5a;   /* marcador / interacción */
  --led:    #5cf2a5;   /* LED de servidor en línea */
  --display: "Barlow Condensed", "Roboto Condensed", "Arial Narrow", sans-serif;
  --text:    "IBM Plex Sans", "Segoe UI", system-ui, sans-serif;
}
```

| Variable | Uso |
|---|---|
| `--ink` | Fondo de la página |
| `--sheet` | Fondo de la fila de cabecera de la tabla |
| `--chalk` | Color de texto principal |
| `--cyan` | Escáner, bordes y resaltado de fila |
| `--amber` | Elementos interactivos (hover, selección, celda activa) |
| `--led` | Punto verde junto al título |
| `--display` | Títulos, cabeceras de tabla y botones |
| `--text` | Texto general |

Las fuentes se piden a Google Fonts con `@import`. Si el cliente no tiene Internet, se usan las fuentes de reserva de cada lista y la página sigue siendo legible.

## Estructura del archivo

| Sección | Qué hace |
|---|---|
| Fondo | Degradados azules, cuadrícula de delineante y grano de papel (SVG incrustado, sin peticiones externas) |
| Escáner | Franja de luz que recorre la página cada pocos segundos (`html::after`, animación `scan`) |
| Página | Ancho máximo de 1400 px, márgenes adaptables con `clamp()` |
| Título `h1` | LED verde que parpadea y línea de cota bajo el texto |
| Selector de tablas (`h1 + p`) | Enlaces con borde discontinuo que se rellenan en ámbar al pasar el ratón |
| Nombre de tabla `h2` | Barra ámbar a la izquierda |
| Contenedor `.scroll` | Lámina con marcas de registro en las esquinas, scroll propio y altura máxima del 72 % de la ventana |
| Tabla | Cabecera fija (`position: sticky`) y filas alternas muy sutiles |
| Efecto "mira" | Al pasar el ratón se resalta la fila y la columna, y la celda exacta en ámbar |
| Tabla vacía | El mensaje "Sense dades" sale sobre una zona rayada |
| Paginación (`.scroll + p`) | Cajetín abajo a la derecha, con botones Anterior / Següent |
| Animaciones | `grid-in`, `plot`, `dimension`, `rise`, `draw-down`, `led`, `scan` |
| Móvil | Por debajo de 640 px se quita la altura máxima y la paginación ocupa todo el ancho |
| Accesibilidad | `prefers-reduced-motion` desactiva animaciones y escáner |

## Selectores que dependen del HTML de `index.php`

El CSS no usa clases propias salvo `.scroll`; el resto se apoya en la estructura del HTML. Si se cambia `index.php`, hay que mantener esto:

| Elemento | Selector | Requisito |
|---|---|---|
| Selector de tablas | `h1 + p` | El `<p>` con los enlaces debe ir justo después del `<h1>` |
| Contenedor de la tabla | `.scroll` | La tabla debe estar dentro de `<div class="scroll">` |
| Paginación | `.scroll + p` | El `<p>` de paginación debe ir justo después del `.scroll` |

## Detalles técnicos

### Cabecera fija

```css
th { position: sticky; top: -10px; }
table { border-collapse: separate; border-spacing: 0; }
```

`border-collapse: separate` es necesario para que la cabecera fija se dibuje bien. El `top: -10px` compensa el `padding` de 10 px de `.scroll`.

### Resaltado de columna

```css
:where(table:has(td:nth-child(1):hover)) td:nth-child(1) { ... }
```

Usa `:has()` para detectar sobre qué columna está el ratón y resaltar sus celdas y su cabecera. El archivo repite la regla para las columnas 1 a 20. La tabla `equipaments` tiene unas 40 columnas, así que a partir de la 21 solo se resalta la fila.

### Animaciones y rendimiento

El escáner y la cuadrícula usan `position: fixed` con `pointer-events: none`, por lo que no interfieren con los clics. Si el sistema tiene activado "reducir movimiento", todas las animaciones se anulan:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation: none !important; transition: none !important; }
  html::after { display: none; }
}
```

## Compatibilidad

- Navegadores actuales (Chrome, Edge, Firefox y Safari recientes). El resaltado de columnas requiere `:has()`.
- En navegadores antiguos sin `:has()` o `backdrop-filter`, la página funciona igual pero sin esos efectos.

## Problemas habituales

| Síntoma | Causa | Solución |
|---|---|---|
| La página se ve sin diseño | `style.css` no está en `public/` o no se puede leer | `ls -l /var/www/w-n06/public/` y revisar permisos |
| Las fuentes se ven distintas | El cliente no llega a Google Fonts | Es normal; se usan las fuentes de reserva |
| `style.css` devuelve `404` | Archivo en otra carpeta o con otro nombre | Debe estar junto a `index.php` |
| La cabecera de la tabla no queda fija | Se cambió `border-collapse` a `collapse` | Mantener `separate` |
| El diseño no cambia tras editarlo | Caché del navegador | Recargar con `Ctrl+F5` |
