# Despliegue en contratoya.online

Guía para subir **ContratoYa** a un hosting compartido (cPanel, Hostinger, etc.).

## URL final

```
https://contratoya.online/
```

El login queda en esa misma dirección.

---

## Requisitos del hosting

- PHP **8.0+** (recomendado 8.1 o 8.2)
- MySQL o MariaDB
- Extensiones: `mysqli`, `mbstring`, `intl`, `json`, `curl`
- `mod_rewrite` activo (Apache)
- Certificado SSL (HTTPS) — la mayoría de hostings lo incluye gratis

---

## Paso 1 — Subir archivos

### Opción recomendada (más segura)

Estructura en el servidor:

```
/home/tuusuario/
├── contratoya/          ← carpeta FUERA de public_html
│   ├── app/
│   ├── database/
│   ├── public/          ← solo el CONTENIDO de esta carpeta va al dominio
│   ├── system/
│   ├── writable/
│   └── .env
└── public_html/         ← document root del dominio
    ├── index.php        (copiado desde public/)
    ├── .htaccess        (copiado desde public/)
    ├── css/
    ├── js/
    ├── icons/
    ├── manifest.php
    ├── sw.js
    └── ...
```

En cPanel: **Dominios → contratoya.online → Document root** apuntando a la carpeta `public` del proyecto.

Si el panel permite elegir carpeta, usa:

```
/home/tuusuario/contratoya/public
```

### Opción simple (todo en public_html)

Sube **toda** la carpeta del proyecto a `public_html/contratoya/` y en cPanel apunta el dominio a:

```
public_html/contratoya/public
```

O sube todo a `public_html/` y el archivo `.htaccess` de la raíz del proyecto redirige a `public/`.

---

## Paso 2 — Base de datos

1. En cPanel → **Bases de datos MySQL**:
   - Crear base de datos (ej: `tuuser_contratoya`)
   - Crear usuario y contraseña
   - Asignar todos los privilegios al usuario

2. Importar datos en **phpMyAdmin**:

   **Más fácil:** exporta tu base local desde XAMPP (phpMyAdmin → Exportar → `compraventa`) e impórtala en el hosting.

   **Desde cero:** importa en este orden:
   - `database/compraventa.sql`
   - `database/migration_superadmin.sql`
   - Luego ejecuta por SSH (si tienes acceso):
     ```bash
     php database/run_migration_abonos.php
     php database/run_migration_empleados.php
     php database/run_migration_finanzas.php
     php database/run_migration_usernames.php
     ```
     (Antes edita host/usuario/clave en esos scripts, o usa la exportación de tu BD local.)

---

## Paso 3 — Archivo `.env` en el servidor

Copia `.env.production.example` a `.env` en la raíz del proyecto y completa:

```env
CI_ENVIRONMENT = production

app.indexPage =

database.default.hostname = localhost
database.default.database = tuuser_contratoya
database.default.username = tuuser_contratoya
database.default.password = TU_PASSWORD_SEGURO
database.default.DBDriver = MySQLi
database.default.port = 3306
```

> En muchos hostings el hostname es `localhost`. Si no conecta, revisa el panel (a veces es algo como `mysql.tudominio.com`).

---

## Paso 4 — Permisos de carpetas

La carpeta `writable/` debe ser escribible por el servidor:

```
writable/          → 755 o 775
writable/cache/    → 775
writable/logs/     → 775
writable/session/  → 775
writable/uploads/  → 775
```

En cPanel → Administrador de archivos → clic derecho → Permisos.

---

## Paso 5 — HTTPS y dominio

1. En cPanel activa **SSL** para `contratoya.online` (Let's Encrypt).
2. En `public/.htaccess`, **descomenta** estas líneas:

```apache
RewriteCond %{HTTPS} off
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [L,R=301]
```

3. En cPanel → **Zona DNS** del dominio:
   - Registro **A** `@` → IP del hosting
   - Registro **A** o **CNAME** `www` → mismo destino (opcional)

La propagación DNS puede tardar hasta 24–48 horas.

---

## Paso 6 — Probar

1. Abre `https://contratoya.online/`
2. Debe cargar el login de ContratoYa
3. Prueba iniciar sesión con un usuario de tu negocio
4. Desde el celular, instala la PWA con “Agregar a pantalla de inicio”

---

## Credenciales de prueba (si importaste la BD de desarrollo)

| Rol | Usuario | Contraseña |
|-----|---------|------------|
| Super admin plataforma | `platform` | `admin123` |
| Dueño negocio Valery | `admin` | `admin123` |

**Cambia estas contraseñas en producción.**

---

## Problemas frecuentes

| Problema | Solución |
|----------|----------|
| Página en blanco | Revisa `writable/logs/` y activa temporalmente `CI_ENVIRONMENT = development` |
| Error 500 | Permisos de `writable/`, PHP 8+, `.env` correcto |
| CSS no carga | Document root debe ser la carpeta `public/` |
| Login no conecta BD | Usuario, clave y nombre de BD en `.env` |
| Solo funciona con `www` | Revisa DNS y reglas HTTPS en `.htaccess` |

---

## Qué NO subir (opcional, para ahorrar espacio)

- `writable/debugbar/` (archivos de depuración locales)
- `.git/` si usas Git
- Tu `.env` local de XAMPP (crea uno nuevo en el servidor)

---

## Soporte

- **Hostinger:** guía detallada en [`DEPLOY-HOSTINGER.md`](DEPLOY-HOSTINGER.md)
- Otros hostings: sigue los pasos generales de este archivo
