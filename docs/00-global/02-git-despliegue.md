# Control de Versiones y Despliegue con Git y GitHub

## 1. Autenticación mediante Personal Access Token (PAT)

Debido a la política de seguridad de GitHub que deshabilita la autenticación mediante contraseña en operaciones Git sobre HTTPS, se ha implementado la autenticación mediante un **Personal Access Token (PAT)** clásico.

### 1.1 Generación del Token en GitHub

1. Acceder a la cuenta de GitHub y navegar a **Settings** > **Developer Settings** > **Personal access tokens** > **Tokens (classic)**.
2. Seleccionar **Generate new token (classic)**.
3. Definir una descripción identificativa del equipo/servidor (ejemplo: `W-N06-Token`).
4. Asignar los permisos del alcance (*scope*): Seleccionar de manera obligatoria el grupo **`repo`** (Full control of private/public repositories).
5. Generar el token y copiar la cadena alfanumérica obtenida.

### 1.2 Configuración del Token en la URL Remota

Para evitar la solicitud constante de credenciales o errores de autenticación interactiva en los servidores sin interfaz gráfica, se vincula el PAT directamente dentro de la URL del origen remoto (`origin`).

Ejecutar en la terminal del sistema:

```bash
# Formato general de la URL remota con autenticación
git remote set-url origin https://<USUARIO>:<TOKEN>@[github.com/](https://github.com/)<PROPIETARIO>/P0.0-ASIXc2gC-G06.git

# Ejemplo de comando aplicado en el entorno local/servidor
git remote set-url origin https://jheremysanchez7e9-web:ghp_xK98aB...example...@github.com/alexsampietro7e9-glitch/P0.0-ASIXc2gC-G06.git
```

### 2. Configuración de Identidad Local

Configuración de la firma de autoría para los commits del proyecto:

```Bash
git config --global user.name "Jheremy Sánchez"
git config --global user.email "jheremysanchez7e9-web@users.noreply.github.com"
git config --global init.defaultBranch main
git config --global pull.rebase true
```

### 3. Creación y Estructura del Repositorio

La jerarquía del proyecto se organiza por módulos según la responsabilidad de cada integrante.

```Plaintext
.
├── README.md
├── .gitignore
├── docs/
│   ├── 00-global/       # Documentación transversal (Arquitectura, SSH, Git, Pruebas)
│   ├── 01-r-n06/        # Router R-N06 (Aman)
│   ├── 02-w-n06/        # Servidor Web W-N06 y aplicación PHP (Jheremy)
│   ├── 03-f-n06/        # Servidor FTP F-N06 (Alex)
│   ├── 04-b-n06/        # Base de Datos MySQL B-N06 (Alex)
│   └── 05-clientes/     # Documentación Clientes
├── config/              # Configuraciones respaldadas (r-n06, w-n06, f-n06, b-n06)
├── app/                 # Código fuente de la aplicación Web PHP (index.php, db.php, style.css)
└── scripts/             # Scripts de automatización (crear_bchecker.sh, import_csv.py)
```

Comandos ejecutados para la generación e inicialización del árbol:

```Bash
# Creación de la estructura de carpetas
mkdir -p docs/{00-global,01-r-n06,02-w-n06,03-f-n06,04-b-n06,05-clientes}
mkdir -p config/{r-n06,w-n06,f-n06,b-n06}
mkdir -p app scripts

# Creación de ficheros de documentación iniciales
touch docs/00-global/{00-arquitectura.md,01-ssh-usuarios.md,02-git-despliegue.md,03-pruebas.md,backlog.md}
touch docs/02-w-n06/01-web-app.md
```

### 4. Flujo Diario de Trabajo (Git Workflow)
Para prevenir conflictos de integración durante el desarrollo colaborativo, se aplica la siguiente secuencia de comandos:

```Bash
# 1. Sincronizar el repositorio remoto evitando commits de merge innecesarios
git pull origin main --rebase

# 2. Añadir todas las modificaciones y archivos creados al staging
git add -A

# 3. Registrar el commit local con mensaje descriptivo y prefijo
git commit -m "W-N06: Añadida documentación de despliegue Git y estructura de carpetas"

# 4. Publicar las modificaciones en GitHub
git push origin main
```
