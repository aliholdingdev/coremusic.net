---
node_id: K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa
node_type: module
status: draft
parent_id: K002-electroncis/devre-ve-analog
children: []
depends_on:
  - K004-dsp
  - K001-donanım
evidence:
  - ".ai/electronic/amplifier/class-ab.md satır 21-22, 38-60"
  - ".ai/electronic/amplifier/class-d.md satır 42-47"
  - ".ai/electronic/frequency-response.md satır 50-56"
  - ".ai/electronic/snr-thd-measurement.md satır 29, 45-48"
validation: "draft — 2026-10-13 yazıldı · ≥500 ölçümü yazım sonrası · 2026-10-14 · K000 kaynaklı taşıma eklendi (bkz §T-K000)"
open_questions:
  - "Q-DVA1: filtre tasarım sınırı K004-dsp → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# devre-ve-analog-anayasa — Analog Devre Anayasası

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya K002'nin ilk
> alt-konu anayasasıdır: bileşen seçimi, sinyal zinciri, filtre, ölçüm kapıları.

## §1 ELI15

Analog devre, sesin **gerilim ve akım olarak dolaştığı** yerdir. Burada üç soru
yanıtlanır: hangi bileşen hangi değerle (seçim), sinyal hangi sırayla geçer
(zincir), ve ölçüm masasında hangi rakam tutmalıdır (kapı). Dijital işlem K004,
güç topolojisi `guc-yonetimi`, üretim `imalat-ve-test` — bu dosya yalnızca
**sinyalin kendisini** sahiplenir.

## §2 Kapsam

| # | Kapsam İÇİ | Kapsam DIŞI (gittiği yer) |
|---|---|---|
| 1 | Pasif bileşenler (R, C, L) ve tolerans | Satın alma envanteri → K001-donanım |
| 2 | Op-amp seçimi, kazanç, gürültü | DSP algoritması → K004-dsp |
| 3 | Class AB / Class D güç aşaması şeması | Güç topolojisi ayrıntısı → guc-yonetimi |
| 4 | Filtre devreleri (Sallen-Key, LC) | Dijital filtre katsayıları → K004-dsp |
| 5 | Osilatör / PLL analog yüzeyi | Saat register programlama → dijital-ve-mikroislemci |
| 6 | ADC/DAC ön zinciri (referans, anti-aliasing) | Register map kodu → K005-firmware |
| 7 | Ölçüm eşikleri (tasarım tarafı) | Üretim testi uygulaması → imalat-ve-test |
| 8 | DC offset algılama (0.5V tetik) | Röle sürme güç devresi → guc-yonetimi |

## §3 Bileşen Seçimi

### §3.1 Direnç

| # | Kriter | Ses yolu | Güç yolu | Not |
|---|---|---|---|---|
| 1 | Tolerans | %1 metal film | %1-2 | TCR bak |
| 2 | Gürültü | Düşük gürültü (eloksal değil) | Orta | Johnson gürültü |
| 3 | Parazit | Serbest endüktans düşük | Orta | Yüksek frekansta SRF |
| 4 | Güç | 1/4W yeterli | Hesapla (P = I²R) | Derating %50 |
| 5 | Paket | 0603/0805 SMD | 1206/2512 | Termal |

### §3.2 Kondansatör

| # | Teknoloji | Avantaj | Dezavantaj | Kullanım |
|---|---|---|---|---|
| 1 | C0G/NP0 | Sıfır piezo, stabil | Düşük yoğunluk | Filtre, osilatör, referans |
| 2 | X7R | Yüksek yoğunluk | DC bias kaybı | Decoupling, bulk |
| 3 | X5R | Ucuz | Sıcaklık kayması | Düşük hassasiyet |
| 4 | Elektrolitik | 10,000µF'e kadar | ESR, ömür | Smoothing |
| 5 | Tantal | Düşük ESR | Ateşleme modu | RF decoupling |
| 6 | Film | Düşük gürültü | Hacim | Ses yolu, ölçüm |

**Kanıt (vault):** `pcb-design.md` satır 44-52 — her IC pinine 100nF + bulk 10µF
decoupling kuralı; MLCC DC bias kaybı riski (R5, master §7).

### §3.3 Bobin / Ferrit

| # | Kriter | Eşik | Not |
|---|---|---|---|
| 1 | SRF | Çalışma frekansının üstünde | Rezonans üstü = kapasitif |
| 2 | Doygunluk akımı | Akım ≤ Idsat | Güç yolunda kritik |
| 3 | DCR | Düşük | Kayıp ve ısınma |
| 4 | Ekran | Toroid tercih | Sızıntı minimal |

### §3.4 Op-amp Seçim Kriterleri

| # | Parametre | Ses Yolu Hedefi | Örnek Bileşen (TI HTTP 200) | Kullanım |
|---|---|---|---|---|
| 1 | Gürültü | <10nV/√Hz | OPA1611, OPA1656 | Giriş tamponu |
| 2 | THD+N | <0.001% | LM4562, OPA2134 | Amfi öncesi |
| 3 | GBW | ≥10× gereken bant | OPA1642 (JFET) | Yüksek kazanç |
| 4 | Slew rate | ≥10V/µs | TL074 (JFET) | Büyük sinyal |
| 5 | Offset | <1mV (precision) | OPA2277, OP07 | DC yolu |
| 6 | Rail-to-rail | Gerekiyorsa | LM324 (düşük maliyet) | Jenerik |
| 7 | Fiyat/erişim | — | hepsi TI datasheet 200 | dogrulama §5 |

**Kaynak:** TI datasheet URL'leri `devre-ve-analog-dogrulama.md` §5'te HTTP 200
ile doğrulandı (2026-10-13); envanter kesişimi 0.

## §4 Sinyal Zinciri (vault kanıtlı)

### §4.1 Class AB güç aşaması (class-ab.md:38-60)

| # | Aşama | İşlev | Vault kanıtı |
|---|---|---|---|
| 1 | Giriş | RCA/XLR, 47kΩ empedans | class-ab.md:42 |
| 2 | Differential pair | CMRR yüksek, gürültü düşük | class-ab.md:40 |
| 3 | VAS | Gerilim yükselteç | class-ab.md:44 |
| 4 | Driver | Darlington/bootstrap | class-ab.md:47 |
| 5 | Push-pull output | NPN+PNP tamamlayıcı | class-ab.md:50 |
| 6 | Geri besleme | Negatif feedback → girişi | class-ab.md:57 |
| 7 | Koruma | DC offset rölesi (0.5V) | protection.md:37 |

**Hedef:** 100W @ 8Ω, THD+N <0.01% (class-ab.md:21-22).

### §4.2 Class D güç aşaması (class-d.md:42-47)

