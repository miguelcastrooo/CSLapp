# Guía de pruebas manuales

Esta guía permite recorrer el sistema antes de una presentación o publicación. Úsala con los datos de demostración locales. No realices acciones finales de baja, promoción o egreso si no quieres cambiar el estado de los alumnos de prueba.

## Antes de iniciar

- Enciende Apache y MySQL en XAMPP.
- Inicia Laravel con `php artisan serve` si se trabajará desde el servidor de desarrollo.
- Inicia sesión con una cuenta activa autorizada.
- Ten abierta una hoja de notas para anotar cualquier resultado inesperado.

## Registro de resultados

Para cada punto anota: fecha, usuario usado, pantalla, acción realizada, resultado esperado, resultado obtenido y captura de pantalla si hubo error.

## 1. Inicio de sesión y seguridad

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Credenciales correctas | Inicia sesión con una cuenta activa. | Redirige al panel principal. |
| Contraseña incorrecta | Escribe una contraseña errónea. | Muestra un aviso y no inicia sesión. |
| Ruta protegida | Cierra sesión e intenta abrir `/dashboard`. | Redirige al inicio de sesión. |
| Registro público | Intenta abrir `/register`. | La página no está disponible. |
| Recuperación pública | Intenta abrir `/forgot-password`. | La página no está disponible. |
| Cierre de sesión | Selecciona Cerrar sesión. | Regresa al inicio y no permite volver al panel sin iniciar sesión. |

## 2. Panel principal y búsqueda

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Indicadores | Revisa alumnos, niveles, plataformas y archivados. | Los números coinciden con los listados correspondientes. |
| Carrusel | Usa las flechas e indicadores. | Cambia manualmente sin bloquear la pantalla. |
| Búsqueda por nombre | Escribe al menos dos letras del nombre de un alumno. | Aparecen sugerencias autorizadas. |
| Búsqueda por matrícula | Escribe una matrícula existente. | Muestra al alumno correcto. |
| Expediente desde sugerencia | Selecciona un resultado. | Abre el expediente sin errores. |

## 3. Expediente de alumno

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Consulta | Abre un expediente. | Datos académicos, familiares y plataformas se muestran ordenados. |
| Edición | Selecciona Editar expediente, cambia un dato no crítico y guárdalo. | Guarda, muestra confirmación e incorpora la modificación. |
| Historial | Consulta el historial después de editar. | Registra fecha, usuario y cambio realizado. |
| Hermanos | Agrega un hermano, guarda, vuelve a editar y retíralo. | Permite agregar y quitar sin límite rígido ni datos duplicados. |
| Validaciones | Intenta guardar un campo obligatorio vacío. | Muestra un mensaje claro y no guarda información incompleta. |

Al terminar la prueba de edición, vuelve a dejar el dato como estaba si deseas conservar los datos de demostración limpios.

## 4. Bajas, archivados, reactivaciones y egresados

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Validación de baja | Intenta enviar una baja sin motivo. | No permite guardar. |
| Baja controlada | Sólo si aceptas modificar datos demo, da de baja un alumno de prueba con un motivo claro. | Sale de activos y aparece en Archivados. |
| Detalle de baja | Abre el alumno archivado. | Se ven motivo y fecha de baja. |
| Reactivación | Reactiva el alumno con un motivo. | Regresa a activos y conserva ambos movimientos en el historial. |
| Egresados | Consulta la sección de Egresados. | Está separada de Archivados y no muestra alumnos como bajas. |

## 5. Plataformas y configuración académica

Estas pruebas requieren SuperAdmin.

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Asignación por nivel | Abre Configuración académica. | Cada nivel sólo tiene marcadas sus plataformas correspondientes. |
| Resumen de relaciones | Revisa la tabla Administrar lo creado. | Nivel educativo muestra Nivel principal; grados y secciones muestran su jerarquía; plataformas muestran los niveles asignados. |
| Plataforma inactiva | Si existe una plataforma sin datos relacionados, desactívala y actualiza el panel. | Deja de aparecer para registros nuevos y el contador de plataformas activas cambia. |
| Protección de datos | Intenta desactivar una plataforma con credenciales relacionadas. | Muestra advertencia y no borra ni oculta información de forma accidental. |
| Nivel, grado y sección | Revisa formularios sin guardar. | La jerarquía es clara: nivel, luego grado, luego sección. |

No crees más plataformas, niveles o grados sólo para probarlos. Si necesitas hacerlo, utiliza nombres claramente temporales y elimínalos al terminar.

## 6. Usuarios, roles y permisos

| Prueba | Acción | Resultado esperado |
|---|---|---|
| SuperAdmin | Inicia con administrador. | Ve Usuarios y roles, historiales y configuración. |
| Control Escolar | Inicia con Control Escolar. | Puede gestionar alumnos, bajas y grupos; no ve Usuarios y roles. |
| Coordinación | Inicia con una coordinación. | Sólo ve niveles y alumnos autorizados para su coordinación. |
| Ruta no autorizada | Con una coordinación, intenta abrir una dirección de Usuarios y roles. | Muestra acceso denegado o no permite la acción. |
| Cuenta inactiva | Desactiva temporalmente una cuenta demo e intenta iniciar sesión. | El acceso es rechazado. Reactívala al terminar. |

## 7. Promoción de grupos y cierre escolar

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Guía visual | Abre Mover grupos. | Explica el orden: filtra, revisa y confirma. |
| Filtros | Elige nivel, grado y sección. | La lista corresponde al grupo seleccionado. |
| Revisión | Selecciona un grupo y revisa el resumen. | El botón de promoción aparece después de revisar alumnos. |
| Egreso | Consulta el caso de último grado. | El sistema indica que el grupo se registraría como egresado. |

No confirmes una promoción ni un egreso durante una prueba visual. Hazlo sólo con datos de demostración que estés dispuesto a modificar o después de contar con un respaldo reciente.

## 8. Perfil, PDF y correo

| Prueba | Acción | Resultado esperado |
|---|---|---|
| Perfil | Cambia nombre o correo con datos de prueba. | Guarda y confirma el cambio. |
| Foto válida | Sube una imagen JPG o PNG menor a 2 MB. | La foto se muestra correctamente. |
| Foto inválida | Intenta subir un archivo no permitido. | Muestra validación y no guarda el archivo. |
| PDF | Genera un PDF permitido desde un expediente o nivel. | Abre o descarga sin texto cortado. |
| Correo | Configura primero una cuenta de correo de prueba y envía a una dirección propia. | El mensaje y el PDF llegan correctamente. |

## 9. Prueba visual y responsiva

- Reduce el ancho del navegador o usa la vista móvil del navegador.
- Revisa menú lateral, tablas, botones, formularios y modales.
- Confirma que no haya texto blanco sobre fondos claros, botones encimados o tablas ilegibles.
- Revisa especialmente: Usuarios y roles, Configuración académica, Mover grupos, Expediente de alumno, Archivados y Perfil.

## 10. Qué hacer si aparece un error

1. No recargues inmediatamente; toma una captura de pantalla.
2. Anota el usuario, la pantalla, los pasos y la hora aproximada.
3. Indica si el error ocurrió al consultar, guardar, editar, eliminar, generar PDF o enviar correo.
4. Si hubo una acción de datos, revisa si se guardó antes de volver a intentarlo.
5. Comparte la captura y la secuencia exacta para poder reproducir el problema.

## Criterio de aprobación

El sistema queda listo para presentación cuando todas las pruebas de consulta, seguridad, permisos, formularios y vista visual se comportan como se espera; las acciones sensibles se prueban sólo con respaldo reciente y datos de demostración.
