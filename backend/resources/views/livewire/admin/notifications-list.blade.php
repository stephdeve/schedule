<div class="bg-white rounded-2xl shadow-md overflow-hidden">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Historique des Notifications</h2>
            <p class="text-gray-600 text-sm">Suivi des communications envoyées</p>
        </div>
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2">
                <input type="checkbox" wire:model="onlyUnread">
                <span>Non lues seulement</span>
            </label>
        </div>
    </div>

    <div class="p-6">
        <div class="space-y-4">
            @forelse($notifications as $n)
            <div class="border-l-4 border-blue-500 pl-4 py-2">
                <div class="flex items-start">
                    <div class="mr-4 mt-1">
                        @if($n->type === 'email')
                        <span class="material-icons-round text-blue-500">email</span>
                        @elseif($n->type === 'sms')
                        <span class="material-icons-round text-green-500">sms</span>
                        @else
                        <span class="material-icons-round text-purple-500">notifications</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">{{ $n->message }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            {{ $n->created_at->format('d/m/Y H:i') }} • 
                            @if(!$n->lu)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Non lue
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Lue
                            </span>
                            @endif
                        </div>
                    </div>
                    @if(!$n->lu)
                    <button wire:click="markAsRead({{ $n->id }})" class="text-sm text-blue-600 hover:text-blue-800">
                        Marquer comme lue
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <span class="material-icons-round text-4xl text-gray-300 mb-2">notifications_off</span>
                <p>Aucune notification</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $notifications->links() }}
    </div>
</div>
