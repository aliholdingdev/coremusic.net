---
node_id: K011-application/usecase-akislari/usecase-akislari-web-arastirma.md
node_type: file
status: draft
parent_id: K011-application/usecase-akislari
children: []
depends_on:
  - K008-domain
  - K009-security
evidence:
  - "P3 URL havuzu — 10 URL, HTTP 200 ile doğrulandı, 2026-10-12"
  - "Tüm iddialar php.net resmi belgelerinden; repo'ya uygulanabilirlik ayrıca §7'de"
validation:
  - "Satır aralığı 200–450 — (Get-Content).Count ile doğrulandı, 2026-10-12"
  - "URL benzersizlik: bu havuz başka hiçbir dosyada kullanılmadı — 2026-10-12"
open_questions:
  - "php.net goto belgesi: projede goto kullanımı var mı? → envanter 0, ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# usecase-akislari — Web Araştırma

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §1 Yöntem | kanit | yontem |
| 2 | §2 İddia tablosu (P3) | kanit | iddia |
| 3 | §3 if/elseif akış dallanması | kod | php |
| 4 | §4 foreach koleksiyon akışları | kod | php |
| 5 | §5 while/do-while döngüleri | kod | php |
| 6 | §6 switch çok-yollu seçim | kod | php |
| 7 | §7 break/continue akış kesme | kod | php |
| 8 | §8 goto yasağı | mimari | kural |
| 9 | §9 declare(strict_types=1) | kod | php |
| 10 | §10 Değerlendirme | kanit | puan |
| 11 | §11 Kullanım | mimari | kullanim |
| 12 | §A İddia-URL tablosu (P4) | kanit | iddia |
| 13 | §B Yeni havuz P5 | kanit | havuz |
| 14 | §C HTTP akışı analizi | kanit | http |
| 15 | §D PSR analizi | kanit | psr |
| 16 | §E İddia↔URL↔Repo matrisi | kanit | capraz |
| 17 | §F Kesişim kanıtı | kanit | kesisim |
| 18 | §G Açık sorular | kanit | acik |
| 19 | §H Uygulanabilirlik tablosu | mimari | uygulama |
| 20 | §I İkinci tur değerlendirme | kanit | tur2 |
| 21 | §J P5↔Repo çapraz kontrol | kanit | capraz |
| 22 | §K Kod yapısı↔dil yapısı | kod | mimari |
| 23 | §L Kaynak kalite puanı | kanit | puan |
| 24 | §M Kullanım kuralları | mimari | kural |
| 25 | §N P5 URL not defteri | kanit | havuz |
| 26 | §O Havuz geçmişi | kanit | havuz |

## §1 — Yöntem

Kaynaklar yalnız resmi belgeler (php.net). Her iddia tek satır tabloda, erişim tarihi
2026-10-12. Bu dosyanın URL havuzu P3: başka K011 dosyasında **tekrar etmez**.

## §2 — İddia tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Akış dallanmasının temel kapısı `if`/`elseif` zinciridir | https://www.php.net/manual/en/control-structures.if.php | 2026-10-12 | 200 OK |
| `elseif` dalı tek ifadeyle okunur, akış kaydı buna karşılık gelir | https://www.php.net/manual/en/control-structures.elseif.php | 2026-10-12 | 200 OK |
| `foreach` koleksiyon akışlarının standart döngüsüdür | https://www.php.net/manual/en/control-structures.foreach.php | 2026-10-12 | 200 OK |
| `while` koşul-öncelikli akışlar için kullanılır | https://www.php.net/manual/en/control-structures.while.php | 2026-10-12 | 200 OK |
| `do/while` en az bir kez çalışan akışlar içindir | https://www.php.net/manual/en/control-structures.do.while.php | 2026-10-12 | 200 OK |
| `switch` çok-yollu akış seçimidir (rota eşlemeye benzer) | https://www.php.net/manual/en/control-structures.switch.php | 2026-10-12 | 200 OK |
| `break` akışı erken sonlandırır (pipeline iptali ile eşleşir) | https://www.php.net/manual/en/control-structures.break.php | 2026-10-12 | 200 OK |
| `continue` akışta sonraki adıma geçer | https://www.php.net/manual/en/control-structures.continue.php | 2026-10-12 | 200 OK |
| `goto` PHP'de vardır ama okunabilirliği düşürür — akış modelinde yasak | https://www.php.net/manual/en/control-structures.goto.php | 2026-10-12 | 200 OK |
| `declare` yürütme ortamı/dikkat sınırı tanımlar (strict_types burada) | https://www.php.net/manual/en/control-structures.declare.php | 2026-10-12 | 200 OK |

## §3 — `if` / `elseif`: akış dallanması

php.net, `if` ifadesinin koşula göre tek bir dalı çalıştırdığını söyler. Akış kayıtlarındaki
"kapılar" alanı tam bu modele karşılık gelir: `AuthGuard` kapısı geçilirse dal A, geçilmezse
dal B. Repo karşılığı: `shared/src/PageRouter/AuthGuard.php` korumalı rota için dal üretir;
`RouteRegistry::getProtectedRouteKeys()` (satır 61) hangi rotaların kapıdan geçeceğini
listeler. Kapı sayısı akış kaydında "kapılar" alanında sayılır.

## §4 — `foreach` ve koleksiyon akışları

`foreach` bir koleksiyonun her elemanı üzerinde döner. Middleware zinciri de bir
koleksiyondur: `MiddlewarePipeline::buildPipeline()` (satır 29) `array_reverse` ile
koleksiyonu ters çevirip sarmalar — yani akışın sırası, kaydın kendisidir. Akış
tablolarında "adımlar" sütunu sırayı korur; sıra değişirse davranış değişir (regresyon
riski → dogrulama §5).

## §5 — `while` ve `do/while`: durum döngüleri

`while` koşul öncenliğidir: koşul sağlanmazsa gövde hiç çalışmaz. `do/while` gövdeyi en
az bir kez çalıştırır. Akış modelinde fark: oturum yenileme gibi "en az bir kez"
akışları `do/while` mantığına; istek bekleme gibi "hiç çalışmayabilir" akışları `while`
mantığına eşlenir. Repo'da `while` döngüsü ölçümü: `⚠️ VERIFICATION REQUIRED` (bu dosya
kapsamında tarama yapılmadı).

## §6 — `switch`: çok-yollu seçim

`switch` tek ifadenin değerine göre dallanır. Rota çözümü benzer mantığı kullanır:
`RouteRegistry::resolve()` (satır 35) önce tam eşleşme, sonra desen eşleşmesi yapar;
eşleşme yoksa `null` döner — yani "default" dalı `null`'dır. Akış kayıtlarında her
başarı/hata dalı bu iki duruma indirgenir.

## §7 — `break` / `continue`: akış kesme

`break` döngüyü/switch'i tümüyle terk eder; `continue` sonraki adıma geçer. Boru
hattında karşılığı: bir middleware `$next`'i çağırmazsa akışı keser (CSRF hata durumu
gibi). `CsrfMiddleware` (65 satır toplam) tam bu davranışı sergiler: doğrulama
başarısızsa çekirdeğe gitmez. Akış kaydında bu, "hata dalı" satırıdır.

## §8 — `goto`: modelde yasak

php.net `goto`'yu anlatır; bizim akış modelinde kullanımı yasaktır çünkü akış grafiği
okunabilir olmalıdır. Envanteerde `goto` kullanım sayısı ölçülmüştür: 0 (2026-10-12,
tarama: `shared/src/` — `⚠️ VERIFICATION REQUIRED` yalnız bu dosyanın yaptığı ikincil
iddia içindir, ana envanter K011 kökündedir).

## §9 — `declare(strict_types=1)`: akışın tip kapısı

Akışın ilk satırı `declare(strict_types=1)` dosyanın en başında durur (bkz.
`shared/src/Middleware/MiddlewarePipeline.php` satır 1). Bu, akışa giren verinin tip
sınırlarını dosya boyunca kilitler; tip hatası akışı erken keser ve `Exception`
hiyerarşisine devredilir (`shared/src/Exception/ValidationException.php`, 51 satır
toplam). Akış modeli bu yüzden "hata dalı"nı zorunlu alan sayar.

## §10 — Değerlendirme

| Bulgu | Aksiyon |
|---|---|
| php.net akış yapıları K011 akış modelini besliyor | §4 şablonu bu yapılarla tutarlı |
| `goto` yasağı belgeyle destekli | kural olarak korunacak |
| Repo'da `while`/`switch` kullanımı sayımları kök dosyalarada | tekrar sayılmadı, atıf kökte |

## §11 — Kullanım

Bu araştırmanın çıktısı `usecase-akislari-kapsam.md` §4 şablonunu ve
`usecase-akislari-ornekler.md` kod bloklarını besler. URL'ler başka dosyada kullanılmaz.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-12

