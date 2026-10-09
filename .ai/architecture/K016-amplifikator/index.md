---
type: architecture
category: layer
title: "K016 — Amplifikatör"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K016 — Amplifikatör

## §1 Kimlik
- Katman: K016 · Alan: **A5** (K16-K20 — Bileşenler/donanım).
- Kapsam: Class AB amplifikatör tasarımı; kanal varyant ürün ailesi.

## §2 Sorumluluk
1. Class AB amplifikatör topolojisi ve çalışma noktası (ADR-089).
2. Kanal varyant SKU ailesi: mono → 8+1 (ADR-090).
3. Ses zinciri gürültü/bozulma hedefleri — şartname düzeyi.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 altyapı/donanım zemini (K000–K005) — sayısal kural: yalnız alt katman (plan §2-1).
- **Üst (çağıran):** K020 üretim katmanı.
- A5 içi fiziksel akış (K017 güç → K018 termal → K019 PCB) plan §3'te tanımlanmadı → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost (accepted) |
| ADR-090 | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) |

## §5 Durum
**PLANNED** — ADR dosyaları diskte (`.ai/.decisions/accepted/ADR-089-classab-24v.md`); devre/ölçüm kanıtı yok.

## §6 Risk / Not
- Ölçüm/termal doğrulama verisi yok → performans iddiası yazılmaz (**UNKNOWN**).
