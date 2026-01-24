<?php

namespace App\Services\Backend;

use App\Models\AssemblySession;
use App\Models\CommonAreaBooking;
use App\Models\Debt;
use App\Models\IncidentReport;
use App\Models\Unit;
use App\Models\WorkOrder;
use App\Services\BaseService;
use Illuminate\Support\Carbon;

class CoownerDashboardService extends BaseService
{
    public function getDashboardData(string $dwellerId): array
    {
        return [
            'financial_summary' => $this->getFinancialSummary($dwellerId),
            'upcoming_assemblies' => $this->getUpcomingAssemblies(),
            'my_bookings' => $this->getMyBookings($dwellerId),
            'community_status' => $this->getCommunityStatus(),
        ];
    }

    private function getFinancialSummary(string $dwellerId): array
    {
        $totalDebt = Debt::whereHas('unit', function ($q) use ($dwellerId) {
            $q->where('dweller_id', $dwellerId);
        })->where('status', 'pending')->sum('total_amount');

        $units = Unit::where('dweller_id', $dwellerId)->count();

        return [
            'total_debt' => $totalDebt,
            'unit_count' => $units,
        ];
    }

    private function getUpcomingAssemblies(): array
    {
        return AssemblySession::where('scheduled_at', '>=', Carbon::now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->toArray();
    }

    private function getMyBookings(string $dwellerId): array
    {
        return CommonAreaBooking::where('dweller_id', $dwellerId)
            ->where('start_time', '>=', Carbon::now())
            ->orderBy('start_time', 'asc')
            ->with('commonArea')
            ->get()
            ->toArray();
    }

    private function getCommunityStatus(): array
    {
        return [
            'open_incidents' => IncidentReport::where('status', '!=', 'closed')->count(),
            'active_works' => WorkOrder::whereIn('status', ['assigned', 'in_progress'])->count(),
            'latest_works' => WorkOrder::latest()->take(5)->with('supplier')->get()->toArray(),
        ];
    }
}
