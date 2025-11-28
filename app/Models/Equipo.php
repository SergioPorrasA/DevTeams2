<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $table = 'equipo';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'nombre'
    ];

    public function participantes()
    {
        return $this->belongsToMany(
            Participante::class,
            'participante_equipo',
            'Id_equipo',
            'Id_participante'
        )->withPivot('Id_perfil');
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'Id_equipo', 'Id');
    }

    // Obtener cantidad de miembros
    public function getCantidadMiembrosAttribute()
    {
        return $this->participantes()->count();
    }

    // Obtener líder del equipo (primer participante con perfil Líder)
    public function getLiderAttribute()
    {
        return $this->participantes()->wherePivot('Id_perfil', 1)->first() 
            ?? $this->participantes()->first();
    }
    
    // Obtener el perfil desde la tabla intermedia
    public function getPerfilAttribute()
    {
        $participante = $this->participantes()->first();
        if ($participante && $participante->pivot) {
            return Perfil::find($participante->pivot->Id_perfil);
        }
        return null;
    }
}
