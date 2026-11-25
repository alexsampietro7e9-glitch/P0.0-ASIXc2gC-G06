# `db.php` · Credenciales de la base de datos

Archivo de configuración con los datos de conexión a MySQL (B-N06). Lo carga `index.php` con `require`.

## Resumen

| Dato | Valor |
|---|---|
| Servidor | W-N06 (`10.6.1.10`) |
| Ruta | `/etc/w-n06/db.php` |
| Propietario | `root:www-data` |
| Permisos | `640` |
| Lo usa | `index.php` |

## Por qué está fuera del DocumentRoot

El DocumentRoot de Apache es `/var/www/w-n06/public/`. Si las credenciales estuvieran dentro, un error de configuración (por ejemplo, PHP desactivado) podría servirlas como texto plano. En `/etc/w-n06/` Apache no puede publicarlas nunca, aunque falle PHP.

## Contenido

```php
<?php
return [
    'host' => '10.6.2.10',          // B-N06 (Intranet)
    'name' => 'equipaments_db',     // base de datos
    'user' => 'webuser',            // usuario de solo lectura
    'pass' => '<PASSWORD_WEBUSER>', // contraseña (no se publica en el repositorio)
];
```

El archivo devuelve un array. `index.php` lo recoge así:

```php
$cfg = require '/etc/w-n06/db.php';
```

| Clave | Significado |
|---|---|
| `host` | IP del servidor MySQL en la Intranet |
| `name` | Base de datos a la que se conecta |
| `user` | `webuser`, con permiso `SELECT` únicamente |
| `pass` | Contraseña de `webuser` |

## Creación

```bash
sudo mkdir -p /etc/w-n06
sudo nano /etc/w-n06/db.php
sudo chown root:www-data /etc/w-n06/db.php
sudo chmod 640 /etc/w-n06/db.php
```

## Permisos explicados

| Quién | Acceso | Motivo |
|---|---|---|
| `root` | Lectura y escritura | Administra el archivo |
| Grupo `www-data` | Solo lectura | Apache/PHP necesita leerlo |
| Resto de usuarios | Ninguno | No deben ver la contraseña |

Comprobación:

```bash
ls -l /etc/w-n06/db.php
```

```text
-rw-r----- 1 root www-data 132 Nov 23 10:21 /etc/w-n06/db.php
```

## Seguridad

- El usuario `webuser` solo puede leer (`SELECT`) y solo desde `10.6.1.10`, así que aunque se filtrara la contraseña no podría modificar datos ni conectarse desde otra máquina.
- La contraseña **no debe subirse a GitHub**. En el repositorio se publica una versión de ejemplo con `<PASSWORD_WEBUSER>`.
- Si falla la conexión, `index.php` registra el error en el log de Apache y muestra un mensaje genérico, sin revelar el host ni el usuario.

## Problemas habituales

| Síntoma | Causa | Solución |
|---|---|---|
| `Failed to open stream: Permission denied` | Permisos o grupo incorrectos | `chown root:www-data` y `chmod 640` |
| `SQLSTATE[HY000] [2002]` / timeout | Firewall de B-N06 o router bloquea el 3306 | Revisar UFW y reglas `FORWARD` |
| `SQLSTATE[HY000] [1045] Access denied` | Contraseña errónea o usuario creado con otra IP de origen | Comprobar `mysql.user` en B-N06 |
