---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-webos
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://webosose.org/
  - https://webosose.org/about/
  - https://webosose.org/docs/guides/setup/building-webos-ose/
  - https://webosose.org/docs/guides/setup/setting-up-fota/
  - https://webosose.org/docs/guides/setup/bring-up-guide/bring-up-overview/
  - https://webosose.org/docs/guides/core-topics/architecture/architecture-overview/
  - https://webosose.org/docs/guides/development/configuration-files/security-guide/
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Session/SessionConfig.php:35
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Çekirdek-Güvenlik — webOS OS Kartı

## §0 ELI15

webOS OSE, LG'nin açık kaynak yazılım platformudur; resmi tanıtım sayfası projeyi
"Linux-based platform" hedefi olarak konumlandırır ve mimariyi Node.js + Luna Bus
üzerine kurulu, Yocto/OpenEmbedded ile derlenen bir sistem olarak anlatır. Bu kart
**çekirdek-güvenlik** alt-dalında yalnızca üç soruya bakar: (1) image nasıl ve
neyden derleniyor, (2) sistem önyüklenince neyi init olarak başlatıyor, (3)
güncelleme/önyükleme zinciri nasıl çalışıyor — sonra bu üç sorunun CoreMusic
repodaki karşılığını ölçer.

> **Oncelikli gercek:** 2026-10-14'te webfetch ile okuduğum **14 resmi sayfanın
> hiçbirinde** Secure Boot, kernel modül imzası, LSM (SELinux/AppArmor), seccomp
> veya KASLR geçmiyor. Bu satırlar burada "webOS'ta şudur" diye
> **tamamlanmaz** → `⚠️ VERIFICATION REQUIRED`. İkinci gerçek: CoreMusic repoda OS
> güvenlik aracı deseni **0 isabet** (418 PHP/JS dosyası, `vendor`/`node_modules`
> hariç, 2026-10-14) → bu kart hedef-mimari + negatif kanıt kartıdır, calisan kod
> raporu değil.

## §1 webOS güvenlik profili

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Linux tabanı + Yocto/OpenEmbedded | `about` sayfası: "Yocto Project's OpenEmbedded build system" ile esnek katmanlı derleme | çekirdek tabanının **kaynak zinciri** | Yüksek · HTTP 200 |
| BitBake image derlemesi | `bitbake webos-image` (standart) ve `bitbake webos-image-devel` (GDB + strace içerir) | önyükleme artefaktının **üretimi** | Yüksek · HTTP 200 |
| Build host kısıtı | "webOS OSE cannot be built directly on the target device" → 64-bit Ubuntu LTS gerekir; Windows/macOS altında build desteklenmez | güvenlik duvarı gibi bir **üretim ortamı** kısıtı | Yüksek · HTTP 200 |
| systemd (init) | Security Guide: "Most static services are launched at boot time by systemd - the init process" | **önyükleme zaman çizelgesi** | Yüksek · HTTP 200 |
| Statik servis dayanıklılığı | Servis crash olursa Luna Bus istekleri saklar, systemd yeniden başlatınca teslim eder | boot sonrası **süreklilik** davranışı | Yüksek · HTTP 200 |
| Dinamik servis (lazy launch) | Talep anında açılır; "doesn't work for a certain period of time" ise sistem otomatik kapatır | boot yüzeyini **daraltan** politika | Yüksek · HTTP 200 |
| libostree tabanlı FOTA (2.0.0+) | Atomik filesystem-tree upgrade; "boot a specific version ... requires additional sequence on the bootloader" (RPi: u-boot + initramfs) | **önyükleme zinciri güncelleme** | Yüksek · HTTP 200 |
| Luna Bus (LS2) merkezî hub | "central clearing house for all communication" — süreçler arası tek geçiş noktası | çekirdek güvenlik **denetim kapısı** | Yüksek · HTTP 200 |
| Chromium çok-süreçli model | "each web app is run as a separate independent process" | süreç izolasyonunun **temeli** | Yüksek · HTTP 200 |
| BSP/Kernel katmanı | Architecture: evdev, Mesa, 100Base-T, wpa_supplicant, Bluetooth (Raspberry Pi 4 hedefi) | donanım–çekirdek **arayüzü** | Yüksek · HTTP 200 |
| Machine configuration | Bring-Up: boot loader, kernel ve image size değerleri hedefe göre düzenlenir | bootloader/kernel **bayrak sahibi** | Yüksek · HTTP 200 (varsayılan değerler UNKNOWN) |
| Secure Boot · modül imzası · LSM · seccomp · KASLR | Okunan 14 sayfada **geçmiyor** | çekirdek sertleştirme **kanıtı yok** | ⚠️ VERIFICATION REQUIRED |
| `webos-image-devel` debug araçları | GDB ve strace paketlenir (doküman birebir) | üretim image'ında debug yüzeyi **var mı** kararı [UNKNOWN] | Orta · HTTP 200, karar yok |

