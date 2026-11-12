# Sprints y backlog

**Proyecto:** P0.0-ASIXc2C-G06 – Servidor Web

En este documento recogemos cómo hemos organizado el trabajo en cada sprint, qué tareas hemos definido y quién se ha encargado de cada una. Gestionamos las tareas en un tablón de proofhub con las columnas *Pendents backlog (To Do)*, *Assignades sprint*, *En curs (Doing)*, *Bloquejades (Block)*, *Pendents de Revisió (Review)* y *Finalitzades (Done)*.

## Equipo

| Iniciales | Integrante |
|---|---|
| JS | Jheremy Sanchez |
| SA | Amanpreet Singh |
| AS | Alex Sampietro |

## Resumen de los sprints

| Sprint | Enfoque | Estado |
|---|---|---|
| Sprint 1 | Tareas genéricas, agrupadas por servicio y sin fechas | Cerrado |
| Sprint 2 | Tareas específicas, repartidas por fechas de vencimiento | Cerrado |
| Sprint 3 | Documentación, comprobaciones de los servidores, firewall y cierre del servidor web | En curso |

---

## Sprint 1: tareas genéricas

En el primer sprint definimos 11 tareas muy generales, pensando que podríamos acabar todos los servicios en el primer sprint dejando los otros dos para revisar y documentar, sin embargo, acabamos dejando varios servicios a la mitad. Asi que para mejor en nuestro siguiente sprint decidimos añadir tareas más específicas , para atacar los distintos objetivos con un orden más claro.

### Pendents backlog (To Do): 1 tarea

| Tarea | Detalle | Asignado a |
|---|---|---|
| Servidor Web | Aplicación de prueba | JS |

### Assignades sprint: 7 tareas

| Tarea | Detalle | Asignado a |
|---|---|---|
| Documentation | – | SA, JS y AS |
| Servidor de xarxa | SSH | AS |
| Servidor de xarxa | DHCP | AS |
| Servidor de xarxa | DNS | AS |
| Servidor de xarxa | Monitor de red | AS |
| Servidor de fitxers | BBDD MySQL + SSH | SA |
| Hacer diagrama | - | AS |

> En la captura del tablón del Sprint 1 una de las siete tareas de esta columna queda cortada y no se puede leer.

### En curs (Doing): 3 tareas

| Tarea | Detalle | Asignado a |
|---|---|---|
| Servidor Web | SSH | JS |
| Servidor Web | Apache | JS |
| Fer diagrama | – | SA |

---

## Sprint 2: tareas específicas con fechas

En el segundo sprint ampliamos las tareas y las hicimos más específicas. Además, dividimos el trabajo del servidor de ficheros en dos tareas (*Servidor de ficheros* y *Servidor BBDD*) y asignamos a cada tarea una fecha de vencimiento para controlar mejor el avance. En la captura de seguimiento del sprint teníamos finalizadas 5 de las 11 tareas. Las tareas finalizadas són las que terminamos en el sprint 1. Las otras las añadimosa medida que avanzabamos en dicho sprint. Al final Nos quedo asi el proofhub para el sprint 2:

### Tareas del Sprint 2

| Tarea | Detalle | Fecha | Asignado a | Estado en la captura de seguimiento |
|---|---|---|---|---|
| Fer diagrama | – | 19 Oct | SA | Finalizada |
| Servidor de xarxa | Monitor de red | 26 Oct | AS | Finalizada |
| Servidor BBDD | Servicio BBDD | 26 Oct | SA | Finalizada |
| Servidor de xarxa | DHCP | 26 Oct | AS | Finalizada |
| Estructurar Github | – | 26 Oct | JS | Finalizada |
| Servidor de xarxa | DNS | 26 Oct | AS | En revisión |
| Servidor Web | SSH | 02 Nov | JS | En curso |
| Servidor Web | Apache | 02 Nov | JS | En curso |
| Servidor de ficheros | – | 02 Nov | SA | En curso |
| PC linux y windows | – | 02 Nov | Sin asignar en la captura | Asignada al sprint |
| Documentación | – | 13 Nov | SA, JS y AS | Asignada al sprint |

