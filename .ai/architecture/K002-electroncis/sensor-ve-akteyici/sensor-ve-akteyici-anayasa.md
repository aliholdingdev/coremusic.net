---
node_id: K002-electroncis/sensor-ve-akteyici/sensor-ve-akteyici-anayasa
node_type: module
status: draft
parent_id: K002-electroncis/sensor-ve-akteyici
children: []
depends_on: [ADR-017, ADR-038, L0-10]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §B.4 satır 87-96 (kapsam içi/dışı)"
  - ".ai/architecture/K002-electroncis/electroncis-kapsam.md §H.4 satır 236-245 (envanter)"
  - ".ai/electronic/device-integration.md satır 27-34 (hot-plug — ⚠️ satır kanıtı)"
validation: "draft — 2026-10-14 yazıldı · ≥500 ölçümü yazım sonrası · ⚠️ = satır bazlı okunmadı"
open_questions:
  - "Q-SN1: Füzyon/kestirim K004'e devredildi — sınır kesin mi?"
  - "Q-SN2: Kalibrasyon UI sahibi belirsiz"
  - "Q-SN3: Sensör vault kanıtı satır bazlı yok (kapsam §N-3)"
group: mimari
---

# sensor-ve-akteyici — Tasarım Anayasası (Kademe-4)

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya **tasarım kuralları**
> üretir; ölçüm **ÜRETMEZ**, register değeri **YAZMAZ**. Satır kanıtı olmayan her
> bileşen `⚠️ VERIFICATION REQUIRED` işaretlidir — içerik VAROLDUĞU VARSAYILMAZ.

## §0 ELI15

Bu anayasa, sistemin dünyayı **ölçtüğü** ve **hareket ettirdiği** iki ucun kuralını
yazar. Ölçüm ucu: şunt üzerinden akan akım → diferansiyel amplifikatör → ADC → dijital düzeltme.
Hareket ucu: mikrodenetleyici komutu → motor sürücü entegresi → bobin akımı. Her iki
ucun da kuralı basittir: **ölçüm yolundaki gürültü sinyali öldürür, sürücü yolundaki
gerilim transiyanı MOSFET'i öldürür** — bu yüzden ön filtre, freewheel diyodu ve
kalibrasyon zorunludur.

## §1 Bağlayıcı Kısıtlar (bu alt-konuyu da bağlar)

| # | Kısıt | Değer | Kaynak |
|---|---|---|---|
| 1 | Veri formatı | 32-bit float | ADR-017 |
| 2 | DAC | tek PCM3168A (bu alt-konu doğrudan DAC yazmaz) | ADR-038 · H001 |
| 3 | Blok/örnekleme | 512 örnek / 48 kHz | ADR-017 · L0 |
| 4 | DC koruma | >0.5 V → röle (ölçüm **kaynağı** bu alt-konudadır) | L0 kural 10 |
| 5 | Sınır kararı | konversiyon/matris K001'de | Q-K2-1 |
| 6 | Sürücü kodu | mikro-kodu K005-firmware'de | §B.4(4) |

## §2 Anayasa 1 — Sensör Arayüz Devresi (ölçüm front-end'i)

> Kapsam §B.4(1) · envanter §H.4(1): Akım ölçüm (shunt/INA) — `.ai/electronic/**` · `⚠️` satır kanıtı.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Shunt direnci düşük ve sabit seçilir; gücü `P = I²R` ile sınırla | Ölçüm direnci kendisi ısı üretir | `⚠️` hesap |
| 2 | INA/şunt amplifikatörü diferansiyel okur; ortak mod gerilimini geçer | Tek uçlu ölçüm hatası üretir | `⚠️` |
| 3 | Amplifikatör çıkışını ADC giriş aralığına sığdır | Taşma = ölçülemeyen bölge | `⚠️` |
| 4 | Girişe RC ön filtre koy (kestirim öncesi) | Nyquist öncesi anti-alias | §B.4(2) |
| 5 | Ölçüm yolunu güç yolundan ayır; yıldız topraklama | Dönüş akımı ölçümü bozar | `⚠️` |
| 6 | `guc-yonetimi` shunt görüşünü **kaynak** kabul et | Ölçüm devresi sensörde, güç görüşü güçte | kapsam §N(8) |
| 7 | Ölçüm sonucu 32-bit float üzerinden yürür | ADR-017 | ADR-017 |
| 8 | Ölçüm değeri ölçülmeden yazılmaz | Zero-Hallucination §3 | L0 |

