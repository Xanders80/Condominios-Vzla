<?php

namespace App\Http\Requests\Backend\Booking;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_date' => 'sometimes|date',
            'status' => 'sometimes|in:pending,confirmed,cancelled,completed',
            'amount_paid' => 'sometimes|numeric|min:0',
        ];
    }
}
