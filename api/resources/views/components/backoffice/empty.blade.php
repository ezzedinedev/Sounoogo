@props(['icon' => 'mood-empty', 'title', 'subtitle' => null])
<div class="empty">
    <div class="empty-icon"><i class="ti ti-{{ $icon }} icon"></i></div>
    <p class="empty-title">{{ $title }}</p>
    @if ($subtitle)<p class="empty-subtitle text-secondary">{{ $subtitle }}</p>@endif
    @if ($slot->isNotEmpty())<div class="empty-action">{{ $slot }}</div>@endif
</div>
