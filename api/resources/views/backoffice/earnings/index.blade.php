<x-admin.page title="Mes revenus" description="Tes consultations terminées et tes versements Mobile Money.">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="Disponible" :value="number_format($summary['available'], 0, ',', ' ').' F'" detail="Montant que tu peux demander" />
        <x-admin.stat label="En cours de versement" :value="number_format($summary['requested'], 0, ',', ' ').' F'" detail="Demandes en attente de traitement" />
        <x-admin.stat label="Déjà versé" :value="number_format($summary['paid'], 0, ',', ' ').' F'" detail="Depuis ton inscription" />
        <x-admin.stat label="Consultations à venir" :value="number_format($summary['upcoming'], 0, ',', ' ').' F'" detail="Payées, pas encore tenues (avant commission)" />
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-5">
        <x-admin.card class="lg:col-span-3" title="Six derniers mois" description="Ta part sur les consultations terminées.">
            @if ($monthly->sum('amount') === 0)
                <x-admin.empty icon="chart-bar" title="Pas encore de revenus" subtitle="Ils apparaîtront ici dès ta première consultation terminée." />
            @else
                @php($chart = [
                    'labels' => $monthly->map(fn ($row) => ucfirst($row['month']->translatedFormat('M')))->values(),
                    'series' => [['name' => 'Revenus', 'data' => $monthly->pluck('amount')->values()]],
                    'unit' => ' F',
                ])
                <div class="-mx-2 h-[240px]" role="img" aria-label="Revenus des six derniers mois" data-bar-chart='@json($chart)'></div>
                <p class="mt-3 text-[13px] text-muted-foreground">
                    {{ trans_choice(':count consultation terminée|:count consultations terminées', $monthly->sum('count')) }} sur la période.
                    @if ($commission > 0) Commission SUNOOGO : {{ $commission }} % du prix de la séance. @endif
                </p>
            @endif
        </x-admin.card>

        <x-admin.card class="lg:col-span-2" title="Demander un versement" description="Versement sur ton compte Mobile Money, traité par l'équipe SUNOOGO.">
            @if ($summary['available'] < $minimum)
                <div class="rounded-lg border border-dashed px-4 py-5 text-center text-sm text-muted-foreground">
                    <x-icon name="wallet" :size="22" class="mx-auto mb-2" />
                    Le minimum pour un versement est de <x-admin.money :amount="$minimum" />.
                </div>
            @else
                <form method="post" action="{{ route('backoffice.earnings.payouts.store') }}" class="grid gap-4">
                    @csrf
                    <x-admin.input name="amount" type="number" label="Montant (F CFA)" :value="$summary['available']" :min="$minimum" :max="$summary['available']" step="1" required inputmode="numeric" />
                    <x-admin.input name="phone" type="tel" label="Numéro Mobile Money" :value="$profile->user->phone" placeholder="+226 70 00 00 00" autocomplete="tel" required />
                    <x-admin.button icon="send">Envoyer la demande</x-admin.button>
                </form>
            @endif
        </x-admin.card>
    </div>

    <x-admin.card class="mt-4" title="Historique des versements" flush>
        @if ($payouts->isEmpty())
            <x-admin.empty icon="cash" title="Aucune demande de versement" />
        @else
            <x-admin.table>
                <thead><tr><th>Demandé le</th><th>Numéro</th><th class="text-end">Montant</th><th>État</th><th>Détail</th></tr></thead>
                <tbody>
                    @foreach ($payouts as $payout)
                        <tr>
                            <td class="whitespace-nowrap">{{ $payout->created_at->translatedFormat('j M Y') }}</td>
                            <td class="tabular-nums">{{ $payout->phone }}</td>
                            <td class="text-end font-medium"><x-admin.money :amount="$payout->amount" /></td>
                            <td><x-admin.status :value="$payout->status" /></td>
                            <td class="text-muted-foreground">
                                @if ($payout->reference)Réf. <span class="font-mono text-xs">{{ $payout->reference }}</span>@endif
                                {{ $payout->note }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin.page>