| # | Aşama | İşlev | Eşik |
|---|---|---|---|
| 1 | PWM komparatör | Analog → PWM | Anahtarlama ≥400kHz |
| 2 | Güç MOSFET çifti | Anahtarlama | PVDD 18-53.5V |
| 3 | LC çıkış filtresi | 50kHz pasif alçak geçiren | damping >200 @8Ω |
| 4 | Geri besleme | Yük duyarsızlık | class-d.md:47 |
| 5 | Verim | — | >90% (class-d.md:42) |

### §4.3 DAC ön zinciri

| # | Blok | İşlev | Kural |
|---|---|---|---|
| 1 | I2S alıcı | Dijital veri | `dijital-ve-mikroislemci` sınırı |
| 2 | DAC çip | D→A | **Yalnız PCM3168A (ADR-038 H001)** |
| 3 | Diferansiyel → tekil | Op-amp sürücü | THS4521 (TI 200) |
| 4 | Anti-aliasing / tembel filtre | Nyquist öncesi | 2. derece, 20kHz üstü |
| 5 | Referans | Gürültü/PSRR | C0G + LDO (guc-yonetimi) |

**YASAK:** PCM5122 — ADR-038 H001 (kural 6). Bu alt-konuda her DAC referansı
PCM3168A'dır.

## §5 Filtre Tasarımı

| # | Topoloji | Kullanım | Avantaj | Not |
|---|---|---|---|---|
| 1 | Sallen-Key | 2. derece LC eşdeğeri | Op-amp 1 adet | Q kontrolü kolay |
| 2 | Multiple feedback | Nyquist filtresi | Tersinir | Yüksek Q'da duyarlı |
| 3 | Bessel | Faz doğrusal | Grup gecikmesi düz | Ses yolu tercih |
| 4 | Butterworth | Düz bant içi | En dip | Ölçüm |
| 5 | LC pasif (Class D) | Çıkış filtresi | Kayıpsız | damping >200 |
| 6 | Değişken EQ | Tone control | Aktif | K004 sınırı (Q-DVA1) |

**Sınır:** Bu alt-konuda filtre DEVRESİ (topoloji + bileşen değeri) anlatılır;
dijital EQ katsayıları `K004-dsp`'dedir (Q-DVA1 açık).

## §6 Ölçüm Kapıları (tasarım tarafı)

| # | Parametre | Hedef | Vault kanıtı | Durum |
|---|---|---|---|---|
| 1 | SNR (A-wtd) | >100dB | snr-thd-measurement.md:29 | Ölçüm PLANNED |
| 2 | THD+N @1kHz 1W | <0.01% | snr-thd-measurement.md:45 | Ölçüm PLANNED |
| 3 | THD+N @1kHz 10W | <0.001% | snr-thd-measurement.md:46 | Ölçüm PLANNED |
| 4 | Noise floor | <-100dB | snr-thd-measurement.md:48 | Ölçüm PLANNED |
| 5 | Frekans yanıt | 20Hz-20kHz ±0.5dB | frequency-response.md:50-56 | Ölçüm PLANNED |
| 6 | Damping factor | >200 @8Ω | class-d.md:47 | Tasarım |
| 7 | Çıkış gücü | 100W @8Ω | class-ab.md:21-22 | Tasarım |
| 8 | DC offset çıkış | <0.5V (röle eşiği) | protection.md:37 | Zorunlu (kural 10) |

## §7 Tasarım Kuralları

| # | Kural | Değer | Kaynak |
|---|---|---|---|
| 1 | DAC çipi | Yalnız PCM3168A | ADR-038 H001 |
| 2 | PCM5122 | ABSOLUTELY FORBIDDEN | Kural 6 |
| 3 | Ses veri tipi (dijital yol) | 32-bit float | ADR-017 |
| 4 | Buffer/örnekleme | 512 örnek, 48kHz | Sartname (ölçüm PLANNED) |
| 5 | DC offset koruma | >0.5V → röle | Kural 10 |
| 6 | Analog trace genişliği | 10mil | pcb-design.md:44-52 |
| 7 | I2S/clock trace | 8mil | pcb-design.md:44-52 |
| 8 | Decoupling | 100nF + 10µF | pcb-design.md:44-52 |
| 9 | Gürültü ölçümü | A-wtd | snr-thd-measurement.md:29 |

## §8 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Op-amp gürültüsü SNR'ı düşürür | Yüksek | Orta | <10nV/√Hz seçimi |
| 2 | MLCC DC bias kaybı — filtre kayması | Orta | Orta | C0G kritik noktalar |
| 3 | Class D çıkış filtresi rezonansı | Orta | Düşük | damping >200 |
| 4 | Referans gürültüsü → THD | Yüksek | Düşük | C0G + LDO (guc) |
| 5 | Anti-aliasing yok → aliasing | Yüksek | Düşük | 2. derece filtre |
| 6 | PCM5122 kullanımı | Kritik | Düşük | ADR-038 denetimi |
| 7 | Analog/dijital plane karışımı | Orta | Orta | Tek nokta birleşim |
| 8 | Bias kayması → crossover distorsiyonu | Yüksek | Orta | Sınıf-A bias tasarımı |

## §9 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | SOA | Safe Operating Area — transistör güvenli çalışma alanı |
| 2 | GBW | Gain-Bandwidth Product — kazanç-bant genişliği |
| 3 | CMRR | Common Mode Rejection Ratio — ortak mod bastırma |
| 4 | ESR | Equivalent Series Resistance — eşdeğer seri direnç |
| 5 | SRF | Self-Resonant Frequency — kendi rezonansı |
| 6 | PSRR | Power Supply Rejection Ratio — besleme reddetme |
| 7 | THD+N | Total Harmonic Distortion + Noise |
| 8 | SNR | Signal-to-Noise Ratio |
| 9 | VAS | Voltage Amplifier Stage |
| 10 | Push-pull | NPN+PNP tamamlayıcı çıkış |
| 11 | Bias | Çalışma noktası akımı |
| 12 | Crossover distorsiyonu | Sınıf-A/B geçiş bozulması |
| 13 | Slew rate | Maksimum gerilim değişim hızı |
| 14 | Aliasing | Nyquist örtüşmesi — sahte frekans |
| 15 | Bessel/Butterworth | Filtre karakteri (faz/ düz bant) |
| 16 | Sallen-Key | Yaygın aktif filtre topolojisi |
| 17 | Damping factor | Yük duyarsızlık (Zout/Zload) |
| 18 | A-wtd | A-weighting — gürültü ağırlık eğrisi |
| 19 | Johnson gürültü | Direnç termal gürültüsü |
| 20 | Decoupling | IC pin kısa mesafe kondansatörü |

