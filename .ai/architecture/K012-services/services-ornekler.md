---
node_id: K012-services
node_type: module
role: ornekler
status: draft
parent_id: K012-services
children: []
depends_on:
  - "services-master.md"
  - "services-dogrulama.md"
evidence:
  - "repo S1: shared/src/Api (Registry/Versioning/Middleware) — 19 satır"
  - "repo S1: home.coremusic.net (.htaccess/web.config/MusicStreamHandler)"
  - "pseudocode kartları TASARIM İDDİASI olarak işaretli"
validation:
  - "her kartta Dosya+Satır veya Kaynak/YOK satırı var (2026-10-13)"
  - "uydurma kütüphane/framework adı 0 (2026-10-13)"
open_questions:
  - "Q5 background-jobs motoru — örnek motor kodu YAZILAMAZ"
  - "Q9 servisler arası taşıyıcı — akış örnekleri pseudocode kalır"
  - "Q1 ai-service sahipliği — ai kartı sembolik"
date: 2026-10-13
version: 1.0.0
authority: "SSOT: repo kanıtı (S1) + services-dogrulama.md §6"
group: kod
---

# K012-services — services-ornekler.md

> Bu dosya K012'nin **kod/akış örnekleri** kart desteğidir. Her kart iki
> sınıfdır: **(A) repo kanıtlı örnek** (`Dosya:` + `Satır:`) veya
> **(B) tasarım pseudocode'u** (`Kaynak: …` / `Repo'da … YOK`). Arada sınıf
> yoktur: kanıtsız "gerçek kod" taklidi yazılmaz.

## §1 Amaç ve Kapsam

| Madde | Değer |
|---|---|
| Dosya | `K012-services/services-ornekler.md` |
| Rol | örnek/kanıt kartları (kod düzeyi referans) |
| Kapsam | K012 kök örnekleri — registry, versioning, rate limit, yönlendirme, streaming |
| Kapsam dışı | kütüphane tanıtımı, framework öğretimi, üretime hazır kod |
| Tarih | 2026-10-13 |
| Otorite | Vault Steward — Red Team · Human Mode · Truth Mode |

Örnekler bu dosyada **kanıt** olarak kullanılır; "çalışır" iddiası taşımaz.
Çalışırlık kanıtı bu görevde yapılmadı (build/test yok) → `⚠️ VERIFICATION REQUIRED`.

## §2 Örnek Yazım Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Repo kaynağı olan her örnek `Dosya:` + `Satır:` taşır | S1 kanıtı |
| 2 | Repo'da olmayan alan `Repo'da {alan} seviyesi kod YOK — kaynak: …` der | S5 dürüstlüğü |
| 3 | Pseudocode bloğu `TASARIM İDDİASI` etiketli olur | kanıt ayrımı |
| 4 | Uydurma kütüphane/framework/versiyon adı YAZILMAZ | R7 (riskler §10) |
| 5 | Bilinmeyen davranış `UNKNOWN` / `⚠️` ile işaretlenir | Zero-Hallucination |
| 6 | Yasak desenler §13'te listelenir | anti-örnek disiplini |

## §3 Örnek Envanteri

| # | Kart | Sınıf | Kanıt |
|---|---|---|---|
| 1 | servis kaydı (register) | A | `ServiceRegistry.php:26` |
| 2 | servis keşfi (discover) | A | `ServiceRegistry.php:38` |
| 3 | sağlık sorgusu | A | `ServiceRegistry.php:52` + `ServiceHealth.php:44` |
| 4 | servis tanımı varsayılanları | A | `ServiceDefinition.php:79` |
| 5 | sürüm çözümleme (başlık) | A | `VersionResolver.php:57` |
| 6 | oran kısıtı (60/60s) | A | `RateLimitMiddleware.php:34` |
| 7 | uç nokta yönlendirme (Apache/IIS) | A | `.htaccess:26` + `web.config:14` |
| 8 | aralık (Range) stream yanıtı | A | `MusicStreamHandler.php:91` |
| 9 | servis yaşam döngüsü akışı | B | pseudocode — §9 |
| 10 | arka plan iş kuyruğu akışı | B | **YOK** — Q5 → §10 |
| 11 | ai-service sunum akışı | B | **YOK** — Q1 → §11 |
| 12 | hata/yeniden deneme akışı | B | pseudocode — §12 |

