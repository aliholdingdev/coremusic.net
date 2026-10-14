---
title: "OS Donanım Tespiti — Kapsam"
type: architecture-module
node_id: os-donanim-tespiti-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/os-donanim-tespiti
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "shared/src/Device/DeviceDetector.php (272 satır · class satır 19 · detect() satır 57 — IMPLEMENTED, uygulama-seviyesi tespit)"
  - "shared/src/Device/DeviceManager.php (588 satır · 7 cihaz sabiti satır 29–40 · ALL_DEVICES satır 37–40 — IMPLEMENTED)"
  - ".ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md · ADR-038-8-1-sound-card-chip-selection.md · ADR-064-electronics-platform-architecture.md (dosya varlıkları doğrulandı 2026-10-13 glob)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ (HTTP 200, 2026-10-13, HEAD)"
  - "https://docs.kernel.org/driver-api/driver-model/index.html (HTTP 200, 2026-10-13, HEAD)"
  - "https://devicetree.org/ (HTTP 200, 2026-10-13, HEAD)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · OS-seviyesi tespit kodu repo'da YOK (grep 0) · fiziksel ölçüm YOK · tüm platform iddiaları ⚠️ VERIFICATION REQUIRED"
open_questions:
  - "Q-DT1: Hedef platform seti K000 Q4 ile aynı — Win32 mi, POSIX mı, ikisi mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DT2: Repo'da OS-seviyesi donanım tespit kodu YOK — uygulama-seviyesi DeviceDetector bunun yerine mi geçiyor? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DT3: PnP hotplug senaryosu CoreMusic'te gerçek mi (USB ses kartı tak-çıkar) — sahibi K000 mi K003 mü? → ⚠️ VERIFICATION REQUIRED"
date: "2026-10-13"
updated: "2026-10-13"
group: mimari
---

# os-donanim-tespiti-kapsam — OS Donanım Tespiti Kapsamı (K000 Kademe-2)

> **Yazım notu:** Bu dosya `os-donanim-tespiti/` alt-dalının **kapsam**
> belgesidir: işletim sisteminin donanımı nasıl keşfettiği/sürdüğü sınırı —
> device enumeration, PnP/ekleme-çıkarma olayları, sürücü bağlama, cihaz
> ağacı (device tree / SetupAPI / devnode), donanım-ID eşleme ve OID/ioctl
> yüzeyi. Bu dal K001 `donanaım-type` (tür sınıflandırma) ve K001
> `donanım-devices` (cihaz envanteri) ile **çapraz bağlantılıdır ama sahiplik
> K000'dedir** (`.ai/CLAUDE.md` §2: "Çekirdek sürücü kodu ring-0 → K000").

## §1 ELI15

Bilgisayar açıldığında hiçbir donanım "ben buradayım" diye bağırmaz. İşletim
sistemi bir **keşif turu** atar: anakarttaki yolları (bus) tek tek tarar,
bağlı her cihazın **kimlik bilgisini** okur (vendor/product ID), bu kimliğe
uygun **sürücü modülünü** arar, bulursa yükleyip bağlar ve cihazı bir **ağaç**
içine yerleştirir (Windows'ta devnode ağacı, Linux'ta sysfs ağacı, gömülü
sistemlerde device-tree). Bir cihaz takılıp çıkarıldığında bu turun **kısmi
tekrarı** çalışır: PnP manager bir olay yayar, sistem cihazı kaldırır veya
yeniden keşfeder. Bu dosya bu sürecin **sınırlarını** çizer: hangi adım hangi
OS bileşeninin, hangi adım CoreMusic'in (K001+) ve hangi adım sürücü
geliştiricisinin (K000 çekirdek) sorumluluğundadır.

> ⚠️ VERIFICATION REQUIRED: Bu dosyadaki platform-specific iddialar (SetupAPI,
> devnode, sysfs, udev, device-tree, uevent) **resmi dokümanların varlık
> iddialarıdır** — bu oturumda tek tek API sayfaları okunmadı, yalnızca kök
> sayfalar HEAD ile 200 döndürülmüştür (§11). CoreMusic'in OS-seviyesi
> donanım tespiti için **repo kodu YOK** (`shared/src/Device/*.php` içinde
> `sysfs|SetupDi|udev|devnode|PnP|ioctl` grep = 0 isabet, 2026-10-13) — bu
> dosya **mevcut bir keşif sistemi tarif etmez**, OS'nin genel keşif
> modelini belgeler ve CoreMusic sınırını çizer. Uydurma API çağrısı, uydurma
> IOCTL numarası, uydurma VID/PID yazılmaz.

## §2 Kapsam — İÇİNDE NE var