## §10 Kapsam Dışı

| # | Konu | Sahip |
|---|---|---|
| 1 | DSP algoritması / EQ katsayıları | K004-dsp |
| 2 | Register map / I2S init kodu | K005-firmware |
| 3 | OS sürücü / ses API | K000-isletim-sistemi |
| 4 | Fiziksel parça envanteri | K001-donanım |
| 5 | PCB üretim/reflow | imalat-ve-test |
| 6 | Boost/LDO topolojisi ayrıntısı | guc-yonetimi |
| 7 | Sensör Dünya arayüzü | sensor-ve-akteyici |

## §11 Kaynakça

| # | Kaynak | Satır | Tür |
|---|---|---|---|
| 1 | `.ai/electronic/amplifier/class-ab.md` | 21-22, 38-60 | Vault (CP1254) |
| 2 | `.ai/electronic/amplifier/class-d.md` | 42-47 | Vault |
| 3 | `.ai/electronic/frequency-response.md` | 50-56 | Vault |
| 4 | `.ai/electronic/snr-thd-measurement.md` | 29, 45-48, 55-59 | Vault |
| 5 | `.ai/electronic/hardware/pcb-design.md` | 44-52 | Vault |
| 6 | `.ai/electronic/amplifier/protection.md` | 37-53 | Vault |
| 7 | TI datasheet/AN | 12 URL | HTTP 200 (2026-10-13) |
| 8 | ADR-038 · ADR-017 | — | Frozen |

## §12 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `devre-ve-analog-ornekler.md` §2 | Dönüşüm örnekleri |
| 2 | `devre-ve-analog-dogrulama.md` §5 | Web doğrulama |
| 3 | `electroncis-master.md` §3.2 | Sinyal zinciri özeti |
| 4 | `guc-yonetimi/*` | Güç aşaması kardeş |
| 5 | `dijital-ve-mikroislemci/*` | Saat/I2S sınırı |
| 6 | `imalat-ve-test/*` | Ölçüm uygulaması |

## §13 Op-Amp Karşılaştırma Matrisi (seçim tablosu)

| Parametre | OPA1611 | OPA1656 | LM4562 | OPA2134 | TL074 | LM324 | OPA1642 |
|---|---|---|---|---|---|---|---|
| Kanal başına maliyet kademesi | yüksek | yüksek | orta-yüksek | orta | düşük | düşük | orta |
| Gürültü (tipik, dense/kHz kademesi) | çok düşük | çok düşük | çok düşük | düşük | orta | yüksek | düşük |
| Bozulma karakteri | düşük | çok düşük | çok düşük | düşük | orta | yüksek | düşük |
| Slew rate kademesi | yüksek | çok yüksek | yüksek | orta | orta | düşük | orta |
| Gerilim besleme aralığı | orta | orta | orta | orta | orta | geniş (tek kaynak dahil) | orta |
| Çıkış akımı kademesi | yüksek | yüksek | yüksek | orta | orta | düşük | orta |
| Rail-to-rail çıkış | hayır | hayır | hayır | hayır | hayır | kısma (RL tipik sınırlı) | hayır |
| Önerilen katman | preamp / kritik gain | preamp / kritik gain | preamp / dengeleyici | line sürücü | genel amaç | DC / yardımcı | filtre / buffer |
| K002 kullanıldığı yer | K002-eleman-secimi | K002-eleman-secimi | K002-eleman-secimi | K002-eleman-secimi | K002-eleman-secimi | K002-eleman-secimi | K002-eleman-secimi |

**Okuma kuralı:** bu matris **kademeli** (çok düşük / düşük / orta / yüksek) karşılaştırır — sayısal değer
**vermez**. Sayısal değer üretmek için üreticinin güncel datasheet'i şarttır; datasheet elde edilmemiş
durumda sayı **uydurulmaz**. Nihai bileşen seçimi K002-anayasa'nın kapısıdır: seçime gerekçe ve
kaynak yazılır, "herkes kullanıyor" gerekçe sayılmaz.

**Tek kanal / çok kanal ayrımı:** TL074 ve LM324 dörderli paketlerdir — dört bağımsız amplifikatörü
aynı gövdede taşır. Bir fazör/dengeleme ağı dört OPERASYONERSEL katman gerektiriyorsa dört ayrı
tekli yerine bir dörtlü paket, hem alan hem maliyet hem BOM satırı kazandırır; ancak kanallar
arası sızma (crosstalk) ve ortak ısı damlası (thermal drag) paylaşılmış olur. Kritik ses yolunda
tekli paket, yardımcı DC yolunda çoklu paket ayrımı bu nedenle yapılır.

**Ders notu (topoloji değil, bileşen davranışı):** gerçek dünyada "ideal op-amp" yoktur. Giriş
offset gerilimi, bias akımı, sınırlı bant genişliği, sınırlı slew rate ve sınırlı çıkış akımı
HEP birden etkindir; yüksek frekansta yükseltilmiş bir gerçek karta bileşen idealleşirse
kararlılık kaybedilir. TL07x ailesi FET girişlidir (bias akımı çok düşüktür) ama LM324
bipolar girişlidir (bias akımı belirgin ve sıcaklıkla değişir) — DC kopleajlı bir kademede
içinde giriş bias dirençleri dengelenmeden kullanılırsa çıkışta ölçülebilir DC kayması oluşur.

---

## §14 Filtre Topolojileri (adım adım)

| # | Topoloji | Sıra | Tipik kullanım | Kritik nokta |
|---|---|---|---|---|
| 1 | Buffer (voltage follower) | 1 | empedans ayrıştırma | gain=1, kararlılık en yüksek |
| 2 | Non-inverting gain | 1 | preamp kadesi | gain = 1 + Rf/Rg |
| 3 | Inverting gain | 1 | ters fazlı kade | gain = -Rf/Rin, giriş empedansı ≈ Rin |
| 4 | Sallen-Key LPF | 2 | 2. derece alçak geçiren | Q ve kesim frekansı birbirine bağımlı |
| 5 | Multiple-Feedback (MFB) | 2 | 2. derece bant/düşük | notch karakteri daha keskin, gürültü duyarlı |
| 6 | Ayrık bileşen RLC | 0 (pasif) | yüksek akım / HF | empedans eşleme şart |
| 7 | Biquad (aktiv + buffer) | 3+ | eşik/çok bantlı EQ | kademeli kopyalama, hata birikir |

