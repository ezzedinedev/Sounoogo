@props(['for' => null, 'required' => false])
<label @if ($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'mb-2 block text-sm font-medium leading-none']) }}>{{ $slot }}@if ($required)<span class="text-destructive"> *</span>@endif</label>
