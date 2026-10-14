---
title: display — Örnekler (Repo Kodu, Şablonlar, Çalışma Sayfaları)
type: mimari-ornekler
node_id: K001-donanım/donanaım-type/display/display-ornekler
node_type: module
status: proposed
parent_id: K001-donanım/donanaım-type
children:
  - "§2 repo-code kanıtı (700 dosya sayımı)"
  - "§4 EDID/DDC okuma şablonu"
  - "§6 panel↔arayüz eşleme çalışma sayfası"
  - "§9 dokunma kalibrasyon formu"
depends_on:
  - K001-donanım
  - ADR-102-tur-taksonomi-genislemesi-5-dal
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §4 (display satırı) · §4.1 (sınır: kablo/ekran ayrımı) · §4.2 (ADR-038/089/064 DEĞİŞMEZ) — 2026-10-13 eklendi (orkestratör cerrahi patch)"
  - "kendi repo sayımım (2026-10-13): 700 kaynak dosyada EDID=0 DDC=0 HDMI=0 DisplayPort=0 VESA=0"
  - "kendi repo sayımım: *.cpp=0 *.h=0 *.c=0 *.xc=0 *.ino=0 (3186 dosya, .ai/.git/vendor/node_modules hariç değil — genel sayı)"
  - "touchscreen|dokunmatik = 19 eşleşme (ScaleManager.js, d-embedded.css, DeviceDetectorTest.php vb.)"
  - "HEAD (kendi çalıştırığım, 2026-10-13): displayport.org=200 · khronos.org=200"
validation: "⚠️ VERIFICATION REQUIRED — §1'deki repo-code bölümü 'yok' üzerine kuruludur; şablonlar dolu ölçüm içermez"
open_questions:
  - "VD-01: display tipsiz mi — 19 dokunmatik eşleşmesi arayüz mi tasarım mı, ayrıştırılmadı"
  - "EDID/DDC şablonu (§4) gerçek cihazda denenmedi — S1 yok"
  - "panel↔arayüz çalışma sayfası (§6) boş — doldurulmadı"
date: 2026-10-13
updated: 2026-10-13
group: kod
---

# display — Örnekler

> **Kapsam**: display/ için örnek kanıtlar, şablonlar, çalışma sayfaları.
> **Gerçeklik kuralı**: repo'da display'e ait kod **yok** (§2 sayımı) — bu yüzden §1 doğrudan
> "yok" der ve boşluğu şablonlarla kapatır. Şablonlar **boştur**: hiçbirinde uydurma ölçüm,
> uydurma cihaz adı, uydurma URL yok. Dolu görünen tek şeyler: kendi sayımım (§2) ve HEAD kaydı (§11).


---

## §1 Repo-code örnekleri — durum: YOK

| Aranan | Sonuç (kendi sayımım, 2026-10-13) |
|---|---|
| `*.cpp` / `*.h` / `*.c` / `*.xc` / `*.ino` | **0 / 0 / 0 / 0 / 0** (3186 dosya içinde) |
| EDID referansı | **0** (700 kaynak dosyada) |
| DDC / DDC-CI | **0** |
| HDMI | **0** |
| DisplayPort | **0** |
| VESA | **0** |
| `touchscreen` / `dokunmatik` | **19** (aşağıda) |

**Sonuç**: display'e ait **üretilmiş kod örneği yok**. Bu bir boşluk kaydıdır, başarı değil:
ya display arayüzü kasıtlı olarak yoktur (VD-01) ya da henüz yazılmamıştır. Karar Vault Steward'ın.

### §1.1 Dokunmatik eşleşmeler (19 — hepsi arayüz/tasarım katmanı)

| Dosya | Ne | display tipi ilişkisi |
|---|---|---|
| `assets.coremusic.net/js/managers/ScaleManager.js` | ölçekleme yöneticisi | D-T07 dokunma katmanı girişi (muhtemel) |
| `assets.coremusic.net/Css/08_Devices/d-embedded.css` | cihaz CSS'i | D-T07 / D-T08 (tasarım) |
| `shared/tests/Unit/Device/DeviceDetectorTest.php` | cihaz algılama testi | D-T01..D-T03 cihaz algılama (tasarım) |
| (diğer 16 eşleşme) | — | **tasarım/uygulama katmanı** — donanım arayüzü değil |

