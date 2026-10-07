---
title: "katman-baglilik-matrisi — Katman Bağımlılık Matrisi (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-layers
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# katman-baglilik-matrisi — Katman Bağımlılık Matrisi (stub)

**Durum:** `architecture/katman-baglilik-matrisi.md` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

**L0–L6 bağımlılık matrisi (tek kaynak):** `.ai/CLAUDE.md` §5.1 Layer Dependency Matrix

| Kural | Değer |
|-------|-------|
| L6→L5→L4→L3→L2→L1→L0 | ✅ izinli (içe doğru) |
| L0→L2/L3 · L1→L3 · L3→L0 | ❌ Layer Violation → derhal revert + log CRITICAL |
| K-satırı kısıtları | `.claude/CLAUDE.md` §5 (her katmanın "Katı Kısıtlamalar" sütunu) |
| K→domain eşlemesi | [[architecture/00-master-index]] §2 (legacy K0-K20 → d01-d10) |
| Domain içi katman bağımlılıkları | 10 domain tablosu (her tablo kendi K-aralığının bağımlılık notlarını taşır) |

**Not:** Yeni ağaçta bağımlılık matrisi iki düzlemdedir: (1) L0-L6 vault matrisi (`.ai/CLAUDE.md` §5.1),
(2) K000-K499 domain eşlemesi (bu ağaç). İkisi de yukarıdadır; ayrı dosya **üretilmez** (SSOT dedup).
