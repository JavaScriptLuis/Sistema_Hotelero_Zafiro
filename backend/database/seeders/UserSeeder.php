<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Personal;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \DB::beginTransaction();

        try {
            $usuario = User::create([
                'email' => '1@com',
                'password' => bcrypt('123'),
                'rol' => 'admin',
            ]);

            Personal::create([
                'user_id' => $usuario->id,
                'sucursal_id' => 1, 
                'nombre' => 'admin',
                'paterno' => 'admin',
                'materno' => 'admin',
                'cedula' => '1123',
                'telefono' => '123'
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            echo 'Error al registrar el personal: ' . $e->getMessage();
        }
    }
}
