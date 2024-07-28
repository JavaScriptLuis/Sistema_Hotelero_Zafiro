<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('productos')->insert([
            [   
                'sucursal_id' => 1,
                'nombre' => 'producto 1',
                'descripcion' => 'ninguna',
                'precio_venta' => 25,
                'precio_compra' => 20,
            ],
            [   
                'sucursal_id' => 1,
                'nombre' => 'producto 2',
                'descripcion' => 'ninguna',
                'precio_venta' => 10,
                'precio_compra' => 5,
            ],
            [   
                'sucursal_id' => 1,
                'nombre' => 'producto 3',
                'descripcion' => 'ninguna',
                'precio_venta' => 15,
                'precio_compra' => 10,
            ],
            [   
                'sucursal_id' => 1,
                'nombre' => 'producto 4',
                'descripcion' => 'ninguna',
                'precio_venta' => 50,
                'precio_compra' => 40,
            ],
            
        ]);
    }
}
