@props([
    'show' => 'show',
    'size' => 'lg',        // sm | md | lg | xl | 2xl | 3xl | 4xl | full
    'width' => null,       // override bebas, mis. '600px', '42rem', '80vw' -> lewat inline style
    'maxHeight' => '90vh', // mis. '80vh', '600px'
])

@php
    // Preset Tailwind class WAJIB ditulis literal di sini (bukan dirakit
    // dari string dinamis), supaya tetap terdeteksi oleh Tailwind JIT scanner
    // -- Tailwind membaca file blade sebagai teks mentah untuk cari nama
    // class, bukan mengeksekusi PHP-nya. Kalau class dirakit runtime (mis.
    // "max-w-{$size}"), JIT tidak akan pernah melihat class itu dan CSS-nya
    // tidak pernah ter-generate.
    $sizeMap = [
        'sm'   => 'max-w-sm',
        'md'   => 'max-w-md',
        'lg'   => 'max-w-lg',
        'xl'   => 'max-w-xl',
        '2xl'  => 'max-w-2xl',
        '3xl'  => 'max-w-3xl',
        '4xl'  => 'max-w-4xl',
        'full' => 'max-w-full',
    ];

    // Kalau $width diisi (nilai CSS bebas, bukan nama preset), lebar diatur
    // lewat inline style -- ini sengaja BUKAN class Tailwind arbitrary
    // (mis. "max-w-[{$width}]"), karena nilai yang dirakit dari variabel
    // PHP di-runtime tidak akan pernah dikenali JIT scanner (lihat catatan
    // di atas). Inline style bebas dari batasan itu, jadi aman dipakai
    // untuk ukuran benar-benar custom / dinamis.
    $widthClass = $width ? 'w-full' : ($sizeMap[$size] ?? $sizeMap['lg']);
    $widthStyle = $width ? "max-width: {$width};" : '';
@endphp

<div x-show="{{ $show }}" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     x-transition.opacity>
    <div
        {{ $attributes->merge(['class' => "w-full {$widthClass} bg-white rounded-2xl shadow-xl p-6 overflow-y-auto"]) }}
        style="{{ $widthStyle }} max-height: {{ $maxHeight }};"
    >

        @isset($title)
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                <button type="button" @click="{{ $show }} = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <i class="ri-close-line ri-lg"></i>
                </button>
            </div>
        @endisset

        {{ $slot }}

        @isset($footer)
            <div class="flex items-center justify-end gap-x-2 pt-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
