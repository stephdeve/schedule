<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Gestion des Cours</h2>
            <p class="text-gray-600 text-sm">Organisez le planning universitaire</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" wire:model.debounce.400ms="search" 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64"
                       placeholder="Rechercher un cours...">
                <span class="absolute left-3 top-2.5 material-icons-round text-gray-400">search</span>
            </div>
            <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2 px-4 py-2 rounded-lg transition duration-300">
                <span class="material-icons-round">add</span>
                <span>Nouveau cours</span>
            </button>
        </div>
    </div>

    <div class="p-6">
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                <span class="text-sm">Informatique</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-green-500"></div>
                <span class="text-sm">Mathématiques</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-purple-500"></div>
                <span class="text-sm">Physique</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-amber-500"></div>
                <span class="text-sm">Chimie</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professeur</th>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salle</th>
                        <th class="py-4 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cours as $c)
                    @php
                        // Map subject to explicit Tailwind classes (safe for purge)
                        $subjectDot = [
                            'informatique' => 'bg-blue-500',
                            'mathématiques' => 'bg-green-500',
                            'physique' => 'bg-purple-500',
                            'chimie' => 'bg-amber-500',
                        ];
                        $subKey = strtolower($c->nom_cours);
                        $dotClass = $subjectDot[$subKey] ?? 'bg-gray-400';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-6 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-3 {{ $dotClass }}"></div>
                                <div class="font-medium text-gray-900">{{ $c->nom_cours }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-gray-600">{{ $c->professeur->nom_complet ?? '-' }}</td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <div class="text-gray-900">{{ $c->date }}</div>
                            <div class="text-gray-600 text-sm">{{ $c->heure_debut }} - {{ $c->heure_fin }}</div>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $c->salle }}
                            </span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="openEdit({{ $c->id }})" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">
                                    <span class="material-icons-round text-sm">edit</span>
                                </button>
                                <button wire:click="publish({{ $c->id }})" class="p-2 rounded-lg bg-green-100 hover:bg-green-200 text-green-700">
                                    <span class="material-icons-round text-sm">send</span>
                                </button>
                                <button wire:click="delete({{ $c->id }})" onclick="return confirm('Supprimer ce cours ?')" class="p-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700">
                                    <span class="material-icons-round text-sm">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 px-6 text-center text-gray-500">
                            <span class="material-icons-round text-4xl text-gray-300 mb-2">book</span>
                            <p>Aucun cours trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $cours->links() }}
    </div>

    <!-- Modal Create/Edit -->
    @if($showModal)
        <div class="fixed inset-0 z-40" style="background: rgba(17,24,39,.5)"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">
                <form wire:submit.prevent="save">
                    <div class="p-4 border-b font-medium">{{ $isEditing ? 'Éditer un cours' : 'Nouveau cours' }}</div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm text-gray-600">Nom du cours</label>
                            <input type="text" wire:model.defer="form.nom_cours" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.nom_cours') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Professeur</label>
                            <select wire:model.defer="form.prof_id" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Choisir --</option>
                                @foreach($professeurs as $p)
                                    <option value="{{ $p->id }}">{{ $p->nom_complet }}</option>
                                @endforeach
                            </select>
                            @error('form.prof_id') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Salle</label>
                            <input type="text" wire:model.defer="form.salle" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.salle') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Date</label>
                            <input type="date" wire:model.defer="form.date" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.date') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Heure début</label>
                            <input type="time" wire:model.defer="form.heure_debut" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.heure_debut') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Heure fin</label>
                            <input type="time" wire:model.defer="form.heure_fin" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.heure_fin') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Semestre</label>
                            <input type="text" wire:model.defer="form.semestre" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('form.semestre') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-sm text-gray-600">Étudiants</label>
                            <select multiple wire:model.defer="form.etudiant_ids" class="mt-1 w-full border rounded px-3 py-2 h-32 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($etudiants as $e)
                                    <option value="{{ $e->id }}">{{ $e->nom_complet }}</option>
                                @endforeach
                            </select>
                            @error('form.etudiant_ids') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
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
