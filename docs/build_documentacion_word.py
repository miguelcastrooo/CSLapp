from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.style import WD_STYLE_TYPE
from docx.enum.table import WD_ALIGN_VERTICAL
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_BREAK
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Pt, RGBColor

OUTPUT = 'docs/Documentacion_Sistema_Control_Escolar_Instituto_Horizonte.docx'


def set_cell_shading(cell, fill):
    tc_pr = cell._tc.get_or_add_tcPr()
    shading = OxmlElement('w:shd')
    shading.set(qn('w:fill'), fill)
    tc_pr.append(shading)


def set_cell_border(cell, color='D9D9D9'):
    tc_pr = cell._tc.get_or_add_tcPr()
    borders = tc_pr.first_child_found_in('w:tcBorders')
    if borders is None:
        borders = OxmlElement('w:tcBorders')
        tc_pr.append(borders)
    for edge in ('top', 'left', 'bottom', 'right', 'insideH', 'insideV'):
        tag = 'w:' + edge
        element = borders.find(qn(tag))
        if element is None:
            element = OxmlElement(tag)
            borders.append(element)
        element.set(qn('w:val'), 'single')
        element.set(qn('w:sz'), '6')
        element.set(qn('w:color'), color)


def set_cell_margins(cell, top=110, start=120, bottom=110, end=120):
    tc_pr = cell._tc.get_or_add_tcPr()
    margins = tc_pr.first_child_found_in('w:tcMar')
    if margins is None:
        margins = OxmlElement('w:tcMar')
        tc_pr.append(margins)
    for side, value in [('top', top), ('start', start), ('bottom', bottom), ('end', end)]:
        node = margins.find(qn('w:' + side))
        if node is None:
            node = OxmlElement('w:' + side)
            margins.append(node)
        node.set(qn('w:w'), str(value))
        node.set(qn('w:type'), 'dxa')


def add_page_number(paragraph):
    paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = paragraph.add_run('Página ')
    fld_char1 = OxmlElement('w:fldChar')
    fld_char1.set(qn('w:fldCharType'), 'begin')
    instr_text = OxmlElement('w:instrText')
    instr_text.set(qn('xml:space'), 'preserve')
    instr_text.text = 'PAGE'
    fld_char2 = OxmlElement('w:fldChar')
    fld_char2.set(qn('w:fldCharType'), 'end')
    run._r.append(fld_char1)
    run._r.append(instr_text)
    run._r.append(fld_char2)


def set_font(run, bold=False, size=10.5, color='000000'):
    run.font.name = 'Aptos'
    run._element.rPr.rFonts.set(qn('w:ascii'), 'Aptos')
    run._element.rPr.rFonts.set(qn('w:hAnsi'), 'Aptos')
    run.font.size = Pt(size)
    run.font.bold = bold
    run.font.color.rgb = RGBColor.from_string(color)


def add_text(doc, text, style=None, bold_prefix=None):
    paragraph = doc.add_paragraph(style=style)
    if bold_prefix and text.startswith(bold_prefix):
        set_font(paragraph.add_run(bold_prefix), bold=True)
        set_font(paragraph.add_run(text[len(bold_prefix):]))
    else:
        set_font(paragraph.add_run(text))
    return paragraph


def add_bullets(doc, items):
    for item in items:
        paragraph = doc.add_paragraph(style='List Bullet')
        set_font(paragraph.add_run(item))


def add_numbered(doc, items):
    for item in items:
        paragraph = doc.add_paragraph(style='List Number')
        set_font(paragraph.add_run(item))


def add_table(doc, headers, rows, widths=None):
    table = doc.add_table(rows=1, cols=len(headers))
    table.autofit = False
    table.style = 'Table Grid'
    header = table.rows[0]
    for index, value in enumerate(headers):
        cell = header.cells[index]
        cell.text = ''
        set_cell_shading(cell, '173D5B')
        set_cell_border(cell)
        set_cell_margins(cell)
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
        paragraph = cell.paragraphs[0]
        paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
        set_font(paragraph.add_run(value), bold=True, size=9.2, color='FFFFFF')
        if widths:
            cell.width = Cm(widths[index])
    for row_index, row_values in enumerate(rows):
        cells = table.add_row().cells
        for index, value in enumerate(row_values):
            cell = cells[index]
            cell.text = ''
            set_cell_border(cell)
            set_cell_margins(cell)
            cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
            if row_index % 2 == 1:
                set_cell_shading(cell, 'F3F7FA')
            paragraph = cell.paragraphs[0]
            set_font(paragraph.add_run(str(value)), size=9.2)
            if widths:
                cell.width = Cm(widths[index])
    doc.add_paragraph()
    return table


