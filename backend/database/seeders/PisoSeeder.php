<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('pisos')->insert([
            [
                'sucursal_id' => 1,
                'nombre' => 'piso 1',
            ],
            [
                'sucursal_id' => 1,
                'nombre' => 'piso 2',
            ],
            [
                'sucursal_id' => 1,
                'nombre' => 'piso 3',
            ],
            [
                'sucursal_id' => 1,
                'nombre' => 'piso 4',
            ],
        ]);
    }
}
