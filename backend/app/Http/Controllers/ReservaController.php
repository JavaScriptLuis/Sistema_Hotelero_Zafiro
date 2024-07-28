<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Registro;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $personalId = $user->relacionPersonal->id;

        $data = Reserva::with(['relacionCliente', 'relacionHabitacion'])
            ->where('personal_id', $personalId)
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id,
                    'cliente_id' => $reserva->cliente_id,
                    'habitacion_id' => $reserva->habitacion_id,
                    'personal_id' => $reserva->personal_id,
                    'fecha_inicio' => $reserva->fecha_inicio,
                    'fecha_fin' => $reserva->fecha_fin,
                    'hora_inicio' => $reserva->hora_inicio,
                    'hora_fin' => $reserva->hora_fin,
                    'observacion' => $reserva->observacion,
                    'adelanto' => $reserva->adelanto,
                    'cliente_identidad' => $reserva->relacionCliente->cedula ?? null,
                    'habitacion_numero' => $reserva->relacionHabitacion->numero ?? null,
                ];
            });

        return response()->json($data, 200);
    }

    public function verificarReserva()
    {
        $now = Carbon::now();
        \Log::info("Iniciando verificación de reservas a las " . $now->toDateTimeString());

        $estadiasParaExtender = Registro::where('estado', 'activa')
            ->where(function ($query) use ($now) {
                $query->where(function ($q) use ($now) {
                    $q->whereDate('fecha_fin', '<', $now->toDateString())
                        ->orWhere(function ($subQ) use ($now) {
                            $subQ->whereDate('fecha_fin', '=', $now->toDateString())
                                ->whereTime('hora_fin', '<=', $now->toTimeString());
                        });
                });
            })
            ->whereHas('habitacion', function ($query) {
                $query->where('check_status', 'ocupado');
            })
            ->with('habitacion')
            ->get();

        $estadiasExtendidas = 0;
        foreach ($estadiasParaExtender as $estadia) {
            $fechaHoraFin = Carbon::createFromFormat(
                'Y-m-d H:i',
                $estadia->fecha_fin . ' ' . $estadia->hora_fin
            );


            if ($now->greaterThanOrEqualTo($fechaHoraFin) && !$estadia->hora_extendido) {
                $estadia->hora_extendido = true;
                $estadia->save();
                $estadiasExtendidas++;
            }
        }

        return response()->json([
            'status' => 'ok',
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservaRequest $request, Reserva $reserva)
    {
        $user = auth()->user();
        $reserva = Reserva::create([
            'cliente_id' => $request->cliente_id,
            'habitacion_id' => $request->habitacion_id,
            'personal_id' => $user->relacionPersonal->id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'observacion' => $request->observacion,
            'adelanto' => $request->adelanto,
            'estado' => $request->adelanto > 0 ? 'confirmada' : 'pendiente'
        ]);

        if ($request->adelanto > 0) {
            $reserva->confirmar();
        }

        return response(['message' => 'Reserva registrada'], 201);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservaRequest $request, string $id)
    {
        $reserva = Reserva::find($id);
        $reserva->update([
            'cliente_id' => $request->cliente_id,
            'habitacion_id' => $request->habitacion_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'observacion' => $request->observacion,
            'adelanto' => $request->adelanto
        ]);

        return response(['message' => 'Reserva actualizada'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reserva = Reserva::find($id);
        $reserva->delete();
        return response(['message' => 'Reserva eliminada'], 200);
    }

    
}
