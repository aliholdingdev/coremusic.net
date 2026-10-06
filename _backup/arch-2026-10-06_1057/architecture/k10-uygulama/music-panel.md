---
title: "K10 Music Panel - Ana Müzik Paneli"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Music Panel

## Genel Bakış

Music Panel, COREMUSIC'in kalbi olan ana müzik yönetim arayüzüdür. Kullanıcıların playlist oluşturmasını, şarkı çalmasını, equalizer ayarlamasını ve müzik kütüphanesini yönetmesini sağlar. Gerçek zamanlı playback senkronizasyonu ve çoklu cihaz kontrolü ile profesyonel bir müzik deneyimi sunar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🔍 Şarkı, sanatçı, albüm ara...          [🔔] [👤]   │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 📁       │  │  🎵 Now Playing                      │    │
│ Playlists│  │  ┌────────┐  Song Title              │    │
│          │  │  │ 🎶     │  Artist Name              │    │
│ ▸ Pop    │  │  │ Cover  │  Album Name               │    │
│ ▸ Rock   │  │  │  Art   │  ⏮ ⏯ ⏭  🔀 🔁         │    │
│ ▸ Jazz   │  │  └────────┘  ═══════●════════ 3:42    │    │
│ ▸ Lo-fi  │  │              🔊 ━━━━━━━━━━━━━━ 80%    │    │
│          │  └──────────────────────────────────────┘    │
│ 🎵       │                                             │
│ Albums   │  ┌──────────────────────────────────────┐    │
│          │  │  📋 Playlist Detail                  │    │
│ ▸ Rock   │  │  # │ Title    │ Artist │ Duration   │    │
│ ▸ Pop    │  │  1 │ Song A   │ Art 1  │ 3:42  ▶    │    │
│ ▸ Jazz   │  │  2 │ Song B   │ Art 2  │ 4:15       │    │
│          │  │  3 │ Song C   │ Art 3  │ 2:58       │    │
│ 🎤       │  │  4 │ Song D   │ Art 4  │ 5:01       │    │
│ Artists  │  └──────────────────────────────────────┘    │
│          │                                             │
│ ⚙ Equalizer ┌──────────────────────────────────────┐    │
│            │  🎛 Equalizer                          │    │
│            │  32Hz:  ━━━━━●━━━━━━  +3dB            │    │
│            │  64Hz:  ━━━━━━●━━━━━  +1dB            │    │
│            │  125Hz: ━━━━━━━●━━━━  -2dB            │    │
│            │  250Hz: ━━━━━━━━●━━━  +4dB            │    │
│            │  1kHz:  ━━━━━━━━━●━━  0dB             │    │
│            │  4kHz:  ━━━━━━━━━━●━  +2dB            │    │
│            │  8kHz:  ━━━━━━━━━━━●  +1dB            │    │
│            │  16kHz: ━━━━━━━━━━━━● +5dB            │    │
│            │  [Flat] [Bass+] [Vocal] [Custom]       │    │
│            └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
MusicPanel/
├── MusicLayout.tsx            # Ana layout wrapper
├── Sidebar/
│   ├── SidebarNav.tsx         # Sol navigasyon
│   ├── PlaylistList.tsx       # Playlist listesi
│   └── PlaylistItem.tsx       # Tekil playlist kartı
├── Player/
│   ├── NowPlaying.tsx         # Çalan şarkı bileşeni
│   ├── PlaybackControls.tsx   # Oynat/duraklat/ileri/geri
│   ├── ProgressBar.tsx        # İlerleme çubuğu
│   ├── VolumeControl.tsx      # Ses kontrolü
│   └── QueueManager.tsx       # Sıra yönetimi
├── Library/
│   ├── SongList.tsx           # Şarkı listesi tablosu
│   ├── AlbumGrid.tsx          # Albüm grid görünümü
│   ├── ArtistList.tsx         # Sanatçı listesi
│   └── SearchBar.tsx          # Gerçek zamanlı arama
├── Equalizer/
│   ├── EqualizerPanel.tsx     # EQ paneli
│   ├── FrequencySlider.tsx    # Frekans kaydırıcı
│   ├── PresetSelector.tsx     # Preset seçici
│   └── SpectrumAnalyzer.tsx   # Spectrum görselleştirme
└── Shared/
    ├── MusicCard.tsx          # Paylaşılan müzik kartı
    ├── DurationBadge.tsx      # Süre rozeti
    └── GenreTag.tsx           # Tür etiketi
