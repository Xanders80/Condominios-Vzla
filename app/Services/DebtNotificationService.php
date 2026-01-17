<?php

namespace App\Services;

use App\Models\Unit;
use App\Models\Debt;
use App\Models\CollectionNotice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DebtNotificationService
{
    /**
     * Generate a formal collection notice (Acta de Notificación).
     */
    public function sendFormalAct(Unit $unit): CollectionNotice
    {
        $totalDebt = $unit->debts()->where('status', '!=', 'paid')->sum('amount');
        $debts = $unit->debts()->where('status', '!=', 'paid')->with('receipt')->get();

        $pdf = Pdf::loadView('pdfs.notifications.formal_act', [
            'unit' => $unit,
            'totalDebt' => $totalDebt,
            'debts' => $debts,
            'date' => Carbon::now()->format('d/m/Y'),
        ]);

        $pdfContent = $pdf->output();
        $contentHash = hash('sha256', $pdfContent);

        $fileName = "notices/act_{$unit->id}_" . time() . ".pdf";
        Storage::disk('public')->put($fileName, $pdfContent);

        return CollectionNotice::create([
            'unit_id' => $unit->id,
            'notice_type' => 'formal_act',
            'content_hash' => $contentHash,
            'proof_path' => $fileName,
            'sent_at' => Carbon::now(),
        ]);
    }

    /**
     * Generate a legal prevention notice (Prevención Legal).
     */
    public function sendLegalPrevention(Unit $unit): CollectionNotice
    {
        $totalDebt = $unit->debts()->where('status', '!=', 'paid')->sum('amount');

        $pdf = Pdf::loadView('pdfs.notifications.legal_prevention', [
            'unit' => $unit,
            'totalDebt' => $totalDebt,
            'date' => Carbon::now()->format('d/m/Y'),
        ]);

        $pdfContent = $pdf->output();
        $contentHash = hash('sha256', $pdfContent);

        $fileName = "notices/preventive_{$unit->id}_" . time() . ".pdf";
        Storage::disk('public')->put($fileName, $pdfContent);

        return CollectionNotice::create([
            'unit_id' => $unit->id,
            'notice_type' => 'legal_prevention',
            'content_hash' => $contentHash,
            'proof_path' => $fileName,
            'sent_at' => Carbon::now(),
        ]);
    }
}
