<form @submit.prevent="submitForm()">
    <x-molecules.modal show="showFormModal">

        <x-slot:title>
            <span x-text="isEdit ? 'Edit Icon' : 'Tambah Icon'"></span>
        </x-slot:title>

        <div class="space-y-4">
            <x-atoms.input
                type="text"
                name="value"
                label="Value (nama class icon)"
                placeholder="ri-home-line"
                x-model="form.value"
                errors-var="errors"
            />

            <x-atoms.input
                type="text"
                name="section"
                label="Section"
                placeholder="remixicon"
                x-model="form.section"
                errors-var="errors"
            />

            <div class="flex items-center gap-x-3">
                <i :class="form.value" class="ri-lg text-gray-600"></i>
                <span class="text-sm text-gray-400">Preview icon</span>
            </div>

            <x-atoms.switch label="Aktif" x-model="form.is_active" />
        </div>

        <x-slot:footer>
            <x-molecules.modal-form-actions show="showFormModal" createLabel="Tambah Icon" />
        </x-slot:footer>

    </x-molecules.modal>
</form>
