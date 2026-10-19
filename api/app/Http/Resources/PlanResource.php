<?php

namespace App\Http\Resources;

use App\Domain\Billing\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Plan */
class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'includes_courses' => $this->includes_courses,
            'consultation_discount' => $this->consultation_discount,
            'features' => $this->features ?? [],
        ];
    }
}
