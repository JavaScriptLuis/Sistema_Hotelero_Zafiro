<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Sucursal::orderBy('id', 'desc')->get();
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSucursalRequest $request)
    {
        Sucursal::create([
            'nombre_sucursal' => $request->nombre_sucursal,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono
        ]);

        return response()->json(['message' => 'Sucursal registrada'], 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSucursalRequest $request, string $id)
    {
        $sucursal = Sucursal::find($id);
        $sucursal->update([
            'nombre_sucursal' => $request->nombre_sucursal,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono
        ]);

        return response()->json(['message' => 'Sucursal actualizada'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sucursal = Sucursal::find($id);
        $sucursal->estado = false;
        $sucursal->update();
        return response()->json(['message' => 'Sucursal eliminada'], 200);
    }
    public function activeSucursal(string $id)
    {
        $sucursal = Sucursal::find($id);
        $sucursal->estado = true;
        $sucursal->update();
        return response()->json(['message' => 'Sucursal activada'], 200);
    }
}
