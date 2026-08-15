---
type: ecosystem
category: state-machines
title: "State Machines — CoreMusic Durum Makineleri"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/state-machines.md"
  adr:
    - "decisions/accepted/ADR-011-session-management"
    - "decisions/accepted/ADR-017-dsp-hardware-mode"
    - "decisions/accepted/ADR-086-event-driven-architecture"
---

# State Machines — CoreMusic Durum Makineleri

**İlgili ADR:** [[decisions/accepted/ADR-011-session-management]] · [[decisions/accepted/ADR-017-dsp-hardware-mode]] · [[decisions/accepted/ADR-086-event-driven-architecture]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ecosystem/7-service-integration]] · [[architecture/00-overview/architecture-master]]

---

## 1. Amaç

CoreMusic platformundaki tüm durum makinelerini (session, playback, service, device) tanımlar ve geçiş kurallarını belgeler.

---

## 2. Session State Machine (ADR-011)

```
┌─────────────────────────────────────────────────────────────┐
│                    SESSION LIFECYCLE                        │
│                                                             │
│  ┌──────────┐    Login     ┌──────────┐                    │
│  │  Guest   │────────────→│ Authenticated│                 │
│  │  (0)     │←────────────│ (100-1999)│                    │
│  └──────────┘   Logout    └─────┬────┘                    │
│                                  │                          │
│                    ┌─────────────┼─────────────┐           │
│                    │             │             │           │
│                    ▼             ▼             ▼           │
│              ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│              │ Active   │ │ Idle     │ │ Expired  │      │
│              │ (200)    │ │ (301)    │ │ (401)    │      │
│              └────┬─────┘ └────┬─────┘ └────┬─────┘      │
│                   │            │             │            │
│                   │            └─────────────┘            │
│                   │            3600s timeout               │
│                   ▼                                       │
│              ┌──────────┐                                 │
│              │ Destroyed│                                 │
│              │ (500)    │                                 │
│              └──────────┘                                 │
└─────────────────────────────────────────────────────────────┘
```

### 2.1 Durum Tanımları

| Durum | Kod | Tanım | Geçiş |
|-------|-----|-------|-------|
| **Guest** | 0 | Kimlik doğrulanmamış | Login → Authenticated |
| **Authenticated** | 100-1999 | Rol bazlı kimlik | Session start → Active |
| **Active** | 200 | Aktif oturum | Request gelir → Active |
| **Idle** | 301 | 300s hareketsiz | 3600s → Expired |
| **Expired** | 401 | Oturum süresi doldu | Yeniden login |
| **Destroyed** | 500 | Oturum sonlandırıldı | — |

### 2.2 Roller (RBAC)

| Rol | ID Aralığı | Yetki |
|-----|------------|-------|
| guest | 0 | Sadece genel |
| regular | 100-199 | Temel erişim |
| car | 500-599 | Araç içi mod |
| premium | 700-799 | Yüksek kalite, offline |
| studio | 800-899 | Stüdyo modu, 8.1 surround |
| admin | 1000-1999 | Tam sistem yönetimi |
| system | 1900-1999 | Sistem servisleri |

---

## 3. Playback State Machine

```
┌─────────────────────────────────────────────────────────────┐
│                    PLAYBACK LIFECYCLE                       │
│                                                             │
│  ┌──────────┐    Play      ┌──────────┐                    │
│  │  Idle    │────────────→│ Playing  │                    │
│  │  (0)     │←────────────│ (1)      │                    │
│  └──────────┘    Stop     └─────┬────┘                    │
│                                  │                          │
│                    ┌─────────────┼─────────────┐           │
│                    │             │             │           │
│                    ▼             ▼             ▼           │
│              ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│              │ Paused   │ │ Buffering│ │ Error    │      │
│              │ (2)      │ │ (3)      │ │ (4)      │      │
│              └────┬─────┘ └────┬─────┘ └────┬─────┘      │
│                   │            │             │            │
│                   └────────────┼─────────────┘            │
│                                ▼                          │
│                          ┌──────────┐                     │
│                          │ Ended    │                     │
│                          │ (5)      │                     │
│                          └──────────┘                     │
└─────────────────────────────────────────────────────────────┘
```

