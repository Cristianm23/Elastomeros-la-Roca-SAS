# Elastómeros La Roca S.A.S.

Plataforma web empresarial B2B para Elastómeros La Roca S.A.S., empresa
colombiana especializada en soluciones técnicas de caucho y elastómeros.

La aplicación permite presentar la empresa, consultar el catálogo, conocer
los servicios, solicitar cotizaciones y administrar la operación comercial
desde un panel protegido.

## Aplicación

### Sitio público

- Página corporativa y propuesta de valor.
- Catálogo de productos con búsqueda, categorías, detalle, galería y
  especificaciones.
- Catálogo de servicios y páginas de detalle.
- Formularios de contacto y solicitud de cotización.
- Adjuntos técnicos para solicitudes, con validación de tipo y tamaño.
- Preguntas frecuentes, testimonios, información corporativa y páginas
  legales.
- Diseño responsive, accesible, con modos claro, oscuro y sistema.
- PWA con contenido público disponible offline.
- SEO básico, metadata dinámica, `robots.txt` y `sitemap.xml`.

### Gestión comercial y administrativa

- Registro, inicio de sesión, cierre de sesión y recuperación de contraseña.
- Autenticación mediante Laravel Sanctum.
- Roles: `GUEST`, `CUSTOMER`, `EMPLOYEE` y `ADMIN`.
- Permisos granulares para productos, cotizaciones y usuarios.
- Clientes limitados a sus propios registros.
- Dashboard administrativo con indicadores de cotizaciones, contactos,
  productos, usuarios y actividad.
- Gestión de productos, categorías, servicios, FAQ, testimonios y blog.
- Seguimiento y actualización del estado de cotizaciones.
- Asignación de cotizaciones, notas y auditoría de operaciones.
- Notificaciones por correo y base de datos mediante colas.

### Observabilidad y calidad

- Analytics de navegación, productos, búsquedas, cotizaciones y contactos,
  sin registrar contraseñas, tokens ni información sensible.
- Captura y redacción de errores del frontend y backend.
- Pruebas PHPUnit, Vitest, Testing Library y Playwright.
- Protección contra abuso, validación backend, sanitización y rate limiting.
- Headers de seguridad, CORS explícito y cookies seguras en producción.

## Tecnologías

### Backend

- PHP 8.3+
- Laravel 13
- PostgreSQL
- Laravel Sanctum
- Laravel Notifications
- Laravel Queues
- Policies, Gates y permisos backend

### Frontend

- React 19
- TypeScript
- Vite
- CSS con tokens de diseño
- Vitest
- Testing Library
- Playwright

## Estructura del repositorio

```text
.
├── backend/       API Laravel, módulos, migraciones y pruebas
├── frontend/      Aplicación React, páginas, componentes y pruebas
├── docs/          Arquitectura, API, seguridad y despliegue
├── .github/       Automatización CI/CD
├── docker-compose.yml
├── render.yaml
├── fly.toml
└── Dockerfile.fly
```

El backend está organizado por módulos de dominio dentro de
`backend/app/Modules`, mientras que el frontend separa componentes, páginas,
servicios, hooks, tipos, validación y rutas.

## Requisitos

- PHP 8.3 o superior
- Composer
- Node.js 22 o superior
- npm
- PostgreSQL 16 o superior
- Docker Desktop opcional para desarrollo local

## Instalación local

### Base de datos

La forma recomendada de iniciar PostgreSQL local es:

```powershell
docker compose up -d postgres
```

Si se utiliza una instalación externa, configurar sus credenciales en el
archivo de entorno del backend.

### Backend

```powershell
cd backend
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

La API quedará disponible en `http://127.0.0.1:8000`.

Para procesar notificaciones encoladas durante el desarrollo:

```powershell
php artisan queue:work
```

### Frontend

```powershell
cd frontend
copy .env.example .env
npm install
npm run dev
```

El sitio quedará disponible en `http://localhost:5173`.

La variable `VITE_API_URL` debe apuntar al prefijo de la API, sin añadir
`/v1`. En desarrollo puede usarse el valor predeterminado `/api`; para una
instalación separada:

```dotenv
VITE_API_URL=https://api.example.com/api
```

Las variables `VITE_*` son públicas y nunca deben contener secretos.

## API

La API está versionada bajo `/api/v1`.

### Catálogo público

```text
GET  /api/v1/products
GET  /api/v1/products/{slug}
GET  /api/v1/categories
GET  /api/v1/categories/{slug}
GET  /api/v1/services
GET  /api/v1/services/{slug}
```

### Contacto y cotizaciones

```text
POST /api/v1/contact
POST /api/v1/quotes
```

### Autenticación

```text
POST /api/v1/auth/register
POST /api/v1/auth/login
POST /api/v1/auth/forgot-password
POST /api/v1/auth/reset-password
GET  /api/v1/auth/me
POST /api/v1/auth/logout
```

### Analytics y administración

```text
POST /api/v1/analytics/events
GET  /api/v1/admin/dashboard
GET  /api/v1/admin/quotes
GET  /api/v1/admin/contacts
GET  /api/v1/admin/users
GET  /api/v1/admin/analytics
```

La referencia completa de payloads, respuestas y autenticación está en
[`docs/api.md`](docs/api.md).

## Seguridad y privacidad

- No se versionan archivos `.env`.
- Las credenciales se configuran en el proveedor de despliegue.
- La autorización se verifica en el backend, no solo en la interfaz.
- Los adjuntos de cotizaciones se almacenan fuera de las respuestas públicas.
- Los formularios aplican validación, sanitización, honeypot y rate limiting.
- Las rutas administrativas requieren autenticación y permisos.
- Las operaciones sensibles no se ejecutan ni se reintentan offline.
- Analytics excluye contraseñas, tokens, archivos, correos y teléfonos.

La auditoría y las decisiones de seguridad están documentadas en
[`docs/security-audit.md`](docs/security-audit.md).

## Pruebas y validación

### Backend

```powershell
cd backend
php artisan test
```

### Frontend

```powershell
cd frontend
npm run lint
npm test -- --run
npm run build
```

### E2E

```powershell
cd frontend
npm run test:e2e
```

La estrategia de pruebas y rendimiento está descrita en
[`docs/testing-performance.md`](docs/testing-performance.md).

## Despliegue

La aplicación puede desplegarse separando el frontend estático de la API:

- Frontend: Vercel, Render Static Site o Railway.
- Backend: Railway, Render, Koyeb o Fly.io.
- Base de datos: PostgreSQL administrado.
- Colas: worker separado con `php artisan queue:work`.
- Scheduler: ejecución de `php artisan schedule:run` cada minuto.
- Archivos: bucket S3-compatible privado.

Se incluyen plantillas para Render y Fly.io, Dockerfiles y CI en GitHub
Actions. La guía con variables, dominios, HTTPS, CORS, backups, logs y
checklist de producción está en [`docs/deployment.md`](docs/deployment.md).

El pipeline de CI valida backend, frontend, pruebas y build en cada pull
request y push a `main`.

## Identidad visual

La interfaz utiliza una paleta corporativa basada en:

- Azul primario: `#1f4f82`
- Blanco secundario: `#ffffff`
- Negro terciario: `#111827`
- Rojo de acción: `#e53935`

El sistema visual está disponible en `/design-system`.

## Documentación

- [`docs/architecture.md`](docs/architecture.md): arquitectura y modelo de
  dominio.
- [`docs/api.md`](docs/api.md): endpoints y contratos de la API.
- [`docs/security-audit.md`](docs/security-audit.md): controles de seguridad.
- [`docs/analytics-notifications.md`](docs/analytics-notifications.md):
  observabilidad y notificaciones.
- [`docs/testing-performance.md`](docs/testing-performance.md): pruebas y
  rendimiento.
- [`docs/deployment.md`](docs/deployment.md): despliegue y operación.

## Licencia

Proyecto privado de Elastómeros La Roca S.A.S. Todos los derechos reservados.
