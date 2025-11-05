<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom_complet',
        'email',
        'mot_de_passe',
        'role',
        'departement',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mot_de_passe' => 'hashed',
        ];
    }

    /**
     * Override to support custom password column name.
     */
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * Courses taught by the professor (when role = professeur)
     */
    public function coursEnseignes(): HasMany
    {
        return $this->hasMany(Cours::class, 'prof_id');
    }

    /**
     * Courses a student is enrolled in (when role = etudiant)
     */
    public function coursInscrits(): BelongsToMany
    {
        return $this->belongsToMany(Cours::class, 'etudiants_cours', 'etudiant_id', 'cours_id');
    }

    /**
     * Notifications received by the user.
     */
    public function notificationsDb(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}

