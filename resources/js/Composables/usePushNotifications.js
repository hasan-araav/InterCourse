import { ref, onMounted } from 'vue';
import axios from 'axios';

export function usePushNotifications() {
    const isSubscribed = ref(false);
    const permission = ref(typeof Notification !== 'undefined' ? Notification.permission : 'default');

    const urlBase64ToUint8Array = (base64String) => {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    };

    const registerServiceWorker = async () => {
        if (!('serviceWorker' in navigator)) {
            console.error('Service workers are not supported in this browser.');
            return null;
        }
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });
            console.log('Service worker registered:', registration);

            // Force update if there's a new version
            await registration.update();

            return registration;
        } catch (e) {
            console.error('Service worker registration failed:', e);
            return null;
        }
    };

    const subscribeUser = async () => {
        try {
            const registration = await registerServiceWorker();
            if (!registration) {
                alert('Failed to register service worker. Please ensure you are using a secure context (HTTPS or localhost).');
                return;
            }

            const response = await axios.get(route('notifications.vapid-key'));
            const vapidPublicKey = response.data.publicKey;

            if (!vapidPublicKey) {
                console.error('VAPID public key not found.');
                return;
            }

            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            });

            await axios.post(route('notifications.subscribe'), subscription.toJSON());
            isSubscribed.value = true;
            permission.value = Notification.permission;
        } catch (e) {
            console.error('Failed to subscribe user:', e);
            if (e.name === 'NotAllowedError') {
                alert('Notification permission was denied. Please enable it in your browser settings.');
            } else {
                alert('An error occurred while enabling notifications: ' + e.message);
            }
        }
    };

    const unsubscribeUser = async () => {
        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            if (subscription) {
                await subscription.unsubscribe();
                await axios.post(route('notifications.unsubscribe'), {
                    endpoint: subscription.endpoint
                });
                isSubscribed.value = false;
            }
        } catch (e) {
            console.error('Failed to unsubscribe user:', e);
        }
    };

    const checkSubscription = async () => {
        if (!('serviceWorker' in navigator)) return;

        try {
            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.getSubscription();
            isSubscribed.value = !!subscription;
            permission.value = Notification.permission;
        } catch (e) {
            console.error('Error checking subscription:', e);
        }
    };

    const requestPermission = async () => {
        if (typeof Notification === 'undefined') {
            alert('Notifications are not supported in this browser.');
            return;
        }

        if (Notification.permission === 'denied') {
            alert('Notification permission is blocked in your browser settings. Please enable it to receive updates.');
            return;
        }

        try {
            const result = await Notification.requestPermission();
            permission.value = result;
            if (result === 'granted') {
                await subscribeUser();
            }
        } catch (e) {
            console.error('Error requesting permission:', e);
        }
    };

    onMounted(async () => {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            await registerServiceWorker();
            await checkSubscription();
        }
    });

    return {
        isSubscribed,
        permission,
        requestPermission,
        unsubscribeUser
    };
}
