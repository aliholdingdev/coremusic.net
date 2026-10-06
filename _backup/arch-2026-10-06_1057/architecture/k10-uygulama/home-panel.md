---
title: "K10 Home Panel - Ev Medya Merkezi"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Home Panel

## Genel Bakış

Home Panel, akıllı ev medya merkezi kontrol arayüzüdür. Kullanıcıların evlerindeki farklı odalarda müzik ve medya oynatmasını kontrol etmesini, ses seviyelerini ayarlamasını, oda senaryoları oluşturmasını ve bağlı cihazları yönetmesini sağlar. Multi-room audio desteği ile ev genelinde senkronize medya deneyimi sunar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🏠 Ev Medya Merkezi                   [🔔] [👤]      │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 🏠 Rooms │  │  📍 Salon                    🔊 75% │    │
│          │  │  ┌────────┐ ┌────────┐ ┌────────┐   │    │
│ ▸ Salon  │  │  │ 🎵     │ │ 🎵     │ │ 🎵     │   │    │
│ ▸ Mutfak │  │  │ Song A │ │ Song B │ │ Song C │   │    │
│ ▸ Yatak  │  │  │ 3:42   │ │ 4:15   │ │ 2:58   │   │    │
│ ▸ Banyo  │  │  │ ▶ playing│ │ ⏸ pause │ │ ⏹ stop │   │    │
│ ▸ Bahçe  │  │  └────────┘ └────────┘ └────────┘   │    │
│          │  └──────────────────────────────────────┘    │
│ 🔊 Devices│                                            │
│          │  ┌──────────────────────────────────────┐    │
│ ▸ Speaker│  │  🔊 Cihazlar                         │    │
│ ▸ TV     │  │  Device          │ Room   │ Status   │    │
│ ▸ Sound..│  │  ─────────────────────────────────── │    │
│          │  │  Living Speaker  │ Salon  │ 🟢 Online│    │
│ 🎭 Scenes│  │  Kitchen Speaker│ Mutfak │ 🟢 Online│    │
│          │  │  Bedroom TV     │ Yatak  │ 🟡 Idle  │    │
│ ▸ Morning│  │  Garden Speaker │ Bahçe  │ 🔴 Offline│   │
│ ▸ Evening│  └──────────────────────────────────────┘    │
│ ▸ Party  │                                             │
│ ▸ Sleep  │  ┌──────────────────────────────────────┐    │
│          │  │  🎭 Senaryolar                        │    │
│ + Yeni   │  │  ┌─────────────┐ ┌─────────────┐    │    │
│          │  │  │ 🌅 Sabah    │ │ 🌙 Akşam    │    │    │
│          │  │  │ 07:00       │ │ 19:00       │    │    │
│          │  │  │ ☀ Jazz+News │ │ 🌙 Lo-fi    │    │    │
│          │  │  │ Salon+Mutfak│ │ Tüm odalar  │    │    │
│          │  │  │ [Düzenle]   │ │ [Düzenle]   │    │    │
│          │  │  └─────────────┘ └─────────────┘    │    │
│          │  └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
HomePanel/
├── HomeLayout.tsx              # Ana layout
├── RoomManager/
│   ├── RoomGrid.tsx            # Oda grid görünümü
│   ├── RoomCard.tsx            # Tekil oda kartı
│   ├── RoomDetail.tsx          # Oda detay görünümü
│   └── RoomControls.tsx        # Oda kontrol paneli
├── DeviceManager/
│   ├── DeviceList.tsx          # Cihaz listesi
│   ├── DeviceCard.tsx          # Tekil cihaz kartı
│   ├── DeviceStatus.tsx        # Cihaz durum göstergesi
│   └── DevicePairing.tsx       # Yeni cihaz eşleştirme
├── SceneEditor/
│   ├── SceneList.tsx           # Senaryo listesi
│   ├── SceneCard.tsx           # Tekil senaryo kartı
│   ├── SceneEditor.tsx         # Senaryo düzenleme
│   └── SceneScheduler.tsx      # Zamanlayıcı ayarları
├── VolumeControl/
│   ├── VolumeSlider.tsx        # Ses kontrolü
│   ├── MasterVolume.tsx        # Ana ses kontrolü
│   └── RoomVolume.tsx          # Oda bazlı ses
└── Shared/
    ├── RoomIcon.tsx            # Oda ikonları
    └── StatusBadge.tsx         # Durum rozeti
