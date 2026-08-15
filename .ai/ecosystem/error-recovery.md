---
type: ecosystem
category: error-recovery
title: "Error Recovery — CoreMusic Hata Kurtarma Stratejileri"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/error-recovery.md"
  adr:
    - "decisions/accepted/ADR-017-dsp-hardware-mode"
    - "decisions/accepted/ADR-039-7-service-platform-architecture"
---

# Error Recovery — CoreMusic Hata Kurtarma Stratejileri

**İlgili ADR:** [[decisions/accepted/ADR-017-dsp-hardware-mode]] · [[decisions/accepted/ADR-039-7-service-platform-architecture]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ecosystem/7-service-integration]] · [[ecosystem/service-health-check]]

---

## 1. Amaç

7 servisteki hata türlerini, kurtarma stratejilerini, fallback zincirlerini ve rollback prosedürlerini tanımlar.

---

## 2. Hata Kategorileri

| Kategori | Seviye | Örnek | Yanıt Süresi |
|----------|--------|-------|-------------|
| **CRITICAL** | Sistem durması | Auth DB çökmesi, tüm servisler 503 | Anlık |
| **HIGH** | Kritik işlev kaybı | Audio Service çökmesi, indirme durması | 15s |
| **MEDIUM** | Kısmi işlev kaybı | AI önerileri yavaş, cache miss | 30s |
| **LOW** | Kozmetik hata | UI rendering hatası, log hatası | 60s |

---

## 3. Servis Bazlı Hata Senaryoları

### 3.1 Control Service (Port 81)

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| MySQL bağlantı hatası | 500 tüm API'lerde | DB reconnect, pool reset | Cache'den session oku |
| Session store hatalı | 401 tüm panellerde | Session reset | Yeni session oluştur |
| RBAC hatası | 403 yetkili kullanıcı | Permission cache reset | Default permission |
| Rate limit dolu | 429 | Rate limit reset | İsteği ertele |

### 3.2 Media Service (Port 5000)

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| FFmpeg crash | Encoding hatası | FFmpeg restart | Düşük kalite encoding |
| Dosya sistemi hatası | Metadata yüklenemiyor | Disk check | Cache'den metadata |
| WebSocket kopuk | Gerçek zamanlı güncelleme yok | WS reconnect | HTTP polling |
| DB write hatası | Metadata kaydedilemiyor | Retry (3x) | Geçici buffer |

### 3.3 Audio Service (Port 9741)

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| ASIO device kaybı | Ses kesildi | WASAPI fallback | Null Output (sessizlik) |
| Buffer underrun | Ses takılması/tıslama | Buffer artır (512→1024) | Fade-out → sessizlik |
| DSP chain hatası | Distorsiyon | DSP bypass | Doğrudan passthrough |
| Exclusive lock | ASIO başlatılamıyor | Diğer uygulamayı kapat | WASAPI Shared mode |

### 3.4 Download Service (Port 3001)

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| Rate limit (Deezer) | 429 indirme | ARL token rotasyonu | YouTube fallback |
| Proxy ban | İndirme başarısız | Proxy rotasyonu | Manuel indirme |
| Disk dolu | İndirme kaydedilemiyor | Eski dosyaları temizle | Queue'da bekle |
| Node.js crash | İndirme durdu | Process restart | Queue korunur |

### 3.5 AI Service

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| Model yüklenemedi | Öneri gelmiyor | Model reload | Default öneriler |
| DB hatası | Öneriler hesaplanamıyor | Retry | Kullanıcı tercihi |
| CPU aşırı yük | Öneri gecikmeli | Throttle | Basit öneri |

### 3.6 Device Service

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| BLE kopuk | Bluetooth bağlantısı yok | BLE reconnect | WiFi Direct |
| WiFi disconnect | Ağ bağlantısı yok | WiFi reconnect | USB tethering |
| USB çıkarıldı | Cihaz kayboldu | USB reconnect | Hata bildirimi |

### 3.7 Network Audio

| Hata | Belirti | Çözüm | Fallback |
|------|---------|-------|----------|
| WebRTC kopuk | Multi-room senkronizasyonu bozuldu | ICE restart | Local playback |
| P2P mesh bozuldu | Odalar arası iletişim yok | Mesh rebuild | Tek oda modu |
| Jitter太高 | Ses kesintisi | Buffer artır | Düşük kalite |

---

## 4. Fallback Zincirleri (Tüm Servisler)

```
┌─────────────────────────────────────────────────────────────┐
│ Birincil Servis Çöktü                                       │
│                                                             │
│  1. Retry (3x, exponential backoff)                        │
│     ↓ başarısız                                             │
│  2. Circuit Breaker OPEN (30s)                             │
│     ↓                                                       │
│  3. Fallback servisi kullan                                 │
│     ↓ başarısız                                             │
│  4. Cache'den serve et                                      │
│     ↓ başarısız                                             │
│  5. Default/empty response                                 │
│     ↓                                                       │
│  6. CRITICAL alert → Human intervention                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. Rollback Stratejileri

| Değişiklik Tipi | Rollback Yöntemi | Süre |
|-----------------|-------------------|------|
| Database migration | Reverse migration | 5 dk |
| Config değişikliği | Eski config'e dön | 1 dk |
| Servis deploy | previous version'a dön | 3 dk |
| Code deploy | `git revert` + re-deploy | 10 dk |
| Cache invalidation | Cache flush | <1 dk |

---

## 6. Data Recovery

| Durum | Kurtarma Yöntemi | Kaynak |
|-------|------------------|--------|
| DB veri kaybı | Backup restore | MySQL dump (günlük) |
| Dosya kaybı | Backup restore | NAS/Cloud backup |
| Session kaybı | Yeniden auth | Kullanıcı tekrar giriş |
| Cache bozulması | Cache flush + rebuild | DB'den yeniden yükle |
| Config kaybı | Git restore | `.env` dosyası |

---

## 7. Degraded Mode Operasyonu

| Servis Durumu | Sistem Davranışı |
|---------------|-----------------|
| 1 servis down | Kalan servisler çalışmaya devam |
| 2 servis down | Temel özellikler korunur |
| 3+ servis down | Read-only mod |
| Auth down | Tüm login'ler engellenir, mevcut session korunur |
| DB down | All write operations paused |

---

## 8. Cross References

| Dosya | Amaç |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | Sağlık kontrolü tetikleyicileri |
| [[ecosystem/service-communication]] | Retry/fallback protokolleri |
| [[architecture/06-audio]] | Audio hata detayları |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Error Categories** | 4 (CRITICAL, HIGH, MEDIUM, LOW) |
| **Service Scenarios** | 7 servis × 3-4 hata = ~25 senaryo |
| **Fallback Depth** | 6 seviye |
| **Rollback Types** | 5 |
| **Data Recovery Types** | 5 |
| **ADR Coverage** | 017, 039 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
