{{--
    CATATAN: layout ini saya susun berdasarkan pola umum Stisla (head sama persis
    dengan layouts.app, minus sidebar/navbar), karena saya belum lihat markup asli
    login.html/register.html kamu. Kalau kamu upload file itu, saya sesuaikan lagi
    class wrapper-nya (kemungkinan besar ada class khusus seperti "auth" / "auth-card"
    di style.css Meridian yang belum saya konfirmasi persis).

    Pakai:
    <x-layout.guest title="Sign in">
        ...form login...
    </x-layout.guest>
--}}
@props(['title' => null])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    <script>
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
    <div class="flex min-h-screen items-center justify-center p-4">
        {{ $slot }}
    </div>

    <script type="module" src="https://cdn.jsdelivr.net/npm/@stisla/vanilla@3/dist/stisla.js"></script>
    <script src="{{ asset('vendor/meridian/js/theme.js') }}"></script>

    {{ $scripts ?? '' }}
</body>
</html>
