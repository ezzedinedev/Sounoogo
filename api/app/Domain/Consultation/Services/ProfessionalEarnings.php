<?php

namespace App\Domain\Consultation\Services;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Enums\PayoutStatus;
use App\Domain\Consultation\Models\ProfessionalProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/** Revenus d'un professionnel : consultations terminées, versements, solde disponible. */
final class ProfessionalEarnings
{
    /** @return array{earned: int, upcoming: int, requested: int, paid: int, available: int} */
    public function summary(ProfessionalProfile $profile): array
    {
        $earned = (int) $profile->bookings()->where('status', BookingStatus::Completed)->sum('professional_share');
        $requested = (int) $profile->payouts()->where('status', PayoutStatus::Requested)->sum('amount');
        $paid = (int) $profile->payouts()->where('status', PayoutStatus::Paid)->sum('amount');

        return [
            'earned' => $earned,
            // Consultations payées mais pas encore tenues : indicatif, avant commission.
            'upcoming' => (int) $profile->bookings()->where('status', BookingStatus::Confirmed)->sum('price'),
            'requested' => $requested,
            'paid' => $paid,
            'available' => max(0, $earned - $requested - $paid),
        ];
    }

    /**
     * Revenus des derniers mois (consultations terminées), du plus ancien au plus récent.
     *
     * @return Collection<int, array{month: Carbon, amount: int, count: int}>
     */
    public function monthly(ProfessionalProfile $profile, int $months = 6): Collection
    {
        $from = now()->startOfMonth()->subMonths($months - 1);
        $bookings = $profile->bookings()
            ->where('status', BookingStatus::Completed)
            ->where('completed_at', '>=', $from)
            ->get(['completed_at', 'professional_share']);

        return collect(range(0, $months - 1))->map(function (int $offset) use ($from, $bookings) {
            $month = $from->copy()->addMonths($offset);
            $inMonth = $bookings->filter(fn ($booking) => $booking->completed_at->isSameMonth($month));

            return ['month' => $month, 'amount' => (int) $inMonth->sum('professional_share'), 'count' => $inMonth->count()];
        });
    }
}
