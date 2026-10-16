---
title: "Windows — İşletim Sistemi Güvenlik Profili"
type: architecture
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-guvenlik"
node_type: security-profile
status: active
date: 2026-10-15
parent_id: "K000-isletim-sistemi/isletim-sistemi-turu/windows"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-security"
  - "K009-security"
evidence:
  - "home.coremusic.net/web.config:6-11 (security headers — okundu)"
  - "home.coremusic.net/web.config:20-27 (403 rewrite kuralları — okundu)"
  - "home.coremusic.net/.htaccess:2-3,10-15,18-24 (Apache hardening — okundu)"
  - "shared/src/Api/Bff/DesktopBff.php:35 (php_uname('s') — okundu)"
  - ".github/workflows/ci.yml:14-15,27,69,112,144,163,180 (permissions + 6× ubuntu-latest — okundu)"
  - "grep wdac|applocker|secureboot|credential.guard (shared/src) → 0 isabet (2026-10-15)"
validation: "2026-10-15 — 6 vendor URL erişildi (HTTP 200); repo iddiaları DOSYA:SATIR kanıtlı; negatif iddialar grep ile doğrulandı"
open_questions:
  - "Windows Update yama kanalı repo'da nasıl yönetiliyor? — ⚠️ VERIFICATION REQUIRED (bu oturumda doğrulanmış vendor URL yok)"
  - "WDAC/AppLocker politikası üretim ortamında uygulanıyor mu? (grep 0 isabet) — UNKNOWN"
  - "web.config ×4 hangi host'ta aktif? (Apache mi IIS mi çift hedef) — isletim-sistemi-runtime'a bakılacak"
group: mimari
---

# Windows — İşletim Sistemi Güvenlik Profili

> **Kapsam:** Bu dosya, Windows'un işletim sistemi güvenlik modelini (Secure Boot, kimlik
> koruması, kernel-mod sürücü denetimi, ACL, App Control) tanımlar ve CoreMusic deposundaki
> izlerini DOSYA:SATIR kanıtıyla bağlar. Uygulama seviyesi güvenlik kontrolleri (CSRF, auth,
> session) **K009**'a aittir — bkz §4.

---

## §0 — ELI15 (Explain Like I'm 15)

Windows, bilgisayar açıldığında donanımı yöneten ve programların çalışmasına izin veren
işletim sistemidir. Güvenlik fikri basit bir soruya dayanır: **bilgisayara kimin ne
yapabileceğini sıkı sıkıya kontrol etmek.**

Windows bunu beş katmanda yapar:

1. **Açılıştan itibaren zincirleme doğrulama (Secure Boot).** Bilgisayar açılırken
   firmware, her açılış parçasının (UEFI sürücüsü, EFI uygulaması, işletim sistemi yükleyicisi)
   imzasını denetler. İmza geçerli değilse açılış durur — değiştirilmiş bir sistem yüklenmez.
2. **Kimlikler güçlü ve şifresiz (Windows Hello).** Zayıf/yinelenen parola, parola
   püskürtme (password spraying) ve oltalama (phishing) gibi risklere karşı Windows Hello
   for Business ve Enhanced Sign-in Security (ESS) kimlik hırsızlığını azaltır.
3. **Sürücüler çekirdeğe girmeden önce denetlenir (kernel-mode driver).** Windows, sürücü
   geliştirmeyi güvenlik odaklı kurallara bağlar: nesne yöneticisi, bellek yöneticisi ve
   *Security Reference Monitor* erişim denetimini çekirdek içinde yürütür.
4. **Dosya/dizin erişimi ACL ile kontrol edilir (icacls).** Her dosyanın bir sahibi ve
   bir *discretionary access control list* (DACL) listesi vardır; `icacls` ile hangi
   kullanıcının hangi hakka sahip olduğu açıkça yazılır.
5. **Yalnızca onaylı yazılım çalışır (App Control).** WDAC/AppLocker politikaları, imzalı
   veya güvenilir olmayan çalıştırılabilir dosyaların (EXE, DLL, sürücü) çalışmasını
   engeller; işletim sistemi böylece bilinmeyen kodu yüklemeyi reddeder.

