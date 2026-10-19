@php
    use App\Support\Trend;
    $conversationsTrend = new Trend($stats['conversations_week'], $stats['conversations_prev_week']);
    $membersTrend = new Trend($stats['members_week'], $stats['members_prev_week']);
    $revenueTrend = new Trend($stats['revenue_month'], $stats['revenue_prev_month']);
@endphp
<x-admin.page title="Tableau de bord" :description="ucfirst(now()->translatedFormat('l j F Y'))">
    <x-slot:actions>
        @can('conversations.listen')
            <x-admin.button :href="route('backoffice.listening.index')" icon="messages">Ouvrir l'écoute</x-admin.button>
        @endcan
    </x-slot:actions>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="En attente d'écoute" :value="$stats['waiting']"
            :badge="$stats['waiting'] > 0 ? 'À prendre' : 'À jour'" :badge-variant="$stats['waiting'] > 0 ? 'warning' : 'success'"
            :hint="$stats['active'].' conversation'.($stats['active'] > 1 ? 's' : '').' en cours'"
            :detail="$stats['listeners_online'].' écoutant'.($stats['listeners_online'] > 1 ? 's' : '').' disponible'.($stats['listeners_online'] > 1 ? 's' : '')"
            :href="auth()->user()->can('conversations.listen') ? route('backoffice.listening.index') : null" />
        <x-admin.stat label="Alertes ouvertes" :value="$stats['open_alerts']"
            :badge="$stats['open_alerts'] > 0 ? 'Prioritaire' : 'Aucune'" :badge-variant="$stats['open_alerts'] > 0 ? 'destructive' : 'success'"
            :badge-icon="$stats['open_alerts'] > 0 ? 'alert-triangle' : 'shield-check'"
            :hint="$stats['pending_reports'].' signalement'.($stats['pending_reports'] > 1 ? 's' : '').' à traiter'" detail="Protocole d'urgence actif"
            :href="auth()->user()->can('alerts.manage') ? route('backoffice.alerts.index') : null" />
        <x-admin.stat label="Conversations · 7 j" :value="$stats['conversations_week']"
            :badge="$conversationsTrend->label()" :badge-icon="$conversationsTrend->icon()"
            :hint="$stats['members_week'].' nouveau'.($stats['members_week'] > 1 ? 'x' : '').' membre'.($stats['members_week'] > 1 ? 's' : '')" :detail="'Inscriptions : '.$membersTrend->label().' sur 7 jours'" />
        <x-admin.stat label="Revenus du mois" :value="number_format($stats['revenue_month'], 0, ',', ' ').' F'"
            :badge="$revenueTrend->label()" :badge-icon="$revenueTrend->icon()"
            :hint="$stats['bookings_upcoming'].' consultation'.($stats['bookings_upcoming'] > 1 ? 's' : '').' confirmée'.($stats['bookings_upcoming'] > 1 ? 's' : '')" detail="Consultations, formations et abonnements" />
    </div>

    <x-admin.card class="mt-4" title="Activité" description="Conversations ouvertes et alertes, 30 derniers jours">
        <x-slot:actions>
            <div class="flex items-center gap-4 text-xs text-muted-foreground">
                <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-chart-1"></span>Conversations</span>
                <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-chart-2"></span>Alertes</span>
            </div>
        </x-slot:actions>
        <div data-area-chart='@json($chart)' class="-mx-2 h-[260px]" role="img" aria-label="Évolution des conversations et des alertes sur 30 jours"></div>
    </x-admin.card>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        @can('alerts.manage')
            <x-admin.card title="Alertes en cours" description="Les plus urgentes en premier" flush data-live-alerts>
                <x-slot:actions><x-admin.button :href="route('backoffice.alerts.index')" variant="outline" size="sm">Tout voir</x-admin.button></x-slot:actions>
                @if ($alerts->isEmpty())
                    <x-admin.empty icon="shield-check" title="Aucune alerte en cours" subtitle="Les nouvelles alertes apparaissent ici en temps réel." />
                @else
                    <x-admin.table>
                        <thead><tr><th>Niveau</th><th>Origine</th><th>Membre</th><th class="text-end">Reçue</th></tr></thead>
                        <tbody>
                            @foreach ($alerts as $alert)
                                <tr>
                                    <td><x-admin.status :value="$alert->level" /></td>
                                    <td>{{ $alert->sourceLabel() }}</td>
                                    <td>
                                        @if ($alert->conversation)
                                            <a href="{{ route('backoffice.listening.show', $alert->conversation) }}" class="font-medium hover:underline">{{ $alert->member->pseudonym }}</a>
                                        @else
                                            {{ $alert->member->pseudonym }}
                                        @endif
                                    </td>
                                    <td class="text-end text-muted-foreground">{{ $alert->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-admin.table>
                @endif
            </x-admin.card>
        @endcan

        @can('conversations.listen')
            <x-admin.card title="Mes conversations" description="Celles que tu accompagnes en ce moment" flush>
                @if ($myConversations->isEmpty())
                    <x-admin.empty icon="message-circle" title="Aucune conversation en cours" subtitle="Passe en « Disponible » pour recevoir la prochaine conversation." />
                @else
                    <ul class="divide-y">
                        @foreach ($myConversations as $conversation)
                            <li>
                                <a href="{{ route('backoffice.listening.show', $conversation) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-muted/40">
                                    <span class="inline-flex size-9 items-center justify-center rounded-full bg-primary-soft text-sm font-semibold text-primary">{{ mb_substr($conversation->member->pseudonym, 0, 1) }}</span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-medium">{{ $conversation->member->pseudonym }}</span>
                                        <span class="block truncate text-sm text-muted-foreground">{{ $conversation->latestMessage?->body }}</span>
                                    </span>
                                    <span class="text-xs text-muted-foreground">{{ $conversation->updated_at->diffForHumans(short: true) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>
        @endcan
    </div>
</x-admin.page>
