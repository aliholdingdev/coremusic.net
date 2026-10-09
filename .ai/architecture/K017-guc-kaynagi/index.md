---
type: architecture
category: layer
title: "K017 — Güç Kaynağı"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K017 — Güç Kaynağı

## §1 Kimlik
- Katman: K017 · Alan: **A5** (K16-K20).
- Kapsam: 6S LiPo batarya + ±35V boost konvertör beslemesi.

## §2 Sorumluluk
1. 6S LiPo batarya paketi ve şarj/koruma akışı (ADR-089).
2. ±35V boost konvertör tasarımı ve filtreleme.
3. Amplifikatör (K016) besleme şartnamesi.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 zemini (K000–K005) — sayısal kural (plan §2-1).
- **Üst (çağıran):** K016 (amplifikatörü besler) · K020 (üretim).
- A5 içi kenarlar plan §3'te yok → **UNKNOWN**.

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

## §5 Durum
**PLANNED** — ADR-089 şartname düzeyinde; güç hesabı/ölçüm dosyası diskte yok.

## §6 Risk / Not
- Koruma devresi (aşırı akım/ısıl) şartnamesi kanıtlanmadı → **UNKNOWN**.
