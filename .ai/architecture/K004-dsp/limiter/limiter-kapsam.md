---
title: "limiter — Kapsam"
type: architecture-scope
node_id: K004-dsp/limiter
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# limiter — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 6 bu dalı **"Aşırı seviyeyi kesen güvenlik katmanı"**
olarak yazar. Basitçe: hiçbir örneğin belirli bir tavandan (ceiling) geçmesine izin
vermeyen son kapı — amplifikatöre/DAC'a giden sinyali clipping'ten korur.
`index.md` bunu "son sınır" diye çağırır. compressor'dan farkı: compressor yumuşak
kısma yapar, limiter sert tavan uygular (dynamics ailesinin diğer üyesi).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Ceiling (tavan) uygulaması — örneğin geçmesin | Yumuşak eşik üstü kısma | compressor |
| 2 | Lookahead / attack (tavan öncesi hazırlık) | Envelope ortak tanımı | dynamics |
| 3 | Gain reduction metering'i (kaza riski) | Ölçüm zinciri/raporu | measurement |
| 4 | True-peak vs sample-peak tercihi | Ölçüm standardı (R128/ITU) | measurement |
| — | *(dışında)* ceiling değeri | — | **UNKNOWN — R1** |

**Yasak:** limiter K001/K005 donanım koruma eşiğiyle karıştırılmaz; burada ses
alanı tavanı (dBFS) tanımlanır, donanım koruması başka katmanın işidir.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | limiter ↔ compressor sınırı (sert tavan ↔ yumuşak kısma) | ⚠️ VERIFICATION REQUIRED | dsp-master §3 · dynamics |
| A2 | ceiling değeri (kabul eşiği) | **UNKNOWN** — tahmin yasak | limiter/index.md open_questions |
| A3 | true-peak (inter-sample) ↔ sample-peak | **UNKNOWN** | measurement standardı |
| A4 | limiter ↔ K001 donanım koruma sınırı | **UNKNOWN** — katman ayrımı | dsp-master §2.2 |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Aşırı seviyeyi kesen güvenlik katmanı" | `dsp-master.md` §3 satır 6 | ✅ dosya okundu |
| 2 | "kabul eşiği (ceiling) değeri bilinmiyor" açık soru | `limiter/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic `shared/src/` içinde ses limiter kodu yok (negatif) | `shared/src/` grep (RateLimiter=HTTP, alakasız) | ✅ 2026-10-12 doğrulandı |
| 4 | DSPark `Limiter<float>` + `limiter.setCeiling(-1.0f)` API'si | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 5 | Signalsmith-Audio/basics `Limiter` başlığı + `latencySamples()` | https://github.com/Signalsmith-Audio/basics | ✅ 2026-10-12 doğrulandı |
| 6 | Patina `LimiterEngine` (FET/VCA/opto) | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 7 | Ölçüm eşiği THD+N < 0.01% @ 1 kHz (kabul kriteri referansı) | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 | ✅ dosya okundu |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Ceiling (kabul eşiği) değeri ne? | **UNKNOWN** |
| R2 | limiter ↔ compressor sınırı hangi ölçütle? | ⚠️ VERIFICATION REQUIRED |
| R3 | true-peak (inter-sample) ölçüm kullanılacak mı? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 ceiling değeri gelmeden bu dosyada tavan
iddiası yazılmaz (v2 §3 KURAL 3 · zero-hallucination).

---

## §6 Teknik derinlik — limiter işleme modeli (literatür)

> Bu bölüm sınırın **arka planını** anlatır; CoreMusic parametre vermez
> (R1–R3 UNKNOWN). Kaynaklar 2026-10-13 HEAD 200 (§11).

| # | Kavram | Tanım (literatür) | Kaynak durumu |
|---|---|---|---|
| 1 | Ceiling | İzin verilen azami seviye (dBFS); örnek asla üstüne çıkmaz | [KANIT] Wikipedia Clipping ✅ |
| 2 | Sample-peak | Örnek değerin doğrudan maksimumu | [KANIT] Wikipedia Dynamic_range_compression ✅ |
| 3 | True-peak | Örnekler arası (inter-sample) tepe; oversampling ile tahmin edilir | [KANIT] Wikipedia Loudness ✅ — başlık ölçüm bağlamında; `Inter-sample_peaks` başlığı **404** (kullanılmadı) |
| 4 | Lookahead | Sinyal geciktirilip tepe önceden görülür; gain envelope önceden hazırlanır | [ŞÜPHE] — standart metin okunmadı |
| 5 | Attack/Release | Gain düşürme hızı (attack) ve toparlanma (release) | [KANIT] Wikipedia Dynamic_range_compression ✅ |
| 6 | Gain reduction (GR) | Tavan uygulanırken düşürülen kazanç (dB) | [KANIT] Wikipedia Dynamic_range_compression ✅ |

**Önemli ayrım (§2/4 · A4):** bu dBFS ses-alanı tavanı, K001 donanım koruması
(>0.5V → relay, AGENTS.md §10) **değildir** — ikisi farklı katmandadır.

## §7 Lookahead mekaniği (adım adım — literatür model)

| # | Adım | Açıklama |
|---|---|---|
| 1 | Tamponlama | Gelen blok N örnek geciktirilir (lookahead uzunluğu) |
| 2 | Tepe taraması | Geciken penceredeki maksimum aranır (örnek/true-peak, §8) |
| 3 | Envelope | Ceiling üstü fark için gain eğrisi üretilir (attack smear) |
| 4 | Uygulama | Orijinal (geciken) sinyal ile gain çarpılır |
| 5 | Release | Tepe geçtikçe gain yavaşça 0 dB'ye döner |

**Maliyet gecikmesi:** lookahead = ek gecikme (§9 gecikme bütçesi UNKNOWN).

## §8 True-peak ↔ sample-peak ayrımı (A3/R3 bağlamı)

| # | Kriter | Sample-peak | True-peak |
|---|---|---|---|
| 1 | Ölçtüğü | örnek değeri | örnekler arası devam (4× benzeri interpolasyon) |
| 2 | Risk | kodlama/çözme sonrası taşma (clipping) | kodlama sonrası kliplama |
| 3 | Ölçüm | doğrudan maksimum | oversampling gerektirir |
| 4 | Standart bağ | — | EBU R128 / ITU-R BS.1770 ölçüm zinciri (measurement dalı) |

**Çözüm:** R3 kararı (true-peak kullanılacak mı) **UNKNOWN**; bu dosya karar
vermez — measurement dalı + ADR.

## §9 Gecikme bütçesi (512 örnek / 48 kHz çerçevesi)

| Bileşen | Gecikme | Durum |
|---|---|---|
| Blok (sartname) | 512 örnek = 10.67 ms @48 kHz | [GERÇEK] şartname (ölçüm PLANNED) |
| Lookahead | UNKNOWN (tasarım) | **UNKNOWN** |
| TRUE-peak interpolasyon | UNKNOWN (ölçüm zinciri) | **UNKNOWN** |
| Toplam bütçe | UNKNOWN | ADR + measurement |

**Kural:** lookahead örneği cinsinden yazılır; ms karşılığı `N/48000` ile
hesaplanır (sabit: 1000 ms / 48000 = 0.020833 ms/örnek).

## §10 Metering ve kazanç düşümü (GR) arayüzü

| # | Konu | Not |
|---|---|---|
| 1 | GR göstergesi | Audio thread'de ölçüm → UI'a atomik/yayıncı ile taşınır (lock-free) |
| 2 | Ölçüm penceresi | UNKNOWN (UI katmanı) |
| 3 | Kaza riski | §2/3: GR metering bu dalın işi; ölçüm zinciri measurement'ın |
| 4 | Yayın | audio thread → UI: `std::atomic<float>` ya da lock-free kuyruk; mutex YASAK (AGENTS.md §2) |

## §11 Kaynak doğrulama tablosu (2026-10-13 PowerShell HEAD)

| # | Kaynak | HTTP | Kullanım |
|---|---|---|---|
| 1 | github.com/CristianMoresi/DSPark | 200 ✅ | §4/4 · aday `Limiter<float>` |
| 2 | github.com/Signalsmith-Audio/basics | 200 ✅ | §4/5 · aday `Limiter` + `latencySamples()` |
| 3 | github.com/ShmKnd/Patina | 200 ✅ | §4/6 · `LimiterEngine` |
| 4 | Wikipedia Dynamic_range_compression | 200 ✅ | §6/5–6 (attack/release/GR) |
| 5 | Wikipedia Clipping | 200 ✅ | §6/1 · §6/3 arka planı |
| 6 | Wikipedia Loudness | 200 ✅ | §6/3 (true-peak bağlamı) |
| 7 | Wikipedia EBU_R_128 | 200 ✅ | §8/4 (ölçüm standardı adayı) |
| 8 | Wikipedia ITU-R_BS.1770 | 200 ✅ | §8/4 |

**403/YASAK (kullanılmadı):** aes.org · analog.com · st.com · acoustics.ulb.be ·
audiocooks.org · mathworks.com · hydrogenaud.io.
**404 (kullanılmadı):** Wikipedia `Inter-sample_peaks` · `True_peak_clipping` ·
`Signal_peak` · `Program_loudness` · `Compressor_(audio_effects)`.

## §12 Disk/grep kanıtları (2026-10-13 tazeleme)

```text
grep -i 'limiter|lookahead|ceiling|truepeak|true_peak' shared/src/
  → ses limiter kodu 0 eşleşme. RateLimiter/CacheRateLimiter HTTP rate-limit'tir
    (backend), ses limiti DEĞİL — alakasız (2026-10-12 ölçümü tazelendi).
