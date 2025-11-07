<div class="container mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Tableau de bord</h1>
        <p class="text-gray-600">Aperçu général du système Schedule</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <span class="material-icons-round text-blue-600">school</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Étudiants</p>
                    <p class="text-2xl font-bold">{{ $stats['etudiants'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <span class="material-icons-round text-green-600">person</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Professeurs</p>
                    <p class="text-2xl font-bold">{{ $stats['professeurs'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <span class="material-icons-round text-purple-600">book</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Cours</p>
                    <p class="text-2xl font-bold">{{ $stats['cours'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-md p-6 border-l-4 border-amber-500">
            <div class="flex items-center">
                <div class="p-3 bg-amber-100 rounded-lg mr-4">
                    <span class="material-icons-round text-amber-600">notifications</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Notifications</p>
                    <p class="text-2xl font-bold">{{ $stats['notifications'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Planning Section -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Planning Hebdomadaire</h2>
            <div class="flex gap-2">
                <button class="p-2 rounded-full bg-gray-100 hover:bg-gray-200">
                    <span class="material-icons-round">chevron_left</span>
                </button>
                <button class="p-2 rounded-full bg-gray-100 hover:bg-gray-200">
                    <span class="material-icons-round">today</span>
                </button>
                <button class="p-2 rounded-full bg-gray-100 hover:bg-gray-200">
                    <span class="material-icons-round">chevron_right</span>
                </button>
            </div>
        </div>
        @livewire('admin.weekly-planner')
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Derniers cours</h2>
            @livewire('admin.cours-table')
        </div>
        
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Notifications récentes</h2>
            @livewire('admin.notifications-list')
        </div>
    </div>
    
    <div class="mt-8">
        @livewire('admin.users-table')
    </div>
</div>
