<?php

namespace App\Rules;

use App\Models\Registro;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Reserva;

class UniqueReservation implements ValidationRule
{
    protected $habitacion_id;

    public function __construct($habitacion_id)
    {
        $this->habitacion_id = $habitacion_id;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fecha_inicio = request()->input('fecha_inicio');
        $fecha_fin = request()->input('fecha_fin');
        $hora_inicio = request()->input('hora_inicio');
        $hora_fin = request()->input('hora_fin');
        if (!$fecha_inicio || !$fecha_fin || !$hora_inicio || !$hora_fin) {
            return;
        }
        $query = Reserva::where('habitacion_id', $this->habitacion_id)->where('estado', 'confirmada');
        $query1 = Registro::where('habitacion_id', $this->habitacion_id)->where('estado', 'activa');
        if ($fecha_inicio === $fecha_fin) {
            $query->where('fecha_inicio', $fecha_inicio)
                ->where(function ($q) use ($hora_inicio, $hora_fin) {
                    $q->where(function ($q) use ($hora_inicio, $hora_fin) {
                        $q->where('hora_inicio', '<', $hora_fin)
                            ->where('hora_fin', '>', $hora_inicio);
                    });
                });
            $query1->where('fecha_inicio', $fecha_inicio)
                ->where(function ($q) use ($hora_inicio, $hora_fin) {
                    $q->where(function ($q) use ($hora_inicio, $hora_fin) {
                        $q->where('hora_inicio', '<', $hora_fin)
                            ->where('hora_fin', '>', $hora_inicio);
                    });
                });
        } else {
            $query->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin])
                    ->orWhereBetween('fecha_fin', [$fecha_inicio, $fecha_fin])
                    ->orWhere(function ($q) use ($fecha_inicio, $fecha_fin) {
                        $q->where('fecha_inicio', '<=', $fecha_inicio)
                            ->where('fecha_fin', '>=', $fecha_fin);
                    });
            });
            $query1->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                $q->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin])
                    ->orWhereBetween('fecha_fin', [$fecha_inicio, $fecha_fin])
                    ->orWhere(function ($q) use ($fecha_inicio, $fecha_fin) {
                        $q->where('fecha_inicio', '<=', $fecha_inicio)
                            ->where('fecha_fin', '>=', $fecha_fin);
                    });
            });
        }
        if ($query->exists()) {
            if ($fecha_inicio === $fecha_fin) {
                $fail('La habitación ya está reservada en ese horario.');
            } else {
                $fail('La habitación ya está reservada en ese rango de fechas.');
            }
        }
        if ($query1->exists()) {
            if ($fecha_inicio === $fecha_fin) {
                $fail('La habitación ya está reservada en ese horario.');
            } else {
                $fail('La habitación ya está reservada en ese rango de fechas.');
            }
        }
    }
}