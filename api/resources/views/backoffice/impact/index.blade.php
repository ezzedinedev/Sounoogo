<x-admin.page title="Rapport d'impact" description="Chiffres agrégés et anonymisés, prêts à partager avec les partenaires. Les effectifs inférieurs à {{ config('sunoogo.impact.min_cell_size') }} sont masqués.">
    <x-slot:actions>
        <x-admin.button variant="outline" size="sm" icon="download" :href="route('backoffice.impact.csv', ['periode' => $periodKey])">CSV</x-admin.button>
        <x-admin.button size="sm" icon="file-text" :href="route('backoffice.impact.pdf', ['periode' => $periodKey])">PDF</x-admin.button>
    </x-slot:actions>

    <nav class="mb-4 flex flex-wrap items-center gap-1.5" aria-label="Période">
        @foreach ($periods as $key => $label)
            <a href="{{ route('backoffice.impact.index', ['periode' => $key]) }}" @class([
                'inline-flex h-8 items-center rounded-full border px-3.5 text-[13px] font-medium transition-colors',
                'border-primary bg-primary text-primary-foreground' => $periodKey === $key,
                'bg-background text-muted-foreground hover:bg-accent hover:text-foreground' => $periodKey !== $key,
            ]) @if ($periodKey === $key) aria-current="true" @endif>{{ $label }}</a>
        @endforeach
        <span class="ms-auto text-[13px] text-muted-foreground">Du {{ $from->translatedFormat('j F Y') }} au {{ $to->translatedFormat('j F Y') }}</span>
    </nav>

    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ($sections as $title => $figures)
            <x-admin.card :title="$title">
                <dl class="grid grid-cols-2 gap-x-6 gap-y-5">
                    @foreach ($figures as $figure)
                        <div @class(['col-span-2' => $loop->first])>
                            <dt class="text-[13px] text-muted-foreground">{{ $figure->label }}</dt>
                            <dd @class(['mt-1 font-semibold tabular-nums tracking-tight', 'text-3xl' => $loop->first, 'text-xl' => ! $loop->first, 'text-muted-foreground' => $figure->suppressed])
                                @if ($figure->suppressed) title="Effectif trop faible pour être publié" @endif>{{ $figure->display() }}</dd>
                            @if ($figure->hint)<p class="mt-1 text-xs text-muted-foreground">{{ $figure->hint }}</p>@endif
                        </div>
                    @endforeach
                </dl>
            </x-admin.card>
        @endforeach
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-5">
        <x-admin.card class="lg:col-span-3" title="Conversations par mois" description="Douze mois jusqu'à la fin de la période.">
            @php($chart = [
                'labels' => $monthly->map(fn ($row) => ucfirst($row['month']->translatedFormat('M')))->values(),
                'series' => [['name' => 'Conversations', 'data' => $monthly->map(fn ($row) => $row['figure']->exportValue())->values()]],
            ])
            <div class="-mx-2 h-[240px]" role="img" aria-label="Conversations ouvertes par mois" data-bar-chart='@json($chart)'></div>
            @if ($monthly->contains(fn ($row) => $row['figure']->suppressed))
                <p class="mt-2 text-xs text-muted-foreground">Les mois comptant moins de {{ config('sunoogo.impact.min_cell_size') }} conversations ne sont pas affichés.</p>
            @endif
        </x-admin.card>

        <x-admin.card class="lg:col-span-2" title="Thèmes abordés" flush>
            @if ($topics === [])
                <x-admin.empty icon="messages" title="Aucune conversation sur la période" />
            @else
                @php($max = max(array_map(fn ($figure) => $figure->suppressed ? 0 : $figure->value, $topics)) ?: 1)
                <ul class="grid gap-3 px-6 pb-6">
                    @foreach ($topics as $figure)
                        <li>
                            <div class="flex items-baseline justify-between text-sm">
                                <span>{{ $figure->label }}</span>
                                <span @class(['tabular-nums font-medium', 'text-muted-foreground' => $figure->suppressed])>{{ $figure->display() }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-muted">
                                <div class="h-full rounded-full bg-primary transition-[width] duration-700" style="width: {{ $figure->suppressed ? 0 : round($figure->value * 100 / $max) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    <p class="mt-6 max-w-3xl text-xs leading-relaxed text-muted-foreground">
        Méthode : comptages sur la période choisie, sans aucun pseudonyme ni contenu de conversation. Les médianes et moyennes
        calculées sur moins de {{ config('sunoogo.impact.min_cell_size') }} personnes sont masquées. Chaque consultation ou export de ce rapport est inscrit au journal.
    </p>
</x-admin.page>
