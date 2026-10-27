<x-layouts.site title="Professionnels" description="Psychologues, conseillers et coachs vérifiés, en cabinet, par téléphone ou en visio.">
    <x-slot:hero>
        <x-site.page-hero eyebrow="Consultations" title="Trouver un professionnel" accent="qui te correspond">
            Tous les professionnels sont vérifiés par notre équipe. La réservation se fait dans l'application, et tu peux rester anonyme.
        </x-site.page-hero>
    </x-slot:hero>

    <section class="py-12 md:py-16">
        <x-site.container>

            <form method="get" class="flex flex-col sm:flex-row gap-3" role="search">
                <label class="sr-only" for="specialty">Spécialité</label>
                <input id="specialty" name="specialite" value="{{ request('specialite') }}" placeholder="Anxiété, couple, confiance…" class="py-3 px-4 block w-full sm:max-w-sm rounded-full border border-layer-line bg-layer text-foreground placeholder:text-muted-foreground focus:border-primary focus:ring-primary">
                <label class="sr-only" for="mode">Format</label>
                <select id="mode" name="format" class="py-3 px-4 pe-9 block rounded-full border border-layer-line bg-layer text-foreground focus:border-primary focus:ring-primary">
                    <option value="">Tous les formats</option>
                    @foreach (\App\Domain\Consultation\Enums\ConsultationMode::cases() as $mode)
                        <option value="{{ $mode->value }}" @selected(request('format') === $mode->value)>{{ $mode->label() }}</option>
                    @endforeach
                </select>
                <x-site.button type="submit">Rechercher</x-site.button>
            </form>

            @if ($professionals->isEmpty())
                <div class="mt-12 rounded-3xl border border-dashed border-line-3 p-12 text-center">
                    <p class="font-semibold">Aucun professionnel ne correspond à ta recherche.</p>
                    <p class="mt-1 text-muted-foreground">Essaie une autre spécialité ou un autre format.</p>
                    <x-site.button :href="route('professionals')" variant="secondary" class="mt-6">Réinitialiser</x-site.button>
                </div>
            @else
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($professionals as $professional)
                        @include('site.partials.professional-card')
                    @endforeach
                </div>
                <div class="mt-10">{{ $professionals->links() }}</div>
            @endif
        </x-site.container>
    </section>
</x-layouts.site>
