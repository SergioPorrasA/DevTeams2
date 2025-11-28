<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyecto';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'Id_equipo',
        'Id_evento',
        'Id_asesor',
        'Categoria'
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'Id_equipo', 'Id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'Id_evento', 'Id');
    }

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'Id_asesor', 'Id');
    }

    public function repositorio()
    {
        return $this->hasOne(Repositorio::class, 'Id', 'Id_repositorio');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'Id_criterio', 'Id');
    }

    public function avances()
    {
        return $this->hasMany(Avance::class, 'Id_avance', 'Id');
    }
}
