---
title: "K10 Media Panel - Medya Oynatıcı"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Media Panel

## Genel Bakış

Media Panel, çoklu medya formatı desteği ile gelişmiş bir medya oynatıcı arayüzüdür. Ses ve video dosyalarını oynatma, subtitle yönetimi, media info görüntüleme ve playlist oluşturma fonksiyonlarını içerir. HLS/DASH streaming desteği ile uzaktan medya içeriklerini de oynatır.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  🎬 Medya Oynatıcı             [📁 Aç] [🔗 URL]       │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │                                                 │   │
│  │              🎬 Video Area                      │   │
│  │                                                 │   │
│  │           ┌─────────────────────┐               │   │
│  │           │                     │               │   │
│  │           │    🎥 Video         │               │   │
│  │           │    Playback Area    │               │   │
│  │           │                     │               │   │
│  │           │    [Subtitle Text]  │               │   │
│  │           └─────────────────────┘               │   │
│  │                                                 │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  🎵 Now Playing: Song Title - Artist Name              │
│  ═══════════════════●═══════════════════════ 02:45/05:12│
│  🔇 ━━━━━━━━━━━━━━━━━━━━ 🔊  │ ⏮ ▶ ⏭  │ 🔀 🔁 │ 📋 │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │  📋 Medya Bilgisi                               │   │
│  │  Format: MP4 (H.264/AAC)                        │   │
│  │  Çözünürlük: 1920x1080 (Full HD)                │   │
│  │  FPS: 30                                       │   │
│  │  Bitrate: 5.2 Mbps                             │   │
│  │  Süre: 05:12                                   │   │
│  │  Codec: H.264 (High Profile)                   │   │
│  │  Ses: AAC 256kbps Stereo                       │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  ┌─────────────────────────────────────────────────┐   │
│  │  📁 Kütüphane                                   │   │
│  │  ┌─────┬──────────────┬──────┬────────────┐    │   │
│  │  │ 🎬  │ Dosya Adı    │ Boyut│ Süre       │    │   │
│  │  ├─────┼──────────────┼──────┼────────────┤    │   │
│  │  │ 🎵  │ Song_A.mp3   │ 8MB  │ 3:42       │    │   │
│  │  │ 🎬  │ Video_B.mp4  │ 250MB│ 12:30      │    │   │
│  │  │ 🎵  │ Song_C.flac  │ 45MB │ 4:15       │    │   │
│  │  │ 🎬  │ Movie_D.mkv  │ 2.1GB│ 1:42:00    │    │   │
│  │  └─────┴──────────────┴──────┴────────────┘    │   │
│  └─────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
MediaPanel/
├── MediaLayout.tsx              # Medya layout
├── Player/
│   ├── VideoPlayer.tsx          # Video oynatıcı
│   ├── AudioPlayer.tsx          # Ses oynatıcı
│   ├── PlaybackControls.tsx     # Oynatma kontrolleri
│   ├── ProgressBar.tsx          # İlerleme çubuğu
│   ├── VolumeControl.tsx        # Ses kontrolü
│   ├── FullscreenToggle.tsx     # Tam ekran
│   ├── SpeedControl.tsx         # Oynatma hızı
│   └── PictureInPicture.tsx     # Resim içinde resim
├── Subtitles/
│   ├── SubtitleManager.tsx      # Altyazı yöneticisi
│   ├── SubtitleRenderer.tsx     # Altyazı render
│   └── SubtitleSettings.tsx     # Altyazı ayarları
├── MediaInfo/
│   ├── MediaInfoPanel.tsx       # Medya bilgi paneli
│   ├── FormatInfo.tsx           # Format bilgisi
│   ├── CodecInfo.tsx            # Codec bilgisi
│   └── StreamInfo.tsx           # Akış bilgisi
├── Library/
│   ├── MediaGrid.tsx            # Medya grid görünümü
│   ├── MediaList.tsx            # Medya listesi görünümü
│   ├── MediaCard.tsx            # Medya kartı
│   ├── MediaFilter.tsx          # Medya filtreleme
│   └── MediaSort.tsx            # Medya sıralama
├── Streaming/
│   ├── StreamUrl.tsx            # URL girişi
│   ├── HLSPlayer.tsx           # HLS oynatıcı
│   └── DASHPlayer.tsx          # DASH oynatıcı
└── Shared/
    ├── MediaPlayer.tsx          # Genel medya oynatıcı
    ├── BufferIndicator.tsx      # Buffer göstergesi
    └── QualitySelector.tsx      # Kalite seçici
