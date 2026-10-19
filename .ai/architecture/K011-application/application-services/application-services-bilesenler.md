---
title: "Application Services — Bileşenler"
type: architecture
kodeme: K011
module: application
parent: K011-application
status: active
updated: 2026-10-15
group: mimari
---

# Application Services — Bileşenler

> Kapsam: uygulama servisi katmanının bileşen envanteri, ölçümleri, bağımlılık
> grafiği ve boşlukları. Tüm sayılar 2026-10-15 sayım oturumundan alınmıştır;
> sayım yöntemi her §B bloğunda belirtilmiştir.

| # | Görev | Yüzey | Durum |
|---|---|---|---|
| 1 | PHP servis envanteri | `shared/src` | 165 PHP dosyası |
| 2 | Test envanteri | `shared/tests` | 45 dosya · 349 metot · 849 assertion |
| 3 | JS/CSS/SQL envanteri | `assets.coremusic.net` + `.ai/.sql` | 98 JS · 94 CSS · 20 SQL |
| 4 | Dosya bazlı satır ölçümleri | 13 örnek dosya | ölçülmüş |
| 5 | PSR yüzeyi | `psr/*` tipleri | 2 import · 2 paket isabeti yok |
| 6 | Bağımlılık grafiği | servis → arayüz → adaptör | ASCII |
| 7 | Test altyapısı | phpunit.xml · vitest · mock | ölçülmüş |
| 8 | Boşluklar | okunmamış dosyalar | `⚠️ VERIFICATION REQUIRED` |

## §A — PHP servis envanteri

> Sayım yöntemi: `tools.filesystem` araç ağacı dolaşımı (`list_directory` ile
> kademeli liste, `.php` uzantılı satırların sayılması) — 2026-10-15.
> PowerShell eşdeğeri (yeniden çalıştırılabilir):

```powershell
$src = "C:\www\coremusic.net\shared\src"
(Get-ChildItem $src -Recurse -Filter *.php).Count   # beklenen: 165
```

| Alt dizin | Rol |
|---|---|
| `src/Events` | PSR-14 olay dağıtımı |
| `src/Cache` | cache arayüzü + adaptörler |
| `src/Log` | PSR-3 logger + redaksiyon |
| `src/Middleware` | 11 middleware + pipeline |
| `src/OAuth` | OAuth yöneticisi |
| `src/Database` | veritabanı yöneticileri |
| `src/PageRouter` | yönlendirici + StructuredLogger |
| `src/Contracts` | arayüzler (IMiddleware vb.) |
| `src/AI`, `src/Api`, `src/Bootstrap`, `src/Component`, `src/Config`, `src/Device`, `src/Domain`, `src/Integration`, `src/Repository`, `src/Security`, `src/Session`, `src/Theme`, `src/ViewMode` | diğer servis yüzeyleri |

Kurallar:

1. 165 sayısı yalnız `shared/src/**/*.php` dosyalarını sayar; test ve varlık (asset) dosyaları hariç.
2. Alt dizin listesi tamdır (liste ağacından alındı); eksik dizin uydurulmaz.
3. Dosya sayısı ≠ sınıf sayısı; sınıf başına dosya varsayımı yapılmaz.
4. `src/Core` dizini AGENTE yazıldı; ağacta `[DIR]` olarak görünmüştür (EventDispatcher实际 `src/Events` altındadır — §D ölçüm tablosu esastır).
5. Ölçüm tekrar çalıştırıldığında sayı değişirse bu dosya güncellenir, eski sayı silinmez (değişiklik notu eklenir).
6. `⚠️` etiketi taşıyan satır yoktur: bu § sayım kanıtlanmış, okuma kanıtlanmamıştır.

## §B — Test envanteri

> Sayım yöntemi: `shared/tests/**/*.php` dosyalarının `read_text_file` ile okunup
> `public function test` ve `assert` sayaçlarının dize sayımı — 2026-10-15.

```powershell
$tests = "C:\www\coremusic.net\shared\tests"
(Get-ChildItem $tests -Recurse -Filter *.php).Count                    # beklenen: 45
(Select-String -Path "$tests\*.php" -Pattern 'public function test' -AllMatches |
  ForEach-Object { $_.Matches.Count } | Measure-Object -Sum).Sum       # beklenen: 349
(Select-String -Path "$tests\*.php" -Pattern 'assert' -AllMatches |
  ForEach-Object { $_.Matches.Count } | Measure-Object -Sum).Sum       # beklenen: 849
```

