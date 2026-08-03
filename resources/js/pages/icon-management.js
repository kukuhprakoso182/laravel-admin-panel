window.iconManagement = function () {
    return {
        icons: [],
        meta: {},
        loading: false,
        selected: [],
        submitting: false,
        showFormModal: false,
        isEdit: false,
        errors: {},
        filters: { search: '', page: 1 },
        form: { id: null, value: '', section: '', is_active: true },

        init() {
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            const params = formatUtils.buildQueryParams(this.filters);

            fetch(`/icons/data?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    const { items, meta } = tableUtils.normalizePaginated(data);
                    this.icons = items;
                    this.meta = meta;
                })
                .catch((err) => {
                    console.error('fetchData error:', err);
                    window.alertError('Gagal memuat data icon.');
                })
                .finally(() => this.loading = false);
        },

        openCreate() {
            this.isEdit = false;
            this.errors = {};
            this.form = { id: null, value: '', section: '', is_active: true };
            this.showFormModal = true;
        },

        openEdit(icon) {
            this.isEdit = true;
            this.errors = {};
            this.form = { id: icon.id, value: icon.value, section: icon.section, is_active: icon.is_active };
            this.showFormModal = true;
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            const url = this.isEdit ? `/icons/${this.form.id}` : '/icons';
            const method = this.isEdit ? 'PUT' : 'POST';

            try {
                const result = await apiUtils.apiFetch(url, {
                    method,
                    body: JSON.stringify({
                        value: this.form.value,
                        section: this.form.section,
                        is_active: this.form.is_active,
                    }),
                });

                if (result.validationError) {
                    this.errors = result.errors;
                    window.alertError(result.message);
                    return;
                }

                this.showFormModal = false;
                window.alertSuccess(result.data?.message || (this.isEdit ? 'Icon berhasil diperbarui.' : 'Icon berhasil ditambahkan.'));
                this.fetchData();
            } catch (err) {
                window.alertError(err.message || 'Gagal menyimpan data.');
            } finally {
                this.submitting = false;
            }
        },
    };
};
