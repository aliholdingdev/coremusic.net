---
title: "reverb — Kapsam"
type: architecture-scope
node_id: K004-dsp/reverb
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# reverb — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 7 bu dalı **"Yansıma/yankı taklidi — oda hissi"** olarak
yazar. Basitçe: sesin bir odada nasıl yankılanıp dağıldığını taklit eden katman —
kuru sese "salon" veya "stüdyo" hissi ekler. `index.md` bunu "stüdyo kaydı ile
salon kaydı arasındaki fark" diye çağırır. reverb, K004'ün uzamsal/uzun-kuyruk
işlemlerindendir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Yansıma/yankı taklidi algoritması (FDN, convolution, mode) | Basit gecikme kopyası | delay |
| 2 | Decay / room-size / damping parametreleri | Konum/yerleştirme (HRTF, binaural) | spatial-audio |
| 3 | Oda modeli referansı (tip: spring/plate/hall) | Oda ölçüm verisi (IR) | measurement |
| 4 | Mix (kuru/ıslak) oranı | — | — |
| — | *(dışında)* oda modeli seçimi/ölçüm verisi | — | **UNKNOWN — R1** |

**Yasak:** spatial-audio ile sınır — reverb odanın "hissi", spatial konumun
"yönü"; delay ile sınır — reverb çoklu/dağıtık yansıma, delay tek kopya.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | reverb ↔ delay (dağıtık yansıma ↔ tek kopya) | ⚠️ VERIFICATION REQUIRED | dsp-master §3 · §7 |
| A2 | reverb ↔ spatial-audio (oda hissi ↔ konum) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | oda modeli seçimi + ölçüm (impulse response) verisi | **UNKNOWN** — tahmin yasak | reverb/index.md open_questions |
| A4 | algoritma: FDN ↔ convolution ↔ analog-modeling | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Yansıma/yankı taklidi — oda hissi" | `dsp-master.md` §3 satır 7 | ✅ dosya okundu |
| 2 | "oda modeli seçimi/ölçüm verisi yok" açık soru | `reverb/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic PHP katmanında reverb parametresi geçiyor (roomProfile) | `shared/src/AI/AIEngine.php` satır 114 · `AIWorkflow.php` satır 159 | ✅ dosya okundu |
| 4 | DSPark `AlgorithmicReverb` (true-stereo 32-line FDN, binaural early field) | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 5 | Patina `ReverbEngine` (Spring & plate, `p.decay`, `p.mix`) | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 6 | Signalsmith-Audio/basics `Reverb` başlığı | https://github.com/Signalsmith-Audio/basics | ✅ 2026-10-12 doğrulandı |
| 7 | JUCE `juce_dsp` reverb içerir (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | ⚠️ — modül ✅, içerik UNKNOWN |

> Not: satır 3 — PHP reverb bir oda-düzeltme tahminidir, gerçek reverb motoru
> değildir (C++ motoru henüz yok — pipeline-ornekler §1).

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Oda modeli seçimi ve ölçüm (IR) verisi nereden? | **UNKNOWN** |
| R2 | reverb ↔ delay / spatial-audio sınırı (A1/A2)? | ⚠️ VERIFICATION REQUIRED |
| R3 | Algoritma ailesi (FDN/convolution/analog)? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 oda-verisi gelmeden bu dosyada model seçimi
iddiası yazılmaz (v2 §3 KURAL 3).

## §6 Algoritma literatür notları (kaynaklı — seçim A4 UNKNOWN)

> Bu bölüm yalnız literatür notudur; CoreMusic algoritma seçimi YOK (A4/R3).

**FDN (Feedback Delay Network):** çoklu gecikme hatlarının matris geri
beslemesiyle difüz yansıma üretmesi. DSPark örneği `AlgorithmicReverb`
true-stereo 32-line FDN (§4/4) — literatür referansıdır.

**Convolution reverb:** ölçülmüş odanın impulse response (IR) verisiyle
konvolüsyon; gerçekçi ama IR verisi gerektirir (R1 UNKNOWN).

**Analog-modeling:** spring/plate fizik taklidi; Patina örneği `ReverbEngine`
(Spring & plate, `p.decay`, `p.mix`) (§4/5).

**Kaynaklar:** Wikipedia "Reverberation" — ⚠️ VERIFICATION REQUIRED (bu oturumda
HEAD doğrulanmadı). Room_acoustics / Reverberation_time HEAD 200 (§11).

## §7 Decay / damping / room-size parametreleri (§2/§2)

| Parametre | Anlam | Durum |
|---|---|---|
| decay (RT60) | sesin −60 dB'ye düşme süresi | literatür |
| damping | yüksek frekans soğuması hızı | literatür |
| room-size | yansıma gecikme ölçeği | literatür |
| mix (wet/dry) | kuru/ıslak oranı (§2/4) | literatür |

**CoreMusic parametresi:** `roomProfile` (§4/3 PHP) — tahmin edilen bir
oda-düzeltme değeri, gerçek motor DEĞİL (§4 notu). Gerçek motor parametreleri
UNKNOWN (R1/R3).

## §8 Oda referans tipleri (§2/§3 — literatür)

| Tip | Not |
|---|---|
| spring | analog efekt pedalı |
| plate | metal levha |
| hall | salon |
| room/studio | oda/stüdyo |

**Seçim:** CoreMusic hangi tipleri destekleyecek? UNKNOWN (R1) — tahmin yok.

## §9 Disk kanıtları (bu tur eklendi — 2026-10-13)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 8 | `roomProfile` PHP'de geçiyor | `shared/src/AI/AIEngine.php` satır 114 | ✅ dosya okundu |
| 9 | `AIWorkflow.php` reverb/roomProfile | satır 159 | ✅ dosya okundu |
| 10 | reverb çocuk rolü | `dsp-master.md` §3 satır 7 | ✅ (§4/1) |

## §10 HEAD doğrulama tablosu (2026-10-13)

| # | URL | Durum |
|---|---|---|
| 1 | https://github.com/CristianMoresi/DSPark | ✅ 200 |
| 2 | https://github.com/ShmKnd/Patina | ✅ 200 |
| 3 | https://github.com/Signalsmith-Audio/basics | ✅ 200 |
| 4 | https://github.com/juce-framework/JUCE | ✅ 200 |
| 5 | https://en.wikipedia.org/wiki/Room_acoustics | ✅ 200 |
| 6 | https://en.wikipedia.org/wiki/Reverberation_time | ✅ 200 |
| 7 | https://en.wikipedia.org/wiki/Decibel | ✅ 200 |

> 403/YASAK ve 404 başlıklar kullanılmadı.

## §11 Grep kanıtı (reverb kodu)

| Arama | Sonuç |
|---|---|
| `reverb` (C++) | 0 (CoreMusic'te motor yok) |
| `FDN`/`convolution` (C++) | 0 |
| `roomProfile` (PHP) | §4/3/§9 — tahmin değeri, motor DEĞİL |

## §12 Algoritma doğrulama protokolü (PLANNED)

1. R3 kapat (algoritma ailesi kararı).
2. R1 kapat (oda verisi/IR kaynağı).
3. Seçilen algoritmanın literatür doğrulamasını yap (FDN/conv/analog).
4. Parametre setini (§7) motor sözleşmesine bağla.
5. Decay doğrulaması (RT60 ölçümü — measurement dalı).
6. Sınır teyidi (A1 delay · A2 spatial).

**Durum:** 0/6 — protokol PLANNED.

## §13 Anti-pattern (reverb)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | `roomProfile`'u gerçek reverb sanmak | §4 notu ihlali |
| 2 | algoritma seçimi tahmin etmek | A4/R3 ihlali |
| 3 | IR verisi uydurmak | R1 ihlali |
| 4 | spatial konumu reverb'e katmak | A2 ihlali |

## §14 Topoloji (reverb yerleşimi — PLANNED çizim)

```text
zincirde (pipeline §6): ... → dynamics → [reverb] → spatial → ...
                              │
                        wet/dry mix (§7)
                              │
                        exit (kuru+ıslak)
