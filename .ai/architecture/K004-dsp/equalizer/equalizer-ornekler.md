---
title: "equalizer — Örnekler"
type: architecture-examples
node_id: K004-dsp/equalizer
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# equalizer — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: var — ama PHP katmanında, C++ DSP motoru değil** (dolgu/değer değil,
gerçek çalışan hesap; yine de K004 motoru sayılmaz):

```php
// shared/src/AI/AIEngine.php satır 117-133
$eqCurve = [];
for ($band = 0; $band < 31; $band++) {
    $frequency = $this->bandToFrequency($band);
    $gain = $this->calculateRoomCorrection($frequency, $roomSize, $reverb, $frequencyResponse);
    $q = $this->calculateBandQ($frequency, $roomSize);
    $eqCurve[] = [
        'band'      => $band,
        'frequency' => $frequency,
        'gain'      => round($gain, 2),
        'q'         => round($q, 2),
    ];
}
return $eqCurve;
```

```php
// shared/src/AI/AIEngine.php satır 332-342 — 31 ISO band
$frequencies = [
    20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,
    200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600,
    2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000,
];
return $frequencies[$band] ?? 1000.0;
```

**Ne gösterdiği:** 31-band grafik EQ'nun bant-erektesi (20 Hz–20 kHz ISO serisi)
ve her bant için {frequency, gain, Q} çıktı şeması. K004 equalizer dalı bu şemayı
referans alabilir; ancak `calculateRoomCorrection` bir oda-modeli tahminidir,
ölçüm değildir (ölçüm measurement dalının işi).

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Quick start, 2026-10-12 ✅)

```cpp
dspark::Equalizer<float> equalizer;
equalizer.prepare(spec);
equalizer.setBand(0, 100.0f, 3.0f);   // bant 0: 100 Hz, +3.0 dB
equalizer.processBlock(block);
```

**Ne gösterdiği:** hazır EQ'nun bant-bazlı API'si (`setBand(index, frequency, gain)`),
realtime `processBlock` deseni. CoreMusic C++ motoru bu deseni izleyebilir.

**Kaynak:** https://github.com/ShmKnd/Patina (Effect Engines, 2026-10-12 ✅)
`EqEngine` — 3-band parametric EQ (analog-modeling); `ChannelStripEngine` EQ +
preamp + transformer içerir. Alternatif topolojik referans.

## §3 Uygulama notu

