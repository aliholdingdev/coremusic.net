---
title: "K10 PWA Features - Progressive Web App"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 PWA Features

## Genel Bakış

PWA modülü, COREMUSIC'in Progressive Web App özelliklerini yönetir. Service Worker kurulumu, offline destek, push notification, manifest yapılandırması ve install prompt gibi PWA bileşenlerini içerir. Kullanıcıların uygulamayı yerel uygulama gibi yüklemesini ve çevrimdışı çalışmasını sağlar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  📱 PWA Durumu                    [⚙ PWA Ayarları]     │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 📊       │  │  📊 PWA Durum Panosu                │    │
│ Status   │  │                                      │    │
│          │  │  ✅ Service Worker: Active            │    │
│ 🔧       │  │  ✅ Manifest: Loaded                 │    │
│ Service  │  │  ✅ HTTPS: Enabled                   │    │
│ Worker   │  │  ⚠️ Cache: 85% (2.4GB)              │    │
│          │  │  ❌ Push: Not Subscribed             │    │
│ 💾 Cache │  │                                      │    │
│          │  │  ┌─────────────────────────────────┐ │    │
│ 📦       │  │  │ 💾 Cache Yönetimi              │ │    │
│ Manifest │  │  │                                 │ │    │
│          │  │  │ Cache Stratejisi:               │ │    │
│ 🔔       │  │  │ ○ Cache First                  │ │    │
│ Push     │  │  │ ● Network First                │ │    │
│          │  │  │ ○ Stale While Revalidate       │ │    │
│ 📱       │  │  │ ○ Network Only                 │ │    │
│ Install  │  │  │                                 │ │    │
│          │  │  │ Cache Boyutu: 2.4 GB / 5 GB    │ │    │
│          │  │  │ Cached Files: 1,247            │ │    │
│          │  │  │ Last Sync: 5 dk önce           │ │    │
│          │  │  │                                 │ │    │
│          │  │  │ [🗑 Clear Cache] [🔄 Force Sync]│ │    │
│          │  │  └─────────────────────────────────┘ │    │
│          │  │                                     │    │
│          │  │  ┌─────────────────────────────────┐ │    │
│          │  │  │ 📦 Manifest İçeriği             │ │    │
│          │  │  │ name: COREMUSIC                │ │    │
│          │  │  │ short_name: CMusic             │ │    │
│          │  │  │ display: standalone            │ │    │
│          │  │  │ theme_color: #1a1a2e           │ │    │
│          │  │  │ background_color: #0f0f23      │ │    │
│          │  │  │ icons: 192px, 512px            │ │    │
│          │  │  └─────────────────────────────────┘ │    │
│          │  │                                     │    │
│          │  │  ┌─────────────────────────────────┐ │    │
│          │  │  │ 🔔 Push Notification           │ │    │
│          │  │  │ Durum: Abone Değil             │ │    │
│          │  │  │ [🔔 Push Bildirimlerini Aç]    │ │    │
│          │  │  │                                 │ │    │
│          │  │  │ Bildirim Tercihleri:           │ │    │
│          │  │  │ ☑ Yeni şarkı bildirimleri      │ │    │
│          │  │  │ ☑ Playlist güncelleme          │ │    │
│          │  │  │ ☐ Sistem bildirimleri          │ │    │
│          │  │  └─────────────────────────────────┘ │    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
PWAFeatures/
├── PWAManager.tsx                # Ana PWA yöneticisi
├── ServiceWorker/
│   ├── SWRegistration.tsx        # Service Worker kayıt
│   ├── SWUpdate.tsx              # SW güncelleme bildirimi
│   ├── SWStatus.tsx              # SW durum göstergesi
│   └── SWInstaller.tsx           # SW kurulumu
├── Cache/
│   ├── CacheManager.tsx          # Cache yöneticisi
│   ├── CacheStatus.tsx           # Cache durumu
│   ├── CacheSettings.tsx         # Cache ayarları
│   └── CacheCleaner.tsx          # Cache temizleme
├── Manifest/
│   ├── ManifestLoader.tsx        # Manifest yükleme
│   ├── ManifestEditor.tsx        # Manifest düzenleme
│   └── ManifestValidator.tsx     # Manifest doğrulama
├── Push/
│   ├── PushManager.tsx           # Push notification yöneticisi
│   ├── PushSubscribe.tsx         # Push abonelik
│   ├── PushSettings.tsx          # Push ayarları
│   └── PushHandler.tsx           # Push mesaj işleme
├── Install/
│   ├── InstallPrompt.tsx         # Yükleme istemi
│   ├── InstallBanner.tsx         # Yükleme banner'ı
│   └── InstallInstructions.tsx   # Yükleme talimatları
└── Shared/
    ├── OfflineIndicator.tsx      # Çevrimdışı göstergesi
    ├── SyncStatus.tsx            # Sync durumu
    └── NetworkStatus.tsx         # Ağ durumu
```

### State Management

```typescript
// PWA Store - Zustand
interface PWAState {
  // Service Worker
  swRegistration: ServiceWorkerRegistration | null;
  swState: 'installing' | 'waiting' | 'activating' | 'active' | 'redundant';
  swUpdateAvailable: boolean;

  // Cache
  cacheStatus: CacheStatus;
  cachedFiles: number;
  cacheSize: number;
  cacheStrategy: 'cache-first' | 'network-first' | 'stale-while-revalidate' | 'network-only';

