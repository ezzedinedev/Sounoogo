{{-- Carte KPI façon shadcn « section cards » : libellé, valeur, badge de tendance, pied explicatif. --}}
@props(['label', 'value', 'badge' => null, 'badgeVariant' => 'outline', 'badgeIcon' => null, 'hint' => null, 'detail' => null, 'href' => null])
<div {{ $attributes->merge(['class' => 'relative flex flex-col gap-6 rounded-xl border bg-linear-to-t from-primary-soft/60 to-card p-6 shadow-xs']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm text-muted-foreground">{{ $label }}</p>
            <p class="mt-1.5 text-3xl font-semibold tabular-nums tracking-tight">{{ $value }}</p>
        </div>
        @if ($badge)
            <x-admin.badge :variant="$badgeVariant">@if ($badgeIcon)<x-icon :name="$badgeIcon" :size="14" />@endif{{ $badge }}</x-admin.badge>
        @endif
    </div>
    @if ($hint || $detail)
        <div class="text-sm">
            @if ($hint)<p class="font-medium">{{ $hint }}</p>@endif
            @if ($detail)<p class="mt-0.5 text-muted-foreground">{{ $detail }}</p>@endif
        </div>
    @endif
    @if ($href)<a href="{{ $href }}" class="absolute inset-0 rounded-xl" aria-label="{{ $label }}"></a>@endif
</div>
