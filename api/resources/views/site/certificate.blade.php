<x-layouts.site noindex title="Vérification d'attestation" description="Vérifier l'authenticité d'une attestation de formation SUNOOGO.">
    <section class="py-24">
        <x-site.container class="max-w-xl text-center">
            <span class="inline-flex size-16 items-center justify-center rounded-full bg-primary-50 text-primary-700">
                <x-icon name="circle-check" :size="32" />
            </span>
            <h1 class="mt-6 text-3xl font-medium tracking-tight">Attestation authentique</h1>
            <p class="mt-4 leading-relaxed text-muted-foreground-1">
                Cette attestation a bien été délivrée par SUNOOGO à
                <strong class="text-foreground">{{ $certificate->holder_name }}</strong>
                pour avoir suivi l'intégralité de la formation
                <strong class="text-foreground">« {{ $certificate->course->title }} »</strong>.
            </p>
            <dl class="mx-auto mt-8 grid max-w-sm grid-cols-2 gap-4 rounded-2xl border p-5 text-start text-sm">
                <div><dt class="text-muted-foreground">Délivrée le</dt><dd class="mt-1 font-medium">{{ $certificate->issued_at->translatedFormat('j F Y') }}</dd></div>
                <div><dt class="text-muted-foreground">Code</dt><dd class="mt-1 font-mono font-medium">{{ $certificate->code }}</dd></div>
            </dl>
            <p class="mt-6 text-xs text-muted-foreground">Une attestation SUNOOGO reconnaît le suivi d'un parcours d'autonomie. Ce n'est ni un diplôme ni une certification professionnelle.</p>
        </x-site.container>
    </section>
</x-layouts.site>
