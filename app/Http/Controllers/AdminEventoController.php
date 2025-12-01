<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Juez;
use Illuminate\Support\Facades\Log;

class AdminEventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::with(['juez', 'proyectos'])->orderBy('Fecha_inicio', 'desc')->get();
        $jueces = Juez::all();
        
        return view('admin.eventos.index', compact('eventos', 'jueces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'id_juez' => 'nullable|exists:juez,Id'
        ]);

        try {
            $evento = new Evento();
            $evento->Nombre = $request->nombre;
            $evento->Descripcion = $request->descripcion;
            $evento->Fecha_inicio = $request->fecha_inicio;
            $evento->Fecha_fin = $request->fecha_fin;
            $evento->Id_juez = $request->id_juez;
            $evento->save();

            Log::info('Evento creado', ['evento_id' => $evento->Id]);

            return redirect()->route('admin.eventos.index')
                ->with('success', 'Evento creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear evento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear el evento: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $evento = Evento::with(['juez', 'proyectos.equipo.participantes.usuario', 'proyectos.asesor'])
            ->findOrFail($id);
        
        return view('admin.eventos.show', compact('evento'));
    }

    public function edit($id)
    {
        $evento = Evento::findOrFail($id);
        $jueces = Juez::all();
        
        return view('admin.eventos.edit', compact('evento', 'jueces'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'id_juez' => 'nullable|exists:juez,Id'
        ]);

        try {
            $evento = Evento::findOrFail($id);
            $evento->Nombre = $request->nombre;
            $evento->Descripcion = $request->descripcion;
            $evento->Fecha_inicio = $request->fecha_inicio;
            $evento->Fecha_fin = $request->fecha_fin;
            $evento->Id_juez = $request->id_juez;
            $evento->save();

            return redirect()->route('admin.eventos.index')
                ->with('success', 'Evento actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar evento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar el evento')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $evento = Evento::findOrFail($id);
            $evento->delete();

            return redirect()->route('admin.eventos.index')
                ->with('success', 'Evento eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar evento: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el evento');
        }
    }

    public function equiposInscritos($id)
    {
        $evento = Evento::with(['proyectos.equipo.participantes.usuario', 'proyectos.asesor'])
            ->findOrFail($id);
        
        return view('admin.eventos.equipos', compact('evento'));
    }
}
