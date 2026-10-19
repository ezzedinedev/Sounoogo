<x-admin.page title="Guides" description="« Aider un proche » : conseils pratiques pour l'entourage.">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.guides.create')" icon="plus">Nouveau guide</x-admin.button>
    </x-slot:actions>

    <x-admin.card flush>
        @if ($guides->isEmpty())
            <x-admin.empty icon="book" title="Aucun guide" subtitle="Rédige un premier guide pour l'entourage." />
        @else
            <x-admin.table>
                <thead><tr><th>Guide</th><th class="text-end">Lecture</th><th>Publication</th><th></th></tr></thead>
                <tbody>
                    @foreach ($guides as $guide)
                        <tr>
                            <td class="max-w-lg"><p class="font-medium">{{ $guide->title }}</p><p class="truncate text-xs text-muted-foreground">{{ $guide->summary }}</p></td>
                            <td class="text-end tabular-nums">{{ $guide->readingMinutes() }} min</td>
                            <td>@if ($guide->published_at)<x-admin.badge variant="success">Publié</x-admin.badge>@else<x-admin.badge>Brouillon</x-admin.badge>@endif</td>
                            <td class="text-end"><x-admin.button :href="route('backoffice.guides.edit', $guide)" variant="outline" size="sm">Modifier</x-admin.button></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
    </x-admin.card>
</x-admin.page>