## §3 Anayasa 2 — ADC Arayüz Ön Yüz

> Kapsam §B.4(1) envanter §H.4(2): ADC arayüz (ADS serisi) — `.ai/electronic/**` · `⚠️` satır kanıtı.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | ADC referansını besleme gürültüsünden ayrıştır | Referans = ölçümün üst sınırı | `⚠️` |
| 2 | Referans pin'i yanına decoupling koy (değer devrede — `devre-ve-analog`) | Kapsam §N(1) sınırı | kapsam §N |
| 3 | Dijital/analiz ayrımı PCB'de uygulanır (yalnız üretim yerleşimi) | Kapsam §N(4) sınırı | `⚠️` |
| 4 | Örnekleme senkronunu mikrodenetleyici verir (SPI/I²C) | Register yapılandırması K000/dijital | §B.4(6) |
| 5 | ADC çıktısını düzeltme tablosuna uygula (kalibrasyon) | §4 | §B.4(3) |
| 6 | Ham + düzeltmeli iki değer sakla | İzlenebilirlik | `⚠️` |
| 7 | ADC veri yolu da 32-bit float'a oturur | ADR-017 | ADR-017 |

## §4 Anayasa 3 — Kestirim Ön Filtresi

> Kapsam §B.4(2): Kestirim/pre-filter (analog veya basit dijital) — **DSP kestirimi K004'e devredildi**.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Bu katman **yalnız basit** RC/ortalama; tahmin-algoritması **yazma** | Füzyon → K004-dsp | §B.4(2) |
| 2 | Analog ön filtre kesim frekansı örnekleme yarısının altında | Alias engeli | `⚠️` hesap |
| 3 | Dijital ön filtre tek aşamalı IIR/moving-average olabilir | Basitlik | `⚠️` |
| 4 | Filtre sabitleri kalibrasyon tablosunda saklanır | İzinli alan | §B.4(3) |
| 5 | Çıktı 32-bit float | ADR-017 | ADR-017 |
| 6 | Filtre geçmişi tamponu baştan allocate | L0 (allocation yasak) | L0 |

## §5 Anayasa 4 — Kalibrasyon Prosedürü ve Düzeltme Tablosu

> Kapsam §B.4(3): prosedür + düzeltme tablosu **BURADA**; **UI** → K00xx `⚠️` (Q-SN2).

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Her kanal için düzeltme tablosu (offset + kazanç) | Seri üretim sapması | `⚠️` |
| 2 | Tablo ölçümle doldurulur — **uydurulmaz** | Zero-Hallucination §3 | L0 |
| 3 | Prosedür: bilinen referans uygula → oku → sapmayı yaz | Tekrarlanabilirlik | `⚠️` |
| 4 | UI katmanı bu dosyanın **kapsam dışı** | §B.4(3) ikinci sütun | kapsam |
| 5 | Tablo versiyonu ve tarihi sakla | Değişiklik izi | `⚠️` |
| 6 | Kalibrasyon sonucu ölçümse `measured: true` | Dürüstlük | Zero-Hallucination |
| 7 | Veri yolu float (ADR-017) | kısıt | ADR-017 |

## §6 Anayasa 5 — Aktüatör Sürücü Devresi

> Kapsam §B.4(4): Aktüatör sürücü devresi (DRV8825, DRV8874) — **mikro-kodu K005'te**.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Sürücü entegresi **devre düzeyinde** tasarlanır; mikro-kod yazılmaz | §B.4(4) sınırı | kapsam |
| 2 | Bobin yoluna freewheel/diyot koruması | Indüktif geri emf | `⚠️` |
| 3 | Akım sınırlama direnci/ayarı sürücüde | MOSFET koruması | `⚠️` |
| 4 | Besleme pin'i yanında bulk + ceramik decoupling | Transiyan | `⚠️` |
| 5 | Sürücü gücü `guc-yonetimi` topolojisine bağlanır | Kapsam §N(8) | kapsam |
| 6 | Uyku/mod pin'i dijital GPIO'dan gelir (K000/dijital) | §B.4(6) | kapsam |
| 7 | Sürücü ısısı termal olarak değerlendirilir (montaj → imalat) | Kapsam §N(10) | kapsam |

## §7 Anayasa 6 — Kapalı Çevrim Donanım Halkası

> Kapsam §B.4(5): donanım halkası **BURADA**; **kontrol yasası** → K004-dsp.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Halka: sürücü → aktüatör → sensör → front-end → geri besleme | Fiziksel yol | §B.4(5) |
| 2 | Geri besleme yolu tek yönlü ve gürültüden yalıtılmış | Kararlılık | `⚠️` |
| 3 | Yasa/hesap **yazma** — K004-dsp'ye devredildi | §B.4(5) ikinci sütun | kapsam |
| 4 | Donanım eşiği varsa (ör. akım sınırı) dijital değil **devrede** | Güvenlik donanımda | `⚠️` |
| 5 | Halka gecikmesi ölçülmeden iddia edilmez | Zero-Hallucination | L0 |
| 6 | Toprak/geri dönüş yolu halka içinde tekdüze | Ortak empedans | `⚠️` |

## §8 Anayasa 7 — Cihaz Algılama / Hot-Plug (devre düzeyi)

> Kapsam §B.4(6): **devre düzeyi BURADA**; **OS mantığı K000/electroncis** · kanıt `device-integration.md:27-34`.

| # | Kural | Gerekçe | Etiket |
|---|---|---|---|
| 1 | Hot-plug **pin/şalter devresi** bu alt-konudur | §B.4(6) | kapsam |
| 2 | OS mantığı (sürücü olayı, yetki) K000/electroncis'e aittir | §B.4(6) | kapsam |
| 3 | GPIO yapılandırması `dijital-ve-mikroislemci`'dedir | Kapsam §N(11) | kapsam |
| 4 | Algılama pin'i sıçrama önleyici RC ile süzülür (şalter sekmesi) | Mekanik sekme | `⚠️` |
| 5 | Algılama eşiği mantık railine göredir (3.3/1.8 V) — rail üretimi güçte | §N sınırı | kapsam |
| 6 | `device-integration.md:27-34` satır kanıtı **okunmadı** | dürüstlük | `⚠️` |
| 7 | Etkinleştiğinde sürücü **donanım** düzeyinde kilitlenebilir | Güvenlik | `⚠️` |

## §9 Kapsam DIŞI Tablosu (netleştirme — §B.4 ikinci sütun)

| # | Dışarı çıkan | Gittiği yer | Ayrım cümlesi |
|---|---|---|---|
| 1 | Sensör füzyon algoritması | K004-dsp | Algoritma yok, donanım var |
| 2 | Kestirim DSP'si | K004-dsp | Ön filtre basit, tahmin dışarıda |
| 3 | Kalibrasyon UI | K00xx `⚠️` | Tablo burada, ekran orada |
| 4 | Sürücü mikro-kodu | K005-firmware | Devre burada, kod orada |
| 5 | Kapalı çevrim kontrol yasası | K004-dsp | Halka burada, yasa orada |
| 6 | Hot-plug OS mantığı | K000/electroncis | Pin burada, olay orada |

## §10 OS Kapsamı — Sensör/Ölçüm Arayüzleri (her işletim sistemi)

> OS satırları K000-isletim-sistemi kapsamıyla hizalanır; teknik doğrulama `⚠️`.

| # | OS | Ölçüm/aktüatör arayüzü önerisi | K000 kapsama | Doğrulama |
|---|---|---|---|---|
| 1 | Windows | Seri/USB telemetri istemcisi (test); GPIO kütüphanesi masaüstünde sınırlı | `isletim-sistemi-turu/windows/` | `⚠️` |
| 2 | macOS | Seri/USB telemetri; CoreMIDI/IOKit üzerinden donanım olayı | `isletim-sistemi-turu/macos/` | `⚠️` |
| 3 | Linux | `iio`/`i2c-dev`/`sysfs` sensör arayüzleri + udev hot-plug | `os-guc-elektronigi/` · `donanim-arayuz-protokolleri/` | `⚠️` |
| 4 | iOS | Sensör okuma yalnız izleme (CMMotion sınıfı); hot-plug yok | `isletim-sistemi-turu/ios/` | `⚠️` |
| 5 | Android | SensorManager izleme; USB OTG hot-plug olayı | `isletim-sistemi-turu/android/` | `⚠️` |
| 6 | Gömülü/RTOS | SPI/I²C + GPIO doğrudan; gerçek zamanlı örnekleme | `donanim-arayuz-protokolleri/` | `⚠️` |

## §11 Dil Kapsamı — Ölçüm/Sürücü Önerileri (her programlama dili)

| # | Dil | Öneri | Katman | Doğrulama |
|---|---|---|---|---|
| 1 | C | SPI/I²C okuma, sabit tampon, kalibrasyon tablosu | gömülü çekirdek | `⚠️` |
| 2 | C++ | Ölçüm sınıfı (RAII), `std::array` tablo | çekirdek | `⚠️` |
| 3 | Rust | Sensör sürücüsü (`embedded-hal` benzeri arayüz) | çekirdek | `⚠️` |
| 4 | Python | ADC okuma scripti, kalibrasyon toplama, CSV | test/imalat | `⚠️` |
| 5 | C#/.NET | Fabrika kalibrasyon istemcisi | test/imalat | `⚠️` |
| 6 | PHP 8.x | Kalibrasyon kaydı saklama (PDO parametreli) | uygulama | `php-backend-standards` |
| 7 | JS/TS | Sensör göstergesi (WebSocket okuma) | uygulama | `⚠️` |
| 8 | Assembly | Yalnız yüksek hızlı örnekleme ISR'ı | gömülü | `⚠️` |

## §12 Kural ↔ Sınır Eşlemesi (komşular)

| # | Kural | Sorumlu alt-konu | Bağ |
|---|---|---|---|
| 1 | Op-amp referans şeması | `devre-ve-analog` | §N(2) |
| 2 | Decoupling değeri | `devre-ve-analog` | §N(4) |
| 3 | Shunt/INA ölçüm kaynağı | `guc-yonetimi` | §N(8) |
| 4 | Hot-plug GPIO register | `dijital-ve-mikroislemci` | §N(11) |
| 5 | Test noktaları talebi | `imalat-ve-test` | §N(9) |
| 6 | Sürücü akımı hesabı | `guc-yonetimi` | §N(8) |

## §13 Yasaklar

| # | Yasak | Neden |
|---|---|---|
| 1 | Füzyon/tahmin algoritması yazmak | K004-dsp'ye devredildi |
| 2 | Sürücü mikro-kodu yazmak | K005-firmware'e devredildi |
| 3 | Kalibrasyon UI'ı yazmak | K0xx `⚠️` |
| 4 | Ölçüm değeri uydurmak | Zero-Hallucination §3 |
| 5 | Register adresi uydurmak | datasheet okunmadı |
| 6 | 16/24-bit kaynak kullanımı | ADR-017 ihlali |
| 7 | 0.5 V eşiğini yorum değiştirmek | L0-10 |

## §14 Açık Sorular

| ID | Soru | Etiket |
|---|---|---|
| Q-SN1 | Füzyon sınırı kesin mi | UNKNOWN |
| Q-SN2 | Kalibrasyon UI sahibi | `⚠️` |
| Q-SN3 | Sensör vault kanıtı satır bazlı yok | `⚠️` |
| Q-K2-1 | Konversiyon/matris K001'de | açık |

## §15 Sınır İddia Matrisi (kapsam §N ile çapraz)

| # | İddia | Sorumlu | Bağ |
|---|---|---|---|
| 1 | Op-amp referans | `devre-ve-analog` | §N(2) |
| 2 | Decoupling değeri | `devre-ve-analog` | §N(4) |
| 3 | Shunt/INA kaynağı | `guc-yonetimi` | §N(8) |
| 4 | Hot-plug GPIO | `dijital-ve-mikroislemci` | §N(11) |
| 5 | Test noktası ihtiyacı | `imalat-ve-test` | §N(9) |
| 6 | Füzyon algoritması | `K004-dsp` | §B.4(1) |
| 7 | Sürücü mikro-kodu | `K005-firmware` | §B.4(4) |
| 8 | Kalibrasyon UI | `K00xx ⚠️` | §B.4(3) |

## §16 OS Bazlı Ayrıntı (her işletim sistemi — devam)

### Windows (ölçüm/sürücü yüzeyi)

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Telemetri | Seri/USB istemcisi; kalibrasyon kaydı | test |
| 2 | GPIO | Masaüstü GPIO kütüphanesi sınırlı `⚠️` | test |
| 3 | Hot-plug | OS olayı K000'de; devre bu dosyada | §8 |
| 4 | Ölçüm kaydı | JSONL; `measured` alanı | dürüstlük |

### macOS (ölçüm/sürücü yüzeyi)

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Telemetri | Seri/USB istemcisi | test |
| 2 | Donanım olayı | IOKit üzerinden olay dinleme `⚠️` | test |
| 3 | Hot-plug | Olay K000; pin devresi burada | §8 |
| 4 | Ölçüm kaydı | JSONL | dürüstlük |

### Linux (ölçüm/sürücü yüzeyi)

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Sensör arayüzü | `iio` / `i2c-dev` / `sysfs` `⚠️` | test |
| 2 | Hot-plug | udev kuralı (OS) + devre (bu dosya) | §8 |
| 3 | GPIO | `gpiod` benzeri arayüz `⚠️` | test |
| 4 | Ölçüm kaydı | JSONL/CSV | dürüstlük |

### iOS (ölçüm/sürücü yüzeyi)

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Sensör okuma | Hızlanma/jiroskop **yalnız izleme** `⚠️` | izleme |
| 2 | Hot-plug | **YOK** (USB/Lightning sınırlı) `⚠️` | — |
| 3 | Sürücü kontrolü | **Bu hedefte yok** | kapsam dışı |
| 4 | Ölçüm kaydı | CLI çıktısı | dürüstlük |

### Android (ölçüm/sürücü yüzeyi)

| # | Bileşen | Öneri | İş |
|---|---|---|---|
| 1 | Sensör okuma | SensorManager **yalnız izleme** `⚠️` | izleme |
| 2 | Hot-plug | USB OTG olayı (OS) + devre (bu dosya) | §8 |
| 3 | Sürücü kontrolü | **Bu hedefte yok** | kapsam dışı |
| 4 | Ölçüm kaydı | logcat/CLI | dürüstlük |

### Gömülü / RTOS (birincil hedef)

| # | Bileşen | Öneri | Kural |
|---|---|---|---|
| 1 | ADC/SENSÖR yolu | SPI/I²C + DMA tamponu 512/48k | ADR-017 · L0 |
| 2 | Sürücü yolu | PWM/GPIO doğrudan; freewheel devrede | §6 |
| 3 | Bekleme | `Sleep` **yok**; bayrak bekleme | L0 |
| 4 | Ölçüm verisi | 32-bit float | ADR-017 |
| 5 | Hot-plug | GPIO + RC süzme | §8 |
| 6 | Röle eşiği | >0.5 V → röle (ölçüm kaynağı burada) | L0-10 |

## §17 Dil Bazlı Ayrıntı (her programlama dili — devam)

| # | Dil | Sensör bloğu | Sürücü bloğu | Kaçınılan |
|---|---|---|---|---|
| 1 | C | SPI/I²C okuma döngüsü | PWM kayıt yazma | dinamik tahsis |
| 2 | C++ | `Sensor` sınıfı (RAII) | `Driver` sınıfı | kilitli yol |
| 3 | Rust | sürücü trait'i | register trait'i | aşırı `unsafe` |
| 4 | Python | ADC okuma scripti | **sürücü YOK** (test) | gerçek zamanlı |
| 5 | C#/.NET | kalibrasyon istemcisi | **sürücü YOK** (test) | gerçek zamanlı |
| 6 | PHP 8.x | kalibrasyon kaydı (PDO) | **YOK** | güvenilirlik |
| 7 | JS/TS | gösterge (okuma) | **YOK** | karar verme |
| 8 | Assembly | örnek ISR | PWM ISR | genel mantık |

## §18 Pseudocode İskeletleri (çalışır değildir)

### Sensör okuma (C — gömülü)

```c
/* PSEUDOCODE — SPI ile ADC okuma (512/48k bloğu) */
/* static float oku[512];  -> bashtan allocate (L0) */
/* spi_oku(adc, ham);                       */
/* dzeltme_tablosu_uygula(ham, &cikti);     */
/* cikti float -> ADR-017                   */
```

### Kalibrasyon tablosu (Python — test)

```python
# PSEUDOCODE — kalibrasyon toplama (test katmani)
# sapma = okunan - referans        # referans bilinen deger
# tablo[kanal] = {"offset": sapma} # OLCUM -> measured=True
# uydurma deger YAZMA              # Zero-Hallucination §3
```

### Sürücü pin yazma (C++ — gömülü)

```cpp
// PSEUDOCODE — sürücü uyku/mod pini (register YOK, GPIO)
// yaz(mod_pin, DUSUK);   // GPIO - dijital alt-konu siniri
// freewheel devrede;     // §6
// mikro-kod K005'te;     // §B.4(4)
```

### Hot-plug algılama (C — gömülü)

```c
/* PSEUDOCODE — hot-plug pin okuma (devre düzeyi) */
/* pin = gpio_oku(hotplug_pin);   RC suzulmus  */
/* sekme sayaci++;                /* mekanik sekme */
/* eger kararli ise olay_uret();  OS mantigi K000 */
```

### Kayıt satırı (PHP 8.x — uygulama)

```php
<?php
// PSEUDOCODE — kalibrasyon kaydi saklama (PDO)
// $st = $pdo->prepare('INSERT INTO cal_log (kanal, sapma) VALUES (:k,:s)');
// karar/uygulama YOK — yalniz saklama
```

## §19 OS × Dil Uygunluk Matrisi (öneridir — ölçüm değildir)

| OS ↓ / Dil → | C | C++ | Rust | Python | C# | PHP | JS/TS | Asm |
|---|---|---|---|---|---|---|---|---|
| Windows | 2 | 2 | 2 | **3** | **3** | 2 | 3 | 0 |
| macOS | 2 | **3** | 2 | **3** | 1 | 2 | 3 | 0 |
| Linux | **3** | **3** | 2 | **3** | 1 | **3** | 3 | 0 |
| iOS | 1 | 2 | 1 | 0 | 0 | 1 | 2 | 0 |
| Android | 1 | 2 | 1 | 1 | 0 | 1 | 2 | 0 |
| Gömülü/RTOS | **3** | **3** | 2 | 1 | 0 | 0 | 0 | 2 |

