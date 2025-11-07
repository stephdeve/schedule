<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cours;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use App\Mail\CoursPublie;

class CoursTable extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    // Advanced filters
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public string $salle = '';
    public ?int $profId = null;
    // Sorting
    public string $sortField = 'date';
    public string $sortDirection = 'asc';

    // Modal state & form
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editId = null;
    public array $form = [
        'nom_cours' => '',
        'prof_id' => null,
        'salle' => '',
        'date' => '',
        'heure_debut' => '',
        'heure_fin' => '',
        'semestre' => '',
        'etudiant_ids' => [],
    ];

    // Bulk selection
    public array $selected = [];
    public bool $selectAll = false;

    protected $updatesQueryString = ['search' => ['except' => '']];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void { $this->resetPage(); }
    public function updatedSalle(): void { $this->resetPage(); }
    public function updatedProfId(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }
    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->rows->getCollection()->pluck('id')->all();
        } else {
            $this->selected = [];
        }
    }

    public function delete(int $id): void
    {
        $cours = Cours::findOrFail($id);
        $cours->delete();
        $this->dispatch('toast', message: 'Cours supprimé');
    }

    public function publish(int $id): void
    {
        $cours = Cours::with(['professeur','etudiants'])->findOrFail($id);
        $destinataires = collect([$cours->professeur])->merge($cours->etudiants);
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
        $this->dispatch('toast', message: 'Cours publié et notifications envoyées');
    }

    public function publishSelected(): void
    {
        if (empty($this->selected)) {
            $this->dispatch('toast', message: 'Aucun cours sélectionné');
            return;
        }
        foreach ($this->selected as $id) {
            $this->publish((int) $id);
        }
        $this->selected = [];
        $this->selectAll = false;
        $this->dispatch('toast', message: 'Emploi du temps publié (cours sélectionnés)');
    }

    public function setSort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $c = Cours::with('etudiants')->findOrFail($id);
        $this->form = [
            'nom_cours' => (string) $c->nom_cours,
            'prof_id' => (int) $c->prof_id,
            'salle' => (string) $c->salle,
            'date' => (string) $c->date,
            'heure_debut' => (string) $c->heure_debut,
            'heure_fin' => (string) $c->heure_fin,
            'semestre' => (string) $c->semestre,
            'etudiant_ids' => $c->etudiants()->pluck('users.id')->all(),
        ];
        $this->isEditing = true;
        $this->editId = $c->id;
        $this->showModal = true;
    }

    protected function rules(): array
    {
        return [
            'form.nom_cours' => ['required','string','max:255'],
            'form.prof_id' => ['required','integer','exists:users,id'],
            'form.salle' => ['required','string','max:255'],
            'form.date' => ['required','date'],
            'form.heure_debut' => ['required'],
            'form.heure_fin' => ['required'],
            'form.semestre' => ['required','string','max:50'],
            'form.etudiant_ids' => ['array'],
            'form.etudiant_ids.*' => ['integer','exists:users,id'],
        ];
    }

    public function save(): void
    {
        $this->validate();
        // Conflict check
        $date = (string) $this->form['date'];
        $debut = (string) $this->form['heure_debut'];
        $fin = (string) $this->form['heure_fin'];
        $prof = (int) $this->form['prof_id'];
        $salle = (string) $this->form['salle'];
        $ignore = $this->isEditing ? $this->editId : null;
        if ($this->hasConflict($date, $debut, $fin, $prof, $salle, $ignore)) {
            $this->dispatch('toast', message: 'Conflit détecté: salle ou professeur indisponible à ce créneau');
            return;
        }

        if ($this->isEditing && $this->editId) {
            $cours = Cours::findOrFail($this->editId);
            $payload = $this->form;
            $etudiants = $payload['etudiant_ids'] ?? [];
            unset($payload['etudiant_ids']);
            $cours->update($payload);
            $cours->etudiants()->sync($etudiants);
            $this->dispatch('toast', message: 'Cours mis à jour');
        } else {
            $payload = $this->form;
            $etudiants = $payload['etudiant_ids'] ?? [];
            unset($payload['etudiant_ids']);
            $cours = Cours::create($payload);
            if (!empty($etudiants)) { $cours->etudiants()->sync($etudiants); }
            $this->dispatch('toast', message: 'Cours créé');
        }
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->form = [
            'nom_cours' => '',
            'prof_id' => null,
            'salle' => '',
            'date' => '',
            'heure_debut' => '',
            'heure_fin' => '',
            'semestre' => '',
            'etudiant_ids' => [],
        ];
    }

    private function hasConflict(string $date, string $debut, string $fin, int $profId, string $salle, ?int $ignoreId = null): bool
    {
        return \App\Models\Cours::query()
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

    public function getRowsProperty()
    {
        return Cours::query()
            ->with('professeur')
            ->when($this->search !== '', function (Builder $q) {
                $q->where('nom_cours', 'like', '%'.$this->search.'%');
            })
            ->when($this->salle !== '', fn (Builder $q) => $q->where('salle', 'like', '%'.$this->salle.'%'))
            ->when(!empty($this->profId), fn (Builder $q) => $q->where('prof_id', $this->profId))
            ->when(!empty($this->dateFrom), fn (Builder $q) => $q->whereDate('date', '>=', $this->dateFrom))
            ->when(!empty($this->dateTo), fn (Builder $q) => $q->whereDate('date', '<=', $this->dateTo))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        $professeurs = User::where('role', 'professeur')->orderBy('nom_complet')->get(['id','nom_complet']);
        $etudiants = User::where('role', 'etudiant')->orderBy('nom_complet')->get(['id','nom_complet']);
        return view('livewire.admin.cours-table', [
            'cours' => $this->rows,
            'professeurs' => $professeurs,
            'etudiants' => $etudiants,
        ]);
    }
}