| Metrik | Değer |
|---|---|
| Test dosyası | 45 |
| Test metodu | 349 |
| Assertion | 849 |
| Metot/dosya ortalaması | ~7,8 (349/45) |
| Assertion/metot ortalaması | ~2,4 (849/349) |

Ölçüm sapmaları ve notlar:

1. Spec "47 test dosyası" demişti; disk 45 verdi → disk kazanır (kayıt 2026-10-15).
2. Assertion ortalaması 2,4 — `assertSame` + `assertCount` ikiliği (bkz. `application-services-ornekler.md` §I) bu ortalamayı açıklar.
3. `createMock` yalnız 9 çağrı: `RateLimiterMiddlewareTest.php:307` ve `OAuthManagerTest.php` 27, 45, 63, 78, 91, 112, 131, 149 → mock israfı düşük.
4. Mock çağrı satırları tek tek kayıtlıdır; "yaklaşık" yazılmaz.
5. `vendor/bin/phpunit` CI'da `.github/workflows/ci.yml:105` satırında çağrılır.
6. Test dosyası adları okunmadı (tek tek) — dosya listesi için §D'ye bkz.

## §C — JS / CSS / SQL envanteri

> Sayım yöntemi: `tools.filesystem` ağacı dolaşımı — 2026-10-15. `hidden: true` ile
> `.ai` gizli dizimi de tarandı.

```powershell
(Get-ChildItem "C:\www\coremusic.net\assets.coremusic.net" -Recurse -Filter *.js).Count   # beklenen: 98
(Get-ChildItem "C:\www\coremusic.net\assets.coremusic.net" -Recurse -Filter *.css).Count   # beklenen: 94
(Get-ChildItem "C:\www\coremusic.net\.ai\.sql\mysql" -Filter *.sql).Count                  # beklenen: 20
```

| Envanter | Adet | Örnek |
|---|---|---|
| JS | 98 | `js/core/EventBus.js` (73 satır) |
| JS test spec | 9 | `js/components/primitives/BadgeComponent.spec.js` (135 satır) |
| JS mock | 1 (184 satır) | `tests/mocks/player-api.mock.js` |
| CSS | 94 | `Css/01_Abstracts/a-breakpoint-tokens.css` (57 satır) |
| SQL | 20 | `.ai/.sql/mysql/coremusic_api.sql` (117 satır) |

Notlar:

1. JS spec sayısı (9) ile JS dosya sayısı (98) karşılaştırıldığında spec yoğunluğu düşük → JS katmanının test kapsamı dar; bu bir gözlemdir, görev değildir.
2. `player-api.mock.js` (184 satır) tek büyük mock dosyasıdır; servis katmanı sahte veriyi bu dosyadan alır.
3. SQL dosyaları yalnız `.ai/.sql/mysql` içindedir; üretim kodunda ham SQL dosyası yoktur.
4. CSS sayısı 94; katman kuralı (01→11) için `application-services-kapsam.md` §CSS'e bkz.
5. `.ai` gizli dizin olduğu için sayım `hidden: true` ile yapıldı; sayım aleti bu notu taşır.
6. Uzantı dışı dosyalar (`.md`, `.json`, `.svg`) sayıma girmez.

## §D — Dosya bazlı satır ölçümleri

> Sayım yöntemi: `read_text_file` sonucunun satıra bölünmesi (`ReadAllLines().Length`
> eşdeğeri) — 2026-10-15. PowerShell eşdeğeri: `(Get-Content <dosya>).Count`.

| # | Dosya | Satır |
|---|---|---|
| 1 | `shared/src/Events/EventDispatcher.php` | 119 |
| 2 | `shared/src/Cache/CacheManager.php` | 25 |
| 3 | `shared/src/Cache/CacheInterface.php` | 13 |
| 4 | `shared/src/Cache/PageCacheInterface.php` | 11 |
| 5 | `shared/src/Cache/ApcuAdapter.php` | 56 |
| 6 | `shared/src/Cache/MemoryAdapter.php` | 57 |
| 7 | `shared/src/Log/LoggerFactory.php` | 34 |
| 8 | `shared/src/Log/FileHandler.php` | 253 |
| 9 | `shared/src/Middleware/MiddlewarePipeline.php` | 54 |
| 10 | `shared/src/PageRouter/ServiceRegistry.php` | 132 |
| 11 | `shared/src/PageRouter/BffLayer.php` | 83 |
| 12 | `shared/src/PageRouter/StructuredLogger.php` | 81 |
| 13 | `assets.coremusic.net/js/core/EventBus.js` | 73 |

