<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id', 'user_id',
        'name', 'description',
        'priority', 'status',
        'due_date', 'reminder_sent',
        'created_at', 'changes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'date',
        'reminder_sent' => 'boolean',
        'changes' => 'array', // automatycznie serializuje/deserializuje JSON
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pobiera czytelne etykiety dla pól
     */
    public function getFieldLabels(): array
    {
        return [
            'name' => 'Nazwa',
            'description' => 'Opis',
            'priority' => 'Priorytet',
            'status' => 'Status',
            'due_date' => 'Termin',
            'reminder_sent' => 'Przypomnienie wysłane',
        ];
    }

    /**
     * Pobiera czytelne wartości dla niektórych pól
     */
    public function getReadableValue($field, $value): string
    {
        return match ($field) {
            'priority' => match ($value) {
                'low' => 'Niski',
                'medium' => 'Średni',
                'high' => 'Wysoki',
                default => $value,
            },
            'status' => match ($value) {
                'to-do' => 'Do zrobienia',
                'in_progress' => 'W trakcie',
                'done' => 'Ukończone',
                default => $value,
            },
            'reminder_sent' => $value ? 'Tak' : 'Nie',
            'due_date' => $value ? \Carbon\Carbon::parse($value)->format('d.m.Y') : '',
            'description' => $value ?: '— brak opisu —',
            default => (string) $value,
        };
    }

    /**
     * Formatuje zmiany do wyświetlenia
     */
    public function getFormattedChanges(): array
    {
        if (!$this->changes) {
            return [];
        }

        $labels = $this->getFieldLabels();
        $formatted = [];

        foreach ($this->changes as $field => $change) {
            $fieldLabel = $labels[$field] ?? $field;
            
            $formatted[] = [
                'field' => $field,
                'label' => $fieldLabel,
                'old_value' => $this->getReadableValue($field, $change['old'] ?? null),
                'new_value' => $this->getReadableValue($field, $change['new'] ?? null),
            ];
        }

        return $formatted;
    }

    /**
     * Generuje opis akcji na podstawie zmian
     */
    public function getActionDescription(): string
    {
        if (!$this->changes) {
            return 'Edycja zadania';
        }

        $changedFields = array_keys($this->changes);
        $labels = $this->getFieldLabels();
        
        if (count($changedFields) === 1) {
            $field = $changedFields[0];
            $label = strtolower($labels[$field] ?? $field);
            return "Zmiana {$label}";
        }
        
        if (count($changedFields) === 2) {
            $field1 = strtolower($labels[$changedFields[0]] ?? $changedFields[0]);
            $field2 = strtolower($labels[$changedFields[1]] ?? $changedFields[1]);
            return "Zmiana {$field1} i {$field2}";
        }
        
        if (count($changedFields) <= 4) {
            $fieldNames = array_map(function($field) use ($labels) {
                return strtolower($labels[$field] ?? $field);
            }, $changedFields);
            
            $lastField = array_pop($fieldNames);
            return "Zmiana " . implode(', ', $fieldNames) . " i {$lastField}";
        }
        
        return "Edycja zadania (" . count($changedFields) . " zmian)";
    }
}