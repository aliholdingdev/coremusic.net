---
title: "OS Donanım Tespiti — Uygulama Örnekleri"
type: architecture-module
node_id: os-donanim-tespiti-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/os-donanim-tespiti
children: []
depends_on:
  - os-donanim-tespiti-kapsam
  - os-donanim-tespiti-dogrulama
evidence:
  - "Repo ölçümü 2026-10-13: shared/src/Device/ = 4 PHP dosyası (DeviceDetector 316/272 · DeviceManager 662/588 · DeviceCssMap 57/49 · DeviceRenderer 162/144 — toplam/dolu satır)"
  - "Repo ölçümü 2026-10-13: sysfs|SetupDi|udev|devnode|PnP|ioctl grep = 0 isabet (4 dosyanın tamamı)"
  - "Repo ölçümü 2026-10-13: *.c=0 · *.cpp=0 · *.h=0 · *.hpp=0 · *.cs=0 · *.inf=0 · *.rs=0 · *.xc=0 · *.ino=0"
  - "https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://docs.kernel.org/driver-api/driver-model/index.html (HTTP 200, 2026-10-13, HEAD)"
  - "https://devicetree.org/ (HTTP 200, 2026-10-13, HEAD)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · OS-seviyesi keşif kodu repo'da YOK (grep 0) · fiziksel ölçüm YOK · tüm platform iddiaları ⚠️ VERIFICATION REQUIRED"
open_questions:
  - "Q-DO1: Windows gözlem formu (§3.2) doldurulamaz — çalışan makine envanteri yok (Q-DD1) → BEKLEMEDE"
  - "Q-DO2: Linux gözlem formu (§4.2) doldurulamaz — aynı engel (Q-DD1) → BEKLEMEDE"
  - "Q-DO3: Gerçek VID/PID / modalias / compatible değeri yok — uydurma yasak (Q-DD2) → UNKNOWN"
  - "Q-DO4: Sürücü bağlama örnekleri yalnız şablondur — repo'da *.inf=0 · *.c=0 → PLANNED"
date: "2026-10-13"
updated: "2026-10-13"
group: kod
---

# os-donanim-tespiti-ornekler — OS Donanım Tespiti Uygulama Örnekleri

> **Kural:** §1 repo kodu birebir aranır (yol + satır + sayı); ilgizse
> "— yok" yazılır. §2–§10 **adım şablonlarıdır** — gerçek çalışan OS keşif
> kodu, gerçek IOCTL numarası, gerçek VID/PID veya gerçek gözlem çıktısı
> olarak sunulmaz. Uydurma API çağrısı, uydurma IOCTL, uydurma VID/PID,
> uydurma `/sys` alt yolu **üretilmez**; bilinmeyen `UNKNOWN` ile işaretlenir.

## §1 Gerçek repo kodu (ÖNCELİK 1)

### §1.1 `shared/src/Device/` envanteri (2026-10-13, repo kökü `C:\www\coremusic.net`)

| # | Dosya | Toplam satır | Dolu satır | Sınıf / giriş | Etiket |
|---|---|---|---|---|---|
| 1 | `shared/src/Device/DeviceDetector.php` | 316 | 272 | `final class DeviceDetector` (satır 19) · `detect()` (satır 57) | IMPLEMENTED |
| 2 | `shared/src/Device/DeviceManager.php` | 662 | 588 | 7 cihaz sabiti (satır 29–40) · `fromRequest()` (satır 179) | IMPLEMENTED |
| 3 | `shared/src/Device/DeviceCssMap.php` | 57 | 49 | 3 CSS haritası (satır 8–33) · 3 getter (satır 35–48) | IMPLEMENTED |
| 4 | `shared/src/Device/DeviceRenderer.php` | 162 | 144 | `final class DeviceRenderer` (satır 19) · `tierOf()` (satır 63) | IMPLEMENTED |

> **Not (metodoloji):** "Dolu satır" = boş satır hariç sayım; toplam satır
> `[System.IO.File]::ReadAllLines` değeridir (bkz. `-dogrulama.md` §12.8).
> `-kapsam.md` §13'teki 272/588/49 değerleri dolu satır sayımıdır — bu
> dosya **çelişmez, tamamlar** (`DeviceRenderer.php` keşfi eklendi).

### §1.2 OS-seviyesi keşif kodu araması (negatif kanıt — öncelik 0)

