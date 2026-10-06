---
title: "index — K0 İşletim Sistemi — İçindekiler"
type: architecture-layer
category: k0-isletim-sistemi
version: 1.0.0
status: active
updated: 2026-10-06
authority: "SSOT — .ai/architecture.1/k0-isletim-sistemi"
---

# index — K0 İşletim Sistemi — İçindekiler

**Katman:** K0 (en alt yazılım katmanı) · **Kapsam:** işletim sistemi servisleri, çekirdek mekanizmalar, izolasyon, taşınabilirlik
**Konum:** `.ai/architecture.1/k0-isletim-sistemi/` · **Kaynak:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (14 dosya)
**Sınırlar:** donanım (DAC/PCB), DSP, web ve veritabanı katmanları bu dizinde **değildir**.

---

## 1. Genel Bakış

K0 katmanı, CoreMusic'in tüm katmanlarının temelini oluşturan işletim sistemi hizmetlerini tanımlar: bellek yönetimi, threading, IPC, sistem çağrıları, süreç izolasyonu, konteyner runtime ve platform başına çekirdek erişimi.

## 2. Dosya Grupları

### 2.1 Kök

| Dosya | İçerik |
|-------|--------|
| [[README]] | Genel bakış, bileşen haritası (K0-01…K0-20), alt katman şeması, kanıt kataloğu, 2026 durum notları |
| [[index]] | Bu dosya — içindekiler ve bağlantılar |

### 2.2 `01-platformlar/` — Platform Çekirdekleri

| Dosya | İçerik |
|-------|--------|
| [[01-platformlar/windows-core]] | Process/memory/thread, Registry, Service, Event Log, WMI, COM, Named Pipes, Job Objects, UAC |
| [[01-platformlar/windows-api]] | ASIO 2.3 SDK, WASAPI modları/akış, Win32 threading, large page, COM init |
| [[01-platformlar/linux-core]] | cgroups, namespaces, systemd, D-Bus, epoll, inotify, seccomp, capabilities, tmpfs, /proc |
| [[01-platformlar/macos-core]] | GCD, XPC, Core Foundation, Metal, IOKit, LaunchAgent/Daemon, App Sandbox, Hardened Runtime |
| [[01-platformlar/rpi5-core]] | GPIO, DMA, PWM ses, I2S arayüzü |

### 2.3 `02-cekirdek-mekanizmalar/` — Çekirdek Mekanizmalar

| Dosya | İçerik |
|-------|--------|
| [[02-cekirdek-mekanizmalar/system-calls]] | POSIX syscall, Windows NT API, io_uring, epoll, kqueue |
| [[02-cekirdek-mekanizmalar/threading-model]] | Thread pool, lock-free yapılar, atomik işlemler, condition variables, mutex hiyerarşisi |
| [[02-cekirdek-mekanizmalar/memory-management]] | Sanal bellek, sayfa tabloları, memory pool, slab allocator, buffer yönetimi |
| [[02-cekirdek-mekanizmalar/ipc-mekanizmalari]] | Unix domain sockets, Windows named pipes, shared memory, message queues, gRPC |

### 2.4 `03-guvenlik-izolasyon/` — Güvenlik ve İzolasyon

| Dosya | İçerik |
|-------|--------|
| [[03-guvenlik-izolasyon/process-isolation]] | Process sandbox, capability dropping, chroot, namespaces, seccomp-bpf |
| [[03-guvenlik-izolasyon/container-runtime]] | Docker Engine, containerd, Kubernetes, Docker Compose, health check |

### 2.5 `04-tasinabilirlik/` — Taşınabilirlik

| Dosya | İçerik |
|-------|--------|
| [[04-tasinabilirlik/cross-platform-api]] | pthreads, SDL2, libuv, libevent, Boost.Asio, Rust tokio |

## 3. 2026 Araştırma Durumu (2026-10-06, exa web araştırması)

| Dosya | Zenginleştirildi mi? |
|-------|----------------------|
| README · windows-core · windows-api · linux-core · macos-core · rpi5-core · system-calls · threading-model · process-isolation · container-runtime · cross-platform-api | ✅ "2026 Durumu ve Kaynaklar" bölümü eklendi (kaynaklı) |
| memory-management · ipc-mekanizmalari | ❌ Araştırma kapsamı dışında → `UNKNOWN` olarak işaretlendi |

**Ana bulgular:** PREEMPT_RT Linux 6.12 ile mainline'a girdi · Windows 11 26H1 → WDK 10.0.28000.2526 / KMDF 1.33 · WASAPI `IAudioClient3` düşük gecikme · containerd 2.3.0-beta.2 (Nis 2026) · RPi5 üzerinde 7.0.1/7.1-rc PREEMPT_RT testleri (May 2026).

## 4. Bağımlılıklar

### 4.1 Alt Katmanlar

K0'ın altında doğrudan donanım/driver katmanı bulunur — bu dizin **kapsam dışı**:
`../../architecture/k030-driver-stack/` · `../../architecture/k033-platform-suruculeri/` · `../../architecture/k036-asio-drivers/` · `../../architecture/k037-wasapi-exclusive/` · `../../architecture/k038-core-audio-macos/` · `../../architecture/k039-alsa-native/`
(Dizin adları diskte doğrulandı; içerik eşlemesi `⚠️ VERIFICATION REQUIRED`.)

### 4.2 Üst Katmanlar

Ses motoru, DSP zinciri, backend/API, UI ve veri katmanları — bu dizin **kapsam dışı**.

## 5. Düzen Kuralları

1. **Yer tutucu adlandırma:** `01-platformlar` (platform) · `02-cekirdek-mekanizmalar` (çekirdek) · `03-guvenlik-izolasyon` (izolasyon) · `04-tasinabilirlik` (taşınabilirlik).
2. **Başlık formatı:** her dosyada `H1 = <dosya adı> — <başlık>`; frontmatter 7 zorunlu alan (`title`, `type`, `category`, `version: 1.0.0`, `status: active`, `updated`, `authority`).
3. **Bağlantı formatı:** `[[relative/path/to/file]]` (wiki-link).
4. **Taşıma/silme:** `.ai/architecture/` (k019…k029 dahil) **değiştirilmedi** — bu dizin salt yeniden yazımdır.
5. **Zero-Hallucination:** kanıtsız iddia `⚠️ VERIFICATION REQUIRED`, araştırılmayan konu `UNKNOWN`.

## 6. Durum: Implementasyon

| Öğe | Durum |
|------|-------|
| Kaynak 14 dosyanın hedefe yazımı | ✅ TAMAM (2026-10-06) |
| Gruplama (kök + 4 grup) | ✅ TAMAM |
| Frontmatter + H1 normalizasyonu | ✅ TAMAM (7 alan, `version: 1.0.0`, `status: active`) |
| Exa web araştırması ile zenginleştirme | ✅ 11 dosya · ❌ 2 dosya `UNKNOWN` |
| `log.md` kaydı | ✅ append-only |
| Commit | ❌ orkestratöre ait (bu oturumda atılmadı) |