**Kademeli (cascaded) korelasyon uyarısı:** iki 2. derece kadeyi arka arkaya koyarak 4. derece
elde edersiniz; ancak kademeler **birbirinden bağımsız değildir** — ilk kadenin çıkış empedansı
ikinci kadenin girişini yükler. Bu nedenle her kade arasına **buffer** (gain=1 op-amp) konur.
Buffer yoksa kese tasarımında hesaplanan kesim frekansı gerçekte kayar ve kurtarma (Q) tepkisi
bozulur. Bu, "kağıt üzerinde çalışan filtre gerçek kartta neden ölmüyor" sorusunun bir numaralı
cevabıdır.

**Kurtarma (Q) ve fazör:** Q yükseldikçe kesim yakınında kazanç tepe yapar (kurtarma), faz kayması
artar. Ses uygulamasında tepe yapan bir filtre "daha net duruyor" hissi verir ama grup gecikmesi
(group delay) de artar — fazör düzeltmesi yapılmayan bir sistemde bu, stereo görüntünün
derinliğini bozar. Düz (Butterworth) tepki faz doğruluğu için; tepe/kurtarma istisnai olarak ve
gerekçeli seçilir.

---

## §15 Gain Planı (kazanç planı)

| Aşama | Görev | Gain kademesi | Dikkat |
|---|---|---|---|
| 1 | Giriş tamponu | 1 (buffer) | empedans eşleme, kablo gürültüsü |
| 2 | Preamp / MC-MM kade | değişken | en düşük gürültü burada kazanılmalı |
| 3 | Filtre kademesi | 0 dB (düz) veya Q'lu | gain planını bozmamalı |
| 4 | Ana kazanç / potansiyometre | değişken | potans sesli/çatlaklı olmamalı (ilmikli film) |
| 5 | Çıkış sürücü | 1..2 | hat empedansı ~100 Ω kademesi |
| 6 | Koruma / DC kesme | 0 dB | AC kopleaj, röle/ektin |

**Altın kural: kazanç en erken kademede, en düşük gürültüyle kazanılır.** Girişte yeterince
kazanç yoksa sonraki her kade kendi gürültüsünü ekler ve toplam gürültü figure'sü (noise figure)
bozulur; girişte fazla kazanç varsa ilk kade taşar (clipping) ve kazanılamaz. Bu iki uç arasındaki
denge **gain planı**dır ve tasarımın ilk sayfasında yazılır.

**Taşma (clipping) hesabı:** her kadenin maksimum çıkış genliği, bir sonraki kadenin maksimum
giriş genliğinden **büyük olmamalı**, aksi halde zincirde bir yerde tıkanır. K002-master §14'teki
eşikler burada uygulanır: TASARIM içinde -1 dBFS headroom bırakılır; ölçüm kapısında 0 dBFS'e
kadar çıkılabilir (bu iki farklı kapıdır, karıştırılmaz).

**Potansiyometre yeri:** ana kazanç potansı filtre kademesinin **öncesinde** konursa potans
direnç değeri sinyal yoluna seri girer ve filtre empedans dengesini bozar; **arkasında**
konursa filtre sabit yük altında çalışır. Filtre + potans birlikte kullanılıyorsa potans
çıkışı yüksek empedanslı bir tampona alınır.

---

## §16 Güç Pürdüzelliği ve Referans Gerilim

| # | Kaynak | Belirti | Tasarım karşılığı |
|---|---|---|---|
| 1 | Ana hat (AC-mains) ve switcher gürültüsü | sürekli hışırtı, 100/120 Hz kök | NC filtre + topraklama düzeni (guc-yonetimi) |
| 2 | 50/60 Hz repek | iyi hizalanmamış kabloda benek | kablo rotası + twisted çift |
| 3 | Op-amp PSRR sınırlaması | hattan gelen gürültü yükselterek geçer | yerel LDO + bypass |
| 4 | Toprak halkası (ground loop) | sistem çalışınca oluşan vızıltı | tek nokta topraklama / Balanced |
| 5 | Referans gerilimi kayması | DC offset drift | şerit referans / bandgap + iyi bypass |
| 6 | Tek kaynak vs split kaynağı | tek kaynakta kertme (headroom) düşer | split ± veya rail-to-rail plan |

**Yerel bypass (decoupling) kuralı:** her op-amp'ın güç pinlerine, gövdeye **en yakın** noktaya
100 nF seramik konur. "En yakın" sözcüğü fizikseldir — pin'e 2-3 mm'den uzak konan bir 100 nF,
yerine göre hiç işe yaramaz. Yanına büyük değerli elektrolitik (µF kademesi) konur ama o büyük
kondansatör yüksek frekansta işe yaramaz; ikisi farklı iş yapar, biri diğerinin yerine geçemez.

**Topraklama filozofu (analog tarafı):** analog ve dijital aynı karttaysa toprak **tek noktada**
birleşir, hiçbir yerde paralelleşmez. İki noktadan bağlanmış toprak bir halka (loop) oluşturur;
halka anten gibi çalışır ve güç hattındaki manyetik alan bu halkadan akım indükler — çalışır
durumdaki sistemde duyulan vızıltının kök nedeni budur. Şematoda toprak sembolünü "her yerde
serbest" sanmak en yaygın hatadır.

---

## §17 Koruma ve DC Sınır

| # | Tehlike | Sonuç | Önlem (K002 kapsamı) |
|---|---|---|---|
| 1 | Çıkışta DC bileşeni | hoparlör bobini ısınır/ses | AC kopleaj + DC koruma |
| 2 | K002 90° (AD004) >0.5 V DC | röle şart (zorunlu) | koruma rölesi / DC algılama |
| 3 | Bağlarken kıvılcım (hot plug) | pop, potansiyometre hasarı | mühürleme (make-before-break) tasarımı |
| 4 | Aşırı akım / kısa devre | amp hasarı | sigorta / akım sınırlama / termal |
| 5 | ESD | kalıcı hasar | TVS / dizin koruma / kademeli topraklama |

**DC çift kontrol:** (1) kaynak tarafında DC'nin üretilmemesi, (2) çıkışta AC kopleaj ile
kesilmesi, (3) algılama devresi ile hissedilip rölenin açılması. Üçü birden uygulanır çünkü
hiçbiri tek başına her arıza modunu kapsamaz. AD004 (0.5 V) eşiği donanım kısıtıdır — K002
tarafında karşılanmazsa sistem **hiçbir koşulda** çalıştırılamaz (K002-bagimliliklar §13).

**Hot-plug (bağlarken) tasarımı:** fiş takılırken toprak ucu genelde ilk bağlanır, sinyal
uçları sonrada — ama kesilirken tam tersi sırayla olur ve bu sırada kısa bir asimetri/doğrusal
olmayan temas oluşur. Çıkışında DC kesme kondansatörü + girişinde empedans olan bir sürücüde
bu, tek seferlik bir tıkırtı olarak duyulur ve zararsızdır; DC kesmesiz bir sistemde ise
hoparlöre enerji transferi olur. Tasarım bu sıralamayı **bilerek** hesaba katar.

