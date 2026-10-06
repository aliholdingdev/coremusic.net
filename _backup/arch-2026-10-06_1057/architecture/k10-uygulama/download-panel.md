---
title: "K10 Download Panel - İndirme Yöneticisi"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Download Panel

## Genel Bakış

Download Panel, dosya indirme işlemlerini yöneten arayüzdür. Batch download desteği, indirme kuyruğu yönetimi, bant genişliği kontrolü ve indirme geçmişi sunar. Kullanıcıların müzik dosyalarını, albüm kapaklarını ve podcaster içeriklerini organize bir şekilde indirmesini sağlar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  📥 İndirme Yöneticisi         [↓ Aktif: 3] [⏸ 1]     │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 📥 Active│  │  ↓ Aktif İndirmeler                  │    │
│          │  │                                      │    │
│ 📋 Queue │  │  🎵 Album_Rock_2026.zip             │    │
│          │  │  ████████████░░░░░░░░░░░  52%  12MB/s │    │
│ ⏸ Paused│  │  Kalan: 1:23  [⏸] [❌]              │    │
│          │  │                                      │    │
│ ✅ Done  │  │  🎵 Jazz_Collection.flac            │    │
│          │  │  ██████████████████████  100% ✓ Tamamlandı│   │
│ ❌ Failed│  │                                      │    │
│          │  │  🎵 Podcast_Episode_45.mp3          │    │
│ 📁 Files │  │  ██████░░░░░░░░░░░░░░░  28%  8MB/s  │    │
│          │  │  Kalan: 3:45  [⏸] [❌]              │    │
│ ⚙ Settings│ │                                      │    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  📋 İndirme Kuyruğu (8 dosya)       │    │
│          │  │  ┌────┬──────────────┬──────┬──────┐ │    │
│          │  │  │ #  │ Dosya Adı    │ Boyut│ Durum│ │    │
│          │  │  ├────┼──────────────┼──────┼──────┤ │    │
│          │  │  │ 1  │ Album_A.zip  │ 250MB│ ⏳   │ │    │
│          │  │  │ 2  │ Album_B.zip  │ 180MB│ ⏳   │ │    │
│          │  │  │ 3  │ Single_C.mp3 │ 8MB  │ ⏳   │ │    │
│          │  │  │ 4  │ Podcast_D    │ 45MB │ ⏳   │ │    │
│          │  │  └────┴──────────────┴──────┴──────┘ │    │
│          │  │  [▶ Tümünü Başlat] [⏸ Tümünü Durdur]│    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  ⚙ İndirme Ayarları                  │    │
│          │  │  Max Eşzamanlı: [3] ▼               │    │
│          │  │  Bant Genişliği: [∞ Sınırsız] ▼     │    │
│          │  │  Hedef Klasör: [Downloads/CMusic] ▼  │    │
│          │  │  Otomatik İndir: [☑]                 │    │
│          │  └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
DownloadPanel/
├── DownloadLayout.tsx           # İndirme layout
├── ActiveDownloads/
│   ├── ActiveList.tsx           # Aktif indirme listesi
│   ├── DownloadItem.tsx         # Tekil indirme kartı
│   ├── ProgressRing.tsx         # Progress halka
│   └── SpeedIndicator.tsx       # Hız göstergesi
├── Queue/
│   ├── QueueList.tsx            # Kuyruk listesi
│   ├── QueueItem.tsx            # Kuyruk öğesi
│   ├── QueueActions.tsx         # Kuyruk işlemleri
│   └── DragReorder.tsx          # Sürükle-bırak sıralama
├── History/
│   ├── HistoryList.tsx          # Geçmiş listesi
│   ├── HistoryItem.tsx          # Geçmiş öğesi
│   └── FailedList.tsx           # Başarısız indirmeler
├── FileManager/
│   ├── FileGrid.tsx             # Dosya grid görünümü
│   ├── FileCard.tsx             # Dosya kartı
│   ├── FilePreview.tsx          # Dosya önizleme
│   └── StorageInfo.tsx          # Depolama bilgisi
├── Settings/
│   ├── DownloadSettings.tsx     # İndirme ayarları
│   ├── BandwidthControl.tsx     # Bant genişliği
│   └── FolderSelector.tsx       # Klasör seçici
└── Shared/
    ├── Progress.tsx             # Genel progress bar
    ├── StatusBadge.tsx          # Durum rozeti
    └── RetryButton.tsx          # Tekrar deneme butonu
