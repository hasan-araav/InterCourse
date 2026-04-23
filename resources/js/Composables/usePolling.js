import { onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

export function usePolling(interval = 10000) {
    let pollingTimer = null;

    const startPolling = () => {
        pollingTimer = setInterval(() => {
            // Re-fetch the current page data without a full page reload
            router.reload({ 
                preserveScroll: true,
                preserveState: true,
                only: ['workshops'] // Only refresh the workshops data
            });
        }, interval);
    };

    const stopPolling = () => {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    };

    onMounted(() => {
        // We only start polling if Echo (WebSockets) is not connected
        // This assumes Laravel Echo is set up globally
        if (!window.Echo || !window.Echo.connector.pusher.connection.state === 'connected') {
            startPolling();
        }
    });

    onUnmounted(() => {
        stopPolling();
    });

    return {
        startPolling,
        stopPolling
    };
}
