@props([
    'name' => null,
    'label' => null,
    'descLabel' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helper' => null,
    'rows' => 4,
    'errorsVar' => null,
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $oldValue = old($name, $value);

    $base = 'py-2.5 sm:py-3 px-4 block w-full rounded-lg sm:text-sm
        placeholder:text-gray-400 disabled:opacity-50 disabled:pointer-events-none bg-slate-100';

    $state = $hasError
        ? 'border-red-500 text-gray-900 focus:border-red-500 focus:ring-red-500'
        : 'border-gray-200 text-gray-900 focus:border-blue-500 focus:ring-blue-500 focus:ring-1';
@endphp

<div>
    @if($label)
        <x-atoms.label :for="$id" :required="$required" size="base">{{ $label }}</x-atoms.label>
    @endif

    @if($descLabel)
        <span class="text-xs text-gray-400 font-normal">{{ $descLabel }}</span>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        {{ $attributes->merge(['class' => "$base $state"]) }}
    >{{ $oldValue }}</textarea>

    @if($helper && !$hasError)
        <p class="text-sm text-gray-500 mt-2">{{ $helper }}</p>
    @endif

    @if($errorsVar)
        <p
            x-show="{{ $errorsVar }}.{{ $name }}"
            x-text="{{ $errorsVar }}.{{ $name }}?.[0]"
            class="text-sm text-red-600 -mt-1"
        ></p>
    @else
        <x-atoms.input-error :for="$name" />
    @endif
</div>