Buna üstte **Credential Guard** (kimlik sırlarının sanalizasyon altında korunması) ve
**aylık yama döngüsü** eklenir.

CoreMusic açısından anlamı: Windows, bu mimarinin **çalıştığı bir istemci/ortaç (client)
ortamıdır** — CoreMusic backend'i Windows'ta değil, Linux sunucuda çalışır. Windows'ta
yalnızca `web.config`/`.htaccess` gibi yapılandırma yüzeyleri ve UA tabanlı istemci
tespiti vardır; OS'nin kendi güvenlik katmanı CoreMusic'e devredilmiştir.

---

## §1 — OS Güvenlik Profili

### 1.1 Temel katmanlar

| Katman | Mekanizma | Ne yapar | Kaynak |
|---|---|---|---|
| Boot | **Secure Boot** | Açılış zincirindeki her imzayı (UEFI sürücüsü, EFI uygulaması, OS yükleyicisi) doğrular; `db`/`dbx`/`KEK`/`PK` veritabanları firmware NV-RAM'de tutulur | §5 #6 |
| Boot | **Rollback protection** | Firmware'in eski sürüme geri düşmesini engeller | §5 #6 |
| Identity | **Windows Hello for Business / ESS** | Parolasız kimlik ve Enhanced Sign-in Security ile credential theft'i azaltır | §5 #2 |
| Kernel | **Security Reference Monitor** | Çekirdek içinde erişim denetimi (access control) sağlar; sürücüler bu arayüzle çalışır | §5 #3 |
| Kernel | **Kernel-mode sürücü modeli** | Object/Memory/Process-Thread/I/O Manager ile sürücü yüzeyi sınırlandırılır; Safe String Library kernel-mode string güvenliği sağlar | §5 #3 |
| File | **DACL + icacls** | Dosya/dizin sahipliği ve erişim listesi; `/grant`, `/deny`, `/setintegritylevel` ile hak verilir/reddedilir | §5 #4 |
| App | **App Control (WDAC/AppLocker)** | Kod imzalama + policy ile yalnızca onaylı çalıştırılabilir dosyanın çalışması sağlanır; audit/enforcement modları | §5 #5 |
| Identity | **Credential Guard** (genel kabul) | Kimlik sırları hypervisor-altı sanal alanda tutulur — bu dosyada vendor URL ile derinleştirilmedi | ⚠️ |

### 1.2 Güvenlik modelinin varsayılanları

- **Default deny (App Control):** İmzalı/whitelist'te olmayan çalıştırılabilir dosya
  varsayılan olarak engellenir; yalnızca policy'de tanımlananlar çalışır.
- **Least privilege (ACL):** Bir dosyaya erişim, DACL'de açıkça verilmedikçe reddedilir;
  `icacls` miras (inheritance) yoluyla üst konteynerden hak devralır.
- **Defense in depth:** Secure Boot + kernel denetimi + ACL + App Control üst üste
  bindirilmiştir; tek bir katmanın ihlali sistemi düşürmez.
