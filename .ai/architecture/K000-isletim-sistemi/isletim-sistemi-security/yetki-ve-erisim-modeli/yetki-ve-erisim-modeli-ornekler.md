---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
evidence:
  - shared/src/Session/SessionInitializer.php:31
  - shared/src/Session/SessionConfig.php:20
  - shared/src/Middleware/PermissionMiddleware.php:31
  - shared/src/Api/Middleware/AuthorizationMiddleware.php:64
  - home.coremusic.net/.htaccess:18
  - api.coremusic.net/.htaccess:24
validation:
  - "2026-10-13: 6 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
  - "2026-10-13: 0 web URL (bu dosya yalnizca disk kaniti tasir)."
open_questions:
  - "S2: Oturum dizini izni neden tek degere (muhtemelen 0750) indirgenmiyor? [UNKNOWN]"
group: kod
---

# yetki-ve-erisim-modeli-ornekler

> **Kapsam (tek cumle):** Yetki ve erisim modelinin bu repo'daki **gercek kod
> ornekleri** — dizin izni (DAC), iki ayri RBAC uygulamasi, `.htaccess`
> erisim kapilari ve production bypass kilidi.

> **Oncelikli gercek:** Bu dosyadaki her kod parcasi repo'dan okunmus
> **gercek satirdir**; uyarlama veya "tipik ornek" degildir. Her blok onune
> gelen `Dosya:` + `Satir:` satiri onayana da `Select-Object -Index (N-1)`
> indeksi verir.

> **Yontem:** 2026-10-13 tarihinde `Select-String` / `Select-Object -Index`
> ile okundu. 6 ornegin tamami satir birebir dogrulandi (otomatik karsilastirma
> sonucu: **OK=6 / BAD=0**).

## §1 — DAC: oturum dizini izin kontrasti (bu dalin ana bulgusu)

### §1.1 — `0777` ile olusturan cagri

```text
Dosya: shared/src/Session/SessionInitializer.php
Satir: 31
Select-Object -Index 30
```

```php
            @mkdir($savePath, 0777, true);
```

### §1.2 — `0750` ile olusturan cagri (ayni repo, baska dosya)

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

### §1.3 — Sabit tanim (SSOT)

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 20
Select-Object -Index 19
```

```php
    public const DIR_PERMISSIONS = 0750;       // Session dizin izinleri
```

### §1.4 — Ucuncu kaynak (ayni deger)

```text
Dosya: home.coremusic.net/config/constants.php
Satir: 77
Select-Object -Index 76
```

```php
    @mkdir($sessionPath, 0750, true);
```

### §1.5 — Karsilastirma tablosu

| # | Dosya | Satir | Mod | `DIR_PERMISSIONS` tutarli mi? |
|---|---|---|---|---|
| 1 | `shared/src/Session/SessionInitializer.php` | 31 | `0777` | **HAYIR** |
| 2 | `shared/src/Session/SessionConfig.php` | 35 | `0750` | EVET |
| 3 | `shared/src/Session/SessionConfig.php` | 20 | `0750` (sabit) | — (tanim) |
| 4 | `home.coremusic.net/config/constants.php` | 77 | `0750` | EVET |

### §1.6 — Guvenlik degerlendirmesi

| Madde | Degerlendirme |
|---|---|
| `0777` ne anlama gelir | sahip/grup/diger hepsi `rwx` — herkes yazabilir ve silebilir |
| `0750` ne anlama gelir | sahip `rwx` · grup `r-x` · diger `---` |
| fark | paylasilan bir makinede baska kullanici, `0777` dizini icinde oturum dosyalarini **okuyabilir** |
| `DIR_PERMISSIONS` sabitinin varligi | 4 cagridan 3'u sabitle **ayni**; 1 tanesi **farkli** |
| neden onemli | oturum dosyasi = kimlik; kimlik dosyasinin world-readable olmasi oturum ele gecirme yuzeyi acar |
| oncelik | **HIGH** (gizlilik etkisi), duzeltme maliyeti dusuk |

### §1.7 — Duzeltme onerisi (kod YAZILMAZ, sahibine devredilir)

```text
Degisiklik: shared/src/Session/SessionInitializer.php:31
Degisen: 0777  ->  SessionConfig::DIR_PERMISSIONS  (0750)
Sahibi: backend-architect  (security-engineer denetler)
```

> **Bu vault'ta kod degisikligi YAPILMAZ.** Bu dosya yalnizca kaniti ve
> oneriyi tasir; karar `open_questions` S2'ye baglidir `[UNKNOWN]`.

## §2 — RBAC (uygulama): ornek ornegi — `PermissionMiddleware`

### §2.1 — Rol okumasi ve en-dusuk yetki varsayilani

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 27
Select-Object -Index 26
```

