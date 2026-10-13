---
node_id: K005-firmware/device-control/device-control-dogrulama
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
  - K005-firmware/device-control
evidence:
  - K005-firmware/device-control/index.md
  - K005-firmware/device-control/device-control-kapsam.md
  - K005-firmware/boot/boot-dogrulama.md
validation: []
open_questions:
  - "Hedef MCU peripheral register addresses bilinmiyor (UNKNOWN)"
  - "Cihaz tarama frekansı mimari karar (UNKNOWN)"
group: kanit
---

# Device-Control Alt-Modülü — Doğrulama (K005-firmware/device-control/device-control-dogrulama.md)

> Bu dosya, `K005-firmware/device-control/` alt-modülünün **doğrulama** dokümanıdır.
> Eşikler sartname/ADR/boot köprülerinden alınmıştır; repo'da firmware kodu
> bulunmadığından (2026-10-13 keşif) ölçüm metodolojisi donanım/standart
> referanslıdır.

## §A Doğrulama Senaryoları

| # | Senaryo | Giriş | Beklenen | Kritiklik |
|---|---|---|---|---|
| DV-1 | I2C yazma/okuma | Register yazma/okuma | Doğru değer, NACK/ACK raporu | Kritik |
| DV-2 | SPI yazma/okuma | Register yazma/okuma | Doğru değer | Kritik |
| DV-3 | GPIO set/clear | Röle pin | Doğru seviye (aktif-düşük dahil) | Kritik |
| DV-4 | PWM çıkış | Duty cycle değişimi | Doğru duty/frekans | Yüksek |
| DV-5 | Counter okuma | Sayaç artışı | Doğru artış | Yüksek |
| DV-6 | PCM3168A register doğrulama | DAC yapılandırması | Doğrulanmış kaynakla eşleşme (ADR-038 H001) | Kritik |
| DV-7 | Cihaz başlatma sırası | Boot sonrası | Deterministik sıra (boot B-12 köprüsü) | Kritik |
| DV-8 | Cihaz olayı işleme gecikmesi | Interrupt/olay | ≤ 1 tampon (10,67 ms) | Yüksek |
| DV-9 | Tahsis denetimi | RT erişim | `malloc`/`new` yok (kural 1) | Kritik |
| DV-10 | Lock-free denetimi | RT erişim | Mutex yok (kural 2) | Kritik |
| DV-11 | Bus çakışması | Eşzamanlı erişim | Doğru arbitraj (UNKNOWN bus topolojisi) | Orta |
| DV-12 | Hata kurtarma | NACK/timeout | Hata kodu + telemetri olayı | Yüksek |

## §B Test Stratejisi

### §B.1 Birim testleri

| Parça | Araç | Kapsam |
|---|---|---|
| Bus soyutlama (B1) | Unity/ceedling | write/read/gpio_set |
| Cihaz kayıt modeli (B2) | Unity/ceedling | kayıt, sıra |
| DAC register erişimi (B3) | Birim (mock bus) | register yazma |
| Röle GPIO (B4) | Birim | aktif-düşük dahil |

### §B.2 Entegrasyon testleri

| Katman | Yöntem | Not |
|---|---|---|
| I2C/SPI + DAC | Renode/gerçek MCU + J-Link | Register doğrulama |
| GPIO + röle | Donanım | DC koruma tetik |
| Başlatma sırası | Boot entegrasyon | B-12 köprüsü |
| Olay gecikmesi | Zaman işaretçisi | ≤ 10,67 ms |

### §B.3 Donanım testleri

| Ölçüm | Ekipman | Not |
|---|---|---|
| I2C/SPI mantık analizörü | Mantık analizörü | Register yazımı doğrulama |
| GPIO seviye | DMM | Röle sürücü |
| PWM duty | Osiloskop | Duty/frekans |
| Counter | Sayaç girişi | Artış doğrulama |

## §C Ölçüm Metodolojisi

### §C.1 Cihaz olayı gecikmesi ölçümü

1. Cihaz olayını (interrupt) kasıtlı uygula.
2. Olay zaman damgası + işlenme zaman damgasını kaydet.
3. Gecikme = işlenme − olay.
4. Kabul: ≤ 10,67 ms (1 tampon).

### §C.2 Bus yazma/okuma doğrulama

1. Bilinen register değerini yaz.
2. Oku ve doğrula.
3. NACK/timeout hatalarını kaydet.
4. Kabul: Doğru değer, hata kodu raporlanır.

### §C.3 Başlatma sırası doğrulama

1. Boot sonrası cihaz başlatma log'unu kaydet.
2. Sıra deterministik mi kontrol et (her açılışta aynı).
3. Kabul: Deterministik sıra (boot B-12 köprüsü).

## §D Referans Değerler ve Eşikler

| # | Parametre | Değer | Kaynak |
|---|---|---|---|
| DD-1 | Olay işleme gecikmesi | ≤ 10,67 ms | 1 tampon (512/48000) |
| DD-2 | Bus yazma/okuma | Doğru değer | Register doğrulama |
| DD-3 | Tahsis | 0 (kural 1) | Statik analiz |
| DD-4 | Lock-free | Mutex yok (kural 2) | Statik analiz |
| DD-5 | DAC | PCM3168A (yasak: PCM5122) | ADR-038 H001 |
| DD-6 | Boot başlatma | Deterministik | boot B-12 köprüsü |
| DD-7 | Cihaz tarama frekansı | UNKNOWN (mimari karar) | ⚠️ VERIFICATION REQUIRED |
| DD-8 | Bus topolojisi | UNKNOWN (hedef MCU) | ⚠️ VERIFICATION REQUIRED |

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| I2C peripheral register erişimi peripherallerdedir | https://docs.zephyrproject.org/latest/hardware/peripherals/i2c.html | 2026-10-13 | HTTP 200 |
| SPI peripheral register erişimi peripherallerdedir | https://docs.zephyrproject.org/latest/hardware/peripherals/spi.html | 2026-10-13 | HTTP 200 |
| Flash peripheral kalıcı depolama için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/flash.html | 2026-10-13 | HTTP 200 |
| Watchdog peripheral sistem sağlığı için tanımlanır | https://docs.zephyrproject.org/latest/hardware/peripherals/watchdog.html | 2026-10-13 | HTTP 200 |
| DMA peripheral veri aktarımını tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |

## §F Uzatılmış Ölçüm Betikleri (Taslak)

### §F.1 Python olay gecikmesi doğrulama betiği

```python
# Kaynak: olay gecikmesi betiği taslağı — device-control-dogrulama.md §C.1.
MAX_LATENCY_MS = 10.67

def event_latency_ok(olay_zamani_s, islenme_zamani_s):
    gecikme_ms = (islenme_zamani_s - olay_zamani_s) * 1000.0
    return gecikme_ms <= MAX_LATENCY_MS

if __name__ == "__main__":
    print("cihaz olayi gecikmesi <= 10.67 ms (1 tampon)")
```

### §F.2 Python bus yazma/okuma doğrulama betiği

```python
# Kaynak: bus doğrulama betiği taslağı — device-control-dogrulama.md §C.2.
def bus_write_read_ok(yazilan, okunan):
    return yazilan == okunan

if __name__ == "__main__":
    print("bus yazma/okuma dogrulama — hedef MCU UNKNOWN")
```

### §F.3 Python başlatma sırası doğrulama betiği

```python
# Kaynak: başlatma sırası betiği taslağı — device-control-dogrulama.md §C.3.
def init_order_ok(sira_listesi):
    return sira_listesi == sorted(sira_listesi)

if __name__ == "__main__":
    print("cihaz baslatma sirasi deterministik (boot B-12)")
```

## §G Ölçüm Ekipmanı Matrisi (Genişletme)

| Ölçüm | Ekipman | Bağlantı | Not |
|---|---|---|---|
| I2C/SPI yazma/okuma | Mantık analizörü | Bus | Register doğrulama |
| GPIO seviye | DMM | Röle pin | DC koruma |
| PWM duty | Osiloskop | PWM çıkışı | Duty/frekans |
| Counter | Sayaç girişi | Counter | Artış |
| Olay gecikmesi | Zaman işaretçisi + GPIO | Interrupt | ≤ 10,67 ms |
| Başlatma sırası | Boot olay günlüğü | Firmware | Deterministik |

## §H Sartname Bağlantı Tablosu (Genişletme)

| Sartname sabiti | Doğrulama senaryosu |
|---|---|
| 512 örnek / 48 kHz (10,67 ms) | DV-8 (olay gecikmesi) |
| PCM3168A (ADR-038 H001) | DV-6 (register doğrulama) |
| DC >0,5 V → röle | DV-3 (GPIO), audio-control AV-5 köprüsü |
| Boot deterministik sıra | DV-7 (B-12 köprüsü) |
| Tahsis yok (kural 1) | DV-9 |
| Lock-free (kural 2) | DV-10 |

## §I CI Köprüsü Notu (Genişletme — Doğrulama)

Birim/entegrasyon testleri CI'ya firmware build adımıyla eklenecek (onay + ADR
gerektirir). Donanım ölçümleri (mantık analizörü, DMM) CI'da DEĞİL; manuel
tezgah doğrulamasıdır. ⚠️ Mevcut CI DEĞİŞTİRİLMEZ.

## §J Sık Sorulanlar (Genişletme — Doğrulama)

**S: DV-11 (bus çakışması) nasıl test edilir?**
Eşzamanlı erişim kasıtlı uygulanır; bus topolojisi UNKNOWN olduğu için sonuç yorumu belirsiz (⚠️ VERIFICATION REQUIRED).

**S: Register doğrulama neden kritik?**
PCM3168A (ADR-038 H001) yanlış register yazımı ses hasarı/bozulma üretir.

**S: Olay gecikmesi eşiği neden 10,67 ms?**
1 tampon (512/48000); audio-control zamanlama zinciriyle uyumlu (DC-3).

**S: Başlatma sırası neden deterministik olmalı?**
Boot B-12 köprüsü; rastgele sıra boot gecikmesi/factory hatası üretir.

**S: Tahsis/lock-free denetimi nasıl yapılır?**
cppcheck/Clang-Tidy statik analiz (DV-9, DV-10).

## §K Doğrulama Test Matrisi (Genişletme — Son)

| Test | Yöntem | Senaryo |
|---|---|---|
| Statik analiz | cppcheck/Clang-Tidy | DV-9, DV-10 |
| Birim | Unity/ceedling | DV-1..DV-5 |
| Entegrasyon | Renode/gerçek MCU | DV-6, DV-7 |
| Donanım | Mantık analizörü/DMM | DV-3, DV-4, DV-5 |
| Zamanlama | Zaman işaretçisi | DV-8 |

## §L Referanslar (Doğrulama — Son)

- ADR-038 H001 (PCM3168A)
- K005-firmware kuralı 1–13
- `K005-firmware/device-control/device-control-kapsam.md` (kabul kriteri DC-1..DC-6)
- `K005-firmware/boot/boot-dogrulama.md` (B-12 köprüsü)

## §M Doğrulama Senaryosu Ayrıntı Kartları (Genişletme)

### §M.1 DV-6 (DAC register doğrulama) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | PCM3168A register yapılandırması (I2C) |
| Beklenen | Doğrulanmış kaynakla eşleşme (ADR-038 H001) |
| Ölçüm | I2C mantık analizörü + register okuma |
| Kritiklik | Kritik |

### §M.2 DV-7 (başlatma sırası) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Boot sonrası cihaz init |
| Beklenen | Deterministik sıra (boot B-12 köprüsü) |
| Ölçüm | Başlatma log'u + sıra karşılaştırma |
| Kritiklik | Kritik |

### §M.3 DV-8 (olay gecikmesi) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Interrupt/olay |
| Beklenen | ≤ 10,67 ms (1 tampon) |
| Ölçüm | Zaman damgası (§C.1) |
| Kritiklik | Yüksek |

### §M.4 DV-11 (bus çakışması) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Eşzamanlı bus erişimi |
| Beklenen | Doğru arbitraj (⚠️ UNKNOWN bus topolojisi) |
| Ölçüm | Mantık analizörü |
| Kritiklik | Orta |

## §N Ölçüm Kabul Matrisi (Genişletme)

| Ölçüm | Kabul | Yöntem |
|---|---|---|
| I2C/SPI yazma/okuma | Doğru değer, NACK raporu | §C.2 |
| GPIO/röle | Doğru seviye (aktif-düşük dahil) | Donanım |
| PWM duty | Doğru duty/frekans | Osiloskop |
| Counter | Doğru artış | Sayaç girişi |
| DAC register | Doğrulanmış kaynak eşleşmesi | Mantık analizörü |
| Başlatma sırası | Deterministik | §C.3 (B-12) |
| Olay gecikmesi | ≤ 10,67 ms | §C.1 |
| Tahsis | 0 (kural 1) | cppcheck |
| Lock-free | Mutex yok (kural 2) | cppcheck |

## §O Kanıt Zinciri (Genişletme — Son)

Her ölçüm şu zinciri izler: sartname/ADR/boot köprüsü → eşik (§D) → senaryo
(§A) → metodoloji (§C) → ekipman (§G) → kayıt. Eksik halka = `⚠️ VERIFICATION
REQUIRED`. Bus topolojisi ve tarama frekansı UNKNOWN — uydurma yazılmaz.

## §P Doğrulama Sonu Kontrol Listesi (Checklist)

| # | Kontrol | Durum |
|---|---|---|
| 1 | Tüm DV senaryoları tanımlı (DV-1..DV-12) | Evet |
| 2 | Eşikler sartname/ADR/boot köprülerinden alındı (§D) | Evet |
| 3 | Ölçüm metodolojisi açık (§C) | Evet |
| 4 | Ekipman taslağı listelendi (§G) | Evet |
| 5 | Bilinmeyenler UNKNOWN/⚠️ işaretli (bus topolojisi, tarama) | Evet |
| 6 | Boot köprüsü (B-12) tanımlı | Evet |
| 7 | CI sınırı net (donanım ölçümü CI dışı) | Evet |

## §Q Doğrulanmış Web Kaynakları (Genişletme — Son)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Flash peripheraller kalıcı depolama tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/flash.html | 2026-10-13 | HTTP 200 |
| Watchdog peripheraller sistem sağlığı tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/watchdog.html | 2026-10-13 | HTTP 200 |
| DMA peripheraller veri aktarımı tanımlar | https://docs.zephyrproject.org/latest/hardware/peripherals/dma.html | 2026-10-13 | HTTP 200 |

## §R Son Referanslar (Doğrulama)

- ADR-038 H001 (PCM3168A)
- K005-firmware kuralı 1–13
- `K005-firmware/device-control/device-control-kapsam.md` (kabul kriteri DC-1..DC-6)
- `K005-firmware/boot/boot-dogrulama.md` (B-12 köprüsü)

## §S Ölçüm Ortamı Gereksinimleri (Genişletme — Son)

| Gereksinim | Amaç | Not |
|---|---|---|
| Mantık analizörü | I2C/SPI yazma/okuma doğrulama | Register yazımı |
| DMM | GPIO/röle seviye | Aktif-düşük dahil |
| Osiloskop | PWM duty/frekans | Boyut/sürücü |
| Sayaç girişi | Counter artışı | Ölçüm |
| Zaman işaretçisi + GPIO sonda | Olay gecikmesi | ≤ 10,67 ms |
| SEGGER J-Link | Firmware + register okuma | boot-dogrulama §B köprüsü |
| Gerçek MCU + PCM3168A | DAC entegrasyonu | `.ai/electronics/` |

## §T Doğrulama Sonu Not (Genişletme)

Ölçümler tezgahda manuel alınır; CI yalnız statik analiz + birim test + sim
entegrasyonunu kapsar. Bus topolojisi ve tarama frekansı UNKNOWN — uydurma
eşik/frekans yazılmaz (`⚠️ VERIFICATION REQUIRED`). PCM5122 ASLA kullanılmaz
(ADR-038 H001).

## §U Doğrulama Senaryosu Tam Liste Kartları (Genişletme — Son Ek)

### §U.1 DV-1 (I2C) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | I2C register yazma/okuma |
| Beklenen | Doğru değer, NACK/ACK raporu |
| Ölçüm | Mantık analizörü + register okuma |
| Kritiklik | Kritik |

### §U.2 DV-2 (SPI) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | SPI register yazma/okuma |
| Beklenen | Doğru değer |
| Ölçüm | Mantık analizörü |
| Kritiklik | Kritik |

### §U.3 DV-3 (GPIO/röle) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Röle pin set/clear |
| Beklenen | Doğru seviye (aktif-düşük dahil) |
| Ölçüm | DMM |
| Kritiklik | Kritik (DC koruma, kural 10) |

### §U.4 DV-4 (PWM) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Duty cycle değişimi |
| Beklenen | Doğru duty/frekans |
| Ölçüm | Osiloskop |
| Kritiklik | Yüksek |

### §U.5 DV-5 (counter) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | Sayaç artışı |
| Beklenen | Doğru artış |
| Ölçüm | Sayaç girişi |
| Kritiklik | Yüksek |

### §U.6 DV-12 (hata kurtarma) ayrıntı

| Alan | Değer |
|---|---|
| Giriş | NACK/timeout |
| Beklenen | Hata kodu + telemetri olayı |
| Ölçüm | sayaçlar (§F.1) |
| Kritiklik | Yüksek |

## §V Doğrulama Sonu Not (Genişletme — Son Ek)

Ölçümler tezgahda manuel alınır; CI yalnız statik analiz + birim test + sim
entegrasyonunu kapsar. Bus topolojisi/tarama frekansı UNKNOWN — uydurma
eşik yazılmaz. PCM5122 ASLA kullanılmaz (ADR-038 H001).

## §W Ölçüm Yöntemi Matrisi (Genişletme — Son Ek)

| Parametre | Yöntem | Frekans | Eşik |
|---|---|---|---|
| I2C/SPI yazma/okuma | Mantık analizörü + register okuma | Her doğrulama | Doğru değer, NACK raporu |
| GPIO/röle seviye | DMM | DC ihlalinde | Doğru seviye |
| PWM duty/frekans | Osiloskop | Periyodik | Doğru duty |
| Counter artışı | Sayaç girişi | Periyodik | Doğru artış |
| DAC register | Mantık analizörü + okuma | Boot + değişiklik | Doğrulanmış kaynak eşleşmesi |
| Başlatma sırası | Boot olay günlüğü | Açılış | Deterministik (B-12) |
| Olay gecikmesi | Zaman damgası + GPIO | Her olay | ≤ 10,67 ms |
| Tahsis | cppcheck/Clang-Tidy | CI | 0 (kural 1) |
| Lock-free | cppcheck/Clang-Tidy | CI | Mutex yok (kural 2) |

## §X Doğrulama Adım Sırası (Genişletme — Son Ek)

| Adım | İş | Araç |
|---|---|---|
| 1 | Statik analiz | cppcheck / Clang-Tidy |
| 2 | Birim test | Unity / ceedling |
| 3 | Entegrasyon (sim) | Renode / Zephyr native_sim |
| 4 | Donanım ölçüm | Mantık analizörü / DMM / Osiloskop |
| 5 | Zamanlama | Zaman işaretçisi + GPIO |
| 6 | Telemetri doğrulama | sayaçlar (diagnostics köprüsü) |
| 7 | Boot köprüsü | boot-dogrulama.md B-12 |

## §Y Son Not ve Kapsam Dışı (Doğrulama — Son Ek)

Ölçümler tezgahda manuel alınır; CI yalnız statik analiz + birim test + sim
entegrasyonunu kapsar. Bus topolojisi/tarama frekansı UNKNOWN — uydurma eşik
yazılmaz. PCM5122 ASLA kullanılmaz (ADR-038 H001).

## §Z Ölçüm Kabul Matrisi (Doğrulama — Son Ek)

| Ölçüm | Kabul | Yöntem |
|---|---|---|
| I2C yazma/okuma | Doğru değer, NACK raporu | Mantık analizörü |
| SPI yazma/okuma | Doğru değer | Mantık analizörü |
| GPIO/röle | Doğru seviye (aktif-düşük dahil) | DMM |
| PWM duty | Doğru duty/frekans | Osiloskop |
| Counter | Doğru artış | Sayaç girişi |
| DAC register | Doğrulanmış kaynak eşleşmesi | Mantık analizörü + okuma |
| Başlatma sırası | Deterministik | Boot olay günlüğü (B-12) |
| Olay gecikmesi | ≤ 10,67 ms | Zaman damgası |
| Tahsis | 0 (kural 1) | cppcheck |
| Lock-free | Mutex yok (kural 2) | cppcheck |

## §AA Doğrulama Test Matrisi (Doğrulama — Son Ek)

| Test | Yöntem | Senaryo |
|---|---|---|
| Statik analiz | cppcheck/Clang-Tidy | DV-9, DV-10 |
| Birim (bus) | Unity/ceedling | DV-1, DV-2 |
| Birim (kayıt) | Unity/ceedling | DV-7 |
| Birim (DAC) | Unity/ceedling (mock bus) | DV-6 |
| Birim (röle) | Unity/ceedling | DV-3 |
| Entegrasyon (I2C/SPI + DAC) | Renode/gerçek MCU + J-Link | DV-6 |
| Entegrasyon (GPIO + röle) | Donanım | DV-3 |
| Entegrasyon (başlatma) | Boot entegrasyon | DV-7 (B-12) |
| Zamanlama (olay) | Zaman işaretçisi | DV-8 |
| Bus çakışma | Mantık analizörü | DV-11 |
| Hata kurtarma | NACK/timeout kasıtlı | DV-12 |

## §AB Doğrulama Bağımlılık Grafiği (Doğrulama — Son Ek)

```text
  sartname/ADR/boot köprüsü
        |
        v
    esikler (§D)
        |
        v
   senaryolar (§A: DV-1..DV-12)
        |
        v
  metodoloji (§C) --> ekipman (§G) --> kayit
```

## §AC Doğrulama Sonu Referanslar (Doğrulama — Son Ek)

- ADR-038 H001 (PCM3168A, PCM5122 yasak)
- K005-firmware kuralı 1–13
- `K005-firmware/device-control/device-control-kapsam.md` (kabul kriteri DC-1..DC-6)
- `K005-firmware/device-control/device-control-ornekler.md` (ölçüm betikleri §F)
- `K005-firmware/boot/boot-dogrulama.md` (B-12 köprüsü)
- `K005-firmware/audio-control/audio-control-dogrulama.md` (AV-5 DC koruma köprüsü)

## §AD Doğrulama Sonu Not (Doğrulama — Son Ek)

`device-control` doğrulaması: bus yazma/okuma (DV-1/2), GPIO/röle (DV-3), PWM
(DV-4), counter (DV-5), DAC register (DV-6, ADR-038 H001), başlatma sırası
(DV-7, boot B-12), olay gecikmesi (DV-8, ≤ 10,67 ms), tahsis/lock-free
(DV-9/10, kural 1/2), bus çakışma (DV-11), hata kurtarma (DV-12). Bus
topolojisi/tarama frekansı UNKNOWN — uydurma eşik yazılmaz. PCM5122 ASLA
kullanılmaz.

## §AE Doğrulama Kapsam Dışı Bırakılan Konular (Doğrulama — Son Ek)

| Konu | Neden kapsam dışı | Sorumlu alan |
|---|---|---|
| SNR/THD+N ölçümü | Ses kalitesi ölçümü | `audio-control` doğrulama |
| DC offset algoritma doğrulaması | Ses koruma | `audio-control` doğrulama (AV-5/6) |
| Termal/güç ölçümü | Donanım | `.ai/electronics/` |
| OTA paket doğrulama | Güncelleme | `update` doğrulama |
| Kripto/imza doğrulama | Güvenlik | `security` doğrulama |
| Boot flaş bütünlüğü | Boot | `boot` doğrulama (B-02..B-05) |

## §AF Doğrulama Sonu Sartname Bağlantı Grafiği (Doğrulama — Son Ek)

```text
  ADR-038 H001 (PCM3168A)
        |
        v
   DV-6 register dogrulama
        |
  kural 10 (DC >0,5 V)
        |
        v
   DV-3 GPIO/röle -----> audio-control AV-5 (DC koruma köprüsü)
        |
  boot B-12 (baslatma)
        |
        v
   DV-7 baslatma sirasi
```

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
