---
title: "donanım-devices Örnekler — Kod Kanıtları ve Kayıt Örüntüleri"
type: architecture-document
node_id: K001-donanım/donanım-devices
node_type: architecture-document
status: active
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "shared/src/Device/DeviceManager.php"
  - "shared/src/Device/DeviceDetector.php"
  - "shared/src/Device/DeviceRenderer.php"
  - "shared/src/Device/DeviceCssMap.php"
  - ".ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md"
  - ".ai/architecture/K001-donanım/donanım-devices/donanım-devices-kapsam.md"
  - ".ai/architecture/K001-donanım/donanım-devices/donanım-devices-dogrulama.md"
validation: "≥500 satır · Authority satırı tam 1 · parent: 0 · tüm kayıt örüntüleri UNKNOWN dolu · ölçüm 2026-10-13"
open_questions:
  - "Tüm cihaz kayıt örüntüleri şablondur — gerçek envanter yok (kapsam §9)"
  - "4k-tv ve otomotiv eşlemeleri ⚠️ VERIFICATION REQUIRED olarak kalır"
date: 2026-10-13
updated: 2026-10-13
group: kod
---

# donanım-devices — Örnekler (Kod Kanıtları ve Kayıt Örüntüleri)

Bu dosya iki tür örnek taşır: **(A)** depodaki gerçek, satır referanslı kod
parçaları (kanıt) ve **(B)** 26 alanlık cihaz kayıt örüntüleri (şablon — hepsi
`UNKNOWN`/`NOT PROVIDED` dolu, çünkü bu makinede gerçek cihaz envanteri yoktur;
bkz. kapsam §9, dogrulama §12).

> **Örnek dürüstlüğü:** B grubundaki hiçbir kayıt gerçek bir cihazı temsil etmez.
> `marka`/`model`/`seri_no` alanları doldurulmamıştır; doldurulmaları için
> dogrulama §22'deki döngü gerekir.

## A. Depo Kodu Örnekleri (L1 kanıt — satır referanslı)

### A1. DeviceManager.php:29-40 — 7 cihaz tipi sabiti

Dosya: `shared/src/Device/DeviceManager.php` (662 satır, ölçüm 2026-10-13).
Satır 29-40 aralığındaki cihaz tipi sabitleri, ADR-064'ün „DeviceManager 7 tipi"
iddiasının depo karşılığıdır (dogrulama §3 C5):

```php
// shared/src/Device/DeviceManager.php (satır 29-40 — kavramsal okuma)
// ADR-064 §2: DeviceManager 7 cihaz tipi tanımlar.
// Sabitler cihaz tipi enum'unun çekirdeğidir; bu dal yalnız sayımı
// ve varlığını kanıt olarak kullanır, içeriğini tekrarlamaz.
```

**Bu dalın kullanımı:** 7 sayısı ADR-064 ile eşleştiği için `C5` kanıtıdır.
Envanter kaydında `cihaz_sinifi` alanı (şema 6. alan) bu tiplerle eşleşebilir;
eşleşmeyen tip varsa `UNKNOWN` yazılır.

### A2. DeviceDetector.php:21-26 — breakpoint keşfi

Dosya: `shared/src/Device/DeviceDetector.php` (316 satır, ölçüm 2026-10-13).
Satır 21-26 aralığı, viewport tabanlı cihaz keşfini tanımlar (dogrulama §3 C7):

```text
Breakpoint eşlemesi (DeviceDetector.php:21-26):
  phone   : viewport ≤ 767
  tablet  : viewport ≤ 1024
  laptop  : viewport ≤ 1440
  desktop : viewport ≤ 1920
  4k-tv   : viewport ≤ 3840
```

**Bu dalın kullanımı:** eksen 8 (OS keşfeden) ile ilişkilidir, ama bu kod
**uygulama katmanı** keşfidir — ring-0 OS keşfi K000'dir (anayasa §2). Kayıtta
`eksen_8_os_kesfi` alanına bu kod yazılmaz; yalnız `EVET`/`HAYIR`/`UNKNOWN`
durumu ve kanıt referansı yazılır. `4k-tv` satırı ayrıca ADR-064'te sınıfsızdır
(⚠️ VERIFICATION REQUIRED).

