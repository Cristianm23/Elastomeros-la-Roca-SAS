# Elastómeros La Roca S.A.S.

Plataforma web empresarial B2B para presentación corporativa, catálogo, servicios,
solicitudes de cotización y gestión administrativa.

## Estado de la fase 1

La fase inicial deja un monorepo preparado con:

- `frontend/`: React + TypeScript + Vite, con una landing corporativa responsive y accesible.
- `backend/`: reservado para Laravel 12 + PostgreSQL + Sanctum.
- `docs/architecture.md`: decisiones y límites de la arquitectura inicial.

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

## Próxima fase

Configurar el backend Laravel con PostgreSQL, variables de entorno y el contrato
base de la API antes de implementar catálogo o autenticación.
