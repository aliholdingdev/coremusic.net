---
title: "K012-services — Services Master"
type: architecture
category: k-space-v3
node_id: K012-services
node_type: module
role: master
status: draft
parent_id: K012-services
children:
  - "music-service"
  - "media-service"
  - "playback-service"
  - "audio-service"
  - "user-service"
  - "device-service"
  - "ai-service"
  - "integration-service"
  - "background-jobs"
  - "service-contracts"
depends_on:
  - "K006-yapay-zeka"
  - "K015-AI"
  - "K007-data"
  - "K008-domain"
  - "K013-integraion"
  - "K010-network"
  - "K014-media"
evidence:
  - "v2 §2.12 çocuk listesi (10/10 birebir)"
  - "repo S1 kanıtı: shared/src/Api Registry+Versioning+RateLimit (dogrulama §6)"
validation:
  - "çocuk listesi metin karşılaştırması (2026-10-12)"
  - "registry §5/12 master adı okundu (2026-10-12)"
  - "tur-2 derinleştirme — 19 satır repo kanıtı (2026-10-13)"
open_questions:
  - "Q1 ai-service → K006 vs K016"
  - "Q2 playback-service ↔ K015/playback sahipliği"
  - "Q3 audio-service ↔ K003-audio sınırı"
  - "Q4 service-contracts kapsamı"
  - "Q5 background-jobs iş motoru"
  - "Q6 device-identity sahipliği"
  - "Q7 BC/DB ilişkisi"
  - "Q8 swagger/PSR-7/PSR-15"
  - "Q9 servisler arası gerçek taşıyıcı"
date: 2026-10-12
updated: 2026-10-13
version: 2.0.0
authority: "SSOT: v2 §2.12 + .ai/architecture/index.md §5/12"
related: ["services-bagimliliklar.md", "services-dogrulama.md", "services-riskler.md", "services-ornekler.md", "index.md"]
group: mimari
---


# K012-services — services-master.md

> Bu dosya v2 §2.12'de adı geçen **master** dosyasıdır (ad kaynaktan birebir,
> ADR-097 K2/K3). Yeni rol dosyaları: services-bagimliliklar.md ·
> services-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.12: *"Arka planda iş yapan küçük uygulamalar — 'çalan parça servisi',
'kullanıcı servisi'."*

K012, sistemin **iş yapan sırtıdır**. Arayüz göstermez; istek geldiğinde işi
yapar, sonucu döner, gerekirse zamanlar. Üç iş yapar:

1. **Çalıştır** — çalma, katalog, kullanıcı, cihaz işlerini yürütür (ilk 8 çocuk).
2. **Zamanla** — arka plana atılmış işleri kuyruğa alıp koşar (background-jobs).
3. **Tanımla** — servislerin birbirine ne söylediğini yazar (service-contracts).

Ne yapmaz: arayüz çizmez (K011), kalıcı veri şemasına karar vermez (K007-data),
dış API'yi kendisi protokolle konuşmaz (K013-integraion — integration-service
yalnız oradan gelen kontratı kullanır), ses sinyali işlemez (K003-audio/K004-dsp).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Servis süreçleri: music · media · playback · audio · user · device · ai · integration
- Zamanlanmış iş: background-jobs
- Servisler arası sözleşme: service-contracts

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| Uygulama akışı / arayüz | K011-application | K011 yalnız çağırır |
| Dış API protokolü, adaptör, hata | K013-integraion | integration-service K013'ün kontratını kullanır |
| Ağ taşıması ayrıntısı | K010-network | K012 yalnız kullanır |
| Kitaplık/metadata/akış dosyaları | K014-media | Servis veriyi okur, sahibi değildir |
| İş kuralı / domain modeli | K008-domain | Servis kuralı uygular, üretmez |
| Kalıcılık ve şema | K007-data | ORM/SQL kararı burada verilmez |
| ML modeli / ajan altyapısı | K006-yapay-zeka / K015-AI | ai-service yalnız sunar (hangisi ⚠️) |
| Servislerin dağıtımı/dağıtım hattı | K17-devops | |
| Servis test altyapısı | K18-testing | K012 yalnız testini yazar |

