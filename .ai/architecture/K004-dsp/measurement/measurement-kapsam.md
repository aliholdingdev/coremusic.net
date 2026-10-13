---
title: "measurement — Kapsam"
type: architecture-scope
node_id: K004-dsp/measurement
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# measurement — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 11 bu dalı **"Frekans/zaman tepkisi ölçümü"** olarak
yazar. Basitçe: sistemin gerçekte nasıl davrandığını **ölçen** düğüm — frekans
tepki eğrisi, bozulma (THD), gecikme, seviye. `index.md` bunu "ayna karşısında
saç taramak — iddia değil, ölçüm" diye çağırır. measurement, K004'ün
kabul-kriteri kapısıdır: SNR/THD eşikleri buradan karşılaştırılır.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Frekans tepkisi ölçümü (frequency response) | EQ düzeltme hedefi hesabı | equalizer |
| 2 | Bozulma ölçümü (THD / THD+N) | Limiter/DSP bozulma üretimi | limiter · compressor |
| 3 | Gecikme (latency) ölçümü | Pipeline latency bütçesi tahmini | pipeline |
| 4 | Seviye/loudness ölçümü (R128 vb.) | Ölçüm standardı seçimi | testing |
| 5 | Ölçüm eşikleriyle karşılaştırma (kabul kriteri: SNR > 100 dB · THD+N < 0.01% @ 1 kHz) | Eşiklerin belirlenmesi (kaynak: AGENTS.md §5) | dsp-master §4 T7 |
| — | *(dışında)* ölçüm araç zinciri + rapor formatı | — | **UNKNOWN — R1** |

**Yasak:** measurement yalnız ölçer ve karşılaştırır; düzeltme uygulamaz (EQ'nun
işi). Ölçüm eşikleri (T7) [KANIT]'tır; araç zinciri/rapor formatı kanıtsız yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | measurement ↔ testing (gerçek ölçüm ↔ test senaryosu) | ⚠️ VERIFICATION REQUIRED | dsp-master §7 Q4 |
| A2 | measurement ↔ equalizer (ölçüm ↔ düzeltme hedefi) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | ölçüm araç zinciri + rapor formatı | **UNKNOWN** — tahmin yasak | measurement/index.md open_questions |
| A4 | loudness standardı (R128/ITU vs başka) | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Frekans/zaman tepkisi ölçümü" | `dsp-master.md` §3 satır 11 | ✅ dosya okundu |
| 2 | "ölçüm araç zinciri ve rapor formatı yok" açık soru | `measurement/index.md` open_questions | ✅ dosya okundu |
| 3 | Ölçüm eşikleri: SNR > 100 dB · THD+N < 0.01% @ 1 kHz [KANIT] | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 satır 50 | ✅ dosya okundu |
| 4 | PHP `analyzeHardware()` snr/thd alanları üretir ama placeholder | `shared/src/AI/AIEngine.php` satır 138–155 | ✅ dosya okundu |
| 5 | DSPark `SpectrumAnalyzer`, `LoudnessMeter`, EBU R128 metering | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | DSPark `DelayEstimator`, `OnsetDetector`, `PitchDetector` analiz ailesi | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 7 | Signalsmith-Audio/basics `Analyser` (spectrumHz / spectrumEnergy) | https://github.com/Signalsmith-Audio/basics | ✅ 2026-10-12 doğrulandı |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Ölçüm araç zinciri + rapor formatı? | **UNKNOWN** |
| R2 | measurement ↔ testing / equalizer sınırı (Q4/A2)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Loudness standardı seçimi (R128/ITU)? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 araç-zinciri + R3 standart kapanmadan bu
dosyada ölçüm-iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3). Eşikler (§4/3) ise
[KANIT]'tır ve bağlayıcıdır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
