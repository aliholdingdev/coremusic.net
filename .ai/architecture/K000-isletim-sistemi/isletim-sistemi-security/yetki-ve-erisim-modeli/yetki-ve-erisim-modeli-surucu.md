---
node_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/yetki-ve-erisim-modeli-anayasa
  - K000-isletim-sistemi/isletim-sistemi-kernel
evidence:
  - https://man7.org/linux/man-pages/man2/umask.2.html
  - https://man7.org/linux/man-pages/man3/getumask.3.html
  - https://docs.kernel.org/admin-guide/sysctl/fs.html
  - https://docs.kernel.org/admin-guide/sysctl/kernel.html
  - https://man7.org/linux/man-pages/man1/setfacl.1.html
  - shared/src/Middleware/MiddlewarePipeline.php:32
  - shared/src/Session/SessionInitializer.php:31
  - api.coremusic.net/.htaccess:24
validation:
  - "2026-10-13: 5 web URL HEAD ile HTTP 200; ucu da 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 3 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? [UNKNOWN]"
group: mimari
---

# yetki-ve-erisim-modeli-surucu

> **Kapsam (tek cumle):** Yetki kararinin **nasil calistigi** — syscall'ten
> VFS'e, oradan DAC -> ACL -> MAC sirasina; umask'in karara etkisi; denetim
> (audit) kaydinin nerede olustugu; ve bu zincirin uygulama tarafindaki karsiligi
> olan middleware boru hatti.

> **Oncelikli gercek:** OS denetim zinciri bu sistemde **kismen etkin** —
> DAC `mkdir` mod argumaniyla calisiyor, ACL/MAC/audit **etkin degil**.
> Uygulama tarafindaki denetim zinciri ise **tam calisiyor** (gercek kod §6).

## §1 — Denetim zinciri (syscall'ten karara)

Linux'ta bir dosya erisimi asagidaki sirayi gecer; **her katman "deny"
derse durur** (fail-closed):

```text
[1] syscall cagrisi        open() / mkdir() / stat() / execve()
   ↓
[2] syscall denetimi       seccomp filtresi / capability kontrolu
   ↓
[3] VFS yol cozumleme      /a/b/c -> inode bulma (symlink, .. kontrolu)
   ↓
[4] ACL kontrolu           POSIX ACL girdisi varsa (yoksa atlanir)
   ↓
[5] DAC kontrolu           sahip / grup / diger bitleri (mode)
   ↓
[6] MAC kontrolu           LSM etiket + siyaset (SELinux/AppArmor)
   ↓
[7] karar                  allow  |  deny (-EACCES / -EPERM)
```

**Sirasi onemlidir:** ACL DAC'den **once** bakar (ACL mask'i DAC'i de
sinirlar); MAC en sondadir cunku etiket, izin bitlerinden bagimsizdir.
Sekizinci bir katman olarak **audit kaydi** karardan **sonra** yazilir —
audit, karari **degistirmez**, yalnizca kaydeder.

> **Bu repo'daki durum:** [2] etkin degil (seccomp/capability 0 cagri),
> [4] etkin degil (ACL 0 cagri), [5] **etkin** (mkdir mod argumani),
> [6] etkin degil (LSM 0), audit etkin degil. Yani zincirin **tek**
> ayaklasi DAC'dir; uygulama katmani kendi zincirini middleware ile kurmustur
> (§6).

## §2 — VFS yol cozumleme ve traversal (zinirlama noktasi)

Yol cozumleme sirasinda klasik iki hata:

| Hata | Mekanizma | Sonuc |
|---|---|---|
| `..` traversal | ` /a/../../etc/passwd` | dizin disina cikis |
| symlinksiz kontrol | kontrol edildikten sonra link degisimi | TOCTOU |
| mutlak yol karisimi | user girisi `/etc/...` | beklenen dizin disi |

**Guvenli kalip (PHP):** giris `realpath()` ile gercek yola indirgenir, sonra
mutlak gercek yolun **beklenen dizin kumesi icinde** oldugu kontrol edilir.
Bu kalip bu repo'da **gercek olarak mevcuttur** (kod ornegi `-ornekler.md`
§2 — `MusicStreamHandler` yol dogrulama zinciri).

