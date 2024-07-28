<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HabitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('habitacions')->insert([
            [   
                'tipo_id' => 1,
                'sucursal_id' => 1,
                'piso_id' => 1,
                'numero' => 1,
                'descripcion' => 'desc',
                
            ],
            [   
                'tipo_id' => 2,
                'sucursal_id' => 1,
                'piso_id' => 1,
                'numero' => 2,
                'descripcion' => 'desc',
            ],
            [
                'tipo_id' => 2,
                'sucursal_id' => 2,
                'piso_id' => 1,
                'numero' => 1,
                'descripcion' => 'desc',
            ],
            [
                'tipo_id' => 1,
                'sucursal_id' => 1,
                'piso_id' => 1,
                'numero' => 3,
                'descripcion' => 'desc',
            ],
            [
                'tipo_id' => 1,
                'sucursal_id' => 1,
                'piso_id' => 1,
                'numero' => 4,
                'descripcion' => 'desc',
            ],
            [
                'tipo_id' => 1,
                'sucursal_id' => 1,
                'piso_id' => 1,
                'numero' => 5,
                'descripcion' => 'desc',
            ],
        ]);
    }
}
