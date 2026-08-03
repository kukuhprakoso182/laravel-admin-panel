window.menuManagement = function () {
    return {
        menus: [],
        meta: {},
        loading: false,
        selected: [],
        submitting: false,
        showFormModal: false,
        isEdit: false,
        errors: {},
        icons: [],   // ← baru: daftar icon untuk lookup preview
        filters: { search: '', parent_id: '', page: 1 },
        form: { id: null, name: '', link: '', link_alias: '', parent_id: '', icon_id: '', order: 0, is_active: true },
        viewMode: 'table', // 'table' | 'tree'
        treeMenus: [],
        loadingTree: false,
        expandedNodes: {},
        init() {
            this.fetchData();
        },

        get selectedIconClass() {
            const found = this.icons.find(i => String(i.id) === String(this.form.icon_id));
            return found ? found.value : '';
        },

        switchView(mode) {
            this.viewMode = mode;
            if (mode === 'tree' && this.treeMenus.length === 0) {
                this.fetchTree();
            }
        },

        fetchTree() {
            this.loadingTree = true;
            fetch('/menus/tree', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    this.treeMenus = data.data ?? data;
                })
                .catch((err) => {
                    console.error('fetchTree error:', err);
                    window.alertError('Gagal memuat struktur menu.');
                })
                .finally(() => this.loadingTree = false);
        },

        toggleNode(id) {
            this.expandedNodes[id] = !this.isExpanded(id);
        },

        isExpanded(id) {
            return this.expandedNodes[id] !== false; // default: terbuka
        },

        fetchData() {
            this.loading = true;
            const params = formatUtils.buildQueryParams(this.filters);

            fetch(`/menus/data?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    const { items, meta } = tableUtils.normalizePaginated(data);
                    this.menus = items;
                    this.meta = meta;
                })
                .catch((err) => {
                    console.error('fetchData error:', err);
                    window.alertError('Gagal memuat data menu.');
                })
                .finally(() => this.loading = false);
        },

        openCreate() {
            this.isEdit = false;
            this.errors = {};
            this.form = { id: null, name: '', link: '', link_alias: '', parent_id: '', icon_id: '', icon_class: '', order: 0, is_active: true };
            this.showFormModal = true;
        },

        openEdit(menu) {
            this.isEdit = true;
            this.errors = {};
            this.form = {
                id: menu.id,
                name: menu.name,
                link: menu.link ?? '',
                link_alias: menu.link_alias ?? '',
                parent_id: menu.parent?.id ?? '',
                icon_id: menu.icon?.id ?? '',
                icon_class: menu.icon?.value ?? '',
                order: menu.order ?? 0,
                is_active: menu.is_active,
            };
            this.showFormModal = true;
        },


        async submitForm() {
            this.submitting = true;
            this.errors = {};

            const url = this.isEdit ? `/menus/${this.form.id}` : '/menus';
            const method = this.isEdit ? 'PUT' : 'POST';

            try {
                const result = await apiUtils.apiFetch(url, {
                    method,
                    body: JSON.stringify({
                        name: this.form.name,
                        link: this.form.link || null,
                        link_alias: this.form.link_alias || null,
                        parent_id: this.form.parent_id || null,
                        icon_id: this.form.icon_id || null,
                        order: this.form.order,
                        is_active: this.form.is_active,
                    }),
                });

                if (result.validationError) {
                    this.errors = result.errors;
                    window.alertError(result.message);
                    return;
                }

                this.showFormModal = false;
                window.alertSuccess(result.data?.message || (this.isEdit ? 'Menu berhasil diperbarui.' : 'Menu berhasil ditambahkan.'));
                this.fetchData();
            } catch (err) {
                window.alertError(err.message || 'Gagal menyimpan data.');
            } finally {
                this.submitting = false;
            }
        },
    };
};
