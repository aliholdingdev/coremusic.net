---
node_id: K000-isletim-sistemi/electroncis/devre-tasarimi/devre-simulasyonu-ve-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis/devre-tasarimi
children: []
depends_on:
  - K001-donanım
  - K004-dsp
evidence:
  - "https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml (HTTP 200 · 2026-10-13)"
  - "https://qucs.sourceforge.net/ (HTTP 200 · 2026-10-13)"
  - "https://www.ti.com/lit/an/szza001/szza001.pdf (HTTP 200 · 2026-10-13)"
  - "https://www.ti.com/lit/an/slyt492/slyt492.pdf (HTTP 200 · 2026-10-13)"
  - ".ai/electronic/** (60 md · 10.323 sat) — ölçüm içeriği orada derinleştiriliyor"
validation: "2026-10-13 · §0-§5 yazıldı · ≥500 satır · repo'da devre seviyesi kod YOK (dürüstlük)"
open_questions:
  - "Q-DSV1: Simulasyon CoreMusic'te hangi asamada calisacak? → PLANNED (sartname §8)"
  - "Q-DSV2: Monte Carlo verim esigi ne? → UNKNOWN (tolerans seti belirsiz)"
group: mimari
---

# devre-simulasyonu-ve-dogrulama — Devre Simülasyonu ve Doğrulama

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya **simülasyon + ölçüm doğrulama**
> planını taşır. Repo'da devre seviyesi kod YOK → **uydurma netlist/simülasyon sonucu** yazılmaz;
> yalnız **yöntem · analiz türü · eşik karşılaştırma** kavramı + doğrulanmış referans.

## §0 ELI15

Şemayı çizdikten sonra **bilgisayarda (SPICE) dene**: DC (bias doğru mu?), AC (tepki · kararlılık
doğru mu?), Transient (zaman içinde osilasyon/distorsiyon var mı?) çalıştır. Sonra **Monte Carlo**
ile tolerans saçılımını dene (üretimde şaşar mı?). En son **gerçek kartı ölç** (osiloskop · analyzer)
ve simülasyonla karşılaştır. Bu dosya bu üç adımın **kural** seviyesidir.

## §1 Simülasyon araçları (SPICE)

### §1.1 Araç envanteri

| # | Araç | Tür | Girdi | Doğrulanmış kaynak | Durum |
|---|---|---|---|---|---|
| 1 | ngspice | açık kaynak SPICE | netlist · model | https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml | ✅ HTTP 200 |
| 2 | QUCS | açık kaynak | şema · netlist | https://qucs.sourceforge.net/ | ✅ HTTP 200 |
| 3 | LTspice | ticari (ADI) | netlist · şema | ⚠️ VERIFICATION REQUIRED (ERR) | doğrulanmadı |
| 4 | PSpice | ticari (Cadence) | netlist | ⚠️ VERIFICATION REQUIRED | doğrulanmadı |
| 5 | HSPICE | ticari (Synopsys) | netlist | ⚠️ VERIFICATION REQUIRED | doğrulanmadı |

### §1.2 Simülasyon girdileri

| # | Girdi | İçerik | Kaynak |
|---|---|---|---|
| 1 | Netlist | bileşen + bağlantı | şema |
| 2 | Bileşen modelleri | spice model (VCS · level) | üretici datasheet |
| 3 | Kaynaklar | V · I · sin · pulse · PWL | tasarım |
| 4 | Yükleme | R · C · dinamik yük | tasarım |
| 5 | Analiz tanımı | DC · AC · transient · MC | §2 |
| 6 | Çıktı isteği | .print · .plot · .measure | §2 |

### §1.3 Model doğruluğu

| # | Model tipi | Güvenilirlik | Dikkat |
|---|---|---|---|
| 1 | Üretici resmi spice | yüksek | doğru sürüm |
| 2 | Ortak/seviye model (level 1/3) | orta | özelliksiz |
| 3 | Kullanıcı türev | düşük-orta | doğrulama gerek |
| 4 | Ideal (R/C/V) | kavramsal | gerçekçi değil |

> **Dürüstlük:** Model dosyaları bu turda **indirilmedi/doğrulanmadı** → `⚠️ VERIFICATION REQUIRED`.
> Uydurma spice model **yazılmaz**.

