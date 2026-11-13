# Sistema de Control Escolar — Instituto Horizonte

Aplicación escolar construida con Laravel 12 para administrar alumnos, niveles, grupos, plataformas, bajas, egresos y usuarios con roles.

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

Si se requieren datos ficticios, define `DEMO_SEED_PASSWORD` sólo en tu `.env` local antes de ejecutar un seeder de demostración. Nunca uses una contraseña real.

## Seguridad antes de publicar

- Nunca subas `.env`, respaldos SQL, archivos de `storage`, PDFs generados o fotos cargadas por usuarios.
- Revisa los seeders antes de ejecutarlos; deben contener sólo datos ficticios.
- Usa `APP_DEBUG=false`, HTTPS y `SESSION_SECURE_COOKIE=true` en producción.
- Las cuentas se crean y administran sólo desde SuperAdmin; no hay registro público ni restablecimiento público de contraseña.

## Módulos deshabilitados temporalmente

Calificaciones permanece fuera de las rutas hasta completar sus permisos por nivel y su flujo académico.

## Antes de un commit

1. Ejecuta `php artisan view:cache`.
2. Ejecuta `npm run build`.
3. Ejecuta las pruebas disponibles con `php artisan test`.
4. Revisa `git status` y confirma que no aparezcan datos personales ni archivos locales.
