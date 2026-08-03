@props([
    'type' => 'text',
    'name' => null,
    'label' => null,
    'descLabel' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'helper' => null,
    'currency' => 'Rp',
    'accept' => null,
    'preview' => null,
    'passwordToggle' => true,
    'errorsVar' => null,
])


@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $oldValue = old($name, $value);

    // Base style ala Preline: rounded-lg, border tipis, ring-1 saat focus
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

    @switch($type)

        @case('password')
            <div class="relative" x-data="{ show: false }">
                <input
                    :type="show ? 'text' : 'password'"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    value="{{ $oldValue }}"
                    placeholder="{{ $placeholder }}"
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    @if($readonly) readonly @endif
                    {{ $attributes->merge(['class' => "$base $state" . ($passwordToggle ? ' pe-11' : '')]) }}
                >

                @if($passwordToggle)
                    <button
                        type="button"
                        @click="show = !show"
                        tabindex="-1"
                        class="absolute inset-y-0 inset-e-0 flex items-center pe-4 text-gray-400 hover:text-gray-600"
                    >
                        <svg x-show="!show" x-cloak class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg x-show="show" x-cloak class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c6.5 0 10 8 10 8a17.5 17.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                            <path d="M6.61 6.61A17.5 17.5 0 0 0 2 12s3.5 8 10 8a9.7 9.7 0 0 0 5.39-1.61" />
                            <line x1="2" y1="2" x2="22" y2="22" />
                        </svg>
                    </button>
                @endif
            </div>
            @break

        @case('email')
            <div class="relative">
                <input
                    type="email"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    value="{{ $oldValue }}"
                    placeholder="{{ $placeholder }}"
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    @if($readonly) readonly @endif
                    {{ $attributes->merge(['class' => "$base $state" . ($hasError ? ' pe-11' : '')]) }}
                >
            </div>
            @break

        @case('currency')
            <div class="relative">
                <span class="absolute inset-y-0 inset-s-0 flex items-center ps-4 text-sm text-gray-500">
                    {{ $currency }}
                </span>
                <input
                    type="text"
                    inputmode="decimal"
                    id="{{ $id }}"
                    name="{{ $name }}"
                    value="{{ $oldValue }}"
                    placeholder="{{ $placeholder ?? '0' }}"
                    x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                    @if($required) required @endif
                    @if($disabled) disabled @endif
                    @if($readonly) readonly @endif
                    {{ $attributes->merge(['class' => "$base $state ps-12"]) }}
                >
            </div>
            @break

        @case('file')
            <div
                x-data="{
                    preview: @js($preview),
                    fileName: '',
                    isImage: true,
                    onChange(e) {
                        const file = e.target.files[0];
                        if (!file) { this.preview = null; this.fileName = ''; return; }
                        this.fileName = file.name;
                        this.isImage = file.type.startsWith('image/');
                        if (this.isImage) {
                            const reader = new FileReader();
                            reader.onload = (ev) => this.preview = ev.target.result;
                            reader.readAsDataURL(file);
                        } else {
                            this.preview = null;
                        }
                    }
                }"
            >
                <div class="flex items-center gap-4">
                    <div class="size-20 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                        <template x-if="preview && isImage">
                            <img :src="preview" class="w-full h-full object-cover" alt="Preview">
                        </template>
                        <template x-if="!preview || !isImage">
                            <svg class="size-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </template>
                    </div>

                    <div class="flex-1">
                        <label for="{{ $id }}"
                            class="cursor-pointer inline-flex items-center gap-x-2 py-2 px-3 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Pilih file
                        </label>
                        <input
                            type="file"
                            id="{{ $id }}"
                            name="{{ $name }}"
                            @change="onChange($event)"
                            @if($accept) accept="{{ $accept }}" @endif
                            @if($required) required @endif
                            class="hidden"
                        >
                        <p class="mt-1 text-xs text-gray-500" x-text="fileName || 'Belum ada file dipilih'"></p>
                    </div>
                </div>
            </div>
            @break

        @case('textarea')
            <textarea
                id="{{ $id }}"
                name="{{ $name }}"
                rows="4"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                {{ $attributes->merge(['class' => "$base $state"]) }}
            >{{ $oldValue }}</textarea>
            @break

        @case('phone')
            <input
                type="tel"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $oldValue }}"
                placeholder="{{ $placeholder ?? '08xxxxxxxxxx' }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                {{ $attributes->merge(['class' => "$base $state"]) }}
            >
            @break

        @default
            <input
                type="{{ $type }}"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $oldValue }}"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                {{ $attributes->merge(['class' => "$base $state"]) }}
            >
    @endswitch

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
