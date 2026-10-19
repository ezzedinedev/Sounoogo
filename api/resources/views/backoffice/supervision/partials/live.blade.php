@php
    $median = $today['median_response'];
    $medianLabel = $median === null ? '—' : ($median < 60 ? $median.' s' : intdiv($median, 60).' min '.str_pad((string) ($median % 60), 2, '0', STR_PAD_LEFT));
@endphp
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat label="En attente" :value="$waiting->count()"
        :badge="$waiting->where('risk_level.value', 'high')->isNotEmpty() ? 'Risque élevé' : ($waiting->isEmpty() ? 'À jour' : 'À prendre')"
        :badge-variant="$waiting->where('risk_level.value', 'high')->isNotEmpty() ? 'destructive' : ($waiting->isEmpty() ? 'success' : 'warning')"
        :hint="$listeners->count().' écoutant'.($listeners->count() > 1 ? 's' : '').' disponible'.($listeners->count() > 1 ? 's' : '')" detail="Les plus à risque passent en premier" />
    <x-admin.stat label="Écoutes en cours" :value="$active->count()"
        :badge="$active->where('risk_level.value', '!=', 'none')->count().' à surveiller'" :badge-variant="$active->where('risk_level.value', '!=', 'none')->isNotEmpty() ? 'warning' : 'outline'"
        hint="Triées par niveau de risque" detail="Dernière activité affichée en direct" />
    <x-admin.stat label="Alertes ouvertes" :value="$alerts->count()"
        :badge="$alerts->where('status.value', 'open')->isNotEmpty() ? $alerts->where('status.value', 'open')->count().' sans prise en charge' : 'Toutes prises en charge'"
        :badge-variant="$alerts->where('status.value', 'open')->isNotEmpty() ? 'destructive' : 'success'"
        :hint="$today['alerts'].' aujourd\'hui · '.$today['resolved'].' résolue'.($today['resolved'] > 1 ? 's' : '')" detail="Protocole d'urgence" />
    <x-admin.stat label="Première réponse (médiane)" :value="$medianLabel"
        :hint="$today['conversations'].' conversation'.($today['conversations'] > 1 ? 's' : '').' aujourd\'hui'" detail="Entre la demande et le premier message de l'écoutant" />
</div>

<div class="mt-4 grid gap-4 xl:grid-cols-3">
    <x-admin.card title="Alertes" description="À prendre en charge en priorité." flush class="xl:col-span-1 xl:self-start">
        @forelse ($alerts as $alert)
            <div @class(['flex items-start gap-3 border-t px-6 py-4 first:border-t-0', 'bg-destructive-soft/40' => $alert->status->value === 'open'])>
                <span @class(['mt-1.5 size-2 shrink-0 rounded-full', 'bg-destructive animate-pulse motion-reduce:animate-none' => $alert->status->value === 'open', 'bg-warning' => $alert->status->value !== 'open'])></span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-admin.status :value="$alert->level" />
                        <span class="text-sm font-medium">{{ $alert->member->pseudonym }}</span>
                        <span class="text-xs text-muted-foreground">{{ $alert->sourceLabel() }}</span>
                    </div>
                    @if ($alert->reason)<p class="mt-1 text-xs text-muted-foreground">{{ $alert->reason }}</p>@endif
                    <p class="mt-1 text-xs text-muted-foreground">Il y a <span data-since="{{ $alert->created_at->toIso8601String() }}">{{ $alert->created_at->diffForHumans(syntax: true) }}</span>@if ($alert->handler) · {{ $alert->handler->displayName() }}@endif</p>
                </div>
                <div class="flex shrink-0 flex-col gap-1.5">
                    @if ($alert->conversation)
                        <x-admin.button :href="route('backoffice.listening.show', $alert->conversation)" variant="outline" size="sm">Ouvrir</x-admin.button>
                    @endif
                    @if ($alert->status->value === 'open')
                        <form method="post" action="{{ route('backoffice.alerts.acknowledge', $alert) }}">@csrf<x-admin.button size="sm" class="w-full">Je m'en occupe</x-admin.button></form>
                    @endif
                </div>
            </div>
        @empty
            <x-admin.empty icon="shield-check" title="Aucune alerte" subtitle="Tout est calme." />
        @endforelse
    </x-admin.card>

    <div class="grid content-start gap-4 xl:col-span-2">
        <x-admin.card title="File d'attente" :description="$waiting->isEmpty() ? 'Personne n\'attend.' : 'Temps d\'attente en direct.'" flush>
            @if ($waiting->isNotEmpty())
                <x-admin.table>
                    <thead><tr><th>Membre</th><th>Thème</th><th>Risque</th><th>Attente</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        @foreach ($waiting as $conversation)
                            <tr>
                                <td class="font-medium">{{ $conversation->member->pseudonym }}</td>
                                <td class="text-muted-foreground">{{ $conversation->topicLabel() ?? '—' }}</td>
                                <td><x-admin.status :value="$conversation->risk_level" /></td>
                                <td class="tabular-nums" data-since="{{ $conversation->created_at->toIso8601String() }}" data-warn-after="300">—</td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('backoffice.listening.take', $conversation) }}">@csrf<x-admin.button size="sm">Prendre</x-admin.button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table>
            @endif
        </x-admin.card>

        <x-admin.card title="Écoutes en cours" flush>
            @if ($active->isEmpty())
                <x-admin.empty icon="messages" title="Aucune écoute en cours" />
            @else
                <x-admin.table>
                    <thead><tr><th>Membre</th><th>Écoutant</th><th>Risque</th><th>Dernière activité</th><th class="text-end"></th></tr></thead>
                    <tbody>
                        @foreach ($active as $conversation)
                            <tr>
                                <td class="font-medium">{{ $conversation->member->pseudonym }}</td>
                                <td class="text-muted-foreground">{{ $conversation->listener?->displayName() ?? '—' }}</td>
                                <td><x-admin.status :value="$conversation->risk_level" /></td>
                                <td class="tabular-nums text-muted-foreground">il y a <span data-since="{{ $conversation->updated_at->toIso8601String() }}" data-warn-after="600">—</span></td>
                                <td class="text-end"><x-admin.button :href="route('backoffice.listening.show', $conversation)" variant="outline" size="sm">Suivre</x-admin.button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table>
            @endif
        </x-admin.card>

        <x-admin.card title="Écoutants disponibles" flush>
            @if ($listeners->isEmpty())
                <x-admin.empty icon="user-off" title="Aucun écoutant disponible" subtitle="Les membres attendent tant que personne n'est disponible." />
            @else
                <ul class="flex flex-wrap gap-2 px-6 py-5">
                    @foreach ($listeners as $listener)
                        <li class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-sm">
                            <span class="size-2 rounded-full bg-success"></span>{{ $listener->displayName() }}
                            <span class="text-muted-foreground">· {{ $listener->active_count }} en cours</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>
</div>
