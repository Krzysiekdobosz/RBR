<?php

namespace App\Http\Controllers;

use App\Models\SharedTaskToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SharedTaskController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $shareToken = SharedTaskToken::with('task.user')
            ->where('token', $token)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        if (!$shareToken) {
            return response()->json([
                'success' => false,
                'message' => 'Link jest nieprawidłowy lub wygasł'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'task' => $shareToken->task,
                'expires_at' => $shareToken->expires_at,
            ]
        ]);
    }

    public function deactivate(string $token): JsonResponse
    {
        $shareToken = SharedTaskToken::where('token', $token)->first();

        if (!$shareToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token nie został znaleziony'
            ], 404);
        }

        $shareToken->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Token został dezaktywowany'
        ]);
    }
}