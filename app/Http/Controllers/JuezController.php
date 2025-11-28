<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JuezController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $userRole = DB::table('usuario_rol')
            ->join('rol', 'usuario_rol.Id_Rol', '=', 'rol.Id')
            ->where('usuario_rol.Id_usuario', $user->Id)
            ->first();
        
        // Solo permitir Administrador y Juez
        if (!$userRole || !in_array($userRole->Descripcion, ['Administrador', 'Juez'])) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        $isAdmin = $userRole->Descripcion === 'Administrador';
        
        return view('admin.jueces.index', compact('isAdmin'));
    }
}