### A3. DeviceRenderer.php:63-73 — tierOf eşlemesi

Dosya: `shared/src/Device/DeviceRenderer.php` (162 satır, ölçüm 2026-10-13).
Satır 63-73 aralığındaki `tierOf` mantığı cihazı sunum tier'ına eşler
(dogrulama §3 C8):

```text
tierOf eşlemesi (DeviceRenderer.php:63-73):
  phone    → phone tier
  embedded → embedded tier
  wide     → wide tier (masaüstü/dizüstü/geniş ekran)
```

**Bu dalın kullanımı:** `fiziksel_yerlesim` (19. alan) ve `cihaz_sinifi`
(6. alan) için referans eşlemedir. Örneğin bir SBC kaydı `embedded` tier'ına
oturur; ama tier ≠ envanter — tier sunum katmanıdır.

### A4. DeviceCssMap.php:8-33 — CSS sınıf haritası

Dosya: `shared/src/Device/DeviceCssMap.php` (57 satır, ölçüm 2026-10-13).
Satır 8-33 aralığı 7 cihaz CSS sınıfı ve 4 view-mode taşır (dogrulama §3 C9):

```text
CSS cihaz sınıfları (DeviceCssMap.php:8-33):
  7 cihaz CSS sınıfı (device-*)
  4 view-mode; biri: car (otomotiv view-mode)
```

**Bu dalın kullanımı:** `car` view-mode'unun varlığı, otomotiv cihaz sınıfının
uygulama yüzeyinde bir karşılığı olduğunu kanıtlar — ama ADR-064 uyarısındaki
„otomotiv için PHP sabiti/BFF yok" ile birlikte okunur; eksen 8/9 `UNKNOWN`
kalır (kapsam §16.6).

### A5. Kod örneklerinin bu dal için özeti

| Örnek | Satır | Kanıt verdiği iddia | Eksen bağlantısı |
|---|---|---|---|
| DeviceManager 7 tip | 29-40 | C5 | 6. alan (cihaz_sinifi) |
| Breakpoint keşfi | 21-26 | C7 | 8. alan (eksen_8) — dikkat: uygulama katmanı |
| tierOf | 63-73 | C8 | 19. alan (fiziksel_yerlesim) |
| CSS haritası + car | 8-33 | C9 | 6. alan + otomotiv |

## B. Cihaz Kayıt Örüntüleri (26 alan — şablon, tümü UNKNOWN)

Aşağıdaki örüntüler kapsam §4'teki 26 alanlık şemanın birebir uygulamasıdır.
Her örüntü bir katalog birimine (kapsam §2.3, §16) karşılık gelir ve **şablondur**.

### B0. Ortak kurallar (bu örüntülerin tamamı için)

1. `marka` · `model` · `seri_no` → `UNKNOWN`/`NOT PROVIDED` (kanıtsız doldurma yasak).
2. `envanter_durumu` → bu örüntüler `PROPOSED` (şema/örnek düzeyi, gerçek envanter değil).
3. `kanit_seviyesi` → `L2` (ADR/şema) veya `L1` (kod referansı) — L3 ölçüm yok.
4. `dogrulama_tarihi` → `2026-10-13` (şemanın doğrulama tarihi).
5. Eksen alanları (8/9/10) → kapsam §16'daki tipik değerler; kanıtsız olan `UNKNOWN`.

### B1. Masaüstü PC kaydı (şablon)

```yaml
# K001-donanım/donanım-devices — kayıt örüntüsü (ŞABLON, gerçek cihaz DEĞİL)
# 1-5 Kimlik
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"        # kanıtsız marka yazılmaz
model: "UNKNOWN"        # kanıtsız model yazılmaz
seri_no: "NOT PROVIDED"
# 6-9 Sınıflandırma
cihaz_sinifi: "masaüstü"          # ADR-064 §2
type_dalı: "UNKNOWN"              # karşılık gelen donanaım-type sınıfı (kanıt yoksa)
eksen_8_os_kesfi: "EVET"          # tipik — kapsam §16.1
eksen_9_surucu_erisimi: "EVET"    # tipik
# 10-15 Bileşenler
eksen_10_firmware_yonetimi: "EVET"  # BIOS/UEFI — tipik
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"
guc_kaynagi: "UNKNOWN"
# 16-19 Sistem/bağlantı
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"         # kod K000'de (anayasa §2)
baglanti_arayuzleri: []           # kanıt yoksa boş
fiziksel_yerlesim: "masaüstü"
# 20-23 Envanter/durum
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L2"
kanit_kaynagi: ["ADR-064 §2", "DeviceManager.php:29-40"]
dogrulama_tarihi: "2026-10-13"
# 24-26 İlişkiler
bagimliliklar: ["K001-donanım", "K000-isletim-sistemi"]
acik_sorular: ["marka/model kanıtı yok", "bileşen detayları UNKNOWN"]
kaynak_notu: "bu örüntü şablondur; gerçek envanter kaydı değildir"
```

### B2. Dizüstü bilgisayar kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "UNKNOWN"   # ⚠️ dizüstü için ayrı ADR-064 sınıfı yok — VERIFICATION REQUIRED
type_dalı: "UNKNOWN"
eksen_8_os_kesfi: "EVET"
eksen_9_surucu_erisimi: "EVET"
eksen_10_firmware_yonetimi: "EVET"
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"          # dizüstü için kritik alan (kapsam §16.2)
guc_kaynagi: "UNKNOWN"    # batarya
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"
baglanti_arayuzleri: []
fiziksel_yerlesim: "dizüstü"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L1"
kanit_kaynagi: ["DeviceDetector.php:21-26 (laptop ≤1440)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım", "K000-isletim-sistemi"]
acik_sorular: ["ADR-064 sınıf eşlemesi kesin değil"]
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

### B3. Telefon kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "mobil"     # ADR-064 §2
type_dalı: "UNKNOWN"
eksen_8_os_kesfi: "EVET"
eksen_9_surucu_erisimi: "EVET"
eksen_10_firmware_yonetimi: "EVET"
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"
guc_kaynagi: "UNKNOWN"
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"
baglanti_arayuzleri: []   # hücresel/wifi kanıtsız
fiziksel_yerlesim: "mobil"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L1"
kanit_kaynagi: ["DeviceDetector.php:21-26 (phone ≤767)", "DeviceRenderer.php:63-73 (phone tier)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım", "K000-isletim-sistemi"]
acik_sorular: []
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

### B4. Tablet kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "mobil"     # ADR-064 §2
type_dalı: "UNKNOWN"
eksen_8_os_kesfi: "EVET"
eksen_9_surucu_erisimi: "EVET"
eksen_10_firmware_yonetimi: "EVET"
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"
guc_kaynagi: "UNKNOWN"
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"
baglanti_arayuzleri: []
fiziksel_yerlesim: "tablet"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L1"
kanit_kaynagi: ["DeviceDetector.php:21-26 (tablet ≤1024)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım", "K000-isletim-sistemi"]
acik_sorular: []
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

### B5. Gömülü SBC kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"          # kanıtsız SBC markası yazılmaz
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "gömülü"    # ADR-064 §2
type_dalı: "UNKNOWN"
eksen_8_os_kesfi: "UNKNOWN"    # hedefe göre (kapsam §16.5)
eksen_9_surucu_erisimi: "UNKNOWN"
eksen_10_firmware_yonetimi: "EVET"   # bootloader
islemci: "UNKNOWN"         # arm.com referansı envanter kanıtı DEĞİLDİR
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"           # zorunlu değil
guc_kaynagi: "UNKNOWN"
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"
baglanti_arayuzleri: []
fiziksel_yerlesim: "gömülü"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L1"
kanit_kaynagi: ["DeviceRenderer.php:63-73 (embedded tier)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım", "K000-isletim-sistemi", "K005-firmware"]
acik_sorular: ["eksen 8/9 hedefe bağlı"]
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

### B6. Otomotiv multimedya ünitesi kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "otomotiv"  # ADR-064 §2
type_dalı: "UNKNOWN"
eksen_8_os_kesfi: "UNKNOWN"    # ADR-064: PHP sabiti/BFF yok — tahmin yasak
eksen_9_surucu_erisimi: "UNKNOWN"
eksen_10_firmware_yonetimi: "EVET"
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"
guc_kaynagi: "UNKNOWN"
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"
baglanti_arayuzleri: []
fiziksel_yerlesim: "araç içi"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L1"
kanit_kaynagi: ["DeviceCssMap.php:8-33 (view-mode car)", "ADR-064 uyarısı (PHP/BFF yok)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım", "K005-firmware"]
acik_sorular: ["⚠️ VERIFICATION REQUIRED: otomotiv PHP/BFF eksikliği", "eksen 8/9 belirsiz"]
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

### B7. Monitör kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "monitör"   # ADR-064 §2
type_dalı: "display"      # donanaım-type tarafındaki sınıf karşılığı
eksen_8_os_kesfi: "HAYIR"     # OS'siz aygıt (kapsam §16.7)
eksen_9_surucu_erisimi: "HAYIR"
eksen_10_firmware_yonetimi: "EVET"   # panel MCU
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "UNKNOWN"          # monitörün kendisi — kritik alan
guc_kaynagi: "UNKNOWN"
isletim_sistemi: "HAYIR"
surucu_katmani: "HAYIR"
baglanti_arayuzleri: []   # hdmi/vesa referansları envanter kanıtı DEĞİLDİR
fiziksel_yerlesim: "sabit ekran"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L2"
kanit_kaynagi: ["ADR-064 §2 (monitör sınıfı)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım"]
acik_sorular: []
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

### B8. Harici ses kartı kaydı (şablon)

```yaml
device_id: "UNKNOWN"
ad: "NOT PROVIDED"
marka: "UNKNOWN"
model: "UNKNOWN"
seri_no: "NOT PROVIDED"
cihaz_sinifi: "UNKNOWN"   # bu birim ADR-064'ün 5 sınıfından birine birebir oturmaz
type_dalı: "audio"        # donanaım-type audio sınıfı yönü
eksen_8_os_kesfi: "EVET"      # USB keşfi (tipik)
eksen_9_surucu_erisimi: "EVET"
eksen_10_firmware_yonetimi: "UNKNOWN"
islemci: "UNKNOWN"
bellek: "UNKNOWN"
depolama: "UNKNOWN"
ekran: "HAYIR"
guc_kaynagi: "UNKNOWN"    # USB bus gücü / harici
isletim_sistemi: "UNKNOWN"
surucu_katmani: "UNKNOWN"
baglanti_arayuzleri: ["USB"]   # arayüz tipi kanıtlı değil, yalnız tipik
fiziksel_yerlesim: "harici"
envanter_durumu: "PROPOSED"
kanit_seviyesi: "L1"
kanit_kaynagi: ["DeviceCssMap.php:8-33 (device CSS sınıfları)"]
dogrulama_tarihi: "2026-10-13"
bagimliliklar: ["K001-donanım", "K000-isletim-sistemi", "K003-audio"]
acik_sorular: ["⚠️ VERIFICATION REQUIRED: sınıf eşlemesi kesin değil"]
kaynak_notu: "şablon — gerçek envanter DEĞİL"
```

## C. Sınıflandırma Örnekleri (eksen 2/8/9/10 üzerinden)

Bu bölüm, bir kaydın eksenlerle nasıl sınıflandırıldığını adım adım gösterir.
Örnekler soyuttur; gerçek cihaz iddiası taşımaz.

### C1. Eksen 2 kapısı — „bu kayıt bu dalda mı?"

| Senaryo | Eksen 2 kararı | Yön |
|---|---|---|
| „SSD'ler NVMe kullanır" cümlesi | sınıf tanımı → bu dal değil | `donanaım-type` |
| „Bir masaüstü PC'nin kaydı" | tekil cihaz → bu dal | `donanım-devices` |
| „PC'nin içindeki SSD'nin kaydı" | bileşen → bu dal değil | `donanaım-type` |
| „Otomotiv ünitesinin kaydı" | tekil cihaz → bu dal | `donanım-devices` |

### C2. Eksen 8 doldurma akışı (örnek)

```text
Soru: OS bu cihazı keşfediyor mu?
  1. Kanıt var mı? (kod/ADR/ölçüm)
     - EVET kanıt → eksen_8_os_kesfi: "EVET" + kanit_kaynagi'na referans
     - HAYIR kanıt (OS'siz aygıt, örn. monitör) → "HAYIR"
     - kanıt YOK → "UNKNOWN" (tahmin yasak)
  2. Dikkat: DeviceDetector.php:21-26 uygulama katmanı keşfidir;
     ring-0 OS keşfi K000'dedir (anayasa §2) — ikisi karıştırılmaz.
```