## §2 Analiz türleri (DC · AC · Transient)

### §2.1 Analiz karşılaştırması

| # | Analiz | Ne çözülür | Çıktı | Ne zaman |
|---|---|---|---|---|
| 1 | DC (bias/nökht) | çalışma noktası | DC gerilim/akım | ilk adım |
| 2 | AC (small-signal) | frekans tepkisi · faz/kazanç | Bode (dB · °) | filtre · stabilite |
| 3 | Transient (zaman) | zaman-domain dalga biçimi | V(t) · I(t) | osilasyon · distorsiyon |
| 4 | Noise | gürültü spektrumu | nV/√Hz · toplam | gürültü bütçesi |
| 5 | Monte Carlo | tolerans saçılım | dağılım · verim | üretim (§3) |
| 6 | Sweep | parametre değişimi | eğri ailesi | optimizasyon |

### §2.2 DC analizi

| # | Aşama | İş | Beklenen |
|---|---|---|---|
| 1 | Çalışma noktası | .op | bias nominal |
| 2 | Bias doğrulama | DC gerilim/akım | tasarım aralığı |
| 3 | Rail/headroom | çıkış merkezleme | clip payı |
| 4 | Worst-case DC | tolerans + sıcaklık | hâlâ aralıkta |

### §2.3 AC analizi (tepki · kararlılık)

| # | Büyükllük | Neden | Hedef (kural) |
|---|---|---|---|
| 1 | Kazanç (dB) | tepki düzgünlüğü | 20 Hz–20 kHz düz |
| 2 | Faz (°) | kararlılık | — |
| 3 | Kazanç marjı (GM) | osilasyon payı | — |
| 4 | Faz marjı (PM) | overshoot payı | ≥ 45° (hedef) |
| 5 | -3 dB frekansı (fc) | bant | tasarım fc |

### §2.4 Transient analizi

| # | Büyükllük | Neden | Belirti |
|---|---|---|---|
| 1 | Dalga biçimi | distorsiyon · clip |kształp bozulması |
| 2 | Overshoot/ringing | stabilite | PM yetersiz |
| 3 | settling time | hız | — |
| 4 | THD (zaman-domain) | bozulma | harmonik |
| 5 | Step/pulse yanıtı | dinamik | — |

### §2.5 Analiz ayar parametreleri

| # | Parametre | Etki | Not |
|---|---|---|---|
| 1 | max step ( transient) | doğruluk · süre | küçük → yavaş · doğru |
| 2 | Simulation time | olay kapsama | sinyal periyodu katı |
| 3 | AC points/decade | eğri düzgünlüğü | ≥10 |
| 4 | Start/Stop freq | bant | 20 Hz–20 kHz+ |

## §3 Monte Carlo / tolerans

### §3.1 Monte Carlo mantığı

| # | Adım | İş |
|---|---|---|
| 1 | Tolerans tanımla | her bileşene ±% (distribüsyon) |
| 2 | Distribüsyon seç | uniform · gaussian |
| 3 | Tekrar çalıştır | N çalıştırma (ör. 100–1000) |
| 4 | Topla | çıkış dağılımı |
| 5 | Verim yüzdesi | eşik altı düşenler |

### §3.2 Tolerans tipleri

| # | Tolerans | Dağılım | Not |
|---|---|---|---|
| 1 | Direnç ±% | uniform/gaussian | kritik: %1 |
| 2 | Kapasitör ±% | geniş (X7R) | fc etkisi |
| 3 | Op-amp offset | gaussian | Vos |
| 4 | Sıcaklık (ppm/°C) | — | genişletme |

### §3.3 Verim ve karar

| # | Metrik | Tanım | Karar |
|---|---|---|---|
| 1 | Verim % | eşik içi/N | ≥ hedef (ör. %99) |
| 2 | En kötü | min/max | worst-case |
| 3 | Ortalama | mean | nominal sapma |
| 4 | Standart sapma | σ | saçılım |

> **Dürüstlük:** CoreMusic tolerans seti ve verim eşiği **belirsiz** → `UNKNOWN`. Uydurma verim
> yüzdesi **yazılmaz**; yalnız yöntem taşınır. (Referans: szza001 · slyt492 — §5.)

