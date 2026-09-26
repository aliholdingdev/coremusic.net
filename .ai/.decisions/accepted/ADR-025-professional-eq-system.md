---
title: "CoreMusic — ADR-025: Professional EQ System (31-Band Grafik EQ — ISO 1/3-Oktav, Seçilebilir Aktif Bant 2-31 + Ayrı Parametrik EQ ≥2 Bant · RT-Güvenli Biquad Cascade · Denormal + Headroom Koruması · Bant-Başına Bypass + Preset)"
type: adr
category: audio
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-025 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-025: Professional EQ System (31-Band Grafik EQ — ISO 1/3-Oktav, Seçilebilir Aktif Bant 2-31 + Ayrı Parametrik EQ ≥2 Bant · RT-Güvenli Biquad Cascade · Denormal + Headroom Koruması · Bant-Başına Bypass + Preset)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-025'i sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı: **(a) çift EQ** — 31-band grafik EQ (aktif bant 2–31 seçilebilir) + ayrı parametric EQ (≥2 bant) · **(b) sıra grafik → parametric** (tersine konfigürasyon notu ile) · **(c) RT-güvenli biquad cascade** (float32, callback'te tahsisat yok, DF2T, denormal/FTZ, bant başına CPU bütçesi, faz/toplam-gain notu) · **(d) bant başına bypass + preset kaydı** · **(e) §1.3 web araştırması 9 alan** — debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL; §5.3)**, Tech Lead **✅ (§7)**)
**İlgili ADR'ler:** [[ADR-017-dsp-hardware-mode]] (hard-RT kısıtları — callback tahsisat yasağı, buffer/latency bütçesi, xrun politikası bu kararın **zorunlu** girdisidir; `dsp-chain.md:50-56` `noexcept` arayüzü oraya bağlanır — dosya diskte VAR ✅) · [[ADR-019-per-os-neva-player]] (ortak çekirdek + kill-switch/fallback zinciri — EQ bu çekirdeğin bir stage'i, kill-switch EQ'yu da bypass eder — dosya diskte VAR ✅) · [[ADR-018-footer-player-vaporwave]] (player UI — EQ UI hook'u ve WCAG/reduced-motion bütçesi — dosya diskte VAR ✅) · [[ADR-001-vanilla-js-itcss]] (web katmanı sınırı — EQ UI web'de, DSP çekirdekte; tarayıcı RT yoluna girmez — dosya diskte VAR ✅) · [[ADR-006-performance-targets]] (latency/CWV hedefleri — EQ stage'inin ölçüleceği kapı — dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı — bu ADR'deki her iddia dosya + satır ile etiketli — dosya diskte VAR ✅) · [[ADR-024-ecosystem-modular-docs]] (wiki-link disk kanıtı kuralı + şablon zorunluluğu — bu yazımın usulü — dosya diskte VAR ✅) · karar dizini [[../index]] **satır 62** `[[ADR-025-professional-eq-system]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-025-professional-eq-system` karar dizini `../index.md:62`'de **rezerve boş slottur** (ADR-019-024 aynı istisnayı kaydetmişti). Dikkat: `.ai/architecture/adr/ADR-025-k8-2-k15-siniri.md` **farklı bir numara serisidir** (mimari katman ADR'leri) ve o dosya `:21`/`:196`'da bu numara paylaşımını kendisi beyan eder — iki ayrı dizin, kozmik çakışma değil; birleştirme istenirse üst karar gerekir (`⚠️ VERIFICATION REQUIRED`).

---

## 1. Bağlam (Context)

CoreMusic'in ses motoru (Neva Engine) **çoktan tasarlanmış ama hiç yazılmamış** bir EQ'ya ihtiyaç duyuyor: vault'ta 31-band EQ'nun bant tablosu, biquad katsayısı, preset listesi ve performans hedefleri **hazır** (`eq-parametric.md`, `dsp-chain.md`), web akışında_equalizer ekranı ve preset listesi **hazır** (`03-equalizer.md`), karar dizininde ADR-025 slotu **ayrılmış** (`index.md:62`) — ama repo'da **tek bir `*.cpp`/`*.h` dosyası bile yok** (§1.1-D). Üstelik vault içinde EQ'nun **tanımı bile ikiye bölünmüş**: K3 "31-band parametrik EQ" diyor (`eq-parametric.md:12`), K10 "10-bant grafik equalizer" diyor (`music-panel.md:135`), UI akışı "Desktop 31-band / TV 10-band / Car-Watch preset-only" diyor (`03-equalizer.md:143-148`), `brain.md:980` "ADR-025 | 31-band parametrik EQ" diyor. Hangi EQ'nun yapılacağı, hangi sırada, kaç bantla, RT kurallarıyla ve kimin onaylayacağı **hiçbir tek belgede yok**. Bu ADR o kararı sabitler: **çift EQ (grafik + parametric) tek karar altında**, hard-RT bağlamı [ADR-017](ADR-017-dsp-hardware-mode) ile kenetlenerek.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) VAULT SPEC KATMANI — IMPLEMENTED (doküman var):**

- **`.ai/architecture/k3-ses-motoru/dsp-chain.md` (EQ zincirde VAR — 3 yerde):**
  - `:12` — "15 aşamalı … pipeline", **"Biquad katsayıları, ring buffer'lar ve lock-free yapılar"**.
  - `:50-56` — stage arayüzü `virtual void process(...) noexcept` / `setParameter(...) const noexcept` / `getName() const noexcept` (hard-RT imzası).
  - `:24-30` ve `:32-35` — zincir şeması: **[5] EQ Parametric** (aşama 5) ve **[11] Master EQ** (aşama 11); ASCII şemada `[5]` ve `[10]` etiketleri iki satırda tekrar ediyor (**indeks kayması — §5.1/9**).
  - `:169` — `#### 5. Parametric EQ Stage`; `:174` — `static const uint32_t MAX_BANDS = 31;`; `:220-238` — peaking biquad katsayısı (`A = 10^(gain/40)`, `alpha = sin(w0)/(2Q)`) = **RBJ Cookbook peaking formülüyle aynı** (§1.3-2).
  - `:373-381` — Performans hedefleri: 15 aşama CPU **< %8 ("Gerçek" 6.5%)**, pipeline latency **< 0.1ms** — doküman hedefi, **kod ölçümü yok** → `⚠️ VERIFICATION REQUIRED`.
- **`.ai/architecture/k3-ses-motoru/eq-parametric.md` (EQ spec dosyası VAR):**
  - `:12` — "**31 bantlı parametrik EQ**"; `:19-31` — bant diyagramı "±12dB kazanç, **Q: 0.5-10**".
  - `:117-122` — **31 sabit frekans (ISO 1/3-oktav):** 20 · 25 · 31.5 · 40 · 50 · 63 · 80 · 100 · 125 · 160 · 200 · 250 · 315 · 400 · 500 · 630 · 800 · 1000 · 1250 · 1600 · 2000 · 2500 · 3150 · 4000 · 5000 · 6300 · 8000 · 10000 · 12500 · 16000 · 20000.
  - `:36-47` — `BiquadType` enum (LowPass/HighPass/BandPass/Notch/**Peak**/LowShelf/HighShelf/AllPass); `:66-72` — Peak katsayısı (RBJ).
  - `:258-287` — preset seti (Flat/Rock/Jazz/Classical/Vocal); `:295-324` — `EQ31Band` API.
  - `:329-335` — Performans tablosu: "CPU (31 aktif bant) hedef < %2 / **Gerçek 1.5%**", "İşleme Latency < 0.01ms / Gerçek 0.008ms" → **kod 0 iken "Gerçek" sütunu ölçülmüş değildir, spec hedefidir** → `⚠️ VERIFICATION REQUIRED` (ADR-005).
- **`.ai/architecture/k3-ses-motoru/neva-engine-core.md`:** `:12` "C++20 … **real-time safe** ve **lock-free**"; `:329` `config.bufferSize = 256;`; `:208` `BufferPool<256, 1024>`; `:189-196` `allocate()/delocate() noexcept` (havuzdan, RT-güvenli).
- **`.ai/architecture/k3-ses-motoru/CLAUDE.md:44`** — "Input → Gain → **31-Band EQ** → Compressor → Reverb → Limiter → Output"; **`:61`** — "ADR-025 | 31-band parametrik EQ" (K3 katmanı bu ADR'ye **bağlı**).
- **`.ai/brain.md`:** `:217` K3 "Neva Engine, DSP, **EQ**, Crossover"; **`:390`** callback örneği `s = dspChain[ch].processEQ(s);` (§7.2 ASIO örneği); **`:900`** "EQ Bands | **31**"; **`:980`** "**ADR-025** | 31-band parametrik EQ"; `:665` player utility ikonları arasında **EQ**.

**B) ŞABLON / DİZİN KAYITLARI — IMPLEMENTED (slot açık, dosya bugüne kadar YOKTU):**

| Kayıt | Satır | İçerik |
|---|---|---|
| `.ai/.decisions/index.md` | **:62** | `[[ADR-025-professional-eq-system]] \| Professional EQ System (31-band) \| Audio` — **slug eşleşmesi ✅** |
| `.ai/.templates/adr/adr-audio-template.md` | `:165, :171` | "ADR-025 \| 31-band parametrik EQ"; sözlük "ADR-025 hizalı" |
| `.ai/.templates/adr/adr-audio-template.md` | `:177-179` | `eq_bands = 31`, `eq_gain = −12…+12`, **`eq_q = 0.3 … 10`** |
| `.ai/.templates/adr/adr-audio-template.md` | `:314` | gain stage 4 "EQ (31-band) — **toplam boost bütçe aşımı yasak**" |
| `.ai/.templates/adr/adr-index.md` | `:96` | "25 \| ADR-025 \| 31-band parametrik EQ \| audio" |
| `.ai/.templates/other/c-template.md` | `:69, :493` | "31-band EQ → ADR-025 → coefficient tablosu" |
| `.ai/architecture/index.md` | `:98` | "K3 \| Ses Motoru \| … \| **ADR-025**, ADR-062" (`ADR-062` **diskte YOK — 0 dosya** → `⚠️ VERIFICATION REQUIRED`) |
| `.ai/keys.md` / `.ai/index.md` | `:260` / `:642` | "ADR-025 \| professional EQ, 31-band" / kısa biçim link `[[decisions/accepted/ADR-025-…]]` (ADR-024 §1.1-D'deki bilinen 20 linklik "kısa biçim" kalıbı) |

**C) UI / KATMAN KANITLARI — IMPLEMENTED (akış dokümanı, kod YOK):**

- **`.ai/ui-design/flow/settings/03-equalizer.md`:** `:71` Equalizer ekranı `[Preset] [Custom]`; `:74` "**31-BAND PARAMETRIC EQ**"; `:95-110` preset listesi; **`:143-148` cihaz matrisi: Desktop = Full parametric / 31-band, TV = Large sliders / 10-band, Car = Preset only, Watch = Preset only**; `:133` hata durumu "Preset yüklenemedi → Flat"; **`:135`** "**EQ bant sayısı tutarsız → Varsayılan 31-band**".
- **`.ai/architecture/k10-uygulama/music-panel.md:135-150`** — **"10-bant grafik equalizer"**: 32 · 64 · 125 · 250 · 500 · 1k · 2k · 4k · 8k · 16 kHz, her bant −12…+12 dB, 10 preset adı.
- **Çelişki (bu ADR çözer):** K3 "31-band **parametrik**" ↔ K10 "10-bant **grafik**" ↔ UI "31/10 cihaza göre". Üçü de tek gerçekle uyumlu: **31-band'lik ISO ızgarasının oktav alt kümesi = K10'un 10 bandı** (31.5→32, 63→64 yuvarlaması) ve **aktif bant sayısı seçilebilir (2–31)**.

**D) KOD KATMANI — YOK → PLANNED (uydurulmadı):**

| Tarama | Sonuç |
|---|---|
| Repo geneli `*.cpp`, `*.h`, `*.hpp`, `*.c`, `*.cc` (vendor/node_modules hariç) | **0 dosya** |
| Kodda `biquad \| parametriceq \| graphicEq \| eq31 \| peaking` eşleşmesi (`*.cpp/*.h/*.js/*.php`) | **0 eşleşme** |
| `neva_engine*`, `*biquad*` dosya araması (recursive) | **0 dosya** |
| Kökte `xmos/`, `plugin/`, `firmware/`, `dsp/`, `juce/`, `audio/`, `engine/`, `neva/` | **8/8 `Test-Path = False`** (ADR-017 §1.1-B ile aynı bulgu) |
| `.github/workflows/` | **2 dosya** (`ci.yml`, `secret-scan.yml`) — ADR-017 `:208`'deki "0 dosya" iddiası **bugün stale** (ADR-024 §1.1-E) → bu ADR o sayıyı **tekrarlamaz** |
| `.ai/projects/NevaEngine/`, `.ai/electronic/` | **YOK** (ADR-017 §1.1-B ile aynı) |

**Sonuç etiketi:** **IMPLEMENTED:** vault spec (dsp-chain 15 aşama + `noexcept`, eq-parametric 31 bant/katsayı/preset, neva-engine-core bufferSize=256 + lock-free), K3/K10/UI doküman kayıtları, `brain.md` 4 EQ satırı, karar dizini `index.md:62` slotu, 4 şablon/katalog kaydı. **PLANNED:** tüm EQ kodu (repo C++ = 0), bant başına bypass mekanizması, preset kaydetme katmanı, denormal/FTZ uygulaması, CPU ölçümü, EQ UI bileşenleri. **`⚠️ VERIFICATION REQUIRED`:** (i) `eq-parametric.md` "Gerçek 1.5% CPU / 0.008ms" sütunu **kod olmadan ölçülmüş sayılamaz**; (ii) `dsp-chain.md` ASCII indeks kayması (§5.1/9); (iii) `ADR-062` diskinde yok; (iv) K10 10-band ↔ 31-band frekans eşlemesi **hesaplanmadı, tahmin edildi** (§5.1/6'da doğrulanır); (v) `brain.md:900` "EQ Bands 31" ile `index.md:642` kısa biçim linki **bu ADR ile düzeltilmedi** (kapsam §5.1/8).

### 1.2 Sorun Tanımı

1. **EQ'nun tanımı üç ayrı yerde üç farklı:** K3 "31-band parametrik" (`eq-parametric.md:12`), K10 "10-bant grafik" (`music-panel.md:135`), UI "31/10 cihaza göre" (`03-equalizer.md:143-148`) → kullanıcıya tek bir EQ sunulacağı mı, ikisi birden mi belirsiz.
2. **"Grafik" ile "parametrik" aynı şeye karıştırılmış:** K3'ün 31 sabit frekans + sabit bant yapısı **grafik EQ davranışı**; `eq-parametric.md:30`'daki "Q: 0.5-10" ifadesi ise **parametrik Q aralığı** → tek dosyada iki farklı ürün tarif edilmiş.
3. **RT güvenliği dağınık:** tahsisat yasağı `brain.md` §7.1 (ADR-017), xrun/limiter politikası ADR-017, headroom kuralı `adr-audio-template.md:314` → **EQ'ya özel tek karar yok**; denormal (FTZ/DAZ) hiç geçmiyor.
4. **Sıra ve bypass yok:** grafik → parametric mi, tersi mi; bant başına bypass ve preset kaydı nereye bağlanır — yazılı değil.
5. **Ölçüm yok:** CPU/latency hedefleri spec'te var, kod 0 → "1.5% CPU" denetlenemez durumda.
6. **Numara/seri karmaşası:** `.ai/architecture/adr/ADR-025-k8-2-k15-siniri.md` aynı numarayı farklı seride kullanıyor → linklerin tam yol yazılması şart.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/anahtar kaynak önce (W3C Audio EQ Cookbook, EBU tech.ebu.ch, Rane Note, Intel/JUCE), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) professional graphic EQ tasarımı 2025-26 (1/3 oktav ISO), (b) biquad/peaking filter matematiği (RBJ cookbook), (c) RTA/analyzer, (d) EBU R128 / BS.1770 loudness, (e) denormal RT audio.** Erişim: **7 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "1/3 octave graphic equalizer 31 bands ISO 266 center frequencies 20 Hz 20 kHz professional audio standard" · (2) "RBJ Audio EQ Cookbook peaking filter biquad coefficients direct form II transposed implementation" · (3) "denormal numbers real-time audio thread performance spike flush-to-zero denormal protection DSP" · (4) "EBU R128 BS.1770 loudness normalization playback chain gain stage headroom true peak limiter" · (5) "graphic equalizer versus parametric EQ live sound pro audio 2025 why fixed Q 1/3 octave filter distortion" · (6) "real-time spectrum analyzer RTA FFT overlap smoothing latency audio plugin design 2025" · (7) "cascaded biquad filters CPU cost per filter audio thread group delay phase distortion equalizer gain staging clipping headroom" |
| Web Search **Konusu** | (1) 31 bant / ISO merkez frekans / ±12 dB grafik EQ standardı; (2) RBJ peaking katsayısı + DF2T (transposed direct form II) uygulaması; (3) denormal/subnormal kaynaklı gerçek-zamanlı ses kilitlenmesi ve FTZ/DAZ koruması; (4) EBU R128 / ITU-R BS.1770 loudness + true peak + zincir headroom'u; (5) grafik vs parametrik EQ farkı, sabit-Q (constant-Q) ve faz/distorsiyon tartışması; (6) FFT tabanlı RTA tasarımı (overlap, smoothing, plugin CPU); (7) kaskad biquad'ın CPU/faz/grup gecikmesi ve gain staging. |
| Web Search **Bağlam** | **~31 adlandırılmış kaynak / 7 sorgu**: dbxpro 131 + ART EQ351 + greatchchurchsound + Universal Audio 527-A (LoC) + avcsstechworld + sound.stackexchange (6) · **W3C/webaudio Audio EQ Cookbook** + Wikipedia digital biquad + vmunix DF2T + dsp.se cascade + dsprelated + audiosciencereview + minidsp (7) · **JUCE forum ScopedNoDenormals + JUCE denormal-once-and-for-all** + dsp.se denormal + **Intel** + **NVIDIA CUDA** + LinkedIn reverb-glitch (6) · **tech.ebu.ch R128 PDF** + Wikipedia EBU R128 + MathWorks + forasoft/soundbridge (4) · **Rane Note 101 (Constant-Q)** + **Audison** + r/livesound + gearspace + HoRNet ThirtyOne (5) · **Voxengo SPAN** + Tonalux FFT + Tektronix overlap + Springer ESP32 analyzer (4) · dsp.se cascade-order + KVR (2) |
| Web Search **Kısa Açıklama** | **(1) Grafik EQ standardı:** 31 bant = **1/3 oktav, ISO merkez frekans, 20 Hz–20 kHz**, **±12 dB** kaydırıcı aralığı (dbxpro 131: "31, 1/3 octave bands, ISO frequency centers, ±12 dB"; ART EQ351: "1/3 Octave, ISO Spacing, 20 Hz–20 kHz ±0.5 dB"). **(2) Biquad matematiği:** W3C **Audio EQ Cookbook** (Robert Bristow-Johnson) peaking formülleri `A = 10^(dBgain/40)`, `alpha = sin(w0)/(2Q)` — **K3 dokümanındaki katsayı ile birebir aynı** (`dsp-chain.md:222-231`); **DF2T (Transposed Direct Form II)** iki durum değişkenli, float donanımda en yaygın/sabit-gecikmeli form (Wikipedia digital biquad, vmunix, JUCE). **(3) Denormal:** sessizlik kuyruğunda subnormal üretimi ses thread'ini **10-100× yavaşlatabilir** → FTZ/DAZ (MXCSR) ile kapatılır; JUCE bunu `ScopedNoDenormals` ile `processBlock()` başında yapar (JUCE forum, Intel, NVIDIA, dsp.se). **(4) Loudness:** EBU R128 / ITU-R BS.1770 **LUFS + true peak** ile zincir üst sınırı korunur; **Maximum True Peak Level** zincirin teknik üst sınırıdır (tech.ebu.ch R128 PDF) → boost yapan EQ sonrası true-peak/limiter zorunlu. **(5) Grafik vs parametrik:** sabit frekans + sabit Q nedeniyle grafik EQ **daha az esnek, faz/distorsiyonu daha yüksek** kabul edilir (r/livesound, gearspace); **Constant-Q** tasarımı Rane ile standardize edilmiştir (Rane Note 101), 1/3-oktav'da tipik **Q ≈ 4.4** (Audison). **(6) RTA:** FFT + overlap + smoothing ile spectrum analyzer; spektral işleme **verimli değilse ses düşüklüğü yapar** (Tonalux, Tektronix); Voxengo SPAN = referans RTA. **(7) Kaskad:** N bant = N biquad kaskadı; kaskad **toplam faz/group delay** üretir ama **blok gecikmesi eklemez**; ara kademelerde gain staging taşmayı önler (dsprelated, dsp.se, audiosciencereview, minidsp). |
| Web Search **Uzun Açıklama** | **(a) Grafik EQ tasarımı (kaynak 1-6):** dbxpro 131 ve ART EQ351 üretim cihazlarının ikisi de "31 bant / 1/3 oktav / ISO merkez frekans / ±12 dB" dilini kullanıyor; Universal Audio 527-A tarihsel 1/3-oktav cihazı aynı aralığı (20 Hz–20 kHz) tanımlar; greatchchurchsound "31 bant 1/3-oktav = 20 Hz–20 kHz'in üçte biri" diyerek band genişliğini tarif eder; sound.stackexchange'te frekans listesi (20, 25, 31.5, 40, 50, 63, …) **K3'teki `eq-parametric.md:117-122` listesiyle aynıdır**. → **ISO ızgarası + ±12 dB doğrulandı.** **(b) Biquad/peaking (kaynak 7-13):** W3C Audio EQ Cookbook (hem w3.org/TR hem webaudio.github.io aynası) peaking LP/HP/peak/bandpass formüllerini yayınlar; Wikipedia digital biquad DF1/DF2 ayrımını, vmunix DF2T'yi "2 durum değişkeni" olarak tarif eder; dsp.se "10 bant = 10 biquad kaskad, kazanç değişince katsayı yeniden hesaplanır" der; dsprelated kaskadın yüksek kademelerde sayısal olarak daha stabil olduğunu, audiosciencereview kaskadın **sabit faz/group delay olmadığını** (pulse distorsiyonu) vurgular. → **K3 katsayısı RBJ ile uyumlu; DF2T + faz uyarısı karara girdi.** **(c) Denormal (kaynak 14-19):** JUCE forum "ScopedNoDenormals → FTZ+DAZ" ve "denormal flush" başlıkları, dsp.se "aynı sistem sessizlikte ciddi yavaşlıyor", Intel derleyici kılavuzu "flush denormals to zero", NVIDIA "ses uygulamalarında denormallar genelde işitme eşiği altındadır", LinkedIn reverb-tail örneği "fade-to-silence maliyeti" → **FTZ/DAZ zorunlu.** **(d) Loudness/headroom (kaynak 20-23):** tech.ebu.ch R128 PDF "Maximum True Peak Level … üst teknik sınır", Wikipedia R128 normalizasyon tanımı, MathWorks R128 ölçüm örneği (LUFS + true peak), forasoft/soundbridge hedef tabloları → **EQ boost sonrası true-peak/limiter kapısı zorunlu.** **(e) Grafik vs parametrik + sabit-Q (kaynak 24-28):** Rane Note 101 Constant-Q'yu icat ettiğini/standardize ettiğini anlatır; Audison 1/3-oktav varsayılan Q'yu **4.4** verir; r/livesound "GEQ frekans/Q sabit → PEQ kadar esnek değil"; gearspace "20+ bant GEQ sabitliği yüzünden mastering'de işe yaramaz"; HoRNet ThirtyOne **analyzer + 31 bant GEQ** birleşimi örneği → **grafik EQ'nun yeri: sahne/kullanıcı dengesi; ince ayar için ayrı parametrik gerekli.** **(f) RTA (kaynak 29-31) + kaskad CPU (kaynak 30-31):** Voxengo SPAN FFT/true-peak referansı, Tonalux "verimsiz spektral işleme ses düşüklüğü yapar", Tektronix overlap primeri; Springer ESP32 örneği pencereleme/FFT zincirini gösterir. |
| Web Search **Paragraf Veri Uzun** | Profesyonel 31-band graphic EQ 2025-26'da hâlâ **ISO 1/3-oktav ızgarası + ±12 dB + sabit (constant) Q** üzerine kurulu (dbxpro, ART, greatchchurchsound, Universal Audio); frekans listesi 20/25/31.5/…/20000 (sound.stackexchange) = K3 spec'inin listesiyle birebir. Katsayı matematiği **RBJ Audio EQ Cookbook**'tadır (W3C + webaudio aynası) ve `dsp-chain.md:220-238` ile aynı formülleri içerir; uygulama formu **DF2T** (Wikipedia, vmunix, JUCE) — 2 durum değişkeni, float donanımda stabil. **Denormal**, sessizlikte ses thread'ini yavaşlatan gerçek bir üretim arızasıdır → **FTZ/DAZ** (JUCE `ScopedNoDenormals`, Intel, NVIDIA, dsp.se) callback başında açılır. **Headroom:** boost yapan EQ zinciri, true-peak üst sınırını aşarsa distorsiyon üretir → EBU R128/BS.1770 **Maximum True Peak Level** (tech.ebu.ch) ve limiter kademesi (dsp-chain [12] Limiting) devreye girer. **Tasarım ayrımı:** grafik EQ sabit frekans/Q ile hızlı denge için, **parametrik EQ serbest frekans/Q ile "kendi işitmesine göre" ince ayar** içindir (r/livesound, gearspace, Audison Q≈4.4 sabitlemesi). **CPU/faz:** N bant = N biquad kaskadı; kaskad **blok gecikmesi eklemez** ama **faz/group delay toplamı** üretir (dsprelated, audiosciencereview) — kalıcı faz distorsiyonu riski budur; ara kademe gain staging taşmayı önler (dsprelated). **RTA** ayrı bir FFT yoludur (SPAN, Tonalux, Tektronix) — EQ yoluna **veri okur, yazmaz**. |
| Web Search **Sonucu** | 1) **ISO 1/3-oktav 31-band + ±12 dB + sabit Q doğrulandı** (dbxpro, ART, greatchchurchsound, Universal Audio, sound.stackexchange — ≥2 çapraz) → **§2.1 grafik EQ spec'i.** 2) **RBJ peaking matematiği + DF2T doğrulandı** (W3C/webaudio, Wikipedia, vmunix, dsp.se, JUCE) → **§2.2b biquad formu; K3 katsayısı spec'i ile uyumlu.** 3) **Denormal/FTZ-DAZ zorunluluğu doğrulandı** (JUCE ×2, Intel, NVIDIA, dsp.se) → **§2.2d koruma maddesi.** 4) **True-peak/headroom koruması doğrulandı** (tech.ebu.ch R128, Wikipedia, MathWorks) → **§2.2e toplam gain bütçesi + limiter.** 5) **Grafik/parametrik ayrımı ve sabit-Q (≈4.4) doğrulandı** (Rane, Audison, r/livesound, gearspace, HoRNet) → **§2.2a çift EQ kararı.** 6) **Kaskadın faz/group delay ürettiği, blok gecikmesi eklemediği doğrulandı** (dsprelated, audiosciencereview, minidsp, dsp.se) → **§2.2c faz notu + §4.3 risk 1.** 7) **RTA ayrı yol** (Voxengo SPAN, Tonalux, Tektronix) → **§2.2f analyzer'ın EQ'ya karışmaması.** **Toplam ~31 adlandırılmış kaynak, 7 sorgu**; iki çıkarım açıkça işaretlendi: **⚠️** Q≈4.4 tek kaynağa (Audison) dayanır, 1/3-oktav Q'sunun resmi standardı derin doğrulanmadı; sayfa-içi tur yapılmadığı için sayısal CPU/latency iddiaları **kod ölçümü olmadan doğrulanmaz**. |
| Web Search **Alınan Karar** | **ADR-025 KABUL EDİLİR — ÇİFT EQ SİSTEMİ (5 madde):** **(a) 31-band grafik EQ** — ISO 1/3-oktav ızgara (20 Hz–20 kHz, 31 frekans `eq-parametric.md:117-122` ile aynı), bant başına **±12 dB**, **sabit Q (≈4.3–4.4)**; **aktif bant sayısı kullanıcı/sahne başına seçilebilir: min 2 – max 31** (TV/K10 10-band = bu ızgaranın oktav alt kümesi). **(b) Ayrı parametric EQ** — **min 2 bant (isteğe kadar)**, serbest frekans **20 Hz–20 kHz**, **Q 0.3–10**, **gain ±12 dB** — "kendi işitmesine göre" ince ayar (`adr-audio-template.md:177-179` ile birebir). **(c) Sıra:** **grafik → parametric**; tersine konfigürasyon (parametric → grafik) **izinli ama yazılı konfigürasyon notu ister** (`§2.2c`). Her iki EQ'da **bant başına bypass + preset kaydı**. **(d) DSP:** **RT-güvenli biquad cascade** — float32, **callback'te tahsisat YOK** ([ADR-017](ADR-017-dsp-hardware-mode)), **direct form II transposed**, **denormal koruması (FTZ/DAZ)**, **bant başına CPU bütçesi**, kaskad **blok gecikmesi eklemez** (faz/group delay notu), **toplam gain headroom koruması** (clip önleme → limiter, ADR-017 xrun). **(e) Ölçüm + kill-switch:** CPU/latency ölçümü olmadan "GERÇEK" sayı yazılmaz (`⚠️ VERIFICATION REQUIRED`); bypass tek tuşla tüm EQ'yu devre dışı bırakır ([ADR-019](ADR-019-per-os-neva-player) kill-switch zinciri). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: grafik EQ standardı (6), biquad/RBJ/DF2T (7), denormal (6), loudness/headroom (4), grafik-vs-parametrik + constant-Q (5), RTA (4), kaskad CPU/faz (2) → **~31 adlandırılmış kaynak, 7 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada karşılanır. **Vault tarafı aynı resmi verdi:** EQ spec'i + UI akışı + dizin slotu **IMPLEMENTED**, **tüm EQ kodu PLANNED (C++ = 0)**, K3/K10/UI arasındaki bant tanımı **çelişkili** → bu ADR **spec kararıdır, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (31):** 1) dbxpro.com — 131 31-band EQ · 2) sound.stackexchange.com — 10/31 band frekans formülü · 3) artproaudio.com — EQ351 · 4) greatchchurchsound.com — EQ and How To Use It · 5) tile.loc.gov — Universal Audio 527-A (1/3-octave) · 6) avcsstechworld.com — Equalizer Basics · 7) hornetplugins.com — ThirtyOne (RTA+GEQ) · 8) **ranecommercial.com — Note 101 Constant-Q** · 9) **audison.com — How many EQ bands** · 10) reddit r/livesound — GEQ vs PEQ · 11) gearspace.com — why engineers avoid 20+ band GEQ · 12) **w3.org/TR/audio-eq-cookbook** · 13) **webaudio.github.io — Audio-EQ-Cookbook** · 14) **en.wikipedia.org — Digital biquad filter** · 15) vmunix.com — LLM-generated biquad (DF2T) · 16) dsp.stackexchange.com — cascading filters EQ · 17) dsprelated.com — Biquad glossary (gain staging) · 18) audiosciencereview.com — Why Biquads (group delay) · 19) minidsp.com — FIR vs IIR staging · 20) **forum.juce.com — State of the Art Denormal Prevention (ScopedNoDenormals)** · 21) forum.juce.com — Resolving denormal floats · 22) dsp.stackexchange.com — denormalized numbers · 23) **intel.com — Denormal numbers (flush to zero)** · 24) developer.nvidia.com — Flush Denormals (audio) · 25) linkedin — reverb tail denormal glitches · 26) **tech.ebu.ch/docs/r/r128.pdf** · 27) en.wikipedia.org — EBU R 128 · 28) mathworks.com — R128 loudness · 29) voxengo.com — SPAN · 30) tonalux.org — Efficient FFT for audio plugins · 31) tek.com — FFT Overlap Processing primer. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-017-dsp-hardware-mode]] | **Hard-RT bağlayıcısı:** callback içinde `malloc/free/new/delete/make_shared/vector::push_back`, bloklayıcı I/O ve `throw` **yasak**; buffer/latency bütçesi ve **xrun politikası** bu ADR'ye de uygulanır → EQ stage'i tahsisatsız çalışır, aksi halde bölüm revert. |
| [[ADR-019-per-os-neva-player]] | EQ, ortak çekirdeğin bir **stage'idir**; kill-switch/fallback zinciri EQ'yu da **bypass eder** (platform adapter'ı EQ bilmez). |
| [[ADR-018-footer-player-vaporwave]] + [[ADR-001-vanilla-js-itcss]] | EQ **UI hook'u** web katmanında (footer player + settings/03-equalizer akışı); tarayıcı RT yoluna girmez, **analyzer verisi tek yönlü** (DSP → UI, UI → DSP yalnız parametre). |
| [[ADR-006-performance-targets]] | Ölçüm kapıları: `<10ms ASIO / <20ms WASAPI`, `underrun = 0` (`⚠️ VERIFICATION REQUIRED`) — EQ CPU eklemesi bu bütçeyi aşamaz. |
| [[ADR-005-ultrathink-protocol]] | Kod/vault kanıtı olmayan her iddia etiketli: C++ **0 dosya**, "1.5% CPU" **ölçülmemiş**, `ADR-062` **diskte yok**, 10↔31 bant eşlemesi **tahmin** → `⚠️ VERIFICATION REQUIRED`. |
| Guardrail #16 (şablon zorunlu) | Bu dosya `.templates/adr/adr-template.md` iskeleti (7 bölüm + §1.3 9 alan) + `.templates/adr/adr-audio-template.md` domain sözlüğünden üretildi (§6'da kayıt). |
| In-Place Refactoring | Dosya adları **değiştirilmez**; `eq-parametric.md` (Q 0.5-10 ↔ ADR 0.3-10), `music-panel.md` (10-band) ve `dsp-chain.md` ASCII indeks kayması **bu ADR'de düzeltme yapılmadan** §5.1'e yazılır. |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil**. |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi, `vault-utf8-writer append`). |
| Numara/seri kuralı | `ADR-025-professional-eq-system` rezerve slottur (`../index.md:62`); `.ai/architecture/adr/ADR-025-k8-2-k15-siniri.md` **ayrı seri** → wiki-link **her zaman tam göreli yol** (kısa `[[ADR-025]]` yasak, ADR-024 §4.3/4 ruhu). |
| REDACTED | Kalibrasyon/log çıktılarında credential/`.env` değeri yazılmaz; `.ai/keys.md` içeriği bu ADR'ye kopyalanmaz. |

