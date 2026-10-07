---
title: "amplifier-classab-circuit — Eski Class AB Devre Dokümanı (stub-with-truth)"
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

# amplifier-classab-circuit — Class AB Devresi (stub)

**Durum:** `architecture/amplifier-classab-circuit.md` **diskte YOK** (eski ağaç silindi) — 3 link.
Eşdeğer `electronics/amplifier-classab-circuit.md` de YOK (`.ai/brain.md` §5 referansı ölü).

## Bugünkü Karşılığı (gerçek kanıt — tasarım kaynakları)

- **Karar:** ADR-089 (`classab-24v`) · ADR-090 (kanal varyant SKU) — `.ai/.decisions/accepted/`
- **Devre bileşen listesi:** `.ai/brain.md` §5 "H1: Class AB Amplifikatör (K1)"
  (Q1/Q2 BC546B diferansiyel · Q5 BC556B akım havuzu · Q9 KSC3503 VAS · Q10 BD139 Vbe çarpımı ·
  Q15 MJL21194 NPN · Q17 MJL21193 PNP · SG3525 push-pull kontrolcü)
- **Sistem spesifikasyonu:** `.claude/CLAUDE.md` §5 (K16) — 50W/kanal @ 8Ω, THD <0.005%, 8 kanal modüler

⚠️ **VERIFICATION REQUIRED:** Ayrıntılı devre dokümanı (şema, bias prosedürü, test protokolü)
diskte yok — yeniden yazım `.ai/brain.md` + ADR-089 + `.claude/CLAUDE.md` §5 kaynaklarıyla yapılmalıdır.

**Domain:** d10 → [[architecture/19-domain-d10-elektronik-tasarim]] (K450–K456)
