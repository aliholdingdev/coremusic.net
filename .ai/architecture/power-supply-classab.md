---
title: "power-supply-classab — Eski Güç Kaynağı Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-hardware
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# power-supply-classab — Güç Kaynağı (stub)

**Durum:** `architecture/power-supply-classab.md` **diskte YOK** (eski ağaç silindi) — 1 link.
Eşdeğer `electronics/power-supply-classab.md` de YOK (`.ai/brain.md` §5 referansı ölü).

## Bugünkü Karşılığı (gerçek kanıt)

- **Ana tasarım:** ADR-089 (`classab-24v`) — LM5122 ×2 (boost + inverting) → ±35V, 6S LiPo 22.2V, %96 verim
  (`.claude/CLAUDE.md` §5 K17 satırı)
- **Eski alternatif tasarım:** `.ai/brain.md` §5 — ±40V Push-Pull (SG3525/KA3525), 12V-24V DC giriş,
  merkez-uçlu trafo, Hi-Fi LC filtre (H2)
- **Koruma:** UVP/OVP/OCP/OTP (`.claude/CLAUDE.md` §5 K17 kısıtı)
- **Web doğrulaması:** LM5122 — kök `CLAUDE.md` §Web Verification (ti.com, 2026-10-07)

⚠️ **VERIFICATION REQUIRED:** Ayrıntılı güç kaynağı şema dokümanı diskte yok.

**Domain:** d10 → [[architecture/19-domain-d10-elektronik-tasarim]] (K457–K462)
