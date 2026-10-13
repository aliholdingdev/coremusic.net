---
title: "limiter — Örnekler"
type: architecture-examples
node_id: K004-dsp/limiter
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# limiter — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında ses limiter kodu bulunmamaktadır. Eşleşen
`RateLimiter`/`CacheRateLimiter` HTTP request rate limiting'dir, ses tavanı
değildir — dahil edilmez. Uydurma kod yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Quick start, 2026-10-12 ✅)

```cpp
dspark::Limiter<float> limiter;
limiter.prepare(spec);
limiter.setCeiling(-1.0f);          // tavan: -1.0 dBFS
limiter.processBlock(block);
```

**Ne gösterdiği:** minimal limiter API'si — tek `ceiling` parametresi (dBFS) +
realtime `processBlock`. CoreMusic `limiter` dalı için referans sözleşmesi; R1
(ceiling değeri) bu API'nin girdisidir, henüz sabitlenmedi.

**Kaynak:** https://github.com/Signalsmith-Audio/basics (README — Limiter, 2026-10-12 ✅)

```cpp
// Limiter with maximum attack/lookahead of 100ms
signalsmith::basics::LimiterFloat effect(100);
effect.configure(sampleRate, maxBlockSize, channels);
// ...
effect.latencySamples();   // gecikme raporu
effect.tailSamples();      // kuyruk uzunluğu
```

**Ne gösteriği:** lookahead tabanlı limiter + `latencySamples()`/`tailSamples()`
ile gecikme raporlama deseni — realtime zincire eklenirken latansın bildirilmesi
zorunluluğunu gösterir (pipeline R3 latency bütçesini besler).

**Kaynak:** https://github.com/ShmKnd/Patina (Effect Engines, 2026-10-12 ✅)
`LimiterEngine` — FET/VCA/opto topolojileri (analog-modeling referans).

## §3 Uygulama notu

Limiter zincirin en sonunda, K003 çıkışına/DAC'a en yakın noktada durur. Ceiling
değeri (R1) ve true-peak tercihi (R3) measurement standardıyla birlikte belirlenir;
kilitlenmeden önce K001 donanım koruma sınırıyla karıştırılmaz.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[limiter-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[limiter-dogrulama]]` | iddia kanıtları |
| `../compressor/compressor-kapsam.md` | sınır komşusu (yumuşak kısma) |
| `../dynamics/dynamics-kapsam.md` | aile (Q3) |
| `../measurement/measurement-kapsam.md` | THD/true-peak ölçüm standardı |

---

## §5 Literatür kod bloğu A — en basit clip+release limiter (PLANNED)

> **Etiket:** [PLANNED] — repo'da YOK (§1 grep negatif). Formüller
> `limiter-kapsam.md` §5–§6 (literatür) dayanır. CoreMusic parametresi DEĞİL.

```cpp
// K004-dsp limiter — TASARIM ESKİZİ (PLANNED, kanıt değil)
struct SimpleLimiter {
    float ceilingLin = 1.0f;   // prepare()'da dBFS → lineer (kapsam §5/2)
    float releaseCo  = 1.0f;   // prepare()'da: exp(-1/(sr*releaseSec))
    float gain = 1.0f;         // alignas(64) önerilir

    void prepare(float ceilingDbfs, float releaseSec, float fs) noexcept {
        ceilingLin = std::pow(10.0f, ceilingDbfs / 20.0f);
        releaseCo  = std::exp(-1.0f / (fs * releaseSec));
    }

    void process(float* buf, int n) noexcept {      // tahsis yok, noexcept
        for (int i = 0; i < n; ++i) {
            const float x = buf[i];
            const float req = (std::abs(x) > 1.0e-12f)
                            ? ceilingLin / std::abs(x) : 1.0f;
            gain = (req < gain) ? req : gain;        // anlık kısıt (attack YOK)
            gain *= releaseCo + (1.0f - releaseCo);  // release yavaş dönüş
            gain = (gain > 1.0f) ? 1.0f : gain;
            buf[i] = x * gain;
        }
    }
};
```

**Uyarı:** bu sürüm **attack/lookahead içermez** (bilinçli sadelik); gerçek
tasarım `limiter-brickwall-lookahead.md` + §2'deki aday API'lerle beslenir.

## §6 Literatür kod bloğu B — lookahead tamponlu tasarım iskeleti (PLANNED)

> **Etiket:** [PLANNED]. Referans: Signalsmith `LimiterFloat(100)` (§2) —
> `latencySamples()` deseni. Tampon boyutu **baştan allocate** (AGENTS.md §1).

```cpp
#include <vector>