- **Signed boot chain:** UEFI firmware bileşenleri en az RSA-2048 + SHA-256 ile imzalanır
  ve bootloader zinciri boyunca imza doğrulanır (§5 #6).

### 1.3 Bilinen yapısal riskler (genel mühendislik kabulü)

- Legacy/unsigned driver'lar Secure Boot ve sürücü imza denetimini zayıflatabilir.
- Credential Guard yalnızca desteklenen donanım/zamanlama (virtualization-based security)
  etkinse koruma sağlar; devre dışıysa kimlik sırları normal Bellek'te kalabilir.
- Yama gecikmeleri (patch lag) güncellenmemiş cihaz yüzeyi bırakır.

> ⚠️ **VERIFICATION REQUIRED:** Bu risk maddeleri bu oturumda Microsoft sayfasından birebir
> doğrulanmadı; genel mühendislik bilgisi olarak işaretlenmiştir. Somut kanıt §5'teki
> secure-boot ve App Control sayfalarındadır.

---

## §2 — Repo / CoreMusic İzi (DOSYA:SATIR kanıtlı)

> Bu bölümdeki her satır bu oturumda **okunmuş** gerçek dosya satırıdır. Okunmayan iddia yazılmaz.

### 2.1 IIS yapılandırması — security headers (web.config)

`home.coremusic.net/web.config:6-11` (okundu):

| İddia | Kanıt | Durum |
|---|---|---|
| `X-Powered-By` kaldırılır | `web.config:6` → `<remove name="X-Powered-By" />` | ✅ OKUNDU |
| MIME-sniffing kapatılır | `web.config:7` → `X-Content-Type-Options: nosniff` | ✅ OKUNDU |
| Clickjacking koruması | `web.config:8` → `X-Frame-Options: DENY` | ✅ OKUNDU |
| Referrer sızıntısı sınırı | `web.config:10` → `Referrer-Policy: strict-origin-when-cross-origin` | ✅ OKUNDU |
| Kamera/mikrofon/coğrafi izni kapatılır | `web.config:11` → `Permissions-Policy: camera=(), microphone=(), geolocation=()` | ✅ OKUNDU |

`home.coremusic.net/web.config:20-27` (okundu) — hassas dizin/dosyalar 403 ile engellenir:

- `web.config:20-23` → `config|include|pages` yolları `403 Forbidden`.
- `web.config:24-27` → `.env|.log|.json|.lock|.md|.sql|.sh|.bak|.git|.ini|.htpasswd` 403.

### 2.2 Apache yapılandırması — hardening (.htaccess)

`home.coremusic.net/.htaccess` (okundu):

| İddia | Kanıt | Durum |
|---|---|---|
| Dizin listeleme kapatılır | `.htaccess:2` → `Options -Indexes` | ✅ OKUNDU |
| Sunucu imzası kapatılır | `.htaccess:3` → `ServerSignature Off` | ✅ OKUNDU |
| Security headers (Apache) | `.htaccess:10-15` → X-Frame-Options DENY · nosniff · Referrer-Policy · Server/X-Powered-By unset | ✅ OKUNDU |
| Hassas dosya uzantıları deny | `.htaccess:18-20` → `FilesMatch "\.(env\|log\|json\|...)"` → `Require all denied` | ✅ OKUNDU |
| Front-controller dışındaki PHP engelli | `.htaccess:22-24` → `^(?!index\.php$)(?!debug\.php$).+\.php$` → `Require all denied` | ✅ OKUNDU |

**Çift hedef yapılandırma:** `web.config` (IIS) ve `.htaccess` (Apache) birlikte bulunur —
repo genelinde `web.config` ×4 ve `.htaccess` ×4 vardır (glob, 2026-10-15):
`assets.coremusic.net` · `home.coremusic.net` · `auth.coremusic.net` · `api.coremusic.net`.

### 2.3 Sunucu OS tespiti (Windows'a özgü değil)

`shared/src/Api/Bff/DesktopBff.php:35` (okundu) → `'platform' => php_uname('s')`.
Bu, **sunucunun** kernel adını döndürür (örn. `Linux`); Windows ile ilişkisi yoktur.
**Dürüst sınırlama:** bu satır Windows güvenliği için kanıt sayılmaz — CoreMusic backend'i
Windows'ta çalışmaz.

### 2.4 CI — Windows runner yok

`.github/workflows/ci.yml:14-15` (okundu) → `permissions:` / `contents: read` (least-privilege
GITHUB_TOKEN). Job envanteri (okundu): `runs-on: ubuntu-latest` → L27 · L69 · L112 · L144 ·
L163 · L180 → **6 job, hepsi ubuntu-latest**.

- **`windows-latest` runner job'ı YOK** → Windows üzerinde build/test/QA **otomasyonu yok**.
- `.github/workflows/` envanteri (glob): `ci.yml` + `secret-scan.yml` (2 dosya).

### 2.5 Dürüst negatif kanıtlar (2026-10-15 grep)

| Arama (`shared/src`) | Sonuç |
|---|---|
| `wdac` | **0 isabet** |
| `applocker` | **0 isabet** |
| `secureboot` / `secure boot` | **0 isabet** |
| `credential guard` | **0 isabet** |
| Windows'a özgü güvenlik kodu (WDAC/AppLocker politikası, Credential Guard entegrasyonu) | **0 isabet** |

**Sonuç:** CoreMusic, Windows'u *desteklenen bir yapılandırma hedefi* olarak tanır
(`web.config` ×4) ama Windows'a özgü **hiçbir** güvenlik mekanizması (WDAC, AppLocker,
Secure Boot yönetimi, Credential Guard) içermez. IIS/Apache hardening yalnızca HTTP
katmanındadır (header + rewrite + deny); OS güvenliği tamamen platforma devredilmiştir.
CoreMusic'in sorumluluğu §4/K009 sınırındadır.

