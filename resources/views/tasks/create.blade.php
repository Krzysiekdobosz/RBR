@extends('layouts.app')

@section('title', 'Nowe zadanie - ToDo App')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="taskForm()">
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
                        <span class="text-gray-900 font-medium">Nowe zadanie</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="mt-4">
            <h1 class="text-3xl font-bold text-gray-900">Utwórz nowe zadanie</h1>
            <p class="mt-2 text-sm text-gray-600">
                Wypełnij formularz poniżej, aby dodać nowe zadanie do swojej listy.
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
                           placeholder="np. Przygotować raport miesięczny"
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
                              placeholder="Opcjonalny opis zadania..."
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
                            <option value="">Wybierz priorytet</option>
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
                            <option value="">Wybierz status</option>
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
                           :min="today"
                           required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                           :class="errors.due_date ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                </div>
                <div x-show="errors.due_date" x-cloak class="mt-1">
                    <p class="text-sm text-red-600" x-text="errors.due_date[0]"></p>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Zadanie nie może mieć terminu w przeszłości
                </p>
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
                    <button type="button" 
                            @click="setDueDate(30)"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 hover:bg-indigo-200">
                        Za miesiąc
                    </button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="create_another"
                           x-model="createAnother"
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="create_another" class="ml-2 block text-sm text-gray-900">
                        Utwórz kolejne zadanie po zapisaniu
                    </label>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('tasks.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Anuluj
                    </a>
                    <button type="submit" 
                            :disabled="submitting"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting">
                            <i class="fas fa-save mr-2"></i>Utwórz zadanie
                        </span>
                        <span x-show="submitting" x-cloak>
                            <i class="fas fa-spinner fa-spin mr-2"></i>Tworzenie...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Template Suggestions -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
            Szablony zadań
        </h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <template x-for="template in templates" :key="template.name">
                <button @click="useTemplate(template)"
                        class="text-left p-4 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition-all duration-150">
                    <div class="flex items-center mb-2">
                        <span class="text-lg mr-2" x-text="template.icon"></span>
                        <h4 class="font-medium text-gray-900" x-text="template.name"></h4>
                    </div>
                    <p class="text-sm text-gray-600" x-text="template.description"></p>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                              :class="getPriorityBadge(template.priority)"
                              x-text="getPriorityLabel(template.priority)"></span>
                        <span class="text-xs text-gray-500" x-text="template.duration"></span>
                    </div>
                </button>
            </template>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function taskForm() {
    return {
        form: {
            name: '',
            description: '',
            priority: 'medium',
            status: 'to-do',
            due_date: ''
        },
        errors: {},
        submitting: false,
        createAnother: false,
        today: new Date().toISOString().split('T')[0],
        
        templates: [
            {
                name: 'Spotkanie biznesowe',
                description: 'Przygotowanie i przeprowadzenie spotkania',
                icon: '🤝',
                priority: 'medium',
                duration: '2-3 dni',
                template: {
                    name: 'Spotkanie z [nazwa klienta/zespołu]',
                    description: 'Przygotować agendę, materiały prezentacyjne i notatki ze spotkania',
                    priority: 'medium',
                    status: 'to-do'
                }
            },
            {
                name: 'Projekt kodowania',
                description: 'Implementacja nowej funkcjonalności',
                icon: '💻',
                priority: 'high',
                duration: '1-2 tygodnie',
                template: {
                    name: 'Implementacja [nazwa funkcjonalności]',
                    description: 'Analiza wymagań, projektowanie, kodowanie, testowanie i dokumentacja',
                    priority: 'high',
                    status: 'to-do'
                }
            },
            {
                name: 'Raport/Analiza',
                description: 'Przygotowanie raportu lub analizy danych',
                icon: '📊',
                priority: 'medium',
                duration: '3-5 dni',
                template: {
                    name: 'Raport [nazwa/temat]',
                    description: 'Zebranie danych, analiza, przygotowanie wyników i prezentacja',
                    priority: 'medium',
                    status: 'to-do'
                }
            },
            {
                name: 'Zadanie administracyjne',
                description: 'Rutynowe zadania biurowe',
                icon: '📋',
                priority: 'low',
                duration: '1 dzień',
                template: {
                    name: '[nazwa zadania administracyjnego]',
                    description: 'Wykonanie rutynowego zadania administracyjnego',
                    priority: 'low',
                    status: 'to-do'
                }
            },
            {
                name: 'Szkolenie/Nauka',
                description: 'Uczestnictwo w szkoleniu lub nauka nowych umiejętności',
                icon: '🎓',
                priority: 'medium',
                duration: '1-3 dni',
                template: {
                    name: 'Szkolenie: [temat]',
                    description: 'Przygotowanie do szkolenia, uczestnictwo i podsumowanie zdobytej wiedzy',
                    priority: 'medium',
                    status: 'to-do'
                }
            },
            {
                name: 'Review/Kontrola',
                description: 'Przegląd kodu, dokumentów lub procesów',
                icon: '🔍',
                priority: 'high',
                duration: '1-2 dni',
                template: {
                    name: 'Review: [nazwa projektu/dokumentu]',
                    description: 'Szczegółowy przegląd i weryfikacja jakości z uwagami',
                    priority: 'high',
                    status: 'to-do'
                }
            }
        ],

        init() {
            // Set default due date to tomorrow
            this.setDueDate(1);
        },

        setDueDate(daysFromNow) {
            const date = new Date();
            date.setDate(date.getDate() + daysFromNow);
            this.form.due_date = date.toISOString().split('T')[0];
        },

        useTemplate(template) {
            this.form = { ...this.form, ...template.template };
            
            // Set due date based on template duration
            if (template.duration.includes('tydzień') || template.duration.includes('tygodnie')) {
                const weeks = template.duration.includes('2') ? 2 : 1;
                this.setDueDate(weeks * 7);
            } else if (template.duration.includes('miesiąc')) {
                this.setDueDate(30);
            } else if (template.duration.includes('3-5')) {
                this.setDueDate(5);
            } else if (template.duration.includes('2-3')) {
                this.setDueDate(3);
            } else {
                this.setDueDate(1);
            }
            
            showFlash('Szablon został zastosowany', 'success');
        },

        async handleSubmit() {
            this.errors = {};
            this.submitting = true;

            try {
                const response = await axios.post('/api/web/tasks', this.form);
                
                if (response.data.success) {
                    showFlash('Zadanie zostało utworzone pomyślnie!', 'success');
                    
                    if (this.createAnother) {
                        // Reset form but keep some values
                        const keepValues = {
                            priority: this.form.priority,
                            status: this.form.status
                        };
                        this.form = {
                            name: '',
                            description: '',
                            due_date: '',
                            ...keepValues
                        };
                        this.setDueDate(1); 
                        
                        setTimeout(() => {
                            document.getElementById('name').focus();
                        }, 100);
                    } else {
                        setTimeout(() => {
                            window.location.href = '/tasks/' + response.data.data.id;
                        }, 1000);
                    }
                }
            } catch (error) {
                console.error('Create task error:', error);
                
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                    showFlash('Sprawdź poprawność wprowadzonych danych', 'error');
                } else if (error.response?.data?.message) {
                    showFlash(error.response.data.message, 'error');
                } else {
                    showFlash('Wystąpił błąd podczas tworzenia zadania', 'error');
                }
            } finally {
                this.submitting = false;
            }
        },

        getPriorityBadge(priority) {
            const badges = {
                'low': 'bg-green-100 text-green-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'high': 'bg-red-100 text-red-800'
            };
            return badges[priority] || 'bg-gray-100 text-gray-800';
        },

        getPriorityLabel(priority) {
            const labels = {
                'low': 'Niski',
                'medium': 'Średni',
                'high': 'Wysoki'
            };
            return labels[priority] || priority;
        }
    }
}
</script>
@endsection