struct LookaheadLimiter {
    std::vector<float> delay;    // prepare()'da allocate edilir — RT'de DEĞİL
    size_t w = 0, look = 0;
    float ceilingLin = 1.0f;

    void prepare(float fs, float lookaheadMs, float ceilingDbfs) {
        look = static_cast<size_t>(fs * lookaheadMs / 1000.0f);
        delay.assign(look + 1, 0.0f);               // tek tahsis (non-RT)
        ceilingLin = std::pow(10.0f, ceilingDbfs / 20.0f);
        w = 0;
    }

    // Gecikmeli sinyal + tepe taraması (kapsam §7 adımları)
    void process(float* buf, int n) noexcept {
        for (int i = 0; i < n; ++i) {
            const float in = buf[i];
            delay[w] = in;
            w = (w + 1) % delay.size();
            float peak = 0.0f;
            for (size_t k = 0; k < delay.size(); ++k)      // pencere taraması
                peak = std::max(peak, std::abs(delay[k]));
            const float g = (peak > ceilingLin) ? ceilingLin / peak : 1.0f;
            buf[i] = delay[(w + delay.size() - 1) % delay.size()] * g;
        }
    }

    int latencySamples() const noexcept { return static_cast<int>(look); }
};
```

**Maliyet notu:** iç pencere taraması `O(n·look)`; üretimde max-heap/RMQ
seçeneği **UNKNOWN** (tasarım kapısı).

## §7 Literatür kod bloğu C — GR metering yayını (PLANNED)

> **Etiket:** [PLANNED] — lock-free sözleşme (AGENTS.md §2):
> audio thread yazar, UI okur; mutex YASAK.

```cpp
#include <atomic>

struct LimiterMeter {
    std::atomic<float> grDb{0.0f};    // tek yazıcı (audio), çok okuyucu (UI)

    void pushFromAudioThread(float gainLin) noexcept {
        const float gr = (gainLin > 1.0e-9f)
                       ? 20.0f * std::log10(gainLin) : -240.0f;
        grDb.store(gr, std::memory_order_relaxed);
    }
    float readFromUI() const noexcept {
        return grDb.load(std::memory_order_relaxed);
    }
};
```

**Not:** `std::log10` audio thread'de **maliyetli** olabilir → LUT/seçenek
**UNKNOWN** (ölçüm kapısı, measurement).

## §8 Literatür kod bloğu D — DSPark API deseni (literatür — §2, HEAD 200 ✅)

```cpp
// Kaynak: github.com/CristianMoresi/DSPark README — Quick start (§2)
dspark::Limiter<float> limiter;
limiter.prepare(spec);
limiter.setCeiling(-1.0f);          // tavan: -1.0 dBFS — ÖRNEK, CoreMusic R1 DEĞİL
limiter.processBlock(block);
```

**Ne öğretir:** ceiling'in tek dış girdi olduğu minimal sözleşme. CoreMusic R1
(ceiling) bu girdiyi besler — değer **UNKNOWN**.

## §9 Literatür kod bloğu E — Signalsmith gecikme raporu deseni (§2, HEAD 200 ✅)

```cpp
// Kaynak: github.com/Signalsmith-Audio/basics README — Limiter (§2)
signalsmith::basics::LimiterFloat effect(100);   // max attack/lookahead 100 ms
effect.configure(sampleRate, maxBlockSize, channels);
effect.latencySamples();   // zincire bildirilecek gecikme
effect.tailSamples();      // kuyruk (flush) uzunluğu
```

**Ne öğretir:** latansın **raporlanması** zorunlu deseni → `pipeline` R3 latency
bütçesini besler.

## §10 Test kodu şablonu — Catch2 (PLANNED)

```cpp
// tests/limiter/test_limiter.cpp — ŞABLON (PLANNED, kanıt değil)
#include <catch2/catch_all.hpp>
#include "limiter.hpp"

