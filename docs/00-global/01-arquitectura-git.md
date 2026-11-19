# Arquitectura, direccionamiento IP, SSH y Git

**Proyecto:** P0.0-ASIXc2gC-G06 · **Equipo:** 06 · **Dominio interno:** `g06.lan`

Este documento describe cómo está construida la infraestructura completa, por qué se ha diseñado así y cómo se relacionan las máquinas entre sí. El detalle de instalación de cada máquina está en su propio documento (ver [Índice](#índice)).

> Los apartados marcados con **⚠️ PENDIENTE** son datos que aún hay que confirmar o completar antes de la entrega.

## Índice

1. [Descripción general](#1-descripción-general)
2. [Diagrama de red](#2-diagrama-de-red)
3. [Redes y zonas](#3-redes-y-zonas)
4. [Direccionamiento IP](#4-direccionamiento-ip)
5. [Servicios y puertos](#5-servicios-y-puertos)
6. [Política de firewall y flujos permitidos](#6-política-de-firewall-y-flujos-permitidos)
7. [DNS y DHCP](#7-dns-y-dhcp)
8. [Dependencias entre máquinas](#8-dependencias-entre-máquinas)
9. [Justificación del diseño](#9-justificación-del-diseño)
10. [Usuario `bchecker` y acceso SSH](#10-usuario-bchecker-y-acceso-ssh)
11. [Git y flujo de trabajo](#11-git-y-flujo-de-trabajo)
12. [Ficheros de configuración clave](#12-ficheros-de-configuración-clave)
13. [Limitaciones conocidas y mejoras](#13-limitaciones-conocidas-y-mejoras)
14. [Reparto de responsabilidades](#14-reparto-de-responsabilidades)

---

## 1. Descripción general

La infraestructura es una arquitectura **multicapa** desplegada en NuvuletVDI (Isard). Un único router, `R-N06`, conecta tres redes y aplica NAT y firewall:

- **Capa de presentación (DMZ):** servidor web `W-N06` (Apache + PHP) y servidor FTP `F-N06` (vsftpd con chroot).
- **Capa de datos (Intranet):** base de datos `B-N06` (MySQL 8.0) con el dataset de equipamientos de OpenData Barcelona.
- **Servicios de red (en el router):** DNS (Bind9), DHCP (isc-dhcp-server) y monitorización (Uptime Kuma en Docker).

Flujo principal de la aplicación: el cliente pide la página a `W-N06`, y `W-N06` consulta a `B-N06` por TCP 3306 atravesando el router. Los clientes **nunca** hablan directamente con la base de datos.

## 2. Diagrama de red

![Diagrama de arquitectura de red](img/diagrama.png)


> **⚠️ PENDIENTE:** el diagrama no recoge `CLI-W-N06` (cliente de pruebas en la DMZ, `10.6.1.50`) que aparece en la documentación del servidor web. Decidir si se incluye o se elimina de la documentación.

## 3. Redes y zonas

| Red en Isard | Zona | Rango | Función |
|---|---|---|---|
| `Default` | NAT / salida a Internet | DHCP de Isard (`192.168.120.0/22`) | Salida a Internet del router y de los servidores (instalar paquetes, GitHub) |
| `ASIXc2-ITB6` | **DMZ** | `10.6.1.0/24` | Servicios accesibles por los clientes: web y FTP |
| `ASIXc2-ITB6a` | **Intranet** | `10.6.2.0/24` | Datos (BD) y clientes |

## 4. Direccionamiento IP

| Equipo | Rol | Interfaz | Red | IP | Asignación |
|---|---|---|---|---|---|
| `R-N06` | Router, DNS, DHCP, monitor | `enp1s0` | NAT (Isard) | `192.168.121.184/22` (ejemplo) | DHCP |
| | | `enp2s0` | DMZ | `10.6.1.1/24` | Estática |
| | | `enp3s0` | Intranet | `10.6.2.1/24` | Estática |
| `W-N06` | Servidor web | `enp1s0` | NAT (Isard) | DHCP | DHCP |
| | | `enp2s0` | DMZ | `10.6.1.10/24` | Estática |
| `F-N06` | Servidor FTP | `enp1s0` | NAT (Isard) | `192.168.121.92/22` (ejemplo) | DHCP |
| | | `enp2s0` | DMZ | `10.6.1.20/24` | Estática |
| `B-N06` | Base de datos | `enp1s0` | NAT (Isard) | DHCP | DHCP |
| | | `enp2s0` | Intranet | `10.6.2.10/24` | Estática |
| `PC-LINUX-N06` | Cliente Linux | — | Intranet | `10.6.2.100–200` | DHCP (router) |
| PC Windows | Cliente Windows | — | Intranet | `10.6.2.100–200` | DHCP (router) |

Notas:

- Las IPs del lado `Default` las asigna Isard por DHCP y **pueden cambiar**; no se usan en ninguna configuración del proyecto.
- Todos los servidores tienen **dos interfaces**: una hacia la red `Default` (solo para instalar paquetes y acceder a GitHub) y otra hacia su zona (DMZ o Intranet).
- `W-N06` solo tiene una ruta por defecto (por `enp1s0`) y una **ruta estática** `10.6.2.0/24 via 10.6.1.1` por `enp2s0` para llegar a la Intranet a través del router. Sin esta ruta, la aplicación no conecta con MySQL aunque el firewall esté bien.

> **⚠️ PENDIENTE:** confirmar nombre de interfaz de `B-N06` hacia la Intranet (`enp2s0` según su documentación) y completar la IP del equipo Windows y su nombre de host.

## 5. Servicios y puertos

| Máquina | Servicio | Puerto | Quién puede acceder |
|---|---|---|---|
| `R-N06` | SSH | 22/TCP | Administración |
| `R-N06` | DNS (Bind9) | 53/UDP y TCP | DMZ e Intranet |
| `R-N06` | DHCP | 67-68/UDP | Solo Intranet (`enp3s0`) |
| `R-N06` | Uptime Kuma (Docker) | 3001/TCP | Administración |
| `W-N06` | Apache + PHP | 80/TCP | Clientes |
| `W-N06` | SSH | 22/TCP | Administración |
| `F-N06` | FTP (control) | 21/TCP | Clientes |
| `F-N06` | FTP (datos pasivo) | 40000-40100/TCP | Clientes |
| `F-N06` | SSH | 22/TCP | Administración |
| `B-N06` | MySQL | 3306/TCP | **Solo `W-N06` (`10.6.1.10`)** |
| `B-N06` | SSH | 22/TCP | Administración |

Cortafuegos locales: `F-N06` y `B-N06` usan UFW con política `deny incoming`. Ver [6.2](#62-cortafuegos-locales-ufw).

> **⚠️ PENDIENTE:** confirmar si `W-N06` tiene UFW activo y qué reglas lleva, y si Apache escucha también en 443.

## 6. Política de firewall y flujos permitidos

### 6.1 Router (`iptables`, cadena `FORWARD`)

Reglas aplicadas en `R-N06`, en este orden (guardadas en `/etc/iptables/rules.v4`):

| Nº | Regla | Efecto |
|---|---|---|
| 1 | `-m state --state ESTABLISHED,RELATED -j ACCEPT` | Deja pasar las respuestas de conexiones ya abiertas |
| 2 | `-i enp3s0 -j ACCEPT` | La Intranet puede ir a cualquier sitio |
| 3 | `-i enp2s0 -o enp1s0 -j ACCEPT` | La DMZ puede salir a Internet |
| 4 | `-i enp2s0 -o enp3s0 -s 10.6.1.10 -d 10.6.2.10 -p tcp --dport 3306 -j ACCEPT` | **Única excepción**: `W-N06` puede consultar MySQL en `B-N06` |
| 5 | `-i enp2s0 -o enp3s0 -j DROP` | El resto del tráfico de la DMZ hacia la Intranet se bloquea |

NAT: `iptables -t nat -A POSTROUTING -o enp1s0 -j MASQUERADE` (las dos redes internas salen a Internet con la IP del router). El reenvío está activado con `net.ipv4.ip_forward=1`.

### 6.2 Cortafuegos locales (UFW)

| Máquina | Política | Reglas permitidas |
|---|---|---|
| `B-N06` | `deny incoming` / `allow outgoing` | `22/tcp` desde cualquiera; `3306/tcp` solo desde `10.6.1.10` |
| `F-N06` | `deny incoming` / `allow outgoing` | `22/tcp`, `21/tcp` y `40000:40100/tcp` desde cualquiera |

La base de datos tiene así **doble protección**: el router solo deja pasar a `W-N06` y el UFW de `B-N06` vuelve a filtrar el mismo origen.

### 6.3 Matriz de flujos

| Origen | Destino | Puerto | Permitido | Motivo |
|---|---|---|---|---|
| Clientes (Intranet) | `W-N06` | TCP 80 | ✅ | Acceso a la aplicación (`http://w-n06.g06.lan`) |
| Clientes (Intranet) | `F-N06` | TCP 21 + 40000-40100 | ✅ | Acceso FTP |
| Clientes (Intranet) | `B-N06` | TCP 3306 | ❌ | Lo bloquea el UFW de `B-N06` (solo acepta `W-N06`) |
| `W-N06` | `B-N06` | TCP 3306 | ✅ | Consultas de la aplicación |
| DMZ (resto) | Intranet | cualquiera | ❌ | Regla 5 del router |
| DMZ | Internet | cualquiera | ✅ | Actualizaciones (por NAT) |
| Intranet | Internet | cualquiera | ✅ | Actualizaciones y navegación (por NAT) |
| Cualquiera | `R-N06` | UDP/TCP 53 | ✅ | DNS |
| Clientes Intranet | `R-N06` | UDP 67/68 | ✅ | DHCP |
| Administración | Todas | TCP 22 | ✅ | SSH con `bchecker` |

## 7. DNS y DHCP

Ambos servicios corren en `R-N06`. Los servidores tienen IP fija; solo los clientes de la Intranet usan DHCP.

### 7.1 DNS (Bind9)

- Dominio: `g06.lan` (zona maestra en `/etc/bind/db.g06.lan`).
- Reenviadores: `8.8.8.8` y `1.1.1.1`. Recursión activada, `allow-query { any; }`, `dnssec-validation no`.

| Nombre | Tipo | Valor |
|---|---|---|
| `r-n06.g06.lan` | A | `10.6.2.1` |
| `r.g06.lan` | A | `10.6.2.1` |
| `w-n06.g06.lan` | A | `10.6.1.10` |
| `f-n06.g06.lan` | A | `10.6.1.20` |
| `b-n06.g06.lan` | A | `10.6.2.10` |

Prueba: `dig @localhost r.g06.lan` devuelve `10.6.2.1` (`status: NOERROR`, bandera `aa`).

### 7.2 DHCP (isc-dhcp-server)

Solo escucha en `enp3s0` (Intranet).

| Parámetro | Valor |
|---|---|
| Subred | `10.6.2.0/255.255.255.0` |
| Rango | `10.6.2.100` – `10.6.2.200` |
| Puerta de enlace | `10.6.2.1` |
| DNS | `10.6.2.1` |
| Dominio | `g06.lan` |
| Concesión | 600 s por defecto, 7200 s máximo |

Resultado verificado en el cliente Linux: recibe IP por DHCP, `ping 8.8.8.8` responde y `ping r-n06` resuelve a `10.6.2.1`.

> **⚠️ PENDIENTE:** los servidores (`W-N06`, `F-N06`, `B-N06`) no tienen configurado `10.6.2.1`/`10.6.1.1` como DNS, por lo que no resuelven `g06.lan` por nombre. Si se quiere que lo hagan, añadir `nameservers` en su Netplan y documentarlo.

## 8. Dependencias entre máquinas

| Máquina | Depende de | Para qué |
|---|---|---|
| `W-N06` | `R-N06` (regla FORWARD y ruta `10.6.2.0/24`) | Llegar a la base de datos |
| `W-N06` | `B-N06` (usuario MySQL con `SELECT` y `bind-address`) | Mostrar los datos |
| `B-N06` | `R-N06` | Recibir tráfico solo desde `W-N06` |
| Clientes | `R-N06` (DHCP y DNS) | Obtener IP y resolver `w-n06.g06.lan` |
| Clientes | `W-N06` y `B-N06` | Ver la aplicación (la web falla sin BD) |
| Todos | Red `Default` de Isard | Instalar paquetes y acceder a GitHub |

Orden de despliegue recomendado: `R-N06` (red y firewall) → `B-N06` (MySQL e importación) → `W-N06` (web) → `F-N06` → clientes.

## 9. Justificación del diseño

| Decisión | Motivo |
|---|---|
| **DMZ separada de la Intranet** | Los servicios expuestos (web, FTP) quedan en una zona aislada de los datos. Si un servidor de la DMZ se compromete, no tiene acceso libre a la Intranet. |
| **Un solo agujero DMZ → Intranet (3306 desde `W-N06`)** | Principio de mínimo privilegio: solo se abre lo imprescindible para la aplicación. |
| **BD sin acceso desde clientes ni Internet** | La base de datos solo es accesible a través de la aplicación, no directamente. |
| **Usuario `webuser` de solo lectura** | La aplicación solo necesita `SELECT`; así una inyección SQL no puede modificar los datos. |
| **FTP con chroot y sin acceso anónimo** | Cada usuario queda encerrado en su carpeta y no puede ver el resto del servidor. |
| **Router con NAT** | Las redes internas usan direccionamiento privado y salen a Internet con una única IP. |
| **IP fija en servidores, DHCP en clientes** | Los servicios necesitan direcciones estables (DNS, firewall); los clientes no. |
| **DNS propio (`g06.lan`)** | Permite acceder por nombre (`w-n06.g06.lan`) y es un requisito del enunciado (`R-N06` y `R`). |
| **Uptime Kuma en el router** | Un punto central con visibilidad sobre todas las redes. |

## 10. Usuario `bchecker` y acceso SSH

El enunciado exige un usuario administrador común en **todas** las máquinas: `bchecker` (contraseña `bchecker121`), miembro del grupo `sudo`.

```bash
sudo adduser bchecker
sudo usermod -aG sudo bchecker
```

Script reutilizable: [`scripts/crear_bchecker.sh`](../../scripts/crear_bchecker.sh) (idempotente: se puede ejecutar varias veces).

Todas las máquinas tienen `openssh-server` instalado y habilitado:

```bash
sudo apt install -y openssh-server
sudo systemctl enable --now ssh
```

| Equipo | Comando de acceso |
|---|---|
| `R-N06` | `ssh bchecker@10.6.1.1` (o `10.6.2.1`) |
| `W-N06` | `ssh bchecker@10.6.1.10` |
| `F-N06` | `ssh bchecker@10.6.1.20` |
| `B-N06` | `ssh bchecker@10.6.2.10` |

> **Seguridad:** la contraseña `bchecker121` es solo de laboratorio, impuesta por el enunciado. En un entorno real se usaría autenticación por clave pública con `PasswordAuthentication no`, `PermitRootLogin no` y `AllowUsers`.

> **⚠️ PENDIENTE:** el cortafuegos de `B-N06` permite SSH desde cualquier origen, pero el router bloquea DMZ → Intranet. Anotar desde qué máquina se administra `B-N06` por SSH (un cliente de la Intranet, por ejemplo).

## 11. Git y flujo de trabajo

**Repositorio:** `P0.0-ASIXc2gC-G06` (GitHub) · **Rama principal:** `main`

### 11.1 Autenticación

`W-N06` se autentica en GitHub con un par de claves SSH (`ed25519`), sin contraseña:

```bash
ssh-keygen -t ed25519 -C "w-n06@P0.0-ASIXc2gC-G06" -f ~/.ssh/id_ed25519_github
```

La clave **pública** se registra en GitHub (*Settings → Deploy keys → Add deploy key*, con *Allow write access*). La **privada** no sale nunca del servidor. Configuración en `~/.ssh/config`:

```text
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/id_ed25519_github
    IdentitiesOnly yes
```

Comprobación: `ssh -T git@github.com`.

> **⚠️ PENDIENTE:** el índice del proyecto menciona un *Personal Access Token (PAT)*. Si los compañeros suben por HTTPS con PAT en lugar de clave SSH, describirlo aquí (alcance mínimo del token, caducidad y **nunca** incluir el token en el repositorio). Si todos usan SSH, eliminar la mención del PAT del título.

### 11.2 Configuración inicial

```bash
git config --global user.name  "<nombre>"
git config --global user.email "<correo>"
git config --global init.defaultBranch main
git config --global pull.rebase true
```

`pull.rebase true` mantiene el historial lineal (sin commits de *merge* innecesarios).

### 11.3 Flujo de trabajo

```bash
git status
git add docs/02-w-n06/ app/ config/w-n06/
git commit -m "W-N06: descripción breve del cambio"
git pull --rebase origin main
git push origin main
```

Reglas del equipo:

1. Hacer `pull --rebase` **antes** de cada `push`.
2. Cada integrante trabaja solo en sus carpetas (`docs/0X-*`, `config/*`), lo que evita conflictos.
3. Mensajes de commit con prefijo: `R-N06:`, `W-N06:`, `B-N06:`, `F-N06:`, `DOCS:`.
4. No subir claves privadas ni temporales (ver `.gitignore`).

| Situación | Solución |
|---|---|
| `Permission denied (publickey)` | Revisar la deploy key, `~/.ssh/config` y `ssh -T git@github.com` |
| `rejected (non-fast-forward)` | `git pull --rebase origin main` y repetir el `push` |
| Commit con mensaje erróneo (sin `push`) | `git commit --amend -m "nuevo mensaje"` |
| Fichero añadido por error | `git restore --staged <fichero>` |

### 11.4 Despliegue de la aplicación (repositorio → servidor)

```bash
cd ~/P0.0-ASIXc2gC-G06
git pull origin main
sudo rsync -av --delete app/ /var/www/html/
sudo chown -R www-data:www-data /var/www/html
sudo systemctl reload apache2
```

```text
Edición → git add/commit → git push → (W-N06) git pull → rsync → Apache
```

El servidor web siempre refleja el contenido de `main`.

## 12. Ficheros de configuración clave

| Máquina | Fichero | Función |
|---|---|---|
| `R-N06` | `/etc/netplan/50-cloud-init.yaml` | IPs de las tres interfaces |
| `R-N06` | `/etc/sysctl.conf` | `net.ipv4.ip_forward=1` |
| `R-N06` | `/etc/iptables/rules.v4` | NAT y reglas FORWARD |
| `R-N06` | `/etc/bind/named.conf.options`, `/etc/bind/db.g06.lan` | DNS |
| `R-N06` | `/etc/dhcp/dhcpd.conf` | DHCP |
| `W-N06` | `/etc/netplan/01-netcfg.yaml` | IP y ruta a la Intranet |
| `W-N06` | `/etc/apache2/sites-available/000-default.conf` | VirtualHost |
| `F-N06` | `/etc/vsftpd.conf`, `/etc/vsftpd.chroot_list` | FTP y chroot |
| `B-N06` | `/etc/mysql/mysql.conf.d/mysqld.cnf` | `bind-address` |

Copias respaldadas en el repositorio: carpeta [`config/`](../../config/).

## 13. Limitaciones conocidas y mejoras

Puntos detectados durante la revisión y que conviene reflejar en la memoria como análisis crítico:

| Observación | Mejora propuesta |
|---|---|
| La política por defecto de `FORWARD` es `ACCEPT`: solo se bloquea explícitamente DMZ → Intranet. | Cambiar a `iptables -P FORWARD DROP` y dejar solo las reglas necesarias. |
| La cadena `INPUT` del router también está en `ACCEPT`, y Uptime Kuma (`3001`) queda accesible desde la DMZ. | Restringir `INPUT` (SSH, DNS, DHCP y `3001` solo desde la Intranet). |
| Docker modifica `iptables`; si se cambian reglas hay que reiniciar Docker. | Documentar el orden: firewall primero, Docker después. |
| Credenciales en claro en `app/db.php` y en la documentación. | Variables de entorno o fichero fuera del `DocumentRoot` con permisos restringidos. |
| FTP sin cifrado (vsftpd sin TLS). | Activar FTPS (`ssl_enable=YES`) o usar SFTP. |
| Los servidores tienen una segunda interfaz directa a Internet, que evita el router. | En un entorno real, sacar todo el tráfico por el router. |

## 14. Reparto de responsabilidades

| Persona | Responsabilidad |
|---|---|
| **Aman** | `R-N06` (NAT, firewall, Bind9, DHCP, Uptime Kuma) y clientes |
| **Alex** | `B-N06` (MySQL e importación del CSV) y `F-N06` (FTP) |
| **Jheremy** | `W-N06` (Apache + PHP), Git |
| **Todos** | Usuario `bchecker`, SSH en sus máquinas y gestión de tareas en ProofHub |

---

**Documentación relacionada:** [R-N06](../01-r-n06.md) · [W-N06](../02-w-n06.md) · [F-N06](../03-f-n06.md) · [B-N06](../04-b-n06.md) · [Clientes](../05-clientes.md)
