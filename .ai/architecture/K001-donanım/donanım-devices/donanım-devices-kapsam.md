---
title: "donanım-devices Kapsam — Tamamlanmış Cihaz Envanteri"
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
  - ".ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md"
  - "shared/src/Device/DeviceManager.php"
  - "shared/src/Device/DeviceDetector.php"
  - "shared/src/Device/DeviceRenderer.php"
  - "shared/src/Device/DeviceCssMap.php"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md"
  - ".ai/architecture/K001-donanım/donanaım-type/donanaım-type-kapsam.md"
  - ".ai/architecture/K001-donanım/index.md"
validation: "≥500 satır · Authority satırı tam 1 · parent: 0 · ölçüm 2026-10-13"
open_questions:
  - "26 alanlık şema vault'un başka bir yerinde SSOT olarak bulunamadı — bu dosyada türetilmiştir, genel onay PROPOSED"
  - "4k-tv sınıfı sabitsiz ve otomotiv için PHP sabiti/BFF yok (ADR-064) — ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
group: mimari
---

# donanım-devices — Kapsam (Tamamlanmış Cihaz Envanteri)

## 1. Giriş ve Çelişki Kaydı

### 1.1 Çelişki kaydı — PROPOSED, APPROVAL REQUIRED

| Öğe | Kaynak | İfade | İhtiyaç | Onay durumu |
|---|---|---|---|---|
| K001 çocuk sayısı 4 | mimari ağaç v2 §2.2 (kanıt: `K001-donanım/index.md:14` evidence satırı) | `donanım-kernel` · `donanım-core` · `donanım-api` · `donanaım-type` | Tamamlanmış cihaz envanteri ayrı bir kademe-2 dalı ister (görev promptu §5 ve §7) | Klasör açılışı ✓ Vault Steward onayı — 2026-10-13 |
| K001 çocuk sayısı 4 | `donanım-anayasa.md` §1 (satır 46, "Çocuk sayısı 4") | Aynı 4'lü sayım | Aynı ihtiyaç | **PROPOSED — APPROVAL REQUIRED**: anayasa metni henüz revize edilmedi; bu dosya anayasa'yı değiştirmez |
| 5. çocuk (bu dal) | görev promptu §5 | `K001-donanım/donanım-devices` | Fiziksel tamamlanmış cihaz envanteri | Klasör + 4 dosya üretimi onaylı; metin revizyonu (anayasa §1 / index §GRUPLAR satırı) **beklemede** |

**Kural (dürüstlük):** Bu dosyada üretilen içerik, onaylı klasör açılışının içeriğidir;
`donanım-anayasa.md`, `K001-donanım/index.md` ve v2 spesifikasyonu **bu görev kapsamında
değiştirilmemiştir** (çift-yazım yasağı). Çelişki saklanmaz, tablo yukarıda kalır.

### 1.2 Bu belge ne yapar, ne yapmaz

- **Yapar:** tamamlanmış cihaz kavramını tanımlar, sınıf ayırt eder, sınıflandırma
  eksenlerini (2↔8↔9↔10) bağlar, cihaz kaydının 26 alanlık şemasını türetir, sınır
  tablosunu ve sahiplik kanıtlarını satır referanslı verir.
- **Yapmaz:** anayasa/index/ADR değiştirmez, gerçek cihaz envanteri iddia etmez (bu
  makinede yok — §9), firmware/çekirdek kodu tanımlamaz (K000/K005 işi), sınıf envanterini
  tekrarlamaz (`donanaım-type` işi).

## 2. Kavram Ayrımı — Sınıf (type) vs Birim (devices)

### 2.1 Tanımlar

| Terim | Tanım | Dal |
|---|---|---|
| **Sınıf / tür** | Bir cihaz kategorisi: ses aygıtı, depolama birimi, ekran, güç kaynağı gibi *neyin* grubu | `donanaım-type` |
| **Tamamlanmış cihaz** | Fiziksel olarak birleştirilmiş, çalışan tek bir cihaz gövdesi: bir masaüstü PC, bir dizüstü, bir telefon — *hangi tekil cihazın* | `donanım-devices` (bu dal) |
| **Bileşen** | Cihazın içindeki parça (GPU, disk, hoparlör) — sınıf ilişkili ama tekil cihaz değil | `donanaım-type` altındaki sınıflar |
| **Envanter kaydı** | Bir cihazın 26 alanlık kaydı — kaydın kendisi | bu dal |

Örnek ayrım: *„Sony ALC ses donanımı"* bir **sınıf** örneğidir; *„Masaüstü PC #1 içindeki
harici ses kartı"* bir **birim** envanter kaydıdır. Aynı fiziksel nesne iki eksende de
görünebilir; bu dal yalnızca ikincisini (birim/kayıt) tutar.

### 2.2 Neden ayrı dal?

1. **Sorgu biçimi farklıdır:** sınıf sorgusu „hangi display türleri var?", cihaz sorgusu
   „elimdeki hangi cihaz, hangi OS tarafından keşfedildi, hangi firmware'e bağlı?"
2. **Yaşam döngüsü farklıdır:** sınıf neredeyse sabittir; cihaz envanteri cihaz
   eklenip çıkıldıkça değişir.
3. **Sahiplik farklıdır:** sınıf tanımı Electronics domaininin (ADR-064 §2), cihaz
   envanteri K001'in anayasa §2'deki „cihaz tipi sınıflandırma" satırının envanter boyutudur.

### 2.3 Kesişen tekil cihaz katalogu (görev promptu §7 §katalog)