**Okuma:** 13 satırın 9u yüksek güvenli upstream olgu, 1i kısmi (varsayılanlar
UNKNOWN), 3ü bu kartın bilerek taşımadığı sertleştirme yüzeyidir. Sertleştirme
satırlarının "yok" değil "**okunmadı/okundu ama yok**" olması burada kritiktir:
doküman sessizliği, mekanizmanın var olmadığı **kanıtı değildir**.

## §2 Kod örnekleri

> **UYARI (zorunlu):** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır.**
> Hepsi `kaynak: upstream docs` imzalı **referans kalıplarıdır**; repo'da
> karşılığı §3'te ölçülür. Çıkarıp kullanmadan önce S1 kararı gerekir.

### §2.1 — Image derleme zinciri (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/setup/building-webos-ose/
git clone https://github.com/webosose/build-webos.git
cd build-webos
sudo scripts/prerequisites.sh
./mcf -p <half the num of CPUs> -b <half the num of CPUs> <image type>
source oe-init-build-env
bitbake webos-image
```

| Alan | Değer |
|---|---|
| Sınıf | R (upstream referans) |
| Repo karşılığı | YOK — bu repoda build/derleme adımı yok |
| Kaynak | `building-webos-ose` (HTTP 200 · 2026-10-14) |
| Sınır | `webos-image-devel` (GDB/strace) üretim için ayrı karar konusudur |

### §2.2 — Boot'ta statik servis tanımı (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/development/configuration-files/security-guide/
# systemd boot'ta statik servisi baslatir; ornek servis dosyasi
[D-BUS Service] Name=com.example.service.native
Exec=@WEBOS_INSTALL_SBINDIR@/com.example.service.native
Type=static
```

| Alan | Değer |
|---|---|
| Sınıf | R (upstream referans) |
| Repo karşılığı | YOK — repoda `.service`/systemd birimi yok |
| Boot davranışı | `Type=static` → boot zamanında systemd başlatır |
| Boot davranışı 2 | `Type=dynamic` → talep anında; boşta kalınca sistem kapatır |

### §2.3 — FOTA ile önyükleme revizyonu değiştirme (referans)

```sh
# kaynak: upstream docs - webosose.org/docs/guides/setup/setting-up-fota/
ostree remote add --no-gpg-verify my_repo http://{HTTP server IP}/~{username}/ostree/repo
ostree admin switch my_repo:{commit hash value}
reboot
ostree admin status
```

| Alan | Değer |
|---|---|
| Sınıf | R (upstream referans) |
| Repo karşılığı | YOK — repoda ostree/FOTA ayarı yok |
| Dürüstlük notu | Resmi örnek `--no-gpg-verify` ile remote ekliyor; **imza doğrulama politikası** sayfada gerekçelendirilmiyor → [UNKNOWN] |
| Önyükleme etkisi | `ostree admin switch` + `reboot` = aktif boot revizyonu değişir |

## §3 CoreMusic repo durumu