### 3.1 Durum Tanımları

| Durum | Kod | Tanım | Geçişler |
|-------|-----|-------|----------|
| **Idle** | 0 | Oynatma yok | Play → Playing |
| **Playing** | 1 | Ses çalıyor | Pause → Paused, Stop → Idle, End → Ended |
| **Paused** | 2 | Duraklatıldı | Play → Playing, Stop → Idle |
| **Buffering** | 3 | Veri bekleniyor | Buffer dolu → Playing, Timeout → Error |
| **Error** | 4 | Hata oluştu | Retry → Buffering, Fallback → Idle |
| **Ended** | 5 | Parça bitti | Next → Playing, Stop → Idle |

### 3.2 Geçiş Tetikleyicileri

| Tetikleyici | Kaynak | Hedef |
|-------------|--------|-------|
| `playback.play` | Kullanıcı/UI | Idle → Playing |
| `playback.pause` | Kullanıcı/UI | Playing → Paused |
| `playback.stop` | Kullanıcı/UI | Herhangi → Idle |
| `playback.next` | Kullanıcı/Sistem | Ended → Playing |
| `buffer.underrun` | Sistem | Playing → Buffering |
| `buffer.ready` | Sistem | Buffering → Playing |
| `device.loss` | Sistem | Playing → Error |

---

## 4. Service State Machine

```
┌─────────────────────────────────────────────────────────────┐
│                    SERVICE LIFECYCLE                        │
│                                                             │
│  ┌──────────┐   Boot OK   ┌──────────┐                    │
│  │ Starting │────────────→│ Healthy  │                    │
│  │ (10)     │             │ (200)    │                    │
│  └──────────┘             └─────┬────┘                    │
│                                  │                          │
│                    ┌─────────────┼─────────────┐           │
│                    │             │             │           │
│                    ▼             ▼             ▼           │
│              ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│              │ Degraded │ │ Unhealthy│ │ Stopped  │      │
│              │ (301)    │ │ (503)    │ │ (0)      │      │
│              └────┬─────┘ └────┬─────┘ └──────────┘      │
│                   │            │                          │
│                   └────────────┘                          │
│                   Recovery → Healthy                       │
└─────────────────────────────────────────────────────────────┘
```

### 4.1 Durum Tanımları

| Durum | Kod | Tanım | Geçiş |
|-------|-----|-------|-------|
| **Starting** | 10 | Başlatılıyor | Boot OK → Healthy |
| **Healthy** | 200 | Tam çalışıyor | Hata → Degraded/Unhealthy |
| **Degraded** | 301 | Kısmi çalışıyor | Recovery → Healthy |
| **Unhealthy** | 503 | Çalışmıyor | Retry → Starting |
| **Stopped** | 0 | Durduruldu | Start → Starting |

---

## 5. Device State Machine

```
┌─────────────────────────────────────────────────────────────┐
│                    DEVICE LIFECYCLE                         │
│                                                             │
│  ┌──────────┐   Scan      ┌──────────┐                    │
│  │ Unknown  │────────────→│ Discovered│                   │
│  │ (0)      │             │ (1)      │                    │
│  └──────────┘             └─────┬────┘                    │
│                                  │                          │
│                            Pair/Connect                     │
│                                  │                          │
│                                  ▼                          │
│                            ┌──────────┐                    │
│                            │ Connected│                    │
│                            │ (2)      │                    │
│                            └─────┬────┘                    │
│                                  │                          │
│                    ┌─────────────┼─────────────┐           │
│                    │             │             │           │
│                    ▼             ▼             ▼           │
│              ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│              │ Paired   │ │ Syncing  │ │ Error    │      │
│              │ (3)      │ │ (4)      │ │ (5)      │      │
│              └────┬─────┘ └────┬─────┘ └────┬─────┘      │
│                   │            │             │            │
│                   └────────────┼─────────────┘            │
│                                │                          │
│                           Disconnect                       │
│                                │                          │
│                                ▼                          │
│                          ┌──────────┐                     │
│                          │ Disconnected│                  │
│                          │ (6)        │                   │
│                          └──────────┘                     │
└─────────────────────────────────────────────────────────────┘
```

