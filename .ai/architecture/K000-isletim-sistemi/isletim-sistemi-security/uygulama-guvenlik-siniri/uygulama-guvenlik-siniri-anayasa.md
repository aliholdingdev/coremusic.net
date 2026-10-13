---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security
  - K000-isletim-sistemi/isletim-sistemi-runtime
evidence:
  - https://www.kernel.org/doc/html/latest/userspace-api/seccomp_filter.html
  - https://github.com/containers/bubblewrap
  - https://developer.apple.com/documentation/security/app-sandbox
  - https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - home.coremusic.net/include/Stream/MusicStreamHandler.php:200
  - shared/src/AI/AIEngine.php:292
validation:
  - "2026-10-13: 5 web URL HEAD ile HTTP 200; ucu da 2097'lik katalog envanterinde YOK -> kesisim 0."
  - "2026-10-13: 2 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S1: OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: mimari
---

# uygulama-guvenlik-siniri

> **Kapsam (tek cumle):** Uygulama ile isletim sistemi arasindaki **sinirin
> nereden gectigi** — hangi karar uygulamanin (gercek yol denetimi, arguman
> temizligi, basliklar), hangi karar OS'in (syscall reddi, yetki biti, konteyner
> kisiti), ve bu sinirin bu repo'daki **gözlemlenen karsiligi**.

> **Oncelikli gercek:** Bu sinirin **uygulama tarafi tam calisiyor** (yol
> denetimi 6 adimli, arguman temizligi strict, basliklar 9 satir, CSRF
> timing-safe); **OS tarafi etkin degil** (seccomp/capability/namespace 0
> kullanim). Yani sinir su an **tek katmanli** — ve bu, S2 acik sorusunun
> tam kendisidir.

## §1 — Sinirin tanimi (kim neye karar verir)

| Karar | Sahibi | Neden o sahip | Repo'da |
|---|---|---|---|
| giris gecerli mi (sema) | uygulama | veri formatini uygulama bilir | `Validation` middleware |
| yol dosya sisteminde mi | uygulama | `realpath` uygulama seviyesinde | **var** (6 adimli) |
| arguman shell'e guvenli mi | uygulama | shell ayristirma uygulamada | **var** (`escapeshellarg`) |
| hangi syscall'a izin var | **OS** | cekirdek goruntuler | **yok** (seccomp 0) |
| proses hangi dosyayi acabilir | **OS** | inode/etiket | **kismen** (DAC via mkdir) |
| proses hangi portu acabilir | **OS** | capability | **yok** (0) |
| kaynak tuketimi siniri | **OS** | cgroup/Job Object | **yok** (0) |
| HTTP basliklari neler | uygulama | tarayiciyla uygulama arasi | **var** (9 baslik) |
| istek kimden geliyor | uygulama | Origin/CSRF | **var** |

**Temel ilke:** sinir, "uygulama **bilmedigi** seyi OS'e birakir". Uygulama
**bilmedigi** sey: bir sonraki syscall'in ne oldugu, dis process'in ne yaptigi,
baska kullanicinin ne okudugu. Bunlar OS katmanindadir.

## §2 — Uygulama tarafinin gordugu yuzey (bu repo)

### §2.1 — Yol denetimi (6 adimli zincir — gercek kod)

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 181
Select-Object -Index 180
```

```php
        if (in_array('..', $segments, true)) {
```

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 185
Select-Object -Index 184
```

```php
        if (is_link($trimmed)) {
```

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 189
Select-Object -Index 188
```

```php
        $real = realpath($trimmed);
```

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 194
Select-Object -Index 193
```

```php
        $root = realpath($this->libraryPath);
```

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 199
Select-Object -Index 198
```

```php
        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
```

```text
Dosya: home.coremusic.net/include/Stream/MusicStreamHandler.php
Satir: 200
Select-Object -Index 199
```

```php
        if (!str_starts_with($real, $prefix)) {
```

### §2.2 — Zincirin adim adim okumasi

| # | Satir | Ne yapar | Hangi saldiriyi onler |
|---|---|---|---|
| 1 | 181 | `..` segmenti var mi (strict) | klasik dizin disi gecis |
| 2 | 185 | symlink mi? | link ile baz dizinden kacis |
| 3 | 189 | gercek yola indir (`realpath`) | normalize edilmemis yol |
| 4 | 190-192 | dosya var mi + dosya mi | yoksa 404 (bilgi vermez) |
| 5 | 194 | baz dizini de gerceklestir | baz dizin kendisi symlinkse |
| 6 | 199-202 | `str_starts_with` + **`DIRECTORY_SEPARATOR`** | onek karistirilmasi |

**Neden 6 adim gerekli (tek tek aciklama):**

1. **Adim 1** tek basina yeterli degildir: `..` olmadan da `/etc/passwd`
   mutlak yol olarak gelebilir.
2. **Adim 2** onemlidir: `realpath` symlink'i **cozer**, yani sonradan
   kontrol etmek istesek de etkisizdir — bu yuzden symlink **once**
   reddedilir (TOCTOU onlemi).
3. **Adim 3** mutlak yola indirger; karsilastirma ancak o zaman anlamlidir.
4. **Adim 5** atlanirsa, `libraryPath` kendisi bir symlink ise karsilastirma
   **hep tutar** (yanlis-guvenli degil, yanlis-guvenli DEGIL — yani acik).
5. **Adim 6'daki `DIRECTORY_SEPARATOR`** kritiktir: `str_starts_with('/x/ab',
   '/x/a')` **true** donerdi — onek karistirmasi (prefix confusion). Separator
   eklenerek `/x/a` ile `/x/ab/` ayrilir.

**Kalip adi:** "resolve-then-prefix" — normalise et, sonra onek karsilastir,
separator ekle. OWASP path traversal karsit onlemidir.

### §2.3 — Arguman temizligi (shell siniri — gercek kod)

```text
Dosya: shared/src/AI/AIEngine.php
Satir: 292
Select-Object -Index 291
```

```php
            escapeshellarg($filePath)
```

```text
Dosya: shared/src/AI/AIEngine.php
Satir: 295
Select-Object -Index 294
```

```php
        $output = shell_exec($cmd);
```

**Okuma:** `$filePath` **once** `escapeshellarg` ile sarilir, **sonra**
komut satirina gecirilir (satir 290'daki `sprintf` sablonu `"..."` ile sarili).
Ters sira olsaydi (once yerlestir, sonra temizle) imlecler zaten islenmis olurdu.

**Sinir burada biter:** `escapeshellarg` **yalnizca** shell ayristirma
sorununu cozer. Cagrilan `ffprobe`'un `open()`'u, `connect()`'i veya dosya
okumasi **aynen calisir** — cunku onu durduracak sey uygulama degil, **OS'tir**
(capability / sandbox). §1'deki "hangi syscall'a izin var" satiri tam
buradaki bosluktur.

### §2.4 — HTTP basliklari (tarayici siniri — gercek kod)

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php
Satir: 27
Select-Object -Index 26
```

```php
            'X-Content-Type-Options'         => 'nosniff',
```

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php
Satir: 28
Select-Object -Index 27
```

```php
            'X-Frame-Options'                => 'DENY',
```

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php
Satir: 31
Select-Object -Index 30
```

```php
            'Content-Security-Policy'        => $csp,
```

```text
Dosya: shared/src/Middleware/SecurityHeadersMiddleware.php
Satir: 71
Select-Object -Index 70
```

```php
            "frame-ancestors 'none'; " .
```

**Dokuz baslik + sartli 2:**

| Baslik | Satir | Ne korur |
|---|---|---|
| `X-Content-Type-Options: nosniff` | 27 | MIME sniffing ile script sacilmasi |
| `X-Frame-Options: DENY` | 28 | clickjacking (eski tarayicilar) |
| `X-XSS-Protection: '0'` | 29 | eski tarayicinin kendi XSS filtresi (bilincli kapali) |
| `Referrer-Policy` | 30 | URL sifresi referans ile sizmasi |
| `Content-Security-Policy` | 31 | XSS kaynak kisiti (ana kalkan) |
| `Require-Trusted-Types-For: 'script'` | 32 | DOM XSS (script insertion) |
| `Cross-Origin-Resource-Policy` | 33 | kaynaksiz kaynak cekimi |
| `Permissions-Policy` | 34 | kamera/mikrofon/coğrafi |
| `X-Permitted-Cross-Domain-Policies` | 35 | Flash tarzi cross-domain |
| `Cross-Origin-Opener-Policy` (HTTPS) | 39 | process izolasyonu |
| `Cross-Origin-Embedder-Policy` (HTTPS) | 40 | kaynak izolasyonu |

**CSP notu:** `frame-ancestors 'none'` (satir 71) `X-Frame-Options`'un
**modern karsiligidir**; ikisi birlikte tutulur (geriye donuk uyumluluk).
`base-uri 'self'` (72) ve `form-action 'self'` (73) da ayni CSP blogundadir —
ucu birlikte `<base>` manipülasyonu ve form disari gonderimini kapatir.

### §2.5 — CSRF (istek siniri — gercek kod)

```text
Dosya: shared/src/Middleware/CsrfMiddleware.php
Satir: 42
Select-Object -Index 41
```

```php
        if ($sessionToken === '' || $submitted === '' || !hash_equals($sessionToken, (string)$submitted)) {
```

```text
Dosya: shared/src/Middleware/CsrfMiddleware.php
Satir: 39
Select-Object -Index 38
```

```php
            $submitted = $request['body']['csrf_token'] ?? '';
```

**Neden guvenli:** `hash_equals` **timing-safe** karsilastirma — byte byte
karsilastirma yapar, esitlik durumunda erken donmez. Normal `===` de dogru
sonuc verirdi ama **zamanlama kanali** birakirdi (uzak saldirida pratikte
zor, yine de dogru kalip `hash_equals`).

**Token adi:** `csrf_token` (gövde) + `x-csrf-token` (baslik). **`_csrf_token`
degil** — token adi mimari karardir ve bu repo'da `csrf_token` olarak
gözlenmektedir (satir 39 ve satir 37).

**Red kaynagi:** satir 43 `return $this->fail()` — `halt => true` (satir 62),
yani zincir **durdurulur**, sonraki middleware'a gecilmez.

## §3 — OS tarafinin gordugu yuzey (bu repo'da etkin DEGIL)

### §3.1 — syscall sinirlama (seccomp)

| Mekanizma | Ne yapar | Durum |
|---|---|---|
| seccomp-BPF | syscall kategorisini reddeder | **0 kullanim** |
| syscall listesi | beyaz/kara liste | yok |
| `SCMP_ACT_ERRNO` | reddi errno ile doner | yok |
| `SCMP_ACT_KILL` | ihlalde sureci sonlandirir | yok |

### §3.2 — Kapsayici ve izolasyon

| Mekanizma | Ne yapar | Durum |
|---|---|---|
| namespace (pid/net/mnt) | gorunum izolasyonu | **0 kullanim** |
| cgroup v2 | kaynak siniri (cpu/bellek) | **0 kullanim** |
| bubblewrap | hafif sandbox (bwrap) | **0 kullanim** |
| Job Object (Windows) | surec agaci siniri | **0 kullanim** |
| App Sandbox (Apple) | uygulama izni siniri | **0 kullanim** |

**Tek istisna (dikkat):** `Container` adinda PHP **sinif** kullanilabilir —
bu bir **DI (bagimlilik enjeksiyonu) kapsayicisidir**, **OS konteyneri
degildir**. Iki kavram ayni sozcuğu paylasir ama **farkli katmandadir**;
bu vault'ta karistirilmamalidir (ayrim §5).

### §3.3 — Olumlu kanit aranmis, bulunamadi (negatif defter)

Tarama: `seccomp`, `bwrap`, `unshare`, `clone(`, `CLONE_NEWNET`, `bpf(`,
`CreateJobObject`, `SetCurrentThreadProfile` — PHP agacinda (vendor/node_modules
haric) **0 sonuc**. Bu bir **kanittir**, "bulunamadi" degil: arac-SO
olcusunun sonucudur ve `-dogrulama.md` D-01..D43 defterine islenmistir.

## §4 — userland <-> kernel gecis noktalari

```text
[uygulama]  sprintf -> escapeshellarg -> shell_exec
              ↓ (shell siniri — uygulama tarafı)
[kullanici alanindaki arac]  ffprobe / bash
              ↓ (syscall siniri — OS tarafı, bu repo'da FILTRE YOK)
[cekirdek]  sys_open / sys_connect / sys_read ...
              ↓
[VFS]       DAC -> ACL -> MAC  (yetki-ve-erisim-modeli dali)
              ↓
[donanim / dosya]
```

**Her ok tam nerede kirilir:**

| Gecis | Ne kaybolur | Kim korur |
|---|---|---|
| uygulama -> shell | veri butunlugu (imlec) | uygulama (`escapeshellarg`) |
| shell -> syscall | **ne yapilacagi** | **OS (seccomp) — YOK** |
| syscall -> VFS | erisim karari | OS (DAC/ACL/MAC) — kismen |
| VFS -> donanim | fiziksel kaynak | OS (cgroup) — YOK |

**S2'nin somut hali:** ikinci ve dorduncu ok **su an okunmamaktadir**. Bu
iki okun hangisinin devreye alinacagi, S2'nin kapali sorusudur.

## §5 — Kavram karisikligi onleyici tablo

| Terim | Ne DEGILDIR | Katman |
|---|---|---|
| PHP `Container` (DI) | OS konteyneri (Docker/LXC) | uygulama |
| `escapeshellarg` | sandbox | uygulama |
| sandbox | yetki modeli (RBAC) | farkli katman |
| namespace (OS) | PHP namespace (`namespace X;`) | farkli katman |
| `.htaccess` kapi | syscall filtresi | web sunucusu |
| CSP basligi | kaynak kod guvenligi | tarayici |
| cgroup | ACL | OS |
| RBAC middleware | OS yetki biti | uygulama |

**Bu tablonun onemi:** ayni sozcukler (container, namespace, sandbox) iki
katmanda da kullanilir; biri digerini **göstermez**. Vault'ta her iddia
**katman etiketiyle** yazilmalidir (bu dosyanin tum tablolarinda oldugu gibi).

## §6 — Sinirin bu repo'daki dengesi (ozet)

| Yuzey | Uygulama tarafi | OS tarafi | Denge |
|---|---|---|---|
| yol denetimi | **6 adimli, tam** | n/a (uygulama isi) | uygulama |
| arguman temizligi | **strict** | syscall filtresi **YOK** | **tek katman** |
| HTTP basligi | **9 + 2 sartli** | n/a | uygulama |
| CSRF | **timing-safe** | n/a | uygulama |
| erisim (dosya) | `mkdir` modu | DAC/ACL/MAC | **kismen** OS |
| kaynak siniri | n/a | cgroup **YOK** | **hic yok** |
| surec izolasyonu | n/a | namespace **YOK** | **hic yok** |

**Okuma:** uygulama tarafi **olgun**; OS tarafi **bos**. Bu, K000'un
`isletim-sistemi-security` dalinin ana tezi ve S1/S2 acik sorularinin
kanitidir.

## §7 — Sinir tasarimi kurallari (bu dalin anayasasi)

1. **Derinlikli savunma:** her kritik karar **en az iki katmanda** tekrarlanir.
   (Bu repo'da: yol denetimi uygulamada + `.htaccess` kapisinda — EVET;
   arguman temizligi uygulamada + syscall filtresinde — **HAYIR**.)
2. **Fail-closed:** belirsizlikte **red**. (`hash_equals` bos token -> red;
   `?? 'user'` -> en az yetki; `str_starts_with` -> disarda ise 403.)
3. **Sinir tek yerden okunur:** hangi kararin kimde oldugu **tek tabloda**
   tutulur (§1). Dagilirsa iki katman da "digisi halleder" der.
4. **Kavram ayrimi:** ayni sozcuk iki katmanda varsa **katman etiketi** zorunlu (§5).
5. **Negatif kanit de kandir:** "yok" bir bulgudur; `-dogrulama.md`'ye islenir.
6. **Sinir degisikligi = ADR:** OS tarafina yeni bir ok eklemek (seccomp,
   cgroup, namespace) **yeni bir ADR** ister (>=088), cunku mevcut her
   uygulama davranisini etkiler.

## §8 — Olcum komutlari (yeniden uretilebilir)

```powershell
# seccomp / LSM durumu (Linux hedefi)
Get-Content /proc/self/status | Select-String 'Seccomp|NoNewPrivs|CapEff'

# namespace gosterimi
Get-Content /proc/self/ns/pid   # sembolik: ls -l /proc/self/ns

# cgroup uyeligi
Get-Content /proc/self/cgroup

# konteyner belirtisi (docker)
Test-Path /.dockerenv
```

Bu komutlarin hicbiri bu oturumda calistirilmadi (**taslak**); PHP agaci
taramasinin negatif sonuclari `-dogrulama.md` D-01..D43 listesindedir.

## §9 — S1/S2 etkisi ve karar secenekleri (secim YOK)

| Secenek | Ne ekler | Etki | Karar sahibi |
|---|---|---|---|
| A | Mevcut durum (tek katman) | dusuk riskli web, orta sunucu riski | mimari sahibi |
| B | `NoNewPrivs` + `umask` hardening | dusuk maliyet | devops-engineer |
| C | seccomp-BPF (syscall beyaz listesi) | orta — test yuku | mimari sahibi + ADR (>=088) |
| D | namespace + cgroup (konteyner) | yuksek — altyapi degisikligi | mimari sahibi + devops + ADR |

**Bu dosya secim YAPMAZ.** Dort secenek de S1/S2'ye baglidir.

## §10 — Riskler

| # | Risk | Etki | Sinir |
|---|---|---|---|
| 1 | syscall filtresi olmayisi | `ffprobe`/shell yanlislikla her seyi yapabilir | OS oku **YOK** |
| 2 | kaynak siniri olmayisi | tek istek diskin tamamini doldurabilir | OS oku **YOK** |
| 3 | yol denetiminin tek katmanli olmasi | bir adim unutulursa traversal acilir | uygulama |
| 4 | `shell_exec` varligi | komut calistirma yuzeyi her zaman acik | uygulama |
| 5 | `escapeshellarg`'in sarmalanma sira hatasi | imlec kacisi | uygulama |
| 6 | kavram karisikligi (DI container = OS container) | yanlis guven | surec |

## §11 — Kontrol listesi (revizyon kapisi)

- [ ] Yol denetimi zincirinde 6 adim da yerinde mi? (§2.2)
- [ ] `escapeshellarg` **once** mi, sonra **yerlestirme** mi? (§2.3)
- [ ] `hash_equals` timing-safe karsilastirma mi? (§2.5)
- [ ] CSP `frame-ancestors` + `X-Frame-Options` **ikisi de** var mi? (§2.4)
- [ ] Token adi `csrf_token` mi (`_csrf_token` degil)? (§2.5)
- [ ] Yeni bir syscall/sandbox eklendiyse **ADR (>=088)** acildi mi? (§7.6)
- [ ] Her iddia **katman etiketi** tasiyor mu? (§5)
- [ ] Negatif kanitlar `-dogrulama.md`'ye islenmis mi? (§3.3)

## §12 — Referans (HTTP 200, 2026-10-13, envanter disi)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| seccomp filtreleme API'si syscall kisitlamasini tanimlar | https://www.kernel.org/doc/html/latest/userspace-api/seccomp_filter.html | 2026-10-13 | HTTP 200 (HEAD) |
| bubblewrap hafif Linux sandbox aracidir | https://github.com/containers/bubblewrap | 2026-10-13 | HTTP 200 (HEAD) |
| Apple App Sandbox uygulama izinlerini sinirlar | https://developer.apple.com/documentation/security/app-sandbox | 2026-10-13 | HTTP 200 (HEAD) |
| Windows Job Object surec kaynaklarini sinirlar | https://learn.microsoft.com/en-us/windows/win32/procthread/job-objects | 2026-10-13 | HTTP 200 (HEAD) |
| cgroup v2 kaynak sinirlari yonetimi | https://docs.kernel.org/admin-guide/cgroup-v2.html | 2026-10-13 | HTTP 200 (HEAD) |

Tum URL'ler `-dogrulama.md` §1.2 U-havuzundan alinmistir; 2097'lik K000
katalog envanteriyle kesisim **0** (ayni dosyada olculdu).

## §13 — Acik sorular (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S1 | OS guvenlik katmani (capabilities/seccomp/LSM) hedef mimaride devreye alinacak mi? | `[UNKNOWN]` | Mimari sahibinin yazili karari + ADR (yeni ise `>=088`) |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? | `[UNKNOWN]` | Sinir matrisi onayi (`isletim-sistemi-security-kapsam.md` §3.1) |

> **SORUMLULUK SINIRI:** Bu dosya sinir **tasarimi** tasir. `shell_exec`
> kaldirilmasi, sandbox eklenmesi veya cgroup yapilandirmasi
> **backend-architect / devops-engineer** isidir; security-engineer denetler.

## §14 — Sinir uygulama ornegi: bir medya isteginin tum yolu

Bu dalin anayasasinin somutlastirilmis hali — bir `GET /stream/...` isteginin
gectigi tum katmanlar ve **her katmanda kimin karar verdigi**:

| # | Katman | Dosya/Satir | Karar | Sahip |
|---|---|---|---|---|
| 1 | Origin | `OriginCheckMiddleware` | kaynak izin listesinde mi | uygulama |
| 2 | Rate limit | `RateLimiterMiddleware` | 60/60s asildi mi | uygulama |
| 3 | Baslik | `SecurityHeadersMiddleware:27-35` | 9 baslik yazildi | uygulama |
| 4 | Oturum | `SessionManager` | oturum gecerli mi | uygulama |
| 5 | CSRF | `CsrfMiddleware:42` | token `hash_equals` ile eslesti mi | uygulama |
| 6 | Auth | `AuthMiddleware:23` | `userId` gecerli mi | uygulama |
| 7 | Yetki | `PermissionMiddleware:31,39` | rol + izin eslesti mi | uygulama |
| 8 | Yol | `MusicStreamHandler:181-201` | 6 adimli denetim | uygulama |
| 9 | Dosya ac | syscall `open` | DAC bitleri | **OS** |
| 10 | Kaynak | — | bellek/disk siniri | **OS — YOK** |

**Okuma:** 1-8 numarali adimlarin **tamami uygulama katmanindadir** ve bu
repo'da **gözlemlenebilir** (kod kaniti §2). 9 numarali adim OS'tadir ve
`mkdir` modu disinda **yapilandirma yoktur**. 10 numarali adim **hiç yoktur**.

**Sonuc:** bu tablo, S2 acik sorusunu **rakamla** gosterir: on adimin dokuzu
uygulamada, biri OS'te yarim, biri **hic yok**. OS tarafinin hangi adimlarinin
devreye alinacagi mimari sahibi kararidir (ADR `>=088`).

## §15 — Bu dosyanin kanit baglantisi

| İddia | Kanit tipi | Nerede |
|---|---|---|
| 6 adimli yol denetimi | disk (6 kod satiri) | §2.1 + `-ornekler.md` |
| `escapeshellarg` oncesi/sonrasi | disk (2 kod satiri) | §2.3 + `-ornekler.md` |
| 9 + 2 baslik | disk (4 kod satiri) | §2.4 + `-ornekler.md` |
| CSRF `hash_equals` + `csrf_token` | disk (2 kod satiri) | §2.5 + `-ornekler.md` |
| seccomp/cgroup/namespace 0 kullanim | negatif kanit | `-dogrulama.md` D-01..D43 |
| 5 URL HTTP 200 + envanter disi | web (HEAD) | `-dogrulama.md` §1.2 U-havuzu |

> **Kanit zinciri kurali:** Bu dosyadaki her iddia ya bir `Dosya:` + `Satir:`
> kod satiri, ya bir U-havuzu URL'si, ya da bir negatif olcumdur; hicbiri
> model hafizasindan alinmamistir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
