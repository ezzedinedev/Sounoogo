<x-admin.page title="Ressources de proximité" description="Annuaire vérifié par l'équipe, affiché dans l'application avec une carte.">
    <x-slot:actions>
        <x-admin.button :href="route('backoffice.resources.create')" icon="plus">Ajouter une ressource</x-admin.button>
    </x-slot:actions>

    @if ($cities->isNotEmpty())
        <div class="mb-4 flex flex-wrap gap-2 text-sm">
            <a href="{{ route('backoffice.resources.index') }}" @class(['rounded-full border px-3 py-1', 'bg-primary text-primary-foreground border-primary' => ! request('ville')])>Toutes</a>
            @foreach ($cities as $city)
                <a href="{{ route('backoffice.resources.index', ['ville' => $city]) }}" @class(['rounded-full border px-3 py-1', 'bg-primary text-primary-foreground border-primary' => request('ville') === $city])>{{ $city }}</a>
            @endforeach
        </div>
    @endif

    <x-admin.card flush>
        @if ($resources->isEmpty())
            <x-admin.empty icon="map-pin" title="Annuaire vide" subtitle="Ajoute les centres de santé, associations et services que l'équipe a vérifiés." />
        @else
            <x-admin.table>
                <thead><tr><th>Ressource</th><th>Catégorie</th><th>Ville</th><th>Téléphone</th><th>Carte</th><th>État</th><th></th></tr></thead>
                <tbody>
                    @foreach ($resources as $resource)
                        <tr>
                            <td class="max-w-md"><p class="font-medium">{{ $resource->name }}</p>@if ($resource->address)<p class="truncate text-xs text-muted-foreground">{{ $resource->address }}</p>@endif</td>
                            <td>{{ $resource->category->label() }}@if ($resource->is_free)<x-admin.badge variant="success" class="ms-1">Gratuit</x-admin.badge>@endif</td>
                            <td>{{ $resource->city }}</td>
                            <td class="tabular-nums">{{ $resource->phone ?? '—' }}</td>
                            <td>@if ($resource->latitude !== null)<x-icon name="map-pin" :size="16" class="text-primary" />@else<span class="text-muted-foreground">—</span>@endif</td>
                            <td>@if ($resource->is_active)<x-admin.badge variant="success">Visible</x-admin.badge>@else<x-admin.badge>Masquée</x-admin.badge>@endif</td>
                            <td class="text-end"><x-admin.button :href="route('backoffice.resources.edit', $resource)" variant="outline" size="sm">Modifier</x-admin.button></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        @endif
        <x-slot:footer>@if ($resources->hasPages()){{ $resources->links() }}@endif</x-slot:footer>
    </x-admin.card>
</x-admin.page>
