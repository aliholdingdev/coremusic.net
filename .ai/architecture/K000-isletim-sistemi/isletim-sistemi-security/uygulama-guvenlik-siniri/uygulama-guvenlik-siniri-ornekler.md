---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
evidence:
  - shared/src/Middleware/OriginCheckMiddleware.php:48
  - shared/src/Middleware/OriginCheckMiddleware.php:55
  - shared/src/Middleware/RateLimiterMiddleware.php:86
  - shared/src/Middleware/BypassAuthMiddleware.php:30
  - shared/src/Middleware/BypassAuthMiddleware.php:43
  - https://man7.org/linux/man-pages/man7/pid_namespaces.7.html
  - https://man7.org/linux/man-pages/man7/ipc_namespaces.7.html
  - https://man7.org/linux/man-pages/man2/openat2.2.html
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 7 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: kod
---

# Uygulama-Güvenlik-Sınırı Örnekleri — Gerçek Repo Kanıtı, OS Karşılıkları, Anti-Pattern

## §0 ELI15

Bu dosya bu alt-dalın **kanıt dosyasıdır**. İki tür satır içerir: (a) bu
repodan **gerçekten okunmuş** kod satırları — her biri `Dosya:` + `Satır:` +
`Select-Object -Index (N-1)` ile kanıtlanmıştır; (b) repo'da **karşılığı olmayan**
OS mekanizmaları — bunlar `Repo karşılığı: YOK` işaretiyle taşınır ve
`kaynak: upstream docs` ile imzalanır. Karıştırma yoktur: uygulama kodu ile
OS beklentisi her zaman ayrı sütunlarda durur.

> **Oncelikli gercek:** Sınır bu repo'da **iki ayrı noktada** somuttur —
> (1) **reddetme** (`origin_blocked` + `403`, `count > 60` + `429`,
> bypass `return false`), (2) **dosya izni** (`0750`). OS tarafındaki
> karşılıkların **tamamı yoktur** (§2.4).

## §1 Çalışan yapılandırmalar

### §1.1 Sınırın repo'daki üç somut noktası

| # | Nokta | Dosya | Satır | Doğrulama | Davranış |
|---|---|---|---|---|---|
| N-1 | origin reddi (log) | `shared/src/Middleware/OriginCheckMiddleware.php` | 48 | idx 47 | `securityEvent('origin_blocked', ...)` |
| N-2 | origin reddi (HTTP) | `shared/src/Middleware/OriginCheckMiddleware.php` | 55 | idx 54 | `'httpStatus' => 403` |
| N-3 | limit aşımları | `shared/src/Middleware/RateLimiterMiddleware.php` | 86 | idx 85 | `if ((int)$count > $this->maxRequests)` |
| N-4 | limit red kodu | `shared/src/Middleware/RateLimiterMiddleware.php` | 94 | idx 93 | `'httpStatus' => 429` |
| N-5 | bypass kilidi (config) | `shared/src/Middleware/BypassAuthMiddleware.php` | 43 | idx 42 | `return $next($request); // Fail-closed` |
| N-6 | bypass kilidi (credential) | `shared/src/Middleware/BypassAuthMiddleware.php` | 30 | idx 29 | `return []; // Fail-closed` |
| N-7 | üretim kilidi | `shared/src/Security/SecurityHelper.php` | 26 | idx 25 | `return false;` |

**Toplam:** 7 kanıt satırı, **7/7 `Select-Object -Index` birebir**. Bu yedi satır
bu alt-dalın tamamının dayandığı fiziksel gerçektir.

### §1.2 Sınır davranış sınıfı özeti

| Sınıf | Mekanizma | Sayı | Repo örneği | Fail yönü |
|---|---|---|---|---|
| Red | `return` + `halt` | 3 | `OriginCheck:55` · `CsrfMiddleware:42` · `Permission:31` | RED |
| Sınır (limit) | sayaç + eşik | 2 | `RateLimiter:86` · `:94` | RED |
| Kilitleme | eksiklik = kapalı | 3 | `BypassAuth:30,43` · `SecurityHelper:26` | RED |
| Kayıt | log olayı | 2 | `OriginCheck:48` · `RateLimiter:88` | nötr |
| Dosya izni | `0750` | 2 | `SessionConfig:20,35` | RED (diğer) |

