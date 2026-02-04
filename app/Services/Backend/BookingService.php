<?php

namespace App\Services\Backend;

use App\Models\CommonAreaBooking;
use App\Services\BaseService;

class BookingService extends BaseService
{
    protected $notificationService;

    public function __construct(\App\Services\CommonAreaNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new booking with atomic validation.
     */
    public function createBooking(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $start = \Carbon\Carbon::parse($data['start_time']);
            $end = \Carbon\Carbon::parse($data['end_time']);

            // Lock the common area for update to prevent race conditions
            $commonArea = \App\Models\CommonArea::where('id', $data['common_area_id'])->lockForUpdate()->firstOrFail();

            $availability = $this->checkAvailability($commonArea->id, $start, $end);
            if (!$availability['status']) {
                return $availability;
            }

            $feeDetails = $this->calculateFee($commonArea->id, $start, $end);

            $booking = CommonAreaBooking::create([
                'unit_id' => $data['unit_id'],
                'common_area_id' => $commonArea->id,
                'start_time' => $start,
                'end_time' => $end,
                'status' => 'pending',
                'total_amount' => $feeDetails['base_amount'],
                'currency' => $feeDetails['base_currency'],
                'exchange_rate' => $feeDetails['exchange_rate'],
            ]);

            $this->notificationService->notifyBookingCreated($booking);

            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $booking);
        }, 'Booking creation failed');
    }

    /**
     * Check if a common area is available for a given time range.
     */
    public function checkAvailability(string $commonAreaId, \Carbon\Carbon $start, \Carbon\Carbon $end, ?string $excludeBookingId = null): array
    {
        $commonArea = \App\Models\CommonArea::findOrFail($commonAreaId);

        if (!$commonArea->is_active) {
            return $this->error(__('The common area is currently inactive.'));
        }

        $dayOfWeek = $start->dayOfWeek;
        $schedule = $commonArea->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$schedule) {
            return $this->error(__('The common area is closed on this day.'));
        }

        $openingTime = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->setDateFrom($start);
        $closingTime = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->setDateFrom($start);

        if ($start->lt($openingTime) || $end->gt($closingTime)) {
            return $this->error(__('The selected time is outside the opening hours.') . " ({$schedule->start_time} - {$schedule->end_time})");
        }

        $overlap = CommonAreaBooking::where('common_area_id', $commonAreaId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('start_time', '<', $end)
                      ->where('end_time', '>', $start);
                });
            })
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->exists();

        if ($overlap) {
            return $this->error(__('The common area is already reserved for the selected time.'));
        }

        $now = now();
        $anticipationLimit = $now->addHours($commonArea->min_anticipation_hours);
        if ($start->lt($anticipationLimit)) {
            return $this->error(__('Reservations must be made with at least :hours hours of anticipation.', ['hours' => $commonArea->min_anticipation_hours]));
        }

        return $this->success(__('Available'));
    }

    /**
     * Calculate the fee for a booking, considering multi-currency and BCV rate.
     */
    public function calculateFee(string $commonAreaId, \Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $commonArea = \App\Models\CommonArea::findOrFail($commonAreaId);
        $durationInHours = $start->diffInMinutes($end) / 60;

        $baseAmount = $commonArea->booking_fee;
        if ($commonArea->pricing_type === 'hourly') {
            $baseAmount = $commonArea->booking_fee * $durationInHours;
        }

        $currency = $commonArea->currency;
        $exchangeRate = \App\Models\BcvExchangeRate::latestRate()?->used_for_calculations ?? 1;

        $amountUsd = $currency === 'USD' ? $baseAmount : $baseAmount / $exchangeRate;
        $amountBs = $currency === 'BS' ? $baseAmount : $baseAmount * $exchangeRate;

        return [
            'base_amount' => round($baseAmount, 2),
            'base_currency' => $currency,
            'amount_usd' => round($amountUsd, 2),
            'amount_bs' => round($amountBs, 2),
            'exchange_rate' => $exchangeRate,
        ];
    }

    /**
     * Cancel a booking and calculate penalties.
     */
    public function cancelBooking(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $booking = CommonAreaBooking::with('commonArea')->findOrFail($id);

            if ($booking->status === 'cancelled') {
                return $this->error(__('The booking is already cancelled.'));
            }

            $commonArea = $booking->commonArea;
            $now = now();
            $startTime = $booking->start_time;

            // Optional: check if cancellation is still allowed (e.g., hasn't started yet)
            if ($now->gt($startTime)) {
                return $this->error(__('Cannot cancel a booking that has already started.'));
            }

            // Calculate penalty if needed
            $penalty = 0;
            if ($commonArea->cancellation_penalty_percentage > 0 && $booking->total_amount > 0) {
                $penalty = $booking->total_amount * ($commonArea->cancellation_penalty_percentage / 100);
            }

            $booking->update([
                'status' => 'cancelled',
                // 'notes' => 'Cancelled with penalty: ' . $penalty // Optional: add notes column to migration
            ]);

            $message = trans(config('constants.MESSAGES.DATA_SUCCESS'));
            if ($penalty > 0) {
                // Logic to record penalty (e.g. as a debt)
            }

            $this->notificationService->notifyBookingCancelled($booking, $penalty);

            return $this->success($message, $booking);
        }, 'Booking cancellation failed');
    }

    public function updateBooking(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $booking = CommonAreaBooking::find($id);
            if (!$booking) return $this->error(trans('Booking not found'), [], 404);

            $oldStatus = $booking->status;
            $booking->update($data);

            if ($oldStatus !== 'confirmed' && $booking->status === 'confirmed') {
                $this->notificationService->notifyBookingConfirmed($booking);
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $booking);
        }, 'Booking update failed');
    }

    public function deleteBooking(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $booking = CommonAreaBooking::find($id);
            if (!$booking) return $this->error(trans('Booking not found'), [], 404);
            $booking->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Booking deletion failed');
    }
}