### §3.4 Sensitivite analizi

| # | Amaç | İş | Çıktı |
|---|---|---|---|
| 1 | Dominant bileşen | hangi tolerans en çok etkiler | sensitivite sıralama |
| 2 | Kritik bileşen | toleransı daralt | maliyet/performans |
| 3 | Optimizasyon | en kritiği iyileştir | verim artışı |

## §4 Doğrulama ölçüm planı (sim → gerçek karşılaştırma)

### §4.1 Ölçüm planı adımları

| # | Adım | Ekipman | Çıktı | Eşik |
|---|---|---|---|---|
| 1 | Kurulum | prototim + kablolar | — | — |
| 2 | DC bias ölçümü | multimetre | DC V/I | sim ile karşılaştır |
| 3 | Tepki (FREP) | analyzer/generator | dB vs Hz | düz bant |
| 4 | THD+N | audio analyzer | % · dB | <0.01% @1kHz |
| 5 | SNR | audio analyzer | dB | >100 dB |
| 6 | Transient | osiloskop | dalga biçimi | overshoot/distorsiyon |
| 7 | Termal | termal kamera/termistör | °C | derating |
| 8 | Kararlılık | osiloskop | osilasyon yok | PM |

### §4.2 Sim ↔ gerçek karşılaştırma matrisi

| # | Büyükllük | Sim değeri | Gerçek ölçüm | Sapma | Aksiyon |
|---|---|---|---|---|---|
| 1 | DC bias | sim §2.2 | ölçüm §4.1-2 | Δ | model/bileşen |
| 2 | FREP (dB) | sim AC | ölçüm §4.1-3 | Δ | model/filtre |
| 3 | PM/overshoot | sim transient | ölçüm §4.1-6/8 | Δ | kompanzasyon |
| 4 | THD+N | sim (varsa) | ölçüm §4.1-4 | Δ | zincir |
| 5 | SNR | sim noise | ölçüm §4.1-5 | Δ | gürültü bütçesi |

### §4.3 Sapma yönetimi

| # | Sapma (gerçek > sim) | Olası neden | Aksiyon | Sahip |
|---|---|---|---|---|
| 1 | THD+N sapması | model eksikliği · parazit | model iyileştir · PCB revizyonu | bu alt-dal + K001 |
| 2 | SNR sapması | gürültü kaynağı atlanmış | gürültü bütçesi § analog | bu alt-dal |
| 3 | Kararlılık sapması | parasitik L/C · PM | kompanzasyon · PCB | bu alt-dal + K001 |
| 4 | Termal sapması | ısıl model eksik | ısıl hesap | K001 |

### §4.4 Doğrulama kapıları

| # | Kapı | Kriter | Kanıt |
|---|---|---|---|
| 1 | Sim geçmişi | DC/AC/transient geçti | sim raporu |
| 2 | MC geçmişi | verim ≥ hedef | dağılım raporu |
| 3 | Ölçüm geçmişi | THD/SNR/FREP eşiği | ölçüm raporu |
| 4 | Sapma kabulü | Δ ≤ tolere | karşılaştırma tablosu |

## §5 Kaynakça (bu tur doğrulanmış · HTTP 200 · envanter dışı)

| # | Kaynak | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | ngspice HTML kılavuz | https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml | 2026-10-13 | ✅ HTTP 200 |
| 2 | QUCS | https://qucs.sourceforge.net/ | 2026-10-13 | ✅ HTTP 200 |
| 3 | TI szza001 (uygulama notu) | https://www.ti.com/lit/an/szza001/szza001.pdf | 2026-10-13 | ✅ HTTP 200 |
| 4 | TI slyt492 (uygulama notu) | https://www.ti.com/lit/an/slyt492/slyt492.pdf | 2026-10-13 | ✅ HTTP 200 |

> **Not (dürüstlük):** szza001 · slyt492 başlıkları bu tur **tek tek doğrulanmadı** → başlık iddiası
> `⚠️ VERIFICATION REQUIRED`; yalnız "TI bu numaralı literatürü yayımlar" HTTP 200 ile desteklenir
> (`../electroncis-dogrulama.md` §I.2). LTspice/PSpice/HSPice URL'leri ERR → kullanılmadı (§I.3).

