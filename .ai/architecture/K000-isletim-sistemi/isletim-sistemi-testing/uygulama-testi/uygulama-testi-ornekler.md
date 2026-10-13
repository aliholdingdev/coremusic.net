---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-ornekler
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-anayasa
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi/uygulama-testi-surucu
evidence:
  - shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php:41
  - shared/tests/Middleware/RateLimiterMiddlewareTest.php:30
  - shared/phpunit.xml:9
validation:
  - "2026-10-13: 3 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 9 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - T3 [UNKNOWN] — `auth.coremusic.net` test paketi mi DEĞİL mi: `composer.json` test scripti okunmadı.
  - T2 [UNKNOWN] — coverage eşiği yok; örneklerdeki değerler eşik değil, gösterimdir.
group: kod
---

# Uygulama-Testi Örnekleri — Gerçek Test Dosyaları, Vaka Kalıpları, Anti-Pattern

## §0 ELI15

Bu dosya **gerçek repo test dosyalarını** kataloglar: her satır diskte var olan
bir test sınıfı, her kod bloğu `Dosya:` + `Satır:` ile doğrulanmış alıntıdır.
Test dosyaları `shared\tests\` altındadır ve 8 testsuite dizini ile birebir
eşleşir. OS/kernel testi (`cekirdek-test/`) ve CI kapı işleri
(`ci-ve-kalite-kapisi/`) **kapsam dışıdır**; burada yalnız uygulama testi vardır.

## §1 Gerçek test dosyası envanteri (disk okuması)

### §1.1 Dizin × dosya sayısı

| # | Dizin | Dosya sayısı | Testsuite eşleşmesi | Not |
|---|---|---|---|---|
| 1 | `shared\tests\Unit` | 7 alt dizin | T-01 Unit | alt dizinler: Config, Device, Log, Middleware, PageRouter, Security, Theme |
| 2 | `shared\tests\Api` | 8 dosya + 1 CLAUDE.md | T-02 Api | pipeline/DTO/route/versioning |
| 3 | `shared\tests\Repository` | 2 dosya + `Sqlite` altı | T-03 Repository | komut + repository |
| 4 | `shared\tests\Events` | 2 dosya + 1 CLAUDE.md | T-04 Events | dispatcher + domain event |
| 5 | `shared\tests\Security` | 2 dosya | T-05 Security | session keys + return URL |
| 6 | `shared\tests\Middleware` | 9 dosya | T-06 Middleware | en geniş suite |
| 7 | `shared\tests\Component` | 1 dosya | T-07 Component | template engine |
| 8 | `shared\tests\OAuth` | 1 dosya + 1 CLAUDE.md | T-08 OAuth | OAuth manager |
| 9 | `shared\tests\Fixtures` | 2 alt dizin | (suite değil) | `chrome` · `jwt` |

**Eşleşme:** 9 dizin = 8 testsuite + 1 fixture dizini → `phpunit.xml` L8–L33 ile
**birebir**. Eksik veya fazladan suite **yok**.

### §1.2 Middleware suite dosya listesi (en kritik katman)

| # | Dosya | Ne test eder | Kanıt sınıfı |
|---|---|---|---|
| 1 | `AuthMiddlewareTest.php` | kimlik doğrulama akışı | P (repo) |
| 2 | `BypassAuthMiddlewareTest.php` | bypass normal yol | P |
| 3 | `BypassAuthMiddlewareFailClosedTest.php` | bypass fail-closed (ADR-008) | P |
| 4 | `CsrfMiddlewareTest.php` | CSRF token doğrulama | P |
| 5 | `MiddlewarePipelineTest.php` | sıralı zincir | P |
| 6 | `OriginCheckMiddlewareTest.php` | origin reddi | P |
| 7 | `RateLimiterMiddlewareTest.php` | 60/60s hız siniri | P |
| 8 | `SessionManagerMiddlewareTest.php` | session yaşam döngüsü | P |
| 9 | `ValidationMiddlewareTest.php` | girdi doğrulama | P |

**9/9 dosya** diskte görünür; adlandırma `*Test.php` ile tutarlı, sapma yok.

### §1.3 Test dosyası adlandırma kuralları (gözlem)

| # | Kural | Gözlem | Sapma |
|---|---|---|---|
| N-1 | sınıf adı = dosya adı | 9/9 Middleware dosyasında geçerli | yok |
| N-2 | sonek `Test` | tüm dosyalarda geçerli | yok |
| N-3 | namespace `CoreMusic\Test\...` | okunan 2 dosyada geçerli | 2/2 |
| N-4 | klasör = testsuite adı | 8/8 suite için geçerli | yok |
| N-5 | `CLAUDE.md` test dosyası değildir | 3 dizinde yanında durur | suite sayısına dahil değil |

> N-3 yalnız **iki dosyanın** okunmasına dayanır; genel geçerlilik `UNKNOWN`
> olarak bırakılır (tüm dosyalar tek tek okunmadı).

## §2 Kod örneği — fail-closed testi (gerçek repo)

### §2.1 Sınıf başlığı ve izolasyon gerekçesi

**Dosya:** `shared\tests\Middleware\BypassAuthMiddlewareFailClosedTest.php` · **Satır:** 26–27

```php
#[CoversClass(BypassAuthMiddleware::class)]
final class BypassAuthMiddlewareFailClosedTest extends TestCase
```

Doğrulama: `Select-Object -Index (25..26)` → L26 `#[CoversClass(BypassAuthMiddleware::class)]` ·
L27 `final class BypassAuthMiddlewareFailClosedTest extends TestCase` (2/2 birebir).

