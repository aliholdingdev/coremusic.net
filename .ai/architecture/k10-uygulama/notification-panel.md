---
title: "K10 Notification Panel - Bildirim Sistemi"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Notification Panel

## Genel Bakış

Notification Panel, COREMUSIC uygulama içi bildirim yönetim sistemidir. Toast bildirimleri, badge yönetimi, bildirim geçmişi, tercih ayarları ve real-time bildirim akışını içerir. WebSocket ile gerçek zamanlı bildirimleri_PUSH notification entegrasyonunu destekler.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🔔 Bildirimler                 [Okundu İşle] [⚙ Ayar] │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 📋 All   │  │  📋 Tüm Bildirimler (12 okunmadı)   │    │
│          │  │                                      │    │
│ 🎵 Music │  │  🔴 Yeni bir indirme tamamlandı      │    │
│          │  │  Album_Rock_2026.zip başarıyla       │    │
│ 📥 Download│ │  indirildi.                          │    │
│          │  │  🕐 2 dakika önce          [🗑️]     │    │
│ 🔔 System│  │                                      │    │
│          │  │  🟡 Stüdyo otomatik kaydedildi       │    │
│ 👤 User  │  │  Session_v3.cpr kaydedildi.          │    │
│          │  │  🕐 15 dakika önce         [🗑️]     │    │
│          │  │                                      │    │
│          │  │  🔵 Yeni şarkı önerisi               │    │
│          │  │  ML motorumuz sizin için 5 yeni      │    │
│          │  │  şarkı önerdi.                       │    │
│          │  │  🕐 1 saat önce           [🗑️]      │    │
│          │  │                                      │    │
│          │  │  🔴 Güvenlik uyarısı                 │    │
│          │  │  Farklı bir cihazdan giriş yapıldı.  │    │
│          │  │  🕐 2 saat önce           [🗑️]      │    │
│          │  │                                      │    │
│          │  │  🟢 Sistem güncellemesi mevcut       │    │
│          │  │  COREMUSIC v2.1.0 sürümüne           │    │
│          │  │  yükseltilebilir.                    │    │
│          │  │  🕐 5 saat önce          [🗑️]       │    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  ⚙ Bildirim Tercihleri              │    │
│          │  │                                      │    │
│          │  │  Müzik:     [☑] Bildirimler Açık    │    │
│          │  │  İndirme:   [☑] Bildirimler Açık    │    │
│          │  │  Sistem:    [☑] Bildirimler Açık    │    │
│          │  │  Güvenlik:  [☑] Her Zaman Açık      │    │
│          │  │  Öneriler:  [☐] Bildirimler Kapalı  │    │
│          │  │                                      │    │
│          │  │  Sessiz Mod: [☐]                    │    │
│          │  │  Sessiz Başlangıç: [22:00]          │    │
│          │  │  Sessiz Bitiş:     [08:00]          │    │
│          │  └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
NotificationPanel/
├── NotificationManager.tsx       # Ana bildirim yöneticisi
├── Toast/
│   ├── ToastContainer.tsx       # Toast konteyner
│   ├── ToastItem.tsx            # Tekil toast
│   ├── ToastSuccess.tsx         # Başarı toast'ı
│   ├── ToastError.tsx           # Hata toast'ı
│   ├── ToastWarning.tsx         # Uyarı toast'ı
│   └── ToastInfo.tsx            # Bilgi toast'ı
├── Badge/
│   ├── BadgeManager.tsx         # Badge yöneticisi
│   ├── NavBadge.tsx             # Navigasyon badge'i
│   ├── TabBadge.tsx             # Tab badge'i
│   └── CountBadge.tsx           # Sayı badge'i
├── History/
│   ├── NotificationList.tsx     # Bildirim listesi
│   ├── NotificationItem.tsx     # Tekil bildirim
│   ├── NotificationFilter.tsx   # Bildirim filtreleme
│   └── NotificationSearch.tsx   # Bildirim arama
├── Settings/
│   ├── NotificationSettings.tsx # Bildirim ayarları
│   ├── CategoryToggle.tsx       # Kategori açma/kapama
│   ├── QuietHours.tsx           # Sessiz saatler
│   └── SoundSettings.tsx        # Ses ayarları
├── RealTime/
│   ├── WebSocketHandler.tsx     # WebSocket handler
│   ├── PushHandler.tsx          # Push notification handler
│   └── SyncManager.tsx          # Senkronizasyon yöneticisi
└── Shared/
    ├── NotificationIcon.tsx     # Bildirim ikonları
    ├── NotificationSound.tsx    # Bildirim sesleri
    └── NotificationAnimation.tsx # Bildirim animasyonları
```

### State Management

```typescript
// Notification Store - Zustand
interface NotificationState {
  // Notifications
  notifications: Notification[];
  unreadCount: number;
  selectedCategory: string;

  // Toast
  toasts: Toast[];
  maxToasts: number;
  toastDuration: number;

  // Settings
  settings: NotificationSettings;
  quietHours: QuietHours;

  // Connection
  wsConnected: boolean;
  lastSync: Date;

