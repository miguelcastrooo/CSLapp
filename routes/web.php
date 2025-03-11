<?php

use App\Http\Controllers\CoordinacionController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

// Rutas generales
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas para los alumnos
Route::get('/alumnos', [AlumnoController::class, 'index'])->name('capturista.index');
Route::get('/alumnos/search', [AlumnoController::class, 'search'])->name('alumnos.search');
Route::get('/alumnos/selectsearch', [AlumnoController::class, 'selectSearch'])->name('capturista.selectsearch');
Route::get('/alumnos/search/{nivel?}', [AlumnoController::class, 'search'])->name('alumnos.searchnivel');
Route::get('/alumnos/select', [AlumnoController::class, 'select'])->name('alumnos.select');
Route::get('alumnos/create/{nivel}', [AlumnoController::class, 'create'])->name('alumnos.create');
Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
Route::get('/alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');
Route::get('/alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

Route::get('/send-email/{id}', [AlumnoController::class, 'sendAlumnoEmail']);

Route::get('/grados/{nivel_id}', [AlumnoController::class, 'getGrados'])->name('grados.nivel');



// Admin (Middleware: role:SuperAdmin)
Route::middleware(['auth', 'role:SuperAdmin|CoordinacionPreescolar|CoordinacionPrimaria|CoordinacionSecundaria'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.admincreate');
    Route::post('/admin', [AdminController::class, 'store'])->name('admin.storeAdmin');
    Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.adminedit');
    Route::put('/admin/alumnos/{id}', [AdminController::class, 'update'])->name('admin.adminupdate');
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');

    // Generar PDF de alumnos
    Route::get('/admin/alumnos/pdf', [AdminController::class, 'generarPDF'])->name('admin.alumnos.pdf');
    Route::get('/admin/alumnos/pdf/{nivel}/{id}', [AdminController::class, 'generarPdf'])->name('admin.alumnos.pdf.individual');
    Route::get('/admin/alumnos/pdf/{id}', [AdminController::class, 'generarPdfPorId'])->name('admin.alumnos.pdf.id');

    // Nueva ruta para generar PDF de plataformas según el nivel educativo
    Route::get('/admin/alumnos/pdf/plataformas/{nivel}', [AdminController::class, 'generarPdfPlataformas'])->name('admin.alumnos.pdf.plataformas');

    // Ruta para mostrar el formulario de mover grupos
    Route::get('/admin/mover-grupos', [AdminController::class, 'mostrarFormularioMoverGrupos'])->name('admin.grupos.form');

    // Ruta para procesar el movimiento de los grupos
    Route::post('/admin/mover-grupos', [AdminController::class, 'moverGrupos'])->name('admin.grupos.mover');

    // Ruta para obtener grados y niveles filtrados
    Route::post('/admin/obtener-grados-niveles', [AdminController::class, 'obtenerGradosYNiveles'])->name('admin.obtenerGradosYNiveles');





    // Niveles
    Route::get('/admin/niveles', [AdminController::class, 'nivelesIndex'])->name('admin.niveles');
    Route::get('/admin/select', [AdminController::class, 'select'])->name('admin.select');
    Route::get('/admin/niveles/{nivelId}', [AdminController::class, 'nivelesShow'])->name('niveles.show');

    Route::get('admin/selectadmin', [AdminController::class, 'selectAdmin'])->name('admin.selectadmin');
    Route::get('/admin/select/alumnos/{nivelId}', [AdminController::class, 'showNivelAlumnos'])->name('admin.showNivelAlumnos');

    // Dar de baja a un alumno
    Route::put('/admin/alumnos/confirmarBaja/{id}', [AdminController::class, 'confirmarBaja'])->name('admin.confirmarBaja');
});

Route::middleware(['auth', 'role:CoordinacionPreescolar|CoordinacionPrimaria|CoordinacionSecundaria'])->group(function () {
    // Rutas para el coordinador, dependiendo del rol
    Route::prefix('coordinador')->name('coordinador.')->group(function () {
        
        // Página principal donde se muestran los alumnos según el nivel educativo
        Route::get('/', [CoordinacionController::class, 'index'])->name('index');

        Route::get('coordinacion/alumnos/{id}', [CoordinacionController::class, 'show'])->name('coordinacion.alumnos.show');
        
        // Ruta para editar la información de un alumno
        Route::get('/alumno/{alumno}/edit', [CoordinacionController::class, 'edit'])->name('edit');
        Route::put('/alumno/{alumno}', [CoordinacionController::class, 'update'])->name('update');
        
        // Ruta para generar un PDF de un alumno
        Route::get('/alumno/{alumno}/pdf', [CoordinacionController::class, 'generatePdf'])->name('generatePdf');
    });
});


//Coordinacion
Route::middleware(['auth'])->group(function () {
    // Solo usuarios con rol de AdministracionPreescolar, AdministracionPrimariaBaja, AdministracionPrimariaAlta, AdministracionSecundaria
    Route::resource('/coordinacion', CoordinacionController::class)->only(['index', 'edit', 'update']);
});



// Rutas de perfil (Disponible para todos los usuarios autenticados)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
