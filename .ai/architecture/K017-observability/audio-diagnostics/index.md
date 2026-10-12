---
title: "K017-observability/audio-diagnostics — Subdomain Kaydı"
node_id: K017-observability/audio-diagnostics
node_type: subdomain
status: draft
parent_id: K017-observability
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K017-observability   # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# bu turda disk kanıtı TOPLANMADI — kanıt bekliyor"
validation:
  - "okuma/gözlem YOK — verify-required"
open_questions:
  - "audio-diagnostics hangi donanım/altyapıda ölçülecek (ASIO/WASAPI/RPi5)? (⚠️ VERIFICATION REQUIRED)"
  - "K003-audio/K004-dsp ile sınır: teşhis burada, düzeltme orada — tescil gerekli"
---

# K017-observability/audio-diagnostics

## 1. Rol (ELI15)

Ses zincirine özgü teşhis: xrun/underrun, klip, gecikme ölçümü kayıtları.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K017-observability (sistem gözetimi) altındadır.
- Test stratejisi (K019) ve deploy kapısı (K018) kapsamı DEĞİLDİR — yalnız entegrasyon kuralları bağlayıcıdır.
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain Kökü | [[index]] (K017-observability) · master: observability-master.md |

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
