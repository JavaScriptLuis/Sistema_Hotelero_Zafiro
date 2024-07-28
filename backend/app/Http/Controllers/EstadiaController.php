<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroRequest;
use App\Models\Habitacion;
use App\Models\Registro;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\CheckSalidaRequest;

class EstadiaController extends Controller
{


    public function index()
    {
        $user = auth()->user();
        $personalId = $user->relacionPersonal->id;
        $estadias = Registro::where('personal_id', $personalId)->orderByDesc('id')->get();
        return response()->json($estadias, 200);
    }


    public function store(StoreRegistroRequest $request, Registro $registro)
{
    $now = Carbon::now();

    // Verificar si el cliente ya tiene una estadía activa
    $estadiaActiva = Registro::where('cliente_id', $request->cliente_id)
        ->where('estado', '!=', 'terminada')
        ->exists();

    if ($estadiaActiva) {
        return response()->json([
            'message' => 'El cliente ya tiene una estadía activa.'
        ], 400);
    }

    if ($request->check_tipo === 'reservada') {
        $reserva = Reserva::findOrFail($request->reserva_id);
        $reserva->update([
            'estado' => 'completada',
        ]);
    }

    $user = auth()->user();
    $reserva = Registro::create([
        'cliente_id' => $request->cliente_id,
        'habitacion_id' => $request->habitacion_id,
        'personal_id' => $user->relacionPersonal->id,
        'fecha_inicio' => $request->fecha_inicio,
        'fecha_fin' => $request->fecha_fin,
        'hora_inicio' => $request->hora_inicio,
        'hora_fin' => $request->hora_fin,
        'observacion' => $request->observacion,
        'adelanto' => $request->adelanto,
        'check_in' => $now->toDateTimeString(),
    ]);
    $habitacion = Habitacion::find($request->habitacion_id);

    $habitacion->update([
        'check_status' => 'ocupado',
        'check_tipo' => $request->check_tipo,
    ]);

    return response(['message' => 'Reserva registrada', 'habitacion' => $habitacion], 201);
}


    public function update(Request $request, $id)
    {
        $estadia = Registro::findOrFail($id);
        $estadia->update($request->all());  
        return response(['message' => 'Reserva actualizada', 'estadia' => $estadia], 200);
    }

    public function check_salida(CheckSalidaRequest $request, $id)
    {
        $now = Carbon::now();

        $estadia = Registro::findOrFail($id);
        $habitacion = Habitacion::findOrFail($estadia->habitacion_id);

        \DB::beginTransaction();

        try {
            $habitacion->update([
                'check_status' => 'limpieza',
            ]);

            $estadia->update([
                'estado' => 'terminada',
                'total_pago' => $request->total_pago,
                'check_out' => $now->toDateTimeString(),
            ]);

            \DB::commit();

            return response()->json([
                'message' => 'Reserva terminada',
                'habitacion' => $habitacion
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Error al procesar el check-out',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function agregar_horas(Request $request, $id){

        $horas_aumentar = $request->hora;
        
        $data = Registro::findOrFail($id);
        
        if ($data->hora_fin) {
            $hora_fin = Carbon::parse($data->hora_fin);
            $nueva_hora = ($hora_fin->hour + $horas_aumentar) % 24;
            $data->hora_fin = $hora_fin->setHour($nueva_hora)->format('H:i');
        } else {
            $data->hora_fin = Carbon::now()->addHours($horas_aumentar)->format('H:i');
        }
        
        $data->save();
        return response()->json($data, 200);
    }
}
