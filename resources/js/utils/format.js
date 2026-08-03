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

    return { formatDate, buildQueryParams };
})();
