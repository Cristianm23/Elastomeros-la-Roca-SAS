# API v1

Base URL local: `http://localhost:8000/api/v1`

Todas las respuestas exitosas usan la estructura de Laravel API Resources:

```json
{
  "data": {},
  "links": {},
  "meta": {}
}
```

Los errores de validación usan HTTP `422`:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## Autenticación Sanctum

### `POST /auth/register`

Registra un CUSTOMER y devuelve una sesión Bearer inicial. Requiere:

```json
{
  "name": "Laura Méndez",
  "email": "laura@empresa.co",
  "phone": "+57 300 000 0000",
  "company_name": "Roca Industrial SAS",
  "password": "StrongPass123",
  "password_confirmation": "StrongPass123",
  "privacy_accepted": true,
  "device_name": "web"
}
```

La aceptación de privacidad es obligatoria. El endpoint está limitado a 3
registros por minuto y dirección IP.

### `POST /auth/login`

Solicita un token Bearer para integraciones o clientes que no usan la sesión
first-party. Payload:

```json
{
  "email": "usuario@empresa.co",
  "password": "********",
  "device_name": "mi-integracion"
}
```

El token se entrega una sola vez en `data.token`. Debe enviarse en solicitudes
protegidas como `Authorization: Bearer <token>`. Las credenciales inválidas
responden `422` sin revelar si el correo existe. El endpoint tiene un límite de
5 intentos por minuto y dirección IP.

### `GET /auth/me`

Requiere `Authorization: Bearer <token>` y devuelve el usuario autenticado.

### `POST /auth/logout`

Requiere autenticación y revoca el token actual. Los tokens no se devuelven
en respuestas posteriores.

### Recuperación de contraseña

`POST /auth/forgot-password` recibe `{ "email": "usuario@empresa.co" }` y
responde siempre con un mensaje genérico para no revelar si el correo existe.
`POST /auth/reset-password` recibe `token`, `email`, `password` y
`password_confirmation`. Al restablecer la contraseña se revocan los tokens
existentes.

El frontend first-party usa el modo stateful de Sanctum con cookies HttpOnly y
CSRF. Antes de solicitudes mutantes debe obtener `/sanctum/csrf-cookie` desde
el dominio del backend y enviar las credenciales de la sesión. Los dominios
permitidos se configuran con `SANCTUM_STATEFUL_DOMAINS`; nunca se deben usar
comodines en producción.

## Roles y control de acceso

Los roles iniciales son `GUEST`, `CUSTOMER`, `EMPLOYEE` y `ADMIN`. Los permisos
se almacenan de forma granular, por ejemplo:

- `products.view`, `products.create`, `products.update`, `products.delete`
- `quotes.view`, `quotes.create`, `quotes.update`, `quotes.delete`
- `users.view`, `users.manage`

Las policies y el middleware `permission` se ejecutan en Laravel. Un CUSTOMER
solo puede consultar sus propias cotizaciones; un EMPLOYEE requiere
`quotes.view`; ADMIN tiene acceso mediante la regla administrativa global.
Los filtros enviados por frontend no determinan autorización.

## Catálogo

### `GET /products`

Lista productos activos paginados.

Query params:

- `search`: busca por nombre o descripción corta.
- `category`: filtra por slug de categoría.
- `per_page`: entre 1 y 50; valor por defecto 12.

### `GET /products/{slug}`

Devuelve el producto activo, su categoría, imágenes y especificaciones.
Responde `404` si el slug no existe o está inactivo.

### `GET /categories`

Lista categorías activas paginadas. Acepta `per_page` entre 1 y 50.

### `GET /categories/{slug}`

Devuelve una categoría activa y sus productos activos.

### `GET /services`

Lista servicios activos paginados. Acepta `per_page` entre 1 y 50.

### `GET /services/{slug}`

Devuelve un servicio activo por slug.

## Contacto

### `POST /contact`

Rate limit: 5 solicitudes por minuto y dirección IP.

Payload:

