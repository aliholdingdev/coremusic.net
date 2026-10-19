---
node_id: K005-firmware/audio-control/audio-control-dogrulama
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/audio-control
evidence:
  - K005-firmware/audio-control/index.md
  - K005-firmware/audio-control/audio-control-kapsam.md
  - K005-firmware/boot/boot-dogrulama.md
validation: []
open_questions:
  - "THD+N ölçüm set-up'ı ve referans jeneratör seçimi bilinmiyor (UNKNOWN)"
  - "8.1 kanal eşzamanlı ölçüm donanımı (çoklu ADC/Zaman analizörü) erişilebilirliği UNKNOWN"
group: kanit
---

# Audio-Control Alt-Modülü — Doğrulama (K005-firmware/audio-control/audio-control-dogrulama.md)

> Bu dosya, `K005-firmware/audio-control/` alt-modülünün **doğrulama** dokümanıdır.
> Tüm eşikler sartname.md ve ADR'lerden alınmıştır; repo'da firmware kodu
> bulunmadığından (2026-10-13 keşif) ölçüm metodolojisi donanım/standart
> referanslıdır; kesin dosya/satır referansı verilmez.

## §A Doğrulama Senaryoları

| # | Senaryo | Giriş | Beklenen | Kritiklik |
|---|---|---|---|---|
| AV-1 | Tampon boyutu/örnekleme doğrulama | Sabit yapılandırma | 512 örnek, 48 kHz (10,67 ms) | Kritik |
| AV-2 | 32-bit float hat bütünlüğü | Bilinen sinüs (−20 dBFS) | Hattın tamamında float, dönüşüm kaybı yok (ADR-017) | Kritik |
| AV-3 | SNR ölçümü | 1 kHz @ −20 dBFS | SNR > 100 dB | Kritik |
| AV-4 | THD+N ölçümü | 1 kHz sinusoidal | THD+N < %0,01 | Kritik |
| AV-5 | DC offset eşiği | DC kaydırmalı sinyal (0,6 V karşılığı) | Röle ≤ 1 tampon (10,67 ms) içinde tetiklenir | Kritik |
| AV-6 | DC offset normal aralık | 0,1 V karşılığı | Röle tetiklenmez, sayaç artmaz | Yüksek |
| AV-7 | DMA underrun tespiti | Kasıtlı yetersiz tampon | Underrun sayacı artar, telemetri olayı üretilir | Yüksek |
| AV-8 | DMA overrun tespiti | Kasıtlı aşırı yazma | Overrun sayacı artar | Yüksek |
| AV-9 | ASIO Exclusive Lock ihlali | İkinci uygulama kilit denemesi | İhlal tespit edilir, durum raporu | Yüksek |
| AV-10 | Boot → hazır geçen süre | Sıfırdan açılış | ≤ 1200 ms (boot B-13 köprüsü) | Orta |
| AV-11 | DSP zinciri geçiş etkisi | Beyaz gürültü + EQ bypass/açık | Frekans tepkisi farkı ölçülebilir, clip yok | Orta |
| AV-12 | Kompresör eşiği | Aşırı seviye girişi | Gain reduction uygulanır, distorsiyon artmaz | Orta |
| AV-13 | Kanal ayrışma (crosstalk) | Tek kanallı sinyal | Komşu kanal sızıntısı < −80 dB (eşik belirsiz → UNKNOWN) | Orta |
| AV-14 | TDM slot eşlemesi | Her kanala tek tek sinyal | Doğru kanalda duyulur (eşleme doğrulanır) | Yüksek |
| AV-15 | Röle tetik sonrası kurtarma | DC ihlali temizlenince | Röle bırakma + oturum geri yüklenir (belirsiz → UNKNOWN) | Yüksek |

## §B Test Stratejisi

### §B.1 Birim testleri

| Parça | Araç | Kapsam |
|---|---|---|
| DSP blokları (EQ/kompresör/gain) | Unity/ceedling (boot-dogrulama.md §B.1 köprüsü) | coefficient, gain math |
| DC izleyici eşik mantığı | Unity/ceedling | eşik, histeresis |
| Tampon yöneticisi | statik analiz + birim | ping-pong, doluluk |
| sayaçlar (underrun/overrun) | birim | atomik artış |

### §B.2 Entegrasyon testleri

| Katman | Yöntem | Not |
|---|---|---|
| I2S/TDM + DMA | Renode veya gerçek MCU + SEGGER J-Link | Veri yolu bütünlüğü |
| DAC register yapılandırması | I2C log/telemetri | device-control üzerinden |
| Röle tetik zinciri | power-control sahte/simüle çıkış | ≤ 1 tampon gecikme |
| Boot → hazır | boot-dogrulama.md B-13 köprüsü | ≤ 1200 ms |

### §B.3 Donanım testleri (ölçüm)

| Ölçüm | Ekipman (taslak) | Not |
|---|---|---|
| SNR/THD+N | Audio analizör / osiloskop + spektrum | 1 kHz referans |
| DC offset | DMM / ADC | Eşik 0,5 V |
| Crosstalk | Çoklu kanal kayıt | Eşik belirsiz → UNKNOWN |
| Zamanlama (röle gecikmesi) | Zaman işaretçisi / GPIO lazer | ≤ 10,67 ms |

### §B.4 Test ortamı notu

- Renode/Zephyr native_sim yolu boot-dogrulama.md §B.2'de tanımlandı; audio-control için DMA/I2S modelleri sınırlı olabilir (belirsiz → UNKNOWN).
- Gerçek ölçüm PCM3168A + analog ön yük üzerinden yapılır (`.ai/electronics/`).
- Ölçüm betikleri repo'da YOK (derlenebilir kaynak yok) → betikler taslak olarak audio-control-ornekler.md §B.6'da verilmiştir.

## §C Ölçüm Metodolojisi

### §C.1 SNR ölçümü

1. 1 kHz sinus @ −20 dBFS uygula.
2. Çıkışı kaydet (32-bit float).
3. Sinyal + gürültü gücünü ve gürültü gücünü hesapla.
4. SNR = 10·log10(P_sinyal / P_gürültü).
5. Kabul: SNR > 100 dB.

### §C.2 THD+N ölçümü

1. 1 kHz sinus @ −1 dBFS uygula.
2. Temel harmonik dışındaki tüm enerjiyi (gürültü + harmonikler) ölç.
3. THD+N = 100 · (V_toplam_harmonik_gürültü / V_temel).
4. Kabul: < %0,01.

### §C.3 DC offset ölçümü

1. Sessiz girişi uygula (sinyal yok).
2. Çıkış DC bileşenini ölç (uzun süre ortalaması).
3. Karşılık gelen voltajı hesapla.
4. Eşik: > 0,5 V → röle tetiklenmeli (≤ 1 tampon).

### §C.4 Zamanlama ölçümü

1. DC ihlalini kasıtlı uygula.
2. Röle GPIO tetik zaman damgasını kaydet.
3. Gecikme = tetik − ihlal başlangıcı.
4. Kabul: ≤ 10,67 ms.

## §D Referans Değerler ve Eşikler

| # | Parametre | Değer | Kaynak |
|---|---|---|---|
| D-1 | Tampon boyutu | 512 örnek | sartname.md |
| D-2 | Örnekleme frekansı | 48 kHz | sartname.md |
| D-3 | Tampon süresi | 10,67 ms | 512 / 48000 hesabı |
| D-4 | Veri tipi | 32-bit float | ADR-017 |
| D-5 | SNR eşiği | > 100 dB | K005-firmware audio standardı |
| D-6 | THD+N eşiği | < %0,01 @ 1 kHz | K005-firmware audio standardı |
| D-7 | DC offset koruma eşiği | > 0,5 V | ADR-038 H001 (kural 10) |
| D-8 | Röle tetik gecikmesi | ≤ 1 tampon (10,67 ms) | Taslak hedef (UNKNOWN kesin donanım gecikmesi) |
| D-9 | Boot → hazır süre | ≤ 1200 ms | boot-dogrulama.md B-13 |
| D-10 | Crosstalk eşiği | < −80 dB | ⚠️ VERIFICATION REQUIRED (kesin eşik belirsiz) |
| D-11 | DAC | PCM3168A (yasak: PCM5122) | ADR-038 H001 |
| D-12 | Kanal haritası | 8.1 (+ height belirsiz) | ⚠️ VERIFICATION REQUIRED (UNKNOWN) |

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| ADC peripheralleri ölçüm girdisi için kullanılır | https://docs.zephyrproject.org/latest/hardware/peripherals/adc.html | 2026-10-13 | HTTP 200 |
| DMA peripheralleri veri aktarımını tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |
| PWM peripheralleri röle/sürücü zamanlaması için referanstır | https://docs.zephyrproject.org/latest/hardware/peripherals/pwm.html | 2026-10-13 | HTTP 200 |
| Entropi peripheralleri güvenli rastgelelik için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/entropy.html | 2026-10-13 | HTTP 200 |
| Kullanıcı kipi (usermode) izolasyonu Zephyr'de tanımlanır | https://docs.zephyrproject.org/latest/kernel/usermode/index.html | 2026-10-13 | HTTP 200 |

## §F Uzatılmış Ölçüm Betikleri (Taslak)

### §F.1 Python DC eşiği doğrulama betiği

```python
# Kaynak: DC eşik doğrulama betiği taslağı — audio-control-dogrulama.md §C.3.
# Kural 10: DC >0,5 V → röle. Repo betik yok; taslak.
TRIP_VOLTS = 0.5
BUFFER_SAMPLES = 512

def dc_trip_detected(samples, trip=TRIP_VOLTS):
    if not samples:
        return False
    avg = sum(samples) / len(samples)
    return abs(avg) > trip

def relay_latency_ms(trip_time_s, trip_detected_s):
    return (trip_detected_s - trip_time_s) * 1000.0

if __name__ == "__main__":
    fake = [0.6] * BUFFER_SAMPLES
    print("trip:", dc_trip_detected(fake))
    print("latency_ms <= 10.67 hedefi")
```

### §F.2 SNR ölçüm betiği (taslak)

```python
# Kaynak: SNR ölçüm betiği taslağı — audio-control-dogrulama.md §C.1.
import math

def rms(samples):
    if not samples:
        return 0.0
    return math.sqrt(sum(x * x for x in samples) / len(samples))

def snr_db(signal, noise):
    ps = rms(signal) ** 2
    pn = rms(noise) ** 2
    if pn == 0:
        return float("inf")
    return 10.0 * math.log10(ps / pn)

if __name__ == "__main__":
    print("SNR > 100 dB hedefi (sartname)")
```

### §F.3 THD+N ölçüm betiği (taslak)

```python
# Kaynak: THD+N ölçüm betiği taslağı — audio-control-dogrulama.md §C.2.
import math

def thd_plus_n_percent(total_rms, fundamental_rms):
    if fundamental_rms == 0:
        return float("inf")
    return 100.0 * (total_rms / fundamental_rms)

if __name__ == "__main__":
    print("THD+N < 0.01% hedefi @ 1 kHz")
```

## §G Ölçüm Ekipmanı Matrisi

| Ölçüm | Ekipman | Bağlantı | Not |
|---|---|---|---|
| SNR | Audio analizör | PCM3168A çıkışı | 1 kHz referans |
| THD+N | Spektrum analizörü | PCM3168A çıkışı | 1 kHz @ −1 dBFS |
| DC offset | DMM / yüksek çözünürlüklü ADC | Çıkış | Sessiz girişi |
| Röle gecikmesi | Zaman işaretçisi + GPIO sonda | Röle sürücü | ≤ 10,67 ms |
| Crosstalk | Çoklu kanal kayıt | Tüm kanallar | Eşik UNKNOWN |
| DMA underrun | Telemetri sayacı | firmware içi | AV-7 |
| Boot → hazır | Zaman damgası | Boot olay günlüğü | ≤ 1200 ms |

## §H Sartname Bağlantı Tablosu (Doğrulama)

| Sartname sabiti | Doğrulama senaryosu |
|---|---|
| 512 örnek | AV-1, DC penceresi |
| 48 kHz (10,67 ms) | AV-1, AV-5 gecikme eşiği |
| 32-bit float (ADR-017) | AV-2, AV-3 |
| PCM3168A (ADR-038 H001) | DAC register doğrulama (AV-14) |
| DC >0,5 V → röle | AV-5, AV-6 |
| ASIO Exclusive Lock | AV-9 |
| THD+N < %0,01 | AV-4 |
| SNR > 100 dB | AV-3 |

## §I CI Köprüsü Notu (Doğrulama)

Ölçüm betikleri (§F) CI'ya firmware build adımıyla eklenecek (onay + ADR
gerektirir). Donanım ölçümleri CI'da DEĞİL; manuel/tezgah doğrulamasıdır.
`secret-scan.yml` firmware ikili dosyalarında da çalıştırılmalı. ⚠️ Mevcut
CI DEĞİŞTİRİLMEZ.

## §J Sık Sorulanlar

**S: SNR >100 dB neden PCM3168A ile mümkün?**
PCM3168A yüksek SNR'lı bir DAC'tır (ADR-038 H001); ancak gerçek SNR sistem gürültüsüne (güç, PCB) bağlıdır — ölçüm AV-3 ile doğrulanır.

**S: THD+N neden 1 kHz'de ölçülür?**
Sartname standardı 1 kHz @ %0,01 eşiğini verir (sartname.md); diğer frekanslar ek doğrulamadır.

**S: Röle gecikmesi nasıl ölçülür?**
DC ihlali uygulanır, röle GPIO tetik zaman damgası kaydedilir; gecikme ≤ 10,67 ms (AV-5).

**S: Ölçüm betikleri repo'da var mı?**
Hayır. Repo'da firmware/betik kaynağı yok; betikler taslaktır (§F, `Kaynak:` etiketli).

**S: Crosstalk eşiği neden UNKNOWN?**
Kesin eşik mimari karar/hardware tasarımına bağlı (`.ai/electronics/`); uydurma eşik yazılmaz.

## §K Doğrulama Test Matrisi (özet)

| Test | Yöntem | Senaryo |
|---|---|---|
| Statik analiz | cppcheck/Clang-Tidy | Tümü |
| Birim | Unity/ceedling | DC eşik, sayaçlar |
| Entegrasyon | Renode/gerçek MCU | DMA, I2S |
| Donanım ölçüm | Audio analizör/DMM | SNR, THD+N, DC |
| Zamanlama | Zaman işaretçisi | Röle gecikmesi |

## §L Doğrulama Senaryoları (Genişletme — AV detay kartları)

### §L.1 AV-5 detay kartı — DC koruma

| Alan | Değer |
|---|---|
| Giriş | DC kaydırmalı sinyal (0,6 V karşılığı) |
| Beklenen | Röle ≤ 1 tampon (10,67 ms) içinde tetik |
| Ölçüm | GPIO zaman damgası |
| Kritiklik | Kritik (donanım koruma) |
| Ekipman | Zaman işaretçisi + GPIO sonda |

### §L.2 AV-3/AV-4 detay kartı — SNR/THD+N

| Alan | Değer |
|---|---|
| Giriş | 1 kHz @ −20 dBFS (SNR) / −1 dBFS (THD+N) |
| Beklenen | SNR > 100 dB; THD+N < %0,01 |
| Ölçüm | Spektrum analizörü |
| Kritiklik | Kritik (ses kalitesi) |
| Ekipman | Audio analizör |

### §L.3 AV-9 detay kartı — ASIO kilidi

| Alan | Değer |
|---|---|
| Giriş | İkinci uygulama kilit denemesi |
| Beklenen | İhlal tespiti + durum raporu |
| Ölçüm | Durum bayrağı (B7) |
| Kritiklik | Yüksek (kural 9) |
| Ekipman | Host köprüsü |

