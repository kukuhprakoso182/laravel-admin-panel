@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'value' => '1',
])

@php
    $id = $attributes->get('id')
        ?: ($name ? \Illuminate\Support\Str::slug($name) : 'switch-'.\Illuminate\Support\Str::random(8));

    // pisahkan atribut yang harus ditempel ke <input> (x-model, x-bind:class, dll)
    // dari atribut yang boleh nempel ke <label> (class custom, dsb)
    $inputAttributes = $attributes->whereStartsWith(['x-model', 'wire:model', 'x-bind', ':', '@']);
    $labelAttributes = $attributes->except(array_keys($inputAttributes->getAttributes()));
@endphp

<label {{ $labelAttributes->merge(['for' => $id, 'class' => 'flex items-center gap-x-3 cursor-pointer select-none']) }}>
    <span class="relative inline-flex items-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            @checked(old($name, $checked))
            {{ $inputAttributes->merge(['class' => 'peer sr-only']) }}
        >
        <span class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-blue-600
            peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500 peer-focus-visible:ring-offset-2
            peer-disabled:opacity-50 transition-colors"></span>
        <span class="absolute left-0.5 top-0.5 size-5 bg-white rounded-full shadow
            transition-transform peer-checked:translate-x-5"></span>
    </span>

    @if($label)
        <span class="text-sm text-gray-700">{{ $label }}</span>
    @endif
</label>
