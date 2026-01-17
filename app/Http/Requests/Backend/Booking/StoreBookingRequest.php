<?php

namespace App\Http\Requests\Backend\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_id' => 'required|exists:units,id',
            'common_area_id' => 'required|exists:common_areas,id',
            'booking_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'amount_paid' => 'required|numeric|min:0',
        ];
    }
}
