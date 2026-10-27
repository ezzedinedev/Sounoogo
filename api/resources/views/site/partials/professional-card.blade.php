<article class="flex flex-col rounded-[1.25rem] bg-white p-7 ring-1 ring-line-2 shadow-ambient">
    <div class="flex items-center gap-x-4">
        <x-site.initials :name="$professional->user->name" />
        <div>
            <h3 class="text-lg font-medium">{{ $professional->user->name }}</h3>
            <p class="text-sm text-muted-foreground">{{ $professional->title }}</p>
        </div>
    </div>
    <p class="mt-5 text-[15px] text-muted-foreground-1 leading-relaxed">{{ $professional->specialty }}</p>
    <div class="mt-5 flex flex-wrap gap-2 text-xs">
        @if ($professional->city)<span class="inline-flex items-center gap-x-1 rounded-full bg-background-2 px-2.5 py-1"><x-site.icon name="map-pin" :size="14" />{{ $professional->city }}</span>@endif
        @foreach ($professional->modes ?? [] as $mode)
            <span class="rounded-full bg-primary-50 px-2.5 py-1 text-primary-800">{{ \App\Domain\Consultation\Enums\ConsultationMode::from($mode)->label() }}</span>
        @endforeach
    </div>
    <div class="mt-auto pt-7 flex items-end justify-between">
        <p><span class="text-lg font-semibold text-primary-700">Gratuit</span><span class="text-sm text-muted-foreground"> · {{ $professional->session_minutes }} min</span></p>
    </div>
</article>
