<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Mail\CoursPublie;
use App\Services\Messaging\SmsService;
use App\Services\Messaging\FcmService;

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

        // Conflits: salle/prof/date/heure
        if ($this->hasConflict($data['date'], $data['heure_debut'], $data['heure_fin'], (int) $data['prof_id'], $data['salle'])) {
            return response()->json([
                'message' => 'Conflit détecté (salle ou professeur déjà occupé sur ce créneau).'
            ], 422);
        }

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

        $check = [
            'date' => $data['date'] ?? $cours->date,
            'heure_debut' => $data['heure_debut'] ?? $cours->heure_debut,
            'heure_fin' => $data['heure_fin'] ?? $cours->heure_fin,
            'prof_id' => (int) ($data['prof_id'] ?? $cours->prof_id),
            'salle' => $data['salle'] ?? $cours->salle,
        ];
        if ($this->hasConflict($check['date'], $check['heure_debut'], $check['heure_fin'], $check['prof_id'], $check['salle'], $cours->id)) {
            return response()->json([
                'message' => 'Conflit détecté (salle ou professeur déjà occupé sur ce créneau).'
            ], 422);
        }

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

            // SMS optionnel
            try {
                /** @var SmsService $sms */
                $sms = App::make(SmsService::class);
                $sms->send($user->telephone ?? null, 'Cours publié: '.$cours->nom_cours.' le '.$cours->date.' '.$cours->heure_debut.' - '.$cours->heure_fin.' ('.$cours->salle.')');
            } catch (\Throwable $e) { Log::debug('SMS skipped: '.$e->getMessage()); }

            // Push FCM optionnel
            try {
                /** @var FcmService $fcm */
                $fcm = App::make(FcmService::class);
                $fcm->sendToToken($user->fcm_token ?? null, 'Nouveau cours publié', $cours->nom_cours.' - '.$cours->date.' '.$cours->heure_debut, [ 'cours_id' => $cours->id ]);
            } catch (\Throwable $e) { Log::debug('FCM skipped: '.$e->getMessage()); }
        }

        return response()->json(['message' => 'Cours published and notifications queued']);
    }

    private function hasConflict(string $date, string $debut, string $fin, int $profId, string $salle, ?int $ignoreId = null): bool
    {
        return Cours::query()
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->whereDate('date', $date)
            ->where(function($q) use ($salle, $profId) {
                $q->where('salle', $salle)
                  ->orWhere('prof_id', $profId);
            })
            ->where(function($q) use ($debut, $fin) {
                $q->where('heure_debut', '<', $fin)
                  ->where('heure_fin', '>', $debut);
            })
            ->exists();
    }
}
