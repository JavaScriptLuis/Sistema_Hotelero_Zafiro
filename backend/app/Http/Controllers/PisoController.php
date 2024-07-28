<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePisoRequest;
use App\Http\Requests\UpdatePisoRequest;
use App\Models\Piso;
use Illuminate\Http\Request;

class PisoController extends Controller
{

    public function index()
    {
        $data = Piso::with('relacionSucursal')->where(function ($query) {
            $search = request('search');
            $query->where('nombre', 'like', '%' . $search . '%');
        })->whereHas('relacionSucursal', function ($query) {
            $search = request('search');
            $query->where('nombre_sucursal', 'like', '%' . $search . '%');
        })->orderBy('id', 'desc')->get();
        return response()->json($data, 200);
    }

    public function store(StorePisoRequest $request)
    {
        Piso::create([
            'sucursal_id' => $request->sucursal_id,
            'nombre' => $request->nombre
        ]);
        return response()->json(['message' => 'Piso registrado'], 201);
    }
    public function update(UpdatePisoRequest $request, string $id)
    {
        $piso = Piso::find($id);
        $piso->update([
            'sucursal_id' => $request->sucursal_id,
            'nombre' => $request->nombre
        ]);

        return response()->json(['message' => 'Piso actualizado'], 200);
    }
    public function destroy(string $id)
    {
        $piso = Piso::find($id);
        $piso->estado = false;
        $piso->update();
        return response()->json(['message' => 'Piso eliminado'], 200);
    }
    public function activePiso(string $id)
    {
        $piso = Piso::find($id);
        $piso->estado = true;
        $piso->update();
        return response()->json(['message' => 'Piso activado'], 200);
    }
}
