# Informe de seguridad - Fase 8

Fecha: 2026-10-07

## Resultado

La auditoría de seguridad no encontró vulnerabilidades explotables en el
código revisado. Se verificaron autenticación Sanctum, autorización por
permisos, validación de formularios, consultas Eloquent parametrizadas,
rate limiting, cargas de archivos, sesiones, cookies, variables de entorno y
uso de datos en el frontend.

| Área | Resultado | Medida aplicada |
|---|---|---|
| Authentication | Conforme | Sanctum, expiración de tokens y revocación en logout |
| Authorization | Conforme | Middleware `permission`, policies y autorización backend |
| CORS | Endurecido | Orígenes explícitos mediante `CORS_ALLOWED_ORIGINS` |
| CSRF | Conforme | `statefulApi`, cookies HttpOnly y middleware CSRF de Sanctum |
| XSS | Conforme | React escapa contenido; no se usa `dangerouslySetInnerHTML` |
| SQL injection | Conforme | Eloquent y bindings; no se interpolan consultas SQL crudas |
| Rate limiting | Conforme | Límites por IP para login, registro, contacto y cotizaciones |
| Uploads | Conforme | Extensión/MIME, máximo 10 MB y almacenamiento privado |
| Headers | Endurecido | CSP, nosniff, frame deny, HSTS en HTTPS, Referrer-Policy y Permissions-Policy |
| Cookies/sesiones | Endurecido | HttpOnly, SameSite configurable y Secure automático en producción |
| Secrets | Conforme | No hay API keys, tokens, passwords ni DSNs privados en frontend |
| Sensitive caching | Endurecido | Respuestas auth/admin con `Cache-Control: no-store` |

## Correcciones y mejoras aplicadas

- Se agregó configuración CORS con lista explícita de orígenes.
- Se agregó middleware de headers de seguridad.
- Se activó `Secure` automáticamente para cookies en producción.
- Se mantuvo `SameSite=Lax` configurable.
- Se impidió cachear respuestas de autenticación y administración.
- Se agregó un service worker que nunca intercepta `/api/` ni `/admin`.
- Se agregó página offline para navegación pública.
- Se agregó detección de desconexión y reconexión.
- Los formularios sensibles fallan explícitamente sin conexión y no se
  reenvían automáticamente, evitando duplicados.

## PWA y modo offline

La aplicación publica:

- `manifest.webmanifest`.
- `sw.js`.
- `offline.html`.

El service worker cachea únicamente navegación y assets públicos same-origin.
No cachea tokens, formularios, respuestas administrativas ni endpoints API.
Las operaciones de contacto y cotización requieren conexión activa.

## Recomendaciones operativas

- Definir `CORS_ALLOWED_ORIGINS` únicamente con dominios reales en producción.
- Definir `APP_DEBUG=false` en producción.
- Configurar `SESSION_SECURE_COOKIE=true` explícitamente si existe proxy TLS
  que no preserve correctamente el esquema HTTPS.
- Ejecutar `php artisan queue:work` con un supervisor.
- Rotar credenciales de infraestructura fuera del repositorio.
- Mantener dependencias actualizadas y revisar logs de autenticación/auditoría.
