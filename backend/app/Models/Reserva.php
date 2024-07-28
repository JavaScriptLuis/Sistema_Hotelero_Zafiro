<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'habitacion_id',
        'personal_id',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'observacion',
        'adelanto',
        'estado',
        'hora_extendido'
    ];
    public function relacionCliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function relacionHabitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function relacionPersonal()
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }
    public function confirmar()
    {
        // $this->update([
        //     'estado' => 'confirmada',
        // ]);
        $this->relacionHabitacion->reservar();
    }
}
