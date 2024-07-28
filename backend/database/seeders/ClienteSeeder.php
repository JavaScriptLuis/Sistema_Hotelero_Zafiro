<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Movilidad;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $clientes = [
                [
                    'nombre' => 'Juan',
                    'paterno' => 'Perez',
                    'materno' => 'Lopez',
                    'cedula' => '1234567890',
                    'telefono' => '1234567890',
                    'nit' => '1234567890',
                    'sucursal_id' => 1,
                    'movilidad' => 'ABC123',
                ],
                [
                    'nombre' => 'Rene',
                    'paterno' => 'Cejas',
                    'materno' => 'Cejas',
                    'cedula' => '3333',
                    'telefono' => '1234567890',
                    'nit' => '1234567890',
                    'sucursal_id' => 2,
                    'movilidad' => 'DEF456',
                ],
                [
                    'nombre' => 'Maria',
                    'paterno' => 'Gonzalez',
                    'materno' => 'Rodriguez',
                    'cedula' => '5555555',
                    'telefono' => '9876543210',
                    'nit' => '9876543210',
                    'sucursal_id' => 1,
                    'movilidad' => 'XYZ789',
                ],
            ];

            foreach ($clientes as $clienteData) {
                $movilidad = $clienteData['movilidad'];
                unset($clienteData['movilidad']);

                $cliente = Cliente::create($clienteData);

                Movilidad::create([
                    'cliente_id' => $cliente->id,
                    'placa_vehiculo' => $movilidad
                ]);
            }
        });
    }
}