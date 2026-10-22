# P0.0-ASIXc2gC-G06 — Despliegue de infraestructura

Proyecto intermodular (Módulo 0379). Infraestructura para una aplicación multicapa:
servidor web, monitor de red, SSH, base de datos, DHCP, DNS y FTP, desplegada en **IsardVDI**.

## Equipo 06

| Persona | Responsabilidad |
|---|---|
| **Aman** | Router R-N06 (NAT + firewall), DNS, DHCP, monitor de red, clientes Windows y Linux |
| **Alex** | B-N06 (MySQL + importación del CSV), F-N06 (FTP) |
| **Jheremy** | W-N06 (Apache + app), repositorio GitHub, documentación y diagrama |
| **Todos** | SSH y usuario `bchecker` en sus máquinas, tareas en ProofHub |

## Máquinas

| Hostname | Rol | Red | IP |
|---|---|---|---|
| R-N06 | Router, NAT, firewall, DNS, DHCP, Uptime Kuma | NAT / DMZ / Intranet | DHCP Isard / 10.6.1.1 / 10.6.2.1 |
| W-N06 | Servidor web + aplicación | DMZ | 10.6.1.10 |
| F-N06 | Servidor FTP | DMZ | 10.6.1.20 |
| B-N06 | MySQL | Intranet | 10.6.2.10 |
| PC Windows | Cliente | Intranet | DHCP (10.6.2.100–200) |
| PC Linux | Cliente | Intranet | DHCP (10.6.2.100–200) |

Dominio interno: `g06.lan`. Usuario común en todos los sistemas y aplicaciones: **bchecker / bchecker121**.

## Documentación

1. [Arquitectura y direccionamiento](docs/00-arquitectura.md)
2. [Router R-N06](docs/01-router.md)
3. [DNS y DHCP](docs/02-dns-dhcp.md)
4. [Monitor de red](docs/03-monitor.md)
5. [Base de datos B-N06](docs/04-mysql.md)
6. [Servidor web y aplicación W-N06](docs/05-web-app.md)
7. [Servidor FTP F-N06](docs/06-ftp.md)
8. [SSH y usuario bchecker](docs/07-ssh-usuarios.md)
9. [Control de versiones y despliegue](docs/08-git.md)
10. [Clientes](docs/09-clientes.md)
11. [Pruebas](docs/10-pruebas.md)
12. [Backlog y sprints (ProofHub)](docs/backlog.md)

## Estructura del repositorio

```
.
├── README.md
├── docs/                    Documentación Markdown
├── config/
│   ├── r-n06/               netplan, firewall, bind9, isc-dhcp-server
│   ├── w-n06/               netplan
│   ├── f-n06/               netplan, vsftpd
│   └── b-n06/               netplan, SQL de creación
├── app/                     Aplicación web (PHP) que muestra la tabla
└── scripts/                 crear_bchecker.sh, import_csv.py
```

## Documentación del Proyecto

1. [Arquitectura y direccionamiento](docs/01-arquitectura.md)
2. [Router R-N06](docs/02-router-r-n06.md)
3. [DNS y DHCP](docs/03-dns-dhcp.md)
4. [Monitor de red](docs/04-monitor-red.md)
5. [Base de datos B-N06](docs/05-bd-b-n06.md)
6. [Servidor web y aplicación W-N06](docs/06-servidor-web-w-n06.md)
7. [Servidor FTP F-N06](docs/07-servidor-ftp-f-n06.md)
8. [SSH y usuario bchecker](docs/08-ssh-bchecker.md)
9. [Control de versiones y despliegue](docs/09-control-versiones.md)
10. [Clientes](docs/10-clientes.md)
11. [Pruebas](docs/11-pruebas.md)
12. [Backlog y sprints (ProofHub)](https://tu-enlace-de-proofhub.com)
