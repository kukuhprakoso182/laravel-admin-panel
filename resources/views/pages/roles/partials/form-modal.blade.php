<form @submit.prevent="submitForm()">
    <x-molecules.modal show="showFormModal">

        <x-slot:title>
            <span x-text="isEdit ? 'Edit Role' : 'Tambah Role'"></span>
        </x-slot:title>

        <div class="space-y-4">
            <x-atoms.input
                type="text"
                name="name"
                label="Nama"
                x-model="form.name"
                errors-var="errors"
            />

            <x-atoms.textarea
                name="description"
                label="Deskripsi"
                rows="3"
                x-model="form.description"
                errors-var="errors"
            />
        </div>

        <x-slot:footer>
            <x-molecules.modal-form-actions show="showFormModal" createLabel="Tambah Role" />
        </x-slot:footer>

    </x-molecules.modal>
</form>
