@props(['eyebrow' => null, 'title', 'align' => 'center'])
<div {{ $attributes->merge(['class' => $align === 'center' ? 'max-w-2xl mx-auto text-center' : 'max-w-2xl']) }}>
    @if ($eyebrow)
        <p class="text-sm font-semibold tracking-wide uppercase text-accent">{{ $eyebrow }}</p>
    @endif
    <h2 class="mt-3 font-display text-3xl sm:text-4xl font-semibold tracking-tight text-foreground text-balance">{{ $title }}</h2>
    @if ($slot->isNotEmpty())
        <p class="mt-4 text-lg leading-relaxed text-muted-foreground-1 text-pretty">{{ $slot }}</p>
    @endif
</div>
