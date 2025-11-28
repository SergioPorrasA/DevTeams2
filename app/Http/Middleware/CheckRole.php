<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Obtener rol del usuario
        $userRole = DB::table('usuario_rol')
            ->join('rol', 'usuario_rol.Id_Rol', '=', 'rol.Id')
            ->where('usuario_rol.Id_usuario', $user->Id)
            ->first();

        if (!$userRole) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'No tienes un rol asignado.');
        }

        // Separar roles permitidos
        $allowedRoles = array_map('trim', explode(',', $roles));
        
        // Si el usuario tiene uno de los roles permitidos, CONTINUAR
        if (in_array($userRole->Descripcion, $allowedRoles)) {
            return $next($request);
        }

        // Si NO tiene permiso, redirigir a su dashboard
        switch ($userRole->Descripcion) {
            case 'Administrador':
                return redirect()->route('admin.equipos.index')
                    ->with('error', 'No tienes permisos para acceder a esa sección.');
            
            case 'Juez':
                return redirect()->route('admin.jueces.index')
                    ->with('error', 'No tienes permisos para acceder a esa sección.');
            
            case 'Participante':
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes permisos para acceder a esa sección.');
            
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Rol no reconocido.');
        }
    }
}