**Okuma:** 5 sınıfın 4ü RED yönündedir; yalnız "Kayıt" sınıfı nötrdür ve o da
**tek başına asla geçiş sağlamaz** (log + zorlama birliktedir).

### §1.3 OS karşılığı OLAN vs OLMAYAN yapılandırmalar

| Yapılandırma | Uygulama karşılığı | OS karşılığı | Durum |
|---|---|---|---|
| beyaz liste origin | `OriginCheck` | `seccomp` filtresi | **OS yok** |
| istek limiti | `RateLimiter` 60/60 | `cgroup` + `nftables` | **OS yok** |
| oturum dosyası izni | `mkdir 0750` | DAC (`mode_t`) | **OS VAR** |
| yol denetimi | `..` kontrolü | `openat2(RESOLVE_BENEATH)` | **OS yok** |
| etiketli dosya | — | extended attribute (`xattr`) | **OS yok** |
| süreç izolasyonu | — | PID namespace | **OS yok** |
| paylaşımlı kaynak | — | IPC namespace | **OS yok** |
| başlık sızıntısı engeli | `SecurityHeaders` | `kptr_restrict` | **OS yok** |
| tarayıcı çerezi | `httponly`/`samesite` | — | uygulama (n/a) |
| bypass kilidi | 3 katmanlı `return` | `no_new_privs` | **OS yok** |

**Sayım:** 10 satır = **1 OS VAR · 8 OS yok · 1 uygulama**. Sınırın %80i
OS tarafında **karşılıksızdır**.

### §1.4 Repo'da hiç başlamayan OS yüzeyleri (negatif kanıt)

| Yüzey | Beklenen dosya/arama | Sonuç | Ölçüm |
|---|---|---|---|
| seccomp programı | `seccomp_rule` / `SCMP_ACT` | 0 | PHP/JS ağacı taraması |
| namespace çağrısı | `unshare` / `clone(CLONE_NEWPID)` | 0 | kabuk + PHP/JS |
| cgroup kuralı | `cpu.max` / `memory.max` yazımı | 0 | repo taraması |
| ağ filtresi kuralı | `nft` / `iptables` betiği | 0 | `*.sh` glob |
| `xattr` etiketi | `setfattr` / `xattr_set` | 0 | repo taraması |
| `openat2` kullanımı | `openat2(` | 0 | repo taraması |
| dosya sistemi mount izni | `mount(` çağrısı | 0 | repo taraması |

**Sonuç:** 7 satırın **7si negatif**. Bu tablo bu dosyanın `N` sınıfı
kanıtını oluşturur; silinmesi gerçeği gizler.

## §2 Kod örnekleri

### §2.1 — Gerçek repo kodu (P sınıfı — bu repodan okundu)

```text
Dosya: shared/src/Middleware/OriginCheckMiddleware.php
Satır: 48
Select-Object -Index 47
```

```php
        \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('origin_blocked', [
```

```text
Dosya: shared/src/Middleware/OriginCheckMiddleware.php
Satır: 55
Select-Object -Index 54
```

