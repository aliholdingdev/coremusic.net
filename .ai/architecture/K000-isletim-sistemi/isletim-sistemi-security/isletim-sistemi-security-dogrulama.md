---
node_id: K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
evidence:
  - https://docs.kernel.org/security/self-protection.html
  - https://docs.kernel.org/admin-guide/LSM/index.html
  - https://docs.kernel.org/admin-guide/LSM/SELinux.html
  - https://apparmor.net/
  - https://github.com/seccomp/libseccomp
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://support.apple.com/guide/security/volume-owner-sec8a79b1e66/web
  - shared/src/Middleware/OriginCheckMiddleware.php:55
  - shared/src/Security/SecurityHelper.php:25
validation:
  - "2026-10-13: 68 benzersiz URL HEAD ile HTTP 200 dogrulandi; hicbiri katalog envanterinde (2097) yok -> kesisim 0."
  - "2026-10-13: 43 disk kaniti Select-Object -Index (N-1) ile satir birebir okundu; uydurma satir numarasi 0."
open_questions:
  - "S1: OS guvenlik katmani devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS sorumluluk siniri? [UNKNOWN]"
group: kanit
---

# isletim-sistemi-security — Dogrulama

**Dogrulama tarihi:** 2026-10-13 · **Erisim:** HTTP HEAD yanit kodu + disk satir okuma · **Ortam:** Windows (PowerShell) · **Kapsam:** bu subdomainin 16 dosyasinin tamami.

> **Kural:** Her iddia satiri bir kanita baglidir. Dogrulanamayan iddia `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` olarak isaretlenir; uydurulmaz.

## §1 — Web kanitlari (HTTP 200 · 2026-10-13 · envanter disi · 3. tur 2026-10-14: §15)

### §1.1 Ana kanit matrisi (bu subdomainin dayandigi 6 birincil kaynak)

| # | Iddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| W-01 | Cekirdek kendini koruma (self-protection) mekanizmalari dokumante edilmistir | https://docs.kernel.org/security/self-protection.html | HEAD | HTTP 200 |
| W-02 | Linux Security Modules (LSM) catisi resmi cekirdek dokumantasyonundadir | https://docs.kernel.org/admin-guide/LSM/index.html | HEAD | HTTP 200 |
| W-03 | SELinux cekirdek uzerindeki entegrasyonu resmi dokumantasyonda tanimlanir | https://docs.kernel.org/admin-guide/LSM/SELinux.html | HEAD | HTTP 200 |
| W-04 | AppArmor projesinin resmi sitesi erisilebilirdir | https://apparmor.net/ | HEAD | HTTP 200 |
| W-05 | libseccomp resmi kaynak deposu erisilebilirdir | https://github.com/seccomp/libseccomp | HEAD | HTTP 200 |
| W-06 | POSIX `chmod` fonksiyonu Open Group tarafindan tanimlanir | https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | HEAD | HTTP 200 |
| W-07 | macOS `Volume Owner` kavrami Apple Platform Security'de tanimlanir | https://support.apple.com/guide/security/volume-owner-sec8a79b1e66/web | HEAD | HTTP 200 |

### §1.2 Tam URL defteri (bu subdomainin 16 dosyasinda gecen tum URL'ler)

> Tumu 2026-10-13 tarihinde `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` ile sorgulanmistir. "Env." sutunu: URL'in katalog envanterinde (2097 benzersiz URL) onceden bulunup bulunmadigi — **hepsi YOK** = kesisim 0.