---

## 2. Karar (Decision)

**CoreMusic EQ sistemi ALTI maddeyle bağlayıcı ilan edilir:**

**(a) ÇİFT EQ — zincirde iki ayrı EQ bloğu vardır (kullanıcı onayı):** (1) **31-band grafik EQ** ve (2) **ayrı parametric EQ**. İkisi tek sınıfta birleştirilmez, ikisi de tek başına "EQ" diye adlandırılmaz.

**(b) 31-BAND GRAFİK EQ:** ISO **1/3-oktav** ızgara **20 Hz–20 kHz** (31 frekans, `eq-parametric.md:117-122` listesi ile birebir — §2.2a), bant başına **±12 dB**, **sabit Q (≈4.3–4.4, constant-Q)** — frekans ve Q **sabit**, yalnız kazanç değişken (grafik EQ standardı). **Aktif bant sayısı seçilebilir: min 2 – max 31** (kullanıcı veya sahne/cihaz başına — TV/K10 10-band senaryosu bu kuralın alt kümesidir). Aktif olmayan bant **işlenmez** (CPU kazancı) ve arayüzde pasif gösterilir.

**(c) AYRI PARAMETRİK EQ:** **min 2 bant** (isteğe kadar, üst sınır konfigürasyon), **serbest frekans 20 Hz–20 kHz**, **Q 0.3–10**, **gain ±12 dB** — "kendi işitmesine göre" ince ayar için. Frekans/Q/kazanç her bantta bağımsız.

