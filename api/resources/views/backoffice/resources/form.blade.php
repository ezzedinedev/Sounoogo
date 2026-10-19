@php($editing = $resource->exists)
<x-admin.page :title="$editing ? $resource->name : 'Nouvelle ressource'" description="Ajoute uniquement des structures que l'équipe a vérifiées.">
    @if ($editing)
        <x-slot:actions>
            <form method="post" action="{{ route('backoffice.resources.destroy', $resource) }}" onsubmit="return confirm('Supprimer cette ressource ?')">
                @csrf @method('delete')
                <x-admin.button variant="outline" icon="trash" class="text-destructive">Supprimer</x-admin.button>
            </form>
        </x-slot:actions>
    @endif

    <form method="post" action="{{ $editing ? route('backoffice.resources.update', $resource) : route('backoffice.resources.store') }}" class="grid gap-4 lg:grid-cols-2">
        @csrf
        @if ($editing) @method('put') @endif
        <x-admin.card title="Structure">
            <div class="grid gap-5">
                <x-admin.input name="name" label="Nom" :value="$resource->name" required />
                <div class="grid grid-cols-2 gap-4">
                    <x-admin.select name="category" label="Catégorie" :options="collect(\App\Domain\Content\Enums\ResourceCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all()" :value="$resource->category?->value" required />
                    <x-admin.input name="city" label="Ville" :value="$resource->city" required placeholder="Ouagadougou" />
                </div>
                <x-admin.textarea name="description" label="Description" :value="$resource->description" rows="3" maxlength="500" hint="Ce que la structure propose, à qui elle s'adresse." />
                <x-admin.input name="opening_hours" label="Horaires" :value="$resource->opening_hours" placeholder="Lun–ven, 8 h–16 h" />
                <x-admin.switch name="is_free" label="Gratuit" :checked="$resource->is_free" />
                <x-admin.switch name="is_active" label="Visible dans l'application" :checked="$resource->is_active" />
            </div>
        </x-admin.card>
        <x-admin.card title="Contact et localisation">
            <div class="grid gap-5">
                <x-admin.input name="address" label="Adresse" :value="$resource->address" />
                <div class="grid grid-cols-2 gap-4">
                    <x-admin.input name="phone" label="Téléphone" :value="$resource->phone" />
                    <x-admin.input name="website" type="url" label="Site web" :value="$resource->website" placeholder="https://" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-admin.input name="latitude" type="number" step="0.000001" label="Latitude" :value="$resource->latitude" placeholder="12.371428" />
                    <x-admin.input name="longitude" type="number" step="0.000001" label="Longitude" :value="$resource->longitude" placeholder="-1.519660" />
                </div>
                <p class="text-xs text-muted-foreground">Coordonnées facultatives : sans elles, la ressource apparaît dans la liste mais pas sur la carte.</p>
            </div>
            <x-slot:footer><x-admin.button class="ms-auto">Enregistrer</x-admin.button></x-slot:footer>
        </x-admin.card>
    </form>
</x-admin.page>