---

## §3 — K000 Sınırı → `isletim-sistemi-security` BRIDGE

Bu dosya K000 (`isletim-sistemi-turu`) dalındadır; **OS kartı güvenlik detayları**
`isletim-sistemi-security` dalındadır. Aşağıdaki kartlar diskte mevcuttur (envanter
`isletim-sistemi-security/index.md` + glob üzerinden doğrulanmıştır, 2026-10-15):

| Alt dal | Windows kartı | Bağlantı |
|---|---|---|
| çekirdek-guvenlik | `cekirdek-guvenlik-windows.md` | [`../../isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows.md`](../../isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-windows.md) |
| yetki-ve-erisim-modeli | `yetki-ve-erisim-modeli-windows.md` | [`../../isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-windows.md`](../../isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-windows.md) |
| uygulama-guvenlik-siniri | `uygulama-guvenlik-siniri-windows.md` | [`../../isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-windows.md`](../../isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-windows.md) |

**Sınır kuralı:** Bu dosya yalnızca *Windows OS'nin* güvenlik modelini anlatır. CoreMusic
uygulamasının Windows'a karşı yükümlülükleri (token saklama, UA'ya güvenmeme, HTTPS)
**bu dosyada çözülmez** → §4.

**Ne bu dosya devralır / ne devretmez:**

- **Devralır:** Secure Boot, kimlik koruması (Windows Hello/ESS), kernel-mode sürücü
  denetimi, ACL/icacls, App Control — bunlar *Windows'un* güvenlik kararlarıdır.
- **Devretmez:** HTTP güvenlik header'ları ve rewrite/deny kuralları (`web.config`,
  `.htaccess`) CoreMusic'in kendi katmanıdır — bunlar K009 kapsamında ele alınır; bu dosya
  yalnızca *neden* var olduğunu (Windows/IIS/Apache yüzeyi) bağlar.

Ayrıca bu dalın kardeşleri: `[linux](../linux/index.md)` · `[macos](../macos/index.md)` ·
`[ios](../ios/index.md)` · `[android](../android/index.md)` · parent: `[index](../index.md)`.

---

## §4 — K009 Sınırı (Uygulama Güvenliği)

K009 = `K009-security` — CoreMusic uygulama güvenliği (auth, CSRF, CSP, session, rate limit,
Argon2id, AES-256-GCM). Sınır hedefi: [`../../../K009-security/index.md`](../../../K009-security/index.md).

- **Ne bu dosyada, ne K009'da:** OS kernel içi güvenlik politikaları (Secure Boot zinciri,
  WDAC/AppLocker, kernel-mode sürücü denetimi) → bunlar K000/isletim-sistemi-security alanı.
- **Ne K009'da, ne bu dosyada:** Uygulamanın Windows istemcide *nasıl* güvendiği (TLS, token,
  cookie flags, `web.config` header'larının uygulanması) → K009 alanı.

**Geçiş noktası (Windows-özel):**

