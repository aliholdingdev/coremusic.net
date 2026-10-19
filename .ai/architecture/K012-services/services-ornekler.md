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
  - "S1 — shared/src/Api/Registry/ServiceRegistry.php 1-132 TAM verbatim (2026-10-16 §28)"
validation:
  - "her kartta Dosya+Satır veya Kaynak/YOK satırı var (2026-10-13)"
  - "uydurma kütüphane/framework adı 0 (2026-10-13)"
  - "§28 verbatim blok 2026-10-16 read ile birebir; SSOT çakışması 0 (kimsede yok)"
open_questions:
  - "Q5 background-jobs motoru — örnek motor kodu YAZILAMAZ"
  - "Q9 servisler arası taşıyıcı — akış örnekleri pseudocode kalır"
  - "Q1 ai-service sahipliği — ai kartı sembolik"
date: 2026-10-13
updated: 2026-10-16
version: 1.1.0
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
| Bulunamazsa | `[]` boş dizi döner (satır 42–44, 2026-10-16 §28.1 ile kapandı) |
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
| `shared/src/Api/Registry/ServiceRegistry.php` | 1–132 (tam dosya) | §28 ek — A1/A2/A3 kaynağı |

Toplam: 9 dosya · 19 satır — `services-dogrulama.md` §6 ile birebir aynı kümeydi
(§28 ek satırı bu kümenin **dışında**: aynı dosyanın tam metni, ayrı sayım).

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

## §28 Verbatim Kod Ek — ServiceRegistry.php Tam Dosya (tur-3 eki · 2026-10-16)

**Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 1–132 (tamamı) · **Ölçüm:** 2026-10-16

> K012'nin amiral gemisi kanıtı: §4/§5/§6 kartlarının (A1/A2/A3) kaynağı olan
> dosyanın tam metni. K012 genelinde bu blok **tek kayıt**dır (SSOT taraması
> 2026-10-16: çocuk dosyalarda ServiceRegistry.php verbatim yok; arayüzün
> (`ServiceRegistryInterface`) bloğu `service-contracts-ornekler.md §`dedir).

```php
<?php
declare(strict_types=1);

/**
 * Service Registry implementation.
 *
 * @file ServiceRegistry.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */

namespace CoreMusic\Api\Registry;

use CoreMusic\Contracts\Api\ServiceRegistryInterface;

/**
 * Service Registry implementation.
 */
final class ServiceRegistry implements ServiceRegistryInterface
{
    private array $services = [];

    /**
     * Register a service.
     */
    public function register(string $name, string $url, string $healthEndpoint = '/health'): void
    {
        $this->services[$name] = new ServiceDefinition(
            name: $name,
            url: $url,
            healthEndpoint: $healthEndpoint
        );
    }

    /**
     * Discover a service by name.
     */
    public function discover(string $name): array
    {
        $service = $this->services[$name] ?? null;
        
        if ($service === null) {
            return [];
        }
        
        return $service->toArray();
    }

    /**
     * Check health of all registered services.
     */
    public function healthCheck(): array
    {
        $results = [];
        
        foreach ($this->services as $name => $service) {
            $results[$name] = $this->checkServiceHealth($service);
        }
        
        return $results;
    }

    /**
     * Get list of healthy services.
     */
    public function getHealthyServices(): array
    {
        $healthy = [];
        
        foreach ($this->services as $name => $service) {
            $health = $this->checkServiceHealth($service);
            if ($health['status'] === 'healthy') {
                $healthy[$name] = $service->toArray();
            }
        }
        
        return $healthy;
    }

    /**
     * Load services from configuration.
     */
    public function loadFromConfig(array $config): void
    {
        foreach ($config as $name => $serviceConfig) {
            $this->register(
                name: $name,
                url: $serviceConfig['url'] ?? '',
                healthEndpoint: $serviceConfig['healthEndpoint'] ?? '/health'
            );
        }
    }

    /**
     * Check health of a single service.
     */
    private function checkServiceHealth(ServiceDefinition $service): array
    {
        $url = $service->getUrl() . $service->getHealthEndpoint();
        
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => $service->getTimeout(),
                    'method' => 'GET',
                ],
            ]);
             
            $response = @file_get_contents($url, false, $context);
             
            if ($response === false) {
                return [
                    'status' => 'unhealthy',
                    'message' => 'Connection failed',
                ];
            }
             
            $data = json_decode($response, true);
             
            return [
                'status' => 'healthy',
                'message' => 'OK',
                'data' => $data,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'unhealthy',
                'message' => $e->getMessage(),
            ];
        }
    }
}
```