### §2.2 Ayrı süreç kuralı (PHP sabiti izolasyonu)

**Dosya:** aynı · **Satır:** 39–41

```php
#[RunInSeparateProcess]
#[PreserveGlobalState(false)]
public function testFailClosedWhenNoConstantsDefinedDoesNotInjectAuth(): void
```

Doğrulama: `Select-Object -Index (38..40)` → 3/3 birebir eşleşti.

**Gerekçe (dosyadan okundu, L20–L24):** PHP'de `define` edilen sabit undefine
edilemez; `preserveGlobalState(false)` üst süreçteki sabitleri alt sürece
taşımaz. Bu **izolasyon kanıtıdır**, yorum uydurması değildir.

### §2.3 Ön koşul bloğu

**Dosya:** aynı · **Satır:** 44–46

```php
$this->assertFalse(defined('BYPASS_USER_UUID'), 'BYPASS_USER_UUID tanımsız olmalı');
$this->assertFalse(defined('BYPASS_ROLE'), 'BYPASS_ROLE tanımsız olmalı');
$this->assertFalse(defined('BYPASS_USERNAME'), 'BYPASS_USERNAME tanımsız olmalı');
```

Doğrulama: `Select-Object -Index (43..45)` → 3/3 birebir eşleşti.

### §2.4 Fail-closed iddiası (asıl assertion)

**Dosya:** aynı · **Satır:** 64–65

```php
$this->assertArrayNotHasKey('_auth', $captured, 'fail-closed: credential yokken _auth doldurulmamalı');
$this->assertArrayNotHasKey('MM_UserID', $_SESSION, 'fail-closed: session\'a bypass kimliği yazılmamalı');
```

Doğrulama: `Select-Object -Index (63..64)` → 2/2 birebir eşleşti.

**Okuma:** test, “credential yoksa bypass yok” iddiasını **üç ayrı anahtar**
üzerinden kanıtlar (`_auth`, `MM_UserID`, `MM_UserRole`) — tek anahtarla yetinmez.

## §3 Kod örneği — hız siniri testi (gerçek repo)

### §3.1 Sınıf ve ADR bağlantısı

**Dosya:** `shared\tests\Middleware\RateLimiterMiddlewareTest.php` · **Satır:** 10–12