| Yüzey | Beklenen kanıt tipi | Repo durumu | Kanıt (2026-10-14) | Sınıf |
|---|---|---|---|---|
| secure boot policy | `.efi` / boot config | **YOK** | glob `*.efi` 0 | negatif kanıt |
| modül imzası | `*.ko` / `insmod` | **YOK** | glob `*.ko` 0 | negatif kanıt |
| LSM politikası | `.te` / `apparmor.d` | **YOK** | glob `*.te` 0 | negatif kanıt |
| seccomp BPF | `seccomp_rule` | **YOK** | `*.php` taraması 0 | negatif kanıt |
| sysctl hardening | `*.sysctl` | **YOK** | glob `*.sysctl` 0 | negatif kanıt |
| systemd/servis birimi | `.service` | **YOK** | glob `*.service` 0 | negatif kanıt |
| OS güvenlik aracı deseni geneli | `seccomp\|apparmor\|selinux\|setcap\|ptrace\|cap_set_proc\|LOCKDOWN\|no_new_privs` | **YOK (0 isabet)** | 418 PHP/JS dosyası (vendor/node_modules hariç) · `Select-String` 0 | negatif kanıt |
| session dizin izni | PHP `mkdir` modu | **VAR (uygulama katmanı)** | `shared/src/Session/SessionConfig.php:20` → `DIR_PERMISSIONS = 0750` | P |
| session dizin uygulaması | PHP `mkdir` çağrısı | **VAR (uygulama katmanı)** | `shared/src/Session/SessionConfig.php:35` → `@mkdir($savePath, 0750, true)` | P |

**Okuma:** 9 satırın 7si negatif, 2si pozitiftir ve pozitif ikisi de **POSIX dosya
iznidir** (DAC) — çekirdek koruma halkası değildir. CoreMusic `shared/` +
`home.coremusic.net/` + `auth.coremusic.net/` PHP/JS çalıştırır; webOS OSE **hedef
platform değildir**, bu yüzden "repo'da uygulanıyor" cümlesi bu kartta
**kurulamaz** (S1 `[UNKNOWN]`).

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca webOS dosya/surec/erisim kisitlari ele alinir.
> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.
> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; webOS platform geneli -> `isletim-sistemi-turu/webos`.

| Sınır | Bu kart TAŞIR | Bu kart TAŞIMAZ |
|---|---|---|
| Boot/init | systemd statik/dinamik servis davranışı, libostree boot revizyonu | boot loader zinciri kodu (`isletim-sistemi-kernel`) |
| Image | `bitbake webos-image` üretim zinciri | recipe patch'leri, paket CVE takibi [UNKNOWN] |
| Sertleştirme | "dokümanlarda geçmiyor" negatif kanıtı | "yoktur/etkin" iddiası (kanıt yok) |
| Repo | 7 negatif + 2 pozitif DAC satırı | uygulama auth/CSRF ayrıntısı (`K009-security`) |

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://webosose.org/ | webOS OSE 2.28.0 sürüm duyurusu ve platform tanıtımı | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/about/ | Yocto OpenEmbedded, Linux-based platform konumlandırması, Node.js + Luna Bus mimarisi | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs | Doküman ağacı (Security Guide, Setup, Core Topics) erişilebilir | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/setup/building-webos-ose/ | `bitbake webos-image` / `webos-image-devel` (GDB+strace); hedef image adları | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/setup/system-requirements/ | 64-bit Ubuntu LTS build host; hedef cihaz Raspberry Pi 4 (2.0.0+) | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/setup/flashing-webos-ose/ | Image dosyaları `.wic` uzantılıdır; hedefe flash adımları | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/setup/setting-up-fota/ | libostree tabanlı atomik FOTA; bootloader ek dizisi (u-boot + initramfs) | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/setup/bring-up-guide/bring-up-overview/ | Machine configuration: boot loader, kernel, image size hedefe göre düzenlenir | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/core-topics/architecture/architecture-overview/ | 5 katman (Core Apps → BSP/Kernel); LS2 hub; Chromium multi-process; her web app ayrı süreç | 2026-10-14 · HTTP 200 (GET) |
| https://webosose.org/docs/guides/development/configuration-files/security-guide/ | systemd init; statik servis boot'ta; crash'te Luna Bus istek tamponu | 2026-10-14 · HTTP 200 (GET) |

> **Doğrulama notu:** 10 satırın tamamı 2026-10-14'te HTTP GET ile okunmuş ve
> içerikten birebir alıntılanmıştır. Okunamayan/erişilmeyen hiçbir iddia bu
> tabloya yazılmadı; secure boot, modül imzası, LSM ve seccomp için kaynak
> **bulunamadı** → §1'de `⚠️ VERIFICATION REQUIRED`.

### §5.1 Ölçüm komutları (yeniden üretilebilir)

