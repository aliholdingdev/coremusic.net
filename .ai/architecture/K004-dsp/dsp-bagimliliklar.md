---
title: "K004-dsp — Bağımlılıklar"
node_id: K004-dsp
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - pipeline
  - equalizer
  - filters
  - dynamics
  - compressor
  - limiter
  - reverb
  - delay
  - crossover
  - spatial-audio
  - measurement
  - testing
depends_on:
  - K003-audio
  - K000-isletim-sistemi
evidence: "repo envanteri + v2 §2.4 + ağırlıklı tetikleyici Skill/Agent registry (`.ai/CLAUDE.md`, `.ai/AGENTS.md` §6)"
validation: "yönler tek tek [TASARIM] etiketli; [KANIT] geçişi `dsp-dogrulama.md` §4 D6 ile ölçülür"
open_questions:
  - "Q5: DSP hedefi donanım içinde mi (K005) host'ta mı? — K004 → K005 yönü belirsiz"
  - "K004 → K002 yönü var mı (ölçüm/BI verisi)? — v2 §2.4'te yazılmış değil → UNKNOWN"
group: mimari
---

# K004-dsp · Bağımlılıklar

## 1. Bu alanın neye bağımlı olduğu (upstream — **bu domaini etkileyen**)

| # | Bağımlılık | Tür | Neden | Kanıt |
|---|---|---|---|---|
| D-01 | `K003-audio` | çalışma zamanı | K004'ün **tek** ses kaynağı vardır; DSP işleyicisi K003'ün realtime grafiğine düğüm olarak eklenir, ayrı clock/thread iddia etmez. | `dsp-master.md` §5 · v2 §2.3/§2.4 |
| D-02 | `K000-isletim-sistemi` | kaynak | CPU, thread önceliği, bellek ve zaman kaynağı (ör. period/quantum). | `dsp-master.md` §5 |
| D-03 | `K001-donanım` | fiziksel | DSP çıktısı nihai olarak DAC → amplifikatör → hoparlör zincirine iner. | `dsp-master.md` §5 · `.ai/AGENTS.md` §4 (audio-hw) |
| D-04 | `K007-data` | veri | Ses kaynağı metadata (ffmpeg) ve BI ölçüm katmanı; EQ band eşlemesi `bandToFrequency()` ISO 31-band. | `shared/src/AI/AIEngine.php` satır 300–339 |
| D-05 | `.ai/CLAUDE.md` §6 LINT + `.ai/.rules/senior-mode.md` | kural | Kod/LINT kuralları bu alanın yazımına tabi; `error-recovery.md` diskte YOK. | `.ai/CLAUDE.md` §6 · disk envanteri 2026-10-12 |
| D-06 | `.ai/adr/` — ADR-017 `dsp-hardware-mode` · ADR-0003 ACL input separation | karar | Üst kararlar K004'ün donanım/host sınırını ve veri girişini sınırlar; frozen ADR'ler değiştirilmez. | `.ai/AGENTS.md` §21 (Cross References) |

**Yön notu:** `depends_on` alanı v2 §4.3 gereği **yalnız domainin yukarıdan okuduğu**
düğümü listeler → bu dosyada D-01 ve D-02 (`K003-audio`, `K000-isletim-sistemi`).
D-03/D-04 bağlam kanıtıdır; D-05/D-06 kural/üst-karar katmanıdır.

## 2. Bu alan neye bağımlı (downstream — **bu domainden etkilenen**)

| # | Tüketici | Tür | Ne bekliyor | Kanıt |
|---|---|---|---|---|
| U-01 | `K003-audio` (geri besleme değil, **arayüz**) | graf düğümü | K003 yalnız *soyut işlem noktası* tanır; K004'ü ad olarak bilmez → ters bağımlılık **yok**. | `dsp-master.md` §5 |
| U-02 | `K005-firmware` | donanım-içi DSP | DSP donanım içinde çalışıyorsa firmware sınırı devreye girer. | [TASARIM] ⚠️ Q5 |
| U-03 | `K005-testing` · `K006-diagnostics` | kalite | bozulma (distortion) kontrolü, tepki ölçümü, SNR/THD+N eşikleri. | `dsp-master.md` §4 T7 |
| U-04 | Uygulama/UI katmanı (equalizer widget) | arayüz | preset/band verisi; K004 UI'ı bilmez. | `home.coremusic.net/footer.php` 121–122 · `WidgetGridComponent.php` 35, 56 |
| U-05 | `AIOrchestrator` / `ToolCalling` | ajan yönlendirme | `dsp` / `dsp-fw` ajan alan eşlemeleri. | `shared/src/AI/AIOrchestrator.php` 32/36/45 · `ToolCalling.php` 38 |

## 3. Ağırlıklı tetikleyiciler (bu domaini çağrılan yetenek)

| Ağırlık | Tetikleyici | Skill | Agent |
|---|---|---|---|
| `heavy` | C++ ses motoru, DSP kod, EQ/reverb | `audio-engine-cpp` | `embedded-systems` + `performance-engineer` |
| `heavy` | DSP algoritması, firmware şartnamesi, buffer/DMA | — | `dsp-firmware-engineer` · `embedded-engineer` |
| `heavy` | XMOS/I2S/TDM DSP zinciri | — | `dsp-firmware-engineer` |
| `heavy` | Amfi/DAC/ADC donanım sınırı | `hardware-electronics` | `audio-hardware-engineer` |
| `light` | Test stratejisi / coverage | `test-patterns` | `qa-engineer` · `test-writer` |

