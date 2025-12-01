<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('Correo', $request->email)->first();

        if ($usuario && Hash::check($request->password, $usuario->Contraseña) && $usuario->Is_active) {
            Auth::login($usuario);
            

            $request->session()->regenerate();
            
            $userRole = DB::table('usuario_rol')
                ->join('rol', 'usuario_rol.Id_Rol', '=', 'rol.Id')
                ->where('usuario_rol.Id_usuario', $usuario->Id)
                ->first();

            if ($userRole) {
                switch ($userRole->Descripcion) {
                    case 'Administrador':
                        return redirect()->route('admin.equipos.index');
                    case 'Juez':
                        return redirect()->route('admin.jueces.index');
                    case 'Participante':
                        return redirect()->route('dashboard');
                    default:
                        return redirect()->route('dashboard');
                }
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros o el usuario está inactivo.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