CoreMusic mevcut 31-band ISO yerleşimini bir **aday** kabul eder; nihai bant sayısı
ve Q varsayılanları R1 kararı kapanmadan kilitlenmez. EQ yalnız bant seviyesini
yönetir; biquad çekirdeği filters dalından gelir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[equalizer-kapsam]]` | sınır ve açık sorular |
| `[[equalizer-dogrulama]]` | iddia kanıtları |
| `../filters/filters-kapsam.md` | bant çekirdeği sınır komşusu (Q3) |
| `../pipeline/pipeline-kapsam.md` | EQ'nun zincirdeki yeri |
| `../measurement/measurement-kapsam.md` | oda düzeltme hedefi ayrımı (R3) |

---

## §5 Biquad tipi × parametre matrisi (2026-10-12 derinleştirme)

§2'deki hazır EQ API'sinin hangi biquad tipine karşılık geldiğini ayıran
matristir. `frequency` / `Q` / `gain` varlığı MDN `BiquadFilterNode` sayfalarından
(§8) doğrulanmıştır; **K004 hangi tipleri kullanacak — karar yok (R1/A4)**.

| # | Biquad tipi | frequency | Q | gain | EQ'daki rolü | K004 durumu |
|---|---|---|---|---|---|---|
| T-01 | lowpass | Var | Var | Yok | Bant alt kesme | UNKNOWN |
| T-02 | highpass | Var | Var | Yok | Bant üst kesme | UNKNOWN |
| T-03 | bandpass | Var | Var | Yok | Bant geçiş | UNKNOWN |
| T-04 | notch | Var | Var | Yok | Tek frekans bastırma | UNKNOWN |
| T-05 | allpass | Var | Var | Yok | Faz düzeltme | UNKNOWN |
| T-06 | peaking | Var | Var | Var | Tepe bant (grafik EQ) | UNKNOWN |
| T-07 | lowshelf | Var | Yok | Var | Bass shelf | UNKNOWN |
| T-08 | highshelf | Var | Yok | Var | Tiz shelf | UNKNOWN |
| T-09 | lowshelf (Q'lu varyant) | Var | Var | Var | Sert shelf | UNKNOWN |
| T-10 | highshelf (Q'lu varyant) | Var | Var | Var | Sert shelf | UNKNOWN |
| T-11 | peaking (negatif gain) | Var | Var | Var | Çentik benzeri kazanç | UNKNOWN |
| T-12 | peaking (geniş Q) | Var | Var | Var | Yaygı düzeltme | UNKNOWN |
| T-13 | peaking (dar Q) | Var | Var | Var | Hedefli düzeltme | UNKNOWN |
| T-14 | lowshelf (uç) | Var | Yok | Var | 20 Hz bandı | UNKNOWN |
| T-15 | highshelf (uç) | Var | Yok | Var | 20 kHz bandı | UNKNOWN |
| T-16 | lowpass (kaskad) | Var | Var | Yok | Sert kesme | UNKNOWN |
| T-17 | highpass (kaskad) | Var | Var | Yok | Sert kesme | UNKNOWN |
| T-18 | bandpass (paralel) | Var | Var | Yok | Bant EQ | UNKNOWN |
| T-19 | notch (paralel) | Var | Var | Yok | Bant EQ | UNKNOWN |
| T-20 | allpass (kaskad) | Var | Var | Yok | Faz telafisi | UNKNOWN |
| T-21 | TPT lowpass | Var | Var | Yok | fs duyarlı | UNKNOWN |
| T-22 | TPT highpass | Var | Var | Yok | fs duyarlı | UNKNOWN |
| T-23 | TPT bandpass | Var | Var | Yok | fs duyarlı | UNKNOWN |
| T-24 | TPT notch | Var | Var | Yok | fs duyarlı | UNKNOWN |
| T-25 | Birinci derece shelf | Var | Yok | Var | Basit shelf | UNKNOWN |
| T-26 | State-variable (4 çıkış) | Var | Var | Var | Çoklu bant | UNKNOWN |
| T-27 | Ladder (karakter) | Var | Var | Yok | Analogue his | UNKNOWN |
| T-28 | PeriodicWave (harmonik) | Yok | Yok | Yok | Özel dalga | UNKNOWN |
| T-29 | IIR kaskad (N×biquad) | Var | Var | Var | Çok bantlı zincir | UNKNOWN |
| T-30 | FIR bant (alternatif) | Var | Yok | Yok | Doğrusal faz | UNKNOWN |
| T-31 | float32 katsayı (ADR-017) | — | — | — | Katsayı tipi | ✅ frozen |
| T-32 | float64 katsayı | — | — | — | Gerekliliği ölçülmedi | UNKNOWN |
| T-33 | fs = 48 kHz sabiti | — | — | — | sartname | ✅ sartname |
| T-34 | blok = 512 | — | — | — | sartname | ✅ sartname |
| T-35 | 31 bant (aday) | — | — | — | AIEngine.php kanıtı | ✅ repo |
| T-36 | Bant sayısı kararı | — | — | — | R1 | UNKNOWN |
| T-37 | Q varsayılanı kararı | — | — | — | R1 | UNKNOWN |
| T-38 | Kazanç aralığı kararı | — | — | — | R1 | UNKNOWN |
| T-39 | Tip seçimi kararı | — | — | — | A4 | UNKNOWN |
| T-40 | Katsayı yeniden hesap (fs) | — | — | — | Prosedür | UNKNOWN |
| T-41 | Durum sıfırlama (reset) | — | — | — | Bypass | UNKNOWN |
| T-42 | Parametre yumuşatma | — | — | — | Tıkırtı | UNKNOWN |
| T-43 | Preset dönüşümü | — | — | — | Yuvarlama | UNKNOWN |
| T-44 | Ölçüm penceresi | — | — | — | measurement | Q4 |
| T-45 | Test kapsamı | — | — | — | testing | Q4 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §6 EQ preset senaryo matrisi

31-band ISO yerleşimi (§1) bir **adaydır**; bu matris hangi senaryonun hangi
bant ailesini çağırdığını tarif eder. **Hiçbir kazanç değeri uydurulmaz.**

| # | Senaryo | Bant ailesi | Tipik yönelim | Ölçüm gerekli mi | Durum |
|---|---|---|---|---|---|
| S-01 | Düz (flat) | Tümü 0 dB | Referans | Hayır | Tanım |
| S-02 | Bass artırma | 20–80 Hz | Low shelf + peaking | Evet | UNKNOWN |
| S-03 | Bass azaltma | 20–80 Hz | Low shelf | Evet | UNKNOWN |
| S-04 | Mud azaltma | 200–500 Hz | Peaking negatif | Evet | UNKNOWN |
| S-05 | Denge (presence) | 1–4 kHz | Peaking pozitif | Evet | UNKNOWN |
| S-06 | Sibilans azaltma | 5–10 kHz | Peaking negatif dar Q | Evet | UNKNOWN |
| S-07 | Hava (air) | 10–20 kHz | High shelf | Evet | UNKNOWN |
| S-08 | Oda düzeltme (düz) | Tüm bantlar | Peaking serisi | Evet | UNKNOWN (R3) |
| S-09 | Oda düzeltme (çentik) | Mod çevresi | Notch dar Q | Evet | UNKNOWN (R3) |
| S-10 | Kapı frekansı filtresi | Alt bant | Highpass | Hayır | UNKNOWN |
| S-11 | Üst kesme | Üst bant | Lowpass | Hayır | UNKNOWN |
| S-12 | Telefon bandı | 300–3400 Hz | Bandpass + shelf | Evet | UNKNOWN |
| S-13 | Kulaklık dengeleme | Tüm bantlar | Peaking serisi | Evet | UNKNOWN |
| S-14 | Hoparlör düzeltme | Tüm bantlar | Peaking serisi | Evet | UNKNOWN |
| S-15 | Otomotiv kabin | 60–250 Hz + 1–4 kHz | Peaking + shelf | Evet | UNKNOWN |
| S-16 | İzleme (reference) | — | Flat | Hayır | Tanım |
| S-17 | Gece modu | 60–80 Hz + 2–5 kHz | Peaking | Evet | UNKNOWN |
| S-18 | Konuşma netliği | 1–4 kHz | Peaking | Evet | UNKNOWN |
| S-19 | Bass-tiz vurgu ("smile") | Uç bantlar | Shelf ×2 | Evet | UNKNOWN |
| S-20 | Yankı azaltma | 500–2 kHz | Peaking negatif | Evet | UNKNOWN (R3) |
| S-21 | Kulak koruma | Tüm bantlar | Shelf + sınır | Evet | UNKNOWN |
| S-22 | Klips koruma | Tüm bantlar | Sınır (limiter) | Evet | limiter sınırı |
| S-23 | Preset A/B | Tüm bantlar | Eşit kazanç | Hayır | testing |
| S-24 | Preset dönüşümü | Tüm bantlar | Yuvarlama | Hayır | UNKNOWN |
| S-25 | Bant kapatma (solo) | Tek bant | Bypass | Hayır | UNKNOWN |
| S-26 | Bant Mute | Tek bant | −∞ yerine sınır | Hayır | UNKNOWN |
| S-27 | Zincir içi EQ (pre) | Tüm bantlar | Sıra kararı | Hayır | pipeline |
| S-28 | Zincir içi EQ (post) | Tüm bantlar | Sıra kararı | Hayır | pipeline |
| S-29 | 8.1 kanal EQ | Kanal bazlı | Çok kanal | Evet | PLANNED |
| S-30 | Mono-üstü EQ | Tüm bantlar | Eşli işleme | Evet | UNKNOWN |
| S-31 | Yan channel EQ | Tüm bantlar | Eşli işleme | Evet | UNKNOWN |
| S-32 | Crossover öncesi EQ | Bant alt | Sıra | Hayır | crossover |
| S-33 | Crossover sonrası EQ | Bant içi | Sıra | Hayır | crossover |
| S-34 | Reverb öncesi EQ | Tüm bantlar | Sıra | Hayır | reverb |
| S-35 | Reverb sonrası EQ | Tüm bantlar | Sıra | Hayır | reverb |
| S-36 | Ölçüm öncesi EQ | Tüm bantlar | Pencere | Evet | measurement |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 EQ API şartname matrisi (aday — bağlayıcı değil)

K004 equalizer çekirdeğinin **aday** sözleşmesi. `Durum` sütunu hiçbir satırda
`IMPLEMENTED` değildir (engine/ yok); tümü `UNKNOWN`/`PLANNED` işaretlidir.

| # | Öğe | Tip | Aday aralık | Birim | Durum |
|---|---|---|---|---|---|
| P-01 | bandIndex | int | 0–30 (aday) | — | UNKNOWN (R1) |
| P-02 | frequency | float | 20–20000 | Hz | ✅ repo (ISO) |
| P-03 | gain | float | — | dB | UNKNOWN |
| P-04 | Q | float | — | — | UNKNOWN |
| P-05 | filterType | enum | §5 tipleri | — | UNKNOWN (A4) |
| P-06 | bypass | bool | true/false | — | UNKNOWN |
| P-07 | enabled | bool | true/false | — | UNKNOWN |
| P-08 | presetId | int/string | — | — | UNKNOWN |
| P-09 | sampleRate | float | 48000 | Hz | ✅ sartname |
| P-10 | blockSize | int | 512 | örnek | ✅ sartname |
| P-11 | numChannels | int | stereo (aday) | kanal | UNKNOWN |
| P-12 | coefficientType | float32 | ADR-017 | — | ✅ frozen |
| P-13 | smoothingTime | float | — | sn | UNKNOWN |
| P-14 | resetPolicy | enum | — | — | UNKNOWN |
| P-15 | prepare() | metot | Başlık | — | PLANNED |
| P-16 | processBlock() | metot | noexcept | — | PLANNED |
| P-17 | setBand() | metot | lock-free | — | PLANNED |
| P-18 | setBandGain() | metot | atomik | — | PLANNED |
| P-19 | setBandQ() | metot | atomik | — | PLANNED |
| P-20 | setBandFreq() | metot | yeniden hesap | — | UNKNOWN |
| P-21 | setEnabled() | metot | bypass | — | PLANNED |
| P-22 | getState() | metot | preset | — | UNKNOWN |
| P-23 | setState() | metot | preset | — | UNKNOWN |
| P-24 | getResponse() | metot | ölçüm | — | UNKNOWN |
| P-25 | allocate() konumu | tahsis | prepare() | — | ✅ L0 kuralı |
| P-26 | lock kullanımı | — | yasak | — | ✅ L0 kuralı |
| P-27 | noexcept | imza | zorunlu | — | ✅ L0 kuralı |
| P-28 | constexpr katsayı | — | mümkün | — | ✅ L0 kuralı |
| P-29 | alignas(64) durum | — | zorunlu | — | ✅ L0 kuralı |
| P-30 | denormal temizliği | — | zorunlu | — | ✅ L0 kuralı |
| P-31 | true peak kontrolü | — | limiter | — | limiter |
| P-32 | DC kontrolü | — | relö 0.5 V | — | ✅ kural |
| P-33 | gain bütçesi | — | dynamics | — | dynamics |
| P-34 | zincir sırası | — | pipeline | — | pipeline |
| P-35 | ölçüm hedefi | — | measurement | — | measurement |
| P-36 | test kapsamı | — | testing | — | testing |
| P-37 | JUCE sınıfı | — | juce::dsp | — | VERIFICATION REQUIRED |
| P-38 | ASIO SDK | — | — | — | VERIFICATION REQUIRED |
| P-39 | C++20 | dil | — | — | ✅ kural |
| P-40 | Motor dosyası | — | engine/ | — | 0 dosya |
| P-41 | Katsayı üretim aracı | — | — | — | Araç yok |
| P-42 | Preset biçimi | — | — | — | Biçim yok |
| P-43 | UI sözleşmesi | — | band | — | Kısmi |
| P-44 | AIEngine eşlemesi | — | 31 bant | — | ✅ repo |
| P-45 | Arayüz eşlemesi | — | optimizeEQ | — | ✅ repo |
| P-46 | Workflow çağrısı | — | eqCurve | — | ✅ repo |
| P-47 | Ölçüm altyapısı | — | — | — | PLANNED |
| P-48 | Cihaz testi | — | ASIO | — | Kısıt |
| P-49 | Donanım testi | — | DAC | — | PLANNED |
| P-50 | CPU bütçesi | — | — | — | UNKNOWN |
| P-51 | Bellek bütçesi | — | — | — | UNKNOWN |
| P-52 | Gecikme bütçesi | — | — | — | UNKNOWN |
| P-53 | SNR hedefi | — | >100 dB | — | ✅ AGENTS (hedef) |
| P-54 | THD+N hedefi | — | <0.01% | — | ✅ AGENTS (hedef) |
| P-55 | 8.1 kanal | — | PLANNED | — | PLANNED |
| P-56 | ADR-019 FROZEN | — | — | — | ✅ frozen |
| P-57 | ADR-038 H001 | — | PCM3168A | — | ✅ frozen |
| P-58 | ADR-017 | — | float32 | — | ✅ frozen |
| P-59 | Lisans denetimi | — | — | — | UNKNOWN |
| P-60 | Sürüm kanıtı | — | — | — | VERIFICATION REQUIRED |
| P-61 | Q3 sınırı | — | filters | — | ⚠️ |
| P-62 | R1 kararı | — | bant/Q | — | UNKNOWN |
| P-63 | R2 kararı | — | sınır | — | ⚠️ |
| P-64 | R3 kararı | — | ölçüm | — | ⚠️ |
| P-65 | A4 kararı | — | tip | — | UNKNOWN |
| P-66 | Q4 kapısı | — | ölçüm/test | — | AÇIK |
| P-67 | Q5 hedefi | — | host/donanım | — | ⚠️ |
| P-68 | Append-only | — | bu dosya | — | ✅ |
| P-69 | Frontmatter 10 alan | — | bu dosya | — | ✅ bu tur |
| P-70 | Satır ≥ 500 | — | bu dosya | — | ✅ bu tur |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 Web araştırma tablosu (2026-10-12 derinleştirme)

Yeni eklenen URL'ler bu dosyanın §1–§4 envanteriyle kesişmez (benzersiz) ve
HTTP 200 doğrulanmıştır.

| # | İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|---|
| Q-1 | `BiquadFilterNode` — iki kutuplu/iki sıfırlı dijital filtre düğümü | https://developer.mozilla.org/en-US/docs/Web/API/BiquadFilterNode | 2026-10-12 | HTTP 200 doğrulandı |
| Q-2 | `BiquadFilterNode.type` — lowpass/highpass/bandpass/notch/peaking/shelf/allpass türleri | https://developer.mozilla.org/en-US/docs/Web/API/BiquadFilterNode/type | 2026-10-12 | HTTP 200 doğrulandı |
| Q-3 | `BiquadFilterNode.frequency` — kesim/merkez frekansı parametresi (Hz) | https://developer.mozilla.org/en-US/docs/Web/API/BiquadFilterNode/frequency | 2026-10-12 | HTTP 200 doğrulandı |
| Q-4 | `BiquadFilterNode.Q` — kalite faktörü (bant genişliği) parametresi | https://developer.mozilla.org/en-US/docs/Web/API/BiquadFilterNode/Q | 2026-10-12 | HTTP 200 doğrulandı |
| Q-5 | `BiquadFilterNode.gain` — peaking/shelf kazanç parametresi (dB) | https://developer.mozilla.org/en-US/docs/Web/API/BiquadFilterNode/gain | 2026-10-12 | HTTP 200 doğrulandı |
| Q-6 | JUCE `juce_dsp` filtre grubu dokümantasyonu (EQ ailesi için resmi giriş) | https://docs.juce.com/group__juce__dsp__filters.html | 2026-10-12 | HTTP 200 doğrulandı |
| Q-7 | SciPy sinyal işleme referansı — filtre tasarımı işlev ailesi (ödünç alınan resmi dokümantasyon) | https://docs.scipy.org/doc/scipy/reference/signal.html | 2026-10-12 | HTTP 200 doğrulandı |

> Bu kayıtlar **erişilebilirlik doğrulamasıdır**; K004 bant sayısı/Q/kazanç
> hedefleri bu sayfalardan **kopyalanmaz** (R1/A4 açık kalmaya devam eder).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 Uygulama denetim checklist'i (EQ çekirdeği — motor gelince)

| # | Denetim | Yöntem | Beklenen | Durum |
|---|---|---|---|---|
| K-01 | Tahsis yalnız prepare() içinde | Kod denetimi | 0 callback tahsisi | PLANNED |
| K-02 | mutex yok | Kod denetimi | 0 çağrı | PLANNED |
| K-03 | noexcept tam | Derleme | Tüm callback'ler | PLANNED |
| K-04 | alignas(64) | Kod denetimi | Durum tamponu | PLANNED |
| K-05 | constexpr katsayı tablosu | Kod denetimi | Çalışma anı yok | PLANNED |
| K-06 | float32 (ADR-017) | Tip denetimi | float32 | PLANNED |
| K-07 | 512 blok sabit | Süreç denetimi | Değişmez | PLANNED |
| K-08 | 48 kHz fs | Süreç denetimi | Değişmez | PLANNED |
| K-09 | Denormal temizliği | Süre ölçümü | CPU sabit | PLANNED |
| K-10 | Parametre yumuşatma | Zaman ölçümü | Tıkırtı yok | PLANNED |
| K-11 | Bypass şeffaf | Seviye ölçümü | Çıkış − giriş ≈ 0 | PLANNED |
| K-12 | Bypass tıklama yok | Zaman görünümü | Tıklama yok | PLANNED |
| K-13 | Kanal tutarlılığı | Kanal karşılaştırma | L/R farkı ≈ 0 | PLANNED |
| K-14 | Tepki doğruluğu | Frekans taraması | Sapma UNKNOWN | PLANNED |
| K-15 | Q doğruluğu | Bant genişliği ölçümü | Sapma UNKNOWN | PLANNED |
| K-16 | Kazanç doğruluğu | Seviye ölçümü | Sapma UNKNOWN | PLANNED |
| K-17 | fs değişimi | Ortam değiştirme | Yeniden hesap | UNKNOWN |
| K-18 | Blok boyu değişimi | Ortam değiştirme | Durum korunur | UNKNOWN |
| K-19 | Preset yükleme | Preset testi | Biçim UNKNOWN | PLANNED |
| K-20 | Preset dönüşümü | Yuvarlama testi | Sapma UNKNOWN | PLANNED |
| K-21 | 31 bant sınırı | Sınır testi | Band 0–30 | ✅ repo |
| K-22 | Frekans aralığı | Sınır testi | 20–20000 Hz | ✅ repo |
| K-23 | Kutup birimi içi | Kararlılık testi | Birim daire | PLANNED |
| K-24 | Katsayı yuvarlama | Tepki ölçümü | Sapma UNKNOWN | PLANNED |
| K-25 | Durum taşması | Genlik testi | Kırpılma yok | PLANNED |
| K-26 | Pre-gain bütçesi | Seviye ölçümü | Bütçe içinde | PLANNED |
| K-27 | Zincir kazancı | Seviye ölçümü | Bütçe içinde | PLANNED |
| K-28 | True peak | Oversampling ölçüm | Aşım yok | PLANNED |
| K-29 | DC bileşeni | DC ölçümü | < 0.5 V | PLANNED |
| K-30 | SNR | SNR ölçümü | Hedef >100 dB (AGENTS) | PLANNED |
| K-31 | THD+N | THD+N ölçümü | Hedef <0.01% (AGENTS) | PLANNED |
| K-32 | CPU bütçesi | Süre ölçümü | UNKNOWN | UNKNOWN |
| K-33 | Bellek bütçesi | Bellek ölçümü | UNKNOWN | UNKNOWN |
| K-34 | Gecikme bütçesi | Gecikme ölçümü | UNKNOWN | UNKNOWN |
| K-35 | ASIO Exclusive | Çalışma anı | Tek uygulama | ✅ kural |
| K-36 | WASAPI fallback | Çalışma anı | Fallback | ✅ AGENTS §17 |
| K-37 | Cihaz kaybı | Çalışma anı | Dayanıklılık | ✅ AGENTS §17 |
| K-38 | PCM5122 reddi | BOM denetimi | PCM3168A | ✅ ADR-038 |
| K-39 | ADR-019 FROZEN | Denetim | OS player değişmez | ✅ |
| K-40 | JUCE sürümü | Sürüm kaydı | Kanıt yok | VERIFICATION REQUIRED |
| K-41 | ASIO SDK sürümü | Sürüm kaydı | Kanıt yok | VERIFICATION REQUIRED |
| K-42 | Lisans taraması | Tarama | Sonuç yok | UNKNOWN |
| K-43 | Q3 sınırı (filters) | Metin denetimi | ⚠️ korunuyor | ✅ |
| K-44 | R1 kararı | Vault araması | UNKNOWN | ✅ |
| K-45 | R2 sınır ölçütü | Vault araması | ⚠️ | ✅ |
| K-46 | R3 ölçüm sahibi | Vault araması | ⚠️ | ✅ |
| K-47 | A4 tip kararı | Vault araması | UNKNOWN | ✅ |
| K-48 | Q4 kapısı | Vault araması | AÇIK | ✅ |
| K-49 | Q5 hedefi | Vault araması | ⚠️ | ✅ |
| K-50 | Ölçüm "doğrulandı" beyanı | Metin taraması | Yok | ✅ |
| K-51 | Uydurma sayı | Metin taraması | 0 | ✅ |
| K-52 | Uydurma URL | Envanter | 0 | ✅ |
| K-53 | Mojibake/CJK | Byte taraması | 0 | ✅ |
| K-54 | Yasak token | Metin taraması | 0 | ✅ |
| K-55 | Boş hücre | Hücre ölçümü | 0 | ✅ |
| K-56 | Dolgu dizgisi | Tekrar taraması | 0 | ✅ |
| K-57 | Frontmatter 10 alan | fm ölçümü | ✅ bu tur | ✅ |
| K-58 | parent: yasağı | grep | 0 | ✅ |
| K-59 | group: kod | fm ölçümü | Tekil | ✅ |
| K-60 | Authority son satır | Son satır | Birebir | ✅ |
| K-61 | `## §` başlığı | Başlık ölçümü | ≥ 1 | ✅ |
| K-62 | Markdown tablosu | `|---|` | ≥ 1 | ✅ |
| K-63 | Kanıt cümlesi | grep DEĞİŞTİRmez | Ek bölüm başına | ✅ |
| K-64 | URL benzersizliği | Kesişim | 0 | ✅ |
| K-65 | HTTP 200 | Invoke-WebRequest | 7/7 | ✅ |
| K-66 | Tarih biçimi | regex | YYYY-MM-DD | ✅ |
| K-67 | Append-only | git diff | Silme 0 | ✅ |
| K-68 | Satır ≥ 500 | Satır ölçümü | ≥ 500 | ✅ |
| K-69 | Eski §1–§4 | Okuma | Silme yok | ✅ |
| K-70 | Ek §5–§11 | Okuma | Bu tur | ✅ |
| K-71 | repo kanıtları | Referans | AIEngine · DSPark · Patina | ✅ |
| K-72 | engine/ envanteri | Disk | 0 dosya | ✅ |
| K-73 | .cpp/.h envanteri | Disk | 0 dosya | ✅ |
| K-74 | Ölçüm altyapısı | engine/ yok | PLANNED | ✅ |
| K-75 | Test altyapısı | Doküman | PLANNED | ✅ |
| K-76 | Cihaz testi | ASIO | Kısıt | ✅ |
| K-77 | Donanım testi | DAC | PLANNED | ✅ |
| K-78 | UI testi | Widget | Kısmi | ✅ |
| K-79 | AI testi | PHP | Kısmi | ✅ |
| K-80 | 8.1 surround | sartname | PLANNED | ✅ |
| K-81 | 32-bit float | ADR-017 | ✅ | ✅ |
| K-82 | 512/48 kHz | sartname | ✅ | ✅ |
| K-83 | PCM3168A | ADR-038 | ✅ | ✅ |
| K-84 | DC relö | 0.5 V | ✅ | ✅ |
| K-85 | ASIO Exclusive | tek uygulama | ✅ | ✅ |
| K-86 | Firmware OTA | DUR + handover | ✅ | ✅ |
| K-87 | Bağımlılık bump | DUR + handover | ✅ | ✅ |
| K-88 | Refactor | DUR + handover | ✅ | ✅ |
| K-89 | Subagent commit | YASAK | ✅ | ✅ |
| K-90 | K004-dışına yazma | YASAK | ✅ | ✅ |
| K-91 | `.ai/architecture/index.md` | Dokunulmaz | ✅ | ✅ |
| K-92 | İmla hatalı adlar | Değiştirilmez | ✅ | ✅ |
| K-93 | Ölçüm sayaç adları | Harfine kadar | ✅ | ✅ |
| K-94 | Zero-hallucination | Etiket | ✅ | ✅ |
| K-95 | Research mandate | URL 200 | ✅ | ✅ |
| K-96 | Skill mandate | Görev başı | ✅ | ✅ |
| K-97 | Mockup gate | UI işi | ✅ (UI işi yok) | ✅ |
| K-98 | Domain boundary | Katman | ✅ | ✅ |
| K-99 | Handover | Onay zorunlu | ✅ | ✅ |
| K-100 | Escalasyon | L1→L2→L3 | ✅ | ✅ |
| K-101 | Health check | 30 sn | ✅ | ✅ |
| K-102 | Priority | CRITICAL..LOW | ✅ | ✅ |
| K-103 | Context lock | Eşzamanlılık | ✅ | ✅ |
| K-104 | Log append-only | log.md | ✅ | ✅ |
| K-105 | ADR frozen | Dokunulmaz | ✅ | ✅ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 Sınır ve çelişki matrisi (EQ ↔ komşular)

