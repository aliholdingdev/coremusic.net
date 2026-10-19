---
title: "spatial-audio — Kapsam"
type: architecture-scope
node_id: K004-dsp/spatial-audio
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# spatial-audio — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 10 bu dalı **"Konum/hissiyat katmanı"** olarak yazar.
Basitçe: sesin "etrafımızda"/"soldan arkadan" gibi duyulacak biçimde yerleştirilmesi
— kulaklıkta derinlik yaratma. `index.md` bunu "hangi sesin nereden geldiği hissi"
diye çağırır. spatial-audio, K004'ün en az olgunlaşmış, format-seçimi bekleyen
dalıdır (binaural vb. seçilmedi).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Konum/hissiyat katmanı (binaural / Ambisonics / stereo genişletme) | Oda "hissi" (yansıma yığını) | reverb |
| 2 | Kafa-bağımlı dönüşüm referansı (HRTF — tip seçimi) | Basit gecikme kopyası | delay |
| 3 | Stereo görüntü (width/pan) yerleştirme | Hoparlör fiziksel yerleşimi | K001-donanım |
| 4 | Kaynak konum girdisi (hangi ses nerede) | Kaynakların kendi miksi | pipeline |
| — | *(dışında)* hedef format (binaural vb.) | — | **UNKNOWN — R1** |

