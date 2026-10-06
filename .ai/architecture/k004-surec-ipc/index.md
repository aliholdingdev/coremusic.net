---
title: "Süreç / IPC Alt Katmanı - k004-surec-ipc"
type: architecture-sublayer
category: isletim-sistemi
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - K0 (k003-k005)"
updated: 2026-10-06
---

# Süreç / IPC - `k004-surec-ipc`

> K0 İşletim Sistemi alt katmanı · Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (salt-okunur)
> Dosya: 2 konu + index · Aktarım: verbatim · Güncelleme: 2026-10-06

## Genel Bakış

Klasör, K0 katmanının süreç izolasyonu ve süreçler arası iletişim başlıklarını iki salt-okunur kaynak dosyadan alır: `process-isolation.md` (629 satır) ve `ipc-mekanizmalari.md` (658 satır).

**process-isolation.md — Genel Bakış**

Process Isolation modülü, COREMUSIC'ın güvenlik ve izolasyon stratejilerini tanımlar. Process sandboxing, capability dropping, chroot, namespaces ve seccomp-bpf ile uygulama ve process'leri birbirinden izole eder. Güvenli çalışma ortamı sağlar ve yetki yönetimini kontrol eder.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L12-L14

**ipc-mekanizmalari.md — Genel Bakış**

IPC (Inter-Process Communication) Mekanizmaları modülü, COREMUSIC'ın process arası iletişim çözümlerini yönetir. Unix Domain Sockets, Windows Named Pipes, Shared Memory, Message Queues ve gRPC gibi çeşitli iletişim protokollerini destekler. Yüksek performanslı, düşük gecikmeli ve güvenli veri transferi sağlar.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L12-L14

## İçindekiler

- [[surec-izolasyon.md]] - Process Isolation (Process Sandbox, Capability Dropping, chroot, Namespaces, seccomp-bpf)
- [[ipc-mekanizmalari-karsilastirma.md]] - IPC Mekanizmaları (Unix Domain Sockets, Windows Named Pipes, Shared Memory, Message Queues, gRPC)

## Kaynak Dosyalar

Bölüm aralıkları başlık satırı dahil hesaplanmıştır; ✓ = konu dosyasına verbatim aktarıldı, - = kaynakta duruyor (aktarılmadı).

### process-isolation.md (629 satır)
| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Genel Bakış | L11-L14 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Teknik Detaylar | L15-L16 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ### 1. Process Sandbox | L17-L117 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ### 2. Capability Dropping | L118-L217 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ### 3. chroot | L218-L297 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ### 4. Namespaces | L298-L413 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ### 5. seccomp-bpf | L414-L530 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## API / Arayüz | L531-L581 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Bağımlılıklar | L582-L596 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Performans Metrikleri | L597-L606 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Güvenlik Notları | L607-L614 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Durum: Implementasyon | L615-L629 | ✓ verbatim |

### ipc-mekanizmalari.md (658 satır)
| Kaynak | § Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Genel Bakış | L11-L14 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Teknik Detaylar | L15-L16 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ### 1. Unix Domain Sockets | L17-L103 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ### 2. Windows Named Pipes | L104-L210 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ### 3. Shared Memory | L211-L316 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ### 4. Message Queues | L317-L383 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ### 5. gRPC | L384-L553 | - (kaynakta) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## API / Arayüz | L554-L608 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Bağımlılıklar | L609-L625 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Performans Metrikleri | L626-L635 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Güvenlik Notları | L636-L643 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Durum: Implementasyon | L644-L658 | ✓ verbatim |

