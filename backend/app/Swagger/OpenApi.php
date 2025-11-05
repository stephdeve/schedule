<?php

namespace App\Swagger;

/**
 * @OA\Tag(name="Auth", description="Authentification par token Sanctum")
 * @OA\Tag(name="Users", description="Gestion des utilisateurs")
 * @OA\Tag(name="Cours", description="Gestion des cours et publication")
 * @OA\Tag(name="Emplois", description="Consultation des emplois du temps")
 * @OA\Tag(name="Notifications", description="Consultation des notifications")
 */

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="nom_complet", type="string"),
 *   @OA\Property(property="email", type="string", format="email"),
 *   @OA\Property(property="role", type="string", enum={"admin","professeur","etudiant"}),
 *   @OA\Property(property="departement", type="string", nullable=true),
 *   @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */

/**
 * @OA\Schema(
 *   schema="Cours",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="nom_cours", type="string"),
 *   @OA\Property(property="prof_id", type="integer"),
 *   @OA\Property(property="salle", type="string"),
 *   @OA\Property(property="date", type="string", format="date"),
 *   @OA\Property(property="heure_debut", type="string", example="09:00:00"),
 *   @OA\Property(property="heure_fin", type="string", example="11:00:00"),
 *   @OA\Property(property="semestre", type="string"),
 *   @OA\Property(property="professeur", ref="#/components/schemas/User", nullable=true),
 *   @OA\Property(property="etudiants", type="array", @OA\Items(ref="#/components/schemas/User"))
 * )
 */

/**
 * @OA\Schema(
 *   schema="Notification",
 *   type="object",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="user_id", type="integer"),
 *   @OA\Property(property="message", type="string"),
 *   @OA\Property(property="type", type="string", example="app"),
 *   @OA\Property(property="lu", type="boolean"),
 *   @OA\Property(property="created_at", type="string", format="date-time", nullable=true)
 * )
 */

class OpenApi {}