| # | Iddia | URL | 2026-10-13 | Durum | Env. |
|---|---|---|---|---|---|
| U01 | Cekirdek kendini koruma belgesi | https://docs.kernel.org/security/self-protection.html | HEAD | HTTP 200 | YOK |
| U02 | LSM catisi dokumantasyonu | https://docs.kernel.org/admin-guide/LSM/index.html | HEAD | HTTP 200 | YOK |
| U03 | SELinux cekirdek entegrasyonu | https://docs.kernel.org/admin-guide/LSM/SELinux.html | HEAD | HTTP 200 | YOK |
| U04 | x86 cpuinfo bayrak alani (SMEP/SMAP isaretleri) | https://docs.kernel.org/arch/x86/cpuinfo.html | HEAD | HTTP 200 | YOK |
| U05 | Windows TPM genel bakis | https://learn.microsoft.com/en-us/windows/security/hardware-security/tpm/trusted-platform-module-overview | HEAD | HTTP 200 | YOK |
| U06 | Windows Job Object API | https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects | HEAD | HTTP 200 | YOK |
| U07 | Apple Platform Security ana sayfasi | https://support.apple.com/guide/security/welcome/web | HEAD | HTTP 200 | YOK |
| U08 | POSIX `chmod` spesifikasyonu | https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | HEAD | HTTP 200 | YOK |
| U09 | NIST SP 800-122 (PII koruma) | https://csrc.nist.gov/pubs/sp/800/122/final | HEAD | HTTP 200 | YOK |
| U10 | bubblewrap resmi deposu (Linux sandbox) | https://github.com/containers/bubblewrap | HEAD | HTTP 200 | YOK |
| U11 | libseccomp resmi deposu | https://github.com/seccomp/libseccomp | HEAD | HTTP 200 | YOK |
| U12 | Apple App Sandbox API dokumani | https://developer.apple.com/documentation/security/app-sandbox | HEAD | HTTP 200 | YOK |
| U13 | `proc(5)` — /proc dosya sistemi | https://man7.org/linux/man-pages/man5/proc.5.html | HEAD | HTTP 200 | YOK |
| U14 | Cekirdek security alt sistemi (next dal) | https://docs.kernel.org/next/security/index.html | HEAD | HTTP 200 | YOK |
| U15 | kernel.org HTML aynasi — self-protection | https://www.kernel.org/doc/html/latest/security/self-protection.html | HEAD | HTTP 200 | YOK |
| U16 | AppArmor proje sitesi | https://apparmor.net/ | HEAD | HTTP 200 | YOK |
| U17 | AppArmor kaynak deposu | https://gitlab.com/apparmor/apparmor | HEAD | HTTP 200 | YOK |
| U18 | cgroup v2 yonetim belgesi | https://docs.kernel.org/admin-guide/cgroup-v2.html | HEAD | HTTP 200 | YOK |
| U19 | IP sysctl parametreleri | https://docs.kernel.org/networking/ip-sysctl.html | HEAD | HTTP 200 | YOK |
| U20 | Yonetici kararnamesi 14028 ozeti (NIST) | https://www.nist.gov/itl/executive-order-14028-improving-nations-cybersecurity | HEAD | HTTP 200 | YOK |
| U21 | Windows Defender Application Guard genel bakis | https://learn.microsoft.com/en-us/windows/security/threat-protection/windows-defender-application-guard/wd-app-guard-overview | HEAD | HTTP 200 | YOK |
| U22 | pagemap belgesi (fiziksel sayfa haritasi) | https://docs.kernel.org/admin-guide/mm/pagemap.html | HEAD | HTTP 200 | YOK |
| U23 | initrd/on-kok yukleme belgesi | https://docs.kernel.org/admin-guide/initrd.html | HEAD | HTTP 200 | YOK |
| U24 | Cekirdek kimlik belgelendirmesi (credentials) | https://docs.kernel.org/security/credentials.html | HEAD | HTTP 200 | YOK |
| U25 | IMA sablonlari (butunluk olcumu) | https://docs.kernel.org/security/IMA-templates.html | HEAD | HTTP 200 | YOK |
| U26 | Bellek yonetimi kavramlari | https://docs.kernel.org/admin-guide/mm/concepts.html | HEAD | HTTP 200 | YOK |
| U27 | kernel.org HTML aynasi — LSM | https://www.kernel.org/doc/html/latest/admin-guide/LSM/index.html | HEAD | HTTP 200 | YOK |
| U28 | kernel.org HTML aynasi — seccomp filter | https://www.kernel.org/doc/html/latest/userspace-api/seccomp_filter.html | HEAD | HTTP 200 | YOK |
| U29 | `chroot(1)` komutu | https://man7.org/linux/man-pages/man1/chroot.1.html | HEAD | HTTP 200 | YOK |
| U30 | `chroot(2)` sistem cagrisi | https://man7.org/linux/man-pages/man2/chroot.2.html | HEAD | HTTP 200 | YOK |
| U31 | `pivot_root(2)` — kok degistirme | https://man7.org/linux/man-pages/man2/pivot_root.2.html | HEAD | HTTP 200 | YOK |
| U32 | `mount_setattr(2)` — mount kisitlari | https://man7.org/linux/man-pages/man2/mount_setattr.2.html | HEAD | HTTP 200 | YOK |
| U33 | The Open Group ana sayfasi | https://www.opengroup.org/ | HEAD | HTTP 200 | YOK |
| U34 | NIST Cybersecurity Framework | https://www.nist.gov/cyberframework | HEAD | HTTP 200 | YOK |
| U35 | NIST SP 800-192 (guvenli mimari) | https://csrc.nist.gov/publications/detail/sp/800-192/final | HEAD | HTTP 200 | YOK |
| U36 | SELinux proje deposu | https://github.com/SELinuxProject/selinux | HEAD | HTTP 200 | YOK |
| U37 | Landlock kullanici-arayuzu belgesi | https://docs.kernel.org/userspace-api/landlock.html | HEAD | HTTP 200 | YOK |
| U38 | Windows surucu gelistirme portali | https://learn.microsoft.com/en-us/windows-hardware/drivers/develop/ | HEAD | HTTP 200 | YOK |
| U39 | `CreateJobObjectA` API | https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-createjobobjecta | HEAD | HTTP 200 | YOK |
| U40 | `InitializeSecurityDescriptor` API | https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-initializesecuritydescriptor | HEAD | HTTP 200 | YOK |
| U41 | Apple Platform Security sayfa 103229 | https://support.apple.com/en-us/103229 | HEAD | HTTP 200 | YOK |
| U42 | macOS Volume Owner kavrami | https://support.apple.com/guide/security/volume-owner-sec8a79b1e66/web | HEAD | HTTP 200 | YOK |
| U43 | Apple "security by design" belgesi | https://support.apple.com/guide/security/security-by-design-fsec0a10a6e/web | HEAD | HTTP 200 | YOK |
| U44 | kernel.org HTML aynasi — x86 cpuinfo | https://www.kernel.org/doc/html/latest/arch/x86/cpuinfo.html | HEAD | HTTP 200 | YOK |
| U45 | `netlink(7)` — kullanici-cekirdek iletisimi | https://man7.org/linux/man-pages/man7/netlink.7.html | HEAD | HTTP 200 | YOK |
| U46 | `attributes(7)` — POSIX dosya nitelikleri | https://man7.org/linux/man-pages/man7/attributes.7.html | HEAD | HTTP 200 | YOK |
| U47 | `umask(2)` — izin maskesi | https://man7.org/linux/man-pages/man2/umask.2.html | HEAD | HTTP 200 | YOK |
| U48 | `getumask(3)` — maske okuma | https://man7.org/linux/man-pages/man3/getumask.3.html | HEAD | HTTP 200 | YOK |
| U49 | `fs.*` sysctl parametreleri | https://docs.kernel.org/admin-guide/sysctl/fs.html | HEAD | HTTP 200 | YOK |
| U50 | `kernel.*` sysctl parametreleri | https://docs.kernel.org/admin-guide/sysctl/kernel.html | HEAD | HTTP 200 | YOK |
| U51 | KFENCE (rastgele bellek hatasi dedektoru) | https://docs.kernel.org/dev-tools/kfence.html | HEAD | HTTP 200 | YOK |
| U52 | `capget(2)` — yetki seti okuma | https://man7.org/linux/man-pages/man2/capget.2.html | HEAD | HTTP 200 | YOK |
| U53 | Cekirdek BPF dokumantasyonu | https://docs.kernel.org/bpf/index.html | HEAD | HTTP 200 | YOK |
| U54 | Cekirdek paket filtresi (BPF/sk_filter) | https://docs.kernel.org/networking/filter.html | HEAD | HTTP 200 | YOK |
| U55 | SysRq tuslari (acil komutlar) | https://docs.kernel.org/admin-guide/sysrq.html | HEAD | HTTP 200 | YOK |
| U56 | Donanim aciklari (hw-vuln) indeksi | https://docs.kernel.org/admin-guide/hw-vuln/index.html | HEAD | HTTP 200 | YOK |
| U57 | Yama LSM — ptrace sinirlama | https://www.kernel.org/doc/html/latest/admin-guide/LSM/Yama.html | HEAD | HTTP 200 | YOK |
| U58 | `setfilecon(3)` — dosya etiketi yazma | https://man7.org/linux/man-pages/man3/setfilecon.3.html | HEAD | HTTP 200 | YOK |
| U59 | `setfiles(8)` — etiket esleme | https://man7.org/linux/man-pages/man8/setfiles.8.html | HEAD | HTTP 200 | YOK |
| U60 | `chcon(1)` — etiket degistirme | https://man7.org/linux/man-pages/man1/chcon.1.html | HEAD | HTTP 200 | YOK |
| U61 | `setfacl(1)` — ACL yazma | https://man7.org/linux/man-pages/man1/setfacl.1.html | HEAD | HTTP 200 | YOK |
| U62 | `getfacl(1)` — ACL okuma | https://man7.org/linux/man-pages/man1/getfacl.1.html | HEAD | HTTP 200 | YOK |
| U63 | Bash komut yerine koyma (command substitution) | https://www.gnu.org/software/bash/manual/html_node/Command-Substitution.html | HEAD | HTTP 200 | YOK |
| U64 | Windows Defender Application Control | https://learn.microsoft.com/en-us/windows/security/application-security/application-control/app-control-for-business | HEAD | HTTP 200 | YOK |
| U65 | macOS App Sandbox (Platform Security) | https://support.apple.com/guide/security/sandbox-sec87e29b130/web | HEAD | HTTP 200 | YOK |
| U66 | macOS Secure Boot (Platform Security) | https://support.apple.com/guide/security/secure-boot-sec4d968d614/web | HEAD | HTTP 200 | YOK |
| U67 | Apple "security by design" (2. belge) | https://support.apple.com/guide/security/security-by-design-sec2a5b0f03a/web | HEAD | HTTP 200 | YOK |
| U68 | macOS kernel extension policy yonetimi | https://support.apple.com/guide/deployment/manage-the-kernel-extension-policy-in-macos-dep5b40c0d18/web | HEAD | HTTP 200 | YOK |

