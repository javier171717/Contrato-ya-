# ContratoYa en Hostinger — contratoya.online

Guía paso a paso para **Hostinger** (hPanel).

**URL final:** `https://contratoya.online/`

---

## Estructura en Hostinger (importante)

En Hostinger el dominio principal **siempre** apunta a `public_html`. No se puede cambiar eso en planes compartidos.

Por eso subimos así:

```
/domains/contratoya.online/
├── app/              ← carpeta del proyecto (privada)
├── system/
├── writable/
├── database/
├── .env
└── public_html/      ← SOLO el contenido de tu carpeta local "public/"
    ├── index.php
    ├── .htaccess
    ├── css/
    ├── js/
    ├── icons/
    ├── manifest.php
    └── sw.js
```

`app`, `system` y `writable` quedan **fuera** de `public_html` (más seguro).

---

## Paso 1 — Entrar a hPanel

1. Ve a [hostinger.com](https://www.hostinger.com) → **Iniciar sesión**
2. Abre tu hosting → **Administrar** (hPanel)

---

## Paso 2 — PHP 8.1 o 8.2

1. hPanel → **Avanzado** → **Configuración de PHP**
2. Elige **PHP 8.1** o **8.2**
3. Guarda

---

## Paso 3 — Subir archivos

### Opción A — Administrador de archivos (sin programas extra)

1. hPanel → **Archivos** → **Administrador de archivos**
2. Abre la carpeta de tu dominio, algo como:
   ```
   /domains/contratoya.online/
   ```
3. Sube estas carpetas **al lado de** `public_html` (no dentro):
   - `app`
   - `system`
   - `writable`
   - `database` (opcional, solo para referencia)
4. Entra en `public_html` y sube **todo el contenido** de tu carpeta local `public/`:
   - `index.php`, `.htaccess`, `css/`, `js/`, `icons/`, `manifest.php`, `sw.js`, etc.
5. Sube `.env` (creado en el paso 5) en `/domains/contratoya.online/` (junto a `app/`)

> Puedes comprimir el proyecto en `.zip`, subirlo y extraerlo en el administrador de archivos.

### Opción B — FileZilla (FTP)

1. hPanel → **Archivos** → **Cuentas FTP** → crea una cuenta o usa la principal
2. Conecta con FileZilla (host, usuario y contraseña del panel)
3. Misma estructura de carpetas que arriba

---

## Paso 4 — Base de datos MySQL

1. hPanel → **Bases de datos** → **Administración de MySQL**
2. **Crear base de datos** → nombre ej: `u123456789_contratoya`
3. **Crear usuario** → ej: `u123456789_cv` con contraseña segura
4. **Asignar usuario a la base de datos** con todos los privilegios

### Importar tus datos

**Recomendado (lo más fácil):**

1. En XAMPP: `http://localhost/phpmyadmin` → base `compraventa` → **Exportar** → SQL
2. En Hostinger: hPanel → **phpMyAdmin** → tu base nueva → **Importar** → sube el `.sql`

---

## Paso 5 — Archivo `.env` en el servidor

En `/domains/contratoya.online/.env` (junto a `app/`):

```env
CI_ENVIRONMENT = production

app.indexPage =

database.default.hostname = localhost
database.default.database = u123456789_contratoya
database.default.username = u123456789_cv
database.default.password = TU_PASSWORD_AQUI
database.default.DBDriver = MySQLi
database.default.port = 3306

marca.webUrl = https://sanabriacod-portfolio.netlify.app/
marca.waUrl = https://wa.me/573215821483
```

Reemplaza con los nombres reales que te muestra Hostinger (siempre empiezan con `u` + números).

---

## Paso 6 — Permisos de `writable/`

En el administrador de archivos, carpeta `writable/`:

| Carpeta | Permiso |
|---------|---------|
| `writable/` | 755 |
| `writable/cache/` | 755 |
| `writable/logs/` | 755 |
| `writable/session/` | 755 |
| `writable/uploads/` | 755 |

Clic derecho → **Permisos** → marca lectura/escritura para el propietario y grupo si hace falta.

---

## Paso 7 — SSL (HTTPS)

1. hPanel → **Seguridad** → **SSL**
2. Activa SSL gratuito para `contratoya.online`
3. Espera unos minutos

Luego en `public_html/.htaccess`, **descomenta** estas líneas:

```apache
RewriteCond %{HTTPS} off
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [L,R=301]
```

---

## Paso 8 — Dominio contratoya.online

Si compraste el dominio en Hostinger, suele enlazarse solo.

Si el dominio está en otro proveedor:

1. En el registrador del dominio → **DNS**
2. Registro **A** para `@` → IP de tu hosting (la ves en hPanel → **Detalles del plan**)
3. Registro **CNAME** para `www` → `contratoya.online` (opcional)

En Hostinger: **Dominios** → `contratoya.online` → debe decir que apunta a tu hosting.

Propagación DNS: de minutos a 24 h.

---

## Paso 9 — Probar

1. Abre `https://contratoya.online/`
2. Debe verse el login **ContratoYa**
3. Inicia sesión (ej. usuario `admin` si importaste la BD local)
4. En el celular: menú del navegador → **Agregar a pantalla de inicio**

---

## Si algo falla

| Síntoma | Qué hacer |
|---------|-----------|
| Página en blanco | Revisa `writable/logs/log-*.php` en el administrador de archivos |
| Error 500 | PHP 8.1+, permisos `writable/`, `.env` correcto |
| Estilos rotos | Verifica que `css/` y `js/` estén **dentro** de `public_html/` |
| No conecta BD | Copia exacta de usuario, BD y clave desde hPanel → MySQL |
| `public_html` muestra listado de carpetas | Falta `index.php` dentro de `public_html` |

### Ver error detallado (temporal)

En `.env` cambia a:
```env
CI_ENVIRONMENT = development
```
Recarga la página, anota el error y vuelve a poner `production`.

---

## Seguridad en producción

1. Cambia contraseñas de `platform` y `admin`
2. No dejes `CI_ENVIRONMENT = development` en vivo
3. El archivo `.env` no debe estar dentro de `public_html`

---

## Resumen visual

```
Tu PC (XAMPP)                    Hostinger
─────────────                    ─────────
compraventa/                     domains/contratoya.online/
├── app/          ──────────►   ├── app/
├── system/       ──────────►   ├── system/
├── writable/     ──────────►   ├── writable/
├── .env          ──────────►   ├── .env
└── public/       ──────────►   └── public_html/  ← contenido de public/
    ├── index.php                   ├── index.php
    ├── css/                        ├── css/
    └── js/                         └── js/
```

---

¿Necesitas ayuda en un paso concreto? Dime en qué paso vas (subir archivos, base de datos, SSL, etc.) y lo vemos.
