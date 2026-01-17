<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Condominio - {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #7f8c8d;
            width: 150px;
        }
        .concepts-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .concepts-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .concepts-table td {
            padding: 8px;
            border-bottom: 1px solid #ecf0f1;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #95a5a6;
        }
        .qr-section {
            float: right;
            text-align: center;
            margin-top: 20px;
        }
        .legal-note {
            font-style: italic;
            margin-top: 15px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Recibo de Condominio</h1>
        <p>{{ $condominium->name }} | RIF: {{ $condominium->rif }}</p>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Nro. Recibo:</td>
            <td>{{ $receipt->receipt_number }}</td>
            <td class="label">Fecha Emisión:</td>
            <td>{{ $receipt->issue_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Unidad:</td>
            <td>{{ $unit->name }}</td>
            <td class="label">Fecha Vencimiento:</td>
            <td>{{ $receipt->due_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Propietario/Habitante:</td>
            <td>{{ $unit->dweller->first_name }} {{ $unit->dweller->last_name }}</td>
            <td class="label">Coeficiente:</td>
            <td>{{ number_format($receipt->coownership_coefficient * 100, 4) }}%</td>
        </tr>
    </table>

    <table class="concepts-table">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Descripción</th>
                <th style="text-align: right;">Monto (Bs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->concepts as $concept)
            <tr>
                <td>{{ $concept->concept_name }}</td>
                <td>{{ $concept->description }}</td>
                <td style="text-align: right;">{{ number_format($concept->amount, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">TOTAL A PAGAR:</td>
                <td style="text-align: right;">{{ number_format($receipt->total_amount, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($exchangeRate)
    <p><strong>Tasa de Cambio Referencial (BCV):</strong> {{ number_format($exchangeRate->official_rate, 4, ',', '.') }} Bs/$ (Fecha: {{ $exchangeRate->rate_date->format('d/m/Y') }})</p>
    <p><strong>Equivalente en Divisas:</strong> ${{ number_format($receipt->total_amount / $exchangeRate->official_rate, 2, ',', '.') }}</p>
    @endif

    <div class="qr-section">
        <p>Verificar Recibo</p>
        <img src="data:image/png;base64, {!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate($receipt->qr_verification_hash)) !!} ">
    </div>

    <div class="legal-note">
        <p>Este recibo se emite de conformidad con el Artículo 38 de la Ley de Propiedad Horizontal vigente en la República Bolivariana de Venezuela.</p>
    </div>

    <div class="footer">
        <p>Generado automáticamente por el Sistema de Administración de Condominios</p>
        <p>&copy; {{ date('Y') }} {{ $condominium->name }}</p>
    </div>
</body>
</html>
