<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Enums\PayoutStatus;
use App\Domain\Consultation\Models\Payout;
use App\Domain\Identity\Models\User;

/** L'administration confirme le versement Mobile Money effectué, ou refuse la demande (le montant revient au solde). */
final class ProcessPayout
{
    public function __invoke(Payout $payout, User $by, PayoutStatus $status, ?string $reference = null, ?string $note = null): Payout
    {
        abort_unless($payout->status === PayoutStatus::Requested, 409, 'Ce versement a déjà été traité.');
        abort_if($status === PayoutStatus::Requested, 422);

        $payout->update([
            'status' => $status,
            'reference' => $reference,
            'note' => $note,
            'processed_by' => $by->id,
            'processed_at' => now(),
        ]);

        activity('versements')->causedBy($by)->performedOn($payout)->log($status === PayoutStatus::Paid ? 'Versement effectué' : 'Versement refusé');

        return $payout;
    }
}