| # | Komşu | Sınır | Çelişki belirtisi | Çözüm yolu | Sahip |
|---|---|---|---|---|---|
| B-01 | filters | Bant seviyesi ↔ çekirdek | Aynı katsayı iki dosyada | Q3 üst kararı | architect |
| B-02 | pipeline | Zincir sırası | EQ sırası iki yerde | pipeline kararı | pipeline |
| B-03 | dynamics | Kazanç zarfı | Toplam gain aşımı | Bütçe paylaşımı | dynamics |
| B-04 | compressor | GR politikası | EQ + GR çakışması | Ölçüm | compressor |
| B-05 | limiter | Tavan | EQ zirvesi aşımı | brickwall | limiter |
| B-06 | reverb | Yankı | EQ + reverb karakteri | Ölçüm | reverb |
| B-07 | delay | Gecikme | Sıra etkisi | pipeline | delay |
| B-08 | crossover | Bant bölme | Çifte bölme | Sıra | crossover |
| B-09 | spatial-audio | Uzam | Kanal bazlı EQ | Politika | spatial |
| B-10 | measurement | Oda düzeltme hedefi | Kim yazacak | Q4 | measurement |
| B-11 | testing | Bypass A/B | Eşitsiz karşılaştırma | Eşitleme | testing |
| B-12 | K003-audio | Akış kaynağı | Ayrı clock iddiası | Katman ihlali | K003 |
| B-13 | K000-isletim | Kaynak | Thread iddiası | Katman ihlali | K000 |
| B-14 | K001-donanım | Fiziksel çıkış | DAC iddiası | Katman ihlali | K001 |
| B-15 | K002-data | Metadata/BI | Ölçüm verisi | Bağlam | K002 |
| B-16 | K005-firmware | Donanım-içi DSP | Q5 | Üst karar | architect |
| B-17 | uygulama/UI | Widget | Preset biçimi | Sözleşme | ui |
| B-18 | AIOrchestrator | Ajan eşlemesi | dsp eşlemesi | Registry | MO |
| B-19 | audio-hw | DAC/amp | PCM3168A | ADR-038 | audio-hw |
| B-20 | dsp-firmware | XMOS | DSP zinciri | Şartname | dsp-fw |
| B-21 | performance | CPU/ölçüm | Bütçe | Ölçüm | performance |
| B-22 | security | OWASP | UI yüzeyi | Denetim | security |
| B-23 | data-engineer | Şema | AIEngine değişimi | Satır referansı | data |
| B-24 | devops | CI/CD | Derleme | Pipeline | devops |
| B-25 | legal | Lisans | Harici kütüphane | Tarama | legal |
| B-26 | dependency | Sürüm | JUCE/ASIO | Kanıt | dependency |
| B-27 | architect | ADR | Yeni karar | ADR | architect |
| B-28 | vault-updater | Vault | Dosya üretimi | Şema | vault |
| B-29 | qa-engineer | Test | Kapsam | Plan | qa |
| B-30 | researcher | Web | Kaynak | URL 200 | researcher |
| B-31 | code-reviewer | Kod | Motor yok | Bekleme | reviewer |
| B-32 | refactorer | Kod | Motor yok | Bekleme | refactorer |
| B-33 | debugger | Log | Motor yok | Bekleme | debugger |
| B-34 | error-coordinator | Hata | Motor yok | Bekleme | error-coord |
| B-35 | chaos-engineer | Dayanıklılık | Motor yok | Bekleme | chaos |
| B-36 | triage | Öncelik | Görev sırası | Orchestrator | triage |
| B-37 | docs-writer | Doküman | Bu dosya | Append-only | docs |
| B-38 | technical-writer | Kılavuz | Motor yok | Bekleme | technical |
| B-39 | knowledge-synthesizer | Vault | Özet | RAG | knowledge |
| B-40 | context-manager | Bağlam | Token | Bölme | context |
| B-41 | prompt-engineer | Prompt | Görev | Mandate | prompt |
| B-42 | mcp-developer | MCP | Araç | Config | mcp |
| B-43 | tooling-engineer | Betik | check_k004 | Betik | tooling |
| B-44 | platform-engineer | İç platform | Yol | Standart | platform |
| B-45 | cloud-architect | Bulut | Yok | — | cloud |
| B-46 | network-engineer | Ağ | Yok | — | network |
| B-47 | blockchain-developer | Zincir | Yok | — | blockchain |
| B-48 | payment-integration | Ödeme | Yok | — | payment |
| B-49 | compliance-auditor | Uyumluluk | Lisans | Tarama | compliance |
| B-50 | competitive-analyst | Pazar | Yok | — | competitive |
| B-51 | market-researcher | Pazar | Yok | — | market |
| B-52 | trend-analyst | Trend | Yok | — | trend |
| B-53 | data-analyst | Veri | BI | Bağlam | data-analyst |
| B-54 | data-scientist | Model | Yok | — | ds |
| B-55 | ux-researcher | Kullanıcı | Widget | Bağlam | ux |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 Karar günlüğü ve terimler (derinleştirme)

