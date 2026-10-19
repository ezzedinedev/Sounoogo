@props(['amount'])
<span {{ $attributes->merge(['class' => 'tabular-nums whitespace-nowrap']) }}>{{ number_format($amount, 0, ',', ' ') }} F CFA</span>
