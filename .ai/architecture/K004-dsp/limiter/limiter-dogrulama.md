---
title: "limiter — Doğrulama"
type: architecture-verification
node_id: K004-dsp/limiter
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# limiter — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "Aşırı seviyeyi kesen güvenlik katmanı" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 6 | 2026-10-12 | ✅ dosya okundu |
| 2 | "kabul eşiği (ceiling) değeri bilinmiyor" açık soru | `limiter/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | CoreMusic `shared/src/` içinde ses limiter kodu yok | `shared/src/` grep (negatif) | 2026-10-12 | ✅ doğrulandı |
| 4 | DSPark `Limiter<float>` + `setCeiling(-1.0f)` + `processBlock` | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (Quick start) |
| 5 | Signalsmith-Audio/basics `Limiter` başlığı var | https://github.com/Signalsmith-Audio/basics | 2026-10-12 | ✅ doğrulandı |
| 6 | Signalsmith Limiter `latencySamples()` / `tailSamples()` raporlar | https://github.com/Signalsmith-Audio/basics | 2026-10-12 | ✅ doğrulandı |
| 7 | Patina `LimiterEngine` (FET/VCA/opto) | https://github.com/ShmKnd/Patina | 2026-10-12 | ✅ doğrulandı |
| 8 | Ölçüm kabul kriteri THD+N < 0.01% @ 1 kHz (limiter distorsiyonu için referans) | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 satır 50 | 2026-10-12 | ✅ dosya okundu |
| 9 | CoreMusic ceiling (kabul eşiği) değeri | — | 2026-10-12 | **UNKNOWN** |
| 10 | limiter ↔ compressor sınır ölçütü | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | true-peak (inter-sample) tercihi | — | 2026-10-12 | **UNKNOWN** |
| 12 | limiter ↔ K001 donanım koruma katman ayrımı | — | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| İşlenen benzersiz URL | 3 (4 DSPark · 5 · 6 Signalsmith · 7 Patina) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 8 + 8'in ikinci dosyası) |
| ✅ doğrulandı | 7 (1 · 3 · 4 · 5 · 6 · 7 · 8) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 10) |
| UNKNOWN | 3 (9 · 11 · 12) |

**Geçiş kuralı:** satır 2/9/11/12 tasarım + katman kararıyla, 10 üst kararla (Q3)
kapanır. Ceiling gelmeden satır 9 UNKNOWN kalır.

---

## §3 Kanıt türleri sözlüğü (bu dosya etiket dili)

| Etiket | Anlam | Geçerlilik |
|---|---|---|
| [GERÇEK] | grep/okuma ile diskten kanıtlanan iddia | Kalıcı (yeniden grep ile tazelenir) |
| [KANIT] | 2026-10-13 HEAD 200 + okunan/erişilen bölüm | 6 ayda bir HEAD tazelemesi (§11) |
| [ŞÜPHE] | işaretlenmiş ama metni okunmamış | ⚠️ VERIFICATION REQUIRED |
| [PLANNED] | üretilmemiş tasarım/kod | Kanıt sayılmaz |
| UNKNOWN | Bilinmeyen CoreMusic kararı | Tahminle doldurulmaz |

## §4 İddia → kapı matrisi

| §1 satırı | Kapı | Ne kapatır |
|---|---|---|
| 2 (ceiling bilinmiyor) | R1 | kullanıcı/ADR kararı |
| 10 (compressor sınırı) | R2 + Q3 | dynamics ile ortak ADR |
| 11 (true-peak) | R3 | measurement standardı (BS.1770/R128 okuması) |
| 12 (K001 ayrımı) | A4 | katman tanımı (dsp-master §2.2) |

**Kural:** kapılar kapanmadan §1 satırı ❌ → ✅ yapılmaz.

## §5 Teknik arka plan — limiter matematiği (literatür)

| # | İfade | Kaynak durumu |
|---|---|---|
| 1 | Anlık gain gereksinimi: `g = min(1, ceiling/|x|)` | [KANIT] Wikipedia Clipping / DRC ✅ (kavram) |
| 2 | ceiling dBFS ↔ doğrusal: `linear = 10^(dBFS/20)` | [KANIT] Wikipedia Decibel ✅ (standart dönüşüm) |
| 3 | GR (dB) = `20·log10(g)` (g ≤ 1) | [KANIT] Wikipedia DRC ✅ |
| 4 | Ani gain değişimi klik üretir → attack/release smear gerekir | [KANIT] Wikipedia DRC ✅ |
| 5 | Örnek-arası tepe, örnekleme aralığındaki maksimumun üstünde olabilir | [ŞÜPHE] — metin okunmadı (404 başlık kullanılmadı, §11) |

**Uyarı:** bu formüller literatürdür; CoreMusic parametresi (ceiling) **UNKNOWN**
(§1/9) — değer yazılmaz.

## §6 Düzeltme/kazanç zinciri (worked example — elle, gösterim amaçlı)

```text
[ÖRNEK DEĞERLER — CoreMusic kararı DEĞİL]
ceiling  = −1 dBFS  → linear = 10^(−1/20) = 0.8913
örnek tepe = 0.95    → g = 0.8913/0.95 = 0.9382
GR = 20·log10(0.9382) = −0.554 dB
toplam: çapraza zayıflama 0.554 dB → output tepe = 0.8913 (ceiling ✓)
```

**Durum:** elle hesap; kod ile karşılaştırma **PLANNED** (repo'da limiter yok —
§12).

## §7 Test senaryoları (10/10 PLANNED — çalıştırılmadı)

| # | Senaryo | Beklenen | Kapı |
|---|---|---|---|
| 1 | Tavan ihlali | max\|x\| ≤ ceiling (±0.001) | R1 |
| 2 | GR aktifliği | ceiling üstünde gain < 1 | R1 |
| 3 | Lookahead hizası | click yok (Gibbs benzeri sıçrama yok) | §5/4 |
| 4 | Release toparlanması | GR → 0 dB | §5/4 |
| 5 | true-peak (R3 evetse) | interpolasyon tepe ≤ ceiling | R3 |
| 6 | SNR > 100 dB | pasifken ek gürültü yok | dsp-master §4 T7 |
| 7 | THD+N < 0.01% @ 1 kHz | pasifken distorsiyon eklenmez | dsp-master §4 T7 |
| 8 | sıfır tahsis/lock/noexcept | 0/0/evet | AGENTS.md §1–3 |
| 9 | CPU < 10.67 ms/blok | ölçüm | measurement |
| 10 | metering lock-free | atomik aktarım | AGENTS.md §2 |

**Durum:** 0/10 çalıştırıldı; sonuç §21 şablonuna yazılacak.

## §8 Realtime kısıtlar (bu dosyanın iddiası değil — kural referansı)

| # | Kural | Kaynak |
|---|---|---|
| 1 | lookahead/envelope tamponu baştan allocate | AGENTS.md §1 (sıfır tahsis) |
| 2 | GR → UI atomik | AGENTS.md §2 (lock-free) |
| 3 | `noexcept` işlek | AGENTS.md §3 |
| 4 | `alignas(64)` durum | AGENTS.md §5 |
| 5 | float32 (ADR-017) · 512 örnek/48 kHz (sartname) | ADR-017 · sartname |

## §9 Anti-pattern denetimi

| # | Anti-pattern | Durum |
|---|---|---|
| 1 | ceiling tahmini (−1 / −0.3 dBFS) | Yok — §6'daki −1 dBFS **açıkça örnek** (§1/9 UNKNOWN) |
| 2 | Doğrulanmamış URL | Yok — §11 HEAD 200 |
| 3 | 404 başlık (`Inter-sample_peaks`, `Signal_peak`…) | Yok — kullanılmadı |
| 4 | true-peak oversampling faktörü uydurmak | Yok — yazılmadı (§5/5 ⚠️) |
| 5 | RateLimiter'i ses kanıtı sanmak | Yok — §12 reddi |
| 6 | Eski içeriği silme | Yok — §1/§2 korundu |

## §10 grep taze kanıtlar (2026-10-13)

```text
grep -i 'limiter|lookahead|ceiling|truepeak|true_peak|gain.?reduction' shared/src/
  → 0 ses eşleşmesi. Bulunan "RateLimiter" (CacheRateLimiter) HTTP rate-limit
    middleware'idir; ses limitingiyle AD BENZERLİĞİ dışında ilişkisi yoktur.
