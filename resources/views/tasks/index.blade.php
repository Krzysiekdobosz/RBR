@extends('layouts.app')

@section('title', 'Zadania - ToDo App')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="tasksApp()">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Moje zadania
            </h2>
            <p class="mt-1 text-sm text-gray-500" x-text="'Znaleziono ' + totalTasks + ' zadań'">
                Ładowanie...
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('tasks.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>
                Nowe zadanie
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="filters.status" @change="loadTasks()" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Wszystkie</option>
                        <option value="to-do">Do zrobienia</option>
                        <option value="in_progress">W trakcie</option>
                        <option value="done">Zakończone</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priorytet</label>
                    <select x-model="filters.priority" @change="loadTasks()" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Wszystkie</option>
                        <option value="high">Wysoki</option>
                        <option value="medium">Średni</option>
                        <option value="low">Niski</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data od</label>
                    <input type="date" x-model="filters.due_date_from" @change="loadTasks()"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data do</label>
                    <input type="date" x-model="filters.due_date_to" @change="loadTasks()"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Quick Filters -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Szybkie filtry</label>
                    <div class="space-y-2">
                        <button @click="setOverdueFilter()" 
                                class="w-full text-left px-3 py-2 text-sm rounded-md"
                                :class="filters.overdue ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Przeterminowane
                        </button>
                        <button @click="clearFilters()" 
                                class="w-full text-left px-3 py-2 text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md">
                            <i class="fas fa-times mr-2"></i>Wyczyść filtry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div x-show="selectedTasks.length > 0" x-cloak
         class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-sm font-medium text-indigo-800" 
                      x-text="'Zaznaczono ' + selectedTasks.length + ' zadań'"></span>
            </div>
            <div class="flex items-center space-x-2">
                <select x-model="bulkAction" 
                        class="px-3 py-1 border border-indigo-300 rounded text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Wybierz akcję...</option>
                    <option value="status">Zmień status</option>
                    <option value="priority">Zmień priorytet</option>
                    <option value="delete">Usuń</option>
                </select>
                
                <select x-show="bulkAction === 'status'" x-model="bulkValue"
                        class="px-3 py-1 border border-indigo-300 rounded text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="to-do">Do zrobienia</option>
                    <option value="in_progress">W trakcie</option>
                    <option value="done">Zakończone</option>
                </select>
                
                <select x-show="bulkAction === 'priority'" x-model="bulkValue"
                        class="px-3 py-1 border border-indigo-300 rounded text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="low">Niski</option>
                    <option value="medium">Średni</option>
                    <option value="high">Wysoki</option>
                </select>
                
                <button @click="executeBulkAction()" 
                        :disabled="!bulkAction || (bulkAction !== 'delete' && !bulkValue)"
                        class="px-4 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    Wykonaj
                </button>
                
                <button @click="clearSelection()" 
                        class="px-4 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                    Anuluj
                </button>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div x-show="loading" class="p-6 text-center">
            <i class="fas fa-spinner fa-spin text-gray-400 text-xl"></i>
            <p class="mt-2 text-sm text-gray-500">Ładowanie zadań...</p>
        </div>
        
        <div x-show="!loading && tasks.length === 0" x-cloak class="p-6 text-center">
            <i class="fas fa-inbox text-gray-400 text-3xl"></i>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Brak zadań</h3>
            <p class="mt-1 text-sm text-gray-500">Zacznij od dodania nowego zadania.</p>
            <div class="mt-6">
                <a href="{{ route('tasks.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj zadanie
                </a>
            </div>
        </div>

        <ul x-show="!loading && tasks.length > 0" x-cloak class="divide-y divide-gray-200">
            <template x-for="task in tasks" :key="task.id">
                <li class="hover:bg-gray-50 transition-colors duration-150"
                    :class="'task-priority-' + task.priority">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       :value="task.id"
                                       x-model="selectedTasks"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mr-4">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900 truncate"
                                           :class="task.status === 'done' ? 'line-through opacity-60' : ''"
                                           x-text="task.name"></p>
                                        <span x-show="task.is_overdue"
                                              class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Przeterminowane
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mr-2"
                                              :class="getStatusBadge(task.status)"
                                              x-text="getStatusLabel(task.status)"></span>
                                        
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mr-2"
                                              :class="getPriorityBadge(task.priority)"
                                              x-text="getPriorityLabel(task.priority)"></span>
                                        
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <span x-text="formatDate(task.due_date)"></span>
                                        </span>
                                        
                                        <span x-show="task.description" class="ml-2 flex items-center">
                                            <i class="fas fa-align-left text-gray-400"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <!-- Quick Status Change -->
                                <select @change="quickUpdateStatus(task.id, $event.target.value)"
                                        :value="task.status"
                                        class="text-xs border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="to-do">Do zrobienia</option>
                                    <option value="in_progress">W trakcie</option>
                                    <option value="done">Zakończone</option>
                                </select>
                                
                                <!-- Actions Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="p-1 rounded-full text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-cloak
                                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <a :href="'/tasks/' + task.id" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye mr-2"></i>Zobacz
                                            </a>
                                            <a :href="'/tasks/' + task.id + '/edit'" 
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2"></i>Edytuj
                                            </a>
                                            <button @click="shareTask(task.id)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-share mr-2"></i>Udostępnij
                                            </button>
                                            <button @click="duplicateTask(task.id)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-copy mr-2"></i>Duplikuj
                                            </button>
                                            <div class="border-t border-gray-100"></div>
                                            <button @click="deleteTask(task.id)" 
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                <i class="fas fa-trash mr-2"></i>Usuń
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </template>
        </ul>
    </div>

    <!-- Pagination -->
    <div x-show="!loading && pagination.last_page > 1" x-cloak class="mt-6">
        <nav class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="changePage(pagination.current_page - 1)" 
                        :disabled="pagination.current_page === 1"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Poprzednia
                </button>
                <button @click="changePage(pagination.current_page + 1)" 
                        :disabled="pagination.current_page === pagination.last_page"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    Następna
                </button>
            </div>
            
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Pokazuje 
                        <span class="font-medium" x-text="pagination.from"></span>
                        do 
                        <span class="font-medium" x-text="pagination.to"></span>
                        z 
                        <span class="font-medium" x-text="pagination.total"></span>
                        wyników
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <!-- Pagination buttons will be generated by JavaScript -->
                        <div id="pagination-buttons"></div>
                    </nav>
                </div>
            </div>
        </nav>
    </div>

    <!-- Share Modal -->
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
                    <input type="number" x-model.number="shareModal.expiryHours" min="1" max="168"
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
function tasksApp() {
    return {
        tasks: [],
        loading: true,
        totalTasks: 0,
        selectedTasks: [],
        bulkAction: '',
        bulkValue: '',
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0,
            from: 0,
            to: 0
        },
        filters: {
            status: '',
            priority: '',
            due_date_from: '',
            due_date_to: '',
            overdue: false
        },
        shareModal: {
            show: false,
            taskId: null,
            expiryHours: 24,
            url: ''
        },

        init() {
            // Parse URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status')) this.filters.status = urlParams.get('status');
            if (urlParams.get('priority')) this.filters.priority = urlParams.get('priority');
            if (urlParams.get('overdue') === 'true') this.filters.overdue = true;
            
            this.loadTasks();
        },

        async loadTasks(page = 1) {
            this.loading = true;
            
            try {
                const params = {
                    page,
                    per_page: this.pagination.per_page,
                    ...this.filters
                };
                
                // Remove empty filters
                Object.keys(params).forEach(key => {
                    if (params[key] === '' || params[key] === false) {
                        delete params[key];
                    }
                });
                
                const response = await axios.get('/api/web/tasks', { params });
                
                if (response.data.success) {
                    this.tasks = response.data.data.data;
                    this.pagination = {
                        current_page: response.data.data.current_page,
                        last_page: response.data.data.last_page,
                        per_page: response.data.data.per_page,
                        total: response.data.data.total,
                        from: response.data.data.from || 0,
                        to: response.data.data.to || 0
                    };
                    this.totalTasks = this.pagination.total;
                    this.generatePaginationButtons();
                }
            } catch (error) {
                console.error('Error loading tasks:', error);
                showFlash('Błąd podczas ładowania zadań', 'error');
            } finally {
                this.loading = false;
            }
        },

        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.loadTasks(page);
            }
        },

        generatePaginationButtons() {
            const container = document.getElementById('pagination-buttons');
            if (!container) return;
            
            let buttons = '';
            const current = this.pagination.current_page;
            const last = this.pagination.last_page;
            
            // Previous button
            buttons += `
                <button onclick="document.querySelector('[x-data]').__x.$data.changePage(${current - 1})" 
                        ${current === 1 ? 'disabled' : ''}
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
            
            // Page numbers
            for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
                buttons += `
                    <button onclick="document.querySelector('[x-data]').__x.$data.changePage(${i})" 
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${
                                i === current 
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-600' 
                                    : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
                            }">
                        ${i}
                    </button>
                `;
            }
            
            // Next button
            buttons += `
                <button onclick="document.querySelector('[x-data]').__x.$data.changePage(${current + 1})" 
                        ${current === last ? 'disabled' : ''}
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
            
            container.innerHTML = buttons;
        },

        setOverdueFilter() {
            this.filters.overdue = !this.filters.overdue;
            this.loadTasks();
        },

        clearFilters() {
            this.filters = {
                status: '',
                priority: '',
                due_date_from: '',
                due_date_to: '',
                overdue: false
            };
            this.loadTasks();
        },

        clearSelection() {
            this.selectedTasks = [];
            this.bulkAction = '';
            this.bulkValue = '';
        },

        async executeBulkAction() {
            if (!this.bulkAction || this.selectedTasks.length === 0) return;
            
            if (this.bulkAction === 'delete') {
                if (!confirm('Czy na pewno chcesz usunąć zaznaczone zadania?')) return;
            }
            
            try {
                await axios.post('/api/web/tasks/bulk-update', {
                    task_ids: this.selectedTasks,
                    action: this.bulkAction,
                    value: this.bulkValue
                });
                
                showFlash('Operacja wykonana pomyślnie', 'success');
                this.clearSelection();
                this.loadTasks(this.pagination.current_page);
            } catch (error) {
                console.error('Bulk action error:', error);
                showFlash('Błąd podczas wykonywania operacji', 'error');
            }
        },

        async quickUpdateStatus(taskId, newStatus) {
            try {
                await axios.patch(`/api/web/tasks/${taskId}`, { status: newStatus });
                showFlash('Status zadania został zaktualizowany', 'success');
                this.loadTasks(this.pagination.current_page);
            } catch (error) {
                console.error('Status update error:', error);
                showFlash('Błąd podczas aktualizacji statusu', 'error');
            }
        },

        async deleteTask(taskId) {
            if (!confirm('Czy na pewno chcesz usunąć to zadanie?')) return;
            
            try {
                await axios.delete(`/api/web/tasks/${taskId}`);
                showFlash('Zadanie zostało usunięte', 'success');
                this.loadTasks(this.pagination.current_page);
            } catch (error) {
                console.error('Delete error:', error);
                showFlash('Błąd podczas usuwania zadania', 'error');
            }
        },

        shareTask(taskId) {
            this.shareModal.taskId = taskId;
            this.shareModal.url = '';
            this.shareModal.show = true;
        },

        async generateShareLink() {
            try {
                const response = await axios.post(`/api/web/tasks/${this.shareModal.taskId}/share`, {
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

        async duplicateTask(taskId) {
            try {
                const taskResponse = await axios.get(`/api/web/tasks/${taskId}`);
                const task = taskResponse.data.data;
                
                // Remove ID and update name
                delete task.id;
                task.name = 'Kopia: ' + task.name;
                task.status = 'to-do';
                
                await axios.post('/api/web/tasks', task);
                showFlash('Zadanie zostało zduplikowane', 'success');
                this.loadTasks(this.pagination.current_page);
            } catch (error) {
                console.error('Duplicate error:', error);
                showFlash('Błąd podczas duplikowania zadania', 'error');
            }
        },

        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showFlash('Link został skopiowany do schowka', 'success');
            }).catch(() => {
                showFlash('Błąd podczas kopiowania linku', 'error');
            });
        },

        // Helper methods
        getStatusBadge(status) {
            const badges = {
                'to-do': 'bg-blue-100 text-blue-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'done': 'bg-green-100 text-green-800'
            };
            return badges[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusLabel(status) {
            const labels = {
                'to-do': 'Do zrobienia',
                'in_progress': 'W trakcie',
                'done': 'Zakończone'
            };
            return labels[status] || status;
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
        },

        formatDate(dateString) {
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
    }
}

Alpine.store('tasks', null);

document.addEventListener('alpine:init', () => {
    Alpine.store('tasks', tasksApp());
});
</script>
@endsection