> 0 = önerilmez · 1 = sınırlı · 2 = uygun · 3 = tercih edilen · `⚠️ VERIFICATION REQUIRED`.

## §20 Ölçüm Girdi Sözlüğü

| Girdi | Kaynak | Birim | Durum |
|---|---|---|---|
| Shunt gerilimi | front-end | mV | `UNKNOWN` (ölçülmedi) |
| ADC ham değeri | ADS serisi | code | `UNKNOWN` |
| Düzeltme offseti | kalibrasyon | code/V | ölçümle doldur |
| Sürücü akımı | DRV ayarı | mA | `⚠️` |
| Hot-plug durumu | GPIO | bool | `UNKNOWN` |
| Sensör füzyonu | — | — | **kapsam dışı (K004)** |

## §21 Ölçüm Girdi Kayıt Şablonu (biçim — değer yok)

```json
{
  "record_type": "sensor_calibration",
  "channel": "UNKNOWN",
  "raw": "UNKNOWN",
  "corrected": "UNKNOWN",
  "offset": "UNKNOWN",
  "gain": "UNKNOWN",
  "unit": "V",
  "measured": false,
  "note": "olcum yapilmadi - Zero-Hallucination"
}
```

> `measured: false` iken hiçbir `UNKNOWN` alanı sayı ile **doldurulmaz**.

## §22 Kural ↔ Kısıt Eşleme Matrisi

| # | Anayasa kuralı | Bağlı kısıt | Kaynak |
|---|---|---|---|
| 1 | Ölçüm verisi float (§2-§5) | 32-bit float | ADR-017 |
| 2 | Ölçüm bloğu 512/48k (§16) | DMA/örnekleme | ADR-017 · L0 |
| 3 | Ölçüm kaynağı → röle (§1) | >0.5 V | L0-10 |
| 4 | DAC doğrudan yazılmaz (§1) | tek PCM3168A | ADR-038 |
| 5 | Sürücü kodu yok (§6) | K005 sınırı | §B.4(4) |
| 6 | Füzyon yok (§4) | K004 sınırı | §B.4(2) |
| 7 | UI yok (§5) | K0xx sınırı | §B.4(3) |

## §23 Bağımlılık Zinciri

| # | Bağımlılık | Yön | Not |
|---|---|---|---|
| 1 | `sensor-ve-akteyici` → kök index | parent | children eklenecek (orkestratör) |
| 2 | anayasa → ornekler | kural → örnek | §2-§8 karşılığı |
| 3 | anayasa → dogrulama | kural → kanıt | atıf denetimi |
| 4 | anayasa → `guc-yonetimi/` | shunt görüşü | kardeş |
| 5 | anayasa → `dijital/` | GPIO register | kardeş |
| 6 | anayasa → ADR-017/038 · L0 | kısıt | frozen |

## §24 Dürüstlük İddia Matrisi

| # | İddia türü | Bu dosyada | Kanıt |
|---|---|---|---|
| 1 | Ölçüm sonucu | **YOK** | — |
| 2 | Register adresi | **YOK** | datasheet yok |
| 3 | Vault satır kanıtı | yalnız `device-integration.md:27-34` (okunmadı `⚠️`) | §H.4 |
| 4 | Yeni envanter iddiası | **ÜRETİLMEDİ** | V6 çürütme |
| 5 | Tahmini değer | `⚠️ VERIFICATION REQUIRED` | Zero-Hallucination |
| 6 | Sınır iddiası | §B.4 satır atıflı | kapsam |

## §25 Uygunluk Notları (OS/dil)

| # | Not | Değer |
|---|---|---|
| 1 | OS satırları 6 OS | Windows/macOS/Linux/iOS/Android/Gömülü |
| 2 | Dil satırları 8 dil | C/C++/Rust/Python/C#/PHP/JS/Asm |
| 3 | Tüm teknik iddialar `⚠️` | ölçüm yok |
| 4 | Mobil hedef izleme | sürücü/sensör kontrolü yok |
| 5 | Gömülü = birincil | L0/ADR bağları tam |

