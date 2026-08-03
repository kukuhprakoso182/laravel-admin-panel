@props([
    'editFn' => null,          // expression Alpine, misal 'openEdit(row)'
    'deleteLabel' => null,     // expression Alpine, misal 'row.name'
    'deleteUrl' => null,       // expression Alpine, misal '`/users/${row.id}`'
    'onDeleteSuccess' => null, // expression Alpine dijalankan setelah delete sukses
])

<div class="flex items-center justify-end gap-x-1">
    @if($editFn)
        <button type="button" @click="{{ $editFn }}"
                class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-blue-600 cursor-pointer">
            <i class="ri-edit-box-line ri-lg"></i>
        </button>
    @endif

    @if($deleteUrl)
        <button type="button" @click="confirmDeleteDialog({{ $deleteLabel }}, {{ $deleteUrl }}, {
                    onSuccess: () => { {{ $onDeleteSuccess }} }
                })"
                class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 cursor-pointer">
            <i class="ri-delete-bin-line ri-lg"></i>
        </button>
    @endif

    {{ $slot ?? '' }}
</div>
