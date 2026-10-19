@php($editing = $professional->exists)
<x-admin.page :title="$editing ? $professional->user->name : 'Nouveau profil professionnel'" description="Ces informations apparaissent dans l'annuaire une fois le profil vérifié.">
    <form method="post" action="{{ $editing ? route('backoffice.professionals.update', $professional) : route('backoffice.professionals.store') }}" class="max-w-3xl">
        @csrf
        @if ($editing) @method('put') @endif
        <x-admin.card>
            <div class="grid gap-5">
                @unless ($editing)
                    @if ($candidates->isEmpty())
                        <div class="rounded-lg border bg-muted/50 p-4 text-sm">Aucun compte professionnel sans profil. <a class="font-medium text-primary hover:underline" href="{{ route('backoffice.users.create') }}">Crée d'abord le compte</a> avec le rôle Professionnel.</div>
                    @else
                        <x-admin.select name="user_id" label="Compte" required :value="$professional->user_id" :options="$candidates->mapWithKeys(fn ($c) => [$c->id => $c->name.' · '.$c->email])" />
                    @endif
                @endunless
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="title" label="Titre" :value="$professional->title" placeholder="Psychologue clinicienne" required />
                    <x-admin.input name="specialty" label="Spécialités" :value="$professional->specialty" placeholder="Anxiété, couple, deuil…" required />
                </div>
                <x-admin.textarea name="bio" label="Présentation" :value="$professional->bio" />
                <div class="grid gap-5 sm:grid-cols-3">
                    <x-admin.input name="city" label="Ville" :value="$professional->city" />
                    <x-admin.input name="license_number" label="N° d'agrément" :value="$professional->license_number" />
                    <x-admin.input name="languages" label="Langues" :value="implode(', ', $professional->languages ?? [])" placeholder="Français, Mooré" />
                </div>
                <div class="grid gap-5 sm:grid-cols-3">
                    <x-admin.input name="session_price" type="number" min="0" step="500" label="Tarif (F CFA)" :value="$professional->session_price" required />
                    <x-admin.input name="session_minutes" type="number" min="15" max="180" label="Durée (min)" :value="$professional->session_minutes" required />
                    <fieldset>
                        <legend class="mb-2 text-sm font-medium">Formats <span class="text-destructive">*</span></legend>
                        <div class="grid gap-2">
                            @foreach (\App\Domain\Consultation\Enums\ConsultationMode::cases() as $mode)
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="modes[]" value="{{ $mode->value }}" class="size-4 rounded border-input text-primary focus:ring-ring/40" @checked(in_array($mode->value, old('modes', $professional->modes ?? []), true))>
                                    {{ $mode->label() }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <x-admin.switch name="is_active" label="Profil actif" description="Réservable une fois vérifié." :checked="$professional->is_active ?? true" />
            </div>
            <x-slot:footer>
                <div class="ms-auto flex gap-2">
                    <x-admin.button :href="route('backoffice.professionals.index')" variant="outline">Annuler</x-admin.button>
                    <x-admin.button>Enregistrer</x-admin.button>
                </div>
            </x-slot:footer>
        </x-admin.card>
    </form>
</x-admin.page>
