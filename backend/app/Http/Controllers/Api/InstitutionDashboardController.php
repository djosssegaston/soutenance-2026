<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;

class InstitutionDashboardController extends Controller
{
    protected $analyticsService;

    public function __construct(DashboardAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $institution = $request->user()->institution;

        if (! $institution) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'summary' => [
                        'portfolio' => ['total_funded' => 0, 'active_count' => 0, 'avg_roi' => 0],
                        'repayments' => ['rate' => 0, 'total_repaid' => 0, 'late_count' => 0],
                        'risks' => ['at_risk_count' => 0],
                        'pipeline' => ['analyzed_count' => 0],
                    ],
                    'recent_activity' => [],
                    'alerts' => [
                        'late_repayments' => [],
                        'high_risk_projects' => [],
                    ],
                ],
            ]);
        }

        $summary = $this->analyticsService->getSummaryMetrics($institution);
        $recentActivity = $this->analyticsService->getRecentActivity($institution);
        $alerts = $this->analyticsService->getAlerts($institution);

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => $summary,
                'recent_activity' => $recentActivity,
                'alerts' => $alerts,
            ],
        ]);
    }

    public function charts(Request $request)
    {
        $institution = $request->user()->institution;

        if (! $institution) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'funding_evolution' => [],
                    'sector_distribution' => [],
                    'repayment_status' => [],
                    'risk_portion' => [],
                ],
            ]);
        }

        $charts = $this->analyticsService->getChartData($institution);

        return response()->json([
            'status' => 'success',
            'data' => $charts,
        ]);
    }

    public function alerts(Request $request)
    {
        $institution = $request->user()->institution;

        if (! $institution) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'late_repayments' => [],
                    'high_risk_projects' => [],
                ],
            ]);
        }

        $alerts = $this->analyticsService->getAlerts($institution);

        return response()->json([
            'status' => 'success',
            'data' => $alerts,
        ]);
    }

    public function activity(Request $request)
    {
        $institution = $request->user()->institution;

        if (! $institution) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $activity = $this->analyticsService->getRecentActivity($institution);

        return response()->json([
            'status' => 'success',
            'data' => $activity,
        ]);
    }
}
