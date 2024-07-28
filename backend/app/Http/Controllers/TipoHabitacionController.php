<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTipoHabitacionRequest;
use App\Models\TipoHabitacion;
use Illuminate\Http\Request;

class TipoHabitacionController extends Controller
{
    public function index()
    {
        $data = TipoHabitacion::all();
        return response()->json($data, 200);
    }

    public function store(StoreTipoHabitacionRequest $request)
    {

        $data = TipoHabitacion::create($request->all());
        return response()->json($data, 200);
    }
    public function update(Request $request, $id)
    {
        $data = TipoHabitacion::find($id);
        $data->update($request->all());
        return response()->json($data, 200);
    }
    public function destroy($id)
    {
        $data = TipoHabitacion::find($id);
        $data->estado = false;
        $data->update();
        return response()->json($data, 200);
    }
    public function activeTipo(string $id)
    {
        $piso = TipoHabitacion::find($id);
        $piso->estado = true;
        $piso->update();
        return response()->json(['message' => 'Piso activado'], 200);
    }
}
