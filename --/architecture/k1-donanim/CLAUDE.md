---
title: "k1 Donanım — Katman Anayasası Özeti"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/CLAUDE.md (anayasa SSOT) — bu dosya katman özeti, kural KOYMaz"
updated: 2026-10-07
tier: 3
domain: k1-donanim
ssot: false
risk: medium
owner: "audio-hw"
depends-on: [".ai/architecture/k1-donanim/index.md", ".ai/CLAUDE.md"]
---

# k1 — Katman Anayasası Özeti (derived)

> Bu dosya **özet ve pointer'dır**; bağlayıcı kurallar [[CLAUDE]] (anayasa) ve [[architecture/rules]] içindedir.
> Çelişkide `.ai/CLAUDE.md` > `architecture/rules.md` > bu dosya kazanır (R8).

## K1'a Uygulanan Guardrails (özet)

1. **PCM5122 YASAK** (ADR-038 · H001) → PCM3168A + AK4458 + XMOS XU316. PCM3168A = **codec** (6-in/8-out ADC+DAC) — "yalnız ADC" yazımı web ile düzeltildi (ti.com, 2026-10-07).
2. **Class AB topolojisi zorunlu; Class D YASAK** (ADR-089) · **DC-Only** güç; sinyal zincirine parazit yasak (anayasa §5 K1).
3. **DC offset >0.5V → koruma rölesi zorunlu** (anayasa §23) · güç korumaları UVP/OVP/OCP/OTP zorunlu (anayasa §5 K17).
4. **Zero-Hallucination:** tasarım iddiaları ADR / web (URL+tarih) / repo grep ile kanıtlanır; kanıtsız = `⚠️ VERIFICATION REQUIRED`. Repo kodu yoksa `durum: DESIGN/PLANNED` · `kod-yolu: YOK` (kanıt: ADR-038/061/063/064/089 + xmos.com · ti.com · akm.com · onsemi.com web doğrulaması 2026-10-07).
5. **FM 13 alan** her dosyada zorunlu (validator `fm-check`) · bu dizinde ADR, script, security politika **üretilmez** (R1) · 3'lü kaynak kanıtı zorunlu (R14).
6. Domain kuralların tam özeti: `AGENTS.md` §3 (D1–D10) — orası SSOT'tur, burada tekrarlanmaz (R7).

## Hızlı Doğrulama

```bash
node .ai/scripts/validate.mjs --check   # 11 check — bu dosya da kapsamda
```