---

## §18 Uygulama Sırası (adım adım checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | Gereksinimi sayıya çevir (kazanç, bant, empedans) | şartname tablosu | Q-DVA1 |
| 2 | Topoloji seç (buffer / gain / filtre) | sinyal zinciri şeması | K002-master §2 |
| 3 | Bileşen seç + gerekçeleştir | BOM (kaynaklı) | §13 matrisi |
| 4 | Gain planı yaz | aşama kazançları | §15 |
| 5 | Topraklama ve bypass yerleşimi | şema notları | §16 |
| 6 | Koruma zinciri | DC + akım + ESD | §17 |
| 7 | Simülasyon (varsa) | tepki eğrileri | Q-DVA2 |
| 8 | PCB / breadboard | prototip | imalat-ve-test |
| 9 | Ölçüm (SNR, THD+N, bant) | ölçüm raporu | dogrulama §6 |
| 10 | Sapma varsa kök neden | düzeltme kaydı | §8 riskler |

**Adım 7 uyarısı (dürüstlük):** K002 altında SPICE modeli veya simülasyon çıktısı **diskte
yoktur** — sahte eğri, sahte .raw çıktısı veya "simülasyon onaylı" ifadesi **üretilmez**.
Simülasyon adımı ancak gerçek bir araç ve gerçek bir çalıştırma kanıtı eklendiğinde tamamlanmış
sayılır; o zamana kadar bu satır `⚠️ VERIFICATION REQUIRED` statüsündedir.

---

## §19 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Bypass kondansatörünün pin'den uzak olması | sebebi belirsiz gürültü | 100 nF gövdeye en fazla 3 mm, tek nokta toprak |
| 2 | Toprağı iki yerden bağlamak | çalışır durumda vızıltı | tek nokta birleşim |
| 3 | Filtre kademelerini buffersız kopyalamak | hesap-gerçek frekans sapması | araya buffer |
| 4 | Girişte kazancı atlamak | tüm sistem gürültülü | en erken kademede kazanç |
| 5 | Bias direnci dengelenmeden bipolar op-amp | çıkışta DC kayması | giriş dirençlerini eşitle |
| 6 | Rail-to-rail sanılan TL07x/OPA1xx | uçta kırpılma | çıkış kertmesini hesaba kat |
| 7 | DC kesme olmadan hoparlöre bağlamak | bobin ısınması | AC kopleaj + DC algılama |
| 8 | Datasheetsiz "herkes kullanıyor" seçimi | üretimde sapma | kaynaklı BOM |
| 9 | Ölçümde DC bileşenini yok saymak | THD+N yanlış | DC + AC ayrı ölç |
| 10 | K002'nin K000 işini yaptığını sanmak | OS seviyesi risk | K000'a delege et |

**Hata 1 ayrıntısı:** toprak yolunun ortak empedansı (common impedance) yüzünden iki kade
aynı toprak izinden akım paylaşırsa birinin akımı diğerinin referansını oynatır — bu,
"kanallar birbirini neden konuşturuyor" sorusunun cevabıdır. Toprak izi bir **yol**dır,
**sabit potansiyel değildir**.

---

## §20 Terim Ekleri (İngilizce ↔ Türkçe)

| İngilizce | Türkçe | Kısa tanım |
|---|---|---|
| voltage follower | gerilim takipçisi | gain=1 buffer |
| inverting / non-inverting | ters / doğru kutuplu | giriş bağlantısı yönü |
| gain-bandwidth product | kazanç-bant genişliği ürünü | GBW, kade kazancı ile bant çarpımı sabit |
| slew rate | hızlanma oranı | çıkışın max değişim hızı |
| power supply rejection ratio | besleme reddetme oranı | PSRR |
| offset voltage | offset gerilimi | idealde sıfır olması gereken DC |
| bias current | polarizasyon akımı | girişten akan DC akım |
| common mode | ortak mod | iki girişteki ortak sinyal |
| crosstalk | kanallar arası sızma | paket içi/bağlantı kaynaklı |
| headroom | kertme | taşmadan önceki boşluk |
| clipping | kırpılma | kertme aşımında düzleşme |
| ripple | dalgalanma | DC'de kalan AC bileşen |
| decoupling | ayrıştırma/bypass | yerel enerji deposu |
| ground loop | toprak halkası | çift bağlantılı toprak |
| hot swap / hot plug | takarken çalıştırma | bağlantı anı olayları |
| slew-induced distortion | hız kaynaklı bozulma | yüksek frekansta SR sınırlaması |
| THD+N | toplam harmonik + gürültü | bozulma ölçümü |
| dBFS | dB (tam ölçekli) | dijital referans |
| line-level | hat seviyesi | ekipmanlar arası standart seviye |
| balanced / unbalanced | dengeli / dengesiz | XLR vs RCA/topraklı |

---

## §21 Doğrulama ve Kanıt Bağlantısı

| # | Bu dosyadaki iddia | Kanıt tipi | Gittiği dosya | Seviye |
|---|---|---|---|---|
| 1 | 4 aşamalı sinyal zinciri | vault satırı | `devre-ve-analog-ornekler.md` §1.4 | S4 |
| 2 | THD+N <0.01% @ 1 kHz | eşik (kabul) | `{alt}-dogrulama.md` §4.1 | S5 |
| 3 | SNR >100 dB | eşik (kabul) | `{alt}-dogrulama.md` §4.1 | S5 |
| 4 | Bant 20 Hz–20 kHz ±0.5 dB | vault satırı | `{alt}-dogrulama.md` §4.1 | S4 |
| 5 | DC offset 0.5 V röle tetiği | L0 kuralı | `{alt}-dogrulama.md` §4.1 | S5 |
| 6 | PCM3168A zorunlu / PCM5122 yasak | ADR-038 H001 | `{alt}-dogrulama.md` §4.1 | S5 |
| 7 | 32-bit float veri biçimi | ADR-017 | `{alt}-dogrulama.md` §4.1 | S5 |
| 8 | 512 örnek / 48 kHz | sartname | `{alt}-dogrulama.md` §1.4 | S5 |
| 9 | Repo'da elektronik kodu YOK | disk ölçümü | `{alt}-dogrulama.md` §6.1 | S1 |
| 10 | 12 URL HTTP 200 | HEAD | `{alt}-dogrulama.md` §5.1 | S3 |
| 11 | 60 dosya / 18.242 satır | disk ölçümü | `{alt}-dogrulama.md` §3.2 | S1 |
| 12 | `electroncis` yazım dokunulmazlığı | v2 §3 KURAL 1 | bu dosya §0 | S5 |

### §21.1 Bu dosyanın web kaynakları (benzersiz — kesişim 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| TI OPA1678 veri sayfası | https://www.ti.com/lit/ds/symlink/opa1678.pdf | 2026-10-13 | HTTP 200 |
| TI NE5532 veri sayfası | https://www.ti.com/lit/ds/symlink/ne5532.pdf | 2026-10-13 | HTTP 200 |
| TI TLV9062 veri sayfası | https://www.ti.com/lit/ds/symlink/tlv9062.pdf | 2026-10-13 | HTTP 200 |
| TI THS4551 veri sayfası | https://www.ti.com/lit/ds/symlink/ths4551.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu sboa126 | https://www.ti.com/lit/an/sboa126/sboa126.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu sboa154 | https://www.ti.com/lit/an/sboa154/sboa154.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu sboa242 | https://www.ti.com/lit/an/sboa242/sboa242.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu slyt113 | https://www.ti.com/lit/an/slyt113/slyt113.pdf | 2026-10-13 | HTTP 200 |

### §21.2 Açık soru → kanıt zinciri

| # | Soru | Bu turda ne kanıtlandı | Kalan kanıt |
|---|---|---|---|
| 1 | Q-DVA1 filtre ↔ K004 sınırı | sınır çizildi (§7) | belge/üst karar |
| 2 | Q-DVA2 DC offset sahibi | L0 kuralı 10 okundu | kardeş karar |
| 3 | Q-DVA3 filtre topoloji satırları | okunmadı | satır okuma |
| 4 | Q-DVA-O1 çıktı formatı | tanımlanmadı | karar |
| 5 | Q-DVA-O2 C++ kod yüzeyi | repo'da 0 | K005/L0 |

---

## §22 Değişiklik Geçmişi

| # | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 1 | 2026-10-13 | İlk yazım (§1-§13) | Vault Steward |
| 2 | 2026-10-13 | §21 doğrulama bağlantısı + ≥500 derinleştirme | Vault Steward |
| 3 | — | Q-DVA1..3 açık — üst karar/ajan bekliyor | BEKLEMEDE |

---

## §T-K000 — K000 kaynaklı taşıma (devre-tasarımı · analog · 2026-10-14)

> Bu bölüm, silinmekte olan K000 (`K000-isletim-sistemi/electroncis/devre-tasarımı/`) benzersiz
> içeriğinin taşınmasıyla oluşturulmuştur. İçerik kaynaktan **birebir** kopyalanır (imla korunur);
> frontmatter alanları (node_id · depends_on · evidence · open_questions · wikilink) kopyalanmaz.
> Her blok kaynak § ve satır aralığını taşır; `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` / `PLANNED`
> işaretleri aynen korunur.

### §T-K000.1 Worst-case tasarım metodolojisi (tolerans · derating · güvenlik payı)

> Kaynak: `devre-tasarimi-anayasa.md` §10 (satır 246-281). Amaç: her bileşemin **en kötü durumda**
> (tolerans · sıcaklık · yaşlanma · üretim saçılımı) hâlâ güvenli çalışmasını garantilemek. Sayısal
> değer uydurulmaz; yalnız **hesap yöntemi** taşınır.

**Derating kuralları (§10.1)**

| # | Büyüklük | Kural (tipik mühendislik) | Uygulama |
|---|---|---|---|
| 1 | Kapasitör gerilimi | nominal ≥ 2× çalışır gerilim (elektrolit) | güç filtresi |
| 2 | Kapasitör gerilimi (seramik) | DC bias derating (X5R/X7R) dikkate alınır | decoupling |
| 3 | Direnç gücü | P ≤ ½ · P_nom | sıcaklık |
| 4 | Transistör Vceo/Vds | ≤ ½ · maks | amplifikatör/switcher |
| 5 | Sıcaklık | T_j ≤ T_jmax − 20 °C pay | termal |

**Tolerans bütçesi (§10.2)**

| # | Kaynak | Tipik etki | Toplama yöntemi |
|---|---|---|---|
| 1 | Direnç tolerans (%1/%5) | kazanç sapması | RSS (kök-toplam-kare) veya worst-case |
| 2 | Kapasitör tolerans (%10/%20) | fc sapması | worst-case (filtre) |
| 3 | Op-amp offset/IB | DC sapma | worst-case |
| 4 | ADC/DAC DNL/INL | bozulma | datasheet |
| 5 | Sıcaklık katsayısı (ppm/°C) | drift | RSS + sıcaklık aralığı |

**Güvenlik payı hesabı (§10.3)**

| # | Adım | İşlem | Çıktı |
|---|---|---|---|
| 1 | Nominal değer | tasarım noktası | V_nom · I_nom · T_nom |
| 2 | Tolerans aralığı | ±tolerans uygula | V_min/max · I_min/max |
| 3 | Sıcaklık | katsayıyla genişlet | T aralığı |
| 4 | Worst-case | en kötü kombinasyon | sınır değer |
| 5 | Pay | sınır − nominal | güvenlik payı |
| 6 | Karar | pay > 0 → geç | PASS/FAIL |

### §T-K000.2 Kararlılık · geri besleme · kompanzasyon (PM ≥ 45°)

> Kaynak: `devre-tasarimi-anayasa.md` §11 (satır 283-301).

| # | Kavram | Tanım | Hata belirtisi | Düzeltme |
|---|---|---|---|---|
| 1 | Açık kazanç (Aol) | geri besleme olmadan kazanç | — | — |
| 2 | Geri besleme faktörü (β) | çıktının girdiye oranı | — | — |
| 3 | Kazanç marjı (GM) | faz −180° iken kazanç payı | osilasyon | kompanzasyon |
| 4 | Faz marjı (PM) | kazanç 0 dB iken faz payı | overshoot/ringing | Miller kapasitör |
| 5 | Unity-gain bant (GBW) | kazanç=1 frekansı | bant daralması | doğru op-amp |
| 6 | Slew rate | maksimum çıkış değişim hızı | distorsiyon (yüksek f) | SR seçimi |

**Kararlılık doğrulama adımları (§11.1)**

| # | Adım | Simülasyon | Beklenen |
|---|---|---|---|
| 1 | AC analiz | faz/kazanç grafiği | PM ≥ 45° (hedef) |
| 2 | Transient | adım/impuls yanıtı | overshoot sınırlı |
| 3 | Worst-case | tolerans + sıcaklık | PM hâlâ pozitif |
| 4 | Yük varyasyonu | kapasitif yük | kararlılık korunur |

### §T-K000.3 Gürültü analizi ve bütçesi (√4kTRB · Friis)

