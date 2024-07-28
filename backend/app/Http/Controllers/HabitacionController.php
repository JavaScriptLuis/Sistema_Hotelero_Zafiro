<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHabitacionRequest;
use App\Http\Requests\UpdateHabitacionRequest;
use App\Models\Habitacion;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function recepcionHabitaciones()
    {
        $user = auth()->user();
        $search = request('search');

        $query = Habitacion::with(['relacionTipo'])
            ->whereHas('relacionSucursal', function ($query) use ($user) {
                $query->where('sucursal_id', $user->relacionPersonal->sucursal_id);
            })
            ->where('estado', true);

        if (!empty($search)) {
            $query->where('piso_id', $search);
        }

        $habitaciones = $query->get();
        $habitaciones = $habitaciones->map(function ($habitacion) {
            $estadoActual = $habitacion->estadoActual();
            $habitacion->estado_actual = $estadoActual;
            $servicio_actual = $habitacion->estadiaActual();

            $proximaReserva = $habitacion->proximaReserva();
            $habitacion->servicio_actual = $this->formatearReserva($servicio_actual);
            $habitacion->proxima_reserva = $this->formatearReserva($proximaReserva);

            return $habitacion;
        });

        return response()->json($habitaciones);
    }
    public function recepcionHabitacionesSalida()
    {
        $user = auth()->user();
        $search = request('search');

        $query = Habitacion::with(['relacionTipo'])
            ->whereHas('relacionSucursal', function ($query) use ($user) {
                $query->where('sucursal_id', $user->relacionPersonal->sucursal_id);
            })->where('check_status', 'ocupado')
            ->where('estado', true);
        if (!empty($search)) {
            $query->where('piso_id', $search);
        }
        ;
        $habitaciones = $query->get();
        $habitaciones = $habitaciones->map(function ($habitacion) {
            $estadoActual = $habitacion->estadoActual();
            $habitacion->estado_actual = $estadoActual;

            $servicio_actual = $habitacion->estadiaActual();
            if ($servicio_actual) {
                $consumo = $servicio_actual->consumo;
                if ($consumo) {
                    $consumo = $consumo->first();
                    $detalles_consumo = $consumo->detalles()->with('producto')->get();
                    $servicio_actual->detalles_consumo = $detalles_consumo;
                }
            }
            $habitacion->servicio_actual = $this->formatearSalida($servicio_actual);

            return $habitacion;
        });

        return response()->json($habitaciones);
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
        if (isset($reserva->detalles_consumo)) {
            $formateado['detalles_consumo'] = $reserva->detalles_consumo->map(function ($detalle) {
                return [
                    'id' => $detalle->id,
                    'producto' => $detalle->producto->nombre,
                    'cantidad' => $detalle->cantidad,
                    'precio' => $detalle->precio,
                    'estado' => $detalle->estado,
                    'metodo_pago' => $detalle->metodo_pago,
                ];
            });
        }
        return $formateado;
    }
    private function formatearSalida($reserva)
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
            'total_horas' => $reserva->total_horas,
            'estado' => $reserva->estado,
            'cliente' => $reserva->relacionCliente
        ];
        if ($reserva->consumo) {
            $formateado['consumo'] = [
                'id' => $reserva->consumo->id,
                'total' => $reserva->consumo->total,
                'created_at' => $reserva->consumo->created_at,
                'updated_at' => $reserva->consumo->updated_at,
                'detalles' => $reserva->consumo->detalles->map(function ($detalle) {
                    return [
                        'id' => $detalle->id,
                        'producto' => $detalle->producto->nombre,
                        'cantidad' => $detalle->cantidad,
                        'precio' => $detalle->precio,
                        'estado' => $detalle->estado,
                        'metodo_pago' => $detalle->metodo_pago,
                    ];
                })
            ];
        } else {
            $formateado['consumo'] = null;
        }
        return $formateado;
    }

    public function index()
    {
        $data = Habitacion::with(['relacionTipo', 'relacionSucursal', 'relacionPiso'])
            ->orderBy('id', 'desc')->get();

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHabitacionRequest $request)
    {
        $user = auth()->user();

        Habitacion::create([
            'sucursal_id' => $user->relacionPersonal->sucursal_id,
            'piso_id' => $request->piso_id,
            'tipo_id' => $request->tipo_id,
            'numero' => $request->numero,
            'descripcion' => $request->descripcion
        ]);
        return response(['message' => 'Habitacion registrada'], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHabitacionRequest $request, string $id)
    {
        $habitacion = Habitacion::find($id);
        $habitacion->update([
            'sucursal_id' => $request->sucursal_id,
            'piso_id' => $request->piso_id,
            'tipo_id' => $request->tipo_id,
            'numero' => $request->numero,
            'descripcion' => $request->descripcion
        ]);

        return response(['message' => 'Habitacion actualizada'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $habitacion = Habitacion::find($id);
        $habitacion->estado = false;
        $habitacion->update();
        return response(['message' => 'Habitacion eliminada'], 200);
    }
    public function activeHabitacion(string $id)
    {

        $habitacion = Habitacion::find($id);
        $habitacion->estado = true;
        $habitacion->update();
        return response(['message' => 'Habitacion activada'], 200);
    }

    public function terminarLimpieza(string $id)
    {
        $habitacion = Habitacion::find($id);
        $habitacion->update([
            'check_status' => 'desocupado',
        ]);
        return response(['message' => 'Habitacion activada'], 200);
    }
}