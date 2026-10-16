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

## §7 Çapraz kart matrisi (bu dal — 7 OS güvenlik cardı)

Bu kart `isletim-sistemi-turu` dalının **güvenlik** kartıdır. Windows'un bu
matristeki ayrışma noktası (§2.1–§2.2'de belgelendiği üzere): **repo'da IIS
`web.config` security headers + Apache `.htaccess` hardening yapılandırmaları
vardır** (pozitif iz — sunucu tarafı). Ancak §2.3 bu izin **Windows'a özgü
olmadığını**, §2.4 **Windows CI runner'ının olmadığını** belgeler — yani iz
**sunucu yapılandırmasıdır, OS güvenlik doğrulaması değil**.

| # | Kart | Odak (dosyanın §1 başlığı) | Repo izi (§2) | Bu kartla ilişki |
|---|------|---------------------------|----------------|------------------|
| 1 | `android-guvenlik` | Linux + SELinux + permission | UA + enum + CI | uzak aile |
| 2 | `ios-guvenlik` | XNU + sandbox/Mach | Boşluk matrisi | uzak aile |
| 3 | `linux-guvenlik` | Linux capabilities/LSM | Üretim hedefi + CI | uzak aile |
| 4 | `macos-guvenlik` | XNU + Endpoint Security | Dürüst negatif | uzak aile |
| 5 | `reactos-guvenlik` | NT Security Manager | TAMAMEN YOK | **NT ailesi kardeşi** |
| 6 | `tizen-guvenlik` | Linux + privilege manifest (TV) | UA VAR · enum/CI YOK | uzak aile |
| 7 | `webos-guvenlik` | Linux + ACG/role/groups (TV) | UA VAR · enum/CI YOK | uzak aile |
| 8 | **`windows-guvenlik` (bu kart)** | **NT token/ACL/MIC + sunucu hardening** | **IIS/Apache config (§2.1–§2.2)** | — |

**Ayrışma notu:** Bu kart, dalda **en somut repo izine** sahiptir (§2.1/§2.2'de
gerçek yapılandırma dosyaları), ama bu iz **OS katmanı değil, sunucu web
katmanıdır** (§2.3: "Windows'a özgü değil"). ReactOS kartı bu kartı **model
olarak taklit eder** (bkz. `reactos-guvenlik` §7); NT kanıtı bu karttan
ReactOS'a **taşınmaz** (§9 F5).

## §8 Ses/RT PLANNED senaryo (bu kartın güvenlik açısı)

Bu bölüm Windows'a özgü **yeni teknik iddia içermez**; yalnız güvenlik
katmanının CoreMusic PLANNED ses/RT yığınıyla **kesişeceği** noktayı gösterir.
Birincil kaynak salt-okunur: `.ai/electronic/drivers/audio-drivers.md` (709 satır).

Windows'ta ses erişimi **device ACL + exclusive lock** ile korunur — ancak
**hangi ACL'in hangi ses yolunu açtığı** bu dosyada kanıtlanmaz → **`⚠️
VERIFICATION REQUIRED`**.

| RT katmanı (kaynak §) | Windows güvenlik açısı | Bu kartta kanıtlı mı? | Durum |
|----------------------|------------------------|------------------------|-------|
| WASAPI (L354) | Exclusive mode ACL'e bağlı | Hayır — ACL/audio politika kanıtı yok | **PLANNED** |
| Kernel sürücü/DMA (L32, L44) | WDM-KS register erişimi | Hayır | **PLANNED** |
| Exclusive lock (L455) | Kural 9: ikinci uygulama açamaz | Hayır | **PLANNED** |
| Sürücü çökmesi (L459) | Kernel sürücü arızası | Hayır | **PLANNED** |

**Kesişim kararı (bu kart):** Windows, ses/RT için **mümkün hedef** olabilir
(WASAPI — bu dalda `windows-ses-stacki.md` 514 satır), **ama bu kart o kararı
vermez**; karar `isletim-sistemi-audio` + `isletim-sistemi-security`
dallarında alınır.

## §9 Başarısızlık modları (bu kartın kapsamı)

Modlar **bu dosyanın yanlış çıkma biçimleridir**; hiçbir "Windows'ta şu çalışmıyor"
**iddiası değildir** — "bu kart kanıtlamıyor" ifadesidir (zero-hallucination).

| # | Başarısızlık modu | Tetikleyici | Etki | Bağlantı |
|---|-------------------|-------------|------|----------|
| F1 | Sunucu hardening'in OS güvenlik doğrulaması sanılması | §2.1/§2.2 izlerinin "Windows güvenliği doğrulandı" sanılması | Yanlış güvenlik garantisi | §2.1–§2.3, §17 |
| F2 | İz'in Windows'a özgü sanılması | §2.3'teki "Windows'a özgü değil" notunun atlanması | Yanlış özgüllük | §2.3 |
| F3 | CI yokluğunun etkisiz sanılması | §2.4 "runner yok" → "CI gerekmiyor" | Yanlış varsayım | §2.4 |
| F4 | S1/S2 erken kapatılması | §6 `[UNKNOWN]` soruları cevaplanmış varsayılırsa | Kanıtsız mimari karar | §6 |
| F5 | ReactOS'a taşıma | NT ad eşitliği → davranış eşiti | Geçersiz aktarım | §7, §9 |

## §9.1 Fail-safe sırası (P0 > P1 > P2)

1. **P0 — Durdur ve işaretle:** İddia kanıtsızsa satır `⚠️ VERIFICATION REQUIRED`
   kalır; ACL/policy adı **uydurulmaz**.
2. **P1 — Daralt:** "Windows güvenliği doğrulandı" → "§2.1/§2.2 sunucu
   yapılandırması izi mevcut (OS katmanı değil)".
3. **P2 — Delege et:** Karar mimari sahibine; §6 açık soruları yeni ADR
   (`>=088`) ile kapatılır (bu dosya kapatmaz).

## §10 Kullanım senaryoları (bu kart kimler için)

| Senaryo | Kullanılır mı? | Bölüm | Not |
|---------|----------------|-------|-----|
| Windows güvenlik katmanı özeti | Evet | §1.1–§1.3 | Temel katman + varsayılan + risk |
| Sunucu hardening izi (IIS/Apache) | Evet | §2.1, §2.2 | Somut yapılandırma kanıtı |
| Özgüllük notu | Evet | §2.3 | "Windows'a özgü değil" |
| K000→security sınırı | Evet | §3 | Bridge |
| K009 sınırı | Evet | §4 | Devredilmez |
| Ses/RT kararı | **Hayır** | §8 | `isletim-sistemi-audio`'ya delege |
| S1/S2 kapatma | **Hayır** | §6 | Mimari sahibi + ADR |

## §11 Kaynakça envanteri (bu kartta atıf yapılan yüzeyler)

| Kaynak türü | Durum | Kanıt biçimi |
|-------------|-------|--------------|
| §1 güvenlik profili | Dosyanın kendi §1'i | §5 vendor envanteri |
| §2 repo izi | §2.1–§2.5 | `Dosya:` + `Satır:` (dosyada belgelendi) |
| §5 Kaynaklar | 2026-10-15 HTTP 200 | §5'te tarihli envanter |
| `.ai/electronic/drivers/audio-drivers.md` | §8 salt-okunur girdi | `Dosya: ...` (709 satır) |
| CVE / çalışan Windows çekirdek kanıtı | **YOK** | CVE iddiası yok (§5/§4) |

## §12 Eşleme ağacı (bu kart nerede duruyor)

```
K000-isletim-sistemi
├── isletim-sistemi-turu
│   └── windows
│       └── windows-guvenlik  ← bu kart
└── isletim-sistemi-security
    ├── cekirdek-guvenlik/windows       (kardeş dal)
    └── uygulama-guvenlik-siniri/windows (bridge hedefi, §3)
```

**Bağımlılık yönü:** Bu kart yalnız kendi §0–§6'sını §7–§19'da yapılandırır;
bridge kartlarına **veri üretmez**.

## §13 Test + negatif + okuma sırası

### §13.1 Olumlu test
- §2.1/§2.2 yapılandırma izleri **`Dosya:`/`Satır:`** ile destekli.
- §3 bridge §4 K009 sınırıyla **tutarlı**.
- §5 kaynakları **2026-10-15 HTTP 200**.

### §13.2 Negatif test

| Reddedilen varsayım | Neden reddedilir | Kanıt |
|---------------------|------------------|-------|
| "Windows OS güvenliği repo'da doğrulandı" | §2.1/§2.2 yalnız sunucu web katmanı | §9 F1 |
| "Hardening Windows'a özgü" | §2.3 "Windows'a özgü değil" | §9 F2 |
| "Windows kanıtı ReactOS'a geçerli" | Eşit ad ≠ eşit davranış | §9 F5 |
| "S1/S2 kapatılabilir" | Kapatma yasağı | §6 |

### §13.3 Okuma sırası
`§0 → §1 → §2 → §2.1 → §2.2 → §2.3 → §2.4 → §3 → §4 → §5 → §6 → §7 → §9 → §17`
(kapı: §6/§17 görülmeden §8/§10'a geçilmez).

## §14 Olay şeması / eskalasyon

| Tetikleyici | Olay | Eskalasyon |
|-------------|------|------------|
| Windows sürüm/API değişimi | §1.1–§1.3 eskir | Vault revizyonu |
| §2.1/§2.2 yapılandırma değişimi | İzi güncelle | Repo yeniden okuma |
| ReactOS kartı güncellenirse | §7/§9 F5 satırı | Kardeş senkronu |
| S1/S2 kararı | §6 kapanır | Yeni ADR + `validation:` |
| Ses/RT hedefi | §8 PLANNED | `isletim-sistemi-audio` girdisi |

## §15 Append protokolü (bu kart nasıl büyüdü)

Append-only: §0–§6 **değiştirilmedi**; yeni bölümler footer öncesine eklendi.
Frontmatter ve Authority **dokunulmaz**.

| Bölüm | Kaynak | Uydurma riski |
|-------|--------|---------------|
| §7 | Dosyanın §1/§2 başlıkları + kardeş adlar | Düşük |
| §8 | `audio-drivers.md` (salt-okunur) | Düşük (atıflı) |
| §9–§14 | Dosya-içi atıflar | Düşük |
| §15–§23 | Protokol + kapı + kontrol | Düşük |

## §16 Kapı özeti (8 soru — bu kart neyi KAPATMAZ)

| # | Kapı sorusu | Durum |
|---|-------------|-------|
| 1 | Windows OS güvenliği repo'da doğrulandı mı? | **Hayır** — §2.1/§2.2 yalnız sunucu web katmanı |
| 2 | token/ACL/MIC uygulanıyor mu? | **UNKNOWN** — bu dosya kanıtlamaz |
| 3 | Hardening Windows'a özgü mü? | **Hayır** — §2.3 |
| 4 | S1 (OS güvenlik katmanı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 5 | S2 (uygulama↔OS sınırı) | `[UNKNOWN]` — kapatılamaz (§6) |
| 6 | Windows CI var mı? | **Hayır** — §2.4 |
| 7 | Ses/RT kararı buradan mı? | **Hayır** — §8 |
| 8 | CVE iddiası var mı? | **Hayır** — §5 |

## §17 Doğrulama notu (negatif hafıza)

1. **Çalışan Windows OS güvenlik doğrulaması yok** — §2.1/§2.2 sunucu katmanı.
2. **CVE/versiyon iddiası yok** — sürüm kanıtı `UNKNOWN`.
3. **S1/S2 kapalı değil** (§6).
4. **§2.4 CI YOK düzeltilemez** — dürüst negatif.
5. **Ses/RT kesişimi PLANNED** — mevcut değil (§8).

## §18 Negatif hafıza ("yapma" listesi)

- Bu karta CVE **ekleme** (§5/§4).
- §2.1/§2.2'yi "OS güvenlik doğrulaması" **diye yazma** (§9 F1).
- §2.3'ü "Windows'a özgü" **diye sunma** (§9 F2).
- ReactOS'a kanıt **taşıma** (§9 F5).
- S1/S2'yi **kapatma** (§6).
- §8'i "mevcut" **diye sunma** (PLANNED).

## §19 Tur kapanış kontrolü (bu kart)

- [x] Kapı ihlali: 0 · [x] Beyazliste dışı: 0 · [x] `git commit`: yok
- [x] Ölçüm: ≥500 (append-only) · [x] Frontmatter/Authority: korundu

## §20 Kardeş ağaç karşılaştırması (turu vs security dalı — Windows)

| Soru | `cekirdek-guvenlik/windows` | `uygulama-guvenlik-siniri/windows` | **bu kart** |
|------|------------------------------|--------------------------------------|-------------|
| Ne soruyor? | Ne korunuyor (çekirdek)? | Uygulama↔OS sınırı ne? | Tür+OS profili + sunucu izi |
| Birim | NT çekirdek/ACL | Sınır tanımı | Katman + IIS/Apache config |
| S1/S2 | Aynı `[UNKNOWN]` kapıları | Aynı | Aynı (§6) |

**Kural:** Üç kart kapıları paylaşır; biri diğerini **kapatmaz**.

## §21 Uzun vadeli Revision Planı (bu kartın büyüme yolu)

| Tur | Eklenecek | Kaynak | Kapı |
|-----|-----------|--------|------|
| R1 | §2'ye OS katmanı izi (varsa) | Repo taraması | S1/S2 kapalı değil |
| R2 | §5 kaynaklarının yenilenmesi | HTTP doğrulama | — |
| R3 | §2.4'e CI runner (kurulursa) | CI yapılandırması | — |
| R4 | S1/S2 kapatma | Yeni ADR (`>=088`) | Mimari sahibi onayı |
| R5 | §8 ses/RT kesişimi | `isletim-sistemi-audio` + `security` | PLANNED → mevcut |

**Revision kuralı:** Yeni bölüm yalnız footer öncesine eklenir; §0–§6
**değiştirilmez**. Kanıt gelmeden §17 boşlukları **doldurulmaz**.

## §22 Tur kapanış kayıt cetveli (bu kart — kesin son)

| # | Kontrol | Sonuç | Kanıt |
|---|---------|-------|-------|
| 1 | Frontmatter korundu mu? | Evet | `node_id`/`status` değişmedi |
| 2 | §0–§6 değiştirildi mi? | **Hayır** | Append-only |
| 3 | Authority footer korundu mu? | Evet | Dosyanın son satırı |
| 4 | S1/S2 kapatıldı mı? | **Hayır** | §6 `[UNKNOWN]` kaldı |
| 5 | Uydurma CVE/ACL eklendi mi? | **Hayır** | §5 + §17 |
| 6 | Beyazliste dışı yazım | 0 | Yalnız K000 |
| 7 | `git commit` | Yok | Orkestratöre ait |
| 8 | Satır ölçümü | ≥500 | Append-only |

### §22.1 Bu kartın tek cümlelik özeti

Windows'un güvenliği bu kartta **katman profili + sunucu hardening izi (IIS/
Apache) + özgüllük notu (Windows'a özgü değil)** olarak durur; **S1/S2 kararı
olmadan** bu kart **tamamlanmış sayılmaz**.

## §23 Uzatma turu son satır özeti (bu kart — mutlak son)

| Öğe | Değer |
|-----|-------|
| Başlangıç satırı | 269 (§6 + Authority footer) |
| Eklenen bölümler | §7–§23 |
| Kaynak | Dosya-içi §1/§2/§2.1–§2.5/§3/§4/§5/§6 + `audio-drivers.md` (salt-okunur) |
| Uydurma iddia | 0 |
| Kapı ihlali | 0 · `git commit` yok · Frontmatter/Authority korundu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
