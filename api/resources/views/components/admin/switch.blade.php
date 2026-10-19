{{-- Interrupteur booléen ; le champ caché garantit l'envoi de « 0 » quand il est décoché. --}}
@props(['name', 'label', 'checked' => false, 'description' => null])
<label class="flex cursor-pointer items-start gap-3">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $checked)) class="peer sr-only">
    <span class="relative mt-0.5 inline-flex h-5 w-9 shrink-0 rounded-full bg-input transition-colors peer-checked:bg-primary peer-focus-visible:ring-[3px] peer-focus-visible:ring-ring/40 after:absolute after:start-0.5 after:top-0.5 after:size-4 after:rounded-full after:bg-white after:shadow-sm after:transition-transform peer-checked:after:translate-x-4"></span>
    <span class="text-sm">
        <span class="font-medium">{{ $label }}</span>
        @if ($description)<span class="block text-muted-foreground">{{ $description }}</span>@endif
    </span>
</label>
