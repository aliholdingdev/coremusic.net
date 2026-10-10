---
type: architecture
category: layer-subindex
title: "K000 · isletim-sistemi-kernel — Çekirdek Kavramı ve Zemin Çekirdeği"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# isletim-sistemi-kernel — Çekirdek Kavramı

> Üst indeks: `[[../index]]` · Makro: `[[../os-master]]`

## §1 Kapsam
İki ayrı şey, bu dosyada **karıştırılmaz**:
1. **Gerçek OS kernel'i** (Windows NT kernel, Linux çekirdeği) — bu vault'ta uygulanmaz, yalnız referanstır.
2. **Uygulama çekirdeği** — CoreMusic'in istek yaşam döngüsünü yürüten zemin sınıfı (disk kanıtlı).

## §2 1 — Gerçek OS Kernel'i (web-doğrulanmış referans)
| Konu | Gerçek | Kaynak |
|---|---|---|
| Linux | ALSA sürücüleri çekirdeğe gömülü; `/dev/snd/*` üzerinden user-space erişim | kernel-internals.org/alsa |
| Windows | Core Audio user-mode katmanı (`Audioses.dll`, `Mmdevapi.dll`) kernel sürücüsünü ayırır | learn.microsoft.com — User-Mode Audio Components |
| Sınır | Vault **kernel modifikasyonu yapmaz** — donanım/driver katmanı K001–K003'te |

## §3 2 — Uygulama Çekirdeği (disk kanıtı)
| Sınıf | Yol | Rol |
|---|---|---|
| `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | `handle()` — route meta, HTML shell, redirect, request normalize |
| `RuntimeBootstrap` | `shared/src/Bootstrap/RuntimeBootstrap.php` | runtime başlatma |
| Middleware pipeline | `shared/src/Api/Middleware/ApiMiddlewarePipeline.php` | uçtan uca pipeline |

**İsimlendirme notu:** `PageRouterKernel` proje içi bir uygulama çekirdeğidir; OS kernel'i değildir (master index §4-11 kuralı ile aynı çizgi).

## §4 Bağımlılık
- **Alt:** `isletim-sistemi-core`.
- **Üst:** `isletim-sistemi-api` · K010 (uygulama).

## §5 Durum
**IMPLEMENTED** — §3 dosyaları diskte mevcut. Method gövdeleri bu görevde okunmadı → iç davranış `UNKNOWN`.
