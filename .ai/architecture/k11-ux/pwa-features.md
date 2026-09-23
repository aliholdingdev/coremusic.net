---
title: "Progressive Web App Özellikleri"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# Progressive Web App Özellikleri

## Genel Bakış

PWA, COREMUSIC'i native uygulama deneyimi sunan bir web uygulamasına dönüştürür. Service Worker, Web App Manifest, offline destek ve push notification desteği ile masaüstü ve mobilde kurulum gerektirmeyen bir deneyim sağlar.

## Mimari Yapı

```
pwa/
├── manifest.json          ← Web App Manifest
├── sw.js                  ← Service Worker (Workbox ile)
├── sw-register.js         ← Service Worker kayıt
├── offline.html           ← Offline fallback sayfası
├── icons/                 ← PWA ikonları
│   ├── icon-72x72.png
│   ├── icon-96x96.png
│   ├── icon-128x128.png
│   ├── icon-144x144.png
│   ├── icon-152x152.png
│   ├── icon-192x192.png
│   ├── icon-384x384.png
│   └── icon-512x512.png
└── screenshots/           ← Install prompt screenshots
    ├── wide.png
    └── narrow.png
```

## Web App Manifest

```json
// manifest.json
{
  "name": "COREMUSIC - Müzik Deneyimi",
  "short_name": "COREMUSIC",
  "description": "Kişiselleştirilmiş müzik deneyimi platformu",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#0D0D0D",
  "theme_color": "#7C3AED",
  "orientation": "any",
  "scope": "/",
  "lang": "tr",
  "dir": "ltr",
  "categories": ["music", "entertainment"],
  "icons": [
    {
      "src": "/icons/icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/icons/icon-96x96.png",
      "sizes": "96x96",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-128x128.png",
      "sizes": "128x128",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/icons/icon-384x384.png",
      "sizes": "384x384",
      "type": "image/png"
    },
    {
      "src": "/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ],
  "screenshots": [
    {
      "src": "/screenshots/wide.png",
      "sizes": "1280x720",
      "type": "image/png",
      "form_factor": "wide",
      "label": "COREMUSIC ana sayfa görünümü"
    },
    {
      "src": "/screenshots/narrow.png",
      "sizes": "720x1280",
      "type": "image/png",
      "form_factor": "narrow",
      "label": "COREMUSIC mobil görünüm"
    }
  ],
  "shortcuts": [
    {
      "name": "Son Çalanlar",
      "short_name": "Son Çalanlar",
      "url": "/recent",
      "icons": [{ "src": "/icons/recent.png", "sizes": "96x96" }]
    },
    {
      "name": "Favoriler",
      "short_name": "Favoriler",
      "url": "/favorites",
      "icons": [{ "src": "/icons/favorites.png", "sizes": "96x96" }]
    },
    {
      "name": "Rastgele Çal",
      "short_name": "Rastgele",
      "url": "/shuffle",
      "icons": [{ "src": "/icons/shuffle.png", "sizes": "96x96" }]
    }
  ],
  "share_target": {
    "action": "/share",
    "method": "POST",
    "enctype": "multipart/form-data",
    "params": {
      "files": [
        {
          "name": "audio",
          "accept": ["audio/*"]
        }
      ]
    }
  },
  "handle_links": "preferred",
  "launch_handler": {
    "client_mode": "navigate-existing"
  },
  "protocol_handlers": [
    {
      "protocol": "web+coremusic",
      "url": "/protocol?url=%s"
    }
  ]
}
```

## Service Worker

### Workbox Konfigürasyonu

```javascript
// sw-workbox.config.js
const { InjectManifest } = require('workbox-webpack-plugin');

module.exports = {
  webpackConfig: {
    plugins: [
      new InjectManifest({
        swSrc: './src/sw.js',
        swDest: 'sw.js',
        maximumFileSizeToCacheInBytes: 5 * 1024 * 1024,
        exclude: [
          /\.map$/,
          /^manifest.*\.js$/,
          /^_.*\.scss$/,
        ]
      })
    ]
  }
};
```

### Service Worker Kodu

