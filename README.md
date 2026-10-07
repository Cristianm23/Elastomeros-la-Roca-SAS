# Elastómeros La Roca S.A.S.

Plataforma web empresarial B2B para presentación corporativa, catálogo, servicios,
solicitudes de cotización y gestión administrativa.

## Estado del proyecto

La fase inicial deja un monorepo preparado con:

- `frontend/`: React + TypeScript + Vite, con sitio público responsive, catálogo navegable y sistema visual accesible.
- `backend/`: Laravel 13 + PostgreSQL, con catálogo, servicios, contacto y cotizaciones iniciales.
- `docker-compose.yml`: PostgreSQL local reproducible para desarrollo.
- `docs/architecture.md`: arquitectura, modelo de datos, API, seguridad y permisos.
- `docs/api.md`: endpoints implementados, payloads y comandos de desarrollo.
- `frontend/src/components/ui/`: sistema visual accesible y reutilizable.
- `frontend/src/pages/PublicPages.tsx`: Home, catálogo, detalles, categorías, servicios, nosotros, FAQ, contacto y legales.
- `frontend/src/services/contact.ts`: envío del formulario público al endpoint `/api/v1/contact`.
- `frontend/src/services/quotes.ts`: envío multipart de solicitudes de cotización.

Las fases 1 a 4 están implementadas. La Fase 5 deja publicada la experiencia
pública inicial con datos locales de catálogo, preparada para sustituirse por
la API sin cambiar la navegación ni los componentes visuales.

## Fase 6: herramienta comercial B2B

La ruta `/cotizacion` permite solicitar cotizaciones desde el catálogo o
directamente, incluyendo producto, cantidad, ciudad, fecha requerida,
observaciones y archivo técnico. Los formularios muestran estados de carga,
éxito y error, bloquean el envío duplicado y consumen los endpoints Laravel.

El backend valida datos, fechas, honeypot anti-spam, MIME y tamaño de archivos,
aplica rate limiting y almacena los adjuntos fuera de las respuestas públicas.
Las solicitudes recorren los estados `pending`, `reviewing`, `quoted`,
`accepted`, `rejected`, `completed` y `cancelled`. Las confirmaciones al
cliente y avisos a la empresa usan Laravel Notifications con Queue.

## Fase 7: panel administrativo

El panel está disponible en `/admin`. Sus endpoints requieren Sanctum y
permisos backend; la interfaz no sustituye la autorización del servidor.
Incluye dashboard, cotizaciones, contactos, productos, categorías, servicios,
FAQ, testimonios, blog, usuarios y auditoría. Las operaciones administrativas
registran actor, acción, recurso, valores anteriores/nuevos e IP en `audit_logs`.

## Fase 8: seguridad y offline

La auditoría de seguridad y las correcciones aplicadas están documentadas en
[`docs/security-audit.md`](docs/security-audit.md). El frontend incluye PWA,
service worker, cache únicamente público, página offline y detección de
reconexión. No se cachean ni reintentan operaciones sensibles.

Para procesar las notificaciones en desarrollo o producción:

```powershell
cd backend
php artisan queue:work
```

## Desarrollo del frontend

```powershell
cd frontend
npm run dev
```

En desarrollo, las rutas `/api` y `/sanctum` se redirigen al backend local
`http://127.0.0.1:8000`. Para otro entorno se puede definir
`VITE_API_URL` sin incluir `/v1`, por ejemplo `https://api.example.com/api`.

La página interna del Design System está disponible en
`http://localhost:5173/design-system`. Permite probar componentes, estados,
temas claro/oscuro/sistema, interacción por teclado y responsive desde 320 px.

Validación:

```powershell
npm run lint
npm run build
```

## Fase 5: experiencia pública

Rutas disponibles:

- `/`
- `/productos` y `/productos/:slug`
- `/categorias` y `/categorias/:slug`
- `/servicios` y `/servicios/:slug`
- `/nosotros`
- `/faq`
- `/contacto`
- `/cotizacion`
- `/politica-privacidad`
- `/terminos`
- `/design-system`

El formulario de contacto valida los campos en el navegador, exige aceptación
de privacidad, muestra estados de envío/error y consume `VITE_API_URL` cuando
está definido (por defecto usa `/api`).

## Fase 1

La arquitectura técnica está definida en [`docs/architecture.md`](docs/architecture.md).
También se crearon las estructuras de módulos backend y capas frontend sin
implementar todavía funcionalidades ni endpoints.

## Backend y autenticación

Conectar el entorno de despliegue a PostgreSQL, incorporar autenticación Sanctum
y avanzar con administración, notificaciones e imágenes. Sanctum ya está
configurado para sesiones first-party y tokens Bearer; consulta
[`docs/api.md`](docs/api.md) para el flujo disponible.

La Fase 3 agrega registro, login con expiración y “recordarme”, logout,
recuperación de contraseña, roles, permisos granulares y policies de acceso.