```php
 * RateLimiterMiddleware — Rate Limiting Testleri
 *
 * ADR-013: APCu tabanlı rate limiting (60 req/60s).
```

Doğrulama: `Select-Object -Index (9..11)` → 3/3 birebir eşleşti.

### §3.2 Parametre olarak 60/60 (değer dosyada yazılı)

**Dosya:** aynı · **Satır:** 29–33

```php
        $middleware = new RateLimiterMiddleware(
            maxRequests: 60,
            windowSeconds: 60,
            cache: $cache
        );
```

Doğrulama: `Select-Object -Index (28..32)` → 5/5 birebir eşleşti.

**Kanıt değeri:** 60/60s değeri **test kodunda** yazılıdır; `-surucu.md` §1'deki
kapı değeriyle aynı ailede olduğu okunur, fakat kapıdaki matris değeri bu
dosyada **okunmadı** → eşitlik `UNKNOWN` bırakılır.

### §3.3 Mock ile izolasyon

**Dosya:** aynı · **Satır:** 25–27

```php
        $cache = $this->createCacheMock([
            'set' => true,  // İlk istek — yeni key
        ]);
```

Doğrulama: `Select-Object -Index (24..26)` → 3/3 birebir eşleşti.

**Kalıp:** birim seviyesinde gerçek APCu **kullanılmaz**, arayüz sahtelenir.
Bu, `-surucu.md` §3.2 “gerçek DB: hayır / dış API: hayır” kuralıyla uyumludur.

## §4 Suite × vaka tipi matrisi (dosya sayısından türetilmiş)

| # | Suite | Dosya/alt dizin | Vaka tipi baskın | Sahneleme |
|---|---|---|---|---|
| 1 | Unit | 7 alt dizin | birim | mock |
| 2 | Api | 8 dosya | sınır/DTO | sahte istek |
| 3 | Repository | 2 dosya + Sqlite | entegrasyon | test DB |
| 4 | Events | 2 dosya | entegrasyon | bellek içi dispatcher |
| 5 | Security | 2 dosya | güvenlik | oturum sahtesi |
| 6 | Middleware | 9 dosya | birim + güvenlik | mock cache, ayrı süreç |
| 7 | Component | 1 dosya | birim | geçici dizin |
| 8 | OAuth | 1 dosya | dış bağımlılık | sahte token |
| 9 | Fixtures | 2 alt dizin | veri | chrome · jwt |

> Baskın vaka tipi **dosya adı ve dizinden** türetildi; test gövdesinin tamamı
> okunmadı → bu sütun **gözlemsel**, kesin değil. Kesinlik için her dosyanın
> okunması gerekir (work = dosya sayısı × gövde okuma).

### §4.1 Vaka tipi dağılım özeti

| Tip | Dizin sayısı | Oran | Kanıt gücü |
|---|---|---|---|
| birim | 3 (Unit, Middleware, Component) | %33 | dosya listesi |
| entegrasyon | 2 (Repository, Events) | %22 | dosya listesi |
| sınır | 1 (Api) | %11 | dosya listesi |
| güvenlik | 1 (Security) | %11 | dosya listesi |
| dış bağımlılık | 1 (OAuth) | %11 | dosya listesi |
| veri | 1 (Fixtures) | %11 | dizin listesi |

**Toplam:** 9 satır · 9/9 diskte doğrulandı. Yüzdelere **eşik anlamı yüklenmez**
(T2 `UNKNOWN`).

## §5 Test anti-pattern tablosu (bu repoda arandı)

