@props(['brand'])
<div class="h-16 flex items-center px-5">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-x-2 font-bold text-gray-900 shrink-0">
        <x-atoms.icon src="{{ asset('logo.png') }}" color="teal" shape="md" size="md" fit="contain" />
        <span>{{ $brand }}</span>
    </a>
</div>
