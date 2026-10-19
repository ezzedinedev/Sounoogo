<x-admin.page title="Alertes" description="Protocole d'urgence : prise en charge et compte rendu de chaque alerte.">
    <x-slot:actions>
        <div class="inline-flex rounded-lg bg-muted p-1 text-sm" role="tablist">
            <a href="{{ route('backoffice.alerts.index') }}" @class(['rounded-md px-3 py-1.5 font-medium', 'bg-background shadow-xs' => ! $status, 'text-muted-foreground hover:text-foreground' => $status])>En cours</a>
            <a href="{{ route('backoffice.alerts.index', ['statut' => 'resolved']) }}" @class(['rounded-md px-3 py-1.5 font-medium', 'bg-background shadow-xs' => $status?->value === 'resolved', 'text-muted-foreground hover:text-foreground' => $status?->value !== 'resolved'])>Résolues</a>
        </div>
    </x-slot:actions>

    <x-admin.card flush data-live-alerts>
        @if ($alerts->isEmpty())
            <x-admin.empty icon="shield-check" title="Aucune alerte" subtitle="Les nouvelles alertes apparaissent ici en temps réel." />
        @else
            <x-admin.table>
                <thead><tr><th>Niveau</th><th>Origine</th><th>Membre</th><th>Écoutant</th><th>État</th><th>Reçue</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @foreach ($alerts as $alert)
                        <tr>
                            <td><x-admin.status :value="$alert->level" /></td>
                            <td>
                                {{ $alert->sourceLabel() }}
                                @if ($alert->reason)<p class="mt-1 max-w-xs text-xs text-muted-foreground">{{ $alert->reason }}</p>@endif
                            </td>
                            <td class="font-medium">{{ $alert->member->pseudonym }}</td>
                            <td class="text-muted-foreground">{{ $alert->conversation?->listener?->displayName() ?? 'En attente' }}</td>
                            <td>
                                <x-admin.status :value="$alert->status" />
                                @if ($alert->handler)<p class="mt-1 text-xs text-muted-foreground">{{ $alert->handler->displayName() }}</p>@endif
                                @if ($alert->notes)<p class="mt-1 max-w-xs text-xs text-muted-foreground">{{ $alert->notes }}</p>@endif
                            </td>
                            <td class="text-muted-foreground whitespace-nowrap">{{ $alert->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if ($alert->conversation)
                                        <x-admin.button :href="route('backoffice.listening.show', $alert->conversation)" variant="outline" size="sm">Ouvrir</x-admin.button>
                                    @endif
                                    @if ($alert->status->value === 'open')
                                        <form method="post" action="{{ route('backoffice.alerts.acknowledge', $alert) }}">@csrf<x-admin.button size="sm">Prendre en charge</x-admin.button></form>
                                    @endif
                                    @if ($alert->status->value !== 'resolved')
                                        <x-admin.button type="button" variant="success" size="sm" data-hs-overlay="#resolve-{{ $alert->id }}">Résoudre</x-admin.button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($alerts->hasPages()){{ $alerts->links() }}@endif</x-slot:footer>
    </x-admin.card>

    @foreach ($alerts->where('status.value', '!=', 'resolved') as $alert)
        <x-admin.modal :id="'resolve-'.$alert->id" title="Résoudre l'alerte" description="Le compte rendu est conservé pour la supervision clinique.">
            <form method="post" action="{{ route('backoffice.alerts.resolve', $alert) }}" class="grid gap-4 px-6 py-6">
                @csrf
                <x-admin.textarea :id="'notes-'.$alert->id" name="notes" label="Compte rendu" required placeholder="Actions menées, orientation proposée, suivi prévu…" />
                <div class="flex justify-end gap-2">
                    <x-admin.button type="button" variant="outline" :data-hs-overlay="'#resolve-'.$alert->id">Annuler</x-admin.button>
                    <x-admin.button variant="success">Marquer comme résolue</x-admin.button>
                </div>
            </form>
        </x-admin.modal>
    @endforeach
</x-admin.page>