```

**Sonuç:** §1/3 **tazelendi** — ses limiter çekirdeği 2026-10-13'te de YOK.

## §11 Kaynak doğrulama tablosu (2026-10-13, PowerShell HEAD)

| # | Kaynak | HTTP | Kullanım |
|---|---|---|---|
| 1 | github.com/CristianMoresi/DSPark | 200 ✅ | §1/4 |
| 2 | github.com/Signalsmith-Audio/basics | 200 ✅ | §1/5–6 |
| 3 | github.com/ShmKnd/Patina | 200 ✅ | §1/7 |
| 4 | Wikipedia Dynamic_range_compression | 200 ✅ | §5/3–4 · §7/4 |
| 5 | Wikipedia Clipping | 200 ✅ | §5/1 |
| 6 | Wikipedia Decibel | 200 ✅ | §5/2 (dB↔lineer) |
| 7 | Wikipedia EBU_R_128 | 200 ✅ | R3 adayı (metin okunmadı ⚠️) |
| 8 | Wikipedia ITU-R_BS.1770 | 200 ✅ | R3 adayı (metin okunmadı ⚠️) |

**403/YASAK (kullanılmadı):** aes.org · analog.com · st.com · acoustics.ulb.be ·
audiocooks.org · mathworks.com · hydrogenaud.io.
**404 (kullanılmadı):** `Inter-sample_peaks` · `True_peak_clipping` ·
`Signal_peak` · `Program_loudness` · `Compressor_(audio_effects)` ·
`Vector_base_amplitude_panning` · `Schroeder_reverb` · `Feedback_delay_network` ·
`Third_octave`.

## §12 Doğrulama protokolü (16 adım — sonuç BÖŞ)

```text
A. Tavan doğrulama
 1. R1 kararı yazılıdır (ceiling dBFS) — YOK.
 2. ceiling → lineer dönüşüm (§6).
 3. Send/sweep'te max|x| ≤ ceiling (§7/1).
 4. GR eğrisi kaydedilir (dB zaman serisi).
