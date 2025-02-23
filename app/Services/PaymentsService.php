<?php

namespace App\Services;

use App\Models\Dweller;
use App\Models\Payments;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class PaymentsService
{
    /**
     * Tiempo de caché en segundos
     */
    private const CACHE_TIME = 3600;

    /**
     * Obtiene los datos para las tarjetas del dashboard.
     *
     * @param int|null $year
     * @param string|null $dwellerId
     * @param int|null $month
     * @return array
     */
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

    /**
     * Formatea los datos de las tarjetas del dashboard.
     *
     * @param int $year
     * @param int $totalPayments
     * @param float $totalAmount
     * @param int $dwellersWithPayments
     * @param float $totalAccumulated
     * @return array
     */
    private function formatDataCards(
        int $year,
        int $totalPayments,
        float $totalAmount,
        int $dwellersWithPayments,
        float $totalAccumulated
    ): array {
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

    /**
     * Obtiene los años únicos de los pagos con caché.
     *
     * @return array
     */
    public function getYears(): array
    {
        return Cache::remember(
            key: 'payment_years',
            ttl: self::CACHE_TIME,
            callback: fn() => Payments::selectRaw('YEAR(created_at) as year')
                ->groupBy('year')
                ->orderByDesc('year')
                ->pluck('year')
                ->toArray()
        );
    }

    /**
     * Obtiene los meses con pagos para un año específico.
     *
     * @param int $year
     * @return array
     */
    public function getMonthsForYear(int $year): array
    {
        $key = "payment_months_{$year}";

        return Cache::remember(
            key: $key,
            ttl: self::CACHE_TIME,
            callback: fn() => Payments::whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as month')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('month')
                ->toArray()
        );
    }

    /**
     * Obtiene los pagos de un año con filtros opcionales.
     *
     * @param int $year
     * @param string|null $dwellerId
     * @param int|null $month
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPaymentsForYear(int $year, ?string $dwellerId = null, ?int $month = null)
    {
        return Payments::when($dwellerId, fn($query) => $query->where('dweller_id', $dwellerId))
            ->when($month, fn($query) => $query->whereMonth('created_at', $month))
            ->whereYear('created_at', $year)
            ->get();
    }

    /**
     * Obtiene el total acumulado de pagos con caché.
     *
     * @param string|null $dwellerId
     * @return float
     */
    private function getTotalAccumulated(?string $dwellerId = null): float
    {
        $key = "total_accumulated_{$dwellerId}";

        return Cache::remember(
            key: $key,
            ttl: self::CACHE_TIME,
            callback: fn() => Payments::when($dwellerId, fn($query) => $query->where('dweller_id', $dwellerId))
                ->sum('amount')
        );
    }

    /**
     * Obtiene datos de pagos por mes para un año específico.
     *
     * @param int $year
     * @param string|null $dwellerId
     * @return array
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
     *
     * @param string|null $dwellerId
     * @return array
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
     *
     * @param \Illuminate\Support\Collection $payments
     * @return array
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

    /**
     * Obtiene el conteo de unidades de un usuario.
     *
     * @return int
     */
    public function getUnitCount(): int
    {
        return Unit::where('dweller_id', $this->getDwellerID())
            ->count();
    }

    /**
     * Obtiene el ID del dweller autenticado.
     *
     * @return string|null
     */
    public function getDwellerID(): ?string
    {
        $user = auth()->user();

        return $user->level->code === 'user'
            ? Dweller::where('email', $user->email)->value('id')
            : null;
    }
}
