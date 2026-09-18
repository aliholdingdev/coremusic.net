---
type: ecosystem
category: service-communication
title: "Service Communication â€” CoreMusic Ä°letiÅŸim Protokolleri"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/ecosystem/service-communication.md"
  adr:
    - "decisions/accepted/ADR-032-ipc-contract-versioning"
    - "decisions/accepted/ADR-084-api-gateway-architecture"
    - "decisions/accepted/ADR-086-event-driven-architecture"
---

# Service Communication â€” CoreMusic Ä°letiÅŸim Protokolleri

**Ä°lgili ADR:** [[decisions/accepted/ADR-032-ipc-contract-versioning]] Â· [[decisions/accepted/ADR-084-api-gateway-architecture]] Â· [[decisions/accepted/ADR-086-event-driven-architecture]]

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[ecosystem/7-service-integration]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

7 servis arasÄ±ndaki tÃ¼m iletiÅŸim protokollerini, mesaj formatlarÄ±nÄ±, retry stratejilerini ve versiyonlama kurallarÄ±nÄ± tanÄ±mlar.

---

## 2. Protokol Matrisi

| Protokol | KullanÄ±m | Gecikme | GÃ¼venlik | Servisler |
|----------|----------|---------|----------|-----------|
| **HTTP REST** | Senkron API | 50-200ms | TLS 1.3 | TÃ¼mÃ¼ |
| **WebSocket** | GerÃ§ek zamanlÄ± | 10-50ms | WSS | Media, Audio, Download |
| **gRPC** | YÃ¼ksek performans IPC | 1-10ms | mTLS | Servisler arasÄ± (gelecek) |
| **Shared Memory** | Zero-copy veri | <0.1ms | Process-level | Audio â†” Device |
| **WebRTC** | P2P ses akÄ±ÅŸÄ± | 5-50ms | DTLS | Network Audio |

---

## 3. HTTP REST KurallarÄ±

### 3.1 Request FormatÄ±

```
POST /api/v1/{resource}
Content-Type: application/json
Authorization: Bearer {jwt_token}
X-Request-ID: {uuid-v4}
X-Correlation-ID: {correlation-id}
X-Service-Name: {source-service}

{
  "data": { ... },
  "meta": {
    "version": "1.0.0",
    "timestamp": "2026-08-15T12:00:00Z"
  }
}
```

### 3.2 Response FormatÄ±

```json
{
  "status": "success|error",
  "data": { ... },
  "meta": {
    "version": "1.0.0",
    "request_id": "uuid",
    "latency_ms": 42
  },
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Email is required",
    "details": [...]
  }
}
```

### 3.3 HTTP Status KodlarÄ±

| Kod | KullanÄ±m |
|-----|----------|
| 200 | BaÅŸarÄ±lÄ± |
| 201 | OluÅŸturuldu |
| 204 | Ä°Ã§erik yok (baÅŸarÄ±lÄ±) |
| 400 | GeÃ§ersiz istek |
| 401 | Yetkisiz |
| 403 | Yasak |
| 404 | BulunamadÄ± |
| 409 | Ã‡akÄ±ÅŸma |
| 422 | Ä°ÅŸlenemedi |
| 429 | Rate limit |
| 500 | Sunucu hatasÄ± |
| 503 | Servis kullanÄ±lamÄ±yor |

---

## 4. WebSocket KurallarÄ±

### 4.1 BaÄŸlantÄ±

```
wss://{service}:{port}/ws?token={jwt}
```

### 4.2 Mesaj FormatÄ±

```json
{
  "type": "event|command|response|error",
  "event": "playback.started",
  "data": { ... },
  "timestamp": "2026-08-15T12:00:00Z"
}
```

### 4.3 Heartbeat

| Parametre | DeÄŸer |
|-----------|-------|
| Interval | 30s |
| Timeout | 10s |
| Max Miss | 3 |

---

## 5. Event Bus (PSR-14)

### 5.1 Event YayÄ±nÄ±

```
Service A â†’ EventDispatcher â†’ [Listener1, Listener2, Listener3]
```

### 5.2 Event FormatÄ±

```php
final class TrackDownloadedEvent
{
    public function __construct(
        public readonly string $trackId,
        public readonly string $userId,
        public readonly string $format,
        public readonly int $fileSize,
        public readonly \DateTimeImmutable $occurredAt,
    ) {}
}
```