| # | Kapsam İÇİ | Durum |
|---|---|---|
| A1 | İşletim sisteminin donanım keşif (enumeration) modeli — genel sıra | ✅ mimari tanım |
| A2 | Windows yüzeyi: SetupAPI · devnode · PnP manager · WM_DEVICECHANGE | ⚠️ resmi doküman kökü HEAD-200 (§11) |
| A3 | Linux yüzeyi: sysfs · udev/uevent · device-tree · driver-model | ⚠️ resmi doküman kökü HEAD-200 (§11) |
| A4 | Hotplug / ekleme-çıkarma olay modeli (iki platform) | ✅ sınır tarifi |
| A5 | Sürücü modülü bağlama (INF / alias / modalias) | ✅ sınır tarifi |
| A6 | Cihaz-ID eşleme (VID/PID · compatible · instance) | ✅ sınır tarifi |
| A7 | Başarısız-keşif hata modeli (bulunamayan sürücü, çakışan ID, yetki) | ✅ hata sınıflandırma |
| A8 | OID/ioctl yüzeyinin keşifle ilişkisi (yalnız sınır) | ✅ sınır — ayrıntı `cihaz-erisimi` |
| A9 | OS↔donanım API sınırı (K001 `donanım-api` ile sözleşmenin **OS tarafı**) | ✅ sınır |
| A10 | CoreMusic repo'daki uygulama-seviyesi tespit kodunun durumu (DeviceDetector/DeviceManager) | ✅ disk kanıtı (IMPLEMENTED) |
| A11 | Çelişki kaydı: v2 mini-ağaç eksikliği + `cihaz-erisimi` farkı | ✅ kayıtlı (§10) |
| A12 | Etiket disiplini (IMPLEMENTED … UNKNOWN) | ✅ kural tablosu |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Cihaz erişim API'si: `/dev`, fd, ioctl ayrıntısı, mmap, yetki | `isletim-sistemi-api/cihaz-erisimi/` | `cihaz-erisimi-anayasa.md` §2.1–§2.6 |
| B2 | Sistem çağrılarının kendisi | `isletim-sistemi-api/sistem-cagrilari/` | `isletim-sistemi-api/index.md` §3 |
| B3 | Ses yığını, WASAPI/ASIO sürücü modeli, cihaz kaybı davranışı | `isletim-sistemi-audio/` · `wasapi/` (13 md) | glob 2026-10-13 |
| B4 | Donanım **tür sınıflandırma** (hangi sınıf cihaz var) | K001 `donanaım-type/` | bu dal ≠ sınıflandırma (§9) |
| B5 | Donanım **envanteri** (gerçekten hangi cihazlar elde) | K001 `donanım-devices/` | bu dal ≠ envanter (§9) |
| B6 | Firmware yükleme/güncelleme yaşam döngüsü | K005-firmware (`update/`, `boot/`) | firmware ≠ OS keşfi (§9) |
| B7 | PHP uygulamasının cihaz bazlı UI kararı | K001+ · `DeviceManager` tüketimi | repo §10 |
| B8 | Sürücü **kodunun kendisi** (ring-0 C/C++) | K000 çekirdek sorumluluğu (anayasa §2) — repo'da 0 | `*.cpp=0` (§10) |
| B9 | Kernel içi zamanlama/çalıştırma | `isletim-sistemi-kernel/` | komşu K000 dalı |
| B10 | HTTP/REST API, sunucu kurulumu | K011/K012 · K002 | `isletim-sistemi-api/index.md` §4 |

## §4 Enumeration — Genel Keşif Sırası (platform bağımsız model)

> Bu tablo **sınır tarifi**dir: her adımın sahibi yazılır. Adım
> sayıları/akış sırası **modeldir**, bir platformun birebir kod akışı
> değildir; platforma özgü doğrulama `-dogrulama.md` §6 kapısındadır.

| # | Adım | Ne olur | Sahip | Etiket |
|---|---|---|---|---|
| 1 | Firmware/boot keşfi | Donanımın ilk listesi (ACPI/DT/sabit liste) | firmware (K005) + boot | DOCUMENTED (genel) |
| 2 | Bus tarama | PCIe/USB/SPI/I2C vb. yollar taranır | OS çekirdek (K000) | DOCUMENTED (genel) |
| 3 | Ham kimlik okuma | Vendor/device ID, slot, topoloji konumu okunur | OS çekirdek (K000) | DOCUMENTED (genel) |
| 4 | Cihaz ağacına yerleştirme | Devnode/sysfs/DT düğümü oluşturulur | OS çekirdek (K000) | DOCUMENTED (genel) |
| 5 | ID eşleme | Kimlik → sürücü adayı eşleşir (INF/modalias/DT compatible) | OS + sürücü deposu | DOCUMENTED (genel) |
| 6 | Sürücü bağlama | Modül yüklenir, `probe`/`AddDevice` çağrılır | OS + sürücü (K000 çekirdek) | DOCUMENTED (genel) |
| 7 | Kullanıcıya arayüz açma | `/dev` düğümü, cihaz IOCTL yüzeyi, olay bildirimi | OS → uygulama (K001) | sınır: `cihaz-erisimi` |
| 8 | Kaldırma (çıkarma) | Olay → sürücü bırak → ağaçtan düşürme | OS çekirdek (K000) | DOCUMENTED (genel) |

**Çekirdek prensip:** 1–8. adımların tamamı işletim sistemi ve sürücü
sorumluluğundadır; bir PHP uygulaması bu adımların **hiçbirine** doğrudan
giremez — en fazla 7. adımın çıktısını (cihaz listesi/arayüzü) **okur**.

## §5 Windows Yüzeyi (SetupAPI · devnode · PnP manager)

> ⚠️ VERIFICATION REQUIRED: Bu bölüm Win32 yüzeyinin **doküman-varlık**
> tarifidir; kök sayfalar HEAD-200 ile doğrulanmıştır (§11 satır 1–2), tek
> tek API sayfaları bu oturumda okunmamıştır. IOCTL/struct/enum **adı
> uydurulmaz**; genel bilinen yüzey adları (SetupAPI, devnode, PnP manager)
> kullanılır.

