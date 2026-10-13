---
title: "reverb — Kapsam"
type: architecture-scope
node_id: K004-dsp/reverb
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# reverb — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 7 bu dalı **"Yansıma/yankı taklidi — oda hissi"** olarak
yazar. Basitçe: sesin bir odada nasıl yankılanıp dağıldığını taklit eden katman —
kuru sese "salon" veya "stüdyo" hissi ekler. `index.md` bunu "stüdyo kaydı ile
salon kaydı arasındaki fark" diye çağırır. reverb, K004'ün uzamsal/uzun-kuyruk
işlemlerindendir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Yansıma/yankı taklidi algoritması (FDN, convolution, mode) | Basit gecikme kopyası | delay |
| 2 | Decay / room-size / damping parametreleri | Konum/yerleştirme (HRTF, binaural) | spatial-audio |
| 3 | Oda modeli referansı (tip: spring/plate/hall) | Oda ölçüm verisi (IR) | measurement |
| 4 | Mix (kuru/ıslak) oranı | — | — |
| — | *(dışında)* oda modeli seçimi/ölçüm verisi | — | **UNKNOWN — R1** |

**Yasak:** spatial-audio ile sınır — reverb odanın "hissi", spatial konumun
"yönü"; delay ile sınır — reverb çoklu/dağıtık yansıma, delay tek kopya.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | reverb ↔ delay (dağıtık yansıma ↔ tek kopya) | ⚠️ VERIFICATION REQUIRED | dsp-master §3 · §7 |
| A2 | reverb ↔ spatial-audio (oda hissi ↔ konum) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | oda modeli seçimi + ölçüm (impulse response) verisi | **UNKNOWN** — tahmin yasak | reverb/index.md open_questions |
| A4 | algoritma: FDN ↔ convolution ↔ analog-modeling | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Yansıma/yankı taklidi — oda hissi" | `dsp-master.md` §3 satır 7 | ✅ dosya okundu |
| 2 | "oda modeli seçimi/ölçüm verisi yok" açık soru | `reverb/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic PHP katmanında reverb parametresi geçiyor (roomProfile) | `shared/src/AI/AIEngine.php` satır 114 · `AIWorkflow.php` satır 159 | ✅ dosya okundu |
| 4 | DSPark `AlgorithmicReverb` (true-stereo 32-line FDN, binaural early field) | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 5 | Patina `ReverbEngine` (Spring & plate, `p.decay`, `p.mix`) | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 6 | Signalsmith-Audio/basics `Reverb` başlığı | https://github.com/Signalsmith-Audio/basics | ✅ 2026-10-12 doğrulandı |
| 7 | JUCE `juce_dsp` reverb içerir (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | ⚠️ — modül ✅, içerik UNKNOWN |

> Not: satır 3 — PHP reverb bir oda-düzeltme tahminidir, gerçek reverb motoru
> değildir (C++ motoru henüz yok — pipeline-ornekler §1).

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Oda modeli seçimi ve ölçüm (IR) verisi nereden? | **UNKNOWN** |
| R2 | reverb ↔ delay / spatial-audio sınırı (A1/A2)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Algoritma ailesi (FDN/convolution/analog)? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 oda-verisi gelmeden bu dosyada model seçimi
iddiası yazılmaz (v2 §3 KURAL 3).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