> Kaynak: `analog-ve-dijital-devre-temelleri.md` §4 (satır 181-200) + §10 (satır 325-346).

**Gürültü kaynakları (§4.1)**

| # | Kaynak | Tür | Karakteristik | Azaltma |
|---|---|---|---|---|
| 1 | Direnç (Johnson-Nyquist) | termal | k √(4kTRB) | düşük R · düşük B |
| 2 | Transistör (shot) | akım | ∝√I | akım optimizasyonu |
| 3 | Flicker (1/f) | düşük-f | pink noise | düşük-1/f bileşen · lock-in |
| 4 | Op-amp giriş gürültü | en · In | nV/√Hz · pA/√Hz | doğru op-amp · düşük-Z |
| 5 | Güç kaynağı gürültü | ripple/PSRR | anahtarlama gürültüsü | regülasyon · PSRR |

**Gürültü hesabı (§4.2)**

| # | Büyükllük | Bağıntı | Not |
|---|---|---|---|
| 1 | Toplam gürültü gerilimi | en,total = en·√(B·n) | n bileşen · B bant |
| 2 | Direnç gürültü | vn = √(4kTRB) | k = Boltzmann |
| 3 | Gürültü faktörü (NF) | NF = 10·log(F) | cascaded NF |
| 4 | Cascaded (Friis) | Ftot = F1 + (F2−1)/G1 + ... | ilk aşama kritik |

**Gürültü bütçesi metodolojisi (§10) — sayısal değer YOK**

| # | Adım | İşlem | Çıktı |
|---|---|---|---|
| 1 | Zincir tanımı | aşama listesi (giriş→çıkış) | blok şema |
| 2 | Her aşama gürültüsü | en (nV/√Hz) · kazanç | aşama gürültüsü |
| 3 | Friis uygulama | Ftot = F1 + (F2−1)/G1 + ... | toplam NF |
| 4 | Bant genişliği | gürültü bant (B) | en·√B |
| 5 | Sinyal seviyesi | referans sinyal | SNR hesabı |
| 6 | Eşik karşılaştırma | SNR ≥ hedef | PASS/FAIL |

**Gürültü azaltma stratejileri (§10.1)**

| # | Strateji | Etki | Maliyet |
|---|---|---|---|
| 1 | İlk aşama düşük-gürültü | dominanter (Friis) | maliyet |
| 2 | Bant genişliğini daralt | √B doğrusal | gecikme |
| 3 | Düşük-empedans zincir | Johnson gürültü azalır | akım |
| 4 | Lock-in / chopper | 1/f kaçınma | karmaşıklık |
| 5 | Ekranlama · twisted pair | EMI azalır | PCB/kablo |

### §T-K000.4 Bileşen seçim ve değer tabloları

> Kaynak: `devre-tasarimi-anayasa.md` §15 (satır 340-386) + `analog-ve-dijital-devre-temelleri.md` §7 (satır 241-263). Spesifik marka/model seçimi ilgili K-node'larındır; sayısal değer uydurulmaz, yalnız **seçim ölçütü** verilir.

**Direnç seçimi ölçütleri (§15.1)**

| # | Ölçüt | Neden önemli | Seçim kuralı |
|---|---|---|---|
| 1 | Tolerans (%) | kazanç/fc doğruluğu | kritik: %1 · genel: %5 |
| 2 | Sıcaklık katsayısı (ppm/°C) | drift | kritik: ≤100 ppm |
| 3 | Güç (W) | ısınma | ≥2× hesaplanan güç |
| 4 | Gürültü (Johnson) | ses yolu | düşük-değer · metal-film |
| 5 | Voltage katsayısı | gerilim altı direnç kayması | yüksek-gerilim yerine yüksek-katsayı |

**Kapasitör seçimi ölçütleri (§15.2)**

| # | Tip | Uygulama | Kural | Dikkat |
|---|---|---|---|---|
| 1 | C0G/NP0 | filtre · timing · audio kritik | tolerans %1–5 · düşük kayıp | pahalı, küçük değer |
| 2 | X7R | decoupling · bypass | DC bias derating | değer düşüşü |
| 3 | X5R | decoupling | ekonomik | sıcaklık |
| 4 | Elektrolit (Al) | güç filtresi | ≥2× gerilim · ESR | ripple akım · ömür |
| 5 | Tantal | düşük ESR · kompakt | gerilim derating | ateşlenme riski |
| 6 | Film | ses yolu · yüksek kalite | düşük kayıp · düşük gürültü | boyut |

**Yarı iletken seçimi ölçütleri (§15.3)**

| # | Tür | Uygulama | Ölçüt |
|---|---|---|---|
| 1 | BJT | ses · analog | gürültü · hFE · eşleşme |
| 2 | MOSFET | anahtarlama · Class-D | Rds(on) · Vgs(th) · Qg |
| 3 | Op-amp | filtre · kazanç | GBW · SR · offset · gürültü |
| 4 | Regülatör | güç | PSRR · dropout · gürültü |
| 5 | Koruma (eFuse/TVS) | koruma | eşik · tepki · kapasite |

**Entegre seçimi (ses yolü — PCM3168A referans, §15.4)**

| # | Rol | Tipik bileşen (CoreMusic) | Seçim ölçütü |
|---|---|---|---|
| 1 | DAC | PCM3168A · AK4458 | kanal · THD+N · SNR · I2S/TDM |
| 2 | ADC | ⚠️ UNKNOWN (PLANNED) | fs · bit · SNR |
| 3 | Amplifikatör | Class-AB/D | güç · verim · bozulma |
| 4 | Koruma | eFuse · TVS · termistör | eşik · tepki |

> **Dürüstlük:** PCM3168A/CoreMusic donanımı **ADR-038 H001** ile sabittir; ADC seçimi henüz
> belgelenmemiştir → `⚠️ VERIFICATION REQUIRED` / `PLANNED`.

**Direnç tipleri (analog §7.1)**

| # | Tip | Tolerans | TCR (ppm/°C) | Gürültü | Uygulama |
|---|---|---|---|---|---|
| 1 | Karbon kompozit | %5–20 | yüksek | yüksek | genel · düşük öncelik |
| 2 | Metal film | %1–2 | 25–100 | düşük | ses · ölçüm |
| 3 | Thin film | %0.1–1 | 5–25 | çok düşük | kritik filtre · ADC referans |
| 4 | Wirewound | %1–5 | 10–50 | orta | güç · yüksek akım |
| 5 | Shunt (akım ölçüm) | %0.5–1 | düşük | düşük | akım algılama |

**Kapasitör dielektrik karşılaştırması (analog §7.2)**

