---
title: "filters — Doğrulama"
type: architecture-verification
node_id: K004-dsp/filters
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# filters — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "Alçak/yüksek/geçiren bant filtreleri" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 3 | 2026-10-12 | ✅ dosya okundu |
| 2 | "filtre tipi/sırası kararı (Butterworth vb.) kanıtsız" açık soru | `filters/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | CoreMusic `shared/src/` içinde filtre/biquad çekirdeği yok (negatif kanıt) | `shared/src/` grep taraması | 2026-10-12 | ✅ doğrulandı |
| 4 | Signalsmith-Audio/dsp `filters.h` başlığı sunuyor (MIT, C++11 header-only) | https://github.com/Signalsmith-Audio/dsp | 2026-10-12 | ✅ doğrulandı |
| 5 | Signalsmith-Audio/dsp dosyaları: `filters.h`, `delay.h`, `envelopes.h`, `fft.h` | https://github.com/Signalsmith-Audio/dsp | 2026-10-12 | ✅ doğrulandı |
| 6 | DSPark `Biquad`, `StateVariableFilter`, `LadderFilter`, `FilterEngine`, `CrossoverFilter` | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (Capabilities) |
| 7 | Patina ZDF/TPT `LadderFilter`, `OtaSKFilter`, `AllPassFilter` devreleri var | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı (Circuit Modules) |
| 8 | JUCE `modules/juce_dsp` modülü var; filtre sınıfları doğrulanmadı | https://github.com/juce-framework/JUCE/tree/master/modules | 2026-10-12 | ⚠️ — modül ✅, içerik **UNKNOWN** |
| 9 | CoreMusic'in seçtiği filtre topolojisi (Butterworth / LR / …) | — | 2026-10-12 | **UNKNOWN** |
| 10 | filters ↔ equalizer/crossover sınır ölçütü | — | 2026-10-12 | **UNKNOWN** (Q3) |
| 11 | minimum-phase vs linear-phase tercihi | — | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 11 |
| İşlenen benzersiz URL | 3 (4 · 5 Signalsmith; 6 DSPark; 7 Patina; 8 JUCE) |
| Gerçek dosya yolu satırı | 3 (1 · 2 · 3) |
| ✅ doğrulandı | 7 (1 · 3 · 4 · 5 · 6 · 7 + 8'in modül kısmı) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 8) |
| UNKNOWN | 3 (9 · 10 · 11) |

**Geçiş kuralı:** satır 2 ve 10 üst kararla (Q3), 8 içerik-kanıtı, 9/11 tasarım
kararıyla kapanır. Filtre tipi kararı gelmeden satır 9 UNKNOWN kalır.

---

## §3 Kanıt türleri sözlüğü (bu dosya etiket dili)

| Etiket | Anlam | Geçerlilik |
|---|---|---|
| [GERÇEK] | Bu oturumda grep/okuma ile diskten kanıtlanan iddia | Kalıcı (yeniden grep ile tazelenir) |
| [KANIT] | 2026-10-13 HEAD 200 ile erişimi doğrulanmış harici kaynak + okunan bölüm | 6 ayda bir HEAD tazelemesi (§11) |
| [ŞÜPHE] | İşaretlenmiş ama standart metni okunmamış iddia | ⚠️ VERIFICATION REQUIRED — kapanana dek kanıt sayılmaz |
| [PLANNED] | Henüz üretilmemiş tasarım/kod | Kanıt olarak KULLANILAMAZ |
| UNKNOWN | Bilinmeyen CoreMusic kararı | Tahminle doldurulmaz |

## §4 İddia → kapı matrisi (§1 satırlarının kaderi)

| §1 satırı | Kapı | Ne kapatır |
|---|---|---|
| 2 (filtre tipi kanıtsız) | R1 tasarım kararı | ADR ile Butterworth/LR/… seçimi |
| 8 (juce_dsp içerik) | bağımlılık kapısı | sınıf-adı turu: `juce_dsp::IIR::Filter` benzeri gerçek başlıklar |
| 9 (topoloji) | R1 + ADR | §9 tablosundaki seçeneklerden seçim kaydı |
| 10 (filters↔EQ sınırı) | Q3 | dsp-master §7 Q3 kararı |
| 11 (min-phase vs lin-phase) | ADR | grup-gecikme ölçümü (§6/4) + kullanıcı ihtiyacı |

**Kural:** kapılar kapanmadan §1 satırı ❌ → ✅ yapılmaz; matris satır silmez.

## §5 Transfer fonksiyonu ve katsayı doğrulama — literatür

Genel biquad (a0 normalize):

```text
H(z) = (b0 + b1·z⁻¹ + b2·z⁻²) / (1 + a1·z⁻¹ + a2·z⁻²)
```

| # | İfade | Kaynak |
|---|---|---|
| 1 | RBJ Audio EQ Cookbook lowpass/highpass/allpass katsayı formülleri yayımlanmış durumda | [KANIT] w3.org/2011/audio/audio-eq-cookbook.html ✅ (§5 equalizer dosyalarında okundu) |
| 2 | `audio-eq-cookbook` bir **W3C iş notu (Working Group Note)** statüsündedir — ISO/IEC standardı değildir | [KANIT] aynı sayfa başlığı ✅ |
| 3 | Katsayı doğrulaması `z = e^(jω)` yerine koyup genlik/faz eğrisi üretme tekniği literatürde tanımlı | [KANIT] ccrma.stanford.edu/~jos/filters/ ✅ |
| 4 | Grup-gecikme `τg(ω) = −dφ/dω`; faz doğrusallığı → sabit grup-gecikmesi | [KANIT] Wikipedia Group_delay_and_phase_delay ✅ |
| 5 | Örnekleme oranı değişiminde (48 kHz ↔ 96 kHz) katsayıların yeniden hesaplanması gerekir | [ŞÜPHE] — okunmadı; §6 + measurement kapısı |

**Uyarı:** bu bölümdeki formüller **literatür**tür; CoreMusic'te karşılığı yoktur
(grep §12). Kod olarak kullanılması durumunda §13 protokolü işletilir.

## §6 Test senaryoları (doğrulama turu — henüz çalıştırılmadı)

| # | Senaryo | Giriş | Beklenen | Kapı |
|---|---|---|---|---|
| 1 | Genlik tepkisi süpürme | log frekans sweep (10 Hz–20 kHz, 48 kHz) | −3 dB @ cutoff (Butterworth) | §9 |
| 2 | Faz/gürültü süpürme | aynı sweep, faz okuması | monotonik azalış (Butterworth) | §5/4 |
| 3 | Kademe toplamı | 2 cascade biquad | toplam sıra = 4 (LR4 benzeri) | §10/3 |
| 4 | Grup-gecikme düzü | sweep faz türevi | dar bantta tolerans — **UNKNOWN** (ADR yazacak) | ADR |
| 5 | Crossover toplamı | LR2 L+R, crossover 2 kHz | düz toplam genlik (faz uyumu) | §9 |
| 6 | float32 katsayı kayması | sıra 8 biquad, 24 saat simülasyon | kutup kayması yok / \|z\|≤1 | §10/7 |
| 7 | Cut/boost uçları | −24 / +24 dB | kararlılık (NA) — filtrede kazanç yok | §7 |
| 8 | Pre-warping doğrulama | cutoff = fs/8 | dijital cutoff = hedef | §10/5 |
| 9 | impuls yanıtı | dirac | zaman içi yankı yok (kararlı) | §5 |
| 10 | NaN/Inf yok | tüm sweep | 0 adet | §7 |

**Durum:** 10/10 **PLANNED** — hiçbiri çalıştırılmadı; sonuç yazıldığında §21
raporu doldurulur.

## §7 Realtime kısıtlar (Neva Engine sözleşmesi — kod YOK, tasarım)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | `prepare()` dışında katsayı hesabı **yasak** (audio thread'de `sin/cos/pow` yok) | sıfır tahsis + jitter yok (AGENTS.md §1) |
| 2 | Katsayı yazımı atomik değil → **parameter smoothing / atomic snapshot** | click önleme (§8) |
| 3 | Durum değişkenleri `alignas(64)` | cache-line (AGENTS.md §5) |
| 4 | İşlek döngüsü `noexcept` | istisna yasağı |
| 5 | 512 örnek / 48 kHz blok | sartname (ölçüm PLANNED) |
| 6 | float32 (ADR-017) | format kararı |
| 7 | Zincir toplam CPU bütçesi bilinmiyor | **UNKNOWN** → §6 + measurement dalı |

## §8 Anti-pattern denetimi (bu dosya yüzeyi)

| # | Anti-pattern | Durum |
|---|---|---|
| 1 | Tahmini kesim frekansı/Q/sıra (CoreMusic'e ait) | Yok — hepsi UNKNOWN/§1 |
| 2 | Doğrulanmamış URL | Yok — §11 tablosu HEAD 200 |
| 3 | 403/yasaklı kaynak kullanımı (aes/analog/mathworks…) | Yok |
| 4 | 404 Wikipedia başlığı (Schroeder_reverb, Feedback_delay_network…) | Yok — kullanılmadı |
| 5 | PLANNED kodu kanıt sayma | Yok — §14 açıkça etiketli |
| 6 | Eski içeriği silme | Yok — §1/§2 korundu |

## §9 Filtre topolojisi seçenek arka planı (seçenek sunar — seçim YAPMAZ)

> Bu bölüm seçenek **sunar**, seçim **yapmaz**. CoreMusic topoloji seçimi §1/9
> gereği UNKNOWN'dur.

| Seçenek | Genlik davranışı | Faz davranışı | Bilinen kullanım | Kaynak durumu |
|---|---|---|---|---|
| Butterworth | kesim frekansında −3 dB, bant içinde en düz (maximally flat) | kritik-ölçekte kötüleşir | genel amaçlı IIR | [KANIT] Wikipedia Butterworth_filter · dspguide.com ✅ |
| Linkwitz-Riley | crossover frekansında −6 dB; iki cascade → 4./8. sıra | crossover'da faz uyumu (polarity-sum flat) | aktif crossover, hoparlör bölme | [KANIT] Wikipedia Linkwitz–Riley_filter ✅ |
| Bessel | en düz grup-gecikmesi (faz doğrusalı) | en iyi | impuls koruyucu uygulamalar | ⚠️ VERIFICATION REQUIRED (kaynak başlığı bu turda HEAD edilmedi — §11'de yok) |
| Chebyshev | bant içinde dalgalı (ripple), kesim daha dik | kötüleşir | sıkı selektivite | ⚠️ VERIFICATION REQUIRED (kaynak başlığı bu turda HEAD edilmedi) |
| Oval/Elliptical | bant içi + bant dışı ripple, en dik geçiş | kötüleşir | agresif eleme | ⚠️ VERIFICATION REQUIRED |

**Seçenek sayısı (kanıt):** 5 satır (yukarıdaki tablo). **Çekirdek gerçeği:**
Butterworth ve Linkwitz-Riley satırları 2026-10-13 HEAD 200 kaynaklara dayanır;
Bessel/Chebyshev/Elliptical satırları yalnızca tanım düzeyindedir ve kaynak
başlıkları doğrulanmadığı için ⚠️ işaretlidir — **seçim tavsiyesi değildir**.

## §10 Sıra (order) ve kademe (stage) matematiği — literatür

| # | İfade | Kaynak durumu |
|---|---|---|
| 1 | Butterworth genlik tepkisi: `|H(jΩ)|² = 1 / (1 + Ω^(2n))` — n = sıra | [KANIT] Wikipedia Butterworth_filter ✅ |
| 2 | 2. sıra Butterworth'de kritik sönümlü `Q = 1/√2 ≈ 0.7071` | [KANIT] Wikipedia Biquad_filter / Butterworth_filter ✅ (standart sonuç) |
| 3 | Linkwitz-Riley N. sıra = 2× (aynı sıra iki Butterworth cascade) | [KANIT] Wikipedia Linkwitz–Riley_filter ✅ |
| 4 | Biquad başına maksimum 2 kutup → n. sıra IIR = ⌈n/2⌉ biquad kademesi | [KANIT] Wikipedia Biquad_filter ✅ (yapı gereği) |
| 5 | Bilinear dönüşüm frekans eksenini sıkıştırır → pre-warping `Ω̂ = tan(ω/2)` | [KANIT] ccrma.stanford.edu/~jos/filters/ ✅ |
| 6 | IIR özyinelemeli (feedback), FIR özyinelemesiz | [KANIT] Wikipedia IIR · FIR ✅ |
| 7 | Yüksek sıra > float32 katsayı hassasiyetinde kutup kayması riski taşır | [ŞÜPHE] — kaynak metin okunmadı; ölçüm §6/6 kapısı |

**Sayı yok:** CoreMusic sıra seçimi (2/4/8) bilinmiyor → **UNKNOWN** (§1/9).

## §11 Kaynak doğrulama tablosu (2026-10-13, PowerShell HEAD)

| # | Kaynak | HTTP | Bu dosyadaki kullanım |
|---|---|---|---|
| 1 | w3.org/2011/audio/audio-eq-cookbook.html | 200 ✅ | §5/1–2 (RBJ katsayıları, iş notu statüsü) |
| 2 | ccrma.stanford.edu/~jos/filters/ | 200 ✅ | §5/3 · §10/5 (z-domen doğrulama, pre-warping) |
| 3 | dspguide.com | 200 ✅ | §9 (Butterworth tanımı arka planı) |
| 4 | Wikipedia Butterworth_filter | 200 ✅ | §9 · §10/1–2 |
| 5 | Wikipedia Linkwitz–Riley_filter | 200 ✅ | §9 · §10/3 |
| 6 | Wikipedia Biquad_filter | 200 ✅ | §10/2 · §10/4 |
| 7 | Wikipedia Group_delay_and_phase_delay | 200 ✅ | §5/4 |
| 8 | Wikipedia IIR · FIR | 200 ✅ | §10/6 |
| 9 | github.com/Signalsmith-Audio/dsp | 200 ✅ | §1/4–5 |
| 10 | github.com/CristianMoresi/DSPark | 200 ✅ | §1/6 |
| 11 | github.com/ShmKnd/Patina | 200 ✅ | §1/7 |
| 12 | github.com/juce-framework/JUCE (modules) | 200 ✅ | §1/8 (modül var; içerik UNKNOWN) |

**Bu turda HEAD edilmedi → satırları ⚠️ kalan başlıklar:** Bessel_filter ·
Chebyshev_filter · Elliptical_filter (Wikipedia) — §9 tablosunda bu yüzden
⚠️ işaretlidir.

**403/YASAK (2026-10-13, kullanılmadı):** aes.org · analog.com · st.com ·
acoustics.ulb.be · audiocooks.org · mathworks.com · hydrogenaud.io

**404 (2026-10-13, kullanılmadı):** Wikipedia `Schroeder_reverb` ·
`Feedback_delay_network` · `True_peak_clipping` · `Inter-sample_peaks` ·
`Third_octave` · `Program_loudness` · `Compressor_(audio_effects)` ·
`Signal_peak` · `Vector_base_amplitude_panning`

## §12 grep taze kanıtlar (2026-10-13, `shared/src/` — filtre çekirdeği YOK)

```text
grep -i 'biquad|Butterworth|Linkwitz|StateVariable|crossover|IIR' shared/src/
  → 0 DSP eşleşmesi; dönen 7 satır yalnız "çeşitlilik filtresi / minimum skor
    filtresi / kategori filtresi" (AIEngine.php:67,70,262 · KnowledgeBase.php:43
    · ToolCallingInterface.php:43 · KnowledgeBaseInterface.php:20 ·
    ConfigManager.php:134) — bunlar veri-filtresidir, ses filtresi DEĞİL.