## §3 Kademe-2 çocuk tablosu (v2 §2.12 birebir — 10 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | music-service | Müzik işleri servisi | Katalog sorgusu, arama, sıralama (ayrıntı ⚠️) | draft |
| 2 | media-service | Medya varlığı servisi | Dosya/kaynak varlığına dair işler (ayrıntı ⚠️) | draft |
| 3 | playback-service | Çalma durumu servisi | Sıra, konum, oynatma komutu durumu | draft |
| 4 | audio-service | Ses işleri servisi | K003-audio ile sınır belirsiz ⚠️ | draft |
| 5 | user-service | Kullanıcı servisi | Profil, tercih, abonelik (ayrıntı ⚠️) | draft |
| 6 | device-service | Cihaz servisi | Cihaz kaydı, durum, yetki (K010 ile sınır ⚠️) | draft |
| 7 | ai-service | YZ servisi | K006-yapay-zeka mı K015-AI mı → ⚠️ | draft |
| 8 | integration-service | Dış bağlantı servisi | K013 kontratlarını çağıran servis | draft |
| 9 | background-jobs | Arka plan işleri | Zamanlanmış/kuyruk işleri (motor ⚠️) | draft |
| 10 | service-contracts | Servis sözleşmeleri | Mesaj/DTO biçimi; evrensel spec iddiası §4/2 | draft |

> **Sahiplik notu:** playback-service (K012) ile K015/playback, ai-service ile
> K006/K016 arasında **kapsam çakışması riski** var — §7/1–§7/2 açık sorudur.

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | Servisler arası evrensel sözleşme için hub-and-spoke modeli öneriliyor: tek merkez (hub) evrensel Playlist/Track spec'i tanımlar, her yan (spoke) buna bağlanır (open-playlist-engine yaklaşımı) | ⚠️ VERIFICATION REQUIRED (görev beyanında URL yok) | 2026-10-12 | service-contracts çocuk yapısının tasarımı |
| 2 | Kanonik (canonical) model örneği Protobuf ile kurulabilir (portify benzeri yaklaşım) | ⚠️ VERIFICATION REQUIRED (görev beyanında URL yok) | 2026-10-12 | service-contracts mesaj biçimi seçeneği |

**§4'e girmeyen hiçbir dil, framework, paket veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| Servis çalışma zamanı (dil/çatı) | ⚠️ UNKNOWN |
| Mesaj kuyruğu / zamanlayıcı teknolojisi | ⚠️ UNKNOWN |
| Servisler arası taşıma biçimi (gRPC/REST/olay — hangisi) | ⚠️ UNKNOWN |
| Protobuf mü JSON mu kararı | ⚠️ UNKNOWN (yalnız seçenek var) |

> Her iki §4 iddiası da **seçenek/yaklaşım**tır; CoreMusic kararı **değildir**.
> Karar için ADR gerekir (§7/5).

## §5 Bağımlılık özeti (tam metin: services-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K006-yapay-zeka / K015-AI | ai-service'in arkası | TASARIM İDDİASI + ⚠️ (hangisi) |
| K007-data | Kalıcı veri erişimi | TASARIM İDDİASI |
| K008-domain | İş kuralı | TASARIM İDDİASI |
| K0100-identitiy | Kimlik doğrulama | TASARIM İDDİASI |
| K013-integraion | Dış kontratlar | TASARIM İDDİASI |
| K010-network | Taşıma | TASARIM İDDİASI |
| K014-media | Medya verisi | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Uygulama servis çağrıları | TASARIM İDDİASI |
| K18-testing | Servis testleri | TASARIM İDDİASI (sahiplik ⚠️) |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011-application → K012-services
K012-services
├─→ K008-domain · K007-data          (kural + kalıcılık)
├─→ K0100-identitiy · K006/K015-AI    (kimlik + YZ arkası)
├─→ K013-integraion → K010-network   (dış dünya + taşıma)
└─→ K014-media                       (medya verisi)
K012'ye geri dönüş: yalnız used_by okları (döngü yok)
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.12 ile birebir | metin karşılaştırma (10/10) | ✅ verified |
| 2 | Master dosyası adı registry §5/12 ile birebir | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırları yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt sütunu | ✅ verified (2 satır URL eksik ⚠️) |
| 5 | 5 dosyalık MD seti + 10 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 6 | ai-service'in bağlı olduğu düğüm | — | ❌ belirsiz → ⚠️ (§7/1) |
| 7 | Kademe-3 (bounded-context) kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §7 Açık sorular