**Sayim:** 68 benzersiz URL · HTTP 200 = 68/68 · envanterde onceden bulunan = 0 · kesisim = 0.

### §1.3 Reddedilen adaylar (HTTP 200 DEGIL — kullanilmadi)

| # | Aday URL | Sonuc | Karar |
|---|---|---|---|
| R01 | `https://docs.kernel.org/admin-guide/LSM/AppArmor.html` | HTTP 404 | Kullanilmadi |
| R02 | `https://docs.kernel.org/admin-guide/LSM/Landlock.html` | HTTP 404 | Kullanilmadi |
| R03 | `https://learn.microsoft.com/en-us/windows/security/secure-boot/secure-boot-overview` | HTTP 404 | Kullanilmadi |
| R04 | `https://pubs.opengroup.org/onlinepubs/9699919799/basedefs/sys_stat.html` | HTTP 404 | Kullanilmadi |
| R05 | `https://man7.org/linux/man-pages/man7/lsm.7.html` | HTTP 404 | Kullanilmadi |
| R06 | `https://man7.org/linux/man-pages/man7/shell_syntax.7.html` | HTTP 404 | Kullanilmadi |
| R07 | `https://docs.kernel.org/filesystems/posix_acl.html` | HTTP 404 | Kullanilmadi |
| R08 | `https://selinuxproject.org/` | HTTP hata | Kullanilmadi — yerine U36 (github SELinuxProject) alindi |

> 404/HATA donen URL hicbir dosyaya yazılmamistir. Boylece "uydurma URL" riski kapatilmistir.

## §2 — Disk kanitlari (Select-Object -Index ile satir birebir dogrulama)

### §2.1 Olumlu kanitlar

| # | Iddia | Dosya | Satir | Dogrulama indeksi | Okunan icerik |
|---|---|---|---|---|---|
| D-01 | Dizin izni sabiti 0750 | `shared/src/Session/SessionConfig.php` | 20 | `Select-Object -Index 19` | `public const DIR_PERMISSIONS = 0750;` |
| D-02 | Oturum dizini 0750 ile olusturulur | `shared/src/Session/SessionConfig.php` | 35 | `Select-Object -Index 34` | `@mkdir($savePath, 0750, true);` |
| D-03 | Cookie HttpOnly | `shared/src/Session/SessionConfig.php` | 59 | `Select-Object -Index 58` | `'httponly'  => true,` |
| D-04 | Cookie SameSite=Lax | `shared/src/Session/SessionConfig.php` | 60 | `Select-Object -Index 59` | `'samesite' => 'Lax',` |
| D-05 | Nokta-nokta segmenti reddi | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 181 | `Select-Object -Index 180` | `if (in_array('..', $segments, true)) {` |
| D-06 | Sembolik baglanti reddi | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 185 | `Select-Object -Index 184` | `if (is_link($trimmed)) {` |
| D-07 | `realpath` ile gercek yol cozumu | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 189 | `Select-Object -Index 188` | `$real = realpath($trimmed);` |
| D-08 | Kutuphane kokunun gercek yolu | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 194 | `Select-Object -Index 193` | `$root = realpath($this->libraryPath);` |
| D-09 | Onek karsilastirmasi icin hazirlik | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199 | `Select-Object -Index 198` | `$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;` |
| D-10 | Kok disina cikis reddi | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 200 | `Select-Object -Index 199` | `if (!str_starts_with($real, $prefix)) {` |
| D-11 | Dizin listeleme kapali | `home.coremusic.net/.htaccess` | 2 | `Select-Object -Index 1` | `Options -Indexes` |
| D-12 | Clickjacking basligi (Apache) | `home.coremusic.net/.htaccess` | 10 | `Select-Object -Index 9` | `Header always set X-Frame-Options "DENY"` |
| D-13 | MIME sniffing basligi (Apache) | `home.coremusic.net/.htaccess` | 11 | `Select-Object -Index 10` | `Header always set X-Content-Type-Options "nosniff"` |
| D-14 | Tehlikeli uzantilara erisim reddi | `home.coremusic.net/.htaccess` | 18-19 | `Select-Object -Index 17,18` | `<FilesMatch "\.(env\|log\|json\|...)$">` + `Require all denied` |
| D-15 | Dizin yasagi rewrite kurali | `home.coremusic.net/.htaccess` | 35 | `Select-Object -Index 34` | `RewriteRule ^(config\|include\|pages)(/.*)?$ - [F,L]` |
| D-16 | Shell arguman kacisi | `shared/src/AI/AIEngine.php` | 292 | `Select-Object -Index 291` | `escapeshellarg($filePath)` |
| D-17 | Shell cagrisi (kacis sonrasi) | `shared/src/AI/AIEngine.php` | 295 | `Select-Object -Index 294` | `$output = shell_exec($cmd);` |
| D-18 | CSRF timing-safe karsilastirma | `shared/src/Middleware/CsrfMiddleware.php` | 42 | `Select-Object -Index 41` | `... !hash_equals($sessionToken, (string)$submitted)) {` |
| D-19 | CSRF reddi HTTP 403 | `shared/src/Middleware/CsrfMiddleware.php` | 58 | `Select-Object -Index 57` | `'httpStatus' => 403,` |
| D-20 | CSP nonce uretimi (32 bayt) | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 18 | `Select-Object -Index 17` | `$request['_csp_nonce'] = bin2hex(random_bytes(32));` |
| D-21 | X-Frame-Options DENY (PHP) | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 28 | `Select-Object -Index 27` | `'X-Frame-Options'                => 'DENY',` |
| D-22 | Trusted Types zorunlulugu | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 32 | `Select-Object -Index 31` | `'Require-Trusted-Types-For'      => "'script'",` |
| D-23 | `frame-ancestors 'none'` | `shared/src/Middleware/SecurityHeadersMiddleware.php` | 71 | `Select-Object -Index 70` | `"frame-ancestors 'none'; " .` |
| D-24 | Rate limit varsayilan 60 istek | `shared/src/Middleware/RateLimiterMiddleware.php` | 39 | `Select-Object -Index 38` | `private readonly int $maxRequests   = 60,` |
| D-25 | Rate limit penceresi 60 saniye | `shared/src/Middleware/RateLimiterMiddleware.php` | 40 | `Select-Object -Index 39` | `private readonly int $windowSeconds = 60,` |
| D-26 | Auth yollari fail-closed listesi | `shared/src/Middleware/RateLimiterMiddleware.php` | 20 | `Select-Object -Index 19` | `public const AUTH_FAIL_CLOSED_PATHS = [` |
| D-27 | Cache yokken auth yolu 503 | `shared/src/Middleware/RateLimiterMiddleware.php` | 59 | `Select-Object -Index 58` | `'httpStatus' => 503,` |
| D-28 | Esik asimi kontrolu | `shared/src/Middleware/RateLimiterMiddleware.php` | 86 | `Select-Object -Index 85` | `if ((int)$count > $this->maxRequests) {` |
| D-29 | Asimda 429 | `shared/src/Middleware/RateLimiterMiddleware.php` | 94 | `Select-Object -Index 93` | `'httpStatus' => 429,` |
| D-30 | BypassAuth fail-closed (credential eksik) | `shared/src/Middleware/BypassAuthMiddleware.php` | 30 | `Select-Object -Index 29` | `return [];` |
| D-31 | BypassAuth fail-closed (config yok) | `shared/src/Middleware/BypassAuthMiddleware.php` | 43 | `Select-Object -Index 42` | `return $next($request);` |
| D-32 | Production'da bypass kapali | `shared/src/Security/SecurityHelper.php` | 25-26 | `Select-Object -Index 24,25` | `if ($config->get('app.env') === 'production') {` + `return false;` |
| D-33 | Izinsiz origin reddi HTTP 403 | `shared/src/Middleware/OriginCheckMiddleware.php` | 55 | `Select-Object -Index 54` | `'httpStatus' => 403,` |
| D-34 | Origin olayi loglanir | `shared/src/Middleware/OriginCheckMiddleware.php` | 48 | `Select-Object -Index 47` | `...->securityEvent('origin_blocked', [` |
| D-35 | CORS whitelist kucuk harfe cevrilir | `shared/src/Middleware/CorsMiddleware.php` | 45 | `Select-Object -Index 44` | `$this->allowedOrigins = array_map('strtolower', ...)` |
| D-36 | RBAC rol eslesmezse 403 | `shared/src/Middleware/PermissionMiddleware.php` | 31 | `Select-Object -Index 30` | `if ($requiredRole !== null && $userRole !== $requiredRole) {` |
| D-37 | RBAC yetki yoksa 403 | `shared/src/Middleware/PermissionMiddleware.php` | 40 | `Select-Object -Index 39` | `return $this->forbidden('permission_denied', ...)` |
| D-38 | Oturum kimligi yetkilendirme alani | `shared/src/Middleware/AuthMiddleware.php` | 29 | `Select-Object -Index 28` | `'permissions' => is_array($permissions) ? $permissions : [],` |
| D-39 | Oturum kimligi yeniden uretimi | `shared/src/Session/SessionBootstrapper.php` | 58 | `Select-Object -Index 57` | `session_regenerate_id(true);` |
| D-40 | Yasam dongusu icinde kimlik yenileme | `shared/src/Session/SessionLifecycle.php` | 60 | `Select-Object -Index 59` | `session_regenerate_id(true);` |
| D-41 | Pipeline sirasi ters cevrilerek kurulur | `shared/src/Middleware/MiddlewarePipeline.php` | 32 | `Select-Object -Index 31` | `foreach (array_reverse($this->middlewares) as $middleware) {` |
| D-42 | API dizin listeleme kapali | `api.coremusic.net/.htaccess` | 2 | `Select-Object -Index 1` | `Options -Indexes` |
| D-43 | API'de index.php disi PHP reddi | `api.coremusic.net/.htaccess` | 24-25 | `Select-Object -Index 23,24` | `<FilesMatch "^(?!index\.php$).+\.php$">` + `Require all denied` |

