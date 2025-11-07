<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Schedule Admin' }}</title>
    @vite(['resources/css/app.css','resources/css/theme.css','resources/js/app.js'])
    @livewireStyles
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        /* Desktop collapse for sidebar */
        #sidebar { overflow: hidden; }
        @media (min-width: 768px) {
            #sidebar { width: 18rem; }
            #sidebar.collapsed { width: 0; border-width: 0; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="hidden md:flex flex-col w-72 bg-white/80 backdrop-blur border-r border-gray-200 transition-all duration-300 z-40">
            <div class="px-6 py-6 border-b">
                <a href="/admin" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-sm"></div>
                    <div>
                        <div class="text-lg font-bold">Schedule</div>
                        <div class="text-xs text-gray-500">Administration</div>
                    </div>
                </a>
            </div>
            <nav class="p-4 flex-1">
                <ul class="space-y-1 text-sm">
                    <li>
                        <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <span class="material-icons-round">dashboard</span>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <span class="material-icons-round">book</span>
                            <span>Cours</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <span class="material-icons-round">people</span>
                            <span>Utilisateurs</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <span class="material-icons-round">notifications</span>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li>
                        <a href="/api/documentation" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <span class="material-icons-round">description</span>
                            <span>API Docs</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="p-4 border-t text-xs text-gray-500">© {{ date('Y') }} UnivTime</div>
        </aside>
        <!-- Mobile overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-30 md:hidden hidden" onclick="toggleSidebar(false)"></div>

        <!-- Main -->
        <div class="flex-1 min-w-0 flex flex-col">
            <!-- Topbar -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-gray-200">
                <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <button class="md:hidden inline-flex items-center justify-center rounded-lg border border-gray-300 p-2" onclick="toggleSidebar()" aria-label="Ouvrir le menu">
                            <span class="material-icons-round">menu</span>
                        </button>
                        <button id="collapse-btn" class="hidden md:inline-flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm" onclick="toggleSidebar()" aria-label="Replier la navigation">
                            <span id="collapse-icon" class="material-icons-round">chevron_left</span>
                            <span id="collapse-label" class="hidden lg:inline">Replier</span>
                        </button>
                        <div class="font-semibold text-gray-800">{{ $header ?? 'Tableau de bord' }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="relative inline-flex items-center justify-center w-10 h-10 rounded-full text-gray-700 hover:bg-gray-100" aria-label="Notifications">
                            <span class="material-icons-round">notifications</span>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <details class="relative">
                            <summary class="list-none flex items-center gap-2 cursor-pointer select-none">
                                <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                                <span class="hidden md:inline text-sm font-medium text-gray-700">Admin</span>
                                <span class="material-icons-round text-gray-500">expand_more</span>
                            </summary>
                            <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-md py-2">
                                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-50">Profil</a>
                                <div class="border-t my-2"></div>
                                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Se déconnecter ?')">
                                    @csrf
                                    <button class="w-full text-left block px-4 py-2 text-sm hover:bg-gray-50">Déconnexion</button>
                                </form>
                            </div>
                        </details>
                    </div>
                </div>
            </header>
            <!-- Content -->
            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    @livewireScripts
    <!-- Toasts -->
    <div id="toast-root" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>
    <script>
        function setCollapseButtonState() {
            const sb = document.getElementById('sidebar');
            const icon = document.getElementById('collapse-icon');
            const label = document.getElementById('collapse-label');
            if (!sb || !icon || !label) return;
            const collapsed = sb.classList.contains('collapsed');
            icon.textContent = collapsed ? 'chevron_right' : 'chevron_left';
            label.textContent = collapsed ? 'Déplier' : 'Replier';
        }
        function toggleSidebar(force) {
            const sb = document.getElementById('sidebar');
            const ov = document.getElementById('sidebar-overlay');
            if (!sb) return;
            const isDesktop = window.matchMedia('(min-width: 768px)').matches;
            if (isDesktop) {
                const collapse = (typeof force === 'boolean') ? force : !sb.classList.contains('collapsed');
                sb.classList.toggle('collapsed', collapse);
                try { localStorage.setItem('sidebarCollapsed', collapse ? '1' : '0'); } catch (e) {}
                setCollapseButtonState();
            } else {
                const willShow = sb.classList.contains('hidden');
                sb.classList.toggle('hidden');
                if (ov) ov.classList.toggle('hidden');
            }
        }
        // Restore desktop collapse state
        (function(){
            try {
                const val = localStorage.getItem('sidebarCollapsed');
                if (val === '1') {
                    const sb = document.getElementById('sidebar');
                    if (sb) sb.classList.add('collapsed');
                }
            } catch (e) {}
            setCollapseButtonState();
        })();
        (function () {
            function showToast(message, type = 'success') {
                const root = document.getElementById('toast-root');
                if (!root) return;
                const el = document.createElement('div');
                el.className = 'pointer-events-auto rounded-md shadow bg-gray-900 text-white px-4 py-2 text-sm transition-opacity';
                el.textContent = message;
                root.appendChild(el);
                setTimeout(() => { el.style.opacity = '0'; }, 2600);
                setTimeout(() => { el.remove(); }, 3100);
            }

            // Listen Livewire v3 browser events
            window.addEventListener('toast', (e) => {
                const msg = (e && e.detail && e.detail.message) ? e.detail.message : 'Action effectuée';
                showToast(msg);
            });

            document.addEventListener('livewire:init', () => {
                if (window.Livewire && Livewire.on) {
                    Livewire.on('toast', (payload) => {
                        const msg = (typeof payload === 'string') ? payload : (payload && payload.message) ? payload.message : 'Action effectuée';
                        showToast(msg);
                    });
                }
            });
        })();
    </script>
</body>
</html>
