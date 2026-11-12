# Sprints y backlog

**Proyecto:** P0.0-ASIXc2C-G06 – Servidor Web

En este documento recogemos cómo hemos organizado el trabajo en cada sprint, qué tareas hemos definido y quién se ha encargado de cada una. Gestionamos las tareas en un tablón Kanban con las columnas *Pendents backlog (To Do)*, *Assignades sprint*, *En curs (Doing)*, *Bloquejades (Block)*, *Pendents de Revisió (Review)* y *Finalitzades (Done)*. Para cada sprint tomamos una captura del tablón al empezar y otra al cerrar, y la del cierre de un sprint coincide con el inicio del siguiente.

## Equipo

| Iniciales | Integrante |
|---|---|
| JS | Jheremy Sanchez |
| SA | Amanpreet Singh |
| AS | Alex Sampietro |

## Resumen de los sprints

| Sprint | Enfoque | Inicio (tablón) | Cierre (tablón) |
|---|---|---|---|
| Sprint 1 | Tareas genéricas por servicio, sin fechas | 0 de 11 finalizadas | 5 de 11 finalizadas |
| Sprint 2 | Tareas específicas repartidas por fechas | 5 de 11 finalizadas | 21 finalizadas acumuladas |
| Sprint 3 | Documentación, comprobaciones, firewall y cierre del servidor web | 21 finalizadas acumuladas | En curso |

---

## Sprint 1: tareas genéricas

En el primer sprint definimos 11 tareas muy generales, agrupadas por servicio, pensando que podríamos acabarlo todos los servisios en el primer sprint.

### Inicio del Sprint 1

**Pendents backlog (To Do): 1 tarea**

| Tarea | Detalle | Asignado a |
|---|---|---|
| Servidor Web | Aplicación de prueba | JS |

**Assignades sprint: 7 tareas**

| Tarea | Detalle | Asignado a |
|---|---|---|
| Documentation | – | SA, JS y AS |
| Servidor de xarxa | SSH | AS |
| Servidor de xarxa | DHCP | AS |
| Servidor de xarxa | DNS | AS |
| Servidor de xarxa | Monitor de red | AS |
| Servidor de fitxers | BBDD MySQL + SSH | SA |
| Servidor Web | SSH | JS |
| Servidor Web | Apache | JS |
| Fer diagrama | – | SA |

### Cierre del Sprint 1

Al cerrar el sprint teníamos 5 de las 11 tareas finalizadas, 1 en revisión, 3 en curso y 2 asignadas. Además, las tareas pasaron a ser más específicas y con fecha: el trabajo de *Servidor de fitxers (BBDD MySQL + SSH)* lo separamos en *Servidor BBDD* y *Servidor de ficheros*, y añadimos *Estructurar Github* y *PC linux y windows*.

**Finalitzades (Done): 5 tareas**

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Fer diagrama | – | 19 Oct | SA |
| Servidor de xarxa | Monitor de red | 26 Oct | AS |
| Servidor BBDD | Servicio BBDD | 26 Oct | SA |
| Servidor de xarxa | DHCP | 26 Oct | AS |
| Estructurar Github | – | 26 Oct | JS |

**Pendents de Revisió (Review): 1 tarea**

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Servidor de xarxa | DNS | 26 Oct (vencida) | AS |

**En curs (Doing): 3 tareas**

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Servidor Web | SSH | 02 Nov | JS |
| Servidor Web | Apache | 02 Nov | JS |
| Servidor de ficheros | – | 02 Nov | SA |

**Assignades sprint: 2 tareas (pasan al Sprint 2)**

| Tarea | Fecha | Asignado a |
|---|---|---|
| Documentación | 13 Nov | SA, JS y AS |
| PC linux y windows | 02 Nov | Sin asignar en la captura |

---

## Sprint 2: tareas específicas con fechas

En el segundo sprint trabajamos con tareas más detalladas y con fecha de vencimiento. Partimos de las tareas que quedaron abiertas al cerrar el Sprint 1 (DNS en revisión, Servidor Web SSH y Apache, Servidor de ficheros, PC linux y windows y Documentación) y las fuimos desglosando.

### Inicio del Sprint 2

El tablón de inicio es el mismo que el de cierre del Sprint 1: 5 tareas finalizadas, 1 en revisión, 3 en curso y 2 asignadas, con las fechas indicadas arriba.

### Cierre del Sprint 2

Al cerrar el sprint el tablón mostraba 21 tareas finalizadas en total (acumulando las del Sprint 1) y no teníamos ninguna tarea bloqueada ni en revisión. Las tareas finalizadas que se ven en la captura son estas:

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Servidor de xarxa | Añadir y verificar en los paneles de Uptime Kuma la comprobación de estado de los servicios web | 09 Nov | AS |
| Servidor WEB | Desarrollar y publicar el código fuente PHP | 09 Nov | JS |
| Servidor Web | SSH | 09 Nov | JS |
| Servidor Web | Apache + PHP | 09 Nov | JS |
| Documentación | Servidor ficheros | 13 Nov | SA |
| Servidor de xarxa | Monitor de red | 26 Oct | AS |

---

## Sprint 3: documentación, comprobaciones y cierre del servidor web

En el tercer sprint nos centramos en documentar todo lo que hemos montado, comprobar que cada servidor funciona correctamente, auditar el firewall y cerrar el servidor web. Empezamos con el backlog vacío.

### Inicio del Sprint 3

**Assignades sprint: 7 tareas**

| Tarea | Asignado a |
|---|---|
| Documentar Manual clientes | AS |
| Documentar Arquitectura | AS |
| Documentar manual Admin | SA |
| Comprobació servidor Ficheros | SA y AS |
| Comprobación servidor web | SA y AS |
| Comprobación servidor Base de dadas | SA y AS |
| Acabar de documentar las comprobaciones de servidores | SA y AS |

**En curs (Doing): 4 tareas**

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Documentar la configuració del servidor web i la publicació de l'App | – | 09 Nov (vencida) | JS |
| Sprint | Documentación | – | SA |
| Firewall | Auditar las reglas de firewall en R-N06 y B-N06 para asegurar que solo el tráfico permitido circula entre la DMZ y el resto de redes | 13 Nov | AS |
| Comprobación del servidor de xarxa | – | – | SA y AS |

### Pendiente del servidor web

Del servidor web ya tenemos terminados SSH, Apache + PHP y la publicación del código PHP. Nos falta documentar su configuración y la publicación de la aplicación (tarea de JS, con la fecha del 09 Nov ya vencida) y terminar de desarrollar la web. Cuando lo cerremos, actualizaremos este documento y lo enlazaremos con la documentación de arquitectura y de administración.

### Cierre del Sprint 3

Pendiente de completar cuando cerremos el sprint.

---

## Reparto de tareas por integrante

| Integrante | Sprint 1 | Sprint 2 | Sprint 3 |
|---|---|---|---|
| **JS** | Estructurar Github; Servidor Web SSH y Apache (en curso) | Servidor Web SSH, Apache + PHP y código PHP | Documentar la configuración del servidor web y la publicación de la App |
| **SA** | Fer diagrama y Servidor BBDD; Servidor de ficheros (en curso) | Documentación del servidor de ficheros | Documentar manual Admin, comprobaciones de servidores y Sprint (Documentación) |
| **AS** | Servidor de xarxa (Monitor de red y DHCP; DNS en revisión) | Uptime Kuma y comprobación de estado de los servicios web | Documentar Manual clientes y Arquitectura, Firewall y comprobaciones de servidores |

La tarea de documentación general la hemos compartido los tres en todos los sprints.