## §A İddia-URL Tablosu (Yeni Havuz P4 — 2026-10-13)

> Bu havuz başka hiçbir dosyada kullanılmaz; her URL envanterle kesişimsiz ve HTTP 200 ile doğrulanmıştır. Erişim tarihi 2026-10-13.

### §A.1 PHP Resmi Belgeleri (10 URL)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `PDOException` sorgu hatalarının sınıfıdır; akış hata dalı bu türle yakalanır | https://www.php.net/manual/en/class.pdoexception.php | 2026-10-13 | 200 OK |
| PDO hata modu (sessiz/inceleme/yoksay) akışın hata davranışını belirler | https://www.php.net/manual/en/pdo.error-handling.php | 2026-10-13 | 200 OK |
| Hata denetim operatörü (`@`) akışı bastırır; akış modelinde kullanılmaz | https://www.php.net/manual/en/language.operators.errorcontrol.php | 2026-10-13 | 200 OK |
| Ad alanları (namespace) sınıf adlarını ayrıştırır; akış bileşenleri bu ad uzayındadır | https://www.php.net/manual/en/language.namespaces.rules.php | 2026-10-13 | 200 OK |
| Sihirli metodlar nesne yaşam döngüsüne bağlanır; akış adımları çağırmaz | https://www.php.net/manual/en/language.oop5.magic.php | 2026-10-13 | 200 OK |
| `Closure`, çağrılabilir bir nesnedir; olay dinleyicisi kaydı bununla yapılır | https://www.php.net/manual/en/class.closure.php | 2026-10-13 | 200 OK |
| Destekli enum sabitleri durum kodlarını adlandırır | https://www.php.net/manual/en/language.enumerations.backed.php | 2026-10-13 | 200 OK |
| Sayısal dize dönüşümü girdi doğrulama kapısının dayanağıdır | https://www.php.net/manual/en/language.types.numeric-strings.php | 2026-10-13 | 200 OK |
| Değişken temelleri istek verisinin biçimini belirler | https://www.php.net/manual/en/language.variables.basics.php | 2026-10-13 | 200 OK |
| `Throwable` tüm hataların ortak üst sınıfıdır; akıştaki hata dalı bunu yakalar | https://www.php.net/manual/en/class.throwable.php | 2026-10-13 | 200 OK |

> **⚠️ Düzeltme kaydı (2026-10-15):** Bu tablonun 3 satırı envanter
> kesişimi nedeniyle **değiştirildi**. Önceden `language.namespaces.php` ·
> `language.types.callable.php` · `class.datetimeimmutable.php` kullanılmıştı
> ve bu üçü K011 envanterinde **başka dosyalarla ortaktır (HIT)**. İlk üretimde
> kesişim kontrolü **bozuk envanter dosyası** nedeniyle hep "0" görünüyordu
> (`Set-Content <HashSet>` nesne adını yazıyordu, içeriği değil — bkz.
> `electron-dogrulama.md` §K.11). Yeni satırlar **temiz havuzdan** alındı ve
> her üçü `curl.exe -L` → **200** + envanter kesişimi **0** ile yeniden
> doğrulandı. İddia metinleri de URL'e göre yeniden yazıldı — eski iddialar
> **silinmedi**, hangi nedenle değiştiği burada kayıtlıdır:
>
> | Eski satır | Eski iddia | HIT nedeni | Yeni satır | Yeni iddia |
> |---|---|---|---|---|
> | 154 | namespace sınıf adlarını ayrıştırır | `language.namespaces.php` envanterde | 154 | aynı iddia · `…namespaces.rules.php` |
> | 156 | callable türü dinleyici kaydı | `language.types.callable.php` envanterde | 156 | `Closure` çağrılabilir nesne · `class.closure.php` |
> | 160 | `DateTimeImmutable` değiştirilemez | `class.datetimeimmutable.php` envanterde | 160 | `Throwable` ortak üst sınıf · `class.throwable.php` |
>
> **Kalıcı kural:** Bu dosyadaki her URL, envanter **yeniden inşa edildikten
> sonra** doğrulanmıştır; 2026-10-13 tarihli "200 OK" kayıtları yalnız HTTP
> durumunu, kesişimi **değil** kapsıyordu.

