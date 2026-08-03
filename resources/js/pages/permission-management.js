window.permissionManagement = function () {
    return {
        permissions: [],
        meta: {},
        loading: false,
        selected: [],
        submitting: false,
        showFormModal: false,
        isEdit: false,
        errors: {},
        filters: { search: '', page: 1 },
        form: { id: null, name: '', description: '' },

        init() {
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            const params = formatUtils.buildQueryParams(this.filters);

            fetch(`/permissions/data?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    const { items, meta } = tableUtils.normalizePaginated(data);
                    this.permissions = items;
                    this.meta = meta;
                })
                .catch((err) => {
                    console.error('fetchData error:', err);
                    window.alertError('Gagal memuat data permission.');
                })
                .finally(() => this.loading = false);
        },

        openCreate() {
            this.isEdit = false;
            this.errors = {};
            this.form = { id: null, name: '', description: '' };
            this.showFormModal = true;
        },

        openEdit(permission) {
            this.isEdit = true;
            this.errors = {};
            this.form = { id: permission.id, name: permission.name, description: permission.description ?? '' };
            this.showFormModal = true;
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            const url = this.isEdit ? `/permissions/${this.form.id}` : '/permissions';
            const method = this.isEdit ? 'PUT' : 'POST';

            try {
                const result = await apiUtils.apiFetch(url, {
                    method,
                    body: JSON.stringify({
                        name: this.form.name,
                        description: this.form.description || null,
                    }),
                });

                if (result.validationError) {
                    this.errors = result.errors;
                    window.alertError(result.message);
                    return;
                }

                this.showFormModal = false;
                window.alertSuccess(result.data?.message || (this.isEdit ? 'Permission berhasil diperbarui.' : 'Permission berhasil ditambahkan.'));
                this.fetchData();
            } catch (err) {
                window.alertError(err.message || 'Gagal menyimpan data.');
            } finally {
                this.submitting = false;
            }
        },

        formatDate: formatUtils.formatDate,
    };
};
