<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Registro;

class ActualizarHorasRegistros extends Command
{
    protected $signature = 'registros:actualizar-horas';
    protected $description = 'Actualiza las horas totales de los registros activos';

    public function handle()
    {
        Registro::where('estado', 'activa')->each(function ($registro) {
            $registro->actualizarTotalHoras();
        });

        $this->info('Horas de registros actualizadas.');
    }
}
