<?php

namespace App\Http\Resources;

use App\Domain\Consultation\Enums\ConsultationMode;
use App\Domain\Consultation\Models\ProfessionalProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProfessionalProfile */
class ProfessionalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'title' => $this->title,
            'specialty' => $this->specialty,
            'bio' => $this->bio,
            'photo_url' => $this->photoUrl(),
            'city' => $this->city,
            'languages' => $this->languages ?? [],
            'modes' => collect($this->modes ?? [])
                ->map(fn (string $mode) => ['value' => $mode, 'label' => ConsultationMode::from($mode)->label()])
                ->values(),
            'session_price' => $this->session_price,
            'session_minutes' => $this->session_minutes,
            'next_slots' => SlotResource::collection($this->whenLoaded('slots')),
        ];
    }
}
