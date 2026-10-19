<x-backoffice.page title="Alertes" pretitle="Supervision">
    <x-slot:actions>
        <div class="btn-group" role="group" aria-label="Filtrer par état">
            <a href="{{ route('backoffice.alerts.index') }}" @class(['btn', 'active' => ! $status])>En cours</a>
            <a href="{{ route('backoffice.alerts.index', ['statut' => 'resolved']) }}" @class(['btn', 'active' => $status?->value === 'resolved'])>Résolues</a>
        </div>
    </x-slot:actions>

    <div class="card" data-live-alerts>
        @if ($alerts->isEmpty())
            <div class="card-body"><x-backoffice.empty icon="shield-check" title="Aucune alerte" subtitle="Les nouvelles alertes apparaissent ici en temps réel." /></div>
        @else
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr><th>Niveau</th><th>Origine</th><th>Membre</th><th>Écoutant</th><th>État</th><th>Reçue</th><th class="w-1"></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($alerts as $alert)
                            <tr>
                                <td><x-backoffice.status :value="$alert->level" /></td>
                                <td>{{ $alert->source === 'sos' ? 'Bouton SOS' : 'Mots à risque' }}</td>
                                <td>{{ $alert->member->pseudonym }}</td>
                                <td>{{ $alert->conversation?->listener?->displayName() ?? 'En attente' }}</td>
                                <td>
                                    <x-backoffice.status :value="$alert->status" />
                                    @if ($alert->handler)<div class="small text-secondary">{{ $alert->handler->displayName() }}</div>@endif
                                </td>
                                <td class="text-secondary">{{ $alert->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        @if ($alert->conversation)
                                            <a href="{{ route('backoffice.listening.show', $alert->conversation) }}" class="btn btn-sm">Ouvrir</a>
                                        @endif
                                        @if ($alert->status->value === 'open')
                                            <form method="post" action="{{ route('backoffice.alerts.acknowledge', $alert) }}">@csrf<button class="btn btn-sm btn-primary">Prendre en charge</button></form>
                                        @endif
                                        @if ($alert->status->value !== 'resolved')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#resolve-{{ $alert->id }}">Résoudre</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if ($alert->status->value === 'resolved' && $alert->notes)
                                <tr><td colspan="7" class="text-secondary small pt-0"><i class="ti ti-notes"></i> {{ $alert->notes }}</td></tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $alerts->links() }}</div>
        @endif
    </div>

    @foreach ($alerts->where('status.value', '!=', 'resolved') as $alert)
        <div class="modal modal-blur fade" id="resolve-{{ $alert->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="{{ route('backoffice.alerts.resolve', $alert) }}" class="modal-content">
                    @csrf
                    <div class="modal-header"><h2 class="modal-title h3">Résoudre l'alerte</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button></div>
                    <div class="modal-body">
                        <label class="form-label" for="notes-{{ $alert->id }}">Compte rendu</label>
                        <textarea id="notes-{{ $alert->id }}" name="notes" rows="4" class="form-control" required placeholder="Actions menées, orientation proposée, suivi prévu…"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Marquer comme résolue</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-backoffice.page>