```javascript
// sw.js
import { precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst, StaleWhileRevalidate, NetworkFirst } from 'workbox-strategies';
import { ExpirationPlugin } from 'workbox-expiration';
import { CacheableResponsePlugin } from 'workbox-cacheable-response';
import { BackgroundSyncPlugin } from 'workbox-background-sync';

// Precache
precacheAndRoute(self.__WB_MANIFEST);

// Runtime Cache - Images
registerRoute(
  ({ request }) => request.destination === 'image',
  new CacheFirst({
    cacheName: 'coremusic-images',
    plugins: [
      new CacheableResponsePlugin({ statuses: [0, 200] }),
      new ExpirationPlugin({
        maxEntries: 100,
        maxAgeSeconds: 30 * 24 * 60 * 60 // 30 gün
      })
    ]
  })
);

// Runtime Cache - Audio
registerRoute(
  ({ url }) => url.pathname.endsWith('.mp3') || url.pathname.endsWith('.ogg'),
  new CacheFirst({
    cacheName: 'coremusic-audio',
    plugins: [
      new CacheableResponsePlugin({ statuses: [0, 200] }),
      new ExpirationPlugin({
        maxEntries: 50,
        maxAgeSeconds: 7 * 24 * 60 * 60 // 7 gün
      })
    ]
  })
);

// Runtime Cache - API
registerRoute(
  ({ url }) => url.pathname.startsWith('/api/'),
  new NetworkFirst({
    cacheName: 'coremusic-api',
    plugins: [
      new CacheableResponsePlugin({ statuses: [0, 200] }),
      new ExpirationPlugin({
        maxEntries: 50,
        maxAgeSeconds: 5 * 60 // 5 dakika
      })
    ]
  })
);

// Runtime Cache - Fonts
registerRoute(
  ({ request }) => request.destination === 'font',
  new CacheFirst({
    cacheName: 'coremusic-fonts',
    plugins: [
      new CacheableResponsePlugin({ statuses: [0, 200] }),
      new ExpirationPlugin({
        maxEntries: 30,
        maxAgeSeconds: 365 * 24 * 60 * 60 // 1 yıl
      })
    ]
  })
);

// Runtime Cache - Styles & Scripts
registerRoute(
  ({ request }) =>
    request.destination === 'style' || request.destination === 'script',
  new StaleWhileRevalidate({
    cacheName: 'coremusic-static'
  })
);

// Background Sync - Offline actions
const bgSyncPlugin = new BackgroundSyncPlugin('coremusic-sync', {
  maxRetentionTime: 24 * 60 // 24 saat
});

registerRoute(
  ({ url }) => url.pathname.startsWith('/api/sync'),
  new NetworkFirst({
    plugins: [bgSyncPlugin]
  })
);

// Push Notification
self.addEventListener('push', (event) => {
  const data = event.data.json();
  const options = {
    body: data.body,
    icon: '/icons/icon-192x192.png',
    badge: '/icons/badge-72x72.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: data.primaryKey,
      url: data.url
    },
    actions: [
      { action: 'play', title: 'Çal', icon: '/icons/play.png' },
      { action: 'dismiss', title: 'Kapat', icon: '/icons/close.png' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'play') {
    event.waitUntil(
      clients.openWindow('/player')
    );
  } else if (event.action === 'dismiss') {
    // Kapat
  } else {
    event.waitUntil(
      clients.openWindow(event.notification.data.url || '/')
    );
  }
});

// Media Session
self.addEventListener('message', (event) => {
  if (event.data.type === 'MEDIA_SESSION_UPDATE') {
    // Media session güncellemesi
  }
});
```

### Service Worker Kayıt

```javascript
// sw-register.js
if ('serviceWorker' in navigator) {
  window.addEventListener('load', async () => {
    try {
      const registration = await navigator.serviceWorker.register('/sw.js', {
        scope: '/'
      });

      console.log('SW registered:', registration.scope);

      // Update check
      registration.addEventListener('updatefound', () => {
        const newWorker = registration.installing;
        newWorker.addEventListener('statechange', () => {
          if (newWorker.state === 'activated') {
            showUpdateNotification();
          }
        });
      });

    } catch (error) {
      console.error('SW registration failed:', error);
    }
  });
}

function showUpdateNotification() {
  const toast = document.createElement('div');
  toast.className = 'toast toast--info';
  toast.innerHTML = `
    <span>Yeni sürüm mevcut</span>
    <button class="toast__action" onclick="location.reload()">Güncelle</button>
    <button class="toast__close" onclick="this.parentElement.remove()">×</button>
  `;
  document.body.appendChild(toast);
}
```

## Offline Sayfa

```html
<!-- offline.html -->
<!DOCTYPE html>
<html lang="tr" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Çevrimdışı - COREMUSIC</title>
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: #0D0D0D;
      color: #F1F5F9;
      font-family: 'Inter', sans-serif;
      text-align: center;
    }
    .offline {
      max-width: 400px;
      padding: 2rem;
    }
    .offline__icon {
      font-size: 4rem;
      margin-bottom: 1rem;
    }
    .offline__title {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .offline__text {
      color: #94A3B8;
      margin-bottom: 1.5rem;
    }
    .offline__btn {
      background: #7C3AED;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
    }
    .offline__btn:hover {
      background: #6D28D9;
    }
  </style>
</head>
<body>
  <div class="offline">
    <div class="offline__icon">📡</div>
    <h1 class="offline__title">Çevrimdışısınız</h1>
    <p class="offline__text">
      İnternet bağlantınız yok. Lütfen bağlantınızı kontrol edin
      veya daha sonra tekrar deneyin.
    </p>
    <button class="offline__btn" onclick="location.reload()">
      Yeniden Dene
    </button>
  </div>
</body>
</html>
```

## Offline Fallback Stratejisi

```javascript
// sw.js içinde
const OFFLINE_URL = '/offline.html';

// Install event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open('coremusic-offline').then((cache) => {
      return cache.addAll([
        OFFLINE_URL,
        '/css/app.css',
        '/js/app.js',
        '/icons/icon-192x192.png'
      ]);
    })
  );
});

// Fetch event - offline fallback
self.addEventListener('fetch', (event) => {
  if (event.request.mode === 'navigate') {
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match(OFFLINE_URL);
      })
    );
  }
});
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| workbox-webpack-plugin | 7.0+ | SW build |
| workbox-precaching | 7.0+ | Precache |
| workbox-routing | 7.0+ | Route management |
| workbox-strategies | 7.0+ | Cache strategies |
| workbox-expiration | 7.0+ | Cache cleanup |
| workbox-background-sync | 7.0+ | Offline sync |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (Native-like experience)
