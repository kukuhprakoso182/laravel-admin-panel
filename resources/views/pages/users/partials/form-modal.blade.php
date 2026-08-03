<form @submit.prevent="submitForm()">
    <x-molecules.modal show="showFormModal">

        <x-slot:title>
            <span x-text="isEdit ? 'Edit User' : 'Tambah User'"></span>
        </x-slot:title>

        <div class="space-y-4">
            <x-atoms.input
                type="text"
                name="name"
                label="Nama"
                x-model="form.name"
                errors-var="errors"
            />

            <x-atoms.input
                type="email"
                name="email"
                label="Email"
                x-model="form.email"
                errors-var="errors"
            />

            <x-atoms.input
                type="password"
                name="password"
                label="Password"
                x-model="form.password"
                errors-var="errors"
            >
                <x-slot:desc-label>
                    <span x-show="isEdit">(kosongkan jika tidak diubah)</span>
                </x-slot:desc-label>
            </x-atoms.input>

            <div>
                <x-atoms.label required>Role</x-atoms.label>
                <div class="flex flex-wrap gap-3 border border-gray-200 rounded-lg p-3">
                    @foreach($roles as $role)
                        <x-atoms.checkbox
                            name="roles[]"
                            :label="$role['name']"
                            value="{{ $role['id'] }}"
                            x-model="form.roles"
                        />
                    @endforeach
                </div>
                <p x-show="errors.roles" x-text="errors.roles?.[0]" class="text-sm text-red-600 mt-1"></p>
            </div>

            <x-atoms.switch label="Akun aktif" x-model="form.active" />
        </div>

        <x-slot:footer>
            <x-molecules.modal-form-actions show="showFormModal" createLabel="Tambah User" />
        </x-slot:footer>

    </x-molecules.modal>
</form>
