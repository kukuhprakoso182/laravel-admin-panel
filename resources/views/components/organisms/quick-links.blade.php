{{-- Grid of quick-access shortcuts. --}}
@props(['links' => [], 'fullWidth' => false])

@if (count($links) > 0)
    <div class="{{ $fullWidth ? 'lg:col-span-3' : '' }} bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Akses Cepat</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach ($links as $link)
                <x-molecules.quick-link-item :link="$link" />
            @endforeach
        </div>
    </div>
@endif