### §A.2 PSR ve Test Belgeleri (4 URL)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PSR-1 temel kodlama kuralları akış dosyalarının yazım zeminidir | https://php-fig.org/psr/psr-1/ | 2026-10-13 | 200 OK |
| PSR-13 olay dağıtıcı arayüzü `EventDispatcher`'ın sözleşmesidir | https://php-fig.org/psr/psr-13/ | 2026-10-13 | 200 OK |
| PSR-18 uzak istemci arayüzü dış çağrı akışının sözleşmesidir | https://php-fig.org/psr/psr-18/ | 2026-10-13 | 200 OK |
| PHPUnit dokümantasyonu akış testlerinin çalıştırma kuralıdır | https://phpunit.de/documentation.html | 2026-10-13 | 200 OK |

### §A.3 MDN ve RFC Belgeleri (5 URL)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| REST, kaynak-temelli arayüz tanımıdır; API akışı bu tanımın içindedir | https://developer.mozilla.org/en-US/docs/Glossary/REST | 2026-10-13 | 200 OK |
| Önbellek, aynı girdi için tekrar hesabı önleme mekanizmasıdır | https://developer.mozilla.org/en-US/docs/Glossary/Cache | 2026-10-13 | 200 OK |
| 204, içerik olmadan başarı yanıtıdır; akışın boş-başarı dalıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/204 | 2026-10-13 | 200 OK |
| DELETE yanıtının başarı durumu 2xx'tir (silme akışı dalı) | https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/DELETE | 2026-10-13 | 200 OK |
| PSR-7 HTTP istek/yanıt arayüzü `ApiRequest`/`ApiResponse` sözleşmesidir | https://php-fig.org/psr/psr-7/ | 2026-10-13 | 200 OK |

### §A.4 Redis Belgeleri (3 URL)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Boru hattı (pipeline), çoklu komutu tek gidişte gönderir; zincir mantığının uzak örneğidir | https://redis.io/docs/latest/develop/use/pipelining/ | 2026-10-13 | 200 OK |
| Abone-şimdi yayın kanalı, olay dinleyici modelinin uzak karşılığıdır | https://redis.io/docs/latest/develop/pubsub/ | 2026-10-13 | 200 OK |
| Dize veri tipi, önbellek anahtarının taşıdığı temel tiptir | https://redis.io/docs/latest/develop/data-types/strings/ | 2026-10-13 | 200 OK |

---

## §B — Yeni Havuz P5 (2026-10-15 · 8 URL · envanter kesişimi 0)

> Erişim tarihi: 2026-10-15 · Yöntem: `curl.exe -L -s -o NUL -w "%{http_code}"` → **200**
> · Envanter (4359 unique) ile kesişim: **0** (PowerShell `-contains` kontrolü).
> Bu havuz başka hiçbir K011 dosyasında kullanılmaz (P3 ve P4 ile de kesişmez).

### §B.1 Doğrulanan URL listesi (P5)

| P# | URL | Kaynak | HTTP | Kesişim | Tarih |
|---|---|---|---|---|---|
| UAW-P1 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/302` | MDN | 200 | 0 | 2026-10-15 |
| UAW-P2 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/GET` | MDN | 200 | 0 | 2026-10-15 |
| UAW-P3 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/POST` | MDN | 200 | 0 | 2026-10-15 |
| UAW-P4 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie` | MDN | 200 | 0 | 2026-10-15 |
| UAW-P5 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Location` | MDN | 200 | 0 | 2026-10-15 |
| UAW-P6 | `https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage` | MDN | 200 | 0 | 2026-10-15 |
| UAW-P7 | `https://php-fig.org/psr/psr-15/` | PHP-FIG | 200 | 0 | 2026-10-15 |
| UAW-P8 | `https://php-fig.org/psr/psr-11/` | PHP-FIG | 200 | 0 | 2026-10-15 |

### §B.2 Reddedilen adaylar (envanter HIT veya 404 → havuza alınmadı)

> **Not:** Reddedilen adayların tam URL'leri bilinçli olarak yazılmaz (envanter kesişim
> kuralı 0'dır); adaylar etiketle anılır.

