<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="List users",
     *   security={{"sanctum":{}}},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index()
    {
        return response()->json(User::paginate(20));
    }

    /**
     * @OA\Get(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Get user",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $id)
    {
        return response()->json(User::findOrFail($id));
    }

    /**
     * @OA\Post(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="Create user",
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
    public function store(Request $request)
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
     * @OA\Put(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Update user",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *           @OA\Property(property="nom_complet", type="string"),
     *           @OA\Property(property="email", type="string", format="email"),
     *           @OA\Property(property="password", type="string"),
     *           @OA\Property(property="role", type="string", enum={"admin","professeur","etudiant"}),
     *           @OA\Property(property="departement", type="string")
     *       )
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'nom_complet' => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','unique:users,email,'.$user->id],
            'password' => ['sometimes','min:6'],
            'role' => ['sometimes','in:admin,professeur,etudiant'],
            'departement' => ['nullable','string','max:255'],
        ]);

        if (isset($data['password'])) {
            $data['mot_de_passe'] = Hash::make($data['password']);
            unset($data['password']);
        }

        $user->update($data);
        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Delete user",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}

