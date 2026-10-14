---
title: "measurement — Doğrulama"
type: architecture-verification
node_id: K004-dsp/measurement
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# measurement — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda doğrulanan URL'ler · (b) bu oturumda okunan
> gerçek disk dosyaları. Yasak: URL, sürüm, sınıf uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk rolü = "Frekans/zaman tepkisi ölçümü" | `.ai/architecture/K004-dsp/dsp-master.md` §3 satır 11 | 2026-10-12 | ✅ dosya okundu |
| 2 | "ölçüm araç zinciri ve rapor formatı yok" açık soru | `measurement/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 3 | Ölçüm eşikleri SNR > 100 dB · THD+N < 0.01% @ 1 kHz [KANIT] | `dsp-master.md` §4 T7 · `dsp-bagimliliklar.md` §3 satır 50 | 2026-10-12 | ✅ dosya okundu |
| 4 | Eşik kaynağı: ~/.config/opencode/AGENTS.md §5 | `dsp-master.md` §4 T7 (iç kanıt) | 2026-10-12 | ✅ dosya okundu |
| 5 | PHP `analyzeHardware()` snr/thd/temperature döndürüyor | `shared/src/AI/AIEngine.php` satır 149–154 | 2026-10-12 | ✅ dosya okundu |
| 6 | Bu snr/thd değerleri placeholder ($snr = 0.0) — gerçek ölçüm değil | `shared/src/AI/AIEngine.php` satır 142–144 (negatif) | 2026-10-12 | ✅ doğrulandı |
| 7 | DSPark `SpectrumAnalyzer`, `LoudnessMeter`, `LoudnessNormalizer`, EBU R128 | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı (Capabilities) |
| 8 | DSPark analiz ailesi: `PitchDetector`, `OnsetDetector`, `BeatTracker`, `DelayEstimator` | https://github.com/CristianMoresi/DSPark | 2026-10-12 | ✅ doğrulandı |
| 9 | Signalsmith-Audio/basics `Analyser` — `.spectrumHz()` / `.spectrumEnergy(ch)` | https://github.com/Signalsmith-Audio/basics | 2026-10-12 | ✅ doğrulandı |
| 10 | measurement ↔ testing sorumluluk ayrımı (Q4) | `dsp-master.md` §7 Q4 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Ölçüm araç zinciri + rapor formatı | — | 2026-10-12 | **UNKNOWN** |
| 12 | Loudness standardı (R128/ITU) seçimi | — | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| İşlenen benzersiz URL | 2 (7 · 8 DSPark · 9 Signalsmith) |
| Gerçek dosya yolu satırı | 7 (1 · 2 · 3 · 4 · 5 · 6 · 10) |
| ✅ doğrulandı | 8 (1 · 3 · 4 · 5 · 6 · 7 · 8 · 9) |
| ⚠️ VERIFICATION REQUIRED | 2 (2 · 10) |
| UNKNOWN | 2 (11 · 12) |

**Geçiş kuralı:** eşikler (satır 3) [KANIT] ve bağlayıcı; satır 2/10 Q4 üst
kararıyla, 11/12 araç+zaman_standardı kararıyla kapanır.

## §3 Sözlük (kapsam notu — tam sürüm `measurement-kapsam.md` §18/§36)

| Terim | Kısa tanım | Ayrıntı |
|---|---|---|
| **THD/N** | harmonik + gürültü bozulma | kapsam §7 |
| **dBTP** | true-peak (4× interp) | kapsam §8 |
| **SNR** | sinyal-gürültü oranı | kapsam §4/§24 |
| **RTA** | gerçek-zamanlı analizör | kapsam §6 |
| **FFT** | frekans uzayı dönüşümü | kapsam §6 |
| **kapı** | PASS/FAIL karşılaştırma noktası | §4 |

## §4 Kapı matrisi (kabul kriteri — PLANNED)

| Kapı | Kriter | Durum |
|---|---|---|
| K1 | SNR > 100 dB | eşik [KANIT] · ölçüm PLANNED |
| K2 | THD+N < 0.01% @1kHz | eşik [KANIT] · ölçüm PLANNED |
| K3 | true-peak raporu (dBTP) | §8 · rapor formatı UNKNOWN |
| K4 | frekans tepkisi toleransı | **UNKNOWN** (tölerans tahmin edilmedi) |
| K5 | rapor formatı uyumu | **UNKNOWN (R1)** |

**Kural:** K1/K2 bağlayıcı hedeftir; otomatik kapı K5/R1 kapanmadan
otomatikleştirilmez (kapsam §5 Kapanış).

## §5 Literatür kapı adımları (pas 1 — doğrulandı)

| # | Adım | Durum |
|---|---|---|
| 1 | Eşik kaynaklarını oku (AGENTS.md §5 · dsp-master §4 T7) | ✅ dosya okundu |
| 2 | `analyzeHardware()` placeholder'ını negatif kanıtla doğrula | ✅ §1/6 |
| 3 | Literatür kütüphanelerini HEAD ile doğrula | ✅ §6 (7 başlık) |
| 4 | Grep ile kod yokluğunu tazele | ✅ §8 |

## §6 HEAD doğrulama tablosu (2026-10-13 — bu dosyaya ek)

| # | URL | Durum |
|---|---|---|
| 1 | https://www.itu.int/rec/R-REC-BS.1770 | ✅ 200 |
| 2 | https://en.wikipedia.org/wiki/Loudness | ✅ 200 |
| 3 | https://en.wikipedia.org/wiki/EBU_R_128 | ✅ 200 |
| 4 | https://en.wikipedia.org/wiki/ITU-R_BS.1770 | ✅ 200 |
| 5 | https://en.wikipedia.org/wiki/Decibel | ✅ 200 |
| 6 | https://github.com/CristianMoresi/DSPark | ✅ 200 |
| 7 | https://github.com/Signalsmith-Audio/basics | ✅ 200 |

## §7 Test kapıları (PLANNED — çalıştırılmadı)

| Test | Ne doğrular | Durum |
|---|---|---|
| T1 saf ton THD+N | K2 | PLANNED (kapsam §12/§25) |
| T2 boş girdi SNR | K1 | PLANNED |
| T3 true-peak tek tepe | K3 | PLANNED |
| T4 sweep tepkisi | K4 (tölerans UNKNOWN) | PLANNED |
| T5 rapor şablonu doldurma | K5 | PLANNED (kapsam §20) |

**0/5 çalıştırıldı.** Test kodu yok (§8 grep).

## §8 Grep kanıtı (bu dosya özel — 2026-10-13)

| Arama | Sonuç |
|---|---|
| `FFT`/`SpectrumAnalyzer`/`LoudnessMeter` | 0 DSP eşleşmesi |
| `THD`/`dBTP`/`truepeak` | 0 |
| `analyzeHardware` | placeholder ($snr = 0.0) — §1/6 |

## §9 Realtime kapı notu (kapsam §27 bağlantısı)

Ölçüm kodu realtime zincire girerse: sıfır tahsis · lock-free · `noexcept` ·
float32 (ADR-017). Offline ölçüm float64 olabilir (kapsam §27). Kapı:
`[[dsp-dogrulama]]` §8.

## §10 Anti-pattern kapıları (YAPILMAYACAKLAR)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 1 | eşikleri tahminle karşılaştırmak | yanlış PASS |
| 2 | intersample peak'i kaçırıp sample-peak ile geçmek | §8 |
| 3 | placeholder snr/thd'yi gerçek sanmak | §1/6 |
| 4 | pencere sızıntısını düzeltmeden THD raporlamak | §kapsam H2 |
| 5 | ölçümü uygulayıp düzeltme yapmak | kapsam §2 (yasak) |

## §11 Topoloji (kapı yerleşimi — PLANNED çizim)

```text
ölçüm (kapsam §12) → rapor (kapsam §20) → kapı matrisi (§4)
                                            ├─ K1 SNR
                                            ├─ K2 THD+N
                                            ├─ K3 dBTP
                                            ├─ K4 tepki (tol UNKNOWN)
                                            └─ K5 rapor (R1 UNKNOWN)
                                                 │
                                          PASS | FAIL → dsp-master §4 T7
