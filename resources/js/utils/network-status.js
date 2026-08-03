window.networkStatus = function () {
    return {
        isOnline: navigator.onLine,
        checking: false,
        pingInterval: null,
        pingUrl: '/up',// or 'https://www.google.com/generate_204',

        init() {
            window.addEventListener('online', () => this.handleBrowserOnline());
            window.addEventListener('offline', () => this.setStatus(false));

            // Ping berkala untuk validasi koneksi internet SESUNGGUHNYA,
            // bukan cuma status adapter jaringan (navigator.onLine bisa
            // "true" walau WiFi terhubung tapi tanpa akses internet).
            this.pingInterval = setInterval(() => this.checkConnection(), 15000);
            this.checkConnection();
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
            if (this.pingInterval) clearInterval(this.pingInterval);
        },
    };
};
