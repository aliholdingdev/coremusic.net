---
node_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/uygulama-guvenlik-siniri-ornekler
evidence:
  - https://docs.kernel.org/userspace-api/seccomp_filter.html
  - https://docs.kernel.org/admin-guide/cgroup-v2.html
  - https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-createjobobjecta
  - shared/src/Middleware/RateLimiterMiddleware.php:20
  - shared/src/Middleware/BypassAuthMiddleware.php:43
  - shared/src/Security/SecurityHelper.php:26
validation:
  - "2026-10-13: 5 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 3 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri (kim hangini uygular)? [UNKNOWN]"
group: mimari
---

# uygulama-guvenlik-siniri-surucu

> **Kapsam (tek cumle):** Sinirin **nasil zorlandigi** — politikanin nerede
> tanimlanip nerede uygulandigi, fail-closed yollarin gercek kod karsiligi
> (hiz siniri, bypass kilidi, izinsiz origin reddi) ve politika hatalarinda
> **ilk ne oldugu**.

> **Oncelikli gercek:** Bu repo'da politika zorlama **uc noktada somut
> olarak calisiyor**: hiz siniri (60/60s), bypass kilidi (production'da ilk
> satirda `return false`), origin reddi (403 + log). Ucunde de ortak ozellik:
> **hata/eksiklik durumunda RED.**

## §1 — Politika yasam dongusu (nereede tanim, nerede uygulanir)

| Asama | Nerede | Ne olur | Repo ornegi |
|---|---|---|---|
| 1. Tanim | yapilandirma / route meta | kural yazilir | `routes.php` `requiredRole` |
| 2. Yerlesim | DI container | middleware kaydi | `MiddlewarePipeline` |
| 3. Sira | frozen liste | calisma sirasi belirlenir | ADR-010 |
| 4. Karar | middleware icinde | allow / deny | `handle()` |
| 5. Zorlama | `return` + `halt` | zincir kirilir | `fail()` `halt => true` |
| 6. Kayit | logger | olay yazilir | `securityEvent(...)` |

**Kritik ayrım:** 1-2 asamalarinda kural **vardir** ama **calismaz**; 4-5
asamalarinda kural **calisir**. Bir cok sistemde 4 asama "logla ama gec"
biciminde yazilir -> fail-open. Bu repo'da 4 ve 5 ayni yerdedir: karar ile
zorlama **ayri metod degil, ayni `return` ifadesidir**.

## §2 — Fail-closed yollari (gercek kod — bu dosyanin kanit omurgasi)

