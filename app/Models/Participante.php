<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    protected $table = 'participante';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Usuario_id',  
        'Carrera_id',  
        'No_Control',
        'Correo',
        'telefono'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Usuario_id', 'Id');  
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'Carrera_id', 'Id'); 
    }

    public function equipos()
    {
        return $this->belongsToMany(
            Equipo::class,
            'participante_equipo',
            'Id_participante',
            'Id_equipo'
        );
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'Usuario_Rol', 'Id');
    }
}
