@props([
    'id' => 'confirm-modal',
    'title' => 'Konfirmasi',
    'size' => 'sm',
    'confirmText' => 'Ya, lanjutkan',
    'cancelText' => 'Batal',
    'confirmColor' => 'red',   // red | blue | gray, dst
    'action' => null,          // opsional: URL untuk submit form (misal route delete)
    'method' => 'POST',        // method form kalau pakai action
])

@php
    $confirmColors = [
        'red' => 'bg-red-600 hover:bg-red-700',
        'blue' => 'bg-blue-600 hover:bg-blue-700',
        'gray' => 'bg-gray-600 hover:bg-gray-700',
    ];
    $confirmClass = 'py-2.5 px-4 text-sm font-medium rounded-lg text-white cursor-pointer transition-colors ' .
        ($confirmColors[$confirmColor] ?? $confirmColors['red']);
@endphp

<x-molecules.modal id="{{ $id }}" title="{{ $title }}" size="{{ $size }}">
    {{ $slot }}

    <x-slot:footer>
        <button type="button" data-modal-close="{{ $id }}"
                class="py-2.5 px-4 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer">
            {{ $cancelText }}
        </button>

        @if($action)
            <form method="POST" action="{{ $action }}">
                @csrf
                @if(strtoupper($method) !== 'POST')
                    @method($method)
                @endif
                <button type="submit" class="{{ $confirmClass }}">
                    {{ $confirmText }}
                </button>
            </form>
        @else
            <button type="button" class="{{ $confirmClass }}" data-modal-confirm="{{ $id }}">
                {{ $confirmText }}
            </button>
        @endif
    </x-slot:footer>
</x-molecules.modal>

@once
    @push('scripts')
        <script>
            // Dispatch custom event saat tombol confirm (non-form) diklik,
            // supaya halaman yang pakai bisa handle aksinya sendiri (fetch/Livewire/dsb).
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-modal-confirm]');
                if (!btn) return;

                document.dispatchEvent(new CustomEvent('modal:confirmed', {
                    detail: { id: btn.dataset.modalConfirm }
                }));
            });
        </script>
    @endpush
@endonce
