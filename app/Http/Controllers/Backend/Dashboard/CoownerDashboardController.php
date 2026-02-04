<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Backend\CoownerDashboardService;
use App\Services\PaymentsService;
use App\Support\Helper;
use Illuminate\Http\Request;

class CoownerDashboardController extends Controller
{
    protected $dashboardService;
    protected $paymentsService;

    public function __construct(CoownerDashboardService $dashboardService, PaymentsService $paymentsService, Helper $helper)
    {
        parent::__construct($helper);
        $this->dashboardService = $dashboardService;
        $this->paymentsService = $paymentsService;
    }

    public function index()
    {
        $dwellerId = $this->paymentsService->getDwellerID();
        if (!$dwellerId) {
            return redirect()->route('dashboard')->with('error', 'Only co-owners can access this dashboard.');
        }

        $data = $this->dashboardService->getDashboardData($dwellerId);
        $paymentSummary = $this->paymentsService->getDataCards(null, $dwellerId);

        return view('backend.dashboard.coowner', compact('data', 'paymentSummary'));
    }
}
