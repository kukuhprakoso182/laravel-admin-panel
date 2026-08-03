<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function index(): View
    {
        $user = Auth::user();

        return view('pages.dashboard.index', [
            'user' => $user,
            'summaryCards' => $this->dashboardService->getSummaryCards($user),
            'userGrowth' => $this->dashboardService->getUserGrowth($user, 7),
            'roleBreakdown' => $this->dashboardService->getRoleBreakdown($user),
            'recentActivities' => $this->dashboardService->getRecentActivities($user, 8),
            'quickLinks' => $this->dashboardService->getQuickLinks($user),
        ]);
    }
}
