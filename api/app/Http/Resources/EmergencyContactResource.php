<?php

namespace App\Http\Resources;

use App\Domain\Listening\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin EmergencyContact */
class EmergencyContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'label' => $this->label,
            'phone' => $this->phone,
            'description' => $this->description,
        ];
    }
}
