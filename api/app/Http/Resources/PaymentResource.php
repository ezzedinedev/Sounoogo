<?php

namespace App\Http\Resources;

use App\Domain\Billing\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Payment */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'reference' => $this->reference,
            'label' => $this->meta['label'] ?? null,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'provider' => $this->provider->value,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'checkout_url' => $this->when(! $this->isFinal(), $this->checkout_url),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