**(d) SIRA + BYPASS + PRESET:** varsayılan sıra **grafik → parametric**; tersine kullanım (parametric → grafik) **izinli ama konfigürasyon notu zorunlu** (§2.2c). **Her iki EQ'da bant başına bypass** ve **preset kaydı** (grafik kazanç vektörü + parametrik frekans/Q/kazanç + aktif bant sayısı tek preset içinde saklanır).

**(e) DSP — RT-GÜVENLİ Biquad Cascade:** **float32** işlem, **callback'te tahsisat YOK** ([ADR-017](ADR-017-dsp-hardware-mode) hard-RT), **biquad direct form II transposed (DF2T)**, **denormal koruması (flush-to-zero / denormals-are-zero)**, **bant başına CPU bütçesi** (ölçülmüş), kaskad **blok gecikmesi eklemez** (yalnız faz/group delay — §2.2c), **toplam gain headroom koruması** (klip önleme → limiter kademesi, xrun üretmez).

**(f) ÖLÇÜM + KILL-SWITCH:** CPU/latency değeri **ölçülmeden "GERÇEK" olarak yazılmaz** (`⚠️ VERIFICATION REQUIRED`); tüm EQ'yu tek tuşla devre dışı bırakan **bypass/kill-switch** vardır ve bu, [ADR-019](ADR-019-per-os-neva-player) fallback zincirine bağlıdır.

### 2.1 Neden Bu Seçenek?

- **Tek EQ tek işi yapamaz:** grafik EQ sabit frekans/Q ile **hızlı denge** (sahne/kullanıcı), parametrik **serbest frekans/Q ile kişisel ince ayar** sağlar (§1.3-5: r/livesound, gearspace, Audison) → ikisi ayrı blok.
- **31 bant zaten spec'te:** `eq-parametric.md:117-122` ISO listesi, `brain.md:900` "EQ Bands 31", `dsp-chain.md:174` `MAX_BANDS = 31`, dizin slotu "Professional EQ System (31-band)" → kararı **yeniden icat etmek** değil **netleştirmek** gerek (SSOT).
- **Aktif bant 2–31 esnekliği üç dokümanı tek kuralda birleştirir:** UI "TV = 10-band" (`03-equalizer.md:145`), K10 "10-bant grafik" (`music-panel.md:135`), hata durumu "bant sayısı tutarsız → 31" (`03-equalizer.md:135`) → **seçilebilir aktif bant** üçünü de karşılar, ayrı ayrı EQ üretmez.
- **±12 dB + sabit Q üretildiği gibi doğru:** dbxpro/ART/greatchchurchsound aynı spesifikasyonu veriyor (§1.3-1); `adr-audio-template.md:178` `eq_gain −12…+12` ile birebir.
- **DF2T + float32 + tahsisatsız:** RBJ katsayısı (K3 `dsp-chain.md:220-238` ile aynı formül) en stabil formatta uygulanır; `neva-engine-core.md:329` `bufferSize = 256` bloğunda 31+ biquad tek blokta döner.
- **Denormal ihmal edilemez:** sessizlik kuyruğunda subnormal üretimi ses thread'ini yavaşlatır → **FTZ/DAZ** callback başında (§1.3-3: JUCE/Intel/NVIDIA).
- **Headroom koruması zorunlu:** boost yapan 31 bant toplanır, true-peak aşımı klip üretir (§1.3-4: EBU R128) → `adr-audio-template.md:314` "toplam boost bütçe aşımı yasak" + ADR-017 limiter/xrun.
- **Ölçüm yoksa iddia yok:** spec'teki "Gerçek 1.5%" kod 0 iken **kanıt değildir** (ADR-005) → bu ADR hedef yazar, ölçülmüş sayı **üretmez**.

### 2.2 Teknik Detaylar

**a) Grafik EQ bant ızgarası (ISO 1/3-oktav — 31 sabit frekans, ±12 dB, sabit Q):**

| # | Hz | # | Hz | # | Hz |
|---|---|---|---|---|---|
| 1 | 20 | 12 | 250 | 23 | 2500 |
| 2 | 25 | 13 | 315 | 24 | 3150 |
| 3 | 31.5 | 14 | 400 | 25 | 4000 |
| 4 | 40 | 15 | 500 | 26 | 5000 |
| 5 | 50 | 16 | 630 | 27 | 6300 |
| 6 | 63 | 17 | 800 | 28 | 8000 |
| 7 | 80 | 18 | 1000 | 29 | 10000 |
| 8 | 100 | 19 | 1250 | 30 | 12500 |
| 9 | 125 | 20 | 1600 | 31 | 16000 |
| 10 | 160 | 21 | 2000 | — | — |
| 11 | 200 | 22 | 2500 | — | — |

> 31 sabit frekans = **20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160, 200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600, 2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000** (bant #7 = 5000 Hz, #31 = 20000 Hz). **SSOT:** `eq-parametric.md:117-122` · **Kaynak:** ISO 1/3-oktav + W3C Cookbook (§1.3-1, §1.3-2).

**b) Parametrik EQ parametreleri (şablon sözlüğü ile birebir):**

| Parametre | Aralık | Varsayılan | Kaynak |
|---|---|---|---|
| bant sayısı | **≥ 2** (üst sınır konfigürasyon; önerilen 8) | 2 | kullanıcı kararı |
| frekans | 20 Hz – 20 kHz (log-uzay serbest) | 500 Hz | §1.3-2 |
| Q | **0.3 – 10** | 1.0 | `adr-audio-template.md:179` |
| kazanç | **−12 … +12 dB** | 0 dB | `adr-audio-template.md:178` |
| filtre tipi | Peak (varsayılan) ± LowShelf/HighShelf/Notch (isteğe) | Peak | `eq-parametric.md:36-47` |

> **Sapma kaydı:** `eq-parametric.md:30` "Q: 0.5-10" der; bu ADR **0.3–10** kabul eder (şablon `:179` ile aynı) → doküman düzeltmesi §5.1/4 (In-Place, dosya adı değişmez).

**c) Zincir sırası, faz ve ters-konfigürasyon notu:**

```
Input → Gain → [Graphic EQ (max 31 bant, sabit Q)] → [Parametric EQ (≥2 bant)] → Dynamics → Reverb → Limiter → Output
                    (aşama 5 / "EQ Parametric")                                              (ADR-017 xrun kapısı)
```

- **Varsayılan sıra grafik → parametric:** geniş denge önce (grafik), kişisel düzeltme sonra (parametrik) — parametrik düzeltmeleri grafik kaydırıcılarının "asıltması" engellenir.
- **Ters sıra (parametric → grafik) izinli**; o zaman **konfigürasyon notu zorunlu**: preset adı `*-rev` uzantısı alır ve `preset.json`'da `chainOrder` alanı yazılır (aksi halde preset yanlış yorumlanır).
- **Faz notu:** kaskad **blok/zaman gecikmesi eklemez** (IIR, grup gecikmesi frekansa bağlıdır) → ADR-006 latency bütçesine **ms eklemez**; ancak 31 bant **faz/distorsiyon biriktirir** (§1.3-6: audiosciencereview) → "faz nötr" iddiası **yazılmaz**, ölçüm §5.1/7.
- **Sıra değişikliği ADR gerektirmez** (bu ADR'nin `chainOrder` alanı yeterlidir) ama **ölçüm kaydı zorunludur** (`adr-audio-template.md:317`).

**d) RT-güvenli DSP kuralları (bağlayıcı):**

