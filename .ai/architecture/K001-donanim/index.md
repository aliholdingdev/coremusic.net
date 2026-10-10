---
type: architecture
category: layer
title: "K001 — Donanım"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K001 — Donanım

## §1 Kimlik
- Katman: K001 · Alan: **A0** (K0-K5 — Altyapı/Donanım).
- Kapsam: donanım/elektronik mimarisi — ses kartı çip seçimi, DAC/ADC zinciri, L6 mimari tanımı.

## §2 Sorumluluk
1. Elektronik mimari (L6) tanımı ve katman sorumlulukları (ADR-061).
2. 8.1 ses kartı çip seçimi: PCM3168A + XMOS (ADR-038).
3. Donanım şartnameleri (spec) üretimi — spec düzeyi; üretim dosyası bu katmanda değil.
4. A5 bileşen grubunun (K016–K020) fiziksel hedefi olmak.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K000 (altyapı/barındırma zemini) → plan §2-1.
- **Üst (çağıran):** K002 (sürücü — donanımı programatik kullanır).
- Not: A5 bileşenleri bu katmanı fiziksel olarak besler; A5 içi bağımlılık grafiği plan §3'te tanımlanmadı → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-061 | Electronics Architecture (L6) |
| ADR-038 | 8.1 Sound Card (PCM3168A + XMOS) |

## §5 Alt Klasör Dizini (2026-10-10 disk ölçümü)

| # | Alt klasör | Dosya | Durum |
|---|---|---|---|
| 1 | `donanım-api/` | index.md | PLANNED (spec) |
| 2 | `donanım-core/` | index.md | PLANNED (spec) |
| 3 | `donanım-kernel/` | index.md | PLANNED (spec — sınır) |
| 4 | `donanaım-type/` | index.md · `electroncis-circuits/` (index + `amfiliper/` index) · `hdd/` index · `ssd/` index | circuits: PLANNED · hdd/ssd: **UNKNOWN** |

> Dizin adlarındaki yazım hataları (`donanaım`, `amfiliper`) **mevcut yapıdır — değiştirilmedi** (onaysız rename yasak); rapor edildi.

## §6 Durum
**PLANNED** — şartname/ADR düzeyinde. `.ai/AGENTS.md` §25.2: Embedded/DSP yüzeyi "⚠️ PLANNED (spec mevcut)".

## §7 Risk / Not
- Ölçüm/üretim verisi yok → performans iddiası yazılmaz (**UNKNOWN**).
- Donanım içerikleri yalnız `.ai/.decisions/` altında ADR olarak mevcut.
- `donanaım-type/hdd` ve `ssd` dallarının K001 kapsamına dahil olma kasıtı kanıtlanamadı → `⚠️ VERIFICATION REQUIRED` (§5-4).