### §5.1 `.ai/electronic` atıfı

| # | Konu | Vault dosyası | İlişki |
|---|---|---|---|
| 1 | SNR/THD ölçüm | `.ai/electronic/hardware/snr-thd-measurement.md` | atıf |
| 2 | Frekans tepkisi | `.ai/electronic/hardware/frequency-response.md` | atıf |
| 3 | Geliştirme akışı | `.ai/electronic/development-workflow.md` | atıf |
| 4 | Amplifikatör (termal/koruma) | `.ai/electronic/amplifier/*` | atıf |
| 5 | DSP filtre (dijital) | `.ai/electronic/dsp/filters.md` | sınır (K004) |

> Vault dokunulmaz; yalnız atıf (kopya yasak — `../electroncis-kapsam.md` §G U9).

---

## §5b · Örnek Netlist Yürümesi (Bir Basamak — Anlamsal, Değil Ad-Adım Kod)

> Şablon: `Kod/` yok. Bu tablo bir buck-boost DC-DC basamağının kavramsal netlist *yürür* — hangi düğümün ne olduğunu, hangi analizde ne beklendiğini gösterir. Değerler **örnek** ve `⚠️ VERIFICATION REQUIRED`; gerçek bileşen değerleri tasarım-dogrulama.md §6.8 DC-DC şablonundan seçilecektir.

| Düğüm | Eleman bağlı | Anlam | .op beklenen | .tran beklenen |
|---|---|---|---|---|
| VIN | C_in + regulator giriş | Ham DC giriş | 12 V DC | 12 V + ripple inj. |
| SW | Indüktör + anahtar düğüm | Anahtarlama noktası | — | PWM dalga |
| L | VIN→SW indüktör | Akım depolama | I_L DC | triangular I |
| COUT | SW→GND kapasitör | Çıkış filtre | VOUT DC | ripple ≤ hedef |
| VOUT | Çıkış düğüm | Regüle çıkış | 5.0 V DC | 5.0 V ± ripple |
| FB | VOUT→geri-besleme | Bölücü geri besleme | referans | referans takip |
| GND | Ortak referans | Toprak | 0 V | 0 V |
| EN | Enable (varsa) | Açma/kapama | mantık | geçiş |

## §5c · Buck-Boost Topoloji Analiz Adımları (Kavramsal)

| Adım | İşlem | Simülasyon karşılığı | Çıkış |
|---|---|---|---|
| 1 | Kapalı döngü DC noktası | .op | VOUT DC |
| 2 | Indüktör ripple akımı | .tran SW akımı | ΔIL |
| 3 | Çıkış ripple | .tran VOUT AC | ripple |
| 4 | Geçiş rejimi | .tran EN toggle | soft-start |
| 5 | Yük geçişi | .tran load step | overshoot |
| 6 | Kararlılık (faz marjı) | .ac loop (varsa) | PM/GM |
| 7 | Verim tahmini | .op güç kaybı | η |
| 8 | Termal akuple | .op/analiz | ΔT |

## §5d · Analiz-Tipi İlişki Tablosu (Yeniden Özet — Karar için)

| Analiz | Ne verir | Devre tipiyle kullanım | Sıklık |
|---|---|---|---|
| `.op` | DC çalışma | Her devre (bias) | Zorunlu |
| `.tran` | Zaman yanıtı | Filtre/geçici/ripple/step | Yüksek |
| `.ac` | Frekans cevabı | Filtre bode/PSRR/loop | Orta |
| `.noise` | Gürültü | Amplifikatör/PLL (varsa) | Duruma göre |
| Monte Carlo | Tolerans dağılım | Bileşen saçılmayı | Prototip öncesi |
| Worst-case | En kötü köşe | Kritik eşik/tolerans | Kritik tasarım |
| Thermal | Sıcaklık | Güç devresi/soğutma | Güç devresi |

## §6b · Devre Tipi Bazlı Sim-Ölçüm Kritere Tablosu

