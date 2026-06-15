const CACHE_NAME = 'inventory-deck-cache-v1';
const ASSETS_TO_CACHE = [
  'dashboard.php',
  'https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap',
  'https://cdn-icons-png.flaticon.com/512/564/564619.png'
];

// Install Service Worker and cache essential layouts
self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Activate handler
self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

// Intercept network requests to serve layout when offline
self.addEventListener('fetch', (event) => {
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request);
    })
  );
});

/* --- MESSAGE ROUTING ENGINE FOR STOCK PLACEMENT --- */
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'TRIGGER_NOTIFICATION') {
    const payload = event.data.data;
    const title = payload.title || 'Stock Level Alert';
    const options = {
      body: payload.body || 'An item has dropped below its limit!',
      icon: payload.icon || 'https://cdn-icons-png.flaticon.com/512/564/564619.png',
      badge: payload.icon || 'https://cdn-icons-png.flaticon.com/512/564/564619.png',
      requireInteraction: true,
      tag: 'stock-alert-sync'
    };
    event.waitUntil(
      self.registration.showNotification(title, options)
    );
  }
});

/* --- BACKGROUND PUSH NOTIFICATIONS --- */
self.addEventListener('push', (event) => {
  let title = 'Stock Level Alert';
  let options = {
    body: 'An item in your inventory deck has dropped below its alert limit!',
    icon: 'https://cdn-icons-png.flaticon.com/512/564/564619.png',
    badge: 'https://cdn-icons-png.flaticon.com/512/564/564619.png'
  };

  if (event.data) {
    try {
      const data = event.data.json();
      title = data.title || title;
      options.body = data.body || options.body;
    } catch (e) {
      options.body = event.data.text();
    }
  }

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Handle notification click tracking back to secure domain layout bounds
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if (client.url.includes('inventory.free.je') && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow('https://inventory.free.je/dashboard.php');
      }
    })
  );
});