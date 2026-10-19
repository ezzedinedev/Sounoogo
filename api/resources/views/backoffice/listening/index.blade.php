@php($topics = ['stress' => 'Stress', 'anxiety' => 'Anxiété', 'sadness' => 'Tristesse', 'relationship' => 'Relation', 'family' => 'Famille', 'grief' => 'Deuil', 'violence' => 'Violence', 'self_esteem' => 'Estime de soi', 'other' => 'Autre'])
<x-admin.page title="Écoute" description="File d'attente en temps réel, les conversations à risque en premier.">
    <div class="grid gap-4 lg:grid-cols-5" data-live-queue>
        <x-admin.card class="lg:col-span-3" title="File d'attente" :description="$queue->count().' personne'.($queue->count() > 1 ? 's' : '').' attend'.($queue->count() > 1 ? 'ent' : '')" flush>
            @if ($queue->isEmpty())
                <x-admin.empty icon="mood-smile" title="Personne n'attend" subtitle="Les nouvelles conversations apparaissent ici en temps réel." />
            @else
                <x-admin.table>
                    <thead><tr><th>Membre</th><th>Thème</th><th>Risque</th><th>Attente</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($queue as $conversation)
                            <tr>
                                <td class="font-medium">{{ $conversation->member->pseudonym }}</td>
                                <td>@if ($conversation->topic)<x-admin.badge variant="outline">{{ $topics[$conversation->topic] ?? $conversation->topic }}</x-admin.badge>@else<span class="text-muted-foreground">—</span>@endif</td>
                                <td><x-admin.status :value="$conversation->risk_level" /></td>
                                <td class="tabular-nums text-muted-foreground">{{ $conversation->created_at->diffForHumans(syntax: \Carbon\CarbonInterface::DIFF_ABSOLUTE, short: true) }}</td>
                                <td class="text-end">
                                    <form method="post" action="{{ route('backoffice.listening.take', $conversation) }}">
                                        @csrf
                                        <x-admin.button size="sm">Prendre</x-admin.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table>
            @endif
        </x-admin.card>

        <div class="grid content-start gap-4 lg:col-span-2">
            <x-admin.card title="Mes conversations" flush>
                @if ($mine->isEmpty())
                    <x-admin.empty icon="message-circle" title="Aucune conversation en cours" />
                @else
                    <ul class="divide-y">
                        @foreach ($mine as $conversation)
                            <li>
                                <a href="{{ route('backoffice.listening.show', $conversation) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-muted/40">
                                    <span class="inline-flex size-9 items-center justify-center rounded-full bg-primary-soft text-sm font-semibold text-primary">{{ mb_substr($conversation->member->pseudonym, 0, 1) }}</span>
                                    <span class="min-w-0 flex-1">
                                        <span class="flex items-center gap-2 text-sm font-medium">{{ $conversation->member->pseudonym }}@if ($conversation->risk_level->value !== 'none')<x-admin.status :value="$conversation->risk_level" />@endif</span>
                                        <span class="block truncate text-sm text-muted-foreground">{{ $conversation->latestMessage?->body }}</span>
                                    </span>
                                    <x-icon name="chevron-right" :size="16" class="text-muted-foreground" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>

            @if ($supervised->isNotEmpty())
                <x-admin.card title="Supervision" description="Conversations à risque en cours" flush>
                    <ul class="divide-y">
                        @foreach ($supervised as $conversation)
                            <li>
                                <a href="{{ route('backoffice.listening.show', $conversation) }}" class="flex items-center justify-between gap-3 px-6 py-3 text-sm hover:bg-muted/40">
                                    <span><span class="font-medium">{{ $conversation->member->pseudonym }}</span> <span class="text-muted-foreground">avec {{ $conversation->listener?->displayName() }}</span></span>
                                    <x-admin.status :value="$conversation->risk_level" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-admin.page>
