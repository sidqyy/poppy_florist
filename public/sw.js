self.addEventListener('push', function(event) {
    const data = event.data.json();
    const options = {
        body: data.body || 'Ada pesanan baru!',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: 'new-order',
        requireInteraction: true
    };
    event.waitUntil(
        self.registration.showNotification(data.title || 'Pesanan Masuk', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/kitchen')
    );
});