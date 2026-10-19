<div class="grid grid-cols-3 gap-4 items-center" aria-hidden="true">
    <div class="rounded-3xl bg-linear-to-b from-primary-200 to-primary-100 p-5">
        <p class="text-xs font-medium text-primary-800">Ton parcours</p>
        <p class="mt-1 text-sm font-medium">Reprendre confiance en soi</p>
        <div class="mt-4 h-2 rounded-full bg-white"><div class="h-2 w-1/2 rounded-full bg-primary-700"></div></div>
        <p class="mt-2 text-[11px] text-primary-800">3 leçons sur 6</p>
    </div>
    <div class="rounded-3xl bg-primary-900 p-5 text-white shadow-xl shadow-primary-900/20 -my-4">
        <p class="text-[11px] text-primary-200">Leçon 4 · 9 min</p>
        <p class="mt-1 text-sm font-medium">Poser ses limites</p>
        <ul class="mt-4 space-y-2 text-xs">
            @foreach (['Comprendre l\'estime de soi', 'Repérer sa voix critique', 'Lister ses réussites', 'Poser ses limites'] as $lesson)
                <li class="flex items-center gap-x-2">
                    <span @class(['inline-flex size-5 items-center justify-center rounded-full', 'bg-white text-primary-900' => $loop->index < 3, 'ring-1 ring-white/40' => $loop->index >= 3])>@if ($loop->index < 3)<x-site.icon name="check" :size="12" />@endif</span>
                    {{ $lesson }}
                </li>
            @endforeach
        </ul>
    </div>
    <div class="rounded-3xl bg-linear-to-b from-lilas-100 to-white ring-1 ring-primary-100 p-5">
        <p class="text-xs font-medium text-primary-800">Exercice du jour</p>
        <p class="mt-2 text-sm leading-snug">Note trois choses que tu as réussies cette semaine, même petites.</p>
        <span class="mt-4 block rounded-full bg-primary-900 px-3 py-2 text-center text-xs font-medium text-white">C'est fait</span>
    </div>
</div>
