<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{

    public function index()
    {

        $data = Producto::with('relacionSucursal')->where('estado', true)->get();
        return response()->json($data, 200);
    }

    public function store(StoreProductoRequest $request)
    {

        $data = Producto::create([
            'sucursal_id' => $request->sucursal_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio_venta' => $request->precio_venta,
            'precio_compra' => $request->precio_compra,
        ]);
        return response()->json($data, 200);
    }
    public function update(UpdateProductoRequest $request, $id)
    {
        $data = Producto::find($id);
        $data->update([
            'sucursal_id' => $request->sucursal_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio_venta' => $request->precio_venta,
            'precio_compra' => $request->precio_compra,
        ]);
        return response()->json($data, 200);
    }

    public function destroy($id)
    {
        $data = Producto::find($id);
        $data->update([
            'estado' => false,
        ]);
        return response()->json($data, 200);
    }
    public function activeProducto($id)
    {
        $data = Producto::find($id);
        $data->update([
            'estado' => true,
        ]);
        return response()->json($data, 200);
    }
}
