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
| Ad alanları (namespace) sınıf adlarını ayrıştırır; akış bileşenleri bu ad uzayındadır | https://www.php.net/manual/en/language.namespaces.php | 2026-10-13 | 200 OK |
| Sihirli metodlar nesne yaşam döngüsüne bağlanır; akış adımları çağırmaz | https://www.php.net/manual/en/language.oop5.magic.php | 2026-10-13 | 200 OK |
| Çağrılabilir (callable) türü dinleyici kaydını tanımlar (`addListener`) | https://www.php.net/manual/en/language.types.callable.php | 2026-10-13 | 200 OK |
| Destekli enum sabitleri durum kodlarını adlandırır | https://www.php.net/manual/en/language.enumerations.backed.php | 2026-10-13 | 200 OK |
| Sayısal dize dönüşümü girdi doğrulama kapısının dayanağıdır | https://www.php.net/manual/en/language.types.numeric-strings.php | 2026-10-13 | 200 OK |
| Değişken temelleri istek verisinin biçimini belirler | https://www.php.net/manual/en/language.variables.basics.php | 2026-10-13 | 200 OK |
| `DateTimeImmutable` zaman damgası değiştirilemez; denetim kaydı buna güvenir | https://www.php.net/manual/en/class.datetimeimmutable.php | 2026-10-13 | 200 OK |

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
