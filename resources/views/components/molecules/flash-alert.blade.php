@php
    $flashAlerts = collect();

    if (session()->has('alert')) {
        $flashAlerts->push(session('alert'));
    }

    if (session()->has('alerts')) {
        $flashAlerts = $flashAlerts->merge(session('alerts'));
    }

    // dukung juga pola umum Laravel: session('success'), session('error'), dst.
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (session()->has($type)) {
            $flashAlerts->push(['type' => $type, 'message' => session($type)]);
        }
    }
@endphp

@if($flashAlerts->isNotEmpty())
    <script>
        (function () {
            const alerts = @json($flashAlerts->map(fn($a) => [
                'type' => $a['type'] ?? 'info',
                'message' => $a['message'] ?? '',
            ]));

            function pushFlashAlerts() {
                alerts.forEach(({ type, message }) => {
                    window.alertManager.show(type, message);
                });
            }

            function waitForAlertManager(retries = 40) {
                if (window.alertManager) {
                    pushFlashAlerts();
                } else if (retries > 0) {
                    setTimeout(() => waitForAlertManager(retries - 1), 50);
                } else {
                    console.warn('alertManager tidak pernah siap — flash alert dibatalkan.');
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => waitForAlertManager());
            } else {
                waitForAlertManager();
            }
        })();
    </script>
@endif
