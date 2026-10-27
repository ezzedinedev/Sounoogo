<x-layouts.site :title="$succeeded ? 'Demande confirmée' : 'Demande non aboutie'">
    <section class="py-24">
        <x-site.container class="max-w-xl text-center">
            <span @class([
                'inline-flex size-16 items-center justify-center rounded-full',
                'bg-primary-50 text-primary-700' => $succeeded,
                'bg-sos-soft text-sos' => ! $succeeded,
            ])>
                <x-site.icon :name="$succeeded ? 'circle-check' : 'alert-triangle'" :size="32" />
            </span>
            <h1 class="mt-6 text-3xl font-medium tracking-tight">{{ $succeeded ? 'Merci, ta demande est en cours de confirmation' : 'La demande n\'a pas abouti' }}</h1>
            <p class="mt-4 text-muted-foreground-1 leading-relaxed">
                {{ $succeeded
                    ? 'Tu peux retrouver son état dans l\'application ou contacter notre équipe si tu as besoin d\'aide.'
                    : 'Reviens dans l\'application pour réessayer ou contacte notre équipe pour être accompagné·e.' }}
            </p>
            @if ($reference)
                <p class="mt-6 text-sm text-muted-foreground">Référence de suivi : <span class="font-mono">{{ $reference }}</span></p>
            @endif
        </x-site.container>
    </section>
</x-layouts.site>
