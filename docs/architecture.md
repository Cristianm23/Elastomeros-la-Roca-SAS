# Arquitectura inicial

## Límites de la fase 1

Esta fase establece la estructura del producto y una superficie pública mínima.
No incluye todavía persistencia, autenticación, catálogo, cotizaciones ni panel
administrativo.

## Estructura propuesta

```text
/
├── backend/       Laravel, API versionada, dominio y persistencia
├── frontend/      React + TypeScript + Vite
└── docs/          decisiones técnicas y contratos del producto
```

### Backend

- Laravel 12 sobre PHP 8.3+.
- PostgreSQL como base de datos.
- `/api/v1` como prefijo para contratos versionados.
- Sanctum para sesiones web y tokens de integraciones.
- Policies/Gates para autorización en servidor.
- Jobs y Notifications para procesos de cotización, correo y alertas.
- Validación mediante Form Requests; la interfaz nunca será la autoridad de seguridad.

### Frontend

- React + TypeScript + Vite.
- Componentes visuales sin lógica de negocio ni secretos.
- Cliente HTTP y estado de servidor separados de las vistas cuando se incorpore la API.
- Diseño responsive, navegación semántica, estados de carga y manejo explícito de errores.
- Tema claro/oscuro y preferencias de movimiento como parte del sistema visual.

## Módulos de producto

1. Sitio público: empresa, servicios, FAQ y contacto.
2. Catálogo: productos, categorías, fichas y medios.
3. Cotizaciones: formulario público, seguimiento y notificaciones.
4. Identidad: usuarios, roles, permisos y sesiones.
5. Administración: gestión de contenidos y solicitudes.

## Decisiones pendientes para la siguiente fase

- Configurar PHP con OpenSSL y Composer para crear el backend real.
- Definir conexión PostgreSQL por variables de entorno.
- Establecer el primer contrato OpenAPI o equivalente para `/api/v1`.
- Elegir proveedor de almacenamiento de imágenes y correo transaccional.
- Confirmar requisitos de roles iniciales con el negocio.
