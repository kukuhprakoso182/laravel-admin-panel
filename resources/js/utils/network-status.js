window.networkStatus = function () {
    return {
        isOnline: navigator.onLine,
        checking: false,
        pingInterval: null,
        pingUrl: '/up', // '/up' or 'https://www.google.com/generate_204',

        // Simpan referensi function yang sudah di-bind supaya bisa
        // di-removeEventListener persis dengan reference yang sama.
        _onOnline: null,
        _onOffline: null,
        _onVisibilityChange: null,
        _domObserver: null,

        init() {
            this._onOnline = () => this.handleBrowserOnline();
            this._onOffline = () => this.setStatus(false);
            this._onVisibilityChange = () => this.handleVisibilityChange();

            window.addEventListener('online', this._onOnline);
            window.addEventListener('offline', this._onOffline);
            document.addEventListener('visibilitychange', this._onVisibilityChange);

            this.startPolling();
            this.checkConnection();

            // Auto-cleanup kalau elemen root (x-data) ini dilepas dari DOM
            // tanpa full page reload -- misal dipasang di dalam modal/partial
            // yang di-toggle x-if, bukan di layout root yang cuma hilang saat
            // navigasi penuh. Ini murni DOM API (bukan bergantung fitur
            // internal Alpine), jadi aman dipakai di mana pun component ini
            // dipasang.
            this._domObserver = new MutationObserver(() => {
                if (!this.$el.isConnected) {
                    this.destroy();
                }
            });
            this._domObserver.observe(document.body, { childList: true, subtree: true });
        },

        startPolling() {
            if (this.pingInterval || document.hidden) return;
            this.pingInterval = setInterval(() => this.checkConnection(), 15000);
        },

        stopPolling() {
            if (this.pingInterval) {
                clearInterval(this.pingInterval);
                this.pingInterval = null;
            }
        },

        handleVisibilityChange() {
            if (document.hidden) {
                // Tab di background: hentikan polling, hemat baterai/network.
                this.stopPolling();
            } else {
                // Tab aktif lagi: lanjutkan polling + langsung verifikasi
                // (jangan tunggu sampai interval berikutnya, karena status
                // internet bisa saja sudah berubah selama tab tidak aktif).
                this.startPolling();
                this.checkConnection();
            }
        },

        async handleBrowserOnline() {
            // Event 'online' browser kadang optimis (baru connect ke WiFi
            // tapi belum tentu ada internet) -> tetap verifikasi ke server.
            await this.checkConnection();
        },

        async checkConnection() {
            if (this.checking) return;
            this.checking = true;

            try {
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 5000);

                await fetch(this.pingUrl, {
                    method: 'HEAD',
                    mode: 'no-cors',
                    cache: 'no-store',
                    signal: controller.signal,
                });

                clearTimeout(timeout);
                this.setStatus(true);   // tidak throw = request berhasil sampai server = online
            } catch (err) {
                this.setStatus(false);  // throw = network error/timeout = offline
            } finally {
                this.checking = false;
            }
        },

        setStatus(status) {
            this.isOnline = status;
        },

        destroy() {
            this.stopPolling();

            window.removeEventListener('online', this._onOnline);
            window.removeEventListener('offline', this._onOffline);
            document.removeEventListener('visibilitychange', this._onVisibilityChange);

            if (this._domObserver) {
                this._domObserver.disconnect();
                this._domObserver = null;
            }
        },
    };
};
