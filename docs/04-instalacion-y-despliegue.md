# Instalación y despliegue local

## Requisitos

- PHP 8.2 o superior.
- Composer.
- Node.js y npm.
- MySQL (XAMPP es válido).
- Git.

## Instalación desde el repositorio

```powershell
git clone <URL_DEL_REPOSITORIO>
cd cslprac
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

## Base de datos

1. Cree una base de datos local, por ejemplo `cslprac`.
2. Edite `.env` con sus credenciales locales. Este archivo no debe subirse a Git.
3. Ejecute las migraciones:

```powershell
php artisan migrate
```

4. Si requiere datos de demostración, use únicamente seeders con información ficticia. Revise el seeder antes de ejecutarlo y defina una contraseña de demostración sólo en `.env` local.

## Ejecución local

En una terminal:

```powershell
php artisan serve
```

En otra terminal, para compilar recursos durante desarrollo:

```powershell
npm run dev
```

Abra la dirección mostrada por Laravel, normalmente `http://127.0.0.1:8000`.

## Verificación antes de entregar o publicar

```powershell
php artisan view:cache
npm run build
php artisan test
git status
```

## Reglas de seguridad para Git y producción

- Nunca subir `.env`, respaldos SQL, documentos PDF generados, fotos de perfiles ni archivos de `storage`.
- Nunca incluir alumnos reales, correos reales o contraseñas en seeders.
- Configurar `APP_DEBUG=false` en producción.
- Usar HTTPS y configurar cookies seguras en producción.
- Definir el dominio permitido para CORS si se habilita la API para otra interfaz.
- Realizar un respaldo de la base de datos antes de aplicar migraciones en un servidor real.

## Restauración en otro equipo

El código se recupera desde Git y la información escolar se recupera desde un respaldo SQL separado. Después de importar la base de datos, configure `.env`, instale dependencias, compile recursos y verifique que los enlaces de almacenamiento estén disponibles si se usan fotos de perfil.