| Kural | Değer | Kanıt |
|---|---|---|
| Veri tipi | **float32** zincir boyunca (bit daralması yok, dither yalnız teslimde) | `adr-audio-template.md:325, 338-340` |
| Biquad formu | **Direct Form II Transposed** (2 durum değişkeni, `s1`,`s2`) | §1.3-2 (Wikipedia, vmunix, JUCE) |
| Tahsisat | callback'te **`new/delete/malloc/free/vector::push_back/throw` YOK**; bantlar **ön-tahsislı üye dizisi** (`EQBand bands[31]`) | [[ADR-017-dsp-hardware-mode]] + `brain.md` §7.1 |
| Denormal | **FTZ + DAZ** callback başında açılır (x86 MXCSR; portatif karşılık `ScopedNoDenormals` benzeri RAII) | §1.3-3 (JUCE, Intel, NVIDIA) |
| Katsayı güncelleme | Yalnız parametre değişiminde; **RT thread dışında** hesaplanır, atomik/çift-tampon ile publish (RT thread **yazmaz, okur**) | `dsp-chain.md:201-203` + lock-free `neva-engine-core.md:12` |
| CPU bütçesi | **Ölçülmüş** toplam EQ ≤ **%2** (spec hedefi `eq-parametric.md:331`); bant başına ≈ **%0.065** (2/31 — **türetilmiş, ölçülür**) | §1.3-7 + spec |
| Blok | `bufferSize = 256` (`neva-engine-core.md:329`) — `adr-audio-template.md:176` `block_size` varsayılan **64** ile **farklı**: 64 kullanılırsa CPU/bant **×4** artar → ölçüm **her iki blokta** yapılır (`⚠️ VERIFICATION REQUIRED`) | iki spec çelişkisi |
| Headroom | Toplam boost bütçesi: **aktif boost toplamı ≤ +12 dB** (sabit kazançlı bantlar dahil) → aşarsa **auto-attenuate** (giriş kazancı düşer) + **true-peak limiter** kademesi | `adr-audio-template.md:314` + §1.3-4 |
| Kill-switch | Tüm EQ bypass → bit-düz sinyal (`in == out`), state **korunur** (geri dönüş anında) | [[ADR-019-per-os-neva-player]] |
| Ölçüm | CPU/latency/LUFS/XRUN **sayıyla** `log.md`'ye yazılır; "GERÇEK" etiketi yalnız ölçüm sonrası | `adr-audio-template.md:354-355` |

**e) Bypass / preset veri modeli:**

```jsonc
// preset tek kayıt: iki EQ + zincir sırası + aktif bant (UI 03-equalizer akışı ile aynı alanlar)
{
  "name": "Rock",
  "chainOrder": "graphic>parametric",       // ters sıra: "parametric>graphic" + ad "Rock-rev"
  "graphic":  { "activeBands": 31,          // 2..31
                "gains": [ /* 31 × float, -12..+12 */ ],
                "bandBypass": [ /* 31 × bool */ ] },
  "parametric": { "bands": [ { "freq": 850.0, "q": 1.2, "gain": 3.0,
                               "type": "peak", "bypass": false } ] }  // ≥2
}
```

- **Bant başına bypass:** `bandBypass[i] = true` → o biquad `process()`'e **girmez** (state korunur, CPU 0).
- **Preset kaydı:** grafik kazanç vektörü + aktif bant sayısı + parametrik bantlar + `chainOrder` **tek dosyada**; `03-equalizer.md`'deki 8 preset (Flat/Pop/Rock/Jazz/Classical/Dance/Bass Boost/Vocal) ve `eq-parametric.md:270-286` preset adları **tek listeye birleştirilir** (aynı bilgi iki yerde yaşamaz — ADR-024 ruhu).
- **Eşik/kısıt:** preset'te `activeBands < 2` **kabul edilmez** (min 2); `gains` uzunluğu ≠ 31 ise **reject + Flat** (`03-equalizer.md:135` hata durumu ile aynı davranış).

