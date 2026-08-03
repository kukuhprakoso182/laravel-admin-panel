window.tableUtils = (function () {
    function normalizePaginated(data) {
        return {
            items: data.data ?? [],
            meta: data.meta ?? {
                total: data.total, from: data.from, to: data.to, last_page: data.last_page,
            },
        };
    }

    return { normalizePaginated };
})();
