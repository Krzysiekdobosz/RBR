# 🐳 RBR Tasks - Docker Setup

## Szybki start

1. **Skopiuj wszystkie pliki Docker do swojego projektu**
2. **Uruchom skrypt setup:**
   ```bash
   chmod +x docker-setup.sh
   ./docker-setup.sh
   ```
3. **Otwórz przeglądarkę:** http://localhost:8080


### 🔧 Serwisy
- **app** - Aplikacja Laravel (PHP 8.2 + FPM)
- **webserver** - Nginx 
- **db** - MySQL 8.0
- **redis** - Redis (cache, sessions, queue)
- **queue** - Laravel Queue Worker
- **scheduler** - Laravel Task Scheduler (cron)
- **mailhog** - Test maili

### 🌐 Porty
- **8080** - Aplikacja główna
- **8025** - MailHog UI (testowanie maili)
- **3306** - MySQL (dostęp zewnętrzny)


## 🚀 Komendy

### Podstawowe
```bash
# Uruchomienie
docker-compose up -d

# Zatrzymanie
docker-compose down

# Restart
docker-compose restart

# Logi
docker-compose logs app
docker-compose logs -f app  # na żywo
```

### Praca z aplikacją
```bash
# Wejście do kontenera
docker-compose exec app bash

# Artisan komendy
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller UserController
docker-compose exec app php artisan queue:work

# Composer
docker-compose exec app composer install
docker-compose exec app composer require package/name
```

### 🧪 Testowanie maili

1. **Wyślij testowy mail:**
   ```bash
   docker-compose exec app php artisan tinker
   ```
   ```php
   $user = User::first();
   $task = Task::first();
   $user->notify(new App\Notifications\TaskReminderNotification($task));
   ```

2. **Sprawdź w MailHog:** http://localhost:8025

### 📅 Testowanie schedulera

```bash
# Sprawdź czy scheduler działa
docker-compose exec app php artisan schedule:list

# Uruchom ręcznie
docker-compose exec app php artisan tasks:send-reminders

# Zobacz logi schedulera
docker-compose logs scheduler
```


### Przebudowanie kontenerów
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Reset bazy danych
```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Cache cleanup
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```
