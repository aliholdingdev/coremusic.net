---
title: "spatial-audio — Kapsam"
type: architecture-scope
node_id: K004-dsp/spatial-audio
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# spatial-audio — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 10 bu dalı **"Konum/hissiyat katmanı"** olarak yazar.
Basitçe: sesin "etrafımızda"/"soldan arkadan" gibi duyulacak biçimde yerleştirilmesi
— kulaklıkta derinlik yaratma. `index.md` bunu "hangi sesin nereden geldiği hissi"
diye çağırır. spatial-audio, K004'ün en az olgunlaşmış, format-seçimi bekleyen
dalıdır (binaural vb. seçilmedi).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Konum/hissiyat katmanı (binaural / Ambisonics / stereo genişletme) | Oda "hissi" (yansıma yığını) | reverb |
| 2 | Kafa-bağımlı dönüşüm referansı (HRTF — tip seçimi) | Basit gecikme kopyası | delay |
| 3 | Stereo görüntü (width/pan) yerleştirme | Hoparlör fiziksel yerleşimi | K001-donanım |
| 4 | Kaynak konum girdisi (hangi ses nerede) | Kaynakların kendi miksi | pipeline |
| — | *(dışında)* hedef format (binaural vb.) | — | **UNKNOWN — R1** |

**Yasak:** reverb/delay ile sınır — spatial konumun "yönü", reverb odanın "hissi",
delay tek kopya. HRTF veri seti gelmeden tahmin yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | spatial-audio ↔ reverb (konum ↔ oda hissi) | ⚠️ VERIFICATION REQUIRED | dsp-master §3 · §7 |
| A2 | spatial-audio ↔ delay (yerleştirme ↔ basit gecikme) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | hedef format: binaural / Ambisonics / stereo-genişletme | **UNKNOWN** — tahmin yasak | spatial-audio/index.md open_questions |
| A4 | HRTF veri seti + kafa-takibi kaynağı | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Konum/hissiyat katmanı" | `dsp-master.md` §3 satır 10 | ✅ dosya okundu |
| 2 | "hedef format (binaural vb.) seçilmedi" açık soru | `spatial-audio/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic `shared/src/` içinde spatial/HRTF kodu yok (negatif) | `shared/src/` grep | ✅ 2026-10-12 doğrulandı |
| 4 | DSPark `AlgorithmicReverb` "binaural early field" içerir | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 5 | DSPark `PitchShifter`, `TimeStretch` yan aile (spatial time/pitch) | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | JUCE `juce_dsp` spesifik spatial modül var (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | ⚠️ — modül ✅, içerik UNKNOWN |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Hedef format seçilecek (binaural / Ambisonics / stereo)? | **UNKNOWN** |
| R2 | spatial ↔ reverb / delay sınırı (A1/A2)? | ⚠️ VERIFICATION REQUIRED |
| R3 | HRTF veri seti + kafa-takibi kaynağı? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 format kararı + R3 HRTF verisi kapanmadan bu
dosyada spatial iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3 · zero-hallucination).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
