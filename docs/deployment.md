# Despliegue de producción

Esta guía prepara el monorepo para separar el frontend estático, la API Laravel
y una instancia administrada de PostgreSQL. Los valores que parezcan secretos
deben crearse en el panel del proveedor, nunca en archivos versionados.

## 1. Arquitectura recomendada

| Pieza | Servicio recomendado |
| --- | --- |
| Frontend React/Vite | Vercel, Render Static Site o Railway |
| API Laravel | Railway, Render Web Service, Koyeb o Fly.io |
| PostgreSQL | PostgreSQL administrado del proveedor |
| Colas | Un worker separado ejecutando `php artisan queue:work` |
| Scheduler | Cron cada minuto ejecutando `php artisan schedule:run` |
| Archivos | Bucket S3-compatible privado |
| Logs | `stderr`/logs del proveedor y alertas del proveedor |

`/up` es el health check de Laravel. La API no debe exponerse sin HTTPS.

## 2. Variables de entorno

Copiar la referencia de [`.env.example`](../.env.example) para identificar
variables. Para el backend también existe
[`backend/.env.example`](../backend/.env.example), y para Vite
[`frontend/.env.example`](../frontend/.env.example).

### Backend obligatorio

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:valor-generado-con-php-artisan-key-generate
APP_URL=https://api.example.com
LOG_CHANNEL=stderr
LOG_LEVEL=warning
CORS_ALLOWED_ORIGINS=https://www.example.com
SANCTUM_STATEFUL_DOMAINS=www.example.com
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
DB_SSLMODE=require
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
FILESYSTEM_DISK=s3
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="Elastómeros La Roca"
SALES_NOTIFICATION_EMAIL=...
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
```

Generar la clave una sola vez antes de arrancar:

```powershell
cd backend
composer install --no-dev --optimize-autoloader
php artisan key:generate
```

No reutilizar la `APP_KEY` entre aplicaciones y no cambiarla después de
publicar una aplicación que tenga sesiones o datos cifrados.

### Frontend obligatorio

```dotenv
VITE_APP_NAME="Elastómeros La Roca"
VITE_API_URL=https://api.example.com/api
```

`VITE_*` se incorpora al bundle público: solo debe contener URLs y valores no
secretos. Nunca poner contraseñas, tokens, DSN privados o claves cloud allí.

## 3. Despliegue del backend

### Railway

1. Crear un servicio desde el repositorio y establecer `Root Directory` en
   `backend`.
2. Railway detectará [`backend/Dockerfile`](../backend/Dockerfile). Si se usa
   el builder nativo, configurar `Start Command` como
   `php artisan migrate --force && php artisan optimize && php artisan serve --host=0.0.0.0 --port=$PORT`.
3. Añadir un PostgreSQL administrado y copiar sus credenciales en las variables
   `DB_*`, usando `DB_SSLMODE=require`.
4. Añadir las variables backend, desplegar y comprobar `https://api.../up`.
5. Crear un segundo servicio desde la misma imagen con
   `php artisan queue:work --sleep=3 --tries=3 --timeout=90`.
6. Crear un cron externo del proveedor para `php artisan schedule:run` cada
   minuto. No ejecutar el scheduler dentro del proceso HTTP.

### Render

El archivo [`render.yaml`](../render.yaml) contiene la plantilla para API,
worker, scheduler, frontend y PostgreSQL. Revisar el plan y los nombres antes
de aplicar el Blueprint, completar las variables `sync: false`, y comprobar:

```text
GET https://api.example.com/up -> 200
```

El worker y el web service deben compartir `APP_KEY` y las mismas credenciales
de base de datos. Configurar un bucket externo para no perder archivos al
reiniciar el contenedor.

El workflow de GitHub Actions ejecuta lint, tests y build en cada push/PR. Para
que el último paso dispare Render después de un push exitoso a `main`, crear un
Deploy Hook en Render y guardarlo como secreto de GitHub
`RENDER_DEPLOY_HOOK_URL`. Railway y Vercel pueden usar su integración GitHub
equivalente, configurada para desplegar solo después de que CI esté verde.

### Koyeb

1. Crear un App desde GitHub seleccionando `backend` como contexto y usar
   `backend/Dockerfile`.
2. Definir el puerto HTTP `8080`, health check `GET /up` y todas las variables
   backend.
3. Crear un servicio adicional desde la misma imagen con comando
   `php artisan queue:work --sleep=3 --tries=3 --timeout=90`.
