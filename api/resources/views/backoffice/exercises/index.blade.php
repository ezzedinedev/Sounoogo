<x-admin.page title="Exercices" description="Exercices guidés de l'application, utilisables hors ligne.">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.exercises.create')" icon="plus">Nouvel exercice</x-admin.button>
    </x-slot:actions>

    <x-admin.card flush>
        @if ($exercises->isEmpty())
            <x-admin.empty icon="sparkles" title="Aucun exercice" subtitle="Crée un premier exercice guidé." />
        @else
            <x-admin.table>
                <thead><tr><th>Exercice</th><th>Catégorie</th><th>Format</th><th class="text-end">Durée</th><th>Langue</th><th>Publication</th><th></th></tr></thead>
                <tbody>
                    @foreach ($exercises as $exercise)
                        <tr>
                            <td class="max-w-md"><p class="font-medium">{{ $exercise->title }}</p><p class="truncate text-xs text-muted-foreground">{{ $exercise->summary }}</p></td>
                            <td>{{ $exercise->category->label() }}</td>
                            <td class="text-muted-foreground">
                                {{ $exercise->breathing ? 'Respiration rythmée' : count($exercise->steps ?? []).' étapes' }}
                                @if ($exercise->audio_path)<x-admin.badge variant="primary" class="ms-1">Audio</x-admin.badge>@endif
                            </td>
                            <td class="text-end tabular-nums">{{ $exercise->duration_minutes }} min</td>
                            <td class="uppercase text-muted-foreground">{{ $exercise->language }}</td>
                            <td>@if ($exercise->published_at)<x-admin.badge variant="success">Publié</x-admin.badge>@else<x-admin.badge>Brouillon</x-admin.badge>@endif</td>
                            <td class="text-end"><x-admin.button :href="route('backoffice.exercises.edit', $exercise)" variant="outline" size="sm">Modifier</x-admin.button></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin.page>
