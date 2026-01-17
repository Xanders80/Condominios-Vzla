<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Acta de Notificación de Cobro</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
        .section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 14px; text-align: right; margin-top: 10px; }
        .legal-footer { font-style: italic; font-size: 10px; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ACTA DE NOTIFICACIÓN ADMINISTRATIVA</h2>
        <h3>CONDOMINIO: {{ $unit->condominium->name ?? 'N/A' }}</h3>
        <p>Fecha de Emisión: {{ $date }}</p>
    </div>

    <div class="section">
        <p><strong>DIRIGIDO A:</strong> Propietario / Residente de la Unidad: {{ $unit->name }} (Piso/Nivel: {{ $unit->floorStreet->name ?? 'N/A' }})</p>
    </div>

    <div class="section">
        <p>Por medio de la presente, se hace constar formalmente la existencia de obligaciones pecuniarias pendientes a favor de la Comunidad de Copropietarios. De acuerdo con los registros administrativos a la fecha, su estado de cuenta refleja lo siguiente:</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Recibo / Referencia</th>
                    <th>Fecha de Vencimiento</th>
                    <th>Monto Original</th>
                </tr>
            </thead>
            <tbody>
                @foreach($debts as $debt)
                <tr>
                    <td>{{ $debt->receipt->receipt_number ?? 'Migración/Historico' }}</td>
                    <td>{{ $debt->due_date->format('d/m/Y') }}</td>
                    <td>{{ number_format($debt->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            TOTAL DEUDA CAPITAL: {{ number_format($totalDebt, 2) }}
        </div>
    </div>

    <div class="section">
        <p>Le instamos a regularizar su situación a la brevedad posible para evitar el devengo de intereses moratorios conforme a lo establecido en la Ley de Propiedad Horizontal y el Código Civil Venezolano.</p>
    </div>

    <div class="legal-footer">
        Este documento constituye una notificación formal administrativa. La falta de pago podría dar inicio a gestiones de cobranza extrajudicial o judicial. (Ref: Sarraf Administrativo).
    </div>

    <div class="footer">
        Condominio Digital - Gestión Transparente - {{ $unit->condominium->rif ?? '' }}
    </div>
</body>
</html>
