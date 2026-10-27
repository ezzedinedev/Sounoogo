{{-- Titres de section en Outfit, alignés sur les niveaux de titres MindCorps. --}}
@props(['eyebrow' => null, 'title', 'accent' => null, 'align' => 'center', 'tone' => 'default'])
<div {{ $attributes->merge(['class' => $align === 'center' ? 'max-w-3xl mx-auto text-center' : 'max-w-2xl']) }}>
    @if ($eyebrow)
        <p @class(['text-sm font-semibold tracking-wide', 'text-primary-600' => $tone === 'default', 'text-lilas-200' => $tone === 'inverse'])>{{ $eyebrow }}</p>
    @endif
    <h2 @class(['mt-3 text-[2rem] sm:text-[3rem] font-semibold tracking-[-0.03em] text-balance leading-[1.16]', 'text-foreground' => $tone === 'default', 'text-white' => $tone === 'inverse'])>
        {{ $title }}@if ($accent) <x-site.accent>{{ $accent }}</x-site.accent>@endif
    </h2>
    @if ($slot->isNotEmpty())
        <p @class(['mt-5 text-lg leading-relaxed text-pretty', 'text-muted-foreground-1' => $tone === 'default', 'text-primary-100' => $tone === 'inverse'])>{{ $slot }}</p>
    @endif
</div>