1. ai-service → K006-yapay-zeka mı, K015-AI mı? (registry §6 kapsam ayrımı) → ⚠️ VERIFICATION REQUIRED
2. playback-service (K012) ↔ K015/playback kimin? → ⚠️ VERIFICATION REQUIRED
3. audio-service ↔ K003-audio sınırı (servis mi, motor mu)? → ⚠️ VERIFICATION REQUIRED
4. service-contracts evrensel Playlist/Track spec'ini mi taşıyor? → TASARIM İDDİASI
5. hub-and-spoke + Protobuf seçenekleri için ADR var mı? → onay bekliyor
6. background-jobs motoru (kuyruk/zamanlayıcı) nedir? → ⚠️ UNKNOWN
7. device-service ↔ K0100-identitiy/device-identity sahipliği → ⚠️ VERIFICATION REQUIRED
8. K012 ile K015 arasındaki veri yönlendirmesi tek yönlü mü? → ⚠️ VERIFICATION REQUIRED

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.12 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk listesi + zorunlu alanlar |
| Registry | .ai/architecture/index.md §5/12 · §6 · §8 | Master adı + K006/K016 kapsam ayrımı |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| hub-and-spoke Playlist/Track (open-playlist-engine yaklaşımı) | ⚠️ URL yok | 2026-10-12 beyanı |
| Protobuf canonical model (portify benzeri) | ⚠️ URL yok | 2026-10-12 beyanı |

## §9 Servis Tanımı ve Yaşam Döngüsü