| # | Bileşen | Görev | Keşif rolü |
|---|---|---|---|
| 1 | PnP manager (çekirdek) | Donanım olaylarını koordine eder | 2–6. adımların sahibi |
| 2 | SetupAPI (kullanıcı uzayı kütüphanesi) | Cihaz listeleme/sorgulama arayüzü | 3–5. adım okuması |
| 3 | Devnode (cihaz düğümü ağacı) | Her cihaz için çekirdek içi kayıt | 4. adımın veri yapısı |
| 4 | INF dosyası | Sürücü ↔ cihaz-ID eşleme tarifi | 5. adımın deposu |
| 5 | WM_DEVICECHANGE (pencere mesajı) | Uygulamaya ekleme-çıkarma bildirimi | 8. adım → uygulama köprüsü |

### §5.1 Windows keşif-sonrası okuma yüzeyi (sınır)

| # | Okuma | Amaç | Sahip |
|---|---|---|---|
| 1 | Cihaz listesi sorgusu (SetupAPI ile) | "Ne var?" | uygulama (K001 tüketir) |
| 2 | Cihaz özelliği okuma (sınıf, ID, durum) | "Nasıl bir cihaz?" | uygulama |
| 3 | IOCTL yüzeyi | "Cihaza özgü komut" | **sınır**: `cihaz-erisimi` |
| 4 | Olay dinleme (WM_DEVICECHANGE) | "Eklendi/çıkarıldı" | uygulama (K001 tüketir) |

**Ayrım kuralı:** Windows yüzeyinde de "keşif" (bu dal) ile "erişim" (`cihaz-erisimi`)
ayrıdır: bir uygulama SetupAPI ile cihaz **listesini** okur (keşif çıktısı),
cihazın kendisine **ioctl** ile komut verir (erişim). Bu dosya yalnız 1, 2 ve
4'ü kapsar; 3. satır `cihaz-erisimi-anayasa.md` §2.3'ünündür.

### §5.2 Windows keşif yüzeyi — alt-bileşen envanteri (doküman-varlık düzeyi)

| # | Alt-bileşen | Görev | Keşif rolü | Etiket |
|---|---|---|---|---|
| 1 | PnP manager (çekirdek) | Donanım olaylarını koordine eder | 2–6. adımların sahibi | DOCUMENTED (genel) |
| 2 | Configuration Manager (cfgmgr32) | Devnode ağacı sorgulama yüzeyi | 4. adım okuması | DOCUMENTED (genel) |
| 3 | SetupAPI | Cihaz listeleme/sorgulama kütüphanesi | 3–5. adım okuması | DOCUMENTED (genel) |
| 4 | Device installation | Sürücü kurulum/eşleme servisi | 5–6. adım | DOCUMENTED (genel) |
| 5 | Driver store | İmzalı sürücü paket deposu | 5. adım deposu | DOCUMENTED (genel) |
| 6 | INF dosyası | Sürücü ↔ cihaz-ID eşleme tarifi | 5. adımın deposu | DOCUMENTED (genel) |
| 7 | WM_DEVICECHANGE | Uygulamaya ekleme-çıkarma bildirimi | 8. adım → uygulama köprüsü | DOCUMENTED (genel) |
| 8 | Devnode kaydı | Cihaz başına çekirdek içi kayıt | 4. adımın veri yapısı | DOCUMENTED (genel) |

**Etiket notu:** 8 satır da "DOCUMENTED (genel)"tür: kök doküman sayfaları
HEAD-200 ile doğrulanmıştır (§11 satır 1–2); tek tek API sayfaları bu oturumda
**okunmamıştır**, struct/enum/IOCTL **adı uydurulmaz**. CoreMusic'te bu
yüzeyin **kullanım kodu YOK** (`*.cs = 0` · `SetupDi` grep = 0, §13/4–6) →
CoreMusic tarafı **PLANNED**'dir.

### §5.3 Windows keşif yüzeyi — CoreMusic tüketim senaryosu (şablon, kod yok)

