<?php

namespace App\Services\Backend;

use App\Models\Dweller;
use App\Models\Payments;
use App\Models\Unit;
use App\Services\BaseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PaymentService extends BaseService
{
    /**
     * Tiempo de caché en segundos
     */
    private const CACHE_TIME = 3600;

    /**
     * Create a new payment.
     */
    public function createPayment(array $data, $image = null): array
    {
        return $this->executeTransaction(function () use ($data, $image) {
            $payment = Payments::create($data);
            if (!$payment) {
                return $this->error(trans(config('constants.MESSAGES.DATA_FAILED')));
            }

            if ($image) {
                $payment->file()->create([
                    'data' => [
                        'disk' => config('filesystems.default'),
                        'target' => Storage::putFile('payments/' . date('Y/m/d'), $image),
                        'name' => $image->getClientOriginalName(),
                    ],
                ]);
            }

            return $this->success(trans(config('constants.MESSAGES.MESS_CREATED')), $payment);
        }, 'Payment creation failed');
    }

    /**
     * Update an existing payment.
     */
    public function updatePayment(string $id, array $data, $image = null): array
    {
        return $this->executeTransaction(function () use ($id, $data, $image) {
            $payment = Payments::find($id);
            if (!$payment) {
                return $this->error(trans('Payment not found'), [], 404);
            }

            $payment->update($data);

            if ($image) {
                $payment->file?->forceDelete();
                $payment->file()->create([
                    'data' => [
                        'disk' => config('filesystems.default'),
                        'target' => Storage::putFile('payments/' . date('Y/m/d'), $image),
                        'name' => $image->getClientOriginalName(),
                    ],
                ]);
            }

            return $this->success(trans(config('constants.MESSAGES.DATA_SUCCESS')), $payment);
        }, 'Payment update failed');
    }

    /**
     * Delete a payment.
     */
    public function deletePayment(string $id): array
    {
        return $this->executeTransaction(function () use ($id) {
            $payment = Payments::find($id);
            if (!$payment) {
                return $this->error(trans('Payment not found'), [], 404);
            }
            $payment->delete();
            return $this->success(trans(config('constants.MESSAGES.DATA_DELETE_SUCCESS')));
        }, 'Payment deletion failed');
    }

    /**
     * Conciliate a payment.
     */
    public function conciliatePayment(string $id, bool $status): array
    {
        return $this->executeTransaction(function () use ($id, $status) {
            $payment = Payments::find($id);
            if (!$payment) {
                return $this->error(trans('Payment not found'), [], 404);
            }
            $payment->update([
                'conciliated' => $status,
                'date_confirm' => $status ? now() : null
            ]);
            return $this->success(trans('Payment conciliation updated'));
        }, 'Payment conciliation failed');
    }

    // --- Legacy logic from PaymentsService (consolidated) ---

    public function getDataCards(?int $year = null, ?string $dwellerId = null, ?int $month = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $payments = $this->getPaymentsForYear($year, $dwellerId, $month);

        return $this->formatDataCards(
            year: $year,
            totalPayments: $payments->count(),
            totalAmount: $payments->sum('amount'),
            dwellersWithPayments: $payments->unique('dweller_id')->count(),
            totalAccumulated: $this->getTotalAccumulated($dwellerId)
        );
    }

    private function formatDataCards(int $year, int $totalPayments, float $totalAmount, int $dwellersWithPayments, float $totalAccumulated): array
    {
        $data = [
            [
                'label' => __('Accumulated'),
                'message' => number_format($totalAccumulated, 2, ',', '.'),
                'sub_message' => '',
                'end_text' => __('Total Life Time'),
                'icon' => 'mdi mdi-currency-usd mdi-36px'
            ],
            [
                'label' => __('Payments Made'),
                'message' => $totalPayments,
                'sub_message' => '',
                'end_text' => sprintf(__('Year') . ': %s', $year),
                'icon' => 'mdi mdi-numeric-9-plus-box-multiple-outline mdi-36px'
            ],
            [
                'label' => __('Amount'),
                'message' => number_format($totalAmount, 2, ',', '.'),
                'sub_message' => '',
                'end_text' => sprintf(__('Year') . ': %s', $year),
                'icon' => 'mdi mdi-cash-multiple mdi-36px'
            ]
        ];
        return collect($data)->when(
            auth()->user()->level->code !== 'user',
            function ($collection) use ($dwellersWithPayments, $year) {
                return $collection->push([
                    'label' => __('Dwellers with Payments'),
                    'message' => $dwellersWithPayments,
                    'sub_message' => '',
                    'end_text' => sprintf(__('Year') . ': %s', $year),
                    'icon' => 'mdi mdi-account-multiple mdi-36px'
                ]);
            }
        )->all();
    }

    public function getYears(): array
    {
        return Cache::remember(
            'payment_years',
            self::CACHE_TIME,
            fn() =>
            Payments::selectRaw('YEAR(created_at) as year')
                ->groupBy('year')
                ->orderByDesc('year')
                ->pluck('year')
                ->toArray()
        );
    }

    public function getMonthsForYear(int $year): array
    {
        return Cache::remember(
            "payment_months_{$year}",
            self::CACHE_TIME,
            fn() =>
            Payments::whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as month')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('month')
                ->toArray()
        );
    }

    private function getPaymentsForYear(int $year, ?string $dwellerId = null, ?int $month = null)
    {
        return Payments::when($dwellerId, fn($query) => $query->where('dweller_id', $dwellerId))
            ->when($month, fn($query) => $query->whereMonth('created_at', $month))
            ->whereYear('created_at', $year)
            ->get();
    }

    private function getTotalAccumulated(?string $dwellerId = null): float
    {
        $key = $dwellerId ? "total_accumulated_{$dwellerId}" : 'total_accumulated_all';
        return Cache::remember(
            $key,
            self::CACHE_TIME,
            fn() =>
            Payments::when($dwellerId, fn($query) => $query->where('dweller_id', $dwellerId))
                ->sum('amount')
        );
    }

    public function getUnitCount(): int
    {
        return Unit::where('dweller_id', $this->getDwellerID())->count();
    }

    public function getDwellerID(): ?string
    {
        $user = auth()->user();
        return $user->level->code === 'user' ? Dweller::where('email', $user->email)->value('id') : null;
    }

    /**
     * Obtiene datos de pagos por mes para un año específico.
     */
    public function getPaymentDataMonthByYear(int $year, ?string $dwellerId = null): array
    {
        $payments = $this->getPaymentsForYear($year, $dwellerId)
            ->groupBy(fn($payment) => Carbon::parse($payment->created_at)->format('Y-m'))
            ->map(fn($group) => [
                'year' => Carbon::parse($group->first()->created_at)->year,
                'month' => Carbon::parse($group->first()->created_at)->month,
                'total' => $group->sum('amount')
            ])
            ->sortBy('month')
            ->values();

        return $this->formatPaymentData($payments);
    }

    /**
     * Obtiene datos de pagos por año.
     */
    public function getPaymentDataByYear(?string $dwellerId = null): array
    {
        $payments = Payments::when($dwellerId, fn($query) => $query->where('dweller_id', $dwellerId))
            ->get()
            ->groupBy(fn($payment) => Carbon::parse($payment->created_at)->year)
            ->map(fn($group) => [
                'year' => Carbon::parse($group->first()->created_at)->year,
                'total' => $group->sum('amount')
            ])
            ->values();

        return $this->formatPaymentData($payments);
    }

    /**
     * Formatea datos de pagos para gráficos.
     */
    private function formatPaymentData(\Illuminate\Support\Collection $payments): array
    {
        $data = [];

        foreach ($payments as $payment) {
            $label = isset($payment['month'])
                ? Carbon::createFromFormat('m', $payment['month'])->translatedFormat('F')
                : $payment['year'];

            $data[$label] = $payment['total'];
        }

        return [
            'labels' => array_keys($data),
            'data' => array_values($data)
        ];
    }
}
