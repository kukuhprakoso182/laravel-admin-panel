window.roleManagement = function () {
    return {
        roles: [],
        meta: {},
        loading: false,
        selected: [],
        submitting: false,
        showFormModal: false,
        isEdit: false,
        errors: {},
        filters: { search: '', page: 1 },
        form: { id: null, name: '', description: '' },
        permissions: [],
        showPermissionModal: false,
        loadingMatrix: false,
        savingMatrix: false,
        matrixRole: null,
        matrixMenus: [],
        checkedPairs: {},

        get flatPermissions() {
            return this.permissionGroups.flatMap(g => g.permissions);
        },

        init() {
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            const params = formatUtils.buildQueryParams(this.filters);

            fetch(`/roles/data?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    const { items, meta } = tableUtils.normalizePaginated(data);
                    this.roles = items;
                    this.meta = meta;
                })
                .catch((err) => {
                    console.error('fetchData error:', err);
                    window.alertError('Gagal memuat data role.');
                })
                .finally(() => this.loading = false);
        },

        openCreate() {
            this.isEdit = false;
            this.errors = {};
            this.form = { id: null, name: '', description: '' };
            this.showFormModal = true;
        },

        openEdit(role) {
            this.isEdit = true;
            this.errors = {};
            this.form = { id: role.id, name: role.name, description: role.description ?? '' };
            this.showFormModal = true;
        },

        async submitForm() {
            this.submitting = true;
            this.errors = {};

            const url = this.isEdit ? `/roles/${this.form.id}` : '/roles';
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
                window.alertSuccess(result.data?.message || (this.isEdit ? 'Role berhasil diperbarui.' : 'Role berhasil ditambahkan.'));
                this.fetchData();
            } catch (err) {
                window.alertError(err.message || 'Gagal menyimpan data.');
            } finally {
                this.submitting = false;
            }
        },

        async openPermissionMatrix(role) {
            this.matrixRole = role;
            this.showPermissionModal = true;
            this.loadingMatrix = true;
            this.checkedPairs = {};
            this.matrixMenus = [];

            try {
                const [treeRes, matrixRes] = await Promise.all([
                    fetch('/menus/tree', { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
                    fetch(`/roles/${role.id}/permissions`, { headers: { 'Accept': 'application/json' } }).then(r => r.json()),
                ]);

                this.matrixMenus = treeRes.data ?? treeRes;

                const pairs = {};
                (matrixRes.assigned ?? []).forEach(key => { pairs[key] = true; });
                this.checkedPairs = pairs;
            } catch (err) {
                console.error('openPermissionMatrix error:', err);
                window.alertError('Gagal memuat data permission.');
                this.showPermissionModal = false;
            } finally {
                this.loadingMatrix = false;
            }
        },

        isChecked(menuId, permissionId) {
            return !!this.checkedPairs[`${menuId}:${permissionId}`];
        },

        togglePermission(menuId, permissionId) {
            const key = `${menuId}:${permissionId}`;
            if (this.checkedPairs[key]) {
                delete this.checkedPairs[key];
            } else {
                this.checkedPairs[key] = true;
            }
        },

        async savePermissionMatrix() {
            this.savingMatrix = true;

            const menuPermissions = Object.keys(this.checkedPairs).map(key => {
                const [menu_id, permission_id] = key.split(':').map(Number);
                return { menu_id, permission_id };
            });

            try {
                const result = await apiUtils.apiFetch(`/roles/${this.matrixRole.id}/permissions`, {
                    method: 'POST',
                    body: JSON.stringify({ menu_permissions: menuPermissions }),
                });

                window.alertSuccess(result.data?.message || 'Permission berhasil disimpan.');
                this.showPermissionModal = false;
            } catch (err) {
                window.alertError(err.message || 'Gagal menyimpan permission.');
            } finally {
                this.savingMatrix = false;
            }
        },

        formatDate: formatUtils.formatDate,
    };
};