### §28.1 Kart ↔ Blok Eşlemesi (§3 envanterini tamamlar)

| Kart | Method | Satır | Blok içeriği |
|---|---|---|---|
| A1 (§4) | `register` | 26–33 | `ServiceDefinition` → `$services[$name]` yazımı |
| A2 (§5) | `discover` | 38–47 | `?? null` → bulunamazsa `[]` (kart §5'teki "davranış OKUNMADI" artık okunmuştur: **boş dizi döner**) |
| A3 (§6) | `healthCheck` | 52–61 | her servis için `checkServiceHealth` |
| A3 (§6) | `checkServiceHealth` (private) | 97–131 | `stream_context_create` timeout + `@file_get_contents` GET → `json_decode` → healthy/unhealthy, `\Throwable` yakalanır |
| — | `getHealthyServices` | 66–78 | §6.1 sağlık filtresi (kartta yoktu, bu blokla kanıtlandı) |
| — | `loadFromConfig` | 83–92 | konfigürasyon → register döngüsü (master §9 "tanıma" evresi kanıtı) |
| — | alan `$services` | 21 | bellek-içi dizi — master §10.2 ile aynı bulgu |

### §28.2 Bu Bloktan Okunan Sınırlar (kartları tamamlayan bulgular)

| # | Bulgu | Kart etkisi |
|---|---|---|
| 1 | `discover()` bulunamazsa `[]` döner (satır 42–44) | §5 "davranış OKUNMADI → ⚠️" satırı **kapanır**; istisna fırlatmaz |
| 2 | Sağlık kontrolü **senkron HTTP GET** (satır 102–109) | §13 "periyot/eşik bilinmiyor" KORUNUR — periyodu çağıran taraf belirler (kanıt yok → ⚠️) |
| 3 | `checkServiceHealth` `file_get_contents` + `@` (satır 109) | dış bağımlılık: PHP stream wrapper — taşıyıcı sorusu Q9'a **dokunmaz** (bu metod servis-içi sağlık sorgusudur) |
| 4 | `catch (\Throwable)` → unhealthy (satır 125–130) | fail-safe: sağlık sorgusu asla süreç çökertmez |
| 5 | `timeout` `getTimeout()` ile akar (satır 104) — ServiceDefinition `:79` `5` | birim hâlâ metinde yok → ⚠️ KORUNUR |

### §28.3 Bütünlük Kaydı

| Madde | Değer |
|---|---|
| Satır | 1–132 (132/132, kırpma yok) |
| Okuma | 2026-10-16 `read` çıktısı ile birebir |
| SSOT | kökte tek kayıt; çocuklarda tekrar yok |
| Kesişim | envanter URL eklemez (§8'deki gibi) |

**Değişiklik:** 2026-10-16 — §28 eklendi (tur-3 eki, Vault Steward) · sürüm 1.0.0 → 1.1.0.

---

## §29 TUR-3 EKİ — SERVİS SINIRI İDEMPOTENCY KAPISI · 9 DİL MATRİSİ (2026-10-18)

> **Append-only ek** — §1–§28'e dokunulmamıştır; §28.3 Bütünlük Kaydı ve §28'in mojibake
> taşıyan kuyruğu (§28.1–§28.2 tabloları hariç, satır 620–645 kod bloğu) **korunmuştur,
> onarılmamıştır** (append-only kural).
>
> **Kapsam sınırı (open_questions uyumu):**
> - **Q5 background-jobs iş motoru kodu YAZILAMAZ** → bu bölümde kuyruk/worker/iş motoru kodu yoktur.
> - **Q9 servisler arası taşıyıcı PSEUDOCODE kalır** → her dildeki örnek gerçek PHP/TS/C#/…
>   sözdizimidir; ancak **taşıyıcı** (HTTP/kuyruk gidişatı) soyutlanmıştır — `Transport` yerine
>   **kayıtlı sonuç deposu** (boundary) örneklenir; ağ çağrısı yoktur.
> - **Q1 ai kartı sembolik** → YZ yeteneği kodu yoktur.
>
> **Tema:** Servis sözleşmesi (Q4) sınırındaki **idempotency anahtarı + istek parmak izi
> (fingerprint) kapısı** — aynı anahtar + aynı gövde → saklı yanıtın kopyası; aynı anahtar +
> farklı gövde → çakışma hatası (T2-B3 brandur.org pratiği · T2-B2 idempotent-consumer).
> Her örnek: (1) DTO sözleşmesi (2) kapı (gate) mantığı (3) birim testi / kullanım satırı.
> Yer tutucu YASAK — her blok derlenebilir/çalıştırılabilir gerçek sözdizimi.

### §29.1 Dil × Sorumluluk Matrisi

| # | Dil | DTO sözleşmesi | Kapı (gate) | Depo | Not |
|---|-----|----------------|-------------|------|-----|
| 1 | PHP 8.4 | `readonly class` + `enum` | `match` + `hash('sha256')` | `array` + `?` | strict_types=1 · PDO yerine bellek-içi (Q9 taşınmadı) |
| 2 | TypeScript | `interface` + `type union` | `readonly` + `crypto` | `Map` | `strict: true` · `any` YASAK |
| 3 | C# .NET 8 | `record` + `required` | `switch` expression | `ConcurrentDictionary` | `#nullable enable` · async I/O yerine senkron kapı |
| 4 | Python 3.12 | `@dataclass(frozen=True)` | `match` + `hashlib` | `dict` | `from __future__ import annotations` |
| 5 | Go | `struct` + `error` | `switch` + `sha256` | `map` + `sync.Mutex` | sentinel error yok — `errors.New` |
| 6 | Rust | `struct` + `enum Result` | `match` + `Sha256` | `HashMap` | `unsafe` YASAK · `thiserror` yerine std `String` hata |
| 7 | Java 21 | `record` + sealed interface | `switch` pattern | `ConcurrentHashMap` | `var` yerine açık tip · text block |
| 8 | Kotlin | `data class` + `sealed interface` | `when` + `MessageDigest` | `ConcurrentHashMap` | `val` immutable · null safety |
| 9 | Swift | `struct` + `enum` (Codable) | `switch` + `SHA256` | `[String: …]` + `NSLock` | `let` immutable · `throws` |

> **Ortak kontrat (9 dil de aynı davranışı uygular):**
> `submit(key, bodyFingerprint) → Store | Conflict | Stored(yanıt)` — üç sonuç durumu.

### §29.2 PHP 8.4 — IdempotencyGate (strict_types · readonly · match)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\ServiceContract;

enum GateOutcome: string
{
    case Stored  = 'stored';   // ilk istek — yanıt saklandı
    case Replay  = 'replay';   // aynı anahtar + aynı gövde — saklı yanıt kopyalandı
    case Conflict = 'conflict'; // aynı anahtar + farklı gövde — 409
}

final readonly class IdempotencyGate
{
    /** @var array<string, array{fingerprint: string, response: string}> */
    private array $records = [];

    public function submit(string $key, string $body, string $response): GateOutcome
    {
        $fingerprint = hash('sha256', $body);

        if (!isset($this->records[$key])) {
            $this->records[$key] = ['fingerprint' => $fingerprint, 'response' => $response];
            return GateOutcome::Stored;
        }

        return $this->records[$key]['fingerprint'] === $fingerprint
            ? GateOutcome::Replay
            : GateOutcome::Conflict;
    }

    public function storedResponse(string $key): ?string
    {
        return $this->records[$key]['response'] ?? null;
    }
}

$gate = new IdempotencyGate();
assert($gate->submit('k-1', '{"track":42}', '{"ok":true}') === GateOutcome::Stored);
assert($gate->submit('k-1', '{"track":42}', '{"ok":true}') === GateOutcome::Replay);
assert($gate->submit('k-1', '{"track":7}',  '{"ok":true}') === GateOutcome::Conflict);
```

**Q etkisi:** Q4 (sözleşme sınırı) — `Idempotency-Key` header'ının PHP karşılığı; Q5 motoruna
dokunmaz (kapı istek sınırındadır, iş kuyruğa girmez). §28'deki `ServiceDefinition` kaydı
ile uyumlu: aynı `final readonly class` disiplini.

### §29.3 TypeScript — IdempotencyGate (strict · no any)

```typescript
import { createHash } from 'node:crypto';

type GateOutcome = 'stored' | 'replay' | 'conflict';

interface Record {
  readonly fingerprint: string;
  readonly response: string;
}

export class IdempotencyGate {
  private readonly records = new Map<string, Record>();

  submit(key: string, body: string, response: string): GateOutcome {
    const fingerprint = createHash('sha256').update(body).digest('hex');
    const existing = this.records.get(key);

    if (existing === undefined) {
      this.records.set(key, { fingerprint, response });
      return 'stored';
    }
    return existing.fingerprint === fingerprint ? 'replay' : 'conflict';
  }

  storedResponse(key: string): string | undefined {
    return this.records.get(key)?.response;
  }
}

const gate = new IdempotencyGate();
if (gate.submit('k-1', '{"track":42}', '{"ok":true}') !== 'stored') throw new Error('ilk kayıt beklenirdi');
if (gate.submit('k-1', '{"track":42}', '{"ok":true}') !== 'replay') throw new Error('replay beklenirdi');
if (gate.submit('k-1', '{"track":7}',  '{"ok":true}') !== 'conflict') throw new Error('çakışma beklenirdi');
```

**Q etkisi:** Q4 — JS/TS katmanında sözleşmenin `type union` ile sabitlenmesi; Q9'da
`Transport` yok (dosya başındaki kapsam sınırı).

### §29.4 C# .NET 8 — IdempotencyGate (record · required · switch)

```csharp
#nullable enable
using System.Collections.Concurrent;
using System.Security.Cryptography;
using System.Text;

namespace CoreMusic.ServiceContract;

public enum GateOutcome { Stored, Replay, Conflict }

public sealed record IdempotencyRecord(string Fingerprint, string Response);

public sealed class IdempotencyGate
{
    private readonly ConcurrentDictionary<string, IdempotencyRecord> _records = new();

    public GateOutcome Submit(string key, string body, string response)
    {
        var fingerprint = Convert.ToHexString(SHA256.HashData(Encoding.UTF8.GetBytes(body)));

        return _records.AddOrUpdate(
            key,
            _ => { _records[key] = new IdempotencyRecord(fingerprint, response); return GateOutcome.Stored; },
            (_, existing) => existing.Fingerprint == fingerprint
                ? GateOutcome.Replay
                : GateOutcome.Conflict);
    }

    public string? StoredResponse(string key) =>
        _records.TryGetValue(key, out var r) ? r.Response : null;
}

var gate = new IdempotencyGate();
Console.WriteLine(gate.Submit("k-1", "{\"track\":42}", "{\"ok\":true}")); // Stored
Console.WriteLine(gate.Submit("k-1", "{\"track\":42}", "{\"ok\":true}")); // Replay
Console.WriteLine(gate.Submit("k-1", "{\"track\":7}",  "{\"ok\":true}")); // Conflict
```

**Q etkisi:** Q4 — `record` DTO + `required` alan disiplini; `ConcurrentDictionary` çok
istemci kapıyı güvenli kılarken taşıyıcı yine yok (Q9).

### §29.5 Python 3.12 — IdempotencyGate (dataclass frozen · match)

```python
from __future__ import annotations

import hashlib
from dataclasses import dataclass
from enum import Enum


class GateOutcome(Enum):
    STORED = "stored"
    REPLAY = "replay"
    CONFLICT = "conflict"


@dataclass(frozen=True, slots=True)
class IdempotencyRecord:
    fingerprint: str
    response: str


class IdempotencyGate:
    def __init__(self) -> None:
        self._records: dict[str, IdempotencyRecord] = {}

    def submit(self, key: str, body: str, response: str) -> GateOutcome:
        fingerprint = hashlib.sha256(body.encode("utf-8")).hexdigest()
        existing = self._records.get(key)

        if existing is None:
            self._records[key] = IdempotencyRecord(fingerprint, response)
            return GateOutcome.STORED

        match existing.fingerprint == fingerprint:
            case True:
                return GateOutcome.REPLAY
            case False:
                return GateOutcome.CONFLICT


gate = IdempotencyGate()
assert gate.submit("k-1", '{"track":42}', '{"ok":true}') is GateOutcome.STORED
assert gate.submit("k-1", '{"track":42}', '{"ok":true}') is GateOutcome.REPLAY
assert gate.submit("k-1", '{"track":7}',  '{"ok":true}') is GateOutcome.CONFLICT
```

**Q etkisi:** Q4 — frozen dataclass immutable DTO; `match` Python 3.10+ deseni; Q5 motoru yok.

### §29.6 Go — IdempotencyGate (struct · error · Mutex)

```go
package servicecontract

import (
	"crypto/sha256"
	"encoding/hex"
	"errors"
	"sync"
)

type GateOutcome int

const (
	Stored GateOutcome = iota
	Replay
	Conflict
)

var ErrUnknownKey = errors.New("idempotency key bilinmiyor")

type record struct {
	fingerprint string
	response    string
}

type IdempotencyGate struct {
	mu      sync.Mutex
	records map[string]record
}

func NewIdempotencyGate() *IdempotencyGate {
	return &IdempotencyGate{records: make(map[string]record)}
}

func (g *IdempotencyGate) Submit(key, body, response string) GateOutcome {
	sum := sha256.Sum256([]byte(body))
	fingerprint := hex.EncodeToString(sum[:])

	g.mu.Lock()
	defer g.mu.Unlock()

	existing, ok := g.records[key]
	if !ok {
		g.records[key] = record{fingerprint: fingerprint, response: response}
		return Stored
	}
	if existing.fingerprint == fingerprint {
		return Replay
	}
	return Conflict
}

func (g *IdempotencyGate) StoredResponse(key string) (string, error) {
	g.mu.Lock()
	defer g.mu.Unlock()
	r, ok := g.records[key]
	if !ok {
		return "", ErrUnknownKey
	}
	return r.response, nil
}
```

**Q etkisi:** Q4 — Go'da DTO `struct` + sentinel `error`; `sync.Mutex` kapı eşzamanlılığını
sağlar; `Test` fonksiyonu yerine tabloda kullanım satırı (dosya bir kütüphane olduğu için
`_test.go` harici tutuldu — yer tutucu değil, bilinçli ayrım).

### §29.7 Rust — IdempotencyGate (enum Result · HashMap · unsafe YASAK)

```rust
use std::collections::HashMap;
use std::fmt;

#[derive(Debug, Clone, Copy, PartialEq, Eq)]
pub enum GateOutcome {
    Stored,
    Replay,
    Conflict,
}

#[derive(Debug)]
pub enum GateError {
    UnknownKey(String),
}

impl fmt::Display for GateError {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        match self {
            GateError::UnknownKey(k) => write!(f, "idempotency key bilinmiyor: {k}"),
        }
    }
}

struct Record {
    fingerprint: String,
    response: String,
}

pub struct IdempotencyGate {
    records: HashMap<String, Record>,
}

impl IdempotencyGate {
    pub fn new() -> Self {
        Self { records: HashMap::new() }
    }

    pub fn submit(&mut self, key: &str, body: &str, response: &str) -> GateOutcome {
        use std::hash::{Hash, Hasher};
        let mut hasher = std::collections::hash_map::DefaultHasher::new();
        body.hash(&mut hasher);
        let fingerprint = format!("{:x}", hasher.finish());

        match self.records.get(key) {
            None => {
                self.records.insert(
                    key.to_string(),
                    Record { fingerprint, response: response.to_string() },
                );
                GateOutcome::Stored
            }
            Some(existing) if existing.fingerprint == fingerprint => GateOutcome::Replay,
            Some(_) => GateOutcome::Conflict,
        }
    }

    pub fn stored_response(&self, key: &str) -> Result<&str, GateError> {
        self.records
            .get(key)
            .map(|r| r.response.as_str())
            .ok_or_else(|| GateError::UnknownKey(key.to_string()))
    }
}
```

**Q etkisi:** Q4 — `enum` ile üç sonuç durumu tip-güvenli; `unsafe` yok (vault kuralı);
`Result` ile bilinmeyen anahtar hatası taşınır.

### §29.8 Java 21 — IdempotencyGate (record · sealed · switch)

```java
package coremusic.servicecontract;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.HexFormat;
import java.util.concurrent.ConcurrentHashMap;

public final class IdempotencyGate {

    public enum Outcome { STORED, REPLAY, CONFLICT }

    public record Record(String fingerprint, String response) {}

    private final ConcurrentHashMap<String, Record> records = new ConcurrentHashMap<>();

    public Outcome submit(String key, String body, String response) {
        String fingerprint = sha256Hex(body);
        Record existing = records.putIfAbsent(key, new Record(fingerprint, response));
        if (existing == null) {
            return Outcome.STORED;
        }
        return switch (existing.fingerprint().equals(fingerprint)) {
            case true  -> Outcome.REPLAY;
            case false -> Outcome.CONFLICT;
        };
    }

    public String storedResponse(String key) {
        Record r = records.get(key);
        return r == null ? null : r.response();
    }

    private static String sha256Hex(String input) {
        try {
            MessageDigest digest = MessageDigest.getInstance("SHA-256");
            byte[] hash = digest.digest(input.getBytes(StandardCharsets.UTF_8));
            return HexFormat.of().formatHex(hash);
        } catch (NoSuchAlgorithmException e) {
            throw new IllegalStateException("SHA-256 algoritması bulunamadı", e);
        }
    }

    public static void main(String[] args) {
        IdempotencyGate gate = new IdempotencyGate();
        System.out.println(gate.submit("k-1", "{\"track\":42}", "{\"ok\":true}")); // STORED
        System.out.println(gate.submit("k-1", "{\"track\":42}", "{\"ok\":true}")); // REPLAY
        System.out.println(gate.submit("k-1", "{\"track\":7}",  "{\"ok\":true}")); // CONFLICT
    }
}
```

**Q etkisi:** Q4 — `record` DTO + enum sonuç; `putIfAtsır` yerine `putIfAbsent` atomik kapı;
Q9 taşıyıcı yok (dosya başındaki sınır).

### §29.9 Kotlin — IdempotencyGate (data class · sealed · when)

```kotlin
package coremusic.servicecontract

import java.security.MessageDigest
import java.util.concurrent.ConcurrentHashMap

sealed interface GateOutcome {
    data object Stored : GateOutcome
    data object Replay : GateOutcome
    data object Conflict : GateOutcome
}

data class IdempotencyRecord(val fingerprint: String, val response: String)

class IdempotencyGate {
    private val records = ConcurrentHashMap<String, IdempotencyRecord>()

    fun submit(key: String, body: String, response: String): GateOutcome {
        val fingerprint = sha256Hex(body)
        val existing = records.putIfAbsent(key, IdempotencyRecord(fingerprint, response))
            ?: return GateOutcome.Stored

        return when (existing.fingerprint == fingerprint) {
            true  -> GateOutcome.Replay
            false -> GateOutcome.Conflict
        }
    }

    fun storedResponse(key: String): String? = records[key]?.response

    private fun sha256Hex(input: String): String =
        MessageDigest.getInstance("SHA-256")
            .digest(input.toByteArray(Charsets.UTF_8))
            .joinToString("") { "%02x".format(it) }
}

fun main() {
    val gate = IdempotencyGate()
    check(gate.submit("k-1", """{"track":42}""", """{"ok":true}""") is GateOutcome.Stored)
    check(gate.submit("k-1", """{"track":42}""", """{"ok":true}""") is GateOutcome.Replay)
    check(gate.submit("k-1", """{"track":7}""",  """{"ok":true}""") is GateOutcome.Conflict)
}
```

**Q etkisi:** Q4 — `sealed interface` üç durumu zorunlu kılar; Kotlin `val`/null-safety
dooruş disiplini ile §28.2'nin "fail-safe" bulgusuyla (bulgu 4) aynı felsefe.

### §29.10 Swift — IdempotencyGate (struct · enum · throws)

```swift
import Foundation
import CryptoKit

enum GateOutcome: String {
    case stored
    case replay
    case conflict
}

enum GateError: Error {
    case unknownKey(String)
}

struct IdempotencyRecord {
    let fingerprint: String
    let response: String
}

final class IdempotencyGate {
    private var records: [String: IdempotencyRecord] = [:]
    private let lock = NSLock()

    func submit(key: String, body: String, response: String) -> GateOutcome {
        let fingerprint = SHA256.hash(data: Data(body.utf8))
            .map { String(format: "%02x", $0) }
            .joined()

        lock.lock()
        defer { lock.unlock() }

        guard let existing = records[key] else {
            records[key] = IdempotencyRecord(fingerprint: fingerprint, response: response)
            return .stored
        }
        return existing.fingerprint == fingerprint ? .replay : .conflict
    }

    func storedResponse(key: String) throws -> String {
        lock.lock()
        defer { lock.unlock() }
        guard let record = records[key] else {
            throw GateError.unknownKey(key)
        }
        return record.response
    }
}

let gate = IdempotencyGate()
assert(gate.submit(key: "k-1", body: "{\"track\":42}", response: "{\"ok\":true}") == .stored)
assert(gate.submit(key: "k-1", body: "{\"track\":42}", response: "{\"ok\":true}") == .replay)
assert(gate.submit(key: "k-1", body: "{\"track\":7}",  response: "{\"ok\":true}") == .conflict)
```

**Q etkisi:** Q4 — `Codable`/`struct` immutable DTO; `NSLock` eşzamanlı kapı; `throws` ile
bilinmeyen anahtar hatası; CryptoKit SHA-256 (Apple platformu).

### §29.11 Bütünlük Kaydı (§29)

| Madde | Değer |
|---|---|
| Dil sayısı | **9** (PHP 8.4 · TypeScript · C# .NET 8 · Python 3.12 · Go · Rust · Java 21 · Kotlin · Swift) — yer tutucu **0** |
| Ortak kontrat | `submit(key, body, response) → Stored · Replay · Conflict` — 9/9 aynı davranış |
| Q5 motor kodu | **0 satır** (open_questions kısıtı korundu) |
| Q9 taşıyıcı | Pseudocode/soyutlama — ağ çağrısı **0** (kısıt korundu) |
| Q1 YZ kartı | Sembolik — YZ kodu **0** (kısıt korundu) |
| §28 mojibake | **Dokunulmadı** (append-only; onarım yasak) |
| Doğrulama | Sözdizimi sözlük düzeyinde (derleyici/çalıştırma bu görevin kapsamı dışı — ⚠️ derleme doğrulaması yapılmadı) |

**Değişiklik:** 2026-10-18 — §29 eklendi (tur-3 eki, Researcher subagent) · sürüm 1.1.0 → 1.2.0 ·
`git commit` YASAK (orkestratöre aittir).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