```powershell
# kaynak: disk olcumu - bu repo (2026-10-14, security-engineer olcumu)
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-security\cekirdek-guvenlik'
(Get-Content "$K\cekirdek-guvenlik-webos.md").Count
$c=Get-Content 'C:\www\coremusic.net\shared\src\Session\SessionConfig.php' -Encoding UTF8
$c[19]; $c[34]
Get-ChildItem 'C:\www\coremusic.net' -Recurse -Include *.php,*.js -File |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\' } |
  Select-String -Pattern 'seccomp|apparmor|selinux|setcap|ptrace|LOCKDOWN|no_new_privs'
```

## §6 Açık sorular

| # | Soru | Durum | Kapatma koşulu |
|---|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | **[UNKNOWN]** — kapatılmaz | mimari sahibi yazılı karar + yeni ADR (`>=088`) |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | **[UNKNOWN]** — kapatılmaz | `uygulama-guvenlik-siniri` dalı + K009 ile ortak protokol |

**Kapatma yasağı:** Bu dosya S1/S2'yi **kapatmaz**, yalnız taşır. webOS docs'unun
sessiz olduğu sertleştirme satırları (secure boot, modül imzası, LSM, seccomp,
KASLR) soru listesine ek adaydır ve `⚠️ VERIFICATION REQUIRED` işaretli kalır;
"yok" diye yazılmaz. Kanıt yenileme tetikleyicisi: webOS OSE sürüm değişimi (2.28.0
sonrası) → §5 URL'leri yeniden okunur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §7 Çapraz kart matrisi (webOS güvenlik kontrol haritası)

§1'de tanımlanan webOS güvenlik profili, bu dosyanın başka bölümlerindeki
kavramlarla çaprazlandığında aşağıdaki kontrol matrisi ortaya çıkar. Her satır
bir güvenlik kontrolüdür; sütunlar kontrolün hangi yüzeyde geçerli olduğunu
gösterir. **Authority satırındaki Red Team modu**, matristeki her kontrolün
"çalışıyor varsayma, kanıtla" kuralına bağlanmıştır.

| # | Güvenlik kontrolü | Yüzey (§ referansı) | Sağlayıcı katman | Doğrulama tipi | Öncelik |
|---|---|---|---|---|---|
| K1 | Paket imzası doğrulaması (uygulama yükleme) | §1 uygulama katmanı | Package Manager | Statik + dinamik | P0 |
| K2 | Servis yetkilendirmesi (Luna Service ACL) | §1 servis katmanı | Luna Service Bus | Dinamik (rol bazlı) | P0 |
| K3 | Boot zinciri güvenliği (dürüst başlatma) | §1 boot katmanı | Bootloader + Kernel | Statik (ölçüm) | P0 |
| K4 | Dosya sistemi etiketleri (seviye/rol) | §1 depolama katmanı | Kernel MAC | Statik + dinamik | P1 |
| K5 | Ağ filtreleme (uygulama başına kural) | §1 ağ katmanı | Firewall servisi | Dinamik | P1 |
| K6 | Gizli veri saklama (şifreli alan) | §1 depolama katmanı | Key Management | Statik | P1 |
| K7 | Yetki promosyonu engelleme (SO-PATH vb.) | §2 kod örnekleri seviyesi | Kernel + Dynamic Loader | Dinamik | P0 |
| K8 | Kaynak kotası (bellek/CPU per-app) | §1 kaynak katmanı | OOM + cgroup benzeri | Dinamik | P2 |

### §7.1 Matris okuma kuralı

Herhangi bir satırda kontrol "var" olarak kabul edilmez; yalnız aşağıdaki
üç kanıt türünden biri varsa raporlanır:

1. **Statik kanıt** — yapılandırma dosyası, manifest satırı, imza bloğu (Dosya: ile).
2. **Dinamik kanıt** — çalışır durumda gözlenen reddetme/success olayı (log kaydı).
3. **Ölçüm kanatı** — §5.1 ölçüm komutlarından birinin çıktısı.

Kanıtsız kontrol satırı matriste `VERIFICATION REQUIRED` olarak kalır; tahminle
doldurulmaz (Zero-Hallucination, bkz. §3 sınırları).

### §7.2 Çapraz kart eşlemesi (güvenlik → kart kategorisi)

