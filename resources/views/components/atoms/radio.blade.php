@props([
    'name' => null,
    'label' => null,
    'value' => null,
    'checked' => false,
])

@php
    $id = $attributes->get('id', $name . '-' . $value);
@endphp

<label for="{{ $id }}" class="flex items-center gap-x-3 cursor-pointer select-none">
    <span class="relative flex items-center">
        <input
            type="radio"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            @checked(old($name, $checked) == $value)
            {{ $attributes->merge(['class' => 'peer sr-only']) }}
        >
        <span class="size-5 rounded-full border-2 border-gray-300 bg-white
            peer-checked:border-blue-600 peer-checked:border-[6px]
            peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500 peer-focus-visible:ring-offset-2
            peer-disabled:opacity-50 transition-all"></span>
    </span>

    @if($label)
        <span class="text-sm text-gray-700">{{ $label }}</span>
    @endif
</label>
