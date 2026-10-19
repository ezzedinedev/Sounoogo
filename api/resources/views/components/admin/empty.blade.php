@props(['icon' => 'mood-smile', 'title', 'subtitle' => null])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center gap-2 px-6 py-12 text-center']) }}>
    <span class="mb-2 inline-flex size-11 items-center justify-center rounded-lg border bg-background text-muted-foreground shadow-xs"><x-icon :name="$icon" :size="20" /></span>
    <p class="font-medium">{{ $title }}</p>
    @if ($subtitle)<p class="max-w-sm text-sm text-muted-foreground">{{ $subtitle }}</p>@endif
    @if ($slot->isNotEmpty())<div class="mt-3">{{ $slot }}</div>@endif
</div>
