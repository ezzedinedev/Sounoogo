{{-- Badge d'état unique pour tous les enums métier. --}}
@props(['value'])
@php
    $variant = match ($value->value) {
        'active', 'confirmed', 'succeeded', 'resolved', 'paid' => 'success',
        'waiting', 'pending', 'pending_payment', 'acknowledged', 'medium', 'requested' => 'warning',
        'open', 'failed', 'high', 'rejected' => 'destructive',
        'cancelled', 'closed', 'dismissed', 'expired', 'completed', 'none' => 'secondary',
        default => 'primary',
    };
@endphp
<x-admin.badge :variant="$variant" {{ $attributes }}>
    <span @class(['size-1.5 rounded-full bg-current', 'opacity-60' => $variant === 'secondary'])></span>{{ $value->label() }}
</x-admin.badge>