### Evolución respecto al Sprint 1

- Los servicios de red (DHCP y Monitor de red) pasaron a finalizados y DNS quedó en revisión.
- El servidor de ficheros y la base de datos pasaron de ser una sola tarea a dos tareas independientes.
- El diagrama pasó de En curso a Finalizado.
- Añadimos tareas nuevas: *Estructurar Github* y *PC linux y windows*.

---

## Sprint 3: documentación, comprobaciones y cierre del servidor web

En el tercer sprint nos centramos en documentar todo lo que hemos montado, comprobar que cada servidor funciona correctamente y cerrar el servidor web. Al abrir el sprint el backlog estaba vacío y en la columna de finalizadas acumulábamos ya 21 tareas de los sprints anteriores y del propio sprint 3.

### Assignades sprint: 7 tareas

| Tarea | Asignado a |
|---|---|
| Documentar Manual clientes | AS |
| Documentar Arquitectura | AS |
| Documentar manual Admin | SA |
| Comprobació servidor Ficheros | SA y AS |
| Comprobación servidor web | SA y AS |
| Comprobación servidor Base de dadas | SA y AS |
| Acabar de documentar las comprobaciones de servidores | SA y AS |

### En curs (Doing): 4 tareas

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Documentar la configuració del servidor web i la publicació de l'App | – | 09 Nov (vencida) | JS |
| Sprint | Documentación | – | SA |
| Firewall | Auditar las reglas de firewall en R-N06 y B-N06 para asegurar que solo el tráfico permitido circula entre la DMZ y el resto de redes | 13 Nov | AS |
| Comprobación del servidor de xarxa | – | – | SA y AS |

### Finalitzades (Done) durante el Sprint 3

| Tarea | Detalle | Fecha | Asignado a |
|---|---|---|---|
| Servidor de xarxa | Añadir y verificar en los paneles de Uptime Kuma la comprobación de estado de los servicios web | 09 Nov | AS |
| Servidor WEB | Desarrollar y publicar el código fuente PHP | 09 Nov | JS |
| Servidor Web | SSH | 09 Nov | JS |
| Servidor Web | Apache + PHP | 09 Nov | JS |
| Documentación | Servidor ficheros | 13 Nov | SA |

> En la captura del Sprint 3 la columna de finalizadas tiene 21 tareas y solo se ven algunas, por lo que esta tabla recoge únicamente las visibles.

### Pendiente del servidor web

Lo único que nos queda abierto del servidor web es documentar su configuración y la publicación de la aplicación, tarea asignada a JS y con fecha vencida (09 Nov), y terminar de desarrollar la web. Cuando lo cerremos, actualizaremos este documento y lo enlazaremos con la documentación de arquitectura y de administración.

---

## Reparto de tareas por integrante

| Integrante | Sprint 1 | Sprint 2 | Sprint 3 |
|---|---|---|---|
| **JS** | Servidor Web (SSH, Apache y aplicación de prueba) | Estructurar Github, Servidor Web SSH y Apache | Servidor Web (SSH, Apache + PHP y código PHP) y documentación de la configuración del servidor web |
| **SA** | Servidor de fitxers (BBDD MySQL + SSH) y Fer diagrama | Fer diagrama, Servidor BBDD y Servidor de ficheros | Documentar manual Admin, documentación del servidor de ficheros y comprobaciones de servidores |
| **AS** | Servidor de xarxa (SSH, DHCP, DNS y Monitor de red) | Servidor de xarxa (DHCP, DNS y Monitor de red) | Documentar Manual clientes y Arquitectura, Firewall, Uptime Kuma y comprobaciones de servidores |

La tarea de documentación general la hemos compartido los tres en todos los sprints.
