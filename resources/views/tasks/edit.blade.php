@extends('layouts.app')

@section('title', 'Edytuj: ' . $task->name . ' - ToDo App')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="taskEditForm({{ $task->id }})">
    <!-- Header -->
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
                        <a href="{{ route('tasks.show', $task) }}" class="text-gray-400 hover:text-gray-500 truncate">
                            {{ $task->name }}
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-gray-900 font-medium">Edytuj</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="mt-4">
            <h1 class="text-3xl font-bold text-gray-900">Edytuj zadanie</h1>
            <p class="mt-2 text-sm text-gray-600">
                Zaktualizuj szczegóły zadania poniżej.
            </p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form @submit.prevent="handleSubmit" class="space-y-6 p-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">
                    Nazwa zadania <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <input type="text" 
                           id="name" 
                           x-model="form.name"
                           maxlength="255"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           :class="errors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                </div>
                <div x-show="errors.name" x-cloak class="mt-1">
                    <p class="text-sm text-red-600" x-text="errors.name[0]"></p>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    <span x-text="form.name.length"></span>/255 znaków
                </p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Opis zadania
                </label>
                <div class="mt-1">
                    <textarea id="description" 
                              x-model="form.description"
                              rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                              :class="errors.description ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''"></textarea>
                </div>
                <div x-show="errors.description" x-cloak class="mt-1">
                    <p class="text-sm text-red-600" x-text="errors.description[0]"></p>
                </div>
            </div>

            <!-- Priority and Status Row -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">
                        Priorytet <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select id="priority" 
                                x-model="form.priority"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                :class="errors.priority ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                            <option value="low">🟢 Niski</option>
                            <option value="medium">🟡 Średni</option>
                            <option value="high">🔴 Wysoki</option>
                        </select>
                    </div>
                    <div x-show="errors.priority" x-cloak class="mt-1">
                        <p class="text-sm text-red-600" x-text="errors.priority[0]"></p>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select id="status" 
                                x-model="form.status"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                :class="errors.status ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                            <option value="to-do">📋 Do zrobienia</option>
                            <option value="in_progress">⏳ W trakcie</option>
                            <option value="done">✅ Zakończone</option>
                        </select>
                    </div>
                    <div x-show="errors.status" x-cloak class="mt-1">
                        <p class="text-sm text-red-600" x-text="errors.status[0]"></p>
                    </div>
                </div>
            </div>

            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">
                    Termin wykonania <span class="text-red-500">*</span>
                </label>
                <div class="mt-1">
                    <input type="date" 
                           id="due_date" 
                           x-model="form.due_date"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           :class="errors.due_date ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                </div>
                <div x-show="errors.due_date" x-cloak class="mt-1">
                    <p class="text-sm text-red-600" x-text="errors.due_date[0]"></p>
                </div>
            </div>

            <!-- Quick Date Buttons -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Szybki wybór terminu
                </label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" 
                            @click="setDueDate(0)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                        Dziś
                    </button>
                    <button type="button" 
                            @click="setDueDate(1)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200">
                        Jutro
                    </button>
                    <button type="button" 
                            @click="setDueDate(7)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                        Za tydzień
                    </button>
                    <button type="button" 
                            @click="setDueDate(14)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200">
                        Za 2 tygodnie
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center space-x-4">
                    <button type="button" 
                            @click="resetForm()"
                            class="text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-undo mr-1"></i>
                        Przywróć oryginalne wartości
                    </button>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('tasks.show', $task) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Anuluj
                    </a>
                    <button type="submit" 
                            :disabled="submitting || !hasChanges"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting">
                            <i class="fas fa-save mr-2"></i>
                            <span x-text="hasChanges ? 'Zapisz zmiany' : 'Brak zmian'"></span>
                        </span>
                        <span x-show="submitting" x-cloak>
                            <i class="fas fa-spinner fa-spin mr-2"></i>Zapisywanie...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Changes Preview -->
    <div x-show="hasChanges" x-cloak class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">
                    Masz niezapisane zmiany
                </h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>Zmieniłeś następujące pola:</p>
                    <ul class="list-disc list-inside mt-1" x-html="getChangedFields()"></ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function taskEditForm(taskId) {
    return {
        originalTask: @json($task),
        form: {
            name: @json($task->name),
            description: @json($task->description),
            priority: @json($task->priority),
            status: @json($task->status),
            due_date: @json($task->due_date->format('Y-m-d'))
        },
        errors: {},
        submitting: false,

        get hasChanges() {
            return this.form.name !== this.originalTask.name ||
                   this.form.description !== (this.originalTask.description || '') ||
                   this.form.priority !== this.originalTask.priority ||
                   this.form.status !== this.originalTask.status ||
                   this.form.due_date !== this.originalTask.due_date;
        },

        setDueDate(daysFromNow) {
            const date = new Date();
            date.setDate(date.getDate() + daysFromNow);
            this.form.due_date = date.toISOString().split('T')[0];
        },

        resetForm() {
            this.form = {
                name: this.originalTask.name,
                description: this.originalTask.description || '',
                priority: this.originalTask.priority,
                status: this.originalTask.status,
                due_date: this.originalTask.due_date
            };
            this.errors = {};
            showFlash('Przywrócono oryginalne wartości', 'info');
        },

        getChangedFields() {
            const changes = [];
            
            if (this.form.name !== this.originalTask.name) {
                changes.push('<li>Nazwa zadania</li>');
            }
            if (this.form.description !== (this.originalTask.description || '')) {
                changes.push('<li>Opis</li>');
            }
            if (this.form.priority !== this.originalTask.priority) {
                changes.push('<li>Priorytet</li>');
            }
            if (this.form.status !== this.originalTask.status) {
                changes.push('<li>Status</li>');
            }
            if (this.form.due_date !== this.originalTask.due_date) {
                changes.push('<li>Termin wykonania</li>');
            }
            
            return changes.join('');
        },

        async handleSubmit() {
            this.errors = {};
            this.submitting = true;

            try {
                const response = await axios.put(`/api/web/tasks/${taskId}`, this.form);
                
                if (response.data.success) {
                    showFlash('Zadanie zostało zaktualizowane pomyślnie!', 'success');
                    
                    // Update original task data
                    this.originalTask = { ...this.originalTask, ...this.form };
                    
                    // Redirect to task view
                    setTimeout(() => {
                        window.location.href = `/tasks/${taskId}`;
                    }, 1000);
                }
            } catch (error) {
                console.error('Update task error:', error);
                
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                    showFlash('Sprawdź poprawność wprowadzonych danych', 'error');
                } else if (error.response?.data?.message) {
                    showFlash(error.response.data.message, 'error');
                } else {
                    showFlash('Wystąpił błąd podczas aktualizacji zadania', 'error');
                }
            } finally {
                this.submitting = false;
            }
        }
    }
}

window.addEventListener('beforeunload', function (e) {
    const hasChanges = document.querySelector('[x-data]')?.__x?.$data?.hasChanges;
    if (hasChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection