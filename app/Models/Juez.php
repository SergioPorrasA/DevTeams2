<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Juez extends Model
{
    protected $table = 'juez';  // Nombre correcto de la tabla
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Especialidad_id'
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'Especialidad_id', 'Id');
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'Id_juez', 'Id');
    }
}