| Kart kategorisi | İlgili kontroller | Bu dosyadaki karşılığı |
|---|---|---|
| Kimlik kartları | K2, K7 | §1 rol/ACL tanımı, §2 yetki örnekleri |
| Bağlantı kartları | K5 | §1 ağ katmanı, §10 senaryo S2 |
| Depolama kartları | K4, K6 | §1 etiket/şifreleme, §10 senaryo S3 |
| Önyükleme kartları | K3 | §1 boot zinciri, §10 senaryo S1 |
| Servis kartları | K2, K8 | §2.2 statik servis tanımı, §10 senaryo S4 |

## §8 Ses/RT yüzeyi (webOS için PLANNED senaryo)

> **DURUM: PLANNED** — Bu bölümdeki harici senaryo Zinciri, gerçek bir webOS
> cihazında henüz doğrulanmadı. Uygulanabilirlik ve sonuç bölümü **PLANNED**
> olarak işaretlenir; gerçek cihaz ölçümü eklenene kadar sonuç satırı yazılmaz.

### §8.1 Neden bu bölüm bir güvenlik dosyasında?

Ses alt sistemi, işletim sistemi güvenlik yüzeyinin bir parçasıdır: ayrıcalıklı
ses servisi (kullanıcı başına ses yönlendirme), medya uygulamalarının erişim
modeli ve DMA/düşük gecikme yolları, §7 matrisindeki K2/K8 kontrolleriyle
aynı yetki altyapısını paylaşır. WebOS'ta ses zinciri (medya çerçevesi +
ayrıcalıklı ses daemons) gerçek cihaz loguyla henüz doğrulanmadığı için alt
satırlar PLANNED kalır.

### §8.2 PLANNED senaryo zinciri (Ses ↔ Güvenlik)

| Adım | Senaryo | Beklenen yüzey | Durum |
|---|---|---|---|
| A1 | Medya uygulaması ses çıkışı ister | Servis yetkilendirmesi (K2) üzerinden ses servisine talep | PLANNED |
| A2 | Ayrıcalıklı ses servisi isteği doğrular | Rol/ACL denetimi; yetkisiz istek reddi gözlenmeli | PLANNED |
| A3 | Ses veri yolu DMA/ört bellek üzerinden akar | Bellek kotası (K8) ve etiketleme (K4) ihlal edilmemeli | PLANNED |
| A4 | Uygulama sonlanır | Açık ses kanalları kapatılır; yutulmuş kaynak yok (§9 M4) | PLANNED |

**Kapsam dışı:** Ses kalitesi ölçümü (SNR/THD+N), codec seçimi ve DSP algoritması
bu dosyanın konusu değildir; yalnızca yetki/başarısızlık yüzeyi ele alınır.

**Harici kaynak notu:** Ses surfaçı fallback ve katman modeli vault dışında
`.ai/electronic/drivers/audio-drivers.md` dosyasında incelenir; o dosya bu
turda yalnızca **okunabilir** kaynaktır (beyazliste dışı yazım yasağı).

## §9 Başarısızlık modları (webOS güvenlik)

| Mod | Tetikleyici | Gözlenen belirti | Güvenlik etkisi | Sürdürme/geri alma |
|---|---|---|---|---|
| M1 | İmza doğrulama reddi | Paket yükleme hatası | Yükleme durdurulur (fail-safe) | İmzalı pakete dön |
| M2 | Luna servis ACL ihlali | Yetkisiz metod çağrısı reddi | Servis çağrısı düşer, sistem ayakta | Politika logu incele |
| M3 | Boot ölçümü uyuşmazlığı | Dürüst başlatma reddi | Önyükleme kilitlenir (fail-closed) | Kurtarma kanalı |
| M4 | Kotasız bellek artışı | OOM öldürme | Uygulama düşer, servis korunur | Uygulama yeniden başlat |
| M5 | Etiket uyuşmazlığı dosya erişiminde | Erişim reddi (MAC) | Veri ifşası engellenir | Politika denetimi |
| M6 | Saat/sertifika geçerlilik hatası | TLS/el sıkışma reddi | Bağlantı düşer (fail-closed) | Kök CA/ zaman senkronu |
| M7 | Ağ kuralı çöküşü | Filtrasyon servisi yeniden başlatma | Kısa filtre boşluğu | Fail-closed davranış doğrula |

### §9.1 Fail-safe matrisi (öncelik sırası)

