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
Route::get('/alumnos/select', [AlumnoController::class, 'select'])->name('alumnos.select');
Route::get('alumnos/create/{nivel}', [AlumnoController::class, 'create'])->name('alumnos.create');
Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
Route::get('/alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');
Route::get('/alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

// Admin (Middleware: role:SuperAdmin)
Route::middleware(['auth', 'role:SuperAdmin'])->group(function () {
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


    // Niveles
    Route::get('/admin/niveles', [AdminController::class, 'nivelesIndex'])->name('admin.niveles');
    Route::get('/admin/select', [AdminController::class, 'select'])->name('admin.select');
    Route::get('/admin/niveles/{nivelId}', [AdminController::class, 'nivelesShow'])->name('niveles.show');

    Route::get('admin/selectadmin', [AdminController::class, 'selectAdmin'])->name('admin.selectadmin');
    Route::get('/admin/select/alumnos/{nivelId}', [AdminController::class, 'showNivelAlumnos'])->name('admin.showNivelAlumnos');

    // Dar de baja a un alumno
    Route::put('/admin/alumnos/confirmarBaja/{id}', [AdminController::class, 'confirmarBaja'])->name('admin.confirmarBaja');
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
