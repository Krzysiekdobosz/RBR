<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $task->name }} - Udostępnione zadanie</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome dla ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .task-priority-low { @apply border-l-4 border-green-400; }
        .task-priority-medium { @apply border-l-4 border-yellow-400; }
        .task-priority-high { @apply border-l-4 border-red-400; }
        .task-status-done { @apply opacity-60 line-through; }
    </style>
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-indigo-100 mb-4">
                    <i class="fas fa-share-alt text-indigo-600 text-xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Udostępnione zadanie</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Zadanie udostępnione przez <strong>{{ $task->user->name }}</strong>
                </p>
            </div>

            <!-- Share Info Banner -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-blue-700">
                            Ten link wygaśnie <strong>{{ $shareToken->expires_at->format('d.m.Y H:i') }}</strong>
                            ({{ $shareToken->expires_at->diffForHumans() }})
                        </p>
                    </div>
                </div>
            </div>

            <!-- Task Card -->
            <div class="bg-white shadow rounded-lg task-priority-{{ $task->priority }}">
                <!-- Task Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl font-bold text-gray-900 {{ $task->status === 'done' ? 'task-status-done' : '' }}">
                                {{ $task->name }}
                            </h2>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $task->status === 'to-do' ? 'bg-blue-100 text-blue-800' : 
                                       ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $task->status === 'to-do' ? '📋 Do zrobienia' : 
                                       ($task->status === 'in_progress' ? '⏳ W trakcie' : '✅ Zakończone') }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : 
                                       ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $task->priority === 'low' ? '🟢 Niski priorytet' : 
                                       ($task->priority === 'medium' ? '🟡 Średni priorytet' : '🔴 Wysoki priorytet') }}
                                </span>
                                @if($task->is_overdue)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Przeterminowane
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Details -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Opis zadania</h3>
                                @if($task->description)
                                    <div class="prose max-w-none">
                                        <p class="text-gray-700 whitespace-pre-line">{{ $task->description }}</p>
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <i class="fas fa-file-alt text-gray-300 text-3xl"></i>
                                        <p class="mt-2 text-sm text-gray-500">Brak opisu zadania</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Task Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje</h3>
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Termin wykonania</dt>
                                    <dd class="mt-1 flex items-center text-sm text-gray-900">
                                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                        {{ $task->due_date->format('d.m.Y') }}
                                        @if($task->is_overdue)
                                            <span class="ml-2 text-red-600">({{ abs($task->days_until_due) }} dni temu)</span>
                                        @elseif($task->days_until_due === 0)
                                            <span class="ml-2 text-orange-600">(dziś)</span>
                                        @elseif($task->days_until_due === 1)
                                            <span class="ml-2 text-blue-600">(jutro)</span>
                                        @else
                                            <span class="ml-2 text-gray-600">(za {{ $task->days_until_due }} dni)</span>
                                        @endif
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Data utworzenia</dt>
                                    <dd class="mt-1 flex items-center text-sm text-gray-900">
                                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                                        {{ $task->created_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Ostatnia aktualizacja</dt>
                                    <dd class="mt-1 flex items-center text-sm text-gray-900">
                                        <i class="fas fa-sync mr-2 text-gray-400"></i>
                                        {{ $task->updated_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Właściciel</dt>
                                    <dd class="mt-1 flex items-center text-sm text-gray-900">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        {{ $task->user->name }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Share Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informacje o udostępnieniu</h3>
                            <div class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Udostępnione</dt>
                                    <dd class="mt-1 flex items-center text-sm text-gray-900">
                                        <i class="fas fa-share mr-2 text-gray-400"></i>
                                        {{ $shareToken->created_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Wygasa</dt>
                                    <dd class="mt-1 flex items-center text-sm text-gray-900">
                                        <i class="fas fa-hourglass-end mr-2 text-gray-400"></i>
                                        {{ $shareToken->expires_at->format('d.m.Y H:i') }}
                                        <span class="ml-2 text-xs text-gray-500">
                                            ({{ $shareToken->expires_at->diffForHumans() }})
                                        </span>
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Akcje</h3>
                            <div class="space-y-3">
                                <button onclick="window.print()" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-print mr-2"></i>
                                    Drukuj zadanie
                                </button>
                                
                                <button onclick="copyToClipboard(window.location.href)" 
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-copy mr-2"></i>
                                    Kopiuj link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <div class="bg-white rounded-lg p-6 shadow">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-tasks text-indigo-600 text-2xl mr-3"></i>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">ToDo App</h3>
                            <p class="text-sm text-gray-500">Prosty system zarządzania zadaniami</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Zaloguj się lub utwórz konto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages Container -->
    <div id="flash-messages" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showFlash('Link został skopiowany do schowka', 'success');
            }).catch(() => {
                showFlash('Błąd podczas kopiowania linku', 'error');
            });
        }

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
            
            container.innerHTML += `
                <div id="${id}" class="border ${colors[type]} px-4 py-3 rounded-lg flex items-center shadow-lg max-w-md">
                    <i class="${icons[type]} mr-3"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="document.getElementById('${id}').remove()" class="ml-3 text-lg leading-none">&times;</button>
                </div>
            `;
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                const element = document.getElementById(id);
                if (element) element.remove();
            }, 5000);
        }

        // Auto-refresh if link expires soon
        const expiresAt = new Date('{{ $shareToken->expires_at->toISOString() }}');
        const now = new Date();
        const timeUntilExpiry = expiresAt.getTime() - now.getTime();

        // Show warning if expires in less than 1 hour
        if (timeUntilExpiry > 0 && timeUntilExpiry < 3600000) {
            showFlash('Ten link wygaśnie wkrótce!', 'warning');
        }

        // Redirect if already expired
        if (timeUntilExpiry <= 0) {
            showFlash('Ten link wygasł', 'error');
            setTimeout(() => {
                window.location.href = '/';
            }, 3000);
        }
    </script>
</body>
</html>