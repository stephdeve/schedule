<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etudiants_cours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cours_id')->constrained('cours')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['etudiant_id', 'cours_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etudiants_cours');
    }
};