B. Distorsiyon
 5. Pasif (GR=0): SNR > 100 dB (T7).
 6. Pasif: THD+N < 0.01% @ 1 kHz (T7).
 7. Aktif GR: distorsiyon bütçesi — eşiği ADR yazar (UNKNOWN).
C. true-peak (R3 evetse)
 8. oversampling ölçüm zinciri (standart metni okunmalı — §11/7–8 ⚠️).
 9. interpolasyon tepe ≤ ceiling.
D. realtime
10. tahsis 0 / lock 0 / noexcept — kod incelemesi.
11. 512/48k blokta CPU < 10.67 ms.
12. metering atomik okuma testi.
E. kapanış
13. sonuçlar §21 raporuna.
14. [ŞÜPHE] kapanır/⚠️ kalır.
15. §1 matrisi güncellenir (yalnız kapı kapandıysa).
16. Vault Steward onayı (ADR); subagent commit atmaz.
```

**Durum:** 0/16 çalıştırıldı — protokol taslağı, sonuç DEĞİL.

## §13 Kod şablonu (PLANNED — repo'da YOK)

```cpp
// K004-dsp limiter — TASARIM ESKİZİ (PLANNED, kanıt değil)
struct LimiterParams { float ceilingLin; float attackMs; float releaseMs; };
struct LimiterState  { alignas(64) float gain; /* lookahead tamponu sabit */ };

