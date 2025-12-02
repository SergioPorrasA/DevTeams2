<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Evento;
use App\Models\Participante;
use App\Models\Equipo;
use App\Models\Proyecto;
use App\Models\Asesor;

class EventoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();
        
        $eventos = Evento::where('Estado', 'Activo')
                        ->orderBy('Fecha_inicio', 'desc')
                        ->get();
        
        // ✅ Verificar en qué eventos está inscrito el participante
        $eventosInscritos = [];
        if ($participante) {
            $eventosInscritos = DB::table('proyecto')
                ->join('participante_equipo', 'proyecto.Equipo_id', '=', 'participante_equipo.Id_equipo')
                ->where('participante_equipo.Id_participante', $participante->Id)
                ->pluck('proyecto.Evento_id')
                ->toArray();
        }
        
        // Marcar eventos inscritos
        foreach ($eventos as $evento) {
            $evento->estaInscrito = in_array($evento->Id, $eventosInscritos);
        }
        
        return view('eventos.index', compact('eventos', 'participante'));
    }

    public function show($id)
    {
        $evento = Evento::with('jueces')->findOrFail($id);
        $user = Auth::user();
        
        $participante = Participante::where('user_id', $user->id)->first();
        
        $estaInscrito = false;
        $proyectos = collect();
        
        if ($participante) {
            $proyectos = DB::table('proyecto')
                ->join('equipo', 'proyecto.Equipo_id', '=', 'equipo.Id')
                ->join('participante_equipo', 'equipo.Id', '=', 'participante_equipo.Id_equipo')
                ->where('proyecto.Evento_id', $evento->Id)
                ->where('participante_equipo.Id_participante', $participante->Id)
                ->select('proyecto.*', 'equipo.Nombre as nombre_equipo')
                ->get();
            
            $estaInscrito = $proyectos->isNotEmpty();
        }
        
        return view('eventos.show', compact('evento', 'estaInscrito', 'proyectos', 'participante'));
    }

    public function inscripcion($id)
    {
        $evento = Evento::findOrFail($id);
        $user = Auth::user();
        
        $participante = Participante::where('user_id', $user->id)->first();
        
        if (!$participante) {
            return redirect()->back()
                ->with('error', 'No se encontró tu perfil de participante. Contacta al administrador.');
        }
        
        $equiposIds = DB::table('participante_equipo')
            ->where('Id_participante', $participante->Id)
            ->pluck('Id_equipo');
        
        $equipos = Equipo::whereIn('Id', $equiposIds)
            ->withCount('participantes')
            ->get();
        
        if ($equipos->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Debes pertenecer a un equipo para inscribirte. Crea o únete a un equipo primero.');
        }

        $asesores = Asesor::all();
        
        return view('eventos.inscripcion', compact('evento', 'equipos', 'asesores', 'participante'));
    }


    public function inscribirse(Request $request, $id)
    {
        // Validar datos del formulario
        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipo,Id',
            'asesor_id' => 'required|exists:asesor,Id',
            'nombre_proyecto' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
        ], [
            'equipo_id.required' => 'Debes seleccionar un equipo',
            'asesor_id.required' => 'Debes seleccionar un asesor',
            'nombre_proyecto.required' => 'El nombre del proyecto es obligatorio',
            'categoria.required' => 'Debes seleccionar una categoría',
        ]);

        $user = Auth::user();
        $participante = Participante::where('user_id', $user->id)->first();

        if (!$participante) {
            return redirect()->back()
                ->with('error', 'Debes ser un participante para inscribirte.')
                ->withInput();
        }

        // Verificar que el participante pertenece al equipo
        $perteneceAlEquipo = DB::table('participante_equipo')
            ->where('Id_participante', $participante->Id)
            ->where('Id_equipo', $request->equipo_id)
            ->exists();

        if (!$perteneceAlEquipo) {
            return redirect()->back()
                ->with('error', 'No perteneces al equipo seleccionado.')
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Verificar si el equipo ya está inscrito en este evento
            $yaInscrito = Proyecto::where('Evento_id', $id)
                ->where('Equipo_id', $request->equipo_id)
                ->exists();

            if ($yaInscrito) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Este equipo ya está inscrito en el evento.')
                    ->withInput();
            }

            // Crear el proyecto
            $proyecto = Proyecto::create([
                'Equipo_id' => $request->equipo_id,
                'Evento_id' => $id,
                'Asesor_id' => $request->asesor_id,
                'Nombre' => $request->nombre_proyecto,
                'Categoria' => $request->categoria,
            ]);

            DB::commit();

            // ✅ Redirigir a la vista de eventos con mensaje de éxito
            return redirect()->route('eventos.index')
                ->with('success', '¡Inscripción exitosa! Tu equipo "' . $proyecto->Nombre . '" ha sido inscrito al evento "' . Evento::find($id)->Nombre . '".');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al inscribir: ' . $e->getMessage())
                ->withInput();
        }
    }
}