> Bu 19 satır **kod kanıtıdır (S2)** ama display **donanım** kanıtı değildir — arayüz tasarımı
> "ekran var" demek değildir (I-5 tuzağı: ekran görüntüsü/CI ≠ cihaz).

---

## §2 Sayım metodu (tekrarlanabilir)

```
Tarih: 2026-10-13 (kendi çalıştırığım)
Kapsam: C:\www\coremusic.net · .ai/, .git/, vendor/, node_modules/ hariç
700 kaynak dosya: *.php *.js *.sql *.css
Aranan terimler (büyük/küçük duyarsız): edid · ddc · hdmi · displayport · vesa
Sonuç: 0 0 0 0 0
Ayrıca: touchscreen|dokunmatik → 19
```

**Yorum sınırı**: "şu anki çalışma ağacında yok" der; commit edilmemiş/stash edilmiş değişikliği
görmez; geçmişi bilmez. Geçmiş için `git log -S` gerekir — bu oturumda çalıştırılmadı (`UNKNOWN`).

---

## §3 Boş — repo-code kod bloğu

> Repo'da display kodu olmadığı için kopyalanacak örnek **yoktur**. Bu bölüm bilinçli olarak
> boş bırakılmaz; yerine "ne kod olurdu"nun şablonu §4'tedir. Uydurma örnek yazılmaz.

**Yok**: EDID ayrıştırıcı yok · DDC istemcisi yok · hotplug izleyici yok ·
giriş-lag ölçümü yok · piksel testi yok.

---

## §4 EDID/DCM okuma şablonu (doldurulacak — boş)

> Amaç: ileride EDID okunacaksa kayıt formu hazır olsun. **Hiçbir alan tahmin edilmez.**

```text
[EDID KAYDI — DOLDURULACAK]
tarih         : 
ölçen         : 
cihaz (marka/model — ölçenin kendi cihazı) : 
bağlantı (kablo/port)      : 
araç (hangi program/komut) : 
--- EDID içeriği ---
 üretici kodu (3 harf)     : 
 ürün kodu                 : 
 seri no                   : 
 üretim haftası/yılı        : 
.panel tipi (EDID'de)      : 
 desteklenen modlar         : 
 öncelikli mod              : 
--- DDC/CI ---
 yazma denemesi             : 
 okuma denemesi             : 
 sonuç                      : 
--- hotplug ---
 tak-çalıştır gecikmesi(ms) : 
--- NOT ---
 bu formun doldurulması D-K01 kapısını kapatır (display-dogrulama §3)
```

**Kural**: boşluk = `—`; `0` yalnız gerçek ölçümde; uydurma seri no / marka / mod **yasak**.

---

## §5 DDC/CI kayıt şablonu (boş)

| Komut/parametre | Amaç | Değer | Sonuç | Tarih |
|---|---|---|---|---|
| DDC/CI oku (VCP kodu) | parlaklık/kontrast oku | — | — | — |
| DDC/CI yaz (VCP kodu) | parlaklık/kontrast yaz | — | — | — |
| MCCS sürümü | protokol sürümü | — | — | — |
| Monitor SN | cihaz eşleştirme | — | — | — |

