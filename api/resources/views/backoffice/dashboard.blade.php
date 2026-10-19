<x-backoffice.page title="Tableau de bord" :pretitle="now()->translatedFormat('l j F Y')">
    <div class="row row-deck row-cards">
        @php
            $cards = [
                ['En attente d\'écoute', $stats['waiting'], 'hourglass', $stats['waiting'] > 0 ? 'yellow' : 'secondary', route('backoffice.listening.index')],
                ['Conversations en cours', $stats['active'], 'messages', 'blue', route('backoffice.listening.index')],
                ['Alertes ouvertes', $stats['open_alerts'], 'alert-triangle', $stats['open_alerts'] > 0 ? 'red' : 'secondary', auth()->user()->can('alerts.manage') ? route('backoffice.alerts.index') : null],
                ['Écoutants disponibles', $stats['listeners_online'], 'headset', 'green', null],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $icon, $tone, $link])
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto"><span class="bg-{{ $tone }} text-white avatar"><i class="ti ti-{{ $icon }} icon"></i></span></div>
                            <div class="col">
                                <div class="h1 mb-0">{{ $value }}</div>
                                <div class="text-secondary">@if ($link)<a href="{{ $link }}" class="text-reset">{{ $label }}</a>@else{{ $label }}@endif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Conversations ouvertes · 14 derniers jours</h3></div>
                <div class="card-body">
                    @php($max = max(1, max($chart['values']->all())))
                    <div class="d-flex align-items-end gap-1" style="height: 180px" role="img" aria-label="Nombre de conversations ouvertes par jour sur 14 jours">
                        @foreach ($chart['values'] as $i => $value)
                            <div class="flex-fill d-flex flex-column align-items-center justify-content-end h-100" title="{{ $chart['labels'][$i] }} : {{ $value }}">
                                <div class="small text-secondary mb-1">{{ $value ?: '' }}</div>
                                <div class="w-100 rounded-top bg-primary" style="height: {{ max(2, $value / $max * 100) }}%"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between small text-secondary mt-2">
                        <span>{{ $chart['labels']->first() }}</span><span>{{ $chart['labels']->last() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item"><div class="datagrid-title">Nouveaux membres (7 j)</div><div class="datagrid-content">{{ $stats['members_week'] }}</div></div>
                        <div class="datagrid-item"><div class="datagrid-title">Signalements à traiter</div><div class="datagrid-content">{{ $stats['pending_reports'] }}</div></div>
                        <div class="datagrid-item"><div class="datagrid-title">Consultations confirmées</div><div class="datagrid-content">{{ $stats['bookings_upcoming'] }}</div></div>
                        <div class="datagrid-item"><div class="datagrid-title">Revenus du mois</div><div class="datagrid-content"><x-backoffice.money :amount="$stats['revenue_month']" /></div></div>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user()->can('alerts.manage'))
            <div class="col-lg-6">
                <div class="card" data-live-alerts>
                    <div class="card-header">
                        <h3 class="card-title">Alertes en cours</h3>
                        <div class="card-actions"><a href="{{ route('backoffice.alerts.index') }}">Tout voir</a></div>
                    </div>
                    @if ($alerts->isEmpty())
                        <div class="card-body"><x-backoffice.empty icon="shield-check" title="Aucune alerte en cours" /></div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($alerts as $alert)
                                <a href="{{ $alert->conversation ? route('backoffice.listening.show', $alert->conversation) : route('backoffice.alerts.index') }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <x-backoffice.status :value="$alert->level" />
                                            <span class="ms-2">{{ $alert->source === 'sos' ? 'Bouton SOS' : 'Mots à risque détectés' }}</span>
                                        </div>
                                        <span class="text-secondary small">{{ $alert->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if (auth()->user()->can('conversations.listen'))
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Mes conversations</h3></div>
                    @if ($myConversations->isEmpty())
                        <div class="card-body"><x-backoffice.empty icon="message-circle" title="Aucune conversation en cours" subtitle="Passe en « Disponible » pour recevoir la prochaine conversation." /></div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach ($myConversations as $conversation)
                                <a href="{{ route('backoffice.listening.show', $conversation) }}" class="list-group-item list-group-item-action">
                                    <div class="text-truncate">{{ $conversation->latestMessage?->body }}</div>
                                    <div class="small text-secondary">{{ $conversation->updated_at->diffForHumans() }}</div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-backoffice.page>
