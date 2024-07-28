<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('sucursals')->insert([
            [
                'nombre_sucursal' => 'sucursal 1',
                'direccion' => 'av. ',
                'telefono' => '123',
            ],
            [
                'nombre_sucursal' => 'sucursal 2',
                'direccion' => 'av. ',
                'telefono' => '123',
            ],
        ]);
    }
}
