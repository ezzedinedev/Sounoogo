@props(['variant' => 'secondary'])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-medium whitespace-nowrap '.match ($variant) {
    'outline' => 'border text-foreground',
    'primary' => 'bg-primary-soft text-primary',
    'success' => 'bg-success-soft text-success',
    'warning' => 'bg-warning-soft text-warning',
    'destructive' => 'bg-destructive-soft text-destructive',
    default => 'bg-secondary text-secondary-foreground',
}]) }}>{{ $slot }}</span>
