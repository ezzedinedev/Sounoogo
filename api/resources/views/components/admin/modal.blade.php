{{-- Fenêtre modale (Preline overlay). Ouverture : data-hs-overlay="#{id}". --}}
@props(['id', 'title', 'description' => null])
<div id="{{ $id }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-x-hidden overflow-y-auto pointer-events-none" role="dialog" tabindex="-1" aria-labelledby="{{ $id }}-title">
    <div class="hs-overlay-open:opacity-100 hs-overlay-open:duration-200 opacity-0 transition-all ease-out sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-56px)] flex items-center">
        <div {{ $attributes->merge(['class' => 'w-full flex flex-col rounded-xl border bg-background shadow-lg pointer-events-auto']) }}>
            <div class="flex items-start justify-between gap-4 px-6 pt-6">
                <div>
                    <h3 id="{{ $id }}-title" class="text-lg font-semibold leading-none">{{ $title }}</h3>
                    @if ($description)<p class="mt-2 text-sm text-muted-foreground">{{ $description }}</p>@endif
                </div>
                <button type="button" class="inline-flex size-8 items-center justify-center rounded-md text-muted-foreground hover:bg-accent" aria-label="Fermer" data-hs-overlay="#{{ $id }}"><x-icon name="x" :size="16" /></button>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
