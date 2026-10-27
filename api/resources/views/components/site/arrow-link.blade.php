{{-- Lien avec flèche dans un rond, comme « Take a tour » chez Lyra. --}}
@props(['href', 'tone' => 'default'])
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group inline-flex items-center gap-x-3 font-medium']) }}>
    <span>{{ $slot }}</span>
    <span @class([
        'inline-flex size-11 items-center justify-center rounded-full transition-transform group-hover:translate-x-0.5',
        'bg-primary-50 text-primary-800' => $tone === 'default',
        'bg-white text-primary-900' => $tone === 'inverse',
    ])><x-site.icon name="arrow-right" :size="20" /></span>
</a>