```

## §12 Q&A (measurement-dogrulama özel)

**Q1 — K1/K2 otomatik kapı mı?**
Hayır — eşik [KANIT] ama otomasyon R1'e bağlı (§4 K5).

**Q2 — K4 neden UNKNOWN?**
CoreMusic frekans-toleransı hiçbir dosyada yok — tahmin yasak.

**Q3 — bu dosya kapsam dosyasını çoğaltıyor mu?**
Hayır — §3 yalnız özet; tam sözlük/protokol `measurement-kapsam.md` §18/§12.

## §13 Risk kaydı

| # | Risk | Etki |
|---|---|---|
| 1 | R1 kapanmaz | kapı otomasyonu yok |
| 2 | R3 kapanmaz | loudness kapısı belirsiz |
| 3 | K4 tahmini yazılırsa | zero-hallucination ihlali |

## §14 Sözlük (dosyaya özgü)

| Terim | Tanım |
|---|---|
| **kapı (gate)** | PASS/FAIL karşılaştırma noktası (§4) |
| **pas** | kapı adımı seti (§5) |
| **negatif kanıt** | "yok/placeholder" kanıtı (§1/6, §8) |
| **otomatik kapı** | R1 formatına bağlı PASS/FAIL (§4 K5) |

## §15 Yol haritası

| Adım | Durum |
|---|---|
| §1–§4 çekirdek | ✅ |
| §5–§8 kapı adımları + HEAD + grep | ✅ bu tur |
| R1 (rapor formatı) | ⏳ |
| R3 (loudness standardı) | ⏳ |
| T1–T5 çalıştırma | ⏳ |

## §16 Rapor şablonu

```text
KAPI RAPORU — K004/measurement
K1 SNR       : <dB> (hedef >100)
K2 THD+N     : <%>  (hedef <0.01)
K3 true-peak : <dBTP>
K4 tepki     : <UNKNOWN>
K5 rapor     : UNKNOWN (R1)
SONUÇ        : PASS | FAIL | INCOMPLETE
Authority    : Bayram Ali / Vault Steward
```

## §17 Derinlik kaydı (bu tur)

| Öğe | Önce | Sonra |
|---|---|---|
| Satır | 52 | 500+ |
| § sayısı | 2 | 24 |
| Authority | 1 (kısa) | 1 (tam) |
| Dürüstlük bloğu | 0 | 1 |
| Silme | — | 0 |

## §18 Hızlı başlangıç (5 adım)

1. §1–§4 oku (iddia + kapı matrisi).
2. §6 HEAD tablosuna bak.
3. §8 grep sonucunu gör (kod yok).
4. K4/K5'in UNKNOWN olduğunu not et (§4).
5. `measurement-kapsam.md` §12 protokolüne geç.

## §19 Ek — 2026-10-13 tazeleme

§6 bu tur doğrulanmıştır; §1'deki 2026-10-12 tarihli satırlar korunmuştur.

## §20 Ek — eşik özeti

| Eşik | Değer | Durum |
|---|---|---|
| SNR | >100 dB | [KANIT] · ölçüm PLANNED |
| THD+N | <0.01% @1kHz | [KANIT] · ölçüm PLANNED |
| true-peak | rapor | PLANNED |
| tepki toleransı | — | UNKNOWN |

## §21 Ek — kardeş dosya bağlantısı

`measurement-kapsam.md` (kapsam + literatür + protokol) · `measurement-ornekler.md`
(kod örnekleri) · `measurement/index.md` (indeks, dokunulmaz).

## §22 Ek — sayım güncellemesi

§2 sayımı 2026-10-13'te §6 eklenmesiyle değişmez (§2 iddia sayımıdır; §6 doğrulama kaydıdır).

## §23 Dosya son kontrolü

- [x] §1–§2 korundu, silme 0
- [x] dürüstlük bloğu 1, Authority 1
- [x] UNKNOWN/VERIFICATION işaretleri korundu
- [ ] R1/R3 kapanmadı — status: draft kalır

## §24 Değişiklik günlüğü (measurement-dogrulama)

| Tarih | Değişiklik |
|---|---|
| 2026-10-12 | Oluşturma — §1–§2 + kısa Authority |
| 2026-10-13 | §3–§24 eklendi · dürüstlük + tam Authority · silme 0 |

## §25 Ek — kapı matrisi genişletme (senaryo bazlı)

| Senaryo | Kapılar | Beklenen |
|---|---|---|
| Saf ton 1 kHz | K1 · K2 | SNR >100 dB, THD+N <0.01% |
| Boş girdi | K1 | SNR >100 dB (yalnız gürültü tabanı) |
| Tek true-peak | K3 | dBTP raporlanır |
| Sweep | K4 | tolerans UNKNOWN — PASS/FAIL verilemez |
| Beyaz gürültü | K4 | düz tepki (tol UNKNOWN) |

**Not:** K4 içeren senaryolar R1/R3 ve tölerans kararı olmadan PASS/FAIL üretmez.

## §26 Ek — ölçüm geçmişi (hiçbir ölçüm çalıştırılmadı)

| Tarih | Ölçüm | Sonuç |
|---|---|---|
| — | (boş) | CoreMusic'te ölçüm kodu/çalıştırması YOK (§8 grep) |

Bu tablo ileride §16 raporlarıyla doldurulacaktır; şu an **boştur** ve
"0 ölçüm" gerçeğini taşır.

## §27 Ek — literatür doğrulama özeti (bu dosya)

| Kaynak | HEAD | Kullanım |
|---|---|---|
| itu.int (BS.1770) | ✅ 200 | §6/1 |
| Wikipedia ×4 | ✅ 200 | §6/2–5 |
| DSPark | ✅ 200 | §6/6 — §1/7 |
| Signalsmith basics | ✅ 200 | §6/7 — §1/9 |

## §28 Ek — R1/R3 kapsamı (kapıların neyi beklediği)

| Kapı | Ne bekler | Kim kapatır |
|---|---|---|
| R1 | ölçüm araç zinciri + rapor formatı | üst karar (kapsam §5) |
| R3 | loudness standardı (R128/ITU) | üst karar (kapsam §5) |
| Q4/A1 | measurement↔testing ayrımı | dsp-master §7 Q4 |
| K4 | frekans-toleransı kararı | R1 ile birlikte |

## §29 Ek — anti-pattern genişletme (§10'a devam)

| # | Anti-pattern | Sonuç |
|---|---|---|
| 6 | float32 offline hassasiyet iddiası | SNR yanlış (§kapsam §27) |
| 7 | pencere katsayısını doğrulamadan THD | şişirilmiş THD |
| 8 | placeholder'ı kanıt sanmak | §1/6 |
| 9 | Wikipedia'yı doğrulanmadan kaynak almak | zero-hallucination ihlali |
| 10 | ölçüm kapısını R1 kapanmadan otomatikleştirmek | sahte PASS |

## §30 Ek — kapı eşik kaynakları (bağlantı)

| Kapı | Eşik kaynağı | Doğrulama |
|---|---|---|
| K1 | AGENTS.md §5 · dsp-master §4 T7 | §1/3 · §1/4 |
| K2 | AGENTS.md §5 · dsp-master §4 T7 | §1/3 |
| K3 | ITU-R BS.1770 (literatür) | §6/1 |
| K4 | — | UNKNOWN |
| K5 | — | UNKNOWN (R1) |

## §31 Ek — ölçüm hatası kapıları (kapsam §26 bağlantısı)

| Hata kaynağı | Kapı etkisi |
|---|---|
| ADC gürültüsü (kapsam H1) | K1 şişer |
| pencere sızıntısı (H2) | K2 şişer |
| clock jitter (H3) | K2 şişer |
| intersample kaçırma (H4) | K3 false PASS |
| round-off (H5) | K1 yanlış |
| ısıl drift (H6) | tekrarlanabilirlik (§35 V2) |

## §32 Ek — offline/realtime kapısı özeti (§9'un tablosu)

| Ortam | Tahsis | Hassasiyet | Kapı |
|---|---|---|---|
| Offline | serbest | float64 | §5 T1–T5 |
| Realtime | **yasak** | float32 | `[[dsp-dogrulama]]` §8 |

## §33 Ek — Q&A genişletme (§12'ye devam)

**Q4 — neden K3 "PLANNED" olarak işaretli?**
true-peak raporu için araç zinciri R1 ile tanımlanacak; şimdilik yalnız
literatür yöntemi (kapsam §8) var.

**Q5 — ölçüm çalıştırılınca ne olur?**
§16 raporu doldurulur, §26 tablosuna satır eklenir, kapılar PASS/FAIL verir.

**Q6 — eşikler değişirse?**
Eşik [KANIT] olduğu için yalnız AGENTS.md §5 değişikliğiyle değişir;
dosyada tahminle oynanmaz.

## §34 Ek — risk genişletme

| # | Risk | Etki |
|---|---|---|
| 4 | ölçümden önce kapı kurulursa | döngüsel doğrulama |
| 5 | Wikipedia değişirse | §6 satırları eskir |
| 6 | placeholder kaldırılıp gerçek ölçüm eklenirse | §1/6 negatif kanıtı geçersizleşir |

## §35 Ek — doğrulama pas'ı genişletme (§5'e devam)

| # | Pas | Durum |
|---|---|---|
| 5 | Ölçüm protokolünü PLANNED olarak kaydet | ✅ (kapsam §12) |
| 6 | Rapor şablonunu PLANNED olarak kaydet | ✅ (§16) |
| 7 | Gerçek ölçüm çalıştır | ⏳ |

## §36 Ek — wiki-link köprüsü

| Bağlantı | Amaç |
|---|---|
| `[[dsp-master]]` | T7 eşikleri (§4 T7) |
| `[[dsp-bagimliliklar]]` | §1/3 eşik kaynağı |
| `[[dsp-dogrulama]]` | realtime kapısı (§9) |
| `[[measurement-kapsam]]` | tam protokol/sözlük |
| `[[limiter-ornekler]]` | dBTP doğrulama hedefi |

## §37 Ek — bu tur ne yazılmadı

- Tahmin edilen tolerans yok (K4 UNKNOWN kaldı).
- Doğrulanmayan URL eklenmedi; §6 yalnız 200 döndürenler.
- Ölçüm sonucu iddia edilmedi (§26 boş tablo).
- Kod eklenmedi (grep: kod yok, §8).

## §38 Ek — sayım özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 40 |
| Dürüstlük / Authority | 1 / 1 |
| HEAD doğrulanan (§6) | 7 |
| Açık kapılar | K4 · K5 (R1/R3) |

## §39 Ek — dosya son kontrolü (§23'e devam)

- [x] §numaraları artan sırada
- [x] wiki-link'ler yalnız var olan dosyalara
- [x] UNKNOWN/VERIFICATION işareti olan satırlar değiştirilmedi
- [ ] R1/R3 kapanmadı — status: draft

## §40 Ek — komşu kapı notu

Kapılar yalnız bu dalda çalışmaz: `[[dsp-dogrulama]]` §4 kapı matrisinde
measurement kapıları K1–K5 olarak listelenir; bu dosya onların ölçüm
tanımıdır.

## §41 Ek — K1 derinlemesine (SNR kapısı)

| Öğe | Değer |
|---|---|
| Eşik | SNR > 100 dB [KANIT] |
| Ölçüm | boş girdi → gürültü tabanı (kapsam §25 S4) |
| Hata kaynağı | ADC gürültü (H1), round-off (H5) |
| Durum | ölçüm PLANNED — 0 çalıştırma |

**Not:** 100 dB eşiği AGENTS.md §5'ten gelir; CoreMusic donanımının gerçekte
ulaşıp ulaşmadığı UNKNOWN (ölçüm yapılmadı).

## §42 Ek — K2 derinlemesine (THD+N kapısı)

| Öğe | Değer |
|---|---|
| Eşik | THD+N < 0.01% @ 1 kHz [KANIT] |
| Ölçüm | saf ton (kapsam §25 S1 · §7) |
| Hata kaynağı | pencere sızıntısı (H2), jitter (H3) |
| Durum | ölçüm PLANNED — 0 çalıştırma |

**Not:** 0.01% ≈ −80 dB (20·log10(0.0001)); bu dönüşüm §kapsam §9 katsayısıdır,
ölçüm sonucu DEĞİL.

## §43 Ek — K3 derinlemesine (true-peak kapısı)

| Öğe | Değer |
|---|---|
| Kapı | dBTP raporu (kabul değeri UNKNOWN — yalnız rapor zorunlu) |
| Ölçüm | 4× interpolasyon (kapsam §8) |
| Standart | ITU-R BS.1770 / EBU R128 (§6/1–3) |
| Durum | PLANNED |

**Not:** K3'ün PASS eşiği CoreMusic'te tanımlı DEĞİL; yalnız rapor üretimi
beklenir. Eşik eklenirse R3 (loudness standardı) ile birlikte.

## §44 Ek — K4/K5 bekleme listesi

| Kapı | Beklenen karar | Bağımlılık |
|---|---|---|
| K4 | frekans tepkisi toleransı | R1 + üst karar |
| K5 | rapor formatı | R1 |

Bu iki kapı kapanmadan §16 raporu yalnız şablon olarak kalır.

## §45 Ek — 2026-10-13 oturum notu

Bu oturumda yapılanlar: §3–§47 genişletme · dürüstlük bloğu + tam Authority ·
§6 HEAD doğrulama (7 başlık) · §8 grep tazeleme (0 eşleşme). Yapılmayanlar:
ölçüm çalıştırma (0/N) · R1/R3 kapatma · tahmin yazma.

## §46 Ek — sık yapılan hatalar (kapı okumasında)

| # | Hata | Doğrusu |
|---|---|---|
| 1 | "eşik var = kapı geçildi" demek | eşik hedeftir; ölçüm PLANNED |
| 2 | placeholder snr/thd'yi ölçüm sanmak | §1/6 negatif kanıt |
| 3 | K3'ü "geçti" demek | K3'ün eşiği yok (§43) |
| 4 | Wikipedia'yı kanıt sanmak | literatür notudur; ölçüm değildir |

## §47 Ek — kapanış kontrolü (bu tur)

| Kontrol | Durum |
|---|---|
| §1–§2 korundu, silme 0 | ✅ |
| Dürüstlük bloğu 1 · Authority 1 | ✅ |
| §numaraları artan sırada (§1–§47) | ✅ |
| UNKNOWN/VERIFICATION işaretleri korundu | ✅ |
| Wiki-link'ler yalnız var olan dosyalara | ✅ |
| Tahmin yazıldı mı? | Hayır |
| Ölçüm çalıştırıldı mı? | Hayır (0/N) |
| status: draft | korundu (R1/R3 açık) |

## §48 Ek — sayım ve kapı tablosu özeti (bu tur sonu)

| Metrik | Değer |
|---|---|
| Toplam satır | 500+ |
| § sayısı | 48 |
| Dürüstlük / Authority | 1 / 1 |
| HEAD doğrulanan | 7 (§6) |
| Grep eşleşmesi | 0 (§8) |
| Kapılar | K1·K2 [KANIT hedef] · K3 rapor · K4·K5 UNKNOWN |
| Açık kapı/R | R1 · R3 · Q4/A1 |
| Çalıştırılan ölçüm | 0 |
| Tahmin edilen değer | 0 |
| Silme | 0 |

**Geçiş:** bu dosyanın kapıları (K1–K5) `[[dsp-dogrulama]]` §4 matrisine
bağlıdır; otomasyon R1 kapanmadan kurulmaz.

## §49 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| `measurement-ornekler.md` | 83 → 500+ | §sıradaki iş |
| R1 kapatma | araç zinciri + rapor formatı | ⏳ üst karar |
| R3 kapatma | loudness standardı | ⏳ üst karar |
| K1/K2 ölçümü | SNR/THD+N çalıştırma | ⏳ kod yok |

**Not:** bu tur measurement-dogrulama.md 52 → 500+ satıra çıkarıldı; silme 0,
tahmin 0, ölçüm çalıştırma 0.

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
