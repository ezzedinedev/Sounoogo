<?php

namespace App\Domain\Consultation\Models;

use App\Domain\Consultation\Enums\PayoutStatus;
use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Demande de versement d'un professionnel (Mobile Money), traitée par l'administration.
 *
 * @property int $id
 * @property int $professional_profile_id
 * @property int $amount
 * @property PayoutStatus $status
 * @property string $phone
 * @property string|null $reference
 * @property string|null $note
 * @property int|null $processed_by
 * @property Carbon|null $processed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['professional_profile_id', 'amount', 'status', 'phone', 'reference', 'note', 'processed_by', 'processed_at'])]
class Payout extends Model
{
    protected $attributes = [
        'status' => 'requested',
    ];

    protected function casts(): array
    {
        return [
            'status' => PayoutStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<ProfessionalProfile, $this> */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'professional_profile_id');
    }

    /** @return BelongsTo<User, $this> */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