Aşağıdaki yedi birim, bu dalın envanter kapsamındaki tipik tamamlanmış cihazlardır;
her biri bir kayıt örneği olarak `donanım-devices-ornekler.md` içinde 26 alanıyla
işlenir. Bu katalog *kapsam tanımıdır*, gerçek envanter iddiası değil (§9):

1. masaüstü PC
2. dizüstü bilgisayar
3. telefon
4. tablet
5. gömülü SBC (tek kartlı bilgisayar)
6. otomotiv multimedya ünitesi
7. monitör ve harici ses kartı (tekil çevre birimi cihazları)

## 3. Sınıflandırma Omurgası — Eksenler 2, 8, 9, 10

Görev promptu §7'nin eksenleri bu dalın sırt omurgasıdır. Her eksen bir soru sorar;
bir cihaz kaydı bu dört sorunun cevabını taşımalı:

| Eksen | Soru | Cevabın gösterdiği yer | İlgili dal |
|---|---|---|---|
| **2 — Tamamlanmış cihaz** | Bu kayıt fiziksel olarak birleştirilmiş tek bir cihazı mı anlatıyor? | envanter kaydının kendisi | bu dal |
| **8 — OS keşfeden** | İşletim sistemi bu cihazı keşfediyor mu, nasıl? | K000-isletim-sistemi (anayasa §2: çekirdek/sürücü → K000) | K000 |
| **9 — Sürücü erişen** | Cihaza sürücü üzerinden erişim var mı, hangi katman? | K000 (sürücü kodu) + K001 (erişim kaydı) | K000/K001 sınırı |
| **10 — Firmware yönetilen** | Cihaz firmware'i tarafından mı yönetiliyor? | K005-firmware (ADR-064 firmware sahipliği) | K005 |

### 3.1 Eksen 2 — tamamlanmış cihaz