```

### State Management

```typescript
// Download Store - Zustand
interface DownloadState {
  // Active Downloads
  activeDownloads: Download[];
  maxConcurrent: number;
  bandwidthLimit: number;        // 0 = sınırsız (bytes/s)

  // Queue
  queue: Download[];

  // History
  completed: Download[];
  failed: Download[];

  // Settings
  downloadPath: string;
  autoDownload: boolean;

  // Actions
  addDownload: (url: string, filename: string) => Promise<string>;
  addBatchDownload: (urls: string[]) => Promise<string[]>;
  pauseDownload: (id: string) => void;
  resumeDownload: (id: string) => void;
  cancelDownload: (id: string) => void;
  retryDownload: (id: string) => void;
  removeDownload: (id: string) => void;
  clearCompleted: () => void;
  clearFailed: () => void;
  reorderQueue: (fromIndex: number, toIndex: number) => void;
  setMaxConcurrent: (max: number) => void;
  setBandwidthLimit: (limit: number) => void;
  getDownloadPath: () => Promise<string>;
  selectDownloadPath: () => Promise<string>;
}

// Download Tipi
interface Download {
  id: string;
  url: string;
  filename: string;
  fileSize: number;
  downloaded: number;
  status: 'queued' | 'downloading' | 'paused' | 'completed' | 'failed';
  speed: number;              // bytes/s
  eta: number;                // saniye
  error?: string;
  createdAt: Date;
  completedAt?: Date;
  retryCount: number;
  maxRetries: number;
}
```

### İndirme Motoru

WebSocket tabanlı progress tracking:
- **Chunked Download**: Büyük dosyalar için parçalı indirme
- **Resume Support**: Kesilen indirmelerden devam etme (HTTP Range)
- **Retry Logic**: Otomatik yeniden deneme (exponential backoff)
- **Checksum**: MD5/SHA256 ile dosya bütünlüğü doğrulama
- **Mirror Support**: Alternatif kaynaklardan indirme

### Bant Genişliği Yönetimi

Kullanıcı bant genişliğini kontrol edebilir:
- **Sınırsız**: Maksimum hız
- **Özel Limit**: Kullanıcı tanımlı limit (KB/s veya MB/s)
- **Zamanlanmış**: belirli saatlerde farklı limitler
- **Otomatik**: Ağ durumuna göre adaptif hız
- **Priorite**: Aktif indirmeler arasında öncelik sıralaması

### Batch Download

Toplu indirme özellikleri:
- **URL Listesi**: Manuel URL listesi girme
- **Clipboard Import**: Panodan URL yapıştırma
- **Playlist Import**: Playlist dosyasından toplu indirme
- **Pattern Matching**: wildcard ile dosya filtreleme
- **Auto-Rename**: Otomatik dosya adlandırma kalıpları

### Dosya Yönetimi

İndirilen dosyaların yönetimi:
- **Kategori**: Müzik, Albüm, Podcast, Diğer
- **Önizleme**: Audio player entegrasyonu
- **Taşıma**: Klasörler arası taşıma
- **Silme**: Geri dönüşümlü silme
- **Arama**: Dosya adına göre arama
- **Sıralama**: İsim, tarih, boyut, tür

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K6 | Network | HTTP/HTTPS indirme |
| K0 | Dosya Sistemi | Dosya yazma, disk yönetimi |
| K8 | Media Service | Medya dosyası metadata |
| K5 | Veri Yönetimi | İndirme geçmişi, ayarlar |
| K10 | Notification | İndirme tamamlanma bildirimleri |
| K10 | Music Panel | Müzik kütüphanesine ekleme |
| K3 | Audio Engine | Ses dosyası doğrulama |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Orta
**Kapsam**: Active Downloads, Queue, History, Settings, Batch Download
**Test Kapsamı**: Unit test, Integration test (download engine), E2E test (batch download flow)