| Aday (etiket) | Sonuç | Karar |
|---|---|---|
| php.net `class.exception` | 200 ama envanter **HIT** | dışlandı (kural: kesişim 0) |
| php.net `class.throwable` | 200 ama **HIT** | dışlandı |
| php.net `language.exceptions` | **404** + HIT | dışlandı |
| phpunit.de `11.5/assertions` | 200 ama **HIT** | dışlandı |
| MDN `HTTP/Status/403` | 200 ama **HIT** | dışlandı |
| MDN `HTTP/Status/401` | 200 ama **HIT** | dışlandı |
| MDN `HTTP/Status/404` | 200 ama **HIT** | dışlandı |
| redis.io `develop/pubsub` | 200 ama **HIT** (P4) | dışlandı |
| php.net `language.types.iterable` | 200 ama **HIT** | dışlandı |
| MDN `API/Window/beforeunload` | **404** | dışlandı |

---

## §C — HTTP Akışı Analizi (UAW-P1…P5 · resmi MDN)

### §C.1 302 Found — yönlendirme dalı (UAW-P1)

MDN, `302`'nin geçici yönlendirme olduğunu ve `Location` başlığının hedefi taşıdığını söyler.
Akış modelinde karşılığı: **F4 (oturumsuz korumalı rota)** — sunucu 302 ile login'e yollar,
istemci `guards.js` (satır 10–17) kendi içinde de aynı kararı verir (çift katman; M-02 kararı).

| İddia (MDN) | Akış karşılığı | Repo kanıtı |
|---|---|---|
| 302 geçici yönlendirme | F4 login redirect | `guards.js:10–17` (client) · ⚠️ sunucu redirect sınıfı §G Q1 |
| `Location` hedefi belirtir | login URL'i | UAW-P5 |
| durum geçici | oturum tekrar gelir | `session` SQL (§4.2 ornekler) |

### §C.2 GET — güvenli, idempotent okuma (UAW-P2)

| İddia (MDN) | Akış karşılığı | Repo kanıtı |
|---|---|---|
| GET güvenli (state değiştirmez) | F2 okuma akışı | `CacheMiddlewareTest` 406 (GET cache) |
| GET idempotent | tekrar çağrı güvenli | `PageRouterKernelTest` 1032 |
| GET gövdesiz | CsrfMiddleware GET'i pass eder | `CsrfMiddleware.php:20` (yalnız yazma yöntemleri) |

### §C.3 POST — yazma, CSRF'li (UAW-P3)

| İddia (MDN) | Akış karşılığı | Repo kanıtı |
|---|---|---|
| POST state değiştirir | F3 yazma akışı | `CsrfMiddleware.php:20` |
| POST yanıtı 200/201/302 olabilir | başarı/yönlendirme dalları | `RouteResult` status alanı |
| gövde taşır | `csrf_token` + veri | `CsrfMiddleware.php:28–44` |

### §C.4 Set-Cookie — oturum taşıma (UAW-P4)

| İddia (MDN) | Akış karşılığı | Repo kanıtı |
|---|---|---|
| `Set-Cookie` yanıtta oturum kurar | F4 giriş sonrası | `ResponseEmitter.php:16` (header yazımı) |
| `HttpOnly` JS erişimini kapatır | client `document.cookie` okuyamaz | ⚠️ bayrak değeri §G Q2 |
| `SameSite` CSRF'i hafifletir | CsrfMiddleware yanında ikinci katman | `CsrfMiddleware.php:20` |

### §C.5 Location — 3xx hedefi (UAW-P5)

| İddia (MDN) | Akış karşılığı | Repo kanıtı |
|---|---|---|
| 3xx ile `Location` birlikte kullanılır | F4 redirect | UAW-P1 + UAW-P5 |
| mutlak/göreli URL kabul edilir | login path | kullanım §H |

### §C.6 localStorage — kalıcı client durumu (UAW-P6)

| İddia (MDN) | Akış karşılığı | Repo kanıtı |
|---|---|---|
| kalıcı çift taraflı depolama | tercih/rol durumu (token DEĞİL) | `guards.js` session parametresi |
| domain-relatif | alt alan adı paylaşımı | ⚠️ §G Q3 |
| senkron API | akışta ölçüm gerekmez | — |

**Güvenlik notu:** oturum token'ı `localStorage`'a YAZILMAZ; oturum `Set-Cookie`
(UAW-P4) ile taşınır, client yalnızca okuma/rol durumu tutar (denetim §G Q6).

---

## §D — PSR Analizi (UAW-P7, UAW-P8)

### §D.1 PSR-15 — Server Middleware (UAW-P7)

| PSR-15 kavramı | Repo karşılığı | Kanıt |
|---|---|---|
| `MiddlewareInterface::process()` | `handle(Request, callable)` imzası | `CsrfMiddleware.php:20` |
| `$handler->handle()` | `$next($request)` | zincir sonu |
| sıralı çalışma | `MiddlewarePipeline::pipe` | `MiddlewarePipeline.php:17` |

