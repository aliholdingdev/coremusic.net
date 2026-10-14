---
node_id: K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari/izin-ve-guvenlik-kavramlari-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-core/izin-ve-guvenlik-kavramlari
evidence:
  - https://man7.org/linux/man-pages/man8/setcap.8.html
  - https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html
  - https://man7.org/linux/man-pages/man1/ls.1.html
  - https://httpd.apache.org/docs/2.4/howto/htaccess.html
  - https://man7.org/linux/man-pages/man2/stat.2.html
  - https://learn.microsoft.com/en-us/iis/configuration/system.webServer/
  - shared/src/Middleware/PermissionMiddleware.php:17-63
  - shared/src/Session/SessionConfig.php:20-62
  - shared/src/Component/TemplateEngine.php:37-89
  - shared/src/Log/FileHandler.php:169-184
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:173-209
  - home.coremusic.net/.htaccess:2-36
  - home.coremusic.net/web.config:11-39
validation: "2026-10-14 → §19 grupları G7a-G7d (12 platform kolu) · §19.5 14 YENİ HTTP 200 (envanter kesişimi 0) · §19.6 12 reuse yeniden 200 · acl_*.html (OG Issue 8) HTTP 404 ölçüldü → ACL örnekleri man7'ye bağlandı (dogrulama §J.6 D7) · §20 PHP köprüsü (5 gerçek-satır bloğu + §20.3 zincir özeti · yeni URL 0 · is_writable/umask(/chmod(/fsync grep 0 → §20.5) · §21 çok dilli JS/C/Python/Go/Rust/C# (6 URL = 2 reuse + 4 yeni · 5 kalıp blok · JS kalıpsız = tarayıcıda OS izni yok) · 963 satır · repo satırları Read/PowerShell ile birebir · sayımlar read-only · IIS rewrite dürüstlük notu (§11) korunur"
open_questions:
  - "Q-OR1: api.coremusic.net/.htaccess:19 FilesMatch listesinde `json` YOK (auth:18 ve home:18'de VAR) — kasıtlı fark mı? açık"
  - "Q-OR2: assets.coremusic.net katmanında `Require all denied` 0 isabet — assets yalnız statik mi servis ediyor? (doküman: UNKNOWN)"
  - "Q-OR3: 4 web.config'te PHP doğrudan erişim engeli YOK (FilesMatch/location = 0) — IIS kurulumu bunu başka katmanda karşılıyor mu? UNKNOWN"
  - "Q-OR4: repo kökündeki `--` dizini içinde `bin/api-key-create.php` kopyası var (grep isabeti) — nedir, silinmeli mi? açık"
group: kod
---

# izin-ve-guvenlik-kavramlari-ornekler — Repo Örnekleri ve Anti-Pattern (Kademe-5)

## §0 ELI15