Sınıf dağılımı: **8 A (repo kanıtlı) · 4 B (tasarım/YOK)** — §14 indeksi.

## §4 Kart A1 — Servis Kaydı

**Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 26
(register metodunun kayıtlı olduğu satır — 2026-10-13 okundu)

| Alan | Değer |
|---|---|
| Ne yapar | servis tanımını deftere kaydeder |
| Kanıt sınıfı | S1 |
| Çağrı öncesi | `ServiceDefinition` oluşmalı (S1 `:19`) |
| Çağrı sonrası | `$services`/`$health` güncellenir (S1 `:19`) |
| Davranış ayrıntısı | metod gövdesi bu görevde satır satır analiz edilmedi → ⚠️ |

Okuma notu: kayıt **bellek-içi dizi** üzerine kuruludur; süreç dışı kalıcılık
için kanıt yok (S5) → `⚠️ UNKNOWN`.

## §5 Kart A2 — Servis Keşfi

**Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 38
(discover metodunun kayıtlı olduğu satır)

| Alan | Değer |
|---|---|
| Ne yapar | servis adıyla kaydı bulur |
| Kanıt sınıfı | S1 |
| Girdi | servis adı (id) — `ServiceDefinition` alanı |
| Bulunamazsa | davranış OKUNMADI → ⚠️ VERIFICATION REQUIRED |
| Ağ keşfi (DNS vb.) | repo'da kanıt yok → ⚠️ UNKNOWN (Q9) |

İlişki: §6.2 çağrım akışında `discover()` 4. adımda geçer (oradaki akış
pseudocode'tur; buradaki metod varlığı S1'dir).

## §6 Kart A3 — Sağlık Sorgusu

**Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 52
**Dosya:** `shared/src/Api/Registry/ServiceHealth.php` · **Satır:** 44 (hasFailures)
**Dosya:** `shared/src/Api/Registry/ServiceHealth.php` · **Satır:** 34 (overallStatus)

| Alan | Değer |
|---|---|
| Ne yapar | servis sağlık durumunu toplar/sorgular |
| Kanıt sınıfı | S1 (üç satır) |
| Toplu alan | `overallStatus` |
| Hata sorgusu | `hasFailures()` |
| Periyot/eşik | bilinmiyor → ⚠️ UNKNOWN (master §13) |
| Boşaltma | `ServiceRegistry::clear()` S1 `:97` |

## §7 Kart A4 — Servis Tanımı Varsayılanları

**Dosya:** `shared/src/Api/Registry/ServiceDefinition.php` · **Satır:** 79

| Alan | Değer | Not |
|---|---|---|
| `timeout` | `5` | birim metinde YOK → saniye varsayımı yazılmaz |
| `version` | `'1.0.0'` | tek sürüm kaydı (çoklu sürüm UNKNOWN) |
| `healthy` | `true` | varsayılan sağlık |
| Sınıf tanımı | `:19` | VO düzeyinde servis tanımı |

Kullanım: §12 hata akışındaki timeout örneği bu değeri referans alır; davranış
(soğuma/üretim) ayrıntısı analiz edilmedi → ⚠️.

## §8 Kart A5 — Sürüm Çözümleme

**Dosya:** `shared/src/Api/Versioning/VersionResolver.php` · **Satır:** 57
**Dosya:** `shared/src/Api/Versioning/VersionRegistry.php` · **Satır:** 96 (add)

| Alan | Değer |
|---|---|
| Başlık | `X-API-Version` |
| Ne yapar | istemci başlığından API sürümünü çözümler |
| Kayıt | `VersionRegistry::add()` ile sürüm eklenir |
| Kanıt sınıfı | S1 (iki satır) |
| Kırıcı sürüm politikası | vault'ta yok → ⚠️ UNKNOWN |
| 429 kodu ilişkisi | RFC 6585 (URL 200) — kullanım kanıtı yok → S3 kapsamında değil |

## §9 Kart A6 — Oran Kısıtı (Rate Limit)

**Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` · **Satır:** 34
**Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` · **Satır:** 22 (sınıf)

| Alan | Değer |
|---|---|
| Pencere | 60 istek / 60 saniye |
| Aşım yanıtı | 429 + `Retry-After` (middleware akışı, 2026-10-13 okundu) |
| Kanıt sınıfı | S1 |
| Uygulama katmanı | middleware (PSR-15 iddiası Q8 — bağımsız kanıt yok) |
| Pencere belleği | nerede tutulduğu analiz edilmedi → ⚠️ UNKNOWN |

