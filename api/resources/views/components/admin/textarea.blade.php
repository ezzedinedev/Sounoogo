@props(['name', 'label' => null, 'value' => null, 'hint' => null, 'required' => false, 'id' => null, 'rows' => 4])
@php($id ??= str_replace(['[', ']', '.'], '-', $name))
<div>
    @if ($label)<x-admin.label :for="$id" :required="$required">{{ $label }}</x-admin.label>@endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->class([
            'flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/40',
            'border-destructive' => $errors->has($name),
        ]) }}>{{ old($name, $value) }}</textarea>
    @error($name)<p class="mt-1.5 text-[13px] text-destructive">{{ $message }}</p>@enderror
    @if ($hint && ! $errors->has($name))<p class="mt-1.5 text-[13px] text-muted-foreground">{{ $hint }}</p>@endif
</div>