**Sekiz adimli guvenli zincir (anlatim):**

```text
1. giris al   -> 2. bosluk/ null kontrolu  -> 3. normalize et (realpath)
4. mutlak yola indir                         -> 5. baz dizin ile karsilastir
6. disardaysa RED                            -> 7. varlik kontrolu (file_exists)
8. ac (istenilen mod ile)
```

## §3 — umask mekanizmasi (nasil calisir)

`umask`, proses bazlidir ve **sadece olusturulan nesnelere** uygulanir:

| Adim | Islem |
|---|---|
| 1 | uygulama `mkdir($yol, 0777)` der — istenen mod |
| 2 | cekirdek `istenen & ~umask` hesaplar |
| 3 | nihai mod inode'a yazilir |
| 4 | sonradan `chmod` ile degistirilebilir |

Ornek hesap:

```text
istenen = 0777 (rwxrwxrwx)   umask = 0022 (----w--w- tersi)
~umask  = 0755 (rwxr-xr-x)
sonuc   = 0777 & 0755 = 0755
```

```text
istenen = 0777   umask = 0077
sonuc   = 0777 & 0700 = 0700
```

**Kritik ayrinti:** `umask` istenen modu **genisletemaz**, sadece daraltir.
Bu yuzden `mkdir($yol, 0777)` + `umask 0022` ikilisi **guvenli degildir**
(sonuc 0755 — grup okuyabilir); `umask 0077` olsa sonuc 0700 olur.

**Bu repo'daki durum:** `umask(` cagrisi PHP agacinda **0 sonuc** (negatif
kanit, vendor/node_modules hariç). Yani izin **umask ile degil, `mkdir`
argumaniyla acikca veriliyor**. Bu, anlasilir bir secimdir cunku
`@mkdir($savePath, 0750, true)` gibi cagrilarda maske ihtiyaci kalmaz.

| Kaynak | Satir | Kod | umask etkisi |
|---|---|---|---|
| `shared/src/Session/SessionInitializer.php` | 31 | `@mkdir($savePath, 0777, true)` | maskeye **bagimli** — maske yoksa herkes yazar |
| `shared/src/Session/SessionConfig.php` | 35 | `@mkdir($savePath, 0750, true)` | maskeye **bagimsiz** — mod acik |

**Sonuc:** `0777` cagrisi, `umask` acikca ayarlanmadigi icin **sistemin
varsayilan maskesine** birakilmistir. Sistem varsayilani genelde `0022`
oldugundan pratik sonuc `0755` olur — yine de **beklenen degildir**, cunku
oturum dizini icin hedef `0750`'dir. Bu gozlem `-kapsam.md` S2'ye baglidir.

## §4 — Denetim (audit) kaydi nerede olusur

| Katman | Kayit araci | Bu repo'da | Kayit icerigi |
|---|---|---|---|
| OS (Linux) | `auditd` / `audit.log` | **etkin degil** | syscall, pid, path |
| OS (Windows) | Security Log | **etkin degil** | olay kimligi, hesap |
| Web sunucusu | Apache access/error log | sunucu yapilandirmasi | IP, path, status |
| Uygulama | vault `log.md` (append-only) | **var** | agent/karar kaydi |
| Uygulama | middleware `forbidden()` | **var** | rol/izin red sebebi |

**Denetim kayitlarinin duzeni (4 altin kural — anlatim):**

1. Kayit **zaman damgali** olmali.
2. Kayit **kimlik + eylem + sonuc** icermeli.
3. Kayit **degistirilemez** olmali (append-only).
4. Kayit **geri oynatilabilir** olmali (replay).

