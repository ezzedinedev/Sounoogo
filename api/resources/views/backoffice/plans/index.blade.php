<x-admin.page title="Offres d'abonnement" description="Les tarifs et avantages affichés sur le site et dans l'application.">
    <div class="grid gap-4 lg:grid-cols-2">
        @foreach ($plans as $plan)
            <form method="post" action="{{ route('backoffice.plans.update', $plan) }}">
                @csrf @method('put')
                <x-admin.card :title="$plan->name" :description="'Code : '.$plan->code">
                    <x-slot:actions><x-admin.badge variant="primary">{{ $plan->subscriptions_count }} abonné·es actifs</x-admin.badge></x-slot:actions>
                    <div class="grid gap-5">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <x-admin.input :id="'name-'.$plan->id" name="name" label="Nom" :value="$plan->name" required />
                            <x-admin.input :id="'price-'.$plan->id" name="price" type="number" min="0" step="500" label="Prix / mois" :value="$plan->price" required />
                            <x-admin.input :id="'discount-'.$plan->id" name="consultation_discount" type="number" min="0" max="100" label="Remise consult. %" :value="$plan->consultation_discount" required />
                        </div>
                        <x-admin.input :id="'description-'.$plan->id" name="description" label="Description" :value="$plan->description" />
                        <x-admin.textarea :id="'features-'.$plan->id" name="features" label="Avantages (un par ligne)" :value="implode("\n", $plan->features ?? [])" />
                        <x-admin.switch name="is_active" label="Proposée aux membres" :checked="$plan->is_active" />
                    </div>
                    <x-slot:footer><x-admin.button class="ms-auto">Enregistrer</x-admin.button></x-slot:footer>
                </x-admin.card>
            </form>
        @endforeach
    </div>
</x-admin.page>