| # | Aranan | Sonuç |
|---|---|---|
| 1 | `shared/src/Device/*.php` içinde `sysfs` | **0** |
| 2 | aynı içinde `SetupDi` | **0** |
| 3 | aynı içinde `udev` | **0** |
| 4 | aynı içinde `devnode` | **0** |
| 5 | aynı içinde `PnP` | **0** |
| 6 | aynı içinde `ioctl` | **0** |
| 7 | repo geneli `*.c` · `*.cpp` · `*.h` · `*.hpp` | **0 · 0 · 0 · 0** |
| 8 | repo geneli `*.cs` · `*.inf` · `*.rs` | **0 · 0 · 0** |
| 9 | repo geneli `*.xc` · `*.ino` (XMOS/MCU) | **0 · 0** |
| 10 | OS keşif sarmalayıcısı (SetupAPI/sysfs çağıran kod) | — yok |

**Yorum (bağlayıcı):** Repo'da **OS-seviyesi donanım keşif kodu YOK**.
Aşağıdaki §2–§10 şablonlarının hiçbiri repo'daki bir koddan türetilmemiştir;
tümü **sınır şablonu**dur. UI tahmin kodu (§1.3) keşif değildir.

### §1.3 UI tahmin kodu — keşif DEĞİL (sınır kanıtı, kod var)

| # | Parça | Satır | Ne yapar | Keşif mi? |
|---|---|---|---|---|
| 1 | `DeviceDetector::detect()` | 57–92 | Başlık `HTTP_X_DEVICE_TYPE` (63) → TV agent (70) → embedded agent (75) → viewport (80) → UA (85) → `desktop` (91) | **Hayır** — tahmin |
| 2 | `detectFromViewportAndContext()` | 97–141 | Genişlik eşikleri (21–26: `PHONE_MAX=767` … `FOUR_K_TV_MAX=3840`) + 1024×600→embedded / 1024×768→laptop ayrımı (110–117) | **Hayır** |
| 3 | `detectFromViewport()` | 146–171 | Sadece genişlik/yükseklik (JS fallback) | **Hayır** |
| 4 | `isTvAgent()` / `isEmbeddedDevice()` / `isDesktopOS()` | 176–189 / 194–207 / 212–235 | UA dizesi `stripos` taraması | **Hayır** |
| 5 | `DeviceManager::fromRequest()` | 179–196 | Viewport cookie `cm_viewport_w/h` (187–191) + `detect()` (194) | **Hayır** |
| 6 | `DeviceManager::fromDevice()` | 201–210 | Bilinen tip doğrulaması; geçersiz → `desktop` (208) | **Hayır** |
| 7 | `DeviceCssMap` haritaları | 8–33 | Tip → CSS dosyası eşlemesi (`08_Devices/`, `09_ViewModes/`) | **Hayır** |
| 8 | `DeviceRenderer::tierOf()` | 63–73 | Tip → DOM tier (phone / embedded / wide) | **Hayır** |

**Ayrım kuralı:** Bu 8 satırın tamamı **kullanıcının tarayıcısından gelen
başlık/viewport bilgisine bakar**; donanım bus'ını taramaz, cihaz kimliği
okumaz, sürücü bağlamaz. Bunların "keşif" sayılması `-kapsam.md` §17/#1
hatasıdır.

### §1.4 `DeviceManager` cihaz sabitleri envanteri (kod kanıtı)

| # | Sabit | Değer | Satır |
|---|---|---|---|
| 1 | `EMBEDDED` | `embedded` | 29 |
| 2 | `PHONE` | `phone` | 30 |
| 3 | `TABLET` | `tablet` | 31 |
| 4 | `LAPTOP` | `laptop` | 32 |
| 5 | `DESKTOP` | `desktop` | 33 |
| 6 | `FOUR_K_TV` | `4k-tv` | 34 |
| 7 | `FOUR_K_MON` | `4k-monitor` | 35 |
| 8 | `ALL_DEVICES` (private) | 7 değerin tamamı | 37–40 |
| 9 | `NAV_LINKS` (private) | cihaz tipi → menü link listesi | 45–… |

**Not:** 7 tip ADR-064 kaydıyla çapraz teyitlidir (`-dogrulama.md` §4/#4 —
`DeviceManager.php:29-40` aynı aralık). `fromDevice()` içinde geçersiz tip
`desktop`'a fallback eder (satır 208) — bu **doğrulama** davranışıdır, keşif
değildir.

### §1.5 `DeviceCssMap` / `DeviceRenderer` çıktı yüzeyi (kod kanıtı)

