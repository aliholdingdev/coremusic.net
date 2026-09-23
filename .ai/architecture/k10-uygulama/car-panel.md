---
title: "K10 Car Panel - Araç İçi Panel"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Car Panel

## Genel Bakış

Car Panel, araç içi bilgi-eğlence sistemi (Infotainment) arayüzüdür. Sürücülerin güvenli bir şekilde müzik çalmasını, navigasyon bilgilerini görüntülemesini ve araç içi medya管管理ını sağlaması için tasarlanmıştır. Büyük dokunmatik ekranlar ve sesli komut entegrasyonu ile sürüş güvenliğini ön planda tutar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🚗 Araç İçi Panel          🔊 Ses: 45%  📶 Connected  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────────────────┬─────────────────────────┐  │
│  │  🎵 Müzik               │  🗺 Navigasyon          │  │
│  │  ┌───────────────────┐  │  ┌───────────────────┐  │  │
│  │  │   🎶 Now Playing  │  │  │   📍 Konum:       │  │  │
│  │  │   Song Title      │  │  │   Atatürk Cd. 42  │  │  │
│  │  │   Artist Name     │  │  │   ↗ 200m sonra    │  │  │
│  │  │   ⏮ ⏯ ⏭         │  │  │   sağa dön         │  │  │
│  │  │   ═══●══════ 3:42 │  │  │   ─────────────── │  │  │
│  │  └───────────────────┘  │  │   🚦 12 km/s       │  │  │
│  │                         │  └───────────────────┘  │  │
│  │  📋 Queue (3 şarkı)     │                         │  │
│  │  1. Song A ▶ playing    │  ┌───────────────────┐  │  │
│  │  2. Song B              │  │  📞 Telefon         │  │  │
│  │  3. Song C              │  │  Son Arama: Ahmet  │  │  │
│  └─────────────────────────┴──[Ara] [Son] [Rehber]─┘  │
│                                                         │
│  ┌──────────────────────────────────────────────────┐   │
│  │  🎛 Hızlı Erişim                                │   │
│  │  [🎵 Müzik] [🗺 Navi] [📞 Tel] [📻 Radyo]      │   │
│  │  [🌡 Klima] [📷 Kamera] [⚡ Energji] [⚙ Ayar]   │   │
│  └──────────────────────────────────────────────────┘   │
│                                                         │
│  🌡 22°C  🔋 78%  ⏱ 14:32  📅 20 Eylül 2026           │
└─────────────────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
CarPanel/
├── CarLayout.tsx               # Araç layout (bölünmüş ekran)
├── Music/
│   ├── CarPlayer.tsx           # Basitleştirilmiş oynatıcı
│   ├── CarQueue.tsx            # Queue görünümü
│   ├── QuickPlaylist.tsx       # Hızlı erişim playlistleri
│   └── VoiceCommand.tsx        # Sesli komut arayüzü
├── Navigation/
│   ├── NavMap.tsx              # Harita görünümü
│   ├── NavDirections.tsx       # Yön yönlendirmeleri
│   ├── NavSearch.tsx           # Hedef arama
│   └── POIList.tsx             # İlgi noktaları
├── Phone/
│   ├── PhonePanel.tsx          # Telefon paneli
│   ├── CallHistory.tsx         # Arama geçmişi
│   ├── ContactList.tsx         # Rehber listesi
│   └── DialPad.tsx             # Tuş takımı
├── Dashboard/
│   ├── StatusBar.tsx           # Üst durum çubuğu
│   ├── QuickAccess.tsx         # Hızlı erişim butonları
│   ├── ClimateControl.tsx      # Klima kontrolü
│   └── VehicleStatus.tsx       # Araç durumu
└── Shared/
    ├── LargeButton.tsx         # Büyük dokunmatik buton
    ├── VoiceFeedback.tsx       # Sesli geri bildirim
    └── NightMode.tsx           # Gece modu
```

### State Management

```typescript
// Car Store - Zustand
interface CarState {
  // Music
  currentTrack: Track | null;
  isPlaying: boolean;
  queue: Track[];
  volume: number;