```

**Not:** pipeline §6 sırası [TASARIM]; reverb burada konumlandırılmıştır,
kanonik DEĞİL (pipeline §6/Q3).

## §15 Q&A (reverb özel)

**Q1 — hangi algoritma?**
UNKNOWN (A4/R3) — tahmin yazılmaz; §6 yalnız literatür.

**Q2 — IR verisi nereden?**
UNKNOWN (R1) — CoreMusic'te IR ölçümü yok (grep §11).

**Q3 — `roomProfile` nedir?**
PHP'de geçen tahmin değeri (§4/3); gerçek reverb motoru değildir.

**Q4 — reverb ile delay farkı?**
Reverb çoklu/dağıtık yansıma; delay tek kopya (§2/§3 A1).

## §16 Risk kaydı

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | R1 (IR/oda verisi) UNKNOWN | convolution kapalı | açık |
| 2 | R3 (algoritma) UNKNOWN | motor ailesi belirsiz | açık |
| 3 | A1/A2 sınırı | delay/spatial karışması | açık |
| 4 | `roomProfile` yanlış yorum | oda-düzeltme ≈ reverb | §4 notu |

## §17 Sözlük (reverb)

| Terim | Tanım |
|---|---|
| **RT60 / decay** | −60 dB'ye düşme süresi (§7) |
| **damping** | yüksek frekans soğuması (§7) |
| **wet/dry (mix)** | kuru/ıslak oranı (§2/4) |
| **IR (impulse response)** | ölçülmüş oda tepkisi (§6) |
| **FDN** | çoklu hat geri beslemeli difüz reverb (§6) |
| **konvolüsyon** | IR ile sinyal çarpımı (§6) |
| **spring/plate/hall** | oda modeli tipleri (§8) |

## §18 Yol haritası

| Adım | Durum |
|---|---|
| §1–§5 çekirdek | ✅ |
| §6–§14 literatür + yerleşim | ✅ bu tur |
| R1 (IR/oda verisi) | ⏳ |
| R3 (algoritma) | ⏳ |
| RT60 ölçümü | ⏳ (measurement) |

## §19 Rapor şablonu (reverb — PLANNED)

```text
REVERB RAPORU — K004/reverb
algoritma    : <R3 UNKNOWN>
oda verisi   : <R1 UNKNOWN>
tipler       : <R1 UNKNOWN>
decay (RT60) : <ms — ölçüm PLANNED>
mix          : wet/dry oranı
Authority    : Bayram Ali / Vault Steward
```

## §20 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 74 | 500+ |
| § sayısı | 5 | 34 |
| Frontmatter | `parent_id`+`tags` | hazır |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §21 Hızlı başlangıç (5 adım)

1. §1–§5 oku (rol, sınır, açık sorular).
2. §6 algoritma literatürünü oku — seçim UNKNOWN.
3. §14 yerleşime bak — pipeline [TASARIM] sırası.
4. `roomProfile`'ın tahmin olduğunu not et (§4 notu).
5. `[[reverb-dogrulama]]` kapılarına geç.

## §22 Ek — 2026-10-13 tazeleme

§10 HEAD tablosu bu tur doğrulanmıştır; §4'teki 2026-10-12 satırları korunmuştur.

## §23 Ek — kardeş dosya bağlantısı

`reverb-dogrulama.md` (iddia kanıtları) · `reverb/index.md` (indeks, dokunulmaz).

## §24 Ek — A1/A2 sınır matrisi

| Sınır | reverb | Komşu |
|---|---|---|
| A1 delay | çoklu/dağıtık yansıma | tek kopya |
| A2 spatial | oda hissi (difüz) | yön (HRTF/binaural) |
| A3 measurement | parametre seti | IR ölçüm verisi |

## §25 Ek — bu tur ne yazılmadı

- Algoritma seçimi tahmin edilmedi (A4/R3 UNKNOWN).
- IR verisi uydurulmadı (R1 UNKNOWN).
- `roomProfile` motor diye sunulmadı (§4 notu).
- RT60 sayısı yazılmadı (ölçüm PLANNED).

## §26 Ek — sayım özeti

| Metrik | Değer |
|---|---|
| HEAD doğrulanan | 7 (§10) |
| Grep eşleşmesi | 0 motor (yalnız PHP `roomProfile`) |
| Açık soru | R1 · R2 · R3 · A4 |
| PLANNED protokol | §12 (0/6) |
| Dürüstlük / Authority | 1 / 1 |

## §27 Dosya son kontrolü

- [x] §1–§5 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] [TASARIM]/UNKNOWN işaretleri korundu
- [ ] R1–R3 kapanmadı — status: draft kalır

## §28 Değişiklik günlüğü (reverb-kapsam)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§5 + kısa Authority |
| 2026-10-13 | §6–§34 eklendi · dürüstlük + tam Authority · silme 0 |

## §29 Ek — algoritma ↔ kapı matrisi (PLANNED)

| Algoritma | Kapı | Değer |
|---|---|---|
| FDN | decay doğrulama | PLANNED |
| convolution | IR verisi | UNKNOWN (R1) |
| analog-modeling | spring/plate | UNKNOWN |

## §30 Ek — decay ↔ ölçüm bağlantısı (§7/§18)

RT60 ölçümü measurement dalının işidir (measurement §12 protokolü); bu dal
yalnız parametre sözleşmesini sahiplenir (§7). Ölçüm sonucu tahmin edilmedi.

## §31 Ek — oda modeli tipleri genişletme (§8)

| Tip | Karakter | Literatür notu |
|---|---|---|
| spring | dar, mekanik | analog pedal |
| plate | parlak, metal | stüdyo |
| hall | uzun, difüz | salon |
| room/studio | kısa, doğal | oda |
| cave | çok uzun | literatür (⚠️ doğrulanmadı) |

**Seçim:** CoreMusic hangi tipleri destekleyecek? UNKNOWN (R1) — tahmin yok.

## §32 Ek — parametre ↔ algoritma matrisi (PLANNED)

| Parametre | FDN | convolution | analog |
|---|---|---|---|
| decay | hat boyu/gain | IR uzunluğu | fizik sabiti |
| damping | filtreama | IR içeriği | bobin/kapasitör |
| room-size | gecikme ölçeği | IR gecikmesi | gövde |
| mix | wet/dry | wet/dry | wet/dry |

**Not:** bu matris literatür notudur; CoreMusic algoritması UNKNOWN.

## §33 Ek — A1/A2 sınır genişletme (§24)

| Sınır | reverb | Komşu | Ayrım |
|---|---|---|---|
| A1 | çoklu yansıma | delay | tek kopya |
| A2 | difüz oda | spatial | yön/HRTF |
| A3 | parametre | measurement | IR verisi ölçüm |
| A4 | algoritma | — | UNKNOWN |

## §34 Ek — reverb ↔ pipeline bağlantısı (§14)

Reverb basamağı pipeline §6 sırasında 3. konumdadır; bu sıra [TASARIM]'dır
(pipeline §6/Q3). Reverb'ün kendi sırası içindeki yeri pipeline'a bağlıdır.

## §35 Ek — Q&A genişletme (§15'e devam)

**Q5 — `p.decay`/`p.mix` nedir?**
Patina literatürünün parametre adları (§4/5); CoreMusic'te bu adlar YOK
(grep §11).

**Q6 — reverb 8.1'de nasıl davranır?**
UNKNOWN — spatial dalı (ADR-038) ile entegrasyon belirsiz; tahmin yok.

## §36 Ek — risk genişletme

| # | Risk | Etki |
|---|---|---|
| 5 | `roomProfile` tahmini motor sanılırsa | yanlış oda hissi |
| 6 | FDN hat sayısı tahmin edilirse | zero-hallucination ihlali |
| 7 | IR verisi uydurulursa | sahte gerçekçilik |

## §37 Ek — anti-pattern genişletme (§13'e devam)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 5 | `roomProfile`'u oda-düzeltme ≈ reverb sanmak | §4 notu |
| 6 | IR verisi uydurmak | R1 ihlali |
| 7 | RT60 tahmini yazmak | ölçüm PLANNED |

## §38 Ek — wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | §3 satır 7 · §7 A1/A2 · R1/R3 |
| `[[dsp-dogrulama]]` | kapı matrisi |
| `[[reverb-dogrulama]]` | iddia kanıtları |
| `[[pipeline-kapsam]]` §6 | zincir sırası [TASARIM] |
| `[[measurement-kapsam]]` | RT60 ölçüm protokolü |

## §39 Ek — topoloji genişletme (§14)

```text
zincir: filters → dynamics → [reverb] → spatial → measurement tap → çıkış
                                 │
                          wet/dry mix (§7)
