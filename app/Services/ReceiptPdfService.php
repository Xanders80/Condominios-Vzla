<?php

namespace App\Services;

use App\Models\Receipt;
use App\Models\BcvExchangeRate;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ReceiptPdfService
{
    /**
     * Generate a PDF for the given receipt.
     *
     * @param Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function generate(Receipt $receipt)
    {
        $receipt->load(['unit.dweller', 'concepts', 'unit.towerSector.condominium']);
        $unit = $receipt->unit;
        $condominium = $unit->towerSector->condominium;
        $exchangeRate = BcvExchangeRate::latestRate();

        $pdf = Pdf::loadView('pdfs.receipt', [
            'receipt' => $receipt,
            'unit' => $unit,
            'condominium' => $condominium,
            'exchangeRate' => $exchangeRate,
        ]);

        return $pdf;
    }

    /**
     * Download the PDF for the given receipt.
     *
     * @param Receipt $receipt
     * @return \Illuminate\Http\Response
     */
    public function download(Receipt $receipt)
    {
        $pdf = $this->generate($receipt);
        return $pdf->download("Recibo-{$receipt->receipt_number}.pdf");
    }
}
