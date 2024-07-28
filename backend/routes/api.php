<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ConsumoController;
use App\Http\Controllers\DialogController;
use App\Http\Controllers\EstadiaController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TipoHabitacionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin,recep'])->group(function () {
    Route::apiResource('personales', PersonalController::class);

    Route::apiResource('sucursales', SucursalController::class);
    Route::put('/activeSucursal/{id}', [SucursalController::class, 'activeSucursal']);
    Route::get('/habitaciones-activas', [DialogController::class, 'getHabitacionesActivas']);
    Route::get('/clientes-activos', [DialogController::class, 'getClientesActivos']);
    Route::apiResource('reservas', ReservaController::class);
    Route::post('/recepcion-habitaciones', [HabitacionController::class, 'recepcionHabitaciones']);
    Route::post('/recepcion-habitaciones-salida', [HabitacionController::class, 'recepcionHabitacionesSalida']);
    Route::post('/estadias', [EstadiaController::class, 'store']);
    Route::get('/estadias', [EstadiaController::class, 'index']);
    Route::put('/editar-estadia/{id}', [EstadiaController::class, 'update']);
    Route::put('/check-salida/{id}', [EstadiaController::class, 'check_salida']);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('habitaciones', HabitacionController::class);
    Route::put('/activeHabitacion/{id}', [HabitacionController::class, 'activeHabitacion']);
    Route::apiResource('pisos', PisoController::class);
    Route::put('/activePiso/{id}', [PisoController::class, 'activePiso']);
    Route::get('/pisos-activos', [DialogController::class, 'getPisosActivos']);
    Route::get('/sucursales-activas', [DialogController::class, 'getSucursalesActivas']);
    Route::get('/productos-activos', [DialogController::class, 'getProductosActivos']);
    Route::post('/add-consumo', [ConsumoController::class, 'store']);
    Route::put('/pagarDetalle/{id}', [ConsumoController::class, 'pagarDetalle']);
    Route::put('/terminarLimpieza/{id}', [HabitacionController::class, 'terminarLimpieza']);
    Route::apiResource('productos', ProductoController::class);
    Route::put('/activeProducto/{id}', [ProductoController::class, 'activeProducto']);
    Route::put('/activePersonal/{id}', [PersonalController::class, 'activePersonal']);
    Route::get('/personal-activos', [DialogController::class, 'getPersonalActivos']);
    Route::post('/getAtencionPersonal', [ReporteController::class, 'getAtencionPersonal']);
    Route::get('/getEstadisticas', [ReporteController::class, 'getEstadisticas']);
    Route::get('/totalYear', [ReporteController::class, 'totalYear']);
    Route::apiResource('tipo-habitaciones', TipoHabitacionController::class);
    Route::put('/activateCliente/{id}', [ClienteController::class, 'activeClient']);
    Route::put('/agregar-hora/{id}', [EstadiaController::class, 'agregar_horas']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
