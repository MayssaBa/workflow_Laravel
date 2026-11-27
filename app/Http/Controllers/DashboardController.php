<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(DashboardService $dashboardService): View
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view(
                'dashboard.admin',
                $dashboardService->adminDashboard()
            );
        }

        if ($user->isManager()) {
            return view(
                'dashboard.manager',
                $dashboardService->managerDashboard()
            );
        }

        abort(403, 'You are not authorized to access this dashboard.');
    }
}
