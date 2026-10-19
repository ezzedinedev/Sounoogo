<x-admin.page title="Versements" description="Demandes de versement des professionnels. Effectue le transfert Mobile Money, puis confirme-le ici avec sa référence.">
    <x-admin.card flush>
        <nav class="flex gap-1 border-b px-4 pt-3" aria-label="Filtrer par état">
            @foreach (\App\Domain\Consultation\Enums\PayoutStatus::cases() as $case)
                <a href="{{ route('backoffice.payouts.index', ['statut' => $case->value]) }}" @class([
                    '-mb-px inline-flex items-center gap-2 border-b-2 px-3 pb-2.5 text-sm font-medium transition-colors',
                    'border-primary text-foreground' => $status === $case,
                    'border-transparent text-muted-foreground hover:text-foreground' => $status !== $case,
                ]) @if ($status === $case) aria-current="page" @endif>
                    {{ $case->label() }}
                    <x-admin.badge :variant="$status === $case ? 'primary' : 'secondary'">{{ $counts[$case->value] ?? 0 }}</x-admin.badge>
                </a>
            @endforeach
        </nav>

        @if ($payouts->isEmpty())
            <x-admin.empty icon="cash" title="Aucune demande dans cet état" />
        @else
            <x-admin.table>
                <thead><tr><th>Professionnel</th><th>Numéro</th><th class="text-end">Montant</th><th>Demandé le</th><th>État</th><th></th></tr></thead>
                <tbody>
                    @foreach ($payouts as $payout)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $payout->professional->user->displayName() }}</p>
                                <p class="text-xs text-muted-foreground">{{ $payout->professional->title }}</p>
                            </td>
                            <td class="tabular-nums">{{ $payout->phone }}</td>
                            <td class="text-end font-medium"><x-admin.money :amount="$payout->amount" /></td>
                            <td class="whitespace-nowrap text-muted-foreground">{{ $payout->created_at->translatedFormat('j M Y, H:i') }}</td>
                            <td>
                                <x-admin.status :value="$payout->status" />
                                @if ($payout->processed_at)
                                    <p class="mt-1 text-xs text-muted-foreground">par {{ $payout->processor?->displayName() ?? '—' }}, le {{ $payout->processed_at->translatedFormat('j M') }}</p>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($payout->status === \App\Domain\Consultation\Enums\PayoutStatus::Requested)
                                    <x-admin.button size="sm" variant="outline" type="button" data-hs-overlay="#payout-{{ $payout->id }}">Traiter</x-admin.button>
                                    <x-admin.modal :id="'payout-'.$payout->id" title="Traiter le versement" :description="number_format($payout->amount, 0, ',', ' ').' F CFA vers le '.$payout->phone">
                                        <div class="grid gap-4 px-6 py-6 text-start">
                                            <form method="post" action="{{ route('backoffice.payouts.update', $payout) }}" class="grid gap-3">
                                                @csrf @method('patch')
                                                <input type="hidden" name="status" value="paid">
                                                <x-admin.input name="reference" :id="'reference-'.$payout->id" label="Référence de la transaction" hint="Telle que fournie par l'opérateur Mobile Money." required />
                                                <x-admin.button variant="success" icon="check">Confirmer le versement</x-admin.button>
                                            </form>
                                            <div class="flex items-center gap-3 text-xs text-muted-foreground"><span class="h-px flex-1 bg-border"></span>ou<span class="h-px flex-1 bg-border"></span></div>
                                            <form method="post" action="{{ route('backoffice.payouts.update', $payout) }}" class="grid gap-3">
                                                @csrf @method('patch')
                                                <input type="hidden" name="status" value="rejected">
                                                <x-admin.input name="note" :id="'note-'.$payout->id" label="Motif du refus" placeholder="Visible par le professionnel" required />
                                                <x-admin.button variant="outline" icon="ban">Refuser la demande</x-admin.button>
                                            </form>
                                        </div>
                                    </x-admin.modal>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($payouts->hasPages()){{ $payouts->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
