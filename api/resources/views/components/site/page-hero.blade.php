{{-- En-tête des pages secondaires, dans le bloc arrondi lavande. --}}
@props(['eyebrow' => null, 'title', 'accent' => null])
<div class="mx-auto max-w-6xl px-6 sm:px-10 pt-16 pb-16 sm:pt-20 sm:pb-20">
    @if ($eyebrow)<p class="text-sm font-semibold text-primary-700">{{ $eyebrow }}</p>@endif
    <h1 class="mt-3 max-w-3xl text-5xl sm:text-6xl font-medium tracking-tight leading-[1.05] text-balance">{{ $title }}@if ($accent) <x-site.accent>{{ $accent }}</x-site.accent>@endif</h1>
    @if ($slot->isNotEmpty())<p class="mt-6 max-w-2xl text-lg leading-relaxed text-muted-foreground-1">{{ $slot }}</p>@endif
</div>