  // Actions
  addNotification: (notification: Omit<Notification, 'id' | 'timestamp'>) => string;
  markAsRead: (id: string) => void;
  markAllAsRead: () => void;
  deleteNotification: (id: string) => void;
  clearAll: () => void;
  setCategory: (category: string) => void;

  showToast: (toast: Omit<Toast, 'id'>) => void;
  dismissToast: (id: string) => void;
  clearToasts: () => void;

  updateSettings: (settings: Partial<NotificationSettings>) => void;
  setQuietHours: (hours: QuietHours) => void;

  connectWebSocket: () => void;
  disconnectWebSocket: () => void;
}

// Notification Tipi
interface Notification {
  id: string;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'error';
  category: 'music' | 'download' | 'system' | 'user' | 'security';
  icon?: string;
  action?: NotificationAction;
  read: boolean;
  timestamp: Date;
  metadata?: Record<string, any>;
}

// Toast Tipi
interface Toast {
  id: string;
  message: string;
  type: 'success' | 'error' | 'warning' | 'info';
  duration: number;
  action?: ToastAction;
  dismissible: boolean;
}

// NotificationSettings Tipi
interface NotificationSettings {
  music: boolean;
  download: boolean;
  system: boolean;
  user: boolean;
  security: boolean;
  recommendations: boolean;
  sound: boolean;
  vibration: boolean;
  desktop: boolean;
}

// QuietHours Tipi
interface QuietHours {
  enabled: boolean;
  start: string;       // "22:00"
  end: string;         // "08:00"
}
```

### Bildirim Kategorileri

| Kategori | Renk | İkon | Örnek |
|----------|------|------|-------|
| Music | Primary | 🎵 | Yeni albüm, sanatçı güncellemesi |
| Download | Accent | 🍎 | İndirme tamamlanma, hata |
| System | Info | 🔧 | Güncelleme, bakım |
| User | Secondary | 👤 | Profil güncellemesi, tercihler |
| Security | Error | 🔒 | Güvenlik uyarısı, giriş |
| Recommendations | Success | ⭐ | ML önerileri, yeni şarkılar |

### Toast Bildirim Sistemi

Anlık bildirimler için toast mekanizması:
- **Position**: Üst sağ (varsayılan), alt sağ, üst orta
- **Duration**: Varsayılan 5000ms, error 8000ms
- **Stacking**: Maks 3同一 Anda visible toast
- **Dismiss**: Tıklama ile kapatma, otomatik kaybolma
- **Action**: Tek tıklama ile işlem (indirme aç, ayarları göster)
- **Queue**: Yeni toast'lar kuyruğa alınır

### Badge Yönetimi

Navigasyon ve tab badge'leri:
- **Count Badge**: Okunmamış bildirim sayısı
- **Dot Badge**: Sadece nokta (sayı göstermeden)
- **Max Count**: 99+ limiti
- **Animation**: Sayı değişim animasyonu
- **Sound**: Badge güncelleme sesi

### Real-Time Bildirim Akışı

WebSocket tabanlı gerçek zamanlı bildirimler:
```
Server → WebSocket → NotificationManager → UI Update
                ↓
         Badge Update
                ↓
         Toast Show (optional)
                ↓
         Sound Play (optional)
```

- **Connection**: Otomatik bağlantı ve yeniden deneme
- **Heartbeat**: 30sn heartbeat ile bağlantı canlılığı
- **Reconnection**: Exponential backoff ile yeniden bağlanma
- **Message Queue**: Bağlantı kopukluğunda mesaj kuyruğu

### Bildirim Sesleri

Farklı bildirim türleri için farklı sesler:
- **Info**: Kısa bip sesi
- **Success**: Yükselen ton
- **Warning**: Çift uyarı sesi
- **Error**: Alçalan ton, uyarı
- **Custom**: Kullanıcı tanımlı sesler

### Sessiz Mod

Bildirim susturma özellikleri:
- **Global Mute**: Tüm bildirimleri sustur
- **Category Mute**: Kategori bazlı susturma
- **Quiet Hours**: belirli saatlerde susturma
- **Schedule**: Zamanlanmış sessizlik
- **Override**: Güvenlik bildirimleri sessiz modu deler

### Bildirim Geçmişi

Tam bildirim geçmişi yönetimi:
- **Pagination**: Sayfalama ile geçiş
- **Filter**: Kategori, tarih, durum filtresi
- **Search**: Bildirim içeriğinde arama
- **Export**: Bildirim geçmişi dışa aktarma
- **Auto-Delete**: Belirli süre sonra otomatik silme

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K6 | Network | WebSocket bağlantısı |
| K8 | Push Service | Push notification backend |
| K0 | Browser API | Notification API, Sound API |
| K5 | Veri Yönetimi | Bildirim geçmişi |
| K7 | Session Middleware | Kullanıcı oturumu |
| K10 | Theme Engine | Bildirim renkleri |
| K10 | PWA Features | Push notification PWA |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek
**Kapsam**: Toast, Badge, History, Settings, Real-time, Sound, Quiet Hours
**Test Kapsamı**: Unit test, Integration test (WebSocket), E2E test (notification flow)
