<?php

namespace App\Services;

use App\Models\CommonAreaBooking;
use App\Services\Communication\WhatsAppService;
use App\Support\Helper;
use Illuminate\Support\Facades\Mail;

class CommonAreaNotificationService
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    /**
     * Notify about a new booking.
     */
    public function notifyBookingCreated(CommonAreaBooking $booking)
    {
        $dweller = $booking->unit->dweller;
        if (!$dweller) return;

        $user = \App\Models\User::where('email', $dweller->email)->first();
        if (!$user) return;

        $message = "Hola {$dweller->name}, tu reserva para {$booking->commonArea->name} el día {$booking->start_time->format('d/m/Y')} a las {$booking->start_time->format('H:i')} ha sido recibida y está pendiente de confirmación.";

        // 1. In-app Notification
        Helper::sendNotification($booking, $user->id, [
            'title' => __('New Booking Received'),
            'content' => $message,
            'icon' => 'mdi mdi-calendar-clock',
            'color' => 'text-warning',
            'link' => route('resident.common-areas.history')
        ]);

        // 2. WhatsApp Notification
        $phone = $dweller->cell_phone_number ?? $dweller->phone_number;
        if ($phone) {
            $this->whatsapp->sendMessage($phone, $message);
        }

        // 3. Email Notification (Placeholder for existing Mail logic)
        // Mail::to($dweller->email)->send(new \App\Mail\BookingCreated($booking));
    }

    /**
     * Notify about booking confirmation.
     */
    public function notifyBookingConfirmed(CommonAreaBooking $booking)
    {
        $dweller = $booking->unit->dweller;
        if (!$dweller) return;

        $user = \App\Models\User::where('email', $dweller->email)->first();
        if (!$user) return;

        $message = "¡Confirmado! Tu reserva para {$booking->commonArea->name} ha sido confirmada para el {$booking->start_time->format('d/m/Y')} a las {$booking->start_time->format('H:i')}.";

        Helper::sendNotification($booking, $user->id, [
            'title' => __('Booking Confirmed'),
            'content' => $message,
            'icon' => 'mdi mdi-calendar-check',
            'color' => 'text-success',
            'link' => route('resident.common-areas.history')
        ]);

        $phone = $dweller->cell_phone_number ?? $dweller->phone_number;
        if ($phone) {
            $this->whatsapp->sendMessage($phone, $message);
        }
    }

    /**
     * Notify about booking cancellation.
     */
    public function notifyBookingCancelled(CommonAreaBooking $booking, $penalty = 0)
    {
        $dweller = $booking->unit->dweller;
        if (!$dweller) return;

        $user = \App\Models\User::where('email', $dweller->email)->first();
        if (!$user) return;

        $message = "Tu reserva para {$booking->commonArea->name} ha sido cancelada.";
        if ($penalty > 0) {
            $message .= " Se ha aplicado una penalización de {$booking->currency} " . number_format($penalty, 2) . ".";
        }

        Helper::sendNotification($booking, $user->id, [
            'title' => __('Booking Cancelled'),
            'content' => $message,
            'icon' => 'mdi mdi-calendar-remove',
            'color' => 'text-danger',
            'link' => route('resident.common-areas.history')
        ]);

        $phone = $dweller->cell_phone_number ?? $dweller->phone_number;
        if ($phone) {
            $this->whatsapp->sendMessage($phone, $message);
        }
    }
}
