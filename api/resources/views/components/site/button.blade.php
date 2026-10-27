@props(['variant' => 'primary', 'href' => null, 'size' => 'md', 'icon' => null])
@php
    $base = 'inline-flex items-center justify-center gap-x-2 font-medium rounded-full transition-all duration-200 ease-out focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none';
    $sizes = [
        'md' => 'py-3 px-6 text-[15px]',
        'lg' => 'py-3.5 px-6 text-base',
        'xl' => 'py-4 px-7 text-base',
    ];
    $variants = [
        'primary' => 'bg-linear-to-br from-[#602fb8] to-[#4c2494] text-white shadow-[0_8px_24px_rgba(96,47,184,0.28)] hover:-translate-y-0.5 hover:brightness-105 hover:shadow-[0_12px_32px_rgba(96,47,184,0.36)] focus-visible:ring-4 focus-visible:ring-[#602fb8]/15',
        'secondary' => 'bg-layer border border-layer-line text-layer-foreground hover:bg-layer-hover focus:bg-layer-focus',
        'accent' => 'bg-accent text-accent-foreground hover:bg-accent-hover focus:bg-accent-hover',
        'sos' => 'bg-sos text-white hover:bg-sos-hover focus:bg-sos-hover',
        'ghost' => 'text-foreground hover:bg-secondary focus:bg-secondary',
        'white' => 'border border-[#24123f]/10 bg-white text-[#24123f] shadow-sm hover:bg-[#faf6ff] focus:bg-[#faf6ff]',
        'outline-white' => 'border border-white/30 text-white hover:bg-white/10 focus:bg-white/10',
    ];
    $classes = "{$base} {$sizes[$size]} {$variants[$variant]}";
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
        @if ($icon)<x-site.icon :name="$icon" :size="18" />@endif
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
        {{ $slot }}
        @if ($icon)<x-site.icon :name="$icon" :size="18" />@endif
    </button>
@endif