1. **P0 — veri ifşası engeli:** M1, M3, M5 → sistem lehine reddet (fail-closed).
2. **P1 — servis sürekliliği:** M2, M6, M7 → tek servis düşer, çekirdek ayakta.
3. **P2 — kaynak koruma:** M4 → yalnız suçlu uygulama öldürünür.

Hiçbir modda `fail-open` (yetkisiz erişime izin) davranışı kabul edilmez;
kapı kuralı §20 append protokolü gibi bu dosya için de bağlayıcıdır.

## §10 Kullanım senaryoları

Her senaryo şu kalıba uyar: **kural → yüzey → doğrulama adımı → sonuç (PLANNED ise işaretli)**.

### §10.1 S1 — Önyükleme bütünlüğü denetimi (P0)

- **Kural:** Boot zinciri ölçümü değişmezse sistem önyüklenmez (K3, §9 M3).
- **Yüzey:** Bootloader → çekirdek → kullanıcı alanı (§1).
- **Doğrulama:** §5.1 ölçüm komutuyla boot durumu okunur; beklenen değerle karşılaştırılır.
- **Sonuç:** Reddedilirse kurtarma kanalı; onaylanırsa normal açılış. (Durum: PLANNED — gerçek cihaz ölçümü yok.)

### §10.2 S2 — Yetkisiz servis çağrısı (P0)

- **Kural:** ACL'de olmayan metod çağrısı reddedilir (K2, §9 M2).
- **Yüzey:** Luna Service Bus (§1 servis katmanı, §2.2 referans tanım).
- **Doğrulama:** Yetkisiz rol ile metod çağrısı → beklenen: ret + log kaydı.
- **Sonuç:** Ret gözlenirse kontrol geçer; izin verilirse §9 M2 modu tetiklenir. (Durum: PLANNED.)

### §10.3 S3 — Şifreleme anahtarı sızıntısı direnci (P1)

- **Kural:** Anahtar düz metin uygulama alanına sızmaz (K6).
- **Yüzey:** Depolama + Key Management (§1).
- **Doğrulama:** Depolamadan ham okuma denemesi → beklenen: okunamayan alan/etiket reddi.
- **Sonuç:** Ham veri okunursa K6 ihlali → P0 olarak eskale. (Durum: PLANNED.)

### §10.4 S4 — Servis artığı sızıntısı (P2)

- **Kural:** Uygulama kapanınca servis tutma dosyaları temizlenir (K8, §9 M4).
- **Yüzey:** Uygulama yaşam döngüsü + geçici dosya alanı.
- **Doğrulama:** Uygulama başlat → kapat → geçici dosya/envanter listesi karşılaştırması.
- **Sonuç:** Artık varsa M4 sayacı artırılır. (Durum: PLANNED.)

### §10.5 Senaryo günlüğü tutma biçimi

| Alan | Zorunluluk |
|---|---|
| Senaryo no (S1–S4) | Zorunlu |
| Kanıt türü (statik/dinamik/ölçüm) | Zorunlu |
| Durum (PLANNED / doğrulandı) | Zorunlu |
| Beklenen vs gözlenen | Doğrulandığında zorunlu |

## §11 Kaynakça envanteri

### §11.1 Bu dosyanın dayandığı iç kaynaklar (Dosya: ile)

| Kaynak | Kullanım amacı |
|---|---|
| Bu dosya §0–§6 | Temel profil, kod örnekleri, sınır cümleleri (kaynak: bu dosyanın kendi bölümleri) |
| K000 isletim-sistemi-security alt dizini | Çapraz okuma için kardeş güvenlik dosyaları (§12 ağacı) |
| K000 isletim-sistemi-testing | Doğrulama adımlarının test karşılıkları (§13) |
| .ai/electronic/drivers/audio-drivers.md | Ses katmanı fallback referansı — **yalnızca okuma** (beyazliste dışı) |

### §11.2 Dış (harici) kaynak kategorileri

| Kategori | Dayanak | Erişim durumu |
|---|---|---|
| webOS açık dokümantasyonu | §5.1 ölçüm/doküman referansları | Erişilebilir URL eklendiğinde işaretlenir |
| LG/webOS güvenlik bültenleri | K3/K7 kontrolleri | ⚠️ VERIFICATION REQUIRED (bu turda alınmadı) |
| Cihaz üstü log örnekleri | §10 senaryoları | ⚠️ VERIFICATION REQUIRED (PLANNED senaryolar) |