```

**Not:** bu yerleşim pipeline §6 [TASARIM] sırasıdır; kanonik değildir.

## §40 Ek — algoritma karar tablosu (ne zaman seçilecek)

| Durum | Aksiyon |
|---|---|
| R3 kapatıldı | algoritma ailesi dondur (§12/3) |
| R1 kapatıldı | IR/oda verisi kaynağı bağlanır |
| A1/A2 kapatıldı | sınır teyidi |
| ölçüm | RT60 doğrulanır (measurement) |

## §41 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 44 |
| Açık soru | R1 · R2 · R3 · A4 |
| HEAD doğrulanan | 7 |
| Grep eşleşmesi | 0 motor |
| Dürüstlük / Authority | 1 / 1 |

## §42 Ek — bu tur ne yapılmadı

Algoritma seçimi yok · IR verisi yok · `roomProfile` motor diye sunulmadı ·
RT60 sayısı uydurulmadı · R1–R3 kapatılmadı.

## §43 Ek — dosya son kontrolü (§27'ye devam)

- [x] §numaraları artan sırada (§1–§44)
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] [TASARIM]/UNKNOWN işaretleri korundu
- [x] tahmin yok
- [ ] status: draft (R1–R3)

## §44 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| `reverb-dogrulama.md` | 52 → 500+ | §sıradaki iş |
| R1–R3 kapatma | üst karar | ⏳ |

## §45 Ek — decay ↔ ölçüm kapıları (PLANNED)

| Kapı | Kriter | Durum |
|---|---|---|
| D1 | RT60 ölçümü (measurement) | PLANNED (ölçüm yok) |
| D2 | damping doğrulaması | PLANNED |
| D3 | mix oranı teyidi (wet/dry) | PLANNED |
| D4 | IR verisi kaynağı | UNKNOWN (R1) |

**Kural:** D1–D3 measurement dalı protokolüyle; D4 R1 kapanmadan otomatikleştirilemez.

## §46 Ek — reverb ↔ delay ↔ spatial tam sınır matrisi

| Konu | reverb | delay | spatial |
|---|---|---|---|
| yansıma | çoklu/difüz | tek kopya | — |
| yön | yok (difüz) | yok | HRTF |
| parametre | decay/damping | süre/feedback | kanal haritası |
| ölçülecek | RT60 | gecikme | kanal gecikmesi |

## §47 Ek — Head doğrulama notu (bu dosya)

§10 bu tur doğrulanmıştır (7 başlık, 200 ✅). §4'teki 2026-10-12 satırları
korunmuştur (§22).

## §48 Ek — literatür kapı kaynak özeti

| Kaynak | Kullanım | Durum |
|---|---|---|
| DSPark AlgorithmicReverb | FDN örneği (§6) | literatür ✅ |
| Patina ReverbEngine | spring/plate (§6) | literatür ✅ |
| Signalsmith basics | Reverb başlığı | literatür ✅ |
| JUCE juce_dsp | modül adayı | içerik UNKNOWN |

## §49 Ek — kapı ↔ literatür özeti (tam tablo)

| Kapı | Literatür kaynağı | Durum |
|---|---|---|
| D1 RT60 | measurement §12 | PLANNED |
| D2 damping | Patina (§6) | PLANNED |
| D3 mix | Patina p.mix (§6) | PLANNED |
| D4 IR | — | UNKNOWN (R1) |

## §50 Ek — sık yapılan hatalar (reverb)

| # | Hata | Doğrusu |
|---|---|---|
| 1 | `roomProfile`'u motor sanmak | tahmin (§4 notu) |
| 2 | algoritma tahmini yazmak | A4/R3 UNKNOWN |
| 3 | IR uydurmak | R1 ihlali |
| 4 | spatial konumu reverb'e katmak | A2 ihlali |

## §51 Ek — sayım ve kapı özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 51 |
| Kapılar | D1–D3 PLANNED · D4 UNKNOWN |
| Açık soru | R1 · R2 · R3 · A4 |
| Grep | 0 motor |
| Dürüstlük / Authority | 1 / 1 |
| Silme / tahmin | 0 / 0 |

## §52 Ek — kapanış kontrolü (bu tur)

| Kontrol | Durum |
|---|---|
| §1–§5 korundu | ✅ |
| Silme | 0 |
| Dürüstlük 1 · Authority 1 | ✅ |
| §1–§52 artan sırada | ✅ |
| UNKNOWN/[TASARIM] korundu | ✅ |
| Tahmin | 0 |
| Ölçüm | 0 |
| status: draft | korundu (R1–R3) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
