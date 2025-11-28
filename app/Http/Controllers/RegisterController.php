<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Participante;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:usuario,Nombre',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:255',
            'no_control' => 'required|string|max:20|unique:participante,No_Control',
            'carrera_id' => 'required|integer|exists:carrera,Id',
            'correo' => 'required|email|max:255|unique:participante,Correo',
            'telefono' => 'required|string|max:15',
            'terms' => 'required|accepted',
        ]);

        DB::beginTransaction();

        try {
            // Crear usuario
            $usuario = Usuario::create([
                'Nombre' => $request->username,
                'Correo' => $request->correo,
                'Contraseña' => Hash::make($request->password),
                'Is_active' => true,
            ]);

            // Crear participante
            Participante::create([
                'Usuario_id' => $usuario->Id,
                'No_Control' => $request->no_control,
                'Carrera_id' => $request->carrera_id,
                'Nombre' => $request->nombre,
                'Correo' => $request->correo,
                'telefono' => $request->telefono,
            ]);

            // Asignar rol de participante (ID = 3)
            DB::table('usuario_rol')->insert([
                'Id_usuario' => $usuario->Id,
                'Id_Rol' => 3, // Rol participante
            ]);

            DB::commit();

            // Redirigir al login con mensaje de éxito
            return redirect()->route('login')->with('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar usuario: ' . $e->getMessage()])->withInput();
        }
    }
}