**README.md yaprak kanıtları (verbatim):**
### K0.2 — Bellek & Süreç Yönetimi

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.2.1** | Memory Management (3. katman) | memory-management.md — 11 yaprak |
| K0.2.1.1 | Genel Bakış | memory-management.md L11 |
| K0.2.1.2 | Teknik Detaylar | memory-management.md L15 |
| K0.2.1.3 | API / Arayüz | memory-management.md L463 |
| K0.2.1.4 | Bağımlılıklar | memory-management.md L513 |
| K0.2.1.5 | Performans Metrikleri | memory-management.md L529 |
| K0.2.1.6 | Durum: Implementasyon | memory-management.md L539 |
| K0.2.1.7 | Virtual Memory | memory-management.md L17 |
| K0.2.1.8 | Page Tables | memory-management.md L75 |
| K0.2.1.9 | Memory Pool | memory-management.md L153 |
| K0.2.1.10 | Slab Allocator | memory-management.md L229 |
| K0.2.1.11 | Buffer Management | memory-management.md L353 |
| **K0.2.2** | Process Isolation (3. katman) | process-isolation.md — 12 yaprak |
| K0.2.2.1 | Genel Bakış | process-isolation.md L11 |
| K0.2.2.2 | Teknik Detaylar | process-isolation.md L15 |
| K0.2.2.3 | API / Arayüz | process-isolation.md L531 |
| K0.2.2.4 | Bağımlılıklar | process-isolation.md L582 |
| K0.2.2.5 | Performans Metrikleri | process-isolation.md L597 |
| K0.2.2.6 | Güvenlik Notları | process-isolation.md L607 |
| K0.2.2.7 | Durum: Implementasyon | process-isolation.md L615 |
| K0.2.2.8 | Process Sandbox | process-isolation.md L17 |
| K0.2.2.9 | Capability Dropping | process-isolation.md L118 |
| K0.2.2.10 | chroot | process-isolation.md L218 |
| K0.2.2.11 | Namespaces | process-isolation.md L298 |
| K0.2.2.12 | seccomp-bpf | process-isolation.md L414 |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md - L489-L518

### K0.4 — IPC Mekanizmaları

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.4.1** | IPC Mekanizmaları (3. katman) | ipc-mekanizmalari.md — 12 yaprak |
| K0.4.1.1 | Genel Bakış | ipc-mekanizmalari.md L11 |
| K0.4.1.2 | Teknik Detaylar | ipc-mekanizmalari.md L15 |
| K0.4.1.3 | API / Arayüz | ipc-mekanizmalari.md L554 |
| K0.4.1.4 | Bağımlılıklar | ipc-mekanizmalari.md L609 |
| K0.4.1.5 | Performans Metrikleri | ipc-mekanizmalari.md L626 |
| K0.4.1.6 | Güvenlik Notları | ipc-mekanizmalari.md L636 |
| K0.4.1.7 | Durum: Implementasyon | ipc-mekanizmalari.md L644 |
| K0.4.1.8 | Unix Domain Sockets | ipc-mekanizmalari.md L17 |
| K0.4.1.9 | Windows Named Pipes | ipc-mekanizmalari.md L104 |
| K0.4.1.10 | Shared Memory | ipc-mekanizmalari.md L211 |
| K0.4.1.11 | Message Queues | ipc-mekanizmalari.md L317 |
| K0.4.1.12 | gRPC | ipc-mekanizmalari.md L384 |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md - L536-L553

## Alt / Üst Katmanlar

**process-isolation.md — Bağımlılıklar alt bölümleri**
### Alt Katmanlar
- K0 İşletim Sistemi katmanı
- Linux kernel security features

### Üst Katmanlar
- K1 Ses Motoru (sandbox içinde)
- K3 Uygulama Katmanı (sandbox içinde)


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L589-L596

**ipc-mekanizmalari.md — Bağımlılıklar alt bölümleri**
### Alt Katmanlar
- K0 İşletim Sistemi katmanı (platform-specific)
- Ağ stack'i (TCP/IP)

### Üst Katmanlar
- K1 Ses Motoru
- K2 Ağ Katmanı
- K3 Uygulama Katmanı


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L617-L625

## Dış Bağlantılar

- [[../k000-windows-core/index]]
- [[../k001-linux-rpi5/index]]
- [[../k002-macos-tasinabilirlik/index]]
- [[../k003-cagri-thread/index]]
- [[../k005-bellek-container/index]]

## Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Capability dropping implementasyonu
2. seccomp-bpf filter oluşturma
3. Namespace tabanlı izolasyon
4. chroot jail oluşturma
5. Tam sandbox implementasyonu

**Sonraki Adımlar**:
- Capability dropping testlerinin yapılması
- seccomp filter'larının test edilmesi
- Namespace izolasyon testlerinin yazılması

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L615-L629

**ipc-mekanizmalari.md — Durum içeriği:**

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Shared Memory implementasyonu (en yüksek performans)
2. Unix Domain Sockets implementasyonu
3. Windows Named Pipes implementasyonu
4. Message Queues implementasyonu
5. gRPC entegrasyonu

**Sonraki Adımlar**:
- Shared memory spinlock testlerinin yapılması
- Socket performance benchmark'larının hazırlanması
- gRPC proto dosyalarının compile edilmesi

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L645-L658

---
**Wiki-link:** [[index.md]] · [[surec-izolasyon.md]] · [[ipc-mekanizmalari-karsilastirma.md]]
