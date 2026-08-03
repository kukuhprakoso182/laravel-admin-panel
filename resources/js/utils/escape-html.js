/**
 * Escape string sebelum disisipkan ke innerHTML, mencegah XSS.
 * Contoh: escapeHtml('<img src=x onerror=alert(1)>')
 *   -> '&lt;img src=x onerror=alert(1)&gt;'
 */
export function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

// Expose ke window supaya bisa dipakai langsung dari Blade/onclick inline
// tanpa perlu import di setiap file (opsional, sesuaikan kebutuhan).
window.escapeHtml = escapeHtml;
