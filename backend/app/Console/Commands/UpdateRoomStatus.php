<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Models\Registro;
use Carbon\Carbon;

class UpdateRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:update-room-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de las habitaciones según la hora de fin de las reservas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $estadias = Registro::where('estado', 'confirmada')
                            ->where('hora_fin', '<', $now)
                            ->where('hora_extendido', false)
                            ->get();

        foreach ($estadias as $estadia) {
            $estadia->hora_extendido = true;
            $estadia->save();

            $habitacion = $estadia->habitacion;
            // if ($habitacion->check_status === 'ocupado') {
            //     $habitacion->check_status = 'ocupado rebasado'; // Puedes ajustar el estado según sea necesario
            //     $habitacion->save();
            // }
        }

        $this->info('Estado de habitaciones y reservas actualizado correctamente.');
    }
}
