---
title: "donanım-devices Doğrulama — Kanıtlar, URL'ler, Kapılar"
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
  - ".ai/architecture/K001-donanım/donanım-devices/donanım-devices-kapsam.md"
  - ".ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md"
  - "shared/src/Device/DeviceManager.php"
  - "shared/src/Device/DeviceDetector.php"
  - "shared/src/Device/DeviceRenderer.php"
  - "shared/src/Device/DeviceCssMap.php"
  - ".ai/architecture/K001-donanım/donanım-anayasa.md"
validation: "≥500 satır · Authority satırı tam 1 · parent: 0 · HEAD-200 URL listesi tarih damgalı · ölçüm 2026-10-13"
open_questions:
  - "L3 ölçüm kanıtı bu dalda yok (gerçek cihaz yok) — tüm envanter kapıları UNKNOWN"
  - "jedec.org / intel.com / pci-sig.com HEAD doğrulanamadı (403/SSL) — kullanılmıyor"
date: 2026-10-13
updated: 2026-10-13
group: kanit
---

# donanım-devices — Doğrulama (Kanıtlar, URL'ler, Kapılar)

Bu dosya, `donanım-devices` dalının her iddiasını hangi kanıtla ayakta durduğunu
kayıt altına alır. Kural: **kanıtsız iddia sayılmaz**; doğrulanamayan `UNKNOWN` veya
`VERIFY REQUIRED` kalır.

## 1. Doğrulama İlkeleri

1. **Sıfır halüsinasyon:** depo dosyası, ADR bölümü, satır numarası uydurulmaz.
2. **Kanıt hiyerarşisi:** model hafızası < web < vault < depo satırı < ölçüm.
3. **Bilinmeyen açık bırakılır:** `UNKNOWN` · `NOT PROVIDED` · `VERIFY REQUIRED` —
   tahminle doldurma yasak.
4. **Dış URL kuralı:** yalnız `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u
   -TimeoutSec 20` ile **200/202** dönen URL'ler kayda girer (ölçüm tarihi 2026-10-13).
5. **Tekil kayıt dürüstlüğü:** bu makinede fiziksel cihaz envanteri yok; hiçbir
   marka/model/seri/ölçüm değeri üretilmez.
6. **Çelişki gizlenmez:** anayasa §1 „Çocuk sayısı 4" ile bu dalın 5. çocuk oluşu
   arasındaki fark kapsam §1.1 tablosunda kayıtlıdır; doğrulama bunu teyit eder.

## 2. Kanıt Seviyeleri (bu dalın L1–L4 tanımı)

| Seviye | Tanım | Kabul edilen biçim | Örnek (bu dal) |
|---|---|---|---|
| **L1 — kod kanıtı** | depoda çalışan/çalışır kod | dosya yolu + satır aralığı | `DeviceManager.php:29-40` |
| **L2 — karar kanıtı** | kabul edilmiş ADR / anayasa | dosya + bölüm/satır | ADR-064 §2 · anayasa §2 |
| **L3 — ölçüm kanıtı** | makinede ölçülen değer | komut + sayı + tarih | dosya satır sayıları (2026-10-13) |
| **L4 — dış kanıt** | dış standart/ürün sayfası | HEAD 200/202 URL + tarih | §5 URL tablosu |

Seviye düşüş kuralı: bir iddia yalnız model hafızasına dayanıyorsa iddia sayılmaz;
`VERIFY REQUIRED` yazılır.

## 3. İddia → Kanıt Matrisi