| # | Dielektrik | Sembol | Tolerans | TCC | Dip/geri dönüş | Uygulama |
|---|---|---|---|---|---|---|
| 1 | Sınıf 1 seramik | C0G/NP0 | %0.5–5 | 0±30 ppm/°C | yok | ses · timing · filtre |
| 2 | Sınıf 2 seramik | X7R | %10–20 | ±15% | geri dönüş | decoupling |
| 3 | Sınıf 2 seramik | X5R | %10–20 | ±15% | geri dönüş | decoupling (ekonomik) |
| 4 | Polyester film | — | %5 | orta | düşük | ses · genel |
| 5 | Polypropylene film | — | %1–5 | çok düşük | çok düşük | yüksek-Q filtre · ölçüm |
| 6 | Al elektrolit | — | %20 | yüksek | yüksek | güç filtresi |
| 7 | Tantal | — | %5–20 | orta | düşük | kompakt · düşük ESR |

### §T-K000.5 Tasarım kapı (gate) · red davranışı · RACI

> Kaynak: `devre-tasarimi-anayasa.md` §18 (satır 427-465). Her aşama geçmeden sonrakine başlanmaz
> (WORKFLOW §8.9 hard-gate mantığı). Bu liste **tasarım-anı** kapılarını taşır; üretim kapıları K001'dedir.

**Aşama kapıları (§18.1)**

| # | Kapı | Geçme kiteriyumu | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Gereksinim | ölçülebilir hedef (THD · SNR · fs) | şartname satırı | PLANNED |
| 2 | Mimari | topoloji gerekçeli | mimari notu | PLANNED |
| 3 | Bileşen | worst-case derating geçti | hesap tablosu | PLANNED |
| 4 | Şema | netlist incelemesi | inceleme kaydı | PLANNED |
| 5 | Sim (AC/DC/trans) | PM ≥ 45° · DC bias nominal | sim raporu | PLANNED |
| 6 | Monte Carlo | verim yüzdesi ≥ hedef | dağılım raporu | PLANNED |
| 7 | PCB girişi | şema donduruldu | teslim paketi | PLANNED |
| 8 | Ölçüm | THD/SNR/FREP eşiği geçti | ölçüm raporu | PLANNED |

**Kapı reddi davranışları (§18.2)**

| # | Red nedeni | Aksiyon | Sahip |
|---|---|---|---|
| 1 | PM < 45° | kompanzasyon → tekrar sim | bu alt-dal |
| 2 | Worst-case pay ≤ 0 | bileşen/tolerans revizyonu | bu alt-dal |
| 3 | Verim < hedef | Monte Carlo → tolerans daralt | bu alt-dal |
| 4 | THD/SNR eşiği tutmadı | zincir revizyonu → tekrar ölç | bu alt-dal + K001 |
| 5 | PCB kısıtı çelişkisi | K001 ile müzakere | K001 |

**Roller ve sorumluluk (RACI — tasarım-anı, §18.3)**

| # | Rol | R/A/C/I | Not |
|---|---|---|---|
| 1 | Devre tasarımı | R | şema · sim · ölçüm planı |
| 2 | `guc-yonetimi/` | C | güç gereksinimi |
| 3 | K001-donanım | C (PCB girişi) | teslim alır |
| 4 | K004-dsp | C (fs/bit gereksinimi) | dijital tasarım |
| 5 | K005-firmware | C (eşik/register) | firmware |
| 6 | Vault Steward (Q1) | A (kapsam) | kapsam kararı |
| 7 | QA/ölçüm | C | ölçüm doğrulama |

### §T-K000.6 Ölçüm büyüklükleri ve birim dönüşümleri (hızlı referans)

> Kaynak: `devre-tasarimi-anayasa.md` §16 (satır 388-410) + §19 (satır 467-492).

**Ölçüm büyüklükleri (§16)**

| # | Büyüklük | Tanım | Birim | Neden |
|---|---|---|---|---|
| 1 | THD | toplam bozulma (harmonik) | % · dB | ses saflığı |
| 2 | THD+N | bozulma + gürültü | % · dB | gerçek performans |
| 3 | SNR | sinyal/gürültü oranı | dB | dinamik aralık |
| 4 | DNR | dinamik aralık | dB | en sessiz–en yüksek |
| 5 | FREP | frekans tepkisi | dB vs Hz | düz bant |
| 6 | DNL/INL | ADC/DAC doğrusallık | LSB | çözünürlük |
| 7 | Crosstalk | kanallar arası sızma | dB | kanal izolasyonu |
| 8 | PSRR | güç reddetme oranı | dB | güç gürültüsü |

**Ölçüm koşulları (tipik şartname, §16.1)**

| # | Koşul | Değer | Not |
|---|---|---|---|
| 1 | Referans frekans | 1 kHz | THD/SNR standardı |
| 2 | Örnekleme hızı | 48 kHz | sartname |
| 3 | Bit derinliği | 24-bit | ADR-017 (32-float iç · 24 dış) |
| 4 | Kanal | 2 (stereo) · 8.1 (çevre) | sartname |
| 5 | Yükleme | nominal | yük regülasyonu |
| 6 | Sıcaklık | oda (25 °C) · sıcak | drift ölçümü |

**Hızlı referans — birimler ve semboller (§19)**

| # | Sembol | Büyükllük | Birim | Not |
|---|---|---|---|---|
| 1 | V | gerilim | V · mV · µV | — |
| 2 | I | akım | A · mA · µA | — |
| 3 | R | direnç | Ω · kΩ · MΩ | — |
| 4 | C | kapasitans | F · µF · nF · pF | — |
| 5 | L | indüktans | H · mH · µH | — |
| 6 | f | frekans | Hz · kHz · MHz | — |
| 7 | fc | kesim frekansı | Hz | filtre |
| 8 | fs | örnekleme hızı | Hz | ADC/DAC |
| 9 | Z | empedans | Ω | — |
| 10 | dB | kazanç/oran | dB | 20·log(V) · 10·log(P) |

**Sık kullanılan dönüşümler (§19.1)**

| # | Dönüşüm | Bağıntı | Not |
|---|---|---|---|
| 1 | Voltaj kazancı → dB | dB = 20·log10(Av) | Av > 0 |
| 2 | Güç oranı → dB | dB = 10·log10(P2/P1) | — |
| 3 | dB → voltaj oranı | Av = 10^(dB/20) | — |
| 4 | RC kesim frekansı | fc = 1/(2π·R·C) | 1. katman LPF |
| 5 | LC kesim frekansı | fc = 1/(2π·√(LC)) | 2. katman |
| 6 | Nyquist | fs > 2·fmax | anti-alias |
| 7 | Q ↔ bant genişlik | Q = fc / BW | filtre |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