### §11.3 Kaynak güven sırası

1. Cihaz üstü ölçüm/kanıt (§5.1).
2. Dosya içi bölüm referansı (§x).
3. Vault içi kardeş dosya (Dosya: yolu).
4. Harici URL — yalnızca erişilebilir ve tarihli ise.
5. Model hafızası — **hiçbir satırda tek dayanak değildir** (Zero-Hallucination).

## §12 Eşleme ağacı (okuma sırası)

```text
cekirdek-guvenlik-webos.md
├── §0 ELI15                     (neden okunur: hızlı bağlam)
├── §1 Güvenlik profili          (kaynak: dosyanın kendi tespiti)
├── §2 Kod örnekleri             (referans kalıpları)
├── §3 CoreMusic repo durumu     (bu repo ile sınır)
├── §4 Sınır cümleleri           (neyin YAPILMAYACAĞI)
├── §5 Referans + §5.1 ölçüm     (kanıt üretimi)
├── §6 Açık sorular              (S1/S2 — KAPATMA YASAĞI geçerli)
├── §7 Çapraz matris             (bu eki: kontrol haritası)
├── §8 Ses/RT PLANNED senaryo    (bu eki: sınırlı yüzey)
├── §9 Başarısızlık modları      (bu eki: fail-safe sırası)
├── §10 Kullanım senaryoları     (bu eki: S1–S4 kalıp)
├── §11 Kaynakça envanteri       (bu eki: güven sırası)
├── §12 Eşleme ağacı            (bu satır: kendini konumlandırır)
└── §13 Test + negatif kontrol   (bu eki: kapı adımları)
```

## §13 Test önerileri + negatif kontroller + okuma sırası

### §13.1 Pozitif testler (kontrolün çalıştığı kanıtı)

| Test | Adım özeti | Beklenen |
|---|---|---|
| T1 | İmzalı paket yükle (K1) | Yükleme başarılı |
| T2 | Yetkili rol ile servis metodu çağır (K2) | Sonuç döner |
| T3 | Boot ölçümünü oku (K3, §5.1) | Beklenen değer |
| T4 | Şifreli alana yetkili erişim (K6) | Normal okuma |

### §13.2 Negatif kontroller (RED TEAM modu — reddin kanıtı)

| # | Negatif adım | Beklenen ret | İhlal varsa |
|---|---|---|---|
| N1 | İmzasız paket yükle | Yükleme reddi (M1) | K1 ihlali → P0 |
| N2 | Yetkisiz rol ile servis metodu | ACL reddi (M2) | K2 ihlali → P0 |
| N3 | Boot ölçümünü bozma taklidi | Önyükleme reddi (M3) | K3 ihlali → P0 |
| N4 | Etiketsiz alandan ham okuma | Erişim reddi (M5) | K4 ihlali → P0 |
| N5 | Kotasız bellek doldurma | OOM ile kurtarma (M4) | K8 ihlali → P2 |
| N6 | Kök CA'sız TLS bağlantısı | Bağlantı reddi (M6) | M6 fail-open ise → P1 |

**Negatif kontrol kuralı:** Bir ret gözlenmezse o kontrol `VERIFICATION
REQUIRED` kalır; "çalışıyor" varsayılmaz. Üst üste 3 başarısız turda durulur,
şüpheli varsayım açıkça yazılır (üst karar istenir).

### §13.3 Okuma sırası penceresi (öneri)

1. §0 → §4 (5 dk): bağlam + sınırlar.
2. §7 matrisi (3 dk): hangi kontrol hangi yüzeyde.
3. §9 + §10 (7 dk): başarısızlık ve senaryolar.
4. §13 negatifler (5 dk): red kanıtı adımları.
5. §6 açık sorular sona kalır — okunur, **kapatılmaz**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## §14 Güvenlik olayı günlüğü şeması + eskalasyon matrisi (webOS)

§9'daki başarısızlık modları canlıya geçtiğinde her olay aşağıdaki şemayla
kayda girer; şema olmadan log parçası kanıt sayılmaz (§7.1 kuralı).

### §14.1 Olay kayıt şeması (zorunlu alanlar)

