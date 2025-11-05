<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-gray-500 text-sm">Professeurs</div>
            <div class="text-2xl font-semibold">{{ $stats['professeurs'] ?? 0 }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-gray-500 text-sm">Étudiants</div>
            <div class="text-2xl font-semibold">{{ $stats['etudiants'] ?? 0 }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-gray-500 text-sm">Cours</div>
            <div class="text-2xl font-semibold">{{ $stats['cours'] ?? 0 }}</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-gray-500 text-sm">Notifications</div>
            <div class="text-2xl font-semibold">{{ $stats['notifications'] ?? 0 }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow">
            <div class="p-4 border-b font-medium">Derniers cours</div>
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2 pr-4">Cours</th>
                            <th class="py-2 pr-4">Professeur</th>
                            <th class="py-2 pr-4">Date</th>
                            <th class="py-2 pr-4">Heure</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cours as $c)
                            <tr class="border-t">
                                <td class="py-2 pr-4">{{ $c->nom_cours }}</td>
                                <td class="py-2 pr-4">{{ $c->professeur->nom_complet ?? '-' }}</td>
                                <td class="py-2 pr-4">{{ $c->date }}</td>
                                <td class="py-2 pr-4">{{ $c->heure_debut }} - {{ $c->heure_fin }}</td>
                            </tr>
                        @empty
                            <tr><td class="py-4 text-gray-400" colspan="4">Aucun cours</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded shadow">
            <div class="p-4 border-b font-medium">Dernières notifications</div>
            <div class="p-4">
                <ul class="space-y-2">
                    @forelse($notifications as $n)
                        <li class="text-sm">
                            <span class="font-medium">#{{ $n->id }}</span> - {{ $n->message }}
                            <span class="ml-2 px-2 py-0.5 text-xs rounded bg-gray-100">{{ $n->type }}</span>
                        </li>
                    @empty
                        <li class="text-gray-400">Aucune notification</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="bg-white rounded shadow">
            <div class="p-4 border-b font-medium">Professeurs (10 derniers)</div>
            <div class="p-4">
                <ul class="space-y-1 text-sm">
                    @forelse($professeurs as $p)
                        <li>{{ $p->nom_complet }} <span class="text-gray-400">— {{ $p->email }}</span></li>
                    @empty
                        <li class="text-gray-400">Aucun professeur</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="bg-white rounded shadow">
            <div class="p-4 border-b font-medium">Étudiants (10 derniers)</div>
            <div class="p-4">
                <ul class="space-y-1 text-sm">
                    @forelse($etudiants as $e)
                        <li>{{ $e->nom_complet }} <span class="text-gray-400">— {{ $e->email }}</span></li>
                    @empty
                        <li class="text-gray-400">Aucun étudiant</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
