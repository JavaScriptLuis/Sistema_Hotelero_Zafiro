<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Habitacion;
use App\Models\Personal;
use App\Models\Piso;
use App\Models\Producto;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class DialogController extends Controller
{
    public function getClientesActivos()
    {
        $user = auth()->user();
        $user->relacionPersonal->id;
        $data = Cliente::where('estado', true)->where('sucursal_id', $user->relacionPersonal->sucursal_id)->orderByDesc('id')->get();
        return response()->json($data, 200);
    }

    public function getPisosActivos(){

        $user = auth()->user();
        $data = Piso::where('sucursal_id', $user->relacionPersonal->sucursal_id)->where('estado', true)->get();
        return response()->json($data, 200);
    }

    public function getHabitacionesActivas()
    {
        $user = auth()->user();
        $data = Habitacion::whereHas('relacionSucursal', function ($query) use ($user) {
            $query->where('sucursal_id', $user->relacionPersonal->sucursal_id);
        })
            ->where('estado', true)
            ->get()
            ->map(function ($habitacion) {
                $habitacion->nombre = $habitacion->relacionTipo->nombre;

                $habitacion->tipo = $habitacion->relacionTipo;

                unset($habitacion->relacionTipo);

                return $habitacion;
            });

        return response()->json($data, 200);
        
    }

    public function getSucursalesActivas(){
        $data = Sucursal::where('estado', true)->get();
        return response()->json($data, 200);
    }

    public function getProductosActivos(){
        $user = auth()->user();
        $data = Producto::where('sucursal_id', $user->relacionPersonal->sucursal_id)->where('estado', true)->get();
        return response()->json($data, 200);
    }

    public function getPersonalActivos(){
        $data = Personal::where('estado', true)->get();
        return response()->json($data, 200);
    }
}
