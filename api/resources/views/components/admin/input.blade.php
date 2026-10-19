{{-- Champ de formulaire complet : libellé, saisie, aide et erreur de validation. --}}
@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false, 'id' => null])
@php($id ??= str_replace(['[', ']', '.'], '-', $name))
<div>
    @if ($label)<x-admin.label :for="$id" :required="$required">{{ $label }}</x-admin.label>@endif
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" @required($required)
        {{ $attributes->class([
            'flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/40',
            'border-destructive ring-destructive/20' => $errors->has($name),
        ]) }}>
    @error($name)<p class="mt-1.5 text-[13px] text-destructive">{{ $message }}</p>@enderror
    @if ($hint && ! $errors->has($name))<p class="mt-1.5 text-[13px] text-muted-foreground">{{ $hint }}</p>@endif
</div>
