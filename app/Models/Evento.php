<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
    protected $table = 'evento';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Descripcion',
        'Fecha_inicio',
        'Fecha_fin',
        'Id_juez'
    ];

    protected $casts = [
        'Fecha_inicio' => 'datetime',
        'Fecha_fin' => 'datetime',
    ];

    public function juez()
    {
        return $this->belongsTo(Juez::class, 'Id_juez', 'Id');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'Id_evento', 'Id');
    }

    // Obtener estado del evento
    public function getEstadoAttribute()
    {
        $now = Carbon::now();
        $inicio = Carbon::parse($this->Fecha_inicio);
        $fin = Carbon::parse($this->Fecha_fin);

        if ($now->lt($inicio)) {
            return 'proximo';
        } elseif ($now->between($inicio, $fin)) {
            return 'activo';
        } else {
            return 'finalizado';
        }
    }

    // Obtener etiqueta de estado con estilos
    public function getEstadoLabelAttribute()
    {
        $estado = $this->estado;
        
        switch ($estado) {
            case 'proximo':
                return [
                    'texto' => 'Próximo',
                    'clase' => 'bg-blue-100 text-blue-700'
                ];
            case 'activo':
                return [
                    'texto' => 'En curso',
                    'clase' => 'bg-green-100 text-green-700'
                ];
            case 'finalizado':
                return [
                    'texto' => 'Finalizado',
                    'clase' => 'bg-gray-100 text-gray-600'
                ];
            default:
                return [
                    'texto' => 'Desconocido',
                    'clase' => 'bg-gray-100 text-gray-600'
                ];
        }
    }

    // Verificar si un equipo está inscrito
    public function equipoInscrito($equipoId)
    {
        return $this->proyectos()->where('Id_equipo', $equipoId)->exists();
    }

    // Cantidad de equipos inscritos
    public function getCantidadEquiposAttribute()
    {
        return $this->proyectos()->count();
    }
}
