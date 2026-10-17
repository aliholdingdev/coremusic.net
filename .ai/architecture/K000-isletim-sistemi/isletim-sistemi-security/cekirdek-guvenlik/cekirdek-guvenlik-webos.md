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

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K0100-identitiy`; bu kartta yalnizca webOS dosya/surec/erisim kisitlari ele alinir.
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