**Sayim:** 43 disk kaniti · `Select-Object -Index (N-1)` ile 43/43 dogrulandi · uydurma satir numarasi 0.

### §2.2 Negatif kanit (OS guvenlik aracinin yoklugu)

| # | Arama kalibi | Kapsam dosyalari | Sonuc | Tarih |
|---|---|---|---|---|
| N-01 | `seccomp` | `shared/src`, `api.coremusic.net`, `home.coremusic.net` (`vendor` disi, `*.php`+`*.js`) | 0 isabet | 2026-10-13 |
| N-02 | `cap_set_proc` / `capget` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-03 | `prctl` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-04 | `landlock` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-05 | `apparmor` / `selinux` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-06 | `posix_setuid` / `posix_setgid` / `setuid` / `setgid` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-07 | `chroot` / `bubblewrap` / `JobObject` / `sandbox-exec` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-08 | `unshare` / `CLONE_NEW` | ayni kapsam | 0 isabet | 2026-10-13 |
| N-09 | Toplam (tek birlesik tarama) | 2 grup kalip | **0 isabet** | 2026-10-13 |

> **Yorum:** 0 isabet bir "eksiklik" iddiasi degil, **mevcut durum olcumudur**. OS katmani hedef mimari referansidir (S1 [UNKNOWN]).

## §3 — Yapinsal (frontmatter) dogrulama

| # | Kontrol | Beklenen | Bulunan | Durum |
|---|---|---|---|---|
| F-01 | Dosya sayisi (bu subdomain) | 16 | 16 | GECTI |
| F-02 | `parent:` alani kullanan dosya | 0 | 0 | GECTI |
| F-03 | `group` tirnakli yazilan dosya | 0 | 0 | GECTI |
| F-04 | Eksik `---` kapanisi olan dosya | 0 | 0 | GECTI |
| F-05 | 9. alan `group` bulunmayan dosya | 0 | 0 | GECTI |
| F-06 | `children: []` biciminde bos liste olmayan bos cocuk | 0 | 0 | GECTI |
| F-07 | `group` degeri disaridaki (`mimari`/`kod`/`kanit`) | 0 | 0 | GECTI |
| F-08 | `node_type` gecerli (`module`/`subdomain`) olmayan | 0 | 0 | GECTI |
| F-09 | `status` gecerli (`draft`) olmayan | 0 | 0 | GECTI |
| F-10 | Kok `index.md` `parent_id` = `K000-isletim-sistemi` | Evet | Evet | GECTI |

### §3.1 `group` dagilimi (9. alan)

| `group` | Beklenen dosya | Dosya adi deseni |
|---|---|---|
| `mimari` | 8 | `index.md` ×4 · `-kapsam` ×1 · `-anayasa` ×3 · `-surucu` ×3 = 11 — sayim §3.2'de |
| `kod` | 4 | `-ornekler` ×4 (kok + 3 alt-dal) |
| `kanit` | 1 | `-dogrulama` ×1 |

### §3.2 Dosya turu bazinda sayim (2026-10-13)

| Dosya turu | Adet | group | Satir esigi |
|---|---|---|---|
| Kok `index.md` | 1 | `mimari` | 40–100 |
| Alt-dal `index.md` | 3 | `mimari` | 40–100 |
| `-kapsam` | 1 | `mimari` | >=500 |
| `-anayasa` | 3 | `mimari` | >=500 |
| `-surucu` | 3 | `mimari` | >=500 |
| `-ornekler` | 4 | `kod` | >=500 |
| `-dogrulama` | 1 | `kanit` | >=500 |
| **Toplam** | **16** | — | — |