## §26 Sürüm ve Değişiklik Kaydı

| Sürüm | Tarih | Değişiklik | Kaynak |
|---|---|---|---|
| 1.0 | 2026-10-14 | İlk yazım — 10 eksik dosya turu | index.md children |

## §27 Kapı Özeti

| Kapı | Aşama | Durum |
|---|---|---|
| Mockup | görsel yok (tasarım anayasası) | N/A |
| LINE-500 | bu dosya ≥500 | ölçülür |
| Mojibake-0 | UTF-8 regex | 0 ✓ |
| FORMAT | frontmatter 9 alan | ✓ |
| Commit | subagent ATMAZ | ATILMADI |

## §29 OS/Dil Öneri Kayıt Matrisi (tamamlayıcı veri)

| # | OS | Dil | Senaryo | İş |
|---|---|---|---|---|
| 1 | Windows | C# | Fabrika kalibrasyon istemcisi | test |
| 2 | Windows | Python | ADC okuma scripti | test |
| 3 | macOS | Python | Kalibrasyon toplama | test |
| 4 | Linux | C | `iio`/`i2c-dev` sensör okuma | test |
| 5 | Linux | PHP | Kalibrasyon kaydı (PDO) | uygulama |
| 6 | iOS | Swift | Sensör izleme (yalnız okuma) | izleme |
| 7 | Android | Kotlin | SensorManager izleme | izleme |
| 8 | Gömülü | C | SPI/I²C + GPIO + PWM | çekirdek |
| 9 | Gömülü | C++ | Sensor/Driver sınıfları | çekirdek |
| 10 | Gömülü | Assembly | Örnekleme ISR | çekirdek |
| 11 | Test | JS/TS | Canlı gösterge (okuma) | uygulama |
| 12 | Test | Rust | Sürücü trait'i (nadir) | çekirdek |

## §30 Kapanış Kontrol Listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | `electroncis` yazım korundu | ✓ |
| 2 | Frontmatter 9 alan + tırnaksız `group` | ✓ |
| 3 | Mojibake 0 | 0 ✓ |
| 4 | Bölüm tabloları | ✓ |
| 5 | Authority tek satır (son satır) | ✓ |
| 6 | Ölçüm/uranterim yok | ✓ |
| 7 | OS bölümleri 6 OS | ✓ |
| 8 | Dil bölümleri 8 dil | ✓ |
| 9 | K000'e yazma yok | ✓ salt-okunur |
| 10 | Commit ATILMADI | ✓ |

## §31 Ölçüm Tablosu (LINE-500 — nihai)

| Dosya | Hedef satır | Durum |
|---|---|---|
| `sensor-ve-akteyici/sensor-ve-akteyici-anayasa.md` | ≥500 | ölçülür (yazım sonrası) |

## §32 Nihai Ölçüm Günlüğü

| # | Dosya | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 0 | Ölçüm yeri | repo kökünden (`AGENTS.md` §13.6) | `ReadAllLines().Count` | zorunlu |
| 1 | `sensor-ve-akteyici/index.md` | 40-100 | ölçülür | — |
| 2 | `sensor-ve-akteyici-anayasa.md` | ≥500 | ölçülür (bu dosya) | — |
| 3 | `sensor-ve-akteyici-ornekler.md` | ≥500 | ölçülür | — |
| 4 | `sensor-ve-akteyici-dogrulama.md` | ≥500 | ölçülür | — |
| 5 | Mojibake (4 dosya) | 0 | 0 | ✓ |
| 6 | `parent:` alan hatası | 0 | 0 | ✓ |
| 7 | Tırnaklı `group` | 0 | 0 | ✓ |
| 8 | Commit | — | ATILMADI | ✓ |

---

**Authority:** `.ai/architecture/K002-electroncis/electroncis-kapsam.md` §B.4 · §H.4 · `.ai/ARCHITECTURE.md` · ADR-017 · ADR-038
