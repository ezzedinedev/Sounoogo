{{-- Maquette de l'application : montre le produit plutôt que de le décrire. --}}
<div {{ $attributes->merge(['class' => 'relative mx-auto w-72 sm:w-80 text-foreground']) }} aria-hidden="true">
    <div class="rounded-[2.75rem] bg-primary-950 p-2.5 shadow-2xl shadow-primary-950/50 ring-1 ring-white/10">
        <div class="overflow-hidden rounded-[2.25rem] bg-background">
            <div class="flex items-center justify-between px-5 pt-4 pb-3 bg-white border-b border-line-2">
                <div class="flex items-center gap-x-2">
                    <img src="{{ asset('images/brand/logo-badge.svg') }}" alt="" class="size-7">
                    <div>
                        <p class="text-sm font-semibold leading-tight">Écoute</p>
                        <p class="flex items-center gap-x-1 text-[11px] text-muted-foreground"><span class="size-1.5 rounded-full bg-online"></span>Mariam est là pour toi</p>
                    </div>
                </div>
                <span class="rounded-full bg-sos px-2.5 py-1 text-[11px] font-bold text-white">SOS</span>
            </div>
            <div class="space-y-3 px-4 py-5 h-96 bg-primary-50/60">
                <p class="text-center text-[11px] text-muted-foreground">Conversation anonyme · chiffrée</p>
                <p class="max-w-[85%] rounded-2xl rounded-ss-md bg-white px-3.5 py-2.5 text-[13px] leading-snug shadow-sm">Bonsoir, je suis Mariam. Prends ton temps, je t'écoute.</p>
                <p class="ms-auto max-w-[85%] rounded-2xl rounded-se-md bg-primary px-3.5 py-2.5 text-[13px] leading-snug text-primary-foreground">Je n'arrive plus à dormir… j'ai l'impression de porter tout ça seul.</p>
                <p class="max-w-[85%] rounded-2xl rounded-ss-md bg-white px-3.5 py-2.5 text-[13px] leading-snug shadow-sm">Merci de me le confier. Qu'est-ce qui te pèse le plus ce soir ?</p>
                <div class="flex items-center gap-x-1 ps-2">
                    <span class="size-1.5 rounded-full bg-primary-300 animate-pulse"></span>
                    <span class="size-1.5 rounded-full bg-primary-300 animate-pulse [animation-delay:150ms]"></span>
                    <span class="size-1.5 rounded-full bg-primary-300 animate-pulse [animation-delay:300ms]"></span>
                </div>
            </div>
            <div class="flex items-center gap-x-2 border-t border-line-2 bg-white px-4 py-3">
                <span class="flex-1 rounded-full bg-background-2 px-4 py-2 text-[12px] text-muted-foreground">Écris ce que tu ressens…</span>
                <span class="inline-flex size-8 items-center justify-center rounded-full bg-primary text-white"><x-icon name="arrow-up-right" :size="16" /></span>
            </div>
        </div>
    </div>
</div>