| # | Yüzey | Ne üretir | Satır |
|---|---|---|---|
| 1 | `DEVICE_CSS` | tip → `08_Devices/d-*.css` (4K için tek SSOT `d-4k.css`) | 8–16 |
| 2 | `AUTH_DEVICE_CSS` | tip → `08_Devices/d-auth-*.css` | 18–26 |
| 3 | `VIEW_MODE_CSS` | view mode → `09_ViewModes/v-*.css` (home/pro/studio/car) | 28–33 |
| 4 | `toCssPath()` / `authToCssPath()` / `viewModeToCssPath()` | bilinmeyen → varsayılan fallback (desktop / home) | 35–48 |
| 5 | `DeviceRenderer::tierOf()` | tip → DOM tier: phone / embedded / wide | 63–73 |
| 6 | `DeviceRenderer` LINK_ID sabitleri | client sözleşmesi: `cm-auth-bundled` · `cm-device-css` · `cm-view-css` | 22–26 |

**Sınır yorumu:** Bu yüzeyin tamamı **CSS/tier render** sınıfındadır; OS keşif
yüzeyiyle (SetupAPI/sysfs/devnode) **teması yoktur** (grep 0, §1.2).

## §2 Windows Enumeration Gözlem Şablonu (W1–W2 · boş form)

> ⚠️ VERIFICATION REQUIRED: Bu form **boştur**; çalışan bir Windows makinesinde
> doldurulacaktır (`-dogrulama.md` §5.1 · §12.12). Araç adı, IOCTL numarası
> veya struct adı **uydurulmaz** — araç seçimi gözlem anında yapılır ve buraya
> ham çıktıdan yazılır.

### §2.1 W1 — cihaz listesi okuma formu (BOŞ)

| # | Tarih/saat | Makine (OS sürümü) | Kullanılan yüzey (§5.2 envanterinden) | Ham çıktı dosyası | Liste satır sayısı | Kaydeden |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — |

### §2.2 W2 — sınıf/ID okuma formu (BOŞ)

| # | Cihaz (listeden) | Sınıf adı | Kimlik alanı (biçim §8.1/#4) | Durum | Ham dosya | Durum |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | BEKLEMEDE |

### §2.3 W4 — sürücü bağlama durumu formu (BOŞ)

| # | Cihaz | Bağlı sürücü (okundu) | Bağlama durumu | Ham dosya | Durum |
|---|---|---|---|---|---|
| 1 | — | — | — | — | BEKLEMEDE |

### §2.4 Gözlem adımı kontrol listesi

| # | Kontrol | Geçme koşulu | Durum |
|---|---|---|---|
| 1 | Çalışan makine envanteri var mı? | makine + OS sürümü kayıtlı | ❌ yok (Q-DD1) |
| 2 | Ham çıktı dosyası saklandı mı? | dosya yolu §2.1'de | ❌ doldurulmadı |
| 3 | Kimlik biçimi uydurma mı? | okunan değer, uydurulan değil | ❌ gözlem yok |
| 4 | Örneğe IMPLEMENTED etiketi bindirildi mi? | yalnız S1 sonrası | ❌ yazılamaz |

**Sonuç:** 0/4 geçti → §2 tamamlandı **sayılmaz**.

## §3 Windows Hotplug (PnP) Olay Şablonu (W3 · boş form)

### §3.1 Olay kayıt formu (BOŞ)

| # | Tarih/saat | Eylem (tak/çıkar) | Olay bildirimi alındı mı? | Zaman damgası | Cihaz kimliği (okundu) | Uygulama tepkisi | Ham dosya |
|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — |

### §3.2 Olay akış kontrol listesi (şablon)

| # | Adım | Beklenen | Gerçekleşti mi? | Not |
|---|---|---|---|---|
| 1 | Dinleme yüzeyi başlatıldı | olay beklemeye alındı | ❌ | — |
| 2 | USB cihaz fiziksel takıldı | ekleme olayı | ❌ | — |
| 3 | Olay + kimlik kaydedildi | §3.1 doldu | ❌ | — |
| 4 | Sürücü bağlandı mı? | durum okundu (§2.3) | ❌ | — |
| 5 | USB çıkarıldı | kaldırma olayı | ❌ | — |
| 6 | Ağaçtan düştü mü? | liste yeniden okundu | ❌ | — |
| 7 | Uygulama listeyi yeniledi mi? | uygulama logu | ❌ | Q-DD3 |

**Yasak:** Olay adı (mesaj/anahtar) **uydurulmaz**; gözlemde okunan ad yazılır.

## §4 Linux Enumeration / sysfs Şablonu (L1–L2 · boş form)

### §4.1 L1 — cihaz ağacı listesi formu (BOŞ)

| # | Tarih/saat | Makine (çekirdek sürümü) | Yüzey (§6.2 envanterinden) | Ham çıktı dosyası | Düğüm sayısı | Kaydeden |
|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — |

### §4.2 L2 — modalias / kimlik okuma formu (BOŞ)

| # | Cihaz (yoldan) | modalias / kimlik değeri (ham) | Biçim §8.1/#2'ye uyar mı? | Durum |
|---|---|---|---|---|
| 1 | — | — | — | BEKLEMEDE |

### §4.3 L5 — device-tree okuma formu (BOŞ — gömülü varsa)

| # | Düğüm yolu (ham) | compatible değeri (ham) | Uyduruldu mu? | Durum |
|---|---|---|---|---|
| 1 | — | — | hayır | BEKLEMEDE |

### §4.4 Linux gözlem kontrol listesi

| # | Kontrol | Geçme koşulu | Durum |
|---|---|---|---|
| 1 | Çalışan Linux makinesi var mı? | makine + çekirdek sürümü | ❌ yok (Q-DD1) |
| 2 | `/sys` alt yolu uydurulmadı mı? | değer ham çıktıdan | ❌ gözlem yok |
| 3 | modalias dizesi okundu mu? | §4.2 dolu | ❌ |
| 4 | DT okuması yapıldı mı (gömülü varsa)? | §4.3 dolu veya "gömülü yok" notu | ❌ |

**Sonuç:** 0/4 geçti → §4 tamamlandı **sayılmaz**.

## §5 Linux Hotplug (uevent/udev) Olay Şablonu (L3–L4 · boş form)

### §5.1 Olay kayıt formu (BOŞ)

| # | Tarih/saat | Eylem | uevent alındı mı? | Anahtar/ad (ham) | `/dev` düğümü oluştu mu? | Yetki (ham) | Uygulama tepkisi |
|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — |

### §5.2 Olay akış kontrol listesi (şablon)

| # | Adım | Beklenen | Gerçekleşti mi? |
|---|---|---|---|
| 1 | Olay dinleme yüzeyi açık | beklemeye alındı | ❌ |
| 2 | USB takıldı | uevent geldi | ❌ |
| 3 | udev kuralı çalıştı | `/dev` düğümü + yetki | ❌ |
| 4 | Uygulama listeyi yeniledi | log kaydı | ❌ |
| 5 | USB çıkarıldı | silme uevent'i | ❌ |
| 6 | Düğüm düştü mü? | liste yeniden okundu | ❌ |

## §6 Sürücü Bağlama Şablonu (§8.1 kapsam — boş form)

### §6.1 Bağlama deneme kayıt formu (BOŞ)

| # | Platform | Eşleme anahtarı (§8.1) | Okunan kimlik (ham) | Aday sürücü adayı (okundu) | Bağlama sonucu | Ham dosya |
|---|---|---|---|---|---|---|
| 1 | Windows | — | — | — | — | — |
| 2 | Linux | — | — | — | — | — |

### §6.2 Bağlama karar tablosu (kural seviyesi — değer yok)

| # | Durum | Karar | Etiket |
|---|---|---|---|
| 1 | Kimlik okundu, aday var | bağla + durumu kaydet | şablon |
| 2 | Kimlik okundu, aday yok | hata sınıfı §8.2/#2 | şablon |
| 3 | Aday var, bağlama hatası | hata sınıfı §8.2/#3 | şablon |
| 4 | İki aday çakışıyor | hata sınıfı §8.2/#5 | şablon |
| 5 | Yetki reddi | hata sınıfı §8.2/#4 → `cihaz-erisimi` | şablon |

**Yasak:** INF satırı, modalias dizesi veya DT compatible dizesi **örnek
değerle** doldurulmaz (gerçek değer girişi: kendi donanımından ham çıktı,
`.ai/.env` dışı depolama, K001 `donanım-devices` sahipliği).

## §7 Başarısız-Keşif Hata Modeli Örnekleri (§8.2 kapsam — şablon)

| # | Hata sınıfı (kapsam §8.2) | Gözlem adımı | Kayıt formu | Örnek durum (uydurma değer YOK) |
|---|---|---|---|---|
| 1 | Kimlik okunamadı | bus tarama sonucu boş | §2.1 / §4.1 | — |
| 2 | Eşleşme yok | kimlik var, aday yok | §6.1/#2 | — |
| 3 | Sürücü yükleme hatası | bağımlılık/hata satırı | §6.1/#3 | — |
| 4 | Yetki reddi | erişim reddi satırı | §2.3 | — |
| 5 | Çakışan ID | iki aday kaydı | §6.1/#4 | — |
| 6 | Olay kaybı | olay yok ama durum değişmiş | §3.1 / §5.1 | — |

