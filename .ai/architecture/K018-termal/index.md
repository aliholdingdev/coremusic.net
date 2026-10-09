---
type: architecture
category: layer
title: "K018 — Termal"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K018 — Termal

## §1 Kimlik
- Katman: K018 · Alan: **A5** (K16-K20).
- Kapsam: termal tasarım — ısı dağıtımı, sınırlar, soğutma.

## §2 Sorumluluk
1. Amplifikatör (K016) ve güç kaynağı (K017) için ısı hesabı kapsamı.
2. Sınırların tanımlanması (kasa/radyatör) — şartname düzeyi.
3. Termal koruma eşiği tanımı (donanım katmanları ile).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** A0 zemini (K000–K005) — sayısal kural (plan §2-1).
- **Üst (çağıran):** K016 · K017 (ısı kaynağı bileşenler) · K020.
- A5 içi kenarlar plan §3'te yok → **UNKNOWN**.

## §4 ADR Bağlantıları
Termal başlıklı ADR: **YOK** (`.ai/.decisions/index.md` §3-§4 taramasında termal başlık bulunmadı) → **UNKNOWN**; ADR-089 içeriğinin termal kısıt içerip içermediği okunmadı.

## §5 Durum
**PLANNED** — termal hesap/ölçüm kanıtı diskte yok.

## §6 Risk / Not
- Sıcaklık/ölçüm verisi olmadan SKU (ADR-090) doğrulanamaz; termal ADR gereksinimi Vault Steward'a açık.