Bir kaydın bu dala ait olması için tek şart: kayıt, *tekil bir fiziksel cihaz* hakkındadır.
Sınıf tanımı („SSD'ler NVMe kullanır") bu eksende düşer; tek bir SSD'nin seri numarası
kaydı düşmez (o bileşen kaydıdır, taşınabilir cihaz envanteri değil — bileşen-envanter
kapsamı ADR-064 §2 sınıflarıyla `donanaım-type` tarafında kalır).

### 3.2 Eksen 8 — OS keşfeden

Cihaz, işletim sistemi tarafından keşfedilebilir (masaüstü, dizüstü, telefon) veya
keşfedilmesi Firmware/EEPROM yolundan (otomotiv ünitesi) ya da fiziksel-fark edilebilir
(bir monitör, OS'den bağımsız). Anayasa §2 tablosu çekirdek/sürücü kodunu K000'e verir;
bu dal yalnızca *keşif durumu kaydını* tutar, keşif mekanizmasını değil.

Depo kanıtı: `DeviceDetector.php:21-26` breakpoint tabanlı cihaz keşfi (phone ≤767,
tablet ≤1024, laptop ≤1440, desktop ≤1920, 4k-tv ≤3840) — yani uygulama katmanında
OS/viewpoint keşfi fiilen vardır ve satır referanslıdır.

### 3.3 Eksen 9 — sürücü erişen

Sürücü erişimi, cihazın OS ile temas katmanıdır. Anayasa §2 „Çekirdek sürücü kodu →
HAYIR → K000" der; yani sürücü *kodu* bu dalın konusu değildir. Bu dal şunu kaydeder:
„bu cihaz kaydında sürücü erişimi var mı, hangi kanıtla?" — kanıt yoksa `UNKNOWN`.

### 3.4 Eksen 10 — firmware yönetilen

Firmware yönetimi K005 ve ADR-064'ün firmware başlığının alanıdir. Bu dal yalnızca
„cihaz firmware yönetilen mi (evet/hayır/UNKNOWN)" alanını taşır; firmware içeriği,
versiyonu, güncelleme akışı bu dalda değildir.

### 3.5 Eksenler arası zorunlu bağ

Bir cihaz kaydı ekseni 2'yi karşılamadan (tekil cihaz değilse) 8/9/10 alanları anlamsızdır;
bu yüzden şemada (§4) alan sırası 2 → 8 → 9 → 10 mantığını izler: önce „bu bir cihaz mı",
sonra „OS keşfi", „sürücü", „firmware".

## 4. 26 Alanlık Cihaz Tür Şeması

> **Not (kaynak dürüstlüğü):** „26 alan" adlı şema vault'ta hazır bir SSOT dosyası olarak
> **bulunamadı** (arama: 0 isabet). Şema bu dosyada görev promptu §5'ten türetilmiştir ve
> bu dalın şemasıdır; başka dosyaya atfedilmez. Vault genelinde SSOT ilanı **PROPOSED**.

Alanlar 6 grupta toplanır (26 = 5 + 4 + 6 + 4 + 4 + 3):

### 4.1 Kimlik (5 alan)

| # | Alan | Tip | Bilinmeyen değeri |
|---|---|---|---|
| 1 | `device_id` | string | `UNKNOWN` |
| 2 | `ad` (display name) | string | `NOT PROVIDED` |
| 3 | `marka` | string | `UNKNOWN` (kanıtsız marka yazılmaz) |
| 4 | `model` | string | `UNKNOWN` (kanıtsız model yazılmaz) |
| 5 | `seri_no` | string | `NOT PROVIDED` (envanter hassas, bu makinede yok) |

### 4.2 Sınıflandırma (4 alan — eksen bağları)

| # | Alan | Tip | Not |
|---|---|---|---|
| 6 | `cihaz_sinifi` | enum | ADR-064'ün 5 sınıfı: gömülü · monitör · masaüstü · mobil · otomotiv |
| 7 | `type_dalı` | string | Karşılık gelen `donanaım-type` sınıfı (varsa) |
| 8 | `eksen_8_os_kesfi` | enum | `EVET` / `HAYIR` / `UNKNOWN` |
| 9 | `eksen_9_surucu_erisimi` | enum | `EVET` / `HAYIR` / `UNKNOWN` |

### 4.3 Donanım bileşenleri (6 alan)

| # | Alan | Tip | Not |
|---|---|---|---|
| 10 | `eksen_10_firmware_yonetimi` | enum | `EVET` / `HAYIR` / `UNKNOWN` (K005 sınırı) |
| 11 | `islemci` | string | `UNKNOWN` |
| 12 | `bellek` | string | `UNKNOWN` |
| 13 | `depolama` | string | `UNKNOWN` |
| 14 | `ekran` | string | `UNKNOWN` (monitör sınıfında zorunlu) |
| 15 | `guc_kaynagi` | string | `UNKNOWN` (80plus/ADR-064 güç başlığı sınırı) |

### 4.4 Sistem ve bağlantı (4 alan)

| # | Alan | Tip | Not |
|---|---|---|---|
| 16 | `isletim_sistemi` | string | K000 keşif alanıyla ilişkili |
| 17 | `surucu_katmani` | string | `UNKNOWN` (kod K000'de) |
| 18 | `baglanti_arayuzleri` | list | USB/HDMI/PCIe vb. — kanıt yoksa boş |
| 19 | `fiziksel_yerlesim` | string | masaüstü/dizüstü/gömülü vb. |

### 4.5 Envanter ve durum (4 alan)

| # | Alan | Tip | Not |
|---|---|---|---|
| 20 | `envanter_durumu` | status | `IMPLEMENTED`/`DOCUMENTED`/`PLANNED`/`PROPOSED`/`UNKNOWN`/`VERIFY REQUIRED` |
| 21 | `kanit_seviyesi` | enum | L1 kod · L2 ADR · L3 ölçüm · L4 dış URL (bkz. dogrulama §2) |
| 22 | `kanit_kaynagi` | list | dosya:satır veya URL (yalnız HEAD-200) |
| 23 | `dogrulama_tarihi` | date | `2026-10-13` (bu şemada) |

### 4.6 İlişkiler ve açıklamalar (3 alan)

| # | Alan | Tip | Not |
|---|---|---|---|
| 24 | `bagimliliklar` | list | K000 · K005 · K003 bağımlılık işaretleri |
| 25 | `acik_sorular` | list | `UNKNOWN` alanlar ve doğrulama bekleyenler |
| 26 | `kaynak_notu` | string | kaydın nereden geldiği (bu dalda: yalnız vault/depo) |

### 4.7 Şema kuralları

1. Bilinmeyen hiçbir alan uydurulmaz: `UNKNOWN` · `NOT PROVIDED` · `VERIFY REQUIRED`.
2. `marka`/`model` yalnız elde kanıt varsa doldurulur; bu makinede cihaz envanteri
   olmadığı için tüm örnek kayıtlarda `UNKNOWN`'dır (§9).
3. `envanter_durumu` etiketleri: `IMPLEMENTED` (üretilmiş/kod var) · `DOCUMENTED`
   (yalnız belge) · `PLANNED` (planlı) · `PROPOSED` (onay bekliyor) · `UNKNOWN` ·
   `VERIFY REQUIRED`.
4. Belgede görünmek, fiziksel üretim anlamına gelmez; üretim iddiası ayrı kanıt ister.

## 5. Sınır Tablosu — Bu Dal Kiminle Sinir?

| Bu dal | Bu dal DEĞİL | Sınırın kaynağı (dosya) |
|---|---|---|
| Tekil tamamlanmış cihaz envanteri (birim) | Sınıf/tür sınıflandırması (audio·hdd·ssd·display·power) | `donanaım-type/index.md` (dalın tanımı) |
| Cihaz kaydı ve durum envanteri | Envanterin tek yazıcısı (Kernel inventory single-writer) | `donanım-anayasa.md` §2 (envanter satırı) |
| OS'nin keşfettiği cihazın kaydı (eksen 8) | Ring-0/çekirdek/sürücü kodu | `donanım-anayasa.md` §2 („Çekirdek sürücü kodu HAYIR → K000") |
| Firmware'in yönettiği cihazın kaydı (eksen 10) | Firmware kendisi (versiyon, güncelleme) | ADR-064 (firmware başlığı) + K005 |
| ADR-064'ün 5 cihaz sınıfının envanter boyutu | Sınıfların Electronics domaini sahipliği | ADR-064 §2 (5 sınıf: gömülü·monitör·masaüstü·mobil·otomotiv) |
| Uygulama katmanı cihaz keşif kaydı | Audio DSP/işleme zinciri | K003-anayasa sınırı (anayasa §2 ses işleme satırı) |

## 6. Sahiplik Kanıtları (satır referanslı)

### 6.1 ADR-064 — Electronics platform mimarisi

| Kanıt | İçerik | Kaynak |
|---|---|---|
| 5 cihaz sınıfı | gömülü · monitör · masaüstü · mobil · otomotiv | ADR-064 §2 (`.ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md`) |
| Cihaz ↔ servis matrisi | hangi cihaz sınıfı hangi servisle eşleşir | ADR-064 (matris bölümü) |
| DeviceManager 7 tipi | 7 cihaz tipi sabiti | ADR-064 + `shared/src/Device/DeviceManager.php:29-40` |
| 4k-tv sınıfsız | `4k-tv` sınıfı yok, yalnız breakpoint | ADR-064 uyarısı — ⚠️ VERIFICATION REQUIRED |
| Otomotiv PHP sabitsiz | otomotiv için PHP sabiti/BFF yok | ADR-064 uyarısı — ⚠️ VERIFICATION REQUIRED |

### 6.2 Depo kodu (satır referanslı)

| Dosya | Satır | Kanıt |
|---|---|---|
| `shared/src/Device/DeviceManager.php` | 29-40 | 7 cihaz tipi sabiti (ADR-064 ile eşleşen sayım) |
| `shared/src/Device/DeviceManager.php` | dosya boyu 662 satır | envanter/dao yüzeyinin gerçek boyutu (ölçüm 2026-10-13) |
| `shared/src/Device/DeviceDetector.php` | 21-26 | breakpoint keşfi: phone ≤767 · tablet ≤1024 · laptop ≤1440 · desktop ≤1920 · 4k-tv ≤3840 |
| `shared/src/Device/DeviceDetector.php` | dosya boyu 316 satır | ölçüm 2026-10-13 |
| `shared/src/Device/DeviceRenderer.php` | 63-73 | tierOf: phone / embedded / wide eşlemesi |
| `shared/src/Device/DeviceRenderer.php` | dosya boyu 162 satır | ölçüm 2026-10-13 |
| `shared/src/Device/DeviceCssMap.php` | 8-33 | 7 cihaz CSS sınıfı + 4 view-mode (içinde `car`) |
| `shared/src/Device/DeviceCssMap.php` | dosya boyu 57 satır | ölçüm 2026-10-13 |

### 6.3 Anayasa sınırı

- `donanım-anayasa.md` §2 (satır 53-67 aralığındaki tablo): „Cihaz tipi sınıflandırma →
  EVET → K001" ve „Çekirdek sürücü kodu → HAYIR → K000" satırları bu dalın K001'de,
  sürücü kodunun K000'de kaldığını kanıtlar.
- `donanım-anayasa.md` satır 46: „Çocuk sayısı 4" — bu dalın 5. çocuk olduğuyla
  çelişir; çelişki §1.1 tablosunda PROPOSED olarak kayıtlıdır ve anayasa bu görevde
  değiştirilmemiştir.

## 7. Komşu Dal İlişkileri (özet)

| Komşu | İlişki | Ayrıldığı nokta |
|---|---|---|
| `donanaım-type` | Aynı fiziksel nesneyi farklı eksende görür | sınıf ↔ birim (§2) |
| `donanım-kernel` | Cihazın erişim katmanını tanımlar | kayıt ↔ erişim mekanizması |
| `donanım-core` | Domain nesnelerini taşır | envanter ↔ tek yazıcı/domain |
| `donanım-api` | Cihaz verisini sunar | kayıt ↔ sunum/servis |

## 8. Bağımlılıklar

| Bağımlılık | Yön | Gerekçe |
|---|---|---|
| `K001-donanım` | bu dal → parent | kademe-2 çocuk; anayasa §2 sınıflandırma yetkisi |
| `K000-isletim-sistemi` | bu dal → K000 | eksen 8/9 alanları OS keşfi ve sürücüye referans verir (kod K000'de) |
| `K005-firmware` | referans | eksen 10 alanının tam karşılığı (içerik burada değil) |
| `donanaım-type` | referans | `type_dalı` alanının (9 numaralı alan öncesi, 7. alan) karşılığı |

## 9. Envter Durumu — Bu Makinede Gerçek Cihaz Yok

Bu vault çalışma ortamında (uzak/ölçülen bir donanım listesi yok) fiziksel cihaz
envanteri bulunmamaktadır. Dolayısıyla:

- Hiçbir `marka`/`model`/`seri_no` alanı doldurulmaz — hepsi `UNKNOWN`/`NOT PROVIDED`.
- Cihaz kayıt örüntüleri (`donanım-devices-ornekler.md`) **şablon**tur, envanter değildir.
- Ölçümlü değerler yalnız depo dosya satır sayılarıdır (§6.2, ölçüm 2026-10-13);
  harici ölçüm/performans rakamı uydurulmaz.
- Fiziksel üretim iddiası hiçbir belgede yapılmaz; belgede görünen cihaz adı, üretim
  kanıtı sayılmaz (§4.7 kural 4).

## 10. Kapılar (Gate) — Kapsam Özeti

| Kapı | Kriter | Durum |
|---|---|---|
| Kavram ayrımı | type ↔ devices tablosu §2 | ✓ bu dosyada |
| Eksen omurgası | 2/8/9/10 bağları §3 | ✓ bu dosyada |
| Şema | 26 alan, 6 grup §4 | ✓ türetilmiş, genel onay PROPOSED |
| Sınır | 6 satırlık tablo §5, kaynak dosyalı | ✓ bu dosyada |
| Kanıt | ADR + kod satır referansları §6 | ✓ bu dosyada |
| Çelişki | §1.1 tablosu, APPROVAL REQUIRED açık | ✓ kayıtlı, gizlenmedi |
| Envanter | gerçek kayıt yok, her şey UNKNOWN §9 | ✓ beyan edildi |

## 11. ADR-064'ün 5 Cihaz Sınıfı ile Bu Dalın Kataloğunun Eşlemesi

ADR-064 §2 beş sınıf tanımlar; bu dalın katalog birimleri (§2.3) bu sınıflara
oturur. Eşleme, sınıflandırma eksenleriyle birlikte aşağıdadır. Eşleme *envanter
iddiası değil*, kapsam eşlemesidir:

| ADR-064 sınıfı | Bu dalın tipik birimi | Eksen 2 | Eksen 8 (OS keşif) | Eksen 9 (sürücü) | Eksen 10 (firmware) |
|---|---|---|---|---|---|
| masaüstü | masaüstü PC | ✓ tamamlanmış cihaz | `EVET` (kural olarak) | `EVET` (kural olarak) | `EVET` (BIOS/UEFI) |
| masaüstü | dizüstü bilgisayar | ✓ tamamlanmış cihaz | `EVET` | `EVET` | `EVET` |
| mobil | telefon | ✓ tamamlanmış cihaz | `EVET` | `EVET` | `EVET` |
| mobil | tablet | ✓ tamamlanmış cihaz | `EVET` | `EVET` | `EVET` |
| gömülü | gömülü SBC | ✓ tamamlanmış cihaz | `UNKNOWN` (hedefe göre) | `UNKNOWN` | `EVET` (bootloader) |
| otomotiv | otomotiv multimedya ünitesi | ✓ tamamlanmış cihaz | `UNKNOWN` | `UNKNOWN` | `EVET` |
| monitör | monitör | ✓ tamamlanmış cihaz | `HAYIR` (OS'siz aygıt) | `HAYIR`/`UNKNOWN` | `EVET` (panel MCU) |
| monitör (yakın) | harici ses kartı | ✓ tamamlanmış cihaz | `EVET` (USB keşfi) | `EVET` | `UNKNOWN` |

Notlar:

1. `monitör` sınıfındaki harici ses kartının sınıfsal yeri `donanaım-type` tarafındaki
   audio sınıfıdır; buradaki eşleme yalnız *tamamlanmış cihaz olarak birim* boyutunu
   gösterir (§2.1 ayrımı).
2. `4k-tv` ADR-064'te sınıfsızdır (§6.1) — bu tabloda yer almaz, ⚠️ VERIFICATION REQUIRED.
3. `otomotiv` için PHP sabiti/BFF yoktur (ADR-064 uyarısı) — eksen 8/9 hücreleri
   `UNKNOWN` bırakılmıştır, tahmin edilmemiştir.
4. Tabloların „kural olarak" sütunları *tipik* değerdir; tekil kayıtta kanıt yoksa
   `UNKNOWN` yazılır (§4.7 kural 1).

## 12. Eksen Walkthrough — Örnek Bir Kaydın Geçidi

Bir cihaz kaydı oluşturulurken eksenler sırayla sorulur. Bu bölüm şemanın §4.5'teki
alanlarla nasıl bağlandığını gösterir (kurgusal kayıt değil, akış tanımıdır):

1. **Eksen 2 sorusu:** „Kayıt tekil fiziksel cihaz mı?" → `Cevap EVET` ise dalda kalınır;
   sınıf tanımıysa `donanaım-type`'a gidilir (§2.1).
2. **Eksen 8 sorusu:** „OS keşfediyor mu?" → `eksen_8_os_kesfi` alanına yazılır.
   Kanıt yoksa `UNKNOWN`; kod referansı istenirse `DeviceDetector.php:21-26` (uygulama
   katmanı keşfi) gösterilir ve keşfin ring-0 olmadığı belirtilir.
3. **Eksen 9 sorusu:** „Sürücü erişimi var mı?" → `eksen_9_surucu_erisimi` alanına yazılır.
   Sürücü kodu okunmaz (anayasa §2 → K000); yalnız durum kaydedilir.
4. **Eksen 10 sorusu:** „Firmware yönetiyor mu?" → `eksen_10_firmware_yonetimi` alanına
   yazılır; firmware içeriği K005'e aittir (§5 sınır satırı).
5. **Kapanış:** 4 cevap + kimlik + kanıt alanları birleşir; her boş alan
   `UNKNOWN`/`NOT PROVIDED` olarak açıkça bırakılır, sessizce atlanmaz.

## 13. Sözlük

| Terim | Bu dalda anlamı |
|---|---|
| tamamlanmış cihaz | fiziksel olarak birleştirilmiş, çalışan tek cihaz (eksen 2) |
| cihaz sınıfı | ADR-064'ün 5 sınıfından biri (gömülü·monitör·masaüstü·mobil·otomotiv) |
| tür/type | `donanaım-type` dalındaki sınıf kategorisi |
| envanter kaydı | 26 alanlık cihaz kaydı (§4) |
| kanıt seviyesi | L1 kod · L2 ADR · L3 ölçüm · L4 dış URL (dogrulama §2) |
| envanter durumu | IMPLEMENTED/DOCUMENTED/PLANNED/PROPOSED/UNKNOWN/VERIFY REQUIRED |
| keşif (eksen 8) | OS'nin cihazı görmesi; kodu değil, durumu kaydedilir |
| tek yazıcı | envanterin tek yazıcısı Kernel inventory'dir (anayasa §2) — bu dal onu tekrarlamaz |

## 14. Kapsam Dışı (Explicit Out-of-Scope)

1. Çekirdek/sürücü kodu ve ring-0 katmanı → K000 (anayasa §2).
2. Firmware içeriği, versiyonu, güncelleme akışı → K005 / ADR-064 firmware başlığı.
3. Ses işleme DSP zinciri → K003 (anayasa §2 ses satırı).
4. Sınıf/tür envanteri (audio·hdd·ssd·display·power tanımları) → `donanaım-type`.
5. Envanterin tek yazıcısı olmak → Kernel inventory (anayasa §2).
6. anayasa/index/ADR metinlerinin revizyonu → Vault Steward (§1.1, bu görevde yapılmadı).
7. Gerçek marka/model envanteri → bu makinede yok (§9); uydurma yasak.
8. Harici ölçüm/performans rakamları → yalnız HEAD-200 URL ve depo satır sayıları geçerli.

## 15. Değişiklik Geçmişi (bu dal)

| Tarih | Değişiklik | Onay |
|---|---|---|
| 2026-10-13 | Klasör + 4 dosya açıldı (index · kapsam · dogrulama · ornekler) | ✓ Vault Steward — klasör açılışı |
| 2026-10-13 | 26 alanlık şema bu dosyada türetildi | PROPOSED — Vault Steward genel onayı bekliyor |
| 2026-10-13 | anayasa §1 / v2 §2.2 metin revizyonu (çocuk 4 → 5) | **PROPOSED — APPROVAL REQUIRED** (yapılmadı) |

## 16. Birim Katalogu — Kapsam Tanımları (detay)

§2.3'teki yedi birim, bu bölümde kapsam boyutuyla açılır. Her birim için: ne tür bir
cihazdır, hangi ADR-064 sınıfına oturur, hangi eksenler tipiktir, hangi alanlar
envanterde kritiktir. **Hiçbiri bu makinede fiziksel olarak var değildir; hepsi
kapsam tanımıdır** (§9):

### 16.1 Masaüstü PC

- **Tanım:** Sabit yerleşimli, kullanıcı tarafından kurulmuş/edilmiş tam bir kişisel bilgisayar.
- **ADR-064 sınıfı:** `masaüstü`.
- **Tipik eksen değerleri:** 2 ✓ · 8 `EVET` · 9 `EVET` · 10 `EVET` (BIOS/UEFI).
- **Kritik alanlar:** 11 işlemci · 12 bellek · 13 depolama · 15 güç kaynağı · 18 bağlantı.
- **Depo bağlantısı:** `DeviceManager.php:29-40` masaüstü dahil 7 tipi sayar; `DeviceDetector.php:21-26` desktop breakpoint'i ≤1920.

### 16.2 Dizüstü Bilgisayar

- **Tanım:** Ekran + klavye bütünleşik, bataryalı taşınabilir tam bilgisayar.
- **ADR-064 sınıfı:** `masaüstü` (kategori genişliği) — ayrı bir ADR-064 sınıfı yoktur; ⚠️ VERIFICATION REQUIRED (sınıf eşlemesi vault'ta kesinleşmemiş).
- **Tipik eksen değerleri:** 2 ✓ · 8 `EVET` · 9 `EVET` · 10 `EVET`.
- **Kritik alanlar:** 14 ekran · 15 güç kaynağı (batarya) · 19 fiziksel yerleşim.
- **Depo bağlantısı:** `DeviceDetector.php:21-26` laptop breakpoint'i ≤1440.

### 16.3 Telefon

- **Tanım:** Mobil, dokunmatik ekranlı, hücresel bağlantılı tam cihaz.
- **ADR-064 sınıfı:** `mobil`.
- **Tipik eksen değerleri:** 2 ✓ · 8 `EVET` · 9 `EVET` · 10 `EVET`.
- **Kritik alanlar:** 14 ekran · 16 işletim sistemi · 18 bağlantı.
- **Depo bağlantısı:** `DeviceDetector.php:21-26` phone breakpoint'i ≤767; `DeviceRenderer.php:63-73` phone tier.

### 16.4 Tablet

- **Tanım:** Dokunmatik ekranlı, klavyesiz mobil tam cihaz.
- **ADR-064 sınıfı:** `mobil`.
- **Tipik eksen değerleri:** 2 ✓ · 8 `EVET` · 9 `EVET` · 10 `EVET`.
- **Kritik alanlar:** 14 ekran · 19 fiziksel yerleşim.
- **Depo bağlantısı:** `DeviceDetector.php:21-26` tablet breakpoint'i ≤1024.

### 16.5 Gömülü SBC (tek kartlı bilgisayar)

- **Tanım:** Tek kart üzerinde komple bilgisayar; gövde/ekran/klavye zorunlu değil, hedef sisteme gömülür.
- **ADR-064 sınıfı:** `gömülü`.
- **Tipik eksen değerleri:** 2 ✓ · 8 `UNKNOWN` (hedefe göre) · 9 `UNKNOWN` · 10 `EVET` (bootloader).
- **Kritik alanlar:** 10 firmware · 17 sürücü katmanı · 18 bağlantı.
- **Depo bağlantısı:** `DeviceRenderer.php:63-73` embedded tier; `DeviceCssMap.php:8-33` device CSS sınıfları arasında embedded.

### 16.6 Otomotiv Multimedya Ünitesi

- **Tanım:** Araç içi gömülü multimedya/infotainment birimi.
- **ADR-064 sınıfı:** `otomotiv`.
- **Tipik eksen değerleri:** 2 ✓ · 8 `UNKNOWN` · 9 `UNKNOWN` · 10 `EVET`.
- **Kritik alanlar:** 10 firmware · 19 yerleşim (araç içi).
- **Depo bağlantısı:** `DeviceCssMap.php:8-33` view-mode `car`; ADR-064 uyarısı: otomotiv için PHP sabiti/BFF yok — ⚠️ VERIFICATION REQUIRED.

### 16.7 Monitör ve Harici Ses Kartı

- **Monitör:** OS'siz görüntüleme birimi; sınıf `monitör`. Eksenler: 2 ✓ · 8 `HAYIR` · 9 `HAYIR`/`UNKNOWN` · 10 `EVET` (panel MCU).
- **Harici ses kartı:** USB dış kablosu ile eklenen ses birimi; sınıf yönü `donanaım-type` audio. Eksenler: 2 ✓ · 8 `EVET` (USB keşfi) · 9 `EVET` · 10 `UNKNOWN`.
- **Kritik alanlar (monitör):** 14 ekran · 18 bağlantı; **(ses kartı):** 18 bağlantı · 17 sürücü katmanı.
- **Depo bağlantısı:** `DeviceCssMap.php:8-33` display CSS sınıfı; `DeviceRenderer.php:63-73` wide tier.

## 17. Alan ↔ Eksen Çapraz Referansı

| Eksen | Şemanın karşılık gelen alanları | Boş bırakma kuralı |
|---|---|---|
| 2 — tamamlanmış cihaz | 1-5 kimlik + 19 fiziksel yerleşim + 26 kaynak notu | kayıt tekil cihaz değilse bu dala girmez (§12 adım 1) |
| 8 — OS keşfeden | 8 `eksen_8_os_kesfi` + 16 işletim sistemi | kanıt yoksa `UNKNOWN`, tahmin yok |
| 9 — sürücü erişen | 9 `eksen_9_surucu_erisimi` + 17 sürücü katmanı | sürücü kodu okunmaz, yalnız durum |
| 10 — firmware yönetilen | 10 `eksen_10_firmware_yonetimi` + 24 bağımlılıklar (K005) | firmware içeriği bu dalda değil |

## 18. Doğrulama Yönlendirmesi (özet — tam kapsam dogrulama dosyasında)

| İddia türü | Kabul edilen kanıt | Reddedilen kanıt |
|---|---|---|
| Depo kodu iddiası | dosya + satır referansı, ölçüm tarihli | dosyasız genel cümle |
| ADR iddiası | ADR dosya adı + bölüm | „ADR'de yazıyordu" (kaynaksız) |
| Dış standart iddiası | HEAD 200/202 URL, tarih damgalı | HEAD alınamayan URL (403/SSL vb.) |
| Envanter iddiası | bu makinede yok → yasak | uydurma marka/model/ölçüm |

## 19. Sık Sorulan İhtilaflar (bu dalın kararları)

| İhtilaf | Karar | Gerekçe |
|---|---|---|
| Bir SSD kaydı bu dalda mı? | Hayır — bileşen kaydı `donanaım-type` tarafıdır; yalnız taşıdığı cihaz tamamlanmış cihazsa o cihazın kaydı burada | §2.1 ayrımı (birim ↔ sınıf/bileşen) |
| `DeviceDetector` cihaz keşfi OS keşfi sayılır mı? | Hayır — uygulama katmanı viewpoint keşfidir; ring-0 OS keşfi K000'dir | anayasa §2 (çekirdek → K000) + kapsam §3.2 |
| `4k-tv` bu dalın kataloguna girer mi? | Girmez — ADR-064'te sınıfsızdır; kayıt açılırsa `cihaz_sinifi: UNKNOWN` | ADR-064 uyarısı (kapsam §6.1) |
| Otomotiv ünitesi PHP tarafında temsilsiz — kayıt açılır mı? | Kapsam olarak evet; eksen 8/9 alanları `UNKNOWN` kalır, tahmin yazılmaz | ADR-064 uyarısı (kapsam §16.6) |
| Bu dal envanterin tek yazıcısı mı? | Hayır — tek yazıcı Kernel inventory'dir; bu dal kayıtları referanslar | anayasa §2 + kapsam §5 |
| 26 alan şeması nereden geldi? | Görev promptu §5'ten bu dalda türetildi; vault'ta hazır SSOT yok, genel onay PROPOSED | kapsam §4 giriş notu |
| Anayasa „Çocuk sayısı 4" ne olacak? | Metin revizyonu Vault Steward onayı bekliyor; bu görevde anayasa değiştirilmedi | kapsam §1.1 (çift-yazım yasağı) |

## 20. Kapı Bağlantıları (komşu dosyalara)

| Kapı | Dosya | Ne bekler |
|---|---|---|
| Kanıt kapıları | `donanım-devices-dogrulama.md` | iddia→kanıt matrisi, HEAD URL tablosu, UNKNOWN kapıları |
| Kod örnekleri | `donanım-devices-ornekler.md` | satır referanslı gerçek kod, 26 alanlık boş kayıt örüntüsü |
| Dizin iskeleti | `index.md` | çocuk kataloğu, GRUPLAR, çelişki özeti |

## 21. Ölçüm Kayıtları (bu dosyanın dayandığı sayılar)

Aşağıdaki sayılar 2026-10-13'te depodan `(Get-Content dosya).Count` ile ölçülmüştür;
yalnızca depo dosya satır sayılarıdır, harici ölçüm değildir (§9 kuralı):

| Dosya | Ölçülen satır | Kullanım |
|---|---|---|
| `shared/src/Device/DeviceManager.php` | 662 | kanıt yüzeyi büyüklüğü |
| `shared/src/Device/DeviceDetector.php` | 316 | keşif mantığı büyüklüğü |
| `shared/src/Device/DeviceRenderer.php` | 162 | sunum/tier mantığı |
| `shared/src/Device/DeviceCssMap.php` | 57 | CSS sınıf haritası |
| `donanım-anayasa.md` | vault dosyası (satır referanslı okundu) | §1 satır 46, §2 tablo 53-67 |
| `ADR-064-...md` | vault dosyası (bölüm referanslı okundu) | §2 5 sınıf + matris + 7 tip |

Ölçüm dışı hiçbir sayı (performans, gecikme, güç, sıcaklık vb.) bu dalda yazılmaz;
gerekirse ayrı ölçüm kanıtı (L3) ve tarih ister.

## 22. Kapanış Özeti

1. Bu dal, `donanaım-type`'ın karşıtı olan **tamamlanmış cihaz (birim) envanteri**dir (§2).
2. Sınıflandırma omurgası görev promptu §7 eksenleridir: **2 ↔ 8 ↔ 9 ↔ 10** (§3).
3. Şema 26 alan / 6 gruptur; vault'ta hazır SSOT bulunmadığı için bu dalda türetildi,
   genel onayı **PROPOSED**'dır (§4).
4. Sınır tablosu 6 satır, hepsi kaynak dosyalıdır (§5); sahiplik kanıtları ADR-064 +
   `Device*` kod satır referanslıdır (§6).
5. Çelişki (anayasa §1 / v2 §2.2 „çocuk 4") gizlenmemiştir: klasör açılışı onaylı,
   metin revizyonu **APPROVAL REQUIRED** (§1.1).
6. Bu makinede gerçek cihaz envanteri yoktur; tüm kayıt örüntüleri şablondur, her
   `marka`/`model` alanı `UNKNOWN`'dır (§9).

## §OS × DİL ERİŞİM MATRİSİ (devices)

> **Kapsam:** Cihaz tespiti katmanı — hangi dilde cihaz tespiti repo'da **IMPLEMENTED**?
> Kanıt: `shared/src/Device/DeviceDetector.php` (316 satır · `detect()` L57–92) ·
> `shared/src/Device/DeviceManager.php` (654 satır · L29–40) · istemci JS'i
> `assets.coremusic.net/js/`. Repo dil sayımı 2026-10-14 (`.git`/`vendor`/`node_modules` hariç).

### §OSD.1 Erişim matrisi (13 dil × 3 OS)

| Dil | Windows | macOS | Linux |
|---|---|---|---|
| JavaScript/Node | IMPLEMENTED — istemci cihaz tespiti: `core/breakpoints.js` · `device-layout-updater.js` · `device-loader.js` (16 isabet; `process.platform` 0 isabet) | Aynı istemci betikleri — viewport tabanlı, OS-bağımsız | Aynı istemci betikleri |
| PHP | IMPLEMENTED — `DeviceDetector::detect()` (DeviceDetector.php L57–92) + `DeviceManager` (DeviceManager.php L29–40) · OS algılama `php_uname('s')` (DesktopBff.php:35) | Aynı — sunucu tarafı tespit OS-bağımsız | Aynı — Linux hedef ortam |
| C | UNKNOWN — repo'da `*.c` 0 dosya (ölçüm 2026-10-14) | UNKNOWN — `*.c` 0 | UNKNOWN — `*.c` 0 |
| Python | UNKNOWN — üretim 0; yalnız vault betiği `.ai/scripts/fix-mojibake.py` | Aynı | Aynı |
| Go | UNKNOWN — repo'da `*.go` 0 dosya | UNKNOWN — `*.go` 0 | UNKNOWN — `*.go` 0 |
| Rust | UNKNOWN — repo'da `*.rs` 0 dosya | UNKNOWN — `*.rs` 0 | UNKNOWN — `*.rs` 0 |
| C# | UNKNOWN — repo'da `*.cs` 0 dosya | UNKNOWN — `*.cs` 0 | UNKNOWN — `*.cs` 0 |
| Bash | UNKNOWN — repo'da `*.sh` 0 dosya | UNKNOWN — `*.sh` 0 | UNKNOWN — `*.sh` 0 |
| PowerShell | UNKNOWN — yalnız vault betikleri (`.ai/scripts/*.ps1` — 6 dosya), üretim 0 | Aynı | Aynı |
| Java | UNKNOWN — repo'da `*.java` 0 dosya | UNKNOWN — `*.java` 0 | UNKNOWN — `*.java` 0 |
| Swift | UNKNOWN — repo'da `*.swift` 0 dosya | UNKNOWN — `*.swift` 0 | UNKNOWN — `*.swift` 0 |
| Kotlin | UNKNOWN — repo'da `*.kt` 0 dosya | UNKNOWN — `*.kt` 0 | UNKNOWN — `*.kt` 0 |
| C++ | UNKNOWN — repo'da `*.cpp`/`*.h` 0 dosya | UNKNOWN — 0 | UNKNOWN — 0 |

> **Not (§OSD.1/PHP):** cihaz tespiti satırı yalnız IMPLEMENTED kanıtını taşır; PHP OS
> kancası (`PHP_OS_FAMILY` birinci taraf kodda 0 isabet, §G7.4) bu dosyada ayrı satır
> almaz — PHP kanca tablosu `donanım-kod-ornekleri.md` §OSK'dadır.

### §OSD.2 Öneri ve risk notları

**Öncelikli öneri (PHP/JS):** cihaz tespiti zaten **iki dilde IMPLEMENTED** — sunucu tarafı
PHP (`DeviceDetector`/`DeviceManager` + `php_uname('s')`), istemci tarafı Vanilla JS
(breakpoints/device-layout-updater). Yeni OS×dil tespit kodu için ek dile gerek yok;
yalnız OS algılama kancası PHP'de `php_uname('s')` ile açıktır (DesktopBff.php:35).

**Risk notları:**
- Diğer 11 dil repo üretim kodunda **0 dosya** → `UNKNOWN`; PLANNED'e çevrilebilmesi için
  ADR + kod kanıtı gerekir (uydurma IMPLEMENTED yazılmaz).
- `sysctl hw.memsize` (macOS) — Apple dokümantasyonu 404 (§K4 R1) → ⚠️ VERIFICATION REQUIRED.
- `__APPLE__` ön-tanımlı makrosu — doğrulanmış birincil URL yok (§K4 R4) → ⚠️ VERIFICATION REQUIRED.
- `getifaddrs` (macOS ağ arayüzü) — Apple arşiv sayfası 404 (§K4 R8) → ⚠️ VERIFICATION REQUIRED.
- WASAPI (Windows) · CoreAudio (macOS) · ALSA (Linux) sahiplikleri K000-isletim-sistemi ile
  paylaşılır — bu dosya yalnız çağrı şablonu gösterir (§PLATFORM MATRİSİ P5 sınır notu).

### §OSD.3 Kaynak atıfı

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