## §10 Kart A7 — Uç Nokta Yönlendirme

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 26
**Dosya:** `home.coremusic.net/web.config` · **Satır:** 14

| Alan | Değer |
|---|---|
| Ne yapar | Apache ve IIS'te isteği uc noktaya yönlendirir |
| Kanıt sınıfı | S1 (iki satır — iki platform) |
| İlişki | master §14 "tek uç nokta" kriteri K5/K6/K14 |
| Kural | iki platformda da aynı davranış şart (parite) |
| Ayrıntı | desen metni satır satır bu görevde çözülmedi → ⚠️ |

## §11 Kart A8 — Aralık (Range) Stream Yanıtı

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 91
**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 46 (1 MiB chunk)
**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 24 (MIME tablosu)

| Alan | Değer |
|---|---|
| Ne yapar | `Range` isteğine `206` (kısmi) veya `416` (kabul edilemez) döner |
| Chunk | 1 MiB |
| MIME | tablo `:24` |
| Kanıt sınıfı | S1 (üç satır) |
| Sahiplik | K015 ile ortak kanıt (dogrulama §12.7) |
| HTML istemcisi | bu dosyada kanıtlanmaz — K015'e devredildi |

## §12 Kart B1 — Servis Yaşam Döngüsü Akışı (PSEUDOCODE)

**Repo'da servis yaşam döngüsü akış seviyesi kod YOK — kaynak: tasarım (master §9) + repo metot kanıtları (S1).**

~~~text
TASARIM İDDİASI — pseudocode (çalışır kod değildir)
1. tanim = ServiceDefinition.olustur(ad, surum)      ' kanıt: ServiceDefinition:19
2. registry.kaydet(tanim)                            ' kanıt: ServiceRegistry:26
3. registry.surumEkle(surum)                         ' kanıt: VersionRegistry:96
4. surecIcerinde:
     saglik = registry.saglikSorgu()                 ' kanıt: ServiceRegistry:52
     eger saglik.hasFailures() ise mudahale          ' kanıt: ServiceHealth:44
5. registry.bosalt()  (kapanista)                    ' kanıt: ServiceRegistry:97
~~~

| Alan | Değer |
|---|---|
| Sınıf | B (pseudocode) |
| Adımların varlığı | 1–5 adımlarının her biri repo'da S1 ile kanıtlı (§4–§7) |
| Adımların sırası/çalışması | ⚠️ VERIFICATION REQUIRED (analiz edilmedi) |
| Yasak | bu bloğa gerçek kütüphane adı eklemek (R7) |

## §13 Kart B2 — Arka Plan İş Kuyruğu Akışı (KOD YOK)

**Repo'da background-jobs iş motoru seviyesi kod YOK — kaynak: upstream docs değil, durum: Q5 açık.**

| Alan | Değer |
|---|---|
| Sınıf | B-YOK |
| Motor | ⚠️ UNKNOWN — ad yazılmaz (R7, riskler §10) |
| Zamanlayıcı | ⚠️ UNKNOWN |
| Kuyruk | ⚠️ UNKNOWN |
| Olası tasarım (etiketli) | "iş tanıma → kuyruğa alma → yürütme → sonuç yazımı" — TASARIM İDDİASI |
| Kanıt arayışı | tur-3 (bagimliliklar §18/2) |
| Yasak | kütüphane/kuyruk teknolojisi ADI yazmak |

Pseudocode bloğu bile bu kartta **yazılmaz**; motor bilinmediği için yazılan
blok uydurma davranış taşır.

## §14 Kart B3 — ai-service Sunum Akışı (KOD YOK)

**Repo'da ai-service implementasyon seviyesi kod YOK — kaynak: taslak kapsam (master §21.7).**

| Alan | Değer |
|---|---|
| Sınıf | B-YOK |
| Bağlı domen | ⚠️ UNKNOWN (Q1, R1) |
| Sunum biçimi (arayüz) | ⚠️ UNKNOWN |
| Tasarım iddiası | "diğer servisler YZ yeteneğini ai-service üzerinden çağırır" — TASARIM İDDİASI |
| Kanıt | yalnız vault ELI15 metni (S2) |
| Yasak | K006/K016 adına bu dosyada karar vermek |

