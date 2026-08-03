@props([
    'title' => 'Login',
])
@php
    $appName = config('app.name');
@endphp
<x-layouts.base :title="$title">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">

        <a href="{{ url('/') }}" class="flex flex-col gap-2 items-center my-3 p-2">
            <x-atoms.icon src="{{ asset('logo.png') }}" color="teal" shape="md" size="xl" fit="contain" />
            <x-atoms.span color="teal" weight="bold" size="xl">{{ $appName }}</x-atoms.span>
        </a>
        <div class="">
        </div>

        <div class="w-full max-w-sm bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
            {{ $slot }}
        </div>
    </div>
</x-layouts.base>
