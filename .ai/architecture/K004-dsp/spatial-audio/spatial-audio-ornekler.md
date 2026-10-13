---
title: "spatial-audio — Örnekler"
type: architecture-examples
node_id: K004-dsp/spatial-audio
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# spatial-audio — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında spatial/HRTF/pan kodu bulunmamaktadır
(grep negatif — dogrulama §1/3). Uydurma kod yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — v1.8.0 notes, 2026-10-12 ✅)

```
AlgorithmicReverb rebuilt as a true-stereo 32-line FDN (16 in Eco)
with a binaural early field and frequency-dependent decay control.
```

**Ne gösterdiği:** "binaural early field" — kulaklıkta konum hissi için hazır
erken-refleks alanı. `spatial-audio` dalının reverb'le kesişen sınırına (A1) doğrudan
referans; format seçimi (R1) için bir başlangıç noktasıdır, henüz bağlayıcı değil.

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Time, pitch, and spatial processing | Delay, AlgorithmicReverb, Reverb,
    PitchShifter, TimeStretch, PitchCorrector, GranularProcessor
```

**Ne gösterdiği:** DSPark'ın spatial ailesi — `PitchShifter`/`TimeStretch`
pitch-işlem tarafını, `AlgorithmicReverb`/`Reverb` konum-erken-alanını taşır.
HRTF sınıfı doğrudan listelenmiyor (R3 HRTF kaynağı için ayrı referans gerekir).

## §3 Uygulama notu

CoreMusic `spatial-audio` dalı format seçimi (R1) + HRTF veri kaynağı (R3)
kapanmadan kodlanmaz. Mevcut DSPark "binaural early field" referansı yalnız
erken-alan içindir; tam binaural/HRTF için ek kütüphane-araştırması gerekir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[spatial-audio-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[spatial-audio-dogrulama]]` | iddia kanıtları |
| `../reverb/reverb-kapsam.md` | sınır komşusu (oda hissi ↔ konum) |
| `../delay/delay-kapsam.md` | sınır komşusu (basit gecikme ↔ yerleştirme) |
| `../pipeline/pipeline-kapsam.md` | kaynak konum girdisi (pipeline) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
