<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>CashDash - Krypteringsnyckeldokument</title>
    <style>
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
        }

        .page {
            padding: 40px 50px;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 3px solid #1A3D2E;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }

        .logo-text {
            font-size: 28pt;
            font-weight: bold;
            color: #1A3D2E;
            letter-spacing: -0.5px;
        }

        .logo-accent {
            color: #C4A962;
        }

        .header-subtitle {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 5px;
        }

        .confidential-badge {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 8px 20px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin: 30px 0 40px;
            padding: 30px;
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border-radius: 12px;
            border: 1px solid #bbf7d0;
        }

        .title {
            font-size: 22pt;
            font-weight: bold;
            color: #1A3D2E;
            margin-bottom: 10px;
        }

        .title-description {
            font-size: 11pt;
            color: #374151;
        }

        /* Info Box */
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .info-box-header {
            font-size: 12pt;
            font-weight: bold;
            color: #1A3D2E;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            color: #6b7280;
            font-size: 10pt;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            color: #1f2937;
            font-weight: 500;
        }

        /* Passphrase Section */
        .passphrase-section {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #C4A962;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }

        .passphrase-header {
            font-size: 14pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 15px;
            text-align: center;
        }

        .passphrase-box {
            background: #ffffff;
            border: 2px dashed #C4A962;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 15px 0;
        }

        .passphrase-value {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 14pt;
            font-weight: bold;
            color: #1A3D2E;
            letter-spacing: 1px;
            word-break: break-all;
        }

        .passphrase-warning {
            font-size: 9pt;
            color: #92400e;
            text-align: center;
            margin-top: 10px;
        }

        /* Warning Section */
        .warning-section {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .warning-title {
            font-size: 12pt;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }

        .warning-list {
            list-style: none;
            padding: 0;
        }

        .warning-list li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
            font-size: 10pt;
            color: #7f1d1d;
        }

        .warning-list li:before {
            content: "!";
            position: absolute;
            left: 0;
            color: #dc2626;
            font-weight: bold;
        }

        /* Instructions Section */
        .instructions-section {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .instructions-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 12px;
        }

        .instructions-list {
            list-style: none;
            padding: 0;
        }

        .instructions-list li {
            padding: 8px 0;
            padding-left: 30px;
            position: relative;
            font-size: 10pt;
            color: #1e3a8a;
        }

        .step-number {
            position: absolute;
            left: 0;
            width: 20px;
            height: 20px;
            background: #1e40af;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 9pt;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
            vertical-align: bottom;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: bottom;
        }

        .footer-text {
            font-size: 8pt;
            color: #9ca3af;
            line-height: 1.4;
        }

        .footer-logo {
            font-size: 14pt;
            font-weight: bold;
            color: #1A3D2E;
        }

        .footer-company {
            font-size: 8pt;
            color: #6b7280;
        }

        /* Security Badge */
        .security-badge {
            display: table;
            width: 100%;
            background: linear-gradient(135deg, #1A3D2E, #2D5A45);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .security-icon {
            display: table-cell;
            vertical-align: middle;
            width: 50px;
            font-size: 24pt;
        }

        .security-text {
            display: table-cell;
            vertical-align: middle;
        }

        .security-title {
            font-weight: bold;
            font-size: 11pt;
        }

        .security-desc {
            font-size: 9pt;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="logo-text">Cash<span class="logo-accent">Dash</span></div>
                <div class="header-subtitle">Din kassaflödesdashboard för svenska företag</div>
            </div>
            <div class="header-right">
                <div class="confidential-badge">KONFIDENTIELLT</div>
            </div>
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <div class="title">Krypteringsnyckeldokument</div>
            <div class="title-description">
                Detta dokument innehåller din personliga krypteringsnyckel för CashDash.<br>
                Förvara det säkert - det är det enda sättet att återfå åtkomst till dina data.
            </div>
        </div>

        <!-- Account Information -->
        <div class="info-box">
            <div class="info-box-header">Kontoinformation</div>
            <div class="info-row">
                <div class="info-label">Företag:</div>
                <div class="info-value">{{ $team->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kontoinnehavare:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">E-post:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Skapad:</div>
                <div class="info-value">{{ $createdAt }}</div>
            </div>
        </div>

        <!-- Passphrase Section -->
        <div class="passphrase-section">
            <div class="passphrase-header">Din Krypteringsnyckel (Lösenfras)</div>
            <div class="passphrase-box">
                <div class="passphrase-value">{{ $passphrase }}</div>
            </div>
            <div class="passphrase-warning">
                Skriv ner denna nyckel på ett säkert ställe eller förvara detta dokument i ett låst utrymme.
            </div>
        </div>

        <!-- Security Badge -->
        <div class="security-badge">
            <div class="security-icon">&#128274;</div>
            <div class="security-text">
                <div class="security-title">Zero-Knowledge Kryptering (AES-256)</div>
                <div class="security-desc">
                    Din data krypteras lokalt innan den lagras. Varken CashDash eller någon annan kan läsa dina data utan denna nyckel.
                </div>
            </div>
        </div>

        <!-- Warning Section -->
        <div class="warning-section">
            <div class="warning-title">VIKTIGA VARNINGAR</div>
            <ul class="warning-list">
                <li>Om du förlorar denna nyckel finns det INGET SÄTT att återfå dina krypterade data</li>
                <li>CashDash har ingen kopia av din nyckel och kan inte hjälpa dig återfå den</li>
                <li>Dela ALDRIG denna nyckel med någon som du inte litar på fullständigt</li>
                <li>Om du misstänker att någon har fått tillgång till din nyckel, byt den omedelbart</li>
            </ul>
        </div>

        <!-- Instructions Section -->
        <div class="instructions-section">
            <div class="instructions-title">SÅ HÄR FÖRVARAR DU DOKUMENTET SÄKERT</div>
            <ul class="instructions-list">
                <li><span class="step-number">1</span> Skriv ut detta dokument och förvara det i ett brandsäkert kassaskåp eller bankfack</li>
                <li><span class="step-number">2</span> Alternativt, spara PDF-filen på en krypterad USB-enhet som förvaras separat</li>
                <li><span class="step-number">3</span> Överväg att ha två kopior på olika fysiska platser</li>
                <li><span class="step-number">4</span> Ta bort PDF-filen från din dator efter att du förvarar den säkert</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-text">
                        Detta dokument genererades automatiskt av CashDash.<br>
                        Dokument-ID: {{ strtoupper(substr(md5($team->id . $createdAt), 0, 12)) }}<br>
                        cashdash.se | support@cashdash.se
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-logo">Cash<span style="color: #C4A962;">Dash</span></div>
                    <div class="footer-company">Stafe Development AB | Blomstergatan 6, 591 70 Motala</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
