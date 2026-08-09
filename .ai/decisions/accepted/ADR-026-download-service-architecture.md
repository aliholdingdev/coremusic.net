---
type: adr
category: architecture
title: "ADR-026: Download Service Architecture"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-026: Download Service Architecture

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Architecture
**İlgili Agent:** [[.agents/backend-architect]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun indirme servisi mimarisini tanımlar. Node.js + TypeScript ile oluşturulan servisin port yapısı, kaynak entegrasyonları, anti-ban stratejileri, kalite kontrolü ve veritabanı entegrasyonunu kapsar. Tüm indirme işlemleri bu mimariye göre yönetilir.

---

## 2. Bağlam

CoreMusic, müzik indirme yeteneğine sahip bir medya platformudur:
- YouTube'dan video/müzik indirme
- Deezer'dan FLAC indirme (deemix PHP portu)
- Otomatik indirme (AI önerileri ile)
- Manuel indirme (kullanıcı talebi)
- Çapraz platform senkronizasyonu

İndirme servisi, 7 backend servisinden biridir ve `download.coremusic.net` subdomain'inde port 3001'de çalışır.

---

## 3. Karar

CoreMusic'te **Node.js + TypeScript** ile indirme servisi oluşturulacak. Yüksek performanslı, anti-ban korumalı, çoklu kaynak destekli bir mimari uygulanacaktır.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| TypeScript | Type safety | Bu ADR |
| Anti-ban | Rate limiting + proxy | [[ADR-028-anti-ban-system]] |
| Multi-source | YouTube + Deezer + FLAC | Bu ADR |
| Kalite | FLAC 24/32-bit öncelik | Bu ADR |
| Real-time | WebSocket ile durum | Bu ADR |

---

## 4. Teknik Detaylar

### 4.1 Servis Konfigürasyonu

| Parametre | Değer |
|-----------|-------|
| Port | 3001 |
| Subdomain | download.coremusic.net |
| Protocol | HTTP + WebSocket |
| Runtime | Node.js LTS |
| Language | TypeScript 5.x |
| Framework | Express.js / Fastify |
| Process Manager | PM2 |

### 4.2 Mimari Katmanlar

```
┌─────────────────────────────────────────┐
│ Layer 4: API Gateway                     │
│ REST + WebSocket endpoint'leri           │
├─────────────────────────────────────────┤
│ Layer 3: Queue Manager                   │
│ İndirme kuyruğu yönetimi                │
├─────────────────────────────────────────┤
│ Layer 2: Source Adapters                 │
│ YouTube, Deezer, FLAC kaynak adaptörleri │
├─────────────────────────────────────────┤
│ Layer 1: Core Engine                     │
│ İndirme motoru, kalite kontrolü          │
├─────────────────────────────────────────┤
│ Layer 0: Storage                         │
│ Dosya sistemi + DB metadata             │
└─────────────────────────────────────────┘
```

### 4.3 API Endpoint'leri

#### 4.3.1 REST Endpoint'leri

| Method | Endpoint | Açıklama | Auth |
|--------|----------|----------|------|
| POST | `/api/v1/download` | Yeni indirme başlat | ✅ |
| GET | `/api/v1/download/:id` | İndirme durumu | ✅ |
| GET | `/api/v1/download/queue` | Kuyruk listesi | ✅ |
| DELETE | `/api/v1/download/:id` | İndirme iptal | ✅ |
| GET | `/api/v1/download/history` | İndirme geçmişi | ✅ |
| POST | `/api/v1/download/batch` | Toplu indirme | ✅ |
| GET | `/api/v1/download/health` | Servis sağlık | ❌ |

#### 4.3.2 WebSocket Event'leri

| Event | Yön | Açıklama |
|-------|-----|----------|
| `download:start` | Client → Server | İndirme başlat |
| `download:progress` | Server → Client | İlerleme durumu |
| `download:complete` | Server → Client | Tamamlandı |
| `download:error` | Server → Client | Hata |
| `download:cancel` | Client → Server | İptal |
| `queue:update` | Server → Client | Kuyruk güncellemesi |

### 4.4 Kaynak Entegrasyonları

#### 4.4.1 YouTube Entegrasyonu

| Özellik | Değer |
|---------|-------|
| Kaynak | YouTube Data API v3 + yt-dlp |
| Format | MP3 320kbps (varsayılan), FLAC (mevcutsa) |
| Kalite | En yüksek mevcut |
| Metadata | otomatik çıkarma |
| Kapak | otomatik indirme |

**İndirme Akışı:**
```
YouTube URL → yt-dlp ile çözümleme → Format seçimi → İndirme → Metadata çıkarma → DB'ye kaydet
```

#### 4.4.2 Deezer Entegrasyonu

| Özellik | Değer |
|---------|-------|
| Kaynak | deemix PHP portu |
| Format | FLAC 24-bit / 32-bit (CD kalitesi) |
| Kalite | En yüksek |
| ARL Token | Credential vault'tan |
| Anti-ban | Rate limiting + proxy |

**İndirme Akışı:**
```
Deezer URL → deemix → FLAC indirme → Metadata → DB'ye kaydet
```

#### 4.4.3 FLAC Kalite Kontrolü

| Kontrol | Değer | İhlal |
|---------|-------|-------|
| Bit depth | 24-bit / 32-bit | Fallback: 16-bit |
| Sample rate | 44.1kHz / 48kHz / 96kHz | Varsayılan: 44.1kHz |
| Kanal | Stereo / Mono | Varsayılan: Stereo |
| Metadata | Albüm, sanatçı, tür | Eksik → Manuel ekleme |
| Kapak görseli | ≥500x500px | Eksik → Varsayılan |

### 4.5 Queue Yönetimi

#### 4.5.1 Kuyruk Yapısı

```typescript
interface DownloadJob {
  id: string;
  userId: number;
  source: 'youtube' | 'deezer' | 'flac';
  url: string;
  quality: 'mp3-128' | 'mp3-320' | 'flac-16' | 'flac-24' | 'flac-32';
  status: 'pending' | 'downloading' | 'processing' | 'completed' | 'failed' | 'cancelled';
  progress: number; // 0-100
  priority: 'low' | 'normal' | 'high';
  createdAt: Date;
  updatedAt: Date;
  metadata?: SongMetadata;
  filePath?: string;
  error?: string;
}
```

#### 4.5.2 Kuyruk Kuralları

| Kural | Değer | İhlal |
|-------|-------|-------|
| Max eşzamanlı | 5 indirme | Kaynak tükenmesi |
| Max kuyruk | 100 job | Bellek aşımı |
| Timeout | 300s/job | Askıda kalma |
| Retry | Max 3 | Sonsuz döngü |
| Priority | high > normal > low | Öncelik ihlali |

### 4.6 Anti-Ban Stratejisi

#### 4.6.1 Rate Limiting

| Kaynak | Limit | Süre |
|--------|-------|------|
| YouTube | 10 istek/dakika | Per IP |
| Deezer | 5 istek/dakika | Per ARL token |
| Genel | 20 istek/dakika | Per kullanıcı |

#### 4.6.2 Proxy Rotasyonu

```
İstek 1 → Proxy 1 (US)
İstek 2 → Proxy 2 (EU)
İstek 3 → Proxy 3 (Asia)
...
```

#### 4.6.3 User-Agent Çeşitliliği

| Browser | Version | OS |
|---------|---------|-----|
| Chrome | 120+ | Windows 11 |
| Firefox | 121+ | macOS |
| Safari | 17+ | iOS |
| Edge | 120+ | Windows 10 |

#### 4.6.4 ARL Token Rotasyonu

```
Token 1 → Kullan (limit %80'e kadar)
Token 2 → Hazır bekle
Token 3 → Yedek
```

### 4.7 Metadata Yönetimi

#### 4.7.1 Çıkarılan Metadata

| Alan | Kaynak | Zorunlu mu? |
|------|--------|-------------|
| Başlık | YouTube/Deezer | ✅ |
| Sanatçı | YouTube/Deezer | ✅ |
| Albüm | Deezer / MusicBrainz | ❌ |
| Tür | Deezer / last.fm | ❌ |
| Yıl | Deezer / MusicBrainz | ❌ |
| Kapak | YouTube/Deezer | ❌ |
| Süre | Kaynak | ✅ |
| Bitrate | Dosya | ✅ |
| Sample Rate | Dosya | ✅ |

#### 4.7.2 Metadata Kaynak Önceliği

```
Deezer (en doğru) > MusicBrainz > last.fm > YouTube (en düşük)
```

### 4.8 Veritabanı Entegrasyonu

#### 4.8.1 İlgili Tablolar

| Tablo | Kullanım | ADR |
|-------|----------|-----|
| `coremusic_catalog` | İndirme kuyruğu | [[ADR-040-database-authority]] |
| `coremusic_musics` | Şarkı metadata | [[ADR-040-database-authority]] |
| `coremusic_media` | Medya dosya bilgisi | [[ADR-040-database-authority]] |
| `coremusic_logs` | İndirme logları | [[ADR-040-database-authority]] |

#### 4.8.2 DB İşlemleri

```typescript
// İndirme kaydı oluşturma
const insertDownload = async (job: DownloadJob) => {
  await db.prepare(`
    INSERT INTO coremusic_catalog 
    (user_id, source, url, quality, status, priority, created_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
  `).execute([job.userId, job.source, job.url, job.quality, job.status, job.priority]);
};

// Metadata güncelleme
const updateMetadata = async (id: number, metadata: SongMetadata) => {
  await db.prepare(`
    UPDATE coremusic_musics 
    SET title = ?, artist = ?, album = ?, genre = ?, year = ?, cover_url = ?
    WHERE id = ?
  `).execute([metadata.title, metadata.artist, metadata.album, metadata.genre, metadata.year, metadata.coverUrl, id]);
};
```

### 4.9 Hata Yönetimi

#### 4.9.1 Hata Kodları

| Kod | Hata | Çözüm | Öncelik |
|-----|------|-------|---------|
| `DL001` | Kaynak bulunamadı | URL kontrolü | HIGH |
| `DL002` | Engel yeme (ban) | ARL rotasyonu | CRITICAL |
| `DL003` | Ağ hatası | Retry + timeout | MEDIUM |
| `DL004` | Depolama dolu | Alan temizleme | HIGH |
| `DL005` | Metadata çıkarma hatası | Manuel ekleme | LOW |
| `DL006` | Kalite yetersiz | Fallback kalite | MEDIUM |
| `DL007` | Auth hatası | Token yenileme | HIGH |
| `DL008` | Rate limit aşıldı | Bekleme + retry | MEDIUM |
| `DL009` | Dosya bozulması | Yeniden indirme | HIGH |
| `DL010` | Desteklenmeyen format | Format dönüştürme | MEDIUM |
| `DL011` | Metadata eksik | Manuel ekleme | LOW |
| `DL012` | Kapak görseli yok | Varsayılan görsel | LOW |
| `DL013` | Eşzamanlı indirme limiti | Kuyrukta bekleme | MEDIUM |
| `DL014` | Kullanıcı quota aştı | Quota artırma | HIGH |
| `DL015` | DB bağlantı hatası | Retry + fallback | HIGH |

#### 4.9.2 Hata Öncelik Matrisi

| Öncelik | Süre | Aksiyon |
|---------|------|---------|
| CRITICAL | Anlık | Servis durdur + alert |
| HIGH | 1 saat | Düzeltme + bildirim |
| MEDIUM | 4 saat | Log + retry |
| LOW | 24 saat | Manuel müdahale |

#### 4.9.3 Retry Stratejisi

| Deneme | Bekleme | Multiplier | Max |
|--------|---------|-----------|-----|
| 1. retry | 1sn | — | — |
| 2. retry | 5sn | ×2 | — |
| 3. retry | 15sn | ×3 | — |
| 4. retry | 60sn | ×4 | Max deneme |

### 4.10 Monitoring & Alerting

#### 4.10.1 İzlenen Metrikler

| Metrik | Eşik | Alert |
|--------|------|-------|
| Aktif indirme | >10 | Warning |
| Başarısız oranı | >%10 | Error |
| Ortalama süre | >120sn | Warning |
| Queue boyutu | >50 | Warning |
| CPU kullanımı | >%80 | Error |
| Disk kullanımı | >%90 | Critical |
| Bellek kullanımı | >%80 | Error |
| WebSocket bağlantıları | >100 | Warning |

#### 4.10.2 Alert Kanalları

| Kanal | Kullanım |
|-------|----------|
| Email | Günlük raporlar |
| Slack | Gerçek zamanlı alert |
| PagerDuty | CRITICAL alert'ler |
| Dashboard | Anlık durum |

#### 4.10.3 Dashboard

```
┌─────────────────────────────────────────┐
│ Download Service Dashboard              │
├─────────────────────────────────────────┤
│ Active: 3 │ Queue: 12 │ Failed: 1      │
├─────────────────────────────────────────┤
│ CPU: 45% │ Memory: 62% │ Disk: 34%     │
├─────────────────────────────────────────┤
│ YouTube: 8 │ Deezer: 4 │ FLAC: 1       │
├─────────────────────────────────────────┤
│ Avg Speed: 2.4 MB/s │ Uptime: 99.97%   │
└─────────────────────────────────────────┘
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Eşzamanlı çoklu indirme (limit yok) | Max 5 eşzamanlı | Kaynak tükenmesi |
| ARL token kodda | Credential vault | Güvenlik açığı |
| Hardcoded proxy | Dinamik proxy rotasyonu | Engellenme |
| Blocking I/O | Async/await | Gecikme |
| Hardcoded path | Konfigüre edilebilir | Taşınabilirlik |
| Hata log'da hassas veri | `[REDACTED]` | Veri sızıntısı |
| Timeout olmayan istek | 300s timeout | Askıda kalma |
| Retry sınırsız | Max 3 retry | Sonsuz döngü |
| Metadata eksik | Zorunlu alanlar zorunlu | Kütüphane bozukluğu |
| PLAINTEXT credential | AES-256-GCM | Güvenlik açığı |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Engellenme (ban) | Çoklu istek | ARL rotasyonu + proxy |
| Ağ kopması | İnternet kesintisi | Queue'da bekleme + retry |
| Depolama dolu | Disk %100 | Eski dosyaları temizle |
| Kullanıcı iptal | İndirme sırasında | Graceful cancellation |
| Metadata çıkarma hatası | Yanlış format | Manuel ekleme |
| Dosya boyutu aşımı | >1GB dosya | Boyut kontrolü |
| Concurrent download conflict | Aynı URL | Queue'da birleştirme |
| Token süresi dolmuş | Deezer ARL | Otomatik yenileme |
| Format uyumsuzluğu | Desteklenmeyen codec | Dönüştürme |
| Servis çökmesi | Node.js crash | PM2 ile otomatik restart |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Max 5 eşzamanlı indirme | Kaynak tükenmesi |
| 2 | ARL token credential vault'ta | Güvenlik açığı |
| 3 | 300s timeout zorunlu | Askıda kalma |
| 4 | Max 3 retry | Sonsuz döngü |
| 5 | FLAC 24/32-bit öncelik | Kalite düşüşü |
| 6 | Metadata çıkarma zorunlu | Kütüphane bozukluğu |
| 7 | Anti-ban aktif | Engellenme |
| 8 | WebSocket durum bildirimi | UX düşüşü |
| 9 | DB entegrasyonu zorunlu | İzole veri |
| 10 | Health check endpoint | Servis durumu |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-028-anti-ban-system]] | Anti-ban sistemi | Rate limiting, proxy |
| [[ADR-040-database-authority]] | DB authority | Metadata yönetimi |
| [[ADR-039-7-service-platform-architecture]] | 7 servis mimarisi | Servis konumu |
| [[ADR-034-credential-vault-normalization]] | Credential vault | ARL token saklama |
| [[ADR-006-performance-targets]] | Performans hedefleri | Timeout limitleri |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-028-anti-ban-system]] | Anti-ban |
| § 4.3 API | [[architecture/l2-routing]] | Endpoint tasarımı |
| § 4.5 Queue | [[architecture/l0-infrastructure]] | Altyapı |
| § 4.6 Anti-ban | [[ADR-034-credential-vault-normalization]] | Token yönetimi |
| § 4.8 DB | [[ADR-040-database-authority]] | Veritabanı |
| § 5 Yasak | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 6 Edge | [[ADR-006-performance-targets]] | Performans |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 ADR | [[ADR-039-7-service-platform-architecture]] | Servis yapısı |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Node.js** | JavaScript runtime ortamı |
| **TypeScript** | JavaScript ile type safety |
| **yt-dlp** | YouTube video indirme aracı |
| **deemix** | Deezer müzik indirme aracı |
| **FLAC** | Free Lossless Audio Codec — Kayıpsız ses |
| **ARL Token** | Deezer erişim token'ı |
| **Anti-ban** | Engellenme önleme stratejisi |
| **Queue** | İndirme kuyruğu |
| **Proxy** | Ara sunucu (IP gizleme) |
| **Metadata** | Medya dosyası bilgisi |
| **WebSocket** | Gerçek zamanlı iletişim protokolü |
| **PM2** | Node.js process manager |
| **Graceful shutdown** | Yumuşak kapatma |
| **Rate limiting** | İstek hız sınırlaması |
| **Fallback** | Yedek mekanizma |
| **CD Quality** | 44.1kHz/16-bit (standart) |
| **Hi-Res** | Yüksek çözünürlük (24-bit+) |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| SSOT Authority | ADR-026 Download Service Architecture |
| Last Updated | 2026-08-08 |
| ADR References | 5 |
| Cross References | 9 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 17 |
| API Endpoints | 7 REST + 6 WebSocket |
| Source Adapters | 3 (YouTube, Deezer, FLAC) |
| Error Codes | 10 |
| Queue Limit | 100 jobs |
| Concurrent Limit | 5 downloads |
| Timeout | 300s |
| Retry Limit | 3 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
