<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prevención Jurídica de Cobro</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #900; padding-bottom: 10px; }
        .urgent-tag { background: #900; color: white; padding: 5px 10px; font-weight: bold; margin-bottom: 20px; display: inline-block; }
        .section { margin-bottom: 20px; }
        .highlight { background-color: #fff3f3; border-left: 4px solid #900; padding: 15px; }
        .legal-warning { font-weight: bold; color: #900; text-transform: uppercase; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="color: #900;">PREVENCIÓN LEGAL Y EXTRAJUDICIAL</h2>
        <h3>CONDOMINIO: {{ $unit->condominium->name ?? 'N/A' }}</h3>
    </div>

    <div class="urgent-tag">ULTIMÁTUM DE PAGO</div>

    <div class="section">
        <p><strong>UNIDAD:</strong> {{ $unit->name }}<br>
        <strong>DEUDA TOTAL EXIGIBLE:</strong> {{ number_format($totalDebt, 2) }}<br>
        <strong>FECHA:</strong> {{ $date }}</p>
    </div>

    <div class="section highlight">
        <p class="legal-warning">NOTIFICACIÓN PREVIA A ACCIÓN JUDICIAL</p>
        <p>Se le notifica formalmente que su deuda ha pasado al estatus de **MOROSIDAD CRÍTICA**. De no verificarse el pago total o un acuerdo de pago formal en las próximas 72 horas, este expediente será remitido al departamento legal para el inicio del procedimiento de ejecución de prenda/cobro de bolívares según los artículos 14 y 15 de la Ley de Propiedad Horizontal.</p>
    </div>

    <div class="section">
        <p>Las consecuencias de la vía judicial incluyen:</p>
        <ul>
            <li>Devengo de intereses moratorios a la tasa máxima permitida.</li>
            <li>Costas procesales y honorarios profesionales (estimados en 30% adicional).</li>
            <li>Posible embargo preventivo de bienes.</li>
            <li>Publicación en cartelera de deudores de la comunidad (Art. 13 LPH).</li>
        </ul>
    </div>

    <div class="section" style="margin-top: 50px;">
        <p>Atentamente,</p>
        <br><br>
        <p>__________________________<br>
        ADMINISTRACIÓN / CONSEJO DE CONDOMINIO</p>
    </div>

    <div class="footer">
        Verificación de integridad SHA256 disponible en el portal del residente.
    </div>
</body>
</html>