def heading(doc, text, level=1):
    paragraph = doc.add_paragraph(style=f'Heading {level}')
    run = paragraph.add_run(text)
    set_font(run, bold=True, size=16 if level == 1 else 12.5)
    paragraph.paragraph_format.space_before = Pt(14 if level == 1 else 9)
    paragraph.paragraph_format.space_after = Pt(7)
    return paragraph


doc = Document()
section = doc.sections[0]
section.top_margin = Cm(2.3)
section.bottom_margin = Cm(2.0)
section.left_margin = Cm(2.4)
section.right_margin = Cm(2.4)

styles = doc.styles
styles['Normal'].font.name = 'Aptos'
styles['Normal']._element.rPr.rFonts.set(qn('w:ascii'), 'Aptos')
styles['Normal']._element.rPr.rFonts.set(qn('w:hAnsi'), 'Aptos')
styles['Normal'].font.size = Pt(10.5)
styles['Normal'].paragraph_format.space_after = Pt(6)
for name in ['Title', 'Heading 1', 'Heading 2']:
    styles[name].font.color.rgb = RGBColor(0, 0, 0)

footer = section.footer.paragraphs[0]
add_page_number(footer)
for run in footer.runs:
    set_font(run, size=8.5, color='5B7184')

# Cover
for _ in range(5):
    doc.add_paragraph()
cover_title = doc.add_paragraph(style='Title')
cover_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
set_font(cover_title.add_run('Documentación del Sistema de Control Escolar'), bold=True, size=24)
cover_subtitle = doc.add_paragraph()
cover_subtitle.alignment = WD_ALIGN_PARAGRAPH.CENTER
set_font(cover_subtitle.add_run('Instituto Horizonte'), bold=True, size=17, color='173D5B')
doc.add_paragraph()
cover_text = doc.add_paragraph()
cover_text.alignment = WD_ALIGN_PARAGRAPH.CENTER
set_font(cover_text.add_run('Documento de entrega para estadías profesionales'), size=12, color='4E6577')
doc.add_paragraph()
cover_note = doc.add_paragraph()
cover_note.alignment = WD_ALIGN_PARAGRAPH.CENTER
set_font(cover_note.add_run('Sistema web desarrollado con Laravel 12'), size=10.5, color='4E6577')
doc.add_paragraph().add_run().add_break(WD_BREAK.PAGE)

# Contents
heading(doc, 'Contenido')
contents = [
    '1. Presentación del proyecto', '2. Alcance funcional', '3. Roles y permisos',
    '4. Manual de uso', '5. Arquitectura y tecnologías', '6. Seguridad y trazabilidad',
    '7. Instalación y despliegue', '8. Guion de demostración', '9. Trabajo futuro'
]
add_numbered(doc, contents)
doc.add_paragraph().add_run().add_break(WD_BREAK.PAGE)

heading(doc, '1. Presentación del proyecto')
add_text(doc, 'El Sistema de Control Escolar del Instituto Horizonte es una aplicación web creada para concentrar la información académica y administrativa de la escuela. Atiende la necesidad de sustituir registros dispersos por un proceso ordenado de consulta, actualización y seguimiento de expedientes.')
heading(doc, 'Problema atendido', 2)
add_text(doc, 'Cuando la información de alumnos se distribuye entre listas, formatos y archivos independientes, es más difícil localizar expedientes, conocer el estado actual de un alumno, asignar plataformas digitales y comprobar quién realizó un cambio. El sistema centraliza estas tareas y conserva evidencia de los movimientos relevantes.')
heading(doc, 'Objetivo general', 2)
add_text(doc, 'Facilitar la gestión segura y organizada de la información escolar, reduciendo errores de captura y permitiendo consultar el historial de cambios importantes.')
heading(doc, 'Objetivos específicos', 2)
add_bullets(doc, [
    'Registrar, consultar y actualizar expedientes de alumnos.',
    'Configurar niveles, grados, secciones y plataformas desde la interfaz.',
    'Gestionar bajas, reactivaciones y egresos sin eliminar expedientes.',
    'Promover grupos completos durante el cierre escolar.',
    'Restringir la información visible según el rol del usuario.',
    'Generar documentos PDF y conservar historial de operaciones.'
])

heading(doc, '2. Alcance funcional')
add_table(doc, ['Área', 'Funcionalidad'], [
    ['Alumnos', 'Alta, consulta, búsqueda, expediente, edición, contactos y hermanos.'],
    ['Estructura académica', 'Niveles, grados, secciones y plataformas configurables.'],
    ['Movimientos escolares', 'Bajas, reactivaciones, promoción grupal, cierre escolar y egresos.'],
    ['Plataformas', 'Asignación por nivel y resguardo de credenciales por alumno.'],
    ['Administración', 'Usuarios, roles, activación de cuentas, identidad visual, configuración y respaldos guiados.'],
    ['Seguimiento', 'Historial del alumno e historial de seguridad.'],
    ['Reportes', 'Generación de documentos PDF autorizados.'],
], [4.1, 12.1])

