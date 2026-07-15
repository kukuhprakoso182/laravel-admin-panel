{{--
    Pakai di setiap halaman utama, contoh resources/views/dashboard.blade.php:

    <x-layout.app title="Dashboard">
        <x-slot:pageTitle>Welcome back, <span>{{ auth()->user()->first_name }}</span> 👋</x-slot:pageTitle>

        ...konten halaman...
    </x-layout.app>
--}}
@props(['title' => null, 'messages' => [], 'notifications' => []])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    <script>
        // Terapkan theme tersimpan sebelum first paint biar nggak flash.
        (function () {
            var t = localStorage.getItem("stisla-theme");
            if (t === "dark" || t === "light") document.documentElement.dataset.theme = t;
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
    />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet" />

    <title>{{ $title ? $title . ' · ' : '' }}{{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/meridian/css/style.css') }}" />

    {{ $head ?? '' }}
</head>
<body>
    <div class="app-shell" data-stisla-app-shell data-stisla-app-shell-auto-collapse="true">
        <x-sidebar.sidebar />

        <main class="app-shell__main">
            <x-navbar.navbar :messages="$messages" :notifications="$notifications" />

            <div class="page content">
                <div class="content__container">
                    @isset($pageTitle)
                        <header class="page__header">
                            <h1 class="page__title">{{ $pageTitle }}</h1>
                        </header>
                    @endisset

                    <div class="page__body">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@stisla/vanilla@3/dist/stisla.js"></script>
    <script src="{{ asset('vendor/meridian/js/app-shell.js') }}"></script>
    <script src="{{ asset('vendor/meridian/js/theme.js') }}"></script>

    {{ $scripts ?? '' }}
</body>
</html>
