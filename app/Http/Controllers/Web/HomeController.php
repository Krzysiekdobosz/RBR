<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Summary of dashboard
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        
        // Używamy metod z modelu User
        $stats = $user->getTasksCountByStatus();
        $overdueCount = $user->getOverdueTasksCount();
        
        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'overdueCount' => $overdueCount,
        ]);
    }

    /**
     * Summary of profile
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function profile(Request $request): View
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }
}