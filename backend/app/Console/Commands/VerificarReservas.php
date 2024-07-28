<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReservaController;
class VerificarReservas extends Command
{
    protected $signature = 'reservas:verificar';
    protected $description = 'Verifica y extiende las reservas activas si es necesario';

    public function handle()
    {
        $controller = new ReservaController();
        $controller->verificarReserva();
        $this->info('Verificación de reservas completada.');
    }
}