"Bu projede dosya izinleri gerçekte nasıl uygulanıyor?" sorusunun cevabı: repoda
**POSIX izin primitive'leri kodlanmaz** (grep: chmod/chown/setuid/getfacl/setcap = 0,
`-surucu` §5); onun yerine izin kararı **üç katmanda** dağıtılır: (1) uygulama
katmanı rol/izin denetimi (`PermissionMiddleware`, 403 + `authz_denied` log'u),
(2) uygulama katmanı yol kilidi (`MusicStreamHandler::resolvePath`, 8 kontrol
→ 403/404), (3) sunucu katmanı `.htaccess`/`web.config` reddi. Bu dosya bu üç
katmanı **gerçek satır numaralarıyla** örnekler, Apache↔IIS asimetrilerini
matrisler ve tarihsel iddiaların disk ile çeliştiği iki yeri (AP-P1, AP-P2)
kayıt altına alır. Her sayı 2026-10-13 ölçümüdür; uydurma satır/istatistik yoktur.

## §1 Erişim karar zinciri örneği — `MusicStreamHandler::resolvePath` (8 kontrol)

### §1.1 Adım tablosu — `home.coremusic.net/include/Stream/MusicStreamHandler.php:173-209`

| # | Satır | Kontrol | Başarısızlık | HTTP |
|---|---|---|---|---|
| 1 | 175-178 | `trim($stored)`; boş veya `\0` içeriyor | `fail(404)` | 404 |
| 2 | 180-183 | `preg_split('#[/\\\\]+#')` → `'..'` segmenti var mı | `fail(403)` | 403 |
| 3 | 185-187 | `is_link($trimmed)` — symlink ise | `fail(403)` | 403 |
| 4 | 189-192 | `realpath($trimmed)` === false veya `!is_file($real)` | `fail(404)` | 404 |
| 5 | 194-197 | `realpath($this->libraryPath)` === false (kök çözülemez) | `fail(404)` | 404 |
| 6 | 199-202 | `str_starts_with($real, $prefix)` — kök dışında | `fail(403)` | 403 |
| 7 | 204-206 | `is_readable($real)` — okuma izni yok | `fail(403)` | 403 |
| 8 | 208 | `return $real` — karar zinciri bitti, okuma başlar | — | — |

### §1.2 Karar matrisi — hangi adım hangi HTTP kodunu üretir

| Kod | Üreten adımlar | Anlamı |
|---|---|---|
| 404 | 1, 4, 5 | dosya yok / çözülemedi — **varlık sızdırmayan** yanıt |
| 403 | 2, 3, 6, 7 | dosya var ama politika/red — erişim reddedildi |

`stat(2)` bu ayrımı OS düzeyinde de doğrular: `stat()`/`lstat()` için dosyanın
kendisinde izin gerekmez, yol üzerindeki **her dizinde execute (search) izni**
gerekir; eksikse `EACCES` (`stat.2` ERRORS, 2026-10-13 HTTP 200 — §11 no 5).
`lstat()` symlink'i **izlemez** (DESCRIPTION) — repodaki adım 3 (`is_link`) ile
aynı semantik: izleme-red, dosya-erişim-red'den önce gelir.

### §1.3 Neden bu örneği önemli

Bu tek metot, dosya-sistemi dalının `path_resolution(7)` zincirinin uygulama
karşılığıdır: NUL/boşluk → segment ayrıştırma → link → kanonikleşim → kök
kontrolü → okunabilirlik. Sıra önemlidir — `realpath` (adım 4) `is_link`
(adım 3)'den **sonra** gelir; symlink bir kez çözüldükten sonra `is_link`
artık gerçeği söylemez. `O_NOFOLLOW` repoda **yok** (grep = 0, `-surucu` §5),
yani symlink-yarışı (TOCTOU) yüzeyi bu adımda açık kalır (Q-I5).

## §2 Uygulama katmanı örnekleri (repo, satır kanıtlı)

### §2.1 `PermissionMiddleware` — gerçek akış (dosya 64 satır)

`shared/src/Middleware/PermissionMiddleware.php`:

| Satır | Ne olur |
|---|---|
| 19-20 | `$routeMeta = $request['_route_meta'] ?? []` · `$auth = $request['_auth'] ?? []` |
| 23-25 | `empty($auth['userId'])` → middleware **ilgilenmez**, `$next($request)` |
| 27 | `$userRole = $auth['role'] ?? 'user'` — varsayılan en düşük rol |
| 30-32 | `requiredRole` var ve uymuyorsa → `forbidden('role_insufficient', ...)` |
| 36-41 | `requiredPermission` izin listesinde (`in_array(..., true)`) yoksa → `forbidden('permission_denied', ...)` |
| 47-63 | `forbidden()`: L50-54 `securityEvent('authz_denied', [reason, uri, ip])` → L56-62 `httpStatus: 403`, `halt: true` |

Dikkat: bu dosyada **dosya sistemi denetimi yoktur** — `is_dir(`/`is_readable(`
grep'i bu dosyada 0 isabet (§6.1 ve AP-P1). RBAC = rol/izin karşılaştırması;
POSIX izni ≠ uygulama izni (anayasa §2 farkı).

### §2.2 `SessionConfig` — 0750 dizin + oturum çerezi yüzeyi

`shared/src/Session/SessionConfig.php`:

| Satır | Ne olur |
|---|---|
| 20 | `DIR_PERMISSIONS = 0750` sabiti (tek kaynak) |
| 33-36 | `session.save_path` boşsa `sys_get_temp_dir().'/coremusic_sessions'`; `!is_dir` ise `@mkdir($savePath, 0750, true)` |
| 38-41 | HTTPS kararı: `$_SERVER['HTTPS']` veya `HTTP_X_FORWARDED_PROTO === 'https'` |
| 52-62 | `cookieParams()`: `lifetime: 0` (oturum çerezi), `domain: .coremusic.net` (L45), `secure` (L58), `httponly: true` (L59), `samesite: 'Lax'` (L60) |

Repoda `mkdir` mod dağılımı ölçümü (reuse, `-surucu` §5): 0775×5 · 0750×2 ·
0777×2 · 0700×1 — yani en sık mod 0775, oturum dizini 0750 ile tutarlı tek
istisna; gerçek sonuç yine de süreç umask'ine bağlıdır (Q-I2, UNKNOWN).

### §2.3 `TemplateEngine` — `LOCK_EX` önbellek yazımı

`shared/src/Component/TemplateEngine.php`:

| Satır | Ne olur |
|---|---|
| 37-39 | `!file_exists($templatePath)` → `RuntimeException("Template not found: ...")` |
| 73 | `renderString`: cache dosyası `sys_get_temp_dir()` + `cmtpl_` + `random_bytes(8)` |
| 74-76 | `file_put_contents($cachePath, $template, LOCK_EX) === false` → `RuntimeException` |
| 87-89 | `finally`: `is_file($cachePath)` ise `@unlink($cachePath)` |

`LOCK_EX` tek yazıcıyı garanti eder; dosya-zaman-uzunluğu penceresi kapanır
(eksen: dosya-sistemi dalı kilit kasası). Silme hata yutmaz ama **sessizdir**
(`@unlink`) — kalan cache dosyası gözlemlenemez (bilinen ve kabul edilen dar
sınır).

### §2.4 Log hattı — `FileHandler::securityEvent`

`shared/src/Log/FileHandler.php:169-184`:

| Satır | Ne olur |
|---|---|
| 171 | `[SECURITY] {$event}` mesaj başlığı |
| 172 | yalnız şu alanlar mesaja yazılır: `ip, uri, origin, role, code, reason, path` |
| 178-181 | `match`: `authz_denied` → `warning` |
| 183 | `logAt($level, $message, $context, securityStream: true)` |

`PermissionMiddleware:50` → `FileHandler:169-184` hattı Q-I6'yı kapatır:
**erişim reddi log'u VAR** (anayasa'daki eski "log yok" cümlesi AP-P2 olarak
§7.2'de kayıtlıdır; mevcut satırlar silinmez).

## §3 Sunucu katmanı örneği A — Apache `.htaccess` (repo'da 4 dosya)

### §3.1 Dosya envanteri (2026-10-13 sayımı)

| Dosya | Satır | `Options -Indexes` | `ServerSignature Off` | `display_errors off` | FilesMatch hassas | FilesMatch PHP | rewrite `[F,L]` | SPA `index.php` |
|---|---|---|---|---|---|---|---|---|
| `api.coremusic.net/.htaccess` | 42 | L2 | L3 | L5 | L19-21 (`json` YOK) | L24-26 | L38 (`config\|include`) | L41 |
| `auth.coremusic.net/.htaccess` | 37 | L2 | L3 | L5 | L18-20 | L22-24 | L35 (`config\|include`) | L36 |
| `home.coremusic.net/.htaccess` | 37 | L2 | L3 | L5 | L18-20 | L22-24 (`debug.php` istisnası) | L35 (`config\|include\|pages`) | L36 |
| `assets.coremusic.net/.htaccess` | 47 | L47 | **YOK** | **YOK** | **YOK** | **YOK** | **YOK** | **YOK** |

Toplam `Require all denied` isabeti `.htaccess` içinde: **6** (api:20, api:25 ·
auth:19, auth:23 · home:19, home:23); assets: **0**. Repo geneli aynı dizge
(`Require all denied`, tüm dosya türleri): **41 isabet = 6 kod + 35 doküman**
(`.ai/**/*.md` — vault metinleri bu terimi örnek olarak taşıdığı için sayı
şişer; kod katmanı gerçeği 6'dır).

### §3.2 Dikkat çekici satırlar

- `home:22` — `^(?!index\.php$)(?!debug\.php$).+\.php$`: home'da `debug.php`
  doğrudan erişime **açık** tek PHP dosyasıdır; api:24 ve auth:22'de bu istisna
  yoktur.
- `home:18` vs `api:19` — hassas uzantı listesi `json` içeriyor (auth/home),
  api'de içermiyor (Q-OR1).
- `assets:42-43` — güvenlik header'ları yalnızca `X-Content-Type-Options` ve
  `X-Frame-Options: SAMEORIGIN` (diğer alt alanlarda `DENY` + Referrer-Policy
  + header unsets var, L10-15).
- `.htaccess` mekanizmasının kendisi (AllowOverride) resmi olarak
  `howto/htaccess.html` sayfasında belgelenir (§11 no 4).

## §4 Sunucu katmanı örneği B — IIS `web.config` (repo'da 4 dosya)

### §4.1 Dosya envanteri (2026-10-13 sayımı)

| Dosya | Satır | `directoryBrowse` | `Permissions-Policy` | `CustomResponse 403` | `<rewrite>` |
|---|---|---|---|---|---|
| `api.coremusic.net/web.config` | 41 | YOK | VAR | 2 | VAR |
| `auth.coremusic.net/web.config` | 43 | YOK | VAR | 2 | VAR |
| `home.coremusic.net/web.config` | 43 | YOK (L35 yok) | L11 | 2 (L22, L26) | L14 |
| `assets.coremusic.net/web.config` | 37 | **L35** `<directoryBrowse enabled="false" />` | **YOK** | **0** | **0** |

Genel negatifler (4 web.config): `FilesMatch`/`<location>` = **0** ·
`DS_Store` = **0** · `.php` yalnızca rewrite hedefi olarak geçer
(api:34,36 · auth:36,38 · home:36,38). IIS yapılandırma modelinin kendisi
`learn.microsoft.com/en-us/iis/configuration/system.webServer/` sayfasında
belgelenir (§11 no 6, envanter reuse + bugün 200).

### §4.2 `home` web.config kural listesi (L14-41)

| Kural | Satır | Davranış |
|---|---|---|
| Static files | 16-19 | `.(css\|js\|png\|...)` → `action None` |
| Block config/include/pages | 20-23 | `^(config\|include\|pages)` → `CustomResponse 403` |
| Block sensitive files | 24-27 | `.(env\|log\|json\|lock\|md\|sql\|sh\|bak\|git\|ini\|htpasswd)` → `CustomResponse 403` (L25) |
| SPA Router | 28-35 | `IsFile`/`IsDirectory` → `action None` |
| Rewrite to index.php | 36-39 | kalan her şey → `index.php` (appendQueryString) |

## §5 Asimetri matrisi — Apache ↔ IIS (kanıt satırlı)

| Konu | Apache kanıtı | IIS kanıtı | Sonuç |
|---|---|---|---|
| Dizin listeleme kapatma | `Options -Indexes` **4/4** (htaccess L2/L47) | `directoryBrowse` **1/4** (yalnız assets:35) | IIS tarafında 3 alt alan için **UNKNOWN** (varsayılan `true` mü, ayrı yapılandırma mı?) |
| PHP doğrudan erişim engeli | FilesMatch PHP **3/4** (assets hariç) | `FilesMatch`/`location` **0/4** | IIS'de karşılığı **YOK** (Q-OR3) |
| Hassas dosya engeli | `Require all denied` **6 isabet / 3 dosya** | `CustomResponse 403` **6 / 3 dosya** | iki sitede karşılıklı; assets'te **iki tarafta da yok** |
| `DS_Store` engeli | htaccess **VAR** (api:19 · auth:18 · home:18) | web.config **0** | IIS'de eksik |
| `Permissions-Policy` header | htaccess **0** | web.config **VAR** (api/auth/home) | Apache'de eksik |
| `ServerSignature Off` | **3/4** (assets hariç) | karşılığı yok (kavram dışı) | Apache özgü |
| `php_flag display_errors off` | **3/4** (assets hariç) | karşılığı yok | Apache özgü |
| `debug.php` istisnası | home:22 **VAR** | web.config'te karşılığı **YOK** | IIS'de istisna yok |
| `config\|include\|pages` yasağı | home:35 `[F,L]` | home:20-23 `403` | karşılıklı (status aynı) |

Bu matris `tasinabilirlik-siniri-ornekler.md:232-235` (cross dal) ile aynı
bulguları bağımsız olarak teyit eder; yeni içerik yalnız buradaki **ölçüm
sayıları ve satır numaralarıdır**.

## §6 İzin okuma/yazma envanteri (repo grep, 2026-10-13)

### §6.1 `is_dir(` / `is_readable(` / `is_writable(` — 17 isabet

| # | Dosya:satır | İfade |
|---|---|---|
| 1 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:204` | `!is_readable($real)` |
| 2 | `shared/src/Session/SessionConfig.php:34` | `!is_dir($savePath)` |
| 3 | `shared/src/Session/SessionInitializer.php:30` | `!is_dir($savePath)` |
| 4 | `home.coremusic.net/config/constants.php:76` | `!is_dir($sessionPath)` |
| 5 | `bin/api-key-create.php:73` | `!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)` |
| 6 | `shared/src/PageRouter/HtmlShellRenderer.php:124` | `is_dir($cssDir)` |
| 7 | `shared/src/Media/... ` — `media.coremusic.net/src/Media/CatalogWriter.php:107` | `!is_dir($klasor) && !@mkdir(..., 0775, true)` |
| 8 | `media.coremusic.net/bin/audit.php:262` | `is_dir($tam)` |
| 9 | `media.coremusic.net/bin/audit.php:395` | `!is_readable($tam)` |
| 10 | `media.coremusic.net/bin/ingest.php:81` | `!is_dir($sourceReal)` |
| 11 | `media.coremusic.net/bin/ingest.php:160` | `is_dir($tam)` |
| 12 | `media.coremusic.net/bin/ingest.php:263` | `!is_dir($raporDir) && !@mkdir(..., 0775, true)` |
| 13 | `media.coremusic.net/bin/ingest.php:321` | `!is_dir($hedefDizin) && !@mkdir(..., 0775, true)` |
| 14 | `media.coremusic.net/bin/scan.php:134` | `is_dir($tam)` |
| 15 | `media.coremusic.net/bin/scan.php:151` | `!is_readable($tam)` |
| 16 | `shared/tests/Unit/Log/LoggerRedactionTest.php:47` | `is_dir($this->logDir)` |
| 17 | `--\bin\api-key-create.php:73` | #5 ile aynı ifade — repo kökündeki `--` dizinindeki kopya (Q-OR4) |

Çıkarım: `is_readable` 3 yerde, `is_writable` **hiçbir yerde** (`is_writable(`
0 isabet); izin okuma en çok CLI/audit betiklerinde (`media.../bin`) ve tek
web akışında (`MusicStreamHandler:204`) görünür.

### §6.2 `mkdir` mod dağılımı (envanter reuse)

0775×5 · 0750×2 · 0777×2 · 0700×1 — kaynak: `-surucu` §5 ölçüm tablosu (grep,
2026-10-13). Bu dosya yeniden saymaz; dağılımın yorumu anayasa §4'tedir.

### §6.3 Negatif kanıtlar (grep = 0, `-surucu` §5'ten reuse)

`chmod|chown|umask|setuid|getfacl|setcap|posix_get*|fileowner|filegroup|getmyuid`
→ 0 · `O_NOFOLLOW` → 0 · `extension_loaded(` → 0. Yani dosya capability'si
(`setcap.8` — §11 no 1) ve ACL (`acl.5`) **model-only**dır; uygulama kanıtı
yoktur (Q-I3/Q-I4).

## §7 Anti-pattern'ler ve uyumsuzluk kayıtları (mevcut satırlar KORUNUR)

### §7.1 AP-P1 — `PermissionMiddleware:30-32` → `is_dir/is_readable` iddiası

`izin-anayasa.md:215` (§3.3 adımı 1) "is_dir && is_readable (PermissionMiddleware:30-32)"
der. Disk gerçeği (2026-10-13, tam dosya okundu, 64 satır): **L30-32 rol
kontrolüdür** (`requiredRole` → `forbidden`); dosyada `is_dir(`/`is_readable(`
**0 isabet**. Doğru dosya: `MusicStreamHandler.php:204` (`is_readable`) ve
`SessionConfig.php:34` (`is_dir`). Davranış: mevcut satır **silinmez**; bu
bölümde teyit edilir ve doğrulanabilir gerçek satır listesi §2.1/§6.1'dedir.

### §7.2 AP-P2 — "erişim reddi sessiz" iddiası

`izin-anayasa.md:243-245` (§3.6) "erişim denetimi log'u görülmedi — PermissionMiddleware
reddi sessiz" der. Disk gerçeği: `PermissionMiddleware:47-63` reddi **önce**
`securityEvent('authz_denied', ...)` çağırır (L50-54), **sonra** 403 döner;
`FileHandler:169-184` olayı `warning` + `securityStream` ile yazar. Q-I6 bu
dosyanın `-surucu` §6.1'inde "log VAR" olarak düzeltilmiştir; anayasa satırı
korunur, düzeltme bu kayıttır.

### §7.3 AP-P3 — assets katmanında `Require all denied` YOK

`assets.coremusic.net/.htaccess` (47 satır) yalnız CORS/cache header'ları +
`Options -Indexes` (L47) taşır; `Require all denied` **0 isabet**. Assets
yalnız statik servis ediyorsa kasıtlıdır (Q-OR2); `.md`/`.json` gibi
doküman dosyaları bu kökte duruyorsa erişim açıktır — **UNKNOWN, ölçüm
gerekir** (bilinen kök dışı erişim testi PLANNED).

### §7.4 Kural — ne yapılır, ne yapılmaz

| Yapılır | Yapılmaz |
|---|---|
| Çelişkili iddia **korunur +** bu bölümde disk kanıtıyla düzeltilir | Eski satır silinir/üstü çizilir |
| Yeni iddia = dosya:satır + tarih | Tarih/satırsız genel iddia |
| Negatif kanıt (grep=0) bağlayıcı sayılır | "Kod yok" hissi |

## §8 Hata kataloğu — katman × kod matrisi

| Katman | Örnek | Ürettiği yanıt | Gözlemlenebilirliği |
|---|---|---|---|
| Uygulama — rol/izin | `PermissionMiddleware:30-41` | 403 JSON (`error: role_insufficient` / `permission_denied`) | log VAR (`authz_denied`) |
| Uygulama — yol kilidi | `MusicStreamHandler:175-206` | 404 (yok) / 403 (var-ama-red) | log **UNKNOWN** (`fail()` içi bu dosyada okunmadı — VERIFICATION REQUIRED) |
| Uygulama — şablon | `TemplateEngine:37-39,74-76` | `RuntimeException` (500 sınıfı) | log **UNKNOWN** (üst katmanın yakaladığı bilinmiyor) |
| Sunucu — Apache | `.htaccess` FilesMatch/`[F,L]` | 403 (`Require all denied`, `F`) | Apache erişim log'u (sunucuda, depo dışı) |
| Sunucu — IIS | `web.config` `CustomResponse` | 403 (özel gövde) | IIS log'u (sunucuda, depo dışı) |
| OS | `stat(2)`/`open(2)` | `EACCES` / `ENOENT` / `ELOOP` | syslog **UNKNOWN** (depо dışı) |

## §9 Sınır matrisi — komşu `isletim-sistemi-security` dalı

| Konu | Bu dosya (core/izin) | `isletim-sistemi-security` (16 dosya: `cekirdek-guvenlik`, `uygulama-guvenlik-siniri`, `yetki-ve-erisim-modeli`) |
|---|---|---|
| POSIX izin modeli + repo izin yüzeyi | **sahibi** (bu dosya + anayasa/surucu) | yalnız sınır notu |
| RBAC / `PermissionMiddleware` yetki modeli | örnek düzeyinde (§2.1 — izin kasası açısından) | **sahibi** (`yetki-ve-erisim-modeli`) |
| `.htaccess`/`web.config` güvenlik kuralları | ölçümlü envanter (§3-§5) | uygulama-güvenlik-siniri farklı açıdan işler |
| Denetim/log politikası | hattın varlığı (§2.4) | uygulama ve pentest tarafı |

Kural: bu dosya **model + repo-örnek** taşır; yetki modeli anlatımı ve denetim
uygulaması `-security` dalına devredilir (index §5 komşu sınırı ile uyumlu).

## §10 Ölçüm — bu dosyanın kendi ölçüm tablosu

| Metrik | Değer | Yöntem |
|---|---|---|
| `.htaccess` dosya sayısı | 4 | PowerShell `Get-ChildItem -Filter '.htaccess' -Recurse -Force` |
| `web.config` dosya sayısı | 4 | aynı yöntem |
| `Require all denied` (.htaccess) | 6 (api 2 · auth 2 · home 2 · assets 0) | `Select-String` satır numaralı |
| `Require all denied` (repo geneli) | 41 = 6 kod + 35 doküman | tüm uzantılarda `SimpleMatch` |
| `Options -Indexes` | 4/4 | satır numaralı |
| `directoryBrowse enabled="false"` | 1/4 (assets:35) | `Select-String` |
| `FilesMatch`/`location` (web.config) | 0 | `Select-String` |
| `is_dir(`/`is_readable(`/`is_writable(` (PHP) | 17 (16 ana + 1 `--\` kopyası) | `Get-ChildItem -Filter '*.php'` + `Select-String` |
| `is_writable(` | 0 | aynı tarama |
| `setcap`/`getcap` PHP kodu | 0 | reuse `-surucu` §5 (grep, 2026-10-13) |
| Dosya satır kanıtları | 11 dosya, satır aralıkları §1-§4 | `read`/PowerShell numaralı okuma |
| Çalışan sistem ölçümü | — | **YOK** (depо dışı; sunucu erişimi gerekir, PLANNED) |

Metodoloji: tüm sayımlar 2026-10-13'te repo kökünden (`C:\www\coremusic.net`)
**salt-okunur** komutlarla alındı; yazma yalnız `write`/`edit` ile yapıldı.
Çalışan sunucudan alınan ölçüm yoktur; yüzde/latency **uydurulmaz**.

## §11 Kaynakça — 6 URL, HTTP 200, 2026-10-13

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | `setcap(8)` — dosya capability bağlama (repoda uygulama YOK, §6.3) | https://man7.org/linux/man-pages/man8/setcap.8.html | **YENİ** · 2026-10-13 HTTP 200 · dağılım: `index.md` §4 (ornekler → setcap.8) |
| 2 | `mod_authz_host` — `Require` direktifi ve `all denied` değeri | https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html | **YENİ** · 2026-10-13 HTTP 200 |
| 3 | `ls(1)` — dizin listeleme / izin sütunlarını okuma aracı | https://man7.org/linux/man-pages/man1/ls.1.html | **YENİ** · 2026-10-13 HTTP 200 |
| 4 | `.htaccess` kullanımı (AllowOverride mekanizması) | https://httpd.apache.org/docs/2.4/howto/htaccess.html | **YENİ** · 2026-10-13 HTTP 200 |
| 5 | `stat(2)` — dosya durumu, `EACCES`, path üzerinde execute izni | https://man7.org/linux/man-pages/man2/stat.2.html | reuse (`isletim-sistemi-api-dogrulama.md:201`) · bugün HTTP 200 yeniden doğrulandı |
| 6 | IIS `system.webServer` yapılandırma modeli | https://learn.microsoft.com/en-us/iis/configuration/system.webServer/ | reuse (`isletim-sistemi-runtime/web-runtime-topolojisi/index.md:79`) · bugün HTTP 200 yeniden doğrulandı |

**Dürüstlük notu (bu oturum, IIS rewrite):** IIS URL-rewrite kuralları için 2
tam URL bugün **404** döndü ve **asla cite edilmedi**:
`https://learn.microsoft.com/en-us/iis/configuration/system.webServer/rewrite/rules/`
ve
`https://learn.microsoft.com/en-us/iis/extensions/url-rewrite-module/using-rewrite-rules-in-webconfig`
(önceki oturumda `.../creating-rewrite-rules-with-the-rewrite-module` ve
`.../url-rewrite-module/` kök sayfası da 404 idi — 4/4 başarısız). IIS rewrite
için bu yüzden **doküman yerine repo `web.config` satırları** kanıt olarak
kullanılır (§4.2); kural semantiği üzerinde `⚠️ VERIFICATION REQUIRED`
işareti taşır.

## §12 Açık sorular ve handover

| ID | Soru | Kapanış yolu |
|---|---|---|
| Q-OR1 | api `.htaccess:19` listesinde `json` neden yok? | sahibine sor ya da kasıtlı dene (kayıt: §3.2) |
| Q-OR2 | assets'te `Require all denied` 0 — assets kökünde servis edilmeyen dosya var mı? | deploy ağacı + dış istek testi (PLANNED) |
| Q-OR3 | IIS'de PHP doğrudan erişim engeli nerede? | sunucu IIS yapılandırması (depо dışı, VERIFICATION REQUIRED) |
| Q-OR4 | `--\bin\api-key-create.php` kopyası nedir? | `git log -- '--'` + sahibine sor |
| Q-I1..Q-I6 | izin kasasının ana açık soruları | anayasa §8 (bu dosya yeniden açmaz) |

Handover: §1.1 zinciri dosya-sistemi dalına (`dosya-sistemi-kavramlari-ornekler`)
örnek olarak devredilebilir; §2.1 RBAC akışı `-security/yetki-ve-erisim-modeli`
dosyasına referans verilir; §5 asimetri matrisi `-cross/tasinabilirlik-siniri`
dosyasındaki 232-235 satırlarıyla çaprazlanır.

## §13 Senaryo akışları — şüpheli istek, katman katman (kanıt satırlı)

> Her senaryo yalnız §1-§5'te okunmuş satırlardan türetilir; çalıştırılmamıştır
> (depо dışı sunucu erişimi yok). "Beklenen" sütunu **kod okuması** tahminidir,
> gözlemlenmiş ölçüm değildir.

| # | Senaryo | Apache yolu (kanıt) | IIS yolu (kanıt) | Uygulama yolu (kanıt) | Beklenen |
|---|---|---|---|---|---|
| S1 | `home` altındaki `.env` isteği | `home:18-20` FilesMatch → `Require all denied` | `home:24-27` sensitive → `CustomResponse 403` | — | 403 (iki sunucuda aynı) |
| S2 | `home` altındaki `include/config.php` doğrudan istek | `home:22-24` PHP FilesMatch (index.php dışı her `.php`) | web.config'te karşılık **YOK** (§5) | — | Apache 403 · IIS: **UNKNOWN** (Q-OR3) |
| S3 | `home` altındaki `pages/x` isteği | `home:35` `[F,L]` → 403 | `home:20-23` → 403 | — | 403 (iki sunucuda aynı) |
| S4 | stream URL'de `../../../etc/passwd` | — | — | `MusicStreamHandler:180-183` → 403 | 403, dosya hiç açılmaz |
| S5 | stream URL'si kütüphane dışındaki gerçek dosyayı gösteriyor | — | — | `:189-192` realpath → `:199-202` prefix → 403 | 403 (adım 6) |
| S6 | kütüphane içinde symlink (hedefi başka yerde) | — | — | `:185-187` `is_link` → 403 | 403 (adım 3, izlemeden önce) |
| S7 | okunabilirliği olmayan gerçek dosya (000 modu) | — | — | `:204-206` `is_readable` → 403 | 403 (adım 7) |
| S8 | `user` rolüyle `requiredRole: admin` rotası | — | — | `PermissionMiddleware:30-32` → 403 + `authz_denied` | 403 + log satırı |
| S9 | oturumsuz istek (çerez yok) | — | — | `PermissionMiddleware:23-25` passthrough | middleware kararı yok (önceki katmanların) |
| S10 | `assets` kökünde doküman dosyası | `assets`: FilesMatch reddi **0** (§3.1) | `assets:35` yalnız dizin listeleme kapalı | — | erişim **açık olabilir** — UNKNOWN (Q-OR2) |

Senaryo-S2 ve S10, bu dosyanın en önemli iki **açık** sonucudur: aynı istek
iki sunucu katmanında farklı davranabilir ve assets katmanı her iki yapılandırmada
da dosya-erişim reddi taşımaz. Her ikisi de ölçüm bekleyen açık madde olarak
kapanmaz.

## §14 Çıkarımlar — kanıttan türetilen 8 olgu

| # | Çıkarım | Dayanak |
|---|---|---|
| C1 | İzin kararı tek yerde değil, **üç katmanda** dağıtılmıştır | §2 (uygulama), §3-§4 (sunucu), §6 (envanter) |
| C2 | Uygulama katmanı POSIX izni **kontrol etmez**; rol/izin + yol denetimi yapar | §2.1 (64 satır), §6.1 (0 isabet bu dosyada) |
| C3 | Red yanıtı ikiye ayrılır: **varlık-sızdırmayan 404** ve **politika 403** | §1.2 |
| C4 | Denetim izi reddin **öncesindedir** (`forbidden()` önce log, sonra 403) | §2.4, §7.2 |
| C5 | Dizin listeleme engeli Apache'de tutarlı (4/4), IIS'de istisnai (1/4) | §4.1, §5 |
| C6 | IIS katmanı Apache'nin PHP-erişim FilesMatch'ini taşımaz | §5 (FilesMatch/location = 0) |
| C7 | Dosya capability ve ACL uygulaması **model-only**dır | §6.3 (grep 0, reuse) |
| C8 | Ölçüm zinciri üç halkada kopuktur: kod (var) → sunucu yapılandırma (var) → çalışan sistem (yok) | §10 |

## §15 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-13 | Dosya sıfırdan yazıldı (bu görevin ilk yazımı; öncesi **dosya yoktu** — `Test-Path: False`) | görev kaydı |
| 2026-10-13 | 6 URL doğrulandı (4 yeni + 2 reuse), 2 tam URL 404 → dürüstlük notu §11 | webfetch, §11 |
| 2026-10-13 | Tüm sayımlar repo kökünden read-only alındı | §10 metodolojisi |

Doğrulanabilirlik etkisi: bu dosyadaki her sayı §10 tablosunun tek komutuna
geri götürülebilir; her satır referansı doğrudan `read` ile açılıp görülür.
İddia–kanıt oranı: toplam iddia satırlarının tamamı dosya:satır veya tarihli
URL taşır; taşımayan tek alan §13 "Beklenen" sütunudur ve o da açıkça
"kod okuması tahmini" olarak işaretlidir.

## §16 Sık yapılan hatalar — bu kasada görülmesi muhtemel 7 hata

| # | Hata | Kanıt / ders | Doğrusu |
|---|---|---|---|
| H1 | POSIX izin ile uygulama iznini eşitlemek | `PermissionMiddleware`'te dosya-sistemi denetimi **0 isabet** (§6.1, AP-P1) | ikisi farklı katman; ikisi de gerekli (anayasa §3.3 "kopya değil" notu) |
| H2 | `realpath` sonrası `is_link` kontrolü | adım sırası §1.1: `is_link` (3) → `realpath` (4) | link izlendikten sonra `is_link` gerçeği söylemez; sıra ters çevrilemez |
| H3 | Her reddi 403 yapmak | §1.2: adım 1/4/5 → **404** | dosya yoksa 404 (varlık sızdırmama), politika ise 403 |
| H4 | Redi loglamadan döndürmek | AP-P2 (§7.2): `forbidden()` önce `securityEvent` (L50-54), sonra 403 (L56-62) | denetim izi reddin öncesinde üretilir |
| H5 | Apache `.htaccess` kurallarını `web.config`'e birebir kopya sanmak | §5: `FilesMatch` IIS'te 0, `DS_Store` 0, `directoryBrowse` 1/4 | her sunucu kendi dilinde yazılır; asimetri matrisi tekrar tekrar okunur |
| H6 | `Options -Indexes`'i tek savunma sanmak | assets:47 `Options -Indexes` var, `Require all denied` **0** (§3.1) | dizin listelemesi kapatmak ≠ dosya erişimi kapatmak |
| H7 | `@mkdir(..., 0750)` ile mod **garanti** sanmak | §2.2 + Q-I2: süreç umask'i modu düşürebilir | gerçek mod ölçülür (stat.2/ls.1), sabit yazılmaz |

Hata–bölüm eşlemesi: H1→§2.1/§6.1 · H2→§1.1 · H3→§1.2 · H4→§2.4 · H5→§5 ·
H6→§3.1 · H7→§2.2. Hiçbiri bu oturumda **yaşanmadı**; hepsi kod okumasından
türetilen risk kaydıdır.

## §17 Test senaryoları taslağı — sunucu doğrulama (PLANNED, çalıştırılmadı)

Aşağıdakiler bu dosyanın §13 senaryolarının **gözlem** karşılığıdır; depo dışı
sunucu erişimi olmadığı için çalıştırılmamıştır ve sonuç sütunları **tahmin
değil, beklenen davranış** olarak işaretlenmiştir (gözlem sonucu yazılmaz).

| # | Senaryonun gözlem adımı | Beklenen (kod okuması) | Gerçek sonuç |
|---|---|---|---|
| T1 | `home` altındaki `.env` isteği (dış istek) | 403 (home:18-20) | PLANNED |
| T2 | `home` altındaki `index.php` dışı `.php` isteği | 403 (home:22-24) | PLANNED |
| T3 | aynı isteklerin IIS ayağı (`home` web.config) | T1/T3 için 403; PHP için **UNKNOWN** (Q-OR3) | PLANNED |
| T4 | `assets` kökünde `.md`/`.json` isteği | erişim **açık olabilir** (Q-OR2) | PLANNED |
| T5 | oturum dizininin gerçek modu (`stat`/`ls -l`) | 0750 olması beklenir; umask düşürmüş olabilir | PLANNED |
| T6 | `authz_denied` log satırı gözlemi (red denemesi sonrası) | `[SECURITY] authz_denied reason=... warning` | PLANNED |

Kapanış kuralı: T1-T6'nın sonuçları bu dosyaya **değil**, `-dogrulama` dosyasına
işlenir (test dalı sahibi); sonuç gelmeden "çalışıyor" iddiası yazılmaz.

## §18 Dosya durumu özeti (kapanış tablosu)

| Öğe | Değer |
|---|---|
| Frontmatter | 7 zorunlu alan tam (şablon: `bellek-kavramlari-ornekler`) |
| Bölüm sayısı | §0-§18 (18 bölüm + ELI15) |
| Repo satır kanıtları | 11 dosya (§1-§3, §6) — hepsi 2026-10-13 `read`/sayım |
| Yeni doğrulanmış URL | 4 (setcap.8 · mod_authz_host · ls.1 · howto/htaccess.html) |
| Reuse + bugün 200 | 2 (stat.2 · system.webServer) |
| 404 dürüstlük notu | 2 tam URL bugün + 2 önceki oturum (§11) |
| `⚠️` / UNKNOWN işaretleri | 4 açık madde (Q-OR1..Q-OR4, §12) |
| Korunan çelişki kaydı | AP-P1 (izin-anayasa:215) · AP-P2 (izin-anayasa:243-245) — silinmedi, düzeltme bu dosyada |
| Commit durumu | **atılmadı** — commit orkestratöre aittir (kurallar §16.5) |

---

## §19 Çapraz-platform gruplu kod örnekleri (2026-10-14)

> **Grup kuralı (bağlayıcı):** Her grup **tek kavram + üç platform kolu**
> (Linux/POSIX · Windows (Win32) · macOS/POSIX) taşır. "Sadece Linux" örnek
> bu bölümde geçersizdir. macOS-native man sayfaları bu turda erişilemedi
> (`developer.apple.com/library/archive/.../man*_*.html` → bağlantı koptu/404);
> macOS sütunu POSIX spesifikasyonuna dayanır ve `⚠️ VERIFICATION REQUIRED`
> ile işaretlenir.

### §19.1 G7a - Erişim modu belirleme: `umask` + `chmod` ↔ DACL

**(A) Linux/POSIX —**

```c
#include <sys/stat.h>
#include <fcntl.h>

mode_t old = umask(0077);            /* yeni nesneler yalniz sahibine acik */
int fd = open("/var/tmp/gizli.txt", O_CREAT | O_WRONLY, 0600);
if (fd >= 0) close(fd);

if (chmod("/var/tmp/gizli.txt", 0640) == -1) { /* errno: EPERM / EACCES */ }
umask(old);                           /* eski maskeyi geri koy */
```

**(B) Windows (Win32) — DACL (Discretionary Access Control List):**

```c
#include <windows.h>
#include <aclapi.h>

/* Windows'ta "mod biti" YOKTUR; erisim DACL satirlariyla (ACE) tanimlanir */
PSECURITY_DESCRIPTOR psd = NULL;
DWORD er = GetNamedSecurityInfoW(L"C:\\temp\\gizli.txt",
                                 DACL_SECURITY_INFORMATION,
                                 NULL, NULL, NULL, NULL, &psd);
if (er == ERROR_SUCCESS && psd != NULL) { LocalFree(psd); }

er = SetNamedSecurityInfoW(L"C:\\temp\\gizli.txt",
                            DACL_SECURITY_INFORMATION,
                            NULL, NULL, NULL, NULL);
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

| Kavram | Linux/macOS | Windows |
|---|---|---|
| Tanımlayıcı | 9-12 **bit** (`rwxrwxrwx`) | **DACL** = ACE listesi |
| Varsayılan maske | `umask(0077)` süreç-bazlı | Yok (miras `SECURITY_ATTRIBUTES` ile) |
| Değiştirme | `chmod(path, mode)` | `SetNamedSecurityInfoW` |
| Okuma | `stat().st_mode` | `GetNamedSecurityInfoW` |
| Yetki gereği | sahibi/`CAP_FOWNER` | nesne sahibi ya da `WRITE_DAC` |

### §19.2 G7b - Politik satır (ACE) ekleme

**(A) Linux/POSIX — POSIX.1e ACL (Linux genişletmesi):**

```c
#include <sys/acl.h>
#include <acl/libacl.h>

acl_t a = acl_get_file("/var/tmp/gizli.txt", ACL_TYPE_ACCESS);
if (a != NULL) {
    /* a, bireysel kullanici/grup satirlarini tasir */
    int rc = acl_set_file("/var/tmp/gizli.txt", ACL_TYPE_ACCESS, a);
    (void)rc;
    acl_free(a);
}
```

> **Kapsam notu:** POSIX.1e ACL tabanı POSIX.1-2001/Issue 8 kapsamı **dışındadır**
> (`pubs.opengroup.org/.../functions/acl_*.html` → **HTTP 404**, ölçüldü).
> Bu yüzden (A) kolu **yalnız Linux man sayfalarına** dayanır; macOS koluna
> taşınması `⚠️ VERIFICATION REQUIRED`.

**(B) Windows (Win32) — ACE üret + DACL'e tak:**

```c
#include <windows.h>
#include <aclapi.h>

BYTE buf[sizeof(ACL) + 256];
PACL pacl = (PACL)buf;
if (InitializeAcl(pacl, sizeof buf, ACL_REVISION)) {
    if (AddAccessAllowedAce(pacl, ACL_REVISION,
                            GENERIC_READ, /* SID */ psid)) {
        SetNamedSecurityInfoW(L"C:\\temp\\gizli.txt",
                              DACL_SECURITY_INFORMATION,
                              NULL, NULL, pacl, NULL);
    }
}
```

**(C) macOS / POSIX** — macOS `chmod`/`chown` (A2 §19.3) ile idare edilir;
`acl_*` için **`⚠️ VERIFICATION REQUIRED`** (kaynak erişilemedi).

| Adım | Linux/POSIX ACL | Windows DACL |
|---|---|---|
| Oku | `acl_get_file` | `GetNamedSecurityInfoW` |
| Kur | `acl_set_file` | `SetNamedSecurityInfoW` |
| Satır ekle | `acl_create_entry` (bu turda doğrulanmadı → `⚠️`) | `AddAccessAllowedAce` |
| Revizyon | — | `ACL_REVISION` |

### §19.3 G7c - Kimlik: `setuid`/`seteuid` ↔ süreç erişimi

**(A) Linux/POSIX —**

```c
#include <unistd.h>

uid_t ruid, euid;
getuid(&ruid); geteuid(&euid);       /* gercek / etkin kimlik */
if (seteuid(0) == -1) { /* EPERM - yetki yok */ }
/* is bitince geri al */
seteuid(euid);
```

**(B) Windows (Win32) — token yerine süreç erişim maskesi:**

```c
#include <windows.h>

HANDLE hp = OpenProcess(PROCESS_QUERY_LIMITED_INFORMATION, FALSE, pid);
if (hp == NULL) {
    DWORD e = GetLastError();        /* ERROR_ACCESS_DENIED */
    (void)e;
} else {
    /* sorgulama yap - CloseHandle gerekli */
    CloseHandle(hp);
}
```

**(C) macOS / POSIX** — (A) birebir geçer · `⚠️ VERIFICATION REQUIRED`.

> **Asimetri (bu dalın uyarısı):** POSIX `seteuid` **kimliği kalıcı değiştirir**;
> Windows `OpenProcess` **erişim maskesi** verir, kimlik değiştirmez. İki kol
> **eşdeğer değildir** — eşdeğerlik iddiası `⚠️ VERIFICATION REQUIRED`.

### §19.4 G7d - Hata yüzeyi: `errno` ↔ `GetLastError`

| # | Soru | Linux/POSIX | Windows |
|---|---|---|---|
| 1 | Sayaç nerede? | süreç-bazlı `errno` (`errno.h`) | iplik-bazlı `GetLastError()` |
| 2 | Hata adı | `strerror(errno)` | `FormatMessage` (bu turda doğrulanmadı → `⚠️`) |
| 3 | Yaygın kodlar | `EACCES` · `ENOENT` · `EPERM` · `EEXIST` | `ERROR_ACCESS_DENIED` · `ERROR_FILE_NOT_FOUND` |
| 4 | Bağlantı | `basedefs/errno.h` (reuse) | `SetLastError` / `GetLastError` |
| 5 | Dikkat | `errno` çağrısız **değişmez** | dönüş değeri **önce** okunmalı |

### §19.5 Yeni doğrulanmış URL (2026-10-14 · envanter kesişimi 0 · HTTP 200)

> **Ölçüm (2026-10-14 · yeniden):** `.ai/**` (gizli dizin, `*.md|*.json|*.txt|*.ps1`)
> URL kümesi **bu turda yazılan 6 dosya hariç** tutularak ölçüldü = **3760 tekil URL**;
> aşağıdaki adayların **hiçbiri** bu kümede yok → kesişim **0**. Tur-çapı tek-çatı
> sayım ve kümülatif envanter (**3866**) için bkz. `isletim-sistemi-core-dogrulama.md` **§J**.

| # | İddia | URL | 2026-10-14 |
|---|---|---|---|
| 1 | `acl_get_file` - POSIX.1e ACL okuma (Linux man) | https://man7.org/linux/man-pages/man3/acl_get_file.3.html | ? HTTP 200 · **YENİ** |
| 2 | `acl_set_file` - POSIX.1e ACL yazma (Linux man) | https://man7.org/linux/man-pages/man3/acl_set_file.3.html | ? HTTP 200 · **YENİ** |
| 3 | `chmod` POSIX tanımı (mode bitleri) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chmod.html | ? HTTP 200 · **YENİ** |
| 4 | `fchmod` POSIX tanımı (tanıtıcı üzerinden mod) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fchmod.html | ? HTTP 200 · **YENİ** |
| 5 | `chown` POSIX tanımı (sahiplik) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/chown.html | ? HTTP 200 · **YENİ** |
| 6 | `setuid` POSIX tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/functions/setuid.html | ? HTTP 200 · **YENİ** |
| 7 | `seteuid` POSIX tanımı (etkin kimlik) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/seteuid.html | ? HTTP 200 · **YENİ** |
| 8 | `pathconf` POSIX tanımı (dosya sistemi limitleri) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pathconf.html | ? HTTP 200 · **YENİ** |
| 9 | Windows Access Control List (DACL/ACE) kavramı | https://learn.microsoft.com/en-us/windows/win32/secauthz/access-control-lists | ? HTTP 200 · **YENİ** |
| 10 | `SetNamedSecurityInfoW` - DACL yazma | https://learn.microsoft.com/en-us/windows/win32/api/aclapi/nf-aclapi-setnamedsecurityinfow | ? HTTP 200 · **YENİ** |
| 11 | `GetNamedSecurityInfoW` - DACL okuma | https://learn.microsoft.com/en-us/windows/win32/api/aclapi/nf-aclapi-getnamedsecurityinfow | ? HTTP 200 · **YENİ** |
| 12 | `AddAccessAllowedAce` - izin veren ACE ekleme | https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-addaccessallowedace | ? HTTP 200 · **YENİ** |
| 13 | `InitializeAcl` - ACE listesi başlatma | https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-initializeacl | ? HTTP 200 · **YENİ** |
| 14 | `OpenProcess` - süreç erişim maskesi | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-openprocess | ? HTTP 200 · **YENİ** |
| 15 | `SetLastError` - hata kodu yazma | https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-setlasterror | reuse · HTTP 200 |

### §19.6 Reuse URL - HTTP 200 yeniden doğrulandı (2026-10-14)

| # | İddia | URL |
|---|---|---|
| 1 | `umask(2)` - varsayılan maske | https://man7.org/linux/man-pages/man2/umask.2.html |
| 2 | `chmod(2)` - mod değiştirme | https://man7.org/linux/man-pages/man2/chmod.2.html |
| 3 | `stat(2)` - mod/sahiplik okuma | https://man7.org/linux/man-pages/man2/stat.2.html |
| 4 | `setuid(2)` - gerçek UID | https://man7.org/linux/man-pages/man2/setuid.2.html |
| 5 | `chown(2)` - sahiplik | https://man7.org/linux/man-pages/man2/chown.2.html |
| 6 | `errno.h` - hata kodu taban tanımı | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/errno.h.html |
| 7 | `open` POSIX tanımı (erişim kararının başladığı yer) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html |
| 8 | `GetLastError` - Windows hata okuma | https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror |
| 9 | `CreateFileW` - erişim isteği + share mode | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew |
| 10 | `CloseHandle` | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle |
| 11 | Dosya güvenliği ve erişim hakları (Win32) | https://learn.microsoft.com/en-us/windows/win32/fileio/file-security-and-access-rights |
| 12 | ACE (Access Control Entry) yapısı | https://learn.microsoft.com/en-us/windows/win32/secauthz/access-control-entries |

### §19.7 Bu bölümün ölçüme kaydı (2026-10-14)

| Ölçüm | Değer |
|---|---|
| Eklenen grup | 4 (G7a mod/DACL · G7b ACE · G7c kimlik · G7d hata yüzeyi) |
| Platform kolları | 3/grup = 12 kol |
| Eklenen kod bloğu | 6 (A=3 · B=3) |
| Yeni doğrulanmış URL | 14 (§19.5 #1-#14) · envanter kesişimi **0** · HTTP 200 |
| Reuse URL yeniden 200 | 12 (§19.6) |
| Ölçülen 404 | `pubs.opengroup.org/.../functions/acl_*.html` → **HTTP 404** (ACL POSIX-temel dışında) |
| macOS-native kaynak | 0 erişilebilir → 3 iddia `⚠️ VERIFICATION REQUIRED` |
| Çalıştırılan gözlem | 0 (kod temsilidir; §17 taslak PLANNED kalır) |
| `git commit` | **atılmadı** (orkestratöre ait) |

---

## §20 PHP Köprüsü — izin ve güvenlik (repo satır kanıtlı, 2026-10-14)

> PHP, POSIX izin modeline (`mode`/`umask`/`access`) yalnızca **kısmen** ve çoğunlukla
> **salt-okunur** bakar; Windows DACL/ACL yüzeyine ise **hiç bakmaz**. Bu bölüm her satırı
> `ReadAllLines` ile doğrulanmış gerçek repo kodundan alır ve PHP'nin neyi **kapsadığını**,
> neyi **kapsamadığını** ayrı ayrı söyler.

### §20.1 I1 — Dizin modu: `@mkdir($d, $mod, true)` ↔ `mkdir(2)` ↔ `CreateDirectoryW`

**Repo kanıtları (4 gerçek satır, 4 farklı mod):**

`shared/src/Session/SessionConfig.php:33-36`

```php
$savePath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';
if ($savePath !== '' && !is_dir($savePath)) {
    @mkdir($savePath, 0750, true);
}
```

`shared/src/Session/SessionInitializer.php:29-32`

```php
$savePath = ini_get('session.save_path') ?: 'C:\\temp';
if ($savePath !== '' && !is_dir($savePath)) {
    @mkdir($savePath, 0777, true);
}
```

`home.coremusic.net/config/constants.php:75-78`

```php
$sessionPath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0750, true);
}
```

`bin/api-key-create.php:72-75`

```php
$dir = dirname($path);
if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
    throw new \RuntimeException("Dizin oluşturulamadı: {$dir}");
}
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `@mkdir($p, $mod, true)` | `mkdir($p, $mod)` → etkin mod = `$mod & ~umask` | `CreateDirectoryW` — **mod yok** |
| `!is_dir && !@mkdir && !is_dir` | `EEXIST` tolere (yarış) | `ERROR_ALREADY_EXISTS` tolere |
| zarf `@` + `RuntimeException` | `errno` + çağıran | `GetLastError` + çağıran |

**Önemli üç gerçek:**

1. **PHP `mkdir` modu süreç `umask`'ı ile maskelenir** (`0777 & ~umask`); depoda
   `umask()` çağrısı **grep → 0 isabet** (§20.5) → etkin mod sunucu varsayılanına
   bağlıdır ve **sürüm-dışı bilgidir** (`⚠️ VERIFICATION REQUIRED`).
2. **Windows'ta mod yok sayılır**; PHP 8.x'te `mkdir` modu NTFS ACL'ye **çevrilmez**.
   SessionInitializer `$savePath` fallback'inin `C:\temp` olması bu yüzden
   tutarsız değildir — Windows'ta mod zaten etkisizdir.
3. `0777` (SessionInitializer:31) POSIX'te **herkes yazabilir** demektir; `0750`
   (SessionConfig:35, constants:77) ile çelişir. **Kod-satırı gözlemidir**, karar
   değil — vault'ta hangisinin standart olduğu **kayıtlı değil** → `⚠️ VERIFICATION REQUIRED`.

### §20.2 I2 — Okunabilirlik kontrolü: `is_readable` ↔ `access(R_OK)` ↔ `GetFileAttributesW`

**Repo kanıtları:**

`home.coremusic.net/include/Stream/MusicStreamHandler.php:204-206`

```php
if (!is_readable($real)) {
    $this->fail(403, 'Erişim reddedildi');
}
```

`media.coremusic.net/bin/scan.php:151-153`

```php
if (!is_readable($tam)) {
    $hata++;
    fwrite(STDERR, "HATA | {$goreli} | dosya-var | dosya okunamadi\n");
```

| PHP | POSIX | Windows Win32 |
|---|---|---|
| `is_readable($p)` | `access(p, R_OK)` (gerçek UID ile) | `GetFileAttributesW` + ACL denemesi |
| `is_executable` | `access(p, X_OK)` | uzantı/AppLocker |
| `is_writable` | `access(p, W_OK)` | ACL denemesi |

**Dağılım:** `is_readable` → **var** (2 isabet, ikisi de yukarıda). `is_writable` →
**grep 0 isabet** (§20.5). Yani depoda **yazma izni önceden kontrol edilmiyor**;
yazma denemesi `file_put_contents === false` ile (dosya-sistemi §13.1-§13.2)
**yakalanıyor** — bu desen TOCTOU'a **daha dirençlidir** (kontrol yerine deneme).

### §20.3 I3 — Zincir: kimlik redi + kök-kapsam (özür değil, özet)

Yol güvenliği zinciri bu dosyanın **§1'inde** (`MusicStreamHandler::resolvePath`) tam
hâliyle kanıtlanmıştır; PHP katmanı üç adımı da **POSIX'ten bağımsız** yapar:
`is_link` (red) → `realpath` (çözüm) → `str_starts_with($real, $prefix)` (kapsam).
Karşılıkları:

| Adım | PHP | POSIX | Windows Win32 |
|---|---|---|---|
| symlink redi | `is_link` | `lstat` + `S_ISLNK` | `FILE_ATTRIBUTE_REPARSE_POINT` |
| gerçek yol | `realpath` | `realpath(3)` | `GetFinalPathNameByHandleW` |
| kapsama | `str_starts_with` | `openat` + `O_NOFOLLOW` (PHP'de yok) | `NtCreateFile` |
| okunabilirlik | `is_readable` | `access(R_OK)` | ACL |

`⚠️ VERIFICATION REQUIRED`: PHP'de `O_NOFOLLOW`-eşdeğer atomik API **yoktur**; `is_link`
ile `realpath` arasındaki yarış kapatılmaz (dosya-sistemi §13.6 ile aynı bulgu).

### §20.4 I4 — Hata yüzeyinde redaction: log sızıntısının engellenmesi

**Repo kanıtı:** `shared/src/PageRouter/PageRouterKernel.php:128-136`

```php
} catch (\Throwable $e) {
    $errorMsg = date('c') . ' [' . $traceId . '] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
    // Merkezi redaction (§17 Edge Case 3): trace argümanlarındaki hassas alanlar maskelenir.
    $errorMsg = Redactor::redact($errorMsg);
    $logDir = $this->config->get('app.log_dir', dirname(__DIR__, 3));
    @file_put_contents($logDir . '/coremusic_php_errors.log', $errorMsg, FILE_APPEND | LOCK_EX);
```

| Katman | PHP | POSIX | Windows |
|---|---|---|---|
| hata yakalama | `catch (\Throwable)` | `errno` + sinyal | `GetLastError` + `SEH` |
| log yazımı | `file_put_contents(LOCK_EX)` | `open(O_APPEND)` + `flock` | `LockFileEx` |
| maskeleme | `Redactor::redact` | (yok) | (yok) |

**Gözlem:** maskeleme PHP'nin hata sistemine **değil**, uygulama katmanına **sonradan**
eklenmiştir. POSIX `errno`/Windows `GetLastError` değeri hiçbir yerde PHP'ye
yansımaz → `EACCES`/`ERROR_ACCESS_DENIED` ayrımı yapılamaz (bellek §18.4 ile aynı bulgu).

### §20.5 I5 — Olumsuz kanıt (grep → **0 isabet**)

`umask(` · `chmod(` · `is_writable(` · `chown(` · `posix_setuid` · `posix_acl` /
`setfacl` / `getfacl` — depoda **hiçbiri kullanılmıyor**. Ek olarak:

- PHP çekirdeğinde **POSIX ACL** (`posix_acl_*`) ve **Windows DACL** erişimi **yoktur**;
  bunlar yalnız `setfacl`/`icacls` gibi dış araçların CLI çağrısıyla yapılabilir
  (depoda `proc_open` yalnız testte, §20.5'e **dahil değil** — bkz. süreç §20.1).
- **Yani:** PHP kodu yalnız `mode` (rwx) ve **imleç-düzeyi** (open/test) denetimi yapar.
  Dosya sahibi/grup/ACL satırları **PHP'nin kapsamı dışındadır** → §19.1-§19.2'deki
  `umask`/`chmod` ↔ DACL ve ACE eşlemeleri **model-only**dır, PHP koduyla
  doğrulanamaz (`⚠️ VERIFICATION REQUIRED`) — §6.3'ün negatif kanıtıyla aynı sonuç.

### §20.6 Ölçüm

| Ölçüm | Değer |
|---|---|
| Eklenen PHP kod bloğu | 6 (§20.1 ×4 · §20.2 ×2 + §20.4) |
| Repo satır çifti | 8 (SessionConfig:33-36 · SessionInitializer:29-32 · constants:75-78 · api-key-create:72-75 · MusicStreamHandler:204-206 · scan.php:151-153 · PageRouterKernel:128-136) |
| Yeni URL | **0** → envanter kesişimi 0 |
| Olumsuz grep | `umask` · `chmod` · `is_writable` · `chown` · `setfacl` → 0 |
| ⚠️ işaretli iddia | 3 (§20.1 umask etkisizliği · §20.1 mod tutarsızlığı kararı · §20.3/§20.5 O_NOFOLLOW + ACL kapsamı) |

## §21 — Çok dilli izin/güvenlik örnekleri: JS · C · Python · Go · Rust · C#

> **Kanıt hiyerarşisi:** §20 PHP blokları depo satırına dayanıyordu. Burada **yalnız JS (98 dosya)
> ve Python (1 dosya) depoda taşınır**; C/Go/Rust/C# **0 dosya** → `⚠️ KALIP (repo YOK)` + resmi
> doküman URL'i (2026-10-14 · hepsi HTTP 200 → dogrulama §J.9).

### §21.0 Ölçüm — kanıt durumu (2026-10-14)

| Dil | Repo dosyası | İzin yüzeyi grep (repo) | Kanıt tipi |
|---|---|---|---|
| JavaScript | **98** | `chmod` **0** · `umask` **0** · `setuid` **0** · `is_writable` **0** | repo + olumsuz kanıt |
| Python | **1** | `chmod` **0** (`.ai/scripts/fix-mojibake.py` yalnız oku/yaz) | kalıp + `os.html` |
| C / Go / Rust / C# | **0 / 0 / 0 / 0** | — | kalıp + doküman |

### §21.1 JavaScript — depoda dosya-modu API'si yok

98 JS dosyasında `chmod`/`umask`/`setuid` **0 isabet** → tarayıcı JS'i dosya izni **üretemez**;
izin yüzeyi OS değil, tarayıcı politikasıdır (CSP / feature politikası — ayrı bir güvenlik katmanı).
`⚠️ VERIFICATION REQUIRED`: depoda `navigator.permissions` kullanımı aranmadı (grep yapılmadı).
Bu bölüm için **JS kalıp bloğu yazılmadı** — JS'in OS izni yoktur, uydurulacak karşılığı da yoktur.

### §21.2 C — `umask` + yaratma modu (kalıp)

```c
/* ⚠️ KALIP (repo YOK) — Kaynaklar:
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/umask.html (YENİ · 200)
   https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html  (reuse · 200) */
#include <sys/stat.h>
#include <fcntl.h>

mode_t old = umask(027);                    /* bundan sonraki yaratmalarda grup-yazma kapalı */
int fd = open(p, O_CREAT | O_EXCL | O_WRONLY, 0640);   /* istenen mod */
umask(old);                                 /* geri al */
```

**Eşleme:** PHP §20.1'deki `@mkdir($d, 0750, true)` ↔ C'de `umask` + `mode` **iki adımdır**;
PHP'de `umask()` çağrısı depoda **0 isabet** (§20.5) — C kalıbı tam olarak PHP'nin atladığı addır.

### §21.3 Python — `os.chmod` + erişim kontrolünün tuzağı (kalıp)

```python
# ⚠️ KALIP (repo YOK) — Kaynak: https://docs.python.org/3/library/os.html (reuse · 200)
import os, stat

os.chmod(p, 0o750)                          # PHP §20.1 0750 eşlemesi
mode = os.stat(p).st_mode                  # okuma
if not stat.S_ISREG(mode):
    raise RuntimeError("regular file değil → symlink/red")
# os.access(p, os.R_OK) → TOCTOU: kontrol ile kullanım arasında yarış (§20.2 ile aynı)
```

**Eşleme:** `os.chmod` ↔ PHP `chmod` — **depoda PHP `chmod(` 0 isabet** (§20.5). `os.access` tuzağı
PHP §20.2'deki `is_readable` tuzağının birebir aynısıdır (kontrol ≠ kullanım).

### §21.4 Go — `os.Chmod` (kalıp)

```go
// ⚠️ KALIP (repo YOK) — Kaynak: https://pkg.go.dev/os (YENİ · 200)
package main

import "os"

func main() {
    _ = os.Chmod("secret.key", 0o600)
    fi, _ := os.Stat("secret.key")
    _ = fi.Mode() // os.FileMode — perm & os.ModePerm
}
```

**Eşleme:** Go `0o600` yazımı PHP'nin `0600` sekizlik yazımıyla aynı değeri üretir;
`⚠️ VERIFICATION REQUIRED`: Windows'ta `os.Chmod`'un yalnız read-only bitini yansıtması
dokümanda bu turda **okunmadı**.

### §21.5 Rust — `fs::set_permissions` (kalıp)

```rust
// ⚠️ KALIP (repo YOK) — Kaynak:
// https://doc.rust-lang.org/std/fs/fn.set_permissions.html (YENİ · 200)
use std::fs;
use std::os::unix::fs::PermissionsExt;      // unix'e özel — §21.7 notu

let mut perm = fs::metadata("secret.key")?.permissions();
perm.set_mode(0o600);
fs::set_permissions("secret.key", perm)?;
```

**Eşleme:** `PermissionsExt::set_mode` POSIX modunu doğrudan kurar (PHP §20.1 `0750` eşlemesi).
`⚠️ VERIFICATION REQUIRED`: `std::os::unix` bloğunun **yalnız unix** olduğu (Windows derlemesi
kırar) iddiası dokümanda bu turda **okunmadı**.

### §21.6 C# — `File.SetUnixFileMode` + Windows ACL (kalıp)

```csharp
// ⚠️ KALIP (repo YOK) — Kaynak:
// https://learn.microsoft.com/en-us/dotnet/api/system.io.file.setunixfilemode (YENİ · 200)
using System.IO;

File.SetUnixFileMode("secret.key",
    UnixFileMode.UserRead | UnixFileMode.UserWrite);   // 0600 karşılığı
// Windows tarafı: FileSystemAcl sınıfı (⚠️ URL doğrulanmadı, bu blokta yok)
```

**Eşleme:** `UnixFileMode` ↔ POSIX `mode_t` (§21.2 ile aynı kavram). `⚠️ VERIFICATION REQUIRED`:
`SetUnixFileMode`'in eklendiği .NET sürümü ve Windows'taki davranışı **okunmadı**; Windows ACL
(`FileSystemAcl`) için URL **eklenmedi**.

### §21.7 Ölçüm (§21)

| Öğe | Değer |
|---|---|
| Dil | 6 (JS · C · Python · Go · Rust · C#) — **JS kalıpsız** (OS izni yok) |
| Repo satır kanıtlı blok | **0** (bu bölüm yalnız olumsuz kanıt + kalıp taşır) |
| `⚠️ KALIP (repo YOK)` blok | **5** (§21.2 C · §21.3 Python · §21.4 Go · §21.5 Rust · §21.6 C#) |
| Bu bölümün URL'i | **6** → **2 reuse** (`pubs open.html` · `docs.python os.html`) **+ 4 yeni** (`pubs umask` · `pkg.go.dev/os` · `rust set_permissions` · `MS SetUnixFileMode`) — hepsi HTTP 200 (dogrulama §J.9) |
| Repo grep (olumsuz kanıt) | JS `chmod` · `umask` · `setuid` · `is_writable` → **0** · PHP `chmod(` · `umask(` → **0** (§20.5) |
| ⚠️ işaretli iddia | 5 (§21.1 `navigator.permissions` aranmadı · §21.4 Windows `os.Chmod` · §21.5 `std::os::unix` tekilliği · §21.6 .NET sürümü + ACL · §21.2 `umask` geri-alım emri kalıp) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