### 5.3 Event Kategorileri

| Kategori | Event'ler | YayÄ±nlayan |
|----------|-----------|-----------|
| **Auth** | UserAuthenticated, UserLoggedOut, SessionExpired | Control |
| **Media** | TrackAdded, AlbumUpdated, LibrarySynced | Media |
| **Playback** | PlaybackStarted, PlaybackPaused, PlaybackEnded | Audio |
| **Download** | DownloadQueued, DownloadStarted, TrackDownloaded | Download |
| **Device** | DeviceConnected, DeviceDisconnected, DeviceSynced | Device |
| **AI** | RecommendationGenerated, AutoDownloadTriggered | AI |

---

## 6. IPC SÃ¶zleÅŸmeleri (ADR-032)

### 6.1 Versiyonlama

```
/api/v{major}/{resource}
```

| Version | DeÄŸiÅŸiklik | Geriye DÃ¶nÃ¼k |
|---------|------------|--------------|
| major | Breaking change | âŒ |
| minor | New feature | âœ… |
| patch | Bug fix | âœ… |

### 6.2 Contract First

```
OpenAPI Spec â†’ DTO â†’ Contract â†’ Validation â†’ Use Case â†’ Kod
```

**Kod hiÃ§bir zaman sÃ¶zleÅŸmeden Ã¶nce yazÄ±lmaz.**

---

## 7. Retry & Timeout Stratejisi

| Parametre | DeÄŸer |
|-----------|-------|
| Connect Timeout | 5s |
| Read Timeout | 30s |
| Max Retry | 3 |
| Initial Delay | 100ms |
| Max Delay | 5000ms |
| Backoff | Exponential (x2) |
| Jitter | Â±20% |

### 7.1 Retry KuralÄ±

| HTTP Kodu | Retry? | AÃ§Ä±klama |
|-----------|--------|----------|
| 2xx | âŒ | BaÅŸarÄ±lÄ± |
| 4xx | âŒ | Ä°stemci hatasÄ± |
| 429 | âœ… | Rate limit â€” Retry-After header'Ä± |
| 5xx | âœ… | Sunucu hatasÄ± |
| Timeout | âœ… | BaÄŸlantÄ± kopmasÄ± |

---

## 8. Service Discovery

| YÃ¶ntem | KullanÄ±m | Durum |
|--------|----------|-------|
| Static config | Basit kurulum | âœ… Mevcut |
| DNS | Subdomain tabanlÄ± | âœ… Mevcut |
| Service Registry | Dinamik keÅŸif | ğŸ“‹ Gelecek |

---

## 9. Cross References

| Dosya | AmaÃ§ |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | SaÄŸlÄ±k kontrolÃ¼ |
| [[ecosystem/error-recovery]] | Hata kurtarma |
| [[architecture/03-contracts/api-architecture-master]] | API mimarisi |
| [[architecture/10-network]] | AÄŸ protokolleri |

---

## 10. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team Â· Human Mode Â· Truth Mode verified |
| **Protocol Count** | 5 |
| **Event Categories** | 6 |
| **HTTP Status Codes** | 11 |
| **ADR Coverage** | 032, 084, 086 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team Â· Human Mode Â· Truth Mode

---

## Faz 3 DoÃ„Å¸rulamasÃ„Â±: Servis Durum Matrisi

| Servis | Entegrasyon Durumu | KanÃ„Â±t / AÃƒÂ§Ã„Â±klama |
|--------|--------------------|------------------|
| Control Service | **IMPLEMENTED** | shared/src/, uth.coremusic.net/ aktif |
| Media Service | **PLANNED** | TasarÃ„Â±m aÃ…Å¸amasÃ„Â±nda |
| Audio Service | **PLANNED** | C++ NevaEngine taslak |
| Device Service | **PLANNED** | DonanÃ„Â±m (I2S/BLE) beklemede |
| Network Audio | **PLANNED** | WebRTC mimarisi ÃƒÂ§izildi |
| AI Service | **PLANNED** | Python entegrasyonu planlandÃ„Â± |
| Download Service | **PLANNED** | Node.js servis klasÃƒÂ¶rÃƒÂ¼ yok |

