<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReporteRequest;
use App\Models\Habitacion;
use App\Models\Reserva;
use Illuminate\Http\Request;
use App\Models\Registro;
use App\Models\Consumo;
use App\Models\DetalleConsumo;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function getAtencionPersonal(StoreReporteRequest $request)
    {

        $fechaInicio = $request->fecha_inicio . ' 00:00:00';
        $fechaFin = $request->fecha_fin . ' 23:59:59';

        $registros = Registro::where('personal_id', $request->personal_id)
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('check_in', [$fechaInicio, $fechaFin])
                    ->orWhereBetween('check_out', [$fechaInicio, $fechaFin])
                    ->orWhere(function ($q) use ($fechaInicio, $fechaFin) {
                        $q->where('check_in', '<=', $fechaInicio)
                            ->where('check_out', '>=', $fechaFin);
                    });
            })
            ->with(['relacionCliente', 'habitacion', 'consumos.detalles.producto'])
            ->get();


        return response()->json([
            'registros' => $registros->map(function ($registro) {
                return [
                    'id' => $registro->id,
                    'cliente' => $registro->relacionCliente ? $registro->relacionCliente : 'Sin cliente',
                    'habitacion' => $registro->habitacion ? $registro->habitacion->numero : 'Sin habitación',
                    'check_in' => $registro->check_in,
                    'check_out' => $registro->check_out,
                    'total_pago' => $registro->total_pago,
                    'consumos' => $registro->consumos ? $registro->consumos->map(function ($consumo) {
                        return [
                            'id' => $consumo->id,
                            'total' => $consumo->total,
                            'detalles' => $consumo->detalles ? $consumo->detalles->map(function ($detalle) {
                                return [
                                    'producto' => $detalle->producto ? $detalle->producto->nombre : 'Producto no disponible',
                                    'cantidad' => $detalle->cantidad,
                                    'precio' => $detalle->precio,
                                    'estado' => $detalle->estado,
                                    'metodo_pago' => $detalle->metodo_pago,
                                ];
                            }) : [],
                        ];
                    }) : [],
                ];
            }),
        ]);
    }
    public function getEstadisticas()
    {
        $user = auth()->user();


        $actives = Habitacion::where('sucursal_id', $user->relacionPersonal->sucursal_id)->where('estado', true)->where('check_status', 'desocupado')->count();
        $inactives = Habitacion::with('relacionTipo')->where('sucursal_id', $user->relacionPersonal->sucursal_id)->where('estado', true)->where('check_status', 'ocupado')->get();
        $countInactives = $inactives->count();
        $limpiezas = Habitacion::where('sucursal_id', $user->relacionPersonal->sucursal_id)->where('estado', true)->where('check_status', 'limpieza')->count();

        $hoy = Carbon::today();

        $reservas = Reserva::where('personal_id', $user->relacionPersonal->id)->where('estado', 'confirmada')
            ->where(function ($query) use ($hoy) {
                $query->where(function ($q) use ($hoy) {
                    $q->where('fecha_inicio', '<=', $hoy->format('Y-m-d'))
                        ->where('fecha_fin', '>=', $hoy->format('Y-m-d'));
                });
            })
            ->count();

        $inactives = $inactives->map(function ($habitacion) {
            $estadoActual = $habitacion->estadoActual();
            $habitacion->estado_actual = $estadoActual;
            $servicio_actual = $habitacion->estadiaActual();
            $habitacion->servicio_actual = $this->formatearReserva($servicio_actual);

            return $habitacion;
        });
        $data = [
            'actives' => $actives,
            'inactives' => $inactives,
            'countInactives' => $countInactives,
            'limpiezas' => $limpiezas,
            'reservas' => $reservas,
        ];
        return response()->json($data, 200);

    }
    private function formatearReserva($reserva)
    {
        if (!$reserva) {
            return null;
        }
        $formateado = [
            'id' => $reserva->id,
            'fecha_inicio' => $reserva->fecha_inicio,
            'hora_inicio' => $reserva->hora_inicio,
            'fecha_fin' => $reserva->fecha_fin,
            'hora_fin' => $reserva->hora_fin,
            'observacion' => $reserva->observacion,
            'adelanto' => $reserva->adelanto,
            'estado' => $reserva->estado,
            'cliente' => $reserva->relacionCliente
        ];
        return $formateado;
    }
    public function totalYear()
    {
        $añoActual = Carbon::now()->year;

        $totalesRegistros = Registro::select(
            \DB::raw('MONTH(fecha_inicio) as mes'),
            \DB::raw('SUM(total_pago) as total_pagos')
        )
            ->whereYear('fecha_inicio', $añoActual)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->pluck('total_pagos', 'mes')
            ->toArray();

        $totalesConsumos = Consumo::select(
            \DB::raw('MONTH(created_at) as mes'),
            \DB::raw('SUM(total) as total_consumos')
        )
            ->whereYear('created_at', $añoActual)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->pluck('total_consumos', 'mes')
            ->toArray();

        $totalesCombinados = [];
        $mesActual = Carbon::now()->month;
        for ($mes = 1; $mes <= $mesActual; $mes++) {
            $totalRegistros = $totalesRegistros[$mes] ?? 0;
            $totalConsumos = $totalesConsumos[$mes] ?? 0;
            $totalesCombinados[$mes] = $totalRegistros + $totalConsumos;
        }

        return response()->json($totalesCombinados);
    }
}