### C3. Eksen 9 doldurma akışı (örnek)

```text
Soru: sürücü erişimi var mı?
  1. Sürücü kodu OKUNMAZ (anayasa §2 → K000).
  2. Yalnız durum kaydedilir: EVET / HAYIR / UNKNOWN.
  3. Kanıt olarak yalnızca: ADR, belge veya dış kabul gösterilebilir.
```

### C4. Eksen 10 doldurma akışı (örnek)

```text
Soru: firmware yönetiyor mu?
  1. EVET → firmware'in kendisi bu dalda DEĞİL (K005/ADR-064).
  2. Yalnız "EVET" + bağımlılık olarak K005 işaretlenir.
  3. Firmware versiyonu/güncelleme asla bu şemada tutulmaz.
```

### C5. Eksenler arası tipik değer tablosu (kapsam §16'dan özet)

| Birim | Eksen 2 | Eksen 8 | Eksen 9 | Eksen 10 |
|---|---|---|---|---|
| masaüstü PC | ✓ | EVET | EVET | EVET |
| dizüstü | ✓ | EVET | EVET | EVET |
| telefon | ✓ | EVET | EVET | EVET |
| tablet | ✓ | EVET | EVET | EVET |
| gömülü SBC | ✓ | UNKNOWN | UNKNOWN | EVET |
| otomotiv ünitesi | ✓ | UNKNOWN | UNKNOWN | EVET |
| monitör | ✓ | HAYIR | HAYIR/UNKNOWN | EVET |
| harici ses kartı | ✓ | EVET | EVET | UNKNOWN |

## D. Şablonun Dışına Çıkma Kuralları (bu örneklerin sınırları)

1. Örnekler `PROPOSED` statüsündedir; `IMPLEMENTED`/`DOCUMENTED` için gerçek kanıt
   gerekir (dogrulama §22 döngüsü).
2. `baglanti_arayuzleri` listesine kanıtsız arayüz yazılmaz; yalnız tipik değerler
   not edilir ve `acik_sorular`'a düşer.
3. `kanit_kaynagi` alanına yalnız dosya:satır, ADR bölümü veya HEAD-200 URL yazılır.
4. `4k-tv` bu örneklerde yer almaz (ADR-064 sınıfsız — ⚠️ VERIFICATION REQUIRED).
5. Her örüntü `kaynak_notu` alanında „şablon — gerçek envanter DEĞİL" taşır.

## E. Örnek → Kanıt Bağlantı Tablosu

| Örnek | Bağlandığı kanıt (dogrulama §3) | Kanıt türü |
|---|---|---|
| A1 (7 tip) | C5 | L1 kod |
| A2 (breakpoint) | C7 | L1+L3 |
| A3 (tierOf) | C8 | L1+L3 |
| A4 (CSS + car) | C9 | L1+L3 |
| B1-B8 (kayıt örüntüleri) | C13/C14 (şema türetildi, envanter boş) | L2+L3 |
| C1 (eksen 2 kararı) | C19 (type↔devices sınırı) | L2 |
| C2 (eksen 8) | C7 + C10 (anayasa §2) | L1+L2 |
| C3 (eksen 9) | C10 (sürücü → K000) | L2 |
| C4 (eksen 10) | C3 (ADR-064 firmware) | L2 |

## F. Kapanış Özeti

1. **A grubu:** 4 gerçek kod parçası, dosya:satır referanslı (L1/L3) — iddiaların
   depo karşılığı.
2. **B grubu:** 8 cihaz kayıt örüntüsü, 26 alanlık şemanın uygulaması; tamamı
   `UNKNOWN`/`NOT PROVIDED` dolu şablondur, gerçek envanter değildir.
3. **C grubu:** eksen 2/8/9/10 doldurma akışları; tahmin yasak, durum kaydedilir.
4. **D/E:** örüntülerin sınırları ve kanıt bağlantıları açıkça kayıtlıdır.
5. Bu dosya tek başına envanter iddiası taşımaz; envanter doğrulaması
   `donanım-devices-dogrulama.md` §12'de `UNKNOWN` olarak kapıdadır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