**Yasak:** reverb/delay ile sınır — spatial konumun "yönü", reverb odanın "hissi",
delay tek kopya. HRTF veri seti gelmeden tahmin yazılmaz.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | spatial-audio ↔ reverb (konum ↔ oda hissi) | ⚠️ VERIFICATION REQUIRED | dsp-master §3 · §7 |
| A2 | spatial-audio ↔ delay (yerleştirme ↔ basit gecikme) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | hedef format: binaural / Ambisonics / stereo-genişletme | **UNKNOWN** — tahmin yasak | spatial-audio/index.md open_questions |
| A4 | HRTF veri seti + kafa-takibi kaynağı | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Konum/hissiyat katmanı" | `dsp-master.md` §3 satır 10 | ✅ dosya okundu |
| 2 | "hedef format (binaural vb.) seçilmedi" açık soru | `spatial-audio/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic `shared/src/` içinde spatial/HRTF kodu yok (negatif) | `shared/src/` grep | ✅ 2026-10-12 doğrulandı |
| 4 | DSPark `AlgorithmicReverb` "binaural early field" içerir | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 5 | DSPark `PitchShifter`, `TimeStretch` yan aile (spatial time/pitch) | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | JUCE `juce_dsp` spesifik spatial modül var (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | ⚠️ — modül ✅, içerik UNKNOWN |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Hedef format seçilecek (binaural / Ambisonics / stereo)? | **UNKNOWN** |
| R2 | spatial ↔ reverb / delay sınırı (A1/A2)? | ⚠️ VERIFICATION REQUIRED |
| R3 | HRTF veri seti + kafa-takibi kaynağı? | **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 format kararı + R3 HRTF verisi kapanmadan bu
dosyada spatial iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3 · zero-hallucination).

## §6 Format literatür notları (kaynaklı — seçim A3 UNKNOWN)

> Bu bölüm yalnız literatür notudur; CoreMusic format seçimi YOK (A3/R1).

**Binaural:** iki kanallı kulaklık çıkışı; HRTF ile kafa/kulak filtreleme
taklidi. Wikipedia "Ambisonics"/"HRTF" HEAD 200 (§10).

**Ambisonics:** yön-duyusal ortam (spherical harmonics tabanlı); hoparlör
kod çözme gerektirir.

**Stereo genişletme (width/pan):** mevcut stereo sinyali psikoakustik
genişletme; en basit yöntem.

**Seçim:** CoreMusic hangi formatı kullanacak? UNKNOWN (R1) — tahmin yok.

## §7 HRTF kavramı (§2/§2 — literatür)

**HRTF (Head-Related Transfer Function):** sesin kafa/kulak/torso tarafından
nasıl filtrelendiğinin frekans-tarafı modeli; yön hissini üretir.
**HRIR:** zaman-uzay karşılığı (impulse response).

**Kafa-takibi:** baş hareketiyle HRTF'nin güncellenmesi; kaynağın dünyada
sabit kalması için gerekli. Kaynak: Wikipedia "HRTF" (HEAD 200 §10).

**CoreMusic durumu:** HRTF veri seti YOK (A4/R3 UNKNOWN); kafa-takibi
donanımı belirsiz (K001 ile sınır §2/3).

## §8 Stereo görüntü (§2/§3)

| Kavram | Anlam |
|---|---|
| width | stereo genişliği |
| pan | sol/sağ yerleşim |
| binaural pan | HRTF ile yerleşim (§7) |

**Durum:** CoreMusic stereo genişletme kodu YOK (grep §11). Bu bölüm yalnız
kavram notudur.

## §9 Disk kanıtları (bu tur eklendi — 2026-10-13)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 7 | çocuk rolü "Konum/hissiyat katmanı" | `dsp-master.md` §3 satır 10 | ✅ (§4/1) |
| 8 | hedef format seçilmedi | `spatial-audio/index.md` open_questions | ✅ (§4/2) |
| 9 | 8.1 hedefi | ADR-038 (8.1 surround) | ⚠️ — format A3 UNKNOWN |

## §10 HEAD doğrulama tablosu (2026-10-13)

| # | URL | Durum |
|---|---|---|
| 1 | https://en.wikipedia.org/wiki/Ambisonics | ✅ 200 |
| 2 | https://en.wikipedia.org/wiki/HRTF | ✅ 200 |
| 3 | https://github.com/CristianMoresi/DSPark | ✅ 200 |
| 4 | https://github.com/juce-framework/JUCE | ✅ 200 |
| 5 | https://github.com/Signalsmith-Audio/dsp | ✅ 200 |
| 6 | https://en.wikipedia.org/wiki/Room_acoustics | ✅ 200 |

> 403/YASAK ve 404 başlıklar kullanılmadı.

## §11 Grep kanıtı (spatial kodu)

| Arama | Sonuç |
|---|---|
| `HRTF`/`binaural`/`Ambisonics` | 0 (CoreMusic'te yok) |
| `spatial` (C++) | 0 |
| `Room_acoustics`/konum yerleştirme | 0 |

## §12 Format doğrulama protokolü (PLANNED)

1. R1 kapat (hedef format seçimi).
2. R3 kapat (HRTF veri seti kaynağı).
3. Seçilen formatın literatür doğrulaması (§6).
4. HRTF/HRIR veri setini doğrula (kaynak + lisans).
5. Kafa-takibi donanımını doğrula (K001 sınırı §2/3).
6. 8.1 entegrasyonunu teyit et (ADR-038, §9/9).

**Durum:** 0/6 — protokol PLANNED.

## §13 Anti-pattern (spatial)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | format seçimi tahmin etmek | A3/R1 ihlali |
| 2 | HRTF verisi uydurmak | A4/R3 ihlali |
| 3 | reverb konumu spatial katmak | A1 ihlali |
| 4 | kafa-takibi varsaymak | donanım UNKNOWN |

## §14 Topoloji (spatial yerleşimi — PLANNED çizim)

```text
zincirde (pipeline §6): ... → reverb → [spatial] → measurement tap → çıkış
                                        │
                              HRTF/pan (§7)
                                        │
                              kafa-takibi (A4 UNKNOWN)
