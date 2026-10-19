{{-- Icônes Tabler (MIT) en SVG inline, générées par scripts/build-icons.mjs. --}}
@props(['name', 'size' => 20])
@php($paths = (require resource_path('icons/tabler.php'))[$name] ?? throw new InvalidArgumentException("Icône inconnue : {$name}"))
<svg {{ $attributes->merge(['class' => 'shrink-0']) }} xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths !!}</svg>