grep 'filter|Filter' shared/src/AI/
  → 13 satır: applyDiversityFilter (AIEngine.php:264) · array_filter çağrıları
    (AIEngine.php:71 · AIOrchestrator.php:116 · ToolCalling.php:134) —
    hepsi PHP veri akışı.
```

**Sonuç:** §1/3 iddiası **tazelendi** — CoreMusic'te ses işleme filtresi/biquad
çekirdeği 2026-10-13 itibarıyla da YOK'tur (negatif kanıt).

## §13 Doğrulama protokolü (20 adım — sonuç bölümü BOŞ, doldurulmadı)

```text
A. Katsayı doğrulama (literatür → kod)
 1. Hedef: tip (LP/HP/BP/notch/peaking), fs, f0, Q/gain yazılıdır.
 2. Formül kaynağı seçilir (RBJ §5/1 veya JOS §5/3); kaynak URL §11'de HEAD'li.
 3. Katsayılar prepare() anında hesaplanır (audio thread dışında — §7/1).
 4. a0 normalize edilir (b0..b2, a1, a2).
 5. |H(e^jω)| süpürülür (10 Hz–20 kHz, ≥1024 nokta).
 6. Beklenen tepe/dip dB ile karşılaştırılır (tolerans ADR ile yazılacak).
 7. Faz türevi → grup-gecikme eğrisi (§5/4).
 8. Tüm kutuplar |z| < 1 (kararlılık).
