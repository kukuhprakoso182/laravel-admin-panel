@props(['show' => 'show', 'editLabel' => 'Simpan Perubahan', 'createLabel' => 'Simpan'])

<x-atoms.button type="button" variant="white" @click="{{ $show }} = false">Batal</x-atoms.button>
<x-atoms.button type="submit" color="blue" x-bind:disabled="submitting">
    <span x-show="submitting" class="animate-spin inline-block size-4 border-2 border-current border-t-transparent rounded-full"></span>
    <span x-text="isEdit ? '{{ $editLabel }}' : '{{ $createLabel }}'"></span>
</x-atoms.button>
