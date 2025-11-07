@php use App\Helpers\DepartmentColors; @endphp

<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Gestion des Utilisateurs</h2>
            <p class="text-gray-600 text-sm">Administrez les étudiants, professeurs et administrateurs</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.debounce.400ms="search"
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
                       placeholder="Rechercher...">
                <span class="absolute left-3 top-2.5 material-icons-round text-gray-400">search</span>
            </div>
            <select wire:model="role" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tous les rôles</option>
                <option value="admin">Administrateurs</option>
                <option value="professeur">Professeurs</option>
                <option value="etudiant">Étudiants</option>
            </select>
            <select wire:model="perPage" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2 px-4 py-2 rounded-lg transition duration-300">
                <span class="material-icons-round">add</span>
                <span>Nouvel utilisateur</span>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                    <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                    <th class="py-4 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-6 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-10 h-10 mr-3"></div>
                            <div class="font-medium text-gray-900">{{ $u->nom_complet }}</div>
                        </div>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-gray-600">{{ $u->email }}</td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        @php
                            $roleColors = [
                                'admin' => 'bg-blue-100 text-blue-800',
                                'professeur' => 'bg-purple-100 text-purple-800',
                                'etudiant' => 'bg-green-100 text-green-800'
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleColors[$u->role] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($u->role) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        @if($u->departement)
                            @php $deptColor = DepartmentColors::getColor($u->departement); @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $deptColor['bg'] }} {{ $deptColor['text'] }}">
                                {{ $u->departement }}
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-right">
                        <div class="flex justify-end gap-2">
                            <button wire:click="openEdit({{ $u->id }})" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700" title="Éditer">
                                <span class="material-icons-round text-sm">edit</span>
                            </button>
                            <button wire:click="delete({{ $u->id }})" onclick="return confirm('Supprimer cet utilisateur ?')" class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700" title="Supprimer">
                                <span class="material-icons-round text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 px-6 text-center text-gray-500">
                        <span class="material-icons-round text-4xl text-gray-300 mb-2">group_off</span>
                        <p>Aucun utilisateur trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-40" style="background: rgba(17,24,39,.5)"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
                <form wire:submit.prevent="save">
                    <div class="p-4 border-b font-medium">{{ $isEditing ? 'Éditer un utilisateur' : 'Nouvel utilisateur' }}</div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="text-sm text-gray-600">Nom complet</label>
                            <input type="text" wire:model.defer="form.nom_complet" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.nom_complet') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Email</label>
                            <input type="email" wire:model.defer="form.email" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.email') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-sm text-gray-600">Rôle</label>
                                <select wire:model.defer="form.role" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="admin">Admin</option>
                                    <option value="professeur">Professeur</option>
                                    <option value="etudiant">Étudiant</option>
                                </select>
                                @error('form.role') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Département</label>
                                <input type="text" wire:model.defer="form.departement" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('form.departement') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Mot de passe {{ $isEditing ? '(laisser vide pour ne pas changer)' : '' }}</label>
                            <input type="password" wire:model.defer="form.password" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.password') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="p-4 border-t flex items-center justify-end gap-2">
                        <button type="button" class="inline-flex items-center rounded bg-gray-200 text-gray-900 px-3 py-2 text-sm" wire:click="closeModal">Annuler</button>
                        <button type="submit" class="inline-flex items-center rounded bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-sm">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
