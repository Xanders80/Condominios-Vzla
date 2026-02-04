<?php

namespace App\Http\Controllers\Backend\CommonArea;

use App\Http\Controllers\Controller;
use App\Models\CommonArea;
use App\Models\CommonAreaBooking;
use App\Services\Backend\BookingService;
use App\Services\PaymentsService;
use App\Support\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResidentCommonAreaController extends Controller
{
    protected $bookingService;

    protected $paymentsService;

    public function __construct(BookingService $bookingService, PaymentsService $paymentsService, Helper $helper)
    {
        parent::__construct($helper);
        $this->bookingService = $bookingService;
        $this->paymentsService = $paymentsService;
    }

    public function index()
    {
        $dwellerId = $this->paymentsService->getDwellerID();
        if (! $dwellerId) {
            return redirect()->route('dashboard')->with('error', 'Only residents can access this section.');
        }

        // Get the unit(s) associated with this dweller to find the condominium
        $unit = \App\Models\Unit::whereHas('dweller', function ($q) use ($dwellerId) {
            $q->where('id', $dwellerId);
        })->first();

        if (! $unit) {
            return redirect()->back()->with('error', 'No unit associated with your account.');
        }

        $areas = CommonArea::where('condominiums_id', $unit->condominiums_id)
            ->where('is_active', true)
            ->get();

        return view('backend.resident.common-areas.index', compact('areas'));
    }

    public function calendar(string $id)
    {
        $area = CommonArea::findOrFail($id);

        return view('backend.resident.common-areas.calendar', compact('area'));
    }

    public function book(Request $request)
    {
        $dwellerId = $this->paymentsService->getDwellerID();
        $unit = \App\Models\Unit::whereHas('dweller', function ($q) use ($dwellerId) {
            $q->where('id', $dwellerId);
        })->first();

        if (! $unit) {
            return response()->json(['status' => false, 'message' => 'No unit associated with your account.']);
        }

        $data = $request->all();
        $data['unit_id'] = $unit->id;
        $data['status'] = 'pending';

        if ($request->has('dry_run') && $request->dry_run) {
            $start = Carbon::parse($data['start_time']);
            $end = Carbon::parse($data['end_time']);

            $availability = $this->bookingService->checkAvailability($data['common_area_id'], $start, $end);
            if (! $availability['status']) {
                return response()->json($availability);
            }

            $fee = $this->bookingService->calculateFee($data['common_area_id'], $start, $end);

            return response()->json([
                'status' => true,
                'message' => 'Available',
                'data' => [
                    'fee' => $fee,
                ],
            ]);
        }

        $result = $this->bookingService->createBooking($data);

        return response()->json($result);
    }

    public function cancel(string $id)
    {
        $dwellerId = $this->paymentsService->getDwellerID();
        $unitIds = \App\Models\Unit::whereHas('dweller', function ($q) use ($dwellerId) {
            $q->where('id', $dwellerId);
        })->pluck('id');

        $booking = CommonAreaBooking::whereIn('unit_id', $unitIds)->findOrFail($id);

        $result = $this->bookingService->cancelBooking($booking->id);

        return response()->json($result);
    }

    public function history()
    {
        $dwellerId = $this->paymentsService->getDwellerID();
        if (! $dwellerId) {
            return redirect()->route('dashboard')->with('error', 'Only residents can access this section.');
        }

        $unitIds = \App\Models\Unit::whereHas('dweller', function ($q) use ($dwellerId) {
            $q->where('id', $dwellerId);
        })->pluck('id');

        $bookings = CommonAreaBooking::with('commonArea')
            ->whereIn('unit_id', $unitIds)
            ->orderBy('start_time', 'desc')
            ->get();

        return view('backend.resident.common-areas.history', compact('bookings'));
    }

    public function show(string $id)
    {
        $dwellerId = $this->paymentsService->getDwellerID();
        $unitIds = \App\Models\Unit::whereHas('dweller', function ($q) use ($dwellerId) {
            $q->where('id', $dwellerId);
        })->pluck('id');

        $booking = CommonAreaBooking::with('commonArea')
            ->whereIn('unit_id', $unitIds)
            ->findOrFail($id);

        return view('backend.resident.common-areas.show', compact('booking'));
    }
}
