<?php

namespace App\Http\Resources;

use App\Domain\Learning\Enums\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Un jalon, obtenu ou non.
 *
 * @property array{badge: Badge, awarded_at: Carbon|null} $resource
 */
class BadgeResource extends JsonResource
{
    public static function of(Badge $badge, ?Carbon $awardedAt = null): self
    {
        return new self(['badge' => $badge, 'awarded_at' => $awardedAt]);
    }

    public function toArray(Request $request): array
    {
        $badge = $this->resource['badge'];

        return [
            'code' => $badge->value,
            'label' => $badge->label(),
            'description' => $badge->description(),
            'earned' => $this->resource['awarded_at'] !== null,
            'awarded_at' => $this->resource['awarded_at']?->toIso8601String(),
        ];
    }
}