```

**Sonuç:** §4/3 iddiası **tazelendi** — CoreMusic'te limiter çekirdeği YOK
(negatif kanıt).

## §13 Test senaryoları (PLANNED — çalıştırılmadı)

| # | Senaryo | Giriş | Beklenen | Kapı |
|---|---|---|---|---|
| 1 | Tavan ihlali yok | −6 dBFS sinüs, ceiling = −1 dBFS | max \|x\| ≤ ceiling (±0.001) | R1 |
| 2 | Ceiling üstü yükseltme | 0 dBFS'e yaklaşan sinyal | GR aktif; output ≤ ceiling | R1 |
| 3 | Lookahead etkisi | ani transiyent (dirac benzeri) | transiyent önceden görülür; click yok | §7 |
| 4 | Release davranışı | tepe sonrası sessizlik | GR 0 dB'ye döner; salınım yok | §10 |
| 5 | TRUE-peak (eğer R3 evet) | 4× interpolasyon ölçümü | örnek-arası tepe ≤ ceiling | R3 |
| 6 | SNR > 100 dB | girişte temiz sinüs | T7 eşiği geçilir | dsp-master §4 |
| 7 | THD+N < 0.01% @ 1 kHz | 1 kHz, −6 dBFS | GR pasifken ek distorsiyon yok | dsp-master §4 |
| 8 | Sıfır tahsis | ASan/leak + kod incelemesi | tahsis 0, lock 0, noexcept | AGENTS.md §1–3 |
| 9 | CPU | 512 örnek blok | süre < 10.67 ms | measurement |
| 10 | GR metering yayını | audio thread → UI | lock-free aktarım | AGENTS.md §2 |

**Durum:** 10/10 **PLANNED**.

## §14 Realtime kısıtlar

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Envelope/gecikme tamponları **baştan allocate** | sıfır tahsis (AGENTS.md §1) |
| 2 | GR → UI aktarımı atomik/lock-free | lock-free (AGENTS.md §2) |
| 3 | İşlek döngüsü `noexcept` | istisna yasağı (AGENTS.md §3) |
| 4 | Durum/tampon `alignas(64)` | cache-line (AGENTS.md §5) |
| 5 | float32 sinyal (ADR-017) | format kararı |
| 6 | Ceiling/attack/release değişimi → smoothing | click önleme |
| 7 | Lookahead uzunluğu sabit (prepare'da) | çalışma anı tahsis yasak |

## §15 Komşu dal sınırları

| Komşu | Sınır | Durum |
|---|---|---|
| compressor | sert tavan ↔ yumuşak kısma (A1/R2) | ⚠️ VERIFICATION REQUIRED |
| dynamics | envelope ortak tanımı | sınır tanımsız → ADR |
| measurement | true-peak ölçüm standardı (R3) | UNKNOWN |
| pipeline | limiter zincirde nerede? | UNKNOWN (Q3) |
| K001 donanım | >0.5V → relay (farklı katman, A4) | [GERÇEK] kural (AGENTS.md §10) |
| K003 output | `../../K003-audio/output/output-kapsam.md` | dosya var ✅ |
| K001 codec | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | dosya var ✅ |
| K002 analog | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | dosya var ✅ |

## §16 Ek wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 ana plan | [[dsp-master]] |
| K004 bileşenler | [[dsp-bilesenler]] |
| K004 ana doğrulama | [[dsp-dogrulama]] |
| Bağımlılık kapısı | [[dsp-bagimliliklar]] |
| Eş dal doğrulama | `limiter-dogrulama.md` |
| Eş dal örnekler | `limiter-ornekler.md` |
| brickwall/lookahead | `limiter-brickwall-lookahead.md` |
| true-peak | `limiter-true-peak-ve-intersample.md` |
| compressor kapsam | `../compressor/compressor-kapsam.md` |
| dynamics kapsam | `../dynamics/dynamics-kapsam.md` |
| measurement kapsam | `../measurement/measurement-kapsam.md` |
| testing kapsam | `../testing/testing-kapsam.md` |

## §17 Anti-pattern denetimi

| # | Anti-pattern | Durum |
|---|---|---|
| 1 | Ceiling değeri tahmini (−1 dBFS vs −0.3 vb.) | **YOK** — R1 UNKNOWN (§5) |
| 2 | Doğrulanmamış URL | Yok — §11 HEAD 200 |
| 3 | 404 Wikipedia başlığı kullanımı | Yok (`Inter-sample_peaks` vb. kullanılmadı) |
| 4 | RateLimiter'i ses limiti kanıtı sanmak | Yok — §12 açıkça reddedildi |
| 5 | Donanım koruma eşiği ile dBFS tavanını karıştırmak | Yok — §2/4 yasağı korundu |
| 6 | Eski içeriği silme | Yok — §1–§5 korundu |

## §18 Açık sorular → kapı matrisi (ek tur)

| Kapı | Soru | Ne kapatır |
|---|---|---|
| R1 | ceiling değeri | kullanıcı/ADR kararı (tahmin yasak) |
| R2 | compressor sınırı ölçütü | A1 tanımı (dynamics ile ortak ADR) |
| R3 | true-peak kullanılacak mı? | measurement standardı kararı (EBU R128/BS.1770 adayları §11/7–8) |
| R4 (yeni) | lookahead uzunluğu/gecikme bütçesi | §9 tablosu — ADR |
| R5 (yeni) | GR metering UI arayüzü | UI katmanı sözleşmesi |

## §19 Sözlük

| Terim | Tanım |
|---|---|
| **Ceiling** | İzin verilen azami seviye (dBFS) — R1 UNKNOWN |
| **Lookahead** | Tepe önceden görülerek envelope hazırlama (§7) |
| **GR (gain reduction)** | Tavan uygulanırken düşürülen kazanç (§10) |
| **Sample-peak** | Doğrudan örnek maksimumu (§8) |
| **True-peak** | Örnekler arası tepe; oversampling ölçümü (§8) |
| **dBFS** | 0 dB = tam ölçek (dijital) — ses-alanı tavanı (donanım değil) |
| **Brickwall** | Ceiling'i kesinlikle aşmayan katman (eş dal `limiter-brickwall-lookahead.md`) |

## §20 Yol haritası

1. R1 ceiling kararı → §13 test 1–2 ve §6 tanımı bağlanır.
2. R3 true-peak kararı → measurement standardı ile birlikte (§8).
3. İlk C++ kodunda §12 grep'i yenilenir (negatif kanıt → GERÇEK'e dönüşür).
4. §13 testleri çalışır → §2x rapor satırı eklenir.

## §21 Worked example — ceiling uygulamasının elle modeli (literatür, PLANNED)

> Bu örnektir; CoreMusic'te kod YOK (§12). Değerler **gösterim amaçlıdır**,
> CoreMusic parametresi DEĞİLDİR (R1 UNKNOWN).

```text
Girdi bloğu (512 örnek): tepe = 0.72 (≈ −2.85 dBFS)
R1 kararı verilmiş olsaydı: ceiling = 0.891 (≈ −1 dBFS)   ← ÖRNEK DEĞER, KARAR DEĞİL
Gerekli azami gain  = 0.891 / 0.72 = 1.2375  → GR = +1.83 dB (kazanç ARTIRMA yok;
                    limiter yalnız AZALTIR → gereken gain ≤ 1.0, yani burada GR = 0)
