<div class="grid grid-cols-3 gap-4 items-center" aria-hidden="true">
    <div class="rounded-3xl bg-linear-to-b from-primary-200 to-primary-100 p-5">
        <p class="text-center text-xs font-medium text-primary-800">Pour commencer</p>
        <p class="mt-1 text-center text-sm font-medium">De quoi veux-tu parler ?</p>
        <div class="mt-4 grid gap-2">
            @foreach (['Stress', 'Tristesse', 'Relation', 'Autre chose'] as $topic)
                <span @class(['rounded-xl px-3 py-2 text-xs font-medium', 'bg-primary-900 text-white' => $loop->index === 2, 'bg-white text-foreground' => $loop->index !== 2])>{{ $topic }}</span>
            @endforeach
        </div>
    </div>
    <div class="rounded-3xl bg-primary-900 p-5 text-white shadow-xl shadow-primary-900/20 -my-4">
        <div class="flex items-center justify-between">
            <span class="text-xs text-primary-200">Écoute · anonyme</span>
            <span class="rounded-full bg-sos px-2 py-0.5 text-[10px] font-bold">SOS</span>
        </div>
        <div class="mt-4 space-y-2 text-xs leading-snug">
            <p class="w-5/6 rounded-2xl rounded-ss-sm bg-white/10 px-3 py-2">Bonsoir, je suis là. Prends ton temps.</p>
            <p class="ms-auto w-5/6 rounded-2xl rounded-se-sm bg-white px-3 py-2 text-primary-950">Je n'arrive plus à dormir…</p>
            <p class="w-5/6 rounded-2xl rounded-ss-sm bg-white/10 px-3 py-2">Merci de me le confier. Depuis quand ?</p>
        </div>
        <div class="mt-4 rounded-full bg-white/10 px-3 py-2 text-[11px] text-primary-200">Écris ce que tu ressens…</div>
    </div>
    <div class="rounded-3xl bg-linear-to-b from-lilas-100 to-white ring-1 ring-primary-100 p-5 text-center">
        <span class="mx-auto inline-flex size-12 items-center justify-center rounded-full bg-white text-primary-700 shadow-sm"><x-icon name="lock" :size="22" /></span>
        <p class="mt-3 text-sm font-medium">Ton code de récupération</p>
        <p class="mt-2 rounded-xl bg-white px-2 py-2 font-mono text-xs tracking-wider">K7QP-2M9X-R4TA</p>
        <p class="mt-2 text-[11px] text-muted-foreground">Toi seul·e le connais.</p>
    </div>
</div>
