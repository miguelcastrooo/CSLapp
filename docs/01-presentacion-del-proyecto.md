# Presentación del proyecto

## Sistema de Control Escolar — Instituto Horizonte

### Problema atendido

La administración de información escolar suele dispersarse entre listas, formatos impresos y archivos separados. Esto dificulta localizar expedientes, conocer el estado de un alumno, asignar plataformas digitales, controlar bajas y saber quién realizó un cambio.

### Solución propuesta

Se desarrolló una aplicación web para centralizar el control escolar. El sistema permite administrar expedientes de alumnos, organizar la estructura académica, asignar plataformas por nivel y controlar el acceso de cada integrante del personal mediante roles.

### Objetivo general

Facilitar la gestión segura y ordenada de la información escolar, reduciendo errores de captura y conservando un historial de los cambios importantes.

### Objetivos específicos

- Registrar, consultar y actualizar expedientes de alumnos.
- Organizar niveles educativos, grados, secciones y plataformas sin modificar código.
- Gestionar bajas, reactivaciones y egresos sin eliminar expedientes.
- Promover grupos completos al siguiente grado durante el cierre escolar.
- Limitar la información visible según el rol del usuario.
- Conservar evidencia de cambios administrativos y académicos.
- Generar documentos PDF de alumnos y plataformas cuando corresponda.

## Alcance funcional

| Área | Funcionalidad principal |
|---|---|
| Alumnos | Alta, consulta, búsqueda, expediente, edición y contactos familiares. |
| Estructura académica | Niveles, grados, secciones y plataformas configurables. |
| Plataformas | Asignación por nivel y resguardo de credenciales por alumno. |
| Movimientos escolares | Baja, reactivación, promoción grupal, cierre escolar y egreso. |
| Administración | Usuarios, roles, activación de cuentas, configuración y respaldos guiados. |
| Seguimiento | Historial del alumno e historial de seguridad. |
| Reportes | Consulta y generación de PDF de información autorizada. |

## Roles del sistema

| Rol | Alcance |
|---|---|
| SuperAdmin | Administra usuarios, roles, estructura académica, configuración, historiales y todos los alumnos. |
| Control Escolar | Gestiona expedientes, altas, bajas, archivados, egresos, promoción y cierre escolar; no administra usuarios ni roles. |
| Coordinación de Preescolar | Consulta y gestiona sólo alumnos y grupos de Preescolar. |
| Coordinación de Primaria | Consulta y gestiona sólo Primaria Baja y Primaria Alta. |
| Coordinación de Secundaria | Consulta y gestiona sólo Secundaria. |

## Beneficios

- Información escolar concentrada en una sola aplicación.
- Menos duplicidad y menor riesgo de pérdida de expedientes.
- Roles que limitan el acceso a la información necesaria.
- Historial para identificar cambios, fechas y responsables.
- Crecimiento posible: se pueden agregar niveles, grados, secciones y plataformas desde la interfaz.

## Tecnologías empleadas

- Laravel 12 y PHP 8.2.
- MySQL.
- Blade, Bootstrap/Tabler, JavaScript y Vite.
- Laravel Breeze para autenticación.
- Spatie Laravel Permission para roles y permisos.
- DomPDF para reportes PDF.
- Laravel Sanctum para una API preparada para una futura aplicación cliente.

## Trabajo futuro sugerido

La aplicación web Laravel cubre el objetivo principal. Como extensiones posteriores se proponen asistencia diaria, reportes de asistencia, boletas por periodo e importación/exportación de alumnos. Angular y la API de calificaciones pueden retomarse como una ampliación independiente cuando el flujo académico esté definido por la institución.