> Bu şablonun başlıkları literatürdeki yaygın MCCS/DDC kavramlarından gelir (S3 — "bilinen
> kavram"), değerleri **S1 = yok**. Kavram varlığı ≠ cihazda uygulanma kanıtı.

---

## §6 Panel tipi ↔ arayüz/protokol eşleme çalışma sayfası

> Anayasa §12.1: panel tipi (LCD/LED/OLED) ≠ protokol. Bu sayfa ayrımı zorunlu kılar:
> **sol sütun = bileşen**, **sağ sütun = arayüz/protokol** — asla tek hücrede birleştirilmez.

| # | Fiziksel bileşen (panel/gövde) | Bağlı olduğu arayüz | Konuşulan protokol/sürüm | Kanıt | Durum |
|---|---|---|---|---|---|
| 1 | D-T01/T02/T03 panel (kullanıcı cihazında) | — | — | — | `UNKNOWN` |
| 2 | D-T04 GPU display çıkışı (varsa) | — | — | — | `UNKNOWN` |
| 3 | D-T08 display controller | — | — | — | `UNKNOWN` |
| 4 | D-T07 touch layer (varsa) | — | — | — | `UNKNOWN` |
| 5 | D-T05 DisplayPort bağlantısı (varsa) | — | — | — | `UNKNOWN` |
| 6 | D-T06 HDMI bağlantısı (varsa) | — | — | — | `UNKNOWN` |

**Çalışma kuralı**: bir satır doldurulurken ölçülen değer S1, doküman S3, tahmin **yasak**.
Panel tipi bilinse bile arayüz `UNKNOWN` bırakılır (ayrım bozulmaz).

---

## §7 Arayüz sürüm kayıt formu (boş)

| Alan | Değer | Kaynak (S1/S3/S4) | Tarih |
|---|---|---|---|
| DisplayPort sürümü | — | — | — |
| HDMI sürümü | — | — | — |
| RS-232/USB kontrol var mı | — | — | — |
| Hotplug/EDID davranışı | — | — | — |
| Ses kanalları (varsa) | — | — | — |

> Sürüm tahmini yasak: "1.4 gibi" yazılmaz; ölçülmüş/okunmuş değilse `—`.

---

## §8 Panel tipi — kavramsal örnek (etiketli, ölçüm içermeyen)

| Tip | Gövde mü panel mi | Örnek kaynak | Etiket |
|---|---|---|---|
| D-T01 LCD (kendi sınıfı) | panel bileşeni (gövdenin içinde) | `display-kapsam §6` D-T01 | `DOCUMENTED` |
| D-T02 LED arkadan aydınlatmalı | panel + aydınlatma | D-T02 | `DOCUMENTED` |
| D-T03 OLED | panel (emisif) | D-T03 | `DOCUMENTED` |
| kullanıcıya bitmiş monitör | **cihaz** (gövde) | `T17-monitor-22fhd` ekranları | `DOCUMENTED` (tasarım) |

> Bu tablo **ölçüm değildir** — tip tanımı ve tasarım referansı (S3). Parlaklık/renk/tepe
> gibi sayısal hiçbir iddia içermez; içler `⚠️ VERIFICATION REQUIRED`.

---

## §9 Dokunma kalibrasyon formu (D-K05 — boş)

| Alan | Değer | Ölçen | Tarih | Koşul |
|---|---|---|---|---|
| Kalibrasyon yöntemi (4 nokta / 9 nokta) | — | — | — | — |
| Öncesi maks sapma (px) | — | — | — | — |
| Sonrası maks sapma (px) | — | — | — | — |
| Multi-touch çalışan parmak sayısı | — | — | — | — |
| Dokunma→yanıt gecikmesi (ms) | — | — | — | — |
| Teknoloji (kapasitif/rezistif) | — | — | — | — |

**Boşluk nedeni**: repo'da dokunma testi altyapısı yok (§1.1'deki 19 eşleşme arayüz katmanı,
ölçüm değil) — form, altyapı kurulmadan doldurulamaz.

---

## §10 Ölçüm doldurma kuralları (özet — bağlamsal değil, kural)

1. Her sayısal alan: değer + ölçen + tarih + koşul (cihaz/kablo/sürücü/OS) — dördü birlikte.
2. Ölçülmedi = `—` (tire); `0` yalnız gerçekten sıfır ölçüldüğünde.
3. Literatür tipik değer formun yerine geçmez (S4 ≠ S1).
4. Marka/model yalnız ölçenin **kendi** cihazı ise yazılır; başkası için varsayım yasak.
5. Şablondaki başlıklar değiştirilebilir; **uydurma dolgu asla**.

---

## §11 HEAD kaynak tablosu (erişim kanıtı — içerik okunmadı)

