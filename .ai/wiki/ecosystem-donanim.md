---
title: "Ekosistem Donanım — Devre ve BOM Referansları"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-donanim]
tags: [ekosistem, donanım, dac, amplifikator, guk, bom, adr-089]
---

# Ekosistem Donanım

Tek ham dosyanın (`donanım-devre-referanslari.md`, seri 4/6) tema özeti: TPA3255, ddabidov XU316+ES9039, MT3608 boost, modüler amfi/çıkış stage ve tek-çip rp2040 referanslarını K1/K16/K17/K19/K20 (+K18) katmanlarına bağlar; ADR-089 (Class AB amplifikatör) kararını ve PCM5122 yasağını taşır. Ham dosya adı Türkçe karakterli gelir ve yeniden adlandırılmaz; slug ASCII `ecosystem-donanim`'dir.

## Dosyalar

| Dosya | İçerik/Boyut |
|-------|--------------|
| `raw/ecosystem/donanım-devre-referanslari.md` | TPA3255, XU316+ES9039, MT3608, amfi/çıkış stage, ADR-089, lisans/yasak matrisi · 32.7KB · 540 satır |

## Ana Bulgular

1. Başlık DÜZELTME bloğu (2026-09-24, `.ai/CLAUDE.md` SSOT) K eşlemesini düzeltir: DAC dizisi → K1 · güç amplifikatörü → K16 · boost/güç kaynağı → K17 · PCB → K19 · BOM → K20 · termal → K18.
2. ⚠ Belge içi çelişki: gövde eski eşlemeyi sürdürür (§2.1 "DAC → K17", §3.3/§3.6.2/§3.7.3 "boost → K19", §3.7.4 "K17 — DAC") → `⚠ VERIFICATION REQUIRED`; `index.md` §3.4 düzeltmeyle tutarlı.
3. TPA3255 (Class-D, filter-less full-bridge) **ADR-089 ile reddedildi** — yalnız topoloji dersi; K16 = Class AB ×8 (Darlington MJL21194/MJL21193).
4. ddabidov XU316 köprüsü birebir aynı çip → firmware/sözleşme referansı; ES9039 vs mevcut PCM3168A+AK4458 farkı ayrı DAC ADR'si gerektirir (§5.3: geç / koru / hibrit).
5. ADR-089 ekseni: 12-24V → ±35V boost (LM5122 ×2, %96) · 6S LiPo 22.2V / 19-24V adaptör · 1/2/4/6/8 modüler kanal · 120dB+ hedef (SNR >105dB / THD+N <0.005%) · hibrit MCU (XU316 + STM32H7·RP2040 + RPi5).
6. rp2040-dac-amp **olumsuz referans**: çözünürlük yetersiz → "neden kullanılmıyor" red gerekçesi; K17 eşleşmesi ❌.
7. Lisans: TPA3255 = TI veri sayfası ✅; ddabidov, MT3608, modular-amplituner/PBA MK1/DA15, OpAmp-Headphone, spin-dac, rp2040 → hepsi `⚠ VERIFICATION REQUIRED` (LICENSE okunmadan kopya yok); ❌ PCM5122 · ❌ AGPL/uyumsuz şema dosyası.

## İlgili Sayfalar

- [[ecosystem-genel]] — envanter ve lisans kapısı
- [[ecosystem-audio-dsp]] — DSP↔donanım I2S/sürücü sözleşmesi
- [[ecosystem-mimarileri]] — güç telemetri devri (K14)
- [[neva-engine]] — ses motoru · [[vault-brain]] — ADR-089 defteri