Bu dordunu bu repo'da **uygulama katmaninda** karsilayan sey vault `log.md`
append-only kuralidir; OS seviyesi karsiligi **yok** (S1'e bagli).

## §5 — Boru hatti (pipeline) sirasi neden baglayicidir

Uygulama katmanindaki denetim zinciri, middleware sirasina **gore** calisir.
Sira bir kez tanimlanir ve **degistirilmesi yasaktir** (frozen — ADR-010):

```text
OriginCheck -> Cors -> RateLimiter -> SecurityHeaders -> SessionManager
-> Csrf -> BypassAuth -> Auth -> Permission -> Validation
```

**Sirainin neden boyle oldugu:**

| Siradaki middleware | Neden bu sirada |
|---|---|
| `OriginCheck` en onde | istek daha kaynak kontrolunden gecmeden reddedilsin |
| `Cors` ikinci | yanit basliklari erken kurulsun |
| `RateLimiter` ucuncu | yetkisiz iskence (brute force) hemen durdurulsun |
| `SessionManager` | kimlik verisi sonrasi adimlara hazir olsun |
| `Csrf` | oturum acildiktan SONRA token dogrulanir |
| `Auth` | kimlik dogrulamasi yetki kontrolunden once |
| `Permission` | **Auth'dan sonra** — rolsuz yetki kontrolu anlamsiz |
| `Validation` en son | yetkisiz istek veri dogrulamaya bile girmesin |

**Sirayi calistiran mekanizma** (gercek kod):

```text
Dosya: shared/src/Middleware/MiddlewarePipeline.php
Satir: 32
Select-Object -Index 31
```

```php
        foreach (array_reverse($this->middlewares) as $middleware) {
```

`array_reverse` + sarma (wrap) kalibi: son middleware en dis katman olur,
ilk middleware en ic katman olur; istek disari dogru, yanit iceri dogru akar.
Sira **kod tarafinda degil, kayit (registry) tarafinda** tanimlidir — bu,
siranin tek bir noktadan denetlenebilmesini saglar.

## §6 — Uygulama katmani denetim akisi (gercek kod)

RBAC kontrolunun gercekte calisma bicimi:

```text
Dosya: shared/src/Middleware/PermissionMiddleware.php
Satir: 27
Select-Object -Index 26
```

```php
        $userRole = $auth['role'] ?? 'user';
```

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
Satir: 44
Select-Object -Index 43
```

```php
        return $next($request);
```

**Akis (adim adim):**

| # | Satir | Ne olur | Karar |
|---|---|---|---|
| 1 | `AuthMiddleware.php:29` | `permissions` oturuma yazilir | veri hazir |
| 2 | `PermissionMiddleware.php:27` | kullanici rolu okunur, yoksa `user` | varsayilan en dusuk |
| 3 | `PermissionMiddleware.php:30` | route'un requiredRole'u okunur | yoksa bu adim atlanir |
| 4 | `PermissionMiddleware.php:31` | rol eslesmiyorsa `forbidden` | **RED** |
| 5 | `PermissionMiddleware.php:36` | requiredPermission okunur | yoksa atlanir |
| 6 | `PermissionMiddleware.php:39` | izin dizisinde strict arama | bulunamazsa **RED** |
| 7 | `PermissionMiddleware.php:44` | `next($request)` — zincire devam | **IAZIN** |

**Fail-closed dogrulamasi:**

- Auth bilgisi **yoksa** Permission ilgilenmez (`AuthMiddleware` onunde durur) —
  yani kimliksiz istek `Permission`'a bile ulasamaz.
- Rol **yoksa** `?? 'user'` — en dusuk yetki.
- Yetki dizisi **degilse** `!is_array(...)` — hemen RED.
- Izin **yoksa** `!in_array(..., true)` — hemen RED.

Dort durumun hepsinde varsayilan **red** yonunde; acma (allow) yalnizca
ikisi de tutarsa verilir. Bu, fail-closed tasarimin somut kanitidir.

## §7 — Ornek: bir isteğin gectigi tum kapilar

```text
istemci  POST /api/ayarlar
  [1] OriginCheck   Origin basligi izin listesinde mi?     -> hayir -> 403
  [2] Cors          yanit CORS basliklarini kur
  [3] RateLimiter   60/60s siniri asildi mi?               -> evet -> 429
  [4] SecurityHeaders  CSP/X-Frame/... basliklari ekle
  [5] SessionManager   oturum yukle
  [6] Csrf          csrf_token eslesti mi?                 -> hayir -> 419
  [7] BypassAuth    production'da kapali (her zaman false)
  [8] Auth          oturum gecerli mi?                     -> hayir -> 401
  [9] Permission    role/izin eslesti mi?                  -> hayir -> 403
  [10] Validation   giris semasi dogru mu?                 -> hayir -> 422
  [11] Controller   is mantigi calisir
```

**Sekiz kapinin kacininda ne olur:** [1] kaynak · [3] hiz · [6] CSRF ·
[8] kimlik · [9] yetki · [10] veri — alti **karsi tarafin kontrolunde olmayan**
bilgiye bakar (Origin, hiz sayaci, token, oturum, ro, sema).

## §8 — Sysctl / sistem seviyesi karar noktalari

Cekirdegin davranisini belirleyen (ve bu dalin ilgilendigi) ayarlar:

| Alan | Ornek anahtar | Etki | Bu repo'da |
|---|---|---|---|
| dosya sistemi | `fs.*` | dosya limitleri, suid davranisi | yapilandirma **yok** |
| cekirdek | `kernel.*` | ptrace, kptr mask, dmesg | yapilandirma **yok** |
| ag | `net.*` | spoofing korumasi | yapilandirma **yok** |

`fs.protected_symlinks` / `fs.protected_hardlinks` gibi anahtarlar, sahipsiz
linklerin taklidini engeller; bunlar **deny-by-default** davranisidir.
Depoda bunlari uygulayan bir yapilandirma (sysctl dosyasi, Ansible vb.)
**gözlenmedi** (negatif kanit: `sysctl` cagrisi 0, `.conf` dosyasi yok).
Devreye alma => S1 + devops-engineer isidir.

## §9 — Enjeksiyon ve yetki kesisimi

Yetki modeli, enjeksiyon siniriyla **kesisir**; ikisi ayni katmanda degil:

| Yuzey | Kimin sorumlulugu | Mekanizma |
|---|---|---|
| shell argumani kacisi | uygulama (`escapeshellarg`) | imlecler ayristirma |
| SQL enjeksiyonu | uygulama (prepared statement) | parametre baglama |
| yol traversal | uygulama (`realpath` kontrolu) | baz dizin siniri |
| syscall siniri | **OS** (seccomp) | filtre kurali |
| yetki siniri | **OS** (DAC/ACL/MAC) + uygulama (RBAC) | denetim zinciri |

**Kesisme noktasi:** `escapeshellarg` **yalnizca** shell ayristirma sorununu
cozer; cagrilan komutun **kendisi** hala cagiran prosesin yetkisiyle calisir.
Yani imlec kacisi engellenmis olsa bile, `CAP_SYS_MODULE`-lu bir proses
`insmod` calistirabilir. Bu yuzden "enjeksiyon guvenligi = yetki guvenligi"
degildir — ikisi **ayri katmandir** ve ikisi de gereklidir.

## §10 — Apache katmanindaki yetki kapisi (gercek kod)

`.htaccess`, uygulama katmanindan **once** calisan bir yetki katmanidir.
İki dosyada da ayni kalip:

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

**Neden onemli:** Bu kapi, `index.php` disindaki tum PHP dosyalarinin
**dogrudan calistirilmasini** engeller. Yani bir dosya (upload, backup,
yanlislikla olusan) var olsa bile istek **onden gecemez**. Bu, "yetki =
uygulama icindeki rol" degil, "yetki = dosya sistemine erisim bicimi" katmanidir.

| Katman | Arac | Ne engeller | Sira |
|---|---|---|---|
| Web sunucusu | `.htaccess` `Require all denied` | dosyaya dogrudan erisim | 1 |
| Uygulama | `Auth` / `Permission` | kimliksiz / yetkisiz istek | 2 |
| OS | DAC (`0750`) | baska kullanici dosya okur | 3 |

Uc katman **birbirinin yerine gecmez**; ucuncusu de olsa ilk ikisi calisir.

## §11 — Hata kodlari ve anlamlari

| Kod | Kaynak | Anlami | Tipik uretim noktasi |
|---|---|---|---|
| `0` / allow | cekirdek | izin verildi | tum kontroller gecti |
| `-EACCES` (13) | cekirdek | erisim reddedildi | DAC/ACL/MAC |
| `-EPERM` (1) | cekirdek | yetki yetmiyor | capability |
| `-ENOENT` (2) | cekirdek | dosya yok | yol cozumleme |
| `-ELOOP` (40) | cekirdek | symlink dongusu | yol cozumleme |
| HTTP 401 | uygulama | kimliksiz | `Auth` |
| HTTP 403 | uygulama | kimlik var, yetki yok | `Permission` |
| HTTP 419 | uygulama | CSRF token gecersiz | `Csrf` |
| HTTP 422 | uygulama | veri semasi gecersiz | `Validation` |
| HTTP 429 | uygulama | hiz siniri asildi | `RateLimiter` |

**Ayrım (onemli):** `-EACCES` **dosya seviyesi** reddi (varlik var ama izin
yok), `-EPERM` **yetki seviyesi** reddi (proses yetkisiz). HTTP 401 ile 403
ayni ayrimi uygulama katmaninda tekrarlar: 401 = "kimsin" · 403 = "kimsin
ama bunu yapamazsin".

## §12 — Olcum komutlari (yeniden uretilebilir)

```powershell
# proses umask degeri (Linux)
Get-Content /proc/self/status | Select-String 'Umask'

# dosya izin bitleri
Get-Content /proc/self/fd/0   # sembolik: ls -l

# aktif LSM modulleri
Get-Content /sys/kernel/security/lsm

# Apache yetki kapisi denemesi (yerel)
Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20 https://site/api/x.php
```

Bu komutlarin hicbiri bu oturumda calistirilmadi (**taslak**); calistirilan
negatif kanitlar `-dogrulama.md` D-01..D-43 listesindedir.

## §13 — S1/S2 etkisi ve karar secenekleri (secim YOK)

| Secenek | Ne degisir | Maliyet | Karar sahibi |
|---|---|---|---|
| A | Mevcut durumu koru (DAC + uygulama RBAC) | dusuk | mimari sahibi |
| B | `umask 0077` ekle + `0777` cagrisini `0750`'ye cevir | dusuk | backend-architect |
| C | seccomp/capability devreye al | orta | mimari sahibi + ADR (>=088) |
| D | LSM (SELinux/AppArmor) devreye al | yuksek | mimari sahibi + devops + ADR |

**Bu dosya secim YAPMAZ.** Dort secenek de `open_questions` S1/S2 uzerine
kuruludur ve her biri **yeni bir ADR** gerektirir (ADR 001-037 frozen;
yeni guvenlik ADR taslaklari `>=088`).

## §14 — Riskler

| # | Risk | Etki | Katman |
|---|---|---|---|
| 1 | umask ayarlanmadan `0777` kullanilmasi | paylasilan makinede oturum dizini acik | DAC |
| 2 | middleware sirasinin degistirilmesi | CSRF/Auth onceginin bozulmasi | uygulama |
| 3 | `Permission`'in `Auth`'dan one alinmasi | rolsuz yetki gecisi | uygulama |
| 4 | audit zincirinin kapali olmasi | olay sonrasi izlenebilirlik yok | OS |
| 5 | sysctl hardening'inin uygulanmamasi | symlink/hardlink taklidi | OS |
| 6 | `.htaccess` kapisi olmayan alt dizin | dogrudan PHP calistirma | web sunucusu |

## §15 — Kontrol listesi (revizyon kapisi)

- [ ] Middleware sirasi frozen listeyle birebir ayni mi? (ADR-010)
- [ ] `Permission` her zaman `Auth`'dan sonra mi?
- [ ] `BypassAuth` production'da her zaman false mi?
- [ ] `mkdir` cagrilari beklentiye uygun mod mu veriyor? (§3 kontrasti)
- [ ] `.htaccess` `FilesMatch` kapi(tari) her alt dizinde mi?
- [ ] `in_array` karsilastirmalari strict mi? (§6)
- [ ] Hata kodlari dogru HTTP karsiligina mi cevriliyor? (§11)
- [ ] Audit kaydi (OS seviyesi) acilacaksa sahibi atandi mi? (S1)

## §16 — Referans (HTTP 200, 2026-10-13, envanter disi)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| `umask(2)` olusturulan izinlerden maske cikarir | https://man7.org/linux/man-pages/man2/umask.2.html | 2026-10-13 | HTTP 200 (HEAD) |
| `getumask(3)` maske okuma araci | https://man7.org/linux/man-pages/man3/getumask.3.html | 2026-10-13 | HTTP 200 (HEAD) |
| `fs.*` sysctl anahtarlari dosya sistemi kararlari | https://docs.kernel.org/admin-guide/sysctl/fs.html | 2026-10-13 | HTTP 200 (HEAD) |
| `kernel.*` sysctl anahtarlari cekirdek kararlari | https://docs.kernel.org/admin-guide/sysctl/kernel.html | 2026-10-13 | HTTP 200 (HEAD) |
| `setfacl(1)` POSIX ACL girdisi yazar | https://man7.org/linux/man-pages/man1/setfacl.1.html | 2026-10-13 | HTTP 200 (HEAD) |

Tum URL'ler `-dogrulama.md` §1.2 U-havuzundan alinmistir; 2097'lik K000
katalog envanteriyle kesisim **0** (ayni dosyada olculdu).

## §17 — Acik sorular (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S1 | OS denetim zincirinin (seccomp/ACL/MAC/audit) hangi ayaklari devreye alinacak? | `[UNKNOWN]` | Mimari sahibinin yazili karari + ADR (>=088) |
| S2 | `0777` ve `0750` cagrilari neden tek degere indirgenmiyor? | `[UNKNOWN]` | Mimari sahibi karari; kod degisikligi bu vault'ta YAPILMAZ |

> **SORUMLULUK SINIRI:** Bu dosya mekanizma anlatir. Middleware sirasi,
> izin degeri veya sysctl yapilandirmasi degisikligi **backend-architect /
> devops-engineer** isidir; security-engineer yalnizca denetler.

## §18 — Ayri sistematik zararli yazilim (yetki katmaninin uzantisi)

Yetki katmaninin en ince parcasi, tek bir cagrinin ne yapabilecegini
**onceden** sinirlamaktir (syscall filtresi):

| Mekanizma | Ne yapar | Seviye | Bu repo'da |
|---|---|---|---|
| seccomp-BPF | syscall kategorisini reddeder | proses | etkin degil |
| Landlock | dosya sistemi erisimini kisitlar | proses (kendi kendine) | etkin degil |
| capability | tek bir yetki parcasi verilir | proses | etkin degil |
| Job Object (Windows) | surec agacini kisitlar | proses | etkin degil |

**Ornek kural (anlatim):** bir metin isleyici asagidaki syscall'lari
**görmemeli** bile:

```text
RED  open(path, O_WRONLY)      -> disariya yazma
RED  connect / bind            -> ag cikisi
RED  ptrace                    -> baska sureci izleme
RED  init_module / finit_module-> modul yukleme
ALLOW read / mmap / exit       -> temel calisma
```

`RED` listesi **negatif listedir** (beyaz liste degil): tanimlanmayan her
sey reddedilir -> fail-closed. Bu, §1'deki [2] adiminin somut bicimidir ve
bu sistemde **etkin degildir** (seccomp cagrisi 0 — negatif kanit).

**Neden yine de yazildi:** S1 kapanirken cevaplanmasi gereken soru
"hangi syscall yuzeyi kapatilacak?" sorusudur; cevap verilmeden LSM/capability
karari **anlamsizdir**. Bu tablo o sorunun sorulabilir hale gelmesi icin
vardir, mevcut durum raporu degildir.

## §19 — Yetki kararinda hata yonetimi

Denetim zincirinde hata ile karari ayirmak zorundadir; **hata == red
degildir**, ama red **varsayilan**dir:

| Durum | Davranis | Neden |
|---|---|---|
| ACL okunamadi (I/O hatasi) | **red** (fail-closed) | belirsizlikte erisim kapali |
| inode bilgisi alinamadi | **red** | DAC degerleri bilinmiyor |
| LSM siyaseti yuklenemedi | LSM **devre disi** kalir (bilinen pazarlik) | sistemin acilmasi gerekir |
| oturum verisi bozuksa | 401 (kimlik yok) | red yonu |
| rol alani yoksa | en dusuk yetki (`?? 'user'`) | red yonu |
| izin dizisi degilse | 403 | red yonu |

**Istisna (bilgi):** LSM'in "siyaset yuklenemedi -> modul devre disi"
davranisi **fail-open** bir istisnadir ve kasitlidir: sistem, sahibi
erisemeyen bir kutu haline gelmemelidir. Bu istisna, §1'deki [6] adiminin
tek bilinen acikligidir ve bu yuzden LSM devreye alimi **ayri bir ADR**
 gerektirir (baslik: siyetsizlik yonetimi).

**Uygulama katmaninda ayni kural:** `PermissionMiddleware` I/O hatasi
durumunda istisna firlatmaz; veri yoksa **red yonune** duser (`!is_array`).
Yani her iki katman da ayni felsefeyi paylasir — bu, tasarlarken
korunmasi gereken **tek ortak ozelliktir**.

## §20 — OS grupları (8 platform · 2026-10-14)

> Denetim zincirinin (syscall'ten karara) platform bazli yolu farklidir; platform
> bazli detay ve upstream-docs kaynakli kod ornekleri ayni klasordeki OS
> kartlarindadir (§0 ELI15 · §1 OS profili · §2 kod ornegi `kaynak: upstream docs`
> imzali · §3 repo durumu = hedef referans · §4 sinir cumleleri · §5 referans ·
> §6 acik sorular S1/S2 kapatilmaz).

| OS | Kart | Denetim zinciri karsiligi | Repo durumu |
|---|---|---|---|
| Windows | [`yetki-ve-erisim-modeli-windows.md`](yetki-ve-erisim-modeli-windows.md) | token/privilege denetimi (SeAccessCheck benzeri akis) | hedef referans (0 kullanim) |
| Linux | [`yetki-ve-erisim-modeli-linux.md`](yetki-ve-erisim-modeli-linux.md) | LSM hook zinciri (DAC -> ACL -> MAC -> allow/deny) | hedef referans (0 kullanim) |
| macOS | [`yetki-ve-erisim-modeli-macos.md`](yetki-ve-erisim-modeli-macos.md) | MACF (Mandatory Access Control Framework) + sandbox | hedef referans (0 kullanim) |
| iOS | [`yetki-ve-erisim-modeli-ios.md`](yetki-ve-erisim-modeli-ios.md) | sandbox profile denetimi + entitlement kontrolu | hedef referans (0 kullanim) |
| Android | [`yetki-ve-erisim-modeli-android.md`](yetki-ve-erisim-modeli-android.md) | SELinux hook + binder IPC denetimi | hedef referans (0 kullanim) |
| Tizen | [`yetki-ve-erisim-modeli-tizen.md`](yetki-ve-erisim-modeli-tizen.md) | Smack hook (dosya/IPC etiket denetimi) | hedef referans (0 kullanim) |
| webOS | [`yetki-ve-erisim-modeli-webos.md`](yetki-ve-erisim-modeli-webos.md) | ACG policy denetimi (servis-bazli syscall kisiti) | hedef referans (0 kullanim) |
| ReactOS | [`yetki-ve-erisim-modeli-reactos.md`](yetki-ve-erisim-modeli-reactos.md) | NT executive denetim yolu (tasarim — olgunslasmamis) | hedef referans (0 kullanim) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
