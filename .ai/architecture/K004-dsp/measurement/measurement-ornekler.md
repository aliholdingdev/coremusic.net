---
title: "measurement — Örnekler"
type: architecture-examples
node_id: K004-dsp/measurement
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# measurement — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: PHP katmanında ölçüm-alanı var, gerçek ölçüm yok.** `analyzeHardware()`
snr/thd alanlarını döndürür ama değerler placeholder (`0.0`) — gerçek ölçüm
yapılmaz (C++ ölçüm motoru henüz yok — dogrulama §1/6):

```php
// shared/src/AI/AIEngine.php satır 141-155
$status = 'healthy';
$snr = 0.0;
$thd = 0.0;
$temperature = 0.0;
// Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)
// Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak
return [
    'status' => $status, 'snr' => $snr, 'thd' => $thd,
    'temperature' => $temperature, 'recommendations' => $recommendations,
];
```

**Ne gösterdiği:** CoreMusic'in snr/thd **alan adlarını** hazır tuttuğunu; ölçümün
henüz C++ tarafına bağlanmadığını. K004 measurement dalı bu alan adlarını
koruyabilir, ancak doldurma kaynağı gerçek ölçüm motoru olacaktır.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Analysis and metering | SpectrumAnalyzer, LoudnessMeter, AudioIntervalAnalyzer,
    LoudnessNormalizer, PitchDetector, OnsetDetector, BeatTracker, LoopFinder,
    DelayEstimator
```

**Ne gösterdiği:** hazır ölçüm/metering ailesi — spectrum, loudness (EBU R128),
gecikme-tahmini (`DelayEstimator`). CoreMusic `measurement` dalı için doğrudan
araç referansı (R1 araç-zinciri için aday).

**Kaynak:** https://github.com/Signalsmith-Audio/basics (README — Analyser, 2026-10-12 ✅)

```cpp
signalsmith::basics::AnalyserFloat analyser;
// spectrum data: .spectrumHz() and .spectrumEnergy(channel)
// thread-safe meter: .wantsMeters(bool) / .hasMeters()
```

**Ne gösterdiği:** spectrum ölçümünün ana thread/UI thread'e güvenli aktarım
deseni (`.wantsMeters`/`.hasMeters`) — realtime ölçüm okuması için referans.

## §3 Uygulama notu

CoreMusic `measurement` dalı DSPark analiz ailesini temel alır; snr/thd alan adları
korunur ama placeholder `0.0` yerine gerçek ölçüm motorundan beslenir. Kabul
eşikleri (SNR > 100 dB · THD+N < 0.01%) [KANIT]tır ve bu ölçümlerle karşılaştırılır.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[measurement-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[measurement-dogrulama]]` | iddia kanıtları |
| `../testing/testing-kapsam.md` | test senaryosu sınırı (Q4) |
| `../equalizer/equalizer-kapsam.md` | düzeltme hedefi sınır komşusu (A2) |
| `../dsp-master.md` | §4 T7 eşikleri [KANIT] |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
