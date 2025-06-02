<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SharedTaskToken;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SharedTaskController extends Controller
{
    /**
     * Wyświetl udostępnione zadanie (widok HTML)
     */
    public function show(string $token): View
    {
        $shareToken = SharedTaskToken::with('task.user')
            ->where('token', $token)
            ->active()
            ->first();

        if (!$shareToken) {
            abort(404, 'Link jest nieprawidłowy lub wygasł');
        }

        $task = $shareToken->task;

        return view('shared.show', compact('task', 'shareToken'));
    }
}