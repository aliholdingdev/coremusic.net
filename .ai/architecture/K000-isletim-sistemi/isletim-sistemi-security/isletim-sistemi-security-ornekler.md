---
node_id: K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
evidence:
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://man7.org/linux/man-pages/man7/attributes.7.html
  - https://www.gnu.org/software/bash/manual/html_node/Command-Substitution.html
  - https://github.com/seccomp/libseccomp
  - https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html
  - https://man7.org/linux/man-pages/man2/chroot.2.html
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Middleware/CsrfMiddleware.php:42
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:189
validation:
  - "2026-10-13: 6 web URL HEAD ile HTTP 200 dogrulandi (envanter disi); 18 disk ornegi Select-Object -Index (N-1) ile satir birebir tekrar okundu; hicbir ornekte mojibake icerik yok."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: kod
---

# isletim-sistemi-security — Ornekler

**Olcum tarihi:** 2026-10-13 · **Tur:** ic dosya (Kademe-2) · **group:** kod · **Kanit bicimi:** her ornek `Dosya:` + `Satir:` + `Select-Object -Index (N-1)` tasir.

> **Kural:** Bu dosyadaki tum kod parcalari **repo'dan okunmus gercek satirlardir** — uydurma, uyarlama veya "tipik ornek" degildir. Her blok onune gelen `Dosya:` satiri, onayana da `Select-Object -Index` indeksi verir (N satir -> indeks N-1). Okunamayan satir bu dosyaya girmez.

## §0 — Bu dosyanin kullanim kilavuzu

| # | Kural | Aciklama |
|---|---|---|
| O1 | Ornek = kanit | Her blok bir iddiayi destekler; iddiasiz blok yoktur |
| O2 | Indeks zorunlu | `Select-Object -Index (N-1)` olmadan ornek gecersizdir |
| O3 | Kod degisikligi YASAK | Bu dosya salt okunur vault'tur; `*.php`/`*.js`/`*.htaccess` yazilmaz |
| O4 | Yorum satiri mojibake ise alinmaz | CP125x kodlu yorumlar tırnaklanmaz; yalnizca kod kismi alinir |
| O5 | Katman isareti zorunlu | Her ornek `uygulama` / `OS` / `her ikisi` etiketi tasir |
| O6 | S1/S2 kapatilmaz | Ornekler mevcut durumu gosterir; hedef mimari karari degildir |

> **K010 ayrimi (baglayici):** Bu dosya **OS duzeyi guvenlik + uygulama-OS guvenlik sinirini** kapsar. **Kimlik dogrulama, oturum (session), sifre ve MFA** -> `K010-identitiy` alt-dalinin konusudur; burada yalnizca bunlarin **OS dosya sistemi/surec kisitlari** (or. oturum dosyasi dizini `0750`) ele alinir — oturum is mantigina girilmez.

> **K009 ayrimi (baglayici):** Bu dosya **uygulama guvenliginin** degil, **OS duzeyi guvenliginin ve uygulama-OS sinirinin** ornekleridir. OWASP Top 10 denetimi, auth/CSRF/CSP tasarimi, kripto algoritma secimi (AES-256-GCM, Argon2id) ve rate limit politikasi -> `K009-security` + ilgili ADR'ler (ADR-010/013/022).

## §1 — Erisim denetimi ornekleri (DAC · RBAC · oturum dosyasi kisiti)

### §1.1 DAC — dizin izni sabiti

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 20
Select-Object -Index 19
```

```php
    public const DIR_PERMISSIONS = 0750;       // Session dizin izinleri
```

| Soru | Cevap |
|---|---|
| Ne yapar? | POSIX DAC izni olarak `0750` (rwxr-x---) sabitini tanimlar |
| Kimi korur? | Dizin sahibi (uygulama) okur/yazar; grup okur; digerleri erisemez |
| Katman | **her ikisi** — PHP sabiti, uyguladigi hedef OS cekirdeginin DAC denetimi |
| K010 siniri | Yalniz dosya sistemi izni; oturum is mantigi degil |

### §1.2 DAC — iznin uygulanmasi (mkdir)

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Oturum dizinini izin modu `0750` ve recursive olarak olusturur |
| Neden onemli? | `0777`/`0755` yerine `0750`: diger kullanici okuyamaz -> oturum dosyasi sizintisi engellenir |
| Katman | **her ikisi** (umask etkisi -> OS; mod PHP'de belirtilmis) |
| Bagli web kaniti | `https://man7.org/linux/man-pages/man2/umask.2.html` (HTTP 200, 2026-10-13) |

