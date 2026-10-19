<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Consultation\Models\AvailabilitySlot;
use App\Domain\Consultation\Models\Booking;
use App\Domain\Identity\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class BookConsultation
{
    public function __invoke(User $member, AvailabilitySlot $slot, ConsultationMode $mode, bool $keepAnonymous = true, ?string $note = null): Booking
    {
        return DB::transaction(function () use ($member, $slot, $mode, $keepAnonymous, $note) {
            $slot = AvailabilitySlot::query()->with('professional')->lockForUpdate()->findOrFail($slot->id);

            if ($slot->is_booked || $slot->starts_at->isPast()) {
                throw ValidationException::withMessages(['slot_id' => 'Ce créneau n\'est plus disponible.']);
            }

            $professional = $slot->professional;

            if (! in_array($mode->value, $professional->modes ?? [], true)) {
                throw ValidationException::withMessages(['mode' => 'Ce professionnel ne propose pas ce format de consultation.']);
            }

            $slot->update(['is_booked' => true]);

            return Booking::create([
                'member_id' => $member->id,
                'professional_profile_id' => $professional->id,
                'availability_slot_id' => $slot->id,
                'mode' => $mode,
                'status' => BookingStatus::PendingPayment,
                'price' => $this->priceFor($member, $professional->session_price),
                'keep_anonymous' => $keepAnonymous,
                'member_note' => $note,
            ]);
        });
    }

    /** Remise éventuelle liée à l'abonnement du membre. */
    private function priceFor(User $member, int $basePrice): int
    {
        $discount = $member->activeSubscription()?->plan->consultation_discount ?? 0;

        return (int) round($basePrice * (100 - $discount) / 100);
    }
}
