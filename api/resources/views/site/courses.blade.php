<x-layouts.site title="Formations" description="Parcours de confiance en soi et d'indépendance affective, conçus avec des professionnels.">
    <section class="py-16 md:py-20">
        <x-site.container>
            <x-site.section-header align="start" eyebrow="Confiance en soi" title="Formations">
                Des parcours courts, à suivre dans l'application, à ton rythme. La première leçon de chaque parcours est offerte.
            </x-site.section-header>

            @if ($courses->isEmpty())
                <div class="mt-12 rounded-3xl border border-dashed border-line-3 p-12 text-center">
                    <p class="font-semibold">Les premières formations arrivent bientôt.</p>
                </div>
            @else
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($courses as $course)
                        @include('site.partials.course-card')
                    @endforeach
                </div>
            @endif
        </x-site.container>
    </section>
</x-layouts.site>
