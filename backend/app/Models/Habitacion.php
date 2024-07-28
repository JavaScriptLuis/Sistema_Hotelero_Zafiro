<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Habitacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'tipo_id',
        'piso_id',
        'tipo_id',
        'numero',
        'descripcion',
        'estado_servicio',
        'estado',
        'check_tipo',
        'check_status',
        'hora_extendido',
    ];

    public function relacionSucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function relacionTipo()
    {
        return $this->belongsTo(TipoHabitacion::class, 'tipo_id');
    }
    public function relacionPiso(){
        return $this->belongsTo(Piso::class, 'piso_id');
    }
    public function reservar()
    {
        $this->update(['estado_servicio' => 'reservada']);
        $this->update(['check_tipo' => 'reservada']);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
    public function estadias()
    {
        return $this->hasMany(Registro::class);
    }
    public function estadiaActual()
    {
        $now = Carbon::now();

        return $this->estadias()
            ->where(function ($query) use ($now) {
                $query->where('estado','activa')->where('fecha_inicio', '<=', $now->format('Y-m-d'))
                    ->where('fecha_fin', '>=', $now->format('Y-m-d'))
                    ->where(function ($q) use ($now) {
                        $q->where('hora_inicio', '<=', $now->format('H:i'))
                            ->where(function ($subQ) use ($now) {
                                $subQ->where('hora_fin', '>', $now->format('H:i'))
                                    ->orWhere(function ($subSubQ) use ($now) {
                                        $subSubQ
                                            ->where('hora_extendido', true)
                                            ->where('estado', 'activa');
                                    });
                            });
                    });
            })
            ->orderBy('fecha_inicio')
            ->orderBy('hora_inicio')
            ->first();
    }
    public function extenderReserva(Reserva $reserva)
    {
        $now = Carbon::now();
        $horaExtendido = $now->copy()->addMinute()->format('H:i');

        $reserva->update([
            'hora_extendido' => $horaExtendido
        ]);
    }

    public function proximaReserva()
    {
        $now = Carbon::now();
        $tenMinutesFromNow = $now->copy()->addMinutes(10);
        $fiveMinutesAgo = $now->copy()->subMinutes(5);

        $proximaReserva = $this->reservas()
            ->where('estado', 'confirmada')
            ->where(function ($query) use ($fiveMinutesAgo, $tenMinutesFromNow) {
                $query->where(function ($q) use ($fiveMinutesAgo, $tenMinutesFromNow) {
                    $q->where('fecha_inicio', '=', $fiveMinutesAgo->format('Y-m-d'))
                        ->where('hora_inicio', '>=', $fiveMinutesAgo->format('H:i'))
                        ->where('hora_inicio', '<=', $tenMinutesFromNow->format('H:i'));
                })->orWhere(function ($q) use ($fiveMinutesAgo) {
                    $q->where('fecha_inicio', '>', $fiveMinutesAgo->format('Y-m-d'));
                });
            })
            ->orderBy('fecha_inicio')
            ->orderBy('hora_inicio')
            ->first();

        return $proximaReserva;
    }


    private function determinarEstadoReserva($reserva, $now)
    {
        $inicioReserva = Carbon::parse($reserva->fecha_inicio . ' ' . $reserva->hora_inicio);
        $finReserva = Carbon::parse($reserva->fecha_fin . ' ' . $reserva->hora_fin);
        $inicioPreparacion = $inicioReserva->copy()->subMinutes(10);
        $finTolerancia = $finReserva->copy()->addMinutes(10);
        $finRebasado = $finTolerancia->copy()->addMinutes(10);

        if ($this->check_status === 'ocupado') {
            if ($now <= $finReserva) {
                return 'ocupado';
            } elseif ($now <= $finTolerancia) {
                return 'ocupado tolerado';
            } elseif ($now <= $finRebasado) {
                return 'ocupado rebasado';
            }
        } else {
            if ($now >= $inicioPreparacion && $now < $inicioReserva) {
                return 'preparado para reserva';
            } elseif ($now >= $inicioReserva && $now <= $finReserva) {
                if ($this->check_tipo !== 'reserva') {
                    return 'reserva con retraso';
                }
                return 'en espera';
            } elseif ($now > $finReserva && $now <= $finTolerancia) {
                return 'reserva vencida';
            }
        }

        return 'finalizado';
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }

    public function estadoActual()
    {
        $now = Carbon::now();
        $estadiaActual = $this->estadiaActual();
        $proximaReserva = $this->proximaReserva();

        if ($proximaReserva) {
            $inicioReserva = Carbon::parse($proximaReserva->fecha_inicio . ' ' . $proximaReserva->hora_inicio);
            $finReserva = Carbon::parse($proximaReserva->fecha_fin . ' ' . $proximaReserva->hora_fin);
            $finTolerancia = $finReserva->copy()->addMinutes(3);

            if ($now < $inicioReserva) {
                return 'preparado para reserva';
            } elseif ($now >= $inicioReserva && $now <= $finReserva) {
                if ($this->check_tipo !== 'reserva' || $this->check_status !== 'ocupado') {
                    return 'reserva con retraso';
                }
            } elseif ($now > $finReserva && $now <= $finTolerancia) {
                return 'reserva vencida';
            }
        }

        if (empty($estadiaActual)) {
            if ($this->check_status === 'limpieza') {
                return 'limpieza';
            }
            if ($this->check_status === 'disponible') {
                return 'disponible';
            }
        }

        if ($estadiaActual) {
            $finReserva = Carbon::parse($estadiaActual->fecha_fin . ' ' . $estadiaActual->hora_fin);
            $finTolerancia = $finReserva->copy()->addMinutes(3);

            switch ($this->check_status) {
                case 'ocupado':
                    if ($now->lessThanOrEqualTo($finReserva)) {
                        return 'ocupado';
                    } elseif ($now->lessThanOrEqualTo($finTolerancia)) {
                        return 'tiempo desalojo';
                    } else {
                        return 'Sobrepasado el Tiempo';
                    }

                case 'desocupado':
                    if ($this->check_tipo === 'momento') {
                        return 'disponible';
                    } elseif ($this->check_tipo === 'reserva') {
                        if ($now->lessThanOrEqualTo($finReserva)) {
                            return 'reserva activa';
                        } elseif ($now->lessThanOrEqualTo($finTolerancia)) {
                            return 'reserva tolerada';
                        } else {
                            return 'reserva rebasada';
                        }
                    }
                    break;
            }
        }

        return 'desocupado';
    }



}
