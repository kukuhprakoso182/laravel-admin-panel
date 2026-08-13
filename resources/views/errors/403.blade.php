<x-layouts.guest title="403">
    <div class="w-full max-w-md mx-auto text-center">
        <p class="text-6xl font-bold text-amber-500">403</p>
        <h1 class="mt-4 text-xl font-semibold text-gray-800">Akses Ditolak</h1>
        <p class="mt-2 text-sm text-gray-500">
            Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika Anda merasa ini keliru.
        </p>

        <div class="mt-6">
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-layouts.guest>