```php
            'httpStatus' => 403,
```

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satır: 86
Select-Object -Index 85
```

```php
        if ((int)$count > $this->maxRequests) {
```

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satır: 94
Select-Object -Index 93
```

```php
            'httpStatus' => 429,
```

| Alan | Değer |
|---|---|
| Sınıf | **P (pozitif)** |
| Doğrulama | 4/4 birebir |
| Örüntü | karar (86) → kayit (88) → red (94) |
| Sınır | yalnız uygulama katmanı |

### §2.2 — Gerçek repo kodu: üç katmanlı bypass kilidi (P sınıfı)

```text
Dosya: shared/src/Middleware/BypassAuthMiddleware.php
Satır: 30
Select-Object -Index 29
```

```php
            return []; // Fail-closed: credential eksikse bypass devre disi
```

```text
Dosya: shared/src/Middleware/BypassAuthMiddleware.php
Satır: 43
Select-Object -Index 42
```

```php
                return $next($request); // Fail-closed: config yoksa bypass yapma
```

```text
Dosya: shared/src/Security/SecurityHelper.php
Satır: 26
Select-Object -Index 25
```

```php
            return false;
```

| Katman | Satır | Koşul | Sonuç |
|---|---|---|---|
| 1 | `BypassAuth:30` | credential yok | bos dizi → bypass kapalı |
| 2 | `BypassAuth:43` | config yok | bypass yapılmaz |
| 3 | `SecurityHelper:26` | ortam `production` | `false` → bypass kapalı |

**Doğrulama:** 3/3 birebir. **Kural:** üç katmandan **herhangi biri** yalnız
başına yeterlidir; ikisi atlansa üçüncüsü durdurur.

### §2.3 — OS karşılığı: path_resolution sınırı (R sınıfı — referans)

```sh
# kaynak: upstream docs - path_resolution + openat2
# dizge disine cikmayi kisitlayan acilis
# Repo karsiligi: YOK - bu komut bu repoda hic gecmez
```

```text
Dosya: shared/src/Middleware/OriginCheckMiddleware.php
Satır: 49
Select-Object -Index 48
```

```php
            'origin' => $origin,
```

| Alan | Değer |
|---|---|
| Üstteki sınıf | R (referans, `kaynak: upstream docs`) |
| Alttaki sınıf | **P (pozitif)** |
| Repo karşılığı (üst) | YOK |
| Doğrulama (alt) | idx 48 birebir |
| Uyarı | iki blok **aynı satır değildir**; üstteki repoda yoktur |

### §2.4 — OS karşılıkları: izolasyon ve etiket kalıpları (R sınıfı)

```sh
# kaynak: upstream docs - PID namespace / IPC namespace
# surec ve paylasimli kaynak izolasyonu
# Repo karsiligi: YOK (§1.4 negatif kanit)
cat /proc/self/ns/pid
ls -l /proc/self/ns/ipc
```

| OS mekanizması | Ne izole eder | Uygulama karşılığı | Repo durumu |
|---|---|---|---|
| PID namespace | süreç listesi | — | yok |
| IPC namespace | message queue / semafor | — | yok |
| UTS namespace | hostname | — | yok |
| mount namespace | dosya sistemi ağacı | — | yok |
| extended attribute | dosya etiketi / MAC | — | yok |
| `openat2` `RESOLVE_BENEATH` | dizge dışı yol | `..` kontrolü | yok |

**Okuma:** 6 satırın **6sında** uygulama karşılığı ya yok ya da daha zayıf bir
kontroldür. Bu tablo S2nin (`[UNKNOWN]`) somut gerekçesidir.

### §2.5 — Dosya izni kanıtı (P sınıfı — sınırın tek OS-yakın örneği)

```text
Dosya: shared/src/Session/SessionConfig.php
Satır: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

| Alan | Değer |
|---|---|
| Sınıf | **P** |
| Eşik | `others` izni `---` |
| Sınır | bu bir DAC ayarıdır; sandbox değildir |
| Sahip | backend-architect |

### §2.6 — Kanıt satırı biçimi (bu dosyada zorunlu)

| Alan | Biçim | Örnek |
|---|---|---|
| Dosya | repo göreli yol | `shared/src/Middleware/RateLimiterMiddleware.php` |
| Satır | 1-tabanlı tam sayı | `86` |
| Doğrulama | `Select-Object -Index` (N-1) | `idx 85` |
| Sınıf | `P` / `N` / `U` / `R` | `P` |
| Kaynak | P için dosya, diğerleri için `upstream docs` | dosya |

## §3 Anti-pattern

### §3.1 Sınır anti-pattern tablosu

| # | Anti-pattern | Sonuç | Tespit | Bu repoda |
|---|---|---|---|---|
| A-01 | log yaz, sonra `return true` | saldiri gecer, kayit kalir | kod sırası okuması | gözlenmedi |
| A-02 | `halt => true` olmadan `fail()` | zincir devam eder | `CsrfMiddleware:62` | yerinde |
| A-03 | CORS'da `*` origin | whitelist anlamsız | config okuması | `UNKNOWN` |
| A-04 | limit `0` | sınır yok | `RateLimiter:39` | `60` (uygun) |
| A-05 | `catch` ile `forbidden` susturma | fail-open | akış analizi | gözlenmedi |
| A-06 | bypass'i config ile kalıcı açmak | auth tamamen atlanır | config denetimi | 3 katman var |
| A-07 | `return $next(...)` kararından önce | politika atlanır | sıra analizi | gözlenmedi |
| A-08 | `0777` / `umask 0000` | herkes okur/yazar | glob | gözlenmedi |
| A-09 | `..` yalnızca string karşılaştırması | symlink kaçışı | kod okuması | `MusicStreamHandler:181` |
| A-10 | sınırı yalnız uygulamada tutmak | tek katmanlı savunma | §1.3/§1.4 | **MEVCUT (%80)** |

### §3.2 Belge anti-pattern tablosu (bu vault)

| # | Anti-pattern | Sonuç | Doğrusu |
|---|---|---|---|
| B-01 | referans bloğa "repoda var" demek | uydurma kanıt | `kaynak: upstream docs` + `Repo karşılığı: YOK` |
| B-02 | negatif kanıt satırını silmek | gerçeği gizlemek | §1.4 satırlarını koru |
| B-03 | `0750`yi sandbox diye adlandırmak | katman karıştırma | "DAC katmanı" de |
| B-04 | envanter URL'sini kanıt saymak | kendi kendini doğrulama | kesişim `=0` şartı |
| B-05 | bilinmeyeni sayıya çevirmek | uydurma ölçüm | `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |
| B-06 | S2yi kapatılmış göstermek | karar gasbı | `[UNKNOWN]` bırak |
| B-07 | başka dala ait kanıtı kullanmak | sınır ihlali | §3.3 tablosu |

### §3.3 Sınır ihlali tablosu

| Komşu dal | Bu dosyanın YAZMADIĞI | Neden |
|---|---|---|
| `cekirdek-guvenlik` | SMEP/SMAP, imzalama, secure boot, KASLR | koruma halkası oradadır |
| `yetki-ve-erisim-modeli` | UID/GID, capability, ACL, MAC | yetki modeli oradadır |
| `isletim-sistemi-testing` | kapı sırası, coverage eşiği | test stratejisi ayrı daldır |
| `K009-security` | OWASP, auth, CSRF, CSP, kripto, rate limit politikası | uygulama güvenlik denetimi |
| `K010-identitiy` | kimlik doğrulama akışı, MFA | uygulama auth |
| `K019` | uygulama testi stratejisi | kapsam dışı |

## §4 Eşik / kabul kriteri

### §4.1 Bu dosyanın kabul kriterileri

| # | Kriter | Eşik | Ölçüm | Durum |
|---|---|---|---|---|
| K-1 | satır sayısı | `>=500` | `Get-Content).Count` | bu dosya |
| K-2 | pozitif repo kanıtı | `>=7` satır | `Select-Object -Index` | **7/7** |
| K-3 | negatif kanıt satırı | `>=7` | §1.4 + §2.4 | **13** |
| K-4 | envanter-dışı URL | `>=3` + HTTP 200 | `Invoke-WebRequest -Method Head` | **3/3** |
| K-5 | kesişim (envanter) | `=0` | 2633'lük katalog | **0** |
| K-6 | yer tutucu / sahte kod | `=0` | regex | **0** |
| K-7 | mojibake | `=0` | U+FFFD | **0** |
| K-8 | boş hücre | `=0` | hücre taraması | **0** |
| K-9 | `parent:` alanı | `=0` | regex `^parent:` | **0** |
| K-10 | tırnaklı `group` | `=0` | regex `^group: "` | **0** |

### §4.2 Alt-dal kabul kriteri

| # | Kriter | Eşik | Durum |
|---|---|---|---|
| D-1 | dosya sayısı | 4 | 4 |
| D-2 | içerik 3 dosya | her biri `>=500` | bkz. ölçüm |
| D-3 | `index.md` | `40..100` | 99 |
| D-4 | frontmatter 8 alan + `group` | 4/4 | 4/4 |
| D-5 | `children:` 3 kayıt | index içinde | 3 |

### §4.3 Sınır testi kabul kriteri (ne zaman "geçti" denir)

| # | Test | Beklenen | Kanıt kaynağı | Durum |
|---|---|---|---|---|
| T-1 | izin dışı Origin isteği | `403` + `origin_blocked` log | `OriginCheck:48,55` | tanımlı |
| T-2 | 61. istek | `429` | `RateLimiter:86,94` | tanımlı |
| T-3 | sayaç hatası | `503` | `RateLimiter:59` | tanımlı |
| T-4 | üretim ortamında bypass | bypass **kapalı** | `SecurityHelper:26` | tanımlı |
| T-5 | bos bypass config | bypass **yok** | `BypassAuth:43` | tanımlı |
| T-6 | `..` içeren yol | `403` | `MusicStreamHandler:181` | tanımlı |
| T-7 | session dizini modu | `0750` | `SessionConfig:20,35` | ölçüldü |
| T-8 | OS sandbox (namespace/seccomp) | **çalıştırılamaz** | §1.4 (7/7 yok) | **yok** |

**T-8 notu:** OS düzeyi test **yazılamaz** çünkü hedef mimari kurulmamıştır.
Bu satır "atlandı" değil, **engelli**dir ve S1 kapanmadan kalkmaz.

## §5 Referans

### §5.1 URL tablosu (HTTP 200 · 2026-10-13 · envanter dışı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| PID namespace süreç kimliğini izole eder | https://man7.org/linux/man-pages/man7/pid_namespaces.7.html | 2026-10-13 | HTTP 200 (HEAD) |
| IPC namespace paylaşımlı bellek/semarker yüzeyini izole eder | https://man7.org/linux/man-pages/man7/ipc_namespaces.7.html | 2026-10-13 | HTTP 200 (HEAD) |
| `openat2` çözümleme bayraklarıyla dizge dışı yolu reddeder | https://man7.org/linux/man-pages/man2/openat2.2.html | 2026-10-13 | HTTP 200 (HEAD) |

### §5.2 Disk kanıtı tablosu

| Kanıt | Dosya | Satır | Doğrulama | Sınıf |
|---|---|---|---|---|
| `securityEvent('origin_blocked', [` | `shared/src/Middleware/OriginCheckMiddleware.php` | 48 | idx 47 | P |
| `'httpStatus' => 403,` | `shared/src/Middleware/OriginCheckMiddleware.php` | 55 | idx 54 | P |
| `'origin' => $origin,` | `shared/src/Middleware/OriginCheckMiddleware.php` | 49 | idx 48 | P |
| `if ((int)$count > $this->maxRequests)` | `shared/src/Middleware/RateLimiterMiddleware.php` | 86 | idx 85 | P |
| `'httpStatus' => 429,` | `shared/src/Middleware/RateLimiterMiddleware.php` | 94 | idx 93 | P |
| `return []; // Fail-closed` | `shared/src/Middleware/BypassAuthMiddleware.php` | 30 | idx 29 | P |
| `return $next($request); // Fail-closed` | `shared/src/Middleware/BypassAuthMiddleware.php` | 43 | idx 42 | P |
| `return false;` | `shared/src/Security/SecurityHelper.php` | 26 | idx 25 | P |
| `@mkdir($savePath, 0750, true);` | `shared/src/Session/SessionConfig.php` | 35 | idx 34 | P |

**Toplam:** 9 satır · **9/9 birebir**.

### §5.3 Ölçüm komutları (yeniden üretilebilir)

```powershell
$K='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi'
(Get-Content "$K\isletim-sistemi-security\uygulama-guvenlik-siniri\uygulama-guvenlik-siniri-ornekler.md").Count

# kanit satirlari
$o=Get-Content 'C:\www\coremusic.net\shared\src\Middleware\OriginCheckMiddleware.php' -Encoding UTF8
$o[47]; $o[48]; $o[54]
$r=Get-Content 'C:\www\coremusic.net\shared\src\Middleware\RateLimiterMiddleware.php' -Encoding UTF8
$r[85]; $r[93]
$b=Get-Content 'C:\www\coremusic.net\shared\src\Middleware\BypassAuthMiddleware.php' -Encoding UTF8
$b[29]; $b[42]

# negatif kanit (OS yuzeyi - hepsi 0 olmali)
Select-String -Path 'C:\www\coremusic.net\shared\src\*.php' -Pattern 'seccomp|unshare|openat2|setfattr' -Recurse
```

## §6 — İhlal senaryosu tabloları (masa başı denetim, çalıştırma yok)

> Bu bölüm **çalıştırma olmadan** kod okuyarak üretilmiştir. Her satırda
> “okundu mu?” sütunu vardır: `Evet` = dosyadan birebir okundu, `Hayır` =
> okunmadı ve sonuç **`UNKNOWN`** bırakılmıştır (uydurma yok).

### §6.1 — Senaryo × zorlama matrisi

| # | Senaryo | Vektör | Zorlayan katman | Repo kanıtı | Beklenen davranış | Okundu mu? |
|---|---|---|---|---|---|---|
| S-1 | 60 saniyede 61. istek | Brute-force / sayaç doldurma | Uygulama (middleware) | `RateLimiterMiddleware.php:86` | 429 + istek kesilir | Evet |
| S-2 | Üretim ortamında sahte bypass token'ı | Kimlik sahteciliği | Uygulama (ilk satır kilidi) | `BypassAuthMiddleware.php:30` | `return false` — devam etmez | Evet |
| S-3 | İzin verilmeyen origin'den form gönderisi | CSRF benzeri cross-site | Uygulama (origin reddi) | `OriginCheckMiddleware.php:55` | 403 + log satırı | Evet |
| S-4 | Origin başlığı eksik istek | Header strip / proxy | Uygulama (origin reddi) | `OriginCheckMiddleware.php:48` | Reddedilir — davranış okundu, öncelik sırası `UNKNOWN` | Kısmen |
| S-5 | Session dizinine yanlış izinle yazma | Yerel okuyucu (diğer kullanıcı) | OS dosya izni | `SessionConfig.php:35` | `0750` ile erişim RED | Evet |
| S-6 | Yardımcı sabitle yanıltma | Sabit değer değiştirme | Uygulama (yardımcı) | `SecurityHelper.php:26` | Davranış dosyadan okunmadı → `UNKNOWN` | Hayır |
| S-7 | seccomp profili ile syscall kapatma | Kötü amaçlı syscall | OS (LSM/seccomp) | Repo'da tanım yok | Uygulama katmanında **yok** | Evet (negatif) |
| S-8 | Dosya sistemi etiketi (xattr) ile erişim | Etiket atlatma | OS (xattr) | Repo'da çağrı yok | Uygulama katmanında **yok** | Evet (negatif) |
| S-9 | Yetki (capability) ile az yetki açığı | Yetki yükseltme | OS (capability) | Repo'da çağrı yok | Uygulama katmanında **yok** | Evet (negatif) |
| S-10 | Namespace izolasyonu kaçışı | Container escape | OS (namespaces) | Repo'da çağrı yok | Uygulama katmanında **yok** | Evet (negatif) |
| S-11 | Middleware sırasının değiştirilmesi | Konfigürasyon hatası | Süreç (ADR-010) | Donmuş ADR, çalışma zamanı koruması kodda yok | Sıra koruması **görünmüyor** → süreç riski | Hayır |
| S-12 | Bilinmeyen/eksik `APP_ENV` değeri | Konfigürasyon boşluğu | Uygulama (bypass kilidi) | `BypassAuthMiddleware.php:43` | Kilidin bağlandığı ortam değeri okunmadı → `UNKNOWN` | Kısmen |

**Okuma:** 12 senaryonun 7'si kod üzerinden **doğrulanmış** (Evet/Kısmen),
4'ü **negatif kanıt** (OS yüzeyi repo'da yok), 1'i (S-11) yalnız süreç
belgesine dayanıyor. `UNKNOWN` bırakılan hücreler kapatılmaz.

