---
type: ecosystem
category: state-machines
title: "State Machines â€” CoreMusic Durum Makineleri"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/ecosystem/state-machines.md"
  adr:
    - "decisions/accepted/ADR-011-session-management"
    - "decisions/accepted/ADR-017-dsp-hardware-mode"
    - "decisions/accepted/ADR-086-event-driven-architecture"
---

# State Machines â€” CoreMusic Durum Makineleri

**Ä°lgili ADR:** [[decisions/accepted/ADR-011-session-management]] Â· [[decisions/accepted/ADR-017-dsp-hardware-mode]] Â· [[decisions/accepted/ADR-086-event-driven-architecture]]

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[ecosystem/7-service-integration]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

CoreMusic platformundaki tÃ¼m durum makinelerini (session, playback, service, device) tanÄ±mlar ve geÃ§iÅŸ kurallarÄ±nÄ± belgeler.

---

## 2. Session State Machine (ADR-011)

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                    SESSION LIFECYCLE                        â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    Login     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚  â”‚  Guest   â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â†’â”‚ Authenticatedâ”‚                 â”‚
â”‚  â”‚  (0)     â”‚â†â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚ (100-1999)â”‚                    â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   Logout    â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”           â”‚
â”‚                    â”‚             â”‚             â”‚           â”‚
â”‚                    â–¼             â–¼             â–¼           â”‚
â”‚              â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”      â”‚
â”‚              â”‚ Active   â”‚ â”‚ Idle     â”‚ â”‚ Expired  â”‚      â”‚
â”‚              â”‚ (200)    â”‚ â”‚ (301)    â”‚ â”‚ (401)    â”‚      â”‚
â”‚              â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜      â”‚
â”‚                   â”‚            â”‚             â”‚            â”‚
â”‚                   â”‚            â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜            â”‚
â”‚                   â”‚            3600s timeout               â”‚
â”‚                   â–¼                                       â”‚
â”‚              â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                                 â”‚
â”‚              â”‚ Destroyedâ”‚                                 â”‚
â”‚              â”‚ (500)    â”‚                                 â”‚
â”‚              â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                                 â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 2.1 Durum TanÄ±mlarÄ±

| Durum | Kod | TanÄ±m | GeÃ§iÅŸ |
|-------|-----|-------|-------|
| **Guest** | 0 | Kimlik doÄŸrulanmamÄ±ÅŸ | Login â†’ Authenticated |
| **Authenticated** | 100-1999 | Rol bazlÄ± kimlik | Session start â†’ Active |
| **Active** | 200 | Aktif oturum | Request gelir â†’ Active |
| **Idle** | 301 | 300s hareketsiz | 3600s â†’ Expired |
| **Expired** | 401 | Oturum sÃ¼resi doldu | Yeniden login |
| **Destroyed** | 500 | Oturum sonlandÄ±rÄ±ldÄ± | â€” |

### 2.2 Roller (RBAC)

| Rol | ID AralÄ±ÄŸÄ± | Yetki |
|-----|------------|-------|
| guest | 0 | Sadece genel |
| regular | 100-199 | Temel eriÅŸim |
| car | 500-599 | AraÃ§ iÃ§i mod |
| premium | 700-799 | YÃ¼ksek kalite, offline |
| studio | 800-899 | StÃ¼dyo modu, 8.1 surround |
| admin | 1000-1999 | Tam sistem yÃ¶netimi |
| system | 1900-1999 | Sistem servisleri |

---

## 3. Playback State Machine

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                    PLAYBACK LIFECYCLE                       â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    Play      â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚  â”‚  Idle    â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â†’â”‚ Playing  â”‚                    â”‚
â”‚  â”‚  (0)     â”‚â†â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”‚ (1)      â”‚                    â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    Stop     â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”           â”‚
â”‚                    â”‚             â”‚             â”‚           â”‚
â”‚                    â–¼             â–¼             â–¼           â”‚
â”‚              â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”      â”‚
â”‚              â”‚ Paused   â”‚ â”‚ Bufferingâ”‚ â”‚ Error    â”‚      â”‚
â”‚              â”‚ (2)      â”‚ â”‚ (3)      â”‚ â”‚ (4)      â”‚      â”‚
â”‚              â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜      â”‚
â”‚                   â”‚            â”‚             â”‚            â”‚
â”‚                   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜            â”‚
â”‚                                â–¼                          â”‚
â”‚                          â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                     â”‚
â”‚                          â”‚ Ended    â”‚                     â”‚
â”‚                          â”‚ (5)      â”‚                     â”‚
â”‚                          â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                     â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 3.1 Durum TanÄ±mlarÄ±

