<x-admin.page title="Numéros d'urgence" description="Affichés sur le site, dans l'application et lors de chaque alerte. Vérifie-les régulièrement auprès des autorités locales.">
    <x-admin.card flush>
        <x-admin.table>
            <thead><tr><th>Pays</th><th>Service</th><th>Numéro</th><th>Description</th><th>Ordre</th><th>Actif</th><th></th></tr></thead>
            <tbody>
                @foreach ($contacts as $contact)
                    @php($form = 'contact-'.$contact->id)
                    <tr>
                        <td class="w-20"><input form="{{ $form }}" name="country" value="{{ $contact->country }}" maxlength="2" required aria-label="Pays" class="h-8 w-14 rounded-md border border-input bg-background px-2 text-sm uppercase shadow-xs"></td>
                        <td><input form="{{ $form }}" name="label" value="{{ $contact->label }}" required aria-label="Service" class="h-8 w-full min-w-40 rounded-md border border-input bg-background px-2 text-sm shadow-xs"></td>
                        <td class="w-28"><input form="{{ $form }}" name="phone" value="{{ $contact->phone }}" required aria-label="Numéro" class="h-8 w-24 rounded-md border border-input bg-background px-2 text-sm font-medium tabular-nums shadow-xs"></td>
                        <td><input form="{{ $form }}" name="description" value="{{ $contact->description }}" aria-label="Description" class="h-8 w-full min-w-48 rounded-md border border-input bg-background px-2 text-sm shadow-xs"></td>
                        <td class="w-20"><input form="{{ $form }}" type="number" name="position" value="{{ $contact->position }}" min="0" required aria-label="Ordre" class="h-8 w-16 rounded-md border border-input bg-background px-2 text-sm shadow-xs"></td>
                        <td>
                            <input form="{{ $form }}" type="hidden" name="is_active" value="0">
                            <input form="{{ $form }}" type="checkbox" name="is_active" value="1" @checked($contact->is_active) aria-label="Actif" class="size-4 rounded border-input text-primary">
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <form method="post" action="{{ route('backoffice.emergency-contacts.update', $contact) }}" id="{{ $form }}">@csrf @method('put')<x-admin.button size="sm" variant="outline">Enregistrer</x-admin.button></form>
                                <form method="post" action="{{ route('backoffice.emergency-contacts.destroy', $contact) }}" onsubmit="return confirm('Supprimer ce numéro ?')">
                                    @csrf @method('delete')
                                    <x-admin.button variant="ghost" size="icon-sm" aria-label="Supprimer"><x-icon name="trash" :size="16" class="text-muted-foreground" /></x-admin.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table>
        <x-slot:footer>
            <form method="post" action="{{ route('backoffice.emergency-contacts.store') }}" class="grid w-full gap-3 sm:grid-cols-12 sm:items-end">
                @csrf
                <input type="hidden" name="is_active" value="1">
                <x-admin.input id="new-country" name="country" label="Pays" value="BF" maxlength="2" required class="uppercase" />
                <div class="sm:col-span-3"><x-admin.input id="new-label" name="label" label="Service" required /></div>
                <div class="sm:col-span-2"><x-admin.input id="new-phone" name="phone" label="Numéro" required /></div>
                <div class="sm:col-span-3"><x-admin.input id="new-description" name="description" label="Description" /></div>
                <x-admin.input id="new-position" name="position" type="number" label="Ordre" :value="$contacts->count()" min="0" required />
                <div class="sm:col-span-2"><x-admin.button icon="plus" class="w-full">Ajouter</x-admin.button></div>
            </form>
        </x-slot:footer>
    </x-admin.card>
</x-admin.page>
