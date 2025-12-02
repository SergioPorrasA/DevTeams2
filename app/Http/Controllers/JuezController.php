<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Juez;

class JuezController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verificar si el usuario tiene rol de Juez
        if ($user->hasRole('Juez')) {
            // Obtener información del juez
            $juez = Juez::where('user_id', $user->id)->first();
            
            // Verificar si también es administrador
            $isAdmin = $user->hasRole('Administrador');
            
            return view('admin.jueces.index', compact('juez', 'isAdmin'));
        }

        return redirect()->route('dashboard')
            ->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