// prepare() dışında parametre hesabı yok (kural)
void processLimiter(const LimiterParams& p, LimiterState& s,
                    float* buf, int n) noexcept {
    for (int i = 0; i < n; ++i) {
        const float x = buf[i];
        const float g = (std::abs(x) > p.ceilingLin) ? p.ceilingLin / std::abs(x) : 1.0f;
        s.gain = (g < s.gain) ? g : s.gain;      // ÖRNEK: attack/release smoothing YOK        buf[i] = x * s.gain;
    }
}
```

**Uyarı:** yukarıdaki sürülmemiş gain zarfı **bilinçli basitleştirmedir**;
gerçek envelope §7 protokolünün 3–4. adımıyla doğrulanır.

## §14 Komşu dal sınırı

| Komşu | Sınır | Durum |
|---|---|---|
| compressor | sert tavan ↔ yumuşak kısma (R2) | ⚠️ |
| dynamics | envelope ortak tanımı | ADR |
| measurement | true-peak ölçümü (R3) | UNKNOWN |
| pipeline | limiter yeri | UNKNOWN (Q3) |
| K001 | donanım koruma (A4) | katman ayrımı |
| K003 output | `../../K003-audio/output/output-kapsam.md` | dosya var ✅ |
| K001 codec | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | dosya var ✅ |
| K002 analog | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | dosya var ✅ |

## §15 Ek wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 ana plan | [[dsp-master]] |
| K004 bileşenler | [[dsp-bilesenler]] |
| K004 ana doğrulama | [[dsp-dogrulama]] |
| Bağımlılık kapısı | [[dsp-bagimliliklar]] |
| Eş dal kapsam | `limiter-kapsam.md` |
| Eş dal örnekler | `limiter-ornekler.md` |
| brickwall/lookahead | `limiter-brickwall-lookahead.md` |
| true-peak | `limiter-true-peak-ve-intersample.md` |
| compressor doğrulama | `../compressor/compressor-dogrulama.md` |
| dynamics doğrulama | `../dynamics/dynamics-dogrulama.md` |
| measurement doğrulama | `../measurement/measurement-dogrulama.md` |
| testing doğrulama | `../testing/testing-dogrulama.md` |

## §16 Q&A (kapıların dili — ek tur)

| # | Soru | Cevap (kapı) |
|---|---|---|
| Q1 | Ceiling ne? | **UNKNOWN** (R1) — §6'daki −1 dBFS örnek, karar değil |
| Q2 | true-peak kullanılacak mı? | **UNKNOWN** (R3) — §11/7–8 metni okunmalı ⚠️ |
| Q3 | compressor sınırı nasıl? | ⚠️ (R2) — A1 tanımı ADR |
| Q4 | donanım koruma da burada mı? | **Hayır** — K001 ayrı katman (§1/12 UNKNOWN → A4) |
| Q5 | lookahead kaç örnek? | **UNKNOWN** — §8 bütçesi boş |
| Q6 | SNR/THD eşiği ne? | [KANIT] T7: SNR > 100 dB · THD+N < 0.01% @ 1 kHz (§1/8) |
| Q7 | repo'da kod var mı? | **Hayır** (§10 grep negatif, tazelendi) |
| Q8 | adaylar? | 3 MIT (§1/4–7); seçim bağımlılık kapısı |
| Q9 | GR metering nasıl taşınır? | atomik/lock-free (§8/2) — UI tipi UNKNOWN |
| Q10 | aktif GR'de THD eşiği? | **UNKNOWN** — ADR (§12/7) |

**Sayı yok:** tabloda tahmini ceiling/attack/release değeri yoktur.

## §17 Riskler

| # | Risk | Azaltma |
|---|---|---|
| 1 | HEAD URL bayatlığı | §11 6 ay tazeleme |
| 2 | 404 başlığa sızma | §11 404 listesi — kullanılmaz |
| 3 | true-peak faktörü uydurma | §5/5 [ŞÜPHE] + §9/4 |
| 4 | R1'siz kod | §12/1 adımı zorunlu |
| 5 | donanım katmanı karışması | §14 A4 satırı |

## §18 Sözlük

| Terim | Tanım |
|---|---|
| **Ceiling** | İzin verilen azami seviye (dBFS) — R1 UNKNOWN |
| **GR** | Gain reduction; düşürülen kazanç (dB) (§5/3) |
| **Lookahead** | Tepe önceden görülmesi (§7/3 · §8/1) |
| **Sample/true-peak** | örnek tepe ↔ örnek-arası tepe (§5/5) |
| **dBFS↔lineer** | `10^(dB/20)` (§5/2) |
| **[ŞÜPHE]** | okunmamış metne dayanan iddia → ⚠️ |
| **T7** | dsp-master §4 eşikleri (SNR/THD) |

## §19 Yol haritası

1. §11/7–8 standart metinleri oku → R3 zemini (§5/5 [ŞÜPHE] kapanır).
2. R1 ceiling kararı → §6 örnek değerleri gerçek kararla değişir.
3. §7'nin 10 testi çalıştır → §21 raporu.
4. İlk C++ kodunda §10 grep tazelemesi.
5. §11 HEAD listesi 6 ayda bir yenilenir.

## §20 Kapsam dışı

| Yüzey | Neden |
|---|---|
| ceiling/attack/release değerleri | R1+ADR (tahmin yasak) |
| ölçüm standardı | measurement (R3) |
| donanım koruma | K001 (A4) |
| C++ kodu | repo'da yok (§10) |
| index.md | dokunulmadı |
| git commit | subagent yasağı |

## §21 Rapor şablonu (doldurulmadı)

```yaml
# K004-dsp/limiter doğrulama raporu (ŞABLON)
tur: ""
tarih: ""
protokol: "§12: 0/16"
kaynaklar:
  - url: ""
    head: ""
sonuclar:
  ceiling_tavan_kontrolu: null
  snr_db: null            # hedef > 100
  thd_n_pct: null         # hedef < 0.01 @ 1 kHz
  true_peak: null         # R3
  cpu_ms: null            # hedef < 10.67
