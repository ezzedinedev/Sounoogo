@php($editing = $guide->exists)
<x-admin.page :title="$editing ? $guide->title : 'Nouveau guide'" description="Texte en Markdown : ## pour un intertitre, - pour une liste, **gras**.">
    @if ($editing)
        <x-slot:actions>
            <form method="post" action="{{ route('backoffice.guides.destroy', $guide) }}" onsubmit="return confirm('Supprimer ce guide ?')">
                @csrf @method('delete')
                <x-admin.button variant="outline" icon="trash" class="text-destructive">Supprimer</x-admin.button>
            </form>
        </x-slot:actions>
    @endif

    <form method="post" action="{{ $editing ? route('backoffice.guides.update', $guide) : route('backoffice.guides.store') }}" class="grid gap-4 lg:grid-cols-5">
        @csrf
        @if ($editing) @method('put') @endif
        <x-admin.card title="Informations" class="lg:col-span-2">
            <div class="grid gap-5">
                <x-admin.input name="title" label="Titre" :value="$guide->title" required />
                <x-admin.input name="slug" label="Adresse (slug)" :value="$guide->slug" placeholder="générée depuis le titre" />
                <x-admin.textarea name="summary" label="Résumé" :value="$guide->summary" rows="3" maxlength="300" required />
                <x-admin.input name="position" type="number" min="0" label="Ordre" :value="$guide->position" />
                <x-admin.switch name="published" label="Publié" description="Visible dans l'application." :checked="$guide->published_at !== null" />
            </div>
            <x-slot:footer><x-admin.button class="ms-auto">Enregistrer</x-admin.button></x-slot:footer>
        </x-admin.card>
        <x-admin.card title="Contenu" class="lg:col-span-3">
            <x-admin.textarea name="body" label="Texte" :value="$guide->body" rows="24" required class="font-mono text-[13px]" />
        </x-admin.card>
    </form>
</x-admin.page>