**f) Analyzer/RTA sınırı:** spektrum analizi (`.ai/architecture/k3-ses-motoru/analysis-spectrum.md`, `coremusic_neva` spektrum modülü `brain.md:317`) **EQ yolundan veri okur, EQ'ya yazmaz**; FFT/overlap yolu ayrı süreçtir (§1.3-6) ve **callback bütçesi içinde ölçülür** — EQ CPU bütçesine **dahil edilmez, ayrı satırda raporlanır**.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Tek 31-band "parametrik" EQ** (bugünkü K3 dili — sabit frekans + değişken Q) | Tek blok, tek preset, daha az kod | Frekanslar sabitken "parametrik" etiketi yanıltıcı; kişisel ince ayar için serbest frekans yok; K10/UI 10-band senaryosu karşılanmaz | Kullanıcı kararı **çift EQ**; `03-equalizer.md:143-148` cihaz matrisi (TV 10-band / Desktop 31) tek blokla esnetilemez |
| 2 | **Yalnız 10-band grafik EQ** (K10 `music-panel.md:135`) | UI'da basit, CPU düşük (10 biquad) | ISO 1/3-oktav standardı ve `brain.md:900` "31" ihlal edilir; ince ayar imkânsız; "Professional EQ System (31-band)" slotuyla çelişir | Dizin slotu `index.md:62` + K3 spec + `MAX_BANDS=31` → **10-band alt kümedir, ana karar değildir** |
| 3 | **Dış plugin/SaaS EQ** (VST3 harici çözüm) | Sıfır RT riski, hazır UI | `.ai/projects/NevaEngine` **YOK**, kod yok; harici bağımlılık + lisans + offline sınır; kill-switch/fallback zinciri (ADR-019) dışına kaçar | Çekirdek ses motoru EQ'yu **kendi** taşımalı (ADR-017 3 katman); YAGNI — spec zaten repo'da |
| 4 | **Linear-phase (FIR) EQ** | Faz nötr, "daha temiz" | FIR **blok gecikmesi ekler** (yüzlerce ms) → ADR-006 `<10ms` bütçesi kırılır; FIR bellek/CPU maliyeti yüksek; callback'te tahsisat yasağına takılır | Latency bütçesi ihlali (ADR-017/006); bu karar **minimum-phase biquad** ile sınırlı |
| 5 | **Analog-tarzı sabit bant + otomatik "smile" eğrisi** (EQ'suz kullanıcı profili) | Kullanıcı için sıfır ayar | Ölçüm/kişiselleştirme gerekir, "kendi işitmesine göre" gereksinimi karşılanmaz; kör tarafletme riski | Kullanıcı kararı: **parametrik elle ince ayar** açık kalmalı |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek karar iki ihtiyacı karşılar:** sahne/denge (grafik, hızlı) + kişisel ince ayar (parametrik) — üç ayrı dokümandaki (K3/K10/UI) EQ tanımı **tek SSOT'a** iner.
- **Standart uyumu:** ISO 1/3-oktav + ±12 dB + sabit-Q ile **üretim cihazlarıyla karşılaştırılabilir** davranış (dbxpro, ART — §1.3-1); kullanıcı 31-band cihaz preset'ini birebir taşır.
- **Aktif bant 2–31 → CPU doğrudan ölçeklenir:** 10-band senaryosu (TV/K10) 31 bant yerine 10 biquad işler; pasif bantlar **sıfır maliyet**.
- **RT güvenliği baştan yazılı:** tahsisatsız, DF2T, FTZ/DAZ, katsayı publish'i → ADR-017 xrun/underrun hedefine **ayrılan** değil, **katkıda** olan bir stage.
- **Headroom/klip koruması tanımlı:** toplam boost bütçesi + limiter → gerçek-distorsiyon (klip) ve xrun riski **kurala** bağlandı.
- **Ölçülebilir:** CPU/LUFS/XRUN sayıları `log.md`'ye yazılır → spec'teki "Gerçek" sütunları gelecekte **kanıtlanabilir** hale gelir.
- **UI hook'u hazır:** `03-equalizer.md` akışı + footer player (ADR-018) + preset listesi bu veri modeline **bağlanır**, yeniden tasarlanmaz.

### 4.2 Olumsuz Sonuçlar

- **Kod yazımı tamamen önümüzde:** repo'da **0 C++ dosyası** → bu ADR **spec'tir**; EQ "var" sayılmaz (§1.1-D).
- **CPU riski gerçek:** 31 + ≥2 biquad = **≥33 kaskad × 2 kanal**; `bufferSize=256` yerine `block_size=64` kullanılırsa maliyet ×4 → bütçe aşımı olasılık dahilinde (§4.3/1).
- **Faz birikimi:** 31 bant minimum-phase IIR **distorsiyon biriktirir**; "faz nötr" vaat edilemez (§1.3-6) — kalitesiz içerikte duyulabilir.
- **Çift EQ karmaşası:** kullanıcı iki EQ arasında kaybolabilir → UI'da **grafik birincil, parametrik "Gelişmiş" bölümü** olarak sunulur (§5.1/5).
- **Spec çelişkileri çözülmeden kalır:** `eq-parametric.md` Q 0.5-10 ↔ ADR 0.3-10; `dsp-chain.md` ASCII indeks kayması; `index.md:642` kısa biçim linki → In-Place düzeltme ayrı işlem (§5.1/4, 8, 9).
- **Debate tamamlandı, Tech Lead onaylı:** §5.3'te 3 tur / 20 persona → **18/2/0 KABUL**; 3 bağlayıcı şart §5.5'e eklendi — kod PLANNED kalemleri (§5.4) **açık kaldı**.

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **CPU bütçesi aşımı:** 33+ biquad × kanal, 64-örnek blokta ×4 maliyet → xrun/underrun (ADR-017) | 3 (Olası) | 4 (Yüksek) | **Bant başına CPU ölçümü** (§2.2d); aktif bant otomatik düşürme (`activeBands` 31→16→8); bütçe aşımında **grafik EQ bypass** (kill-switch, ADR-019) |
| 2 | **Faz/grup gecikmesi distorsiyonu:** 31 bant boost/kesim birikimi → "bulanık" dinleme | 3 (Olası) | 3 (Orta) | Toplam boost bütçesi ≤ +12 dB (§2.2d); "faz nötr" iddiası yasak; ölçüm §5.1/7; parametrik bant **bypass** ile izolasyon |
| 3 | **Denormal spike:** sessizlikte subnormal → sessiz anlarda CPU sıçraması (FTZ açılmazsa) | 2 (Mümkün) | 4 (Yüksek) | **FTZ/DAZ zorunlu** (§2.2d) + sessizlik testi (10 sn −90 dBFS) CPU profilinin eşitliği §5.1/7 |
| 4 | **Klip/headroom aşımı:** 31 bant boost + parametrik boost → true-peak aşımı, distorsiyon + limiter tetiklenmesi | 3 (Olası) | 4 (Yüksek) | Toplam boost bütçesi + **auto-attenuate** + true-peak limiter (§2.2d); EBU R128 true-peak ölçümü `log.md`'ye (§1.3-4) |
| 5 | **Preset tutarsızlığı:** bant sayısı/frekans uyuşmazlığı → yanlış yorum (TV 10-band vs 31) | 3 (Olası) | 3 (Orta) | Preset şeması doğrulama: `gains.length == 31`, `activeBands ≥ 2`, aksi halde **reject + Flat** (`03-equalizer.md:135` davranışı) |
| 6 | **Ölçülmüş sayı uydurması:** spec'teki "Gerçek 1.5% / 0.008ms" kod olmadan "gerçek" sanılır | 4 (Çok olası) | 3 (Orta) | ADR-005: yalnız ölçüm sonrası "GERÇEK"; aksi `⚠️ VERIFICATION REQUIRED` (§1.1-D, §5.1/7) |
| 7 | **Numara/seri karışıklığı:** `.ai/architecture/adr/ADR-025-k8-2-k15-siniri.md` ↔ bu dosya | 4 (Çok olası) | 2 (Düşük) | Wiki-link **her zaman tam göreli yol + slug** (§1.4); iki seri `index.md`'de ayrı kategori; birleştirme üst kararı (`⚠️`) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Bant ızgarasını SSOT yap:** `eq-parametric.md:117-122` 31 frekans listesini **tek kaynak** ilan et; `music-panel.md` 10-band tablosunu "ISO ızgaranın oktav alt kümesi (31.5→32, 63→64)" notuyla **bağla** (dosya adı değişmez) | Embedded Engineer + Vault Steward | 0.5 gün |
| 2 | **RT-güvenli `GraphicEQStage` + `ParametricEQStage` arayüzü** yaz (spec: §2.2a-d; `IDSPStage` `noexcept`, ön-tahsislı `bands[31]`, DF2T, FTZ/DAZ, çift-tampon katsayı publish) — **kod 0 → bu adım PLANNED** | Embedded Engineer | 1.5 hafta |
| 3 | **Aktif bant mekanizması** (`setActiveBandCount(2..31)`) + **bant başına bypass** + preset şeması (§2.2e) | Embedded Engineer + UI Designer | 3 gün |
| 4 | **Spec düzeltmeleri (In-Place, dosya adı yok):** `eq-parametric.md:30` "Q 0.5-10" → "0.3–10 (ADR-025)"; başlık/terminoloji "grafik vs parametrik" ayrımı; `dsp-chain.md` ASCII indeks kayması ([5]/[10] tekrarı) | Vault Steward | 0.5 gün |
| 5 | **EQ UI hook'u:** `03-equalizer.md` akışını §2.2e veri modeline bağla (Grafik birincil / Parametrik "Gelişmiş", cihaz matrisi aktif-bant eşlemesi, preset hata durumları) | UI Designer | 2 gün |
| 6 | **10↔31 bant eşlemesini doğrula** (§1.1-D iv: `⚠️` tahmin) — gerçek frekans eşlemesi hesaplanır, `log.md`'ye sayıyla yazılır | Embedded Engineer | 0.5 gün |
| 7 | **Ölçüm turu:** CPU/bant (64 ve 256 blok), sessizlik (−90 dBFS 10 sn) CPU profili, toplam boost → true-peak, faz/grup gecikmesi (min/max(boost)) → hepsi **sayıyla** `log.md`'ye | Embedded Engineer + QA Engineer | 1 hafta |
| 8 | **Katalog/özet senkronu:** `brain.md:980` özeti "ADR-025 | Professional EQ (31-band grafik + parametrik)" olur; `index.md:642` kısa biçim linki ADR-024 biçimiyle (tam göreli yol) düzeltilir; `keys.md:260` anahtar kelimesi güncellenir | MO (vault-updater) | 0.5 gün |
| 9 | **dsp-chain şema düzeltmesi + bağ:** EQ stage'leri `[5]` (grafik) ve `[6]` (parametrik) olarak **tek satırda** göster; `Master EQ` `[11]` ile çelişki giderilir | Embedded Engineer + Vault Steward | 0.5 gün |
| 10 | **Doğrulama:** `şablon tutamağı taraması (ADR-025, hedef 0)` = 0 · wiki-link disk kontrolü (§6) · `vault-utf8-writer scan` (mojibake 0) · `.ai/.decisions/index.md:62` slug eşleşmesi · `log.md` append 1 satır | Vault Steward | 0.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (c) çift EQ yerine tek EQ istenirse → **parametrik blok kaldırılır, grafik 31-band kalır** (grafik zaten K3 spec'inin çekirdeği); (b) aktif bant mekanizması vazgeçilirse → sabit `activeBands = 31`, TV/10-band senaryosu `03-equalizer.md`'de **preset-only**'ye çekilir; (e) denormal/FTZ'yi uygulamazsan → **EQ stage'i RT'de üretilmez** (ADR-017 ihlali, xrun riski açık kalır).

**Tam geri dönüş:** dosya adı/yerleşim değişmediği için (In-Place Refactoring korundu) geri dönüş = (1) EQ stage'leri `setStageActive(false)` ile zincirden düşürülür (pipeline **14 aşamaya** iner, `dsp-chain.md:378` "Stage Sayısı 15" satırı güncellenir); (2) preset şeması `status: reverted` işaretlenir, dosyalar **silinmez**; (3) `vault-utf8-writer` yedeği (`<file>.bak`, ilk yedek) eski içeriği verir; (4) `.ai/log.md`'ye tek satır revert append'i atılır; (5) `.ai/.decisions/index.md:62` satırı `status: reverted` olur; (6) **bu ADR düzenlenmez**, `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** `log.md` append-only geçmiş, karar dizini satırı ve `eq-parametric.md` spec içeriği **bozulmaz**; kod yok olduğu için geri döndürülecek tek şey spec'tir.

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| Tur 1 | 20 persona | ✅ TAMAMLANDI | **Bulgu:** `dsp-chain.md` spec'te EQ stage 5 — `MAX_BANDS = 31` (`:174`), RBJ katsayıları (`:220-238`), `noexcept` arayüz (`:50-56`) · **repo kodu 0** · **16 × `⚠️ VERIFICATION REQUIRED`** · **15 kabul/neutral** · **4 uyarı** (QA: frekans yanıt testi · UI: çift EQ ayrımı · Perf: headroom teyit · Critic: çift EQ faz/gain bütçesi şart) |
| Tur 2 | İtiraz→çözüm | ✅ TAMAMLANDI | **4 itiraz → 4 çözüm → bağlayıcı şart:** (1) çift EQ faz/bütçe → **toplam gain staging + faz doğrulama testi** → **şart 1a**; (2) zincir sırası belirsiz → **varsayılan grafik→parametrik, tersi desteklenir (dokümante)** → **şart 1b**; (3) kod 0 → **RBJ katsayı + frekans yanıt unit testi (±0.5 dB)** → **şart 2**; (4) UI karmaşası → **EQ UI grafik ızgara + parametric knob ayrımı (ADR-018)** → **şart 3** |
| Tur 3 | Oy | ✅ TAMAMLANDI | **18 kabul / 2 çekimser / 0 red → KABUL** |
| **Toplam** | **3 tur / 20 persona** | **✅ TAMAMLANDI** | **18/2/0 KABUL** — 3 şart §5.5'e yazıldı; Tech Lead **✅** (§7) |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| Tüm EQ C++ kodu (`GraphicEQStage`, `ParametricEQStage`, biquad, FTZ) | ❌ PLANNED (repo `*.cpp/*.h` = **0**) | §5.1/2-3 |
| Bant başına bypass + preset kaydetme katmanı | ❌ PLANNED | §5.1/3 |
| CPU/latency/faz ölçümleri ("GERÇEK" sayılar) | ⚠️ VERIFICATION REQUIRED | §5.1/7 |
| Spec düzeltmeleri (Q aralığı, ASCII indeks, 10↔31 eşleme) | ⚠️ PLANLI (In-Place) | §5.1/1, 4, 6, 9 |
| Debate 3/20 + Tech Lead onayı | ✅ TAMAMLANDI (18/2/0 KABUL — 3 şart §5.5) | §5.3, §5.5, §7 |
| `ADR-062` (DSP Pipeline Architecture) diskinde yok | ⚠️ VERIFICATION REQUIRED | üst karar |

### 5.5 Kabul Şartları (debate Tur 2 → Tur 3 çıktısı — bağlayıcı)

> **Kaynak:** §5.3 debate (3 tur / 20 persona → 18/2/0 KABUL). 3 şart, oylamada **KABUL koşulu** olarak bağlandı; "kabul ≠ tamamlandı" (§5.4) — şartlar kapanmadan EQ stage'i üretime girmez.

| # | Şart | Bağlayıcı madde | Sorumlu | Kapanış |
|---|------|-----------------|---------|---------|
| **1a** | **Gain staging + faz doğrulama testi:** çift EQ'nun (grafik + parametrik) **toplam gain bütçesi** ve **faz doğrulaması** — aktif boost toplamı ≤ **+12 dB** (§2.2d headroom), auto-attenuate + true-peak limiter; **faz doğrulama testi** zorunlu, "faz nötr" iddiası yasak (§2.2c) | §2.2c, §2.2d, §4.3/2, §4.3/4 | Embedded Engineer + QA Engineer | §5.1/7 |
| **1b** | **Zincir sırası dokümante:** varsayılan **grafik → parametrik**; ters sıra (parametrik → grafik) **desteklenir** ve `chainOrder` alanı + preset adı `*-rev` uzantısı ile **dokümante edilir** (§2.2c) — konfigürasyon notu olmayan ters sıra kabul edilmez | §2.2c, §2.2e | Embedded Engineer + Vault Steward | §5.1/4 |
| **2** | **Frekans yanıt testi:** RBJ peaking katsayısı (`dsp-chain.md:220-238`) **unit test** ile doğrulanır; ölçülen **frekans yanıtının referans eğriden sapması ≤ ±0.5 dB**. Repo kodu 0 (§1.1-D) → test kod ile birlikte yazılır; **test yoksa §5.1/2 kapanmaz** | §1.1-A, §2.2a, §2.2b | QA Engineer + Embedded Engineer | §5.1/2, §5.1/7 |
| **3** | **EQ UI ayrımı + preset paketi:** UI'da **grafik EQ ızgara** (kaydırıcı) ile **parametrik knob** (frekans/Q/kazanç) **görsel olarak ayrılır** ([[ADR-018-footer-player-vaporwave]] — grafik birincil, parametrik "Gelişmiş" bölümü, §4.2) + **preset paketi tek listeye** birleştirilir (§2.2e: `03-equalizer.md` 8 preset + `eq-parametric.md:258-287` adları) | §2.2e, §5.1/3, §5.1/5 | UI Designer + Vault Steward | §5.1/3, §5.1/5 |

**Şart takibi:** 1a+1b = **Şart 1**, test = **Şart 2**, UI+preset = **Şart 3**; durumları kapanışa kadar **`⚠️ VERIFICATION REQUIRED`** ve §5.1/10 doğrulamasında tek tek denetlenir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail'ler (bu ADR'nin yazım usulü) |
| [[../../AGENTS.md]] | Agent registry — §5 domain (`*.cpp` → Embedded Engineer), §16 Embedded kalite standardı "Zero-allocation, lock-free, **noexcept**", §25.3 kural 2/3 (frozen + log append-only) |
| [[../../WORKFLOW.md]] | Süreçler — uygulama adımlarının faz bağlamı |
| [[../index]] | Karar dizini — **satır 62** `[[ADR-025-professional-eq-system]]` (slug ✅) |
| [[../../index]] | Master katalog — satır 642 kısa biçim link kaydı (§5.1/8) |
| [[../../brain]] | Mimari karar özeti — `:390` processEQ, `:900` EQ Bands 31, `:980` ADR-025 (§5.1/8) |
| [[ADR-017-dsp-hardware-mode]] | **Hard-RT bağlayıcısı** — tahsisat/bloklayıcı yasağı, buffer/latency, xrun/limiter (§1.4, §2.2d) |
| [[ADR-019-per-os-neva-player]] | Ortak çekirdek + **kill-switch** — EQ bypass zinciri (§2.2d) |
| [[ADR-018-footer-player-vaporwave]] | Player UI — EQ UI hook'u, WCAG/reduced-motion bütçesi (§1.4) |
| [[ADR-001-vanilla-js-itcss]] | Web katmanı sınırı — tarayıcı RT yoluna girmez (§1.4) |
| [[ADR-006-performance-targets]] | Ölçüm kapıları — `<10ms/<20ms`, `underrun = 0` (§1.4) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1-D) |
| [[ADR-023-persona-driven-testing]] | Debate/test disiplini — §5.3'ün usulü |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı + şablon zorunluluğu + UTF-8 tek arayüz (§1.4, §6) |
| [[../../architecture/k3-ses-motoru/dsp-chain]] | 15 aşama, `noexcept` (`:50-56`), EQ stage'leri (`:169-240`) — bu kararın sinyal zinciri kaynağı |
| [[../../architecture/k3-ses-motoru/eq-parametric]] | 31 bant frekansı (`:117-122`), katsayı (`:36-104`), preset (`:258-287`), performans hedefi (`:327-335`) |
| [[../../architecture/k3-ses-motoru/neva-engine-core]] | RT-safe + lock-free (`:12`), `bufferSize = 256` (`:329`), buffer pool (`:208`) |
| [[../../architecture/k3-ses-motoru/index]] | K3 katman indeksi — `:30, :114` EQ kayıtları |
| [[../../architecture/k10-uygulama/music-panel]] | **10-bant grafik EQ** (`:135-150`) — çelişki/alt küme (§1.1-C) |
| [[../../ui-design/flow/settings/03-equalizer]] | EQ ekranı, preset listesi, cihaz matrisi, hata durumları (§1.1-C) |
| [[../../.templates/adr/adr-template]] | İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16) |
| [[../../.templates/adr/adr-audio-template]] | Domain sözlüğü — `eq_bands/eq_gain/eq_q` (`:177-179`), gain stage (`:307-319`), yasaklı örüntüler (`:332-350`) |
| [[../../.templates/adr/adr-index]] | ADR şablon envanteri — `:96` ADR-025 satırı |

> **Kabul şartları (debate §5.3 → §5.5):** 3 şart bağlayıcı — (1) gain staging + zincir sırası (1a/1b), (2) frekans yanıt testi (±0.5 dB), (3) EQ UI grafik/parametrik ayrımı + preset paketi · debate usulü [[ADR-023-persona-driven-testing]] · wiki-link kuralı [[ADR-024-ecosystem-modular-docs]]

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar içeriği) |
| Tech Lead | — | 2026-09-25 | ✅ (debate §5.3 tamamlandı — 3 tur / 20 persona, 18/2/0 KABUL, 3 şart §5.5) |
| Arch Lead | — | 2026-09-25 | ⏳ PENDING |

---

**1.0.0 | 2026-09-25 | Created**

*ADR-025 — Professional EQ System (31-Band Grafik EQ + Parametrik EQ)*
*Authority: ADR-025 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
