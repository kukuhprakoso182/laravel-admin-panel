{{-- User count breakdown per role, as progress bars. --}}
@props(['roles'])

@if ($roles->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Distribusi User per Role</h2>
        <div class="space-y-3">
            @foreach ($roles as $item)
                <x-molecules.role-progress-item :item="$item" />
            @endforeach
        </div>
    </div>
@endif
