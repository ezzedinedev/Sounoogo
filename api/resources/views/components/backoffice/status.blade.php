{{-- Badge d'état unique pour tous les enums métier. --}}
@props(['value'])
@php
    $tone = match ($value->value) {
        'active', 'confirmed', 'succeeded', 'resolved' => 'green',
        'waiting', 'pending', 'pending_payment', 'acknowledged' => 'yellow',
        'open', 'failed', 'high' => 'red',
        'medium' => 'orange',
        'cancelled', 'closed', 'dismissed', 'expired', 'completed', 'none' => 'secondary',
        default => 'blue',
    };
@endphp
<span {{ $attributes->merge(['class' => "badge bg-{$tone}-lt"]) }}>{{ $value->label() }}</span>