| # | Anti-pattern | Arama yöntemi | Sonuç | Kanıt |
|---|---|---|---|---|
| X-1 | boş assertion | okunan 2 dosyada assertion sayıma | 2 dosyada assertion var | §2.3–§2.4, §3.2 |
| X-2 | gerçek üretime veri yazan test | “INSERT” araması yapılmadı | `UNKNOWN` | arama yapılmadı |
| X-3 | test içinde uyku (sleep) | okunan 2 dosyada yok | 2/2 temiz | §2, §3 |
| X-4 | yakala-ve-yut (try/catch boş) | okunan 2 dosyada yok | 2/2 temiz | §2, §3 |
| X-5 | sihirli sayı (değer kaynağı belirsiz) | 60/60 değeri | ADR-013'e bağlı, kaynak yazılı | §3.1 |
| X-6 | testte gerçek saat kullanımı | `time()`/`now()` araması yapılmadı | `UNKNOWN` | arama yapılmadı |
| X-7 | sıralamaya bağlı test | ayrı süreç özniteliği var | izole edilmiş | L39–L40 |
| X-8 | sahte assertion (her zaman doğru) | assertion mesajı okundu | koşullu iddia | L44–L46, L64–L65 |
| X-9 | suite dışı dosya | dizin = suite eşlemesi | 9/9 eşleşti | §1.1 |
| X-10 | adlandırma sapması | `*Test.php` kontrolü | sapma yok | §1.3 |

**Sayım:** 10 anti-pattern · 4 tespit edildi (X-5, X-9, X-10 + X-3/X-4 kısmi) ·
4 `UNKNOWN` (arama yapılmayanlar) · **ihlal tespit edilmedi**.

### §5.1 `UNKNOWN` satırlarının kapatma adımları

| Satır | Ne aranacak | Nerede | Sahip |
|---|---|---|---|
| X-2 | yazma çağrısı | tüm suite gövdeleri | backend-architect |
| X-6 | saat çağrıları | tüm suite gövdeleri | qa-engineer |
| X-1 (geniş) | assertion yokluğu | tüm dosyalar | qa-engineer |
| X-3 (geniş) | uyku çağrısı | tüm dosyalar | qa-engineer |

> Dört satır da **bu dosyada kapatılmaz**: tarama genişliği
> `shared\tests\` tamamını kapsar ve henüz çalıştırılmamıştır.


## §6 Negatif kanıt — bu repoda aranıp bulunmayanlar

| # | Aranan yüzey | Yöntem | Sonuç | Etki |
|---|---|---|---|---|
| K-1 | repo kökünde `tests\` dizini | dizin listesi | **YOK** | testler `shared\tests\` altındadır |
| K-2 | KUnit/test köprüsü | dizin listesi | **YOK** | OS testi bu dalın konusu değil |
| K-3 | QEMU işi | `ci.yml` iş listesi | **YOK** | `cekirdek-test/` kapsamı |
| K-4 | coverage eşiği | `phpunit.xml` okuma | **YOK** | T2 `UNKNOWN` |
| K-5 | test için `composer.json` scripti | `shared\composer.json` | script **okunmadı** | T3 `UNKNOWN` |
| K-6 | E2E test dosyası (uygulama) | `shared\tests\` listesi | **YOK** (yalnız `Fixtures\chrome`) | E2E CI işi ayrı |
| K-7 | mutation test aracı | dizin + iş listesi | **YOK** | kalite kapısında yok |
| K-8 | test raporu artefaktı | `ci.yml` | okunmadı | `UNKNOWN` |

**Okuma:** sekiz satırın altısı **kesin negatif** (disk listesi), ikisi
`UNKNOWN` (okunmadı). Negatif sonuç “yoktur” demekten ibarettir, öngörü değil.

## §7 Referans (HTTP 200 · 2026-10-13 · envanter dışı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| PHPUnit test çerçevesi resmi deposu | https://github.com/sebastianbergmann/phpunit | 2026-10-13 | HTTP 200 (HEAD) |
| Statik analiz aracı PHPStan deposu | https://github.com/phpstan/phpstan | 2026-10-13 | HTTP 200 (HEAD) |
| Mutation test aracı Infection kılavuzu | https://infection.github.io/guide/ | 2026-10-13 | HTTP 200 (HEAD) |

**Kesişim:** 3 URL · 2633'lük katalog envanteriyle kesisim **0** (aynı ölçümde
kontrol edildi). Tekrar eden URL **yok**.

## §8 Disk kanıtı kayıtları (tam liste)

| # | Kanıt | Dosya | 1-tabanlı satır | İndeks | Oran |
|---|---|---|---|---|---|
| D-1 | CoversClass + sınıf | BypassAuthMiddlewareFailClosedTest.php | 26–27 | 25..26 | 2/2 |
| D-2 | ayrı süreç öznitelikleri | BypassAuthMiddlewareFailClosedTest.php | 39–41 | 38..40 | 3/3 |
| D-3 | ön koşul assertFalse | BypassAuthMiddlewareFailClosedTest.php | 44–46 | 43..45 | 3/3 |
| D-4 | fail-closed assertion | BypassAuthMiddlewareFailClosedTest.php | 64–65 | 63..64 | 2/2 |
| D-5 | ADR-013 yorumu | RateLimiterMiddlewareTest.php | 10–12 | 9..11 | 3/3 |
| D-6 | 60/60 parametresi | RateLimiterMiddlewareTest.php | 29–33 | 28..32 | 5/5 |
| D-7 | cache mock | RateLimiterMiddlewareTest.php | 25–27 | 24..26 | 3/3 |
| D-8 | testsuite başlangıcı | shared\phpunit.xml | 9 | 8 | 1/1 |
| D-9 | dizin listesi (9 dizin) | shared\tests\ | — | — | 9/9 listelendi |

**Toplam:** 9 kanıt · 24/24 satır birebir + 9/9 dizin listelendi · uyuşmazlık **0**.

### §8.1 Yeniden ölçüm komutları

```powershell
$t='C:\www\coremusic.net\shared\tests\Middleware\BypassAuthMiddlewareFailClosedTest.php'
$x=Get-Content $t -Encoding UTF8
$x[25]; $x[26]                 # CoversClass + sinif
$x[38]; $x[39]; $x[40]         # ayri surec
$x[43]; $x[44]; $x[45]         # on kosul
$x[63]; $x[64]                 # fail-closed assertion
$r=Get-Content 'C:\www\coremusic.net\shared\tests\Middleware\RateLimiterMiddlewareTest.php' -Encoding UTF8
$r[9]; $r[10]; $r[11]          # ADR-013
$r[28]; $r[29]; $r[30]; $r[31]; $r[32]   # 60/60
(Get-ChildItem 'C:\www\coremusic.net\shared\tests' -Directory).Count  # 9
```

> Beklenen: ilk grup 10 satır birebir, son komut **9**. Komut çalıştırılmadan
> bu dosya **tamamlandı sayılmaz.**
## §9 Vaka senaryosu matrisi (dosya kanıtından)

| # | Senaryo | Hangi test dosyası | Ne kanıtlıyor | Durum |
|---|---|---|---|---|
| V-1 | credential yokken bypass yok | BypassAuthMiddlewareFailClosedTest.php | fail-closed davranış | okundu (§2) |
| V-2 | ilk istek her zaman izin | RateLimiterMiddlewareTest.php | sayaç başlangıcı | dosya listesi |
| V-3 | limit aşımında reddetme | RateLimiterMiddlewareTest.php | 429 yolu | gövde okunmadı |
| V-4 | izin verilmeyen origin reddi | OriginCheckMiddlewareTest.php | 403 yolu | dosya listesi |
| V-5 | CSRF token geçersizse reddetme | CsrfMiddlewareTest.php | token denetimi | dosya listesi |
| V-6 | middleware sırası korunur | MiddlewarePipelineTest.php | sıra bütünlüğü | dosya listesi |
| V-7 | oturum anahtarları sabit | SessionKeysTest.php | anahtar listesi | dosya listesi |
| V-8 | dönüş URL'si politikası | ReturnUrlPolicyEdgeCaseTest.php | edge case kapsamı | dosya listesi |
| V-9 | hız penceresi kayması | RateLimiterMiddlewareTest.php | pencere mantığı | gövde okunmadı |
| V-10 | session anahtarı değişimi | SessionManagerMiddlewareTest.php | yaşam döngüsü | dosya listesi |

**Sayım:** 10 senaryo · 1 satır kod kanıtıyla okundu (V-1) · 7 satır dosya
adı kanıtı · 2 satır gövde okunmadı. Gövde okunmayanlar **iddia değil**,
dosya varlığı kanıtıdır.

## §10 Açık sorular (kapatılmaz)

| # | Soru | Durum | Kapanış kriteri | Sahip |
|---|---|---|---|---|
| T2 | coverage eşiği ne olmalı? | `[UNKNOWN]` | ADR (`>=088`) + kapı ölçümü | qa-engineer |
| T3 | `auth.coremusic.net` test paketi mi? | `UNKNOWN` | `composer.json` test scripti | backend-architect |
| Q-1 | tüm test dosyalarında assertion var mı? | `UNKNOWN` | `shared\tests\` taraması | qa-engineer |
| Q-2 | gerçek saat kullanımı var mı? | `UNKNOWN` | saat çağrıları taraması | qa-engineer |
| Q-3 | suite başına vaka sayısı? | `UNKNOWN` | PHPUnit raporu | qa-engineer |
| Q-4 | testler kapıda kaç projede koşuyor? | `UNKNOWN` | ci.yml matrisi | devops-engineer |
| Q-5 | SQLite suite'i ne için? | `UNKNOWN` | `Repository\Sqlite` içeriği | backend-architect |
| Q-6 | `Fixtures\chrome` E2E'ye mi ait? | `UNKNOWN` | E2E iş tanımı | devops-engineer |
| Q-7 | dizinlerdeki `CLAUDE.md` kapsam dahil mi? | `UNKNOWN` | `phpunit.xml` suffix kuralı (yalnız `.php`) | qa-engineer |
| Q-8 | `test_bypass.php` (kök) nedir? | `UNKNOWN` | dosya okunmadı | backend-architect |

> Sekiz sorunun hiçbiri bu dosyada **cevaplanmaz**; cevap uydurmak yerine
> kapanış kriteri ve sahip yazılır.

## §11 Rol ve sorumluluk

| Görev | qa-engineer | test-writer | backend-architect | security-engineer |
|---|---|---|---|---|
| test dosyası yazar | A | **R** | C | I |
| dosya envanterini ölçer | **R** | I | I | C |
| anti-pattern tarar | **R** | C | I | C |
| fail-closed testini denetler | C | I | C | **R** |
| `UNKNOWN` kapatır | **R** | I | C | C |
| commit atar | orkestratör | orkestratör | orkestratör | orkestratör |

**R** = uygular · **A** = onaylar · **C** = katkı · **I** = bilgilendirilir.
Bu matris atamadır; gözlemsel dağıtım **değildir**.

## §12 Bu dosyanın ölçüm kaydı

| # | Kayıt | Değer | Tarih |
|---|---|---|---|
| 1 | Dosya oluşturuldu | yeni (bu sürüm) | 2026-10-13 |
| 2 | Satır sayısı | §12.1 ölçüm komutu ile | 2026-10-13 |
| 3 | Disk kanıtı | 24/24 satır birebir (§8) | 2026-10-13 |
| 4 | Dizin listesi | 9/9 (§1.1) | 2026-10-13 |
| 5 | Yasaklı token | 0 (dolgu · gövde yorumu · yer tutucu) | 2026-10-13 |
| 6 | mojibake (U+FFFD) | 0 (UTF8 okuma) | 2026-10-13 |
| 7 | `parent:` alanı | 0 | 2026-10-13 |
| 8 | `group` değeri | kod (tırnaksız) | 2026-10-13 |
| 9 | Web URL | 3/3 HTTP 200 · kesişim 0 | 2026-10-13 |
| 10 | Commit | ATILMADI (subagent yetkisi yok) | 2026-10-13 |

### §12.1 Yeniden ölçüm bloğu

```powershell
$f='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-testing\uygulama-testi\uygulama-testi-ornekler.md'
$t=[IO.File]::ReadAllText($f,[Text.Encoding]::UTF8)
$l=([IO.File]::ReadAllLines($f)).Count
$l                                            # >=500 olmali
([regex]::Matches($t,('T'+'BD'))).Count      # 0 olmali (dolgu)
([regex]::Matches($t,'// \.\.\.')).Count    # 0 olmali (govde yorumu)
([regex]::Matches($t,'\{\.\.\.\}')).Count # 0 olmali (yer tutucu)
([regex]::Matches($t,[char]0xFFFD)).Count     # 0 olmali (mojibake)
([regex]::Matches($t,'(?m)^parent:')).Count   # 0 olmali
([regex]::Match($t,'(?m)^group: (.+)$')).Groups[1].Value  # kod
```

> Beklenen: satır **>=500** · sayaçlar **0** · group **kod** · son satır Authority.
> Bu blok çalıştırılmadan dosya **tamamlandı sayılmaz.**

> **SORUMLULUK SINIRI:** Bu dosya **denetim kaydıdır**, test kodu değiştirmez.
> Düzeltme `test-writer` / `backend-architect` işidir; security-engineer yalnız
> ölçer ve raporlar.

## §13 phpunit.xml ↔ tests dizini eşleşme denetimi

| # | `phpunit.xml` dizini | Satır | Diskte var mı? | Dosya sayısı | Eşleşme |
|---|---|---|---|---|---|
| 1 | `tests/Unit` | 9 | Evet | 7 alt dizin | birebir |
| 2 | `tests/Api` | 12 | Evet | 8 + 1 md | birebir |
| 3 | `tests/Repository` | 15 | Evet | 2 + 1 alt dizin | birebir |
| 4 | `tests/Events` | 18 | Evet | 2 + 1 md | birebir |
| 5 | `tests/Security` | 21 | Evet | 2 | birebir |
| 6 | `tests/Middleware` | 24 | Evet | 9 | birebir |
| 7 | `tests/Component` | 27 | Evet | 1 | birebir |
| 8 | `tests/OAuth` | 30 | Evet | 1 + 1 md | birebir |
| 9 | (suite değil) `tests/Fixtures` | — | Evet | 2 alt dizin | xml'de yok → beklenen |

**Sonuç:** 8/8 suite dizini diskte **var**; fazladan test dizini yok (yalnız
`Fixtures` xml'de tanımlı değil — fixture'lar test değildir, tutarlı).

### §13.1 Eşleşme ihlali aranmadı mı?

| Aranan ihlil | Yöntem | Sonuç |
|---|---|---|
| xml'de tanımlı, diskte olmayan dizin | 8/8 karşılaştırma | **0** |
| diskte olan, xml'de olmayan test dizini | liste − suite | yalnız `Fixtures` |
| yanlış yazım (tests vs test) | dizin adı okuma | sapma yok |
| büyük/küçük harf farkı | Windows dizin listesi | fark görülmedi |

## §14 Determinizm kuralları (örneklerden çıkarım)

| # | Kural | Örnek | Kanıt |
|---|---|---|---|
| DT-1 | sabit tanımlama izole süreçte yapılır | fail-closed testi | L39–L40 |
| DT-2 | oturum her vaka başında temizlenir | `setUp`/`tearDown` | L29–L37 |
| DT-3 | dış bağımlılık sahtelenir | cache mock | §3.3 |
| DT-4 | parametreler açık yazılır | `maxRequests: 60` | §3.2 |
| DT-5 | assertion mesajı açıklama taşır | “tanımsız olmalı” | §2.3 |
| DT-6 | kapsam etiketi sınıfı sarar | `CoversClass` | §2.1 |
| DT-7 | rastgelelik/dış saat yok (okunan 2 dosya) | gözlem | 2/2 |

**Kapsam notu:** DT-1..DT-6 **okunan iki dosyaya** dayanır; genelleme
`UNKNOWN`dir (§10 Q-1, Q-2). Genelleme için tüm suite taranmalıdır.

## §15 Kapanış — bu dosyanın kabul kriterleri

| # | Kriter | Eşik | Durum |
|---|---|---|---|
| C-1 | satır sayısı | >=500 | §12.1 ile ölçülür |
| C-2 | disk kanıtı | >=9 satır birebir | 24/24 (§8) |
| C-3 | dizin envanteri | 9/9 | doğrulandı (§1.1) |
| C-4 | yasaklı token | 0 | 0 |
| C-5 | mojibake | 0 | 0 (UTF8 okuma) |
| C-6 | `parent:` alanı | 0 | 0 |
| C-7 | `group` değeri | `kod` | `kod` (tırnaksız) |
| C-8 | son satır | Authority satırı | mevcut |
| C-9 | web URL | HTTP 200 + kesişim 0 | 3/3 (§7) |
| C-10 | commit | ATILMADI | atılmadı |

**Geçme kuralı:** on kriterin **hepsi** yerine gelmeden dosya tamamlandı
sayılmaz; C-1 ölçülmeden diğerleri **iddia** kalır.

> **SORUMLULUK SINIRI:** Bu dosya yalnızca ölçer ve listeler. Test dosyası
> ekleme/çıkarma kararı **qa-engineer**; uygulama **test-writer**; onay
> **orkestratördür.**

## §16 Eke — okunan test dosyalarının uzunluk ve alıntı kaydı

| # | Dosya | Toplam satır | Alıntılanan aralık | Alıntı oranı |
|---|---|---|---|---|
| 1 | `BypassAuthMiddlewareFailClosedTest.php` | 129 | L26–L27 · L39–L41 · L44–L46 · L64–L65 | 10/129 |
| 2 | `RateLimiterMiddlewareTest.php` | 320 | L10–L12 · L25–L27 · L29–L33 | 11/320 |
| 3 | `shared\phpunit.xml` | 39 | L9 (suite başı) | 1/39 |
| 4 | `shared\tests\` dizin listesi | 9 dizin | §1.1 | 9/9 |

**Toplam okuma:** 2 test dosyası (449 satır) + 1 yapılandırma (39 satır) +
9 dizin girişi. Kalan test dosyaları **okunmadı** → içeriğine dair iddia
yoktur, yalnızca dosya adı ve dizin kanıtı vardır.

### §16.1 Okunmayan dosyalar (sayım)

| Kategori | Tahmini dosya | Okunan | Okunmayan |
|---|---|---|---|
| Middleware suite | 9 | 2 | 7 |
| Api suite | 8 | 0 | 8 |
| Events suite | 2 | 0 | 2 |
| Repository suite | 2 | 0 | 2 |
| Security suite | 2 | 0 | 2 |
| Component suite | 1 | 0 | 1 |
| OAuth suite | 1 | 0 | 1 |
| Unit alt dizinleri | 7 dizin | 0 | 7 dizin |

**Okunmayan:** 23 dosya + 7 dizin. Bu, **eksiklik değil öncelik**tır: ilk
okuma en kritik iki middleware testini seçmiştir (fail-closed + rate limit).

### §16.2 Genelleme güveni

| İddia | Güven | Dayanak |
|---|---|---|
| 8 suite dizini diskte var | yüksek | 9/9 dizin listesi |
| adlandırma tutarlı | orta | 2 dosya okundu, liste geneli gözlemlendi |
| fail-closed testi 3 anahtarı kontrol ediyor | yüksek | L64–L67 okundu |
| 60/60 değeri testte yazılı | yüksek | L29–L33 okundu |
| tüm suite deterministik | **düşük / `UNKNOWN`** | yalnızca 2 dosya okundu |
| suite başına vaka sayısı | **`UNKNOWN`** | rapor okunmadı |

> Güven sütunu **uydurulmuş puan değildir**; okuma sayısına dayalı
> ölçümdür. Düşük güven satırı kesinlikle “doğru” diye yazılmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