| # | Kaynak | HEAD | Kullanım | İçerik |
|---|---|---|---|---|
| 1 | `https://www.displayport.org/` | **200** (2026-10-13, kendi HEAD'ım) | display-ornekler §6/§7 | ⚠️ okunmadı |
| 2 | `https://www.khronos.org/` | **200** (2026-10-13, kendi HEAD'ım) | display-ornekler §8 | ⚠️ okunmadı |

> Diğer 4 display kaynağı (vesa, hdmi, displayhdr, cta) `display-kapsam.md` tablosunda —
> G6 unique URL nedeniyle burada tekrarlanmaz. 403/ERR sonuçları (jedec, analog, littelfuse,
> formfactors) **hiçbir yerde kullanılmadı**.

---

## §12 Kullanım özeti — bu dosya neyi kanıtlar

| Kanıtlanan | Seviye |
|---|---|
| display kodu repo'da yok (700 dosya, 0 eşleşme) | S2 |
| dokunmatik teması yalnız arayüz katmanında (19) | S2 |
| 2 kaynak URL erişilebilir (içerik okunmadı) | S4 |
| şablonlar hazır, ölçüm **0** | — |

---

## §13 Tip-bazlı şablon kayıtları (D-T01…D-T08 — hepsi boş)

> Her tip için "bu tip ölçülecekse şu alanlar doldurulur" kartı. **Tahmin yasak; hepsi `—`.**

### Şablon D-T01 · LCD

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Panel tipi yazımı (EDID/menü) | — | — | — |
| Çözünürlük × refresh (ölçülen) | — | — | — |
| Tepki süresi (ms) | — | — | — |
| Sıcaklık (°C) | — | — | — |

### Şablon D-T02 · LED backlight

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Aydınlatma tipi kaynağı (EDID/doküman) | — | — | — |
| Backlight sızması (karanlık oda) | — | — | — |
| Uniformite notu | — | — | — |
| Parlaklık ölçümü (nit — S1 zorunlu) | — | — | — |

### Şablon D-T03 · OLED

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Ölü piksel sayımı (siyah test) | — | — | — |
| Yanma testi notu | — | — | — |
| Parlaklık ölçümü (nit — S1 zorunlu) | — | — | — |
| Panel tipi yazımı | — | — | — |

### Şablon D-T04 · GPU display çıkışı

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Çıkış tipi (port listesi) | — | — | — |
| Aktif bağlantı sayısı | — | — | — |
| Max çözünürlük+refresh (çıkışta) | — | — | — |
| Hotplug gecikmesi (ms) | — | — | — |

### Şablon D-T05 · DisplayPort

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Sürüm (okunan) | — | — | — |
| Link hızı (okunan) | — | — | — |
| Eğitim hatası/retry | — | — | — |
| EDID okuma sonucu | — | — | — |

### Şablon D-T06 · HDMI

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Sürüm (okunan) | — | — | — |
| Mod listesi (EDID üzerinden) | — | — | — |
| Ses kanalı kullanımı | — | — | — |
| Hotplug davranışı | — | — | — |

### Şablon D-T07 · dokunma katmanı

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Teknoloji (kapasitif/rezistif) | — | — | — |
| Kalibrasyon sonrası maks sapma (px) | — | — | — |
| Multi-touch parmak sayısı | — | — | — |
| Dokunma→yanıt gecikmesi (ms) | — | — | — |

### Şablon D-T08 · display controller

| Alan | Değer | Ölçen | Tarih |
|---|---|---|---|
| Kontrolcü tipi (okunan/doküman) | — | — | — |
| Sürücü adı+sürümü | — | — | — |
| Firmware sürümü | — | — | — |
| EDID/DDC sorumluluğu (varsa) | — | — | — |

**Kural**: her şablon 4 satır × 8 tip = 32 boş satır — bu dosyanın **kanıt** değildir;
doldurulması ayrı ölçüm oturumu ister (display-dogrulama K-D4/K-D7).

---

## §14 Ölçüm ortamı kayıt formu (tüm display ölçümleri için — boş)

> Her ölçüm oturumu bu formun bir kopyasıyla başlar; koşullar yazılmadan ölçüm sayılmaz.

| Alan | Değer |
|---|---|
| Oturum adı | — |
| Ölçülen cihaz (ölçenin kendi cihazı) | — |
| Kullanılan kablo/port | — |
| İşletim sistemi + sürüm | — |
| Ekran sürücüsü + sürüm | — |
| Ortam sıcaklığı (°C) | — |
| Ölçüm aracı (donanım/yazılım) | — |
| Ölçen kişi | — |
| Tarih | — |
| Ölçüm sayısı (n) | — |

**Kural**: form eksikse ölçüm sonuçları `VERIFY REQUIRED` kalır; `0` doldurulmaz.

---

## §15 Panel↔arayüz eşleme sayfası — genişletilmiş (D-T01…D-T08)

> §6'nın geniş sürümü: her satırda panel sınıfı ile arayüz **ayrı hücrelerde** (ayrım bozulmaz).

| # | Panel/sınıf (D-T01..03) | Çıkış bileşeni (D-T04) | Arayüz (D-T05/06) | Kontrol (D-T07/08) | Ölçülen mod | Kanıt |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — |
| 2 | — | — | — | — | — | — |
| 3 | — | — | — | — | — | — |
| 4 | — | — | — | — | — | — |
| 5 | — | — | — | — | — | — |
| 6 | — | — | — | — | — | — |
| 7 | — | — | — | — | — | — |
| 8 | — | — | — | — | — | — |

**Doldurma kuralı**: bir satırda hem "OLED" hem "HDMI" yazılabilir — ama **ayrı hücrelerde**
ve her biri kendi kanıtına (S1/S3/S4) bağlanır; tek hücrede birleştirilmez (anayasa §12.1).

---

## §16 EDID okuma oturum defteri (boş — çoklu oturum)

> Birden fazla oturumda EDID okunursa her oturum bir satır; tek dosyada toplanır.

| # | Tarih | Cihaz | Araç | Üretici kodu | Panel tipi | Modlar | Sonuç |
|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — |
| 2 | — | — | — | — | — | — | — |
| 3 | — | — | — | — | — | — | — |
| 4 | — | — | — | — | — | — | — |
| 5 | — | — | — | — | — | — | — |

**Kural**: satır sayısı = gerçek oturum sayısı; boş satırlar doldurulmayı bekler; uydurma satır yok.

---

## §17 Kapanış — bu dosya neyi kanıtlar (özet)

| Kanıtlanan | Seviye |
|---|---|
| display kodu repo'da yok (700 dosya, 0 eşleşme) | S2 |
| dokunmatik teması yalnız arayüz katmanında (19) | S2 |
| 2 kaynak URL erişilebilir (içerik okunmadı) | S4 |
| şablonlar hazır, dolum **0** | — |
| 8 tip şablonu + ortam formu + eşleme sayfası boş | — |
| git commit | **ATILMADI** (subagent taahhüdü) |

---

## §18 Arayüz bağlantı defteri (kablo bazlı — boş)

| # | Tarih | Port tipi | Kablo | Çözünürlük×refresh | Hotplug (ms) | Sorun | Ölçen |
|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — |
| 2 | — | — | — | — | — | — | — |
| 3 | — | — | — | — | — | — | — |
| 4 | — | — | — | — | — | — | — |
| 5 | — | — | — | — | — | — | — |

> Her satır = bir fiziksel bağlantı gözlemi; "sorun" sütunu boş bırakılmaz (yoksa `yok` yazılır).

---

## §19 Ölçüm oturum günlüğü (boş)

| # | Tarih | Oturum | Kapı | Doldurulan form | Sonuç | Ölçen |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — |
| 2 | — | — | — | — | — | — |
| 3 | — | — | — | — | — | — |
| 4 | — | — | — | — | — | — |
| 5 | — | — | — | — | — | — |

> Her satır bir ölçüm oturumunu temsil eder; oturum yapılmadıkça satır `—` kalır.

---

## §20 Sık hatalar (bu tip için — bağlamsal uyarı listesi)

| # | Hata | Sonuç | Kaçınma |
|---|---|---|---|
| 1 | "belgede geçiyor" = "repo'da var" demek | hayali envanter | S2 sayımı zorunlu |
| 2 | panel tipini protokolle aynı hücrede yazmak | ayrım kaybı | 4 eksenli model (kapsam §5) |
| 3 | ekran görüntüsünü donanım kanıtı saymak | I-5 tuzağı | yalnız cihaz kanıtı sayılır |
| 4 | HEAD 200'ü "içerik doğrulandı" sanmak | kanıt şişirme | ⚠️ okunmadı etiketi |
| 5 | "tipik değer" yazıp ölçüm sanmak | S1 sahteciliği | tipik değer formdaki yere yazılmaz |
| 6 | tahmini sürüm yazmak ("1.4 gibi") | doğrulanamayan iddia | yalnız okunan sürüm yazılır |
| 7 | ölçülmediği hâlde `0` yazmak | veri kirliliği | `—` kuralı |
| 8 | anayasa revizesi olmadan `DOCUMENTED` etiklemek | yetki aşımı | status=proposed disiplini |

---

## §21 Doldurma akışı (kavramsal — ölçüm talimatı değil)

1. Ölçüm oturumu açılır → §14 ortam formu doldurulur (koşullarsız ölçüm sayılmaz).
2. İlgili tip şablonu seçılır (§13: D-T01…D-T08) → yalnız o şablonun alanları doldurulur.
3. EDID okunacaksa §4 + §16 defteri birlikte doldurulur (oturum satırı + özet).
4. Arayüz bağlantısı gözlemlenmişse §18 defterine bir satır eklenir.
5. Eşleme sayfası (§15) güncellenir — panel ve arayüz **ayrı hücrelerde**.
6. Her doldurulan satırda ölçen + tarih + koşul bulunur; bulunmayan satır geri alınır.
7. Kapı hesabı: hangi kapıların kapandığı `display-dogrulama §3` tablosundan işaretlenir.
8. **Bu dosyaya uydurma veri girişi yasak** — doldurulmayan `—` kalır.

> Bu akış bir **plan**tır; uygulaması ölçüm oturumuna aittir ve bu dosyada gerçekleşmez.

---

## §22 Kapanış — bu dosya neyi kanıtlar (özet)

| Kanıtlanan | Seviye |
|---|---|
| display kodu repo'da yok (700 dosya, 0 eşleşme) | S2 |
| dokunmatik teması yalnız arayüz katmanında (19) | S2 |
| 2 kaynak URL erişilebilir (içerik okunmadı) | S4 |
| şablonlar hazır, dolum **0** | — |
| 8 tip şablonu + ortam formu + eşleme sayfası boş | — |
| git commit | **ATILMADI** (subagent taahhüdü) |

---

## §22 Sürüm geçmişi (bu dosya)

| # | Tarih | Ne değişti | Değiştiren |
|---|---|---|---|
| 1 | 2026-10-13 | ilk yazım (§1–§22) | Vault Steward (subagent) |

> Gelecek revizyonlar bu tabloya eklenir; silme yasak (append-only).

---

## §23 İlişkili dosyalar (cross-reference)

| Dosya | İlişki |
|---|---|
| `display/index.md` | bu dosyanın künyesi ve kapı özeti |
| `display/display-kapsam.md` | D-T01…D-T08 tip tanımları · §0 çatışma kaydı · §5 ayrım modeli |
| `display/display-dogrulama.md` | kapılar K-D1…K-D8 · ölçüm formları (§4–§6) · HEAD tablosu (§9) |
| `donanaım-type/index.md` | kademe-3 kataloğu (display satırı, 2026-10-13) |
| `K001-donanım/index.md` | kademe-3 tablosu (display satırı, 2026-10-13) |
| `donanım-anayasa.md` | §2 çatışma kaynağı — **dokunulmaz**, yalnız okundu |
| `donanaım-type/donanaım-type-kapsam.md` | §2/A6, §7/3-T1, §12/M6 çatışma kaynağı — dokunulmaz |
| `audio/audio-ornekler.md` | yapısal şablon (önceki oturum) — dokunulmadı |

---

## §24 Terim notları (display — kısa, bağlamsal değil)

| Terim | Bu dosyadaki anlamı |
|---|---|
| EDID | ekrandan okunan tanıtım bloğu — panel/mod bilgisi kaynağı |
| DDC/CI | ekranla yazılımsal kontrol kanalı (VCP komutları) |
| hotplug | kablo tak-çıkar algılama olayı |
| panel | görüntüyü üreten bileşen (LCD/OLED sınıfı) |
| arayüz | panelin/çıktının bağlandığı port protokolü (DP/HDMI) |
| touch layer | gövdeye eklenen dokunma yüzeyi — ayrı bileşen |
| giriş lag | komuttan görüntüye geçen süre (ms) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
