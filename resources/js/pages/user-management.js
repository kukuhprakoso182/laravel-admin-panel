window.userManagement = function () {
    return {
        users: [],
        meta: {},
        loading: false,
        selected: [],
        submitting: false,
        showFormModal: false,
        showDeleteModal: false,
        isEdit: false,
        errors: {},
        deleteTarget: null,
        filters: { search: '', role: '', page: 1 },
        form: { id: null, name: '', email: '', password: '', roles: [], active: true },
        exporting: false,

        init() {
            this.fetchData();

            this.$watch('filters.search', () => this.resetPageAndSelection());
            this.$watch('filters.role', () => this.resetPageAndSelection());
        },

        resetPageAndSelection() {
            this.selected = [];
            this.filters.page = 1;
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            const params = formatUtils.buildQueryParams(this.filters);

            fetch(`/users/data?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    const { items, meta } = tableUtils.normalizePaginated(data);
                    this.users = items;
                    this.meta = meta;
                })
                .catch((err) => {
                    console.error('fetchData error:', err);
                    window.alertError('Gagal memuat data user.');
                })
                .finally(() => this.loading = false);
        },

        async exportData() {
            this.exporting = true;

            try {
                const params = formatUtils.buildQueryParams({
                    search: this.filters.search,
                    role: this.filters.role,
                });

                const res = await fetch(`/users/export?${params}`, {
                    headers: { 'X-CSRF-TOKEN': apiUtils.csrfToken() },
                });

                if (!res.ok) {
                    const body = await res.json().catch(() => null);
                    throw new Error(body?.message || 'Gagal mengekspor data.');
                }

                const blob = await res.blob();
                const disposition = res.headers.get('Content-Disposition') || '';
                const match = disposition.match(/filename="?([^"]+)"?/);
                const filename = match ? match[1] : 'export.csv';

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            } catch (err) {
                console.error('exportData error:', err);
                window.alertError(err.message || 'Gagal mengekspor data.');
            } finally {
                this.exporting = false;
            }
        },

        get isAllSelectedAcrossPages() {
            return this.selected.length === this.meta.total;
        },

        clearSelection() {
            this.selected = [];
        },

        openCreate() {
            this.isEdit = false;
            this.errors = {};
            this.form = { id: null, name: '', email: '', password: '', roles: [], active: true };
            this.showFormModal = true;
        },

        openEdit(user) {
            this.isEdit = true;
            this.errors = {};
            this.form = {
                id: user.id, name: user.name, email: user.email, password: '',
                roles: user.roles.map(r => r.id), active: user.status === 'active',
            };
            this.showFormModal = true;
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            const url = this.isEdit ? `/users/${this.form.id}` : '/users';
            const method = this.isEdit ? 'PUT' : 'POST';

            try {
                const result = await apiUtils.apiFetch(url, {
                    method,
                    body: JSON.stringify({
                        name: this.form.name,
                        email: this.form.email,
                        password: this.form.password || undefined,
                        roles: this.form.roles,
                        status: this.form.active ? 'active' : 'inactive',
                    }),
                });

                if (result.validationError) {
                    this.errors = result.errors;
                    window.alertError(result.message);
                    return;
                }

                this.showFormModal = false;
                window.alertSuccess(result.data?.message || (this.isEdit ? 'User berhasil diperbarui.' : 'User berhasil ditambahkan.'));
                this.fetchData();
            } catch (err) {
                window.alertError(err.message || 'Gagal menyimpan data.');
            } finally {
                this.submitting = false;
            }
        },

        openDelete(user) {
            this.deleteTarget = user;
            this.showDeleteModal = true;
        },

        async confirmDelete() {
            this.submitting = true;

            try {
                const result = await apiUtils.apiFetch(`/users/${this.deleteTarget.id}`, { method: 'DELETE' });
                this.showDeleteModal = false;
                this.selected = this.selected.filter(id => id !== this.deleteTarget.id); // tambahan
                window.alertSuccess(result.data?.message || 'User berhasil dihapus.');
                this.fetchData();
            } catch (err) {
                window.alertError(err.message || 'Gagal menghapus data.');
            } finally {
                this.submitting = false;
            }
        },

        formatDate: formatUtils.formatDate,
    };
};