## §M Ölçüm Yöntemi Matrisi (Genişletme)

| Parametre | Yöntem | Frekans | Eşik |
|---|---|---|---|
| SNR | 1 kHz sinus, sinyal+gürültü | Her doğrulama | > 100 dB |
| THD+N | 1 kHz sinus, harmonik+gürültü | Her doğrulama | < %0,01 |
| DC offset | Sessizlik ortalaması | Sürekli (firmware) | > 0,5 V trip |
| Röle gecikmesi | Zaman damgası | DC ihlalinde | ≤ 10,67 ms |
| Underrun/overrun | sayaç (telemetri) | Sürekli | 0 hedefi (UNKNOWN kabul) |
| Boot → hazır | Boot olay günlüğü | Açılış | ≤ 1200 ms |
| Crosstalk | Çoklu kanal kayıt | Periyodik | UNKNOWN (⚠️) |

## §N Sartname Bağlantı Tablosu (Genişletme)

| Sartname sabiti | Doğrulama senaryosu | Eşik |
|---|---|---|
| 512 örnek | AV-1, AV-5 (pencere) | 512 |
| 48 kHz (10,67 ms) | AV-1, AV-5 (gecikme) | 10,67 ms |
| 32-bit float (ADR-017) | AV-2, AV-3 | dönüşüm kaybı yok |
| PCM3168A (ADR-038 H001) | AV-14 | register doğrulama |
| DC >0,5 V → röle | AV-5, AV-6 | 0,5 V |
| ASIO Exclusive Lock | AV-9 | ihlal tespiti |
| THD+N < %0,01 | AV-4 | %0,01 |
| SNR > 100 dB | AV-3 | 100 dB |

## §O CI Köprüsü Notu (Genişletme — Doğrulama)

Ölçüm betikleri (§F) firmware build adımıyla CI'ya eklenecek (onay + ADR
gerektirir). Donanım ölçümleri (SNR/THD+N/DC) CI'da DEĞİL; manuel tezgah
doğrulamasıdır. Statik analiz + birim test CI'da çalışır. ⚠️ Mevcut CI
DEĞİŞTİRİLMEZ.

## §P Sık Sorulanlar (Genişletme — Doğrulama)

**S: Ölçümler neden tezgahda?**
SNR/THD+N/DC için gerçek DAC (PCM3168A) + analog ön yük gerekir (`.ai/electronics/`); simülasyon yetersizdir.

**S: AV-10 boot→hazır nasıl ölçülür?**
Boot olay günlüğü + hazır zaman damgası; boot-dogrulama.md B-13 köprüsü (≤1200 ms).

**S: Crosstalk eşik neden UNKNOWN?**
Donanım topolojisine (PCB, ekranlama) bağlı; uydurma eşik yazılmaz.

**S: Betikler repo'da var mı?**
Hayır; taslak (§F, `Kaynak:` etiketli). Repo'da firmware/betik kaynağı yok.

**S: Röle gecikme hedefi neden 10,67 ms?**
1 tampon (512/48000); duyulabilir hasarı önlemek için (kural 10).

## §Q Referanslar (Bu Dosya)

- ADR-017 (32-bit float), ADR-038 H001 (PCM3168A)
- K005-firmware kuralı 1–13 (RT, tahsis, noexcept, constexpr, alignas, koruma, ASIO)
- `K005-firmware/audio-control/audio-control-kapsam.md` (kabul kriterleri)
- `K005-firmware/boot/boot-dogrulama.md` (B-13 köprüsü)

## §R Senaryo Tetikleyici Matrisi (Genişletme)

| Senaryo | Tetikleyici | Öncelik | Beklenen sonuç |
|---|---|---|---|
| AV-1 | Yapılandırma değişikliği | Kritik | 512/48k doğrulama |
| AV-2 | Tip değişikliği (ADR-017) | Kritik | float hat |
| AV-3 | SNR doğrulama | Kritik | > 100 dB |
| AV-4 | THD+N doğrulama | Kritik | < %0,01 |
| AV-5 | DC ihlali (kasıtlı) | Kritik | Röle tetik |
| AV-6 | DC normal | Yüksek | Röle boşta |
| AV-7 | DMA underrun (kasıtlı) | Yüksek | sayaç artar |
| AV-8 | DMA overrun (kasıtlı) | Yüksek | sayaç artar |
| AV-9 | ASIO ikinci uygulama | Yüksek | İhlal raporu |
| AV-10 | Açılış | Orta | ≤ 1200 ms |
| AV-11 | EQ bypass/açık | Orta | Frekans farkı |
| AV-12 | Aşırı seviye | Orta | Gain reduction |
| AV-13 | Tek kanallı sinyal | Orta | Sızıntı < −80 dB (UNKNOWN) |
| AV-14 | Her kanala tek tek | Yüksek | Doğru kanal |
| AV-15 | DC temizlenince | Yüksek | Kurtarma |

## §S Ölçüm Kabul Matrisi (Genişletme)

| Ölçüm | Kabul | Yöntem |
|---|---|---|
| SNR | > 100 dB | §C.1 |
| THD+N | < %0,01 @ 1 kHz | §C.2 |
| DC trip | > 0,5 V → röle ≤ 10,67 ms | §C.3, §C.4 |
| Underrun/overrun | 0 (kabul; UNKNOWN kesin eşik) | Telemetri |
| Boot → hazır | ≤ 1200 ms | B-13 köprüsü |
| Crosstalk | < −80 dB (UNKNOWN eşik) | Çoklu kanal |
| TDM eşleme | Doğru kanal | Tek kanallı test |

## §T Kanıt Zinciri (Doğrulama)

Her ölçüm şu zinciri izler: sartname.md/ADR → eşik (§D) → senaryo (§A) →
metodoloji (§C) → ekipman (§B.3) → kayıt. Eksik halka = `⚠️ VERIFICATION
REQUIRED`. Kesin donanım gecikmesi (röle) UNKNOWN — taslak hedef (D-8).

## §U Sartname Bağlantı Tablosu (Son)

| Sartname sabiti | Doğrulama | Eşik |
|---|---|---|
| 512 örnek | AV-1, AV-5 | 512 |
| 48 kHz (10,67 ms) | AV-1, AV-5 | 10,67 ms |
| 32-bit float (ADR-017) | AV-2, AV-3 | dönüşüm yok |
| PCM3168A (ADR-038 H001) | AV-14 | register |
| DC >0,5 V → röle | AV-5, AV-6 | 0,5 V |
| ASIO Exclusive Lock | AV-9 | ihlal |
| THD+N < %0,01 | AV-4 | %0,01 |
| SNR > 100 dB | AV-3 | 100 dB |

## §V Referanslar (Doğrulama — Son)

- ADR-017 (32-bit float), ADR-038 H001 (PCM3168A)
- K005-firmware kuralı 1–13
- `K005-firmware/audio-control/audio-control-kapsam.md` (kabul kriteri)
- `K005-firmware/boot/boot-dogrulama.md` (B-13 köprüsü)

## §W Doğrulanmış Web Kaynakları (Doğrulama — Son)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| ADC peripheralleri ölçüm girdisi için kullanılır | https://docs.zephyrproject.org/latest/hardware/peripherals/adc.html | 2026-10-13 | HTTP 200 |
| PWM peripheralleri röle/sürücü zamanlaması için referanstır | https://docs.zephyrproject.org/latest/hardware/peripherals/pwm.html | 2026-10-13 | HTTP 200 |
| DMA peripheralleri veri aktarımını tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |
| Entropi peripheralleri güvenli rastgelelik için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/entropy.html | 2026-10-13 | HTTP 200 |

## §X Doğrulama Adım Sırası (Kanıt Zinciri — Son)

