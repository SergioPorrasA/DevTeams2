<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\AdminEventoController;
use App\Http\Controllers\AdminEquipoController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\JuezController;

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas para PARTICIPANTES
Route::middleware(['role:Participante'])->group(function () {
    Route::get('/dashboard', [EquipoController::class, 'index'])->name('dashboard');
    Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::get('/equipos/{id}', [EquipoController::class, 'show'])->name('equipos.show');
    Route::post('/equipos/{id}/salir', [EquipoController::class, 'leave'])->name('equipos.leave');
    Route::post('/equipos/{id}/invitar', [EquipoController::class, 'invite'])->name('equipos.invite');
    
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/{id}', [EventoController::class, 'show'])->name('eventos.show');
    Route::get('/eventos/{id}/inscripcion', [EventoController::class, 'inscripcion'])->name('eventos.inscripcion');
    Route::post('/eventos/{id}/inscribirse', [EventoController::class, 'inscribirse'])->name('eventos.inscribirse');
    
    Route::get('/codigos', function () {
        return view('codigos.index');
    })->name('codigos.index');
    
    Route::get('/invitaciones', function () {
        return view('invitaciones.index');
    })->name('invitaciones.index');
});

// Rutas ADMIN (prefix 'admin')
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Ruta de Jueces - Accesible por Administrador Y Juez
    Route::middleware(['auth'])->get('/jueces', [JuezController::class, 'index'])->name('jueces.index');
    
    // Rutas SOLO para Administradores
    Route::middleware(['role:Administrador'])->group(function () {
        // Equipos
        Route::get('/equipos', [AdminEquipoController::class, 'index'])->name('equipos.index');
        Route::get('/equipos/{id}', [AdminEquipoController::class, 'show'])->name('equipos.show');
        Route::delete('/equipos/{id}', [AdminEquipoController::class, 'destroy'])->name('equipos.destroy');
        
        // Eventos
        Route::get('/eventos', [AdminEventoController::class, 'index'])->name('eventos.index');
        Route::post('/eventos', [AdminEventoController::class, 'store'])->name('eventos.store');
        Route::get('/eventos/{id}', [AdminEventoController::class, 'show'])->name('eventos.show');
        Route::get('/eventos/{id}/equipos', [AdminEventoController::class, 'equiposInscritos'])->name('eventos.equipos');
        Route::get('/eventos/{id}/edit', [AdminEventoController::class, 'edit'])->name('eventos.edit');
        Route::put('/eventos/{id}', [AdminEventoController::class, 'update'])->name('eventos.update');
        Route::delete('/eventos/{id}', [AdminEventoController::class, 'destroy'])->name('eventos.destroy');
    });
});