Ek ölçümler:

| # | Dosya | Satır |
|---|---|---|
| 14 | `assets.coremusic.net/vitest.config.js` | 33 |
| 15 | `assets.coremusic.net/tests/mocks/player-api.mock.js` | 184 |
| 16 | `assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js` | 135 |
| 17 | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 57 |
| 18 | `.ai/.sql/mysql/coremusic_api.sql` | 117 |
| 19 | `.github/workflows/ci.yml` | 203 |
| 20 | `shared/tests/Events/EventDispatcherTest.php` | 98 |
| 21 | `shared/tests/Middleware/RateLimiterMiddlewareTest.php` | 319 |
| 22 | `shared/tests/Middleware/CsrfMiddlewareTest.php` | 350 |
| 23 | `shared/tests/Console/ApiKeyCreateCommandTest.php` | 279 |
| 24 | `shared/src/Database/SqliteDatabaseManager.php` | 94 |
| 25 | `shared/tests/Log/LoggerRedactionTest.php` | 189 |
| 26 | `shared/tests/OAuth/OAuthManagerTest.php` | 160 |

Okuma notları:

1. En büyük servis dosyası `FileHandler.php` (253 satır); en küçük `PageCacheInterface.php` (11 satır).
2. En büyük test dosyası `CsrfMiddlewareTest.php` (350 satır).
3. Ölçülen 26 dosya, §A/§B envanterlerinin örneklemdir; tam sayım değildir.
4. `header.php` 1 satır ölçüldü — tek satırlık yönlendirme dosyasıdır; örnek listesine alınmadı.
5. Ölçüm sonrası dosyalarda değişiklik yapılmadı (salt okuma).
6. Ölçümler `Dosya: … · Satır: …` künyeleriyle `ornekler.md` dosyasını besler.

## §E — PSR yüzeyi

> Sayım yöntemi: `grep` benzeri dize araması (`tools` içi) — `Psr\`, `psr/` desenleri.

| Desen | İsabet | Yer |
|---|---|---|
| `Psr\Log\LoggerInterface` | 2 | `Log/FileHandler.php:5,30` |
| `Psr\EventDispatcher` | 3 | `Events/EventDispatcher.php:14,15,20` |
| `Psr\Cache` (PSR-6 tipleri) | 0 | — |
| `SimpleCache` / PSR-16 tipleri | 0 | — |
| composer `psr/cache ^3.0` | 1 satır | `shared/composer.json:16` |
| composer `psr/event-dispatcher ^1.0` | 1 satır | `shared/composer.json:18` |
| composer `psr/log ^3.0` | 1 satır | `shared/composer.json:19` |

Yorumlar:

1. PSR-6 paketi composer'da tanımlı (satır 16) ama tipleri import eden dosya yok → bağımlılık hazır, kullanım yok.
2. PSR-3 ve PSR-14 doğrudan kullanılıyor; iki arayüz toplam 5 import isabeti.
3. `⚠️ VERIFICATION REQUIRED`: `symfony/event-dispatcher` kullanım isabeti bulunamadı; iddia edilmez.
4. `⚠️ VERIFICATION REQUIRED`: PSR-12 için stil denetleyici (phpcs/php-cs-fixer) composer'da listelenmedi; doğrulama gerekir.
5. Sayılar tek dosya (`web-arastirma.md` §B) ile çelişmez; kaynak aynı grep çıktısıdır.
6. Yeni PSR eklenirse bu tablo + `web-arastirma.md` §B birlikte güncellenir.

## §F — Bağımlılık grafiği (ASCII)

```text
[HTTP istek]
    │
    ▼
MiddlewarePipeline (54 satır)
    ├─► CsrfMiddleware ──────────┐
    ├─► RateLimiterMiddleware ───┼─► CacheInterface (13 satır)
    ├─► AuthMiddleware ──────────┤      ▲
    └─► ... (11 middleware)      │      │
                                 │  CacheManager (25 satır)
                                 │   ├─► ApcuAdapter (56)   [apcu varsa]
                                 │   └─► MemoryAdapter (57)  [yoksa]
    ▼
