<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Participante',
                'descripcion' => 'Usuario participante en eventos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Juez',
                'descripcion' => 'Califica proyectos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Asesor',
                'descripcion' => 'Asesora equipos',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
