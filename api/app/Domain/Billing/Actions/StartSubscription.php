<?php

namespace App\Domain\Billing\Actions;

use App\Domain\Billing\Enums\SubscriptionStatus;
use App\Domain\Billing\Models\Plan;
use App\Domain\Billing\Models\Subscription;
use App\Domain\Identity\Models\User;

final class StartSubscription
{
    public function __invoke(User $user, Plan $plan): Subscription
    {
        abort_unless($plan->is_active, 404);

        return $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
        ]);
    }
}
