<x-admin.page title="Supervision" description="File d'attente, écoutes en cours et alertes, en temps réel.">
    <x-slot:actions>
        <span class="inline-flex items-center gap-2 text-sm text-muted-foreground">
            <span class="relative flex size-2.5"><span class="absolute inline-flex size-full animate-ping rounded-full bg-success opacity-60 motion-reduce:hidden"></span><span class="relative inline-flex size-2.5 rounded-full bg-success"></span></span>
            En direct
        </span>
    </x-slot:actions>

    <div data-supervision="{{ route('backoffice.supervision.live') }}" aria-live="polite">
        @include('backoffice.supervision.partials.live')
    </div>
</x-admin.page>