```

### State Management

```typescript
// Music Store - Zustand
interface MusicState {
  // Playback
  currentTrack: Track | null;
  isPlaying: boolean;
  volume: number;          // 0-100
  progress: number;        // 0-100 (yüzde)
  duration: number;        // saniye
  currentTime: number;     // saniye
  shuffle: boolean;
  repeatMode: 'off' | 'all' | 'one';

  // Queue
  queue: Track[];
  queueIndex: number;

  // Library
  playlists: Playlist[];
  albums: Album[];
  artists: Artist[];
  searchQuery: string;

  // Equalizer
  eqEnabled: boolean;
  eqPreset: string;
  eqBands: number[];       // 10 band frequency values

  // Actions
  play: (track: Track) => void;
  pause: () => void;
  next: () => void;
  previous: () => void;
  setVolume: (vol: number) => void;
  seekTo: (time: number) => void;
  addToQueue: (track: Track) => void;
  removeFromQueue: (index: number) => void;
  createPlaylist: (name: string) => void;
  addToPlaylist: (playlistId: string, track: Track) => void;
  setEqPreset: (preset: string) => void;
  adjustEqBand: (band: number, value: number) => void;
}
```

### Equalizer Teknik Detayları

10-bant grafik equalizer aşağıdaki frekans aralıklarını destekler:

| Band | Frekans | Aralık | Kullanım |
|------|---------|--------|----------|
| 1 | 32 Hz | Sub-bass | Bass derinliği |
| 2 | 64 Hz | Bass | Bass güç |
| 3 | 125 Hz | Low-mid | Sıcaklık |
| 4 | 250 Hz | Mid | Vücut |
| 5 | 500 Hz | Mid | Diklik |
| 6 | 1 kHz | Upper-mid | Netlik |
| 7 | 2 kHz | Presence | Tanım |
| 8 | 4 kHz | Brilliance | Parlaklık |
| 9 | 8 kHz | Air | Hava |
| 10 | 16 kHz | Ultra-high | Detail |

Her band -12dB ile +12dB arasında ayarlanabilir. Preset'ler: Flat, Bass Boost, Treble Boost, Vocal, Rock, Pop, Jazz, Classical, Electronic, Custom.

### Playlist Yönetim Sistemi

Playlist'ler hiyerarşik yapıda organize edilir:
- **Oluşturma**: İsim, açıklama, kapak görseli ile yeni playlist
- **Düzenleme**: Sürükle-bırak ile sıralama, toplu ekleme/çıkarma
- **Paylaşma**: Public/Private/Link-sharing modları
- **İçe/Dışa Aktarma**: M3U, PLS, XSPF format desteği
- **Akıllı Playlist**: Sanatçı, tür, tarih, dinlenme sayısı filtresi ile otomatik playlist oluşturma

### Gerçek Zamanlı Senkronizasyon

WebSocket üzerinden cihazlar arası senkronizasyon:
- Çoklu cihazda aynı playlist kontrolü
- Playback durumu real-time senkronizasyon
- Volume değişiklikleri anlık yansıtma
- Queue güncellemeleri multi-device sync

### Keyboard Shortcuts

| Kısayol | İşlev |
|---------|-------|
| Space | Play/Pause |
| → | İleri sar (5sn) |
| ← | Geri sar (5sn) |
| ↑ | Ses artır |
| ↓ | Ses azalt |
| N | Sonraki şarkı |
| P | Önceki şarkı |
| S | Shuffle aç/kapat |
| R | Repeat modu değiştir |
| L | Like/Beğen |
| Q | Sırayı göster/gizle |
| / | Aramaya odaklan |

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K3 | Audio Engine | Ses oynatma, DSP processing |
| K4 | ML Katmanı | Şarkı önerileri, mood analizi |
| K5 | Veri Yönetimi | Playlist, metadata depolama |
| K8 | Media Service | Streaming, dosya erişimi |
| K7 | Session Middleware | Oturum yönetimi |
| K10 | Theme Engine | Tema özelleştirme |
| K10 | Notification | Bildirim gösterimi |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Kritik
**Kapsam**: Playlist CRUD, Player, Equalizer, Queue, Search
**Test Kapsamı**: Unit test (bileşen), Integration test (API), E2E test (Oynatma akışı)
