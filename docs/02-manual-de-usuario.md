# Manual de usuario

## Inicio de sesión

1. Abra el sistema en el navegador.
2. Escriba el correo y contraseña proporcionados por la administración.
3. Seleccione **Iniciar sesión**.

No existe registro público. Las cuentas, roles, contraseñas y activación de usuarios son responsabilidad del SuperAdmin.

## Panel principal

El panel muestra indicadores generales y accesos rápidos. El buscador permite localizar alumnos por nombre, apellido o matrícula. Las sugerencias sólo muestran expedientes permitidos para el rol que inició sesión.

## Gestión de alumnos

### Registrar alumno

1. Seleccione el nivel educativo.
2. Complete los datos del alumno y de sus contactos.
3. Agregue hermanos sólo si aplica; el formulario permite agregar los necesarios.
4. Revise la información y guarde.

### Consultar y editar expediente

1. Busque al alumno por matrícula o nombre.
2. Abra el expediente para revisar la información.
3. Seleccione **Editar expediente** si requiere un cambio.
4. Modifique sólo los datos necesarios y guarde.

Los cambios relevantes quedan registrados en el historial del alumno.

### Bajas y reactivaciones

- Una baja retira al alumno de los listados activos, pero conserva su expediente y exige un motivo.
- En **Alumnos archivados** se puede consultar el motivo y la fecha de baja.
- Una reactivación solicita un motivo y conserva ambas acciones en el historial.

### Egresados

Los alumnos que concluyen el último grado se registran como egresados durante el cierre escolar. Un egresado no es una baja: su expediente se conserva para consulta histórica y no aparece entre los alumnos activos.

## Promoción y cierre escolar

La promoción está diseñada para mover un grupo completo.

1. Filtre por nivel, grado y sección.
2. Revise los alumnos que serán incluidos.
3. Confirme la promoción al siguiente grado.

Si el grupo no tiene siguiente grado, el sistema lo trata como egreso. Antes de ejecutar un movimiento, revise cuidadosamente el resumen del grupo.

## Plataformas

Las plataformas se asignan por nivel educativo. Un alumno sólo ve o recibe las plataformas habilitadas para su nivel.

Un SuperAdmin puede crear una plataforma, asignarla a uno o varios niveles, desasignarla de un nivel o desactivarla. La desasignación y la desactivación no borran credenciales históricas; el sistema protege opciones que aún tengan datos relacionados.

## Configuración académica (SuperAdmin)

### Identidad institucional

Al inicio de esta pantalla, el SuperAdmin puede cambiar el nombre de la escuela, el subtítulo, el logotipo y los colores del portal. Hay una vista previa antes de guardar.

- El cambio se refleja en el inicio de sesión, menú lateral, panel, tablas y documentos PDF.
- El logotipo acepta archivos PNG, JPG o WEBP de hasta 2 MB.
- Se recomienda usar colores oscuros o medios para menú y botones, para conservar una lectura clara.
- **Restaurar valores de ejemplo** recupera el diseño inicial sin modificar alumnos, plataformas ni historiales.

La configuración sigue este orden:

1. Nivel educativo.
2. Grado perteneciente al nivel.
3. Sección perteneciente al grado.
4. Plataforma y asignación de plataformas por nivel.

Las opciones inactivas dejan de mostrarse para registros nuevos. Antes de desactivar una opción que ya se use, el sistema mostrará una advertencia.

## Usuarios y roles (SuperAdmin)

Desde **Usuarios y roles** el SuperAdmin puede crear cuentas, asignar o retirar roles y activar o desactivar accesos. Las personas usuarias sólo pueden actualizar los datos permitidos de su propio perfil, como nombre, correo y foto.

## Historiales (SuperAdmin)

- **Historial de alumnos:** altas, ediciones, bajas, reactivaciones, promociones y egresos.
- **Historial de seguridad:** accesos, cambios de roles, activación o desactivación de cuentas, configuración y consultas sensibles.

## Avisos internos

La campana de la parte superior muestra movimientos escolares pendientes de revisar, como altas, bajas, reactivaciones, promociones y egresos. El sistema avisa a SuperAdmin, Control Escolar y a la coordinación que corresponde al nivel.

SuperAdmin y Control Escolar también pueden abrir **Avisos** desde la campana y enviar comunicados internos a roles seleccionados. Las coordinaciones pueden consultar sus avisos, pero no emitir comunicados generales.

## Recomendaciones de uso

- Confirme el alumno antes de editar, dar de baja o promover.
- Escriba motivos claros en bajas y reactivaciones.
- Use cuentas personales; no comparta contraseñas.
- Cierre sesión al terminar su jornada.
