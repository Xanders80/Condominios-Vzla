<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\Debt;
use App\Models\InterestCalculation;
use App\Models\CollectionNotice;
use Carbon\Carbon;

class JudicialCollectionReportService
{
    /**
     * Generate a detailed dataset for a judicial collection folder.
     */
    public function generateForUnit(Unit $unit): array
    {
        $unit->load(['debts.receipt', 'debts.interestCalculations', 'collectionNotices']);

        $report = [
            'unit_info' => [
                'name' => $unit->name,
                'condominium' => $unit->condominium->name ?? 'N/A',
                'piso' => $unit->floorStreet->name ?? 'N/A',
            ],
            'debts_summary' => [],
            'notifications_trail' => [],
            'total_capital' => 0,
            'total_interests' => 0,
            'grand_total' => 0,
        ];

        foreach ($unit->debts as $debt) {
            $interests = $debt->interestCalculations->sum('interest_amount');
            $report['debts_summary'][] = [
                'receipt' => $debt->receipt->receipt_number ?? 'Historico',
                'due_date' => $debt->due_date->format('d/m/Y'),
                'capital' => $debt->amount,
                'interests' => $interests,
                'total' => $debt->amount + $interests,
                'status' => $debt->status,
            ];

            $report['total_capital'] += $debt->amount;
            $report['total_interests'] += $interests;
        }

        foreach ($unit->collectionNotices as $notice) {
            $report['notifications_trail'][] = [
                'type' => $notice->notice_type,
                'sent_at' => $notice->sent_at->format('d/m/Y H:i'),
                'hash' => $notice->content_hash,
            ];
        }

        $report['grand_total'] = $report['total_capital'] + $report['total_interests'];

        return $report;
    }
}
