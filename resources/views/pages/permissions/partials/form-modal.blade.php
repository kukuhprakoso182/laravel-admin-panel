<form @submit.prevent="submitForm()">
    <x-molecules.modal show="showFormModal">

        <x-slot:title>
            <span x-text="isEdit ? 'Edit Permission' : 'Tambah Permission'"></span>
        </x-slot:title>

        <div class="space-y-4">
            <x-atoms.input
                type="text"
                name="name"
                label="Nama Permission"
                x-model="form.name"
                errors-var="errors"
                placeholder="contoh: view, create, edit, delete"
            />

            <x-atoms.input
                type="textarea"
                name="description"
                label="Deskripsi"
                x-model="form.description"
                errors-var="errors"
            />
        </div>

        <x-slot:footer>
            <x-molecules.modal-form-actions show="showFormModal" createLabel="Tambah Permission" />
        </x-slot:footer>

    </x-molecules.modal>
</form>
