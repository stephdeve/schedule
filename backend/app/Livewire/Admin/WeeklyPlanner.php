<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Cours;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\CoursPublie;

class WeeklyPlanner extends Component
{
    public string $weekStart = ''; // YYYY-MM-DD (Monday)
    public array $timeSlots = [];

    public function mount(): void
    {
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        if (!isset($this->weekStart) || $this->weekStart === '') {
            $this->weekStart = $monday;
        }
        // Generate 30-minute time slots from 08:00 to 18:00
        $slots = [];
        for ($h = 8; $h <= 18; $h++) {
            foreach (['00','30'] as $m) {
                $time = sprintf('%02d:%s', $h, $m);
                $slots[] = $time;
            }
        }
        $this->timeSlots = $slots;
    }

    public function getDaysProperty(): array
    {
        $start = Carbon::parse($this->weekStart);
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $start->copy()->addDays($i);
            $days[] = [
                'date' => $d->toDateString(),
                'label' => $d->format('D d/m'),
            ];
        }
        return $days;
    }

    public function getWeekCoursesProperty()
    {
        $start = Carbon::parse($this->weekStart);
        $end = $start->copy()->addDays(6)->toDateString();
        return Cours::with('professeur')
            ->whereBetween('date', [$start->toDateString(), $end])
            ->orderBy('date')
            ->orderBy('heure_debut')
            ->get();
    }

    public function getByKeyProperty(): array
    {
        $map = [];
        foreach ($this->weekCourses as $c) {
            $key = $c->date.' '.substr((string) $c->heure_debut, 0, 5);
            $map[$key] = $map[$key] ?? [];
            $map[$key][] = $c;
        }
        return $map;
    }

    public function prevWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->subDays(7)->toDateString();
    }

    public function nextWeek(): void
    {
        $this->weekStart = Carbon::parse($this->weekStart)->addDays(7)->toDateString();
    }

    public function thisWeek(): void
    {
        $this->weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
    }

    #[On('planner:move')]
    public function onPlannerMove(array $payload): void
    {
        $courseId = (int) ($payload['courseId'] ?? 0);
        $date = (string) ($payload['date'] ?? '');
        $time = (string) ($payload['time'] ?? '');
        if ($courseId && $date && $time) {
            $this->moveCourse($courseId, $date, $time);
        }
    }

    public function moveCourse(int $courseId, string $date, string $time): void
    {
        $cours = Cours::findOrFail($courseId);
        $duration = Carbon::parse($cours->heure_debut)->diffInMinutes(Carbon::parse($cours->heure_fin));
        $newStart = Carbon::parse($date.' '.$time);
        $newEnd = $newStart->copy()->addMinutes($duration);

        if ($this->hasConflict($date, $newStart->format('H:i:s'), $newEnd->format('H:i:s'), (int) $cours->prof_id, (string) $cours->salle, $cours->id)) {
            $this->dispatch('toast', message: 'Conflit détecté: déplacement impossible');
            return;
        }

        $cours->update([
            'date' => $date,
            'heure_debut' => $newStart->format('H:i:s'),
            'heure_fin' => $newEnd->format('H:i:s'),
        ]);
        $this->dispatch('toast', message: 'Cours déplacé');
    }

    public function publishWeek(): void
    {
        $start = Carbon::parse($this->weekStart);
        $end = $start->copy()->addDays(6);
        $coursList = Cours::with(['professeur','etudiants'])
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();
        $ids = $coursList->pluck('id')->all();
        if (empty($ids)) {
            $this->dispatch('toast', message: 'Aucun cours dans la semaine affichée');
            return;
        }

        // Try API batch publish first (requires Sanctum token in session)
        try {
            $token = session('api_token');
            if ($token) {
                $resp = Http::withToken($token)->post(url('/api/emplois/publier'), [
                    'cours_ids' => $ids,
                    'semaine' => $start->isoWeekYear.'-W'.str_pad((string) $start->isoWeek, 2, '0', STR_PAD_LEFT),
                    'message' => 'Publication de l\'emploi du temps',
                ]);
                if ($resp->successful()) {
                    $this->dispatch('toast', message: 'Emploi du temps publié (API)');
                    return;
                }
            }
        } catch (\Throwable $e) {
            // Fallback below
        }

        // Fallback local: créer notifications + emails pour chaque cours
        foreach ($coursList as $cours) {
            $destinataires = collect([$cours->professeur])->merge($cours->etudiants)->filter()->unique('id');
            foreach ($destinataires as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'message' => 'Publication du cours: '.$cours->nom_cours.' le '.$cours->date.' ('.$cours->heure_debut.' - '.$cours->heure_fin.') en salle '.$cours->salle,
                    'type' => 'app',
                    'lu' => false,
                ]);
                if (!empty($user->email)) {
                    try { Mail::to($user->email)->send(new CoursPublie($cours, $user)); } catch (\Throwable $e) { /* ignore */ }
                }
            }
        }
        $this->dispatch('toast', message: 'Emploi du temps publié (local)');
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

    public function render()
    {
        return view('livewire.admin.weekly-planner', [
            'days' => $this->days,
            'timeSlots' => $this->timeSlots,
            'byKey' => $this->byKey,
        ]);
    }
}
