window.apiUtils = (function () {
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    /**
     * Wrapper fetch dengan header standar (JSON + CSRF) dan
     * penanganan error konsisten: 422 dikembalikan sebagai
     * { validationError: true, errors, message }, error lain di-throw.
     */
    async function apiFetch(url, options = {}) {
        const res = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                ...(options.headers || {}),
            },
        });

        if (res.status === 422) {
            const body = await res.json().catch(() => ({}));
            return {
                ok: false,
                validationError: true,
                errors: body.errors ?? {},
                message: body.message || 'Periksa kembali input yang Anda masukkan.',
            };
        }

        if (!res.ok) {
            const body = await res.json().catch(() => null);
            throw new Error(body?.message || `Permintaan gagal (status ${res.status}).`);
        }

        const body = await res.json().catch(() => null);
        return { ok: true, data: body };
    }

    return { apiFetch, csrfToken };
})();