## §4 — Icerik yasaklari dogrulamasi

| # | Yasak | Kontrol yontemi | Sonuc |
|---|---|---|---|
| Y-01 | Plan tutanagi dolgusu (uc harfli anahtar) | 16 dosyada literal arama | 0 |
| Y-02 | Ertelenmis-is etiketi (Turkce "gelecek zaman" dolgusu) | 16 dosyada literal arama | 0 |
| Y-03 | Iki egik cizgi + uc nokta (kod dolgusu) | 16 dosyada literal arama | 0 |
| Y-04 | Suspendlu parantez icinde uc nokta (kod dolgusu) | 16 dosyada literal arama | 0 |
| Y-05 | Bos tablo hucresi (iki pipe arasi bosluk) | 16 dosyada desen arama | 0 |
| Y-06 | Mojibake (bozuk UTF-8 baytlari — isaret gorseli bu satirda bilincli olarak tutulmaz) | 16 dosyada bayt deseni | 0 |
| Y-07 | Uydurma CVE numarasi | elle gozden gecirme | 0 — hicbir CVE iddia edilmedi |
| Y-08 | Uydurma dosya/satir numarasi | `Select-Object -Index` ile geri dogrulama | 0 |
| Y-09 | `parent:` alani | literal arama | 0 |
| Y-10 | Tirnakli `group:` | desen arama (`group: "`) | 0 |
| Y-11 | Beyazliste disina yazilan dosya | `git status` diff listesi | 0 |

### §4.1 Imla hatalarinin korunmasi (bilincli — duzeltilmez)

| Imla hatasi | Kullanildigi yer | Durum |
|---|---|---|
| `electroncis` | kardes dal adi | KORUNDU |
| `donanaım-type` | `K001-donanaım-type` | KORUNDU |
| `identitiy` | `K010-identitiy` | KORUNDU |
| `integraion` | `K013-integraion` | KORUNDU |
| `comilance` | vault adi | KORUNDU (kullanildiysa) |
| `comere` | vault adi | KORUNDU (kullanildiysa) |
| `recomenataon` | vault adi | KORUNDU (kullanildiysa) |

## §5 — Kapsam disi girisim denetimi (komşu dallar)

| # | Yasak yuzey | Denetlenen yol | Sonuc |
|---|---|---|---|
| Z-01 | `K009-security/**` | `git status` + yazilan dosya listesi | GIRILMEDI |
| Z-02 | `K010-identitiy/**` | ayni | GIRILMEDI |
| Z-03 | `K001..K019/**` (digerleri) | ayni | GIRILMEDI |
| Z-04 | `isletim-sistemi-{core,cross,kernel,observability,plan,runtime,testing}/**` | ayni | GIRILMEDI |
| Z-05 | `isletim-sistemi-{api,audio,turu}/**` | ayni | GIRILMEDI |
| Z-06 | `electroncis/**` | ayni | GIRILMEDI |
| Z-07 | `.ai/electronic/**` | ayni | GIRILMEDI |
| Z-08 | `.ai/architecture/index.md` | ayni | GIRILMEDI (yalniz orkestratör) |
| Z-09 | `*.php` `*.js` `*.css` `*.sql` | `git status` | YAZILMADI (yalniz okundu) |
| Z-10 | Beyazliste disi tum `.md` | `git status` | 0 |

### §5.1 Okunan ama YAZILMAYAN kaynak dosyalar (kanit amaciyla)

