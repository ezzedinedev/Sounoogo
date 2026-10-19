@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null, 'required' => false, 'id' => null])
@php($id ??= str_replace(['[', ']', '.'], '-', $name))
@php($selected = (string) old($name, $value))
<div>
    @if ($label)<x-admin.label :for="$id" :required="$required">{{ $label }}</x-admin.label>@endif
    <select id="{{ $id }}" name="{{ $name }}" @required($required)
        {{ $attributes->class(['flex h-9 w-full rounded-md border border-input bg-background px-3 pe-8 text-sm shadow-xs focus-visible:outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/40']) }}>
        @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @error($name)<p class="mt-1.5 text-[13px] text-destructive">{{ $message }}</p>@enderror
</div>
