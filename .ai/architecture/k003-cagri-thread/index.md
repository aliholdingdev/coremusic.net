---
title: "Çağrı/Thread Alt Katmanı - k003-cagri-thread"
type: architecture-sublayer
category: isletim-sistemi
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - K0 (k003-k005)"
updated: 2026-10-06
---

# Çağrı / Thread - `k003-cagri-thread`

> K0 İşletim Sistemi alt katmanı · Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (salt-okunur)
> Dosya: 2 konu + index · Aktarım: verbatim · Güncelleme: 2026-10-06

## Genel Bakış

Klasör, K0 katmanının çağrı ve thread başlıklarını iki salt-okunur kaynak dosyadan alır: `threading-model.md` (736 satır) ve `system-calls.md` (692 satır).

**threading-model.md — Genel Bakış**

Threading Model modülü, COREMUSIC'ın çoklu iş parçacığı yönetim stratejilerini tanımlar. Thread pool'lar, lock-free veri yapıları, atomik operations, condition variables ve mutex hiyerarşisi ile yüksek performanslı ve thread-safe bir ortam sağlar. Gerçek zamanlı ses işleme için deterministik davranış ve minimum gecikme hedefler.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md - L12-L14

**system-calls.md — Genel Bakış**

System Calls modülü, COREMUSIC'ın düşük seviyeli işletim sistemi arayüzlerini tanımlar. POSIX syscall interface, Windows NT API, Linux io_uring ve epoll/kqueue ile yüksek performanslı I/O işleme sağlar. Her platform için optimize edilmiş system call implementasyonları içerir.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L12-L14

## İçindekiler

- [[threading-ve-gercek-zaman.md]] - Threading Model (Thread Pools, Lock-free Structures, Atomic Operations, Condition Variables, Mutex Hierarchy)
- [[system-calls-rehberi.md]] - System Calls (POSIX Syscall Interface, Windows NT API, Linux io_uring, epoll, kqueue)

## Kaynak Dosyalar

Bölüm aralıkları başlık satırı dahil hesaplanmıştır; ✓ = konu dosyasına verbatim aktarıldı, - = kaynakta duruyor (aktarılmadı).

### threading-model.md (736 satır)
| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Genel Bakış | L11-L14 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Teknik Detaylar | L15-L16 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ### 1. Thread Pools | L17-L181 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ### 2. Lock-free Structures | L182-L315 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ### 3. Atomic Operations | L316-L442 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ### 4. Condition Variables | L443-L519 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ### 5. Mutex Hierarchy | L520-L622 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## API / Arayüz | L623-L695 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Bağımlılıklar | L696-L710 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Performans Metrikleri | L711-L721 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Durum: Implementasyon | L722-L736 | ✓ verbatim |

### system-calls.md (692 satır)
| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Genel Bakış | L11-L14 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Teknik Detaylar | L15-L16 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ### 1. POSIX Syscall Interface | L17-L120 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ### 2. Windows NT API | L121-L264 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ### 3. Linux io_uring | L265-L402 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ### 4. epoll (Linux) | L403-L485 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ### 5. kqueue (macOS/BSD) | L486-L591 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## API / Arayüz | L592-L649 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Bağımlılıklar | L650-L666 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Performans Metrikleri | L667-L677 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Durum: Implementasyon | L678-L692 | ✓ verbatim |

**README.md yaprak kanıtları (verbatim):**
### K0.3 — Threading Model

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.3.1** | Threading Model (3. katman) | threading-model.md — 11 yaprak |
| K0.3.1.1 | Genel Bakış | threading-model.md L11 |
| K0.3.1.2 | Teknik Detaylar | threading-model.md L15 |
| K0.3.1.3 | API / Arayüz | threading-model.md L623 |
| K0.3.1.4 | Bağımlılıklar | threading-model.md L696 |
| K0.3.1.5 | Performans Metrikleri | threading-model.md L711 |
| K0.3.1.6 | Durum: Implementasyon | threading-model.md L722 |
| K0.3.1.7 | Thread Pools | threading-model.md L17 |
| K0.3.1.8 | Lock-free Structures | threading-model.md L182 |
| K0.3.1.9 | Atomic Operations | threading-model.md L316 |
| K0.3.1.10 | Condition Variables | threading-model.md L443 |
| K0.3.1.11 | Mutex Hierarchy | threading-model.md L520 |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md - L519-L535

### K0.5 — Sistem Çağrıları

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.5.1** | System Calls (3. katman) | system-calls.md — 15 yaprak |
| K0.5.1.1 | Genel Bakış | system-calls.md L11 |
| K0.5.1.2 | Teknik Detaylar | system-calls.md L15 |
| K0.5.1.3 | API / Arayüz | system-calls.md L592 |
| K0.5.1.4 | Bağımlılıklar | system-calls.md L650 |
| K0.5.1.5 | Performans Metrikleri | system-calls.md L667 |
| K0.5.1.6 | Durum: Implementasyon | system-calls.md L678 |
| K0.5.1.7 | POSIX Syscall Interface | system-calls.md L17 |
| K0.5.1.8 | Windows NT API | system-calls.md L121 |
| K0.5.1.9 | Linux io_uring | system-calls.md L265 |
| K0.5.1.10 | epoll (Linux) | system-calls.md L403 |
| K0.5.1.11 | kqueue (macOS/BSD) | system-calls.md L486 |
| K0.5.1.12 | COREMUSIC System Calls API Başlık Dosyası | system-calls.md L594 |
| K0.5.1.13 | Gereksinimler | system-calls.md L652 |
| K0.5.1.14 | Alt Katmanlar | system-calls.md L658 |
| K0.5.1.15 | Üst Katmanlar | system-calls.md L662 |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md - L554-L574

## Alt / Üst Katmanlar

**threading-model.md — Bağımlılıklar alt bölümleri**
### Alt Katmanlar
- K0 Memory Management
- K0 İşletim Sistemi katmanı

### Üst Katmanlar
- K1 Ses Motoru
- K3 Uygulama Katmanı


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md - L703-L710

**system-calls.md — Bağımlılıklar alt bölümleri**
### Alt Katmanlar
- CPU instruction set
- Donanım I/O

### Üst Katmanlar
- K0 Cross-Platform API
- K0 IPC Mekanizmaları
- K1 Ses Motoru


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L658-L666

## Dış Bağlantılar

- [[../k000-windows-core/index]]
- [[../k001-linux-rpi5/index]]
- [[../k002-macos-tasinabilirlik/index]]
- [[../k004-surec-ipc/index]]
- [[../k005-bellek-container/index]]

## Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Thread pool implementasyonu
2. Lock-free queue implementasyonu
3. Spinlock implementasyonu
4. Mutex hierarchy
5. RWLock implementasyonu

**Sonraki Adımlar**:
- Thread pool performans testlerinin yapılması
- Lock-free yapıların stres testleri
- Deadlock test senaryolarının yazılması

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md - L722-L736

**system-calls.md — Durum içeriği:**

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. io_uring implementasyonu (Linux için en yüksek performans)
2. epoll implementasyonu
3. kqueue implementasyonu
4. POSIX syscall wrapper'ları
5. Windows NT API entegrasyonu

**Sonraki Adımlar**:
- io_uring benchmark testlerinin yapılması
- epoll ile high-performance networking testleri
- kqueue macOS implementasyon testleri

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L679-L692

---
**Wiki-link:** [[index.md]] · [[threading-ve-gercek-zaman.md]] · [[system-calls-rehberi.md]]
