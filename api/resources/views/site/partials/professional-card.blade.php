<article class="flex flex-col rounded-3xl bg-card border border-card-line p-6">
    <div class="flex items-center gap-x-4">
        <x-site.initials :name="$professional->user->name" />
        <div>
            <h3 class="font-semibold">{{ $professional->user->name }}</h3>
            <p class="text-sm text-muted-foreground">{{ $professional->title }}</p>
        </div>
    </div>
    <p class="mt-4 text-sm text-muted-foreground-1 leading-relaxed">{{ $professional->specialty }}</p>
    <dl class="mt-5 space-y-2 text-sm flex-1">
        @if ($professional->city)
            <div class="flex items-center gap-x-2"><dt><x-site.icon name="map-pin" :size="16" class="text-muted-foreground" /><span class="sr-only">Ville</span></dt><dd>{{ $professional->city }}</dd></div>
        @endif
        <div class="flex items-center gap-x-2"><dt><x-site.icon name="clock" :size="16" class="text-muted-foreground" /><span class="sr-only">Durée</span></dt><dd>{{ $professional->session_minutes }} min · {{ collect($professional->modes)->map(fn ($mode) => \App\Domain\Consultation\Enums\ConsultationMode::from($mode)->label())->join(', ', ' ou ') }}</dd></div>
    </dl>
    <div class="mt-6 pt-5 border-t border-card-divider flex items-center justify-between">
        <x-site.price :amount="$professional->session_price" class="font-semibold" />
        <span class="text-xs text-muted-foreground">par séance</span>
    </div>
</article>
