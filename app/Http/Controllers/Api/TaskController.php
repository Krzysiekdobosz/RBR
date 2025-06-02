<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\SharedTaskToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskController extends Controller
{

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::where('user_id', auth()->id());

        // Filtrowanie
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        if ($request->boolean('overdue')) {
            $query->where('due_date', '<', now())->whereNot('status', 'done');
        }

        $sortBy = $request->get('sort_by', 'due_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $tasks = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:to-do,in_progress,done',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $task = Task::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Zadanie zostało utworzone pomyślnie',
            'data' => $task
        ], 201);
    }

    /**
     * Summary of show
     * @param \App\Models\Task $task
     * @return JsonResponse|mixed
     */
    public function show(Task $task): JsonResponse
    {
        // Sprawdź uprawnienia
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Brak uprawnień do tego zadania'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return JsonResponse|mixed
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        // Sprawdź uprawnienia
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Brak uprawnień do tego zadania'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:to-do,in_progress,done',
            'due_date' => 'sometimes|required|date',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Zadanie zostało zaktualizowane pomyślnie',
            'data' => $task->fresh()
        ]);
    }

    /**
     * Summary of destroy
     * @param \App\Models\Task $task
     * @return JsonResponse|mixed
     */
    public function destroy(Task $task): JsonResponse
    {
        // Sprawdź uprawnienia
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Brak uprawnień do tego zadania'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Zadanie zostało usunięte pomyślnie'
        ]);
    }

    /**
     * Summary of generateShareToken
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return JsonResponse|mixed
     */
    public function generateShareToken(Request $request, Task $task): JsonResponse
    {
        // Sprawdź uprawnienia
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Brak uprawnień do tego zadania'
            ], 403);
        }

        $validated = $request->validate([
            'expiry_hours' => 'required|integer|min:1|max:168'
        ]);

        SharedTaskToken::where('task_id', $task->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $shareToken = SharedTaskToken::create([
            'task_id' => $task->id,
            'token' => Str::random(32),
            'expires_at' => Carbon::now()->addHours($validated['expiry_hours']),
            'is_active' => true,
        ]);

        $shareUrl = url("/shared-tasks/{$shareToken->token}");

        return response()->json([
            'success' => true,
            'message' => 'Link udostępniania został wygenerowany',
            'data' => [
                'token' => $shareToken->token,
                'url' => $shareUrl,
                'expires_at' => $shareToken->expires_at->toISOString(),
            ]
        ]);
    }

    /**
     * Summary of bulkUpdate
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|mixed
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
            'action' => 'required|in:status,priority,delete',
            'value' => 'required_unless:action,delete',
        ]);

        // Sprawdź uprawnienia
        $tasks = Task::whereIn('id', $validated['task_ids'])
            ->where('user_id', auth()->id())
            ->get();

        if ($tasks->count() !== count($validated['task_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Niektóre zadania nie należą do Ciebie'
            ], 403);
        }

        switch ($validated['action']) {
            case 'status':
                $tasks->each(function ($task) use ($validated) {
                    $task->update(['status' => $validated['value']]);
                });
                break;

            case 'priority':
                $tasks->each(function ($task) use ($validated) {
                    $task->update(['priority' => $validated['value']]);
                });
                break;

            case 'delete':
                $tasks->each->delete();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Operacja wykonana pomyślnie'
        ]);
    }
}