```

### State Management

```typescript
// Media Store - Zustand
interface MediaState {
  // Player
  currentMedia: MediaFile | null;
  isPlaying: boolean;
  volume: number;
  progress: number;
  duration: number;
  currentTime: number;
  playbackRate: number;
  isFullscreen: boolean;
  isPiP: boolean;

  // Subtitles
  subtitles: SubtitleTrack[];
  activeSubtitle: string | null;
  subtitleStyle: SubtitleStyle;

  // Library
  mediaFiles: MediaFile[];
  filteredFiles: MediaFile[];
  sortBy: 'name' | 'date' | 'size' | 'duration';
  sortOrder: 'asc' | 'desc';
  filterType: 'all' | 'audio' | 'video';

  // Streaming
  streamUrl: string;
  streamQuality: 'auto' | 'low' | 'medium' | 'high';

  // Actions
  loadMedia: (file: MediaFile | string) => Promise<void>;
  play: () => void;
  pause: () => void;
  seek: (time: number) => void;
  setVolume: (vol: number) => void;
  setPlaybackRate: (rate: number) => void;
  toggleFullscreen: () => void;
  togglePiP: () => void;
  loadSubtitle: (url: string, lang: string) => void;
  setActiveSubtitle: (id: string | null) => void;
  updateSubtitleStyle: (style: Partial<SubtitleStyle>) => void;
  addToLibrary: (files: MediaFile[]) => void;
  removeFromLibrary: (fileId: string) => void;
  setFilter: (type: string) => void;
  setSort: (by: string, order: string) => void;
}

// MediaFile Tipi
interface MediaFile {
  id: string;
  name: string;
  type: 'audio' | 'video';
  format: string;
  size: number;
  duration: number;
  path: string;
  thumbnail?: string;
  metadata: MediaMetadata;
}

// MediaMetadata Tipi
interface MediaMetadata {
  codec: string;
  bitrate: number;
  sampleRate?: number;
  channels?: number;
  width?: number;
  height?: number;
  fps?: number;
  artist?: string;
  album?: string;
  year?: number;
  genre?: string;
}
```

### Desteklenen Formatlar

| Kategori | Formatlar |
|----------|----------|
| Ses | MP3, FLAC, WAV, AAC, OGG, WMA, AIFF, ALAC |
| Video | MP4, MKV, AVI, MOV, WebM, FLV, WMV, 3GP |
| Codec (Ses) | AAC, MP3, FLAC, Opus, Vorbis, AC3, DTS |
| Codec (Video) | H.264, H.265/HEVC, VP9, AV1, MPEG-4 |
| Streaming | HLS (m3u8), DASH (mpd), RTMP, WebRTC |
| Altyazı | SRT, VTT, ASS/SSA, SUB, DFXP |

### Video Oynatıcı Özellikleri

- **Hardware Acceleration**: GPU ile hızlandırılmış decode
- **Adaptive Streaming**: HLS/DASH ile adaptif kalite
- **Subtitle Rendering**: Custom font, renk, pozisyon
- **Picture-in-Picture**: Pip desteği
- **Fullscreen**: Tam ekran modu
- **Playback Speed**: 0.25x - 2.0x hız
- **Frame Stepping**: Frame-by-frame ileri/geri
- **Audio Track Selection**: Çoklu ses kanalı seçimi

### Media Info Analizi

ffprobe benzeri detaylı medya analizi:
- **Container**: Format, süre, boyut
- **Video Stream**: Codec, çözünürlük, FPS, bitrate
- **Audio Stream**: Codec, sample rate, kanal, bitrate
- **Metadata**: ID3 tag, MP4 metadata
- **Chapters**: Bölüm bilgileri
- **Subtitle Track**: Altyazı dilleri

### Streaming Protokolleri

```
Client → CDN → Origin Server
  ↓         ↓         ↓
HLS    DASH    WebRTC
  ↓         ↓         ↓
m3u8    mpd     signaling
  ↓         ↓         ↓
TS      fMP4    SRTP
```

- **HLS**: Apple ecosystem, iOS Safari native
- **DASH**: Geniş desteği, MPEG-DASH
- **WebRTC**: Ultra-low latency live streaming

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K3 | Audio Engine | Ses decode, DSP |
| K6 | Network | Streaming protokolleri |
| K8 | Media Service | Dosya metadata, streaming |
| K0 | Dosya Sistemi | Medya dosyası erişimi |
| K0 | GPU | Hardware decode acceleration |
| K10 | Music Panel | Ses dosyası kaynakları |
| K10 | Download Panel | İndirme entegrasyonu |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Orta-Yüksek
**Kapsam**: Video/Audio Player, Subtitles, Media Info, Streaming, Library
**Test Kapsamı**: Unit test, Integration test (media decode), E2E test (playback scenarios)