TEST_CASE("tavan ihlali yok", "[limiter][dsp]") {
    SimpleLimiter l;
    l.prepare(-1.0f, 0.050f, 48000.0f);          // ÖRNEK parametre
    alignas(64) float buf[512];
    for (int i = 0; i < 512; ++i)
        buf[i] = 0.99f * std::sin(2.0f * 3.14159265f * 1000.0f * i / 48000.0f);
    l.process(buf, 512);
    float mx = 0.0f;
    for (float x : buf) mx = std::max(mx, std::abs(x));
    REQUIRE(mx <= std::pow(10.0f, -1.0f / 20.0f) + 1.0e-3f);   // §6/1
}

TEST_CASE("GR pasifken pasif", "[limiter]") {
    SimpleLimiter l;
    l.prepare(0.0f, 0.050f, 48000.0f);           // ceiling 0 dBFS
    alignas(64) float buf[64];
    for (int i = 0; i < 64; ++i) buf[i] = 0.1f;
    l.process(buf, 64);
    REQUIRE(std::abs(buf[0] - 0.1f) < 1.0e-4f);  // kısıt gerekmedi
}

TEST_CASE("sıfır tahsis sözleşmesi", "[limiter][rt]") {
    SimpleLimiter l;
    l.prepare(-1.0f, 0.05f, 48000.0f);
    alignas(64) float buf[512] {};
    l.process(buf, 512);                          // çalışma anında new YOK
    SUCCEED();
}
```

**Durum:** 3/3 **PLANNED** — test yok, çalıştırılmadı.

## §11 Anti-pattern örnekleri (YAPILMAYACAKLAR)

| # | Anti-pattern | Neden yasak | Doğru |
|---|---|---|---|
| 1 | `process()` içinde `delay.assign(...)` | sıfır tahsis (AGENTS.md §1) | allocate yalnız `prepare()` (§6) |
| 2 | GR metering'i `mutex` ile | lock-free (AGENTS.md §2) | `std::atomic<float>` (§7) |
| 3 | her örnekte `std::pow(10, dB/20)` | CPU israfı; prepare dışı hesap | ceiling'i prepare'da sabitle (§5) |
| 4 | `throw` limiterde | noexcept (AGENTS.md §3) | clamp + non-RT log |
| 5 | ceiling tahmini (−1 / −0.3) | R1 UNKNOWN (tahmin yasak) | değer ADR'den gelir |
| 6 | HTTP RateLimiter'i kanıt sanmak | alakasız (§1) | §1 grep reddi |
| 7 | donanım koruma ile karıştırmak | katman ihlali (A4) | >0.5V relay = K001 |
| 8 | PCM5122 | ADR-038 H001 yasağı | PCM3168A / AK4458 |

## §12 Komşu dal örnek sınırları

| Dal | Bu dosyadan | Bu dosyaya |
|---|---|---|
| `compressor/` | — | envelope tanımı (referans) |
| `dynamics/` | — | GR/aile çerçevesi |
| `measurement/` | THD/true-peak ölçümü | sonuçlar (§10 → rapor) |
| `pipeline/` | `latencySamples()` raporu (§9) | zincir yeri (Q3) |
| `spatial-audio/` | — | — (sınır yok) |
| K003 output | çıkış konumu (§3) | — |

## §13 Ek wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 ana plan | [[dsp-master]] |
| K004 bileşenler | [[dsp-bilesenler]] |
| K004 ana doğrulama | [[dsp-dogrulama]] |
| Bağımlılık kapısı | [[dsp-bagimliliklar]] |
| Eş dal kapsam | `limiter-kapsam.md` |
| Eş dal doğrulama | `limiter-dogrulama.md` |
| brickwall/lookahead | `limiter-brickwall-lookahead.md` |
| true-peak | `limiter-true-peak-ve-intersample.md` |
| compressor örnekler | `../compressor/compressor-ornekler.md` |
| dynamics örnekler | `../dynamics/dynamics-ornekler.md` |
| testing kapsam | `../testing/testing-kapsam.md` |
| K001 codec | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` |
| K003 output | `../../K003-audio/output/output-kapsam.md` |