4. Usar un cron de Koyeb o externo para `php artisan schedule:run`.
5. Asociar un PostgreSQL administrado y exigir SSL con `DB_SSLMODE=require`.

### Fly.io

[`fly.toml`](../fly.toml) deja configurados HTTPS, puerto 8080, health check y
la migración de release. Cambiar `app`, crear la aplicación y adjuntar una
base PostgreSQL administrada:

```powershell
fly launch --no-deploy
fly secrets set APP_KEY="..." APP_URL="https://api.example.com" `
  CORS_ALLOWED_ORIGINS="https://www.example.com" `
  SANCTUM_STATEFUL_DOMAINS="www.example.com"
fly secrets set DB_CONNECTION=pgsql DB_HOST="..." DB_PORT=5432 `
  DB_DATABASE="..." DB_USERNAME="..." DB_PASSWORD="..." DB_SSLMODE=require
fly deploy
```

Crear el worker como process group separado y conservar al menos una máquina
HTTP activa. Configurar backups de PostgreSQL fuera de la máquina de la API.

## 4. Despliegue del frontend

### Vercel

1. Importar el repositorio y establecer `Root Directory` en `frontend`.
2. Build command: `npm run build`.
3. Output directory: `dist`.
4. Añadir `VITE_API_URL=https://api.example.com/api`.
5. [`frontend/vercel.json`](../frontend/vercel.json) mantiene funcionando las
   rutas SPA y evita cachear el service worker.
6. Añadir el dominio, activar HTTPS automático y actualizar
   `frontend/public/robots.txt` y `sitemap.xml` con el dominio definitivo.

### Render o Railway

Crear un servicio estático con:

```text
Root: frontend
Build: npm ci && npm run build
Publish directory: dist
```

Configurar la reescritura `/* -> /index.html`, `VITE_API_URL` y el dominio
HTTPS. La misma definición está incluida en `render.yaml`.

## 5. Dominio, CORS y cookies

1. Publicar primero la API en `api.example.com` y después el frontend en
   `www.example.com`.
2. Configurar DNS y esperar el certificado administrado.
3. Usar el origen exacto del frontend en `CORS_ALLOWED_ORIGINS`; no usar `*`
   con credenciales.
4. Usar `SANCTUM_STATEFUL_DOMAINS` solo para dominios first-party controlados.
5. Mantener `SESSION_SECURE_COOKIE=true`, `HttpOnly` y `SameSite=lax`.
6. Probar login, logout, CSRF/session y tokens Bearer desde HTTPS.

## 6. Base de datos, migraciones y backups

Antes del primer tráfico:

```powershell
cd backend
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
php artisan storage:link
```

En producción, hacer backup automático diario de PostgreSQL, conservar varias
retenciones y probar una restauración mensualmente. No usar `migrate:fresh`.
Las migraciones deben ejecutarse como release/deploy step, una sola vez por
despliegue.

## 7. Operación

- HTTP: un proceso web por instancia; escalar horizontalmente.
- Queue: al menos un worker separado; revisar `failed_jobs` y reinicios.
- Scheduler: exactamente un cron por minuto.
- Cache: usar Redis administrado si el proveedor lo ofrece; database cache es
  el fallback actual.
- Storage: usar bucket privado para adjuntos de cotización y configurar CORS
  del bucket solo para los dominios necesarios.
- Logs: `LOG_CHANNEL=stderr`, nivel `warning` en producción y alertas para
  errores 5xx, jobs fallidos y health checks.
- Crash reporting: conectar el proveedor elegido a `CrashReporter` sin enviar
  passwords, tokens, archivos ni secretos.

## 8. Checklist de aceptación

- [ ] `APP_DEBUG=false`, `APP_KEY` única y secretos fuera de Git.
- [ ] HTTPS, dominio y redirects funcionando.
- [ ] `/up` responde 200.
- [ ] PostgreSQL usa SSL, migraciones y backups probados.
- [ ] Login, autorización por roles y logout probados.
- [ ] Contacto, cotización, archivos y emails probados.
- [ ] Worker procesa notificaciones y `failed_jobs` se monitoriza.
- [ ] Analytics y crash reporting excluyen datos sensibles.
- [ ] CORS acepta solo el frontend oficial.
- [ ] `robots.txt`, `sitemap.xml`, privacidad y términos publicados.
- [ ] Errores 404/500, responsive, dark mode y offline verificados.
- [ ] CI pasa lint, tests y build antes de desplegar.