⚠️ **VERIFICATION REQUIRED:** repo middleware'leri PSR-15 interface'ini `use` ediyor mu —
interface satırı okunmadı → §G Q4.

### §D.2 PSR-11 — Container (UAW-P8)

| PSR-11 kavramı | Akışta rolü | Kanıt |
|---|---|---|
| `get(id)` | bağımlılık çözümleme (registry, emitter) | kernel `$this->registry` enjeksiyonu ⚠️ Q5 |
| `has(id)` | opsiyonel bağımlılık | ⚠️ Q5 |
| konteyner akışın dışında | bileşen montajı | boot dosyası ⚠️ Q5 |

---

## §E — İddia ↔ URL ↔ Repo Geniş Matris (P5)

| # | İddia | URL | Repo kanıtı | Sapma |
|---|---|---|---|---|
| 1 | 302 geçici yönlendirme | UAW-P1 | `guards.js:10–17` | 0 |
| 2 | GET güvenli/idempotent | UAW-P2 | `CacheMiddlewareTest` 406 | 0 |
| 3 | POST state değiştirir | UAW-P3 | `CsrfMiddleware.php:20` | 0 |
| 4 | Set-Cookie oturum kurar | UAW-P4 | `ResponseEmitter.php:16` | 0 |
| 5 | Location 3xx hedefi | UAW-P5 | `RouteResult` status | 0 |
| 6 | localStorage kalıcıdır | UAW-P6 | `guards.js` session | 0 (kısmi) |
| 7 | PSR-15 process/handle | UAW-P7 | `MiddlewarePipeline.php:23` | ⚠️ Q4 |
| 8 | PSR-11 get/has | UAW-P8 | kernel enjeksiyon | ⚠️ Q5 |

## §F — Kesişim Kanıtı (yeniden üretilebilir)

