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

    // Configurar el campo de email para Laravel
    public function getEmailAttribute()
    {
        return $this->Correo;
    }

    // Configurar el campo de contraseña para Laravel
    public function getAuthPassword()
    {
        return $this->Contraseña;
    }

    // Configurar el nombre del campo email
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