| Adım | İş | Araç |
|---|---|---|
| 1 | Statik analiz | cppcheck / Clang-Tidy |
| 2 | Birim test | Unity / ceedling |
| 3 | Entegrasyon (sim) | Renode / Zephyr native_sim |
| 4 | Donanım ölçüm | Audio analizör / DMM |
| 5 | Zamanlama | Zaman işaretçisi + GPIO |
| 6 | Telemetri doğrulama | sayaçlar (diagnostics köprüsü) |
| 7 | Boot köprüsü | boot-dogrulama.md B-13 |

## §Y Doğrulama Senaryosu Ayrıntı Kartları (Son Ek)

### §Y.1 AV-11 (DSP geçiş etkisi) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Beyaz gürültü + EQ bypass/açık |
| Beklenen | Frekans tepkisi farkı ölçülebilir; clip yok |
| Ölçüm | Spektrum karşılaştırma |
| Kritiklik | Orta |

### §Y.2 AV-12 (kompresör) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Aşırı seviye sinyali (eşiği aşar) |
| Beklenen | Gain reduction uygulanır; distorsiyon artmaz |
| Ölçüm | Gain reduction zaman serisi |
| Kritiklik | Orta |

### §Y.3 AV-13 (crosstalk) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Tek kanallı sinyal |
| Beklenen | Komşu kanal sızıntısı < −80 dB (⚠️ UNKNOWN eşik) |
| Ölçüm | Çoklu kanal kayıt |
| Kritiklik | Orta |

### §Y.4 AV-15 (kurtarma) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | DC ihlali temizlenir |
| Beklenen | Röle bırakma + oturum geri yüklenir (⚠️ UNKNOWN politika) |
| Ölçüm | Durum makinesi geçişi (Tripped → Recovering → Ready) |
| Kritiklik | Yüksek |

## §Z Son Not ve Kapsam Dışı (Doğrulama)

Ölçüm sonuçları (SNR/THD+N/DC/crosstalk) tezgahda manuel alınır; CI yalnız
statik analiz + birim test + sim entegrasyonunu kapsar. Kesin donanım gecikmesi
(röle) ve crosstalk eşiği UNKNOWN — uydurma eşik yazılmaz (`⚠️ VERIFICATION
REQUIRED`).

## §AA Ölçüm Ortamı Gereksinimleri (Son Ek)

| Gereksinim | Amaç | Not |
|---|---|---|
| PCM3168A + analog ön yük | SNR/THD+N/DC ölçümü | `.ai/electronics/` alanı |
| 1 kHz referans jeneratör | SNR/THD+N girdisi | Bilinmeyen model (UNKNOWN) |
| DMM / yüksek çözünürlüklü ADC | DC offset | Çıkış |
| Zaman işaretçisi + GPIO sonda | Röle gecikmesi | ≤ 10,67 ms |
| Çoklu kanal kayıt | Crosstalk | Eşik UNKNOWN |
| SEGGER J-Link | Firmware + DMA sayaç okuma | boot-dogrulama §B köprüsü |

## §AB Doğrulama Sonu Kontrol Listesi (Checklist)

| # | Kontrol | Durum |
|---|---|---|
| 1 | Tüm AV senaryoları tanımlı (AV-1..AV-15) | Evet |
| 2 | Eşikler sartname/ADR'den alındı (§D) | Evet |
| 3 | Ölçüm metodolojisi açık (§C) | Evet |
| 4 | Ekipman taslağı listelendi (§G, §AA) | Evet |
| 5 | Bilinmeyenler UNKNOWN/⚠️ işaretli (crosstalk, röle gecikmesi) | Evet |
| 6 | Boot köprüsü (B-13) tanımlı | Evet |
| 7 | CI sınırı net (donanım ölçümü CI dışı) | Evet |

## §AC Son Referanslar (Doğrulama)

- ADR-017 (32-bit float), ADR-038 H001 (PCM3168A)
- K005-firmware kuralı 1–13
- `K005-firmware/audio-control/audio-control-kapsam.md` (kabul kriteri AC-1..AC-7)
- `K005-firmware/audio-control/audio-control-ornekler.md` (ölçüm betikleri §F)
- `K005-firmware/boot/boot-dogrulama.md` (B-13 köprüsü)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