```php
        $userRole = $auth['role'] ?? 'user';
```

**Neden guzel:** rol alani yoksa `user` — yani **en dusuk yetki**. Fail-closed
yonunde calisir; "rol yoksa her sey serbest" degil.

### §2.2 — Rol esitligi kontrolu

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 30
Select-Object -Index 29
```

```php
        $requiredRole = $routeMeta['requiredRole'] ?? null;
```

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 31
Select-Object -Index 30
```

```php
        if ($requiredRole !== null && $userRole !== $requiredRole) {
```

**Neden guzel:** iki kontrol var: (a) route rol istiyor mu? (b) kullanici
rolu ayni mi? `!==` **strict** — tip donusumu yok.

### §2.3 — Izin kontrolu (strict array search)

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 39
Select-Object -Index 38
```

```php
            if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true)) {
```

**Neden guzel:** uc arguman `true` -> `in_array` **strict** calisir
(`"1" === 1` olmaz). Tip donusumu soylemi (type juggling) kapali. Ayrica
once `is_array` tip kontrolu var.

### §2.4 — Red yaniti

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 40
Select-Object -Index 39
```

```php
                return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
```

**Neden guzel:** donus **`return`** — yani istek `next($request)`'e **ulasamaz**.
Zincir kirilir, hata bir sonraki middleware'a devredilmez.

### §2.5 — Zincire devam (izin verildiginde)

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 44
Select-Object -Index 43
```

```php
        return $next($request);
```

### §2.6 — RBAC akis ozeti

| Satir | Karar | Sonuc |
|---|---|---|
| 27 | rol alani yoksa `user` | en dusuk yetki |
| 30 | route rol istiyor mu? | hayirsa adim atlanir |
| 31 | rol eslesiyor mu? | hayirsa `forbidden` (403) |
| 36 | route izin istiyor mu? | hayirsa adim atlanir |
| 39 | izin dizisinde strict ara | yoksa `forbidden` (403) |
| 44 | ikisi de gecti | `next($request)` |

## §3 — RBAC (uygulama): ikinci ornek — `AuthorizationMiddleware`

Bu repo'da **iki ayri RBAC uygulamasi** var. Ikincisi API katmanindadir ve
**coklu rol** (`roles` dizisi) modeli kullanir.

### §3.1 — Rol kontrolu (API katmani)

```text
Dosya: shared/src/Api/Middleware/AuthorizationMiddleware.php
Satir: 64
Select-Object -Index 63
```

```php
            if (!in_array($requiredRole, $userRoles, true)) {
```

### §3.2 — Izin kontrolu (API katmani)

```text
Dosya: shared/src/Api/Middleware/AuthorizationMiddleware.php
Satir: 77
Select-Object -Index 76
```

```php
            if (!in_array($requiredPermission, $userPermissions, true)) {
```

### §3.3 — Karşılaştırma: iki RBAC, iki model

| Ozellik | `PermissionMiddleware` | `AuthorizationMiddleware` |
|---|---|---|
| Dosya | `shared/src/Middleware/` | `shared/src/Api/Middleware/` |
| Rol modeli | **tekil** `role` (string) | **coklu** `roles` (dizi) |
| Rol okumasi | `$auth['role'] ?? 'user'` | `$authUser['roles'] ?? []` |
| Izin okumasi | `$auth['permissions'] ?? []` | `$authUser['permissions'] ?? []` |
| Karsilastirma | `!==` (esitlik) | `in_array(..., true)` (dizi icinde) |
| Red bicimi | `forbidden(...)` | `ApiResponse::error('FORBIDDEN', ..., 403)` |
| strict mi? | EVET (`!==`) | EVET (`in_array` 3. arguman) |
| Zincir kirma | `return $this->forbidden` | `return ApiResponse::error(...)` |

**Gozlem:** iki uygulama da **strict** ve **fail-closed** — bu ortak
ozellik dogru tasarlandiginin gostergesidir. Fark **rol modelinde**: biri
tekil, digeri coklu. Bu fark kasitli mi, evrimsel mi -> `open_questions` S2.

### §3.4 — Route meta'si nereden geliyor

```text
Dosya: shared/src/Api/Routing/RouteTable.php
Satir: 55
Select-Object -Index 54
```

```php
        $route['requiredRole']       = $route['requiredRole'] ?? null;
```

```text
Dosya: shared/src/Api/Routing/RouteTable.php
Satir: 56
Select-Object -Index 55
```

```php
        $route['requiredPermission'] = $route['requiredPermission'] ?? null;
```

**Okuma:** yetki **route taniminda** bildirilir, kod icinde gizlenmez. Bu,
"hangi ucun hangi yetki istedigi" sorusunun **tek yerden** okunabilmesini
saglar (declarative RBAC).

### §3.5 — Sozlesme belgesi (dokumantasyon satiri)

```text
Dosya: api.coremusic.net/config/routes.php
Satir: 15
Select-Object -Index 14
```

```text
 *   requiredRole / requiredPermission : AuthorizationMiddleware RBAC
```

## §4 — Kimlik verisinin kaynagi: `AuthMiddleware`

### §4.1 — Oturumdan yetki okumasi

```text
Dosya: shared/src/Middleware/AuthMiddleware.php
Satir: 21
Select-Object -Index 20
```

```php
        $permissions = $session['MM_Permissions'] ?? [];
```

```text
Dosya: shared/src/Middleware/AuthMiddleware.php
Satir: 26
Select-Object -Index 25
```

```php
                'role'        => $role !== null ? (string)$role : 'user',
```

```text
Dosya: shared/src/Middleware/AuthMiddleware.php
Satir: 29
Select-Object -Index 28
```

```php
                'permissions' => is_array($permissions) ? $permissions : [],
```

### §4.2 — Guvenlik notlari

| Satir | Kod | Neden guvenli |
|---|---|---|
| 21 | `?? []` | yetki yoksa **bos dizi** -> hiçbir izin gecmez |
| 26 | `?: 'user'` | rol yoksa **en dusuk yetki** |
| 29 | `is_array(...) ? : []` | tip kontrolu; dizi degilse bos dizi |

Uc satir da ayni felsefeyi tasir: **eksik veri = az yetki**, fazla yetki degil.
Buna "fail-closed veri yorumlamasi" denir ve RBAC'in en sik ihlal edilen
kismidir (bir cok sistemde "rol yok -> admin" hatasi gorulur; burada yok).

### §4.3 — RBAC'in kimlik onu sarti

```text
Dosya: shared/src/Middleware/AuthMiddleware.php
Satir: 23
Select-Object -Index 22
```

```php
        if ($userId !== null && is_string($userId) && $userId !== '') {
```

**Neden onemli:** `_auth` blogu **yalnizca** kimlik gecerliyse doldurulur.
Yani `PermissionMiddleware`'in okudugu `$auth['role']`, her zaman
**dogrulanmis bir oturumdan** gelir; kimliksiz istekte `$auth` bos kalir ve
§2'deki `?? 'user'` devreye girer.

## §5 — Erisim kapilari: `.htaccess` (Apache katmani)

### §5.1 — Dizin listelemeyi kapatma

```text
Dosya: home.coremusic.net/.htaccess
Satir: 2
Select-Object -Index 1
```

```apache
Options -Indexes
```

**Etki:** dizinde `index.php` yoksa dosya listesi **goruntulenemez**. Bilgi
sizintisi yuzeyini kapatir (dosya adlari sirket ici yapiyi ele verir).

### §5.2 — Hassas dosya uzantilarini kapatma

```text
Dosya: home.coremusic.net/.htaccess
Satir: 18
Select-Object -Index 17
```

```apache
<FilesMatch "\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd|DS_Store)$">
```

```text
Dosya: home.coremusic.net/.htaccess
Satir: 19
Select-Object -Index 18
```

```apache
    Require all denied
```

**Kapsam:** `.env` (sirrlar) · `.log` (loglar) · `.sql` (sema) · `.sh`
(betikler) · `.bak` (yedekler) · `.git` (kaynak deposu) · `.ini`
(yapilandirma) · `.htpasswd` (sifre dosyasi) — hepsi **dogrudan erisime kapali**.

### §5.3 — index.php disindaki PHP dosyalarini kapatma (home)

```text
Dosya: home.coremusic.net/.htaccess
Satir: 22
Select-Object -Index 21
```

```apache
<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">
```

```text
Dosya: home.coremusic.net/.htaccess
Satir: 23
Select-Object -Index 22
```

```apache
    Require all denied
```

**Okuma:** negatif beklenti (lookahead) ile **iki istisna** birakilmis:
`index.php` ve `debug.php`. Geri kalan **her** `.php` dosyasi reddedilir.

> **Gozlem (NOT):** `debug.php` istisnasi, eger o dosya varsa ve production'da
> aciksa, bir **bilgi sizintisi yuzeyi** olusturur. Bu vault'ta `debug.php`
> varligi **gözlenmedi** (dosya aramasi 0 sonuc) -> istisna su anlik olarak
> zararsiz; yine de istisnanin **neden** var oldugu `open_questions` S2'ye
> baglidir `[UNKNOWN]`.

### §5.4 — API katmaninda ayni kapi (istisnasiz)

```text
Dosya: api.coremusic.net/.htaccess
Satir: 24
Select-Object -Index 23
```

```apache
<FilesMatch "^(?!index\.php$).+\.php$">
```

```text
Dosya: api.coremusic.net/.htaccess
Satir: 25
Select-Object -Index 24
```

```apache
    Require all denied
```

**Fark:** API katmaninda **yalnizca** `index.php` istisnasi var —
`debug.php` istisnasi **yok**. Yani API katmani **daha sikidir**.

### §5.5 — Dizin yasagi (rewrite kurali)

```text
Dosya: home.coremusic.net/.htaccess
Satir: 35
Select-Object -Index 34
```

```apache
    RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
```

**Okuma:** `config` · `include` · `pages` altindaki **her sey** `[F]` = HTTP 403
ile reddedilir; `[L]` = son kural (diger kurallara bakilma). Bu, uygulama
kaynaginin (PHP dosyalari) web uzerinden **okunamaz** olmasini saglar.

### §5.6 — Kapi katmanlari ozeti

| Katman | Dosya | Mekanizma | Ne engeller |
|---|---|---|---|
| dizin listeleme | `home/.htaccess:2` | `Options -Indexes` | dosya adi listesi |
| uzanti kapi | `home/.htaccess:18-19` | `FilesMatch` + `Require` | `.env` `.sql` `.log` ... |
| php kapi (home) | `home/.htaccess:22-23` | `FilesMatch` + `Require` | `index.php`/`debug.php` disi |
| php kapi (api) | `api/.htaccess:24-25` | `FilesMatch` + `Require` | `index.php` disi |
| dizin yasagi | `home/.htaccess:35` | `RewriteRule [F,L]` | `config`/`include`/`pages` |

**Katman ilkesi:** hepsi **beyaz liste mantigiyla calisir** — "istisna
haricinde her sey reddedilir". Bu, `yetki-ve-erisim-modeli-surucu.md` §1'deki
fail-closed ilkesinin web sunucusu katmanindaki karsiligidir.

## §6 — Production bypass kilidi (en yuksek oncelikli kilit)

### §6.1 — Gercek kod

```text
Dosya: shared/src/Security/SecurityHelper.php
Satir: 25
Select-Object -Index 24
```

```php
        if ($config->get('app.env') === 'production') {
```

```text
Dosya: shared/src/Security/SecurityHelper.php
Satir: 26
Select-Object -Index 25
```

```php
            return false;
```

### §6.2 — Neden bu satir en onemlisidir

| Madde | Degerlendirme |
|---|---|
| kosul | `app.env === 'production'` |
| donus | **`false`** — yani bypass **kapali** |
| onem | eger production'da bypass acik olsaydi, tum Auth/Permission adimlari **anlamsiz** olurdu |
| sonuc | production'da `test_mode` / `force_auth_bypass` anahtarlari **okunmaz bile**, ilk satirda `false` doner |
| sinif | "kilit" — cunku sonraki satir (28-29) yalnizca production **disinda** degerlendirilir |

**Mimari kural ile uyum:** `BypassAuth` middleware'i her zaman production'da
kapali olmalidir (Sistem Kurallari §13). Bu satir, o kuralin **kod
karsiligidir** — kural degil, kanittir.

### §6.3 — Kilit zinciri

```text
app.env = production  ->  return false   (bypass KAPALI, okuma bile yok)
app.env != production ->  test_mode mi?  |  force_auth_bypass mi?
                              ↓ evet            ↓ evet
                          bypass ACIK      bypass ACIK
                              ↓ hayir            ↓ hayir
                                            bypass KAPALI
```

## §7 — Oturum kimliginin korunmasi (DAC ile kesisen kisim)

### §7.1 — Cookie guvenlik bayraklari

```text
Dosya: shared/src/Session/SessionInitializer.php
Satir: 45
Select-Object -Index 44
```

```php
            'httponly'  => true,   
```

```text
Dosya: shared/src/Session/SessionInitializer.php
Satir: 46
Select-Object -Index 45
```

```php
            'samesite' => 'Lax',   
```

### §7.2 — Neden bu dosyada

| Bayrak | Ne engeller | Katman |
|---|---|---|
| `httponly => true` | JavaScript'in `document.cookie` ile okumasi (XSS'de calisma) | tarayici |
| `samesite => 'Lax'` | cross-site isteklerde cookinin gonderilmesi (CSRF yuzeyi) | tarayici |
| `secure => $isHttps` | HTTPS disinda cookinin gonderilmesi | iletim |
| `DIR_PERMISSIONS = 0750` | baska kullanici tarafindan dosyanin okunmasi | **OS (DAC)** |

**Dort katman birlikte calisir:** bayraklar tarayiciyi, `0750` ise isletim
sistemini korur. Biri eksik olursa digeri hala calisir — bu, **derinlikli
savunma** (defence in depth) ornegidir. §1.5'te gorulen `0777` farki,
dort katmanin **sonuncusunu zayiflatir**.

## §8 — Olum (nekro) kontroller — hatali ornek degil, tuzak ornekler

Asagidaki ornekler **YAPILMASI GEREKEN** seyi degil, **yapilmamasi gerekeni**
gosterir; `yetki-ve-erisim-modeli-anayasa.md` §13'teki risklerle eslesir.

### §8.1 — Tuzak: `0777` (§1.1)

```text
Dosya: shared/src/Session/SessionInitializer.php
Satir: 31
Select-Object -Index 30
```

```php
            @mkdir($savePath, 0777, true);
```

**Tuzak nerede:** `0777` + `umask` ayarlanmamis (repo'da `umask(` cagrisi 0) =>
nihai mod **sistem varsayilanina** birakilmistir. Dogrusu: `0750` (§1.4).

### §8.2 — Tuzak: `@` hata susturma

```text
Dosya: shared/src/Session/SessionConfig.php
Satir: 35
Select-Object -Index 34
```

```php
            @mkdir($savePath, 0750, true);
```

**Tuzak nerede:** `@` oneki, `mkdir` hatalarini (izin reddi, disk dolu)
**susturur**. Yani dizin olusmazsa sistem **sessizce devam eder** ve oturum
sonrasi `session_save_path` var olmayan bir dizini isaret eder. Bu, fail-closed
ilkesine **aykiridir** (hata == red olmali). Bu yuzden `@` oneki ayrica bir
inceleme maddesidir — yalnizca `0750` degil, hata yonetimi de ele alinmalidir.

### §8.3 — Tuzak: `debug.php` istisnasi

```text
Dosya: home.coremusic.net/.htaccess
Satir: 22
Select-Object -Index 21
```

```apache
<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">
```

**Tuzak nerede:** istisna listesi, production'da gereksiz bir acik birakir.
Dosya su an yok (0 sonuc) ama istisna **koda gömülüdür** — dosya bir gun
olusturulursa kapi zaten acik olur.

## §9 — Olumlu / olumsuz ornek eslesme tablosu

| # | Ornek | Tur | Katman | Kanit |
|---|---|---|---|---|
| 1 | `0777` mkdir | **olumsuz** | DAC | `SessionInitializer.php:31` |
| 2 | `0750` mkdir | olumlu | DAC | `SessionConfig.php:35` |
| 3 | `DIR_PERMISSIONS = 0750` | olumlu | DAC | `SessionConfig.php:20` |
| 4 | `0750` (constants) | olumlu | DAC | `constants.php:77` |
| 5 | `?? 'user'` | olumlu | RBAC | `PermissionMiddleware.php:27` |
| 6 | `!==` rol kontrolu | olumlu | RBAC | `PermissionMiddleware.php:31` |
| 7 | `in_array(..., true)` | olumlu | RBAC | `PermissionMiddleware.php:39` |
| 8 | `forbidden` return | olumlu | RBAC | `PermissionMiddleware.php:40` |
| 9 | `next($request)` | olumlu | RBAC | `PermissionMiddleware.php:44` |
| 10 | `in_array` (API, coklu rol) | olumlu | RBAC | `AuthorizationMiddleware.php:64` |
| 11 | `in_array` (API, izin) | olumlu | RBAC | `AuthorizationMiddleware.php:77` |
| 12 | route meta `?? null` | olumlu | RBAC | `RouteTable.php:55` |
| 13 | `roles`/izin dizi okuma | olumlu | RBAC | `AuthMiddleware.php:29` |
| 14 | `userId` sarti | olumlu | kimlik | `AuthMiddleware.php:23` |
| 15 | `Options -Indexes` | olumlu | web kapi | `home/.htaccess:2` |
| 16 | uzanti `FilesMatch` | olumlu | web kapi | `home/.htaccess:18` |
| 17 | php `FilesMatch` (home) | olumlu | web kapi | `home/.htaccess:22` |
| 18 | php `FilesMatch` (api) | olumlu | web kapi | `api/.htaccess:24` |
| 19 | `RewriteRule [F,L]` | olumlu | web kapi | `home/.htaccess:35` |
| 20 | `return false` production | olumlu | kilit | `SecurityHelper.php:26` |
| 21 | `httponly` / `samesite` | olumlu | oturum | `SessionInitializer.php:45,46` |
| 22 | `@` hata susturma | **olumsuz** | hata yonetimi | `SessionConfig.php:35` |
| 23 | `debug.php` istisnasi | **olumsuz** | web kapi | `home/.htaccess:22` |

**Sayim:** 23 ornek · 20 olumlu · 3 olumsuz (tuzak) · hepsi disk kaniti.

## §10 — Repoya ozgu tekrar kontrol listesi

- [ ] `0777` cagrisi hala `SessionInitializer.php:31`'de mi? (evet, 2026-10-13)
- [ ] `DIR_PERMISSIONS` sabiti hala `0750` mi? (evet)
- [ ] Her iki RBAC de hala strict `in_array(..., true)` kullanıyor mu? (evet)
- [ ] `SecurityHelper` production'da hala ilk satirda `return false` mi? (evet)
- [ ] `.htaccess` `FilesMatch` kapilari hala yerinde mi? (evet)
- [ ] `debug.php` istisnasi hala var mı ve dosya hala yok mu? (istisna var, dosya yok)
- [ ] `umask(` cagrisi hala 0 mu? (evet — §8.1 tuzaginin devam nedeni)

## §11 — Referans (bu dosyada web URL yok)

Bu dosya **yalnizca disk kaniti** tasir. Web referanslari
`yetki-ve-erisim-modeli-anayasa.md` §15 ve `-dogrulama.md` §1.2 U-havuzundadir
(2097'lik K000 katalog envanteriyle kesisim 0).

## §12 — Acik sorular (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S2 | `0777` cagrisi `0750`'ye (veya `DIR_PERMISSIONS` sabitine) neden indirgenmiyor? | `[UNKNOWN]` | Mimari sahibi karari; kod degisikligi bu vault'ta YAPILMAZ |
| S2b | Tekil rol (`role`) ile coklu rol (`roles`) modelleri neden birlikte var? | `[UNKNOWN]` | Mimari sahibi karari — birlestirme ya da sinirlama |

> **SORUMLULUK SINIRI:** §1 ve §8'deki tuzaklarin duzeltilmesi
> **backend-architect** isidir; bu dosya yalnizca kanit + oneri tasir.
> `security-engineer` duzeltme **sonrasinda** teyit eder (read-only audit).

## §13 — OS grupları (8 platform · 2026-10-14)

> Bu dosyadaki ornek siniflarinin (DAC/RBAC/ACL/izin) platform bazli karsiliklari ve
> tam ornek metinleri ayni klasordeki OS kartlarindadir (her kart: §0 ELI15 ·
> §1 OS profili · §2 kod ornegi `kaynak: upstream docs` imzali, "repo'da uygulaniyor"
> yazilmaz · §3 repo durumu = hedef referans · §4 sinir cumleleri · §5 referans ·
> §6 acik sorular S1/S2 kapatilmaz).

| OS | Kart | Bu dosyadaki ornek siniflarinin platform karsiligi | Repo durumu |
|---|---|---|---|
| Windows | [`yetki-ve-erisim-modeli-windows.md`](yetki-ve-erisim-modeli-windows.md) | `icacls` / `Set-Acl` · SDDL ile ACL yazimi | hedef referans (0 kullanim) |
| Linux | [`yetki-ve-erisim-modeli-linux.md`](yetki-ve-erisim-modeli-linux.md) | `chmod`/`umask`/`setfacl`/`getfacl` | hedef referans (0 kullanim) |
| macOS | [`yetki-ve-erisim-modeli-macos.md`](yetki-ve-erisim-modeli-macos.md) | `chmod` + `codesign` entitlement denetimi | hedef referans (0 kullanim) |
| iOS | [`yetki-ve-erisim-modeli-ios.md`](yetki-ve-erisim-modeli-ios.md) | entitlement plist + Keychain access group | hedef referans (0 kullanim) |
| Android | [`yetki-ve-erisim-modeli-android.md`](yetki-ve-erisim-modeli-android.md) | runtime permission request akisi | hedef referans (0 kullanim) |
| Tizen | [`yetki-ve-erisim-modeli-tizen.md`](yetki-ve-erisim-modeli-tizen.md) | privilege-based permission (manifest) | hedef referans (0 kullanim) |
| webOS | [`yetki-ve-erisim-modeli-webos.md`](yetki-ve-erisim-modeli-webos.md) | ACG servis izni | hedef referans (0 kullanim) |
| ReactOS | [`yetki-ve-erisim-modeli-reactos.md`](yetki-ve-erisim-modeli-reactos.md) | ACL editor / NT Security Descriptor | hedef referans (0 kullanim) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
