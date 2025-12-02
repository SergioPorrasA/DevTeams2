<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Equipo;
use App\Models\Participante;
use App\Models\Perfil;

class EquipoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        if (!$participante) {
            return view('equipos.index', ['equipos' => collect()]);
        }

        // ✅ Obtener equipos del participante con sus miembros
        $equipos = Equipo::whereIn('Id', function($query) use ($participante) {
            $query->select('Id_equipo')
                  ->from('participante_equipo')
                  ->where('Id_participante', $participante->Id);
        })
        ->with(['participantes.usuario']) // ✅ Usar 'usuario' no 'user'
        ->get();

        return view('dashboard', compact('equipos'));
    }

    public function create()
    {
        return view('equipos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:equipo,Nombre',
        ]);

        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        if (!$participante) {
            return back()->with('error', 'Debes ser un participante para crear un equipo.');
        }

        DB::beginTransaction();

        try {
            // Crear el equipo
            $equipo = Equipo::create([
                'Nombre' => $request->nombre,
            ]);

            // Obtener el perfil de "Líder"
            $perfilLider = Perfil::where('Nombre', 'Líder')->first();

            if (!$perfilLider) {
                DB::rollBack();
                return back()->with('error', 'No se encontró el perfil de Líder en el sistema.');
            }

            // Asignar al creador como líder
            DB::table('participante_equipo')->insert([
                'Id_participante' => $participante->Id,
                'Id_equipo' => $equipo->Id,
                'Id_perfil' => $perfilLider->Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('equipos.show', $equipo->Id)
                ->with('success', '¡Equipo creado exitosamente! Eres el líder del equipo.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el equipo: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        // ✅ Cargar equipo con participantes y sus usuarios
        $equipo = Equipo::with(['participantes.usuario', 'participantes.carrera'])->findOrFail($id);
        
        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        // Verificar si el usuario actual es miembro del equipo
        $esMiembro = false;
        $esLider = false;

        if ($participante) {
            $miembro = DB::table('participante_equipo')
                ->where('Id_equipo', $equipo->Id)
                ->where('Id_participante', $participante->Id)
                ->first();

            if ($miembro) {
                $esMiembro = true;
                
                // Verificar si es líder
                $perfilLider = Perfil::where('Nombre', 'Líder')->first();
                if ($perfilLider && $miembro->Id_perfil == $perfilLider->Id) {
                    $esLider = true;
                }
            }
        }

        return view('equipos.show', compact('equipo', 'esMiembro', 'esLider'));
    }

    public function edit($id)
    {
        $equipo = Equipo::findOrFail($id);
        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        if (!$participante) {
            return redirect()->route('equipos.index')
                ->with('error', 'No tienes permisos para editar este equipo.');
        }

        // Verificar si es líder
        $perfilLider = Perfil::where('Nombre', 'Líder')->first();
        $esLider = DB::table('participante_equipo')
            ->where('Id_equipo', $equipo->Id)
            ->where('Id_participante', $participante->Id)
            ->where('Id_perfil', $perfilLider->Id)
            ->exists();

        if (!$esLider) {
            return redirect()->route('equipos.show', $equipo->Id)
                ->with('error', 'Solo el líder puede editar el equipo.');
        }

        return view('equipos.edit', compact('equipo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:equipo,Nombre,' . $id . ',Id',
        ]);

        $equipo = Equipo::findOrFail($id);
        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        if (!$participante) {
            return back()->with('error', 'No tienes permisos para actualizar este equipo.');
        }

        // Verificar si es líder
        $perfilLider = Perfil::where('Nombre', 'Líder')->first();
        $esLider = DB::table('participante_equipo')
            ->where('Id_equipo', $equipo->Id)
            ->where('Id_participante', $participante->Id)
            ->where('Id_perfil', $perfilLider->Id)
            ->exists();

        if (!$esLider) {
            return back()->with('error', 'Solo el líder puede actualizar el equipo.');
        }

        $equipo->update([
            'Nombre' => $request->nombre,
        ]);

        return redirect()->route('equipos.show', $equipo->Id)
            ->with('success', 'Equipo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        if (!$participante) {
            return back()->with('error', 'No tienes permisos para eliminar este equipo.');
        }

        // Verificar si es líder
        $perfilLider = Perfil::where('Nombre', 'Líder')->first();
        $esLider = DB::table('participante_equipo')
            ->where('Id_equipo', $equipo->Id)
            ->where('Id_participante', $participante->Id)
            ->where('Id_perfil', $perfilLider->Id)
            ->exists();

        if (!$esLider) {
            return back()->with('error', 'Solo el líder puede eliminar el equipo.');
        }

        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo eliminado exitosamente.');
    }
}
