<?php
// app/Http/Controllers/Web/SharedTaskWebController.php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SharedTaskToken;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SharedTaskWebController extends Controller
{
    public function show(string $token): View
    {
        $shareToken = SharedTaskToken::with('task.user')
            ->where('token', $token)
            ->active()
            ->first();

        if (!$shareToken) {
            abort(404, 'Link jest nieprawidłowy lub wygasł');
        }

        return view('shared.task', [
            'task' => $shareToken->task,
            'shareToken' => $shareToken,
        ]);
    }
}