| Evre | Ne olur | Sahip | Kanıt |
|---|---|---|---|
| tanıma | servis adı + sürüm kaydı | K012 | S1 `ServiceRegistry.php:26` |
| kayıt | `register()` ile deftere giriş | K012 | S1 `ServiceRegistry.php:26` |
| keşif | `discover()` ad ile bulma | çağıran taraf | S1 `ServiceRegistry.php:38` |
| çalışma | iş yürütme (iş = §1'deki üç iş) | servis | TASARIM İDDİASI |
| sağlık | `healthCheck()` periyodik sorgu | K012 | S1 `ServiceRegistry.php:52` |
| kapanış | `clear()` / süreç sonu | K012 | S1 `ServiceRegistry.php:97` |

> Evre sırası **tasarım iddiasıdır**; registry metodlarının varlığı S1 ile
> kanıtlanmıştır, evrelerin çalışma anı sırası ⚠️ VERIFICATION REQUIRED.

## §10 Topoloji

### §10.1 İddia: hub-and-spoke (seçenek — karar değil)

| Alan | Değer |
|---|---|
| İddia | tek merkez (hub) evrensel Playlist/Track spec'i tanımlar, her yan (spoke) buna bağlanır |
| Kaynak | 2026-10-12 görev beyanı — URL yok → ⚠️ VERIFICATION REQUIRED |
| Statü | **seçenek**, CoreMusic kararı değil (§4 notu) |
| Etkilediği | service-contracts çocuk yapısı |
| Karar | ADR gerekir (§7/5) |

### §10.2 Repo kanıtı: süreç-içi defter topolojisi

| Kanıt | Satır | Ne gösterir |
|---|---|---|
| `ServiceRegistry.php` | 19 | `$services` + `$health` bellek-içi diziler |
| `ServiceRegistry.php` | 38 | tek süreç içinde `discover()` |

Sonuç: repo'daki topoloji **tek süreçli kayıt defteri**; hub-and-spoke için
repo kanıtı YOK (S5). Dağıtık defter iddiası `⚠️ VERIFICATION REQUIRED`.

## §11 Discovery (Keşif)

| Soru | Cevap | Kanıt |
|---|---|---|
| Keşif metodu var mı? | evet — `discover()` | S1 `ServiceRegistry.php:38` |
| Anahtar nedir? | servis adı (id) | S1 `ServiceDefinition.php:19` + kayıt akışı |
| Dış keşif (DNS/etcd) var mı? | bilinmiyor | ⚠️ UNKNOWN (Q9) |
| Çağrıran taraf kim? | K011-application (iddia) | TASARIM İDDİASI §5.2 |
| Keşif başarısızsa ne olur? | metod davranışı okunmadı | ⚠️ VERIFICATION REQUIRED |

## §12 Yük Dağıtım ve Oran Kısıtı

| Alan | Değer | Kanıt |
|---|---|---|
| Rate limit penceresi | 60 istek / 60 saniye | S1 `RateLimitMiddleware.php:34` |
| Aşım yanıtı | 429 + `Retry-After` | S1 (middleware akışı, 2026-10-13) |
| Yük dengeleme (load balancer) | repo'da kanıt yok | S5 → ⚠️ UNKNOWN |
| Çoklu örnek (replica) | bilinmiyor | ⚠️ UNKNOWN |
| Sıra tabanlı kuyruk | bilinmiyor (motor Q5) | ⚠️ UNKNOWN |

Yük dağıtım biçimi bu düğümde **kanıtlanmamıştır**; yalnız istek kısıtının
middleware düzeyinde kanıtı vardır. Dağıtık dengeleme iddiası yazılmaz.

## §13 Health Check (Sağlık Kontrolü)

| Alan | Değer | Kanıt |
|---|---|---|
| Toplu sağlık alanı | `overallStatus` | S1 `ServiceHealth.php:34` |
| Hata varlığı sorgusu | `hasFailures()` | S1 `ServiceHealth.php:44` |
| Registry üzerinden sorgu | `healthCheck()` | S1 `ServiceRegistry.php:52` |
| Servis başına sağlık kaydı | `$health` dizisi | S1 `ServiceRegistry.php:19` |
| Varsayılan sağlık | `healthy=true` | S1 `ServiceDefinition.php:79` |
| Periyot / eşik | bilinmiyor | ⚠️ UNKNOWN |
| Uplink bağımlılık zinciri | bilinmiyor | ⚠️ UNKNOWN |

## §14 Timeout, Retry ve Bağımlılık Kesme

| Alan | Değer | Kanıt |
|---|---|---|
| Varsayılan timeout | `5` (birim bilinmiyor — saniye varsayımı yazılmaz) | S1 `ServiceDefinition.php:79` |
| Timeout birimi | metinde birim yok | ⚠️ VERIFICATION REQUIRED |
| Retry politikası | repo'da kanıt yok | S5 → ⚠️ UNKNOWN |
| Bağımlılık kesme (circuit breaker) | repo'da kanıt yok | S5 → ⚠️ UNKNOWN |
| 503 kullanımı | RFC 6585 kaynağı var (URL 200) | S3 — kullanım kanıtı yok |

## §15 Sürümleme

| Alan | Değer | Kanıt |
|---|---|---|
| Sürüm başlığı | `X-API-Version` | S1 `VersionResolver.php:57` |
| Sürüm kaydı | `VersionRegistry::add()` | S1 `VersionRegistry.php:96` |
| Kayıtlı sürüm örneği | `version='1.0.0'` | S1 `ServiceDefinition.php:79` |
| Çoklu sürüm eşzamanlı | kayıt tek sürüm gösteriyor | ⚠️ UNKNOWN (kısmi çelişki, dogrulama §22) |
| Kırıcı sürüm politikası | vault'ta politika yok | ⚠️ UNKNOWN |
| Bağımlılık kesme + sürüm | ADR gereği | §7/5 açık |

## §16 Hata Kurtarma

| Senaryo | Davranış | Kanıt |
|---|---|---|
| Defter kirlenmesi | `clear()` ile sıfırlama | S1 `ServiceRegistry.php:97` |
| Oran aşımı | 429 + `Retry-After` | S1 (middleware) |
| Sağlık kaybı | `hasFailures()` tespiti | S1 `ServiceHealth.php:44` |
| Süreç çöküşü | bilinmiyor | ⚠️ UNKNOWN |
| Veri kaybı kurtarma | K007'nin alanı | TASARIM İDDİASI (§2.2) |
| Yedek devreye alma | bilinmiyor | ⚠️ UNKNOWN |

Kurtarma mekanizmalarının çoğu bu turda **kanıtlanmadı**; yalnız registry
müdahale metodları (clear/healthCheck) S1 ile doğrulanmıştır.

## §17 Gözlemlenebilirlik

| Alan | Durum | Not |
|---|---|---|
| Sağlık metriği | kısmi S1 | `ServiceHealth` + `healthCheck` |
| İstek sayacı | kısmi S1 | RateLimit sayaçları (middleware içi) |
| Loglama | ⚠️ UNKNOWN | log biçimini bu görevde okumadık |
| Metrik/izleme dış sistemi | ⚠️ UNKNOWN | K17-devops alanı |
| Dağıtık izleme (trace) | ⚠️ UNKNOWN | kanıt yok |
| Uyarı (alert) | ⚠️ UNKNOWN | kanıt yok |

Gözlemlenebilirlik bu düğümde **sağlık sinyalleriyle sınırlı** kanıtlanmıştır;
tam görsellik için tur-3 gerekir.

## §18 Terim Sözlüğü

| Terim | Bu dosyadaki anlamı |
|---|---|
| servis | arayüz çizmeden iş yapan süreç (§1) |
| kayıt defteri | `ServiceRegistry` — bellek-içi servis listesi |
| keşif (discovery) | `discover()` ile servis adıyla bulma |
| sözleşme | servisler arası mesaj biçimi (service-contracts) |
| hub-and-spoke | tek merkezli spec topolojisi (seçenek, §10.1) |
| kanonik model | tüm servislerin ortak veri modeli (Protobuf seçeneği) |
| oran kısıtı | dakikada istek sınırı (60/60s) |
| sağlık | `ServiceHealth` toplu durum alanı |
| timeout | servis başına zaman aşımı (`timeout=5`) |
| sürüm başlığı | `X-API-Version` ile sürüm bildirimi |
| DAG | döngüsüz bağımlılık grafiği (§5.3) |
| TASARIM İDDİASI | onaysız, test edilmemiş iddia |

## §19 Kapsam Dışı Yüzeyler

| Yüzey | Sahibi | Bu dosyada |
|---|---|---|
| Uygulama arayüzü | K011-application | yazılmaz |
| Şema/SQL | K007-data | yazılmaz |
| Dış API protokolü | K013-integraion | yazılmaz |
| Ağ ayrıntısı | K010-network | yalnız bağımlılık satırı |
| Medya dosyası sahipliği | K014-media | yalnız bağımlılık satırı |
| CI/CD/dağıtım | K17-devops | yazılmaz |
| Test altyapısı | K18-testing | yalnız test yazarlığı |
| Kaynak kod (`*.php` vb.) | ilgili agent'lar | salt-okunur |

## §20 Tur-2 Kaydı ve Kapanış

| Madde | Değer |
|---|---|
| Tarih | 2026-10-13 |
| Değişiklik | FM §4.2 (9 alan) + §9–§19 eklendi; §1–§8 korundu |
| Sürüm | 1.0.0 → 2.0.0 |
| Kanıt | 19 satır S1 (services-dogrulama.md §6) |
| Açık soru | Q1–Q9 taşındı (dokunulmadı) |
| Bir sonraki | Q1–Q6 kapanışı → 2.1.0 + tur-3 |

## §21 Servis Katalog Detay Kartları (10 çocuk)

Her kart TASARIM İDDİASI seviyesindedir; ayrıntı `⚠️` ile işaretlidir.

### §21.1 music-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Müzik işleri servisi |
| Kapsam | katalog sorgusu, arama, sıralama (ayrıntı ⚠️) |
| Giden bağımlılık | K007 (veri), K008 (kural), K015 (medya verisi) |
| Açık soru | — (kapsam notu belirsiz ⚠️) |
| Repo kanıtı | bu görevde aranmadı → ⚠️ VERIFICATION REQUIRED |

### §21.2 media-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Medya varlığı servisi |
| Kapsam | dosya/kaynak varlığına dair işler (ayrıntı ⚠️) |
| Giden bağımlılık | K007, K015 |
| Sınır | dosyanın sahibi K015'tir — servis yalnız iş yapar |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED |

### §21.3 playback-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Çalma durumu servisi |
| Kapsam | sıra, konum, oynatma komutu durumu |
| Giden bağımlılık | K007, K008, K015 |
| Açık soru | Q2 — K015/playback ile sahiplik çakışması |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED |

### §21.4 audio-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Ses işleri servisi |
| Kapsam | K003-audio ile sınır belirsiz ⚠️ |
| Giden bağımlılık | K008 (kural), K015 (ses verisi iddiası) |
| Açık soru | Q3 — servis mi motor mu |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED |

### §21.5 user-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Kullanıcı servisi |
| Kapsam | profil, tercih, abonelik (ayrıntı ⚠️) |
| Giden bağımlılık | K007, K008, K010 |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED |

### §21.6 device-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Cihaz servisi |
| Kapsam | cihaz kaydı, durum, yetki |
| Giden bağımlılık | K007, K010 |
| Açık soru | Q6 — device-identity sahipliği (K0100-identitiy ile sınır) |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED |

### §21.7 ai-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | YZ servisi — diğer servislere YZ yeteneği sunar |
| Kapsam | öneri, prompt, araç çağrısı (ayrıntı ⚠️) |
| Giden bağımlılık | K006/K016 (hangisi ⚠️), K014 |
| Açık soru | Q1 — bağlı olduğu domen |
| Repo kanıtı | implementasyon YOK → S5 (dogrulama §7/4) |

### §21.8 integration-service

| Alan | Değer |
|---|---|
| Rol (ELI15) | Dış bağlantı servisi |
| Kapsam | K013 kontratlarını çağıran servis |
| Giden bağımlılık | K013, K014 |
| Kural | dış protokol yalnız adaptörde yaşar (bagimliliklar §8.4) |
| Repo kanıtı | ⚠️ VERIFICATION REQUIRED |

### §21.9 background-jobs

| Alan | Değer |
|---|---|
| Rol (ELI15) | Arka plan işleri |
| Kapsam | zamanlanmış/kuyruk işleri |
| Giden bağımlılık | K007 (sonuç yazımı iddiası) |
| Açık soru | Q5 — iş motoru bilinmiyor |
| Repo kanıtı | motor kaynağı YOK → S5 (dogrulama §7/3) |

### §21.10 service-contracts

| Alan | Değer |
|---|---|
| Rol (ELI15) | Servis sözleşmeleri |
| Kapsam | mesaj/DTO biçimi; evrensel spec iddiası §4/1 |
| Giden bağımlılık | K014 (taşıma) |
| Açık soru | Q4 — kapsam (tüm K'lar mı K012 mi) |
| Repo kanıtı | vaat (DTO) seviyesinde → kısmi S1 |

## §22 Örnek Akışlar (tasarım iddiası — adım adım)

### §22.1 Servis kaydı akışı

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | servis tanımı oluşturulur (`ServiceDefinition`) | S1 `:19` |
| 2 | `register()` çağrılır | S1 `:26` |
| 3 | `$services`/`$health` dizilerine yazılır | S1 `:19` |
| 4 | sürüm kaydedilir (`VersionRegistry::add`) | S1 `:96` |
| 5 | sağlık durumu okunur | S1 `:52` |

### §22.2 Çağrı akışı (iddia)

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | K011 servisi çağırır (biçim ⚠️) | TASARIM İDDİASI |
| 2 | oran kısıtı kontrolü (60/60s) | S1 `:34` |
| 3 | sürüm başlığı okunur (`X-API-Version`) | S1 `:57` |
| 4 | `discover()` ile servis bulunur | S1 `:38` |
| 5 | iş yürür (biçim ⚠️ Q9) | ⚠️ UNKNOWN |
| 6 | sonuç döner | TASARIM İDDİASI |

### §22.3 Hata akışı (iddia)

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | oran aşımı → 429 + `Retry-After` | S1 (middleware) |
| 2 | sağlık kaybı → `hasFailures()` true | S1 `:44` |
| 3 | müdahale → `clear()` / kayıt yenileme | S1 `:97` |
| 4 | süreç çöküşü → davranış bilinmiyor | ⚠️ UNKNOWN |

## §23 Sınır Kararları Özeti

| # | Karar | Dayanak |
|---|---|---|
| 1 | servis kural üretmez | §2.2 + K008 sahipliği |
| 2 | servis şema yazmaz | §2.2 + K007 sahipliği |
| 3 | dış protokol integration-service'te | §2.2 + K013 sahipliği |
| 4 | medya dosyası sahibi K015 | §2.2 + K015 sahipliği |
| 5 | arayüz K011'in işi | §2.2 |
| 6 | taşımada K012 yalnız kullanır | §2.2 + K014 sahipliği |
| 7 | test altyapısı K019, testi K012 yazar | §2.2 |
| 8 | dağıtım K018 | §2.2 |
| 9 | YZ arkası K006/K016 — hangisi ⚠️ | Q1 |
| 10 | imla hatalı adlar değişmez | v2 §3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
