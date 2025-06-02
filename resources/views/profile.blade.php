@extends('layouts.app')

@section('title', 'Profil - Krzysztof Dobosz')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="profileApp()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Profil użytkownika</h1>
        <p class="mt-2 text-sm text-gray-600">
            Zarządzaj swoimi danymi osobowymi i ustawieniami konta.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Info -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informacje osobiste</h3>
                </div>
                <form @submit.prevent="updateProfile" class="p-6 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Imię i nazwisko
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   id="name" 
                                   x-model="form.name"
                                   required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   :class="errors.name ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                        </div>
                        <div x-show="errors.name" x-cloak class="mt-1">
                            <p class="text-sm text-red-600" x-text="errors.name[0]"></p>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Adres email
                        </label>
                        <div class="mt-1">
                            <input type="email" 
                                   id="email" 
                                   x-model="form.email"
                                   required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   :class="errors.email ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                        </div>
                        <div x-show="errors.email" x-cloak class="mt-1">
                            <p class="text-sm text-red-600" x-text="errors.email[0]"></p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                :disabled="submitting"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!submitting">Zapisz zmiany</span>
                            <span x-show="submitting" x-cloak>Zapisywanie...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="mt-6 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Zmiana hasła</h3>
                </div>
                <form @submit.prevent="changePassword" class="p-6 space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">
                            Obecne hasło
                        </label>
                        <div class="mt-1">
                            <input type="password" 
                                   id="current_password" 
                                   x-model="passwordForm.current_password"
                                   required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   :class="passwordErrors.current_password ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                        </div>
                        <div x-show="passwordErrors.current_password" x-cloak class="mt-1">
                            <p class="text-sm text-red-600" x-text="passwordErrors.current_password[0]"></p>
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Nowe hasło
                        </label>
                        <div class="mt-1">
                            <input type="password" 
                                   id="password" 
                                   x-model="passwordForm.password"
                                   required
                                   minlength="8"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   :class="passwordErrors.password ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''">
                        </div>
                        <div x-show="passwordErrors.password" x-cloak class="mt-1">
                            <p class="text-sm text-red-600" x-text="passwordErrors.password[0]"></p>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Potwierdź nowe hasło
                        </label>
                        <div class="mt-1">
                            <input type="password" 
                                   id="password_confirmation" 
                                   x-model="passwordForm.password_confirmation"
                                   required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                :disabled="passwordSubmitting"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50">
                            <span x-show="!passwordSubmitting">Zmień hasło</span>
                            <span x-show="passwordSubmitting" x-cloak>Zmienianie...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statystyki konta</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data rejestracji</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d.m.Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ostatnie logowanie</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Wszystkich zadań</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="stats.total || 0"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ukończonych zadań</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="stats.completed || 0"></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Procent ukończenia</dt>
                            <dd class="mt-1 text-sm text-gray-900" x-text="getCompletionRate() + '%'"></dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Akcje konta</h3>
                </div>
                <div class="p-6 space-y-3">                    
                    <button @click="showDeleteModal = true"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                        <i class="fas fa-trash mr-2"></i>
                        Usuń konto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-red-900">Usuń konto</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600 mb-4">
                    <strong>Uwaga:</strong> Ta operacja jest nieodwracalna. Wszystkie Twoje zadania zostaną trwale usunięte.
                </p>
                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-700">
                        Potwierdź hasłem
                    </label>
                    <input type="password" 
                           id="delete_password" 
                           x-model="deletePassword"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button @click="showDeleteModal = false; deletePassword = ''"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Anuluj
                </button>
                <button @click="deleteAccount()"
                        :disabled="!deletePassword"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50">
                    Usuń konto
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function profileApp() {
    return {
        user: @json($user),
        form: {
            name: @json($user->name),
            email: @json($user->email)
        },
        errors: {},
        submitting: false,
        
        passwordForm: {
            current_password: '',
            password: '',
            password_confirmation: ''
        },
        passwordErrors: {},
        passwordSubmitting: false,
        
        stats: {
            total: 0,
            completed: 0
        },
        
        showDeleteModal: false,
        deletePassword: '',

        init() {
            this.loadStats();
        },

        async loadStats() {
            try {
                const response = await axios.get('/api/web/user');
                if (response.data.success) {
                    const userStats = response.data.data.tasks_stats;
                    this.stats.total = Object.values(userStats).reduce((a, b) => a + b, 0);
                    this.stats.completed = userStats.done || 0;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        getCompletionRate() {
            if (this.stats.total === 0) return 0;
            return Math.round((this.stats.completed / this.stats.total) * 100);
        },

        async updateProfile() {
            this.errors = {};
            this.submitting = true;

            try {
                showFlash('Profil zostanie zaktualizowany w przyszłej wersji', 'info');
            } catch (error) {
                console.error('Update profile error:', error);
                showFlash('Błąd podczas aktualizacji profilu', 'error');
            } finally {
                this.submitting = false;
            }
        },

        async changePassword() {
            this.passwordErrors = {};
            this.passwordSubmitting = true;

            if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
                this.passwordErrors.password = ['Hasła nie są identyczne'];
                this.passwordSubmitting = false;
                return;
            }

            try {
                showFlash('Zmiana hasła zostanie zaimplementowana w przyszłej wersji', 'info');
                this.passwordForm = {
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                };
            } catch (error) {
                console.error('Change password error:', error);
                showFlash('Błąd podczas zmiany hasła', 'error');
            } finally {
                this.passwordSubmitting = false;
            }
        },

        async exportData() {
            try {
                showFlash('Eksport danych zostanie zaimplementowany w przyszłej wersji', 'info');
            } catch (error) {
                console.error('Export error:', error);
                showFlash('Błąd podczas eksportu danych', 'error');
            }
        },

        async deleteAccount() {
            if (!this.deletePassword) return;

            if (!confirm('Czy na pewno chcesz usunąć swoje konto? Ta operacja jest nieodwracalna!')) {
                return;
            }

            try {
                showFlash('Usuwanie konta zostanie zaimplementowane w przyszłej wersji', 'warning');
                this.showDeleteModal = false;
                this.deletePassword = '';
            } catch (error) {
                console.error('Delete account error:', error);
                showFlash('Błąd podczas usuwania konta', 'error');
            }
        }
    }
}
</script>
@endsection