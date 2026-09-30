class DialogManager {
    constructor() {
        this.DIALOG_BASIC = "DIALOG_BASIC";
        this.DIALOG_CONFIRM = "DIALOG_CONFIRM";
        this.sectionDialogId = 'section_dialog';
        this.closeDuration = 200; // samakan dengan durasi transition di CSS

        this.colorMap = {
            slate: {
                icon: 'text-slate-600 bg-slate-100',
                button: 'bg-slate-600 hover:bg-slate-700',
            },
            red: {
                icon: 'text-red-600 bg-red-100',
                button: 'bg-red-600 hover:bg-red-700',
            },
            blue: {
                icon: 'text-blue-600 bg-blue-100',
                button: 'bg-blue-600 hover:bg-blue-700',
            },
            green: {
                icon: 'text-teal-600 bg-teal-100',
                button: 'bg-teal-600 hover:bg-teal-700',
            },
        };

        this.init();
    }

    /* ------------------------------------------------------------------ */
    /* Keamanan: escaping & validasi                                       */
    /* ------------------------------------------------------------------ */

    static escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Tandai string HTML sebagai tepercaya. HANYA pakai untuk HTML yang
    // seluruh bagian dinamisnya sudah di-escape (lihat confirmDeleteDialog).
    static trusted(html) {
        return { __html: String(html ?? '') };
    }

    // Default: escape. HTML hanya lolos jika dibungkus DialogManager.trusted().
    renderText(value) {
        if (value && typeof value === 'object' && '__html' in value) {
            return value.__html;
        }
        return DialogManager.escapeHtml(value);
    }

    // Batasi redirect ke same-origin (cegah open redirect & URL javascript:)
    static safeRedirect(url) {
        try {
            const parsed = new URL(url, window.location.origin);
            const isHttp = parsed.protocol === 'http:' || parsed.protocol === 'https:';
            return isHttp && parsed.origin === window.location.origin ? parsed.href : null;
        } catch {
            return null;
        }
    }

    static csrfHeaders() {
        return {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        };
    }

    /* ------------------------------------------------------------------ */
    /* Inisialisasi                                                        */
    /* ------------------------------------------------------------------ */

    init() {
        this.ensureSectionDialog();

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.handleEscapeKey();
        });

        this.exposeToWindow();
    }

    // Buat container-nya sendiri kalau belum ada di DOM
    ensureSectionDialog() {
        let section = document.getElementById(this.sectionDialogId);
        if (!section) {
            section = document.createElement('div');
            section.id = this.sectionDialogId;
            document.body.appendChild(section);
        }
        return section;
    }

    /* ------------------------------------------------------------------ */
    /* Render dialog                                                       */
    /* ------------------------------------------------------------------ */

    generateDialog(type, title, message, actionText = '', bgAction = 'slate', callbackAction = null) {
        if (type !== this.DIALOG_BASIC && type !== this.DIALOG_CONFIRM) {
            console.warn('Dialog type is not defined');
            return;
        }

        const sectionDialog = this.ensureSectionDialog();
        this.clearSectionDialog();

        sectionDialog.innerHTML = this.buildDialogHTML(type, title, message, actionText, bgAction);

        if (type === this.DIALOG_CONFIRM && callbackAction) {
            this.attachConfirmListener(callbackAction, type);
        }

        this.attachCommonListeners(type);
    }

    clearSectionDialog() {
        const sectionDialog = document.getElementById(this.sectionDialogId);
        if (sectionDialog) sectionDialog.innerHTML = '';
    }

    buildDialogHTML(type, title, message, actionText, bgAction) {
        const colors = Object.hasOwn(this.colorMap, bgAction)
            ? this.colorMap[bgAction]
            : this.colorMap.slate;
        const isConfirm = type === this.DIALOG_CONFIRM;

        return `
            <dialog id="${type}" class="js-dialog w-full max-w-md rounded-xl shadow-xl p-0 m-auto border border-gray-200">
                <div class="flex justify-between items-center gap-x-3 p-4 border-b border-gray-200">
                    <div class="flex items-center gap-x-3">
                        ${isConfirm ? `
                            <span class="flex items-center justify-center size-9 rounded-full shrink-0 ${colors.icon}">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        ` : ''}
                        <h3 class="text-lg font-semibold text-gray-800">${this.renderText(title)}</h3>
                    </div>
                    <button type="button" data-dialog-close="${type}"
                            class="js-dialog-close shrink-0 cursor-pointer text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>

                <div class="p-4 md:p-5">
                    <p class="text-sm text-gray-600">${this.renderText(message)}</p>
                </div>

                <div class="flex justify-end gap-x-2 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" data-dialog-close="${type}"
                            class="js-dialog-close py-2.5 px-4 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 cursor-pointer">
                        Close
                    </button>
                    ${isConfirm ? `
                        <button type="button" id="confirmActionButton"
                                class="py-2.5 px-4 text-sm font-medium rounded-lg text-white cursor-pointer transition-colors disabled:opacity-60 disabled:cursor-not-allowed ${colors.button}">
                            ${this.renderText(actionText)}
                        </button>
                    ` : ''}
                </div>
            </dialog>
        `;
    }

    attachConfirmListener(callbackAction, type) {
        const confirmButton = document.getElementById('confirmActionButton');
        if (!confirmButton) return;

        confirmButton.addEventListener('click', () => {
            if (confirmButton.disabled) return; // cegah klik ganda
            confirmButton.disabled = true;

            callbackAction();
            this.closeDialog(type);
        });
    }

    attachCommonListeners(type) {
        const dialog = document.getElementById(type);
        if (!dialog) return;

        // Tombol close (header + footer)
        dialog.querySelectorAll('.js-dialog-close').forEach((btn) => {
            btn.addEventListener('click', () => this.closeDialog(type));
        });

        // Klik backdrop -> close
        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) this.closeDialog(type);
        });

        // Trigger transisi masuk (lihat CSS)
        requestAnimationFrame(() => {
            if (!dialog.isConnected) return; // dialog sudah diganti sebelum sempat tampil
            dialog.showModal();
            requestAnimationFrame(() => dialog.classList.add('is-open'));
        });
    }

    /* ------------------------------------------------------------------ */
    /* API publik                                                          */
    /* ------------------------------------------------------------------ */

    showBasicDialog(title, message) {
        this.generateDialog(this.DIALOG_BASIC, title, message);
    }

    showConfirmDialog(title, message, actionText, callbackAction, bgAction = 'slate') {
        this.generateDialog(this.DIALOG_CONFIRM, title, message, actionText, bgAction, callbackAction);
    }

    closeDialog(id) {
        const dialog = document.getElementById(id);
        if (!dialog) return;

        dialog.classList.remove('is-open');
        setTimeout(() => {
            if (dialog.open) dialog.close();
            // Hapus dialog ini saja, bukan seluruh section,
            // supaya dialog baru yang muncul di sela waktu tidak ikut terhapus.
            dialog.remove();
        }, this.closeDuration);
    }

    handleEscapeKey() {
        const openDialog = document.querySelector('dialog[open]');
        if (openDialog) this.closeDialog(openDialog.id);
    }

    confirmLogoutDialog() {
        this.showConfirmDialog(
            'Logout',
            'Apakah anda ingin logout aplikasi ini ?',
            'Logout',
            () => { document.getElementById('logout-form').submit(); },
            'slate'
        );
    }

    confirmDeleteDialog(dataDelete, deleteUrl, options = {}) {
        this.showConfirmDialog(
            'Delete',
            DialogManager.trusted(
                `Apakah anda ingin delete data <strong>${DialogManager.escapeHtml(dataDelete)}</strong> ?`
            ),
            'Delete',
            () => this.performDelete(deleteUrl, options),
            'red'
        );
    }

    confirmResetPasswordDialog(data, resetPasswordUrl, options = {}) {
        this.showConfirmDialog(
            'Reset Password',
            DialogManager.trusted(
                `Apakah anda ingin reset password <strong>${DialogManager.escapeHtml(data)}</strong> ?`
            ),
            'Reset Password',
            () => this.performAction(resetPasswordUrl, options),
            'red'
        );
    }

    confirmChangeStatusDialog(data, desStatusName, changeStatusUrl, options = {}) {
        this.showConfirmDialog(
            'Change Status',
            DialogManager.trusted(
                `Apakah anda ingin ${DialogManager.escapeHtml(desStatusName)}-kan <strong>${DialogManager.escapeHtml(data)}</strong> ?`
            ),
            desStatusName, // otomatis di-escape oleh renderText
            () => this.performAction(changeStatusUrl, options),
            'red'
        );
    }

    /* ------------------------------------------------------------------ */
    /* Eksekusi aksi (semua lewat fetch + CSRF)                            */
    /* ------------------------------------------------------------------ */

    static async extractErrorMessage(res, fallback) {
        const body = await res.json().catch(() => null);
        if (body?.errors) return Object.values(body.errors).flat().join(' ');
        return body?.message || fallback;
    }

    finishRequest({ redirectTo = null, onSuccess = null } = {}) {
        if (typeof onSuccess === 'function') {
            onSuccess();
            return;
        }

        const target = redirectTo ? DialogManager.safeRedirect(redirectTo) : null;
        if (target) {
            window.location.href = target;
        } else {
            window.location.reload();
        }
    }

    performDelete(deleteUrl, options = {}) {
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: DialogManager.csrfHeaders(),
        })
            .then(async (res) => {
                if (!res.ok) {
                    window.alertError(await DialogManager.extractErrorMessage(res, 'Gagal menghapus data'));
                    return; // STOP, jangan lanjut ke onSuccess
                }

                this.finishRequest(options);
                window.alertSuccess('Berhasil menghapus data');
            })
            .catch((err) => {
                this.showBasicDialog('Error', err.message);
            });
    }

    // Helper generik untuk aksi non-delete (reset password, change status, dll).
    // Default POST + CSRF. Gunakan options.method ('PATCH', 'PUT', dst.) bila perlu.
    // PENTING: route di server tidak boleh lagi menerima GET untuk aksi ini.
    performAction(url, { method = 'POST', redirectTo = null, onSuccess = null } = {}) {
        fetch(url, {
            method: method.toUpperCase(),
            headers: DialogManager.csrfHeaders(),
        })
            .then(async (res) => {
                if (!res.ok) {
                    throw new Error(await DialogManager.extractErrorMessage(res, 'Aksi gagal dijalankan.'));
                }
                this.finishRequest({ redirectTo, onSuccess });
            })
            .catch((err) => {
                this.showBasicDialog('Error', err.message);
            });
    }

    createCustomDialog(config) {
        const { type = this.DIALOG_CONFIRM, title, message, actionText = 'OK', bgAction = 'slate', callback = null } = config;

        if (type === this.DIALOG_BASIC) {
            this.showBasicDialog(title, message);
        } else {
            this.showConfirmDialog(title, message, actionText, callback, bgAction);
        }
    }

    exposeToWindow() {
        // Pastikan escapeHtml tersedia untuk file lain (tidak menimpa jika sudah ada)
        window.escapeHtml = window.escapeHtml || DialogManager.escapeHtml;
        window.trustedHtml = DialogManager.trusted;

        window.showBasicDialog = (title, message) => this.showBasicDialog(title, message);
        window.showConfirmDialog = (title, message, actionText, callbackAction, bgAction) =>
            this.showConfirmDialog(title, message, actionText, callbackAction, bgAction);
        window.closeDialog = (id) => this.closeDialog(id);
        window.confirmLogoutDialog = () => this.confirmLogoutDialog();
        window.confirmDeleteDialog = (dataDelete, deleteUrl, options) => this.confirmDeleteDialog(dataDelete, deleteUrl, options);
        window.confirmResetPasswordDialog = (data, resetPasswordUrl, options) => this.confirmResetPasswordDialog(data, resetPasswordUrl, options);
        window.confirmChangeStatusDialog = (data, desStatusName, changeStatusUrl, options) => this.confirmChangeStatusDialog(data, desStatusName, changeStatusUrl, options);
    }

    static getInstance() {
        if (!DialogManager.instance) {
            DialogManager.instance = new DialogManager();
        }
        return DialogManager.instance;
    }
}

const dialogManager = DialogManager.getInstance();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = DialogManager;
}

document.addEventListener('DOMContentLoaded', () => {
    window.dialogManager = DialogManager.getInstance();
});