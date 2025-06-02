<?php

namespace App\Http\Middleware;

use App\Models\SharedTaskToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSharedTaskToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token jest wymagany'
            ], 400);
        }

        $shareToken = SharedTaskToken::where('token', $token)
            ->active()
            ->first();

        if (!$shareToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token jest nieprawidłowy lub wygasł'
            ], 404);
        }

        $request->merge(['share_token' => $shareToken]);

        return $next($request);
    }
}