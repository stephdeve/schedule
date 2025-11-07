<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cours;
use App\Models\Notification;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'professeurs' => User::where('role', 'professeur')->count(),
            'etudiants' => User::where('role', 'etudiant')->count(),
            'cours' => Cours::count(),
            'notifications' => Notification::count(),
        ];

        $professeurs = User::where('role', 'professeur')->latest()->limit(10)->get();
        $etudiants = User::where('role', 'etudiant')->latest()->limit(10)->get();
        $cours = Cours::with('professeur')->latest('date')->limit(10)->get();
        $notifications = Notification::latest()->limit(10)->get();

        return view('admin.dashboard', compact('stats','professeurs','etudiants','cours','notifications'));
    }
}
