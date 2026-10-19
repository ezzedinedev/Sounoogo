@props(['amount'])
<span {{ $attributes }}>{{ $amount === 0 ? 'Gratuit' : number_format($amount, 0, ',', ' ').' F CFA' }}</span>