## §15 Kart B4 — Hata / Yeniden Deneme Akışı (PSEUDOCODE)

**Repo'da retry akışı seviyesi kod YOK — kaynak: RFC 6585 (429/503, URL 200) + tasarım (master §16).**

~~~text
TASARIM İDDİASI — pseudocode (çalışır kod değildir)
1. istek gelir → oranimKisitiKontrol()          ' kanıt: RateLimitMiddleware:34
2. asim varsa → 429 + Retry-After bas          ' kanıt: middleware akışı
3. saglik kaybi varsa → mudahale (clear/Update) ' kanıt: ServiceRegistry:97
4. zaman asimi → davranis UNKNOWN              ' kanıt YOK
5. surec cokusu → kurtarma UNKNOWN             ' kanıt YOK
~~~

| Alan | Değer |
|---|---|
| Sınıf | B (pseudocode) — 1–3 repo kanıtlı, 4–5 YOK |
| Circuit breaker | repo'da YOK → ⚠️ UNKNOWN |
| Retry sayisi | bilinmiyor — sayı UYDURULMAZ |
| Kaynak | master §16 + dogrulama §10 (RFC 6585) |

## §16 Anti-Örnekler (YASAK DESENLER)

| # | Yasak desen | Neden | Doğrusu |
|---|---|---|---|
| 1 | kanıtsız kütüphane adı (`XQueue v2.1` vb.) | R7 uydurma | `⚠️ UNKNOWN` |
| 2 | "çalışır kod" etiketi pseudocode'a | kanıt karışır | `TASARIM İDDİASI` etiketi |
| 3 | `Satır:` uydurma (okunmamış dosya) | sahte S1 | okunan satır veya YOK cümlesi |
| 4 | timeout=5'i "5 saniye" diye yazmak | birim metinde yok | `birim: ⚠️` |
| 5 | retry "3 kez" diye yazmak | sayı kanıtsız | `sayı: UNKNOWN` |
| 6 | Q1'i bu dosyada çözmek | yetki aşımı | sahiplik kararı üst karar |
| 7 | imla düzeltmesi (`identity` yazmak) | v2 kilidi | `identitiy` (korumalı) |

## §17 Kanıt İndeksi (dosya → satır → kart)

| Dosya | Satır(lar) | Kart |
|---|---|---|
| `shared/src/Api/Registry/ServiceRegistry.php` | 19, 26, 38, 52, 97 | A1, A2, A3, B1 |
| `shared/src/Api/Registry/ServiceDefinition.php` | 19, 79 | A4 |
| `shared/src/Api/Registry/ServiceHealth.php` | 34, 44 | A3 |
| `shared/src/Api/Versioning/VersionResolver.php` | 24, 57 | A5 |
| `shared/src/Api/Versioning/VersionRegistry.php` | 24, 96 | A5, B1 |
| `shared/src/Api/Middleware/RateLimitMiddleware.php` | 22, 34 | A6, B4 |
| `home.coremusic.net/.htaccess` | 26 | A7 |
| `home.coremusic.net/web.config` | 14 | A7 |
| `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 24, 46, 91 | A8 |

Toplam: 9 dosya · 19 satır — `services-dogrulama.md` §6 ile birebir aynı kümeydi.

## §18 Sınıf Dağılım Özeti

| Sınıf | Kart | Adet |
|---|---|---|
| A — repo kanıtlı (Dosya+Satır) | A1–A8 | 8 |
| B — pseudocode (tasarım) | B1, B4 | 2 |
| B-YOK (kod yok, ad yazılmaz) | B2, B3 | 2 |
| Toplam | — | 12 |
| Uydurma kod/kütüphane | — | 0 |
| Kanıtsız S1 iddiası | — | 0 |

## §19 Terimler

| Terim | Anlamı |
|---|---|
| kart | bu dosyadaki tek bir örnek kaydı |
| Sınıf A | `Dosya:` + `Satır:` taşıyan kanıt |
| Sınıf B | pseudocode — tasarım seviyesi |
| B-YOK | repo'da kod yok; ad dahil hiçbir teknik ad yazılmaz |
| pseudocode | davranış iskeleti — çalışır kod değildir |
| anti-örnek | yasaklanan hatalı desen (§16) |
| kanıt indeksi | dosya→satır→kart eşlemesi (§17) |
| parite | iki platformda (.htaccess/web.config) aynı davranış |

## §20 Kontrol Listesi (dosya kapanışı)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | FM §4.2 9 alan + group: kod | evet |
| 2 | her kartta kanıt satırı (Dosya/Satır veya Kaynak/YOK) | evet (§4–§15) |
| 3 | uydurma teknoloji adı | 0 (§16/1) |
| 4 | sayı uydurma (retry/vs.) | 0 (§15 — UNKNOWN) |
| 5 | Q1/Q5 kararları bu dosyada verilmiş mi | hayır (B2/B3'te taşındı) |
| 6 | son satır authority | evet (§24) |

## §21 Alt Alan Bazlı Örnek Durum Kartları

Her kart: o alt alanda bu dosyanın neler taşıyabileceğini ve neler taşıyamayacağını söyler.

### §21.1 music-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok → `Repo'da music-service seviyesi kod YOK — kaynak: taslak kapsam` |
| Taşınabilir kart | yalnız akış pseudocode'u (katalog sorgusu → sıralama) |
| Yasak | gerçek sorgu/şema adı yazmak (K007 alanı) |
| Durum | B-YOK |