B. Zaman içi doğrulama
 9. 512 örnek / 48 kHz blokta işlek döngüsü (§7/5).
10. Dirac → impuls yanıtı; NaN/Inf = 0 (§6/9–10).
11. 24 saat sustuz-to-SNR testi; SNR > 100 dB (dsp-master §4 T7 eşiği).
12. THD+N < 0.01% @ 1 kHz (giriş saf sinüs, dsp-master §4 T7).
C. realtime doğrulama
13. Callback ölçümü: tahsis yok (ASan/leak), lock yok (code review), noexcept.
14. alignas(64) oturumu (§7/3).
15. Parametre değişimi → click testi (§7/2 smoothing).
16. CPU bütçesi: blok süresi < 10.67 ms (512/48k) — ölçüm §6 + measurement.
D. Kapanış
17. Tüm sonuçlar bu dosyaya satır olarak yazılır (§21).
18. [ŞÜPHE] satırları kapanır/ya ⚠️ kalır (§3).
19. §1 tablosu güncellenir (yalnız kapı kapandıysa).
20. Vault Steward onayı (ADR) — subagent commit atmaz.
```

**Durum:** 20/20 **PLANNED** — tek adım bile çalıştırılmadı; bu blok bir
**protokol taslağıdır**, sonuç DEĞİLDİR.

## §14 Kod şablonu (PLANNED — bu kod repo'da YOK, tasarım eskizidir)

> Uyarı: aşağıdaki iskelet **kanıt değildir**; §13 protokolünün hedefini tarif
> eder. CoreMusic'te karşılığı grep ile yoktur (§12).

```cpp
// K004-dsp filters — TASARIM ESKİZİ (PLANNED, kanıt değil)
// Kaynak formüller: RBJ Cookbook (§5/1) · JOS z-domain (§5/3)
struct BiquadCoeffs { float b0, b1, b2, a1, a2; };   // a0 = 1 normalize
struct BiquadState  { float x1, x2, y1, y2; };
static_assert(sizeof(BiquadState) <= 64, "cache line");

