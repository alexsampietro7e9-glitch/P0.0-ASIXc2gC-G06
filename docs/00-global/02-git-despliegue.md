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