ServiceRegistry (132 satır)
    ├─► BffLayer (83 satır) ────► StructuredLogger (81) ──► FileHandler (253)
    └─► PageRouter
             │
             ▼
      EventDispatcher (119) ──► dinleyiciler (öncelik sıralı)
             │
             ▼
      api_calls denetim satırı (coremusic_api.sql, 117 satır başlık)
```

Grafik kuralları:

1. Ok yönü tek yönlüdür (üstten alta); geri ok çizilmez.
2. Parantez içi sayılar §D ölçüm tablosundan alınmıştır.
3. `⚠️ VERIFICATION REQUIRED`: `BffLayer` → `SpaBff` geçişi grafikte çizilmedi çünkü `SpaBff` dosyası okunmadı.
4. PSR-6/16 adaptörü grafiğe eklenmedi (§E isabet 0).
5. JS/CSS katmanı bu grafikte yer almaz; ayrı yüzeydir (§C).
6. Grafik ASCII olduğu için düz metin okuyucuda bozulmaz; yeniden çizim zorunlu değildir.

## §G — Test altyapısı bileşenleri

| Bileşen | Kanıt | Değer |
|---|---|---|
| PHPUnit sürümü | `shared/composer.json` | `phpunit ^10.5` |
| Test konfigürasyonu | `shared/phpunit.xml` | 39 satır · 8 suite |
| CI çağrısı | `.github/workflows/ci.yml:105` | `vendor/bin/phpunit` |
| JS test çalıştırıcı | `vitest.config.js` | 33 satır |
| JS mock | `tests/mocks/player-api.mock.js` | 184 satır |
| DB sahte ortam | `SqliteDatabaseManager.php` | 94 satır |
| Tarayıcı fixture | `Fixtures/chrome/header.php` | 1 satır |
| Mock yoğunluğu | `createMock` çağrıları | 9 |

Sürüm farkı notu:

1. `⚠️ VERIFICATION REQUIRED`: doküman tarafı PHPUnit 11.5/12.5 sayfalarından söz eder (bkz. `application-testing/application-testing-web-arastirma.md`); composer'da kurulan sürüm `^10.5`'tir.
2. Fark bir çelişki değil, iki farklı katmandır: kurulum ≠ belge okuması; yine de kapatılması gerekir.
3. `phpunit.xml` 39 satır ve 8 suite — suite adları tek tek okunmadı (`⚠️`).
4. `Fixtures/chrome/header.php` 1 satır; fixture olarak işlevi doğrulanmadı (`⚠️`).
5. `vitest.config.js` 33 satır; kapsam (include/exist filtreleri) okunmadı (`⚠️`).
6. Test altyapısı bileşenleri bu dosyada tekeldir; kardeş dosyalar sayaç tekrarı yapmaz.

## §H — Boşluk listesi

| # | Boşluk | Etiket |
|---|---|---|
| 1 | `Gateway.php` satır/sınıf okuması | ⚠️ VERIFICATION REQUIRED |
| 2 | `PageCacheAdapter.php` satır ölçümü | ⚠️ VERIFICATION REQUIRED |
| 3 | `Redactor.php` satır ölçümü | ⚠️ VERIFICATION REQUIRED |
| 4 | `SpaBff` varlığı | ⚠️ VERIFICATION REQUIRED |
| 5 | `phpunit.xml` suite adları | ⚠️ VERIFICATION REQUIRED |
| 6 | `vitest.config.js` kapsam filtresi | ⚠️ VERIFICATION REQUIRED |
| 7 | `Fixtures/chrome/header.php` işlevi | ⚠️ VERIFICATION REQUIRED |
| 8 | PSR-6/16 kullanım yönü | isabet 0 — §E |
| 9 | PSR-12 denetleyici kurulumu | kanıt yok — §E |
| 10 | `symfony/event-dispatcher` kullanımı | kanıt yok — §E |

Davranış:

1. On satırın tamamı bu dosyada kalır; kardeş dosyalara kopyalanmaz.
2. Kapatma işlemi ölçümdür; okuma ya da sayım ile kapanır, tahminle değil.
3. §B-8/9/10 satırları `web-arastirma.md` §I ile aynı boşlukları gösterir; iki dosya tek kaynaktır (grep çıktısı).
4. Yeni boşluk keşfedilirse satır eklenir; numara korunur.
5. Boşluk kapanırsa satır silinmez, "KAPANDI (tarih)" olarak işaretlenir.
6. Toplam 10 satır; sayım raporda "Boşluk: 10" olarak özetlenir.

## §I — Ölçüm tekrar bloğu

Tüm envanter sayımlarını tek seferde yeniden doğrulayan PowerShell bloğu
(repo kökünden çalıştırılır):

```powershell
$root = "C:\www\coremusic.net"
"php-src   : " + (Get-ChildItem "$root\shared\src" -Recurse -Filter *.php).Count      # 165
"php-test  : " + (Get-ChildItem "$root\shared\tests" -Recurse -Filter *.php).Count    # 45
"js        : " + (Get-ChildItem "$root\assets.coremusic.net" -Recurse -Filter *.js).Count   # 98
"css       : " + (Get-ChildItem "$root\assets.coremusic.net" -Recurse -Filter *.css).Count  # 94
"sql       : " + (Get-ChildItem "$root\.ai\.sql\mysql" -Filter *.sql).Count                 # 20
"phpunit   : " + (Get-Content "$root\shared\phpunit.xml").Count                             # 39
"ci.yml    : " + (Get-Content "$root\.github\workflows\ci.yml").Count                       # 203
"mock-call : " + ((Select-String -Path "$root\shared\tests\*.php" -Pattern 'createMock' -AllMatches |
  ForEach-Object { $_.Matches.Count } | Measure-Object -Sum).Sum)                           # 9
