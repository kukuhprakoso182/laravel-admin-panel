{{-- Top-of-page greeting card with time-aware salutation and today's date. --}}
@props(['name' => 'Pengguna'])

@php
    $hour = now()->hour;
    $greeting = match (true) {
        $hour >= 4 && $hour < 11 => 'Selamat Pagi',
        $hour >= 11 && $hour < 15 => 'Selamat Siang',
        $hour >= 15 && $hour < 18 => 'Selamat Sore',
        default => 'Selamat Malam',
    };
@endphp

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
    <div>
        <h1 class="text-lg font-semibold text-gray-800">
            {{ $greeting }}, {{ $name }} 👋
        </h1>
        <p class="text-sm text-gray-500">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>
</div>