```json
{
  "name": "Ana Pérez",
  "company_name": "Industria SAS",
  "email": "ana@empresa.co",
  "phone": "+57 300 000 0000",
  "subject": "Consulta técnica",
  "message": "Necesito información sobre sus soluciones.",
  "consent": true,
  "website": ""
}
```

`name`, `email`, `subject`, `message` y `consent` son obligatorios.
`consent` debe ser `true`. `website` es un campo honeypot que debe permanecer
vacío. Responde `201` con el identificador y estado del mensaje. La confirmación
al cliente y la notificación interna se envían mediante Laravel Notifications
encoladas.

## Cotizaciones

### `POST /quotes`

Rate limit: 5 solicitudes por minuto y dirección IP.

Payload:

```json
{
  "company_name": "Industria SAS",
  "contact_name": "Carlos Gómez",
  "email": "carlos@empresa.co",
  "phone": "+57 300 000 0000",
  "city": "Bogotá",
  "required_date": "2026-11-15",
  "observations": "Requerimos una cotización.",
  "consent": true,
  "website": "",
  "items": [
    {
      "product_id": 1,
      "quantity": 10,
      "notes": "Para línea de producción"
    },
    {
      "description": "Sello de especificación especial",
      "quantity": 4
    }
  ]
}
```

Para archivos use `multipart/form-data` y el campo `attachment`. Se aceptan
PDF, documentos Office e imágenes JPG/PNG/WebP hasta 10 MB; el MIME se valida
en backend. `company_name`, `contact_name`, `email`, `city`, `required_date`,
`consent` y al menos un `items` son obligatorios. Cada ítem debe indicar un
`product_id` existente o una `description`, y la cantidad debe ser un entero
entre 1 y 100000. La fecha requerida no puede ser anterior a hoy.

Los estados de una cotización son `pending`, `reviewing`, `quoted`, `accepted`,
`rejected`, `completed` y `cancelled`. Responde `201` con una referencia pública
`COT-...`; nunca expone el correo, IP, ruta del archivo ni datos internos. Al
crear la solicitud se envían confirmaciones encoladas al cliente y al correo
comercial configurado en `SALES_NOTIFICATION_EMAIL`.

## Administración

Todas las rutas administrativas requieren `Authorization: Bearer <token>` y
el permiso correspondiente. Los permisos se validan en middleware Laravel.

- `GET /admin/dashboard`: métricas y actividad reciente (`users.view`).
- `GET /admin/quotes`: búsqueda por referencia/empresa/correo y filtro por estado (`quotes.view`).
- `GET /admin/quotes/{id}`: detalle con ítems y asignación (`quotes.view`).
- `PATCH /admin/quotes/{id}`: estado, asignación y notas (`quotes.update`).
- `GET /admin/contacts`: mensajes recibidos (`users.view`).
- `GET /admin/products`, `/categories`, `/services`: listados administrativos.
- `POST`, `PATCH`, `DELETE /admin/{type}`: CRUD de catálogo (`products.create/update/delete`).
- `GET/POST/DELETE /admin/content/{type}`: FAQ, testimonios y blog.
- `GET /admin/users`, `/roles`, `/activity`: administración y auditoría.
- `PATCH /admin/users/{id}/roles`: asignación de roles (`users.manage`).
- `PATCH /admin/roles/{id}/permissions`: asignación de permisos (`users.manage`).

Los cambios de contenido, cotizaciones, permisos y login generan registros en
`audit_logs`.

## Desarrollo y base de datos

```powershell
cd backend
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

El `.env` debe apuntar a PostgreSQL (`DB_CONNECTION=pgsql`, host, puerto,
base de datos, usuario y contraseña). Las pruebas usan SQLite en memoria:

```powershell
php artisan test
```

Para desarrollo local puede usarse `docker compose up -d postgres`. El servicio
usa credenciales de desarrollo definidas en `docker-compose.yml`; deben
reemplazarse en cualquier entorno compartido o productivo.
