<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): View
    {
        if (Auth::user()->isAdmin) {
            $dashboard = $this->dashboardService->getDashboardData();
            return view('home', compact('dashboard'));
        }

        return view('home');
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
