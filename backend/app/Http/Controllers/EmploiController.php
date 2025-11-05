<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\User;
use Illuminate\Http\Request;

class EmploiController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/emplois",
     *   tags={"Emplois"},
     *   summary="Lister les emplois du temps",
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index()
    {
        // Emplois globaux = tous les cours publiés (ici: tous les cours)
        return response()->json(Cours::with('professeur:id,nom_complet')->orderBy('date')->orderBy('heure_debut')->paginate(50));
    }

    /**
     * @OA\Get(
     *   path="/api/emplois/{user_id}",
     *   tags={"Emplois"},
     *   summary="Afficher l'emploi du temps d'un utilisateur",
     *   @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(string $user_id)
    {
        $user = User::findOrFail($user_id);
        if ($user->role === 'professeur') {
            $cours = Cours::where('prof_id', $user->id)->orderBy('date')->orderBy('heure_debut')->get();
        } elseif ($user->role === 'etudiant') {
            $cours = Cours::whereHas('etudiants', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->orderBy('date')->orderBy('heure_debut')->get();
        } else {
            $cours = Cours::orderBy('date')->orderBy('heure_debut')->get();
        }
        return response()->json($cours->load('professeur:id,nom_complet'));
    }
}
