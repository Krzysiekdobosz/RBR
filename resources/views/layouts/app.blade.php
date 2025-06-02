<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ToDo App')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        
        .task-priority-low { @apply border-l-4 border-green-400; }
        .task-priority-medium { @apply border-l-4 border-yellow-400; }
        .task-priority-high { @apply border-l-4 border-red-400; }
        
        .task-status-done { @apply opacity-60 line-through; }
    </style>
    
    <script>
        // Inicjalizacja Alpine.js przed jego załadowaniem
        document.addEventListener('alpine:init', () => {
            Alpine.store('loading', {
                show: false
            });
        });
    </script>
</head>
<body class="h-full">
    <div id="app" class="min-h-full">
        @auth
            <!-- Nawigacja dla zalogowanych użytkowników -->
            <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ mobileOpen: false }">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Logo i menu główne -->
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                                    <i class="fas fa-tasks mr-2"></i>ToDo App
                                </a>
                            </div>
                            
                            <!-- Menu desktop -->
                            <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                                <a href="{{ route('dashboard') }}" 
                                   class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    <i class="fas fa-home mr-2"></i>Dashboard
                                </a>
                                <a href="{{ route('tasks.index') }}" 
                                   class="{{ request()->routeIs('tasks.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    <i class="fas fa-list mr-2"></i>Zadania
                                </a>
                            </div>
                        </div>
                        
                        <!-- Menu użytkownika desktop -->
                        <div class="hidden sm:ml-6 sm:flex sm:items-center">
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <div class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                        <i class="fas fa-user-circle mr-2 text-lg"></i>
                                        {{ auth()->user()->name }}
                                        <i class="fas fa-chevron-down ml-2 text-xs"></i>
                                    </div>
                                </button>
                                
                                <!-- Dropdown menu -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     x-cloak
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <div class="py-1">
                                        <a href="{{ route('profile') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i>Profil
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Wyloguj
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Przycisk menu mobilnego -->
                        <div class="flex items-center sm:hidden">
                            <button @click="mobileOpen = !mobileOpen"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Menu mobilne -->
                <div x-show="mobileOpen" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="mobileOpen = false"
                     x-cloak
                     class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('dashboard') }}" 
                           class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('tasks.index') }}" 
                           class="{{ request()->routeIs('tasks.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800' }} block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
                            <i class="fas fa-list mr-2"></i>Zadania
                        </a>
                    </div>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-circle text-gray-400 text-3xl"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('profile') }}" 
                               class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Wyloguj
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        @endauth
        
        <!-- Kontener na flash messages -->
        <div id="flash-messages" class="fixed top-4 right-4 z-50 space-y-2"></div>
        
        <!-- Loading indicator -->
        <div id="loading" x-show="$store.loading.show" x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 flex items-center shadow-xl">
                <i class="fas fa-spinner fa-spin mr-3 text-indigo-600 text-xl"></i>
                <span class="text-gray-700">Ładowanie...</span>
            </div>
        </div>
        
        <!-- Główna zawartość -->
        <main class="@auth py-6 @else flex items-center justify-center min-h-screen @endauth">
            <div class="@auth max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 @endauth w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Konfiguracja Axios
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Licznik aktywnych żądań
        let loadingCount = 0;
        
        // Funkcje do kontroli loadera
        function showLoading() {
            loadingCount++;
            if (window.Alpine) {
                Alpine.store('loading').show = true;
            }
        }
        
        function hideLoading() {
            loadingCount = Math.max(0, loadingCount - 1);
            if (loadingCount === 0 && window.Alpine) {
                Alpine.store('loading').show = false;
            }
        }
        
        // Interceptory Axios
        axios.interceptors.request.use(config => {
            showLoading();
            return config;
        }, error => {
            hideLoading();
            return Promise.reject(error);
        });
        
        axios.interceptors.response.use(
            response => {
                hideLoading();
                return response;
            },
            error => {
                hideLoading();
                
                // Obsługa błędów
                if (error.response) {
                    switch (error.response.status) {
                        case 401:
                            window.location.href = '/login';
                            break;
                        case 403:
                            showFlash('Nie masz uprawnień do wykonania tej akcji', 'error');
                            break;
                        case 404:
                            showFlash('Nie znaleziono zasobu', 'error');
                            break;
                        case 422:
                            // Błędy walidacji - obsługiwane osobno
                            break;
                        case 500:
                            showFlash('Wystąpił błąd serwera. Spróbuj ponownie później', 'error');
                            break;
                        default:
                            showFlash('Wystąpił nieoczekiwany błąd', 'error');
                    }
                } else if (error.request) {
                    showFlash('Brak połączenia z serwerem', 'error');
                }
                
                return Promise.reject(error);
            }
        );
        
        // Funkcja do wyświetlania powiadomień
        function showFlash(message, type = 'success') {
            const container = document.getElementById('flash-messages');
            const id = 'flash-' + Date.now();
            
            const colors = {
                success: 'bg-green-100 border-green-400 text-green-700',
                error: 'bg-red-100 border-red-400 text-red-700',
                warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
                info: 'bg-blue-100 border-blue-400 text-blue-700'
            };
            
            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-circle',
                warning: 'fas fa-exclamation-triangle',
                info: 'fas fa-info-circle'
            };
            
            const alert = document.createElement('div');
            alert.id = id;
            alert.className = `border ${colors[type]} px-4 py-3 rounded-lg flex items-center shadow-lg max-w-md transform transition-all duration-300 ease-in-out`;
            alert.innerHTML = `
                <i class="${icons[type]} mr-3"></i>
                <span class="flex-1">${message}</span>
                <button onclick="removeFlash('${id}')" class="ml-3 text-lg leading-none hover:opacity-75 transition-opacity">&times;</button>
            `;
            
            container.appendChild(alert);
            
            // Animacja wejścia
            setTimeout(() => {
                alert.classList.add('translate-x-0');
            }, 10);
            
            // Automatyczne usunięcie po 5 sekundach
            setTimeout(() => {
                removeFlash(id);
            }, 5000);
        }
        
        // Funkcja do usuwania powiadomień
        function removeFlash(id) {
            const element = document.getElementById(id);
            if (element) {
                element.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    element.remove();
                }, 300);
            }
        }
        
        // Obsługa nieobsłużonych błędów
        window.addEventListener('unhandledrejection', event => {
            console.error('Promise rejection:', event.reason);
            showFlash('Wystąpił nieoczekiwany błąd', 'error');
        });
        
        // Wyświetlanie flash messages z sesji Laravel
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                showFlash('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showFlash('{{ session('error') }}', 'error');
            @endif
            
            @if(session('warning'))
                showFlash('{{ session('warning') }}', 'warning');
            @endif
            
            @if(session('info'))
                showFlash('{{ session('info') }}', 'info');
            @endif
        });
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>