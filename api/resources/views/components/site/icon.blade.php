{{-- Icônes Material Symbols Outlined, identiques à la référence MindCorps. --}}
@props(['name', 'size' => 20])
@php($symbols = [
    'alert-triangle' => 'error_outline',
    'arrow-right' => 'arrow_forward',
    'arrow-up-right' => 'arrow_outward',
    'book' => 'school',
    'brand-android' => 'smartphone',
    'brand-apple' => 'phone',
    'check' => 'check',
    'circle-check' => 'check_circle',
    'eye-off' => 'lock_outline',
    'heart-handshake' => 'medical_services',
    'lifebuoy' => 'support_agent',
    'lock' => 'lock',
    'map-pin' => 'location_on',
    'menu-2' => 'menu',
    'phone-call' => 'call',
    'shield-check' => 'verified_user',
    'sparkles' => 'auto_awesome',
    'user-heart' => 'self_improvement',
    'x' => 'close',
])
<span {{ $attributes->merge(['class' => 'material-symbols-outlined shrink-0', 'style' => "font-size: {$size}px"]) }} aria-hidden="true">{{ $symbols[$name] ?? throw new InvalidArgumentException("Icône Material inconnue : {$name}") }}</span>