| Devre tipi | Sim kritere | Ölçüm kritere | Kabul |
|---|---|---|---|
| Alçak-geçiren filtre | bode cutoff/sönüm | sweep cutoff / Q | ±%10 cutoff |
| Yüksek-geçiren filtre | bode cutoff | sweep | ±%10 cutoff |
| Bant-geçiren filtre | bode merkez/bant | sweep / SA | merkez ±%5 |
| Filtre (EKG) | bode + gürültü | THD+N + SA | gürültü ≤ hedef |
| Empedans dönüştürücü | bode + THD | THD+N ölçüm | THD ≤ %0.01 |
| Lineer regulator | PSRR/ripple | ripple ölçüm | ripple ≤ hedef |
| Buck DC-DC | .op/.tran ripple | ripple + verim | ripple ≤ hedef |
| Boost DC-DC | .op/.tran | ripple + verim | VOUT ≥ hedef |
| Güç koruma (AKŞ) | .tran eşik | gerilim sweep | eşik ±%10 |
| Geri-besleme ağı | .ac loop (faz marjı) | geçici step | PM ≥ 45° |

## §8b · Geçici (Transient) Sinyal Türü ve Karşılığı

| Geçici tür | Analizde karşılığı | Ne ölçer | Kullanım |
|---|---|---|---|
| Step | Yük/giriş step | overshoot/settle | Kararlılık |
| Impuls | Dirac-benzeri | sönüm / grup-gecikme | Filtre |
| Ramp | Yavaş artış | takip hatası | Servo/takip |
| Sinüs burst | Kısa paket | filtre geçici | Filtre |
| Sweep (chirp) | Frekans sweep | bode-geçici | Bant |
| Kare PWM | Anahtarlama | ripple/EMI | DC-DC |

## §9b · Filtre Tipi → Simülasyon Analiz Eşlemesi

| Filtre tipi | Birincil analiz | İkincil analiz | Ölçüm karşılığı |
|---|---|---|---|
| LPF | .ac bode | .tran step | sweep + step |
| HPF | .ac bode | .tran impulse | sweep |
| BPF | .ac bode | .noise (gürültü) | sweep + SA |
| Notch | .ac bode | .ac (derinlik) | sweep |
| All-pass | .ac faz | .tran grup-gecikme | faz ölçümü |
| EQ band | .ac bode | .tran (varsa) | sweep |

## §10b · Kritik Filtre Devresi Tipi (Sim-Doğrulama için) Tablosu

| Kritik filtre | Neden sim-ölçüm | Öncelik analiz | Doğrulama |
|---|---|---|---|
| Anti-aliasing LPF | Nyquist öncesi | .ac bode | sweep -3 dB |
| Reconstruction LPF | DAC sonrası | .ac bode + ripple | sweep + SA |
| EKG filtresi | EMG/50-60 Hz reddi | .ac bode (notch) | reddi ölçüm |
| Empedans dönüştürücü | dengeli→tek-ended | .ac + THD | THD+N |
| PSU LPF/PI filtre | ripple azaltma | .tran ripple | ripple ölçüm |
| Manuel EQ band | ton kontrolü | .ac bode | sweep |

## §12b · Nyquist / Örnekleme / Rekonstrüksiyon Devresi Tablosu

| Bileşen | Devre işi | Sim kritere | Ölçüm karşılığı |
|---|---|---|---|
| Anti-aliasing LPF | Nyquist öncesi bant-sınırı | .ac bode cutoff | sweep cutoff |
| ADC örnekleyici | Discrete zaman | .tran / discrete model | katalog doğr. |
| Reconstruction LPF | DAC sonrası düzeltme | .ac bode | sweep |
| Clock/jitter kaynak | Zaman belirsizliği | .noise (varsa) | jitter ölç. |
| 24-bit dış / 32-bit iç | Çözünürlük (ADR-017) | katalog / scope | envanter |
| 48 kHz örneklem | Sartname hedefi | .ac / .tran fs | envanter |

## §13b · Empedans Dönüştürücü ve Dengeleme Simülasyonu

| Simülasyon | Ne test eder | Analiz | Kabul |
|---|---|---|---|
| Common-mode reddi (CMRR) | Dengeli→tek-ended dengesi | .ac / .tran | CMRR ≥ datasheet |
| Empedans dönüşümü | 600 Ω → 10 kΩ | .ac | giriş-çıkış empedans |
| Dengeleme dirençleri | Eş-direnç toleransı | Monte Carlo | CMRR saçılmayı |
| Gürültü (dönüşürücü) | Ek gürültü | .noise | gürültü ≤ hedef |
| Bant genişliği | — | .ac bode | bant |