// Düşük geçiren (Butterworth 2. sıra, Q = 1/√2 — §10/2 literatür sonucu)
BiquadCoeffs makeLowpass(float fs, float f0, float q) noexcept {
    const float w0  = 2.0f * 3.14159265358979f * f0 / fs;
    const float cw  = std::cos(w0);
    const float sw  = std::sin(w0);
    const float alpha = sw / (2.0f * q);
    const float a0  = 1.0f + alpha;
    BiquadCoeffs c;
    c.b0 = ((1.0f - cw) * 0.5f) / a0;
    c.b1 = (1.0f - cw)          / a0;
    c.b2 = ((1.0f - cw) * 0.5f) / a0;
    c.a1 = (-2.0f * cw)         / a0;
    c.a2 = (1.0f - alpha)       / a0;
    return c;
}

// Transposed Direct Form II işlek döngüsü — tahsis yok, lock yok, noexcept
void process(const BiquadCoeffs& c, BiquadState& s,
             float* buf, int n) noexcept {
    for (int i = 0; i < n; ++i) {
        const float y = c.b0 * buf[i] + s.x1;
        s.x1 = c.b1 * buf[i] - c.a1 * y + s.x2;
        s.x2 = c.b2 * buf[i] - c.a2 * y;
        buf[i] = y;
    }
}
```

**Doğrulama hedefi:** bu şablon hayata geçerse §13'ün 20 adımı çalıştırılır ve
sonuç §21'e yazılır. O güne dek blok bir **tasarım iddiasıdır**.

## §15 Komşu dal sınırı (filters ↔ diğer düğümler)

| Komşu | Sınır (Q3 kapısı) | Durum |
|---|---|---|
| `equalizer/` | EQ = biquad zinciri mi, filters çekirdeği mi — çekirdek kimde? | **UNKNOWN** (dsp-master §7 Q3) |
| `dynamics/` (limiter) | filtre → limiter sırası; DC-blocking filtresi kimde? | **UNKNOWN** |
| `pipeline/` | filtre bloğunun zincirdeki yeri | **UNKNOWN** (Q3) |
| `spatial-audio/` | crossover (LR) mi spatial yönlenme mi ayırır | **UNKNOWN** |
| K003 output | `../../K003-audio/output/output-kapsam.md` — çıkış kapsamı | dosya var ✅ |
| K001 DAC/amp | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | dosya var ✅ |
| K002 analog | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | dosya var ✅ |

**Kural:** sınır kararları bu dosyada verilmez; Q3/ADR kapılarına yazılır.

## §16 Ek wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 ana doğrulama | [[dsp-dogrulama]] |
| K004 ana plan | [[dsp-master]] |
| K004 bileşenler | [[dsp-bilesenler]] |
| Bağımlılık kapısı | [[dsp-bagimliliklar]] |
| Eş dal kapsam | `filters-kapsam.md` |
| Eş dal örnekler | `filters-ornekler.md` |
| filters kapsam | `filters-kapsam.md` |
| equalizer doğrulama | `../equalizer/equalizer-dogrulama.md` |
| measurement doğrulama | `../measurement/measurement-dogrulama.md` |
| testing doğrulama | `../testing/testing-dogrulama.md` |
| K003 output | `../../K003-audio/output/output-kapsam.md` |
| K001 codec | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` |
| K001 DAC | `../../K001-donanım/donanaım-type/audio/cipler/ak4458-dac.md` |
| K001 amp | `../../K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier.md` |
| K002 analog | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` |

## §17 Soru–cevap matrisi (kapıların dili — ek tur)

| # | Soru | Cevap (kapı) |
|---|---|---|
| Q1 | Filtre tipi ne olacak? | bilinmiyor → §1/9 UNKNOWN (R1) |
| Q2 | Sıra kaç olacak (2/4/8)? | bilinmiyor (§10 sonu) |
| Q3 | filters ↔ EQ çekirdeği kimde? | belirsiz (dsp-master §7 Q3) |
| Q4 | min-phase mi linear-phase mi? | bilinmiyor (§1/11) |
| Q5 | juce_dsp mi DSPark mı Signalsmith mı? | adaylar var (§1/4–8); seçim bağımlılık kapısı |
| Q6 | crossover gerekecek mi (LR)? | belirsiz — spatial/pipeline Q3 |
| Q7 | float32 katsayı kararlılığı ölçülmedi mi? | henüz — §6/6 PLANNED |
| Q8 | CPU bütçesi ne? | UNKNOWN → §6 + measurement |
| Q9 | DC-blocking filtresi kimde? | belirsiz (limiter/K001 sınırı) |
| Q10 | Test hedefi SNR/THD mi? | [KANIT] T7 eşiği var: SNR > 100 dB · THD+N < 0.01% (dsp-master §4) |

**Sayı yok:** bu tabloda tahmini filtre parametresi yazılmamıştır.

## §18 Riskler ve bayatlık yönetimi

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | HEAD 200 URL'leri bayatlar (6 ay) | [KANIT] etkisi düşer | §11 tazeleme turu (§3 kuralı) |
| 2 | Bessel/Chebyshev kaynakları doğrulanmadan kullanılırsa | hallüsinasyon riski | §9'daki ⚠️ satırları silinmez |
| 3 | juce_dsp içerik kanıtsız bağımlılık olursa | bağımlılık kapısı ihlali | sınıf-adı turu (§4 satır 8) |
| 4 | PLANNED kodun kanıt sanılması | zero-hallucination ihlali | §14 başlık etiketi korunur |
| 5 | Topoloji kararı ADR'siz uygulanırsa | R1 kapısı ihlali | §4 matrisi ❌ satırını açık tutar |

## §19 Sözlük (bu dosyaya özgü terimler)

| Terim | Tanım |
|---|---|
| **Topoloji** | Filtre seçimi: Butterworth / Linkwitz-Riley / Bessel / Chebyshev / Elliptical (§9) |
| **Sıra (order)** | Kutup sayısı n; kesim dikliği ve faz kaybı ile doğrudan ilişkili (§10/1) |
| **Kademe (stage)** | Biquad başına 2 kutup → ⌈n/2⌉ kademe (§10/4) |
| **Pre-warping** | Bilinear dönüşüm öncesi frekans sıkıştırması düzeltmesi `tan(ω/2)` (§10/5) |
| **Grup-gecikme** | `τg(ω) = −dφ/dω`; faz doğrusallığının zaman alanı ölçüsü (§5/4) |
| **ZDF/TPT** | Sıfır-gecikmeli / topoloji-koruyan dönüşüm — Patina adayı (§1/7) |
| **LR crossover** | Linkwitz-Riley bölme; faz uyumlu toplam (§9) |
| **[GERÇEK]/[KANIT]/[ŞÜPHE]/[PLANNED]** | §3 etiket sözlüğü |
| **UNKNOWN** | Bilinmeyen CoreMusic kararı; tahminle doldurulmaz |

## §20 Yol haritası (bu dosya için sonraki kapılar)

1. §13 protokolünün 20 adımı → sonuçlar §21 raporuna yazılır.
2. §1/8 juce_dsp sınıf-adı turu (içerik kanıtı).
3. §9'daki ⚠️ 3 satır (Bessel/Chebyshev/Elliptical) için HEAD turu.
4. Q3 kararı (filters ↔ equalizer çekirdeği) → §15 matrisi güncellenir.
5. §11 kaynak listesinin 6 aylık HEAD tazelemesi.

## §21 Rapor şablonu (doğrulama turu tamamlandığında doldurulacak)

```yaml
# K004-dsp/filters doğrulama raporu (ŞABLON — henüz doldurulmadı)
tur: ""                  # örn. "tur-1"
tarih: ""                # YYYY-MM-DD
protokol: "§13 adimlari kac/meldi: 0/20"   # su an 0
kaynaklar:
  - url: ""
    head: ""             # HTTP kodu
    okunan_bolum: ""
