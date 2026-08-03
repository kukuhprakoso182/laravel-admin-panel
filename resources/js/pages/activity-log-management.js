window.activityLogManagement = function () {
    return {
        logs: [],
        meta: {},
        loading: false,
        exporting: false,
        showDetailModal: false,
        selectedLog: null,
        filters: { search: '', event: '', page: 1 },

        init() {
            this.fetchData();
        },

        fetchData() {
            this.loading = true;
            const params = formatUtils.buildQueryParams(this.filters);

            fetch(`/activity-logs/data?${params}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    const { items, meta } = tableUtils.normalizePaginated(data);
                    this.logs = items;
                    this.meta = meta;
                })
                .catch((err) => {
                    console.error('fetchData error:', err);
                    window.alertError('Gagal memuat data log aktivitas.');
                })
                .finally(() => this.loading = false);
        },

        async openDetail(row) {
            try {
                const res = await fetch(`/activity-logs/${row.id}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Gagal memuat detail.');
                this.selectedLog = await res.json();
                this.showDetailModal = true;
            } catch (err) {
                console.error('openDetail error:', err);
                window.alertError(err.message || 'Gagal memuat detail aktivitas.');
            }
        },

        async exportData() {
            this.exporting = true;

            try {
                const params = formatUtils.buildQueryParams({ search: this.filters.search, event: this.filters.event });
                const res = await fetch(`/activity-logs/export?${params}`, {
                    headers: { 'X-CSRF-TOKEN': apiUtils.csrfToken() },
                });

                if (!res.ok) {
                    const body = await res.json().catch(() => null);
                    throw new Error(body?.message || 'Gagal mengekspor data.');
                }

                const blob = await res.blob();
                const disposition = res.headers.get('Content-Disposition') || '';
                const match = disposition.match(/filename="?([^"]+)"?/);
                const filename = match ? match[1] : 'activity-logs.csv';

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

        formatDate: formatUtils.formatDate,
    };
};
