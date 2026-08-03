@props([
    'title' => config('app.name'),
    'head' => null,
    'scripts' => null,
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('logo.png') }}" rel="icon" type="image/x-icon" />
    <title>{{ $title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $head }}
    @stack('styles')
</head>
<body {{ $attributes->merge(['class' => 'antialiased bg-gray-50 text-gray-900']) }}>
    {{ $slot }}

    @include('components.molecules.flash-alert')
    @stack('scripts')
</body>
</html>
