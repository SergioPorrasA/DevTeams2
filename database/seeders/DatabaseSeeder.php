<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar roles
        DB::table('rol')->insert([
            ['Id' => 1, 'Descripcion' => 'Administrador'],
            ['Id' => 2, 'Descripcion' => 'Juez'],
            ['Id' => 3, 'Descripcion' => 'Participante'],
        ]);

        // Insertar carreras
        DB::table('carrera')->insert([
            ['Nombre' => 'Ingeniería en Sistemas Computacionales'],
            ['Nombre' => 'Ingeniería en Tecnologías de la Información'],
            ['Nombre' => 'Ingeniería en Desarrollo de Software'],
            ['Nombre' => 'Ingeniería Informática'],
            ['Nombre' => 'Licenciatura en Administración'],
            ['Nombre' => 'Ingeniería Industrial'],
        ]);

        // Insertar permisos
        $permisoIds = [];
        $permisoIds[1] = DB::table('permiso')->insertGetId([
            'Nombre' => 'Crear equipos',
            'Descripcion' => 'Permite crear nuevos equipos'
        ]);
        $permisoIds[2] = DB::table('permiso')->insertGetId([
            'Nombre' => 'Editar equipos',
            'Descripcion' => 'Permite editar equipos existentes'
        ]);
        $permisoIds[3] = DB::table('permiso')->insertGetId([
            'Nombre' => 'Eliminar equipos',
            'Descripcion' => 'Permite eliminar equipos'
        ]);
        $permisoIds[4] = DB::table('permiso')->insertGetId([
            'Nombre' => 'Gestionar eventos',
            'Descripcion' => 'Permite gestionar eventos'
        ]);
        $permisoIds[5] = DB::table('permiso')->insertGetId([
            'Nombre' => 'Calificar proyectos',
            'Descripcion' => 'Permite calificar proyectos'
        ]);

        // Asignar permisos a roles
        // Administrador tiene todos los permisos
        foreach ($permisoIds as $permisoId) {
            DB::table('rol_permiso')->insert([
                'Id_rol' => 1,
                'Id_permiso' => $permisoId,
            ]);
        }

        // Juez puede calificar
        DB::table('rol_permiso')->insert([
            'Id_rol' => 2,
            'Id_permiso' => $permisoIds[5],
        ]);

        // Participante puede crear y editar equipos
        DB::table('rol_permiso')->insert([
            ['Id_rol' => 3, 'Id_permiso' => $permisoIds[1]],
            ['Id_rol' => 3, 'Id_permiso' => $permisoIds[2]],
        ]);

        // Insertar perfiles para equipos
        DB::table('perfil')->insert([
            ['Nombre' => 'Líder', 'Descripcion' => 'Líder del equipo'],
            ['Nombre' => 'Diseñador', 'Descripcion' => 'Diseñador del equipo'],
            ['Nombre' => 'Programador Backend', 'Descripcion' => 'Desarrollador backend'],
            ['Nombre' => 'Programador Frontend', 'Descripcion' => 'Desarrollador frontend'],
        ]);

        // Crear usuario administrador
        $adminUsuario = DB::table('usuario')->insertGetId([
            'Nombre' => 'admin',
            'Correo' => 'admin@devteams.com',
            'Contraseña' => Hash::make('admin123'),
            'Is_active' => true,
        ]);

        // Asignar rol de administrador
        DB::table('usuario_rol')->insert([
            'Id_usuario' => $adminUsuario,
            'Id_Rol' => 1, // Administrador
        ]);

        // Crear usuario juez
        $juezUsuario = DB::table('usuario')->insertGetId([
            'Nombre' => 'juez',
            'Correo' => 'juez@devteams.com',
            'Contraseña' => Hash::make('juez123'),
            'Is_active' => true,
        ]);

        // Asignar rol de juez
        DB::table('usuario_rol')->insert([
            'Id_usuario' => $juezUsuario,
            'Id_Rol' => 2, // Juez
        ]);

        // Insertar especialidad para el juez
        $especialidadId = DB::table('especialidad')->insertGetId([
            'Nombre' => 'Desarrollo Web',
            'Descripcion' => 'Experto en desarrollo de aplicaciones web',
        ]);

        // Crear registro de juez
        DB::table('juez')->insert([
            'Id_especialidad' => $especialidadId,
            'Nombre' => 'Juez Principal',
            'Correo' => 'juez@devteams.com',
            'telefono' => '6441234567',
        ]);

        // Crear usuario participante de prueba
        $participanteUsuario = DB::table('usuario')->insertGetId([
            'Nombre' => 'participante',
            'Correo' => 'participante@devteams.com',
            'Contraseña' => Hash::make('participante123'),
            'Is_active' => true,
        ]);

        // Asignar rol de participante
        DB::table('usuario_rol')->insert([
            'Id_usuario' => $participanteUsuario,
            'Id_Rol' => 3, // Participante
        ]);

        // Crear registro de participante
        DB::table('participante')->insert([
            'Usuario_id' => $participanteUsuario,
            'No_Control' => '20170001',
            'Carrera_id' => 1,
            'Nombre' => 'Juan Pérez',
            'Correo' => 'participante@devteams.com',
            'telefono' => '6449876543',
        ]);

        // Crear equipo de ejemplo
        $equipoId = DB::table('equipo')->insertGetId([
            'nombre' => 'Equipo Alpha',
        ]);

        // Asignar participante al equipo con perfil de líder
        DB::table('participante_equipo')->insert([
            'Id_participante' => 1, // ID del participante creado
            'Id_equipo' => $equipoId,
            'Id_perfil' => 1, // Líder
        ]);

        // Crear evento de ejemplo
        $eventoId = DB::table('evento')->insertGetId([
            'Nombre' => 'Hackathon 2024',
            'Descripcion' => 'Desarrolla aplicaciones innovadoras en 48 horas',
            'Id_juez' => 1, // ID del juez creado
            'Fecha_inicio' => '2024-12-14',
            'Fecha_fin' => '2024-12-16',
        ]);

        // Crear criterios de evaluación
        $criterios = [
            'Innovación y Creatividad',
            'Funcionalidad y Calidad Técnica',
            'Diseño y Experiencia de Usuario',
            'Presentación y Documentación',
        ];

        foreach ($criterios as $criterio) {
            DB::table('criterio')->insert([
                'descripcion' => $criterio,
            ]);
        }

        // Crear repositorio
        $repositorioId = DB::table('repositorio')->insertGetId([
            'Url' => 'https://github.com/ejemplo/proyecto',
        ]);

        // Crear proyecto de ejemplo
        DB::table('proyecto')->insert([
            'Id_equipo' => $equipoId,
            'Categoria' => 'Aplicación Web',
            'Id_evento' => $eventoId,
            'Id_repositorio' => $repositorioId,
            'nombre' => 'Sistema de Votación Blockchain',
        ]);
    }
}