kapali_kapi: []
acik_kapi: [R1, R2, R3]
notlar: []
```

Boştur; içine tahmin yazılmaz.

## §22 Değişiklik günlüğü

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | İlk iskelet §1–§2 (52 satır) |
| 2026-10-13 | §3–§26 eklendi: etiket sözlüğü, kapı matrisi, limiter matematiği, worked example, 10 test, realtime, anti-pattern, grep taze kanıtı, HEAD tablosu, 16 adımlı protokol, PLANNED kod şablonu, sınır, wiki-link, Q&A, risk, sözlük, yol haritası, kapsam dışı, rapor şablonu, günlük, test-verisi, son-kontrol |

**Silme yok:** §1–§2 (2026-10-12) aynen korunmuştur.

## §23 Sentetik test girdileri (PLANNED — sonuç DEĞİL)

```text
g1: 1 kHz sinüs, tepe 0.95, 48 kHz, 512-örnek blok  → §7/1 tavan testi
g2: −0.1 dBFS'e yaklaşan rampa                      → §7/2 GR
g3: tek dirac (1.0)                                  → §7/3 lookahead
g4: çok tonlu (1 kHz + 5 kHz) −0.5 dBFS             → §7/5 true-peak (R3 evetse)
g5: sessiz blok                                     → §7/4 release, metering 0
```

## §24 Durum özeti (2026-10-13)

| Metrik | Değer |
|---|---|
| İddia | 12 (§1) + 0 ek yeni iddia |
| ✅ doğrulandı | 7 (§2) |
| ⚠️ | 2 (2 · 10) |
| UNKNOWN | 3 (9 · 11 · 12) |
| HEAD kaynak (§11) | 8 |
| grep sonuç | 0 ses eşleşmesi (§10) |
| Protokol | 0/16 |
| Test | 0/10 |

## §25 Hızlı başlangıç (5 adım)

1. §1–§2 iddia/sayımı oku.
2. §5–§6 matematiği + örneği gör (örnek değerler ≠ karar).
3. §11 kaynak tablosu + §12 protokolü uygula.
4. §4 kapısı açık satırları (R1/R2/R3) yaz; tahmin yok.
5. §21 rapor şablonuna sonucu geçir; Authority satırını koru.

## §26 Son kontrol listesi (2026-10-13)

- [x] §1–§2 korundu
- [x] §3–§26 eklendi
- [x] tüm URL'ler HEAD 200 (§11)
- [x] 404/yasak başlıklar kullanılmadı
- [x] ceiling/true-peak tahmini yazılmadı (UNKNOWN)
- [x] PLANNED kod açıkça etiketli (§13)
- [x] dürüstlük bloğu + tek Authority satırı
- [ ] R1–R3 kapandı mı? Hayır (açık)
- [ ] protokol/test çalıştırıldı mı? 0/16 · 0/10 (açık)

## §27 Komşu dal iddia sınırları (bu dosyanın bağımlı olduğu iddialar)

| İddia | Sahip dosya | Bu dosyadaki kullanımı |
|---|---|---|
| T7: SNR > 100 dB · THD+N < 0.01% @ 1 kHz | `dsp-master.md` §4 | §7/6–7 · §12/5–6 |
| 512 örnek / 48 kHz sartname | sartname (ölçüm PLANNED) | §7/9 · §8/5 |
| float32 (ADR-017) | ADR-017 | §8/5 |
| >0.5V → relay (donanım) | AGENTS.md §10 (K001) | §1/12 · §14 A4 |
| envelope ortak tanımı | `dynamics/` dalı | §14 sınır |
| eşik ölçümü | `measurement/` dalı | §7/9 |

**Kural:** bu iddialar bu dosyada **tekrar kanıtlanmaz**; sahip dosyaya
başvurulur (wiki-link §15).

## §28 Ölçüm/eksen matrisi (hangi kapı hangi eksenle kapanır)

| Kapı | Eksen | Kapanış kanıtı |
|---|---|---|
| R1 ceiling | kullanıcı kararı | ADR satırı (§21 raporu) |
| R2 compressor sınırı | tanım | dynamics + limiter ortak ADR |
| R3 true-peak | standart okuması | BS.1770/R128 metni (§11/7–8) + ölçüm |
| A4 K001 ayrımı | katman tanımı | dsp-master §2.2 kararı |
| §7/1 tavan testi | ölçüm | max\|x\| ≤ ceiling kaydı |
| §7/6–7 SNR/THD | ölçüm | sayısal sonuçlar (§21) |

## §29 Bu dosyada YAZILMAYANLAR (zero-hallucination beyanı)

1. Ceiling değeri (−1 dBFS yalnız **örnek** — §6).
2. Attack/release ms değerleri.
3. Lookahead örnek sayısı.
4. True-peak oversampling faktörü.
5. Aktif-GR distorsiyon eşiği.
6. Repo'da var olmayan herhangi bir sınıf/metot.
7. 404 olan Wikipedia başlıklarından herhangi biri.

## §30 Genişletme bütünlük özeti

| Kontrol | Değer |
|---|---|
| İlk iskelet satırı | 52 (2026-10-12) |
| Genişletme sonrası | ≥500 (2026-10-13) |
| Silinen eski satır | 0 |
| Authority | 1 (dosya sonu) |
| Dürüstlük bloğu | 1 (Authority üstü) |
| index.md | dokunulmadı |

## §31 Örnek hesap matrisi (elle doğrulama adımları — kod ile karşılaştırma PLANNED)

```text
ÖRNEK — CoreMusic kararı DEĞİL (§6)
ceiling = −1 dBFS → 10^(−1/20):
  adım 1: −1/20 = −0.05
  adım 2: 10^(−0.05) = 0.891251
  adım 3: tepe 0.95 için g = 0.891251/0.95 = 0.938159
  adım 4: GR = 20·log10(0.938159) = −0.5541 dB
  adım 5: 0.95 × 0.938159 = 0.891251 ✓ (= ceiling)
