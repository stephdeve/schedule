<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class UsersTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role = '';
    public int $perPage = 10;

    // Modal state & form
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editId = null;
    public array $form = [
        'nom_complet' => '',
        'email' => '',
        'role' => 'etudiant',
        'departement' => '',
        'password' => '',
    ];

    protected $updatesQueryString = ['search' => ['except' => ''], 'role' => ['except' => '']];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRole(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
        $this->dispatch('toast', message: 'Utilisateur supprimé');
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
        $u = User::findOrFail($id);
        $this->form = [
            'nom_complet' => (string) $u->nom_complet,
            'email' => (string) $u->email,
            'role' => (string) $u->role,
            'departement' => (string) ($u->departement ?? ''),
            'password' => '',
        ];
        $this->isEditing = true;
        $this->editId = $u->id;
        $this->showModal = true;
    }

    protected function rules(): array
    {
        return [
            'form.nom_complet' => ['required','string','max:255'],
            'form.email' => [
                'required','email',
                Rule::unique('users','email')->ignore($this->editId),
            ],
            'form.role' => ['required', Rule::in(['admin','professeur','etudiant'])],
            'form.departement' => ['nullable','string','max:255'],
            'form.password' => $this->isEditing
                ? ['nullable','min:6']
                : ['required','min:6'],
        ];
    }

    public function save(): void
    {
        $this->validate();
        if ($this->isEditing && $this->editId) {
            $u = User::findOrFail($this->editId);
            $payload = [
                'nom_complet' => $this->form['nom_complet'],
                'email' => $this->form['email'],
                'role' => $this->form['role'],
                'departement' => $this->form['departement'] ?: null,
            ];
            if (!empty($this->form['password'])) {
                // mot_de_passe has cast "hashed"
                $payload['mot_de_passe'] = $this->form['password'];
            }
            $u->update($payload);
            $this->dispatch('toast', message: 'Utilisateur mis à jour');
        } else {
            $u = User::create([
                'nom_complet' => $this->form['nom_complet'],
                'email' => $this->form['email'],
                'role' => $this->form['role'],
                'departement' => $this->form['departement'] ?: null,
                'mot_de_passe' => $this->form['password'], // hashed via cast
            ]);
            $this->dispatch('toast', message: 'Utilisateur créé');
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
            'nom_complet' => '',
            'email' => '',
            'role' => 'etudiant',
            'departement' => '',
            'password' => '',
        ];
    }

    public function getRowsProperty()
    {
        return User::query()
            ->when($this->search !== '', function (Builder $q) {
                $q->where(function($q){
                    $q->where('nom_complet', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->role !== '', fn (Builder $q) => $q->where('role', $this->role))
            ->orderByDesc('id')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.users-table', [
            'users' => $this->rows,
        ]);
    }
}
