---
title: "k0 Envanter — Platformlar (K0.01.zzz) part01"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/k0-isletim-sistemi/01-platformlar/index.md — bu dosya K0.01.zzz kayıtlarının evidir (index §Alt Dosyalar: inventory/k0-platformlar-part01.md)"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: false
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/01-platformlar/index.md"]
---

# k0 Envanter — Platformlar (K0.01.zzz) part01

**Kapsam:** `01-platformlar` bölümünün bileşen kayıtları — 4 platform çekirdeği + ReactOS/Docker + OS mekanizmaları (Windows/Linux/macOS/RPi5). **Sınırlar:** platform-bağımsız mekanizmalar `02-cekirdek-mekanizmalar` (K0.02.zzz → `part02`) · güvenlik politikaları `03-guvenlik-izolasyon` (K0.03.zzz) · cross-platform API `04-tasinbirlik` (K0.04.zzz).

**Format:** 12 alan — tanım: [[architecture/context]] §4 (bu dosyada tekrar edilmez, R6).
**Durum legend:** `PLANNED` = repo kodu YOK, hedef/tasarım kaydı · `IMPLEMENTED` = repo kod kanıtı ile · kanıtsız iddia yazılmaz (R9 · R14 3'lü kaynak).
**Ortak kanıt tabanı (2026-10-07):** anayasa §5 K0 satırı · §13 Platform Tiers · `01-platformlar/index.md` (01-index) · ADR-019 (per-OS Neva Player) · ADR-032 (IPC kontrat) · k0 CLAUDE.md §1-2 (repo grep negatif kanıt: C++/Docker/pcntl = 0).

---

### K0.01.001 — Windows Core (Win12)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.001` |
| `ad` | Windows Core — Win12 hedef platform |
| `tip` | platform |
| `kod-yolu` | YOK — repo'da C++/WinRT kodu yok (01-index §Alt Dosyalar: PLANNED) |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | — (katman tabanı) |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | anayasa §5 K0 satırı · Tier 1 ana geliştirme (§13) · detay doc `windows-core.md` Faz 1 kapsamı (PLANNED) · ADR-019 backend sırası ASIO→WASAPI→Null |

### K0.01.002 — Linux Core (Lin10)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.002` |
| `ad` | Linux Core — Lin10 hedef platform |
| `tip` | platform |
| `kod-yolu` | YOK — repo'da native Linux kodu yok (01-index: PLANNED) |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | — (katman tabanı) |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | anayasa §5 K0 · Tier 2 destekli (§13) · `linux-core.md` PLANNED · hedef sürücüler ALSA/PipeWire (k2 sınırı) |

### K0.01.003 — macOS Core (Mac8)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.003` |
| `ad` | macOS Core — Mac8 hedef platform |
| `tip` | platform |
| `kod-yolu` | YOK — repo'da macOS kodu yok (01-index: PLANNED) |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | — (katman tabanı) |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | anayasa §5 K0 · Tier 3 destekli (§13: Monterey–Sonoma) · `macos-core.md` PLANNED · hedef sürücü CoreAudio (k2) |

### K0.01.004 — RPi5 Core (ARM64)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.004` |
| `ad` | RPi5 Core — Raspberry Pi 5 (ARM64) |
| `tip` | platform |
| `kod-yolu` | YOK — repo'da RPi kodu yok (01-index: PLANNED) |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | — (katman tabanı) |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | anayasa §5 K0 · Tier 4 destekli (§13) · `rpi5-core.md` PLANNED · deployment: Car Audio / NAS (§14) · hedef I2S DAC (k2) |

### K0.01.005 — ReactOS

| Alan | Değer |
|------|-------|
| `id` | `K0.01.005` |
| `ad` | ReactOS (Windows uyumluluk OS'u) |
| `tip` | platform |
| `kod-yolu` | YOK — repo'da ReactOS-specific kod yok |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | — (bağımsız hedef platform) |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | anayasa §5 K0 satırı · §13 Tier 5 **⚠️ Experimental — sınırlı ses sürücüsü** · destek seviyesi hedeftir, üretim kanıtı yok |

### K0.01.006 — Docker (container platformu)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.006` |
| `ad` | Docker — container platformu |
| `tip` | platform |
| `kod-yolu` | YOK — Dockerfile/container kanıtı grep = 0 (k0 CLAUDE.md §2: Docker 0) |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` (Linux çekirdek üzerinde çalışır) |
| `sahip-agent` | devops-engineer |
| `risk` | medium |
| `guvenlik` | yes — container izolasyonu; politika sınırı 03-guvenlik-izolasyon + K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | anayasa §5 K0 + §12 Containerization (Docker 24+ hedef) · deploy orkestrasyonu K13 kapsamı (refers-to k13-cicd) · k0 AGENTS: devops container/CI kanıtı sahibi |

### K0.01.007 — Windows Process Management

| Alan | Değer |
|------|-------|
| `id` | `K0.01.007` |
| `ad` | Process Management (Windows) |
| `tip` | mekanizma |
| `kod-yolu` | YOK — Win32 process API kodu repo'da yok |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · API iddiaları (ör. CreateProcessW) yalnız web kanıtıyla yazılır (k0 CLAUDE §1) |

### K0.01.008 — Windows Memory Management

| Alan | Değer |
|------|-------|
| `id` | `K0.01.008` |
| `ad` | Memory Management (Windows) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · sanal bellek / paylaşımlı bellek API'leri (02 mekanizmalarıyla sınır: platform-spezifik detay YALNIZ 01) |

### K0.01.009 — Windows Threading

| Alan | Değer |
|------|-------|
| `id` | `K0.01.009` |
| `ad` | Threading (Windows) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · K3 real-time kısıtlarıyla (zero-allocation/lock-free, anayasa §19) ilişkili ama uygulama k3'te |

### K0.01.010 — Windows Registry

| Alan | Değer |
|------|-------|
| `id` | `K0.01.010` |
| `ad` | Registry (Windows) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · yapılandırma okuma/yazma; credential ASLA registry'de (U1) |

### K0.01.011 — Windows Service Control

| Alan | Değer |
|------|-------|
| `id` | `K0.01.011` |
| `ad` | Service Control (Windows) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · servis yaşam döngüsü (Audio Service 9741/9742 süreç hedefi) |

### K0.01.012 — Windows EventLog

| Alan | Değer |
|------|-------|
| `id` | `K0.01.012` |
| `ad` | EventLog (Windows) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · OS log kaynağı — uygulama logu K12/`coremusic_logs` (refers-to k12-izleme) |

### K0.01.013 — Windows WMI

| Alan | Değer |
|------|-------|
| `id` | `K0.01.013` |
| `ad` | WMI (Windows Management Instrumentation) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | yes — uzak yönetim yüzeyi; erişim minimum yetki ile |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · donanım/envanter sorguları (termal/ sensör okumaları için alternatif kanal) |

### K0.01.014 — COM / WinRT

| Alan | Değer |
|------|-------|
| `id` | `K0.01.014` |
| `ad` | COM / WinRT (Windows entegrasyon katmanı) |
| `tip` | mekanizma |
| `kod-yolu` | YOK — COM/WinRT kodu repo'da yok |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · ADR-019 adapter sınırı: `IAudioBackend` arayüzü bu ADR'de tanımlı (repo grep: 0 → PLANNED) · WASAPI COM binding k2 sınırında |

### K0.01.015 — Windows IPC (Named Pipe)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.015` |
| `ad` | IPC — Named Pipe (Windows süreç-arası iletişim) |
| `tip` | mekanizma |
| `kod-yolu` | YOK — IPC mesajlaşma kodu yok (.proto/.cpp = 0, ADR-032 kanıt taraması) |
| `durum` | PLANNED |
| `A-alanı` | `A0` |
| `bagimlilik` | `K0.01.001`, `K0.01.014` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — yerel süreç kimlik doğrulaması (pipe ACL); auth bütünlüğü K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | spec kanıtı: `\\.\pipe\CoreMusicIPC` (windows-core.md:237-244, `_backup` referans R1.3) · ADR-032: şema/sürüm/geriye-uyumluluk sözleşmesi SSOT'u · sürümleme HTTP yüzeyinde IMPLEMENTED (shared/src/Api/Versioning/VersionResolver.php:34-48), süreç-arası mesajlaşma PLANNED |

### K0.01.016 — Windows Job Objects

| Alan | Değer |
|------|-------|
| `id` | `K0.01.016` |
| `ad` | Job Objects (süreç grubu izolasyonu) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001`, `K0.01.007` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — kaynak/kapsam kısıtı; politika 03-guvenlik-izolasyon |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · 03-index kapsam listesinde de yer alır (ortak sınır — politika K6'da) |

### K0.01.017 — Windows UAC

| Alan | Değer |
|------|-------|
| `id` | `K0.01.017` |
| `ad` | UAC (yetki ayrıştırma) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.001` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — minimum yetki ilkesi; politika 03 + K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Windows satırı · 03-index: "Windows: Job Objects, UAC ayrıştırma, AppContainer" |

### K0.01.018 — Linux cgroups v2

| Alan | Değer |
|------|-------|
| `id` | `K0.01.018` |
| `ad` | cgroups v2 (kaynak kontrolü) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | yes — kaynak kuşatma (limit) ; politika 03 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux satırı · container (Docker K0.01.006) alt mekanizması |

### K0.01.019 — Linux namespaces

| Alan | Değer |
|------|-------|
| `id` | `K0.01.019` |
| `ad` | namespaces (PID/mount/net izolasyonu) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — sandbox temeli; politika 03-guvenlik-izolasyon |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux satırı · 03-index kapsamı: "namespace izolasyonu" (mekanizma burada, politika orada) |

### K0.01.020 — Linux systemd

| Alan | Değer |
|------|-------|
| `id` | `K0.01.020` |
| `ad` | systemd (servis/süreç yöneticisi) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux satırı · servis yaşam döngüsü karşılığı (K0.01.011'in Linux dengi) |

### K0.01.021 — Linux epoll

| Alan | Değer |
|------|-------|
| `id` | `K0.01.021` |
| `ad` | epoll (I/O event demultiplexing) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux satırı · reactor temeli — libuv/kqueue/IOCP karşılaştırması web-doğrulanmış (02-index §Teknoloji notu) · cross-platform API kararı 04-tasinbirlik |

### K0.01.022 — Linux inotify

| Alan | Değer |
|------|-------|
| `id` | `K0.01.022` |
| `ad` | inotify (dosya sistemi izleme) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux satırı · kütüphane değişikliği izleme (media library watcher hedefi) |

### K0.01.023 — Linux seccomp-bpf

| Alan | Değer |
|------|-------|
| `id` | `K0.01.023` |
| `ad` | seccomp-bpf (syscall filtresi) |
| `tip` | mekanizma |
| `kod-yolu` | YOK — repo'da sandbox/seccomp kodu kanıtı YOK (03-index §Durum) |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002`, `K0.01.019` |
| `sahip-agent` | win-sw |
| `risk` | high |
| `guvenlik` | yes — syscall yüzeyi kısıtı; politika onayı security-engineer + K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux + 03-index kapsamı · filtre kuralları 03'te tanımlanır (mekanizma burada) |

### K0.01.024 — Linux capabilities (capset)

| Alan | Değer |
|------|-------|
| `id` | `K0.01.024` |
| `ad` | capabilities — capset (minimum yetki) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.002` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — yetki düşürme (dropping); politika 03 + K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index Linux + 03-index: "Capability dropping (capset, minimum yetki)" |

### K0.01.025 — macOS GCD

| Alan | Değer |
|------|-------|
| `id` | `K0.01.025` |
| `ad` | GCD (Grand Central Dispatch) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.003` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index macOS satırı · task/queue modeli — threading-model araştırması (02-index, web ✅ GCD/kqueue/io_uring) |

### K0.01.026 — macOS XPC

| Alan | Değer |
|------|-------|
| `id` | `K0.01.026` |
| `ad` | XPC (süreç-arası iletişim) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.003` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | yes — servis bağlantı kimliği (audit token); politika 03 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index macOS satırı · IPC ailesinin macOS dengi (K0.01.015 ile paralel) |

### K0.01.027 — macOS LaunchAgent

| Alan | Değer |
|------|-------|
| `id` | `K0.01.027` |
| `ad` | LaunchAgent (oturum servisi yaşam döngüsü) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.003` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index macOS satırı · login-item / servis başlatma karşılığı |

### K0.01.028 — macOS App Sandbox

| Alan | Değer |
|------|-------|
| `id` | `K0.01.028` |
| `ad` | App Sandbox |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.003` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — dosya/ağ erişim kuşatması; politika 03 + K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index macOS + 03-index kapsamı · entitlements ile yapılandırılır |

### K0.01.029 — macOS Hardened Runtime

| Alan | Değer |
|------|-------|
| `id` | `K0.01.029` |
| `ad` | Hardened Runtime (+ entitlements) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.003`, `K0.01.028` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | yes — code-signing + runtime saldırı yüzeyi kısıtı; politika 03 + K6 |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index macOS + 03-index kapsam listesi |

### K0.01.030 — RPi5 GPIO

| Alan | Değer |
|------|-------|
| `id` | `K0.01.030` |
| `ad` | GPIO (genel amaçlı I/O) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.004` |
| `sahip-agent` | win-sw |
| `risk` | low |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index RPi5 satırı · kontrol/LED/düğme I/O (car/home panelleri donanım yüzeyi) |

### K0.01.031 — RPi5 DMA

| Alan | Değer |
|------|-------|
| `id` | `K0.01.031` |
| `ad` | DMA (direct memory access) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.004` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index RPi5 satırı · audio buffer taşıma — K3 real-time (§19) ile sınır |

### K0.01.032 — RPi5 PWM audio

| Alan | Değer |
|------|-------|
| `id` | `K0.01.032` |
| `ad` | PWM audio çıkışı |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.004` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index RPi5 satırı · PWM ses çıkışı hedefi — kaliteli çıkış I2S DAC (K0.01.033) tercih edilir (§13 Tier 4: I2S) |

### K0.01.033 — RPi5 I2S

| Alan | Değer |
|------|-------|
| `id` | `K0.01.033` |
| `ad` | I2S (ses veri yolu — RPi5 pin seviyesi) |
| `tip` | mekanizma |
| `kod-yolu` | YOK |
| `durum` | PLANNED |
| `A-alanı` | A0 |
| `bagimlilik` | `K0.01.004` |
| `sahip-agent` | win-sw |
| `risk` | medium |
| `guvenlik` | no |
| `kanit-tarihi` | 2026-10-07 |
| `not` | 01-index RPi5 satırı · anayasa §5 K2 I2S bağlantısı — sürücü yığını **k2-surucu**'da, pin/DMA seviyesi burada (D-sınır: k0 pin, k2 sürücü, k3 DSP) · DAC hedefi: PCM3168A (ADR-038) |

---

**Sayım:** 33 kayıt (K0.01.001–K0.01.033) — platform 6 · Windows mekanizma 11 · Linux 7 · macOS 5 · RPi5 4 (2026-10-07 disk ölçümü).
**Durum dağılımı:** PLANNED 33 · IMPLEMENTED 0 (repo kod kanıtı yok — k0 CLAUDE §2 grep negatif).
**Sonraki part:** `k0-platformlar-part02.md` (K0.02.zzz — 02-cekirdek-mekanizmalar) — bu dosyada K0.02 kaydı YOKTUR (tür/seri ayrımı, R2.2).
**İlişki:** bu dosya 01-index'in envanter karşılığıdır (01-platformlar/index.md §Alt Dosyalar) · format SSOT: [[architecture/context]] §4 · kurallar: [[architecture/rules]].