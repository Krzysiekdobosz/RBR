#!/bin/bash

echo "🚀 Rozpoczynam konfigurację projektu RBR z Dockerem..."

# Sprawdź czy Docker jest zainstalowany
if ! command -v docker &> /dev/null; then
    echo "❌ Docker nie jest zainstalowany!"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose nie jest zainstalowany!"
    exit 1
fi

# Skopiuj .env.docker do .env
echo "📄 Kopiuję plik .env..."
cp .env.docker .env

# Zatrzymaj istniejące kontenery
echo "🛑 Zatrzymuję istniejące kontenery..."
docker-compose down

# Usuń stare obrazy (opcjonalnie)
read -p "🗑️  Czy chcesz usunąć stare obrazy Docker? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    docker-compose down --rmi all --volumes
fi

# Zbuduj i uruchom kontenery
echo "🔨 Buduję kontenery..."
docker-compose build --no-cache

echo "🚀 Uruchamiam kontenery..."
docker-compose up -d

# Czekaj na uruchomienie bazy danych
echo "⏳ Czekam na uruchomienie bazy danych..."
sleep 10

# Wygeneruj klucz aplikacji
echo "🔑 Generuję klucz aplikacji..."
docker-compose exec app php artisan key:generate

# Uruchom migracje
echo "🗃️  Uruchamiam migracje bazy danych..."
docker-compose exec app php artisan migrate

# Uruchom seedery (jeśli istnieją)
echo "🌱 Uruchamiam seedery..."
docker-compose exec app php artisan db:seed --class=DatabaseSeeder 2>/dev/null || echo "Brak seederów lub błąd - kontynuuję..."

# Wyczyść cache
echo "🧹 Czyszczę cache..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Ustaw uprawnienia
echo "🔐 Ustawiam uprawnienia..."
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

echo ""
echo "✅ Konfiguracja zakończona!"
echo ""
echo "🌐 Aplikacja dostępna pod adresem: http://localhost:8080"
echo "📧 MailHog (testowanie maili): http://localhost:8025"
echo "🗄️  MySQL dostępne na porcie: 3306"
echo ""
echo "📋 Przydatne komendy:"
echo "   docker-compose logs app          # Logi aplikacji"
echo "   docker-compose exec app bash     # Wejście do kontenera"
echo "   docker-compose restart           # Restart wszystkich kontenerów"
echo "   docker-compose down              # Zatrzymanie kontenerów"
echo ""
echo "🧪 Test maili:"
echo "   docker-compose exec app php artisan tinker"
echo "   >>> User::first()->notify(new App\\Notifications\\TaskReminderNotification(Task::first()))"
echo ""

# Pokaż status kontenerów
echo "📊 Status kontenerów:"
docker-compose ps