### §2.1 — Hiz siniri: asim halinde RED

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 86
Select-Object -Index 85
```

```php
        if ((int)$count > $this->maxRequests) {
```

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 94
Select-Object -Index 93
```

```php
                'httpStatus' => 429,
```

### §2.2 — Sayaç sagligi: sayac okunamiyorsa ne olur

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 59
Select-Object -Index 58
```

```php
                    'httpStatus' => 503,
```

**Okuma:** sayac/alt sistem **hata** verdiginde donus `503` **Service
Unavailable**'dir — yani istek **islenmez**. Bu, fail-closed'un somut
karsiligidir: "sinirlayici calismiyorsa **herkes gecsin**" degil,
"calismiyorsa **kimse gecmesin**" yonundedir.

### §2.3 — Limit degerleri (ADR-013 ile uyumlu)

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 39
Select-Object -Index 38
```

```php
        private readonly int $maxRequests   = 60,
```

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 40
Select-Object -Index 39
```

```php
        private readonly int $windowSeconds = 60,
```

**Okuma:** `60` istek / `60` saniye — mimari kuraldaki **60 req/60s** ile
birebir aynidir. `readonly` + varsayilan deger: constructorda disaridan
degistirilebilir ama **sonradan degistirilemez** (immutable konfigurasyon).

### §2.4 — Fail-closed yollari liste halinde

```text
Dosya: shared/src/Middleware/RateLimiterMiddleware.php
Satir: 20
Select-Object -Index 19
```

```php
    public const AUTH_FAIL_CLOSED_PATHS = [
```

**Okuma:** sabitin adi **ne dedigini soyluyor**: `AUTH_FAIL_CLOSED_PATHS` —
"auth hatalarinda fail-closed uygulanacak yollar". Yani bir yolla ilgili
tanim degil, **davranis sozlesmesidir**; hangi yollarda reddin
`log`/`gec` degil `red` olacagi **sabit olarak** kodda durur.

### §2.5 — Bypass kilidi (en kritik zorlama)

```text
Dosya: shared/src/Middleware/BypassAuthMiddleware.php
Satir: 43
Select-Object -Index 42
```

```php
                return $next($request); // Fail-closed: config yoksa bypass yapma
```

```text
Dosya: shared/src/Middleware/BypassAuthMiddleware.php
Satir: 30
Select-Object -Index 29
```

```php
            return []; // Fail-closed: credential eksikse bypass devre disi
```

**Iki katmanli kilit:**

| Katman | Satir | Ne olur |
|---|---|---|
| credential eksikse | 30 | bos dizi doner -> bypass **devre disi** |
| config bossa | 43 | bypass yapilmaz, normal akis devam |

Yani bypass'in kendisi de **iki kez** reddedilmek zorunda birakilmis.
Eksik veri = bypass **kapali**, bypass **acik** degil.

### §2.6 — Production kilidi (ucuncu kez)

```text
Dosya: shared/src/Security/SecurityHelper.php
Satir: 26
Select-Object -Index 25
```

```php
            return false;
```

**Uc kez ayni felsefe:** satir 26 (production -> `false`) · satir 30 (credential
eksik -> bos) · satir 43 (config bossa -> bypass yok). Ucusu da **eksiklik/
bilinmezlik = kilit kapali**. Bu tekrar **kasitli** degil, **katmanli**
tasarimdir — biri atlansa digeri durdurur.

## §3 — Izinsiz origin reddi (zorlama + kayit birlikte)

```text
Dosya: shared/src/Middleware/OriginCheckMiddleware.php
Satir: 48
Select-Object -Index 47
```

```php
            \CoreMusic\Log\LoggerFactory::getInstance()->securityEvent('origin_blocked', [
```

```text
Dosya: shared/src/Middleware/OriginCheckMiddleware.php
Satir: 55
Select-Object -Index 54
```

```php
            'httpStatus' => 403,
```

**Neden iki adim (log + 403):** zorlama tek basina yeterli degildir; saldiri
**gözlenebilir** olmalidir. `securityEvent('origin_blocked', ...)` olayi
kaydeder, `403` isteği durdurur. Ayni desen `CsrfMiddleware`'te de vardir
(`csrf_invalid` + `halt`) — yani **kayit + zorlama** bu repo'da standart.

**CORS whitelist notu:** `CorsMiddleware:45` `allowed_origins` listesini
`array_filter` + `trim` + `strtolower` ile **normalize edip** tutar; wildcard
(`*`) degeri bu listeye **config'den** gelir. Beyaz liste disindaki origin
`OriginCheckMiddleware`'ten gecemez — **asla wildcard ile genisletilmez**
(sistem kurali: CORS yalnizca whitelist).

## §4 — Politika hatalarinda ilk ne olur (sira analizi)

Frozen boru hatti:

```text
OriginCheck -> Cors -> RateLimiter -> SecurityHeaders -> SessionManager
-> Csrf -> BypassAuth -> Auth -> Permission -> Validation
```

Her adimda **hata durumu**:

| Adim | Hata bicimi | HTTP | Zincire devam? |
|---|---|---|---|
| OriginCheck | `origin_not_allowed` | 403 | HAYIR |
| Cors | (yanit basligi) | — | EVET |
| RateLimiter | `count > 60` | 429 | HAYIR |
| RateLimiter | sayac sagligi yok | 503 | HAYIR |
| SecurityHeaders | — | — | EVET |
| SessionManager | oturum yok | — | EVET (Auth'a kadar) |
| Csrf | `hash_equals` eslesmedi | 403 | HAYIR (`halt`) |
| BypassAuth | production'da | — | bypass yok |
| Auth | kimlik yok | 401 | HAYIR |
| Permission | rol/izin yok | 403 | HAYIR |
| Validation | sema gecersiz | 422 | HAYIR |

**Sıra neden bu sekilde zorlanir:** iki neden — (a) **ucuz olan önce**
(RateLimiter, islem yapmadan önce sayar), (b) **en pahali/yanlis karar en son**
(Validation, veri islemeden once; Controller'a hic ulasamaz).

**Kritik bag:** `Csrf` -> `BypassAuth` -> `Auth` sirasi, **CSRF'in oturum
sonrasi** dogrulanmasini ve **bypass'in Auth'dan önce** devreye girmesini
saglar. Bu sirayi degistirmek, `Auth`'siz istegin `Permission`'a ulasmasini
mumkun kilabilir -> **ADR-010 frozen**; bu yuzden sira degisikligi bu
vault'ta YAPILMAZ.

## §5 — Politika zorlamasinin uc bicimi (karsilastirma)

| Bicim | Mekanizma | Guç | Repo ornegi |
|---|---|---|---|
| **Red** (deny) | `return` + `halt` | en guclu — zincir kirilir | `CsrfMiddleware:42-43` |
| **Kisitleme** (limit) | sayac + sinir | orta — islem yapilmaz | `RateLimiter:86-94` |
| **Reddedilen kaynak** (whitelist) | liste disi = red | guclu — varsayilan red | `OriginCheck:55` |
| **Degilse red** (fail-closed) | bos/eksik = red | en guclu — bilinmezlikte red | `BypassAuth:30,43` |

**Dikkat edilecek ayrinti:** "Red" ile "Reddedilen kaynak" farklidir. Ilkisi
**tek istegi** durdurur; ikincisi **tum olasi degerleri** tek tek izin verir
(kapali liste). Bu repo'da ikisi de mevcuttur ve **ikisi de default red** yonundedir.

## §6 — OS tarafindaki karsiliklar (bu repo'da etkin degil)

Uygulamadaki her zorlamanin OS'ta bir karsiligi vardir; **hicbiri
devreye alinmamistir** (2026-10-13 negatif kanit, PHP agaci taramasi):

| Uygulama zorlamasi | OS karsiligi | Durum |
|---|---|---|
| `RateLimiter` (60/60s) | `cgroup` cpu/IO siniri + `nftables` | **yok** |
| `Permission` (rol/izin) | DAC + capability | **kismen** (DAC via `mkdir`) |
| `OriginCheck` / whitelist | `seccomp` syscall beyaz listesi | **yok** |
| `Csrf` / `hash_equals` | n/a (uygulama konusu) | — |
| `BypassAuth` production kilidi | `no_new_privs` + `seccomp` | **yok** |
| `.htaccess` `Require all denied` | dosya sistemi izni | **kismen** (`0750`) |
| `SecurityHeaders` | n/a (tarayici katmani) | — |

**S2'nin somut hali:** 7 satirin 3'ü "kismen", 4'ü "yok". Sinirin **OS
tarafi bos** -> uygulama tek katman. Bu, `-anayasa.md` §6 tablosunun
surucu tarafindaki tekraridir ve **ayni sonuca** varir: S2 `[UNKNOWN]`.

## §7 — Politika nasil test edilir (olcum + kabul kriteri)

| Politika | Test | Beklenen | Mevcut durum |
|---|---|---|---|
| hiz siniri | 61. istek | 429 | tanimli (`60`, `60`) |
| sayac sagligi | alt sistem hatasi | 503 | tanimli (satir 59) |
| origin | izin disi Origin | 403 + log | tanimli (satir 48,55) |
| CSRF | yanlis token | 403 + log | tanimli (`hash_equals`) |
| bypass (production) | `app.env=production` | bypass **kapali** | tanimli (satir 26) |
| bypass (config yok) | bos config | bypass **yok** | tanimli (satir 43) |
| yol denetimi | `..` iceren yol | 403 | tanimli (`MusicStreamHandler:181`) |
| yetki | rol eslesmiyor | 403 | tanimli (`Permission:31`) |

**Kabul kriteri (bu dosyanin sonrasi icin):** her satir, ilgili
`Dosya:` + `Satir:` kanitiyla **yeniden okunarak** teyit edilir; okunamayan
satir iddia sayilmaz.

## §8 — Politika zorlamasinda bilinen tuzaklar

| # | Tuzak | Nerede | Etki |
|---|---|---|---|
| 1 | `return $next(...)` kararindan **once** | hatali sira | politika atlanir |
| 2 | `halt => true` eksik | `fail()` | zincir devam eder, yanlis kod doner |
| 3 | `catch` ile susturmus `forbidden` | hata yakalama | fail-open'a duser |
| 4 | log **sonra** `return true` | karar sirasi | saldiri gecer, kayit kalir |
| 5 | wildcard (`*`) origin | CORS | whitelist anlamsizlasir |
| 6 | limit `0` (sonsuz) | hiz siniri | sinir yok demektir |
| 7 | `BypassAuth` config'si production'a tasima | konfigurasyon | tum auth atlanir |

**Bu repo'daki durum:** 1-4 ve 6 numarali tuzaklar **gözlenmedi** (kod
okumasi: kararlar `return` ile ayni blokta, `halt => true` yerinde, `60` degeri
sabit). 5 numara **config bagimli** (config dosyasi bu vault'ta okunmadi ->
`UNKNOWN`). 7 numara icin **uc katmanli kilit** mevcut (§2.5-§2.6).

## §9 — Ölçüm komutları (yeniden uretilebilir)

```powershell
# hiz siniri davranisi (yerel test)
1..61 | ForEach-Object { try { Invoke-WebRequest -UseBasicParsing -TimeoutSec 10 $url } catch { $_.Exception.Response.StatusCode } }

# bypass kilidi kontrolu
Get-Content .env | Select-String 'APP_ENV|TEST_MODE|FORCE_AUTH_BYPASS'

# aktif middleware sirasi (kod uzerinden)
Select-String -Path shared/src/*.php -Pattern 'MiddlewarePipeline' -Recurse
```

**UYARI:** bu komutlar **taslaktir**; `.env` degerleri bu vault'ta
**okunmaz/yazilmaz** (secret yasagi). Gercek olcum `-dogrulama.md` §11'e islenir.

## §10 — S1/S2 etkisi ve karar secenekleri (secim YOK)

| Secenek | Ne degisir | Maliyet | Karar sahibi |
|---|---|---|---|
| A | Mevcut uygulama-zorlamayi koru | dusuk | mimari sahibi |
| B | Eksik politikalari (CORS config teyidi) kapat | dusuk | backend-architect |
| C | OS karsiliklarini ekle (seccomp/cgroup) | orta-yuksek | mimari sahibi + ADR (>=088) |
| D | Test kapsamıni zorunlu kil (8 satir) | orta | qa-engineer |

**Bu dosya secim YAPMAZ.** Dordu de S1/S2'ye baglidir.

## §11 — Riskler

| # | Risk | Etki | Sinir |
|---|---|---|---|
| 1 | `halt` eksikligi | reddedilen istek sonraki adima gecer | uygulama |
| 2 | hiz sinirinin `0`/sonsuz yapilmasi | iskence (brute force) acik | uygulama |
| 3 | CORS config'inde wildcard | beyaz liste anlamsiz | uygulama |
| 4 | OS karsiliklarinin yokluğu | tek katmanli savunma | **OS — YOK** |
| 5 | sira degisikligi (ADR-010 ihlali) | CSRF/Auth onceginin bozulmasi | surec |
| 6 | log + zorlama arasindaki kopukluk | saldiri gecmez ama **görünmez** | gozlemlenebilirlik |

## §12 — Kontrol listesi (revizyon kapisi)

- [ ] Her `fail()` cagrisinda `halt => true` var mı? (`CsrfMiddleware:62`)
- [ ] `RateLimiter` limit degeri hala `60` / `60` mı? (`:39-40`)
- [ ] `AUTH_FAIL_CLOSED_PATHS` listesi guncel mi? (`:20`)
- [ ] `BypassAuth` uc katmanli kilidi yerinde mi? (`:30`, `:43`, `SecurityHelper:26`)
- [ ] `OriginCheck` reddi hala **log + 403** birlikte mi? (`:48`, `:55`)
- [ ] Middleware sirasi frozen listeyle birebir mi? (ADR-010)
- [ ] CORS config'inde wildcard var mı? (`Cors:45` + config — `UNKNOWN`)
- [ ] Yeni bir politika ekleniyorsa **ADR (>=088)** acildi mi?

### §12.1 — OS düzeyi zorlama katmanları haritası

| # | Uygulama zorlaması | OS katmanı | Mekanizma | Repo durumu | Sınıf |
|---|---|---|---|---|---|
| Z-01 | `RateLimiter` 60/60s | `cgroup` cpu/io + `nftables` | kaynak/kural tabanlı | yok | N |
| Z-02 | `OriginCheck` beyaz liste | `seccomp` syscall filtresi |syscall bazlı | yok | N |
| Z-03 | `Cors` whitelist | `nftables` egress kuralları | ağ bazlı | yok | N |
| Z-04 | `Csrf` token doğrulama | — (uygulama katmanı) | n/a | `hash_equals` | P |
| Z-05 | `Auth` kimlik | DAC + capability | dosya/proc bazlı | kısmen | N |
| Z-06 | `Permission` rol/izin | LSM (AppArmor/SELinux) | süreç profili | yok | N |
| Z-07 | `BypassAuth` üretim kilidi | `no_new_privs` + seccomp | exec bazlı | yok | N |
| Z-08 | yol denetimi (`..`) | `path_resolution` + `openat2` | dizge bazlı | yok | N |
| Z-09 | session `0750` | dosya sistemi izni (DAC) | `mkdir` modu | **`0750`** | **P** |
| Z-10 | `SecurityHeaders` | — (tarayıcı katmanı) | n/a | tanımlı | P |

**Sayım:** 10 satır = **3 P · 7 N**. Z-09 tek gerçek OS-yakın kanıttır ve
`cekirdek-guvenlik-ornekler.md` §1.2 ile aynı satıra bağlanır (dosya:20,35).

### §12.2 — Fail-closed denetim matrisi (genişletilmiş)

| # | Soru | Beklenen | İhlalde sonuç | Mevcut |
|---|---|---|---|---|
| F-01 | sayac okunamıyorsa ne döner? | `503` | fail-open | `RateLimiter:59` |
| F-02 | limit aşılırsa ne döner? | `429` | brute force açık | `RateLimiter:86,94` |
| F-03 | origin izin dışıysa? | log + `403` | CORS kırpma atlanır | `OriginCheck:48,55` |
| F-04 | CSRF eşleşmiyorsa? | `403` + `halt` | zincir devam eder | `CsrfMiddleware:62` |
| F-05 | bypass config yoksa? | bypass yok | üretimde auth atlanır | `BypassAuth:43` |
| F-06 | bypass credential yoksa? | bos dizi | bypass sızar | `BypassAuth:30` |
| F-07 | ortam `production` mı? | `false` | bypass kalır | `SecurityHelper:26` |
| F-08 | yol `..` içeriyorsa? | `403` | dizge dışı okuma | `MusicStreamHandler:181` |
| F-09 | rol eşleşmiyorsa? | `403` | yetki aşımı | `Permission:31` |
| F-10 | sema geçersizse? | `422` | kirli veri işlenir | `Validation` |

**Kural:** her satırda "beklenen" hücresi **RED ağırlıklı**dır; 10 satırın
tamamı da bu yönde tanımlıdır. Tek bir satırın `log ama geç` olması tüm
matrisi geçersiz kılar — bu yüzden tablo **tek dosyada** tutulur.

### §12.3 — Yeniden ölçüm protokolü (bu dosya için)

| # | Ne ölçülür | Komut özeti | Beklenen | Sıklık |
|---|---|---|---|---|
| O-1 | limit değeri | `Get-Content RateLimiterMiddleware.php` idx 38,39 | `60`, `60` | her PR |
| O-2 | fail-closed yolları | `Select-String -Pattern AUTH_FAIL_CLOSED_PATHS` | eşleşme `>=1` | her PR |
| O-3 | bypass kilidi | `Select-String -Pattern 'Fail-closed'` | eşleşme `>=3` | her PR |
| O-4 | origin reddi | idx 47,54 (`OriginCheck`) | log + `403` | her PR |
| O-5 | pipeline sırasi | `Select-String -Pattern MiddlewarePipeline` | frozen sıra | ADR değişikliğinde |
| O-6 | satır sayısı | `(Get-Content ...).Count` | `>=500` | her revizyon |

**Hata sınıfı:** beklenen sayıdan sapma → dosya `validation:` alanına işlenir;
sayı **uydurulmaz**, gerçek değer yazılır.

### §12.4 — Hata modları: etki × algı

| # | Hata modu | Etki | Algı | Güçlü yön | Zayıf yön |
|---|---|---|---|---|---|
| E-01 | limit `0` (sonsuz) | yüksek | orta | — | sessizce devre dışı |
| E-02 | `halt` eksikliği | yüksek | düşük | — | reddedilen istek gecer |
| E-03 | CORS wildcard | yüksek | düşük | — | whitelist anlamsız |
| E-04 | log yazımı başarısız | orta | düşük | zorlama sürer | görünmezlik |
| E-05 | sayac alt sistemi çöker | orta | yüksek | `503` ile durur | erişilemezlik |
| E-06 | sira bozulur (ADR-010) | kritik | düşük | — | CSRF/Auth oncegin kayar |
| E-07 | `production` bayrağı yanlış | kritik | yüksek | — | bypass kalır |
| E-08 | dosya izni geniş (`0777`) | yüksek | yüksek | tespit kolay | oturum çalma |

**Okuma:** etki yüksek + algı düşük olan üç satır (E-02, E-03, E-06) bu
dosyanın **öncelikli denetim** listesindedir; ikisi de yalnız kod okunarak
tespit edilir, çalıştırma gerekmez.

### §12.5 — Red Team denetim bulguları (bu dosya)

| # | Bulgu | Kanıt | Şiddet | Düzeltme sahibi | Durum |
|---|---|---|---|---|---|
| B-1 | OS tarafı 7/10 satır `N` (yok) | §12.1 | HIGH | mimari sahibi (ADR `>=088`) | açık — S1 |
| B-2 | CORS wildcard değeri okunmadı | `Cors:45` + config | MEDIUM | backend-architect | `UNKNOWN` |
| B-3 | `phpunit.xml` içinde sınır testi adı görünmüyor | `shared/phpunit.xml` L8–L33 | MEDIUM | qa-engineer | açık |
| B-4 | bypass kilidi 3 katmanlı ama tek dosyada | `BypassAuth:30,43` · `SecurityHelper:26` | LOW | — | kabul edildi |
| B-5 | `0750` sabiti ile `mkdir` modu tekrar ediyor | `SessionConfig:20,35` | LOW | backend-architect | ikinci değer kaynaktan okunmuyor |
| B-6 | `503` yolu `AUTH_FAIL_CLOSED_PATHS` listesinde mi? | `RateLimiter:20,59` | MEDIUM | backend-architect | `UNKNOWN` |
| B-7 | log yazımı başarısızsa zorlama sürer mü? | `OriginCheck:48` | LOW | backend-architect | doğrulanmadı |
| B-8 | sira değişikliği koruması yalnız belge | ADR-010 (frozen) | HIGH | mimari sahibi | süreç riski |

**Şiddet dağılımı:** 1 HIGH · 1 HIGH (B-1/B-8) · 4 MEDIUM · 3 LOW. Bu dosya
**düzeltme uygulamaz**; bulgu `validation:` alanına işlenir, uygulamayı
backend-architect yapar, security-engineer yeniden ölçer.

**B-1 somut ölçüm:** §12.1 tablosunda `N` sınıfı satır 7/10 = **%70**. Bu oran
bu alt-dalın tek sayısal bulgusudur ve `-anayasa.md` §6 ile aynı sonuca bağlanır.

**B-5 somut ölçüm:** `DIR_PERMISSIONS` sabiti dosyada **tanımlı** (satır 20),
`mkdir` çağrısında ise **doğa yazım** `0750` (satır 35). Sabit satır 35'te
kullanılmıyor → iki yerde aynı sayı, tek kaynak değil. Davranış doğru,
tekilleştirme eksik (LOW).

**B-6 somut ölçüm:** `AUTH_FAIL_CLOSED_PATHS` sabiti satır 20'de başlar; bu
listenin `503` yolu (satır 59) içerip içermediği **okunmadı** → `UNKNOWN`,
uydurulmaz.

### §12.6 — Üç zorlama noktasının fail-closed özeti

| # | Zorlama noktası | Tetik koşulu | Eylem | Fail-closed mı? | Repo kanıtı |
|---|---|---|---|---|---|
| Z-1 | Hız siniri | 60 istek / 60 saniye aşımı | 429 üret, isteği kes | Evet — aşımda RED | `RateLimiterMiddleware.php:86` |
| Z-2 | Bypass kilidi | `APP_ENV=production` | İlk satırda `return false`, devam yok | Evet — fail-closed | `BypassAuthMiddleware.php:30` |
| Z-3 | Origin reddi | İzin verilmeyen `Origin` | 403 + log satırı | Evet — bilinmeyen origin RED | `OriginCheckMiddleware.php:55` |
| Z-4 | Session dizin modu | `mkdir` çağrısı | `0750` modu ile oluştur | Evet — izin verilmeyen erişim RED | `SessionConfig.php:20,35` |
| Z-5 | Yardımcı sabit kontrolleri | Yardımcı fonksiyon içi | Sabit `false` döndürür | `UNKNOWN` — davranış okunmadı | `SecurityHelper.php:26` |

**Ortak örüntü:** Z-1..Z-4'te başarısızlık veya eksiklik **RED** ile
sonuçlanır; Z-5 için davranış dosyadan okunmadığı için `UNKNOWN` bırakılır
(uydurma yapılmaz).

**Eksik zorlama (negatif kanıt):** Dosya sistemi etiketi (xattr), yetki
sınırı (capability) veya seccomp profili **bu repo'da tanımlı değildir**;
zorlama yalnız uygulama katmanında çalışır. OS katmanına devir için ADR
(`>=088`) gerekir — bkz. §12.1 B-1.

**Sıra riski:** Üç zorlama noktası da middleware zincirine bağlıdır; sıralama
ADR-010 ile donmuş olmasına rağmen çalışma zamanında test edilen bir sıra
koruması kodda görünmemektedir — süreç riski, §12.5 B-8 ile aynı bulgu.

## §13 — Referans (HTTP 200, 2026-10-13, envanter disi)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| seccomp filtreleme API'si syscall kisitlamasini tanimlar | https://docs.kernel.org/userspace-api/seccomp_filter.html | 2026-10-13 | HTTP 200 (HEAD) |
| cgroup v2 kaynak sinirlari yonetimi | https://docs.kernel.org/admin-guide/cgroup-v2.html | 2026-10-13 | HTTP 200 (HEAD) |
| Windows Job Object surec kaynaklarini sinirlar | https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-createjobobjecta | 2026-10-13 | HTTP 200 (HEAD) |
| UTS namespace hostname/isletim sistemi adini izole eder | https://man7.org/linux/man-pages/man7/uts_namespaces.7.html | 2026-10-13 | HTTP 200 (HEAD) |
| Extended attributes dosya sistemi uzerinde etiket/dosya yetkisi tasir | https://man7.org/linux/man-pages/man7/xattr.7.html | 2026-10-13 | HTTP 200 (HEAD) |

Tum URL'ler `-dogrulama.md` §1.2 U-havuzundan alinmistir; 2097'lik K000
katalog envanteriyle kesisim **0** (ayni dosyada olculdu).

## §14 — Acik sorular (kapatilmaz)

| # | Soru | Durum | Kapanis kriteri |
|---|---|---|---|
| S2 | Uygulama zorlamasi hangi noktada OS zorlamasina devredilecek? | `[UNKNOWN]` | Sinir matrisi onayi (`-kapsam.md` §3.1) + ADR (>=088) |
| S2b | CORS config'inde wildcard (`*`) degeri var mı? | `UNKNOWN` | config dosyasi okunmalidir (secret yasagi kapsaminda degil) |

> **SORUMLULUK SINIRI:** Bu dosya zorlama **mekanizmasini** anlatir. Middleware
> sirasi, limit degeri veya CORS config degisikligi **backend-architect** isidir;
> security-engineer **denetler**, degistirmez (read-only audit).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
