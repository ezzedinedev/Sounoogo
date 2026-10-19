{{-- Rapport d'impact (dompdf). --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport d'impact SUNOOGO</title>
    <style>
        @page { margin: 16mm 16mm 18mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #1c1530; }
        .brand { font-size: 11pt; font-weight: bold; letter-spacing: 2.5pt; color: #4c2494; }
        h1 { margin: 6mm 0 1mm; font-size: 20pt; font-weight: normal; color: #2a1660; }
        .period { color: #514566; }
        h2 { margin: 8mm 0 2mm; font-size: 11pt; color: #4c2494; border-bottom: 0.3mm solid #e4d9f7; padding-bottom: 1.5mm; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1.6mm 0; border-bottom: 0.2mm solid #f0eaf9; vertical-align: top; }
        td.value { text-align: right; font-weight: bold; white-space: nowrap; width: 35mm; }
        .muted { color: #8a7ca3; }
        .hint { font-size: 7.5pt; color: #8a7ca3; }
        .method { margin-top: 10mm; font-size: 7.5pt; line-height: 1.5; color: #514566; }
        .footer { position: fixed; bottom: -10mm; left: 0; right: 0; font-size: 7pt; color: #8a7ca3; }
    </style>
</head>
<body>
    <div class="footer">SUNOOGO · Rapport d'impact généré le {{ now()->translatedFormat('j F Y') }} · Données agrégées et anonymisées</div>

    <div class="brand">SUNOOGO</div>
    <h1>Rapport d'impact</h1>
    <p class="period">Du {{ $from->translatedFormat('j F Y') }} au {{ $to->translatedFormat('j F Y') }}</p>

    @foreach ($sections as $title => $figures)
        <h2>{{ $title }}</h2>
        <table>
            @foreach ($figures as $figure)
                <tr>
                    <td>{{ $figure->label }}@if ($figure->hint)<br><span class="hint">{{ $figure->hint }}</span>@endif</td>
                    <td class="value {{ $figure->suppressed ? 'muted' : '' }}">{{ $figure->display() }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach

    @if ($topics !== [])
        <h2>Thèmes abordés en écoute</h2>
        <table>
            @foreach ($topics as $figure)
                <tr><td>{{ $figure->label }}</td><td class="value {{ $figure->suppressed ? 'muted' : '' }}">{{ $figure->display() }}</td></tr>
            @endforeach
        </table>
    @endif

    <p class="method">
        Méthode : comptages sur la période, sans pseudonyme ni contenu de conversation. Les effectifs compris entre 1 et {{ $threshold - 1 }}
        sont remplacés par « &lt; {{ $threshold }} », de même que les médianes et moyennes calculées sur moins de {{ $threshold }} personnes,
        afin qu'aucune personne ne puisse être reconnue. Les indicateurs d'humeur sont déclaratifs et n'ont pas de valeur clinique.
    </p>
</body>
</html>