### §21.2 media-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok (MIME/Range örneği A8'de ama sahibi K015 ile ortak) |
| Taşınabilir kart | varlık işi akışı (pseudocode) |
| Yasak | dosya yolu/şema gerçekliği iddiası |
| Durum | B-YOK |

### §21.3 playback-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok |
| Taşınabilir kart | sıra/konum akışı (pseudocode) |
| Yasak | K015/playback ile çakışacak sahiplik kodu (R2) |
| Durum | B-YOK — Q2 açık |

### §21.4 audio-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok |
| Taşınabilir kart | sınır kararı gelene kadar YOK |
| Yasak | motor/işleme kodu (K003 alanı, R3) |
| Durum | B-YOK — Q3 açık |

### §21.5 user-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok |
| Taşınabilir kart | profil/tercih akışı (pseudocode) |
| Yasak | kimlik doğrulama kodu (K010 alanı) |
| Durum | B-YOK |

### §21.6 device-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok |
| Taşınabilir kart | cihaz kaydı akışı (pseudocode) |
| Yasak | device-identity veri modeli (R4, Q6) |
| Durum | B-YOK — Q6 açık |

### §21.7 ai-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | YOK (S5, dogrulama §7/4) |
| Taşınabilir kart | yalnız bağlam kartı (B3) |
| Yasak | K006/K016 kararı + araç çağrısı kodu |
| Durum | B-YOK — Q1 açık |

### §21.8 integration-service

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | yok |
| Taşınabilir kart | adaptör çağrısı akışı (pseudocode, kontrat adı yazılmaz) |
| Yasak | dış API protokolü/URL'si (K013 alanı) |
| Durum | B-YOK |

### §21.9 background-jobs

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | YOK (S5, dogrulama §7/3) |
| Taşınabilir kart | yok — motor bilinmiyor (B2) |
| Yasak | kuyruk/zamanlayıcı adı |
| Durum | B-YOK — Q5 açık |

### §21.10 service-contracts

| Alan | Değer |
|---|---|
| Repo kanıtlı örnek | vaat/DTO düzeyinde dolaylı (ServiceDefinition A4) |
| Taşınabilir kart | DTO örneği (vaat — sınıf A) + alan tablosu |
| Yasak | evrensel Playlist/Track spec alanlarını gerçekleştirmek (R5) |
| Durum | A-kısmi + B |

## §22 Örnek Kullanım Senaryoları (nasıl okunur)

| # | Senaryo | Okunacak kartlar | Çıktı |
|---|---|---|---|
| 1 | "servis nasıl kaydedilir?" | A1 + A4 + B1 | kanıtlı adımlar + pseudocode |
| 2 | "istek fazlalaşırsa ne olur?" | A6 + B4 | 60/60s kanıtı + akış |
| 3 | "sürüm nasıl bildirilir?" | A5 | `X-API-Version` (S1) |
| 4 | "stream nasıl parçalanır?" | A8 | 1 MiB + Range 206/416 (S1) |
| 5 | "kuyruk örneği var mı?" | B2 | YOK — ad bile yazılmaz |
| 6 | "ai örneği var mı?" | B3 | YOK — Q1 açık |

