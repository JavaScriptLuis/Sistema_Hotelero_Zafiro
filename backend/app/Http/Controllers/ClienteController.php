<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Cliente;
use App\Models\Movilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Cliente::with('relacionSucursal', 'relacionMovilidad')->whereHas('relacionSucursal', function ($query) use ($user) {
            $query->where('sucursal_id', $user->relacionPersonal->sucursal_id);
        })->get();

        return response()->json($query, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();

            $cliente = Cliente::create([
                'sucursal_id' => $user->relacionPersonal->sucursal_id,
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno,
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'nit' => $request->nit
            ]);

            if ($request->has('placa_vehiculo')) {
                Movilidad::create([
                    'cliente_id' => $cliente->id,
                    'placa_vehiculo' => $request->placa_vehiculo
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cliente y movilidad creados exitosamente',
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Error al crear el cliente y la movilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $cliente = Cliente::findOrFail($id);

            $cliente->update([
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno,
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'nit' => $request->nit
            ]);

            if ($request->has('placa_vehiculo')) {
                $movilidad = Movilidad::updateOrCreate(
                    ['cliente_id' => $cliente->id],
                    ['placa_vehiculo' => $request->placa_vehiculo]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Cliente actualizado correctamente',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Cliente::find($id);
        $client->estado = false;
        $client->update();

        return response(['message' => 'Cliente eliminado'], 200);
    }
    public function activeClient(string $id)
    {
        $client = Cliente::find($id);
        $client->estado = true;
        $client->update();
        return response(['message' => 'Cliente activado'], 200);
    }
}
