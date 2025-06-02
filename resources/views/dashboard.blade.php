@extends('layouts.app')

@section('title', 'Dashboard - ToDo App')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Witaj, {{ $user->name }}! 👋
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Oto przegląd Twoich zadań na dziś
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('tasks.create') }}" 
               class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Nowe zadanie
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- To Do -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Do zrobienia</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['to-do'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-play text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">W trakcie</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['in_progress'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Done -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Zakończone</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['done'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Przeterminowane</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $overdueCount }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Tasks -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Ostatnie zadania
                        </h3>
                        <a href="{{ route('tasks.index') }}" 
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            Zobacz wszystkie
                        </a>
                    </div>
                    
                    <div id="recent-tasks" class="space-y-3">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin text-gray-400 text-xl"></i>
                            <p class="mt-2 text-sm text-gray-500">Ładowanie zadań...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Szybkie akcje
                    </h3>
                    <div class="space-y-3">
                        <a href="{{ route('tasks.create') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            <i class="fas fa-plus mr-2"></i>
                            Dodaj zadanie
                        </a>
                        <button onclick="loadTasks({ status: 'to-do' })"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-list mr-2"></i>
                            Zadania do zrobienia
                        </button>
                        <button onclick="loadTasks({ overdue: true })"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Przeterminowane
                        </button>
                    </div>
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Postęp
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Ukończone</span>
                                <span class="font-medium">{{ $stats['done'] }}/{{ array_sum($stats) }}</span>
                            </div>
                            <div class="mt-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" 
                                     style="width: {{ array_sum($stats) > 0 ? ($stats['done'] / array_sum($stats)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">W trakcie</span>
                                <span class="font-medium">{{ $stats['in_progress'] }}/{{ array_sum($stats) }}</span>
                            </div>
                            <div class="mt-1 bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" 
                                     style="width: {{ array_sum($stats) > 0 ? ($stats['in_progress'] / array_sum($stats)) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadRecentTasks();
});

async function loadRecentTasks() {
    try {
        const response = await axios.get('/tasks', {
            params: {
                per_page: 5,
                sort_by: 'updated_at',
                sort_order: 'desc'
            }
        });
        
        if (response.data.success) {
            displayRecentTasks(response.data.data.data);
        }
    } catch (error) {
        console.error('Error loading recent tasks:', error);
        document.getElementById('recent-tasks').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                <p class="mt-2 text-sm text-red-600">Błąd podczas ładowania zadań</p>
            </div>
        `;
    }
}

function displayRecentTasks(tasks) {
    const container = document.getElementById('recent-tasks');
    
    if (tasks.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-inbox text-gray-400 text-xl"></i>
                <p class="mt-2 text-sm text-gray-500">Brak zadań</p>
                <a href="/tasks/create" class="mt-2 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-plus mr-1"></i>
                    Dodaj pierwsze zadanie
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = tasks.map(task => `
        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg task-priority-${task.priority}">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate ${task.status === 'done' ? 'line-through opacity-60' : ''}">
                    ${task.name}
                </p>
                <div class="flex items-center mt-1 space-x-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getStatusBadge(task.status)}">
                        ${getStatusLabel(task.status)}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getPriorityBadge(task.priority)}">
                        ${getPriorityLabel(task.priority)}
                    </span>
                    <span class="text-xs text-gray-500">
                        ${formatDate(task.due_date)}
                    </span>
                </div>
            </div>
            <div class="ml-4 flex items-center space-x-2">
                <a href="/tasks/${task.id}" class="text-indigo-600 hover:text-indigo-500">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </div>
    `).join('');
}

function loadTasks(filters = {}) {
    const params = new URLSearchParams(filters);
    window.location.href = '/tasks?' + params.toString();
}

function getStatusBadge(status) {
    const badges = {
        'to-do': 'bg-blue-100 text-blue-800',
        'in_progress': 'bg-yellow-100 text-yellow-800',
        'done': 'bg-green-100 text-green-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
}

function getStatusLabel(status) {
    const labels = {
        'to-do': 'Do zrobienia',
        'in_progress': 'W trakcie',
        'done': 'Zakończone'
    };
    return labels[status] || status;
}

function getPriorityBadge(priority) {
    const badges = {
        'low': 'bg-green-100 text-green-800',
        'medium': 'bg-yellow-100 text-yellow-800',
        'high': 'bg-red-100 text-red-800'
    };
    return badges[priority] || 'bg-gray-100 text-gray-800';
}

function getPriorityLabel(priority) {
    const labels = {
        'low': 'Niski',
        'medium': 'Średni',
        'high': 'Wysoki'
    };
    return labels[priority] || priority;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    if (date.toDateString() === today.toDateString()) {
        return 'Dziś';
    } else if (date.toDateString() === tomorrow.toDateString()) {
        return 'Jutro';
    } else {
        return date.toLocaleDateString('pl-PL');
    }
}
</script>
@endsection