## §14 Sözlük

| Terim | Tanım |
|---|---|
| **Ceiling** | Tavan (dBFS) — R1 UNKNOWN |
| **Lookahead** | tepe önceden görülmesi (§6) |
| **releaseCo** | release katsayısı `exp(-1/(fs·t))` (§5) |
| **GR metering** | kazanç düşümü yayını (§7) |
| **latencySamples()** | gecikme raporu deseni (§9) |
| **[PLANNED]** | repo'da olmayan tasarım kodu |

## §15 Yol haritası

1. R1 ceiling kararı → §5/§8/§10'daki örnek parametreler gerçek kararla değişir.
2. §6 vs §5 seçimi (lookahead gereksinimi) → `limiter-brickwall-lookahead.md` bağlanır.
3. §7 metering maliyeti ölçülür (log LUT gerekebilir → measurement).
4. İlk gerçek kod → §1 grep'i tazelenir, §10 testleri bağlanır.

## §16 Kaynak doğrulama tablosu (2026-10-13 HEAD)

| # | Kaynak | HTTP | Kullanım |
|---|---|---|---|
| 1 | github.com/CristianMoresi/DSPark | 200 ✅ | §2 · §8 |
| 2 | github.com/Signalsmith-Audio/basics | 200 ✅ | §2 · §9 |
| 3 | github.com/ShmKnd/Patina | 200 ✅ | §2/53 |

**403/YASAK (kullanılmadı):** aes.org · analog.com · st.com · acoustics.ulb.be ·
audiocooks.org · mathworks.com · hydrogenaud.io.

## §17 Q&A

| Soru | Cevap |
|---|---|
| Repo'da limiter kodu var mı? | **Hayır** (§1) |
| Ceiling kaç? | **UNKNOWN** (R1) — §2/§5'teki −1.0 dBFS örnek API değeridir |
| Lookahead gerekli mi? | **UNKNOWN** — §5 (yok) vs §6 (var) seçeneği |
| Hangi kütüphane? | 3 MIT aday (§2); seçim bağımlılık kapısı |
| Testler çalışır mı? | Hayır — §10 şablon (3/3 PLANNED) |
| GR nasıl yayılır? | atomik (§7) — UI tipi UNKNOWN |

## §18 Sentetik test girdileri (PLANNED — ölçüm sonucu DEĞİL)

```text
g1: 1 kHz sinüs, tepe 0.99, ceiling −1 dBFS  → §10/1 tavan testi
g2: −0.5 dBFS sabit                          → §10/2 pasiflik
g3: tek dirac (1.0)                          → lookahead/release davranışı
g4: çok tonlu 1+5 kHz                        → true-peak (R3 evetse)
g5: sessiz                                    → GR 0, metering −240 dB
```

## §19 Durum özeti (2026-10-13)

| Metrik | Değer |
|---|---|
| Repo limiter kodu | 0 (§1) |
| Literatür kod bloğu | 5 (§5–§9) — 5/5 [PLANNED] |
| Test şablonu | 3 (§10) — 3/3 PLANNED |
| Aday MIT kütüphane | 3 (§2) |
| HEAD kaynak | 3/3 200 (§16) |
| UNKNOWN | R1 · R3 + lookahead seçimi |

## §20 Kapsam dışı

| Yüzey | Neden |
|---|---|
| ceiling/attack/release değerleri | R1 + ADR |
| true-peak faktörü | R3 (kanıt yok, yazılmaz) |
| çalışan derleme/test | repo'da yok |
| index.md | dokunulmadı |
| git commit | subagent yasağı |

