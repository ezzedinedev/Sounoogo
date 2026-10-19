<?php

namespace App\Http\Resources;

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subscription = $this->activeSubscription();

        return [
            'id' => $this->id,
            'pseudonym' => $this->pseudonym,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->primaryRole()?->value,
            'is_anonymous' => $this->is_anonymous,
            'country' => $this->country,
            'locale' => $this->locale,
            'is_available' => $this->when($this->isOneOf(Role::Listener), $this->is_available),
            'subscription' => $subscription ? new SubscriptionResource($subscription->load('plan')) : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
