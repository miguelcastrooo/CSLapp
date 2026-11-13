<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\CoordinacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------
// Rutas generales
// ----------------------------------------
Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'account.active', 'school.verified'])
    ->name('dashboard');

// ----------------------------------------
// Rutas de alumnos
// ----------------------------------------
Route::middleware(['auth', 'account.active', 'school.verified'])->group(function () {
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('capturista.index');
    Route::get('/alumnos/search', [AlumnoController::class, 'search'])->name('alumnos.search');
    Route::get('/alumnos/selectsearch', [AlumnoController::class, 'selectSearch'])->name('capturista.selectsearch');
    Route::get('/alumnos/search/{nivel?}', [AlumnoController::class, 'search'])->name('alumnos.searchnivel');
    Route::get('/alumnos/select', [AlumnoController::class, 'select'])->name('alumnos.select');

    Route::get('alumnos/create/{nivel}', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::get('/alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
    Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
    Route::get('/grados/{nivel_id}', [AlumnoController::class, 'getGrados'])->name('grados.nivel');
    Route::get('/secciones/{grado_id}', [AlumnoController::class, 'getSecciones'])->name('secciones.grado');

    // Bajas y reactivaciones
    Route::get('/alumnos/baja', [AlumnoController::class, 'indexBaja'])->name('index.baja');
    Route::post('/alumnos/baja', [AlumnoController::class, 'darBaja'])->name('alumno.baja');
    Route::get('/alumnos/archivados', [AlumnoController::class, 'indexArchivados'])->name('alumnos.archivados');
    Route::get('/alumnos/egresados', [AlumnoController::class, 'indexEgresados'])->name('alumnos.egresados');
    Route::post('/alumnos/archivados/{id}/reactivar', [AlumnoController::class, 'reactivar'])->name('alumnos.reactivar');
});

// ----------------------------------------
// Rutas protegidas con roles (SuperAdmin, Coordinaciones, ControlEscolar)
// ----------------------------------------
Route::middleware(['auth', 'account.active', 'school.verified', 'role:SuperAdmin|CoordinacionPreescolar|CoordinacionPrimaria|CoordinacionSecundaria|ControlEscolar'])->group(function () {

    // Panel de administración
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Gestión de administradores
    Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.admincreate');
    Route::post('/admin', [AdminController::class, 'store'])->name('admin.storeAdmin');
    Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.adminedit');
    Route::put('/admin/alumnos/{id}', [AdminController::class, 'update'])->name('admin.adminupdate');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

    // PDF de alumnos
    Route::get('/admin/alumnos/pdf', [AdminController::class, 'generarPDF'])->name('admin.alumnos.pdf');
    Route::get('/admin/alumnos/pdf/{nivel}/{id}', [AdminController::class, 'generarPdf'])->name('admin.alumnos.pdf.individual');
    Route::get('admin/generarPdfTodos/{nivel}', [AdminController::class, 'generarPdfTodos'])->name('admin.generarPdfTodos');
    Route::get('/admin/alumnos/pdf/{id}', [AdminController::class, 'generarPdfPorId'])->name('admin.alumnos.pdf.id');
    Route::get('/admin/alumnos/pdf/plataformas/{nivel}', [AdminController::class, 'generarPdfPlataformas'])->name('admin.alumnos.pdf.plataformas');

    // Envío de correos con PDF
    Route::post('admin/alumnos/{alumnoId}/enviarCorreo', [AdminController::class, 'enviarCorreoConPdf'])->name('admin.enviarCorreo');
    Route::post('/admin/enviarCorreoATodos/{nivel}', [AdminController::class, 'enviarCorreoATodos'])->name('admin.enviarCorreoATodos');

    // Mover grupos
    Route::get('/admin/mover-grupos', [AdminController::class, 'mostrarFormularioMoverGrupos'])->name('admin.grupos.form');
    Route::get('/admin/cierre-escolar', [AdminController::class, 'cierreEscolar'])->name('admin.cierre-escolar');
    Route::post('/admin/obtener-grados-niveles', [AdminController::class, 'obtenerGradosYNiveles'])->name('admin.obtenerGradosYNiveles');

    // Niveles
    Route::get('/admin/niveles', [AdminController::class, 'nivelesIndex'])->name('admin.niveles');
    Route::get('/admin/select', [AdminController::class, 'select'])->name('admin.select');
    Route::get('/admin/niveles/{nivelId}', [AdminController::class, 'nivelesShow'])->name('niveles.show');
    Route::get('admin/selectadmin', [AdminController::class, 'selectAdmin'])->name('admin.selectadmin');
    Route::get('/admin/select/alumnos/{nivelId}', [AdminController::class, 'showNivelAlumnos'])->name('admin.showNivelAlumnos');

    // Grados y Coordinación
    Route::get('/admin/grados', [CoordinacionController::class, 'gradosIndex'])->name('admin.grados');
    Route::get('/admin/select/grados', [CoordinacionController::class, 'selectGrado'])->name('admin.selectGrado');
    Route::get('/admin/coordinacion/grados/{gradoId}', [CoordinacionController::class, 'gradosShow'])->name('coordinacion.grados.show');
    Route::get('/admin/select/alumnos/grado/{gradoId}', [CoordinacionController::class, 'showGradoAlumnos'])->name('admin.grados.alumnos');
    Route::get('/admin/coordinacion/alumnos/{id}', [CoordinacionController::class, 'showAlumno'])->name('admin.coordinacion.alumnos.show');
    Route::get('/admin/nivel/{nivelId}/alumnos', [CoordinacionController::class, 'mostrarAlumnos'])->name('admin.nivel.alumnos');

    // Gestión de roles
    Route::get('/admin/assign-roles', [AdminController::class, 'assignRoles'])->name('admin.assignRoles');
    Route::get('/admin/configuracion', [AdminController::class, 'configuracion'])->name('admin.configuracion');
    Route::get('/admin/respaldo', [AdminController::class, 'respaldo'])->name('admin.respaldo');
    Route::post('/admin/configuracion/niveles', [AdminController::class, 'guardarNivel'])->name('admin.configuracion.niveles');
    Route::post('/admin/configuracion/grados', [AdminController::class, 'guardarGrado'])->name('admin.configuracion.grados');
    Route::post('/admin/configuracion/secciones', [AdminController::class, 'guardarSeccion'])->name('admin.configuracion.secciones');
    Route::post('/admin/configuracion/plataformas', [AdminController::class, 'guardarPlataforma'])->name('admin.configuracion.plataformas');
    Route::post('/admin/configuracion/niveles/{nivel}/plataformas', [AdminController::class, 'asignarPlataformasNivel'])->name('admin.configuracion.nivel-plataformas');
    Route::post('/admin/configuracion/{tipo}/{id}/estado', [AdminController::class, 'cambiarEstadoConfiguracion'])->name('admin.configuracion.estado');
    Route::post('/admin/assign-roles', [AdminController::class, 'saveAssignedRoles'])->name('admin.saveAssignedRoles');
    Route::delete('/admin/remove-role/{user}/{role}', [AdminController::class, 'removeRole'])->name('admin.removeRole');
    Route::post('/admin/users/{user}/estado', [AdminController::class, 'cambiarEstadoUsuario'])->name('admin.user.status');
    Route::get('/admin/historial', [AdminController::class, 'historial'])->name('admin.historial');
    Route::get('/admin/historial-seguridad', [AdminController::class, 'historialSeguridad'])->name('admin.historialSeguridad');

    // Promociones y filtros
    Route::post('/admin/alumnos/promover', [AdminController::class, 'promover'])->name('admin.promover');
    Route::get('/admin/alumnos/filtrar', [AdminController::class, 'filtrarAlumnos'])->name('alumnos.filtrar');

    // Calificaciones permanece deshabilitado hasta terminar su rediseño, permisos y reglas por nivel.

});

// ----------------------------------------
// Rutas de perfil (usuarios autenticados)
// ----------------------------------------
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/update-picture', [ProfileController::class, 'updatePicture'])->name('profile.update-picture');
});

// ----------------------------------------
// Rutas de autenticación (Laravel Breeze)
// ----------------------------------------
require __DIR__.'/auth.php';
