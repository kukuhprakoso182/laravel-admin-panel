class AlertManager {
    constructor() {
        this.containerId = 'section_alert';
        this.defaultDuration = 4000; // auto-dismiss (ms), 0 = tidak auto-dismiss

        this.typeMap = {
            success: {
                wrapper: 'bg-teal-50 border-teal-200 text-teal-800',
                icon: 'text-teal-500',
                iconPath: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z',
            },
            error: {
                wrapper: 'bg-red-50 border-red-200 text-red-800',
                icon: 'text-red-500',
                iconPath: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z',
            },
            warning: {
                wrapper: 'bg-yellow-50 border-yellow-200 text-yellow-800',
                icon: 'text-yellow-500',
                iconPath: 'M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 8a1 1 0 100-2 1 1 0 000 2z',
            },
            info: {
                wrapper: 'bg-blue-50 border-blue-200 text-blue-800',
                icon: 'text-blue-500',
                iconPath: 'M18 10A8 8 0 11 2 10a8 8 0 0116 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
            },
        };

        this.init();
    }

    init() {
        this.ensureContainer();
        this.exposeToWindow();
    }

    ensureContainer() {
        let container = document.getElementById(this.containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = this.containerId;
            container.className = 'fixed top-4 right-4 z-50 flex flex-col gap-y-2 w-full max-w-sm pointer-events-none drop-shadow-2xl';
            document.body.appendChild(container);
        }
        return container;
    }

    show(type, message, options = {}) {
        const config = this.typeMap[type] || this.typeMap.info;
        const duration = options.duration ?? this.defaultDuration;
        const container = this.ensureContainer();

        const id = `alert-${Date.now()}-${Math.floor(Math.random() * 1000)}`;

        const el = document.createElement('div');
        el.id = id;
        el.className = `js-alert pointer-events-auto flex items-start gap-x-3 p-4 border rounded-lg shadow-sm ${config.wrapper}`;
        el.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0 mt-0.5 ${config.icon}">
                <path fill-rule="evenodd" d="${config.iconPath}" clip-rule="evenodd" />
            </svg>
            <div class="flex-1 text-sm">${message}</div>
            <button type="button" class="js-alert-close shrink-0 cursor-pointer opacity-60 hover:opacity-100" data-alert-close="${id}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        `;

        container.appendChild(el);

        el.querySelector('.js-alert-close').addEventListener('click', () => this.dismiss(id));

        // trigger transisi masuk
        requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('is-open')));

        if (duration > 0) {
            setTimeout(() => this.dismiss(id), duration);
        }

        return id;
    }

    dismiss(id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.classList.remove('is-open');
        setTimeout(() => el.remove(), 200); // samakan dengan durasi transition CSS
    }

    success(message, options = {}) {
        return this.show('success', message, options);
    }

    error(message, options = {}) {
        return this.show('error', message, options);
    }

    warning(message, options = {}) {
        return this.show('warning', message, options);
    }

    info(message, options = {}) {
        return this.show('info', message, options);
    }

    exposeToWindow() {
        window.showAlert = (type, message, options) => this.show(type, message, options);
        window.alertSuccess = (message, options) => this.success(message, options);
        window.alertError = (message, options) => this.error(message, options);
        window.alertWarning = (message, options) => this.warning(message, options);
        window.alertInfo = (message, options) => this.info(message, options);
    }

    static getInstance() {
        if (!AlertManager.instance) {
            AlertManager.instance = new AlertManager();
        }
        return AlertManager.instance;
    }
}

const alertManager = AlertManager.getInstance();

if (typeof module !== 'undefined' && module.exports) {
    module.exports = AlertManager;
}

document.addEventListener('DOMContentLoaded', () => {
    window.alertManager = AlertManager.getInstance();
});
