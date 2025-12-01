<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Correo',
        'Contraseña',
        'Is_active'
    ];

    protected $hidden = [
        'Contraseña',
        'remember_token',
    ];

    
    public function getEmailAttribute()
    {
        return $this->Correo;
    }

    
    public function getAuthPassword()
    {
        return $this->Contraseña;
    }

    
    public function getAuthIdentifierName()
    {
        return 'Correo';
    }

    public function getAuthIdentifier()
    {
        return $this->Correo;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'usuario_rol', 'Id_usuario', 'Id_Rol');
    }

    public function participante()
    {
        return $this->hasOne(Participante::class, 'Usuario_id', 'Id');
    }
}
