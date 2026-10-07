# Elastómeros La Roca S.A.S.

Plataforma web empresarial B2B para presentación corporativa, catálogo, servicios,
solicitudes de cotización y gestión administrativa.

## Estado de la fase 1

La fase inicial deja un monorepo preparado con:

- `frontend/`: React + TypeScript + Vite, con una landing corporativa responsive y accesible.
- `backend/`: Laravel 13 + PostgreSQL, con catálogo, servicios, contacto y cotizaciones iniciales.
- `docker-compose.yml`: PostgreSQL local reproducible para desarrollo.
- `docs/architecture.md`: arquitectura, modelo de datos, API, seguridad y permisos.
- `docs/api.md`: endpoints implementados, payloads y comandos de desarrollo.

El backend Laravel no pudo instalarse en este equipo porque el PHP disponible no
carga OpenSSL y Composer no está disponible como instalación global. Antes de
inicializarlo se debe habilitar `openssl` en `php.ini` e instalar Composer.

## Desarrollo del frontend

```powershell
cd frontend
npm run dev
```

Validación:

```powershell
npm run lint
npm run build
```

## Fase 1

La arquitectura técnica está definida en [`docs/architecture.md`](docs/architecture.md).
También se crearon las estructuras de módulos backend y capas frontend sin
implementar todavía funcionalidades ni endpoints.

## Próxima fase

Conectar el entorno de despliegue a PostgreSQL, incorporar autenticación Sanctum
y avanzar con administración, notificaciones e imágenes. Sanctum ya está
configurado para sesiones first-party y tokens Bearer; consulta
[`docs/api.md`](docs/api.md) para el flujo disponible.

La Fase 3 agrega registro, login con expiración y “recordarme”, logout,
recuperación de contraseña, roles, permisos granulares y policies de acceso.
