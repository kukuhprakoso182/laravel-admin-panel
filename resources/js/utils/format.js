window.formatUtils = (function () {
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric',
        });
    }

    function buildQueryParams(params) {
        // buang key yang value-nya falsy/empty biar URL bersih (opsional, tapi enak buat query log)
        return new URLSearchParams(params);
    }

    // di dalam formatUtils object/module
    function formatCurrency(value, currency = 'IDR') {
        const number = Number(value) || 0;

        if (currency === 'USD') {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }).format(number);
        }

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(number);
    }

    /**
     * Menyeragamkan berbagai bentuk option (mis. {id, name}, {alat_id, nama_alat},
     * {value, label}) jadi selalu punya key value & label yang dibutuhkan
     * <x-atoms.select>, tanpa membuang field asli lainnya.
     *
     * @param {Array}  list       Array of object dari response API
     * @param {Array}  valueKeys  Kandidat nama kolom untuk value, dicoba berurutan
     * @param {Array}  labelKeys  Kandidat nama kolom untuk label, dicoba berurutan
     */
    function normalizeOptions(list, valueKeys = ['value', 'id'], labelKeys = ['label', 'name']) {
        return (list || []).map(item => {
            const valueKey = valueKeys.find(k => item[k] !== undefined);
            const labelKey = labelKeys.find(k => item[k] !== undefined);

            return {
                ...item,
                value: valueKey ? item[valueKey] : null,
                label: labelKey ? item[labelKey] : '',
            };
        });
    }

    return { formatDate, buildQueryParams, formatCurrency, normalizeOptions  };
})();
