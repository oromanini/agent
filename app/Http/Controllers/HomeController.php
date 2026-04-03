<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): Response
    {
        if (Auth::user()->isAdmin) {
            $dashboard = $this->dashboardService->getDashboardData();

            return Inertia::render('Home', [
                'dashboard' => [
                    'proposals' => $dashboard['proposals'],
                    'proposals_sent_count' => $dashboard['proposals_sent_count'],
                    'closed_proposals_count' => $dashboard['closed_proposals_count'],
                    'average_ticket' => $dashboard['average_ticket'],
                    'total_sales' => $dashboard['total_sales'],
                    'proposals_sent_clients' => $dashboard['proposals_sent_clients']->values()->toArray(),
                    'closed_proposals_clients' => $dashboard['closed_proposals_clients']->values()->toArray(),
                ],
                'isAdmin' => true,
                'authUserPermission' => Auth::user()->permission,
            ]);
        }

        return Inertia::render('Home', [
            'dashboard' => null,
            'isAdmin' => false,
            'authUserPermission' => Auth::user()->permission,
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
