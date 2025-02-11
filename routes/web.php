<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;



// Rutas generales
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de los alumnos
Route::middleware('auth')->group(function () {
    // Mostrar todos los alumnos
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    // Buscar un alumno
    Route::get('/alumnos/search', [AlumnoController::class, 'search'])->name('alumnos.search');
    // Crear un nuevo alumno
    Route::get('/alumnos/create', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
    // Ver un alumno específico
    Route::get('/alumnos/{alumno}', [AlumnoController::class, 'show'])->name('alumnos.show');
    // Editar un alumno
    Route::get('/alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
    Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
    // Eliminar un alumno
    Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

    //Admin

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.admincreate');
    Route::post('/admin', [AdminController::class, 'store'])->name('admin.storeAdmin');
    Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.adminedit');
    Route::put('/admin/alumnos/{id}', [AdminController::class, 'update'])->name('admin.adminupdate');


    //pdf

    // Generar PDF de todos los alumnos
    Route::get('/admin/alumnos/pdf', [AdminController::class, 'generarPDF'])->name('admin.alumnos.pdf');

    // Generar PDF de un alumno específico dentro de un nivel
    Route::get('/admin/alumnos/pdf/{nivel}/{id}', [AdminController::class, 'generarPdf'])
        ->name('admin.alumnos.pdf.individual');

    //nivel

    Route::get('/admin/niveles', [AdminController::class, 'nivelesIndex'])->name('admin.niveles');
    Route::get('/admin/niveles/{nivel}', [AdminController::class, 'nivelesShow'])->name('niveles.show');




});

// Rutas de perfil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas Exclusivas para Admin
//Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard del Admin
  //  Route::get('/dashboard', function () {return view('admin.dashboard');})->name('dashboard');
    
    // Rutas para gestión de alumnos
    //Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
   // Route::get('/alumnos/search', [AlumnoController::class, 'search'])->name('alumnos.search');
    //Route::get('/alumnos/generate-pdf', [AlumnoController::class, 'generatePDF'])->name('alumnos.generatePDF');
    //Route::get('/alumnos/send-email', [AlumnoController::class, 'sendEmail'])->name('alumnos.sendEmail');
//});

require __DIR__.'/auth.php';
