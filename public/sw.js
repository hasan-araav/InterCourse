// Version: 1.3
self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(clients.claim());
});

self.addEventListener('push', function (event) {
    console.log('[Service Worker] Push Received.');

    if (!(self.Notification && self.Notification.permission === 'granted')) {
        console.error('[Service Worker] Notification permission not granted.');
        return;
    }

    if (!event.data) {
        console.error('[Service Worker] Push event has no data.');
        return;
    }

    let payload;
    try {
        payload = event.data.json();
        console.log('[Service Worker] Push Payload:', payload);
    } catch (e) {
        console.warn('[Service Worker] Push data is not JSON, using as text.');
        payload = { body: event.data.text() };
    }

    const title = payload.title || 'Workshop Update';
    const options = {
        body: payload.body || 'You have a new update.',
        icon: payload.icon || 'https://ui-avatars.com/api/?name=I&background=6366f1&color=fff',
        badge: payload.badge || 'https://ui-avatars.com/api/?name=I&background=6366f1&color=fff',
        data: payload.data || {},
        actions: payload.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
            .then(() => console.log('[Service Worker] Notification displayed successfully.'))
            .catch(err => console.error('[Service Worker] Failed to display notification:', err))
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const url = event.notification.data.url;

    if (url) {
        event.waitUntil(
            clients.matchAll({ type: 'window' }).then(windowClients => {
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
        );
    }
});
