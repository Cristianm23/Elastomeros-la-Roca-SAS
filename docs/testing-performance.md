# Fase 10: testing y rendimiento

## Cobertura backend

La suite Laravel incluye:

- Tests unitarios de estados de cotización.
- Tests de autenticación Sanctum, registro, login, sesión, logout y recuperación.
- Tests de autorización CUSTOMER, EMPLOYEE y ADMIN.
- Tests de API para catálogo, contacto, cotizaciones y analytics.
- Tests de validación de registro, archivos, honeypot y productos inexistentes.
- Test de integración del recorrido cliente:
  registro -> login/token -> productos -> detalle -> cotización -> sesión -> logout.
- Tests del panel administrativo y headers de seguridad.

Ejecutar:

```bash
cd backend
php artisan test
```

## Cobertura frontend

Vitest y Testing Library cubren:

- Componentes del design system.
- Estados disabled/loading y navegación de tabs/modal.
- Formularios de contacto y cotización.
- Rutas públicas y design system.

Playwright cubre en Chromium:

- Navegación home -> catálogo -> detalle -> cotización.
- Envío de contacto sin doble submit.
- Panel administrativo, dashboard, productos y cotizaciones.

La autenticación real del recorrido cliente/administrador se prueba contra la
API en Laravel, no con credenciales simuladas en el navegador. Las pruebas
Playwright mockean únicamente respuestas de API para aislar la interfaz.

## Rendimiento

- Catálogo público paginado con máximo de 50 elementos por página.
- Productos cargados con `category`, `images` y `specifications` mediante eager loading.
- Categorías cargadas con `withCount` y productos relacionados en una sola consulta lógica.
- Cotizaciones administrativas cargadas con `items` y `assignee`.
- Dashboard limita actividad a los últimos 10 registros.
- Analytics limita resultados recientes a 50 y productos a 20.
- Service worker cachea únicamente navegación/assets públicos; API y administración no se cachean.
- Imágenes públicas utilizan lazy loading donde no son hero.
- Vite genera bundle comprimido y elimina código no utilizado durante producción.

No se agregó una caché de catálogo con invalidación incompleta: el contenido
administrativo debe reflejar cambios sin esperar expiraciones. La siguiente
optimización de infraestructura puede añadir Redis y etiquetas de caché cuando
se defina el despliegue de producción.
