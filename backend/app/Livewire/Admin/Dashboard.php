<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Cours;
use App\Models\Notification;

if (class_exists(\Livewire\Component::class)) {
    class Dashboard extends \Livewire\Component
    {
        public array $stats = [];
        public $professeurs;
        public $etudiants;
        public $cours;
        public $notifications;

        public function mount(): void
        {
            $this->stats = [
                'professeurs' => User::where('role', 'professeur')->count(),
                'etudiants' => User::where('role', 'etudiant')->count(),
                'cours' => Cours::count(),
                'notifications' => Notification::count(),
            ];

            $this->professeurs = User::where('role', 'professeur')->latest()->limit(10)->get();
            $this->etudiants = User::where('role', 'etudiant')->latest()->limit(10)->get();
            $this->cours = Cours::with('professeur')->latest('date')->limit(10)->get();
            $this->notifications = Notification::latest()->limit(10)->get();
        }

        public function render()
        {
            return view('livewire.admin.dashboard');
        }
    }
} else {
    // Fallback neutre pour ne pas casser l'autoload
    class Dashboard
    {
        public function render()
        {
            return view('livewire.admin.dashboard');
        }
    }
}