**Kural:** Her satır yalnız **gözle** dolar; "örnek" sütunu bilinçli **boş**
bırakılmıştır — doldurulacak değer uydurma olurdu.

### §7.1 Hata olayı kayıt formu (BOŞ — gözlem bekliyor)

| # | Tarih/saat | Platform | Hata sınıfı (#1–#6) | Belirti (ham) | Sahip yüzey | Çözüm/sonuç | Kaydeden |
|---|---|---|---|---|---|---|---|
| 1 | — | — | — | — | — | — | — |

### §7.2 Hata → kanıt zinciri kontrol listesi

| # | Kontrol | Geçme koşulu | Durum |
|---|---|---|---|
| 1 | Her hata kaydı bir ham çıktıya bağlı mı? | dosya yolu dolu | ❌ gözlem yok |
| 2 | Hata sınıfı kapsam §8.2 ile eşleşiyor mu? | 1–6 arası numara | ❌ |
| 3 | Yetki reddi `cihaz-erisimi`'ne mi devredildi? | sınıf #4 | ❌ |
| 4 | Çözüm sonrası sınıf kapatıldı mı? | sonuç + tarih | ❌ |

## §8 OID/IOCTL Yüzeyi — Sınır Örnekleri (`cihaz-erisimi` ≠ keşif)

### §8.1 IOCTL/IOID ne zaman gündeme gelir (akış)

| # | Adım | Bu dalın tutumu | Sınır sahibi |
|---|---|---|---|
| 1 | Keşif turu cihazı bulur | **bu dal** (§4 kapsam, A1–A8) | os-donanim-tespiti |
| 2 | Cihaz ağaca eklenir | **bu dal** | os-donanim-tespiti |
| 3 | `/dev` düğümü / erişim yüzeyi açılır | yalnız tarif — ayrıntı yok | `cihaz-erisimi` |
| 4 | IOCTL/OID komutu gönderilir | **dışarıda** | `cihaz-erisimi-anayasa.md` §2.3 |
| 5 | Cihazdan veri okunur (mmap/fd) | **dışarıda** | `cihaz-erisimi` |
| 6 | Cihaz çıkarılır → olay | **bu dal** | os-donanim-tespiti |

### §8.2 IOCTL numarası girişi (BOŞ — uydurma yasak)

| # | IOCTL/OID | Platform | Ne yapar | Kaynak (ham) | Durum |
|---|---|---|---|---|---|
| 1 | — | — | — | — | UNKNOWN (Q-DD2) |

**Yasak satırı:** Bu tabloya hiçbir IOCTL numarası, OID adı veya struct adı
**ezberden/hatırdan** yazılamaz; yalnız (a) resmi dokümandan **okunmuş** ve
(b) HEAD-200 kaynak gösterilmiş satır girer; ikisi bu oturumda yapılmadı →
`UNKNOWN`.

### §8.3 IOCTL yüzeyi karar akışı (şablon — değer yok)

