<?php

namespace App\Services\Backend;

use App\Models\CommonAreaBooking;
use App\Services\BaseService;

class BookingService extends BaseService
{
    public function createBooking(array $data): array
    {
        return $this->executeTransaction(function () use ($data) {
            $booking = CommonAreaBooking::create($data);
            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $booking);
        }, 'Booking creation failed');
    }

    public function updateBooking(string $id, array $data): array
    {
        return $this->executeTransaction(function () use ($id, $data) {
            $booking = CommonAreaBooking::find($id);
            if (!$booking) return $this->error(trans('Booking not found'), [], 404);
            $booking->update($data);
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
