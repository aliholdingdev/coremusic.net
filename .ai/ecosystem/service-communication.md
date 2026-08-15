---
type: ecosystem
category: service-communication
title: "Service Communication — CoreMusic İletişim Protokolleri"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/service-communication.md"
  adr:
    - "decisions/accepted/ADR-032-ipc-contract-versioning"
    - "decisions/accepted/ADR-084-api-gateway-architecture"
    - "decisions/accepted/ADR-086-event-driven-architecture"
---

# Service Communication — CoreMusic İletişim Protokolleri

**İlgili ADR:** [[decisions/accepted/ADR-032-ipc-contract-versioning]] · [[decisions/accepted/ADR-084-api-gateway-architecture]] · [[decisions/accepted/ADR-086-event-driven-architecture]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ecosystem/7-service-integration]] · [[architecture/00-overview/architecture-master]]

---

## 1. Amaç

7 servis arasındaki tüm iletişim protokollerini, mesaj formatlarını, retry stratejilerini ve versiyonlama kurallarını tanımlar.

---

## 2. Protokol Matrisi

| Protokol | Kullanım | Gecikme | Güvenlik | Servisler |
|----------|----------|---------|----------|-----------|
| **HTTP REST** | Senkron API | 50-200ms | TLS 1.3 | Tümü |
| **WebSocket** | Gerçek zamanlı | 10-50ms | WSS | Media, Audio, Download |
| **gRPC** | Yüksek performans IPC | 1-10ms | mTLS | Servisler arası (gelecek) |
| **Shared Memory** | Zero-copy veri | <0.1ms | Process-level | Audio ↔ Device |
| **WebRTC** | P2P ses akışı | 5-50ms | DTLS | Network Audio |

---

## 3. HTTP REST Kuralları

### 3.1 Request Formatı

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

### 3.2 Response Formatı

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

### 3.3 HTTP Status Kodları

| Kod | Kullanım |
|-----|----------|
| 200 | Başarılı |
| 201 | Oluşturuldu |
| 204 | İçerik yok (başarılı) |
| 400 | Geçersiz istek |
| 401 | Yetkisiz |
| 403 | Yasak |
| 404 | Bulunamadı |
| 409 | Çakışma |
| 422 | İşlenemedi |
| 429 | Rate limit |
| 500 | Sunucu hatası |
| 503 | Servis kullanılamıyor |

---

## 4. WebSocket Kuralları

### 4.1 Bağlantı

```
wss://{service}:{port}/ws?token={jwt}
```

### 4.2 Mesaj Formatı

```json
{
  "type": "event|command|response|error",
  "event": "playback.started",
  "data": { ... },
  "timestamp": "2026-08-15T12:00:00Z"
}
```

### 4.3 Heartbeat

| Parametre | Değer |
|-----------|-------|
| Interval | 30s |
| Timeout | 10s |
| Max Miss | 3 |

---

## 5. Event Bus (PSR-14)

### 5.1 Event Yayını

```
Service A → EventDispatcher → [Listener1, Listener2, Listener3]
```

### 5.2 Event Formatı

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

| Kategori | Event'ler | Yayınlayan |
|----------|-----------|-----------|
| **Auth** | UserAuthenticated, UserLoggedOut, SessionExpired | Control |
| **Media** | TrackAdded, AlbumUpdated, LibrarySynced | Media |
| **Playback** | PlaybackStarted, PlaybackPaused, PlaybackEnded | Audio |
| **Download** | DownloadQueued, DownloadStarted, TrackDownloaded | Download |
| **Device** | DeviceConnected, DeviceDisconnected, DeviceSynced | Device |
| **AI** | RecommendationGenerated, AutoDownloadTriggered | AI |

---

## 6. IPC Sözleşmeleri (ADR-032)

### 6.1 Versiyonlama

```
/api/v{major}/{resource}
```

| Version | Değişiklik | Geriye Dönük |
|---------|------------|--------------|
| major | Breaking change | ❌ |
| minor | New feature | ✅ |
| patch | Bug fix | ✅ |

### 6.2 Contract First

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

**Kod hiçbir zaman sözleşmeden önce yazılmaz.**

---

## 7. Retry & Timeout Stratejisi

| Parametre | Değer |
|-----------|-------|
| Connect Timeout | 5s |
| Read Timeout | 30s |
| Max Retry | 3 |
| Initial Delay | 100ms |
| Max Delay | 5000ms |
| Backoff | Exponential (x2) |
| Jitter | ±20% |

### 7.1 Retry Kuralı

| HTTP Kodu | Retry? | Açıklama |
|-----------|--------|----------|
| 2xx | ❌ | Başarılı |
| 4xx | ❌ | İstemci hatası |
| 429 | ✅ | Rate limit — Retry-After header'ı |
| 5xx | ✅ | Sunucu hatası |
| Timeout | ✅ | Bağlantı kopması |

---

## 8. Service Discovery

| Yöntem | Kullanım | Durum |
|--------|----------|-------|
| Static config | Basit kurulum | ✅ Mevcut |
| DNS | Subdomain tabanlı | ✅ Mevcut |
| Service Registry | Dinamik keşif | 📋 Gelecek |

---

## 9. Cross References

| Dosya | Amaç |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | Sağlık kontrolü |
| [[ecosystem/error-recovery]] | Hata kurtarma |
| [[architecture/03-contracts/api-architecture-master]] | API mimarisi |
| [[architecture/10-network]] | Ağ protokolleri |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Protocol Count** | 5 |
| **Event Categories** | 6 |
| **HTTP Status Codes** | 11 |
| **ADR Coverage** | 032, 084, 086 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
