<x-backoffice.page title="Paiements" pretitle="Administration">
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm"><div class="card-body">
                <div class="text-secondary">Encaissé ce mois-ci</div>
                <div class="h2 mb-0"><x-backoffice.money :amount="$totalMonth" /></div>
            </div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body border-bottom py-3">
            <form method="get" class="row g-2">
                <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Référence SNG-…" aria-label="Référence"></div>
                <div class="col-md-4">
                    <select name="statut" class="form-select" aria-label="État">
                        <option value="">Tous les états</option>
                        @foreach (\App\Domain\Billing\Enums\PaymentStatus::cases() as $case)
                            <option value="{{ $case->value }}" @selected($status === $case)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3"><button class="btn w-100" type="submit">Filtrer</button></div>
            </form>
        </div>
        @if ($payments->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="receipt" title="Aucun paiement" /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Référence</th><th>Objet</th><th>Membre</th><th>Moyen</th><th>Montant</th><th>État</th><th>Date</th></tr></thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="font-monospace">{{ $payment->reference }}</td>
                                <td>{{ $payment->meta['label'] ?? '—' }}</td>
                                <td>{{ $payment->user->pseudonym }}</td>
                                <td>{{ $payment->provider->label() }}</td>
                                <td><x-backoffice.money :amount="$payment->amount" /></td>
                                <td><x-backoffice.status :value="$payment->status" /></td>
                                <td class="text-secondary">{{ ($payment->paid_at ?? $payment->created_at)->translatedFormat('j M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $payments->links() }}</div>
        @endif
    </div>
</x-backoffice.page>
