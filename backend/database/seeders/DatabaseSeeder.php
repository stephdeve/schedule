<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cours;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'nom_complet' => 'Admin Principal',
            'email' => 'admin@example.com',
            'mot_de_passe' => Hash::make('password'),
            'role' => 'admin',
            'departement' => 'Direction',
        ]);

        // Professeur
        $prof = User::create([
            'nom_complet' => 'Pr. Dupont',
            'email' => 'prof@example.com',
            'mot_de_passe' => Hash::make('password'),
            'role' => 'professeur',
            'departement' => 'Informatique',
        ]);

        // Étudiant
        $etu = User::create([
            'nom_complet' => 'Alice Etudiante',
            'email' => 'etu@example.com',
            'mot_de_passe' => Hash::make('password'),
            'role' => 'etudiant',
            'departement' => 'Informatique',
        ]);

        // Échantillon de cours
        $cours = Cours::create([
            'nom_cours' => 'Programmation Web',
            'prof_id' => $prof->id,
            'salle' => 'A-101',
            'date' => now()->toDateString(),
            'heure_debut' => '09:00:00',
            'heure_fin' => '11:00:00',
            'semestre' => 'S1',
        ]);
        $cours->etudiants()->sync([$etu->id]);
    }
}
