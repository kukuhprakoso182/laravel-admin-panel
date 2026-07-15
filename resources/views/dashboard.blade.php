{{--
    Contoh pemakaian x-layouts.app. Konten dashboard (stat card, chart, tabel
    recent orders) BELUM di-slice jadi component — itu next step kalau kamu mau.
--}}
<x-layout.app title="Dashboard">
    <x-slot:pageTitle>
        Welcome back, <span>{{ auth()->user()->first_name ?? 'Steven' }}</span> 👋
    </x-slot:pageTitle>

    <section class="page__section">
        {{-- TODO: stat cards, chart, recent orders table --}}
    </section>
</x-layout.app>
