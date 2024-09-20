archivo web.php

<?php

use Illuminate\Support\Facades\Route;

use  Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta para la vista de productos (puedes ajustarla a tu necesidad)
Route::get('/products', function () {
    return view('welcome');
});

// Ruta para descargar el archivo PDF
Route::get('/manual-usuario', function () {
    // Definir la ruta completa del archivo PDF
    $path = storage_path('app/public/MANUAL_DE_USUARIO_SISTEMA_ZAFIRO.pdf');
    
    // Verificar si el archivo existe antes de intentar descargarlo
    if (file_exists($path)) {
        return response()->download($path); // Descargar el archivo si existe
    } else {
        abort(404, 'Archivo no encontrado.'); // Mostrar error si no se encuentra
    }
});
