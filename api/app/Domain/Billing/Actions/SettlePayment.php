<?php

namespace App\Domain\Billing\Actions;

use App\Domain\Billing\Contracts\Payable;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Applique le résultat final d'un paiement. Idempotent : les prestataires
 * renvoient parfois la même notification plusieurs fois.
 */
final class SettlePayment
{
    public function __invoke(Payment $payment, PaymentStatus $status, array $payload = []): Payment
    {
        return DB::transaction(function () use ($payment, $status, $payload) {
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->isFinal() || $status === PaymentStatus::Pending) {
                return $payment;
            }

            $payment->update([
                'status' => $status,
                'paid_at' => $status === PaymentStatus::Succeeded ? now() : null,
                'meta' => [...($payment->meta ?? []), 'last_notification' => $payload],
            ]);

            if ($status === PaymentStatus::Succeeded && $payment->payable instanceof Payable) {
                $payment->payable->onPaymentSucceeded($payment);
            }

            return $payment;
        });
    }
}
