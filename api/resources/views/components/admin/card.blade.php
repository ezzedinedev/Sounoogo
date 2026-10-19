@props(['title' => null, 'description' => null, 'actions' => null, 'footer' => null, 'flush' => false])
<section {{ $attributes->merge(['class' => 'flex flex-col rounded-xl border bg-card text-card-foreground shadow-xs']) }}>
    @if ($title || $actions)
        <header class="flex items-start justify-between gap-4 px-6 pt-6">
            <div>
                @if ($title)<h2 class="font-semibold leading-none">{{ $title }}</h2>@endif
                @if ($description)<p class="mt-1.5 text-sm text-muted-foreground">{{ $description }}</p>@endif
            </div>
            @if ($actions)<div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>@endif
        </header>
    @endif
    <div @class(['flex-1', 'px-6 py-6' => ! $flush, 'pt-4' => $flush && ($title || $actions)])>{{ $slot }}</div>
    @if ($footer && trim($footer) !== '')
        <footer class="flex items-center border-t px-6 py-4 text-sm">{{ $footer }}</footer>
    @endif
</section>