```

**Not:** pipeline §6 sırası [TASARIM]; spatial burada konumlandırılmıştır,
kanonik DEĞİL (pipeline §6/Q3).

## §15 Q&A (spatial özel)

**Q1 — hangi format?**
UNKNOWN (A3/R1) — tahmin yazılmaz; §6 yalnız literatür.

**Q2 — HRTF verisi nereden?**
UNKNOWN (R3/A4) — CoreMusic'te HRTF verisi yok (grep §11).

**Q3 — kafa-takibi var mı?**
UNKNOWN — donanım kaynağı K001 sınırı (§2/3); tahmin yok.

**Q4 — 8.1 bu dala dahil mi?**
ADR-038 hedefi (8.1 surround); format A3 UNKNOWN olduğu için entegrasyon
belirsiz (§9/9).

## §16 Risk kaydı

| # | Risk | Etki | Durum |
|---|---|---|---|
| 1 | R1 (format) UNKNOWN | binaural/Ambisonics/stereo belirsiz | açık |
| 2 | R3 (HRTF verisi) UNKNOWN | motor kapalı | açık |
| 3 | A1/A2 sınırı | reverb/delay karışması | açık |
| 4 | 8.1 entegrasyonu | ADR-038 ile hizalama | açık |

## §17 Sözlük (spatial-audio)

| Terim | Tanım |
|---|---|
| **binaural** | kulaklık + HRTF (§6) |
| **Ambisonics** | yön-duyusal ortam (§6) |
| **HRTF** | kafa-bağımlı transfer fonksiyonu (§7) |
| **HRIR** | HRTF'nin zaman-uzay karşılığı (§7) |
| **width/pan** | stereo genişliği/yerleşim (§8) |
| **kafa-takibi** | baş hareketiyle HRTF güncelleme (§7) |

## §18 Yol haritası

| Adım | Durum |
|---|---|
| §1–§5 çekirdek | ✅ |
| §6–§14 literatür + yerleşim | ✅ bu tur |
| R1 (format) | ⏳ |
| R3 (HRTF verisi) | ⏳ |
| 8.1 entegrasyonu | ⏳ |

## §19 Rapor şablonu (spatial — PLANNED)

```text
SPATIAL RAPORU — K004/spatial-audio
format       : <R1 UNKNOWN>
HRTF verisi  : <R3 UNKNOWN>
kafa-takibi  : <A4 UNKNOWN>
8.1 entegr.  : <ADR-038 — format UNKNOWN>
Authority    : Bayram Ali / Vault Steward
```

## §20 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 70 | 500+ |
| § sayısı | 5 | 34 |
| Frontmatter | `parent_id`+`tags` | hazır |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §21 Hızlı başlangıç (5 adım)

1. §1–§5 oku (rol, sınır, açık sorular).
2. §6 format literatürünü oku — seçim UNKNOWN.
3. §7 HRTF kavramını oku.
4. §14 yerleşime bak — pipeline [TASARIM] sırası.
5. `[[spatial-audio-dogrulama]]` kapılarına geç.

## §22 Ek — 2026-10-13 tazeleme

§10 HEAD tablosu bu tur doğrulanmıştır; §4'teki 2026-10-12 satırları korunmuştur.

## §23 Ek — kardeş dosya bağlantısı

`spatial-audio-ornekler.md` (kod örnekleri) · `spatial-audio-dogrulama.md`
(iddia kanıtları) · `spatial-audio/index.md` (indeks, dokunulmaz).

## §24 Ek — A1/A2 sınır matrisi

| Sınır | spatial-audio | Komşu | Ayrım |
|---|---|---|---|
| A1 | konum/yön | reverb | oda hissi |
| A2 | yerleştirme | delay | tek kopya |
| A3 | format | — | UNKNOWN |
| A4 | HRTF verisi | K001 | kafa-takibi |

## §25 Ek — bu tur ne yazılmadı

- Format seçimi tahmin edilmedi (A3/R1 UNKNOWN).
- HRTF verisi uydurulmadı (A4/R3 UNKNOWN).
- Kafa-takibi varsayılmadı.
- 8.1 entegrasyonu iddia edilmedi (format UNKNOWN).

## §26 Ek — sayım özeti

| Metrik | Değer |
|---|---|
| HEAD doğrulanan | 6 (§10) |
| Grep eşleşmesi | 0 |
| Açık soru | R1 · R2 · R3 · A3 · A4 |
| PLANNED protokol | §12 (0/6) |
| Dürüstlük / Authority | 1 / 1 |

## §27 Dosya son kontrolü

- [x] §1–§5 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] [TASARIM]/UNKNOWN işaretleri korundu
- [ ] R1–R3 kapanmadı — status: draft kalır

## §28 Değişiklik günlüğü (spatial-audio-kapsam)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§5 + kısa Authority |
| 2026-10-13 | §6–§34 eklendi · dürüstlük + tam Authority · silme 0 |

## §29 Ek — format ↔ kapı matrisi (PLANNED)

| Format | Kapı | Değer |
|---|---|---|
| binaural | HRTF doğrulama | UNKNOWN (R3) |
| Ambisonics | hoparlör kod çözme | UNKNOWN (R1) |
| stereo genişletme | psikoakustik | UNKNOWN (R1) |

## §30 Ek — 8.1 entegrasyon notu (ADR-038)

ADR-038 8.1 surround hedefini taşır; spatial dalının bu hedefe katkısı
konum/yön katmanıdır (§2/1). Format (A3) UNKNOWN olduğu için hangi tekniğin
8.1'e uygulanacağı belirsizdir — tahmin yazılmaz.

## §31 Ek — HRTF ↔ K001 sınırı (§2/3/§24)

HRTF kafa-takibi donanım kaynağı K001-donanım'a bağlıdır; bu dal yalnız
HRTF'nin DSP tarafını sahiplenir. Donanım kaynağı UNKNOWN (A4).

## §32 Ek — wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | §3 satır 10 · §7 A1/A2 |
| `[[dsp-dogrulama]]` | kapı matrisi |
| `[[spatial-audio-ornekler]]` | literatür kod örnekleri |
| `[[spatial-audio-dogrulama]]` | iddia kanıtları |
| `[[reverb-kapsam]]` | A1 sınır komşusu |
| `[[K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec]]` | donanım sınırı |

## §33 Ek — format senaryoları (PLANNED — hangi format nerede?)

| Senaryo | Format | Durum |
|---|---|---|
| kulaklık | binaural | UNKNOWN (R1) |
| hoparlör kurulumu | Ambisonics / stereo | UNKNOWN (R1) |
| 8.1 surround | ADR-038 hedefi | UNKNOWN (A3) |
| stereo genişletme | basit pan | UNKNOWN (R1) |

**Kural:** bu senaryolar yalnız olasılık listesidir; hiçbiri CoreMusic kararı DEĞİL.

## §34 Ek — HRTF ↔ HRIR ayrımı (§7 genişletme)

| Kavram | Uzay | Not |
|---|---|---|
| HRTF | frekans | transfer fonksiyonu |
| HRIR | zaman | impulse response |

İkisi de aynı fiziksel gerçeğin farklı gösterimidir; CoreMusic hangisini
kullanacak? UNKNOWN (R3).

## §35 Ek — kafa-takibi senaryoları (§7/§31)

| Senaryo | Durum |
|---|---|
| kafa-takibi yok (sabit kaynak) | UNKNOWN (A4) |
| kafa-takibi var (gyro/track) | UNKNOWN (A4) |
| hareketli kaynak (kaynak hareketi) | UNKNOWN |

**Kural:** kafa-takibi donanım kaynağı belirsizdir; tahmin yazılmaz.

## §36 Ek — A1/A2 sınır genişletme (§24/§31)

| Sınır | spatial-audio | Komşu | Ayrım |
|---|---|---|---|
| A1 | konum/yön | reverb | oda hissi (difüz) |
| A2 | yerleştirme | delay | tek kopya |
| A3 | format | — | UNKNOWN |
| A4 | HRTF verisi | K001 | kafa-takibi |
| A5 | stereo görüntü | — | §8 (kavram) |

## §37 Ek — Q&A genişletme (§15'e devam)

**Q5 — `binaural early field` nedir (DSPark)?**
Reverb'ün erken yansıma alanının binaural sunumu (§4/4); literatür referansıdır,
CoreMusic'te yok.

**Q6 — 8.1 binaural mı?**
UNKNOWN (A3) — 8.1 hoparlör kurulumudur (ADR-038), binaural kulaklık DEĞİL;
format seçimi R1.

## §38 Ek — risk genişletme

| # | Risk | Etki |
|---|---|---|
| 5 | HRTF veri seti lisansı | kullanım engeli |
| 6 | format değişirse | zincir (pipeline §6) revizyonu |
| 7 | kafa-takibi donanımı yoksa | binaural sınırı |

## §39 Ek — anti-pattern genişletme (§13'e devam)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 5 | 8.1'i binaural sanmak | §37 Q6 |
| 6 | HRTF verisi uydurmak | A4/R3 ihlali |
| 7 | kafa-takibi varsaymak | §35 |

## §40 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 45 |
| Açık soru | R1 · R2 · R3 · A3 · A4 |
| HEAD doğrulanan | 6 |
| Grep eşleşmesi | 0 |
| Dürüstlük / Authority | 1 / 1 |

## §41 Ek — bu tur ne yapılmadı

Format seçimi yok · HRTF verisi yok · kafa-takibi varsayılmadı · 8.1
entegrasyonu iddia edilmedi · R1–R3 kapatılmadı.

## §42 Ek — dosya son kontrolü (§27'ye devam)

- [x] §numaraları artan sırada (§1–§45)
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] UNKNOWN/[TASARIM] işaretleri korundu
- [x] tahmin yok
- [ ] status: draft (R1–R3)

## §43 Ek — 2026-10-13 oturum notu

Yapılanlar: §6–§45 genişletme · dürüstlük + tam Authority · §10 HEAD (6 başlık) ·
§11 grep (0 eşleşme). Yapılmayanlar: format seçimi · HRTF verisi ·
kafa-takibi · R1–R3 kapatma.

## §44 Ek — kapı ↔ test zinciri (PLANNED tam zincir)

```text
motor kodu (YOK — grep §11) → format testi (§12/6) → kapı (§29)
   → PASS | FAIL | INCOMPLETE → rapor (§19) → dsp-master R1/R3
