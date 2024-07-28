<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            SucursalSeeder::class,
            PisoSeeder::class,
            ProductoSeeder::class,
            ClienteSeeder::class,
            TipoHabitacionSeeder::class,
            HabitacionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
