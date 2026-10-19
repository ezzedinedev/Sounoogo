<?php

namespace App\Http\Resources;

use App\Domain\Consultation\Models\AvailabilitySlot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AvailabilitySlot */
class SlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'starts_at' => $this->starts_at->toIso8601String(),
            'ends_at' => $this->ends_at->toIso8601String(),
            'is_booked' => $this->is_booked,
        ];
    }
}
