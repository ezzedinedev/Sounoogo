<x-layouts.site title="Numéros d'urgence" description="Les numéros à appeler en cas de danger immédiat au Burkina Faso.">
    <section class="py-16 md:py-24">
        <x-site.container class="max-w-3xl">
            <p class="inline-flex items-center gap-x-2 rounded-full bg-sos-soft px-3 py-1 text-sm font-semibold text-sos-strong"><x-site.icon name="sos" :size="18" />Urgence</p>
            <h1 class="mt-5 font-display text-4xl sm:text-5xl font-semibold tracking-tight text-balance">Si toi ou quelqu'un est en danger, appelle maintenant.</h1>
            <p class="mt-5 text-lg text-muted-foreground-1 leading-relaxed">Ces services sont gratuits et joignables à toute heure. SUNOOGO ne remplace pas les secours.</p>

            <ul class="mt-10 grid gap-4">
                @foreach ($emergencyContacts as $contact)
                    <li>
                        <a href="tel:{{ $contact->phone }}" class="flex items-center justify-between gap-6 rounded-3xl bg-card border border-card-line p-6 hover:border-sos">
                            <span>
                                <span class="block text-lg font-semibold">{{ $contact->label }}</span>
                                @if ($contact->description)<span class="block mt-1 text-sm text-muted-foreground">{{ $contact->description }}</span>@endif
                            </span>
                            <span class="inline-flex items-center gap-x-2 rounded-full bg-sos px-5 py-3 font-display text-2xl font-semibold text-white"><x-site.icon name="phone-call" />{{ $contact->phone }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-12 rounded-3xl bg-background-1 border border-line-2 p-6 sm:p-8">
                <h2 class="text-lg font-semibold">Dans l'application SUNOOGO</h2>
                <p class="mt-2 text-muted-foreground-1 leading-relaxed">Le bouton <strong>SOS</strong> est toujours visible. Il affiche ces numéros et prévient immédiatement un superviseur de notre équipe. Si tu écris des mots qui montrent que tu es en danger, le superviseur est aussi alerté automatiquement.</p>
            </div>
        </x-site.container>
    </section>
</x-layouts.site>
