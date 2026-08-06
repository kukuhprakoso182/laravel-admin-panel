{{-- Fallback shown when a role has no visible dashboard content at all. --}}
@props(['message' => 'Belum ada modul yang bisa ditampilkan untuk role Anda saat ini.'])

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-10 text-center">
    <p class="text-sm text-gray-400">{{ $message }}</p>
</div>
