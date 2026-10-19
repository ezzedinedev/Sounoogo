@props(['amount'])
<span {{ $attributes->merge(['class' => 'text-nowrap']) }}>{{ number_format($amount, 0, ',', ' ') }} F CFA</span>
