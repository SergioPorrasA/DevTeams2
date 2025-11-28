<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    protected $table = 'asesor';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Correo',
        'Telefono'
    ];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class, 'Id_asesor', 'Id');
    }
}
