---
title: "DSP Engine — Dizin"
type: dsp-index
category: dsp-engine
date: 2026-08-01
status: active
version: 1.2.0
authority: ADR-017, ADR-025, ADR-062
governance: 15
updated: 2026-10-13
---

# DSP Engine — Dizin

> **Amaç**: CoreMusic Neva Engine DSP katmanının SSOT indeksi.
> **Kapsam**: Sinyal işleme zinciri, bileşen dosyaları, ölçüm/disiplin kuralları.
> **Kapsam dışı**: FPGA/firmware ayrıdır (`[[../firmware/index|firmware index]]`), amplifier ayrıdır (`[[../amplifier/index|amplifier index]]`).

## 1. Sinyal Akışı (ADR-062 Pipeline — SSOT)

ADR-062 §2(a)'nın **9 aşamalı bağlayıcı zinciri** (değişimi = yeni ADR):

```text
1 Giriş (Input Gain + kanal eşlemesi)
→ 2 Ön-hazırlık (format + örnek hızı dönüşümü)
→ 3 EQ — Grafik (31-band ISO 1/3-oktav, ADR-025-b)
→ 4 EQ — Parametrik (≥2 bant, ADR-025-c)        [grafik → parametrik SERİ, paralel yok]
→ 5 Dynamics (compressor → limiter/expander)
→ 6 Efekt (reverb → chorus/delay → surround decode)
→ 7 Mix / Route (bus + routing matrix)
→ 8 Master (Master EQ ⚠️ eşleme belirsiz + toplam gain)
→ 9 Çıkış (dithering → output gain → format output)
```

| Aşama | Dosya | Gecikme katkısı |
|---|---|---|
| 1–2 Giriş/Ön-hazırlık | — (dönüşüm) | SR dönüşümü varsa (ölçülür) |
| 3–4 EQ (grafik + parametrik) | [[equalizer]] | kaskad blok gecikmesi eklemez; faz/grup gecikmesi (ADR-025 §2.2c) |
| 5 Dynamics | [[dynamics]] | limiter lookahead (1–5 ms) |
| 6 Efekt | [[effects]] | wet yolu (dry 0) |
| 7 Mix/Route | [[dsp-pipeline]] §2 | 0 (yönlendirme) |
| 8 Master | [[equalizer]] (master EQ ⚠️) | belirsiz eşleme — ADR-062 §2(a)/5 |
| 9 Çıkış | — (dither/format) | 0 |

**Kurallar (ADR-062 §2(a)):** sıra **bağlayıcıdır**, bypass aşamayı atlar sırayı bozmaz, yeniden sıralama **yeni ADR** ister · iki EQ **seri** (grafik → parametrik), paralel şube yok · her aşamanın bypass'ı + `getLatency()` vardır; toplam latency = aşama latency'leri toplamı (iç hedef `<0.1ms`, kapı ADR-006: `<10ms ASIO / <20ms WASAPI`).

**⚠️ Crossover/Loudness eşlemesi:** `[[crossover]]` ve `[[loudness]]` ADR-062'nin 9 aşamasında ayrı aşama olarak geçmez; `[[dsp-pipeline]]` şemasında satırları vardır. İki bakış arasındaki eşleme **kanıtlanmamıştır** → `⚠️ VERIFICATION REQUIRED`. Mutabakata kadar ADR-062 sırası (grafik → parametrik → dynamics → efekt) geçerlidir.

## 2. Bileşen Dosyaları

[[filters]] (biquad/butterworth/RBJ/faz — Q, cutoff, SR) · [[equalizer]] (parametrik EQ, ADR-025 — 31 bant, ±12 dB, Q 0.5–3.0) · [[crossover]] (2/3/4-way LR, ALLPASS — 80/3500 Hz, 24 dB/oct) · [[dynamics]] (compressor, limiter — attack/release/ratio/threshold) · [[effects]] (reverb, delay, chorus, convolver — RT60, wet/dry, feedback) · [[loudness]] (BS.1770-4 / EBU R128 — −23 LUFS, −1 dBTP) · [[dsp-pipeline]] (blok işleme, zero-alloc — 512 örnek, 48 kHz).

## 3. Donanım Eşlemesi (Neva Engine)

**STM32H743** ana DSP MCU (Cortex-M7 @ 480 MHz, [[../hardware/neva-engine|neva-engine]]) · **PCM3168A** 8-ch 24-bit DAC (harici I2S — **yalnız PCM3168A, ADR-038 H001**) · **SAK-06409** barrel jack (güç) · **SRV-06406** 3.5 mm AUX (analog bypass) · **TES-06405** toroid trafo (230V/2×9V).

**Güç**: AUX ↔ PCM3168A ön hizalama (`[[../hardware/amp-prealignment|prealignment]]`). **Sermaye**: ADR-004 (analog sınıf AB) + ADR-005 (BPA0350) → 34.837 ₺ bütçe. ⚠️ VERIFICATION REQUIRED — `.ai/.decisions/accepted/` dosya adları bu eşlemeyi desteklemiyor (ADR-004 = multi-domain-spa, ADR-005 = ultrathink-protocol); doğrulanana kadar kanıt sayılmaz (bkz. `amplifier-architecture` §16.3).

## 4. EQ — ADR-025 (Çift EQ, Bağlayıcı)

İki ayrı EQ **seri** (paralel yok): 31-band **grafik** (1/3-oktav ISO, 20 Hz → 20 kHz, aktif bant 2–31, peaking mid + shelf uçlar, Q ≈4.3–4.4) + ≥2 bant **parametrik** (serbest frekans, Q 0.3–10). Kazanç **±12 dB**. Sıra grafik → parametrik (ADR-062 aşama 3 → 4); her kanal bağımsız (8.1 → 31 × 9 = 279 bant teorik, pratikte master + 2 aux). Ölçüm: sweep 20 Hz–20 kHz, ±0.5 dB ripple. ADR-025 bağlayıcıdır; parametre değişikliği yalnız ADR-025 üzerinden yapılır.

## 5. Crossover, Dinamikler, Loudness (özet)

- **Crossover**: LR topolojisi (2/3/4-way, 24 dB/oct; kutup-sıfır iptaliyle düz faz), ortak noktalar 80 Hz (LF/MF) · 3500 Hz (MF/HF) — amplifier bom'u ile hizalı.
- **Dinamikler**: compressor 2:1–4:1 (attack 1–30 ms, release 50–500 ms, soft-knee) · limiter lookahead brickwall, **true-peak −1 dBTP** · reverb FDN/konvolüsyon (RT60 0.3–2.0 s) · delay tempo-sync, feedback < 1.0.
- **Loudness**: **−23 LUFS** (EBU R128) · **−1 dBTP** · K-weighting (high-shelf + RLB high-pass) · gate −70 LUFS absolute + −10 LU relative.

## 6. Performans Bütçesi (ADR-062 / ADR-017)

Sample rate **48 kHz** · blok **512 örnek** (10.67 ms @ 48k) · bütçe: callback süresi ≤ 10.67 ms (spike > %80 → kırmızı) · örnek format **float32** (ADR-017) · Zenon kaydı 31 Haziran 2026 (governance §16).

## 7. Zero-Allocation Kuralları (C++20)

1. `new`/`malloc`/`delete`/dinamik `push_back` → **SES CALLBACK'İNDE YASAK** · `std::mutex`/`lock_guard`/`unique_lock` → **SES THREAD'İNDE YASAK**
2. Tüm delay/EQ/rev buffer'ları **init'te allocate** edilir; işlem state'i **tek çizgi atomics** ile paylaşılır (`std::atomic<float>` release/load)
3. Callback `noexcept` · `alignas(64)` cache-line hizası · `constexpr` cutoff/frequency tablo hesabı derleme zamanında
4. **Doğrulama**: static analysis (clang-tidy cppcoreguidelines-*) + runtime guard (allocation counter hook)

## 8. Ölçüm Kapıları ve Test Senaryoları

Kabul kapıları: EQ ripple **±0.5 dB** @ sweep (pipeline kabul) · crossover sum faz **0° @ fc** (LR, crossover kabul) · THD+N **< 0.01% @ 1 kHz** (donanım + DSP) · loudness sapma **±1 LU** (mastering gate) · true-peak **≤ −1 dBTP** (release gate) · xrun (underrun) **0** ve callback spike **≤ %80 budget** (soak 60 dk).

Test senaryoları: sine sweep EQ bypass↔aktif (ripple ≤ ±0.5 dB) · fc tone sum (faz 0°) · −1 dBTP sine → limiter (overshoot yok) · pink noise → R128 (−23 ±1 LUFS) · 60 dk soak (0 xrun) · mute/unmute (click yok, ramp 5 ms).

## 9. Kapılar (Gate) — Ne Zaman DUR

Sinyal akışı sırası ADR-062 ile uyuşmuyorsa → DUR · EQ parametreleri ADR-025'ten sapıyorsa → DUR · DAC PCM3168A değilse → DUR (ADR-038 H001) · ölçümü olmayan iddia → DUR · yeni efekt bloğu → gecikme bütçesi yeniden ölçülür.

**Çapraz referans:** `[[../index|electronic kök]]` · `[[../hardware/index|hardware]]` · `[[../firmware/index|firmware]]` · `[[../amplifier/index|amplifier]]` · `[[measurements]]` · `[[bom]]` · `[[debugging]]` · `.decisions/` (ADR-017, ADR-025, ADR-062, ADR-038). Soru→dosya haritası ve öncelikli uygulama sırası (PLANNED — kod yok): [[dsp-pipeline]] §2/§4/§7 · [[equalizer]] §4.1 · [[crossover]] §6 · [[filters]] §5/§9.3 · [[dynamics]] §2 · [[effects]] §2–§5 · [[loudness]] §3.

**Dürüstlük notu**: Repo'da DSP C++ kodu YOK — kaynak: `.ai/electronic/**` pipeline belgeleri (ADR-062) + BS.1770/EBU R128 standartları. Kod iddiası olmadan yalnız mimari/ölçüm disiplini yazıldı. **Sıkıştırma notu (2026-10-13):** 298 satırlık dizin 40–100 bandına sıkıştırıldı; sözlük, test tablosu detayları ve uygulama haritası tablo biçimi bilgi olarak alt içerik dosyalarında (§1'ler) mevcuttur.

> **Authority:** ADR-017, ADR-025, ADR-062 · **governance:** 15 · **güncelleme:** 2026-10-13