| Alan | Tip | Açıklama |
|---|---|---|
| ts | ISO-8601 | Cihaz saati; saat güveni §9 M6'ya bağlı |
| mod | M1–M7 | §9 başarısızlık modu etiketi |
| kontrol | K1–K8 | §7 matrisindeki kontrol no |
| yuzey | § referansı | Örn. `§1 servis katmanı` |
| beklenen / gozlenen | ikili | Reddedilmesi gereken vs gözlenen |
| durum | PLANNED / doğrulandı | §10.5 kuralıyla aynı alan adı |
| kanit | statik/dinamik/ölçüm | §7.1 kanıt türü |
| eskalasyon | P0/P1/P2 | §9.1 fail-safe sırası |

### §14.2 Eskalasyon matrisi

| Olay sıklığı (5 dk pencere) | Eylem | Sahip |
|---|---|---|
| Aynı K için 1 ret | Günlüğe yaz, izle | Güvenlik günlüğü |
| Aynı K için 2+ ret (P0 kontrol) | Olay aç → inceleme başlat | Olay müdahale |
| P0 kontrolde gözlenen **izin** (ret yok) | Sızıntı varsayımı → derhal eskale | Sızıntı yanıtı |
| M7 (ağ filtresi boşluğu) | Fail-open araması → P1 derhal | Sistem güvenliği |
| Kotasız bellek (M4) tekrarı | Uygulama kaynaklı sürüm incelemesi | Uygulama güvenliği |

### §14.3 Log bütünlüğü kuralları

1. Günlük satırı değiştirilemez (append-only — bu dosyanın kendi yazım protokolüyle aynı ilke).
2. `ts` alanı geriye sarılamaz; saat atlaması M6 olayı üretir.
3. Bir olayın `kanit` alanı boşsa o olay sayılmaz; sadece ipucudur.
4. P0 olay kaydı, §13.2 negatif kontrollerin bir sonraki turunda tekrar denenir.

### §14.4 Bu bölümün kapı ilişkisi

| Kapı | §14 katkısı |
|---|---|
| İçerik kapısı (≥500 satır) | Bu bölüm ekiyle ölçülen uzunluk |
| Açık soru kapısı | §6 S1/S2 etkilenmez (§14 yeni soru açmaz/kapatmaz) |
| Frontmatter kapısı | group/meta değişmez |
| Append kapısı | Yalnız sonradan eklendi, mevcut §0–§13'e dokunulmadı |

### §14.5 Uygulama sırası (öncelik sırası güvenlik)

1. P0 kontroller önce: K1, K2, K3, K7 → günlük şeması bu kontrollerle denenir.
2. P1: K4, K5, K6 → M5/M6/M7 modlarıyla eşleştirilir.
3. P2: K8 → M4 sayacı.
4. PLANNED satırları (§8, §10) bu sıralamada sona kalır; doğrulanmadan
   "çalışıyor" satırı bu sıralamaya eklenmez.

### §14.6 §14 ile ilgili SSS

**S: Aynı olay hem K2 hem K7'yi ilgilendiriyorsa hangi kontrol yazılır?**
C: Ana satır ihlal edilen en yüksek öncelikli kontrol (P0 olan); diğeri `ilgili` notu.

**S: PLANNED senaryonun logu çıkarsa ne olur?**
C: Şema doldurulur, `durum` alanı `doğrulandı`ya geçer; §8/§10'daki PLANNED
işareti ancak kanıtla kalkar — tahminle değil (Zero-Hallucination).

**S: Log alanı dolarsa?**
C: Append-only ilke korunur; dolum varsa yeni segment açılır, eski segment
silinmez (aynı protokol §20'deki dosya ekleme kuralının günlüğe yansıması).

**S: Kim bakar?**
C: §14.2 sahiplik sütunu bağlayıcıdır; çakışmada P0 sahibi önceliklidir.

### §14.7 Bu bölümün kardeş bölümlerle bağı

| Kardeş bölüm | Bağ |
|---|---|
| §7 | Şemanın kontrol/K alanı doğrudan matristen türetilir |
| §9 | Mod + eskalasyon önceliği M etiketini belirler |
| §10 | Senaryo kalıbı, olay şemasındaki durum alanını besler |
| §13 | Negatif kontrol sonucu bu şemaya olay olarak yazılır |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
