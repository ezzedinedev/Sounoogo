<?php

namespace App\Domain\Reporting\Services;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Listening\Enums\Topic;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\EmergencyAlert;
use App\Domain\Reporting\Data\Figure;
use App\Domain\Wellbeing\Models\MoodEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Rapport d'impact anonymisé pour les partenaires et bailleurs : uniquement des agrégats,
 * aucun pseudonyme ni contenu, petits effectifs masqués (voir Figure).
 */
final class ImpactReport
{
    /**
     * @return array<string, list<Figure>> sections => chiffres
     */
    public function figures(Carbon $from, Carbon $to): array
    {
        return [
            'Écoute' => $this->listening($from, $to),
            'Sécurité' => $this->safety($from, $to),
            'Consultations' => $this->consultations($from, $to),
            'Autonomie et bien-être' => $this->autonomy($from, $to),
        ];
    }

    /** @return list<Figure> conversations par thème, les plus fréquents d'abord */
    public function topics(Carbon $from, Carbon $to): array
    {
        $counts = Conversation::whereBetween('created_at', [$from, $to])
            ->selectRaw('topic, count(*) as total')
            ->groupBy('topic')
            ->pluck('total', 'topic');

        return collect(Topic::cases())
            ->map(fn (Topic $topic) => Figure::count($topic->label(), (int) ($counts[$topic->value] ?? 0)))
            ->push(Figure::count('Non précisé', (int) ($counts[''] ?? 0)))
            ->filter(fn (Figure $figure) => $figure->value > 0)
            ->sortByDesc('value')
            ->values()
            ->all();
    }

    /**
     * Conversations ouvertes par mois sur les 12 derniers mois jusqu'à $to.
     *
     * @return Collection<int, array{month: Carbon, figure: Figure}>
     */
    public function monthlyConversations(Carbon $to): Collection
    {
        $from = $to->copy()->startOfMonth()->subMonths(11);
        $dates = Conversation::whereBetween('created_at', [$from, $to])->pluck('created_at');

        return collect(range(0, 11))->map(function (int $offset) use ($from, $dates) {
            $month = $from->copy()->addMonths($offset);

            return ['month' => $month, 'figure' => Figure::count($month->translatedFormat('M Y'), $dates->filter(fn ($date) => $date->isSameMonth($month))->count())];
        });
    }

    /** @return list<Figure> */
    private function listening(Carbon $from, Carbon $to): array
    {
        $conversations = Conversation::whereBetween('created_at', [$from, $to])
            ->get(['member_id', 'created_at', 'first_response_at', 'member_rating']);

        $waits = $conversations
            ->filter(fn ($conversation) => $conversation->first_response_at !== null)
            ->map(fn ($conversation) => $conversation->created_at->diffInSeconds($conversation->first_response_at) / 60);
        $ratings = $conversations->pluck('member_rating')->filter();

        return [
            Figure::count('Conversations ouvertes', $conversations->count()),
            Figure::count('Personnes écoutées', $conversations->unique('member_id')->count(), 'Pseudonymes distincts'),
            Figure::statistic('Temps médian avant la première réponse', $this->median($waits), $waits->count(), ' min'),
            Figure::statistic('Satisfaction moyenne', $ratings->isEmpty() ? null : round($ratings->avg(), 1), $ratings->count(), ' / 5', 'Note laissée en fin de conversation'),
        ];
    }

    /** @return list<Figure> */
    private function safety(Carbon $from, Carbon $to): array
    {
        $alerts = EmergencyAlert::whereBetween('created_at', [$from, $to])->get(['source', 'created_at', 'acknowledged_at']);
        $delays = $alerts
            ->filter(fn ($alert) => $alert->acknowledged_at !== null)
            ->map(fn ($alert) => $alert->created_at->diffInSeconds($alert->acknowledged_at) / 60);

        return [
            Figure::count('Alertes de détresse', $alerts->count()),
            Figure::count('… via le bouton SOS', $alerts->where('source', 'sos')->count()),
            Figure::count('… détectées dans les messages', $alerts->whereIn('source', ['keyword', 'ai'])->count(), 'Mots à risque et analyse IA'),
            Figure::statistic('Délai médian de prise en charge', $this->median($delays), $delays->count(), ' min', 'Entre l\'alerte et sa prise en charge par un superviseur'),
        ];
    }

    /** @return list<Figure> */
    private function consultations(Carbon $from, Carbon $to): array
    {
        $completed = Booking::where('status', BookingStatus::Completed)->whereBetween('completed_at', [$from, $to])->get(['mode', 'professional_profile_id']);

        return [
            Figure::count('Consultations tenues', $completed->count()),
            ...array_map(
                fn (ConsultationMode $mode) => Figure::count('… '.mb_strtolower($mode->label()), $completed->where('mode', $mode)->count()),
                ConsultationMode::cases(),
            ),
            Figure::count('Professionnels sollicités', $completed->unique('professional_profile_id')->count()),
        ];
    }

    /** @return list<Figure> */
    private function autonomy(Carbon $from, Carbon $to): array
    {
        $lessons = DB::table('lesson_completions')->whereBetween('created_at', [$from, $to])->count();
        $moods = MoodEntry::whereBetween('recorded_on', [$from->toDateString(), $to->toDateString()])
            ->orderBy('recorded_on')
            ->get(['user_id', 'mood']);

        // Évolution de l'humeur : dernier check-in moins premier, pour les membres qui en ont fait au moins deux.
        $evolutions = $moods->groupBy('user_id')
            ->filter(fn ($entries) => $entries->count() >= 2)
            ->map(fn ($entries) => $entries->last()->mood->value - $entries->first()->mood->value);

        return [
            Figure::count('Leçons terminées', $lessons),
            Figure::count('Formations terminées', Enrollment::whereBetween('completed_at', [$from, $to])->count()),
            Figure::count('Attestations délivrées', Certificate::whereBetween('issued_at', [$from, $to])->count()),
            Figure::count('Personnes suivant leur humeur', $moods->unique('user_id')->count()),
            Figure::statistic(
                'Part dont l\'humeur s\'améliore',
                $evolutions->isEmpty() ? null : round($evolutions->filter(fn (int $delta) => $delta > 0)->count() * 100 / $evolutions->count(), 1),
                $evolutions->count(),
                ' %',
                'Premier et dernier check-in de la période, parmi les personnes qui en ont fait au moins deux. Indicateur déclaratif, sans valeur clinique.',
            ),
        ];
    }

    /** @param  Collection<int, float>  $values  durées en minutes */
    private function median(Collection $values): ?float
    {
        if ($values->isEmpty()) {
            return null;
        }

        return round((float) $values->median(), 1);
    }
}
