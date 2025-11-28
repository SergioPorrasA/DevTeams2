<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Equipo;
use App\Models\Participante;
use App\Models\Proyecto;
use App\Models\Asesor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventoController extends Controller
{
    // Listar eventos
    public function index()
    {
        $eventos = Evento::orderBy('Fecha_inicio', 'desc')->get();
        
        $user = Auth::user();
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        // Obtener equipos del participante
        $equiposIds = collect();
        if ($participante) {
            $equiposIds = DB::table('participante_equipo')
                ->where('Id_participante', $participante->Id)
                ->pluck('Id_equipo');
        }
        
        // Verificar inscripciones
        foreach ($eventos as $evento) {
            $evento->inscrito = false;
            if ($equiposIds->isNotEmpty()) {
                $evento->inscrito = Proyecto::where('Id_evento', $evento->Id)
                    ->whereIn('Id_equipo', $equiposIds)
                    ->exists();
            }
        }
        
        return view('eventos.index', compact('eventos'));
    }

    // Ver detalles del evento
    public function show($id)
    {
        $evento = Evento::with(['proyectos.equipo'])->findOrFail($id);
        
        $user = Auth::user();
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        // Obtener equipos del participante
        $equipos = collect();
        $inscrito = false;
        
        if ($participante) {
            $equipos = Equipo::whereIn('Id', function($query) use ($participante) {
                $query->select('Id_equipo')
                      ->from('participante_equipo')
                      ->where('Id_participante', $participante->Id);
            })->get();
            
            // Verificar si está inscrito
            $equiposIds = $equipos->pluck('Id');
            $inscrito = Proyecto::where('Id_evento', $id)
                ->whereIn('Id_equipo', $equiposIds)
                ->exists();
        }
        
        return view('eventos.show', compact('evento', 'equipos', 'inscrito'));
    }

    // Mostrar formulario de inscripción
    public function inscripcion($id)
    {
        $evento = Evento::findOrFail($id);
        
        // Verificar que el evento esté activo o próximo
        if ($evento->estado === 'finalizado') {
            return redirect()->route('eventos.index')
                ->with('error', 'No puedes inscribirte a un evento finalizado');
        }
        
        $user = Auth::user();
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        if (!$participante) {
            return redirect()->route('eventos.index')
                ->with('error', 'Debes ser un participante para inscribirte');
        }
        
        // Obtener equipos del participante
        $equipos = Equipo::whereIn('Id', function($query) use ($participante) {
            $query->select('Id_equipo')
                  ->from('participante_equipo')
                  ->where('Id_participante', $participante->Id);
        })->with('participantes')->get();
        
        if ($equipos->isEmpty()) {
            return redirect()->route('eventos.index')
                ->with('error', 'Debes crear un equipo primero para inscribirte');
        }
        
        return view('eventos.inscripcion', compact('evento', 'equipos'));
    }

    // Procesar inscripción
    public function inscribirse(Request $request, $id)
    {
        $request->validate([
            'equipo_id' => 'required|exists:equipo,Id',
            'nombre_proyecto' => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'asesor_nombre' => 'required|string|max:255',
            'asesor_correo' => 'required|email|max:255',
            'asesor_telefono' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $evento = Evento::findOrFail($id);
            
            // Verificar que el evento esté activo o próximo
            if ($evento->estado === 'finalizado') {
                return redirect()->route('eventos.index')
                    ->with('error', 'No puedes inscribirte a un evento finalizado');
            }
            
            $user = Auth::user();
            $participante = Participante::where('Usuario_id', $user->Id)->first();
            
            // Verificar que el usuario pertenezca al equipo
            $perteneceAlEquipo = DB::table('participante_equipo')
                ->where('Id_participante', $participante->Id)
                ->where('Id_equipo', $request->equipo_id)
                ->exists();
            
            if (!$perteneceAlEquipo) {
                return redirect()->back()
                    ->with('error', 'No perteneces a este equipo');
            }
            
            // Verificar que el equipo no esté ya inscrito
            $yaInscrito = Proyecto::where('Id_evento', $id)
                ->where('Id_equipo', $request->equipo_id)
                ->exists();
            
            if ($yaInscrito) {
                return redirect()->back()
                    ->with('error', 'Este equipo ya está inscrito en el evento');
            }
            
            // Crear o buscar asesor
            $asesor = Asesor::firstOrCreate(
                ['Correo' => $request->asesor_correo],
                [
                    'Nombre' => $request->asesor_nombre,
                    'Telefono' => $request->asesor_telefono
                ]
            );
            
            // Crear proyecto
            $proyecto = new Proyecto();
            $proyecto->nombre = $request->nombre_proyecto;
            $proyecto->Id_equipo = $request->equipo_id;
            $proyecto->Id_evento = $id;
            $proyecto->Id_asesor = $asesor->Id;
            $proyecto->Categoria = $request->categoria;
            $proyecto->save();

            DB::commit();

            Log::info('Inscripción exitosa', [
                'proyecto_id' => $proyecto->Id,
                'evento_id' => $id,
                'equipo_id' => $request->equipo_id
            ]);

            // Redirigir al índice de eventos
            return redirect()->route('eventos.index')
                ->with('success', '¡Te has inscrito exitosamente al evento "' . $evento->Nombre . '"!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al inscribirse al evento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al inscribirse: ' . $e->getMessage())
                ->withInput();
        }
    }
}
