<?php

namespace App\Http\Resources;

use App\Domain\Consultation\Models\Booking;
use App\Domain\Consultation\Services\VideoRooms;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Booking */
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'mode' => $this->mode->value,
            'mode_label' => $this->mode->label(),
            'price' => $this->price,
            'keep_anonymous' => $this->keep_anonymous,
            'member_note' => $this->member_note,
            'slot' => new SlotResource($this->whenLoaded('slot')),
            'professional' => new ProfessionalResource($this->whenLoaded('professional')),
            'member' => $this->when(
                $request->user()?->id !== $this->member_id && $this->relationLoaded('member'),
                fn () => $this->keep_anonymous
                    ? ['display_name' => $this->member->pseudonym]
                    : ['display_name' => $this->member->name ?? $this->member->pseudonym, 'phone' => $this->member->phone, 'email' => $this->member->email],
            ),
            'video' => $this->when(
                $this->relationLoaded('slot') && app(VideoRooms::class)->applies($this->resource),
                fn () => $this->videoWindow(),
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /** @return array{opens_at: string, closes_at: string, is_open: bool} */
    private function videoWindow(): array
    {
        $rooms = app(VideoRooms::class);
        [$opensAt, $closesAt] = $rooms->window($this->resource);

        return [
            'opens_at' => $opensAt->toIso8601String(),
            'closes_at' => $closesAt->toIso8601String(),
            'is_open' => $rooms->isOpen($this->resource),
        ];
    }
}