```

### State Management

```typescript
// Home Store - Zustand
interface HomeState {
  // Rooms
  rooms: Room[];
  activeRoom: string | null;

  // Devices
  devices: Device[];
  deviceGroups: DeviceGroup[];

  // Scenes
  scenes: Scene[];
  activeScene: string | null;

  // Volume
  masterVolume: number;
  roomVolumes: Record<string, number>;

  // Actions
  setActiveRoom: (roomId: string) => void;
  updateRoomVolume: (roomId: string, volume: number) => void;
  addDevice: (device: Device) => void;
  removeDevice: (deviceId: string) => void;
  pairDevice: (deviceId: string) => Promise<boolean>;
  activateScene: (sceneId: string) => void;
  createScene: (scene: Omit<Scene, 'id'>) => void;
  updateScene: (sceneId: string, updates: Partial<Scene>) => void;
  deleteScene: (sceneId: string) => void;
}

// Room Tipi
interface Room {
  id: string;
  name: string;
  icon: string;          // room icon key
  devices: string[];     // device IDs
  currentTrack: Track | null;
  isPlaying: boolean;
  volume: number;        // 0-100
  color: string;         // accent color
}

// Device Tipi
interface Device {
  id: string;
  name: string;
  type: 'speaker' | 'tv' | 'soundbar' | 'receiver' | 'display';
  roomId: string;
  status: 'online' | 'offline' | 'idle' | 'playing';
  volume: number;
  capabilities: DeviceCapability[];
  firmwareVersion: string;
  lastSeen: Date;
}

// Scene Tipi
interface Scene {
  id: string;
  name: string;
  icon: string;
  description: string;
  triggers: SceneTrigger[];
  actions: SceneAction[];
  isActive: boolean;
  schedule: SceneSchedule | null;
}
```

### Multi-Room Audio

Sistem birden fazla odada senkronize ses çalmayı destekler:
- **Same Source**: Tüm odalarda aynı şarkı senkronizasyonu
- **Independent**: Her odada farklı şarkı
- **Zoned Grouping**: Odaları gruplayarak kontrol
- **Handoff**: Odalar arasında geçiş yaparken kesintisiz aktarım
- **Delay Compensation**: Odalar arası gecikme telafisi

### Senaryo (Scene) Motoru

Senaryolar, belirli koşullarda otomatik çalışan eylem zincirleridir:
- **Trigger**: Zaman, olay (cihaz bağlanma), manuel tetikleme
- **Action**: Şarkı çal, ses ayarla, cihaz aç/kapat, ışık kontrolü
- **Schedule**: Günlük, haftalık, özel zamanlama
- **Condition**: Cihaz durumu, saat, tarih bazlı koşullar

### Cihaz Keşfi ve Eşleştirme

mDNS/DNS-SSD protokolü ile yerel ağdaki cihazlar otomatik keşfedilir:
- **Zeroconf**: Otomatik cihaz bulma
- **Manual IP**: Manuel cihaz ekleme
- **QR Code**: Mobil cihaz eşleştirme
- **Bluetooth**: Kısa menzilli eşleştirme

### WebSocket Gerçek Zamanlı Güncelleme

Her oda ve cihaz durumu WebSocket üzerinden real-time güncellenir:
- Oda ses değişiklikleri anlık yansıtma
- Cihaz online/offline durumu
- Playback durumu senkronizasyonu
- Senaryo tetikleme bildirimleri

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K3 | Audio Engine | Multi-room playback |
| K6 | Network | mDNS, WebSocket |
| K8 | Device Service | Cihaz CRUD, durum |
| K8 | Media Service | Streaming kontrolü |
| K0 | IPC | Cihazlar arası iletişim |
| K10 | Music Panel | Müzik kaynağı |
| K10 | Notification | Senaryo bildirimleri |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek
**Kapsam**: Room management, Device pairing, Scene editor, Volume control
**Test Kapsamı**: Unit test ( bileşenler), Integration test (WebSocket), E2E test (Multi-room akışı)