  // Navigation
  currentLocation: GeoLocation;
  destination: string | null;
  route: RouteInfo | null;
  estimatedArrival: Date | null;

  // Phone
  phoneConnected: boolean;
  callStatus: 'idle' | 'ringing' | 'active' | 'held';
  activeCall: Call | null;
  contacts: Contact[];
  callHistory: CallRecord[];

  // Vehicle
  temperature: number;
  climateMode: 'auto' | 'cool' | 'heat' | 'vent';
  headlightStatus: boolean;

  // Actions
  playTrack: (track: Track) => void;
  setDestination: (dest: string) => void;
  startNavigation: (destination: string) => void;
  answerCall: () => void;
  endCall: () => void;
  adjustClimate: (temp: number, mode: string) => void;
  activateVoiceCommand: () => void;
}
```

### Sesli Komut Entegrasyonu

Sürüş güvenliği için sesli komutlar desteklenir:
- **"COREMUSIC, şarkı çal"**: Rastgele şarkı başlat
- **"COREMUSIC, bir sonraki şarkı"**: Sıradaki şarkıya geç
- **"COREMUSIC, sesi artır/azalt"**: Ses kontrolü
- **"COREMUSIC, [şarkı adı] çal"**: Belirli şarkıyı bul ve çal
- **"COREMUSIC, navigasyon başlat"**: Hedefe git
- **"COREMUSIC, ara [isim]"**: Telefon rehberinden arama

Whisper.cpp veya yerel ASR motoru ile offline ses tanıma desteklenir.

### Drive Mode Optimizasyonu

Sürüş sırasında arayüz basitleştirilir:
- **Large Touch Targets**: Minimum 48x48px dokunma alanı
- **Simplified UI**: Karmaşık menüler gizlenir
- **Voice-First**: Sesli komut öncelikli etkileşim
- **Reduced Distraction**: Animasyonlar ve geçişler minimal
- **Auto-Scroll**: Uzun listeler otomatik kaydırma
- **Night Mode**: Karanlık ortam için koyu tema

### Bluetooth / CarPlay / Android Auto

Çoklu bağlantı protokolü desteği:
- **Bluetooth A2DP**: Temel ses aktarımı
- **Bluetooth HFP**: Hands-free telefon
- **Apple CarPlay**: iOS entegrasyonu
- **Android Auto**: Android entegrasyonu
- **USB Audio**: Yüksek kaliteli kablolu bağlantı

### GPS ve Rota Entegrasyonu

Navigasyon sistemi ile derin entegrasyon:
- **Route-Aware Music**: Rota uzunluğuna göre playlist önerisi
- **ETA-Based Queue**: Varış saatine göre şarkı sayısı ayarı
- **Traffic-Aware**: Trafik durumuna göre içerik önerisi
- **Geofencing**: Belirli konumlarda otomatik senaryo tetikleme

### Güvenlik Kısıtlamaları

Sürüş sırasında güvenlik önlemleri:
- **Video Block**: Hareket halinde video oynatma engelleme
- **Text Input禁用**: Hareket halinde klavye gizleme
- **Menu Depth Limit**: Maksimum 2 seviye menü
- **Auto-Dismiss**: 10sn etkileşim yoksa ana ekrana dönüş

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K6 | Network | Bluetooth, CarPlay, Android Auto |
| K8 | Media Service | Streaming, dosya erişimi |
| K0 | IPC | Bluetooth driver iletişimi |
| K3 | Audio Engine | Araç içi ses sistemi |
| K10 | Music Panel | Müzik kaynağı |
| K10 | Home Panel | Evden çıkış senaryoları |
| K2 | Driver | GPS, Bluetooth driver'ları |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Orta-Yüksek
**Kapsam**: CarPlayer, Navigation integration, Phone, Drive Mode, Voice commands
**Test Kapsamı**: Unit test, Integration test (Bluetooth), E2E test (Sürüş akışı simulation)