heading(doc, '3. Roles y permisos')
add_text(doc, 'El acceso se controla mediante cuentas activas y roles. Cada persona visualiza únicamente los módulos y niveles que necesita para realizar su trabajo.')
add_table(doc, ['Rol', 'Alcance'], [
    ['SuperAdmin', 'Administra usuarios, roles, estructura académica, configuración, historiales y todos los alumnos.'],
    ['Control Escolar', 'Gestiona expedientes, altas, bajas, archivados, egresos, promoción y cierre escolar. No administra usuarios ni roles.'],
    ['Coordinación de Preescolar', 'Consulta y gestiona únicamente Preescolar.'],
    ['Coordinación de Primaria', 'Consulta y gestiona Primaria Baja y Primaria Alta.'],
    ['Coordinación de Secundaria', 'Consulta y gestiona únicamente Secundaria.'],
], [4.7, 11.5])

heading(doc, '4. Manual de uso')
heading(doc, 'Inicio de sesión y panel principal', 2)
add_numbered(doc, ['Abra el sistema en el navegador.', 'Escriba el correo y contraseña proporcionados por la administración.', 'Seleccione Iniciar sesión.'])
add_text(doc, 'No existe registro público. Las cuentas, roles, contraseñas y activación de usuarios son responsabilidad del SuperAdmin.')
add_text(doc, 'El panel principal muestra indicadores y accesos rápidos. El buscador permite localizar alumnos por nombre, apellido o matrícula. Las sugerencias respetan los permisos del usuario que inició sesión.')
heading(doc, 'Registro y edición de alumnos', 2)
add_numbered(doc, ['Seleccione el nivel educativo.', 'Complete los datos del alumno y de sus contactos.', 'Agregue hermanos sólo cuando aplique; el formulario permite añadir los necesarios.', 'Revise la información y guarde.'])
add_text(doc, 'Para actualizar un expediente, primero se consulta la información, luego se selecciona Editar expediente, se modifican únicamente los datos necesarios y finalmente se guardan los cambios. Las actualizaciones relevantes se registran en el historial del alumno.')
heading(doc, 'Bajas, reactivaciones y egresos', 2)
add_bullets(doc, [
    'Una baja retira al alumno de los listados activos, pero conserva su expediente y exige un motivo.',
    'En Alumnos archivados se consulta el motivo y la fecha de baja.',
    'Una reactivación solicita un motivo y conserva ambos movimientos en el historial.',
    'Los egresados se mantienen separados de las bajas como consulta histórica.'
])
heading(doc, 'Promoción y cierre escolar', 2)
add_numbered(doc, ['Filtre por nivel, grado y sección.', 'Revise los alumnos del grupo.', 'Confirme la promoción al siguiente grado.'])
add_text(doc, 'Si un grupo no tiene siguiente grado, el sistema lo registra como egresado. El proceso está diseñado para mover grupos completos y conservar evidencia del movimiento.')
heading(doc, 'Plataformas y configuración académica', 2)
add_text(doc, 'La configuración se realiza en el orden nivel educativo, grado, sección y plataforma. Las plataformas se asignan por nivel. Desasignar una plataforma o desactivarla no elimina el historial de credenciales de alumnos; el sistema muestra advertencias cuando existen datos relacionados.')
add_text(doc, 'Al inicio de Configuración académica, el SuperAdmin puede personalizar el nombre de la escuela, subtítulo, logotipo y colores del portal. La vista previa permite revisar el resultado antes de guardar. Los cambios se reflejan en el inicio de sesión, menú, panel, tablas y documentos PDF; restaurar los valores de ejemplo no modifica información escolar.')
heading(doc, 'Usuarios e historiales', 2)
add_text(doc, 'El SuperAdmin puede crear cuentas, asignar o retirar roles y activar o desactivar accesos. El historial de alumnos registra movimientos sobre expedientes y el historial de seguridad registra accesos, acciones administrativas y cambios relevantes.')

