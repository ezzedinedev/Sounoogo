<x-admin.page title="Paiements" description="Paiements Mobile Money (MoneyFusion) et paiements de test.">
    <div class="mb-4 grid gap-4 sm:grid-cols-3">
        <x-admin.stat label="Encaissé ce mois-ci" :value="number_format($totalMonth, 0, ',', ' ').' F'" detail="Paiements confirmés par les prestataires" />
    </div>

    <x-admin.card flush>
        <form method="get" class="flex flex-col gap-2 border-b p-4 sm:flex-row">
            <div class="relative flex-1">
                <x-icon name="search" :size="16" class="pointer-events-none absolute start-3 top-1/2 -translate-y-1/2 text-muted-foreground" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Référence SNG-…" aria-label="Référence" class="h-9 w-full rounded-md border border-input bg-background ps-9 pe-3 text-sm shadow-xs focus-visible:outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/40">
            </div>
            <x-admin.select name="statut" :options="collect(\App\Domain\Billing\Enums\PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])" :value="$status?->value" placeholder="Tous les états" class="sm:w-48" aria-label="État" />
            <x-admin.button variant="outline">Filtrer</x-admin.button>
        </form>
        @if ($payments->isEmpty())
            <x-admin.empty icon="receipt" title="Aucun paiement" />
        @else
            <x-admin.table>
                <thead><tr><th>Référence</th><th>Objet</th><th>Membre</th><th>Moyen</th><th class="text-end">Montant</th><th>État</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td class="font-mono text-xs">{{ $payment->reference }}</td>
                            <td>{{ $payment->meta['label'] ?? '—' }}</td>
                            <td>{{ $payment->user->pseudonym }}</td>
                            <td><x-admin.badge variant="outline">{{ $payment->provider->label() }}</x-admin.badge></td>
                            <td class="text-end font-medium"><x-admin.money :amount="$payment->amount" /></td>
                            <td><x-admin.status :value="$payment->status" /></td>
                            <td class="text-muted-foreground whitespace-nowrap">{{ ($payment->paid_at ?? $payment->created_at)->translatedFormat('j M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($payments->hasPages()){{ $payments->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
