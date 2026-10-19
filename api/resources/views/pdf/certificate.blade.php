{{-- Attestation de fin de formation (dompdf : CSS 2.1, mise en page en tableaux). --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Attestation — {{ $certificate->course->title }}</title>
    <style>
        /* A4 paysage, marges de 12 mm : 273 × 186 mm utiles. dompdf ignore box-sizing, d'où les tailles calculées. */
        @page { margin: 12mm; }
        body { margin: 0; font-family: 'DejaVu Sans', sans-serif; color: #1c1530; }
        .frame { width: 234.6mm; border: 1.2mm solid #5b2fb3; border-radius: 6mm; padding: 14mm 18mm; }
        .body { min-height: 112mm; }
        .brand { font-size: 13pt; font-weight: bold; letter-spacing: 3pt; color: #4c2494; vertical-align: middle; }
        .eyebrow { margin-top: 12mm; font-size: 10pt; letter-spacing: 3pt; text-transform: uppercase; color: #7a6a96; }
        h1 { margin: 3mm 0 0; font-size: 30pt; font-weight: normal; color: #2a1660; }
        .lead { margin-top: 10mm; font-size: 11pt; color: #514566; }
        .holder { margin-top: 3mm; font-size: 26pt; font-weight: bold; color: #1c1530; }
        .course { margin-top: 3mm; font-size: 15pt; color: #4c2494; }
        .meta { margin-top: 4mm; font-size: 10pt; color: #514566; }
        .footer { font-size: 8.5pt; line-height: 1.5; color: #7a6a96; border-top: 0.3mm solid #e4d9f7; }
        .footer td { padding-top: 4mm; }
        .footer td { vertical-align: bottom; }
        .code { font-family: 'DejaVu Sans Mono', monospace; font-size: 10pt; color: #2a1660; letter-spacing: 1pt; }
        .seal { width: 24mm; height: 15mm; border-radius: 12mm; background: #5b2fb3; color: #ffffff; text-align: center; font-size: 7pt; letter-spacing: 1pt; padding-top: 9mm; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="body">
        <table cellpadding="0" cellspacing="0"><tr>
            <td><img src="{{ $logo }}" alt="" width="40" height="40"></td>
            <td style="padding-left: 3mm"><span class="brand">SUNOOGO</span></td>
        </tr></table>

        <p class="eyebrow">Attestation de suivi</p>
        <h1>Formation terminée</h1>

        <p class="lead">Nous attestons que</p>
        <p class="holder">{{ $certificate->holder_name }}</p>
        <p class="lead" style="margin-top: 4mm">a suivi l'intégralité de la formation</p>
        <p class="course">« {{ $certificate->course->title }} »</p>
        <p class="meta">{{ $lessons }} leçons · environ {{ $minutes }} minutes · terminée le {{ $completedAt->translatedFormat('j F Y') }}</p>

        </div>

        <table class="footer" cellpadding="0" cellspacing="0" width="100%"><tr>
            <td>
                Délivrée le {{ $certificate->issued_at->translatedFormat('j F Y') }} · Code de vérification <span class="code">{{ $certificate->code }}</span><br>
                Vérifiable sur {{ $certificate->verificationUrl() }}<br>
                Cette attestation reconnaît le suivi d'un parcours d'autonomie ; elle n'est ni un diplôme ni une certification professionnelle.
            </td>
            <td width="30mm" align="right"><div class="seal">SUNOOGO</div></td>
        </tr></table>
    </div>
</body>
</html>