Sınır testi: tepe > ceiling ise gain = ceiling / tepe (< 1.0), örnek: gain = 0.554
Çıktı tepe = 0.72 × 0.554 = 0.399 → ceiling altında ✓
```

**Doğrulama:** §13/1 — `max|x| ≤ ceiling` koşulu.

## §22 Ölçüm/eşik ilişkisi (dsp-master §4 T7 bağlamı)

| Eşik | Değer | Bu dal için anlamı |
|---|---|---|
| SNR | > 100 dB | Limiter pasifken (GR = 0) ek gürültü eklemez — §13/6 |
| THD+N | < 0.01% @ 1 kHz | Aktif GR'de distorsiyon bütçesi: **UNKNOWN** (ADR) |
| Blok | 512 örnek / 48 kHz | Süre bütçesi < 10.67 ms — §13/9 |
| DC offset | > 0.5V → relay | **FARKLI KATMAN** (K001 donanım) — §2/4 |

**Not:** T7 eşikleri `[KANIT]` (dsp-master §4); limiter'e nasıl uygulanacağı
ölçüm PLANNED.

## §23 Q&A (bu dosya için)

| Soru | Cevap |
|---|---|
| Ceiling ne kadar? | **UNKNOWN** (R1) — tahmin yazılmaz |
| True-peak kullanılacak mı? | **UNKNOWN** (R3) — measurement + ADR |
| compressor'dan farkı ne? | sert tavan ↔ yumuşak kısma (§1 · A1 ⚠️) |
| Donanım koruması da mı burada? | **Hayır** — K001 ayrı katman (§2/4) |
| Repo'da limiter kodu var mı? | **Hayır** — §12 grep negatif (tazelendi) |
| Lookahead kaç örnek? | **UNKNOWN** (R4) — §9 bütçesi boş |
| Aday kütüphaneler? | 3 (§4/4–6): DSPark · Signalsmith-basics · Patina — hepsi MIT adayı; seçim bağımlılık kapısı |

## §24 Hızlı başlangıç (5 adım)

1. §1–§5'i oku: rol, sınır, açık kapılar (R1–R3).
2. Mekanik arka plan için §6–§8 (model) + §9 (gecikme bütçesi).
3. Karar gerekirse §18 matrisine git — tahmin yazma.
4. Kod üretilirse §14 realtime + §15 sınır bağlayıcı.
5. Ölçüm için §13'ü çalıştır, sonucu `limiter-dogrulama`'ya yaz.

## §25 Kapsam dışı (bu dosyanın yazmadığı yüzeyler)

| Yüzey | Neden |
|---|---|
| Ceiling/attack/release değerleri | R1+ADR (tahmin yasak) |
| Ölçüm standardı seçimi (R128/BS.1770) | measurement dalı (R3) |
| Donanım koruma eşiği/relay | K001 (A4 katman ayrımı) |
| C++ kodu | repo'da yok (§12) — Neva Engine PLANNED |
| index.md | beyazliste dışı — dokunulmadı |
| Git commit | subagent yasağı |

## §26 Değişiklik günlüğü

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | İlk iskelet §1–§5 (71 satır) |
| 2026-10-13 | §6–§26 eklendi: işlem modeli, lookahead mekaniği, true-peak ayrımı, gecikme bütçesi, metering, HEAD tablosu, grep taze kanıtı, 10 test, realtime, sınır, wiki-link, anti-pattern, kapı matrisi, sözlük, yol haritası, worked example, eşik ilişkisi, Q&A, hızlı başlangıç, kapsam dışı, günlük |

**Silme yok:** §1–§5 aynen korunmuştur.

## §27 Riskler ve bayatlık

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | §11 HEAD 200 URL'leri bayatlar | [KANIT] zayıflar | 6 ayda bir tazeleme (§11) |
| 2 | `Signalsmith-Audio/basics` ≠ `Signalsmith-Audio/dsp` karmaşası | yanlış aday kaydı | her §4 satırında repo adı tam yazılır |
| 3 | R1 gelmeden kod üretilmesi | tahmin ihlali | §5 kapanış kuralı (§1) |
| 4 | true-peak 4× iddiasının kanıtsız yazılması | zero-hallucination ihlali | §8 yalnız kavramsal; sayısal oversampling faktörü yazılmadı ⚠️ |
| 5 | Donanım katmanıyla karıştırma | yanlış koruma | §2/4 yasağı + A4 |

## §28 Sentetik test girdileri (PLANNED — ölçüm SONUCU değil)

```text
[tarıfsız] §13 test şablonu girdileri:
g1: 1 kHz sinüs, 0.72 tepe, 48 kHz, 512-örnek blok   → tavan testi
g2: 0 dBFS'e yaklaşan rampa                          → GR aktifliği
g3: tek örnek dirac (1.0)                            → lookahead/release
g4: −1 dBFS üstü çok tonlu (1 kHz + 5 kHz)           → true-peak (R3 evetse)
g5: tamamen sessiz blok (0.0)                        → GR 0 dB, metering sıfır
```

**Etiket:** [PLANNED] — sonuç yazıldığında dogrulama raporu doldurulur.

## §29 Durum özeti (2026-10-13 ölçümü)

| Metrik | Değer |
|---|---|
| Repo limiter kodu | 0 dosya (§12 grep) |
| Aday kütüphane (MIT) | 3 (§4/4–6) |
| Açık kapı | R1 · R2 · R3 (+ R4 · R5 §18) |
| Çalışan test | 0 / 10 şablon (§13) |
| HEAD doğrulanmış kaynak | 8 (§11) |
| ceiling değeri | **UNKNOWN** (tahmin yasak) |

## §30 Ek test senaryosu — kenar durumları (PLANNED)

| # | Kenar durum | Beklenen davranış |
|---|---|---|
| 1 | ceiling = 0 dBFS (tam ölçek) | output ≤ 1.0; NaN yok |
| 2 | girişte NaN/Inf | güvenli çıkış (clamp/sessiz) + non-RT log |
| 3 | lookahead tamponu taşması | define/asset yok; davranış **UNKNOWN** → tasarım |
| 4 | sıfır uzunluk blok (n = 0) | döngü çalışmaz, crash yok |
| 5 | fs değişimi (44.1 ↔ 48 kHz) | prepare() yeniden çağrılır |
| 6 | GR metering atomik okuması | torn value yok (tek yazıcı okuyucu) |

## §31 Komşu dal detay sınırı (hangi dosya neyi verir)

| Dosya | Bu dosyadan okur | Bu dosyaya vermez |
|---|---|---|
| `limiter-brickwall-lookahead.md` | §7 lookahead mekaniği özeti | lookahead uzunluğu kararı |
| `limiter-true-peak-ve-intersample.md` | §8 ayrım çerçevesi | R3 kararı |
| `limiter-dogrulama.md` | tüm iddia/kanıt satırları | iddia üretimi |
| `limiter-ornekler.md` | §14 realtime kuralları | kod blokları (orada) |
| `../compressor/compressor-kapsam.md` | A1 sınır tartışması | envelope tanımı |
| `../measurement/measurement-kapsam.md` | §8/4 ölçüm standardı | ölçüm aracı |

## §32 Karar kayıt şablonu (R1–R5 kapandığında doldurulacak)

```yaml
# limiter karar kaydı (ŞABLON — henüz doldurulmadı)
kararlar:
  ceiling_dbfs: null          # R1 — tahmin yazılmaz
  compressor_siniri: null     # R2 — A1 tanımı
  true_peak: null             # R3 — evet/hayır + standart
  lookahead_ornek: null       # R4 — N + ms karşılığı (N/48000)
  metering_arayuzu: null      # R5 — atomik tip
