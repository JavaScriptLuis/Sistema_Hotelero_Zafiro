<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoHabitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('tipo_habitacions')->insert([
            [
                'nombre' => 'simple',
                'precio_fijo' => 200,
                'precio_decremento' => 100,
                'descripcion' => 'desc',
            ],
            [
                'nombre' => 'conford',
                'precio_fijo' => 300,
                'precio_decremento' => 200,
                'descripcion' => 'desc',
            ],
        ]);
    }
}
