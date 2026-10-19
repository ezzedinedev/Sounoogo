<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Models\Payout;
use App\Domain\Consultation\Models\ProfessionalProfile;
use App\Domain\Consultation\Services\ProfessionalEarnings;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RequestPayout
{
    public function __construct(private readonly ProfessionalEarnings $earnings) {}

    public function __invoke(ProfessionalProfile $profile, int $amount, string $phone): Payout
    {
        return DB::transaction(function () use ($profile, $amount, $phone) {
            // Verrou sur le profil : deux demandes simultanées ne peuvent pas dépasser le solde.
            ProfessionalProfile::whereKey($profile->id)->lockForUpdate()->first();

            $available = $this->earnings->summary($profile)['available'];
            $minimum = (int) config('sunoogo.payouts.minimum_amount');

            if ($amount < $minimum) {
                throw ValidationException::withMessages(['amount' => 'Le montant minimum d\'un versement est de '.number_format($minimum, 0, ',', ' ').' F.']);
            }
            if ($amount > $available) {
                throw ValidationException::withMessages(['amount' => 'Le montant dépasse ton solde disponible ('.number_format($available, 0, ',', ' ').' F).']);
            }

            $payout = $profile->payouts()->create(['amount' => $amount, 'phone' => $phone]);
            activity('versements')->performedOn($payout)->log('Versement demandé');

            return $payout;
        });
    }
}