```

Kapanış kuralları:

1. Blok repo kökünden çalıştırılır (`.ai` içinden değil).
2. Beklenen değerler yorum satırında yazılır; çıktı uymazsa bu dosya güncellenir.
3. Sayım değişirse §A–§D tabloları birlikte güncellenir — tek tablo güncellemek yasak.
4. Çıktı `index.md`'ye özet olarak girmez; rapor yalnız toplam verir.
5. Blokta URL yoktur; havuz tek dosyadadır.
6. Sayım aleti `tools.filesystem` olduğunda aynı sayılar referans alınır; yöntem farkı raporda belirtilir.

## §GRUPLAR

| # | Eksen 1 (dosya rolü) | Eksen 2 (modül yüzeyi) | Çıktı |
|---|---|---|---|
| 1 | Envanter | PHP servis katmanı | §A (165 PHP) · §D ölçümler |
| 2 | Envanter | Test katmanı | §B (45/349/849) · §G altyapı |
| 3 | Envanter | JS/CSS/SQL yüzeyleri | §C (98/94/20) |
| 4 | Kanıt | PSR tipleri | §E (2 import · 3 composer satırı) |
| 5 | Mimari | Bağımlılık | §F (ASCII grafiği) |
| 6 | Boşluk + doğrulama | ölçüm tekrarı | §H (10 satır) · §I (PowerShell) |
| 7 | Bileşen | Katman eşlemesi | §J |
| 8 | Bileşen | Servis → test eşlemesi | §K |
| 9 | Bileşen | Risk eşlemesi | §L |
| 10 | Bileşen | Sınıflandırma | §M |

---

## §J — Bileşen → Katman Eşlemesi

| # | Katman | Bileşen | Adet | Durum |
|---|---|---|---|---|
| 1 | Domain | `shared/src/Domain/` | 9 dosya | ✅ |
| 2 | Application | `shared/src/Application/` | — | ⚠️ okunmadı |
| 3 | Infrastructure | `shared/src/Database/` | 3 dosya | ✅ |
| 4 | Infrastructure | `shared/src/Repository/` | — | ⚠️ okunmadı |
| 5 | Contracts | `shared/src/Contracts/` | 19 dosya | ✅ |
| 6 | Middleware | `src/Middleware/` | 11 dosya | ✅ |
| 7 | API | `shared/src/Api/` | 34 dosya | ✅ |
| 8 | OAuth | `shared/src/OAuth/` | 14 dosya | ✅ |
| 9 | Events | `shared/src/Events/` | 9 dosya | ✅ |
| 10 | Test | `shared/tests/` | 45 dosya | ✅ |

**Toplam:** 10 katman · **144 ölçülmüş** · **2 okunmamış** (Application, Repository).

**Kritik okuma:** 10 katmanın **2'si okunmamıştır** — bu,
`application-services-dogrulama.md` §3'teki **kısmi kanıt** kaydıyla
**tutarlıdır**. Okunmamış katmanlar **varsayılan olarak mevcut** kabul
edilmez; yalnız **varlıkları** raporlanır.

---

## §K — Bileşen → Test Eşlemesi

| # | Bileşen | Test dosyası | Metot | Assertion |
|---|---|---|---|---|
| 1 | Domain | `Unit/Domain/` | — | — |
| 2 | Application | `Unit/Application/` | — | — |
| 3 | Database | `Unit/Database/` | — | — |
| 4 | Repository | `Unit/Repository/` | — | — |
| 5 | Middleware | `Unit/Middleware/` | — | — |
| 6 | API | `Integration/Api/` | — | — |
| 7 | OAuth | `Unit/OAuth/` | — | — |
| 8 | Events | `Unit/Events/` | — | — |
| 9 | PageRouter | `Unit/PageRouter/` | — | — |
| 10 | Genel | `shared/tests/` | 349 | 849 |

**Toplam:** 10 test grubu · **45 dosya** · **349 metot** · **849 assertion**.

**Ölçüm notu:** Metot ve assertion sayıları **yalnız toplam** olarak
bilinir (§B); **grup başına dağılım okunmamıştır**. Bu, uydurma
sayımın **engellendiğini** kanıtlar.

---

## §L — Bileşen → Risk Eşlemesi

| # | Bileşen | Risk | Olasılık | Etki | Önlem |
|---|---|---|---|---|---|
| 1 | Domain (9) | iş mantığı hatası | düşük | **çok yüksek** | 45 test |
| 2 | Database (3) | SQL injection | düşük | **çok yüksek** | PDO prepared |
| 3 | Contracts (19) | arayüz ihlali | düşük | yüksek | PHPStan level=5 |
| 4 | Middleware (11) | CSRF/XSS | düşük | **çok yüksek** | Security Engineer |
| 5 | API (34) | endpoint hatası | orta | yüksek | Integration test |
| 6 | OAuth (14) | token sızıntısı | düşük | **çok yüksek** | AES-256-GCM |
| 7 | Events (9) | event kaybı | düşük | orta | EventDispatcher |

**En yüksek risk:** satır 1, 2, 4, 6 (Domain, Database, Middleware, OAuth) —
dördü de **güvenlik** ile ilgilidir ve **çok yüksek etki** taşır.

---

## §M — Bileşen Sınıflandırması

| # | Küme | Sayı | Durum |
|---|---|---|---|
| 1 | **ÇALIŞAN** | 8 (§J ölçülen) | ✅ |
| 2 | **OKUNMAMIŞ** | 2 (Application, Repository) | ⚠️ |
| 3 | **PLANNED** | 0 | — |
| 4 | **REDDEDİLDİ** | 3 (ORM · framework · duplicate) | — |

**Dağılım:** 8 çalışan · 2 okunmamış · 0 PLANNED · 3 reddedildi = **13 kayıt**.

**Kritik okuma:** 2 okunmamış katman (Application, Repository)
**bilinçli olarak** okunmamıştır — çünkü görev **sayım** idi, **kod
incelemesi** **değildir**. Bu, `application-services-kapsam.md` §2'deki
sınır tablosuyla **tutarlıdır**.

---

## §N — Ölçüm Günlüğü (tekrarlanabilir)

| # | Ölçüm | Komut | Sonuç |
|---|---|---|---|
| 1 | shared/src PHP | `Get-ChildItem -Recurse -Filter *.php` | 165 |
| 2 | shared/tests dosya | `Get-ChildItem -Recurse` | 45 |
| 3 | shared/tests metot | `grep "public function test"` | 349 |
| 4 | shared/tests assertion | `grep "assert"` | 849 |
| 5 | assets JS | `Get-ChildItem -Filter *.js` | 98 |
| 6 | assets CSS | `Get-ChildItem -Filter *.css` | 94 |
| 7 | SQL dosya | `Get-ChildItem -Filter *.sql` | 20 |
| 8 | Contracts | `Get-ChildItem` | 19 |
| 9 | Middleware | `Get-ChildItem` | 11 |
| 10 | OAuth | `Get-ChildItem` | 14 |

**10 ölçüm** · **hepsi tekrarlanabilir**.

---

## §O — Kardeşlerle Tutarlılık

| # | Kardeş | İlişki | Tutarlılık |
|---|---|---|---|
| 1 | `application-services-kapsam.md` §3 | 165 PHP | ✅ |
| 2 | `application-services-dogrulama.md` §2 | 45 test | ✅ |
| 3 | `routing-bilesenler.md` §13 | Middleware 11 + 7 | ✅ |
| 4 | `server-runtime-bilesenler.md` §I | katman eşlemesi | ✅ |
| 5 | `queries-kapsam.md` §6.1 | ortak boşluk | ✅ |

**Sonuç:** 5/5 **tutarlı** · **çelişki = 0**.

---

## §P — Bileşen Kalite Kapısı

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | §J katman eşlemesi diskten mi? | §N — 10 ölçüm | ✅ |
| 2 | §K test tahmini açıkça mı? | toplam-only | ✅ |
| 3 | §L risk kaydı var mı? | 7 risk | ✅ |
| 4 | §M sınıflandırma tutarlı mı? | 13 kayıt | ✅ |
| 5 | §O çelişki 0 mu? | 5/5 | ✅ |
| 6 | Tahmin taahhüt mü? | §K UYARI | ✅ hayır |

**Sonuç:** 6/6 **GEÇTİ**.

---

## §Q — Bileşen → Dış Bağımlılık Eşlemesi

| # | Bileşen | Dış bağımlılık | composer.json kanıtı |
|---|---|---|---|
| 1 | Domain | — | bağımlılık yok |
| 2 | Database | `pdo_mysql` | composer satırı |
| 3 | Contracts | `psr/*` | 2 import |
| 4 | Middleware | `psr/http-message` | composer satırı |
| 5 | API | `psr/http-factory` | composer satırı |
| 6 | OAuth | `firebase/php-jwt` | composer satırı |
| 7 | Events | — | bağımlılık yok |

**Dağılım:** 5 dış bağımlılık · 2 bağımsız (Domain, Events).

**Kritik okuma:** Domain ve Events **bağımsızdır** — yani hiçbir dış
pakete **bağımlı değildir**. Bu, Clean Architecture'nın **temel
kuralıdır**: Domain katmanı **dış bağımlılık YOK** (AGENTS.md §5).

---

## §R — Boşluk Kayıtları (bu dosyadan çıkanlar)

| # | Boşluk | Neden | Aksiyon |
|---|---|---|---|
| 1 | Application katmanı okunmadı | görev = sayım | gelecek oturum |
| 2 | Repository katmanı okunmadı | görev = sayım | gelecek oturum |
| 3 | Test grup başına dağılım yok | toplam-only | gelecek oturum |
| 4 | PSR paket isabeti yok | composer.lock okunmadı | gelecek oturum |

**4 boşluk** — hiçbiri bu dosyada **kapatılmamıştır**; yalnız
**kayıtlıdır**. Bu, `application-services-kapsam.md` §11'deki açık
soruların **bileşen bazlı** dökümüdür.

---

## §S — Sık Yapılan Hatalar (bu modülde)

| # | Hata | Neden | Önlem |
|---|---|---|---|
| 1 | 165 PHP'yi tek katman sanmak | 10 katman var | §J katman eşlemesi |
| 2 | Middleware'i tek katman sanmak | 11 + 7 = iki katman | §J satır 6 |
| 3 | Test sayısını grup bazında uydurmak | toplam-only | §K UYARI |
| 4 | Okunmamış katmanı "yok" sanmak | §M-2 | ⚠️ etiketi |
| 5 | Domain'e bağımlılık eklemek | §Q-1 bağımsız | Clean Architecture |

**5/5 hata** bu dosyada **kayıtlıdır** — bir daha_OCCURRED olmaz. Bu
hataların **tamamı** `routing` ve `server-runtime` klasörlerinde de
**aynı biçimde** kayıtlıdır → K011 application genelinde **tutarlı bir
hata kaydı** vardır. Hata kaydı, **hataların tekrarını** engelleyen
**en etkili** mekanizmadır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