## §23 Kanıt Yokluk Listesi (bu dosyanın dürüstlük borcu)

| Alan | Durum | Cümle |
|---|---|---|
| background-jobs motoru | yok | `Repo'da background-jobs iş motoru seviyesi kod YOK — kaynak: durum: Q5 açık` |
| ai-service implementasyonu | yok | `Repo'da ai-service implementasyon seviyesi kod YOK — kaynak: taslak kapsam` |
| retry akışı | yok | `Repo'da retry akışı seviyesi kod YOK — kaynak: RFC 6585 + tasarım` |
| servis yaşam döngüsü | kısmi | metotlar S1, akış pseudocode (B1) |
| taşıyıcı (transport) | yok | `Repo'da servisler arası taşıyıcı seviyesi kod YOK — kaynak: UNKNOWN (Q9)` |

Bu beş cümle bu dosyanın "bilmediği her şey" kaydıdır; yeni kanıt geldikçe
cümleler S1 kartına dönüştürülür (§17 indeksine kayıtla).

## §24 Authority

Örnek otoritesi kanıt sınıflarındadır: S1 satırı olmayan hiçbir blok "gerçek
kod" sayılmaz; B-YOK kartlarında teknik ad yazılmaz.

## §25 Kart Üretim ve Güncelleme Kuralları

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | yeni kart = sınıf seçimi önce | A mı B mi B-YOK mu |
| 2 | sınıf A için | dosya okunur, satır numarası teyit edilir |
| 3 | sınıf B için | `TASARIM İDDİASI` etiketi + kaynak (§) yazılır |
| 4 | sınıf B-YOK için | `Repo'da … YOK` cümlesi + açık soru (Q) |
| 5 | kart ID'si | A1–A8 / B1–B4 — silinmez, numara tekrar kullanılmaz |
| 6 | güncelleme | yeni kanıt gelince sınıf yükselir (B-YOK → B → A) |
| 7 | silme | yalnız Vault Steward; silinen kart §16 anti-örneğe taşınabilir |
| 8 | çakışma | §17 indeksi ile kart başlığı birebir aynı dosya/ satırı göstermeli |

Sınıf yükseltme kaydı: `services-dogrulama.md` §17 tur-3 satırına yazılır;
kartsız kanıt, kanıtsız kart bu dosyada geçersizdir.

## §26 Tur-2 Kaydı

| Madde | Değer |
|---|---|
| Tarih | 2026-10-13 |
| Oluşturulan | dosya: §1–§26, 12 kart |
| Kanıt tabanı | 9 dosya / 19 satır S1 (dogrulama §6 ile aynı küme) |
| Bulunan ve düzeltilen | §12'de yabancı karakter izi (CJK 2 → 0) |
| Uydurma ad | 0 |
| Açık soru bağları | Q1, Q5, Q9 kartlara asılı (B2, B3, §15) |
| Bir sonraki | tur-3'te sınıf yükseltmeleri (B → A) |

Kart envanteri ile doğrulama dosyası senkrondur: `services-dogrulama.md`
§12'deki 19 satır buradaki §17 indeksinin kaynağıdır; iki dosya çelişirse
doğrulama dosyası kazanır (SSOT: kanıt defteri).

## §27 Sık Sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Örnekler çalışır kod mu? | Hayır — A sınıfı "kod var" der, çalışırlık ayrıca test ister (bu görevde test yok) |
| 2 | Neden kütüphane adı yok? | R7 — kanıt gelene kadar ad yazmak yasak |
| 3 | A8 K015'in değil mi? | Ortak kanıt — sahiplik K015, K012'de referans |
| 4 | B1 neden pseudocode? | Akış sırası analiz edilmedi; metotlar tek tek S1 |
| 5 | Kart eklemek için? | §25 kuralları + kanıt veya YOK cümlesi |
| 6 | Çelişki halinde ne kazanır? | `services-dogrulama.md` (kanıt defteri SSOT) |
| 7 | Yabancı karakter/mojibake? | 0 — sayım §26'da (CJK 2 → 0 düzeltildi) |
| 8 | Bir sonraki tur | tur-3 — sınıf yükseltmeleri (§25/6) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
