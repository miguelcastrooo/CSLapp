# Documentación técnica

## Arquitectura

El sistema sigue una arquitectura MVC de Laravel:

```text
Usuario → Rutas web → Middleware → Controlador → Modelo/Eloquent → MySQL
                                  ↓
                              Vistas Blade
```

- **Rutas:** `routes/web.php` organiza los módulos web; `routes/api.php` conserva la API versionada.
- **Controladores:** concentran reglas de cada módulo en `app/Http/Controllers`.
- **Modelos:** representan entidades y relaciones en `app/Models`.
- **Vistas:** Blade en `resources/views`.
- **Acceso por nivel:** `app/Support/NivelAccess.php` concentra la validación de alcance escolar.

## Entidades principales

| Entidad | Descripción |
|---|---|
| Usuario y rol | Cuenta autenticada y permisos mediante Spatie Permission. |
| Nivel educativo | Etapa escolar principal. |
| Grado | Pertenece a un nivel y tiene un orden de promoción. |
| Sección | Pertenece a un grado. |
| Alumno | Expediente activo con datos académicos y familiares. |
| Alumno archivado | Expediente de baja conservado para consulta y reactivación. |
| Egresado | Alumno que concluyó el último grado. |
| Plataforma | Recurso digital asignable a niveles y alumnos. |
| Configuración institucional | Identidad visual editable: nombre, subtítulo, logotipo y paleta de colores. |
| Historial de alumno | Evidencia de cambios sobre un expediente. |
| Historial de seguridad | Evidencia de acciones administrativas y de acceso. |

## Seguridad y permisos

- Autenticación mediante Laravel Breeze.
- Registro público y recuperación pública de contraseña deshabilitados.
- Cuentas activas verificadas por el middleware `account.active`.
- Roles gestionados por Spatie Laravel Permission.
- Acceso a niveles validado centralmente por `NivelAccess`.
- Formularios protegidos contra solicitudes falsificadas mediante CSRF.
- Cabeceras de seguridad aplicadas por middleware.
- Contraseñas almacenadas con hash; nunca se guardan en texto plano.
- Datos sensibles y archivos locales excluidos del repositorio mediante `.gitignore`.
- Notificaciones internas persistentes mediante la tabla `notifications`; los avisos no incluyen contraseñas ni datos familiares.
- Avisos en tiempo real mediante Laravel Reverb: la campana se actualiza sin recargar, usando un canal privado autorizado por usuario.

## Historial y trazabilidad

Los controladores de alumnos registran altas, actualizaciones, bajas, reactivaciones, promociones y egresos. Las acciones administrativas relevantes también se registran, por ejemplo: creación o desactivación de usuarios, cambios de roles, configuración y generación de documentos.

## Migraciones y datos

Las migraciones se encuentran en `database/migrations`. Permiten crear la estructura completa de una base de datos nueva. Los seeders de demostración deben utilizarse sólo con información ficticia y contraseñas definidas localmente mediante variables de entorno.

La identidad institucional se almacena en la tabla `configuracion_institucional`, no en el archivo `.env`. Así, un SuperAdmin puede personalizar el portal sin cambiar código ni afectar el despliegue. Los logotipos se almacenan en el disco público de Laravel y requieren el enlace de almacenamiento habitual.

## API y Angular

La API está disponible bajo `/api/v1` y usa Laravel Sanctum. Incluye autenticación por token y endpoints de catálogos, alumnos y calificaciones. La aplicación Angular ubicada en `frontend-angular` es un prototipo técnico independiente; no es necesaria para operar la entrega web Laravel y no debe presentarse como módulo productivo mientras no se defina el flujo institucional de calificaciones.

## Operación de avisos en tiempo real

Laravel Reverb es el servicio local que mantiene la comunicación en vivo. Durante el desarrollo se inicia con `php artisan reverb:start`. En un servidor publicado se requiere configurarlo como proceso persistente y usar WebSocket seguro (`wss`) detrás del dominio HTTPS. Las credenciales de Reverb pertenecen al archivo `.env` de cada instalación y nunca se versionan.

## Mantenimiento recomendado

- Revisar dependencias antes de actualizar Laravel o PHP.
- Ejecutar pruebas y compilación de recursos antes de publicar cambios.
- Revisar los historiales después de cambios administrativos importantes.
- Mantener copias de respaldo de la base de datos fuera del repositorio.