heading(doc, '5. Arquitectura y tecnologías')
add_text(doc, 'El sistema utiliza la arquitectura MVC de Laravel. Las rutas envían las solicitudes a controladores, los controladores aplican reglas de negocio, los modelos consultan MySQL mediante Eloquent y las vistas Blade presentan la información al usuario.')
add_table(doc, ['Componente', 'Tecnología o responsabilidad'], [
    ['Backend', 'Laravel 12 y PHP 8.2.'],
    ['Base de datos', 'MySQL.'],
    ['Interfaz', 'Blade, Bootstrap Tabler, JavaScript y Vite.'],
    ['Autenticación', 'Laravel Breeze.'],
    ['Roles', 'Spatie Laravel Permission.'],
    ['PDF', 'DomPDF.'],
    ['API futura', 'Laravel Sanctum para una posible aplicación cliente.'],
], [4.3, 11.9])
heading(doc, 'Estructura de datos', 2)
add_table(doc, ['Entidad', 'Descripción'], [
    ['Usuario y rol', 'Cuenta autenticada y permisos asignados.'],
    ['Nivel, grado y sección', 'Jerarquía académica configurable.'],
    ['Alumno', 'Expediente activo con datos académicos y familiares.'],
    ['Alumno archivado', 'Expediente de baja conservado para consulta y reactivación.'],
    ['Egresado', 'Alumno que concluyó el último grado.'],
    ['Plataforma', 'Recurso digital asignable a niveles y alumnos.'],
    ['Configuración institucional', 'Nombre, subtítulo, logotipo y colores editables por SuperAdmin.'],
    ['Historiales', 'Evidencia de cambios sobre alumnos y administración.'],
], [4.3, 11.9])

heading(doc, '6. Seguridad y trazabilidad')
add_bullets(doc, [
    'Autenticación mediante Laravel Breeze y contraseñas almacenadas con hash.',
    'Registro público y recuperación pública de contraseñas deshabilitados.',
    'Validación de cuentas activas antes de permitir el acceso.',
    'Roles y límites por nivel educativo centralizados.',
    'Protección CSRF en formularios y cabeceras de seguridad.',
    'Historial de alumnos e historial de seguridad para acciones relevantes.',
    'Archivos locales, respaldos y datos sensibles excluidos del repositorio.'
])

heading(doc, '7. Instalación y despliegue')
heading(doc, 'Requisitos', 2)
add_bullets(doc, ['PHP 8.2 o superior.', 'Composer.', 'Node.js y npm.', 'MySQL, por ejemplo XAMPP.', 'Git.'])
heading(doc, 'Instalación local', 2)
add_numbered(doc, [
    'Clone el repositorio y entre a la carpeta del proyecto.',
    'Instale dependencias de PHP con composer install y recursos con npm install.',
    'Copie .env.example como .env y configure exclusivamente una base de datos local.',
    'Ejecute php artisan key generate y php artisan migrate.',
    'Inicie Laravel con php artisan serve y Vite con npm run dev.'
])
add_text(doc, 'Antes de publicar, se recomienda compilar recursos, ejecutar las pruebas disponibles, revisar el estado de Git y verificar que no se incluyan archivos .env, respaldos SQL, documentos generados, fotos o datos personales.')

heading(doc, '8. Guion de demostración')
add_table(doc, ['Momento', 'Demostración sugerida'], [
    ['Contexto', 'Explique la necesidad de centralizar expedientes y controlar cambios.'],
    ['Panel', 'Inicie sesión con SuperAdmin y muestre indicadores, buscador y accesos.'],
    ['Expediente', 'Busque un alumno, abra su expediente y muestre contactos e historial.'],
    ['Movimientos', 'Explique bajas, reactivaciones y diferencia entre archivado y egresado.'],
    ['Configuración', 'Muestre la identidad institucional, la jerarquía nivel, grado, sección y plataformas por nivel.'],
    ['Seguridad', 'Compare los permisos de una coordinación con SuperAdmin e indique los historiales.'],
    ['Cierre escolar', 'Explique la promoción grupal y el egreso del último grado.'],
], [4.0, 12.2])
add_text(doc, 'Mensaje de cierre: El sistema organiza el ciclo de vida del expediente escolar, controla quién puede modificarlo y conserva evidencia de los cambios. La estructura académica puede crecer desde la interfaz sin depender de cambios en el código.', bold_prefix='Mensaje de cierre: ')

heading(doc, '9. Trabajo futuro')
add_text(doc, 'La aplicación web Laravel cubre el objetivo principal de control escolar. Las ampliaciones recomendadas son asistencia diaria con reportes mensuales, boletas por periodo, importación y exportación de alumnos y reportes de seguimiento. La API y el prototipo Angular se conservan como trabajo futuro independiente; no son necesarios para operar la entrega actual mientras la institución no defina el flujo de calificaciones.')

doc.core_properties.title = 'Documentación del Sistema de Control Escolar'
doc.core_properties.subject = 'Documento de entrega del Sistema de Control Escolar Instituto Horizonte'
doc.core_properties.author = 'Instituto Horizonte'
doc.save(OUTPUT)
