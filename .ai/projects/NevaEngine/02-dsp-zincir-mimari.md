---
title: "Neva Engine — DSP Zincir Mimari (K3)"
type: doc
category: audio-engine
version: 1.0.0
status: active
authority: "SSOT: .ai/CLAUDE.md §19 · skill: audio-engine-cpp references/dsp-chain-map.md"
updated: 2026-10-14
---

# Neva Engine — DSP Zincir Mimari (K3)

**Zorunlu Bağlantılar:** [[00-sdk-lib-envanter]] · [[01-gelistirme-plani]] · [[brain.md]] §7
**Skill referansı:** `.claude/skills/audio-engine-cpp/references/dsp-chain-map.md` (SSOT — bu dosya tekrar etmez, genişletir)

## §1 Genişletilmiş Sinyal Zinciri

```
[K2 I/O: ASIO/WASAPI] Float32 48kHz
    ↓
1. Input Gain (lock-free atomic, ±24dB)
    ↓
2. 31-Band Parametric EQ  — TPT biquad (JUCE dsp IIR); band: 20Hz…20kHz, Q 0.3…18, ±12dB
    ↓
3. Compressor (feed-forward, dual-mono/stereo-link)  — Airwindows referans

#### §1.1 Rakip kompresör param seti — VoiceMeeter StripComp (04 §7.3 kanıtlı, işlendi 2026-10-15)

| VP StripComp alanı (XML, format) | Neva karşılığı | Durum |
|---|---|---|
| `gainin` (%0.2f) | Input trim (kompresör öncesi) | ✅ karşılık var |
| `attack` (%0.1f) | Attack (ms) | ✅ |
| `release` (%0.1f) | Release (ms) | ✅ |
| `knee` (%0.2f) | Soft knee (dB) | ✅ |
| `comprate` (%0.3f) | Ratio (:1) — VP adı "compression rate" | ✅ isim farkı |
| `threshold` (%0.2f) | Threshold (dB) | ✅ |
| `automakeup` (%i) | Auto-makeup flag | ✅ |
| `gainout` (%0.2f) | Output trim (kompresör sonrası) | ✅ |

**Sonuç:** VP param seti Neva hedefiyle **8/8 birebir örtüşüyor**; ek farklar: VP'de
ayrı `Limit` yüzeyi var (R10 limiter ayrık modül — karşılaştırma 04 §2.2/§7.3).
VP'nin iç matematiği (knee eğrisi/azaltma katsayısı) ⚠️ `VERIFICATION REQUIRED`
(04 §7.7.4 — opsiyonel Faz 6+ decompile).
    ↓
4. Insert FX (4 reverb modu: Geniş Konser / Düğün Salonu / Oda / Stüdyo)
    ↓
5. Crossover LR4 @80Hz → 8.1 matris (bass management, §9 brain)
    ↓
6. True-Peak Limiter (4x oversample, ceiling -0.1dBTP)  — BS.1770-4 TP ölçüm
    ↓
7. Maximizer (lookahead gain + psychoacoustic)  — Airwindows maximize referans
    ↓
8. Output Matrix 1.0→8.1 → [K2 I/O]
    ↓ (parallel tap)
Metering Bus: LUFS (libebur128) · RTA (juce::dsp::FFT) · Peak/RMS
```

## §2 Katman-Modül Eşlemesi

| # | Modül | Kütüphane | RT-Safe (zero-alloc) |
|---|-------|-----------|----------------------|
| 2 | EQ 31-band | JUCE 9 dsp (`juce::dsp::IIR::Filter`, TPT) | ✅ prepare()'da tahsis |
| 3 | Compressor | Özel + Airwindows (MIT) referans | ✅ |
| 4 | Reverb 4 mod | JUCE Reverb (Freeverb) + delay line preset'leri | ✅ buffer prepare'da |
| 5 | Crossover | JUCE IIR LR4 (Linkwitz-Riley 4. nesil) | ✅ |
| 6 | TP Limiter | Özel (4x oversample FIR + envelope) | ✅ |
| 7 | Maximizer | Airwindows (MIT) referans algoritma | ✅ |
| Metering | LUFS | libebur128 (MIT) — analyze çağrısı callback DIŞINDA (kuyruk) | ⚠️ kuyruk üzerinden |
| Metering | RTA | juce::dsp::FFT (JUCE lisans) | ✅ prepare'da plan tahsisi |

## §3 Guardrails (audio-engine-cpp §4 — bağlayıcı)

1. Callback içinde `new`/`delete`/`malloc`/`throw`/mutex **yasak** (H001 RED).
2. Tüm buffer'lar `prepare()` aşamasında `alignas(64)` ile tahsis edilir.
3. Parametre değişimi: `std::atomic<float>` + audio thread'de snapshot; UI→engine lock-free queue.
4. Metering kuyruğu: SPSC lock-free ring buffer (engine → UI 30fps).
5. ASIO exclusive: tek uygulama kilidi — cihaz kaybında WASAPI fallback (ADR-017 · AGENTS §17 #6).

## §4 Ölçüm Kapısı (Definition of Done)

| Metrik | Hedef | Yöntem |
|--------|-------|--------|
| THD+N | <0.005% @1kHz | Loopback ölçüm (K1 DAC) + Google Test sine analiz |
| SNR | >105dB | -60dBFS dither testi |
| True Peak overshoot | 0 | BS.1770-4 4x TP ölçüm (libebur128) |
| Gecikme | ASIO <10ms · WASAPI <20ms | Roundtrip loopback |
| Coverage | ≥80% min / ≥90% hedef | Google Test |

**Authority:** Embedded Engineer (L1) · DSP Firmware Engineer (L1)