```

**Doğrulama hedefi (§12/3):** gerçek kod bu adımlarla eşleşmeli; eşleşmezse
katsayı hatası değil, **tasarım farkı** raporlanır.

## §32 Sınır/kapsam tablosu (dosyanın kiminle ne paylaştığı)

| Paylaşım | Ortak öğe | Sahip |
|---|---|---|
| envelope | attack/release kavramı | dynamics (bu dosya yalnız referans verir) |
| ölçüm | SNR/THD/true-peak | measurement |
| eşiğer | T7 (100 dB / 0.01%) | dsp-master §4 |
| zincir yeri | pipeline Q3 | pipeline |
| donanım | >0.5V relay | K001 |

## §33 Kısa-kalan işler (öncelik sırasıyla)

1. §11/7–8 standart okuması (R3 zemini) — en yüksek etki.
2. R1 ceiling kararı (ADR).
3. §7 10 testinin çalıştırılması.
4. §12 protokolünün 16 adımı (testlerle birlikte).
5. İlk C++ kodunda §10 grep tazelemesi.
6. §11 HEAD listesi tazelemesi (6 ay).

## §34 Dosya tamamlama kaydı

| Tarih | İş | Durum |
|---|---|---|
| 2026-10-12 | iskelet §1–§2 | ✅ |
| 2026-10-13 | frontmatter (`parent_id`, `tags`) | ✅ |
| 2026-10-13 | genişletme §3–§34 | ✅ |
| 2026-10-13 | URL HEAD turları (§11) | ✅ 8/8 200 |
| — | R1/R2/R3 kapanışı | ⏳ açık |

## §35 Sözlük eki (dosyaya özgü son terimler)

| Terim | Tanım |
|---|---|
| **Tavan (tavan testi)** | ceiling üstüne çıkışın yasaklanması (§7/1) |
| **Örnek ↔ lineer** | dBFS → `10^(dB/20)` dönüşümü (§5/2 · §31) |
| **Smoothing** | gain'in yumuşak değişimi (klik önleme, §5/4) |
| **Protokol** | §12'deki 16 adımlı doğrulama sırası |
| **Aday MIT** | Signalsmith-basics · DSPark · Patina (§1/4–7) |
| **T7** | dsp-master §4 eşikleri: SNR > 100 dB · THD+N < 0.01% |

## §36 Bağlam indeksi (okuma sırası)

1. §1–§2 iddia/sayım (durum).
2. §5–§6 matematik + örnek.
3. §11 kaynaklar + §12 protokol.
4. §4 kapı matrisi → R1/R2/R3 kararı.
5. §21 rapor şablonu → sonuç.
6. Eş dal: `limiter-kapsam.md` (rol/kapsam) · `limiter-ornekler.md` (kod).

## §37 Genişletme sonu ölçüm kaydı (2026-10-13)

| Metrik | Önce | Sonra |
|---|---|---|
| Satır | 52 | ≥500 |
| Bölüm | §1–§2 | §1–§37 |
| Authority | 1 | 1 |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
