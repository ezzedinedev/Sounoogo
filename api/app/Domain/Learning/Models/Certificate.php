<?php

namespace App\Domain\Learning\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Attestation de fin de formation. Le nom inscrit est choisi par le membre au moment de la
 * demande : son compte peut rester anonyme. Le code permet à un tiers de vérifier l'attestation.
 *
 * @property int $id
 * @property string $code
 * @property int $user_id
 * @property int $course_id
 * @property string $holder_name
 * @property Carbon $issued_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['code', 'user_id', 'course_id', 'holder_name', 'issued_at'])]
class Certificate extends Model
{
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /** Code lisible, sans caractères ambigus (0/O, 1/I) : XXXX-XXXX-XXXX. */
    public static function generateCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $chars = array_map(fn () => $alphabet[random_int(0, strlen($alphabet) - 1)], range(1, 12));
            $code = implode('-', str_split(implode('', $chars), 4));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Course, $this> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function verificationUrl(): string
    {
        return route('certificates.verify', $this);
    }
}
