<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        
        return view('dashboard', [
            'user' => $user,
            'stats' => $user->getTasksCountByStatus(),
            'overdueCount' => $user->getOverdueTasksCount(),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }
}