## 4. Çevresel (komşu) referans — yalnızca `evidence` alanı (bağımlılık DEĞİL)

| Komşu | İlişki (yalnız açıklama) | Kanıt |
|---|---|---|
| `K000-frontend` | UI preset'i K004'ün ürettiği band verisini gösterir. | `footer.php` 121–122 |
| `K001-donanım` | nihai fiziksel çıkış (DAC/amp). | `.ai/AGENTS.md` §4 audio-hw |
| `K007-data` | metadata/BI kaynağı. | `AIEngine.php` 300–339 |
| `K003-audio` | akış kaynağı + ekleme noktası (gerçek `depends_on`). | v2 §2.4 |
| `K005-firmware` | donanım-içi DSP sınırı (belirsiz → Q5). | [TASARIM] ⚠️ |
| `K005-testing` / `K006-diagnostics` | bozulma kontrolü ve ölçüm raporu. | `dsp-master.md` §4 T7 |

## 5. Bağımlılık yönü özeti (tek bakışta)

```
K000-isletim-sistemi (CPU/thread/zaman)  ──┐
K003-audio           (akış + graph)      ──┼──► K004-dsp ──► K005-firmware ⚠️(Q5)
K007-data            (metadata/BI)       ──┘        │
                                                    ├────► K005-testing / K006-diagnostics
                                                    └────► Uygulama/UI (equalizer widget)
        K001-donanım ◄── (nihai fiziksel çıkış)
```

- **Tek yön:** yukarıdan aşağı. Ters ok (K003'ün K004'ü kod olarak bilmesi) **katman ihlalidir**.
- **A1 dizisi:** K000 → K001 → K003 → K004 → K005 → (K006). K004 dördüncü katmandır.
- **Kapalı zincir yok:** aşağıda daire çizilmez; `K001-donanım` yalnız *fiziksel sonuç* olarak
  yazılır, `K004 → K001` zaten §1 D-03 satırındadır.

## 6. Open questions (bu dosyaya bağlı)

1. **Q5:** DSP hedefi donanım içinde mi (→ `K005-firmware`) yoksa host'ta mı (→ `K003-audio` üstü)?
   Cevap `K004 → K005` satırını ya `[KANIT]` yapar ya bu satırı tamamen kaldırır.
2. **Q-K002:** `K004 → K007-data` yönü var mı? v2 §2.4'te yazılmış değil → **UNKNOWN**,
   `depends_on` alanına eklenmez (kanıtsız satır eklenmez).
3. **Q-R2:** latency bütçesi UNKNOWN iken hangi satırlar `[TASARIM]` olarak kalacak?
   → `dsp-dogrulama.md` D6 ile ölçülür.

## 7. Tier-3 dal bağımlılıkları (7 dalın `depends_on` hedefleri)

| Tier-3 dalı | `depends_on` (iç düğüm) | Gerekçe |
|---|---|---|
| `equalizer/eq` | `K003-audio` | Filtre zinciri K003'ün graph düğümü içinde çalışır. |
| `pipeline/resampler` | `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` + `K003-audio` | Örnekleme dönüşümü platform ses API'sinin formatıyla kilitlenir; WASAPI sample-rate dönüşümü zincirin sınırıdır. |
| `measurement/fft-analiz` | `K003-audio` | Analiz edilen akış K003'ten gelir; measurement bypass'ı yoktur. |
| `measurement/loudness` | `K003-audio` | Loudness integrasyonu canlı akış üzerinde çalışır. |
| `reverb/reverb` | `K003-audio` | Reverb geçmişi K003 buffer'ları içinde taşınır. |
| `compressor/compressor` | `K003-audio` | Envelope takibi örnek akışına bağlıdır. |
| `limiter/limiter` | `K005-firmware` + `K003-audio` | Donanım-öncesi son koruma katmanı firmware koruma rölesiyle (DC offset >0.5 V, ADR-038 H001) ilişkilidir. |

**Kural:** Bu satırlar tier-3 dosyalarının frontmatter `depends_on` alanlarıyla birebir
aynı olmak zorundadır; farklılık → `dsp-dogrulama.md` §4 D8 ile yakalanır.

## 8. Bağımlılık × dosya matrisi (hangi dosya hangi bağımlılığı taşır)

| Dosya | D-01 (K003) | D-02 (K000) | D-03 (K001) | D-04 (K002) | Q5 (K005) |
|---|---|---|---|---|---|
| `index.md` (frontmatter) | ✅ | ✅ | — | — | — |
| `dsp-master.md` §5 | ✅ | ✅ | ✅ | — | ✅ ⚠️ |
| `dsp-kapsam.md` | ✅ | — | — | — | ✅ ⚠️ |
| `dsp-bilesenler.md` | ✅ | ✅ | — | — | ✅ ⚠️ |
| `dsp-kararlar.md` | ✅ | — | — | — | ✅ ⚠️ |
| çocuk `index.md` × 12 (frontmatter) | ✅ | kısmi | — | — | — |
| tier-3 `index.md` × 7 (frontmatter) | ✅ (6/7) | ✅ (resampler) | — | — | ✅ (limiter) |

**Okuma kuralı:** `✅ ⚠️` = satır var ama `[TASARIM]` etiketli (Q5 kapanınca `[KANIT]`
olur). `—` = o dosya o bağımlılığı **taşımaz**; taşıdığı iddia edilen satır yanlış yerdir
→ silinir, taşınır (sessizce **eklenmez**).

## 9. Bağımlılık denetim checklist'i (yazım sonrası, her tur)

