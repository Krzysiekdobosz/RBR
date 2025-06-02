@extends('layouts.app')

@section('title', 'Wersja z ' . $version->created_at->format('d.m.Y H:i') . ' — ' . $task->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <a href="{{ route('tasks.show', $task) }}" 
       class="text-indigo-600 hover:underline block mb-6">
        ← wróć do bieżącej wersji
    </a>

    <div class="bg-white shadow rounded-lg p-6 space-y-6">
        <div>
            <h1 class="text-2xl font-bold">{{ $version->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Migawka z {{ $version->created_at->format('d.m.Y H:i') }}
            </p>
        </div>

        @if($version->changes)
            <div>
                <ul class="text-sm text-blue-700 space-y-1">
                    @foreach($version->getFormattedChanges() as $change)
                        <li>
                            <strong>{{ $change['label'] }}:</strong>
                            @if($change['old_value'] && $change['new_value'])
                                <span class="text-red-600">{{ $change['old_value'] }}</span>
                                →
                                <span class="text-green-600">{{ $change['new_value'] }}</span>
                            @elseif($change['new_value'])
                                dodano: <span class="text-green-600">{{ $change['new_value'] }}</span>
                            @elseif($change['old_value'])
                                usunięto: <span class="text-red-600">{{ $change['old_value'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="prose max-w-none">
            {{ $version->description ?: '— brak opisu —' }}
        </div>

        <div class="flex flex-wrap gap-2">
            <span class="badge {{ $version->status === 'done' ? 'bg-green-100 text-green-800' :
                                  ($version->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800'
                                                                       : 'bg-blue-100 text-blue-800') }}">
                Status: {{ $version->getReadableValue('status', $version->status) }}
            </span>

            <span class="badge {{ $version->priority === 'high' ? 'bg-red-100 text-red-800' :
                                  ($version->priority === 'medium' ? 'bg-yellow-100 text-yellow-800'
                                                                    : 'bg-green-100 text-green-800') }}">
                Priorytet: {{ $version->getReadableValue('priority', $version->priority) }}
            </span>

            <span class="badge bg-gray-100 text-gray-800">
                Termin: {{ $version->due_date->format('d.m.Y') }}
            </span>

            <span class="badge bg-gray-100 text-gray-800">
                Przypomnienie: {{ $version->getReadableValue('reminder_sent', $version->reminder_sent) }}
            </span>
        </div>
    </div>
</div>

<style>
.badge {
    @apply px-2 py-1 text-xs font-semibold rounded-full;
}
</style>
@endsection