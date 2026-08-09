---
type: architecture
category: network-grpc-ipc
title: "CoreMusic — gRPC & IPC Protocol"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — gRPC & IPC

**See also:** [[architecture/10-network/index]] · [[architecture/l5-services]] · [[architecture/03-contracts/service-ipc]]

---

## 1. Amaç

gRPC ve IPC (Inter-Process Communication), CoreMusic platformunun servisler arası yüksek performanslı iletişim protokolleridir. gRPC uzak servisler için, IPC ise aynı makinedeki process'ler için kullanılır.

---

## 2. gRPC

### Kullanım Alanları
- Servisler arası iletişim (microservices)
- Yüksek throughput, düşük gecikme
- Streaming (audio metadata, firmware chunks)
- Strongly-typed contracts (Protocol Buffers)

### gRPC Stack

```
Client (Service A)
    ↓
gRPC Channel (HTTP/2 + Protobuf)
    ↓
TLS 1.3 + mTLS
    ↓
gRPC Server (Service B)
    ↓
Business Logic
```

### gRPC Service Definitions

```protobuf
service DeviceService {
    rpc RegisterDevice (RegisterDeviceRequest) returns (RegisterDeviceResponse);
    rpc GetDevice (GetDeviceRequest) returns (Device);
    rpc UpdateFirmware (stream FirmwareChunk) returns (FirmwareResult);
    rpc StreamTelemetry (TelemetryRequest) returns (stream TelemetryData);
}
```

### gRPC vs REST Karşılaştırması

| Özellik | gRPC | REST |
|---------|------|------|
| Protocol | HTTP/2 | HTTP/1.1/2 |
| Serialization | Protocol Buffers | JSON |
| Speed | 2-10x hızlı | Yavaş |
| Streaming | Bidirectional | Request-Response |
| Contract | .proto files | OpenAPI/Swagger |
| Browser | Proxy ile | Doğrudan |
| Kullanım | Microservices | Public API |

### gRPC Konfigürasyonu

| Parametre | Değer |
|-----------|-------|
| Protocol | HTTP/2 |
| TLS | 1.3 + mTLS |
| Max Message | 4MB (default) |
| Keepalive | 30s client, 60s server |
| Timeout | 30s default |
| Retry | 3 max, exponential backoff |

---

## 3. IPC (Inter-Process Communication)

### Kullanım Alanları
- Aynı makinedeki servisler arası
- Audio Engine ↔ PHP Backend
- DSP Engine ↔ Driver Manager
- Yerel process'ler arası veri aktarımı

### IPC Türleri

| Tür | Hız | Kullanım | Kısıt |
|-----|-----|----------|-------|
| Unix Socket | <1ms | Yerel process | Aynı makine |
| Named Pipe | <1ms | Windows pipe | Aynı makine |
| Shared Memory | <0.1ms | Yüksek hız | Bellek paylaşımı |
| Message Queue | 1-5ms | Async messaging | Queue boyutu |
| Signal | — | Basit bildirim | Sınırlı veri |

### IPC Konfigürasyonu (CoreMusic)

```
Audio Engine (C++/JUCE)
    ↓
Unix Socket (/tmp/coremusic-audio.sock)
    ↓
PHP Backend (Control Service)
    ↓
Database
```

---

## 4. Shared Memory

### Kullanım
- DSP parametreleri (gerçek zamanlı)
- Audio buffer durumu
- Cihaz durumu (high-frequency updates)

### Shared Memory Layout

```cpp
struct SharedAudioState {
    alignas(64) std::atomic<uint32_t> sampleRate;
    alignas(64) std::atomic<uint32_t> channels;
    alignas(64) std::atomic<float> volume;
    alignas(64) std::atomic<bool> isPlaying;
    alignas(64) std::atomic<uint64_t> position;
};
```

### Güvenlik

| Önlem | Açıklama |
|-------|----------|
| OS Permissions | Sadece yetkili process'ler |
| Size Limit | Max 1MB |
| Lock-free | Atomic operations |
| No Secrets | Hassas veri yazma |

---

## 5. IPC Contract Versioning

| Kural | Değer | ADR |
|-------|-------|-----|
| Version Format | v{major}.{minor} | [[ADR-032]] |
| Breaking Change | Major version artır | [[ADR-032]] |
| Backward Compat | Minor version'da koru | [[ADR-032]] |
| Deprecation | 2 version dual-support | [[ADR-032]] |

---

## 6. Message Format

```json
{
    "version": "1.0",
    "type": "command",
    "source": "web-frontend",
    "target": "audio-engine",
    "action": "play",
    "payload": {
        "track_id": "abc123",
        "format": "flac",
        "sample_rate": 48000
    },
    "timestamp": "2026-08-09T12:00:00Z",
    "request_id": "req_xyz789"
}
```

---

## 7. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-032-ipc-contract-versioning]] | IPC sözleşme versiyonlama |
| [[ADR-017-dsp-hardware-mode]] | DSP-engine iletişimi |
| [[ADR-039-7-service-platform-architecture]] | 7-servis mimarisi |

---

## 8. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| gRPC | [[architecture/l5-services]] | Servis katmanı |
| IPC | [[electronic/dsp/index]] | DSP engine |
| IPC | [[electronic/drivers/index]] | Driver manager |
| Shared Memory | [[electronic/firmware/index]] | Firmware state |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
