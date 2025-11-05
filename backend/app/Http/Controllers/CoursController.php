<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CoursPublie;

class CoursController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/cours",
     *   tags={"Cours"},
     *   summary="Lister les cours",
     *   security={{"sanctum":{}}},
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index()
    {
        return response()->json(
            Cours::with(['professeur:id,nom_complet,email','etudiants:id,nom_complet,email'])->paginate(20)
        );
    }

    /**
     * @OA\Get(
     *   path="/api/cours/{id}",
     *   tags={"Cours"},
     *   summary="Afficher un cours",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function show(string $id)
    {
        return response()->json(
            Cours::with(['professeur:id,nom_complet,email','etudiants:id,nom_complet,email'])->findOrFail($id)
        );
    }

    /**
     * @OA\Post(
     *   path="/api/cours",
     *   tags={"Cours"},
     *   summary="Créer un cours",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nom_cours","prof_id","salle","date","heure_debut","heure_fin","semestre"},
     *       @OA\Property(property="nom_cours", type="string"),
     *       @OA\Property(property="prof_id", type="integer"),
     *       @OA\Property(property="salle", type="string"),
     *       @OA\Property(property="date", type="string", format="date"),
     *       @OA\Property(property="heure_debut", type="string"),
     *       @OA\Property(property="heure_fin", type="string"),
     *       @OA\Property(property="semestre", type="string"),
     *       @OA\Property(property="etudiant_ids", type="array", @OA\Items(type="integer"))
     *     )
     *   ),
     *   @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom_cours' => ['required','string','max:255'],
            'prof_id' => ['required','exists:users,id'],
            'salle' => ['required','string','max:255'],
            'date' => ['required','date'],
            'heure_debut' => ['required'],
            'heure_fin' => ['required'],
            'semestre' => ['required','string','max:50'],
            'etudiant_ids' => ['array'],
            'etudiant_ids.*' => ['integer','exists:users,id'],
        ]);

        $cours = DB::transaction(function () use ($data) {
            $etudiants = $data['etudiant_ids'] ?? [];
            unset($data['etudiant_ids']);
            $cours = Cours::create($data);
            if (!empty($etudiants)) {
                $cours->etudiants()->sync($etudiants);
            }
            return $cours;
        });

        return response()->json($cours->load(['professeur','etudiants']), 201);
    }

    /**
     * @OA\Put(
     *   path="/api/cours/{id}",
     *   tags={"Cours"},
     *   summary="Mettre à jour un cours",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="nom_cours", type="string"),
     *       @OA\Property(property="prof_id", type="integer"),
     *       @OA\Property(property="salle", type="string"),
     *       @OA\Property(property="date", type="string", format="date"),
     *       @OA\Property(property="heure_debut", type="string"),
     *       @OA\Property(property="heure_fin", type="string"),
     *       @OA\Property(property="semestre", type="string"),
     *       @OA\Property(property="etudiant_ids", type="array", @OA\Items(type="integer"))
     *     )
     *   ),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function update(Request $request, string $id)
    {
        $cours = Cours::findOrFail($id);
        $data = $request->validate([
            'nom_cours' => ['sometimes','string','max:255'],
            'prof_id' => ['sometimes','exists:users,id'],
            'salle' => ['sometimes','string','max:255'],
            'date' => ['sometimes','date'],
            'heure_debut' => ['sometimes'],
            'heure_fin' => ['sometimes'],
            'semestre' => ['sometimes','string','max:50'],
            'etudiant_ids' => ['array'],
            'etudiant_ids.*' => ['integer','exists:users,id'],
        ]);

        DB::transaction(function () use ($cours, $data) {
            $etudiants = $data['etudiant_ids'] ?? null;
            unset($data['etudiant_ids']);
            $cours->update($data);
            if (is_array($etudiants)) {
                $cours->etudiants()->sync($etudiants);
            }
        });

        return response()->json($cours->load(['professeur','etudiants']));
    }

    /**
     * @OA\Delete(
     *   path="/api/cours/{id}",
     *   tags={"Cours"},
     *   summary="Supprimer un cours",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function destroy(string $id)
    {
        $cours = Cours::findOrFail($id);
        $cours->delete();
        return response()->json(['message' => 'Cours deleted']);
    }

    /**
     * @OA\Post(
     *   path="/api/cours/{id}/publish",
     *   tags={"Cours"},
     *   summary="Publier un cours et notifier",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function publish(string $id)
    {
        $cours = Cours::with(['professeur','etudiants'])->findOrFail($id);
        // Crée des notifications en base (email/sms seront implémentés ensuite)
        $destinataires = collect([$cours->professeur])->merge($cours->etudiants);
        foreach ($destinataires as $user) {
            Notification::create([
                'user_id' => $user->id,
                'message' => 'Publication du cours: '.$cours->nom_cours.' le '.$cours->date.' ('.$cours->heure_debut.' - '.$cours->heure_fin.') en salle '.$cours->salle,
                'type' => 'app',
                'lu' => false,
            ]);

            // Envoi email (Mailtrap ou log selon .env)
            if (!empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new CoursPublie($cours, $user));
                } catch (\Throwable $e) {
                    // Eviter d'échouer si mailer non configuré
                }
            }
        }

        return response()->json(['message' => 'Cours published and notifications queued']);
    }
}
