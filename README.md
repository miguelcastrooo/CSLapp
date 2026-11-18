# Sistema de Control Escolar — Instituto Horizonte

Aplicación escolar construida con Laravel 12 para administrar alumnos, niveles, grupos, plataformas, bajas, egresos y usuarios con roles.

## Documentación de entrega

La documentación para presentación, uso, instalación y aspectos técnicos está disponible en [docs/README.md](docs/README.md).

## Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL mediante XAMPP u otro servidor compatible

## Instalación local

1. Clona el repositorio.
2. Ejecuta `composer install` y `npm install`.
3. Copia `.env.example` a `.env` y configura exclusivamente tu base de datos local.
4. Ejecuta `php artisan key:generate`.
5. Ejecuta `php artisan migrate`.
6. Para la interfaz en desarrollo, ejecuta `npm run dev`.

### Avisos en tiempo real

En el entorno local de este proyecto, inicia también `php artisan reverb:start` mientras se usa el sistema. Así la campana y los avisos se actualizan sin recargar. Para un clon nuevo, genera claves propias con `php artisan install:broadcasting --reverb --no-interaction`, define `BROADCAST_CONNECTION=reverb` en su `.env` local y vuelve a compilar los recursos con `npm run build` o `npm run dev`.

Si se requieren datos ficticios, define `DEMO_SEED_PASSWORD` sólo en tu `.env` local antes de ejecutar un seeder de demostración. Nunca uses una contraseña real.

## Seguridad antes de publicar

- Nunca subas `.env`, respaldos SQL, archivos de `storage`, PDFs generados o fotos cargadas por usuarios.
- Revisa los seeders antes de ejecutarlos; deben contener sólo datos ficticios.
- Usa `APP_DEBUG=false`, HTTPS y `SESSION_SECURE_COOKIE=true` en producción.
- Las cuentas se crean y administran sólo desde SuperAdmin; no hay registro público ni restablecimiento público de contraseña.
- No copies las claves de Reverb ni ningún archivo `.env` a otro entorno: cada instalación debe generar sus propias claves.

## Módulos deshabilitados temporalmente

Calificaciones y la API experimental de Angular permanecen cerradas hasta completar sus permisos por nivel, reglas académicas y pruebas. El código se conserva, pero las rutas API no se publican por defecto. Sólo se habilitan de forma deliberada con `API_PORTAL_ENABLED=true` en el entorno correspondiente.

## API para Angular

La API se conserva como base para una fase futura de Angular y usa tokens de Laravel Sanctum. Permanece cerrada por defecto. Para desarrollo controlado, habilita `API_PORTAL_ENABLED=true` y permite únicamente el origen necesario en `CORS_ALLOWED_ORIGINS`.

- Al habilitarla de forma controlada, `POST /api/v1/auth/login` obtiene un token de sesión para Angular.
- También se habilitan catálogos, alumnos autorizados por nivel y las rutas de calificaciones; antes de activarlas para uso real deben completarse sus pruebas y reglas académicas.

La API valida el acceso al nivel del alumno y no entrega contraseñas de plataformas.

## Antes de un commit

1. Ejecuta `php artisan view:cache`.
2. Ejecuta `npm run build`.
3. Ejecuta las pruebas disponibles con `php artisan test`.
4. Revisa `git status` y confirma que no aparezcan datos personales ni archivos locales.