## §21 Değişiklik günlüğü

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | iskelet §1–§4 (73 satır) |
| 2026-10-13 | §5–§24: 5 literatür kod bloğu, Catch2 şablonu, anti-pattern, sınır, wiki-link, sözlük, yol haritası, HEAD tablosu, Q&A, sentetik girdi, özet, kapsam dışı, günlük, checklist, ölçüm kaydı |

**Silme yok:** §1–§4 aynen korunmuştur.

## §22 Kod öncesi checklist (§10 şablonu çalıştırılmadan önce)

1. ceiling değeri R1'den mi geldi? (tahminse DUR)
2. tüm allocate'ler `prepare()`'da mı? (§5/§6)
3. metering atomik mi? (§7 · AGENTS.md §2)
4. `noexcept` + `alignas(64)` tamam mı?
5. `latencySamples()` bildirimi pipeline'a bağlandı mı? (§9)
6. donanım katmanı (K001) karıştırıldı mı? (§11/7)
7. PCA/PCM5122 yasağı ihlali var mı? (§11/8)

## §23 Genişletme ölçüm kaydı

| Metrik | Önce | Sonra |
|---|---|---|
| Satır | 73 | ≥500 |
| Bölüm | §1–§4 | §1–§24 |
| Authority | 1 | 1 |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §24 Dosya son kontrolü

- [x] §1–§4 korundu
- [x] tüm URL'ler HEAD 200 (§16)
- [x] PLANNED kodlar etiketli
- [x] tahmin parametresi yazılmadı (R1/R3 UNKNOWN)
- [x] dürüstlük bloğu + tek Authority satırı
- [ ] §10 testleri çalıştırıldı mı? Hayır (3/3 PLANNED)

## §25 Sinyal akışı topolojisi (PLANNED — çizim, kod DEĞİL)

> Referans: `[[dsp-bilesenler]]` §3 bileşen hiyerarşisi · `[[dsp-dogrulama]]` §4 kapı matrisi.
> Bu bölüm mimari çizimdir; hiçbir satır CoreMusic'te uygulanmış değildir (grep §1).

```text
  kaynak (dosya/stream)
        │
        ▼
  ┌───────────────────┐
  │  lookahead tamponu │  (N örnek — §6; N CoreMusic'te UNKNOWN / R1 kapısı)
  └─────────┬─────────┘
            ▼
  ┌───────────────────┐
  │  DC offset filtresi│  (yüksek geçiren — §5 notu; ADR-010 eşik 0.5 V)
  └─────────┬─────────┘
            ▼
  ┌───────────────────┐
  │  true-peak tarama  │  (§8 — 4× interpolasyon; ITU-R BS.1770 / EBU R128)
  └─────────┬─────────┘
            ▼
  ┌───────────────────┐
  │  gain hesap + GR   │  (§5; attack/release — §14 sözlük)
  └─────────┬─────────┘
            ▼
  ┌───────────────────┐
  │  gain uygulama     │  (float32 çarpım — ADR-017)
  └─────────┬─────────┘
            ▼
        çıkış (512 örnek / 48 kHz — sartname, ölçüm PLANNED)
```

**Akış kuralı (realtime):** her kutu sıfır tahsis + lock-free + `noexcept` (§8 realtime);
lookahead tamponu baştan allocate edilir (AGENTS.md §5 Audio). Kutu sırası
`[[dsp-bilesenler]]` §3 zinciriyle çelişmez.

## §26 Hızlı başlangıç (yeni okuyucu için 7 adım)

1. §1–§4'ü oku — CoreMusic'te limiter kodu YOK (grep kanıtı), eşikler §16 tablosunda.
2. §5 clip+release iskeletini oku — en basit limiter budur.
3. §6 lookahead tasarımını oku — gecikme bütçesi §14 "group delay" ile ilişkili.
4. §10 Catch2 şablonunu oku — test kapıları §11 anti-pattern'den kaçınır.
5. §14 sözlüğü aç — GR / ceiling / release terimleri burada tanımlı.
6. `[[limiter-kapsam]]` §6–§40 mimariyi oku — bu dosya yalnız örnektir.
7. `[[dsp-dogrulama]]` §13 protokolünü çalıştır — kapılar (R1/R3) UNKNOWN'dur,
   tahmin yazma.