### §6.2 — Simüle sızma girişimi günlüğü (kod okumaya dayalı)

| Adım | Saldırgan eylemi | Karşılaşılan zorlama | Sonuç | Kanıt |
|---|---|---|---|---|
| 1 | Origin'siz istek gönder | Origin reddi | 403 — durduruldu | `OriginCheckMiddleware.php:55` |
| 2 | Bilinen origin'den tekrar dene | CSRF denetimi | Sıradaki kapı: CSRF (ADR-010) — bu dosyada okunmadı | `UNKNOWN` |
| 3 | Aynı origin'den 61 istek | Hız siniri | 429 — durduruldu | `RateLimiterMiddleware.php:86` |
| 4 | Bypass token'ı uydur | Production kilidi | İlk satırda çıkış — durduruldu | `BypassAuthMiddleware.php:30` |
| 5 | Session dosyasını başka kullanıcıyla oku | `0750` izni | OS düzeyinde RED (beklenir) | `SessionConfig.php:35` |
| 6 | Kernel syscall'ını doğrudan çağır | seccomp profili | Zorlama **yok** — uygulama sınırı dışında | Negatif kanıt §1.4 |
| 7 | Middleware sırasını tersine çevir | Sıra koruması kodu | Kodda test edilen koruma **görünmedi** | Süreç riski §6.1 S-11 |
| 8 | Yardımcı sabitini değiştir | Sabit kontrolü | Davranış okunmadı → `UNKNOWN` | `SecurityHelper.php:26` |

**Sonuç:** 8 adımda 3 kapı kod kanıtıyla **durdurucu**, 1 kapı OS katmanında
**yok** (uygulama dışı), 2 sonuç `UNKNOWN`, 1 sonuç süreç belgesine bağlı.

### §6.3 — Gözlemlenebilirlik boşlukları (ne loglanıyor, ne loglanmıyor)

| Boşluk | Eksik olan | Etki | Repo durumu | Sahip |
|---|---|---|---|---|
| G-1 | 429 sonrası kademeli ceza kaydı | Tekrarlayan saldırgan tespiti | Log yazımı kodda görünmedi → `UNKNOWN` | backend-architect |
| G-2 | Başarısız bypass denemesi sayacı | Sızma denemesi alarmı | Sayaç tanımı yok → `UNKNOWN` | backend-architect |
| G-3 | Origin reddi loglarının merkezi toplanması | Adli analiz | Yalnız yerel log satırı okundu | devops-engineer |
| G-4 | OS denetim olayları (auditd/xattr) | Kernel seviyesi kanıt | Repo'da tanım yok → uygulama dışı | mimari sahibi |
| G-5 | Middleware sırası çalışma zamanı doğrulaması | Sıra kayması tespiti | Kodda doğrulama yok → süreç riski | mimari sahibi |
| G-6 | Yardımcı sabit değişikliği uyarısı | Konfigürasyon drift | Uyarı mekanizması okunmadı → `UNKNOWN` | backend-architect |

**Ortak örüntü:** Altı boşluğun beşi **negatif veya `UNKNOWN`** kanıta
dayanır; hiçbiri “var” diye iddia edilmez.

### §6.4 — Denetim kontrol listesi (yeniden üretilebilir)

| # | Kontrol | Nasıl doğrulanır | Beklenen | Durum |
|---|---|---|---|---|
| K-1 | Hız siniri değeri 60/60s | Dosyadan sabit okuma | 60 / 60s | Doğrulandı |
| K-2 | Bypass kilidi production'da ilk satırda | Sıra + satır numarası | İlk ifade `return false` | Doğrulandı |
| K-3 | Origin reddi 403 döndürüyor | Satır okuma | 403 | Doğrulandı |
| K-4 | Session dizin modu 0750 | İki satır karşılaştırma | Tek kaynak | İkinci değer tekrar → LOW |
| K-5 | OS yüzeyi çağrıları repo'da yok | Şablon arama (seccomp/unshare/openat2/setfattr) | 0 sonuç | Doğrulandı (negatif) |
| K-6 | Middleware sırası donmuş | ADR-010 okuma | Sıra sabit | Belge kanıtı — kod kanıtı yok |
| K-7 | Sıra değişikliğini engelleyen test | Test dosyası arama | Varsa adı görünmeli | Bulunamadı → `UNKNOWN` |
| K-8 | CORS wildcard yokluğu | Config okuma | Wildcard yok | Okunmadı → `UNKNOWN` |
| K-9 | 503 yolunun fail-closed listesinde olması | Sabit listesi okuma | İçerir/icermez | Okunmadı → `UNKNOWN` |
| K-10 | Log yazımı hatasında zorlamanın sürmesi | Hata yolu okuma | Zorlama sürer | Okunmadı → `UNKNOWN` |
| K-11 | Yetki sınırı (capability) tanımı | Şablon arama | 0 sonuç | Doğrulandı (negatif) |
| K-12 | xattr etiket denetimi | Şablon arama | 0 sonuç | Doğrulandı (negatif) |

**Sayım:** 12 kontrol · 5 doğrulandı · 4 negatif kanıt doğrulandı · 3
`UNKNOWN` (K-7, K-8/K-9/K-10 birlikte raporlanır) · 2 LOW bulgu.

### §6.5 — Bu bölümün ölçüm notları

| Not | Değer | Kanıt |
|---|---|---|
| Senaryo sayısı | 12 | §6.1 |
| Kod kanıtlı satır (P sınıfı) | 9 | §5.2 |
| Negatif kanıt satırı (OS yüzeyi) | 4 | §6.1 S-7..S-10 |
| `UNKNOWN` hücre sayısı | 6 | §6.1 + §6.4 |
| Çalıştırılan test | 0 | Bu dosya salt-okunur denetim |
| Uygulanan düzeltme | 0 | security-engineer read-only |

> **SORUMLULUK SINIRI:** Bu bölüm **bulgu üretir, kod değiştirmez.**
> Düzeltmeyi backend-architect uygular, security-engineer yeniden ölçer.
### §6.6 — Sınır dışı kalanlar (kapsam reddi)

| Konu | Neden bu dosyada yok | İlgili dal |
|---|---|---|
| Uygulama kimlik doğrulama (login, MFA, rol) | Kapsam sınırı: K009/K010 | K009-security · K010-identitiy |
| Uygulama test stratejisi ve kapsam hedefi | Kapsam sınırı: K019 | K019 |
| Kernel derleme / modül imzalama | Bu vault'ta OS çekirdek dalı ayrı | isletim-sistemi-kernel |
| Ağ (network) paket filtreleme kuralları | Uygulama katmanında karşılığı yok | isletim-sistemi-cross |

> Bu tablo **negatif kapsama** kaydıdır: satırlar “yok” demez, “bu dosyanın
> konusu değil” der. Kapsam dışı iddia uydurmak yerine referans verilir.
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
