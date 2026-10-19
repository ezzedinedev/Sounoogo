<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Reporting\Data\Figure;
use App\Domain\Reporting\Services\ImpactReport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Rapport d'impact anonymisé : consultation, export CSV et PDF. */
class ImpactController extends Controller
{
    public const PERIODS = [
        'mois' => 'Ce mois-ci',
        'mois-precedent' => 'Mois précédent',
        'trimestre' => 'Trois derniers mois',
        'annee' => 'Depuis le 1er janvier',
        '12-mois' => 'Douze derniers mois',
    ];

    public function index(Request $request, ImpactReport $report): View
    {
        [$key, $from, $to] = $this->period($request);

        activity('rapports')->withProperties(['periode' => $key])->log('Rapport d\'impact consulté');

        return view('backoffice.impact.index', [
            'periodKey' => $key,
            'periods' => self::PERIODS,
            'from' => $from,
            'to' => $to,
            'sections' => $report->figures($from, $to),
            'topics' => $report->topics($from, $to),
            'monthly' => $report->monthlyConversations($to),
        ]);
    }

    public function csv(Request $request, ImpactReport $report): StreamedResponse
    {
        [$key, $from, $to] = $this->period($request);
        activity('rapports')->withProperties(['periode' => $key, 'format' => 'csv'])->log('Rapport d\'impact exporté');

        $rows = collect($report->figures($from, $to))
            ->flatMap(fn (array $figures, string $section) => array_map(fn (Figure $figure) => [$section, $figure], $figures))
            ->concat(array_map(fn (Figure $figure) => ['Thèmes abordés', $figure], $report->topics($from, $to)));

        return response()->streamDownload(function () use ($rows, $from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\u{FEFF}"); // BOM : accents corrects à l'ouverture dans Excel
            fputcsv($out, ['Période', $from->toDateString().' → '.$to->toDateString()], ';');
            fputcsv($out, ['Section', 'Indicateur', 'Valeur', 'Unité', 'Masqué (< seuil)'], ';');
            foreach ($rows as [$section, $figure]) {
                fputcsv($out, [$section, $figure->label, $figure->exportValue(), trim($figure->unit), $figure->suppressed ? 'oui' : 'non'], ';');
            }
            fclose($out);
        }, "sunoogo-impact-{$from->toDateString()}-{$to->toDateString()}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(Request $request, ImpactReport $report): Response
    {
        [$key, $from, $to] = $this->period($request);
        activity('rapports')->withProperties(['periode' => $key, 'format' => 'pdf'])->log('Rapport d\'impact exporté');

        return Pdf::loadView('pdf.impact', [
            'from' => $from,
            'to' => $to,
            'sections' => $report->figures($from, $to),
            'topics' => $report->topics($from, $to),
            'threshold' => (int) config('sunoogo.impact.min_cell_size'),
        ])->download("sunoogo-impact-{$from->toDateString()}-{$to->toDateString()}.pdf");
    }

    /** @return array{string, Carbon, Carbon} */
    private function period(Request $request): array
    {
        $key = array_key_exists((string) $request->query('periode'), self::PERIODS) ? (string) $request->query('periode') : 'mois-precedent';
        $now = now();

        [$from, $to] = match ($key) {
            'mois' => [$now->copy()->startOfMonth(), $now->copy()],
            'mois-precedent' => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'trimestre' => [$now->copy()->subMonthsNoOverflow(3)->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()],
            'annee' => [$now->copy()->startOfYear(), $now->copy()],
            '12-mois' => [$now->copy()->subMonthsNoOverflow(11)->startOfMonth(), $now->copy()],
        };

        return [$key, $from, $to];
    }
}