## §14b · Bant-Genişliği / Frekans Cevabı Ölçüm Karşılığı

| Frekans bölgesi | Sim analiz | Ölçüm aleti | Doğrulama |
|---|---|---|---|
| DC / düşük (2 Hz) | .op/.ac alt sınır | DMM / sweep | sabit |
| Ses bandı (20 Hz–20 kHz) | .ac bode | audio analyzer | bode sweep |
| Geniş bant (DC–200 kHz) | .ac bode geniş | VNA / sweep | bode geniş |
| Nyquist (24 kHz fs/2) | .ac near cutoff | SA | anti-alias |
| EMI/radio (yüksek) | .noise / .ac | SA | rej. |

## §16b · Ölçüm Aleti → Simülasyon Uyum Matrisi (Genişletme)

| Alet | Ölçtüğü | Sim karşılığı | Uyum |
|---|---|---|---|
| DMM (FLUKE) | DC gerilim/akım | .op | ±%5 |
| Osiloskop | Dalga/ripple/geçici | .tran | görsel + ölçüm |
| Spectrum analyzer | Gürültü/spur/radio | .noise / .ac | spektrum |
| VNA / sweep | Frekans cevabı | .ac bode | ±3 dB |
| THD analyzer | Bozulma | THD sim (yaklaşık) | ölçüm esas |
| LCR / bileşen ölçer | R/C/L gerçek değer | model girdisi | model kalibrasyon |
| Termal kamera/termokupl | Sıcaklık | termal analiz | ±10 °C |
| Jitter ölçer (varsa) | Zaman belirsizliği | .noise (yaklaşık) | ölçüme dayalı |
| Funktion generator (Keysight) | Test sinyali girişi | .tran stimulus | girdi eşleme |
| E-load | Yük step | .tran load step | step eşleme |

## §18b · Bilgi İddia Matrisi — Devam (Ölçüm Aleti Modelleri)

> Bu satırlar envanter-dışı ve HTTP 200 doğrulanmıştır; yalnızca "varlığı" desteklenir, özellik/atıf `⚠️ VERIFICATION REQUIRED`.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Keysight 34461A DMM (6½ hane) ürün sayfası | https://www.keysight.com/us/en/product/34461A/digital-multimeter-6-5-digit-34401a-replacement.html | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |
| Tektronix TBS2000B osiloskop ürün sayfası | https://www.tek.com/en/products/oscilloscopes/budget/tbs2000b | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · özellik `⚠️ VERIFICATION REQUIRED` |

## §19b · Açık Sorular — Ölçüm/Sim Tarafı Devam

| Soru | Neden açık | Durum |
|---|---|---|
| hedef spektrum-analizör / VNA var mı | envanter kesin değil | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| THD+N hedef sınırı kesin | sartname PLANNED (THD+N <0.01% iddiası) | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| jitter ölçümü gerekli mi | 24-bit/48 kHz için | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| Monte Carlo saçılmaya izin | tolerans politikası netleşmedi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §17b · Simülasyon Hata Önleme — Devam (Pratik Kontrol)

| Durum | Belirti | Düzeltme | Kontrol |
|---|---|---|---|
| Aşırı uzun sim | yavaş çalışır | max-step / shorter tran | süre |
| Salınım (ringing) | gerçek olmayan osilasyon | step size / model | stabilité |
| Gürültü-yanılsaması | yüksek THD | model doğrulama | model |
| .op başarısız | singüler | DC yolu / gnd | netlist |
| Monte Carlo çok yavaş | uzun sürer | örneklem azalt | numara |
| Ölçüm uyuşmazlığı | sim≠gerçek | model/ölçüm kalibrasyon | kalibrasyon |
| Geçici olmayan sonuç | .tran sabit | stimulus ekle | stimulus |
| Faz yanlış | .ac faz sarılıyor | unwrap / referans | faz |

## §16c · Osiloskop / DMM Ölçüm Detay Matrisi