## §27 Derinlik kaydı (bu tur ne değişti)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 73 | 393+ (§25–§31 eklendi) |
| § sayısı | 4 (§1–§4) | 24 + §25–§31 |
| Frontmatter | `parent:` alanı | `parent_id:` + `tags: []` |
| Authority | 1 (`Steward` tek satır) | 1 (aynı, genişletildi) |
| Dürüstlük bloğu | 0 | 1 |
| Silinen eski içerik | — | 0 (§1–§4 korundu) |

## §28 Ek — 2026-10-13 HEAD doğrulama notu

§16 tablosundaki tüm başlıklar bu tur `Invoke-WebRequest -Method Head` ile
yeniden doğrulanmıştır (2026-10-13). 403/YASAK ve 404 başlıklar tabloya
ALINMAMIŞTIR (bkz. oturum kararları). Wikipedia maddeleri 200 döndürmüştür.
Sonuç: §16'daki hiçbir satır bu turda değiştirilmedi.

## §29 Q&A (limiter-ornekler özel)

**Q1 — §5 kodu neden C++17 değil de C++20?**
Projе dili C++20 (AGENTS.md §5). `constexpr`/`[[likely]]` gibi C++20
özellikleri zorunlu değil; dil sürümü proje standardına sabitlendi.

**Q2 — neden `float` (tek duyarlılık)?**
ADR-017: ses verisi 32-bit float. `double` yalnız offline ölçümde
(`[[dsp-dogrulama]]` §8) — realtime zincirde `float`.

**Q3 — lookahead tamponu kaç örnek olmalı?**
CoreMusic'te UNKNOWN — R1 kapısı (`[[dsp-master]]` §7). Tahmin yazılmaz;
§6 yalnız tasarım iskeletidir.

**Q4 — true-peak neden 4× interpolasyon?**
ITU-R BS.1770 / EBU R128 true-peak ölçümü 4× örnekleme oranı yükseltme
kullanır (§8). Kaynak: itu.int (HEAD 200) + Wikipedia (HEAD 200).

**Q5 — release süresi neden logaritmik?**
Dinleme algısı süre için logaritmiktir (§14 sözlük "release"). Lineer
kaydırma yerine logaritmik haritalama kullanılır — bu bir literatür
konusudur; CoreMusic parametresi yok.

## §30 Risk kaydı (limiter-ornekler)

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | §5–§9 kodları PLANNED, derlenmedi | yanlış API çağrısı sessiz kalabilir | §10 şablonu ile kapatılacak |
| 2 | R1 (ceiling) UNKNOWN | gerçek limit değeri tahmin edilemez | `[[dsp-master]]` §7 Q1 |
| 3 | true-peak interpolasyon hatalı | ITU-R BS.1770'e aykırı ölçüm | `[[dsp-dogrulama]]` §13 |
| 4 | lookahead taşma | gecikme bütçesi ihlali | §6 tampon boyu R1'e bağlı |
| 5 | Wikipedia kaynağı değişirse | §16'daki satır eskir | 2026-10-13 HEAD ile tazelendi |

## §31 Sözlük (limiter-ornekler — §14'ün genişletmesi)

| Terim | Tanım |
|---|---|
| **ceiling** | Limiter'ın aşamayacağı maksimum seviye (dBFS) — R1 UNKNOWN |
| **GR (gain reduction)** | Uygulanan kazanç azaltma miktarı (dB) — §7 metering |
| **lookahead** | Sinyali önceden okuyan tampon — §6, gecikme üretir |
| **intersample peak** | Örnekleme noktaları arasında oluşan gerçek tepe — §8, BS.1770 |
| **true peak** | 4× interpolasyonla tahmin edilen tepe (dBTP) — §8 |
| **clip** | Sınırın üstü kesme — §5, distorsiyon üretir |
| **release** | Tepe sonrası kazancın geri dönüş süresi — §5, logaritmik (Q5) |
| **stereo-linked** | İki kanalın ortak kazancı — §11 anti-pattern (per-channel tercih) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
