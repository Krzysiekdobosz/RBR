@extends('layouts.app')

@section('title', $task->name . ' - RBR Krzysztof Dobosz')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="taskView({{ $task->id }})">
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('tasks.index') }}" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-list mr-1"></i>
                        Zadania
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-900 font-medium truncate">{{ $task->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 task-status-{{ $task->status }}"
                        :class="task.status === 'done' ? 'line-through opacity-60' : ''">
                        {{ $task->name }}
                    </h1>
                    <div class="mt-2 flex items-center space-x-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $task->status === 'to-do' ? 'bg-blue-100 text-blue-800' : ($task->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ $task->status === 'to-do' ? '📋 Do zrobienia' : ($task->status === 'in_progress' ? '⏳ W trakcie' : '✅ Zakończone') }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $task->priority === 'low' ? '🟢 Niski' : ($task->priority === 'medium' ? '🟡 Średni' : '🔴 Wysoki') }}
                        </span>
                        @if($task->sync_to_calendar)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-calendar-check mr-1"></i>W kalendarzu
                            </span>
                        @endif
                        @if($task->is_overdue)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Przeterminowane
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <select @change="updateTaskStatus($event.target.value)"
                            class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="to-do" {{ $task->status === 'to-do' ? 'selected' : '' }}>Do zrobienia</option>
                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>W trakcie</option>
                        <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>Zakończone</option>
                    </select>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-cog mr-2"></i>
                            Akcje
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-cloak
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1">
                                <a href="{{ route('tasks.edit', $task) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-edit mr-2"></i>Edytuj
                                </a>
                                <button @click="shareTask()" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-share mr-2"></i>Udostępnij
                                </button>
                                <button @click="duplicateTask()" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-copy mr-2"></i>Duplikuj
                                </button>
                                <button @click="task.sync_to_calendar ? unsyncFromCalendar() : syncToCalendar()" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i :class="task.sync_to_calendar ? 'fas fa-calendar-times' : 'fas fa-calendar-plus'" class="mr-2"></i>
                                    <span x-text="task.sync_to_calendar ? 'Usuń z kalendarza' : 'Dodaj do kalendarza'"></span>
                                </button>
                                <div class="border-t border-gray-100"></div>
                                <button @click="deleteTask()" 
                                        class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                    <i class="fas fa-trash mr-2"></i>Usuń
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Opis zadania</h3>
                </div>
                <div class="px-6 py-4">
                    @if($task->description)
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line">{{ $task->description }}</p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-file-alt text-gray-300 text-3xl"></i>
                            <p class="mt-2 text-sm text-gray-500">Brak opisu zadania</p>
                            <a href="{{ route('tasks.edit', $task) }}" 
                               class="mt-2 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                                <i class="fas fa-plus mr-1"></i>
                                Dodaj opis
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Historia zmian</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">
                                Zadanie zostało utworzone
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $task->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                    </div>

                    @foreach($versions as $version)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-history text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $version->getActionDescription() }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $version->created_at->format('d.m.Y H:i') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('tasks.versions.show', [$task, $version]) }}"
                                       class="text-indigo-600 hover:underline text-sm">
                                        zobacz pełną wersję
                                    </a>
                                </div>
                                
                                @if($version->changes)
                                    <div class="mt-2 space-y-1">
                                        @foreach($version->getFormattedChanges() as $change)
                                            <div class="text-sm text-gray-600">
                                                <span class="font-medium">{{ $change['label'] }}:</span>
                                                @if($change['old_value'] && $change['new_value'])
                                                    <span class="text-red-600">{{ Str::limit($change['old_value'], 30) }}</span>
                                                    →
                                                    <span class="text-green-600">{{ Str::limit($change['new_value'], 30) }}</span>
                                                @elseif($change['new_value'])
                                                    dodano: <span class="text-green-600">{{ Str::limit($change['new_value'], 30) }}</span>
                                                @elseif($change['old_value'])
                                                    usunięto: <span class="text-red-600">{{ Str::limit($change['old_value'], 30) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($versions->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-history text-gray-300 text-3xl"></i>
                            <p class="mt-2 text-sm text-gray-500">Brak historii edycji</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informacje</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
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
                        <dt class="text-sm font-medium text-gray-500">Przypomnienie</dt>
                        <dd class="mt-1 flex items-center text-sm text-gray-900">
                            <i class="fas fa-bell mr-2 text-gray-400"></i>
                            {{ $task->reminder_sent ? 'Wysłane' : 'Nie wysłane' }}
                        </dd>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Szybkie akcje</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if($task->status !== 'done')
                        <button @click="markAsDone()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i>
                            Oznacz jako zakończone
                        </button>
                    @else
                        <button @click="markAsInProgress()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                            <i class="fas fa-undo mr-2"></i>
                            Oznacz jako w trakcie
                        </button>
                    @endif
                    
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i>
                        Edytuj zadanie
                    </a>
                    
                    <button @click="shareTask()"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-share mr-2"></i>
                        Udostępnij zadanie
                    </button>
                    
                    <div class="border-t pt-3 mt-3">
                        @if($task->sync_to_calendar)
                            <button @click="unsyncFromCalendar()"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                                <i class="fas fa-calendar-times mr-2"></i>
                                Usuń z kalendarza Google
                            </button>
                            @if($task->calendar_synced_at)
                                <p class="mt-2 text-xs text-center text-gray-500">
                                    Zsynchronizowano: {{ $task->calendar_synced_at->diffForHumans() }}
                                </p>
                            @endif
                        @else
                            <button @click="syncToCalendar()"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Dodaj do kalendarza Google
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="shareModal.show" x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Udostępnij zadanie</h3>
            </div>
            <div class="px-6 py-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Czas wygaśnięcia (godziny)
                    </label>
                    <input type="number" x-model="shareModal.expiryHours" min="1" max="168"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div x-show="shareModal.url" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Link udostępniania</label>
                    <div class="flex">
                        <input type="text" :value="shareModal.url" readonly
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm">
                        <button @click="copyToClipboard(shareModal.url)"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button @click="shareModal.show = false"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Anuluj
                </button>
                <button @click="generateShareLink()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    <span x-text="shareModal.url ? 'Regeneruj' : 'Generuj link'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function taskView(taskId) {
    return {
        task: @json($task),
        shareModal: {
            show: false,
            expiryHours: 24,
            url: ''
        },

        async updateTaskStatus(newStatus) {
            try {
                const response = await axios.patch(`/api/web/tasks/${taskId}`, { status: newStatus });
                
                if (response.data.success) {
                    this.task.status = newStatus;
                    showFlash('Status zadania został zaktualizowany', 'success');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } catch (error) {
                console.error('Status update error:', error);
                showFlash('Błąd podczas aktualizacji statusu', 'error');
            }
        },

        async markAsDone() {
            await this.updateTaskStatus('done');
        },

        async markAsInProgress() {
            await this.updateTaskStatus('in_progress');
        },

        async deleteTask() {
            if (!confirm('Czy na pewno chcesz usunąć to zadanie?')) return;
            
            try {
                await axios.delete(`/api/web/tasks/${taskId}`);
                showFlash('Zadanie zostało usunięte', 'success');
                
                setTimeout(() => {
                    window.location.href = '/tasks';
                }, 1000);
            } catch (error) {
                console.error('Delete error:', error);
                showFlash('Błąd podczas usuwania zadania', 'error');
            }
        },

        shareTask() {
            this.shareModal.taskId = taskId;
            this.shareModal.url = '';
            this.shareModal.show = true;
        },

        async generateShareLink() {
            try {
                const response = await axios.post(`/api/web/tasks/${taskId}/share`, {
                    expiry_hours: this.shareModal.expiryHours
                });
                
                if (response.data.success) {
                    this.shareModal.url = response.data.data.url;
                    showFlash('Link udostępniania został wygenerowany', 'success');
                }
            } catch (error) {
                console.error('Share link error:', error);
                showFlash('Błąd podczas generowania linku', 'error');
            }
        },

        async duplicateTask() {
            try {
                const task = { ...this.task };
                
                delete task.id;
                task.name = 'Kopia: ' + task.name;
                task.status = 'to-do';
                
                await axios.post('/api/web/tasks', task);
                showFlash('Zadanie zostało zduplikowane', 'success');
                
                setTimeout(() => {
                    window.location.href = '/tasks';
                }, 1000);
            } catch (error) {
                console.error('Duplicate error:', error);
                showFlash('Błąd podczas duplikowania zadania', 'error');
            }
        },

        async syncToCalendar() {
            try {
                // Pokaż loader
                showFlash('Synchronizowanie z kalendarzem...', 'info');
                
                const response = await axios.post(`/api/web/tasks/${taskId}/sync-calendar`);
                
                if (response.data.success) {
                    this.task.sync_to_calendar = true;
                    showFlash('Zadanie zostało dodane do kalendarza Google', 'success');
                    
                    // Odśwież stronę po krótkim czasie
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } catch (error) {
                console.error('Calendar sync error:', error);
                
                // Sprawdź czy to błąd autoryzacji
                if (error.response && error.response.status === 403) {
                    showFlash('Brak uprawnień do synchronizacji z kalendarzem', 'error');
                } else if (error.response && error.response.data && error.response.data.message) {
                    showFlash(error.response.data.message, 'error');
                } else {
                    showFlash('Błąd podczas synchronizacji z kalendarzem Google', 'error');
                }
            }
        },

        async unsyncFromCalendar() {
            if (!confirm('Czy na pewno chcesz usunąć to zadanie z kalendarza Google?')) return;
            
            try {
                showFlash('Usuwanie z kalendarza...', 'info');
                
                const response = await axios.delete(`/api/web/tasks/${taskId}/sync-calendar`);
                
                if (response.data.success) {
                    this.task.sync_to_calendar = false;
                    showFlash('Zadanie zostało usunięte z kalendarza Google', 'success');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } catch (error) {
                console.error('Calendar unsync error:', error);
                
                if (error.response && error.response.data && error.response.data.message) {
                    showFlash(error.response.data.message, 'error');
                } else {
                    showFlash('Błąd podczas usuwania z kalendarza Google', 'error');
                }
            }
        },

        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showFlash('Link został skopiowany do schowka', 'success');
            }).catch(() => {
                showFlash('Błąd podczas kopiowania linku', 'error');
            });
        }
    }
}
</script>
@endsection