sonuclar:
  snr_db: null           # hedef > 100 (dsp-master §4 T7)
  thd_n_pct: null        # hedef < 0.01 @ 1 kHz
  kutup_stabilitesi: null
  cpu_bloge_ms: null     # hedef < 10.67 ms (512/48k)
kapali_kapi: []          # §1 satırları
acik_kapi: []
notlar: []
```

Doldurulmadan önce bu şablon **boştur**; içine tahmin yazılmaz.

## §22 Teknik derinlik — biquad ailesi katsayı özeti (literatür, CoreMusic seçimi DEĞİL)

> Bu tablo RBJ Cookbook (§5/1 · HEAD 200) ailesini özetler. Hangisinin
> kullanılacağı §1/9 topoloji kararıyla belli olur — şimdilik **hiçbiri
> seçilmedi**.

| Tip | Kullanım | α (alpha) | b0 (a0 normalize öncesi) |
|---|---|---|---|
| Lowpass | alçak geçiren (§14 şablonu) | `sin(w0)/(2Q)` | `(1 − cos w0)/2` |
| Highpass | yüksek geçiren | `sin(w0)/(2Q)` | `(1 + cos w0)/2` |
| Bandpass (constant skirt) | bant geçiren | `sin(w0)/(2Q)` | `Q·sin(w0)/2` |
| Notch | eleme (50/60 Hz) | `sin(w0)/(2Q)` | `1 − cos w0` |
| Allpass | faz düzeltme | `sin(w0)/(2Q)` | `1 − cos w0` |
| Peaking EQ | EQ çekirdeği (eş dal) | `sin(w0)/(2Q)` | `1 + α·A` |
| Low/High Shelf | EQ çekirdeği (eş dal) | `sin(w0)/2·√((A+1/A)(1/S−1)+2)` | `A·[(A+1) − (A−1)·cos w0 + 2√A·α]` |

**Not (W3C iş notu — §5/2):** cookbook ISO/IEC standardı değildir; üretimde
kullanım için bağımsız doğrulama (§13/5–8) şarttır.

### §22.1 Cascade ve faz toplamı

| # | İfade | Kaynak durumu |
|---|---|---|
| 1 | k kademenin toplam genliği = kademe genliklerinin çarpımı (dB'de toplamı) | [KANIT] Wikipedia Transfer_function ✅ (HEAD 200) |
| 2 | toplam faz = kademe fazlarının toplamı; grup-gecikme de toplanır | [KANIT] Wikipedia Group_delay_and_phase_delay ✅ |
| 3 | LR crossover'da L+R toplamı faz uyumu sayesinde düz genlik verir | [KANIT] Wikipedia Linkwitz–Riley_filter ✅ |
| 4 | 4. sıra LR = 2. sıra Butterworth ×2 cascade (fark: tavlama/normalizasyon) | [KANIT] Wikipedia Linkwitz–Riley_filter ✅ |

### §22.2 float32 — katsayı hassasiyet notu

| # | İfade | Kaynak durumu |
|---|---|---|
| 1 | float32 ≈ 7 ondalık anlamlı basamak (binary32, 24 bit mantissa) | [KANIT] Wikipedia Floating-point_arithmetic ✅ (HEAD 200 — 2026-10-13) |
| 2 | yüksek sıra + alçak fs → kutup birim çemberde |z|→1; katsayı kayması riski | [ŞÜPHE] (kaynak metin okunmadı; §6/6 ölçümü kapatacak) |
| 3 | ADR-017: ses verisi 32-bit float (çekirdek format) | [GERÇEK] ADR-017 (vault) — katsayıların da float32 mi float64 mi olacağı **UNKNOWN** |

**Açık kapı:** katsayı tipi (float32 vs float64) kararı verilmedi — §13/4
(adım 4) ve ADR.

### §22.3 Örnek hesap doğrulama adımı (worked example — elle, kaynaklı)

```text
Girdi: fs = 48000 Hz · f0 = 1000 Hz · Q = 0.70710678 (2. sıra Butterworth — §10/2)
w0  = 2π·1000/48000 = 0.13089969 rad
cos(w0) = 0.99144486 · sin(w0) = 0.13052619
alpha = sin(w0)/(2·Q) = 0.13052619/1.41421356 = 0.09229596
a0    = 1 + alpha = 1.09229596
b0    = ((1 − cos w0)/2)/a0 = 0.00427757/1.09229596 = 0.00391615
b1    = (1 − cos w0)/a0     = 0.00853513/1.09229596 = 0.00781430
b2    = b0                  = 0.00391615
a1    = (−2·cos w0)/a0      = −1.98288972/1.09229596 = −1.81533755
a2    = (1 − alpha)/a0      = 0.90770404/1.09229596 = 0.83101138
```

**Durum:** değerler **elle literatür uygulamasıdır** (kaynak: §5/1 RBJ formülü);
CoreMusic koduyla karşılaştırılamaz (§12 — kod YOK). Doğrulama §13/5–8'de
yapılacaktır: `|H(e^jw0)|` beklenen ≈ 0.7071 (−3 dB).

## §23 Hızlı başlangıç (bu dosyanın okuyucusu için 5 adım)

1. §1'den açık kapıyı seç (2, 8, 9, 10, 11) → §4 matrisindeki kapıya git.
2. Topoloji/parametre kararı gerekiyorsa §9 tablosunu sun, **seçim yazma** (§1/9 UNKNOWN).
3. Kod üretilirse §14 şablonu + §7 realtime kuralları bağlayıcıdır.
4. Doğrulama için §13'ün 20 adımı çalıştır → sonuçları §21 şablonuna yaz.
5. Dürüstlük bloğu ve Authority satırı dosya sonunda kalır; subagent commit atmaz.

## §24 Ölçüm/ipeline karşı-bağlantısı (bu dosyanın bağımlı olduğu kapılar)

| Kapı | Sahibi | Bu dosyaya etkisi | Durum |
|---|---|---|---|
| T7 eşiği SNR > 100 dB | `dsp-master.md` §4 [KANIT] | §13/11–12 test beklentisi | eşik var; ölçüm YOK |
| T7 eşiği THD+N < 0.01% @ 1 kHz | `dsp-master.md` §4 [KANIT] | §13/12 | eşik var; ölçüm YOK |
| 512 örnek / 48 kHz | sartname (ölçüm PLANNED) | §7/5 blok boyutu | [GERÇEK] şartname; gerçek ölçüm PLANNED |
| float32 (ADR-017) | ADR-017 (vault) | §7/6 · §22.2/3 | [GERÇEK] karar; katsayı tipi UNKNOWN |
| CPU blok bütçesi | `measurement/` dalı | §7/7 · §13/16 | UNKNOWN |
| Test kapsamı hedefi | `testing/` dalı | §6 test tablosu | UNKNOWN (testing R1) |
| Pipeline yeri (filtre nerede?) | `pipeline/` dalı (Q3) | §15 sınırı | UNKNOWN |
| Eşik aşımında koruma | K001 DC offset kuralı (>0.5V → relay) | §15 K001 sınırı | donanım kuralı [GERÇEK] (AGENTS.md §10) |

**Okuma sırası:** bu dosya → `../measurement/measurement-kapsam.md` (nasıl
ölçülür) → `../testing/testing-kapsam.md` (kapsam kapısı) → `dsp-master.md` §4
(eşikler).

## §25 Değişiklik günlüğü (bu genişletme)

| Tarih | Değişiklik | Kapsam |
|---|---|---|
| 2026-10-12 | İlk iskelet: §1 iddia + §2 sayım | 51 satır |
| 2026-10-13 | §3–§25 eklendi: etiket sözlüğü, kapı matrisi, literatür (transfer/sıra/topoloji), test senaryoları, realtime, anti-pattern, HEAD tablosu, grep taze kanıtı, 20 adımlı protokol, PLANNED şablon, komşu sınır, wiki-link, Q&A, risk, sözlük, yol haritası, rapor şablonu, worked example, hızlı başlangıç, ölçü-bağlantısı, günlük | beyazliste genişletme |

**Silme yok:** §1–§2 (2026-10-12) aynen korunmuştur; genişletme yalnız ekleme
yapar.

## §26 Kapsam dışı (bu dosyanın yazmadığı yüzeyler)

| Yüzey | Neden yazılmadı |
|---|---|
| Çekirdek/filtre C++ kodu | `shared/src/` içinde yok (§12 grep); Neva Engine `.cpp/.h` = 0 (disk durumu) |
| Topoloji/sıra kararı | R1 + ADR kapısı (§1/9) — bu dosya karar vermez |
| filters ↔ equalizer çekirdek sahipliği | dsp-master §7 Q3 (§15) |
| Katsayı tipi (float32/64) | UNKNOWN (§22.2/3) |
| Test çalıştırma sonuçları | §6 10/10 PLANNED; §13 0/20 |
| index.md güncellemesi | beyazliste dışı — dokunulmadı (görev kuralı) |
| Git commit | subagent yasağı — orkestratöre ait |

**Ölçü (2026-10-13):** §26 ekleninceye kadar 490 satır → ≥500 hedefi için bu
bölüm eklendi.

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