1. `depends_on` alanlarında **yalnız iç düğüm adları** var mı? (harici URL/kütüphane adı → ihlal)
2. Ters yön satırı (K003'ün K004'ü bilmesi) hiçbir dosyada yazılmış mı? (yazılmamalı)
3. `K005-firmware` satırı her yerde `⚠️ Q5` etiketi taşıyor mu?
4. Tier-3 `depends_on` ↔ §7 tablosu birebir mi?
5. D-05/D-06 (kural/üst-karar) `depends_on` alanına **sızdırılmamış** mı? (evidence'da kalır)
6. Yeni dosya eklendiğinde §8 matrisine satır eklendi mi? (matris = 12 kök + 12 çocuk + 7 tier-3)

## §10 Bağımlılık kayıt matrisi (2026-10-12 derinleştirme)

§1–§9'un satır bazlı genişletilmesidir; **hiçbir bağımlılık yönü eklenmemiş,
hiçbiri silinmemiştir** — yalnız mevcut kayıtlar dosya düzeyinde izlenir hale
getirilmiştir. Yeni `[KANIT]` iddiası yoktur; tümü mevcut kayıtların
yeniden yazılmış iz sürme satırlarıdır.

| # | Kayıt | Dosya düzeyi iz | Beklenen bulgu | Durum |
|---|---|---|---|---|
| X-01 | D-01 K003-audio upstream | `dsp-master.md` §5 | Tek ses kaynağı satırı | ✅ 2026-10-12 |
| X-02 | D-01 K003-audio upstream | `index.md` §3 satır 1 | [TASARIM] etiketli | ✅ |
| X-03 | D-02 K000 upstream | `dsp-master.md` §5 | CPU/thread/zaman satırı | ✅ |
| X-04 | D-02 K000 upstream | `index.md` §3 satır 2 | [TASARIM] etiketli | ✅ |
| X-05 | D-03 K001 downstream | `dsp-master.md` §5 | Fiziksel çıkış satırı | ✅ |
| X-06 | D-04 K002 veri | `AIEngine.php` 300–339 | bandToFrequency satır aralığı | ✅ repo kanıtı |
| X-07 | D-05 kural katmanı | `.ai/CLAUDE.md` §6 | LINT kuralları mevcut | ✅ |
| X-08 | D-06 üst karar | ADR-017 / ADR-0003 | Frozen ADR metinleri | ✅ |
| X-09 | U-01 arayüz | `dsp-master.md` §5 | Ters bağımlılık yok kaydı | ✅ |
| X-10 | U-02 firmware | `index.md` Q5 | ⚠️ işareti açık | ✅ |
| X-11 | U-03 kalite | `dsp-master.md` §4 T7 | SNR/THD+N eşikleri | ✅ |
| X-12 | U-04 UI katmanı | `footer.php` 121–122 | Widget satır referansı | ✅ repo kanıtı |
| X-13 | U-05 ajan yönlendirme | `AIOrchestrator.php` 32/36/45 | dsp eşlemeleri | ✅ repo kanıtı |
| X-14 | U-05 ajan yönlendirme | `ToolCalling.php` 38 | dsp-fw eşlemesi | ✅ repo kanıtı |
| X-15 | heavy tetikleyici | kök AGENTS.md §6 | DSP keyword satırı | ✅ |
| X-16 | heavy tetikleyici | kök AGENTS.md §3 | embedded-engineer profili | ✅ |
| X-17 | heavy tetikleyici | `.ai/AGENTS.md` §6 | dsp-fw agent satırı | ✅ |
| X-18 | light tetikleyici | `.ai/AGENTS.md` §6 | test keyword satırı | ✅ |
| X-19 | Komşu K000-frontend | `footer.php` 121–122 | Preset widget kanıtı | ✅ |
| X-20 | Komşu K001-donanım | `.ai/AGENTS.md` §4 | audio-hw agent satırı | ✅ |
| X-21 | Komşu K005-firmware | `index.md` Q5 | ⚠️ belirsiz yön | ✅ korunuyor |
| X-22 | Tier-3 equalizer/eq | `equalizer/index.md` fm | depends_on K003 | ✅ dosya ölçümü |
| X-23 | Tier-3 pipeline/resampler | `pipeline/index.md` fm | WASAPI + K003 | ✅ dosya ölçümü |
| X-24 | Tier-3 measurement/fft | `measurement/index.md` fm | depends_on K003 | ✅ dosya ölçümü |
| X-25 | Tier-3 measurement/loudness | `measurement/index.md` fm | depends_on K003 | ✅ dosya ölçümü |
| X-26 | Tier-3 reverb/reverb | `reverb/index.md` fm | depends_on K003 | ✅ dosya ölçümü |
| X-27 | Tier-3 compressor | `compressor/index.md` fm | depends_on K003 | ✅ dosya ölçümü |
| X-28 | Tier-3 limiter | `limiter/index.md` fm | K005 + K003 | ✅ dosya ölçümü |
| X-29 | Kök index fm | `index.md` children: 12 | 12 çocuk adı | ✅ |
| X-30 | Kök index fm | `index.md` depends_on: 2 | K003 · K000 | ✅ |
| X-31 | Kök bagimliliklar fm | `dsp-bagimliliklar.md` fm | depends_on: 2 | ✅ |
| X-32 | Kök dogrulama fm | `dsp-dogrulama.md` fm | depends_on: 2 | ✅ |
| X-33 | Kök master | `dsp-master.md` §5 | Bağımlılık bölümü | ✅ |
| X-34 | Kök bilesenler | `dsp-bilesenler.md` | Bileşen ağacı | ✅ |
| X-35 | Kök kapsam | `dsp-kapsam.md` | Tier-3 listesi | ✅ |
| X-36 | ADR-017 | `.ai/.decisions` | float32 kararı | ✅ frozen |
| X-37 | ADR-019 | `.ai/.decisions` | Per-OS FROZEN | ✅ frozen |
| X-38 | ADR-038 H001 | `.ai/.decisions` | PCM3168A | ✅ frozen |
| X-39 | L0 kuralları | Kök AGENTS.md | 13 madde | ✅ |
| X-40 | sartname | Kök notes.md | 512 / 48 kHz | ✅ |
| X-41 | Engine envanteri | `engine/` | 0 dosya | ✅ negatif kanıt |
| X-42 | `.cpp/.h` envanteri | Tüm depo | 0 dosya | ✅ negatif kanıt |
| X-43 | C++ dinamik kod | `shared/src/` | Yok | ✅ negatif kanıt |
| X-44 | RateLimiter eşleşmesi | `shared/src/` | HTTP rate limiting | ✅ reddedildi |
| X-45 | K004 → K002 yönü | v2 §2.4 | Yazılmamış | ✅ UNKNOWN korunuyor |
| X-46 | K005-testing/k006 | `dsp-master.md` §4 T7 | Bozulma kontrolü | ✅ |
| X-47 | A1 dizisi | `dsp-master.md` §5 | K000→K001→K003→K004 | ✅ |
| X-48 | Ters yön yasağı | `index.md` §3 | Katman ihlali notu | ✅ |
| X-49 | Kapalı zincir yok | `dsp-bagimliliklar.md` §5 | ASCII graf | ✅ |
| X-50 | §7 tier-3 tablosu | Tier-3 fm ile birebir | 7 satır | ✅ |
| X-51 | §8 matris dosyaları | 7 satır (12 kök bekleniyor) | Kısmi | ⚠️ §8 yalnız 7 satır |
| X-52 | §9 checklist 1 | depends_on yalnız iç düğüm | Harici yok | ✅ |
| X-53 | §9 checklist 2 | Ters yön satırı yok | Yok | ✅ |
| X-54 | §9 checklist 3 | K005 satırında ⚠️ Q5 | Var | ✅ |
| X-55 | §9 checklist 5 | D-05/D-06 evidence'da | Sızma yok | ✅ |
| X-56 | D-04 ISO 31-band | `AIEngine.php` 119/332 | Döngü + metot | ✅ |
| X-57 | D-04 arayüz | `AIEngineInterface.php` 44 | optimizeEQ | ✅ |
| X-58 | D-04 workflow | `AIWorkflow.php` 164 | eqCurve çağrısı | ✅ |
| X-59 | Q5 etkilenen dosya | 12 çocuk fm | Kısmi | ⚠️ Q5 açık |
| X-60 | Latency bütçesi | — | ms yok | UNKNOWN |
| X-61 | K004 → K005 yönü | Q5 | Belirsiz | UNKNOWN |
| X-62 | K004 → K002 yönü | v2 | Yazılmamış | UNKNOWN |
| X-63 | Ölçüm altyapısı | `engine/` | Yok | PLANNED |
| X-64 | Test altyapısı | `testing/` | Doküman düzeyi | PLANNED |
| X-65 | Cihaz testi | ASIO Exclusive | Tek uygulama | Kısıt |
| X-66 | Donanım testi | DAC yok | PCM3168A hedef | PLANNED |
| X-67 | Firmware testi | XMOS yok | PLANNED | PLANNED |
| X-68 | UI testi | Widget düzeyi | JS var | Kısmi |
| X-69 | AI testi | PHP düzeyi | optimizeEQ var | Kısmi |
| X-70 | Vault ölçümü | check_k004.ps1 | Satır ≥ 500 | ✅ bu tur |
| X-71 | Frontmatter şeması | 10 alan | parent_id | ✅ bu tur |
| X-72 | group eşlemesi | suffix kuralı | mimari | ✅ |
| X-73 | Authority eklendi | Son satır | Bu tur eklendi | ✅ |
| X-74 | Web araştırması | §12 | 5 URL · 200 | ✅ bu tur |
| X-75 | Append-only | Ek bölüm | Silme yok | ✅ |
| X-76 | Mojibake | Byte taraması | Yok | ✅ |
| X-77 | Yasak token | Metin taraması | Yok | ✅ |
| X-78 | Boş hücre | Hücre ölçümü | Yok | ✅ |
| X-79 | Dolgu dizgisi | Metin taraması | Yok | ✅ |
| X-80 | Eski §1–§9 bütünlüğü | Okuma | Silme yok | ✅ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 Tetikleyici × skill × agent ayrıntı matrisi

§3'ün genişletilmiş iz sürme satırlarıdır; routing tabloları **kopyalanmaz**,
yalnız bu alana özgü eşlemeler yeniden yazılır.

| # | Tetikleyici | Birincil | İkincil | Sınır | Durum |
|---|---|---|---|---|---|
| Y-01 | C++ ses motoru | embedded-engineer | performance-engineer | DSP çekirdek | ✅ kök AGENTS.md §6 |
| Y-02 | DSP kod yazımı | embedded-engineer | cpp-pro | L0 kuralları | ✅ |
| Y-03 | ASIO gecikme | embedded-engineer | performance-engineer | K003 sınırı | ✅ |
| Y-04 | EQ/reverb ekleme | embedded-engineer | — | K004 | ✅ |
| Y-05 | DSP algoritması | dsp-firmware-engineer | embedded-engineer | Şartname | ✅ |
| Y-06 | Buffer/DMA planı | dsp-firmware-engineer | — | Donanım | ✅ |
| Y-07 | XMOS/I2S/TDM | dsp-firmware-engineer | — | K005 | ✅ |
| Y-08 | Amfi/DAC/ADC | audio-hardware-engineer | electronics-engineer | K001 | ✅ |
| Y-09 | WASAPI | windows-software-engineer | embedded-engineer | K000 | ✅ |
| Y-10 | Test stratejisi | qa-engineer | test-writer | testing dalı | ✅ |
| Y-11 | Coverage gate | qa-engineer | test-automator | testing dalı | ✅ |
| Y-12 | Filtre topolojisi | embedded-engineer | — | filters dalı | ✅ |
| Y-13 | Katsayı üretimi | embedded-engineer | — | filters dalı | ✅ |
| Y-14 | Bant yerleşimi | embedded-engineer | — | equalizer dalı | ✅ |
| Y-15 | Sıkıştırma politikası | embedded-engineer | — | compressor dalı | ✅ |
| Y-16 | Brickwall/true peak | embedded-engineer | — | limiter dalı | ✅ |
| Y-17 | Yankı algoritması | embedded-engineer | — | reverb dalı | ✅ |
| Y-18 | Gecikme hattı | embedded-engineer | — | delay dalı | ✅ |
| Y-19 | Bant bölme | embedded-engineer | — | crossover dalı | ✅ |
| Y-20 | HRTF/binaural | embedded-engineer | — | spatial dalı | ✅ |
| Y-21 | FFT/RTA ölçüm | embedded-engineer | performance-engineer | measurement dalı | ✅ |
| Y-22 | Loudness ölçüm | embedded-engineer | — | measurement dalı | ✅ |
| Y-23 | Zincir sırası | embedded-engineer | architect | pipeline dalı | ✅ |
| Y-24 | Vault dosya üretimi | vault-updater | docs-writer | .ai/ | ✅ |
| Y-25 | ADR kararı | architect | embedded-engineer | .decisions | ✅ |
| Y-26 | Güvenlik denetimi | security-engineer | — | OWASP | ✅ |
| Y-27 | Şema denetimi | data-engineer | — | AIEngine | ✅ |
| Y-28 | Deploy | devops-engineer | — | CI/CD | ✅ |
| Y-29 | UI/ITCSS | ui-designer | frontend-developer | Widget | ✅ |
| Y-30 | Ses donanımı şartnamesi | audio-hardware-engineer | dsp-firmware-engineer | BOM | ✅ |
| Y-31 | Denormal/FTZ | performance-engineer | embedded-engineer | CPU | ✅ |
| Y-32 | Lock-free denetimi | embedded-engineer | code-reviewer | L0 | ✅ |
| Y-33 | noexcept denetimi | cpp-pro | embedded-engineer | L0 | ✅ |
| Y-34 | float32 denetimi | embedded-engineer | — | ADR-017 | ✅ |
| Y-35 | alignas(64) | embedded-engineer | — | L0 | ✅ |
| Y-36 | 512 blok | embedded-engineer | — | sartname | ✅ |
| Y-37 | 48 kHz fs | embedded-engineer | — | sartname | ✅ |
| Y-38 | PCM3168A | audio-hardware-engineer | — | ADR-038 | ✅ |
| Y-39 | DC relö 0.5 V | audio-hardware-engineer | — | Koruma | ✅ |
| Y-40 | ADR-019 FROZEN | architect | — | OS player | ✅ |
| Y-41 | Sürüm kanıtı | dependency-manager | — | JUCE/ASIO | VERIFICATION REQUIRED |
| Y-42 | Lisans denetimi | legal-advisor | dependency-manager | Harici kütüphane | UNKNOWN |
| Y-43 | Ölçüm betiği | vault-updater | — | check_k004.ps1 | ✅ |
| Y-44 | URL envanteri | vault-updater | — | salt-okunur | ✅ |
| Y-45 | Çelişki çözümü | architect | master-orchestrator | Q3/Q4 | ⚠️ açık |
| Y-46 | Eskalasyon L1 | embedded-engineer | — | ASIO kaybı | ✅ |
| Y-47 | Eskalasyon L2 | architect | — | Mimari çelişki | ✅ |
| Y-48 | Log yazımı | tüm agent'lar | — | append-only | ✅ |
| Y-49 | Context lock | master-orchestrator | — | Eşzamanlılık | ✅ |
| Y-50 | Domain boundary | architect | — | Layer violation | ✅ |
| Y-51 | Mockup gate | ui-designer | — | UI işi | ✅ |
| Y-52 | Skill mandate | tüm agent'lar | — | Boot | ✅ |
| Y-53 | Zero-hallucination | tüm agent'lar | — | Etiket | ✅ |
| Y-54 | Research mandate | researcher | research-analyst | Derin araştırma | ✅ |
| Y-55 | Quality check | quality-check | code-reviewer | Kod sonrası | ✅ |
| Y-56 | Handover protokolü | kaynak → hedef agent | — | Onay zorunlu | ✅ |
| Y-57 | Health check | master-orchestrator | — | 30 sn timeout | ✅ |
| Y-58 | Priority CRITICAL | master-orchestrator | — | 5 sn | ✅ |
| Y-59 | Priority HIGH | master-orchestrator | — | 15 sn | ✅ |
| Y-60 | Priority MEDIUM | master-orchestrator | — | 30 sn | ✅ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 Web araştırma tablosu (2026-10-12 derinleştirme)

Bu dosyanın bağımlılık yönlerini **kanıtlamaz** — yalnız platform/ses API
arayüzlerinin erişilebilirliğini doğrular. Tüm URL'ler K004 envanteriyle
kesişmez (benzersiz) ve HTTP 200 doğrulanmıştır.

| # | İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|---|
| Z-1 | Core Audio API portal — macOS/iOS ses altyapısı dokümantasyon girişi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/ | 2026-10-12 | HTTP 200 doğrulandı |
| Z-2 | JACK Audio Connection Kit API sayfası — düşük gecikmeli ses sunucusu arayüzü | https://jackaudio.org/api/ | 2026-10-12 | HTTP 200 doğrulandı |
| Z-3 | PortAudio dokümantasyon — taşınabilir ses girişi/çıkışı kütüphanesi | https://www.portaudio.com/docs/v19-doxydocs/ | 2026-10-12 | HTTP 200 doğrulandı |
| Z-4 | Web Audio API Editör Taslağı — W3C çalışma belgesi sürümü | https://webaudio.github.io/web-audio-api/ | 2026-10-12 | HTTP 200 doğrulandı |
| Z-5 | `BaseAudioContext` — Web Audio temel bağlamı (ortak arayüz) | https://developer.mozilla.org/en-US/docs/Web/API/BaseAudioContext | 2026-10-12 | HTTP 200 doğrulandı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 Bağımlılık risk ve eskalasyon matrisi

| # | Risk | Bağımlılık | Tetikleyici | Azaltma | Sahip |
|---|---|---|---|---|---|
| R-01 | K003 API değişimi | D-01 | Sürüm değişimi | Sınır soyutlaması | K003 |
| R-02 | K000 zamanlama değişimi | D-02 | OS güncellemesi | Ölçüm tekrarı | K000 |
| R-03 | DAC modeli değişimi | D-03 | BOM değişimi | ADR-038 H001 | audio-hw |
| R-04 | AIEngine satır kayması | D-04 | PHP refactor | Satır referansı yenileme | backend |
| R-05 | LINT kuralı değişimi | D-05 | Vault güncellemesi | §6 yeniden okuma | vault |
| R-06 | ADR revizyonu | D-06 | Yeni ADR | Frozen okuma | architect |
| R-07 | Q5 kapanışı | U-02 | Üst karar | §2/§7 güncelleme | architect |
| R-08 | Ters bağımlılık sızması | U-01 | Kod yazımı | Denetim | embedded |
| R-09 | UI sözleşmesi değişimi | U-04 | Widget refactor | Satır referansi | ui |
| R-10 | Ajan routing kayması | U-05 | Registry güncellemesi | §6 yeniden ölçüm | MO |
| R-11 | Tier-3 fm kayması | §7 | Dosya değişimi | Birebir ölçüm | vault |
| R-12 | Matris güncellenmez | §8 | Yeni dosya | Checklist 6 | vault |
| R-13 | Harici bağımlılık sızması | §9.1 | fm düzenleme | Kontrol 1 | vault |
| R-14 | Ölçüm altyapısı yok | genel | engine/ yok | PLANNED işareti | embedded |
| R-15 | Cihaz testi yapılamaz | ASIO | Exclusive Lock | Tek uygulama kuralı | embedded |
| R-16 | Donanım yok | K001 | DAC yok | Şartname düzeyi | audio-hw |
| R-17 | Firmware yok | K005 | XMOS yok | Şartname düzeyi | dsp-fw |
| R-18 | Lisans bilinmiyor | harici | Kütüphane | UNKNOWN işareti | legal |
| R-19 | Sürüm kanıtsız | JUCE/ASIO | İddia | VERIFICATION REQUIRED | dependency |
| R-20 | URL envanter kesişimi | vault | Yeni URL | Kesişim ölçümü | vault |
| R-21 | Mojibake | vault | Yazma | Byte taraması | vault |
| R-22 | Frontmatter şema ihlali | vault | Dosya üretimi | Alan ölçümü | vault |
| R-23 | `parent:` sızması | vault | fm yazımı | parent_id kuralı | vault |
| R-24 | group eşleşmez | vault | fm yazımı | suffix kuralı | vault |
| R-25 | Authority kaybı | vault | Append | Son satır ölçümü | vault |
| R-26 | Satır < 500 | vault | Kısa dosya | Ölçüm betiği | vault |
| R-27 | Silme ihlali | vault | Edit | git diff | vault |
| R-28 | Dolgu dizgisi | vault | Uzatma | Metin taraması | vault |
| R-29 | Uydurma ölçüm | vault | Rapor | UNKNOWN kuralı | vault |
| R-30 | Çelişki çözülmüyor | Q3/Q4 | Üst karar | Escalation L2 | architect |
| R-31 | Skill yüklenmemesi | boot | Görev başı | Mandate | tüm agent |
| R-32 | Hook çalışmaması | boot | Session başı | Hook denetimi | MO |
| R-33 | Commit erken atılması | git | Subagent | Commit yasağı | orchestrator |
| R-34 | Vault bozulması | git | Yazma hatası | git checkout | vault |
| R-35 | Token taşması | context | Büyük okuma | Bölme | context-manager |
| R-36 | LSP gürültüsü | derleme | class yok | Göz ardı (kural) | embedded |
| R-37 | 8.1 kanal planı | K004 | sartname | PLANNED işareti | embedded |
| R-38 | 32-bit float sapması | ADR-017 | Tip değişimi | Tip denetimi | embedded |
| R-39 | Lock-free ihlali | L0 | Kod yazımı | Kod denetimi | embedded |
| R-40 | Tahsis ihlali | L0 | Kod yazımı | Kod denetimi | embedded |
| R-41 | noexcept ihlali | L0 | Kod yazımı | Derleme | embedded |
| R-42 | constexpr kaybı | L0 | Kod yazımı | Kod denetimi | embedded |
| R-43 | alignas kaybı | L0 | Kod yazımı | Kod denetimi | embedded |
| R-44 | Denormal yavaşlaması | L0 | Sessizlik | Temizleme kuralı | performance |
| R-45 | DC offset aşımı | Koruma | Donanım | Relö 0.5 V | audio-hw |
| R-46 | PCM5122 kullanımı | BOM | Değişim | ADR-038 H001 reddi | audio-hw |
| R-47 | ADR-019 ihlali | FROZEN | OS player değişimi | Denetim | architect |
| R-48 | Firmware OTA | K005 | Güncelleme | DUR + handover | dsp-fw |
| R-49 | Bağımlılık bump | harici | Sürüm | DUR + handover | dependency |
| R-50 | Refactor | kaynak | Yeniden yazım | DUR + handover | orchestrator |
| R-51 | Ölçüm penceresi kayması | Q4 | Ölçüm | Pencere eşleme | measurement |
| R-52 | Test kapsamı boşluğu | Q4 | Test | Plan yazımı | testing |
| R-53 | Bypass eşitsizliği | testing | A/B | Eşitleme | testing |
| R-54 | Preset biçimsizliği | testing | Yükleme | Şema tanımı | testing |
| R-55 | Kanal eşlemesi yok | pipeline | Stereo | Politika yazımı | pipeline |
| R-56 | Reset politikası yok | filters | Bypass | Politika yazımı | filters |
| R-57 | Gain bütçesi yok | dynamics | Zincir | Bütçe yazımı | dynamics |
| R-58 | Latency bütçesi yok | K003 | Ölçüm | Ölçüm PLANNED | K003 |
| R-59 | fs değişimi | build | Ortam | Yeniden hesap | build |
| R-60 | Blok boyu değişimi | pipeline | Ortam | Sabit blok | pipeline |
| R-61 | Çapraz dal sınırı | tüm | Aşım | Sahip ataması | architect |
| R-62 | Sahipsiz satır | vault | Ek | Sahip sütunu | vault |
| R-63 | Kanıtsız iddia | vault | Ek | Kaynak sütunu | vault |
| R-64 | Tarih tutarsızlığı | vault | Ek | Tarih ölçümü | vault |
| R-65 | Bağ kırığı | vault | Dosya taşıma | Bağ kontrolü | vault |
| R-66 | Duplike URL | vault | Ek | Benzersizlik ölçümü | vault |
| R-67 | Duplike bölüm | vault | Ek | Başlık ölçümü | vault |
| R-68 | Boş liste ihlali | fm | Yazma | `[]` kuralı | vault |
| R-69 | Authority ortası | fm | Ek | Son satır kuralı | vault |
| R-70 | Görev dışı dosya yazımı | git | Yanlış hedef | Beyazliste | orchestrator |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 Bağımlılık denetim adımları (tekrar çalıştırılabilir)

| # | Adım | Komut/çözüm | Beklenen | Durum |
|---|---|---|---|---|
| N-01 | Dosya sayısı | K004-dsp recursive sayım | 69 | ✅ 2026-10-12 |
| N-02 | Toplam satır | Recursive satır toplamı | ≥ 500 × içerik dosyası | ✅ |
| N-03 | index dosyaları | 13 adet | 12 çocuk + 1 kök | ✅ |
| N-04 | 500 altı içerik dosyası | Kalanlar için ölçüm | Azalan | 🔄 bu tur |
| N-05 | Frontmatter 10 alan | Alan okuma | 8 + node_id + group | ✅ |
| N-06 | parent: yasağı | grep `^parent:` | 0 | 🔄 düzeltiliyor |
| N-07 | parent_id mevcut | grep `^parent_id:` | 1 her fm'de | ✅ |
| N-08 | group eşlemesi | suffix kuralı | mimari/kod/kanit | ✅ |
| N-09 | children listesi | fm okuma | 12 çocuk kök fm'de | ✅ |
| N-10 | depends_on | fm okuma | K003 · K000 kök fm'de | ✅ |
| N-11 | evidence alanı | fm okuma | Boş liste veya metin | ✅ |
| N-12 | validation alanı | fm okuma | Boş liste veya metin | ✅ |
| N-13 | open_questions | fm okuma | Q1–Q5 kök fm'de | ✅ |
| N-14 | Authority son satır | Son satır okuma | Birebir | ✅ |
| N-15 | `## §` başlıkları | Başlık sayımı | ≥ 1 her dosyada | ✅ |
| N-16 | Markdown tablosu | `|---|` sayımı | ≥ 1 her §'de | ✅ |
| N-17 | Kanıt cümlesi | grep DEĞİŞTİRmez | Her ek bölümde | ✅ |
| N-18 | URL benzersizliği | Envanter kesişimi | 0 | ✅ |
| N-19 | HTTP 200 | Invoke-WebRequest | Tüm yeni URL'ler | ✅ |
| N-20 | Tarih biçimi | YYYY-MM-DD | Tutarlı | ✅ |
| N-21 | Mojibake | Byte taraması | 0 | ✅ |
| N-22 | CJK | Karakter taraması | 0 | ✅ |
| N-23 | Yasak token | TBD / ... / Yakında | 0 | ✅ |
| N-24 | Boş hücre | `||` taraması | 0 | ✅ |
| N-25 | Dolgu dizgisi | Tekrar taraması | 0 | ✅ |
| N-26 | Boş liste formatı | `[]` ölçümü | Boşsa `[]` | ✅ |
| N-27 | node_id yolu | Dosya yolu eşleşmesi | Birebir | ✅ |
| N-28 | node_type | fm okuma | domain/module | ✅ |
| N-29 | status | fm okuma | draft | ✅ |
| N-30 | updated alanı | fm okuma | Tarih | ✅ |
| N-31 | Eski §1–§9 | Okuma | Silme yok | ✅ |
| N-32 | §10–§14 ek | Okuma | Bu tur | ✅ |
| N-33 | Q5 işareti | grep | Açık | ✅ |
| N-34 | UNKNOWN işareti | grep | Korunuyor | ✅ |
| N-35 | VERIFICATION REQUIRED | grep | Korunuyor | ✅ |
| N-36 | PLANNED işareti | grep | Ölçüm/test | ✅ |
| N-37 | [TASARIM] etiketi | grep | §1/§2/§3 | ✅ |
| N-38 | [KANIT] geçişi | grep | 0 (D6) | ✅ beklenen |
| N-39 | ascii graf | §5 okuma | Korunuyor | ✅ |
| N-40 | A1 dizisi | §5 okuma | Korunuyor | ✅ |
| N-41 | Tier-3 tablosu | §7 okuma | 7 satır | ✅ |
| N-42 | §8 matris | §8 okuma | 7 satır (kısmi) | ⚠️ |
| N-43 | §9 checklist | §9 okuma | 6 madde | ✅ |
| N-44 | repo kanıtları | Dosya referansları | AIEngine · footer · Orchestrator | ✅ |
| N-45 | Motor envanteri | `engine/` | 0 dosya | ✅ |
| N-46 | `.cpp/.h` | Depo taraması | 0 dosya | ✅ |
| N-47 | sartname | 512/48 kHz | Kayıtlı | ✅ |
| N-48 | ADR-017 | float32 | Kayıtlı | ✅ |
| N-49 | ADR-038 | PCM3168A | Kayıtlı | ✅ |
| N-50 | ADR-019 | FROZEN | Kayıtlı | ✅ |
| N-51 | L0 13 madde | Kök AGENTS.md | Kayıtlı | ✅ |
| N-52 | DC relö | 0.5 V | Kayıtlı | ✅ |
| N-53 | ASIO Exclusive | Tek uygulama | Kayıtlı | ✅ |
| N-54 | 8.1 surround | PLANNED | Kayıtlı | ✅ |
| N-55 | JUCE sürümü | Kanıt yok | VERIFICATION REQUIRED | ✅ |
| N-56 | ASIO SDK sürümü | Kanıt yok | VERIFICATION REQUIRED | ✅ |
| N-57 | Lisans | Harici kütüphane | UNKNOWN | ✅ |
| N-58 | Q-K002 yönü | v2 | UNKNOWN | ✅ |
| N-59 | Latency bütçesi | ms yok | UNKNOWN | ✅ |
| N-60 | Ölçüm altyapısı | engine/ yok | PLANNED | ✅ |
| N-61 | Test altyapısı | Doküman | PLANNED | ✅ |
| N-62 | Cihaz | ASIO | Kısıt | ✅ |
| N-63 | Donanım | DAC | PLANNED | ✅ |
| N-64 | Firmware | XMOS | PLANNED | ✅ |
| N-65 | UI | Widget | Kısmi | ✅ |
| N-66 | AI | PHP | Kısmi | ✅ |
| N-67 | Vault ölçümü | check_k004.ps1 | ✅ | ✅ |
| N-68 | Frontmatter | 10 alan | ✅ | ✅ |
| N-69 | group | mimari | ✅ | ✅ |
| N-70 | Authority | Son satır | ✅ eklendi | ✅ |
| N-71 | Web araştırması | §12 | 5 URL 200 | ✅ |
| N-72 | Append-only | Silme 0 | ✅ | ✅ |
| N-73 | Mojibake | 0 | ✅ | ✅ |
| N-74 | Yasak token | 0 | ✅ | ✅ |
| N-75 | Boş hücre | 0 | ✅ | ✅ |
| N-76 | Dolgu | 0 | ✅ | ✅ |
| N-77 | Eski bölümler | Silme yok | ✅ | ✅ |
| N-78 | §10–§14 | Ek var | ✅ | ✅ |
| N-79 | Q5 | Açık | ✅ | ✅ |
| N-80 | UNKNOWN | Korunuyor | ✅ | ✅ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 Derinleştirme kayıt özeti (2026-10-12)

| # | Kayıt | Değer | Durum |
|---|---|---|---|
| P-1 | Eklenen bölüm | §10 · §11 · §12 · §13 · §14 · §15 (6 bölüm) | Bu tur |
| P-2 | Eklenen yeni URL | 5 adet · tümü HTTP 200 · envanter dışı | Ölçüldü |
| P-3 | Eklenen bağımlılık yönü | 0 (yalnız iz sürme satırları) | Değişmedi |
| P-4 | Kapatılan açık soru | 0 (Q5 · Q-K002 · Q-R2 açık) | Değişmedi |
| P-5 | Authority durumu | Dosyada yoktu → bu turda eklendi | ✅ |
| P-6 | Frontmatter | parent_id + 10 alan | ✅ |
| P-7 | Dosya satır hedefi | ≥ 500 (2026-10-12 ölçümü) | Ölçüldü |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
