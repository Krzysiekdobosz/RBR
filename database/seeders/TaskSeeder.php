<?php
// database/seeders/TaskSeeder.php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Brak użytkowników. Uruchom najpierw UserSeeder.');
            return;
        }

        $sampleTasks = [
            [
                'name' => 'Przygotować raport miesięczny',
                'description' => 'Zebrać dane sprzedażowe i przygotować raport za październik',
                'priority' => 'high',
                'status' => 'to-do',
                'due_date' => Carbon::tomorrow(),
            ],
            [
                'name' => 'Spotkanie z klientem',
                'description' => 'Omówić wymagania nowego projektu',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => Carbon::today()->addDays(3),
            ],
            [
                'name' => 'Code review',
                'description' => 'Przejrzeć kod w PR #123',
                'priority' => 'medium',
                'status' => 'to-do',
                'due_date' => Carbon::today()->addDays(2),
            ],
            [
                'name' => 'Aktualizacja dokumentacji',
                'description' => 'Zaktualizować README i dokumentację API',
                'priority' => 'low',
                'status' => 'done',
                'due_date' => Carbon::yesterday(),
            ],
            [
                'name' => 'Backup bazy danych',
                'description' => 'Wykonać cotygodniowy backup produkcyjnej bazy',
                'priority' => 'high',
                'status' => 'to-do',
                'due_date' => Carbon::today()->addWeek(),
            ],
            [
                'name' => 'Szkolenie z nowych technologii',
                'description' => 'Uczestnictwo w webinarze o Vue 3 Composition API',
                'priority' => 'medium',
                'status' => 'to-do',
                'due_date' => Carbon::today()->addDays(5),
            ],
            [
                'name' => 'Optymalizacja zapytań SQL',
                'description' => 'Zoptymalizować wolne zapytania w module raportów',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => Carbon::today()->addDays(4),
            ],
            [
                'name' => 'Testy jednostkowe',
                'description' => 'Napisać testy dla nowego modułu uwierzytelniania',
                'priority' => 'medium',
                'status' => 'to-do',
                'due_date' => Carbon::today()->addDays(6),
            ],
        ];

        foreach ($users as $user) {
            $userTasks = collect($sampleTasks)->shuffle()->take(rand(3, 6));
            
            foreach ($userTasks as $taskData) {
                Task::create(array_merge($taskData, [
                    'user_id' => $user->id,
                    'due_date' => Carbon::parse($taskData['due_date'])->addDays(rand(-2, 5)),
                ]));
            }
        }

        $this->command->info('Dodano przykładowe zadania dla wszystkich użytkowników.');
    }
}