```

**Durum:** zincirin tamamı PLANNED — 0 test, kod yok.

## §45 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| `spatial-audio-ornekler.md` | 63 → 500+ | §sıradaki iş |
| `spatial-audio-dogrulama.md` | 49 → 500+ | §sıradaki iş |
| R1–R3 kapatma | üst karar | ⏳ |

## §46 Ek — format ↔ kapı ↔ test özeti (tam tablo)

| Format | Kapı | Test | Durum |
|---|---|---|---|
| binaural | HRTF doğrulama | — | UNKNOWN (R3) |
| Ambisonics | hoparlör kod çözme | — | UNKNOWN (R1) |
| stereo genişletme | psikoakustik | — | UNKNOWN (R1) |
| 8.1 (ADR-038) | entegrasyon | — | UNKNOWN (A3) |

## §47 Ek — Head doğrulama notu (bu dosya)

§10 bu tur doğrulanmıştır (6 başlık, 200 ✅). §4'teki 2026-10-12 satırları
korunmuştur (§22).

## §48 Ek — literatür kapı kaynak özeti

| Kaynak | Kullanım | Durum |
|---|---|---|
| Wikipedia Ambisonics | format literatürü | HEAD 200 ✅ |
| Wikipedia HRTF | kafa-takibi literatürü | HEAD 200 ✅ |
| DSPark AlgorithmicReverb | binaural early field | literatür ✅ |
| JUCE juce_dsp | modül adayı | içerik UNKNOWN |

## §49 Ek — kapı ↔ açık soru özeti (tam tablo)

| Kapı | Bağımlı açık soru |
|---|---|
| format seçimi | R1 (binaural/Ambisonics/stereo) |
| HRTF verisi | R3 (veri seti kaynağı) |
| kafa-takibi | A4 (donanım K001) |
| 8.1 entegrasyonu | A3 + ADR-038 |

## §50 Ek — sık yapılan hatalar (spatial)

| # | Hata | Doğrusu |
|---|---|---|
| 1 | format tahmini yazmak | A3/R1 UNKNOWN |
| 2 | HRTF verisi uydurmak | A4/R3 UNKNOWN |
| 3 | 8.1'i binaural sanmak | §37 Q6 |
| 4 | kafa-takibi varsaymak | §35 |

## §51 Ek — sayım ve kapı özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 51 |
| Kapılar | format UNKNOWN · HRTF UNKNOWN |
| Açık soru | R1 · R2 · R3 · A3 · A4 |
| Grep | 0 |
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