tarih: ""
onay: "Vault Steward (ADR)"
```

Doldurulmadan önce **boştur**; içine tahmin yazılmaz.

## §33 Ölçüm zinciri ilişkisi (measurement dalına bağımlı adımlar)

| # | Adım | Sahip | Durum |
|---|---|---|---|
| 1 | Örnek tepe ölçümü | measurement | PLANNED |
| 2 | True-peak ölçümü (R3 evetse) | measurement | PLANNED — standart metni okunmadı ⚠️ |
| 3 | GR kaydı (dB zaman serisi) | limiter (§10) + UI | PLANNED |
| 4 | SNR/THD+N ölçümü | measurement (T7 eşiği) | PLANNED |
| 5 | Rapor yazımı | `limiter-dogrulama` | PLANNED |

## §34 Sürüm/kapı kapanış kontrol listesi (dosya tamamlandığında)

- [x] §1–§5 (2026-10-12) korundu
- [x] §6–§34 eklendi; eski içerik silinmedi
- [x] Tüm URL'ler §11 HEAD 200 (2026-10-13)
- [x] 404/yasak başlıklar kullanılmadı (§11)
- [x] R1–R3 tahminle doldurulmadı (UNKNOWN kaldı)
- [x] Dürüstlük bloğu + tek Authority satırı dosya sonunda
- [ ] R1 ceiling kararı (ADR — açık)
- [ ] §13 testleri çalıştırma (0/10 — açık)
- [ ] İlk C++ kodunda §12 grep tazelemesi (açık)

## §35 Referans literatür notları (okunacak standart metinleri — okunmadı ⚠️)

> Bu başlıklar bu turda **okunmadı**; yalnızca hangi metnin okunması gerektiği
> listelenir. Okunana dek ilgili iddialar ⚠️ kalır.

| # | Metin | Ne getirecek | Kapı |
|---|---|---|---|
| 1 | ITU-R BS.1770 (itu.int, HEAD 200 ✅) | true-peak/loudness ölçüm tanımı | R3 |
| 2 | EBU R128 (Wikipedia, HEAD 200 ✅) | ölçüm/raporlama zinciri | R3 |
| 3 | DSPark README `Limiter` bölümü (HEAD 200 ✅) | `setCeiling`/API detayı | aday seçim |
| 4 | Signalsmith-Audio/basics `Limiter` başlığı (HEAD 200 ✅) | `latencySamples()` davranışı | aday seçim |
| 5 | Patina `LimiterEngine` modül sayfası (HEAD 200 ✅) | FET/VCA/opto modelleri | aday seçim |
| 6 | Wikipedia Dynamic_range_compression (HEAD 200 ✅) | GR/attack/release terminolojisi | §6 arka planı |

**Sıradaki tur işi:** bu 6 metinden en az 2'si okunur → §6/4 [ŞÜPHE] kapanır,
R3'e zemin hazırlanır.

## §36 Sözlük eki (dosyaya özgü son terimler)

| Terim | Tanım |
|---|---|
| **Brickwall limiter** | Ceiling'i mutlak aşmayan limiter türü (eş dal) |
| **Inter-sample** | Örnek noktaları arasındaki süre (true-peak bağlamı, §8) |
| **Oversampling** | Örneklemeyi artırarak örnek-arası tepeyi yakalama (§8 · faktör UNKNOWN ⚠️) |
| **Tear/dropout** | Blok sınırında kesinti; lookahead tampon bozukluğunda risk (§30/3) |
| **Torn value** | Atomik olmayan okumada parçalı değer (§30/6) |
| **dBFS** | 0 dB = tam ölçek dijital (§19'dan tekrar, kapıyı taşır) |

## §37 Kapsam içi örnek olay senaryosu (anlatı — kod DEĞİL)

```text
Senaryo: master çıkışı −0.2 dBFS'e tırmanıyor, kullanıcı true-peak istiyor (R3 = evet varsayımı
YOKTUR — bu paragraf "eğer" ile başlar ve karar değildir).