| Dosya | Amaç | Yazildi mi? |
|---|---|---|
| `shared/src/Session/SessionConfig.php` | DAC izni kaniti (D-01..D-04) | HAYIR |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php` | traversal kaniti (D-05..D-10) | HAYIR |
| `home.coremusic.net/.htaccess` | Apache baslik kaniti (D-11..D-15) | HAYIR |
| `api.coremusic.net/.htaccess` | API katman kaniti (D-42..D-43) | HAYIR |
| `shared/src/AI/AIEngine.php` | shell kacis kaniti (D-16..D-17) | HAYIR |
| `shared/src/Middleware/*.php` (11 dosya) | middleware kanitlari (D-18..D-41) | HAYIR |
| `shared/src/Security/SecurityHelper.php` | production kilidi (D-32) | HAYIR |
| `shared/src/Session/SessionBootstrapper.php` | kimlik yenileme (D-39) | HAYIR |
| `shared/src/Session/SessionLifecycle.php` | kimlik yenileme (D-40) | HAYIR |

## §6 — Acik sorularin durumu (bu dogrulama kapatir mi?)

| # | Soru | Onceki durum | Bu revizyon | Kapatildi mi? |
|---|---|---|---|---|
| S1 | OS guvenlik katmani devreye alinacak mi? | [UNKNOWN] | [UNKNOWN] — kanitlar YALNIZ mevcut durumu tasir | HAYIR |
| S2 | Uygulama <-> OS sorumluluk siniri? | [UNKNOWN] | [UNKNOWN] — §2.1 kanitlari sinir taslagini besler | HAYIR |

> **Durluk beyani:** Bu dosyada "repo'da OS araci yoktur" kanitlanmistir; "OS araci eklenmelidir" **kanitlanmamistir** — bu bir mimari karardir (S1) ve `>=088` numarali yeni bir ADR gerektirir.

## §7 — Olcum ozeti (2026-10-13 · 3. tur 2026-10-14: §15)

| Metrik | Deger |
|---|---|
| Katalog envanteri oncesi benzersiz URL | 2097 |
| Bu subdomainde kullanilan benzersiz URL | 68 |
| Kesisim (envanter ∩ bu subdomain) | **0** |
| HTTP 200 dogrulanan URL | 68 / 68 |
| Reddedilen (404/hata) aday URL | 8 |
| Disk kaniti (olumlu) | 43 |
| Disk kaniti (negatif) | 9 arama kalibi, 0 isabet |
| Uydurma satir numarasi | 0 |
| Yazilan dosya (beyazliste) | 16 |
| Beyazliste disi yazilan | 0 |
| Mojibake | 0 |
| Dolgu (plan tutanagi / ertelenmis-is / iki egik cizgi / suspendlu parantez) | 0 |
| `parent:` | 0 |
| Tirnakli `group:` | 0 |
| Eksik frontmatter | 0 |

## §8 — Sonuc

| # | Sonuc | Deger |
|---|---|---|
| 1 | Web kanitlari | 68/68 HTTP 200 |
| 2 | Disk kanitlari | 43/43 `Select-Object -Index` ile birebir |
| 3 | Negatif kanit | 0 isabet (OS araci yok — mevcut durum) |
| 4 | Uygulama katmani pratiigi | KANITLANDI |
| 5 | OS katmani kullanimi | KANITLANMIS YOKTUR |
| 6 | S1 / S2 | [UNKNOWN] — **kapatilmadi** |
| 7 | Frontmatter 9 alan | 16/16 tam |
| 8 | Icerik esigi (>=500) | 12/12 |
| 9 | Index esigi (40–100) | 4/4 |
| 10 | `git commit` | ATILMADI (subagent yasagi — orkestratöre ait) |

## §9 — Dosya bazlı iddia ↔ kanıt eşlemesi (16 dosya)

| # | Dosya | Taşıdığı iddia sınıfı | Dayandığı kanıt | Kanıt türü |
|---|---|---|---|---|
| E-01 | `isletim-sistemi-security/index.md` | Klasör künyesi, 3 çocuk, gruplar | §3.1 · §3.2 yapısal sayılar | yapısal |
| E-02 | `isletim-sistemi-security-kapsam.md` | Kapsam sınırı, ayrım matrisi | K-D1..K-D12 · §6.1 negatif | disk + negatif |
| E-03 | `isletim-sistemi-security-dogrulama.md` | Tüm iddiaların kanıtı (bu dosya) | D-01..D-43 · U01..U68 | disk + web |
| E-04 | `isletim-sistemi-security-ornekler.md` | Gerçek repo kod örnekleri | D-16..D-23 · D-30..D-32 | disk |
| E-05 | `cekirdek-guvenlik/index.md` | Alt-dal künyesi, 3 çocuk | §3.2 sayım | yapısal |
| E-06 | `cekirdek-guvenlik-anayasa.md` | Koruma halkası, önyükleme, mitigasyon | U01, U15, U04, U56 | web (hedef referans) |
| E-07 | `cekirdek-guvenlik-surucu.md` | Sürücü imzalama, ioctl yüzeyi | U38, U14, U27 | web (hedef referans) |
| E-08 | `cekirdek-guvenlik-ornekler.md` | Mitigasyon konfigürasyon örnekleri | §2.2 negatif + U50 | negatif + web |
| E-09 | `yetki-ve-erisim-modeli/index.md` | Alt-dal künyesi, 3 çocuk | §3.2 sayım | yapısal |
| E-10 | `yetki-ve-erisim-modeli-anayasa.md` | DAC/MAC/RBAC/BEL karşılaştırma | U08, U33, U36, U16 | web |
| E-11 | `yetki-ve-erisim-modeli-surucu.md` | Denetim akışı, umask, audit | D-01..D-04 · U47 | disk + web |
| E-12 | `yetki-ve-erisim-modeli-ornekler.md` | RBAC/DAC repo örnekleri | D-36, D-37, D-38 | disk |
| E-13 | `uygulama-guvenlik-siniri/index.md` | Alt-dal künyesi, 3 çocuk | §3.2 sayım | yapısal |
| E-14 | `uygulama-guvenlik-siniri-anayasa.md` | Syscall yüzeyi, sandbox türleri | U11, U28, U10, U12 | web (hedef referans) |
| E-15 | `uygulama-guvenlik-siniri-surucu.md` | Politika sürme, başarısızlık modları | D-26, D-27, D-30, D-31 | disk |
| E-16 | `uygulama-guvenlik-siniri-ornekler.md` | Auth/CSRF/.htaccess gerçek kodu | D-11..D-23 · D-42..D-43 | disk |

> **Eşleme kuralı:** Bir dosyadaki her somut iddia bu tablodaki kanıt satırına indirgenebilir;
> indirgenemeyen iddia `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

## §10 — Yeniden üretilebilir ölçüm komutları (PowerShell)

> Bu komutlar 2026-10-13 tarihinde `C:\www\coremusic.net` kökünden çalıştırılmıştır;
> çıktılar §1.2, §2.1 ve §7 tablolarıyla birebir aynıdır.

```powershell
# K1 — Katalog envanteri (benzersiz URL sayimi)
$files = Get-ChildItem -Path '.ai\architecture' -Recurse -Filter '*.md'
$urls  = foreach ($f in $files) {
  [regex]::Matches((Get-Content $f.FullName -Raw), 'https?://[^\s\)\]\|`]+')
  | ForEach-Object { $_.Value.TrimEnd('.,', ')') }
}
($urls | Sort-Object -Unique).Count        # beklenen: 2097
```

```powershell
# K2 — Aday URL HTTP 200 dogrulamasi (HEAD)
Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20 -Uri $url
# beklenen: StatusCode = 200
```

```powershell
# K3 — Envanter kesisimi (yeni URL envanterde olmamali)
$inv = New-Object 'System.Collections.Generic.HashSet[string]'
# K1 ciktisindaki 2097 URL eklenir
($new | Where-Object { $inv.Contains($_) }).Count   # beklenen: 0
```

```powershell
# K4 — Disk kaniti: satir birebir dogrulama (N satir -> index N-1)
Get-Content 'shared\src\Session\SessionConfig.php' | Select-Object -Index 19
# beklenen: public const DIR_PERMISSIONS = 0750;
Get-Content 'shared\src\Middleware\CsrfMiddleware.php' | Select-Object -Index 41
# beklenen: ... !hash_equals($sessionToken, (string)$submitted)) {
```

```powershell
# K5 — Negatif kanit (OS araci yoklugu)
$pat = 'seccomp|cap_set_proc|capget|prctl|landlock|apparmor|selinux|setuid|chroot'
# kapsam: shared\src, api.coremusic.net, home.coremusic.net (vendor disi)
# beklenen: 0 isabet
```

```powershell
# K6 — Satir sayimi (esik denetimi)
(Get-Content $dosya).Count
# icerik dosyasi icin beklenen: >= 500
# index.md icin beklenen: 40..100
```

```powershell
# K7 — Frontmatter denetimleri
Select-String -Path $dosya -Pattern '^parent:'      # beklenen: 0
Select-String -Path $dosya -Pattern '^group: "'     # beklenen: 0
Select-String -Path $dosya -Pattern $yasakDolguKalip
# yasak dolgu kalip degiskeni (4 adet; icerigi bu dosyada literal olarak YAZILMAZ)
# beklenen isabet: 0
```

```powershell
# K8 — Beyazliste denetimi (yazilan dosyalar)
git status --porcelain -- '.ai/architecture/K000-isletim-sistemi/isletim-sistemi-security'
# beklenen: yalniz bu klasor altindaki 16 dosya
```

## §11 — İkinci tur tekrar ölçüm kaydı (2026-10-13)

| # | Ölçüm | 1. tur | 2. tur | Fark | Karar |
|---|---|---|---|---|---|
| T-01 | Katalog envanteri benzersiz URL | 2097 | 2097 | 0 | sabit |
| T-02 | Bu subdomain benzersiz URL | 68 | 68 | 0 | sabit |
| T-03 | Envanter kesisimi | 0 | 0 | 0 | GECTI |
| T-04 | HTTP 200 doğrulanan URL | 68/68 | 68/68 | 0 | GECTI |
| T-05 | Reddedilen aday (404/hata) | 8 | 8 | 0 | GECTI |
| T-06 | Disk kanıtı (olumlu) | 43 | 43 | 0 | GECTI |
| T-07 | Negatif kanıt (OS aracı) | 0 isabet | 0 isabet | 0 | GECTI |
| T-08 | Yazılan dosya | 4 → 16 | 16 | +12 | hedefe ulasti |
| T-09 | Mojibake | 0 | 0 | 0 | GECTI |
| T-10 | `parent:` alanı | 0 | 0 | 0 | GECTI |
| T-11 | Tirnaklı `group:` | 0 | 0 | 0 | GECTI |
| T-12 | Dolgu (plan tutanagi / ertelenmis-is / iki egik cizgi / suspendlu parantez) | 0 | 0 | 0 | GECTI |

> **Not (frontmatter düzeltmesi):** Bu dosyanın `validation` alanı ilk taslakta "25 disk kaniti"
> yazıyordu; §2.1'deki gerçek sayı 43'tür. Alan **43 olarak düzeltildi** (2026-10-13) —
> iç tutarsızlık kapatıldı, kanıt sayısı büyütülmedi.

## §12 — Doğrulama zinciri (iddia sınıfı → bölüm)

| İddia sınıfı | Kanıt biçimi | Bu dosyadaki bölüm | Kabul kriteri |
|---|---|---|---|
| Web iddiası (standart, proje, doküman) | URL + HTTP 200 + envanter yokluğu | §1.1 · §1.2 | 2026-10-13 HEAD = 200 · kesisim 0 |
| Reddedilen web adayı | URL + hata kodu | §1.3 | hiçbir dosyaya yazılmadı |
| Repo kod iddiası | `Dosya:` + `Satır` + `Select-Object -Index` | §2.1 | satır içeriği birebir |
| Yokluk iddiası (negatif) | arama kalıbı + kapsam + 0 isabet | §2.2 | tekrar tarama = 0 |
| Yapısal iddia (frontmatter) | alan adı + beklenen/bulunan | §3 | GECTI satırı |
| Yasak içerik iddiası | literal/desen arama | §4 | Sonuc = 0 |
| Kapsam sınırı iddiası | yazılan dosya listesi | §5 | girilmedi |
| Açık soru durumu | `[UNKNOWN]` işareti | §6 | kapatılmadı |
| Sayısal özet | tekil sayılar | §7 · §8 | §1–§6 ile tutarlı |

## §13 — Güvenilirlik kısıtları (bu doğrulamanın yapamadığı şeyler)

| # | Kısıt | Neden | Etkilendiği iddia | Davranış |
|---|---|---|---|---|
| L-01 | Runtime(OS) davranış doğrulaması yapılamaz | Repo'da OS aracı yok; çalıştırılacak bir yapılandırma bulunmuyor | S1 | `[UNKNOWN]` bırakıldı |
| L-02 | Üretim ortamı yapılandırması okunamadı | `.env` gizli; dışarıya sızdırılmaz | S2 | tahmin edilmedi |
| L-03 | Windows (PowerShell) ortamı | Hedef üretim platformu bilinmiyor | OS mekanizması eşdeğerliği | eşdeğerlik iddia edilmedi |
| L-04 | `vendor/` dizini tarama dışı | Bağımlılık kodu bu dalın pratiği değil | negatif kanıt kapsamı | kapsam açıkça yazıldı |
| L-05 | İçerik yalnızca statik okuma | Kod çalıştırılmadı, test edilmedi | davranışsal iddialar | yalnız yapısal iddia |
| L-06 | Bel (`BEL`) tanımı vault'ta yok | Kapsayan tanıma ulaşılamadı | `yetki-ve-erisim-modeli-anayasa` §1 | `UNKNOWN` yazıldı, uydurulmadı |
| L-07 | CVE kimliği doğrulanmadı | Hiçbir CVE gerekmedi | — | CVE iddia edilmedi |
| L-08 | URL içeriği değil yalnız erişilebilirlik doğrulandı | HEAD yalnız durum kodu verir | §1.2 iddiaları | iddialar "erişilebilir" düzeyde |

> **Bu kısitlar §1–§8'deki sonuçları geçersiz kılmaz; yalnız sonuçların *ne kadarını* kapsadığını tanımlar.**

## §14 — Kanıt numaralandirma tutarliligi (D / U / N / F serileri)

| Seri | Anlam | Aralik | Bu dosyadaki yer | Toplam |
|---|---|---|---|---|
| `W-01..W-07` | Birincil web kanitlari | 7 | §1.1 | 7 |
| `U01..U68` | Tam URL defteri (hepsi HTTP 200) | 68 | §1.2 | 68 |
| `R01..R08` | Reddedilen adaylar (HTTP 200 degil) | 8 | §1.3 | 8 |
| `D-01..D-43` | Disk kanitlari (Select-Object -Index) | 43 | §2.1 | 43 |
| `N-01..N-09` | Negatif kanit satirlari | 9 | §2.2 | 9 |
| `F-01..F-10` | Yapinsal (frontmatter) kontroller | 10 | §3 | 10 |
| `Y-01..Y-11` | Icerik yasagi kontrolleri | 11 | §4 | 11 |
| `Z-01..Z-10` | Kapsam disi girisim denetimi | 10 | §5 | 10 |
| `T-01..T-12` | Tekrar olcum (2. tur) | 12 | §11 | 12 |
| `L-01..L-08` | Guvenilirlik kisitlari | 8 | §13 | 8 |
| `E-01..E-16` | Dosya bazli iddia-esleme | 16 | §9 | 16 |

> **Numaralandirma kurali:** Her seri kendi § icinde tanimlanir; seri numaralari disarida
> yeniden kullanilmaz (carsamba carpani yok). Bir iddia iki seride gorunurse — ornek: bir
> URL hem `W-` hem `U-` serisinde — bu kasitlidir (`W-` birincil kaniti, `U-` defteri tasir;
> sayim `U-` serisinden yapilir, boylece 68 sayimi iki katina cikmaz).

## §15 — Üçüncü tur ölçüm kaydı (2026-10-14 · 24 OS kartı genişlemesi)

| # | Ölçüm | Değer | Karar |
|---|---|---|---|
| V-01 | Kapsam dosya sayısı | 16 → **40** (16 temel + 24 OS kartı, 8 platform × 3 alt-dal) | hedefe ulaştı |
| V-02 | Ana URL doğrulama | 87/87 benzersiz URL HTTP 200 (2026-10-14) · ham çıktı `C:\temp\opencode\sec-urls-2026-10-14.txt` | GECTİ |
| V-03 | Kart subagentı ek doğrulamaları (tur raporu beyanı) | Windows 17/17 · Linux 22 okundu + 2 yazılmadı · webOS 14/14 · iOS 8+12+10 · Android 4 ek · macOS 21 doğrulandı + 12 reddedildi (10×404, 2×konu uyuşmazlığı) · ReactOS 7 doğrulandı + 2 reddedildi (404) · Tizen 6/6 | reddedilenler yazılmadı |
| V-04 | Disk kanıtı (olumlu) | 43 (1.–2. tur) + 3. tur ek taramalar = 46 | 0 uydurma satır |
| V-05 | Negatif kanıt (OS aracı) | macOS 729 dosya → 0 isabet · ReactOS 721 dosya → 0 isabet · Tizen 418 PHP+JS → 3 isabet (üçü de UA algılama: `breakpoints.js:31`, `breakpoints.spec.js:54`, `DeviceDetector.php:33`) | GECTİ |
| V-06 | Ölçüm standardı düzeltmesi | eski "215 dosya" ölçümü kapsamı yanlış (CSS/SQL/vendor dahil) → **canonical 418 PHP+JS** (320 PHP + 98 JS, vendor/node_modules hariç) | 215 geçersiz, 418 geçerli |
| V-07 | Yapısal denetim (40/40) | kart 150–260: 24/24 (154–258 arası) · içerik >=500: 12/12 · index 40–100: 4/4 · `parent:` 0 · `group: kod` 24/24 · Authority 24/24 · S1/S2 açık 40/40 | GECTİ |
| V-08 | `git commit` | ATILMADI — 24 yeni kart diskte untracked (subagent yasağı, orkestratöre ait) | yasak korundu |

> **Bilerek boş bırakılanlar (korunacak):** iOS 2 kartında 2 boş URL + 2 `⚠️`;
> Linux kartında lockdown/posix_acl yazılmadı (`⚠️`); webOS kartında secure boot/LSM/
> seccomp eşleşmedi (`⚠️`); macOS kartında `sandbox-exec` kalıbı ve denetim-sırası
> iddiası (`⚠️`); ReactOS kartında uygulama uyumlulama yüzdesi `UNKNOWN`. Ayrıca
> `docs.tizen.org` 2 URL 200 dönüyor ama 302 ile aynı açılış sayfasına düşüyor →
> "taşıma kaydı, kanıt değil" olarak işaretlendi. Doğrulanamayan hiçbir iddia yazılmadı.

> **Eşik yorumu:** içerik dosyaları için 500 **alt** sınırdır (499 başarısız) —
> §2.4 kuralı gereği tüm içerik dosyaları 500+ satırdır; 150–260 bandı yalnız
> yeni OS kartlarına uygulanır.

## §16 — Dördüncü tur ölçüm kaydı (2026-10-14 · `kod-ornekleri/` 15 kart genişlemesi)

| # | Ölçüm | Değer | Karar |
|---|---|---|---|
| V-09 | Yazılan dosya | 40 → **55** (+15 `kod-ornekleri/` kartı: 6 mevcut + 9 yeni — 7 OS kartı `windows/macos/ios/android/tizen/webos/reactos` + 2 dil kartı `c/go`) | hedefe ulaştı |
| V-10 | URL doğrulama (4. tur) | 21 upstream URL GET 200 + **içerik okundu** (2026-10-14): go.dev `install/source` + `doc/security` · pkg.go.dev `x/sys/{windows,unix}` v0.49.0 · nodejs.org `os.html` · php.net `function.chmod` + `book.posix` · docs.python.org `os` + `using/ios` + `using/android` · learn.microsoft.com `job-objects` + `filesystemaccessrule` + `what-is-maui` · docs.tizen.org `application/native` (landing) · webosose.org `docs/` (landing) · reactos.org · docs.kernel.org `seccomp_filter` + `no_new_privs` · pubs.opengroup `chmod` + `setuid` · support.apple.com `sandbox` (TOC) · doc.rust-lang.org `platform-support` + `PermissionsExt` + `OpenOptionsExt` · docs.rs `nix` + `ndk` | GECTİ |
| V-11 | Reddedilen aday genişlemesi | R09..R16 (8 yeni, §16.1) — hiçi hiçbir karta gövde olarak girmedi | GECTİ |
| V-12 | Repo ölçümü (tekrar) | 418 PHP+JS (320+98) · `*.c,*.h,*.cpp,*.go,*.rs,*.cs` **0** · 9 yeni kartın desenleri tek tek ölçüldü: windows `CreateJobObject\|FileSystemAccessRule\|share_mode` 0 · macos `SetUnixFileMode\|posix_geteuid\|sandbox-exec\|seatbelt` 0 · ios `sys\.platform\|entitlement\|PermissionsExt` 0 · android `getuid\|geteuid\|native_activity` 0 · tizen `setuid\|prctl\|seccomp\|capset` 0 · webos `LunaService` 0 · reactos `CreateJobObject\|AdjustTokenPrivileges\|DuplicateTokenEx\|CryptProtectData` 0 · c `seccomp\|sock_fprog\|PR_SET_SECCOMP` 0 · go `golang.org/x/sys\|go:build` 0 | GECTİ (0 uydurma iddia) |
| V-13 | Yapısal denetim (9/9) | satır 150–260: **9/9** (191–251) · Mojibake 0 (UTF-8 okuma) · `parent_id` 9/9 · `group: kod` 9/9 · Authority 9/9 · S1/S2 `[UNKNOWN]` 9/9 · "repo'da uygulanıyor" iddiası 0 | GECTİ |
| V-14 | `index.md` güncellemesi | `children` + alt-dal satırı + GRUPLAR satırı + Toplam 56 dosya; 100/100 sınırında (40–100 bandı korundu) | GECTİ |
| V-15 | `git commit` | ATILMADI — 9 yeni kart diskte untracked (orkestratöre ait) | yasak korundu |

### §16.1 — Reddedilen adaylar (R serisi genişlemesi — 4. tur)

| # | Aday | Sonuç | Kullanıldığı kart |
|---|---|---|---|
| R09 | `https://pkg.go.dev/doc/install/source` | **404** (doğru slug `go.dev/doc/install/source`) | go |
| R10 | `https://learn.microsoft.com/en-us/windows/win32/secauthz/protecting-objects` | **404** | windows |
| R11 | `https://developer.android.com/ndk/guides/bionic` | **404** | android · c |
| R12 | `https://source.android.com/docs/security/bionic` | **404** | android · c |
| R13 | `https://developer.apple.com/.../preventing-insecure-exception-behavior` | **404** | macos |
| R14 | `https://docs.python.org/3/using/macos.html` | **404** | macos |
| R15 | `https://wiki.reactos.org/...` | **bağlantı hatası** (yanıt alınamadı) | reactos |
| R16 | `https://support.apple.com/guide/security/sandbox` | HTTP 200 ama **redirect → TOC**; sandbox detay metni gelmedi → "gövde kanıt değil" | macos · ios |

### §16.2 — Bilerek boş bırakılan kombinasyonlar (korunacak)

Örnek yazan kombinasyon **29** · `⚠️ VERIFICATION REQUIRED` bırakılan kombinasyon **36**
(toplam 65 = 7 OS × 7 dil + 2 dil × 8 OS). Dağılım: tizen 7/7 ⚠️ · webos 7/7 ⚠️ ·
reactos 7/7 ⚠️ · c kartı 6 ⚠️ (yalnız windows/linux ✅) · ios 3 ⚠️ (C/JS/PHP) · android 2 ⚠️
(C/PHP) · macos 1 ⚠️ (C) · go kartı 3 ⚠️ (tizen/webos/reactos — GOOS listesi negatif kanıt).
Ek alt-yüzey ⚠️'leri: macos sandbox/seatbelt (R16), ios entitlement, macos/iOS Go
sandbox/entitlement. Doğrulanamayan hiçbir iddia yazılmadı; POSIX spec sayfaları tek başına
OS C yüzeyi teyidi sayılmadı (L-03 kısıtı, c kartı §2.3).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
