<x-layouts.site :title="$succeeded ? 'Paiement reçu' : 'Paiement non abouti'">
    <section class="py-24">
        <x-site.container class="max-w-xl text-center">
            <span @class([
                'inline-flex size-16 items-center justify-center rounded-full',
                'bg-primary-50 text-primary-700' => $succeeded,
                'bg-sos-soft text-sos' => ! $succeeded,
            ])>
                <x-icon :name="$succeeded ? 'circle-check' : 'alert-triangle'" :size="32" />
            </span>
            <h1 class="mt-6 text-3xl font-medium tracking-tight">{{ $succeeded ? 'Merci, ton paiement est en cours de validation' : 'Le paiement n\'a pas abouti' }}</h1>
            <p class="mt-4 text-muted-foreground-1 leading-relaxed">
                {{ $succeeded
                    ? 'Tu peux revenir dans l\'application : ta réservation ou ton abonnement s\'activera dès que le paiement Mobile Money sera confirmé.'
                    : 'Aucun montant n\'a été débité. Reviens dans l\'application pour réessayer ou choisir un autre moyen de paiement.' }}
            </p>
            @if ($reference)
                <p class="mt-6 text-sm text-muted-foreground">Référence : <span class="font-mono">{{ $reference }}</span></p>
            @endif
        </x-site.container>
    </section>
</x-layouts.site>
