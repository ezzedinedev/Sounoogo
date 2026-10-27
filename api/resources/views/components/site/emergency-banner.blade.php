@props(['contacts'])
{{-- Bandeau d'urgence : toujours accessible, sans détour. --}}
<div class="bg-sos-soft border-b border-sos-line">
    <x-site.container class="py-2 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-sos-strong">
        <span class="inline-flex items-center gap-x-1.5 font-semibold">
<x-site.icon name="alert-triangle" :size="16" />
            En danger immédiat ?
        </span>
        @foreach ($contacts as $contact)
            <a href="tel:{{ $contact->phone }}" class="underline underline-offset-4 decoration-sos-line hover:decoration-sos-strong">
                {{ $contact->label }} <strong>{{ $contact->phone }}</strong>
            </a>
        @endforeach
    </x-site.container>
</div>