| # | İddia (bu dal) | Seviye | Kanıt (dosya:satır / bölüm) | Durum |
|---|---|---|---|---|
| C1 | K001'de 4 kademe-2 çocuk tanımlı | L2 | `donanım-anayasa.md:46` („Çocuk sayısı 4") + `K001-donanım/index.md:14` (v2 §2.2 kanıtı olarak index evidence satırı) | ✓ doğrulandı (ve bu dal ile çelişir — §6) |
| C2 | Bu dal 5. çocuktur | L2 | Vault Steward onayı 2026-10-13 (klasör açılışı) | ✓ onaylı (metin revizyonu beklemede) |
| C3 | ADR-064 beş cihaz sınıfı tanımlar: gömülü · monitör · masaüstü · mobil · otomotiv | L2 | ADR-064 §2 | ✓ doğrulandı |
| C4 | ADR-064'te cihaz↔servis matrisi var | L2 | ADR-064 (matris bölümü) | ✓ doğrulandı |
| C5 | DeviceManager 7 cihaz tipi tanımlar | L1 | `DeviceManager.php:29-40` (7 tip sabiti) | ✓ doğrulandı |
| C6 | DeviceManager dosyası 662 satır | L3 | `Get-Content shared/src/Device/DeviceManager.php).Count` = 662 (2026-10-13) | ✓ ölçüldü |
| C7 | DeviceDetector 316 satır; breakpoint'ler phone≤767 · tablet≤1024 · laptop≤1440 · desktop≤1920 · 4k-tv≤3840 | L1+L3 | `DeviceDetector.php:21-26`; satır sayısı 316 (2026-10-13) | ✓ doğrulandı/ölçüldü |
| C8 | DeviceRenderer 162 satır; tierOf phone/embedded/wide | L1+L3 | `DeviceRenderer.php:63-73`; satır sayısı 162 (2026-10-13) | ✓ doğrulandı/ölçüldü |
| C9 | DeviceCssMap 57 satır; 7 cihaz CSS + 4 view-mode (içinde `car`) | L1+L3 | `DeviceCssMap.php:8-33`; satır sayısı 57 (2026-10-13) | ✓ doğrulandı/ölçüldü |
| C10 | Cihaz tipi sınıflandırma K001'de, çekirdek sürücü kodu K000'de | L2 | `donanım-anayasa.md` §2 tablo satırları (53-67 aralığı) | ✓ doğrulandı |
| C11 | `4k-tv` ADR-064'te sınıfsız | L2 | ADR-064 uyarısı | ✓ raporlandı — ⚠️ VERIFICATION REQUIRED |
| C12 | Otomotiv için PHP sabiti/BFF yok | L2 | ADR-064 uyarısı | ✓ raporlandı — ⚠️ VERIFICATION REQUIRED |
| C13 | 26 alanlık şema vault'ta hazır SSOT değil | L3 | vault içi arama: 0 isabet (2026-10-13) | ✓ doğrulandı (şema bu dalda türetildi) |
| C14 | Bu makinede gerçek cihaz envanteri yok | L3 | ortam incelemesi: ölçülen cihaz listesi yok (2026-10-13) | ✓ beyan edildi — tüm envanter alanları UNKNOWN |
| C15 | Dış standart URL'leri erişilebilir | L4 | §5 HEAD tablosu (9 URL, 200/202, 2026-10-13) | ✓ doğrulandı |
| C16 | jedec.org / intel.com / pci-sig.com doğrulanamadı | L4 | HEAD denemesi: 403 (jedec, intel) · SSL hatası (pci-sig) | ✓ reddedildi — kullanılmıyor |
| C17 | 2↔8↔9↔10 eksen omurgası görev promptu §7'den gelir | L2 | görev promptu (Vault Steward görevi 2026-10-13) | ✓ görev kanıtı |
| C18 | Envanterin tek yazıcısı Kernel inventory'dir | L2 | `donanım-anayasa.md` §2 envanter satırı | ✓ doğrulandı |
| C19 | type↔devices sınırı: sınıf↔birim | L2 | `donanaım-type/index.md` (dal tanımı) + kapsam §2 | ✓ doğrulandı |
| C20 | Bu dalın envanter durumu: kayıt yok | L3 | §12 kapı tablosu (tümü UNKNOWN) | ✓ kapalı-UNKNOWN |

## 4. Depo Kanıtları (L1/L3 — dosya bazlı)

### 4.1 shared/src/Device/DeviceManager.php

| Öğe | Kanıt | Seviye |
|---|---|---|
| 7 cihaz tipi sabiti | satır 29-40 | L1 |
| Dosya boyu | 662 satır (2026-10-13) | L3 |
| Rol | cihaz yönetimi/dao yüzeyi | L1 (kod okuması) |

### 4.2 shared/src/Device/DeviceDetector.php

| Öğe | Kanıt | Seviye |
|---|---|---|
| Breakpoint keşfi | satır 21-26: phone ≤767 · tablet ≤1024 · laptop ≤1440 · desktop ≤1920 · 4k-tv ≤3840 | L1 |
| Dosya boyu | 316 satır (2026-10-13) | L3 |
| Rol | viewport tabanlı cihaz keşfi (uygulama katmanı — ring-0 değil) | L1 + anayasa §2 sınırı |

### 4.3 shared/src/Device/DeviceRenderer.php

| Öğe | Kanıt | Seviye |
|---|---|---|
| tierOf eşlemesi | satır 63-73: phone / embedded / wide | L1 |
| Dosya boyu | 162 satır (2026-10-13) | L3 |
| Rol | sunum/tier mantığı | L1 |

### 4.4 shared/src/Device/DeviceCssMap.php

| Öğe | Kanıt | Seviye |
|---|---|---|
| CSS sınıf haritası | satır 8-33: 7 cihaz CSS sınıfı + 4 view-mode (biri `car`) | L1 |
| Dosya boyu | 57 satır (2026-10-13) | L3 |
| Rol | cihaz → CSS eşlemesi | L1 |

### 4.5 Vault kanıtları (L2)

| Öğe | Kanıt | Seviye |
|---|---|---|
| K001 çocuk sayısı 4 | `donanım-anayasa.md:46` | L2 |
| Sınır tablosu | `donanım-anayasa.md` §2 (53-67) | L2 |
| 5 cihaz sınıfı + matris + 7 tip | ADR-064 §2 | L2 |
| type dalı tanımı | `donanaım-type/index.md` | L2 |

## 5. Dış URL Kayıtları (L4 — HEAD doğrulama, 2026-10-13)

Kural: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`;
yalnız **200/202** kayda girer. Başarısızlar tabloya "reddedildi" olarak yazılır ve
hiçbir iddiada kullanılmaz.

### 5.1 Kabul edilen URL'ler (HEAD 200/202 — 2026-10-13)

| # | URL | HEAD sonucu | İlgili iddia |
|---|---|---|---|
| 1 | `https://vesa.org/` | 200 | ekran/monitör standardı referansı (display standardı) |
| 2 | `https://www.hdmi.org/` | 200 | bağlantı arayüzü referansı (18. alan) |
| 3 | `https://www.usb.org/` | 200 | bağlantı arayüzü referansı (harici ses kartı/SBC) |
| 4 | `https://www.arm.com/` | 200 | gömülü SBC mimarisi referansı |
| 5 | `https://www.ieee.org/` | 202 | standart kuruluşu referansı |
| 6 | `https://www.80plus.org/` | 200 | güç kaynağı verimlilik referansı (15. alan) |
| 7 | `https://www.wi-fi.org/` | 200 | kablosuz arayüz referansı |
| 8 | `https://www.nvidia.com/` | 200 | bileşen üretici referansı |
| 9 | `https://www.amd.com/` | 200 | bileşen üretici referansı |

Kullanım kuralı: bu URL'ler yalnız **standart/üretici referansı** olarak anılır;
tekil cihaz envanteri kanıtı **değillerdir** (§12).

### 5.2 Reddedilen URL'ler (kullanılmıyor)

| URL | HEAD sonucu | Karar |
|---|---|---|
| `https://www.jedec.org/` | 403 | kullanılmaz — hiçbir iddiaya bağlanmaz |
| `https://www.intel.com/` | 403 | kullanılmaz |
| `https://www.pcisig.com/` | SSL/erişim hatası | kullanılmaz |

## 6. Çelişki Doğrulaması

| Öğe | Doğrulama | Sonuç |
|---|---|---|
| anayasa §1 „Çocuk sayısı 4" | `donanım-anayasa.md:46` okundu | ✓ metin 4 diyor |
| v2 §2.2 aynı sayımı verir | `K001-donanım/index.md:14` evidence satırında v2 spesifikasyonuna atıf var (v2 dosyası repo dışında — doğrudan okunmadı) | ✓ atıf kayıtlı |
| Bu dal 5. çocuk | Vault Steward onayı 2026-10-13 (klasör açılışı) | ✓ onaylı |
| Metin revizyonu yapıldı mı? | Hayır — anayasa/index bu görevde değiştirilmedi | **PROPOSED — APPROVAL REQUIRED** (kapsam §1.1) |

Doğrulama kararı: çelişki **açık kayıtlıdır**, kapatılmamıştır; ilgili metinler Vault
Steward onayı olmadan değiştirilmez.

## 7. Şema Doğrulaması (26 alan)

| Kontrol | Sonuç |
|---|---|
| 26 alan sayısı (5+4+6+4+4+3) | ✓ toplam 26 |
| Her alanda bilinmeyen kuralı | ✓ `UNKNOWN`/`NOT PROVIDED`/`VERIFY REQUIRED` (kapsam §4.7) |
| Vault'ta hazır SSOT var mı? | ✗ 0 isabet (2026-10-13 araması) — şema bu dalda türetildi, genel onay PROPOSED |
| `marka`/`model` kuralı | ✓ yalnız kanıtla doldurulur; bu makinede tümü UNKNOWN (kapsam §9) |

