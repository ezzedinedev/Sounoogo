<?php

namespace App\Http\Resources;

use App\Domain\Content\Models\SupportResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SupportResource */
class SupportResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'city' => $this->city,
            'address' => $this->address,
            'phone' => $this->phone,
            'website' => $this->website,
            'description' => $this->description,
            'opening_hours' => $this->opening_hours,
            'is_free' => $this->is_free,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
