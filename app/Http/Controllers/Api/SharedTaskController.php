<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SharedTaskToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SharedTaskController extends Controller
{
    /**
     * Summary of show
     * @param string $token
     * @return JsonResponse|mixed
     */
    public function show(string $token): JsonResponse
    {
        $shareToken = SharedTaskToken::with('task.user')
            ->where('token', $token)
            ->active()
            ->first();

        if (!$shareToken) {
            return response()->json([
                'success' => false,
                'message' => 'Link jest nieprawidłowy lub wygasł'
            ], 404);
        }

        $task = $shareToken->task;
        
        return response()->json([
            'success' => true,
            'data' => [
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'due_date' => $task->due_date,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                    'is_overdue' => $task->is_overdue,
                    'days_until_due' => $task->days_until_due,
                ],
                'owner' => [
                    'name' => $task->user->name,
                ],
                'share_info' => [
                    'expires_at' => $shareToken->expires_at,
                    'shared_at' => $shareToken->created_at,
                ]
            ]
        ]);
    }

    /**
     * Summary of deactivate
     * @param \Illuminate\Http\Request $request
     * @param string $token
     * @return JsonResponse|mixed
     */
    public function deactivate(Request $request, string $token): JsonResponse
    {
        $shareToken = SharedTaskToken::with('task')
            ->where('token', $token)
            ->active()
            ->first();

        if (!$shareToken) {
            return response()->json([
                'success' => false,
                'message' => 'Link jest nieprawidłowy lub wygasł'
            ], 404);
        }

        // Sprawdź czy użytkownik jest właścicielem zadania
        if ($shareToken->task->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Brak uprawnień do dezaktywacji tego tokenu'
            ], 403);
        }

        $shareToken->deactivate();

        return response()->json([
            'success' => true,
            'message' => 'Link został dezaktywowany'
        ]);
    }
}