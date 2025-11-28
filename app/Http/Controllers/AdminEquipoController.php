<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Participante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminEquipoController extends Controller
{
    // Listar todos los equipos
    public function index()
    {
        $equipos = Equipo::with(['participantes.usuario', 'proyectos.evento'])->get();
        
        return view('admin.equipos.index', compact('equipos'));
    }

    // Ver detalles de un equipo
    public function show($id)
    {
        $equipo = Equipo::with(['participantes.usuario', 'proyectos.evento', 'proyectos.asesor'])->findOrFail($id);
        
        return view('admin.equipos.show', compact('equipo'));
    }

    // Eliminar equipo
    public function destroy($id)
    {
        try {
            $equipo = Equipo::findOrFail($id);
            
            // Eliminar relaciones en participante_equipo
            DB::table('participante_equipo')
                ->where('Id_equipo', $id)
                ->delete();
            
            // Eliminar equipo
            $equipo->delete();

            return redirect()->route('admin.equipos.index')
                ->with('success', 'Equipo eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar equipo: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el equipo');
        }
    }
}