| Durum | Kod | TanÄ±m | GeÃ§iÅŸler |
|-------|-----|-------|----------|
| **Idle** | 0 | Oynatma yok | Play â†’ Playing |
| **Playing** | 1 | Ses Ã§alÄ±yor | Pause â†’ Paused, Stop â†’ Idle, End â†’ Ended |
| **Paused** | 2 | DuraklatÄ±ldÄ± | Play â†’ Playing, Stop â†’ Idle |
| **Buffering** | 3 | Veri bekleniyor | Buffer dolu â†’ Playing, Timeout â†’ Error |
| **Error** | 4 | Hata oluÅŸtu | Retry â†’ Buffering, Fallback â†’ Idle |
| **Ended** | 5 | ParÃ§a bitti | Next â†’ Playing, Stop â†’ Idle |

### 3.2 GeÃ§iÅŸ Tetikleyicileri

| Tetikleyici | Kaynak | Hedef |
|-------------|--------|-------|
| `playback.play` | KullanÄ±cÄ±/UI | Idle â†’ Playing |
| `playback.pause` | KullanÄ±cÄ±/UI | Playing â†’ Paused |
| `playback.stop` | KullanÄ±cÄ±/UI | Herhangi â†’ Idle |
| `playback.next` | KullanÄ±cÄ±/Sistem | Ended â†’ Playing |
| `buffer.underrun` | Sistem | Playing â†’ Buffering |
| `buffer.ready` | Sistem | Buffering â†’ Playing |
| `device.loss` | Sistem | Playing â†’ Error |

---

## 4. Service State Machine

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                    SERVICE LIFECYCLE                        â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   Boot OK   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚  â”‚ Starting â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â†’â”‚ Healthy  â”‚                    â”‚
â”‚  â”‚ (10)     â”‚             â”‚ (200)    â”‚                    â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜             â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”           â”‚
â”‚                    â”‚             â”‚             â”‚           â”‚
â”‚                    â–¼             â–¼             â–¼           â”‚
â”‚              â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”      â”‚
â”‚              â”‚ Degraded â”‚ â”‚ Unhealthyâ”‚ â”‚ Stopped  â”‚      â”‚
â”‚              â”‚ (301)    â”‚ â”‚ (503)    â”‚ â”‚ (0)      â”‚      â”‚
â”‚              â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜      â”‚
â”‚                   â”‚            â”‚                          â”‚
â”‚                   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                          â”‚
â”‚                   Recovery â†’ Healthy                       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 4.1 Durum TanÄ±mlarÄ±

| Durum | Kod | TanÄ±m | GeÃ§iÅŸ |
|-------|-----|-------|-------|
| **Starting** | 10 | BaÅŸlatÄ±lÄ±yor | Boot OK â†’ Healthy |
| **Healthy** | 200 | Tam Ã§alÄ±ÅŸÄ±yor | Hata â†’ Degraded/Unhealthy |
| **Degraded** | 301 | KÄ±smi Ã§alÄ±ÅŸÄ±yor | Recovery â†’ Healthy |
| **Unhealthy** | 503 | Ã‡alÄ±ÅŸmÄ±yor | Retry â†’ Starting |
| **Stopped** | 0 | Durduruldu | Start â†’ Starting |

---

## 5. Device State Machine

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                    DEVICE LIFECYCLE                         â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   Scan      â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚  â”‚ Unknown  â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â†’â”‚ Discoveredâ”‚                   â”‚
â”‚  â”‚ (0)      â”‚             â”‚ (1)      â”‚                    â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜             â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                            Pair/Connect                     â”‚
â”‚                                  â”‚                          â”‚
â”‚                                  â–¼                          â”‚
â”‚                            â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚                            â”‚ Connectedâ”‚                    â”‚
â”‚                            â”‚ (2)      â”‚                    â”‚
â”‚                            â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”           â”‚
â”‚                    â”‚             â”‚             â”‚           â”‚
â”‚                    â–¼             â–¼             â–¼           â”‚
â”‚              â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”      â”‚
â”‚              â”‚ Paired   â”‚ â”‚ Syncing  â”‚ â”‚ Error    â”‚      â”‚
â”‚              â”‚ (3)      â”‚ â”‚ (4)      â”‚ â”‚ (5)      â”‚      â”‚
â”‚              â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜      â”‚
â”‚                   â”‚            â”‚             â”‚            â”‚
â”‚                   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜            â”‚
â”‚                                â”‚                          â”‚
â”‚                           Disconnect                       â”‚
â”‚                                â”‚                          â”‚
â”‚                                â–¼                          â”‚
â”‚                          â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                     â”‚
â”‚                          â”‚ Disconnectedâ”‚                  â”‚
â”‚                          â”‚ (6)        â”‚                   â”‚
â”‚                          â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                     â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 5.1 Durum TanÄ±mlarÄ±