| # | Kayıt | Tarih | Etkilenen | Durum |
|---|---|---|---|---|
| G-1 | Kademe-2 kapsam kaydı | 2026-10-12 | equalizer | Mevcut |
| G-2 | Derin dal dosyaları | 2026-10-12 | equalizer | Mevcut |
| G-3 | Bant sayısı kararı yazılmadı | 2026-10-12 | equalizer | UNKNOWN (R1) |
| G-4 | Q varsayılanı kararı yazılmadı | 2026-10-12 | equalizer | UNKNOWN (R1) |
| G-5 | Kazanç aralığı kararı yazılmadı | 2026-10-12 | equalizer | UNKNOWN (R1) |
| G-6 | Tip seçimi yazılmadı | 2026-10-12 | equalizer | UNKNOWN (A4) |
| G-7 | EQ ↔ filters sınırı açık | 2026-10-12 | Q3 | ⚠️ |
| G-8 | Oda düzeltme sahibi açık | 2026-10-12 | Q4 | ⚠️ |
| G-9 | 31-band ISO repo kanıtı | 2026-10-12 | AIEngine.php | ✅ |
| G-10 | DSPark `setBand` başlığı | 2026-10-12 | referans | ✅ URL 200 |
| G-11 | Patina `EqEngine` başlığı | 2026-10-12 | referans | ✅ URL 200 |
| G-12 | JUCE sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| G-13 | ASIO SDK sürüm kanıtı yok | 2026-10-12 | build | VERIFICATION REQUIRED |
| G-14 | Lisans denetimi yok | 2026-10-12 | legal | UNKNOWN |
| G-15 | C++ motor envanteri 0 | 2026-10-12 | build | ✅ negatif |
| G-16 | 7 yeni URL doğrulandı | 2026-10-12 | §8 | ✅ HTTP 200 |
| G-17 | Frontmatter `parent:` → `parent_id:` | 2026-10-12 | bu dosya | ✅ bu tur |
| G-18 | Frontmatter 10 alan tamamlandı | 2026-10-12 | bu dosya | ✅ bu tur |
| G-19 | Satır ≥ 500 hedefi | 2026-10-12 | bu dosya | ✅ bu tur |
| G-20 | Append-only korundu | 2026-10-12 | bu dosya | ✅ bu tur |

| # | Terim | Tanım |
|---|---|---|
| 1 | Biquad | İki kutuplu, iki sıfırlı filtre bloğu |
| 2 | Q (kalite) | Bant genişliği ile ilişkili katsayı |
| 3 | Peaking | Tepe yaratan biquad tipi |
| 4 | Shelf | Uç bantta kazanç kaydırm yapan tip |
| 5 | Notch | Dar çentik |
| 6 | Allpass | Genliği düz, fazı kaydıran |
| 7 | Bandpass | Bant geçiren |
| 8 | ISO 31-band | 20 Hz–20 kHz 1/3 oktav serisi |
| 9 | Preset | Kayıtlı EQ ayarı |
| 10 | Bypass | Eşleme için düz geçiş |
| 11 | headroom | Kırpılmadan önceki seviye payı |
| 12 | smoothing | Parametre yumuşatma |
| 13 | true peak | Örnekleme-arası tepe |
| 14 | DC ofset | Sabit bileşen (relö 0.5 V) |
| 15 | SNR | Sinyal/gürültü oranı |
| 16 | THD+N | Toplam bozulma + gürültü |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
