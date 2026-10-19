@props(['name', 'size' => 'size-14'])
@php($initials = collect(explode(' ', preg_replace('/^(Dr|Pr)\.?\s+/u', '', $name)))->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode(''))
<span {{ $attributes->merge(['class' => "{$size} inline-flex items-center justify-center rounded-full bg-primary-100 text-primary-800 font-semibold"]) }} aria-hidden="true">{{ $initials }}</span>
