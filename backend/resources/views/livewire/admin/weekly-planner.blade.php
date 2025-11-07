<div class="bg-white rounded-2xl shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Builder d'Emploi du Temps</h2>
        <div class="flex items-center gap-2">
            <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200" wire:click="prevWeek">
                <span class="material-icons-round">chevron_left</span>
            </button>
            <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200" wire:click="thisWeek">
                <span class="material-icons-round">today</span>
            </button>
            <button class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200" wire:click="nextWeek">
                <span class="material-icons-round">chevron_right</span>
            </button>
            <button class="bg-blue-600 hover:bg-blue-700 text-white flex items-center gap-2 px-4 py-2 rounded-lg transition duration-300">
                <span class="material-icons-round">send</span>
                <span>Publier l'emploi du temps</span>
            </button>
        </div>
    </div>

    <div class="overflow-auto border border-gray-200 rounded-xl">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 w-24 text-left text-sm font-medium text-gray-500 uppercase">Heure</th>
                    @foreach($days as $d)
                        <th class="p-4 text-left text-sm font-medium text-gray-500 uppercase">{{ $d['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $slot)
                    <tr class="border-t border-gray-200">
                        <td class="p-4 text-gray-500 font-medium">{{ $slot }}</td>
                        @foreach($days as $d)
                            @php $key = $d['date'].' '.substr($slot,0,5); @endphp
                            <td class="p-2 align-top">
                                <div class="min-h-[80px] p-2 bg-gray-50 rounded-lg dropzone"
                                     data-date="{{ $d['date'] }}" data-time="{{ $slot }}">
                                    @foreach(($byKey[$key] ?? []) as $c)
                                        @php
                                            $subjectColors = [
                                                'informatique' => 'bg-blue-100 border-blue-300',
                                                'mathématiques' => 'bg-green-100 border-green-300',
                                                'physique' => 'bg-purple-100 border-purple-300',
                                                'chimie' => 'bg-amber-100 border-amber-300'
                                            ];
                                            $color = $subjectColors[strtolower($c->nom_cours)] ?? 'bg-gray-100 border-gray-300';
                                        @endphp
                                        <div class="draggable cursor-move {{ $color }} border rounded-lg p-3 mb-2 shadow-sm" 
                                             draggable="true" data-course-id="{{ $c->id }}">
                                            <div class="font-medium text-gray-900">{{ $c->nom_cours }}</div>
                                            <div class="text-xs text-gray-600">{{ $c->professeur->nom_complet ?? '-' }} • {{ $c->salle }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        // Existing drag & drop implementation remains
    </script>
</div>
