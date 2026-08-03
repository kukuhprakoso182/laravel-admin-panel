@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'shadow' => true,
])

<div {{ $attributes->merge(['class' => 'flex flex-col bg-white border border-gray-200 rounded-xl ' . ($shadow ? 'shadow-sm' : '')]) }}>
    @if($title || $subtitle)
        <div class="p-4 md:p-5 border-b border-gray-200">
            @if($title)
                <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
            @endif
            @if($subtitle)
                <p class="text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="p-4 md:p-5">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="p-4 md:p-5 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>