  // Manifest
  manifest: Manifest | null;
  isInstallable: boolean;
  isInstalled: boolean;

  // Push
  pushPermission: NotificationPermission;
  isPushSubscribed: boolean;
  pushSubscription: PushSubscription | null;

  // Network
  isOnline: boolean;
  lastOnline: Date;

  // Actions
  registerServiceWorker: () => Promise<void>;
  updateServiceWorker: () => Promise<void>;
  clearCache: () => Promise<void>;
  setCacheStrategy: (strategy: string) => void;
  requestPushPermission: () => Promise<NotificationPermission>;
  subscribePush: () => Promise<void>;
  unsubscribePush: () => Promise<void>;
  sendPushNotification: (title: string, options: NotificationOptions) => void;
  installApp: () => Promise<boolean>;
  checkForUpdates: () => Promise<boolean>;
}
```

### Service Worker Stratejisi

```javascript
// sw.js - Service Worker yapısı
const CACHE_NAME = 'coremusic-v1';
const STATIC_CACHE = 'coremusic-static-v1';
const DYNAMIC_CACHE = 'coremusic-dynamic-v1';

// Install event
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => {
      return cache.addAll([
        '/',
        '/index.html',
        '/static/js/main.js',
        '/static/css/main.css',
        '/manifest.json'
      ]);
    })
  );
});

// Fetch event - Network First stratejisi
self.addEventListener('fetch', (event) => {
  if (event.request.url.includes('/api/')) {
    // API istekleri: Network First
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          const clone = response.clone();
          caches.open(DYNAMIC_CACHE).then((cache) => {
            cache.put(event.request, clone);
          });
          return response;
        })
        .catch(() => {
          return caches.match(event.request);
        })
    );
  } else {
    // Statik dosyalar: Cache First
    event.respondWith(
      caches.match(event.request).then((response) => {
        return response || fetch(event.request);
      })
    );
  }
});

// Activate event - Eski cache'leri temizle
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== STATIC_CACHE && name !== DYNAMIC_CACHE)
          .map((name) => caches.delete(name))
      );
    })
  );
});
```

### Cache Stratejileri

| Strateji | Kullanım | Avantaj |
|----------|----------|---------|
| Cache First | Statik dosyalar | Hızlı yükleme, offline |
| Network First | API çağrıları | Her zaman güncel veri |
| Stale While Revalidate | Dinamik içerik | Hız + güncellik dengesi |
| Network Only | Gerçek zamanlı veri | Her zaman taze |
| Cache Only | Offline-only içerik | Sadece cache'den |

### Push Notification

Web Push Notification entegrasyonu:
- **Permission**: Kullanıcı izni alma
- **Subscription**: Push subscription oluşturma
- **VAPID Keys**: Voluntary Application Server Identification
- **Payload**: JSON tabanlı push mesajı
- **Actions**: Bildirim eylemleri (aç, kapat, ertele)
- **Badge**: Badge ikonu güncelleme
- **Tag**: Benzer bildirimleri gruplama

### Manifest Yapılandırması

```json
{
  "name": "COREMUSIC - Müzik Platformu",
  "short_name": "COREMUSIC",
  "description": "Profesyonel müzik deneyimi",
  "start_url": "/",
  "display": "standalone",
  "orientation": "any",
  "theme_color": "#1a1a2e",
  "background_color": "#0f0f23",
  "icons": [
    {
      "src": "/icons/icon-192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/icons/icon-512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ],
  "screenshots": [
    {
      "src": "/screenshots/desktop.png",
      "sizes": "1920x1080",
      "type": "image/png",
      "form_factor": "wide"
    },
    {
      "src": "/screenshots/mobile.png",
      "sizes": "390x844",
      "type": "image/png",
      "form_factor": "narrow"
    }
  ],
  "categories": ["music", "entertainment"],
  "shortcuts": [
    {
      "name": "Müzik Çal",
      "url": "/music?action=play",
      "icons": [{ "src": "/icons/play.png", "sizes": "96x96" }]
    }
  ]
}
```

### Offline Desteği

Çevrimdışı çalışabilirlik:
- **Essential Data**: Kullanıcı tercihleri, playlist metadata
- **Cached Content**: Son dinlenen şarkılar, albüm kapakları
- **Offline Queue**: Çevrimdışı yapılan işlemler, online olunca sync
- **Background Sync**: Arka plan senkronizasyonu
- **Periodic Sync**: Periyodik arka plan güncellemesi

### Yükleme (Install) Deneyimi

Kullanıcı yükleme deneyimi:
- **BeforeInstallPrompt**: Custom install prompt yakalama
- **Install Banner**: Alt taraftan install banner'ı
- **iOS Instructions**: iOS için manuel yükleme talimatları
- **A2HS Prompt**: Add to Home Screen istemi
- **Deferred Prompt**: Prompt'u erteleme ve sonraki zamanda gösterme

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K6 | Network | Online/offline algılama |
| K0 | Browser API | Service Worker, Cache API |
| K0 | Dosya Sistemi | Cache depolama |
| K8 | Push Service | Push notification backend |
| K10 | Notification | In-app bildirim entegrasyonu |
| K10 | Theme Engine | Manifest tema renkleri |
| K10 | Tüm Paneller | Offline içerik stratejisi |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek
**Kapsam**: Service Worker, Cache, Manifest, Push, Install, Offline
**Test Kapsamı**: Unit test (SW), Lighthouse PWA audit, Cross-browser testing, Offline testing
