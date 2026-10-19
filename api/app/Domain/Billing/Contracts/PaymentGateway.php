<?php

namespace App\Domain\Billing\Contracts;

use App\Domain\Billing\Data\CheckoutSession;
use App\Domain\Billing\Data\Payer;
use App\Domain\Billing\Data\WebhookOutcome;
use App\Domain\Billing\Models\Payment;
use Illuminate\Http\Request;

interface PaymentGateway
{
    /** Crée la session de paiement chez le prestataire et renvoie l'URL où rediriger le membre. */
    public function createCheckout(Payment $payment, Payer $payer): CheckoutSession;

    /** Vérifie l'authenticité d'une notification du prestataire et la traduit. Null si invalide. */
    public function parseWebhook(Request $request): ?WebhookOutcome;
}
