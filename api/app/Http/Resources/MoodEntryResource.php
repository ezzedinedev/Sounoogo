<?php

namespace App\Http\Resources;

use App\Domain\Wellbeing\Enums\Feeling;
use App\Domain\Wellbeing\Models\MoodEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MoodEntry */
class MoodEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->recorded_on->toDateString(),
            'mood' => $this->mood->value,
            'mood_label' => $this->mood->label(),
            'feelings' => $this->feelings?->map(fn (Feeling $feeling) => $feeling->value)->values() ?? [],
            'note' => $this->note,
        ];
    }
}
