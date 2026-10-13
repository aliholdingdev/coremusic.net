---
type: system
category: electronics-service
title: "CoreMusic Electronics Service Architecture"
date: 2026-08-09
updated: 2026-08-10
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics Service Architecture

**Zorunlu Bağlantılar:** [[electronic/software-architecture]] · [[electronic/device-architecture]] · [[electronic/device-ecosystem]] · [[ecosystem/7-service-integration]] · [[ADR-039-7-service-platform-architecture]]

---

## 1. Amaç

CoreMusic ELECTRONICS **tek bir uygulama değildir**. Bağımsız servislerden oluşur ve her servisin net bir sorumluluk alanı vardır. Servisler arası iletişim message bus üzerinden gerçekleştirilir. Her servis: tek sorumluluk, bağımsız geliştirme, bağımsız test, bağımsız dağıtım prensibiyle çalışır.

---

## 2. Servis Mimarisi (13 Servis)

```
┌─────────────────────────────────────────────────────────────────┐
│                        API Gateway                              │
│              (Routing, Auth, Rate Limit)                        │
├─────────────────────────────────────────────────────────────────┤
│  Core Services          │  Media Services        │  System     │
│  ├── 1. Auth Service    │  ├── 4. Audio Service  │  Services   │
│  ├── 2. User Service    │  ├── 5. DSP Service    │  ├── 9. AI  │
│  └── 3. Device Service  │  ├── 6. Streaming      │  ├── 10. Update
│                         │  ├── 7. Download       │  ├── 11. Notify
│                         │  └── 8. Media Library  │  ├── 12. Monitor
│                         │                        │  └── 13. Log
├─────────────────────────────────────────────────────────────────┤
│                     Message Bus (Event Driven)                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. Servis Detayları (13 Servis)

### 3.1 Auth Service

| İşlev | Açıklama |
|-------|----------|
| Login | Kullanıcı girişi (username/password, OAuth) |
| Logout | Oturum kapatma |
| Token Management | JWT üretme, yenileme, iptal |
| Device Auth | Cihaz kimlik doğrulama (X.509) |
| Session Management | Oturum yönetimi (cross-subdomain) |
| MFA | Çoklu faktörlü kimlik doğrulama |

**API Endpoints:**
- `POST /auth/login` — Kullanıcı girişi
- `POST /auth/logout` — Oturum kapatma
- `POST /auth/token/refresh` — Token yenileme
- `POST /auth/device/register` — Cihaz kaydı
- `GET /auth/session/check` — Oturum kontrolü

Detaylar: [[architecture/08-auth/index]], [[ADR-043-auth-subdomain-consolidation]]

### 3.2 User Service

| İşlev | Açıklama |
|-------|----------|
| Profile Management | Profil görüntüleme/güncelleme |
| Roles | Rol yönetimi (admin, user, device) |
| Permissions | İzin yönetimi (RBAC) |
| Preferences | Tercihler (tema, dil, birim) |
| Theme | Tema seçimi (ADR-044) |
| Language | Dil seçimi |

**API Endpoints:**
- `GET /users/me` — Mevcut kullanıcı
- `PUT /users/me` — Profil güncelleme
- `GET /users/:id/roles` — Roller
- `PUT /users/me/preferences` — Tercihler

Detaylar: [[ADR-044-dynamic-user-theme-engine]]

### 3.3 Device Service

| İşlev | Açıklama |
|-------|----------|
| Registration | Cihaz kaydı |
| Discovery | Cihaz algılama (mDNS, SSDP) |
| Health Check | Sağlık izleme |
| Configuration | Uzaktan yapılandırma |
| Firmware Version | Firmware versiyon yönetimi |
| Driver Version | Sürücü versiyon yönetimi |

**API Endpoints:**
- `GET /devices` — Cihaz listesi
- `POST /devices` — Cihaz kaydı
- `GET /devices/:id/health` — Sağlık durumu
- `PUT /devices/:id/config` — Yapılandırma
- `POST /devices/:id/update` — Güncelleme başlatma

### 3.4 Audio Service

| İşlev | Açıklama |
|-------|----------|
| Playback | Ses oynatma (play, pause, stop, seek) |
| Queue | Oynatma kuyruğu |
| Playlist | Çalma listesi yönetimi |
| Audio Session | Ses oturumu yönetimi |
| Volume | Ses seviyesi kontrolü |
| Equalizer | EQ ayarlama |

**API Endpoints:**
- `POST /audio/play` — Oynat
- `POST /audio/pause` — Duraklat
- `POST /audio/stop` — Durdur
- `PUT /audio/seek` — İlerle
- `GET /audio/queue` — Kuyruk
- `PUT /audio/volume` — Ses seviyesi

### 3.5 DSP Service

| İşlev | Açıklama |
|-------|----------|
| EQ | 31-bant parametrik equalizer |
| Compressor | Sıkıştırıcı |
| Limiter | Limiter |
| Crossover | Bass/treble ayrımı |
| Delay | Gecikme |
| Reverb | Yankı |
| FIR Filter | Finite Impulse Response |
| IIR Filter | Infinite Impulse Response |
| FFT Analysis | Frekans analizi |

**API Endpoints:**
- `GET /dsp/preset` — Mevcut preset
- `PUT /dsp/eq` — EQ ayarlama
- `PUT /dsp/compressor` — Compressor ayarlama
- `POST /dsp/preset` — Preset kaydetme
- `GET /dsp/fft` — FFT analizi

Detaylar: [[electronic/dsp/index]], [[ADR-062-dsp-pipeline-architecture]], [[ADR-025-professional-eq-system]]

### 3.6 Streaming Service

| İşlev | Açıklama |
|-------|----------|
| HTTP Streaming | HTTP/HTTPS streaming |
| Local Streaming | Yerel dosya streaming |
| Multi-Room | Çoklu oda senkronizasyon |
| Adaptive Streaming | Adaptive bitrate |
| Protocol Support | RTSP, HLS, DASH |

**API Endpoints:**
- `POST /stream/start` — Stream başlat
- `POST /stream/stop` — Stream durdur
- `PUT /stream/zone` — Zone yapılandırma
- `GET /stream/status` — Stream durumu

### 3.7 Download Service

| İşlev | Açıklama |
|-------|----------|
| Queue Management | İndirme kuyruğu yönetimi |
| Resume | Kesintili indirme devamı |
| Retry | Otomatik yeniden deneme |
| Cache | Önbellekleme |
| Metadata | Metadata çıkarma |

**API Endpoints:**
- `POST /download/add` — İndirme ekle
- `DELETE /download/:id` — İndirmeyi kaldır
- `GET /download/queue` — Kuyruk listesi
- `PUT /download/:id/pause` — Duraklat
- `PUT /download/:id/resume` — Devam

Detaylar: [[projects/download-service]], [[ADR-026-download-service-architecture]]

### 3.8 Media Library Service

| İşlev | Açıklama |
|-------|----------|
| Scan | Medya tarama |
| Index | Dizin oluşturma |
| Metadata | Metadata çıkarma/güncelleme |
| Album | Albüm yönetimi |
| Artist | Sanatçı yönetimi |
| Genre | Tür yönetimi |
| Search | Tam metin arama |

**API Endpoints:**
- `GET /library/scan` — Tarama başlat
- `GET /library/songs` — Şarkı listesi
- `GET /library/albums` — Albüm listesi
- `GET /library/artists` — Sanatçı listesi
- `GET /library/search?q=` — Arama

### 3.9 AI Service

| İşlev | Açıklama |
|-------|----------|
| Sistem Analizi | Donanım/yazılımperformans analizi |
| Kod Analizi | Kod kalitesi, güvenlik analizi |
| Donanım Analizi | PCB, termal, sinyal analizi |
| Dokümantasyon | Otomatik doküman üretimi |
| Hata Analizi | Root cause, troubleshooting |
| Tahmine Dayalı Bakım | Predictive maintenance |
| Müzik Önerisi | Kişiselleştirilmiş öneri |
| Otomatik EQ | AI destekli EQ ayarlama |

Detaylar: [[architecture/ai/ai-engine]], [[architecture/ai/ai-workflow]], [[ADR-030-ai-strategy-core]]

### 3.10 Update Service

| İşlev | Açıklama |
|-------|----------|
| Firmware Update | Firmware güncelleme |
| Driver Update | Sürücü güncelleme |
| DSP Profile Update | DSP profili güncelleme |
| AI Model Update | AI model güncelleme |
| Config Update | Konfigürasyon güncelleme |
| UI Assets Update | UI varlık güncelleme |

**API Endpoints:**
- `GET /update/check` — Güncelleme kontrolü
- `POST /update/apply` — Güncelleme uygula
- `POST /update/rollback` — Geri dönüş
- `GET /update/history` — Güncelleme geçmişi

### 3.11 Notification Service

| İşlev | Açıklama |
|-------|----------|
| Push Notification | Push bildirim |
| Email | E-posta bildirimi |
| In-App | Uygulama içi bildirim |
| Webhook | Webhook bildirimi |

### 3.12 Monitoring Service

| Metrik | Açıklama | Eşik |
|--------|----------|------|
| CPU Usage | İşlemci kullanımı | >90% |
| RAM Usage | Bellek kullanımı | >85% |
| Disk Usage | Disk kullanımı | >90% |
| Network | Ağ trafiği | >1Gbps |
| DSP Load | DSP yükü | >80% |
| Driver Errors | Sürücü hataları | >0 |
| Temperature | Sıcaklık | >80°C |
| Audio Buffer | Ses buffer | Underrun |
| Latency | Gecikme | >20ms |

**API Endpoints:**
- `GET /monitoring/metrics` — Metrikler
- `GET /monitoring/health` — Sağlık durumu
- `GET /monitoring/alerts` — Uyarılar

### 3.13 Logging Service

| İşlev | Açıklama |
|-------|----------|
| Structured Logging | Yapılandırılmış günlük |
| Audit Trail | Denetim izi |
| Log Aggregation | Günlük toplama |
| Log Rotation | Günlük döndürme |

Detaylar: [[log.md]], [[ADR-004-multi-domain-spa]]

---

## 4. Servis İletişimi

### 4.1 İletişim Akışı

```
Application Contract → Message Bus → Target Service
```

Tüm servislerarası iletişim **event-driven** message bus üzerinden gerçekleşir. Servisler birbirinin DB'sine **erişemez**.

### 4.2 Event Kataloğu

| Event | Kaynak | Hedef |
|-------|--------|-------|
| `UserCreated` | Auth Service | User, Monitoring, Logging |
| `UserLoggedIn` | Auth Service | Monitoring, Logging |
| `DeviceRegistered` | Device Service | Monitoring, Notification |
| `DeviceOffline` | Device Service | Monitoring, Notification |
| `DeviceOnline` | Device Service | Monitoring, Notification |
| `FirmwareUpdated` | Update Service | Device, Notification, Logging |
| `AudioPlaybackStarted` | Audio Service | DSP, Monitoring |
| `AudioPlaybackStopped` | Audio Service | DSP, Monitoring |
| `PlaylistCreated` | Media Library | Monitoring |
| `DownloadCompleted` | Download Service | Media Library, Notification |
| `DSPPresetChanged` | DSP Service | Audio, Monitoring |
| `HealthCheckFailed` | Monitoring Service | Notification, Logging |
| `SecurityAlertDetected` | Auth Service | Notification, Logging |

### 4.3 Service İletişim Kuralları

| Kural | Açıklama |
|-------|----------|
| Async First | Tüm iletişim asenkron |
| Event Sourcing | Olaylar kaydedilir |
| Idempotency | Tekrarlanabilir işlemler |
| Circuit Breaker | Bağımlılık kırılma noktası |
| Retry with Backoff | Üstel geri çekilme ile yeniden deneme |
| Dead Letter Queue | Başarısız mesajlar |
| Service Isolation | Hiçbir servis diğerinin DB'sine erişmez |

---

## 5. Servis Yaşam Döngüsü

```
Initialize → Config → Dependency → Health → Ready → Running → Monitoring → Shutdown
```

| Aşama | Açıklama |
|-------|----------|
| Initialize | Servis başlatma, bağımlılıklar kontrol |
| Config | Konfigürasyon yükleme (env, DB, file) |
| Dependency | Bağımlılık servislerinin hazır olması |
| Health | İlk sağlık kontrolü |
| Ready | Trafik almaya hazır |
| Running | Aktif servis |
| Monitoring | Sürekli izleme, metrik toplama |
| Shutdown | Graceful kapanma, cleanup |

---

## 6. API Gateway

| Özellik | Açıklama |
|---------|----------|
| Routing | URL tabanlı yönlendirme |
| Authentication | Token doğrulama |
| Rate Limiting | İstek sınırlandırma (60 req/60s) |
| Validation | Giriş doğrulama |
| Logging | İstek günlüğü |
| Load Balancing | Yük dengeleme |
| Circuit Breaker | Servis koruması |

Detaylar: [[architecture/03-contracts/api-architecture-master]], [[architecture/03-contracts/api-rate-limit]]

---

## 7. Servis Sağlık Kontrolü

| Durum | Tanım | Aksiyon |
|-------|-------|---------|
| Healthy | Servis normal | Devam |
| Degraded | Yavaş yanıt | Uyarı |
| Failed | Servis çalışmıyor | Escalation |
| Unknown | Durum bilinmiyor | Yeniden kontrol |

Detaylar: [[ecosystem/service-health-check]]

---

## 8. AI Servis Entegrasyonu

| AI Yeteneği | Kullanım |
|-------------|----------|
| Sistem Analizi | Donanım/yazılım performans analizi |
| Kod Analizi | Kod kalitesi, güvenlik taraması |
| Donanım Analizi | PCB, termal, sinyal kalitesi |
| Dokümantasyon | Otomatik doküman üretimi |
| Hata Analizi | Root cause, troubleshooting |
| Predictive Maintenance | Tahmine dayalı bakım |
| Music Recommendation | Kişiselleştirilmiş öneri |
| Auto-EQ | Otomatik equalizer ayarlama |
| Room Correction | Oda akustik düzeltmesi |

Detaylar: [[architecture/ai/ai-engine]], [[architecture/ai/ai-workflow]], [[ADR-030-ai-strategy-core]]

---

## 9. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/software-architecture]] | Yazılım mimarisi |
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi |
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | Health check |
| [[ecosystem/service-communication]] | Servis iletişim |
| [[ADR-039-7-service-platform-architecture]] | 7-servis ADR |
| [[ADR-062-dsp-pipeline-architecture]] | DSP pipeline |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Services | 13 |
| API Endpoints | 50+ |
| Event Types | 13+ |
| Service Lifecycle | 8 aşamalı |
| Cross References | 8 |

---

## 11. Servis Kabul Kontrol Listesi (Uygulama Öncesi)

Her servis (§2–§3'teki 13 servis) canlıya alınmadan önce adımlar sırayla tamamlanır; atlanan adım varsa servis **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | Servis sınırını doğrula (sorumluluk, sahip olduğu veri) | §3 | servis sözleşmesi |
| 2 | API Gateway rotasını tanımla | §6 | rota listesi |
| 3 | Olay kataloğuna olayını ekle | §4.2 | event kayıt kimliği |
| 4 | İletişim kuralına uy (senkron/asenkron ayrımı) | §4.3 | iletişim şeması |
| 5 | Yaşam döngüsü aşamasını tanımla | §5 | 8 aşama kaydı |
| 6 | Sağlık kontrolünü bağla | §7 | health endpoint |
| 7 | Logging/Monitoring'e bağla | §3.12 · §3.13 | log/metrik akışı |
| 8 | API sürümleme kuralını uygula | §6 | sürüm stratejisi |

### 11.1 Sık Yapılan Servis Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Servis sınırı olmadan doğrudan başka servisin verisine erişim | Bağımlılık baku (bow-tie) | §3 — yalnız kendi verisi |
| 2 | Senkron zincirin uzaması (masaüstü gecikme) | Tek hata tüm zinciri düşürür | §4.3 — olay tabanlıya böl |
| 3 | Event kataloğunda olmayan olay yayını | Tüketici tarafında sessiz kayıp | §4.2 kaydı zorunlu |
| 4 | Health check'in yalnız uptime döndürmesi | Bağımlılık kopmuşken "healthy" | §7 — bağımlılık kontrolü |
| 5 | Log seviyesinin her yerde "info" olması | Olayların kaybı, teşhis imkânsız | §3.13 — seviye disiplini |
| 6 | Ölçüm/sağlık verisi olmadan "çalışıyor" iddiası | Kanıtsız iddia | `⚠️ VERIFICATION REQUIRED` |

### 11.2 Servis DUR Kapıları

| Kapı | Koşul | Sonuç |
|---|---|---|
| Sınır kapısı | Servin başka servisin DB'sine doğrudan erişimi | DUR — katman ihlali |
| Olay kapısı | Katalogda olmayan event yayınlanıyor | DUR — §4.2 |
| Sağlık kapısı | Health check bağımlılık içermiyor | DUR — §7 |
| Ölçüm kapısı | Sağlama/metrik kanıtı yok | `⚠️ VERIFICATION REQUIRED` |
| Sürüm kapısı | Geriye uyumsuz API değişikliği – sürüm artışı yok | DUR — §6 |

**Not:** Bu bölüm mevcut §1–§10 içeriğini **değiştirmez**; yalnız denetim sırasını ve dur-kararlarını toplar. Repo'da servis kodu yüzeyi (PHP endpoint) bu vault dosyasının dışındadır — kod iddiası yazılmaz.

### 11.3 Servis → Alan Eşlemesi (Sorumluluk Haritası)

13 servisin hangi vault alanına dayandığı — çelişkide ilgili alan/AZR kazanır:

| Servis (§3) | Dayandığı alan | Not |
|---|---|---|
| Auth (§3.1) | `.ai/architecture/07-security` | Auth bypass ADR-008 |
| User (§3.2) | `.ai/architecture/` (domain) | Kullanıcı verisi sahipliği |
| Device (§3.3) | `electronic/device-ecosystem` | Cihaz kayıt/durum |
| Audio (§3.4) | `electronic/audio-architecture` | Ses akışı |
| DSP (§3.5) | `electronic/dsp/index` · ADR-062 | Aşama sırası bağlayıcı |
| Streaming (§3.6) | `electronic/audio-architecture` | Protokol/akış |
| Download (§3.7) | `.ai/architecture/` | Dosya teslim |
| Media Library (§3.8) | `.ai/architecture/` | Meta veri |
| AI (§3.9) | `.ai/architecture/` | Model/entegrasyon |
| Update (§3.10) | `electronic/firmware-architecture` §7 | OTA imza/CRC/rollback |
| Notification (§3.11) | `.ai/architecture/` | Olay üretimi |
| Monitoring (§3.12) | `.ai/architecture/` | Metrik/topoloji |
| Logging (§3.13) | `.ai/log.md` | Append-only kuralı |

### 11.4 Ölçüm ve Kanıt Durumu

| İddia türü | Durum | Davranış |
|---|---|---|
| 13 servis / 50+ endpoint / 13+ event (§10) | Dokümante sayım | Kod envanteriyle kesişimi **ölçülmedi** → `⚠️ VERIFICATION REQUIRED` |
| Servis sağlık/latency değerleri | Ölçülmedi | Ölçümü olmadan SLA değeri yazılmaz |
| Servis kodu (PHP) | Bu vault dosyasının dışında | Kod iddiası bu dosyada yapılmaz |

**Çapraz referans:** `[[electronic/index]]` · `[[electronic/dsp/index]]` · `[[electronic/firmware-architecture]]` · `[[electronic/audio-architecture]]` · `[[electronic/device-ecosystem]]`.

### 11.5 Servis Yaşam Döngüsü Kapıları (§5 — Hızlı Referans)

§5'teki 8 aşama (`Initialize → Config → Dependency → Health → Ready → Running → Monitoring → Shutdown`) — aşama atlanmaz, bir öncekinin başarısı şarttır:

| Aşama (§5) | Denetim karşılığı | Kapı |
|---|---|---|
| Initialize | Servis başlatma + bağımlılık kontrolü | §11 adım 1 |
| Config | env/DB/file konfigürasyonu | Konfigürasyon yoksa DUR |
| Dependency | Bağımlı servisler hazır | Bağımlı yoksa Ready'ye geçme |
| Health | İlk sağlık kontrolü | §7 — bağımlılık dahil |
| Ready | Trafik almaya hazır | Gateway rota açılışı (§6) |
| Running | Aktif servis | Olay/endpoint kaydı (§4.2) |
| Monitoring | Sürekli metrik toplama | §3.12 · §3.13 |
| Shutdown | Graceful kapanma, cleanup | Plan yoksa DUR |

**Not:** Yaşam döngüsü aşama **sırası** mevcut §5'ten birebir alınmıştır; yeni aşama **tanımlanmaz**.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