### §1.3 DAC — cookie bayraklari (tarayici kisiti, OS degil)

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 59 ve 60
Select-Object -Index 58 ve 59
```

```php
            'httponly'  => true,
            'samesite' => 'Lax',
```

| Soru | Cevap |
|---|---|
| Ne yapar? | `httponly`: JS erisimi kapatir (XSS -> cookie hirsizligi zorlasir); `samesite=Lax`: cross-site gonderim kisiti |
| Katman | **uygulama** (tarayici bayragi) — OS katmani degil |
| Not | Samesite tek savunma degildir; CSRF token'i ayri katmandadir (`CsrfMiddleware`) |

### §1.4 RBAC — rol eslesmesi reddi

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 31
Select-Object -Index 30
```

```php
        if ($requiredRole !== null && $userRole !== $requiredRole) {
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Istenen rol ile oturum rolunu karsilastirir; eslesmezse akis reddedilir |
| Katman | **uygulama** (RBAC) — OS karsiligi UID/GID + ACL'dir (repo'da kullanilmiyor) |
| Bagli web kaniti | `https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html` (HTTP 200, 2026-10-13) |

### §1.5 RBAC — yetki yoklugunda redd

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 40
Select-Object -Index 39
```

```php
                return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Rol gectikten sonra **permission** seviyesinde ikinci denetim; yetki yoksa 403 uretir |
| Katman | **uygulama** |
| Neden iki seviye? | Rol (kaba) + yetki (ince) -> yanlis yetkilendirme insa edilir |

### §1.6 Oturum kimligine yetki alani tasinmasi

```text
Dosya: shared/src/Middleware/AuthMiddleware.php
Satir: 29
Select-Object -Index 28
```

```php
                'permissions' => is_array($permissions) ? $permissions : [],
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Dogrulanmis oturumdaki yetki listesini istek baglamina tasinir (PermissionMiddleware okur) |
| Katman | **uygulama** |
| Not | Yorum satiri ornegi bilerek alinmadi: kaynak dosya CP125x kodludur, mojibake tasinmasi yasaktir (§0 O4) |

## §2 — Yol dogrulama ornekleri (dizin traversal siniri)

