---
type: architecture
category: layer-subindex
title: "K001 · electroncis-circuits/amfiliper — Amplifikatör Devresi"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# amfiliper — Amplifikatör Devresi

> Üst indeks: `[[../index]]` · Karar SSOT: `ADR-089-classab-24v` · `ADR-090-channel-variant-product-family`
> Dizin adındaki yazım (`amfiliper`) **mevcut yapıdır — değiştirilmedi**.

## §1 Kapsam
Class AB amplifikatör devresi — ses yolu güç aşaması (ADR-061 zincirinin `VAS → Output → hoparlör` halkası).

## §2 Kararlar (ADR başlığı düzeyi — metin bu görevde okunmadı)

| ADR | Karar | Durum |
|---|---|---|
| `ADR-089-classab-24v` | Class AB Amplifikatör Sistemi — **8→50W, 120dB+, 12-24V Boost, Hibrit MCU** | accepted |
| `ADR-090-channel-variant-product-family` | Kanal varyant ürün ailesi — mono · 2 · 2+1 · 4 · 5 · 6 · 7 · 8 · 7+1 · 8+1 | accepted |

> Kaynak: `.ai/.decisions/accepted/` başlık/status satırları (2026-10-10 grep). Ayrıntılı parametreler
> (gaz/distorsiyon/topoloji) ADR-089 metnindedir → bu dosyaya **kopyalanmaz** (SSOT).

## §3 Bağımlılık
- **Alt:** K000 (zemin).
- **Üst:** `[[../index]]` · K016 (Amplifikatör katmanı — A5) · K017 (Güç Kaynağı — 12-24V boost).

## §4 Durum
**PLANNED** — ADR accepted; devre/ölçüm/üretim kanıtı K016/K017–K020'de ve o katmanlar da PLANNED.

## §5 Risk / Not
- Performans iddiası (120dB, 50W) ADR başlığından alınmıştır; **ölçüm verisi YOK** → `⚠️ VERIFICATION REQUIRED`.
