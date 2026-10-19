@props(['title', 'description' => null, 'actions' => null])
<x-admin.layout :title="$title">
    <div class="mx-auto max-w-7xl">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">{{ $title }}</h1>
                @if ($description)<p class="mt-1 text-sm text-muted-foreground">{{ $description }}</p>@endif
            </div>
            @if ($actions)<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endif
        </div>
        <x-admin.flash />
        {{ $slot }}
    </div>
</x-admin.layout>