| # | Senaryo | Beklenen OS çıktısı | CoreMusic sahibi | Durum |
|---|---|---|---|---|
| 1 | USB ses kartı takıldı | yeni cihaz + sınıf bilgisi | K003 audio (akış) · K000 (keşif) | PLANNED (kod 0) |
| 2 | Cihaz listesi okuma | mevcut cihazların ID listesi | uygulama (K001) | PLANNED (kod 0) |
| 3 | Cihaz durum değişimi | hazır/hata durumu | uygulama (K001) | PLANNED (kod 0) |
| 4 | Cihaz çıkarıldı | olay + kaldırma | K003 audio (akış kesme) | PLANNED (kod 0) |
| 5 | Sürücü bulunamadı | hata sınıfı (§8.2/#2) | teşhis (K000 observability) | PLANNED (kod 0) |

**Yasak:** Bu tabloya gerçek bir cihaz adı, gerçek bir IOCTL numarası veya
gerçek bir hata kodu **uydurularak** yazılamaz; gerçek değerler yalnız çalışan
bir Windows sisteminde gözlemle girer → kapı `-dogrulama.md` §6.2.

## §6 Linux Yüzeyi (sysfs · udev/uevent · device-tree · driver-model)

> ⚠️ VERIFICATION REQUIRED: Kök sayfalar HEAD-200 (§11 satır 3–6). Dosya
> yolları (`/sys`, `/dev`) genel bilinen Linux yüzeyidir; bu oturumda çalışan
> bir Linux sisteminde **gözlemlenmemiştir** — gözlem kapıları
> `-dogrulama.md` §6'dadır.

| # | Bileşen | Görev | Keşif rolü |
|---|---|---|---|
| 1 | driver-model (bus/device/driver) | Çekirdekte cihaz-sürücü bağlama çekirdeği | 2–6. adımların sahibi |
| 2 | sysfs | Cihaz ağacının kullanıcı uzayına yansıması | 3–5. adım okuması |
| 3 | uevent | Çekirdek→udev olay bildirimi | 8. adımın çekirdek tarafı |
| 4 | udev | Olay → kural → `/dev` düğümü oluşturma | 7. adımın kurulumu |
| 5 | device-tree | Gömülü sistemde donanım ağacı (compatible anahtarı) | 1–3. adım (gömülü) |
| 6 | modalias / alias | Kimlik → modül adı eşleme anahtarı | 5. adımın deposu |

### §6.1 Linux keşif-sonrası okuma yüzeyi (sınır)

| # | Okuma | Amaç | Sahip |
|---|---|---|---|
| 1 | sysfs ağaç okuması | "Ne var, durumu ne?" | uygulama (K001 tüketir) |
| 2 | uevent/udev olay dinleme | "Eklendi/çıkarıldı" | uygulama (K001 tüketir) |
| 3 | `/dev` düğümü üzerinden erişim | "Cihaza komut" | **sınır**: `cihaz-erisimi` |
| 4 | device-tree okuması (gömülü) | "Donanım ağacı" | uygulama (K001 tüketir) |

### §6.2 Linux keşif yüzeyi — alt-bileşen envanteri (doküman-varlık düzeyi)

| # | Alt-bileşen | Görev | Keşif rolü | Etiket |
|---|---|---|---|---|
| 1 | driver-model (bus/device/driver) | Çekirdekte cihaz-sürücü bağlama çekirdeği | 2–6. adımların sahibi | DOCUMENTED (genel) |
| 2 | sysfs | Cihaz ağacının kullanıcı uzayına yansıması | 3–5. adım okuması | DOCUMENTED (genel) |
| 3 | sysfs kural dokümanı | `/sys` yapısı ve kural dışı davranışlar | 3. adım doğrulaması | DOCUMENTED (HEAD-200 §11/5) |
| 4 | uevent | Çekirdek→udev olay bildirimi | 8. adımın çekirdek tarafı | DOCUMENTED (genel) |
| 5 | udev | Olay → kural → `/dev` düğümü oluşturma | 7. adımın kurulumu | DOCUMENTED (genel) |
| 6 | device-tree | Gömülü sistemde donanım ağacı | 1–3. adım (gömülü) | DOCUMENTED (HEAD-200 §11/6) |
| 7 | modalias / alias | Kimlik → modül adı eşleme anahtarı | 5. adımın deposu | DOCUMENTED (genel) |
| 8 | /proc device sorguları | Bazı alt sistemler için ek okuma yolu | 3. adım (alt sisteme bağlı) | DOCUMENTED (genel) |

**Etiket notu:** 8 satır da "DOCUMENTED (genel)"tür: kök doküman sayfaları
HEAD-200 (§11 satır 3–6); tek tek sayfalar okunmamıştır; `/sys` alt yolları,
uevent anahtarları ve modalias biçimleri **uydurulmaz**. CoreMusic'te bu
yüzeyin **kullanım kodu YOK** (`sysfs|udev` grep = 0, §13/4) → PLANNED.

### §6.3 Linux keşif yüzeyi — CoreMusic tüketim senaryosu (şablon, kod yok)

| # | Senaryo | Beklenen OS çıktısı | CoreMusic sahibi | Durum |
|---|---|---|---|---|
| 1 | USB ses kartı takıldı | uevent + sysfs düğümü | K003 audio (akış) · K000 (keşif) | PLANNED (kod 0) |
| 2 | Cihaz listesi okuma | sysfs ağacı listesi | uygulama (K001) | PLANNED (kod 0) |
| 3 | Cihaz durum değişimi | sysfs durum dosyası | uygulama (K001) | PLANNED (kod 0) |
| 4 | Cihaz çıkarıldı | uevent + düğüm silme | K003 audio (akış kesme) | PLANNED (kod 0) |
| 5 | Modül bulunamadı | modalias eşleşmedi (§8.2/#2) | teşhis (K000 observability) | PLANNED (kod 0) |
| 6 | Gömülü boot'ta keşif | device-tree okuması | firmware/boot (K005) | PLANNED (kod 0) |

## §7 Hotplug / Ekleme-Çıkarma Olay Modeli

| # | Olay evresi | Windows | Linux | Sahip |
|---|---|---|---|---|
| 1 | Fiziksel bağlantı | PnP manager duyarsızlaştırma zinciri | bus/usb/sdhci vb. çekirdek duyarsızlaştırma | OS çekirdek (K000) |
| 2 | Keşif tekrarı | devnode yeniden oluşturulur | sysfs düğümü + uevent | OS çekirdek (K000) |
| 3 | Sürücü yeniden bağ | AddDevice/probe yeniden | modprobe/probe | OS + sürücü (K000) |
| 4 | Uygulama bildirimi | WM_DEVICECHANGE | udev kuralı / uevent dinleme | OS → uygulama (K001) |
| 5 | Kaldırma | cihaz ağacından düşme | sysfs silme + uevent | OS çekirdek (K000) |
| 6 | Uygulama tepkisi | listeyi yenile / akışı kes | listeyi yenile / akışı kes | uygulama (K001) — CoreMusic'te K003 audio cihaz kaybı |

**CoreMusic bağı:** 6. satırın "akışı kes" tepkisi ses yığınında **K003-audio**
sorumluluğundadır (ADR-017 ASIO/WASAPI bağlamı); bu dal yalnız 1–5. satırları
belgeler. Q-DT3: CoreMusic'te gerçek bir tak-çıkar senaryosu (USB ses kartı)
tanımlı mı → ⚠️ VERIFICATION REQUIRED.

### §7.1 Hotplug olay sınıflandırması (genel model)

| # | Olay sınıfı | Ne tetikler | Keşif etkisi | Sahip |
|---|---|---|---|---|
| 1 | Ekleme (arrival) | fiziksel bağlantı | keşif turu + sürücü bağlama | OS çekirdek (K000) |
| 2 | Kaldırma (removal) | fiziksel ayrılma | ağaçtan düşürme + sürücü bırakma | OS çekirdek (K000) |
| 3 | Durum değişimi | güç/ağ/hazır durumu | ağaçta güncelleme, keşif tekrarı yok | OS çekirdek (K000) |
| 4 | Sürücü yeniden bağ | modül yükleme/kaldırma | probe/AddDevice yeniden | OS + sürücü (K000) |
| 5 | Başlatma başarısız | probe hatası | hata sınıfı (§8.2) | OS + sürücü (K000) |

### §7.2 CoreMusic hotplug etki alanı (sınır)

| # | Etki | CoreMusic sahibi | Bu dalın tutumu |
|---|---|---|---|
| 1 | Ses akışının kesilmesi | K003-audio (`device/`, `driver/`) | yalnız atıf — iddia yok |
| 2 | Cihaz listesinin yenilenmesi | uygulama (K001) | keşif çıktısı bu dalın (§4/#7) |
| 3 | UI'da cihaz uyarısı | uygulama (K001+) | iddia yok |
| 4 | Olay kaydının tutulması | K000-observability | iddia yok |
| 5 | yetki reddi durumunda tepki | `cihaz-erisimi` yetki modeli | sınır (§8.2/#4) |

## §8 Sürücü Modülü Bağlama ve Cihaz-ID Eşleme

### §8.1 Bağlama anahtarları

| # | Anahtar | Platform | Ne eşleşir | Örnek biçim (uydurulmaz, biçim düzeyi) |
|---|---|---|---|---|
| 1 | INF içi ID listesi | Windows | cihaz-ID ↔ sürücü paketi | vendor+product biçiminde ID satırları |
| 2 | modalias | Linux | sysfs modalias ↔ modül alias'ı | bus tipi + vendor/product biçiminde |
| 3 | DT compatible | gömülü (Linux) | cihaz ağacı düğümü ↔ sürücü | üretici-model dizesi |
| 4 | Vendor/Product ID | evrensel | donanımın kimliği | USB VID/PID biçimi (4+4 hex) |
| 5 | Sınıf kodu | evrensel | cihaz sınıfı ↔ sınıf sürücüsü | sınıf kodu (ör. ses sınıfı) |

**Yasak:** Bu tabloya **gerçek** bir VID/PID, INF satırı, modalias dizesi
veya compatible dizesi **uydurularak** yazılamaz; gerçek değerler yalnız
(a) çekirdek ağacından okunmuş gözlem, (b) donanımın kendi datasheet'i, (c)
sürücü deposu kaydı — üçünden biriyle girer; hiçbiri bu oturumda yok →
`UNKNOWN`.

### §8.2 Başarısız-keşif hata modeli

| # | Hata sınıfı | Belirti | Sahip / çözüm yüzeyi |
|---|---|---|---|
| 1 | Kimlik okunamadı | Bus taraması cihazı hiç görmez | OS çekirdek / donanım (K001) |
| 2 | Eşleşme yok | ID okundu, sürücü adayı yok | sürücü deposu (K000 çekirdek) |
| 3 | Sürücü yükleme hatası | Modül var, probe başarısız | sürücü (K000 çekirdek) |
| 4 | Yetki reddi | Keşif/erişim yetkisi eksik | OS güvenlik (`cihaz-erisimi` yetki §2.5) |
| 5 | Çakışan ID | İki sürücü aynı ID'yi iddia eder | OS çözüm politikası |
| 6 | Olay kaybı | Hotplug olayı uygulamaya ulaşmaz | OS → uygulama köprüsü (§7 satır 4) |

## §9 OS↔Donanım API Sınırı (K001 `donanım-api` ile sözleşme — OS tarafı)

> Bu bölüm sözleşmenin **yalnız OS tarafını** yazar: OS ne **verir**
> (keşif çıktısı) ve ne **ister** (donanımın sunması gereken). K001'in bu
> sözleşmeyi nasıl tükettiği K001 `donanım-api` dosyalarının konusudur — bu
> dosya K001'in tarafı hakkında **iddia kurmaz**.

| # | Sözleşme kalemi | Yön | Sahip |
|---|---|---|---|
| 1 | Cihaz kimliği (vendor/product/sınıf) | donanım → OS | OS okur (bu dal) |
| 2 | Cihaz listesi/ağacı | OS → uygulama | OS verir, uygulama okur |
| 3 | Ekleme/çıkarma olayı | OS → uygulama | OS verir, uygulama dinler |
| 4 | Sürücü bağlama durumu | donanım+OS ↔ sürücü | OS+K000 çekirdek |
| 5 | IOCTL/OID komut yüzeyi | uygulama → cihaz | **`cihaz-erisimi`** sınırı |
| 6 | Firmware yükleme | uygulama/OS → cihaz | **K005-firmware** sınırı |

### §9.1 Sözleşme kalemlerinin etiket durumu

| # | Kalem | OS tarafı durumu | K001 tarafı durumu |
|---|---|---|---|
| 1 | Cihaz kimliği okuma | DOCUMENTED (genel model §4–§6) | UNKNOWN (tüketim kodu aranmadı) |
| 2 | Cihaz listesi/ağacı | DOCUMENTED (genel) | UNKNOWN (tüketim kodu aranmadı) |
| 3 | Ekleme/çıkarma olayı | DOCUMENTED (genel §7) | UNKNOWN (dinleme kodu yok, §13/4) |
| 4 | Sürücü bağlama durumu | DOCUMENTED (genel §8) | — (OS+sürücü işi, uygulama değil) |
| 5 | IOCTL/OID yüzeyi | — (`cihaz-erisimi` belgeler) | UNKNOWN |
| 6 | Firmware yükleme | — (K005 belgeler) | UNKNOWN |

**Kural:** Bu tablo K001'in tarafı hakkında **iddia kurmaz**; "UNKNOWN" =
tüketim kodu bu oturumda aranmadı/dosyalanmadı — yokluğu değil, **bilinmezliği**
işaretler. K001 `donanım-api` dosyaları kendi iddialarını kendi
`-dogrulama.md` dosyalarında kanıtlar; bu dosya onları **tekrarlamaz**.

## §10 Çelişki Kaydı (gizleme yasak)

### §10.1 v2 mini-ağaç eksikliği — PROPOSED — APPROVAL REQUIRED

| # | Konu | Kaynak | Durum |
|---|---|---|---|
| 1 | v2 mini-ağaçta K000 kademe-2 listesinde `os-donanim-tespiti` **YOK** | `.ai/architecture/index.md` K000 satırı (`-bagimliliklar · -dogrulama`) · `K000-isletim-sistemi/index.md` §4 envanter tablosu (12 düğüm) · §GRUPLAR (12 Kademe-2 satırı) | çelişki kayıtlı |
| 2 | Yeni ihtiyaç | prompt §8 K000 sınırı ("donanım keşfi OS'nindir") + K001 çapraz referans (donanaım-type ≠ keşif, donanım-devices ≠ envanter-olay) | gerekçe |
| 3 | Klasör açımı onayı | Vault Steward 2026-10-13 (bu görev beyazlistesi) | ✅ ONAYLI |
| 4 | K000 kök `index.md` children/§GRUPLAR + registry metin revizyonu | orkestratöre devredildi (çift-yazım çakışması yasak) | ⏳ **APPROVAL REQUIRED** |
| 5 | Etiket | — | **PROPOSED** (dal içerikleri DOCUMENTED/IMPLEMENTED sınırları içinde §12) |

### §10.2 `cihaz-erisimi` ile fark — ikisi de yaşar

| # | Konu | `cihaz-erisimi` | `os-donanim-tespiti` (bu dal) |
|---|---|---|---|
| 1 | Ne | Cihaz erişim **API çağrısı yüzeyi**: `/dev`, fd, ioctl, mmap, yetki | Enumeration/sürücü bağlama **yaşam döngüsü**: keşif, olay, eşleme, kaldırma |
| 2 | Zaman | Cihaz **zaten keşfedilmişken** | Cihazın **keşif anı** ve tak-çıkar anı |
| 3 | Sahip dosya | `isletim-sistemi-api/cihaz-erisimi/{index,anayasa,surucu,ornekler}.md` (4 dosya) | bu dalın 4 dosyası |
| 4 | IOCTL | ana konu (anayasa §2.3) | yalnız sınır (§8.1/#5, §9/#5) |
| 5 | Hotplug | anayasa §2.4'te bir satır (olay **dinleme** yüzeyi) | olayın **keşif tekrarı** ve sürücü yeniden bağlama (§7) |
| 6 | Karar | iki dal da **yaşar**; tekrar yazma yasak: erişim ayrıntısı buraya, keşif ayrıntısı oraya | — |

## §11 Web Kaynakları (HEAD 2026-10-13 — yalnız 200/202 kaydedildi)

| # | URL | Yöntem | HTTP | Kullanım |
|---|---|---|---|---|
| 1 | https://learn.microsoft.com/en-us/windows/win32/api/setupapi/ | HEAD (Invoke-WebRequest, 20 sn) | ✅ 200 | SetupAPI doküman kökü (§5) |
| 2 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | HEAD (aynı yöntem) | ✅ 200 | Windows kernel sürücü doküman kökü (§5) |
| 3 | https://docs.kernel.org/driver-api/driver-model/index.html | HEAD (aynı yöntem) | ✅ 200 | Linux driver-model (§6) |
| 4 | https://docs.kernel.org/driver-api/infrastructure.html | HEAD (aynı yöntem) | ✅ 200 | Linux sürücü altyapısı (§6) |
| 5 | https://docs.kernel.org/admin-guide/sysfs-rules.html | HEAD (aynı yöntem) | ✅ 200 | sysfs kural dokümanı (§6) |
| 6 | https://devicetree.org/ | HEAD (aynı yöntem) | ✅ 200 | Device-tree kaynağı (§6) |
| 7 | https://uefi.org/ | HEAD (aynı yöntem) | ❌ 403 | **kullanılmadı** |
| 8 | https://www.usb.org/ | HEAD (aynı yöntem) | ✅ 200 | USB VID/PID programı kaynağı (§8.1/#4) |

**Kurallar:** 403/404 döndüren URL **kullanılmaz** ve "kaynak" sayılmaz (7. satır
yalnız kayıt). Yeni envanter kesişimi **0** — bu dosya donanım envanterine
fiziksel parça iddiası eklemez. Uydurma URL/sürüm/tarih üretilmez.

## §12 Etiket Disiplini (belgede geçen ≠ keşfedilmiş)

| # | Etiket | Anlamı | Bu dosyada geçerli örnek |
|---|---|---|---|
| 1 | IMPLEMENTED | Kod/fiziksel kanıt diskte vardır | `DeviceDetector.php` (272 satır) · `DeviceManager.php` (588 satır) |
| 2 | DOCUMENTED | Belge/ADR okunmuştur; OS gözlemi yok | §5–§8 platform modelleri (HEAD-200 kökler) |
| 3 | PLANNED | Karar/spec var, kod/keşif yok | OS-seviyesi tespit kodu (repo 0) |
| 4 | PROPOSED | Öneri; karar/kayıt tamamlanmadı | bu dalın K000 kök kaydı (§10.1) |
| 5 | UNKNOWN | Veri hiç yok | hedef platform seti (Q-DT1) · gerçek VID/PID |
| 6 | ⚠️ VERIFICATION REQUIRED | Doğrulanamayan iddia | tüm API/id/ölçüm iddiaları |

**Kapı kuralı:** Tek satırda tek etiket; karışık durumda **en düşük** etiket.
"IMPLEMENTED" yalnız dosya yolu + satır/sayı ile yazılır.

## §13 CoreMusic Repo'daki Tespit Kodunun Durumu (disk kanıtı)

> Bu bölüm **bu oturumun ölçümüdür** (2026-10-13, repo kökü `C:\www\coremusic.net`).

| # | Ölçüm | Sonuç | Yorum |
|---|---|---|---|
| 1 | `shared/src/Device/DeviceDetector.php` | 272 satır · `final class DeviceDetector` satır 19 · `detect()` satır 57 | **IMPLEMENTED** — UA+viewport tabanlı **uygulama-seviyesi** tespit |
| 2 | `shared/src/Device/DeviceManager.php` | 588 satır · 7 cihaz sabiti satır 29–40 | **IMPLEMENTED** — UI kararı yönetimi |
| 3 | `shared/src/Device/DeviceCssMap.php` | 49 satır | **IMPLEMENTED** — cihaz→CSS eşlemesi |
| 4 | `shared/src/Device/*.php` içinde `sysfs|SetupDi|udev|devnode|PnP|ioctl` | **0 isabet** | OS-seviyesi tespit **YOK** |
| 5 | Repo geneli `*.c|*.cpp|*.h|*.hpp` | **0** | ring-0 sürücü kodu yok (K000 çekirdek PLANNED) |
| 6 | Repo geneli `*.cs` · `*.inf` · `*.rs` | **0 · 0 · 0** | Windows sürücü/INF, Rust çekirdek yok |
| 7 | `isletim-sistemi-audio/wasapi/` | 13 md dosya (glob 2026-10-13) | ses yığını ayrı dal — dokunulmaz |

**Sınır yorumu (bağlayıcı):** CoreMusic bugün donanımı **OS'den okumuyor**;
tarayıcıdan gelen UA/viewport'a bakarak **UI cihaz tipi** tahmin ediyor
(`DeviceDetector::detect()` — `HTTP_X_DEVICE_TYPE` başlığı satır 63, TV agent
satır 70, embedded agent satır 75). Bu **keşif (enumeration) değildir** —
kullanıcının kendini tanımlamasıdır. Aradaki fark bu dalın varlık
gerekçesidir (Q-DT2).

## §14 İlişkili ADR'ler (varlık iddiası)

| # | ADR | Dosya | Bu dosyadaki kullanım |
|---|---|---|---|
| 1 | ADR-017 (DSP hardware mode) | `.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md` | yalnız **varlık** iddiası — ASIO/WASAPI bağlamı (§7) |
| 2 | ADR-038 (ses kartı çekirdeği) | `.ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md` | yalnız **varlık** iddiası — PCM3168A+XU316 bağlamı |
| 3 | ADR-064 (5 cihaz sınıfı) | `.ai/.decisions/accepted/ADR-064-electronics-platform-architecture.md` | **okundu** (§1, §1.1 — 2026-10-13): `DeviceManager.php:29-40` 7 tip kanıtı bu ADR'de de kayıtlı |

## §15 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | "Tahmin" ile "keşif" karıştırılması (Q-DT2) | yüksek | **aktif** | §13 sınır yorumu + §12 etiket |
| 2 | Bu dalın `cihaz-erisimi`'ni tekrar yazması | orta | orta | §10.2 fark tablosu |
| 3 | K000 kök index'e çift-yazım (orkestratör çakışması) | yüksek | orta | beyazliste: kök index'e DOKUNMAZ |
| 4 | Uydurma VID/PID/IOCTL | kritik | düşük | §8.1 yasak satırı |
| 5 | PROPOSED statüsünün "kabul" sanılması | orta | orta | §10.1/5 etiket |
| 6 | Platform yüzeyi iddiasının gözlemsiz yazılması | yüksek | yüksek | §5/§6 ⚠️ blokları + `-dogrulama.md` §6 kapıları |

## §16 Uygulama Sırası (checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | Çelişki kaydını yaz (§10.1) | PROPOSED tablosu | orkestratör onayı |
| 2 | Platform modellerini belgele (§5–§8) | doküman | HEAD-200 kökler |
| 3 | Repo ölçümünü yap (§13) | sayı tablosu | bu oturum ✅ |
| 4 | Sınır tablosunu gözden geçir (§9, §10.2) | sınır | sahiplik 0 çelişki |
| 5 | Gözlem kapılarını aç (Linux sysfs / Windows SetupAPI) | gözlem raporu | `-dogrulama.md` §6 |
| 6 | Gerçek VID/PID envanteri (varsa) | envanter | `UNKNOWN` → doldur |
| 7 | K000 kök kaydı (index/§GRUPLAR) | orkestratör işi | APPROVAL REQUIRED |

## §17 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | DeviceDetector'ı "donanım keşfi" sanmak | UA tahmini enumeration sayılır | §13 — tahmin ≠ keşif |
| 2 | `cihaz-erisimi` konusunu buraya taşımak | ioctl ayrıntısı tekrarlanır | §10.2 — ikisi de yaşar |
| 3 | Uydurma VID/PID/INF/modalias yazmak | Sahte donanım iddiası | §8.1 yasağı |
| 4 | PROPOSED dalı "onaylı" sunmak | Kayıtsız mimari | §10.1 — APPROVAL REQUIRED |
| 5 | Kök `index.md`'yi kendiliğinden düzenlemek | Çift-yazım çakışması | beyazliste — orkestratör |
| 6 | Firmware keşfini OS keşfi saymak | K005/K000 karışması | §9/#6 |
| 7 | `{...}` / `TBD` bırakmak | Okunamaz tablo | `UNKNOWN` / `⚠️` |

## §18 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Enumeration | OS'nin bus taramasıyla cihazları listelemesi |
| 2 | PnP (Plug and Play) | Windows donanım olay koordinasyon modeli |
| 3 | Devnode | Windows çekirdeğindeki cihaz düğümü kaydı |
| 4 | SetupAPI | Windows kullanıcı uzayı cihaz sorgulama kütüphanesi |
| 5 | sysfs | Linux çekirdek nesnelerinin kullanıcı uzayına yansıması |
| 6 | udev | Linux'ta olay → `/dev` düğümü kural motoru |
| 7 | uevent | Çekirdek→uzay olay bildirimi (Linux) |
| 8 | device-tree (DT) | Gömülü sistem donanım ağacı veri yapısı |
| 9 | compatible | DT'de sürücü eşleme anahtarı |
| 10 | INF | Windows sürücü kurulum/tarif dosyası |
| 11 | modalias | Linux'ta modül alias eşleme dizesi |
| 12 | VID / PID | USB vendor / product identifier |
| 13 | Hotplug | Çalışırken tak-çıkarma |
| 14 | IOCTL | Cihaza özgü kontrol çağrısı (erişim — sınır) |
| 15 | OID | Ağ/çevre birimi nesne tanımlayıcısı (yönetim yüzeyi) |
| 16 | probe / AddDevice | Sürücünün cihaza bağlanma girişi (Linux/Windows) |
| 17 | IMPLEMENTED / PLANNED | Kod kanıtlı / spec-only ayrımı (§12) |
| 18 | PROPOSED | Öneri — karar verilmedi |
| 19 | UNKNOWN | Veri hiç yok |
| 20 | ⚠️ VERIFICATION REQUIRED | Doğrulanamayan iddia işareti |

## §19 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `index.md` | Bu dalın künyesi |
| 2 | `../index.md` | K000 kök — children/§GRUPLAR **eklenmedi** (orkestratör) |
| 3 | `../isletim-sistemi-api/cihaz-erisimi/index.md` | API ≠ yaşam döngüsü (§10.2) |
| 4 | `../isletim-sistemi-audio/wasapi/index.md` | ses yığını ayrı (13 md, dokunulmaz) |
| 5 | `K001-donanım/donanaım-type/index.md` | keşif ≠ sınıflandırma (§9) |
| 6 | `K001-donanım/donanım-devices/index.md` | envanter ≠ keşif olayı (§9) |
| 7 | `K001-donanım/donanım-api/index.md` | sözleşmenin K001 tarafı (iddia yok) |
| 8 | `K005-firmware/index.md` | firmware yükleme ≠ OS keşfi (§9/#6) |
| 9 | `os-donanim-tespiti-dogrulama.md` | kapılar, gözlem formları, URL |
| 10 | `os-donanim-tespiti-ornekler.md` | repo kodu + şablonlar |

---

## §20 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya 8 adet HEAD-200 web URL'si (§11),
3 PHP repo dosyası ölçümü (§13) ve 3 ADR varlık iddiası (§14 — ADR-064 ayrıca
okunmuştur) üzerine kuruludur; 403 döndüren URL kullanılmamıştır. Repo'da
OS-seviyesi donanım tespiti **kodunun 0 olduğu** ölçümlenmiştir (§13/4–6);
bu dosya **keşif sistemi tarif etmez**, genel OS modelini belgeler ve
CoreMusic sınırını çizer. Gerçek VID/PID, IOCTL numarası, INF/modalias/DT
dizesi uydurulmamıştır; bilinmeyenler `UNKNOWN` ile işaretlenmiştir. Bu dalın
K000 kök kaydı **PROPOSED — APPROVAL REQUIRED** olarak kayıtlıdır (§10.1);
`cihaz-erisimi` ile fark tablosu yazılmış, iki dal da yaşatılmıştır (§10.2).
`{...}` ve `TBD` yer tutucusu kullanılmamıştır. Fiziksel/gözlemsel ölçüm **0**
— tüm platform gözlem iddiaları `⚠️ VERIFICATION REQUIRED` altındadır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode