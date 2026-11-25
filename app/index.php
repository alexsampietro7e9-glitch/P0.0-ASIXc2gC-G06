# `index.php` · Página principal

Página que consulta la base de datos de B-N06 y muestra el contenido de las tablas con paginación.

## Resumen

| Dato | Valor |
|---|---|
| Servidor | W-N06 (`10.6.1.10`) |
| Ruta | `/var/www/w-n06/public/index.php` |
| Propietario | `www-data:www-data` |
| Depende de | `db.php` (credenciales) y `style.css` (diseño) |
| URL | `https://10.6.1.10/` |

## Qué hace

1. Carga las credenciales desde `/etc/w-n06/db.php`.
2. Se conecta a MySQL (`10.6.2.10`) con PDO.
3. Lista las tablas de la base de datos (`SHOW TABLES`) como enlaces.
4. Muestra la tabla seleccionada con 25 filas por página.
5. Muestra el total de filas y los enlaces **Anterior / Següent**.

Con la tabla `equipaments` muestra **2709 filas en 109 páginas**.

## Parámetros de la URL

| Parámetro | Ejemplo | Función |
|---|---|---|
| `t` | `?t=equipaments` | Tabla a mostrar. Si falta, usa la primera |
| `p` | `?t=equipaments&p=3` | Número de página. Si falta, es la 1 |

## Código

```php
<?php
$cfg = require '/etc/w-n06/db.php';

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};dbname={$cfg['name']};charset=utf8mb4",
        $cfg['user'],
        $cfg['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    exit('No se pudo conectar con la base de datos.');
}

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$table  = $_GET['t'] ?? ($tables[0] ?? '');
if (!in_array($table, $tables, true)) {
    http_response_code(400);
    exit('Tabla no válida.');
}

$perPage = 25;
$page    = max(1, (int)($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;

$total = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
$rows  = $pdo->query("SELECT * FROM `$table` LIMIT $perPage OFFSET $offset")
             ->fetchAll(PDO::FETCH_ASSOC);
$pages = max(1, (int)ceil($total / $perPage));

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>W-N06 · <?= h($table) ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Servidor W-N06</h1>
<p>Taules:
<?php foreach ($tables as $t): ?>
  <a href="?t=<?= urlencode($t) ?>"><?= h($t) ?></a>
<?php endforeach; ?>
</p>
<h2><?= h($table) ?> (<?= $total ?> files)</h2>
<div class="scroll">
<table>
<?php if ($rows): ?>
  <tr><?php foreach (array_keys($rows[0]) as $c): ?><th><?= h($c) ?></th><?php endforeach; ?></tr>
  <?php foreach ($rows as $r): ?>
    <tr><?php foreach ($r as $v): ?><td><?= h($v) ?></td><?php endforeach; ?></tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr><td>Sense dades</td></tr>
<?php endif; ?>
</table>
</div>
<p>
<?php if ($page > 1): ?><a href="?t=<?= urlencode($table) ?>&p=<?= $page - 1 ?>">« Anterior</a><?php endif; ?>
Pàgina <?= $page ?> de <?= $pages ?>
<?php if ($page < $pages): ?><a href="?t=<?= urlencode($table) ?>&p=<?= $page + 1 ?>">Següent »</a><?php endif; ?>
</p>
</body>
</html>
```

## Explicación por bloques

### Conexión

```php
$pdo = new PDO("mysql:host=...;dbname=...;charset=utf8mb4", $user, $pass, [...]);
```

- `charset=utf8mb4` evita problemas con acentos y caracteres como `ç` o `·`.
- `ERRMODE_EXCEPTION` convierte los fallos de MySQL en excepciones que se pueden capturar.
- `ATTR_TIMEOUT => 5` evita que la página se quede colgada si B-N06 no responde.
- Si la conexión falla, se devuelve `500`, se guarda el detalle en el log y el navegador solo recibe un mensaje genérico.

### Validación de la tabla

```php
if (!in_array($table, $tables, true)) { ... }
```

El nombre de la tabla viene de la URL y no puede ir como parámetro preparado dentro de un `SELECT`. Por eso solo se acepta si está en la lista real de `SHOW TABLES`; cualquier otro valor devuelve `400`.

### Paginación

```php
$offset = ($page - 1) * $perPage;
... LIMIT $perPage OFFSET $offset
```

`$page` se convierte a entero con `(int)` y `max(1, ...)` impide páginas negativas o cero. Así los valores de `LIMIT` y `OFFSET` son siempre numéricos.

### Salida segura

```php
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
```

Todo lo que viene de la base de datos o de la URL pasa por `h()` antes de imprimirse. Evita que un dato con `<script>` se ejecute en el navegador (XSS).

## Medidas de seguridad

| Riesgo | Medida |
|---|---|
| Inyección SQL | La tabla se valida contra `SHOW TABLES`; `p` se fuerza a entero |
| XSS | Salida escapada con `htmlspecialchars` |
| Fuga de información | Errores al log, no al navegador (`display_errors = Off`) |
| Credenciales expuestas | Guardadas fuera del DocumentRoot en `db.php` |
| Modificación de datos | `webuser` solo tiene `SELECT` |

## Instalación
# `index.php` · Página principal

Página que consulta la base de datos de B-N06 y muestra el contenido de las tablas con paginación.

## Resumen

| Dato | Valor |
|---|---|
| Servidor | W-N06 (`10.6.1.10`) |
| Ruta | `/var/www/w-n06/public/index.php` |
| Propietario | `www-data:www-data` |
| Depende de | `db.php` (credenciales) y `style.css` (diseño) |
| URL | `https://10.6.1.10/` |

## Qué hace

1. Carga las credenciales desde `/etc/w-n06/db.php`.
2. Se conecta a MySQL (`10.6.2.10`) con PDO.
3. Lista las tablas de la base de datos (`SHOW TABLES`) como enlaces.
4. Muestra la tabla seleccionada con 25 filas por página.
5. Muestra el total de filas y los enlaces **Anterior / Següent**.

Con la tabla `equipaments` muestra **2709 filas en 109 páginas**.

## Parámetros de la URL

| Parámetro | Ejemplo | Función |
|---|---|---|
| `t` | `?t=equipaments` | Tabla a mostrar. Si falta, usa la primera |
| `p` | `?t=equipaments&p=3` | Número de página. Si falta, es la 1 |

## Código

```php
<?php
$cfg = require '/etc/w-n06/db.php';

try {
    $pdo = new PDO(
        "mysql:host={$cfg['host']};dbname={$cfg['name']};charset=utf8mb4",
        $cfg['user'],
        $cfg['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    exit('No se pudo conectar con la base de datos.');
}

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$table  = $_GET['t'] ?? ($tables[0] ?? '');
if (!in_array($table, $tables, true)) {
    http_response_code(400);
    exit('Tabla no válida.');
}

$perPage = 25;
$page    = max(1, (int)($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;

$total = (int)$pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
$rows  = $pdo->query("SELECT * FROM `$table` LIMIT $perPage OFFSET $offset")
             ->fetchAll(PDO::FETCH_ASSOC);
$pages = max(1, (int)ceil($total / $perPage));

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<meta charset="utf-8">
<title>W-N06 · <?= h($table) ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Servidor W-N06</h1>
<p>Taules:
<?php foreach ($tables as $t): ?>
  <a href="?t=<?= urlencode($t) ?>"><?= h($t) ?></a>
<?php endforeach; ?>
</p>
<h2><?= h($table) ?> (<?= $total ?> files)</h2>
<div class="scroll">
<table>
<?php if ($rows): ?>
  <tr><?php foreach (array_keys($rows[0]) as $c): ?><th><?= h($c) ?></th><?php endforeach; ?></tr>
  <?php foreach ($rows as $r): ?>
    <tr><?php foreach ($r as $v): ?><td><?= h($v) ?></td><?php endforeach; ?></tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr><td>Sense dades</td></tr>
<?php endif; ?>
</table>
</div>
<p>
<?php if ($page > 1): ?><a href="?t=<?= urlencode($table) ?>&p=<?= $page - 1 ?>">« Anterior</a><?php endif; ?>
Pàgina <?= $page ?> de <?= $pages ?>
<?php if ($page < $pages): ?><a href="?t=<?= urlencode($table) ?>&p=<?= $page + 1 ?>">Següent »</a><?php endif; ?>
</p>
</body>
</html>
```

## Explicación por bloques

### Conexión

```php
$pdo = new PDO("mysql:host=...;dbname=...;charset=utf8mb4", $user, $pass, [...]);
```

- `charset=utf8mb4` evita problemas con acentos y caracteres como `ç` o `·`.
- `ERRMODE_EXCEPTION` convierte los fallos de MySQL en excepciones que se pueden capturar.
- `ATTR_TIMEOUT => 5` evita que la página se quede colgada si B-N06 no responde.
- Si la conexión falla, se devuelve `500`, se guarda el detalle en el log y el navegador solo recibe un mensaje genérico.

### Validación de la tabla

```php
if (!in_array($table, $tables, true)) { ... }
```

El nombre de la tabla viene de la URL y no puede ir como parámetro preparado dentro de un `SELECT`. Por eso solo se acepta si está en la lista real de `SHOW TABLES`; cualquier otro valor devuelve `400`.

### Paginación

```php
$offset = ($page - 1) * $perPage;
... LIMIT $perPage OFFSET $offset
```

`$page` se convierte a entero con `(int)` y `max(1, ...)` impide páginas negativas o cero. Así los valores de `LIMIT` y `OFFSET` son siempre numéricos.

### Salida segura

```php
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
```

Todo lo que viene de la base de datos o de la URL pasa por `h()` antes de imprimirse. Evita que un dato con `<script>` se ejecute en el navegador (XSS).

## Medidas de seguridad

| Riesgo | Medida |
|---|---|
| Inyección SQL | La tabla se valida contra `SHOW TABLES`; `p` se fuerza a entero |
| XSS | Salida escapada con `htmlspecialchars` |
| Fuga de información | Errores al log, no al navegador (`display_errors = Off`) |
| Credenciales expuestas | Guardadas fuera del DocumentRoot en `db.php` |
| Modificación de datos | `webuser` solo tiene `SELECT` |

## Instalación

```bash
sudo nano /var/www/w-n06/public/index.php
sudo chown www-data:www-data /var/www/w-n06/public/index.php
sudo chmod 644 /var/www/w-n06/public/index.php
php -l /var/www/w-n06/public/index.php
```

```text
No syntax errors detected in /var/www/w-n06/public/index.php
```

## Verificación

```bash
curl -kI https://localhost
```

Debe devolver `HTTP/1.1 200 OK`. En el navegador, `https://10.6.1.10/` muestra **equipaments (2709 files)**.

## Problemas habituales

| Síntoma | Causa | Solución |
|---|---|---|
| Página en blanco o `500` | Error de PHP o de conexión | `sudo tail -5 /var/log/apache2/w-n06_ssl_error.log` |
| `No se pudo conectar con la base de datos.` | B-N06 inaccesible o credenciales erróneas | Revisar `db.php`, UFW y reglas `FORWARD` |
| `Tabla no válida.` | `t` no coincide con ninguna tabla | Usar un nombre que aparezca en la lista |
| La página se ve sin diseño | `style.css` no se carga | Comprobar que está en `public/` y es legible |
```bash
sudo nano /var/www/w-n06/public/index.php
sudo chown www-data:www-data /var/www/w-n06/public/index.php
sudo chmod 644 /var/www/w-n06/public/index.php
php -l /var/www/w-n06/public/index.php
```

```text
No syntax errors detected in /var/www/w-n06/public/index.php
```

## Verificación

```bash
curl -kI https://localhost
```

Debe devolver `HTTP/1.1 200 OK`. En el navegador, `https://10.6.1.10/` muestra **equipaments (2709 files)**.

## Problemas habituales

| Síntoma | Causa | Solución |
|---|---|---|
| Página en blanco o `500` | Error de PHP o de conexión | `sudo tail -5 /var/log/apache2/w-n06_ssl_error.log` |
| `No se pudo conectar con la base de datos.` | B-N06 inaccesible o credenciales erróneas | Revisar `db.php`, UFW y reglas `FORWARD` |
| `Tabla no válida.` | `t` no coincide con ninguna tabla | Usar un nombre que aparezca en la lista |
| La página se ve sin diseño | `style.css` no se carga | Comprobar que está en `public/` y es legible |
