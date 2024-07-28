<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsumoRequest;
use App\Models\Consumo;
use App\Models\DetalleConsumo;
use App\Models\Registro;
use Illuminate\Http\Request;

class ConsumoController extends Controller
{
    public function store(StoreConsumoRequest $request)
    {
        \DB::beginTransaction();
        try {
            $consumo = Consumo::where('registro_id', $request->registro_id)->first();

            if (!$consumo) {
                $consumo = Consumo::create([
                    'registro_id' => $request->registro_id,
                    'total' => $request->total,
                ]);
            } else {
                $consumo->total += $request->total;
                $consumo->save();
            }

            $productos = $request->productos;

            foreach ($productos as $value) {
                $consumo->detalles()->create([
                    'producto_id' => $value['producto_id'],
                    'cantidad' => $value['cantidad'],
                    'precio' => $value['precio_venta'],
                    'estado' => $request->estado,
                    'metodo_pago' => $request->metodo_pago,
                ]);
            }

            \DB::commit();

            return response()->json(['message' => 'Consumo registrado correctamente'], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['message' => 'Error al registrar el consumo: ' . $e->getMessage()], 500);
        }
    }

    public function pagarDetalle(Request $request, $id){
        $consumo = DetalleConsumo::find($id);
        $consumo->metodo_pago = $request->metodo_pago;
        $consumo->estado = 'pagado';
        $consumo->update();
        return response()->json(['message' => 'Metodo de pago actualizado correctamente'], 201);
    }
    
}