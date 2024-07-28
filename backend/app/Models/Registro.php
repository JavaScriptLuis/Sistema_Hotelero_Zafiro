<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Registro extends Model
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
        'hora_extendido',
        'check_in',
        'check_out',
        'total_pago',
        'adelanto',
        'estado',
    ];
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class);
    }
    public function relacionCliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function calcularHorasTranscurridas()
    {
        if ($this->check_in) {
            $checkIn = Carbon::parse($this->check_in);

            $ahora = Carbon::now();
            // $ahora = Carbon::parse('2024-07-20 17:30:00');
            $diferenciaEnMinutos = $ahora->diffInMinutes($checkIn);

            $horasTotales = ceil($diferenciaEnMinutos / 55);

            return $horasTotales;
        }

        return 0; 
    }
    public function consumo()
    {
        return $this->hasOne(Consumo::class);
    }
    public function actualizarTotalHoras()
    {
        $this->total_horas = $this->calcularHorasTranscurridas();
        $this->save();
    }
    public function consumos()
    {
        return $this->hasMany(Consumo::class);
    }
}
