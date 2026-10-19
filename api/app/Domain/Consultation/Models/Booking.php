<?php

namespace App\Domain\Consultation\Models;

use App\Domain\Billing\Contracts\Payable;
use App\Domain\Billing\Models\Payment;
use App\Domain\Consultation\Enums\BookingStatus;
use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property int $professional_profile_id
 * @property int|null $availability_slot_id
 * @property ConsultationMode $mode
 * @property BookingStatus $status
 * @property int $price
 * @property int|null $professional_share
 * @property string|null $video_room
 * @property bool $keep_anonymous
 * @property string|null $member_note
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $reminded_at
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'member_id', 'professional_profile_id', 'availability_slot_id', 'mode', 'status',
    'price', 'professional_share', 'keep_anonymous', 'member_note', 'video_room', 'confirmed_at', 'reminded_at', 'cancelled_at', 'completed_at',
])]
class Booking extends Model implements Payable
{
    protected $attributes = [
        'status' => 'pending_payment',
        'keep_anonymous' => true,
    ];

    protected function casts(): array
    {
        return [
            'mode' => ConsultationMode::class,
            'status' => BookingStatus::class,
            'keep_anonymous' => 'boolean',
            'confirmed_at' => 'datetime',
            'reminded_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /** @return BelongsTo<ProfessionalProfile, $this> */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'professional_profile_id');
    }

    /** @return BelongsTo<AvailabilitySlot, $this> */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySlot::class, 'availability_slot_id');
    }

    /** @return MorphMany<Payment, $this> */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function paymentLabel(): string
    {
        return "Consultation {$this->mode->label()}";
    }

    public function onPaymentSucceeded(Payment $payment): void
    {
        if ($this->status === BookingStatus::PendingPayment) {
            $this->update(['status' => BookingStatus::Confirmed, 'confirmed_at' => now()]);
        }
    }
}
