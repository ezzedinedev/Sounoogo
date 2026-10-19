<div class="grid grid-cols-3 gap-4 items-center" aria-hidden="true">
    <div class="rounded-3xl bg-linear-to-b from-primary-200 to-primary-100 p-5">
        <p class="text-center text-sm font-medium">Des pros qui te correspondent</p>
        <div class="mt-4 grid gap-2">
            @foreach (['Anxiété', 'Couple', 'Confiance'] as $tag)
                <span class="flex items-center justify-between rounded-xl bg-white px-3 py-2 text-xs">{{ $tag }}<x-site.icon name="arrow-right" :size="14" /></span>
            @endforeach
        </div>
        <span class="mt-3 block rounded-full bg-primary-900 px-3 py-2 text-center text-xs font-medium text-white">Voir les profils</span>
    </div>
    <div class="rounded-3xl bg-primary-900 p-5 text-white shadow-xl shadow-primary-900/20 -my-4">
        <div class="flex flex-col items-center">
            <span class="inline-flex size-12 items-center justify-center rounded-full bg-white/15 font-semibold">AT</span>
            <p class="mt-2 text-sm font-medium">Dr Aminata T.</p>
            <p class="text-[11px] text-primary-200">Choisis une date</p>
        </div>
        <div class="mt-3 grid grid-cols-4 gap-1.5 text-center text-[10px]">
            @foreach (['Lun 20', 'Mar 21', 'Mer 22', 'Jeu 23'] as $day)
                <span @class(['rounded-lg py-1.5', 'bg-white text-primary-950 font-semibold' => $loop->index === 1, 'bg-white/10' => $loop->index !== 1])>{{ $day }}</span>
            @endforeach
        </div>
        <span class="mt-3 block rounded-full bg-white px-3 py-2 text-center text-xs font-semibold text-primary-950">14:00</span>
        <span class="mt-2 block rounded-full ring-1 ring-white/30 px-3 py-2 text-center text-xs">16:00</span>
    </div>
    <div class="rounded-3xl bg-linear-to-b from-lilas-100 to-white ring-1 ring-primary-100 p-5 text-center">
        <span class="mx-auto inline-flex size-12 items-center justify-center rounded-full bg-white text-primary-700 shadow-sm"><x-site.icon name="circle-check" :size="24" /></span>
        <p class="mt-3 text-sm font-medium">C'est réservé !</p>
        <p class="mt-1 text-[11px] text-muted-foreground">Mardi 21 · 14:00 · Téléphone</p>
        <p class="mt-3 rounded-xl bg-white px-2 py-2 text-[11px]">Payé avec <strong>Wave</strong></p>
    </div>
</div>
