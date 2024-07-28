<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonalRequest;
use App\Http\Requests\UpdatePersonalRequest;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cliente = Personal::with(['relacionSucursal', 'relacionUser'])->orderBy('id', 'desc')->get();

        return response()->json($cliente, 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonalRequest $request)
    {
        \DB::beginTransaction();

        try {
        $filename = $this->imageSetting(null,$request->foto);

            $usuario = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->cedula),
                'rol' => $request->rol,
            ]);

            Personal::create([
                'user_id' => $usuario->id,
                'sucursal_id' => $request->sucursal_id,
                'nombre' => $request->nombre,
                'paterno' => $request->paterno,
                'materno' => $request->materno,
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'foto' => $filename
            ]);

            \DB::commit();

            return response()->json(['message' => 'Personal registrado'], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['message' => 'Error al registrar el personal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonalRequest $request, string $id)
    {
        $personal = Personal::find($id);
        $user = User::find($personal->user_id);
        $user->update([
            'email' => $request->email,
            'password' => bcrypt($request->cedula),
        ]);
        $filename = $this->imageSetting($personal,$request->foto);
        $personal->update([
            'sucursal_id' => $request->sucursal_id,
            'nombre' => $request->nombre,
            'paterno' => $request->paterno,
            'materno' => $request->materno,
            'cedula' => $request->cedula,
            'telefono' => $request->telefono,
            'foto' => $filename
        ]);

        return response(['message' => 'Personal actualizado'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $personal = Personal::find($id);
        $personal->estado = false;
        $personal->update();
        return response(['message' => 'Personal eliminado'], 200);
    }

    public function activePersonal(string $id)
    {
        $personal = Personal::find($id);
        $personal->estado = true;
        $personal->update();
        return response(['message' => 'Personal activado'], 200);
    }
    private function imageSetting($personal = null, $image)
    {
        $subfolder = 'public/fotos';
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            if (!Storage::exists($subfolder)) {
                Storage::makeDirectory($subfolder);
            }
    
            if ($personal !== null && Storage::exists("$subfolder/{$personal->foto}")) {
                Storage::delete("$subfolder/{$personal->foto}");
            }
    
            $originalName = $image->getClientOriginalName();
            $filename = date('YmdHi') . $originalName;
            Storage::put("$subfolder/$filename", file_get_contents($image));
    
            return $filename;
        } else {

            return $personal ? $personal->foto : null;
        }
    }
}
