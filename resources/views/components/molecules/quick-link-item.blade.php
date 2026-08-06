{{-- One button in the quick-access grid. --}}
@props(['link'])

<a href="{{ route($link['route']) }}"
   class="text-sm text-center px-3 py-3 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
    {{ $link['label'] }}
</a>
