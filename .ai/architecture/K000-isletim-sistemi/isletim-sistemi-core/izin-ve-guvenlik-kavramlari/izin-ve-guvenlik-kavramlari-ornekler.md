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
validation: "2026-10-13 → 6 URL HTTP 200 (4 YENİ: setcap.8 · mod_authz_host · ls.1 · howto/htaccess.html · 2 reuse bugün yeniden 200: stat.2 · system.webServer) · repo satırları Read/PowerShell ile birebir · sayımlar read-only · IIS rewrite için 2 tam URL bugün 404 (§11 dürüstlük notu)"
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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
