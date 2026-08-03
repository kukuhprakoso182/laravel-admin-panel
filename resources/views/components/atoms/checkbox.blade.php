@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'value' => '1',
])

@php
    // fallback id: kombinasi name+value biar unik walau dipakai berulang dalam @foreach
    $id = $attributes->get('id')
        ?: ($name ? \Illuminate\Support\Str::slug($name.'-'.$value) : 'checkbox-'.\Illuminate\Support\Str::random(8));

    $hasError = $name ? $errors->has($name) : false;
@endphp

<div>
    <label for="{{ $id }}" class="flex items-center gap-x-3 cursor-pointer select-none">
        <span class="relative flex items-center">
            <input
                type="checkbox"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $value }}"
                @checked(old($name, $checked))
                {{ $attributes->merge(['class' => 'peer sr-only']) }}
            >
            <span class="size-5 rounded-md border-2 border-gray-300 bg-white
                peer-checked:bg-blue-600 peer-checked:border-blue-600
                peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500 peer-focus-visible:ring-offset-2
                peer-disabled:opacity-50 transition-colors flex items-center justify-center">
                <svg class="hidden peer-checked:block size-3 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </span>
        </span>

        @if($label)
            <span class="text-sm text-gray-700">{{ $label }}</span>
        @endif
    </label>

    @if($name)
        <x-atoms.input-error :for="$name" />
    @endif
</div>
