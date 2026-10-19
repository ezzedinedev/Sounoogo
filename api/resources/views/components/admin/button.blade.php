@props(['variant' => 'default', 'size' => 'default', 'href' => null, 'icon' => null])
@php
    $classes = 'inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:opacity-50 '
        .match ($size) {
            'sm' => 'h-8 px-3 text-[13px]',
            'icon' => 'size-9',
            'icon-sm' => 'size-8',
            default => 'h-9 px-4',
        }.' '
        .match ($variant) {
            'outline' => 'border bg-background shadow-xs hover:bg-accent',
            'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
            'ghost' => 'hover:bg-accent',
            'destructive' => 'bg-destructive text-white shadow-xs hover:bg-destructive/90',
            'success' => 'bg-success text-white shadow-xs hover:bg-success/90',
            'link' => 'text-primary underline-offset-4 hover:underline',
            default => 'bg-primary text-primary-foreground shadow-xs hover:bg-primary/90',
        };
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>@if ($icon)<x-icon :name="$icon" :size="16" />@endif{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>@if ($icon)<x-icon :name="$icon" :size="16" />@endif{{ $slot }}</button>
@endif