| Durum | Kod | TanÄ±m | GeÃ§iÅŸ |
|-------|-----|-------|-------|
| **Unknown** | 0 | Bilinmiyor | Scan â†’ Discovered |
| **Discovered** | 1 | Bulundu | Pair â†’ Connected |
| **Connected** | 2 | BaÄŸlÄ± | Pair â†’ Paired, Sync â†’ Syncing |
| **Paired** | 3 | EÅŸleÅŸtirildi | Sync â†’ Syncing, Disconnect â†’ Disconnected |
| **Syncing** | 4 | Senkronizasyon | Complete â†’ Paired, Error â†’ Error |
| **Error** | 5 | Hata | Retry â†’ Connected, Disconnect â†’ Disconnected |
| **Disconnected** | 6 | BaÄŸlantÄ± kesildi | Reconnect â†’ Connected |

---

## 6. Download Queue State Machine

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                    DOWNLOAD LIFECYCLE                       â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   Add       â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚  â”‚ Empty    â”‚â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â†’â”‚ Queued   â”‚                    â”‚
â”‚  â”‚ (0)      â”‚             â”‚ (1)      â”‚                    â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜             â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                            Start download                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                                  â–¼                          â”‚
â”‚                            â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                    â”‚
â”‚                            â”‚ Downloadingâ”‚                  â”‚
â”‚                            â”‚ (2)        â”‚                  â”‚
â”‚                            â””â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜                    â”‚
â”‚                                  â”‚                          â”‚
â”‚                    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”           â”‚
â”‚                    â”‚             â”‚             â”‚           â”‚
â”‚                    â–¼             â–¼             â–¼           â”‚
â”‚              â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”      â”‚
â”‚              â”‚ Completedâ”‚ â”‚ Failed   â”‚ â”‚ Paused   â”‚      â”‚
â”‚              â”‚ (3)      â”‚ â”‚ (4)      â”‚ â”‚ (5)      â”‚      â”‚
â”‚              â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜      â”‚
â”‚                                â”‚             â”‚            â”‚
â”‚                                â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜            â”‚
â”‚                                Retry â†’ Queued              â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 7. Event-Driven State Updates

TÃ¼m state deÄŸiÅŸiklikleri event bus Ã¼zerinden yayÄ±nlanÄ±r (ADR-086):

| State Change | Event | YayÄ±nlayan | TÃ¼keten |
|-------------|-------|-----------|---------|
| Guest â†’ Authenticated | `UserAuthenticated` | Control | Media, AI |
| Idle â†’ Playing | `PlaybackStarted` | Audio | AI, Device |
| Playing â†’ Paused | `PlaybackPaused` | Audio | UI |
| Playing â†’ Ended | `PlaybackEnded` | Audio | AI, Download |
| Starting â†’ Healthy | `ServiceStarted` | Sistem | Monitoring |
| Healthy â†’ Unhealthy | `ServiceFailed` | Sistem | All services |
| Unknown â†’ Connected | `DeviceConnected` | Device | Audio, Network |
| Queued â†’ Downloading | `DownloadStarted` | Download | Media |
| Downloading â†’ Completed | `TrackDownloaded` | Download | Media, AI |

---

## 8. Cross References

| Dosya | AmaÃ§ |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-communication]] | Event bus detaylarÄ± |
| [[ecosystem/panel-integration]] | Panel durum yÃ¶netimi |
| [[architecture/l1-security]] | Session yÃ¶netimi |
| [[architecture/06-audio]] | Playback detaylarÄ± |

---

## 9. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team Â· Human Mode Â· Truth Mode verified |
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

