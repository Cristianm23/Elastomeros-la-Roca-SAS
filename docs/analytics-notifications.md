# Analytics, observabilidad y notificaciones

## Analytics

El frontend envía eventos mínimos a `POST /api/v1/analytics/events`. El backend
aplica una lista blanca de nombres y propiedades, limita el tamaño de los
valores y nunca recibe formularios, correos, teléfonos, contraseñas, tokens o
archivos.

Eventos disponibles:

- `page_view`, `product_view`, `search`
- `quote_started`, `quote_submitted`, `contact_submitted`
- `login`, `register`, `cta_click`
- `whatsapp_click`, `phone_click`, `pdf_download`
- `frontend_error`, `api_failure`

Los administradores con `users.view` pueden consultar `GET /api/v1/admin/analytics`.
La respuesta incluye totales por evento, productos más vistos y una muestra
reciente sin datos de formulario.

## Crash reporting

El frontend captura errores globales y rechazos de promesas mediante
`services/observability.ts`. Los errores se reducen a su tipo antes de enviarse
como `frontend_error`; los errores HTTP se registran como `api_failure`.

Laravel registra excepciones en `CrashReporter` con clase, mensaje, ruta, método
y estado HTTP. No se registra el cuerpo de la petición, headers de
autenticación, cookies, tokens ni credenciales. Esta interfaz permite sustituir
el canal de logs por Sentry u otro proveedor mediante configuración de
infraestructura sin cambiar la aplicación.

## Notificaciones

Las notificaciones usan clases Laravel encoladas y canales desacoplados:

- Email: activo para nuevas cotizaciones, confirmaciones de contacto y cambios
  de estado.
- Web: canal `database` preparado para usuarios autenticados.
- Push: reservado como canal `push` futuro, sin registrar tokens ni instalar un
  proveedor prematuramente.
- Recuperación de contraseña: usa el flujo seguro integrado de Laravel.

Los eventos enviados al canal web contienen únicamente referencia, tipo y
estado. El contenido sensible de cotizaciones no se copia a notificaciones.