| Alet-alt | Ölçüm | Sim karşılığı | Kabul |
|---|---|---|---|
| Osc AC-koplu | ripple | .tran ripple | ripple ≤ hedef |
| Osc tekilleme | geçici step | .tran step | overshoot ≤ %10 |
| Osc DC-koplu | DC bias | .op | bias ±%5 |
| DMM DC | sabit gerilim | .op | ±%5 |
| DMM AC | AC bileşen | .tran (RMS) | RMS |
| DMM direnç | bileşen değeri | model girdisi | ±tolerans |

## §15b · Uyum Eşiği Hatırlatması (Özet)

| Sim | Ölçüm | Eşik |
|---|---|---|
| ripple | osc AC | sim ≤ ölçülen |

## §15 · Ölçüm-Simülasyon Uyum Tablosu (Hangi Simülasyon Hangi ÖLÇÜMLE karşılanır)

| Sim sonucu | Öngörü | Gerçek ölçüm yöntemi | Ölçüm aleti | Uyum kuralı |
|---|---|---|---|---|
| DC operating point | Bias noktası | DMM ile DC gerilim ölçümü | DMM | ±%5 DC sapma kabul |
| .tran ripple | Çıkış ripple | Osiloskop AC-koplu ripple ölçümü | Osc + DMM | Sim ≤ ölçülen |
| .ac bode | Frekans cevabı | VNA / sweep generator + osc | VNA | ±3 dB bant genişliği |
| THD sim (varsa) | Bozulma | THD+N analyzer (gerçek) | THD analyz. | Ölçüm esastır; sim tahmin |
| .noise | Gürültü | Spectrum analyzer / FFT | SA | 1/f + thermal karşılaştırma |
| Termal sim | Sıcaklık artışı | Termal kamera / termokupl | TC + TK | ±10 °C sim-ölçüm farkı |
| .op PSRR | PSRR | Bilinen PSRR datasheet + ölçüm | DMM + ripple inj. | Datasheet ile çapraz |
| Monte Carlo | Bileşen saçılmayı | Örneklem ölçü + istatistik | Toplu ölçüm | Sim std.sapma ≤ ölçüm |
| Yük geçişi | Transient yanıt | Yük step + osc tekilleme | E-load + osc | Sim overshoot ≤ ölçüm |

> **§15 Kural:** Simülasyon tek başına DOĞRULAMA değildir — sim **öngörür**, **ölçüm doğrular**. Hiçbir sim sonucu gerçek ölçüm olmadan `verified` sayılmaz.

## §16 · Doğrulama Test Matrisi (Sim → Prototip → Ölçüm)

| Test # | Test adı | Prototip davranışı | Doğrulama ölçütü | Sim ile ilişkisi |
|---|---|---|---|---|
| T1 | Güç-up sıfır gürültü | İlk açılış | Pop/click yok | .tran açılış sim |
| T2 | DC bias doğrulama | Sessiz sinyal DC noktalar | Bias ±%5 | .op |
| T3 | Ripple & gürültü | DC çıkış, osiloskop | Ripple ≤ tasarım | .tran ripple |
| T4 | Bant genişliği sweep | Sine sweep giriş | -3 dB bant | .ac bode |
| T5 | THD+N ölçüm | 1 kHz tam genlik | THD+N < %0.01 (hedef) | THD sim (yaklaşık) |
| T6 | Girişi PSRR ölçüm | Girişe ripple enjeksiyon | PSRR ≥ datasheet | .ac PSRR |
| T7 | Yük geçişi | Ani yük step | Overshoot ≤ %10 | .tran step |
| T8 | Termal soğuma | Uzun süre tam yük | Termal denge ≤ limit | .op/analiz termal |
| T9 | Sinyal-bozulma (intermod) | İki-tone sinyal | IMD limit | Sim intermod |
| T10 | Sönüm/roding | Impuls / step | Aşırı sönüm ≥ kritik | .tran step |

## §17 · Simülasyon Hata Kaynakları ve Kaçınma