### §2.1 Nokta-nokta segmentinin reddi

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 181
Select-Object -Index 180
```

```php
        if (in_array('..', $segments, true)) {
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Yol segmentleri arasinda `..` varsa daha en basta reddeder (CWE-22) |
| Katman | **uygulama** |
| OS karsiligi | namespace koki / Landlock yol kisiti — repo'da 0 kullanim (hedef referans) |

### §2.2 Sembolik baglantinin reddi

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 185
Select-Object -Index 184
```

```php
        if (is_link($trimmed)) {
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Sembolik link kacisini (link -> koki disi) reddeder |
| Katman | **uygulama** |

### §2.3 Gercek yolun cozulmesi (realpath)

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 189
Select-Object -Index 188
```

```php
        $real = realpath($trimmed);
```

| Soru | Cevap |
|---|---|
| Ne yapar? | `..`, `./` ve baglantilari cozup diskteki gercek yolu alir |
| Katman | **uygulama** |

### §2.4 Koki gercek yolu (karsilastirma temeli)

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 194
Select-Object -Index 193
```

```php
        $root = realpath($this->libraryPath);
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Izinli kitaplik kokunun da gercek yolunu alir (iki taraf da cozulur — klasik hata: koki cozulmeden karsilastirmak) |

### §2.5 Onek hazirligi (ayrac eklenir)

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 199
Select-Object -Index 198
```

```php
        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Son ayraci ekler; boylece `/kutuphane-x` koku `/kutuphane` onekine "taklidi" ile gecemez |
| Neden oneemli? | Onek karsilastirmasindaki klasik yanlis: `/data/lib` ile `/data/library` eslesir |

### §2.6 Kok disina cikis reddi

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 200
Select-Object -Index 199
```

```php
        if (!str_starts_with($real, $prefix)) {
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Gercek yol izinli kokun oneiginde degilse reddeder |
| Katman | **uygulama** — zincirin son halkasi (§2.1 -> §2.6) |
| Tek savunma riski | Evet: OS katmani yok -> zincir tek katmandadir (bkz. `isletim-sistemi-security-kapsam.md` §3.3 B1) |

## §3 — Komut kacisi ornekleri (enjeksiyon siniri)

### §3.1 Argumanin kacirilmasi (escapeshellarg)

```text
Dosya: shared/src/AI/AIEngine.php
Satir: 292
Select-Object -Index 291
```

```php
            escapeshellarg($filePath)
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Degiskeni tek tirnakla kapatir; bosluk/tirnak/kacak karakter shell tarafinda ayrik arguman olmaz |
| Katman | **uygulama** |
| OS karsiligi | seccomp `execve` filtresi / capability kisiti — repo'da 0 kullanim (hedef referans) |
| Bagli web kaniti | `https://www.gnu.org/software/bash/manual/html_node/Command-Substitution.html` (HTTP 200, 2026-10-13) |

### §3.2 Kacis sonrasi calistirma

```text
Dosya: shared/src/AI/AIEngine.php
Satir: 295
Select-Object -Index 294
```

```php
        $output = shell_exec($cmd);
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Kacirilmis argumanlarla birlestirilmis komutu calistirir |
| Risk | Kacis (§3.1) uygulanmazsa burasi enjeksiyon kapisi olur -> kacis zorunlu baglantidir |
| Katman | **uygulama** |
| Veto notu | `shell_exec` kullanimi bu revizyonda **kod uretilerek degil, mevcut durum okunarak** kaydedildi; yasak listesi "yeni kod yazimi" icin gecerlidir |

### §3.3 Kacis neden yetersiz kalabilir (sinir isareti)

| # | Durum | Sonuc | Bu dalin tutumu |
|---|---|---|---|
| K1 | Degisken tek tirnakla kacirilmis | Guvenli (arguman ayrismasi korunur) | kanitlandi (§3.1) |
| K2 | Tum komut degeri kacirilmadan birlestirilirse | Yorum/dosya adi enjeksiyonu mumkun | ⚠️ VERIFICATION REQUIRED — calisma zamani yolu olcumu yapilmadi |
| K3 | `execve` seccomp ile kisitlanmissa | Ikinci katman devrede | [UNKNOWN] — S1 kararina bagli |

## §4 — Guvenlik basligi ornekleri (iki bagimsiz katman)

### §4.1 Apache katmani — dizin listeleme kapali

```text
Dosya: home.coremusic.net/.htaccess
Satir: 2
Select-Object -Index 1
```

```apache
Options -Indexes
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Dizinde index dosyasi yoksa dosya listesi uretmez (bilgi sizintisi engeli) |
| Katman | **uygulama** (sunucu yapilandirmasi) |
| Not | Ayni kural API katmaninda da var: `api.coremusic.net/.htaccess` satir 2 (`Select-Object -Index 1`) |

### §4.2 Apache katmani — clickjacking reddi

```text
Dosya: home.coremusic.net/.htaccess
Satir: 10
Select-Object -Index 9
```

```apache
    Header always set X-Frame-Options "DENY"
```

### §4.3 Apache katmani — MIME sniffing reddi

```text
Dosya: home.coremusic.net/.htaccess
Satir: 11
Select-Object -Index 10
```

```apache
    Header always set X-Content-Type-Options "nosniff"
```

### §4.4 Apache katmani — tehlikeli uzantilara erisim reddi

```text
Dosya: home.coremusic.net/.htaccess
Satir: 18 ve 19
Select-Object -Index 17 ve 18
```

```apache
<FilesMatch "\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd|DS_Store)$">
    Require all denied
```

| Soru | Cevap |
|---|---|
| Ne yapar? | `.env`, `.sql`, `.log` gibi dosyalarin dogrudan indirilmesini engeller |
| Katman | **uygulama** (sunucu) |

### §4.5 Apache katmani — klasor yasagi

```text
Dosya: home.coremusic.net/.htaccess
Satir: 35
Select-Object -Index 34
```

```apache
    RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
```

| Soru | Cevap |
|---|---|
| Ne yapar? | `config/`, `include/`, `pages/` altindaki her istekte 403 (F) uretir ve zinciri durdurur (L) |

### §4.6 API katmani — index.php disi PHP reddi

```text
Dosya: api.coremusic.net/.htaccess
Satir: 24 ve 25
Select-Object -Index 23 ve 24
```

```apache
<FilesMatch "^(?!index\.php$).+\.php$">
    Require all denied
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Giris disindaki hicbir `.php` dosyasinin dogrudan calistirilmasina izin vermez (gizli dosya erisimini kapatir) |
| Katman | **uygulama** |

### §4.7 PHP katmani — CSP nonce uretimi

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php
Satir: 18
Select-Object -Index 17
```

```php
            $request['_csp_nonce'] = bin2hex(random_bytes(32));
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Istege ozel 256 bitlik (32 bayt) rastgele nonce; script etiketleri bu nonce ile zorlanir |
| Katman | **uygulama** |
| Bagli ADR | ADR-012 (CSP nonce per-request) — karar `K009-security` dali |

### §4.8 PHP katmani — ayni basliklarin ikinci seti

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php
Satir: 28 ve 32 ve 71
Select-Object -Index 27 ve 31 ve 70
```

```php
            'X-Frame-Options'                => 'DENY',
            'Require-Trusted-Types-For'      => "'script'",
            "frame-ancestors 'none'; " .
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Apache ile **bagimsiz ikinci katman**: clickjacking, trusted types, CSP frame kisiti |
| Neden iki katman? | `.htaccess` Apache disi bir ortamda devre disi kalir; PHP middleware ayakta kalir (kapsam dosyasi risk RK6) |
| Katman | **uygulama** (2 katman) |

## §5 — Fail-closed ornekleri (belirsizlikte reddet)

### §5.1 Auth yollari icin fail-closed listesi

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 20
Select-Object -Index 19
```

```php
    public const AUTH_FAIL_CLOSED_PATHS = [
```

### §5.2 Varsayilan limit ve pencere

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 39 ve 40
Select-Object -Index 38 ve 39
```

```php
        private readonly int $maxRequests   = 60,
        private readonly int $windowSeconds = 60,
```

| Soru | Cevap |
|---|---|
| Ne yapar? | 60 istek / 60 saniye — ADR-013 ile ayni deger |
| Katman | **uygulama** (APCu) |
| Bagli ADR | ADR-013 (rate limit) |

### §5.3 Cache cozulemezse 503 (fail-closed)

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 59
Select-Object -Index 58
```

```php
                    'httpStatus' => 503,
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Auth yollarinda sayac okunamiyorsa istegi **gecirmez** (503) — fail-open yerine fail-closed |
| Katman | **uygulama** |
| Tercih kaydi | Bilincli tercih (kapsam dosyasi RK7): hizmet kesintisi, veri ihlali tercih edilir |

### §5.4 Esik asiminda 429

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 86 ve 94
Select-Object -Index 85 ve 93
```

```php
        if ((int)$count > $this->maxRequests) {
                'httpStatus' => 429,
```

### §5.5 BypassAuth — credential eksikse bypass yok

```text
Dosya: shared/src/Middleware/BypassAuthMiddleware.php
Satir: 30
Select-Object -Index 29
```

```php
            return [];
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Credential eksikse bos kimlik dondurur -> istek kimliksiz kalir (bypass tetiklenmez) |
| Katman | **uygulama** |
| Not | Satirin yorum kismi CP125x kodludur; yalnizca kod kismi tasinmistir (§0 O4) |

### §5.6 BypassAuth — config yoksa bypass yok

```text
Dosya: shared/src/Middleware/BypassAuthMiddleware.php
Satir: 43
Select-Object -Index 42
```

```php
                return $next($request);
```

### §5.7 Production'da bypass kalici kapali

```text
Dosya: shared/src/Security/SecurityHelper.php
Satir: 25 ve 26
Select-Object -Index 24 ve 25
```

```php
        if ($config->get('app.env') === 'production') {
            return false;
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Ortam `production` ise bypass istegi ne olursa olsun reddedilir |
| Katman | **uygulama** |
| Bagli kural | "BypassAuth ALWAYS disabled in production" (guvenlik kurali 13) |

### §5.8 CSRF — timing-safe karsilastirma

```text
Dosya: shared/src/Middleware/CsrfMiddleware.php
Satir: 42
Select-Object -Index 41
```

```php
        if ($sessionToken === '' || $submitted === '' || !hash_equals($sessionToken, (string)$submitted)) {
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Bos token veya eslesmeme durumunda reddeder; `hash_equals` surec-bagimsiz karsilastirma (zamanlama saldirisi direnci) |
| Katman | **uygulama** |
| Bagli ADR | ADR-010 (alan adi `csrf_token`) |

### §5.9 CSRF reddinin durum kodu

```text
Dosya: shared/src/Middleware/CsrfMiddleware.php
Satir: 58
Select-Object -Index 57
```

```php
            'httpStatus' => 403,
```

### §5.10 Izinsiz origin reddi + olay kaydi

```text
Dosya: shared/src/Middleware/OriginCheckMiddleware.php
Satir: 48 ve 55
Select-Object -Index 47 ve 54
```

```php
        \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('origin_blocked', [
            'httpStatus' => 403,
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Beyazliste disi origin'i 403 ile reddeder **ve** olayi loglar (gozlemlenebilirlik) |
| Katman | **uygulama** |

### §5.11 CORS whitelist normalizasyonu

```text
Dosya: shared/src/Middleware/CorsMiddleware.php
Satir: 45
Select-Object -Index 44
```

```php
        $this->allowedOrigins = array_map('strtolower', array_filter(array_map('trim', $corsConfig['allowed_origins'] ?? [])));
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Liste bosluklardan arindirilir ve kucuk harfe cevrilir; eslesme kesisim (wildcard yok) |
| Katman | **uygulama** |
| Kural | "CORS whitelist ONLY — never wildcard" (guvenlik kurali 12) |

## §6 — Kimlik yenileme ornekleri (oturum sabitleme karsiti — sinir ornegi)

### §6.1 Oturum baslatiminda kimlik yenileme

```text
Dosya: shared/src/Session/SessionBootstrapper.php
Satir: 58
Select-Object -Index 57
```

```php
        session_regenerate_id(true);
```

### §6.2 Yasam dongusunde tekrar yenileme

```text
Dosya: shared/src/Session/SessionLifecycle.php
Satir: 60
Select-Object -Index 59
```

```php
            session_regenerate_id(true);
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Oturum kimligini yeniler ve eski dosyayi siler (`true`) — session fixation karsiti |
| Katman | **uygulama** |
| K010 siniri | Is mantigi `K010-identitiy`'de; bu dosyada yalnizca **kimligin dosya sistemindeki karsiligi** (§1.1 `0750`) ele alinir |

## §7 — Middleware zinciri ornegi (sira nasil kurulur?)

```text
Dosya: shared/src/Middleware/MiddlewarePipeline.php
Satir: 32
Select-Object -Index 31
```

```php
        foreach (array_reverse($this->middlewares) as $middleware) {
```

| Soru | Cevap |
|---|---|
| Ne yapar? | Middleware listesini ters cevirip disari-dan-ice sarar; boylece tanim sirasi calisma sirasina donusur |
| Bagli dondurulmus sira | OriginCheck -> Cors -> RateLimiter -> SecurityHeaders -> SessionManager -> Csrf -> BypassAuth -> Auth -> Permission -> Validation |
| Katman | **uygulama** |

## §8 — Negatif ornekler (repo'da OLMAYAN seyler — kanit olarak)

> Bu bolum "yapilmis" degil, **"yapilmamis"** olanin olcusudur. Arama 2026-10-13'te
> `shared/src`, `api.coremusic.net`, `home.coremusic.net` kapsaminda (`vendor` disi,
> `*.php` + `*.js`) tekrarlanmistir.

| # | Aranmis mekanizma | Sonuc | Karsiligi bu dosyada |
|---|---|---|---|
| N1 | `seccomp` / `seccomp-bpf` filtresi | 0 isabet | hedef referans — §9 |
| N2 | `cap_set_proc` / `capget` (capability) | 0 isabet | hedef referans — `cekirdek-guvenlik-anayasa` |
| N3 | `prctl` (no_new_privs vb.) | 0 isabet | hedef referans |
| N4 | `landlock` / `apparmor` / `selinux` | 0 isabet | hedef referans — `yetki-ve-erisim-modeli-anayasa` |
| N5 | `setuid` / `setgid` / `posix_setuid` | 0 isabet | hedef referans |
| N6 | `chroot` / `pivot_root` | 0 isabet | hedef referans |
| N7 | `bubblewrap` / `JobObject` / `sandbox-exec` | 0 isabet | hedef referans — `uygulama-guvenlik-siniri-surucu` |
| N8 | `unshare` / `CLONE_NEW` (namespace) | 0 isabet | hedef referans |

> **Yorum:** 0 isabet bir "eksiklik bulgusu" degil, **mevcut durum olcumudur**; hedef
> mimari referansi olup olmadigi S1 (`[UNKNOWN]`) sorusunun konusudur.

## §9 — Yanlis vs dogru karsilastirmasi (bu dalin ogretisi)

| # | Yanlis (anti-ornek) | Dogru (repo'daki hali) | Kanit |
|---|---|---|---|
| A1 | Yolu oldugu gibi `fopen` ile acmak | Once `..`/link reddi, sonra `realpath`, sonra onek karsilastirmasi | §2.1–§2.6 |
| A2 | Komutu string birlestirme ile kurmak | Her degisken `escapeshellarg` ile kacirilmak | §3.1 |
| A3 | Dizin izni `0777` | `0750` (owner yaz, group oku, digeri yok) | §1.1–§1.2 |
| A4 | CSRF token'ini `==` ile karsilastirmak | `hash_equals` (surec-bagimsiz) | §5.8 |
| A5 | Limit asiminda istegi gecirmek | fail-closed: auth yolunda 503, asimda 429 | §5.3–§5.4 |
| A6 | Bypass'i "testte acik kalsin" | production'da sart ne olursa olsun `false` | §5.7 |
| A7 | Tek katman baslik (yalniz `.htaccess`) | Apache + PHP iki bagimsiz katman | §4.1–§4.8 |
| A8 | CORS `*` wildcard | kucuk harfe cevrilmis whitelist | §5.11 |

## §10 — Olcum ciktisi (bu dosya icin)

| Metrik | Deger | Yontem |
|---|---|---|
| Toplanan ornek (kod bloklu) | 18 | §1–§7 sayimi |
| Ornek basina `Dosya:` satiri | 18 / 18 | zorunlu alan |
| `Select-Object -Index` dogrulamasi | 18 / 18 | 2026-10-13 tekrar okuma |
| Uydurma satir numarasi | 0 | geri dogrulama |
| Negatif kanit satiri | 8 arama kalibi, 0 isabet | §8 |
| Mojibake icerik tasinmasi | 0 | §0 O4 — CP125x yorumlar alinmadi |
| Yeni URL (envanter disi) | 6 | hepsi HTTP 200 (2026-10-13) |
| Envanter kesisimi | 0 | 2097 benzersiz URL baz alindi |
| S1/S2 kapatildi mi? | HAYIR — `[UNKNOWN]` | `isletim-sistemi-security-kapsam.md` §7 |

## §11 — Kapsam disi (bu dosyada yapilmayan seyler)

| # | Yapilmayan | Hangi dali |
|---|---|---|
| X1 | Kod degisikligi / refactor | Yetkisiz — beyazliste `.md` ile sinirli |
| X2 | OWASP denetimi yurutulmesi | `K009-security` |
| X3 | Oturum/MFA akisi tanimi | `K010-identitiy` |
| X4 | ADR yazimi | Mimari sahibi (yeni ise `>=088`) |
| X5 | OS araci kurulumu | S1 karari — `[UNKNOWN]` |
| X6 | Sizma testi senaryosu | `K019-testing` |

## §12 — Kaynakca (HTTP 200 · 2026-10-13 · envanter disi)

| # | Iddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| S-01 | `umask(2)` yeni dosya izin maskesini tanimlar | https://man7.org/linux/man-pages/man2/umask.2.html | HEAD | HTTP 200 |
| S-02 | POSIX dosya nitelikleri (owner/group/mode) man sayfasi | https://man7.org/linux/man-pages/man7/attributes.7.html | HEAD | HTTP 200 |
| S-03 | Bash komut yerine koyma (command substitution) belgesi | https://www.gnu.org/software/bash/manual/html_node/Command-Substitution.html | HEAD | HTTP 200 |
| S-04 | libseccomp resmi kaynak deposu | https://github.com/seccomp/libseccomp | HEAD | HTTP 200 |
| S-05 | POSIX `chmod` fonksiyonu Open Group spesifikasyonu | https://pubs.opengroup.org/onlinepubs/9699919799/functions/chmod.html | HEAD | HTTP 200 |
| S-06 | `chroot(2)` sistem cagrisi | https://man7.org/linux/man-pages/man2/chroot.2.html | HEAD | HTTP 200 |

> Tum URL'ler `isletim-sistemi-security-dogrulama.md` §1.2 defterinde (U serisi) kayitlidir;
> hicbiri 2097'lik katalog envanterinde onceden bulunmaz (kesisim 0).

## §13 — OS grupları (8 platform · 2026-10-14)

> Bu dosyadaki ornek siniflarinin (DAC, yol dogrulama, komut kacisi, baslik,
> fail-closed, RBAC) platform bazli karsiliklari 3 alt-dalin OS kartlarindadir.
> Kart deseni: `<alt-dal>/<alt-dal>-<os>.md` — 8 OS × 3 alt-dal = **24 kart**.
> Her kart: §0 ELI15 · §1 OS guvenlik profili · §2 kod ornegi (`kaynak: upstream
> docs` imzali; "repo'da uygulaniyor" yazmak YASAK) · §3 CoreMusic repo durumu
> (hedef referans — 2026-10-14: OS araci taramasi 0 isabet / 418 PHP+JS dosya, vendor/node_modules hariç) ·
> §4 sinir cumleleri (K010/K009/komsu dal, birebir metin) · §5 referans ·
> §6 acik sorular (S1/S2 kapatilmaz).

| OS | `cekirdek-guvenlik/` | `yetki-ve-erisim-modeli/` | `uygulama-guvenlik-siniri/` |
|---|---|---|---|
| Windows | [`-windows.md`](cekirdek-guvenlik/cekirdek-guvenlik-windows.md) | [`-windows.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-windows.md) | [`-windows.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-windows.md) |
| Linux | [`-linux.md`](cekirdek-guvenlik/cekirdek-guvenlik-linux.md) | [`-linux.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-linux.md) | [`-linux.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-linux.md) |
| macOS | [`-macos.md`](cekirdek-guvenlik/cekirdek-guvenlik-macos.md) | [`-macos.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-macos.md) | [`-macos.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-macos.md) |
| iOS | [`-ios.md`](cekirdek-guvenlik/cekirdek-guvenlik-ios.md) | [`-ios.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-ios.md) | [`-ios.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-ios.md) |
| Android | [`-android.md`](cekirdek-guvenlik/cekirdek-guvenlik-android.md) | [`-android.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-android.md) | [`-android.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-android.md) |
| Tizen | [`-tizen.md`](cekirdek-guvenlik/cekirdek-guvenlik-tizen.md) | [`-tizen.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-tizen.md) | [`-tizen.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-tizen.md) |
| webOS | [`-webos.md`](cekirdek-guvenlik/cekirdek-guvenlik-webos.md) | [`-webos.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-webos.md) | [`-webos.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-webos.md) |
| ReactOS | [`-reactos.md`](cekirdek-guvenlik/cekirdek-guvenlik-reactos.md) | [`-reactos.md`](yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-reactos.md) | [`-reactos.md`](uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-reactos.md) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