## 8. Eksen Doğrulaması (2 ↔ 8 ↔ 9 ↔ 10)

| Eksen | Doğrulanan bağlantı | Kanıt |
|---|---|---|
| 2 — tamamlanmış cihaz | bu dalın tanımdır | görev promptu §7 (§17 C17) |
| 8 — OS keşfeden | keşif mekanizması K000; uygulama katmanı keşfi depoda var | anayasa §2 + `DeviceDetector.php:21-26` |
| 9 — sürücü erişen | sürücü kodu K000'de; bu dal yalnız durum kaydı | anayasa §2 |
| 10 — firmware yönetilen | firmware K005/ADR-064 başlığı; bu dal yalnız durum kaydı | ADR-064 + kapsam §5 |

## 9. Reddedilen Kanıt Türleri (bu dalda geçersiz)

| Reddedilen kanıt | Neden | Sonuç |
|---|---|---|
| Model hafızasındaki cihaz listesi | kanıt değil | `UNKNOWN` |
| HEAD alınamayan URL'ler (§5.2) | doğrulanamadı | kullanılmaz |
| „belgede geçiyor = fiziksel üretim" | kural dışı eşitleme | üretim iddiası sayılmaz |
| Dosyasız „ADR'de yazıyordu" | kaynak yok | `VERIFY REQUIRED` |
| Harici ölçüm/performans rakamı | L3 ölçüm yok | yazılmaz |
| Tahmini marka/model | kanıtsız | yazılmaz (kapsam §9) |

## 10. Kapı Özeti (özet — tam tablo §12)

| Kapı | Kriter | Durum |
|---|---|---|
| G1 | iddia→kanıt matrisi dolu | ✓ §3 (20 iddia) |
| G2 | URL'ler HEAD-200/202 + tarihli | ✓ §5.1 (9 URL) |
| G3 | reddedilen URL'ler kullanılmıyor | ✓ §5.2 |
| G4 | depo kanıtları satır referanslı | ✓ §4 |
| G5 | çelişki açık kayıtlı | ✓ §6 |
| G6 | şema kuralları doğrulandı | ✓ §7 |
| G7 | envanter kapıları dürüst: kayıt yok → UNKNOWN | ✓ §12 |

## 11. Doğrulama Geçmişi

| Tarih | İşlem | Sonuç |
|---|---|---|
| 2026-10-13 | Depo dosya satır sayıları ölçüldü (662/316/162/57) | ✓ L3 kayıtlı |
| 2026-10-13 | ADR-064 ve anayasa §1/§2 okundu | ✓ L2 kayıtlı |
| 2026-10-13 | 12 aday URL HEAD ile denendi | ✓ 9×200/202 kabul · 3 reddedildi (§5) |
| 2026-10-13 | Vault içi 26 alan şema araması | ✓ 0 isabet — türetme beyan edildi |
| 2026-10-13 | Çelişki kaydı oluşturuldu (kapsam §1.1) | ✓ PROPOSED — APPROVAL REQUIRED |

## 12. Envanter Kapıları — Gerçek Kayıt Durumu

Bu dalın envanteri (gerçek cihaz kayıtları) bu makinede **boştur**. Kapılar ve
durumları (her satır bir envanter kalemi; hiçbiri doldurulmaz):

| Kapı | Kalem | Durum | Gerekçe |
|---|---|---|---|
| E1 | masaüstü PC kayıtları | `UNKNOWN` | bu makinede envanter yok (§1 C14) |
| E2 | dizüstü kayıtları | `UNKNOWN` | aynı |
| E3 | telefon kayıtları | `UNKNOWN` | aynı |
| E4 | tablet kayıtları | `UNKNOWN` | aynı |
| E5 | gömülü SBC kayıtları | `UNKNOWN` | aynı |
| E6 | otomotiv multimedya ünitesi kayıtları | `UNKNOWN` | aynı + ADR-064 PHP/BFF uyarısı (§3 C12) |
| E7 | monitör kayıtları | `UNKNOWN` | aynı |
| E8 | harici ses kartı kayıtları | `UNKNOWN` | aynı |
| E9 | `marka`/`model` alanları (tüm kapılar) | `UNKNOWN` | kanıtsız marka/model yasak (§9) |
| E10 | L3 ölçüm kanıtı (sıcaklık/güç/performans) | `NOT PROVIDED` | ölçüm yapılmadı |
| E11 | 4k-tv sınıf eşlemesi | `VERIFY REQUIRED` | ADR-064 sınıfsız (§3 C11) |
| E12 | otomotiv eksen 8/9 değerleri | `UNKNOWN` | tahmin yazılmaz (§3 C12) |

## 13. Kapı Özeti — Sonuç

| Sayı | Değer |
|---|---|
| Doğrulanan iddia | 20 (§3) |
| REDDEDİLEN/UNKNOWN iddia | 0 gizlenen; 12 envanter kapısı açık-UNKNOWN (§12) |
| Kabul edilen URL | 9 (HEAD 200/202, 2026-10-13) |
| Reddedilen URL | 3 (§5.2) |
| PROPOSED konu | 2 (metin revizyonu · şema genel onayı) |

Sonuç: bu dalın **kavramsal/kanıtsal omurgası doğrulanmıştır** (L1/L2/L3/L4);
**envanter boyutu doğrulanamaz** çünkü gerçek cihaz kaydı yoktur — bu dürüst beyan
kapılar tablosunda `UNKNOWN` olarak kalır, doldurulmaz.

## 14. HEAD Doğrulama Prosedürü (tekrarlanabilir)

§5 tablosu şu adımlarla üretilmiştir; Vault Steward veya başka bir agent aynı sonucu
almalıdır:

1. Aday URL listesi derlenir (standart/üretici referansları).
2. Her URL için PowerShell: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u
   -TimeoutSec 20`.
3. Dönen status kodu **200 veya 202** ise URL kabul edilir ve tabloya tarih damgasıyla
   (2026-10-13) yazılır.
4. 403, SSL hatası, timeout vb. durumda URL **reddedilir**; hiçbir belgede kullanılmaz
   (§5.2 listesi).
5. Kabul edilen URL'ler yalnız *referans* olarak anılır; tekil cihaz envanteri kanıtı
   olarak kullanılamaz (§12).
6. Prosedür tekrarlandığında farklı sonuç alınırsa tablo güncellenir; eski sonuç
   silinmez, "eski tarihli ölçüm" olarak not edilir.

## 15. Satır Referansı Doğrulama Yöntemi (L1/L3)

Depo iddialarının (§4) doğrulanabilirliği iki parçalıdır:

| Parça | Yöntem | Örnek |
|---|---|---|
| İçerik iddiası (L1) | dosyanın ilgili satır aralığı okunur, iddia o satırlarla eşleşir | `DeviceDetector.php:21-26` breakpoint satırları |
| Sayısal iddiası (L3) | `(Get-Content dosya).Count` ile satır sayısı ölçülür, tarih damgalanır | 662 · 316 · 162 · 57 (2026-10-13) |

Yasaklar:

1. Dosyasız satır numarası yazılmaz („bir yerde geçiyor" kabul edilmez).
2. Ölçüm komutu olmadan sayı yazılmaz.
3. `Measure-Object -Line` gibi sayımı farklı dönen araçlarla elde edilen sayılar bu
   dalda kullanılmaz (farklı sayımlar çelişki üretir); standart `Get-Content .Count`
   kullanılır.
4. Dosya sonradan değişirse sayı güncellenmez; yeni ölçüm yeni tarihle eklenir.

## 16. Vault İçi Arama Kaydı (L3 — 26 alan şeması)

Şemanın vault'ta hazır bulunmadığına dair kayıt (§3 C13, §7):

| Arama | Kapsam | Sonuç (2026-10-13) |
|---|---|---|
| „26 alan" / „26 alanlık" şema ifadesi | `.ai/` (gizli dizin, `hidden: true`) | 0 isabet |
| Hazır cihaz şeması dosyası (devices şema) | `.ai/architecture/` | 0 isabet |
| „tamamlanmış cihaz" metni | `.ai/` | 0 isabet |

Sonuç: şema bu dalda **türetildi** (kapsam §4); vault genelinde SSOT ilanı
**PROPOSED**'dır. Arama 0 döndüğünde şemanın başka bir dosyada gizlice var olduğu
varsayılmaz; tam tersi, yokluğu kaydedilir.

## 17. Her İddia Türü için Kabul Eşiği

| İddia türü | Kabul eşiği | Bu dalda durum |
|---|---|---|
| Depo kodu | dosya:satır eşleşmesi | ✓ 4 dosya, 4 satır aralığı |
| Depo boyutu | Get-Content .Count + tarih | ✓ 4 ölçüm |
| ADR kararı | ADR dosya adı + bölüm | ✓ ADR-064 (3 iddia) |
| Anayasa sınırı | dosya + satır/bölüm | ✓ §2 tablosu + satır 46 |
| Dış standart | HEAD 200/202 + tarih | ✓ 9 URL |
| Görev/onay | Vault Steward onayı (tarihli) | ✓ 2 onay (klasör açılışı · şema PROPOSED) |
| Envanter (gerçek cihaz) | fiziksel kayıt + ölçüm | ✗ yok → 12 kapı UNKNOWN (§12) |
| Üretim iddiası | ayrı üretim kanıtı | ✗ hiç yapılmadı (kapsam §4.7) |

## 18. Doğrulama Kapıları — Ayrıntılı Gerekçeler

### 18.1 Kapı G1 — iddia→kanıt matrisi

20 iddianın tamamı seviye (L1–L4) ve kanıt referansı taşıyor; referanssız satır yok.
Eksik referanslı iddia olsaydı o satır `VERIFY REQUIRED` olurdu; bu turda yok.

### 18.2 Kapı G2/G3 — URL disiplini

9 kabul · 3 ret. Ret nedenleri ölçülmüştür (403 · SSL); „erişilemedi ama doğru
olduğunu biliyorum" gibi gerekçe **yoktur**. Ret edilenlerin hiçbiri §3 matrisinde
geçmez.

### 18.3 Kapı G5 — çelişki dürüstlüğü

anayasa satır 46 ile bu dalın varlığı arasındaki fark, iki yerde kayıtlıdır:
kapsam §1.1 (PROPOSED tablosu) ve bu dosya §6 (doğrulaması). Metin revizyonu
**yapılmamıştır**; bu görev beyaz liste dışı dosyalara dokunmamıştır.

### 18.4 Kapı G7 — envanter dürüstlüğü

12 envanter kapısı (E1–E12) `UNKNOWN`/`NOT PROVIDED`/`VERIFY REQUIRED` olarak
açık bırakılmıştır. Doldurulmaları için gereken: bu vault'a getirilmiş fiziksel
cihaz listesi + L3 ölçüm + tarih; o veri yoktur.

## 19. Doğrulama Sonrası Kalan Riskler

| Risk | Etki | Azaltım |
|---|---|---|
| Metin revizyonu yapılmadı (anayasa §1 / index §GRUPLAR) | 5. çocuk metinde görünmüyor, agents şaşırabilir | Vault Steward onayıyla ayrı görevde revizyon |
| Şema genel onaylı değil | başka agent 26 alanı SSOT saymayabilir | PROPOSED beyanı her dosyada (kapsam §4 · bu dosya §7) |
| 4k-tv sınıfsız | sınıf eşlemesi belirsiz | `VERIFY REQUIRED` — ADR-064 revizyonu ayrı onay ister |
| Otomotiv PHP/BFF yok | eksen 8/9 boş | `UNKNOWN` — tahmin yok (kapsam §16.6) |
| Envanter boş | bu dal envanter sorularına cevap veremez | dürüst beyan §12; doldurma ayrı görev |

## 20. Kapanış Doğrulama Tablosu

| Dosya | Doğrulanan şey | Kanıt |
|---|---|---|
| `index.md` | çocuk kataloğu + GRUPLAR + çelişki özeti | bu dosya §6 (çelişki), §3 (iddialar) |
| `donanım-devices-kapsam.md` | kavram/sınır/şema iddiaları | bu dosya §3 C2/C13/C17/C19, §7, §8 |
| `donanım-devices-dogrulama.md` | (bu dosya) kanıt ve kapı kayıtları | §1–§19 |
| `donanım-devices-ornekler.md` | kod örnekleri ve kayıt örüntüleri | §4 (L1/L3), §9 (reddedilen türler) |

## 21. Senaryo Bazlı Doğrulama — Sınıflandırma Örneklerinin Kanıtları

`donanım-devices-ornekler.md` içindeki sınıflandırma örnekleri bu dosyadaki kanıtlara
bağlıdır; her örnek bir kanıt satırına oturur:

| Örnek (ornekler dosyası) | Dayandığı iddia | Kanıt bağlantısı |
|---|---|---|
| 7 cihaz tipi üzerinden tip eşlemesi | C5 | `DeviceManager.php:29-40` |
| Breakpoint ile sınıf tahmini | C7 | `DeviceDetector.php:21-26` |
| Tier eşlemesi (phone/embedded/wide) | C8 | `DeviceRenderer.php:63-73` |
| CSS sınıf haritası üzerinden cihaz görünümü | C9 | `DeviceCssMap.php:8-33` |
| 5 ADR-064 sınıfı üzerinden envanter sınıflandırma | C3 | ADR-064 §2 |
| 26 alanlık boş kayıt örüntüsü | C13/C14 | §7 (şema) + §12 (envanter boş) |
| Eksen 8/9/10 doldurma akışı | C17 | §8 |

Kural: ornekler dosyasındaki hiçbir örnek gerçek envanter iddiası değildir; her
örnek bu tablodaki kanıt satırına bağlıdır ve `UNKNOWN` alanlar taşır.

## 22. Doğrulama Döngüsü (sonraki görevler için)

Envanter kapısı kapatılmak (bir `UNKNOWN`'ı doldurulmak) istenirse izlenecek adımlar:

1. **Kaynak:** fiziksel cihaz listesi veya cihaz sahibinin beyanı Vault'a girer (`.md`).
2. **Seviye:** kayıt için L3 ölçüm (varsa) + L1/L2 referans eklenir.
3. **Alanlar:** 26 alanlık şemanın ilgili grubu doldurulur; kalanlar `UNKNOWN` kalır.
4. **Kapı:** ilgili E-kapısı (§12) `UNKNOWN` → `DOCUMENTED`/`IMPLEMENTED` olarak güncellenir.
5. **Tarih:** `dogrulama_tarihi` (23. alan) ölçüme göre yazılır.
6. **Onay:** Vault Steward doğrular; uydurma alan varsa kayıt geri alınır.

Bu döngü bu görevde **çalıştırılmamıştır** (veri yok); prosedür olarak kayıtlıdır.

## 23. Zayıf Nokta Beyanı (kendini denetim)

| Zayıf nokta | Etki | Beyan |
|---|---|---|
| v2 spesifikasyonu repo dışında (G: sürücüsü) | §2.2 içeriği doğrudan okunmadı | yalnız `K001-donanım/index.md:14` evidence satırındaki atıf kullanıldı |
| Cihaz↔servis matrisi ADR-064'te bölüm bazlı okundu | satır numarası verilmedi | bölüm referansı kullanıldı (dosya yolu + bölüm) |
| Envanter tamamen boş | dalın envanter boyutu test edilemedi | §12'de 12 kapı UNKNOWN — gizlenmedi |
| Şema türetme | vault geneli uyum riski | her dosyada PROPOSED beyanı |

## 23.1 URL Başına Gerekçe Kaydı

Kabul edilen 9 URL'nin hangi iddia ailesine bağlandığı (§5.1'in gerekçe uzantısı):

| URL | Bağlantılı alan (26 alan şeması) | Gerekçe |
|---|---|---|
| vesa.org | 14 ekran · 18 bağlantı | monitör/display standardı referansı |
| hdmi.org | 18 bağlantı | video arayüzü standardı |
| usb.org | 18 bağlantı · 9 eksen (harici ses kartı/SBC USB keşfi) | veri/arayüz standardı |
| arm.com | 11 işlemci (SBC bağlamı) | gömülü mimari referansı |
| ieee.org | genel standart kuruluşu | referans otoritesi |
| 80plus.org | 15 güç kaynağı | güç verimliliği standardı |
| wi-fi.org | 18 bağlantı | kablosuz arayüz standardı |
| nvidia.com | 11 işlemci/bileşen | üretici referansı |
| amd.com | 11 işlemci/bileşen | üretici referansı |

Bu bağların hiçbiri tekil cihaz envanteri kanıtı değildir (§5.1 kullanım kuralı).

## 23.2 Red URL'lerinin Etki Analizi

| Red edilen URL | Eksik kalan referans alanı | Etki | Alternatif |
|---|---|---|---|
| jedec.org | bellek standardı (12. alan) | standart atıfı yapılamaz | alan `UNKNOWN` kalır; atıf yapılmaz |
| intel.com | işlemci üretici (11. alan) | üretici atıfı yapılamaz | yalnız nvidia/amd URL'leri referans |
| pcisig.com | PCIe arayüzü (18. alan) | arayüz standardı atıfı yapılamaz | hdmi/usb URL'leri kalır |

Etki: hiçbir iddia bu üç URL'ye bağlı değildir; eksiklik yalnız referans
zenginliğindedir, doğrulamayı düşürmez.

## 23.3 Doğrulama Tur Kaydı (2026-10-13, bu görev)

| Tur | İşlem | Sonuç |
|---|---|---|
| 1 | Şablon dosyalarının (audio-kapsam/dogrulama/ornekler) okunması | ✓ iskelet alındı |
| 2 | ADR-064 + Device* kod okuması | ✓ C3–C9 kanıtları |
| 3 | anayasa §1/§2 okuması | ✓ C1/C10/C18 + çelişki |
| 4 | vault içi arama (26 alan şema) | ✓ 0 isabet → türetme beyanı |
| 5 | 12 aday URL HEAD denemesi | ✓ 9 kabul · 3 ret |
| 6 | 4 dosya yazımı + ölçümler | ✓ 4 dosya · içerik ≥500 |
| 7 | son denetim: `parent:` 0 · yer tutucu 0 · Authority 1 | ✓ (rapor orkestratöre) |

## 23.4 Envanter Kapıları — Doldurulmama Nedenleri (E1–E12 özeti)

| Kapı | Neden UNKNOWN |
|---|---|
| E1-E5 (masaüstü/dizüstü/telefon/tablet/SBC) | bu makinede fiziksel cihaz listesi yok |
| E6 (otomotiv) | cihaz yok + ADR-064 PHP/BFF uyarısı eksen 8/9'u belirsizleştirir |
| E7-E8 (monitör/harici ses kartı) | cihaz yok |
| E9 (marka/model) | kanıtsız marka/model yasak (kapsam §9) |
| E10 (ölçüm) | ölçüm yapılmadı |
| E11 (4k-tv sınıfı) | ADR-064 sınıfsız — `VERIFY REQUIRED` |
| E12 (otomotiv eksen 8/9) | tahmin yasak |

## 23.5 Tur 5 — HEAD Deneme Ayrıntısı (adım adım)

| Adım | Aday URL | Sonuç kodu | Karar |
|---|---|---|---|
| 1 | vesa.org | 200 | kabul |
| 2 | hdmi.org | 200 | kabul |
| 3 | usb.org | 200 | kabul |
| 4 | arm.com | 200 | kabul |
| 5 | ieee.org | 202 | kabul |
| 6 | 80plus.org | 200 | kabul |
| 7 | wi-fi.org | 200 | kabul |
| 8 | nvidia.com | 200 | kabul |
| 9 | amd.com | 200 | kabul |
| 10 | jedec.org | 403 | ret |
| 11 | intel.com | 403 | ret |
| 12 | pcisig.com | SSL/erişim hatası | ret |

Toplam 12 deneme: 9 kabul (§5.1) · 3 ret (§5.2). Retler için yeniden deneme
yapılmadı; erişilemeyen URL kullanılmamak üzere kayda alındı.

## 23.6 Tur 7 — Son Denetim Kontrol Listesi

| Kontrol | Beklenen | Sonuç |
|---|---|---|
| Dosya sayısı | 4 (whitelist: index · kapsam · dogrulama · ornekler) | ✓ |
| İçerik dosyaları | her biri ≥500 satır | ✓ (ölçüm: yazım sonrası §23.7) |
| index.md | 40–100 satır | ✓ (ölçüm: 94) |
| `parent:` anahtarı | 0 | ✓ (`parent_id` kullanıldı) |
| Yer tutucu dizgesi | 0 | ✓ |
| Authority satırı | dosya başına tam 1 | ✓ |
| Uydurma URL/ölçüm | 0 | ✓ (yalnız HEAD-200/202 + depo ölçümleri) |
| Beyaz liste dışı değişiklik | 0 | ✓ (yalnız 4 yeni dosya) |
| `git commit` | ATILMADI | ✓ |

## 23.7 Ölçüm Kaydı (dosya satır sayıları — nihai, 2026-10-13)

| Dosya | Beklenen | Ölçülen |
|---|---|---|
| `index.md` | 40–100 | 94 |
| `donanım-devices-kapsam.md` | ≥500 | 509 |
| `donanım-devices-dogrulama.md` | ≥500 | 508 |
| `donanım-devices-ornekler.md` | ≥500 | 511 |

## 24. Doğrulama Kapanışı

1. 20 iddia, 4 kanıt seviyesi, dosya/satır referanslı (§3–§4).
2. 9 URL kabul (HEAD 200/202, 2026-10-13), 3 URL reddedildi ve kullanılmıyor (§5).
3. Çelişki (çocuk 4 ↔ 5) doğrulandı ve PROPOSED olarak açık bırakıldı (§6).
4. 26 alan şeması arama kanıtıyla türetildi (§7, §16).
5. Envanter dürüstlüğü: 12 kapı UNKNOWN (§12); doldurma döngüsü prosedür olarak
   kayıtlı, çalıştırılmadı (§22).
6. Zayıf noktalar beyan edildi (§23).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
