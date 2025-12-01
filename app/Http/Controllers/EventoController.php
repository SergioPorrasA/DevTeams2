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
    public function index()
    {
        $eventos = Evento::orderBy('Fecha_inicio', 'desc')->get();
        
        $user = Auth::user();
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        //equipos del participante
        $equiposIds = collect();
        if ($participante) {
            $equiposIds = DB::table('participante_equipo')
                ->where('Id_participante', $participante->Id)
                ->pluck('Id_equipo');
        }
        
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

    public function show($id)
    {
        $evento = Evento::with(['proyectos.equipo'])->findOrFail($id);
        
        $user = Auth::user();
        $participante = Participante::where('Usuario_id', $user->Id)->first();
        
        $equipos = collect();
        $inscrito = false;
        
        if ($participante) {
            $equipos = Equipo::whereIn('Id', function($query) use ($participante) {
                $query->select('Id_equipo')
                      ->from('participante_equipo')
                      ->where('Id_participante', $participante->Id);
            })->get();
            
            $equiposIds = $equipos->pluck('Id');
            $inscrito = Proyecto::where('Id_evento', $id)
                ->whereIn('Id_equipo', $equiposIds)
                ->exists();
        }
        
        return view('eventos.show', compact('evento', 'equipos', 'inscrito'));
    }

    //inscripcion al ev
    public function inscripcion($id)
    {
        $evento = Evento::findOrFail($id);
        
        //si el evento esta activo 
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
        
        //seleccionar equipo
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
            
            if ($evento->estado === 'finalizado') {
                return redirect()->route('eventos.index')
                    ->with('error', 'No puedes inscribirte a un evento finalizado');
            }
            
            $user = Auth::user();
            $participante = Participante::where('Usuario_id', $user->Id)->first();
            
            $perteneceAlEquipo = DB::table('participante_equipo')
                ->where('Id_participante', $participante->Id)
                ->where('Id_equipo', $request->equipo_id)
                ->exists();
            
            if (!$perteneceAlEquipo) {
                return redirect()->back()
                    ->with('error', 'No perteneces a este equipo');
            }
            
            $yaInscrito = Proyecto::where('Id_evento', $id)
                ->where('Id_equipo', $request->equipo_id)
                ->exists();
            
            if ($yaInscrito) {
                return redirect()->back()
                    ->with('error', 'Este equipo ya está inscrito en el evento');
            }
            
            //asesor
            $asesor = Asesor::firstOrCreate(
                ['Correo' => $request->asesor_correo],
                [
                    'Nombre' => $request->asesor_nombre,
                    'Telefono' => $request->asesor_telefono
                ]
            );
            
            //proyecto para la inscripcion
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
