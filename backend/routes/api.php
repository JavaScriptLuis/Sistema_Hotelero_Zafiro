<?php

// Importación de controladores y Route
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

// Grupo de rutas con middleware de autenticación y roles específicos
Route::middleware(['auth:sanctum', 'role:admin,recep'])->group(function () {
    // CRUD completo para personal
    Route::apiResource('personales', PersonalController::class);
    
    // CRUD completo para sucursales con ruta adicional para activar una sucursal
    Route::apiResource('sucursales', SucursalController::class);
    Route::put('/activeSucursal/{id}', [SucursalController::class, 'activeSucursal']);

    // Rutas para obtener listados de entidades activas
    Route::get('/habitaciones-activas', [DialogController::class, 'getHabitacionesActivas']);
    Route::get('/clientes-activos', [DialogController::class, 'getClientesActivos']);

    // CRUD completo para reservas
    Route::apiResource('reservas', ReservaController::class);

    // Rutas para la recepción y salida de habitaciones
    Route::post('/recepcion-habitaciones', [HabitacionController::class, 'recepcionHabitaciones']);
    Route::post('/recepcion-habitaciones-salida', [HabitacionController::class, 'recepcionHabitacionesSalida']);

    // CRUD completo para estancias con rutas adicionales para editar y realizar check-out
    Route::apiResource('estadias', EstadiaController::class);
    Route::put('/editar-estadia/{id}', [EstadiaController::class, 'update']);
    Route::put('/check-salida/{id}', [EstadiaController::class, 'check_salida']);

    // CRUD completo para clientes
    Route::apiResource('clientes', ClienteController::class);

    // CRUD completo para habitaciones con ruta adicional para activar una habitación
    Route::apiResource('habitaciones', HabitacionController::class);
    Route::put('/activeHabitacion/{id}', [HabitacionController::class, 'activeHabitacion']);

    // CRUD completo para pisos con ruta adicional para activar un piso
    Route::apiResource('pisos', PisoController::class);
    Route::put('/activePiso/{id}', [PisoController::class, 'activePiso']);

    // Rutas para obtener pisos, sucursales y productos activos
    Route::get('/pisos-activos', [DialogController::class, 'getPisosActivos']);
    Route::get('/sucursales-activas', [DialogController::class, 'getSucursalesActivas']);
    Route::get('/productos-activos', [DialogController::class, 'getProductosActivos']);

    // Rutas para manejar consumos y pagos de detalles
    Route::post('/add-consumo', [ConsumoController::class, 'store']);
    Route::put('/pagarDetalle/{id}', [ConsumoController::class, 'pagarDetalle']);

    // CRUD completo para productos con ruta adicional para activar un producto
    Route::apiResource('productos', ProductoController::class);
    Route::put('/activeProducto/{id}', [ProductoController::class, 'activeProducto']);

    // Rutas adicionales para personal y reportes
    Route::put('/activePersonal/{id}', [PersonalController::class, 'activePersonal']);
    Route::get('/personal-activos', [DialogController::class, 'getPersonalActivos']);
    Route::post('/getAtencionPersonal', [ReporteController::class, 'getAtencionPersonal']);
    Route::get('/getEstadisticas', [ReporteController::class, 'getEstadisticas']);
    Route::get('/totalYear', [ReporteController::class, 'totalYear']);
    Route::apiResource('tipo-habitaciones', TipoHabitacionController::class);
    Route::put('/activateCliente/{id}', [ClienteController::class, 'activeClient']);
    Route::put('/agregar-hora/{id}', [EstadiaController::class, 'agregar_horas']);
    Route::put('/terminarLimpieza/{id}', [HabitacionController::class, 'terminarLimpieza']);
});

// Grupo de rutas para autenticación sin middleware de autenticación para el registro y el inicio de sesión
Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Ruta de cierre de sesión que requiere autenticación
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