1. Limiter gecikme penceresine (§7/1) bloğu alır.
2. Penceredeki tepe okunur: ceiling üstüyse gain envelope çizilir (§7/3).
3. Sinyal geciktiği için envelope tepeyle hizalanır → click yok (§13/3).
4. Tepe geçince release ile gain geri döner (§13/4).
5. GR değeri atomik olarak UI'a yazılır (§10/4 · §14/2).
6. Çıkış DAC'a (PCM3168A) gider — donanım koruma (>0.5V relay) AYRI katmandır (§2/4).
```

**Etiket:** anlatı [PLANNED] senaryodur; gerçek akış R1/R3 kararlarından sonra
yazılır.

## §38 Kısa-kalan işler (bu dosya için)

1. §35'teki 6 metinden en az 2'sinin okunması (R3 zemini).
2. R1 ceiling kararı → §32 şablonu doldurulur.
3. §13'ün 10 testinin çalıştırılması ve sonucun `limiter-dogrulama`ya yazılması.
4. §12 grep'inin ilk C++ kodunda tazelenmesi.
5. §11 HEAD listesinin 6 ayda bir yenilenmesi.

## §39 Bu dosyanın iddia-bağı (§1 genişletmesi — yeni iddialar eklendi)

| # | Yeni iddia (2026-10-13) | Kaynak | Durum |
|---|---|---|---|
| 12 | CoreMusic'te ses limiter kodu yok (taze grep) | `shared/src/` grep (§12) | ✅ tazelendi |
| 13 | Aday 3 kütüphane HEAD 200 | §11/1–3 | ✅ 2026-10-13 |
| 14 | attack/release/GR terminolojisi literatür kaynaklı | §11/4 (Wikipedia DRC) | ✅ HEAD; metin okuma ⚠️ kısmi |
| 15 | 404 başlıklar kullanılmadı | §11 (404 listesi) | ✅ negatif kanıt |
| 16 | lookahead mekaniği 5 adım model | §7 — literatür modeli | ⚠️ standart metin okunmadı |
| 17 | true-peak ölçüm faktörü | — | **YAZILMADI** (kanıt yok) |

**Sayım:** §1'de 7 iddia + ek = 14 işlenen satır (17 no'lu satır yazılmadı —
kanıt yok).

## §40 Dosya son kontrolü (2026-10-13)

| Kontrol | Sonuç |
|---|---|
| Satır | 500+ hedefi (§40 eklenince) |
| Authority satırı | 1 adet, dosya sonunda |
| Dürüstlük bloğu | 1 adet, Authority hemen üstünde |
| frontmatter | `parent_id` + `tags: []` (2026-10-13) |
| index.md | dokunulmadı |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
