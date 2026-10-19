<?php

namespace App\Domain\Billing\Actions;

use App\Domain\Billing\Contracts\Payable;
use App\Domain\Billing\Enums\PaymentProvider;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use App\Domain\Identity\Models\User;
use App\Infrastructure\Payments\PaymentGatewayManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;

final class InitiatePayment
{
    public function __construct(private readonly PaymentGatewayManager $gateways) {}

    public function __invoke(User $user, Payable&Model $payable, int $amount, PaymentProvider $provider): Payment
    {
        $gateway = $this->gateways->for($provider);

        $payment = Payment::create([
            'user_id' => $user->id,
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'amount' => $amount,
            'currency' => config('sunoogo.currency'),
            'provider' => $provider,
            'status' => PaymentStatus::Pending,
            'reference' => 'SNG-'.Str::upper(Str::random(12)),
            'meta' => ['label' => $payable->paymentLabel()],
        ]);

        try {
            $session = $gateway->createCheckout($payment);
        } catch (Throwable $e) {
            $payment->update(['status' => PaymentStatus::Failed, 'meta' => [...$payment->meta, 'error' => $e->getMessage()]]);

            throw $e;
        }

        $payment->update([
            'checkout_url' => $session->checkoutUrl,
            'provider_reference' => $session->providerReference,
        ]);

        return $payment;
    }
}