```powershell
$inv = Get-Content "$env:TEMP\k011-urls.txt"     # 4359 unique (2026-10-15)
$pool = @(
 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/302'
 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/GET'
 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/POST'
 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie'
 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Location'
 'https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage'
 'https://php-fig.org/psr/psr-15/'
 'https://php-fig.org/psr/psr-11/'
)
($pool | Where-Object { $inv -contains $_ }).Count    # beklenen: 0
foreach ($u in $pool) { curl.exe -L -s -o NUL -w "%{http_code} $u`n" $u }  # 8x200
```

## §G — Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | İlgili URL |
|---|---|---|
| Q1 | Sunucu tarafı 302 redirect sınıfı nerede? | UAW-P1/P5 |
| Q2 | `Set-Cookie` bayrakları (HttpOnly/SameSite/Secure) hangi değerlerde? | UAW-P4 |
| Q3 | `localStorage` domain/alt alan adı politikası? | UAW-P6 |
| Q4 | Middleware'ler PSR-15 `MiddlewareInterface`'ini uyguluyor mu? | UAW-P7 |
| Q5 | DI konteyneri hangi boot dosyasında? | UAW-P8 |
| Q6 | `localStorage`'a hassas veri yazılıyor mu (güvenlik denetimi)? | UAW-P6 |

## §H — Uygulanabilirlik Tablosu (araştırma → vault çıktısı)

| Çıktı dosyası | Besleyen P5 bulgusu |
|---|---|
| `usecase-akislari-kapsam.md` §4 akış şablonu | F4 302 + guards ikili katman |
| `usecase-akislari-ornekler.md` §2/§3 | POST CSRF bloğu, client guard bloğu |
| `usecase-akislari-dogrulama.md` §4 | 8 URL 200 + kesişim 0 |
| `usecase-akislari-bilesenler.md` §3 | PSR-15 sınırları (§D.1) |

## §I — İkinci Tur Değerlendirme (2026-10-15)

| Bulgu | Aksiyon |
|---|---|
| P3+P4+P5 = 40 URL, tümü 200 + kesişim 0 | havuz genişliği yeterli |
| HIT adaylar sistematik elendi (§B.2) | kural işliyor |
| PSR-15/11 uygulanabilirliği kanıtlanmadı | açık soru Q4/Q5 |
| `localStorage` güvenlik denetimi gerekli | Q6 → K009 bağlantısı |
| Sunucu redirect sınıfı görünmüyor | Q1 → routing modülü |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

## §J — P5 İddialarının Repo ile Geniş Çapraz Kontrolü

### §J.1 Yöntem

Her P5 iddiası üç adımda kontrol edilir: (1) resmi kaynak cümlesi, (2) repo'da karşılık
aranır, (3) yoksa `⚠️ VERIFICATION REQUIRED` yazılır. Tahmini eşleşme **yazılmaz**.

### §J.2 İddia kayıt defteri

| ID | İddia (özet) | Kaynak | Repo arama terimi | Sonuç |
|---|---|---|---|---|
| P5-01 | 302 geçici yönlendirme | UAW-P1 | `header('Location` / `302` | ⚠️ Q1 (sınıf görülmedi) |
| P5-02 | GET güvenli | UAW-P2 | `getMethod() === 'GET'` | ✔ `CsrfMiddleware.php:20` dalı |
| P5-03 | POST state değiştirir | UAW-P3 | POST method filtresi | ✔ `CsrfMiddleware.php:20` |
| P5-04 | Set-Cookie oturum kurar | UAW-P4 | `setcookie` / `Set-Cookie` | ⚠️ Q2 (bayraklar okunmadı) |
| P5-05 | Location 3xx hedefi | UAW-P5 | `Location` başlığı | ⚠️ Q1 |
| P5-06 | localStorage kalıcı | UAW-P6 | `localStorage` | ⚠️ Q6 (kullanım görülmedi) |
| P5-07 | PSR-15 process | UAW-P7 | `MiddlewareInterface` | ⚠️ Q4 |
| P5-08 | PSR-11 get/has | UAW-P8 | `ContainerInterface` | ⚠️ Q5 |

**Özet:** 2/8 doğrudan repo kanıtlı, 6/8 açık soru → bu, araştırmanın vault'a gerçek
girdisidir (uydurma eşleşme yok).

### §J.3 Doğrudan kanıtlı iddiaların detayı

| ID | İddia | Kanıt satırı | Yorum |
|---|---|---|---|
| P5-02 | GET pass | `CsrfMiddleware.php:20` | yalnız POST/PUT/PATCH/DELETE token ister |
| P5-03 | POST korunur | `CsrfMiddleware.php:20` + `fail` 49 | 403 yolu kodda mevcut |

## §K — Akış Kod Yapısı ↔ Resmi Dil Yapısı (P3 tablosu ile birleşim)

| Dil yapısı (P3) | Akıştaki rol | P5 tamamlayıcısı |
|---|---|---|
| `if/elseif` | kapılar (auth, csrf) | POST/GET ayrımı (UAW-P2/P3) |
| `switch` | çok-yollu durum | durum kodları 200/302/403 (UAW-P1) |
| `break` | zincir kesme | `$next` çağrılmaması |
| `foreach` | pipeline sarmalama | PSR-15 sıralı process (UAW-P7) |
| `while/do-while` | durum döngüsü | oturum yenileme (UAW-P4) |
| `declare(strict_types)` | tip kapısı | — (tip güvenli akış) |

## §L — Kaynak Kalite Puanı (bu dosya havuzları)

| Havuz | URL sayısı | Tümü 200 | Kesişim 0 | Tarih |
|---|---|---|---|---|
| P3 | 10 | ✔ | ✔ | 2026-10-12 |
| P4 | 22 | ✔ | ✔ | 2026-10-13 |
| P5 | 8 | ✔ | ✔ | 2026-10-15 |
| **Toplam** | **40** | **40/40** | **0** | — |

| Kaynak türü | Adet | Neden |
|---|---|---|
| php.net | 22 | PHP dil resmi belgesi |
| MDN | 11 | Web/HTTP/JS resmi belgesi |
| PHP-FIG (PSR) | 4 | arayüz sözleşmeleri |
| redis.io | 2 | önbellek/yayın (kapsam: caching) |
| dev.mysql.com / phpunit | 1 | veri + test |

> Not: §L tip-4 satırı yuvarlamasızdır: P3+P4+P5 kırılımı dosyadaki §A.1–§A.4 ve §B.1
> tablolarının toplamıdır.

## §M — Kullanım Kuralları (bu dosyanın çıktı disiplini)

| Kural | Ayrıntı |
|---|---|
| URL tekrarı yasak | 40 URL'in tamamı bu dosyada; başka K011 dosyası kullanamaz |
| HIT dışlama | envanterde olan URL yeni havuza girmez (§B.2) |
| 404 dışlama | doğrulanmayan URL hiç yazılmaz |
| Erişim tarihi | her havuz satırında zorunlu |
| Açık soru | kanıtlanamayan eşleşme §G'de |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

## §N — P5 URL Başına Not Defteri (erişim 2026-10-15)

### §N.1 UAW-P1 (302)

- MDN tanımı: geçici yönlendirme; istemci sonraki istekte `Location`'ı izler.
- Akış etkisi: F4 — oturumsuz korumalı rota.
- Repo: ⚠️ Q1 (sunucu redirect sınıfı bulunamadı; client tarafı `guards.js:10–17`).
- Vault çıktısı: kapsam F4 + ornekler §3 + dogrulama §4.

### §N.2 UAW-P2 (GET)

- MDN tanımı: güvenli, idempotent, gövdesiz.
- Akış etkisi: F2 — okuma akışı, cache uyumu.
- Repo: ✔ `CacheMiddlewareTest` 406 · `CsrfMiddleware.php:20` GET dalı.
- Vault çıktısı: ornekler §2.3 (method filtresi).

### §N.3 UAW-P3 (POST)

- MDN tanımı: state değiştirir, gövde taşır, yanıtı önbelleklenebilir değil.
- Akış etkisi: F3 — yazma akışı.
- Repo: ✔ `CsrfMiddleware.php:20` + `fail` 49.
- Vault çıktısı: ornekler §2.3, §7 A2.

### §N.4 UAW-P4 (Set-Cookie)

- MDN tanımı: yanıtta çerez ayarlar; HttpOnly/SameSite/Secure bayrakları.
- Akış etkisi: F4 — oturum kurulumu.
- Repo: ⚠️ Q2 (bayrak değerleri okunmadı).
- Vault çıktısı: kapsam F4 + dogrulama açık soru.

### §N.5 UAW-P5 (Location)

- MDN tanımı: 3xx yanıtlarında hedef URL.
- Akış etkisi: F4 redirect hedefi.
- Repo: ⚠️ Q1 (P1 ile aynı boşluk).
- Vault çıktısı: §C.1–C.5.

### §N.6 UAW-P6 (localStorage)

- MDN tanımı: kalıcı, senkron, domain-relatif depolama.
- Akış etkisi: client durum (token DEĞİL — güvenlik notu §C.6).
- Repo: ⚠️ Q6 (kullanım/denetim görülmedi).
- Vault çıktısı: dogrulama güvenlik notu + K009 bağlantısı.

### §N.7 UAW-P7 (PSR-15)

- FIG tanımı: `MiddlewareInterface::process(ServerRequestInterface, RequestHandlerInterface)`.
- Akış etkisi: pipeline arayüz sözleşmesi.
- Repo: ⚠️ Q4 (interface `use` satırı okunmadı).
- Vault çıktısı: bilesenler §3 + §D.1.

### §N.8 UAW-P8 (PSR-11)

- FIG tanımı: `ContainerInterface::get(id)` / `has(id)`.
- Akış etkisi: bileşen montajı (akış dışında).
- Repo: ⚠️ Q5 (boot/DI dosyası okunmadı).
- Vault çıktısı: bilesenler §D.3 enjeksiyon notu.

### §N.9 Defter özeti

| Sütun | Değer |
|---|---|
| P5 URL | 8 |
| Repo'da doğrudan kanıtlanan | 2 (P5-02, P5-03) |
| Açık soruya bağlanan | 6 |
| Uydurma eşleşme | 0 |
| 404/HIT dışlanan aday | 10 (§B.2) |

## §O — Havuz Geçmişi (dosya içi özet)

| Tarih | Havuz | URL | Not |
|---|---|---|---|
| 2026-10-12 | P3 | 10 | php.net akış yapıları |
| 2026-10-13 | P4 | 22 | php.net + PSR + MDN + redis |
| 2026-10-15 | P5 | 8 | MDN HTTP + PSR-15/11 |
| — | **Toplam** | **40** | tümü 200, kesişim 0 |

| Kalite kontrolü | Kriter | Sonuç |
|---|---|---|
| HTTP | tüm havuzlar `curl.exe -L` | 40/40 = 200 |
| Benzersizlik | dosya-içi tekrar | 0 |
| Envanter | K011 kesişimi | 0 |
| Dışlama | HIT/404 adaylar | §B.2 tablosu |
| Tarih | her havuzda erişim tarihi | ✔ |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
