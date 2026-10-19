<x-admin.page title="Nouveau compte équipe" description="Écoutants, superviseurs, professionnels et administrateurs.">
    <form method="post" action="{{ route('backoffice.users.store') }}" class="max-w-2xl">
        @csrf
        <x-admin.card>
            <div class="grid gap-5">
                <x-admin.input name="name" label="Nom complet" required />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-admin.input name="email" type="email" label="E-mail" required />
                    <x-admin.input name="phone" label="Téléphone" placeholder="+226…" />
                </div>
                <fieldset>
                    <legend class="mb-2 text-sm font-medium">Rôle <span class="text-destructive">*</span></legend>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($roles as $role)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 text-sm has-checked:border-primary has-checked:bg-primary-soft">
                                <input type="radio" name="role" value="{{ $role->value }}" class="size-4 border-input text-primary focus:ring-ring/40" @checked(old('role', 'listener') === $role->value)>
                                <span class="font-medium">{{ $role->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <x-admin.input name="password" label="Mot de passe provisoire" minlength="10" required autocomplete="new-password" hint="10 caractères minimum. À transmettre par un canal sûr et à changer à la première connexion." />
            </div>
            <x-slot:footer>
                <div class="ms-auto flex gap-2">
                    <x-admin.button :href="route('backoffice.users.index')" variant="outline">Annuler</x-admin.button>
                    <x-admin.button>Créer le compte</x-admin.button>
                </div>
            </x-slot:footer>
        </x-admin.card>
    </form>
</x-admin.page>
