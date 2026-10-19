<x-layouts.site title="Formations" description="Parcours de confiance en soi et d'indépendance affective, conçus avec des professionnels.">
    <x-slot:hero>
        <x-site.page-hero eyebrow="Confiance en soi" title="Des parcours pour" accent="reprendre confiance">
            Des parcours courts, à suivre dans l'application, à ton rythme. La première leçon de chaque parcours est offerte.
        </x-site.page-hero>
    </x-slot:hero>

    <section class="py-12 md:py-16">
        <x-site.container>

            @if ($courses->isEmpty())
                <div class="mt-12 rounded-3xl border border-dashed border-line-3 p-12 text-center">
                    <p class="font-semibold">Les premières formations arrivent bientôt.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($courses as $course)
                        @include('site.partials.course-card')
                    @endforeach
                </div>
            @endif
        </x-site.container>
    </section>
</x-layouts.site>
