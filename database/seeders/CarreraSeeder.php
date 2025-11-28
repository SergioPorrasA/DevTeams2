<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('carreras')->insert([
            [
                'nombre' => 'Ingeniería en Sistemas Computacionales',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Ingeniería en Informática',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Ingeniería en Software',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Ingeniería en Electrónica',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