| # | Adım | Soru | Evet → | Hayır → |
|---|---|---|---|---|
| 1 | Cihaz keşfedildi mi? | §2.1/§4.1 listesinde var mı | devam | keşif kapısı AÇIK (§2/#1) |
| 2 | `/dev`/erişim düğümü açıldı mı? | `cihaz-erisimi` kaydı | devam | `cihaz-erisimi` kapısı |
| 3 | Komut IOCTL/OID mi? | evet | **dışarıda** — `cihaz-erisimi` §2.3 | keşif kaydına yaz (bu dal) |
| 4 | Yanıt okundu mu? | ham çıktı | uygulama (K001) tüketir | hata sınıfı §7 |

## §9 K001 `donanım-api` Sözleşme Örnekleri (OS tarafı — iddia yok)

### §9.1 Sözleşme kalemi → bu dalın örneği

| # | Kalem (kapsam §9) | Bu dosyada örnek var mı? | Ne sunulabilir | Ne SUNULAMAZ |
|---|---|---|---|---|
| 1 | Cihaz kimliği okuma | hayır (gözlem yok) | yalnız form (§2.2) | okunmuş ID |
| 2 | Cihaz listesi/ağacı | hayır | yalnız form (§2.1/§4.1) | gerçek liste |
| 3 | Ekleme/çıkarma olayı | hayır | yalnız form (§3.1/§5.1) | yakalanmış olay |
| 4 | Sürücü bağlama durumu | hayır | yalnız form (§2.3/§6.1) | bağlanmış sürücü |
| 5 | IOCTL/OID yüzeyi | hayır | yalnız sınır tablosu (§8.1) | numara/ad |
| 6 | Firmware yükleme | hayır | — (K005 sınırı) | — |

**Kural:** K001'in bu sözleşmeyi nasıl tükettiği bu dosyanın **konusu
değildir** (kapsam §9.1 kuralı); burada yalnız **OS tarafı formu** sunulur.
K001 tarafı için iddia: **UNKNOWN** (kod aranmadı — kapsam §9.1).

### §9.2 Örnek bir "iyi kayıt" satırı (uydurma DEĞİL — biçim örneği)

| Alan | Şablon değeri | Not |
|---|---|---|
| tarih | gözlem anındaki tarih | ham kayıt |
| platform | Windows / Linux | — |
| kimlik alanı | okunan ham dize | uydurma örnek DEĞİL |
| etiket | IMPLEMENTED (yalnız S1 sonrası) | §2 seviye kuralı |

> Bu satır **biçim** gösterir; hiçbir hücresinde gerçek veri **iddia
> edilmez**.

## §10 Sık Yapılan Hatalar ve SSS

### §10.1 Sık yapılan hatalar

| # | Hata | Sonuç | Önleme |
|---|---|---|---|
| 1 | UI tahmin kodunu keşif saymak | Sınır kayması | §1.3 — tahmin ≠ keşif |
| 2 | Şablon formu "doldurulmuş" sunmak | Sahte gözlem | form başlığı "(BOŞ)" |
| 3 | IOCTL numarası / modalias / `/sys` yolu uydurmak | Hallüsinasyon | §8.2 · §4.2 yasağı |
| 4 | `cihaz-erisimi` konusunu buraya taşımak | Çifte sahiplik | §8.1 — iki dal yaşar |
| 5 | K001 tarafı hakkında iddia kurmak | Yetki aşımı | §9.1 — UNKNOWN |
| 6 | IMPLEMENTED etiketini şablon bağlama | Sahte kanıt | yalnız S1 sonrası |
| 7 | `{...}` / `TBD` bırakmak | Okunamaz tablo | `UNKNOWN` / `BEKLEMEDE` |
| 8 | ADR'leri içeriklerinden özetlemek | Uydurma iddia | yalnız varlık iddiası |
| 9 | K000 kök index'e kendiliğinden yazmak | Çift-yazım | beyazliste — orkestratör |
| 10 | `DeviceRenderer.php`'yi keşif sınıfı saymak | Envanter şişirme | §1.1 — render ≠ keşif |

### §10.2 Sık sorulanlar

| # | Soru | Cevap |
|---|---|---|
| 1 | Repo'da OS keşif kodu var mı? | **Yok** — §1.2: grep 0 (4 PHP + 9 uzantı) |
| 2 | Kaç PHP tespit dosyası var? | **4** — §1.1 (`DeviceRenderer.php` dahil) |
| 3 | `DeviceRenderer` keşif mi? | Hayır — render/tier (§1.1/#4) |
| 4 | Gözlem formları dolduruldu mu? | Hayır — tümü BEKLEMEDE (Q-DO1/Q-DO2) |
| 5 | IOCTL numarası örneği var mı? | Hayır — `UNKNOWN` (§8.2, uydurma yasak) |
| 6 | Gerçek VID/PID var mı? | Hayır — `UNKNOWN` (Q-DO3, K001 sahipliği) |
| 7 | Eşik değerleri nereden? | Repo kodundan okundu (§1.3/#2: 767/1024/1440/2560/3840) |
| 8 | Cookie anahtarları ne? | `cm_viewport_w` / `cm_viewport_h` (§1.3/#5, satır 187–191) |
| 9 | Şablonlar kod mu? | Hayır — sınır şablonu (başlık notu) |
| 10 | `cihaz-erisimi` ile fark ne? | Keşif burada, erişim orada (§8.1) |

## §11 Uygulama Sırası (Checklist) ve Kapı Eşlemesi

### §11.1 Sıra kontrol listesi

| # | Adım | Bağımlılık | Durum |
|---|---|---|---|
| 1 | Repo kodu ölçümü (§1.1–§1.3) | — | ✅ bu oturum |
| 2 | Negatif kanıt taraması (§1.2) | — | ✅ bu oturum |
| 3 | Windows gözlem formları (§2–§3) | makine envanteri | ❌ BEKLEMEDE |
| 4 | Linux gözlem formları (§4–§5) | makine envanteri | ❌ BEKLEMEDE |
| 5 | Sürücü bağlama kaydı (§6) | 3–4 | ❌ BEKLEMEDE |
| 6 | Hata modeli gözlemi (§7) | 5 | ❌ BEKLEMEDE |
| 7 | IOCTL girişi (§8.2) | resmi doküman okuma | ❌ UNKNOWN |
| 8 | Sözleşme formları (§9) | 1–7 | ❌ form |
| 9 | Etiket güncelleme (IMPLEMENTED) | S1 kanıtı | ❌ yazılamaz |

### §11.2 Adım ↔ kapı eşlemesi (`-dogrulama.md`)

| Adım | İlgili kapı | Kapı durumu |
|---|---|---|
| 1–2 | G1 Repo · G2 Vault · G3 URL | ✅ kapandı |
| 3 | G4 Windows gözlem (W1–W5) | ❌ açık (Q-DD1) |
| 4 | G5 Linux gözlem (L1–L5) | ❌ açık (Q-DD1) |
| 5–6 | G6 Olay | ❌ açık (Q-DD3) |
| 7–8 | G7 Envanter | ❌ açık (Q-DD2) |
| 9 | G8 Kök kayıt | ⏳ orkestratör |

## §12 Çapraz Referanslar

| # | Dosya | İlişki |
|---|---|---|
| 1 | `os-donanim-tespiti-kapsam.md` | Kapsam, sınır, çelişki kaydı (mimari grup) |
| 2 | `os-donanim-tespiti-dogrulama.md` | Kapılar, S1–S5, formlar (kanıt grup) |
| 3 | `index.md` | Dal künyesi (mimari grup) |
| 4 | `../isletim-sistemi-api/cihaz-erisimi/index.md` | IOCTL/erişim sınırı — §8.1 |
| 5 | `../isletim-sistemi-audio/wasapi/index.md` | Ses yığını ayrı (13 md, dokunulmaz) |
| 6 | `K001-donanım/donanım-devices/index.md` | Gerçek VID/PID envanteri sahibi (Q-DO3) |
| 7 | `K001-donanım/donanaım-type/audio/audio-ornekler.md` | İskelet kaynağı (grup: kod, 529 satır) |
| 8 | `shared/src/Device/*.php` (4 dosya) | §1.1 — IMPLEMENTED uygulama kanıtı |
| 9 | ADR-017 · ADR-038 · ADR-064 | Yalnız **varlık** iddiası (kapsam §14) |

## §13 Uygulama Defteri (boş formlar)

### §13.1 Ortam kayıt formu (BOŞ)

| # | Alan | Değer |
|---|---|---|
| 1 | Gözlem ortamı (fiziksel/sanal) | — |
| 2 | OS adı + sürümü (Windows) | — |
| 3 | Çekirdek sürümü (Linux) | — |
| 4 | Erişim yetkisi (yönetici/root?) | — |
| 5 | Kayıt yolu (ham dosyalar) | — |
| 6 | Tarih | — |

### §13.2 Tak-çıkar senaryosu kayıt formu (BOŞ)

| # | Alan | Değer |
|---|---|---|
| 1 | Cihaz (tip, sahiplik) | — |
| 2 | Takılma zamanı | — |
| 3 | Olay alındı mı (W3/L3) | — |
| 4 | Bağlama sonucu | — |
| 5 | Çıkarma zamanı | — |
| 6 | Kaldırma sonucu | — |
| 7 | Uygulama tepkisi (CoreMusic) | — |

### §13.3 Hata ayıklama akışı

| # | Belirti | Olası neden | Sonraki adım |
|---|---|---|---|
| 1 | Liste boş dönüyor | yetki · filtre · sanal makine | §2.4/#1 · `-dogrulama.md` §12.3/#1 |
| 2 | Olay gelmiyor | dinleme yüzeyi yanlış | §3.2 · `-dogrulama.md` §12.3/#2 |
| 3 | Kimlik biçimi şüpheli | uydurma riski | §4.2 — ham kopyala, yorumlama |
| 4 | Bağlama reddedildi | yetki/İmza | §7/#4 |
| 5 | İki sürüm çelişiyor | ölçüm metodolojisi | `-dogrulama.md` §12.8 |

### §13.4 Kapanış kontrol listesi (bu dosya için)

| # | Kontrol | Durum |
|---|---|---|
| 1 | §1 repo kodu ölçümle yazıldı mı? | ✅ 4 dosya + 10 negatif satır |
| 2 | Şablonlar "kod" olarak sunuldu mu? | ✅ hayır |
| 3 | Uydurma IOCTL/VID/PID/`/sys` yolu yazıldı mı? | ✅ hayır (`UNKNOWN`) |
| 4 | `parent:` alanı var mı? | ✅ hayır (`parent_id:`) |
| 5 | Authority tek satır, son satır mı? | ✅ evet |
| 6 | `{...}` / `TBD` gerçek yer tutucu var mı? | ✅ hayır |
| 7 | `cihaz-erisimi` ayrıntısı taşındı mı? | ✅ hayır (yalnız sınır §8.1) |
| 8 | K001 tarafı için iddia kuruldu mu? | ✅ hayır (§9.1 UNKNOWN) |
| 9 | Satır sayısı ≥500 mü? | ölçüm sonrası |
| 10 | Fiziksel/gözlemsel ölçüm iddiası? | ✅ 0 (tümü ⚠️ VERIFICATION REQUIRED) |

---

### §13.5 Bölüm↔Kanıt Eşleme Tablosu (hangi bölüm hangi kanıt türünde)

| # | Bölüm | Kanıt türü | Durum |
|---|-------|-----------|-------|
| 1 | §1 Gerçek repo kodu | S1 — ölçülmüş PHP | ✅ kanıt |
| 2 | §2 Windows Enumeration (W1–W2) | şablon | ölçülmedi · ⚠️ VERIFICATION REQUIRED |
| 3 | §3 Windows Hotplug (W3) | şablon | ölçülmedi · ⚠️ VERIFICATION REQUIRED |
| 4 | §4 Linux Enumeration / sysfs (L1–L2) | şablon | ölçülmedi · ⚠️ VERIFICATION REQUIRED |
| 5 | §5 Linux Hotplug uevent/udev (L3–L4) | şablon | ölçülmedi · ⚠️ VERIFICATION REQUIRED |
| 6 | §6 Sürücü Bağlama | şablon | ölçülmedi · ⚠️ VERIFICATION REQUIRED |
| 7 | §7 Başarısız-Keşif Hata Modeli | şablon | ölçülmedi · ⚠️ VERIFICATION REQUIRED |
| 8 | §8 OID/IOCTL Yüzeyi | sınır tanımı | iddia yok (cihaz-erisimi ≠ keşif) |
| 9 | §9 K001 `donanım-api` | sınır tanımı | iddia yok (§9.1 UNKNOWN) |
| 10 | §10 Sık Yapılan Hatalar / §11 Checklist | sentez | bu dosya içi |
| 11 | §12 Çapraz Referanslar / §13 Uygulama Defteri | kayıt | boş formlar |

**Kural:** `ölçülmedi` satırları **kullanılmış kanıt sayılmaz** — yalnız §1 satırları kanıttır. Bu tabloya uydurulmuş ölçüm değeri, marka, IOCTL numarası, VID/PID veya URL yazılmaz (zero-hallucination §3).

### §13.6 Kullanım Sırası (bu dosya nasıl okunur)

1. Önce §1 — repoda **gerçek** kanıt buradadır; şablonların karşılığı burada aranır.
2. Görevin platformunu belirle → yalnız hedef platformun §2–§7 şablonunu seç.
3. Şablonu kopyala → `⚠️ VERIFICATION REQUIRED` satırlarını **gerçek ölçümle** doldur; boş bırakılan değer uydurulmaz.
4. Sonuçları §13.1–§13.2 formlarına yaz; §13.4/#9 satırı ölçülmeden "doldu" denmez.
5. Kapanışta §13.4 + §14 kontrolü çalıştır (Authority tek satır, dosyanın son satırı; `parent:` yok).
6. Bağlantılar: `index.md` (giriş) · `os-donanim-tespiti-dogrulama.md` (kapılar) · `os-donanim-tespiti-kapsam.md` (kapsam).

---

## §14 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya 4 PHP repo dosyası ölçümü
(§1.1 — toplam/dolu satır biçimiyle), 10 negatif repo ölçümü (§1.2 — grep 0
ve 9 uzantı 0) ve 3 HEAD-200 web kaynağı iddiası (frontmatter evidence)
üzerine kuruludur. §2–§13'ün **tamamı şablondur**; hiçbir hücresi gerçek
gözlem, gerçek kod veya gerçek envanter olarak sunulmamıştır. IOCTL numarası,
VID/PID, modalias/compatible dizesi, `/sys` alt yolu ve olay anahtarı
**uydurulmamıştır** — tümü `UNKNOWN` / `BEKLEMEDE` işaretlidir. OS-seviyesi
keşif kodu repo'da **0'dır** (§1.2); UI tahmin kodu keşif olarak
**sunulmamıştır** (§1.3). K001 `donanım-api` tarafı hakkında **iddia
kurulmamıştır** (§9.1). ADR'ler yalnız **varlık** iddiasıdır. `{...}` ve
`TBD` yer tutucusu kullanılmamıştır. Fiziksel/gözlemsel ölçüm **0** — tüm
platform gözlem iddiaları `⚠️ VERIFICATION REQUIRED` altındadır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
