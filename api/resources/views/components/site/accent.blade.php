{{-- Mot mis en valeur en serif italique (Instrument Serif), légèrement plus grand pour s'aligner optiquement. --}}
<em {{ $attributes->merge(['class' => 'font-display italic font-normal text-[1.12em] leading-none']) }}>{{ $slot }}</em>
