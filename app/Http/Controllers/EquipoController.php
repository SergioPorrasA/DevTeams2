<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Participante;
use App\Models\Perfil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        //equipos del usuario
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        if (!$participante) {
            return view('dashboard', ['equipos' => collect()]);
        }
        
        
        $equipos = Equipo::whereIn('Id', function($query) use ($participante) {
            $query->select('Id_equipo')
                  ->from('participante_equipo')
                  ->where('Id_participante', $participante->Id);
        })->with(['participantes.usuario'])->get();
        
        Log::info('Equipos cargados para participante', [
            'participante_id' => $participante->Id,
            'cantidad_equipos' => $equipos->count()
        ]);
        
        return view('dashboard', compact('equipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            $participante = Participante::firstOrCreate(
                ['Usuario_id' => $user->Id],
                [
                    'Nombre' => $user->Nombre,
                    'Correo' => $user->Correo,
                    'No_Control' => null,
                    'Carrera_id' => null,
                    'telefono' => null
                ]
            );

            $equipo = new Equipo();
            $equipo->nombre = $request->nombre;
            $equipo->save();

            //el participante sera lider del equipo que cree
            DB::table('participante_equipo')->insert([
                'Id_participante' => $participante->Id,
                'Id_equipo' => $equipo->Id,
                'Id_perfil' => 1  
            ]);

            DB::commit();

            Log::info('Equipo creado exitosamente', [
                'equipo_id' => $equipo->Id,
                'equipo_nombre' => $equipo->nombre,
                'participante_id' => $participante->Id
            ]);

            return redirect()->route('dashboard')->with('success', '¡Equipo "' . $equipo->nombre . '" creado exitosamente!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear equipo: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')->with('error', 'Error al crear el equipo: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $equipo = Equipo::with(['participantes.usuario', 'proyectos'])->findOrFail($id);
        
        $user = Auth::user();
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        if (!$participante) {
            return redirect()->route('dashboard')->with('error', 'No tienes acceso a este equipo');
        }
        
        $perteneceAlEquipo = DB::table('participante_equipo')
            ->where('Id_participante', $participante->Id)
            ->where('Id_equipo', $id)
            ->exists();
        
        if (!$perteneceAlEquipo) {
            return redirect()->route('dashboard')->with('error', 'No tienes acceso a este equipo');
        }
        
        return view('equipos.show', compact('equipo'));
    }

    public function leave($id)
    {
        try {
            $user = Auth::user();
            $participante = Participante::where('Usuario_id', $user->Id)->first();
            
            if (!$participante) {
                return redirect()->route('dashboard')->with('error', 'No se encontró el participante');
            }

            
            $cantidadMiembros = DB::table('participante_equipo')
                ->where('Id_equipo', $id)
                ->count();

            
            DB::table('participante_equipo')
                ->where('Id_participante', $participante->Id)
                ->where('Id_equipo', $id)
                ->delete();

            if ($cantidadMiembros <= 1) {
                $equipo = Equipo::find($id);
                if ($equipo) {
                    $equipo->delete();
                }
                return redirect()->route('dashboard')->with('success', 'Has salido del equipo y este ha sido eliminado');
            }

            return redirect()->route('dashboard')->with('success', 'Has salido del equipo exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al salir del equipo: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error al salir del equipo: ' . $e->getMessage());
        }
    }

    //invitar al equipo
    public function invite(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
            'rol' => 'required|string',
        ]);

        try {
            $usuarioInvitado = \App\Models\Usuario::where('Correo', $request->email)->first();
            
            if (!$usuarioInvitado) {
                return redirect()->back()->with('error', 'No se encontró un usuario con ese correo');
            }

            $participanteInvitado = Participante::firstOrCreate(
                ['Usuario_id' => $usuarioInvitado->Id],
                [
                    'Nombre' => $usuarioInvitado->Nombre,
                    'Correo' => $usuarioInvitado->Correo,
                    'No_Control' => null,
                    'Carrera_id' => null,
                    'telefono' => null
                ]
            );

            $yaEsMiembro = DB::table('participante_equipo')
                ->where('Id_participante', $participanteInvitado->Id)
                ->where('Id_equipo', $id)
                ->exists();

            if ($yaEsMiembro) {
                return redirect()->back()->with('error', 'Este usuario ya es miembro del equipo');
            }

            $rolesMap = [
                'lider' => 1,
                'disenador' => 4,
                'backend' => 7,
                'frontend' => 10
            ];

            $perfilId = $rolesMap[$request->rol] ?? 1; 

            DB::table('participante_equipo')->insert([
                'Id_participante' => $participanteInvitado->Id,
                'Id_equipo' => $id,
                'Id_perfil' => $perfilId
            ]);

            return redirect()->back()->with('success', 'Usuario invitado exitosamente al equipo');
        } catch (\Exception $e) {
            Log::error('Error al invitar usuario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al invitar al usuario: ' . $e->getMessage());
        }
    }
}
