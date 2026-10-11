<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Autenticación
 *
 * Endpoints para registrar usuarios, iniciar/cerrar sesión y obtener un token de acceso (Sanctum).
 */
class AuthController extends Controller
{
    /**
     * Registrar usuario
     *
     * Crea un usuario nuevo y devuelve un token de acceso.
     *
     * @bodyParam name string required Nombre del usuario. Example: Alejandro Jimenez
     * @bodyParam email string required Correo del usuario, debe ser único. Example: alejo@test.com
     * @bodyParam password string required Contraseña (mínimo 6 caracteres). Example: password123
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Iniciar sesión
     *
     * Valida las credenciales y devuelve un nuevo token de acceso.
     *
     * @bodyParam email string required Correo del usuario. Example: alejo@test.com
     * @bodyParam password string required Contraseña del usuario. Example: password123
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Cerrar sesión
     *
     * Revoca el token de acceso actual del usuario autenticado.
     *
     * @authenticated
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesion cerrada correctamente',
        ], 200);
    }
}
