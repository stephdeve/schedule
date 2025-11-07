<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CoursPublie;

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

    /**
     * @OA\Post(
     *   path="/api/emplois/publier",
     *   tags={"Emplois"},
     *   summary="Publier un emploi du temps (liste de cours)",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"cours_ids"},
     *       @OA\Property(property="cours_ids", type="array", @OA\Items(type="integer")),
     *       @OA\Property(property="semaine", type="string"),
     *       @OA\Property(property="message", type="string")
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function publish(Request $request)
    {
        $data = $request->validate([
            'cours_ids' => ['required','array','min:1'],
            'cours_ids.*' => ['integer','exists:cours,id'],
            'semaine' => ['nullable','string','max:50'],
            'message' => ['nullable','string','max:1000'],
        ]);

        $coursList = Cours::with(['professeur','etudiants'])->whereIn('id', $data['cours_ids'])->get();
        $destinataires = collect();
        foreach ($coursList as $c) {
            $destinataires = $destinataires->merge([$c->professeur])->merge($c->etudiants);
        }
        $destinataires = $destinataires->filter()->unique('id');

        // Notifications en base + emails
        foreach ($coursList as $cours) {
            foreach ($destinataires as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => ($data['message'] ?? 'Publication de l\'emploi du temps').': '.$cours->nom_cours.' le '.$cours->date.' ('.$cours->heure_debut.' - '.$cours->heure_fin.') en salle '.$cours->salle,
                    'type' => 'app',
                    'lu' => false,
                ]);

                if (!empty($user->email)) {
                    try { Mail::to($user->email)->send(new CoursPublie($cours, $user)); } catch (\Throwable $e) { /* ignore */ }
                }
            }
        }

        // TODO: SMS et Push (FCM) peuvent être intégrés ici si les credentials sont présents dans .env

        return response()->json([
            'message' => 'Emploi du temps publié, notifications créées',
            'cours' => $coursList->count(),
            'destinataires' => $destinataires->count(),
        ]);
    }
}