| Soru | Sahip | Not |
|---|---|---|
| `web.config`/`.htaccess` header'ları K009 politikası mı? | K009 | §2.1-2.2'de kanıtlandı; bu dosya yalnızca HTTP yüzeyini bağlar, politika kararını vermez |
| Windows istemci UA'sına güvenlik kararı bina ediliyor mu? | K009 | §2.3'te `php_uname('s')` sunucu OS'ünü döndürür — istemcini değil; UA spoof riski K009'un |
| WDAC/AppLocker üretimde var mı? | K000 (OS) + DevOps | grep 0 isabet → `⚠️ VERIFICATION REQUIRED` |

---

## §5 — Kaynaklar (bu oturumda erişilmiş · 2026-10-15)

> Tümü bu oturumda **erişilmiş** ve içerik döndürmüştür. Generic OWASP/NIST/CIS
> referansı **kullanılmamıştır** (görev kısıtı). 6 benzersiz URL.

| # | URL | Ne verdi | Durum |
|---|---|---|---|
| 1 | https://learn.microsoft.com/en-us/windows/security/operating-system-security/ | Windows 11 OS güvenlik genel bakış — trusted boot, encryption, ağ ve tehdit koruması | ✅ HTTP 200 |
| 2 | https://learn.microsoft.com/en-us/windows/security/identity/ | Identity protection — Windows Hello for Business, Enhanced Sign-in Security (ESS), credential theft savunması | ✅ HTTP 200 |
| 3 | https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ | Kernel-mode driver architecture — Object/Memory/Process-Thread/I/O Manager, Security Reference Monitor, Safe String Library | ✅ HTTP 200 |
| 4 | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/icacls | `icacls` — DACL görüntüleme/değiştirme, `/grant`, `/deny`, `/setintegritylevel`, miras (inheritance) | ✅ HTTP 200 |
| 5 | https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business/ | App Control (WDAC/AppLocker) — politika tasarımı, imzalama, audit/enforcement modları | ✅ HTTP 200 |
| 6 | https://learn.microsoft.com/en-us/windows-hardware/design/device-experiences/oem-secure-boot | Secure Boot — `db`/`dbx`/`KEK`/`PK` veritabanları, RSA-2048+SHA-256 firmware imzası, açılış zinciri, rollback protection | ✅ HTTP 200 |

**Elenen/askıya alınan iddialar (dürüst kayıt):**
- **Windows Update yama kanalı** → bu oturumda doğrulanmış vendor URL **yok**; §1.3'te
  yama riski genel kabul olarak işaretlendi, somut URL yazılmadı → `⚠️ VERIFICATION REQUIRED`.
- `credential-guard` için ayrı Microsoft Learn sayfası bu oturumda erişilmedi → §1.1'de
  "genel kabul" olarak işaretlendi.

---

## §6 — Açık Sorular

1. **Windows Update yama kanalı:** Üretim/istemci Windows cihazları hangi kanalla güncelleniyor
   (WSUS, Intune, Windows Update for Business)? Repo'da kanıt yok → `⚠️ VERIFICATION REQUIRED`.
2. **WDAC/AppLocker kapsamı:** grep 0 isabet; eğer istemci ortamda App Control kullanılıyorsa
   politika nerede saklanıyor? → DevOps + K000.
3. **`web.config` çift hedefi:** ×4 `web.config` hangi sunucuda aktif (IIS mi, Apache ile
   çakışan yapılandırma mı)? `isletim-sistemi-runtime` §2'ye bakılacak.
4. **Credential Guard donanım bağımlılığı:** VBS (virtualization-based security) hedef
   platformlarda etkin mi? Bilinmiyor → `UNKNOWN`.
5. **CI'da Windows kapsamı yok** (`ci.yml` tüm job'lar ubuntu-latest): Windows güvenlik
   davranışı (ör. header'ların IIS'te gerçekten uygulanması) otomasyonla test edilmiyor —
   bu bilinçli bir kapsam kararı mı?

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