### 5.1 Durum Tanımları

| Durum | Kod | Tanım | Geçiş |
|-------|-----|-------|-------|
| **Unknown** | 0 | Bilinmiyor | Scan → Discovered |
| **Discovered** | 1 | Bulundu | Pair → Connected |
| **Connected** | 2 | Bağlı | Pair → Paired, Sync → Syncing |
| **Paired** | 3 | Eşleştirildi | Sync → Syncing, Disconnect → Disconnected |
| **Syncing** | 4 | Senkronizasyon | Complete → Paired, Error → Error |
| **Error** | 5 | Hata | Retry → Connected, Disconnect → Disconnected |
| **Disconnected** | 6 | Bağlantı kesildi | Reconnect → Connected |

---

## 6. Download Queue State Machine

```
┌─────────────────────────────────────────────────────────────┐
│                    DOWNLOAD LIFECYCLE                       │
│                                                             │
│  ┌──────────┐   Add       ┌──────────┐                    │
│  │ Empty    │────────────→│ Queued   │                    │
│  │ (0)      │             │ (1)      │                    │
│  └──────────┘             └─────┬────┘                    │
│                                  │                          │
│                            Start download                    │
│                                  │                          │
│                                  ▼                          │
│                            ┌──────────┐                    │
│                            │ Downloading│                  │
│                            │ (2)        │                  │
│                            └─────┬────┘                    │
│                                  │                          │
│                    ┌─────────────┼─────────────┐           │
│                    │             │             │           │
│                    ▼             ▼             ▼           │
│              ┌──────────┐ ┌──────────┐ ┌──────────┐      │
│              │ Completed│ │ Failed   │ │ Paused   │      │
│              │ (3)      │ │ (4)      │ │ (5)      │      │
│              └──────────┘ └────┬─────┘ └────┬─────┘      │
│                                │             │            │
│                                └─────────────┘            │
│                                Retry → Queued              │
└─────────────────────────────────────────────────────────────┘
```

---

## 7. Event-Driven State Updates

Tüm state değişiklikleri event bus üzerinden yayınlanır (ADR-086):

| State Change | Event | Yayınlayan | Tüketen |
|-------------|-------|-----------|---------|
| Guest → Authenticated | `UserAuthenticated` | Control | Media, AI |
| Idle → Playing | `PlaybackStarted` | Audio | AI, Device |
| Playing → Paused | `PlaybackPaused` | Audio | UI |
| Playing → Ended | `PlaybackEnded` | Audio | AI, Download |
| Starting → Healthy | `ServiceStarted` | Sistem | Monitoring |
| Healthy → Unhealthy | `ServiceFailed` | Sistem | All services |
| Unknown → Connected | `DeviceConnected` | Device | Audio, Network |
| Queued → Downloading | `DownloadStarted` | Download | Media |
| Downloading → Completed | `TrackDownloaded` | Download | Media, AI |

---

## 8. Cross References

| Dosya | Amaç |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-communication]] | Event bus detayları |
| [[ecosystem/panel-integration]] | Panel durum yönetimi |
| [[architecture/l1-security]] | Session yönetimi |
| [[architecture/06-audio]] | Playback detayları |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **State Machines** | 5 (Session, Playback, Service, Device, Download) |
| **Session States** | 6 |
| **Playback States** | 6 |
| **Service States** | 5 |
| **Device States** | 7 |
| **Download States** | 6 |
| **Event Types** | 9 |
| **ADR Coverage** | 011, 017, 086 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
