# Estudio previo y análisis de riesgos

**Proyecto:** P0.0-ASIXc2gC-G06 – Despliegue de infraestructura multicapa
**Módulo:** 0379 Projecte intermodular

En este documento justificamos las tecnologías que hemos elegido, analizamos los riesgos técnicos y de proyecto que tiene la infraestructura y recogemos las medidas de prevención de riesgos laborales aplicables a nuestro trabajo.

## Índice

1. [Estudio previo: elección de tecnologías](#1-estudio-previo-elección-de-tecnologías)
2. [Coste del proyecto](#2-coste-del-proyecto)
3. [Metodología de análisis de riesgos](#3-metodología-de-análisis-de-riesgos)
4. [Riesgos técnicos](#4-riesgos-técnicos)
5. [Riesgos de proyecto](#5-riesgos-de-proyecto)
6. [Riesgos aceptados por el enunciado](#6-riesgos-aceptados-por-el-enunciado)
7. [Prevención de riesgos laborales (PRL)](#7-prevención-de-riesgos-laborales-prl)
8. [Conclusiones](#8-conclusiones)

---

## 1. Estudio previo: elección de tecnologías

Antes de desplegar comparamos las alternativas para cada servicio que pide el enunciado (web, monitor de red, SSH, BBDD, DHCP, DNS y FTP). El criterio principal ha sido usar software libre, conocido en ASIX y que funcione en Ubuntu Server dentro de NuvuletVDI (Isard).

| Servicio | Alternativas valoradas | Elegido | Motivo |
|---|---|---|---|
| Sistema operativo | Ubuntu Server / Debian / Windows Server | **Ubuntu Server** | Gratuito, el que usamos en clase, mucha documentación |
| Virtualización | NuvuletVDI (Isard) / VirtualBox local | **NuvuletVDI** | Lo proporciona el centro; las redes DMZ e Intranet ya existen |
| Router y firewall | iptables / nftables / pfSense | **iptables + UFW** | Ya incluido en Ubuntu; pfSense necesitaría otra VM y otra interfaz |
| Servidor web | Apache / Nginx | **Apache + PHP** | Integración directa con PHP (`libapache2-mod-php`), visto en el módulo 0376 |
| Base de datos | MySQL / MariaDB / PostgreSQL | **MySQL 8.0** | Lo pide el enunciado; `LOAD DATA INFILE` importa el CSV directamente |
| FTP | vsftpd / ProFTPD | **vsftpd** | Ligero, seguro por defecto y con `chroot` sencillo |
| DNS | Bind9 / dnsmasq | **Bind9** | Estándar en Linux y permite zona propia `g06.lan` |
| DHCP | isc-dhcp-server / dnsmasq | **isc-dhcp-server** | Configuración clara por subred, visto en clase |
| Monitor de red | Uptime Kuma / Zabbix / Nagios | **Uptime Kuma (Docker)** | Instalación en minutos y panel web simple; Zabbix es excesivo para 4 equipos |
| Acceso remoto | SSH / Telnet / RDP | **OpenSSH** | Cifrado; Telnet envía todo en claro |
| Control de versiones | GitHub / GitLab | **GitHub** | Lo pide el enunciado, con autenticación por clave pública |
| Gestión de tareas | ProofHub / Trello | **ProofHub** | Lo pide el enunciado |

## 2. Coste del proyecto

| Concepto | Coste en el proyecto | Coste estimado en un entorno real |
|---|---|---|
| Máquinas virtuales (4 servidores + 2 clientes) | 0 € (NuvuletVDI del centro) | ~25-40 €/mes en un VPS o nube pública |
| Software | 0 € (todo libre) | 0 € (Windows cliente: licencia aparte) |
| GitHub y ProofHub | 0 € (cuentas educativas) | 0-50 €/mes según plan |
| Horas de trabajo | 3 sprints × 10 h × 3 personas = **90 h** | 90 h × ~20 €/h técnico júnior ≈ 1.800 € |

## 3. Metodología de análisis de riesgos

Para cada riesgo valoramos la **probabilidad** y el **impacto** del 1 al 3. El **nivel** es el producto de los dos:

| Nivel | Valor | Acción |
|---|---|---|
| Bajo | 1-2 | Se acepta y se vigila |
| Medio | 3-4 | Se aplica alguna medida |
| Alto | 6-9 | Medida obligatoria antes de dar el servicio por terminado |

## 4. Riesgos técnicos

| ID | Riesgo | Activo afectado | P | I | Nivel | Medida aplicada |
|---|---|---|---|---|---|---|
| T1 | Acceso directo de los clientes a la base de datos | `B-N06` | 2 | 3 | Alto 6 | BD en la Intranet; el router solo deja pasar `10.6.1.10 → 10.6.2.10:3306` y el UFW de `B-N06` vuelve a filtrar el mismo origen (doble protección) |
| T2 | Inyección SQL en la aplicación web | `W-N06`, `B-N06` | 2 | 3 | Alto 6 | PDO, lista blanca de tablas con `SHOW TABLES`, página convertida a entero y usuario `webuser` solo con `SELECT` |
| T3 | Fuga de contraseñas al subirlas a GitHub | Credenciales | 2 | 3 | Alto 6 | `db.php` fuera del DocumentRoot (`/etc/w-n06/`, permisos `640`) y en el repositorio solo aparecen marcadores `<PASSWORD_...>` |
| T4 | Un servidor de la DMZ comprometido salta a la Intranet | Intranet | 1 | 3 | Medio 3 | Regla `DROP` de DMZ a Intranet en el router, salvo la excepción del 3306 |
| T5 | Caída del router (punto único de fallo) | Toda la red | 1 | 3 | Medio 3 | Uptime Kuma avisa de la caída; configuración de iptables guardada en `/etc/iptables/rules.v4` y documentada en Git |
| T6 | Pérdida de los datos de la BD | `B-N06` | 1 | 2 | Bajo 2 | El CSV original se puede volver a importar; recomendado `mysqldump` periódico |
| T7 | Usuario FTP navega por todo el sistema de ficheros | `F-N06` | 2 | 2 | Medio 4 | `chroot_local_user=YES`: cada usuario queda encerrado en su carpeta |
| T8 | Escaneo de puertos y servicios innecesarios expuestos | Todos | 2 | 2 | Medio 4 | UFW con `deny incoming` y solo los puertos necesarios abiertos (ver matriz de flujos en [01-arquitectura-git.md](01-arquitectura-git.md)) |
| T9 | Información del servidor visible (versión de Apache) | `W-N06` | 2 | 1 | Bajo 2 | `ServerTokens Prod`, `ServerSignature Off` y `TraceEnable Off` |
| T10 | Cambio de IP de la red `Default` de Isard | Todos | 3 | 1 | Medio 3 | Ninguna configuración usa esas IPs; DMZ e Intranet tienen IP estática |
| T11 | Caída de la plataforma NuvuletVDI | Todos | 1 | 3 | Medio 3 | Toda la configuración y los pasos están en Git para poder rehacer las máquinas |

## 5. Riesgos de proyecto

| ID | Riesgo | P | I | Nivel | Medida aplicada |
|---|---|---|---|---|---|
| P1 | Retraso de una parte que bloquea a las demás (por ejemplo, el router) | 2 | 3 | Alto 6 | Tareas con responsable y fecha en ProofHub; revisión del tablón en cada sesión |
| P2 | Mala estimación del tiempo | 3 | 2 | Alto 6 | En el Sprint 1 las tareas eran demasiado genéricas; a partir del Sprint 2 las dividimos en tareas concretas con fecha |
| P3 | Conflictos al editar el repositorio a la vez | 2 | 1 | Bajo 2 | Un archivo de documentación por máquina y `git pull` antes de cada `push` |
| P4 | Dependencia de una sola persona en un servicio | 2 | 2 | Medio 4 | Documentación paso a paso de cada máquina para que cualquier miembro pueda rehacerla |
| P5 | Ausencia de un miembro del equipo | 2 | 2 | Medio 4 | Credenciales comunes `bchecker` y acceso SSH a todas las máquinas |

## 6. Riesgos aceptados por el enunciado

Algunas decisiones no serían correctas en producción, pero las impone el enunciado o el entorno de laboratorio. Las dejamos identificadas:

| Riesgo aceptado | Motivo | Qué haríamos en un entorno real |
|---|---|---|
| Contraseña común y débil `bchecker121` en todos los equipos | La exige el enunciado para la corrección | Autenticación SSH por clave pública, `PasswordAuthentication no`, `PermitRootLogin no` y `AllowUsers` |
| FTP sin cifrar (usuario y contraseña viajan en claro) | El enunciado pide FTP | FTPS (`ssl_enable=YES`) o sustituirlo por SFTP |
| Certificado HTTPS autofirmado | No hay dominio público ni CA | Certificado de Let's Encrypt |
| Sin copias de seguridad automáticas ni alta disponibilidad | Fuera del alcance de la práctica | `mysqldump` programado con `cron`, router redundante |

## 7. Prevención de riesgos laborales (PRL)

Nuestro trabajo es de oficina con pantalla de visualización de datos (PVD). La normativa de referencia es la **Ley 31/1995 de Prevención de Riesgos Laborales** y el **RD 488/1997** sobre trabajo con equipos con pantallas de visualización.

| Riesgo | Causa | Medida preventiva |
|---|---|---|
| Trastornos musculoesqueléticos | Postura incorrecta durante sesiones largas | Silla regulable, espalda apoyada, pies en el suelo, antebrazos apoyados en la mesa |
| Fatiga visual | Muchas horas leyendo terminal y documentación | Pantalla a la altura de los ojos y a 50-70 cm, sin reflejos; regla 20-20-20 (cada 20 min, mirar a 6 m durante 20 s) |
| Fatiga mental y estrés | Fechas de entrega y problemas técnicos bloqueantes | Planificación por sprints, reparto equilibrado de tareas y pausas de 5-10 min por cada hora |
| Riesgo eléctrico | Equipos y regletas del aula | No sobrecargar regletas ni manipular equipos encendidos; avisar al profesor de cualquier cable dañado |
| Condiciones ambientales | Iluminación, ruido y temperatura del aula | Iluminación adecuada sin deslumbramientos y ventilación del aula |

## 8. Conclusiones

- Los riesgos más altos (**T1, T2 y T3**) afectan a la base de datos y a sus credenciales, y son los que más medidas tienen: separación DMZ/Intranet, doble firewall, usuario de solo lectura y credenciales fuera del repositorio.
- Los riesgos que quedan abiertos (contraseña común, FTP en claro) los impone el enunciado y están identificados con su alternativa para producción.
- En la gestión del proyecto, el principal riesgo ha sido la estimación del tiempo: el Sprint 1 nos enseñó a dividir las tareas en partes más pequeñas y con fecha.
