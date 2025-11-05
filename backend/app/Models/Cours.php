<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_cours',
        'prof_id',
        'salle',
        'date',
        'heure_debut',
        'heure_fin',
        'semestre',
    ];

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prof_id');
    }

    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'etudiants_cours', 'cours_id', 'etudiant_id');
    }
}
