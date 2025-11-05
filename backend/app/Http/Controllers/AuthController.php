<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/login",
     *   tags={"Auth"},
     *   summary="Login",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"email","password"},
     *           @OA\Property(property="email", type="string", format="email"),
     *           @OA\Property(property="password", type="string")
     *       )
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->mot_de_passe)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Login ok',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/api/register",
     *   tags={"Auth"},
     *   summary="Register (admin only)",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           required={"nom_complet","email","password","role"},
     *           @OA\Property(property="nom_complet", type="string"),
     *           @OA\Property(property="email", type="string", format="email"),
     *           @OA\Property(property="password", type="string"),
     *           @OA\Property(property="role", type="string", enum={"admin","professeur","etudiant"}),
     *           @OA\Property(property="departement", type="string")
     *       )
     *   ),
     *   @OA\Response(response=201, description="Created"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nom_complet' => ['required','string','max:255'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','min:6'],
            'role' => ['required','in:admin,professeur,etudiant'],
            'departement' => ['nullable','string','max:255'],
        ]);

        $user = User::create([
            'nom_complet' => $data['nom_complet'],
            'email' => $data['email'],
            'mot_de_passe' => Hash::make($data['password']),
            'role' => $data['role'],
            'departement' => $data['departement'] ?? null,
        ]);

        return response()->json($user, 201);
    }

    /**
     * @OA\Post(
     *   path="/api/logout",
     *   tags={"Auth"},
     *   summary="Logout",
     *   security={{"sanctum":{}}},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