| Hata kaynağı | Belirti | Önleme | yakalama |
|---|---|---|---|
| Singüler matris | .op başarısız | Anlamlı DC yolu, gnd referans | UBSAN analiz |
| Aşırı zaman adımı | Dalga bozulması | `maxstep` ≤ T/50 | .tran kontrol |
| model eksikliği | Yanlış THD/gürültü | Datasheet modeli | model doğrula |
| Isı unutma | Termal drift | thermal analiz + akuple model | .op thermal |
| Bağlantı hatası | Anlamsız sonuç | netlist gözden geçirme | gözden geçirme |
| Tolerans yok | Tek nokta optimum | Monte Carlo / worst-case | Monte Carlo |
| Sınır koşulu | Dangling node | Her node DC referans | netlist kontrol |
| Doğrulanmamış model | Sim-gerçek uyumsuz | Ölçümle kalibrasyon | sim→ölçüm döngüsü |

## §18 · Bilgi İddia Matrisi (Web Araştırma · 2026-10-13)

> Her satır bir dış web iddiasıdır; tek tek `HTTP 200` + envanter-dışı doğrulanmıştır. Simülasyon-flu başlıklar `⚠️ VERIFICATION REQUIRED` işaretlidir.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| ngspice el kitabı (kullanım / analiz) | https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · devamı `⚠️ VERIFICATION REQUIRED` |
| Qucs genel devre simülatörü (çoklu analiz tipi) | https://qucs.sourceforge.net/ | 2026-10-13 | HTTP 200 doğrulandı · envanter-dışı · devamı `⚠️ VERIFICATION REQUIRED` |

## §19 · Açık Sorular (CEVAP BEKLİYOR)

| Soru | Neden açık | Durum |
|---|---|---|
| hangi simülatör (ngspice/LTspice/Qucs/kişisel) | netleşmedi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| hedef prototip doğrulama platformu (hangi alet) | belirsiz | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| DC bias hedefleri (±%5 mi) | netleşmedi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| ripple/gürültü hedef sınırı | sartname PLANNED | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |
| sim→ölçüm kalibrasyon döngüsü | netleşmedi | UNKNOWN · `⚠️ VERIFICATION REQUIRED` |

## §20 · Authority · Kapanış

- Bu modül gerçek web iddiasını (§18) ve repo gerçeğini (§17) birleştirir; sim-scope Q1 (UNKNOWN) nedeniyle `status: verify-required`.
- Devre-seviyesi kod repo'da YOK (bkz. root §D): sim doğrulama koddan değil, teori + envanter + ölçüm-öncesi tasarım-dogrulama.md §6.8/§6.9 standartlarından çıkar.
- Dış iddialar tek tek HTTP 200 doğrulanmış, envanter-dışı, benzersizdir; simülatör-özel flu iddialar `⚠️ VERIFICATION REQUIRED` işaretlidir; ölçüme dayanmayan sim sonucu `verified` sayılmaz.
- Bu dosyanın yazımı ve doğrulanması: bu görev oturumunda (2026-10-13).

## §20b · Simülasyon-Öncesi Netlist Kontrol Listesi (Doğrulama Kapısı)

| # | Kontrol | Neden | Kabul |
|---|---|---|---|
| 1 | Her düğümün DC referansı var mı | singüler matris önleme | her node referanslı |
| 2 | Toprak (GND) tek ve tutarlı | ortak referans | tek GND |
| 3 | model dosyaları yüklü mü | .include/.lib | model mevcut |
| 4 | Anlamlı DC yolu (her güç dalı) | .op başarısı | DC yolu var |
| 5 | maxstep ayarlı mı (T/50) | dalga doğruluğu | step ≤ T/50 |
| 6 | Bağlantı noktaları doğru | netlist gözden geçirme | netlist doğrulandı |
| 7 | Bileşen değerleri anlamlı | R/C/L | anlamlı değer |
| 8 | Tolerans tanımlı (MC için) | saçılmayı | tol tanımlı |
| 9 | Kaynak/sınır koşulları | girdi/çıkış | sınır tanımlı |
| 10 | Ölçüm probe'ları eklendi | sonuç okuma | probe eklendi |
| 11 | Analog ground neti | gürültü/denge | agnd tanımlı (varsa) |
| 12 | Ölçüm-dogrulama.md §6.8 şablonu | DC-DC iskelet | şablon referans |
| 13 | Ölçüm-dogrulama.md §6.9 kabul | doğrulama kapıları | eşikler referans |
| 14 | Sim→ölçüm kalibrasyon planı | güvenilirlik | plan mevcut |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
