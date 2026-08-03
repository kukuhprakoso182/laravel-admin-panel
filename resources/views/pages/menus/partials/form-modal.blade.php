@props(['parentMenus' => [], 'icons' => []])

<form @submit.prevent="submitForm()">
    <x-molecules.modal show="showFormModal">

        <x-slot:title>
            <span x-text="isEdit ? 'Edit Menu' : 'Tambah Menu'"></span>
        </x-slot:title>

        <div class="space-y-4">
            <x-atoms.input
                type="text"
                name="name"
                label="Nama Menu"
                x-model="form.name"
                errors-var="errors"
            />

            <x-atoms.input
                type="text"
                name="link"
                label="Link"
                placeholder="/dashboard"
                x-model="form.link"
                errors-var="errors"
            />

            <x-atoms.input
                type="text"
                name="link_alias"
                label="Link Alias"
                placeholder="dashboard.*"
                x-model="form.link_alias"
                errors-var="errors"
            />

            <div>
                <x-atoms.label>Parent Menu</x-atoms.label>
                <x-atoms.select
                    model="form.parent_id"
                    :options="collect($parentMenus)->map(fn ($m) => ['value' => $m->id, 'label' => $m->name])"
                    placeholder="Tidak ada (menu utama)"
                />
                <p x-show="errors.parent_id" x-text="errors.parent_id?.[0]" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div>
                <x-atoms.label>Icon</x-atoms.label>
                <div class="flex items-center gap-x-3">
                    <div class="shrink-0 flex items-center justify-center size-10 rounded-lg border border-gray-200 bg-gray-50">
                        <i :class="selectedIconClass" class="ri-lg text-gray-600" x-show="selectedIconClass"></i>
                        <i class="ri-question-line ri-lg text-gray-300" x-show="!selectedIconClass"></i>
                    </div>
                    <div class="flex-1">
                        <x-atoms.select
                            model="form.icon_id"
                            :options="collect($icons)->map(fn ($i) => ['value' => $i->id, 'label' => $i->value])"
                            placeholder="Tanpa icon"
                        />
                    </div>
                </div>
                <p x-show="errors.icon_id" x-text="errors.icon_id?.[0]" class="text-sm text-red-600 mt-1"></p>
            </div>

            <x-atoms.input
                type="number"
                name="order"
                label="Urutan"
                x-model.number="form.order"
                errors-var="errors"
            />

            <x-atoms.switch label="Aktif" x-model="form.is_active" />
        </div>

        <x-slot:footer>
            <x-molecules.modal-form-actions show="showFormModal" createLabel="Tambah Menu" />
        </x-slot:footer>

    </x-molecules.modal>
</form>
