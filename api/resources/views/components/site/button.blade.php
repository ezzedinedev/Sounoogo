@props(['variant' => 'primary', 'href' => null, 'size' => 'md', 'icon' => null])
@php
    $base = 'inline-flex items-center justify-center gap-x-2 font-semibold rounded-full transition-colors duration-150 focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none';
    $sizes = [
        'md' => 'py-3 px-5 text-sm',
        'lg' => 'py-3.5 px-6 text-base',
        'xl' => 'py-4 px-7 text-base',
    ];
    $variants = [
        'primary' => 'bg-primary text-primary-foreground hover:bg-primary-hover focus:bg-primary-focus',
        'secondary' => 'bg-layer border border-layer-line text-layer-foreground hover:bg-layer-hover focus:bg-layer-focus',
        'accent' => 'bg-accent text-accent-foreground hover:bg-accent-hover focus:bg-accent-hover',
        'sos' => 'bg-sos text-white hover:bg-sos-hover focus:bg-sos-hover',
        'ghost' => 'text-foreground hover:bg-secondary focus:bg-secondary',
        'white' => 'bg-white text-primary-800 shadow-lg shadow-primary-950/20 hover:bg-primary-50 focus:bg-primary-50',
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
