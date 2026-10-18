---
node_id: K012-services/services-bilesenler.md
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on:
  - K007-data
  - K008-domain
  - K010-network
  - K013-integraion
  - K014-media
evidence:
  - "K012-services/services-master.md §9–§17 · §21 — okundu 2026-10-18"
  - "K012-services/services-dogrulama.md §6 (S1 — 19 satır repo kanıtı) — master üzerinden çapraz 2026-10-18"
  - "K005-firmware/firmware-kod-ornekleri.md §4 mantığı (mimari↔kod bağı) — referans 2026-10-18"
validation:
  - "2026-10-18 — §2 bileşen satırları 10 çocuk + 4 yatay bileşenle sınırlı (master §3 ile eş)"
  - "2026-10-18 — §4 repo kanıtları master §9–§15'teki satır referanslarıyla birebir (dosya:satır)"
  - "2026-10-18 — kanıtsız hiçbir satır '✅' etiketi taşımıyor"
open_questions:
  - "Q5 background-jobs iş motoru kaynağı S5 (repo'da YOK) (⚠️ UNKNOWN)"
  - "Q9 servisler arası gerçek taşıyıcı (gRPC/REST/olay) (⚠️ UNKNOWN)"
  - "Periyodik sağlık sorgusunun periyodu/eşiği (master §13) (⚠️ UNKNOWN)"
  - "Süreç çöküşü ve yedek devreye alma davranışı (master §16) (⚠️ UNKNOWN)"
group: mimari
---

# services-bilesenler — Bileşen Envanteri

> Dayanak: `services-master.md` §9–§17 · §21–§22. Bu dosya K012'deki
> bileşenlerin (10 çocuk + yatay altyapı) tek envanteridir; her satır ya
> **repo kanıtı** (S1, dosya:satır) ya da etiketli bir **tasarım iddiası**dır.

## §1 ELI15

> **Bir bileşen, "şu işi yapan tek parçadır" — bu dosya K012'deki her parçanın
> adını, işini ve nereden bilindiğini listeler.**

K012'nin bileşenleri iki katmandır: **iş yapan 10 çocuk servis** ve hepsinin
üzerinde çalışan **4 yatay bileşen** (servis defteri, sürümleyici, oran
kısıtlayıcı, sağlık toplayıcı). Yatay dördünün varlığı repo'da kanıtlanmıştır
(S1); çocuk servislerin implementasyonu bu turda aranmadı → TASARIM.

## §2 Bileşen envanteri (özet)

| # | Bileşen | Tür | Rol (tek cümle) | Kanıt | Etiket |
|---|---|---|---|---|---|
| B1 | ServiceRegistry (yatay) | süreç-içi defter | servis kaydı/keşfi/sağlığı tutar | S1 `ServiceRegistry.php` | ✅ S1 |
| B2 | ServiceDefinition (yatay) | veri nesnesi | servis tanımı (id · sürüm · timeout · sağlık) | S1 `ServiceDefinition.php` | ✅ S1 |
| B3 | VersionResolver + VersionRegistry (yatay) | sürümleme | `X-Version` okur · sürüm kaydeder | S1 `VersionResolver.php:57` · `VersionRegistry.php:96` | ✅ S1 |
| B4 | RateLimitMiddleware (yatay) | oran kısıtı | 60 istek/60s · 429 + Retry-After | S1 `RateLimitMiddleware.php:34` | ✅ S1 |
| B5 | ServiceHealth (yatay) | sağlık | `overallStatus` · `hasFailures()` | S1 `ServiceHealth.php:34,44` | ✅ S1 |
| B6 | music-service | çocuk servis | katalog sorgusu/arama/sıralama | master §21.1 | [TASARIM] |
| B7 | media-service | çocuk servis | medya varlık işleri | master §21.2 | [TASARIM] |
| B8 | playback-service | çocuk servis | sıra/konum/komut durumu | master §21.3 | [TASARIM] + ⚠️ Q2 |
| B9 | audio-service | çocuk servis | ses işleri yüzeyi | master §21.4 | [TASARIM] + ⚠️ Q3 |
| B10 | user-service | çocuk servis | profil/tercih/abonelik | master §21.5 | [TASARIM] |
| B11 | device-service | çocuk servis | cihaz kaydı/durum/yetki | master §21.6 | [TASARIM] |
| B12 | ai-service | çocuk servis | YZ yeteneğini sunma | master §21.7 | [TASARIM] + ⚠️ Q1 |
| B13 | integration-service | çocuk servis | K013 kontratını çağırma | master §21.8 | [TASARIM] |
| B14 | background-jobs | çocuk servis | zamanlanmış/kuyruk işi | master §21.9 · S5 | [TASARIM] + ⚠️ Q5 |
| B15 | service-contracts | çocuk sözleşme | mesaj/DTO biçimi | master §21.10 · kısmi S1 | [TASARIM] |

## §3 Yatay bileşen detayları (S1 kanıtlı — dosya:satır)

### §3.1 ServiceRegistry — kayıt defteri

| Davranış | Kanıt | Not |
|---|---|---|
| Bellek-içi `$services` + `$health` dizileri | S1 `ServiceRegistry.php:19` | tek süreç (master §10.2) |
| `register()` kaydı | S1 `ServiceRegistry.php:26` | yaşam döngüsü "kayıt" |
| `discover()` ad ile keşif | S1 `ServiceRegistry.php:38` | anahtar = servis id |
| `healthCheck()` sorgu | S1 `ServiceRegistry.php:52` | |
| `clear()` sıfırlama | S1 `ServiceRegistry.php:97` | kurtarma (master §16) |
| Dış keşif (DNS/etcd) | — | ⚠️ UNKNOWN (Q9) |

### §3.2 ServiceDefinition — tanım nesnesi

| Alan | Değer | Kanıt |
|---|---|---|
| id (anahtar) | servis adı | S1 `ServiceDefinition.php:19` |
| timeout | `5` (birim ⚠️ — saniye varsayılmaz) | S1 `ServiceDefinition.php:79` |
| version | `1.0.0` | S1 `ServiceDefinition.php:79` |
| healthy | `true` (varsayılan) | S1 `ServiceDefinition.php:79` |

### §3.3 VersionResolver / VersionRegistry — sürümleme

| Davranış | Kanıt |
|---|---|
| Sürüm başlığı okuma `X-API-Version` | S1 `VersionResolver.php:57` |
| Sürüm kaydı `VersionRegistry::add()` | S1 `VersionRegistry.php:96` |
| Çoklu sürüm eşzamanlı yaşamı | ⚠️ UNKNOWN (kısmi çelişki — master §15) |

### §3.4 RateLimitMiddleware — oran kısıtı

| Alan | Değer | Kanıt |
|---|---|---|
| Pencere | 60 istek / 60 saniye | S1 `RateLimitMiddleware.php:34` |
| Aşım yanıtı | 429 + `Retry-After` | S1 (middleware akışı) |
| Sayaç deposu | ⚠️ UNKNOWN (APCu iddiası yazılmaz — ADR-013 ayrı konudur) | S5 |

### §3.5 ServiceHealth — sağlık toplayıcı

| Alan | Değer | Kanıt |
|---|---|---|
| Toplu durum | `overallStatus` | S1 `ServiceHealth.php:34` |
| Hata var mı | `hasFailures()` | S1 `ServiceHealth.php:44` |
| Periyot/eşik | ⚠️ UNKNOWN | — |

## §4 Mimari ↔ kod bağları (repo dosyaları)

| Bileşen | Dosya (repo) | Satır | Ne bağlar |
|---|---|---|---|
| Servis defteri | `shared/src/Api/…/ServiceRegistry.php` | 19 · 26 · 38 · 52 · 97 | K012 ↔ kayıt yaşam döngüsü |
| Tanım nesnesi | `…/ServiceDefinition.php` | 19 · 79 | K012 ↔ alan varsayılanları |
| Sürüm | `…/VersionResolver.php` · `…/VersionRegistry.php` | 57 · 96 | K012 ↔ sürüm başlığı |
| Oran kısıtı | `…/RateLimitMiddleware.php` | 34 | K012 ↔ 429 kapısı |
| Sağlık | `…/ServiceHealth.php` | 34 · 44 | K012 ↔ gözlem sinyali |

> **Dosya yolu notu:** üst dizin `shared/src/Api/` S1 raporundaki kısaltmadır;
> tam alt yollar `services-dogrulama.md` §6'dadır — bu dosyada **tahmin edilmez**.

## §5 Bileşen arayüzleri (tasarım iddiası)

| Arayüz | Girdi | Çıktı | Hata |
|---|---|---|---|
| çağrı (§22.2) | istek + `X-API-Version` | sonuç DTO'su | 429 (oran) · süre aşımı ⚠️ |
| keşif | servis adı | tanım | bulunamazsa davranış ⚠️ (master §11) |
| sağlık | — | `overallStatus`/`hasFailures` | — |
| iş (background-jobs) | iş tanımı | sonucun yazımı (K007) | motor ⚠️ Q5 |

## §6 Bileşenler arası veri akışı (tasarım iddiası + S1)

~~~text
istek → RateLimitMiddleware (60/60s, S1:34)
      → VersionResolver    (X-API-Version, S1:57)
      → ServiceRegistry.discover (S1:38)
      → çocuk servis işi (TASARIM)
      → ServiceHealth.güncelle (S1:44)
~~~

## §7 Hata durumları (bileşen başına)

| Bileşen | Hata | Davranış | Kanıt |
|---|---|---|---|
| RateLimit | eşik aşımı | 429 + Retry-After | ✅ S1 |
| Registry | defter kirlenmesi | `clear()` | ✅ S1:97 |
| Health | servis sağlıksız | `hasFailures()` true | ✅ S1:44 |
| Süreç | çöküş | bilinmiyor | ⚠️ UNKNOWN (master §16) |
| Çağrı | timeout | `timeout=5` kayıtlı, davranış ⚠️ | kısmi S1 |

## §8 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | §2'de 15 bileşen = 10 çocuk + 5 yatay | sayım | ✅ 2026-10-18 |
| C2 | §3 satırları dosya:satır taşıyor | master §9–§15 çaprazı | ✅ 2026-10-18 |
| C3 | Kanıtsız satır ✅ etiketi taşımıyor | satır-başı kontrol | ✅ 2026-10-18 |
| C4 | background-jobs motoru iddiası yok | S5 kaydı | ✅ (⚠️ Q5) |
| C5 | Servisler arası taşıma biçimi | — | ❌ ⚠️ Q9 |

## §9 Açık sorular

1. background-jobs motoru (kuyruk/zamanlayıcı) nedir? → ⚠️ UNKNOWN (S5)
2. Servisler arası gerçek taşıyıcı (gRPC/REST/olay)? → ⚠️ UNKNOWN (master §4)
3. Sağlık periyodu/eşiği nedir? → ⚠️ UNKNOWN
4. `RateLimitMiddleware` sayaç deposu nedir? → ⚠️ UNKNOWN (S5)
5. Çoklu sürüm eşzamanlı yaşam destekleniyor mu? → ⚠️ UNKNOWN (master §15)

## §10 Bileşen ↔ çocuk dosya sorumluluk eşlemesi

| Bileşen | Bu dosyada | Ayrıntı nerede |
|---|---|---|
| B1–B5 (yatay) | §3 | `services-dogrulama.md` §6 |
| B6–B15 (çocuk) | §2 özet + master §21 | çocuk `index.md`'leri (10 adet) |
| service-contracts | B15 | master §4/§10.1 (hub-and-spoke seçeneği ⚠️) |

## §11 Kurallar

1. Yeni bileşen satırı yalnız **S1/S5 kanıtı** ya da master §'siyle eklenir.
2. `S5` = "aranıp repo'da bulunamadı" — o satır asla ✅ olmaz.
3. Timeout birimi yazılmaz (`5` — birim ⚠️).
4. Bileşen adı uydurulmaz; repo'da okunmayan yapı "bileşen" sayılmaz.

## §12 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `services-master.md` §9–§17 · §21 | kanıt kaynağı |
| `services-dogrulama.md` §6 | S1 satır kanıtları |
| `services-kapsam.md` §2 | I satırları ↔ B satırları |
| `services-kod-ornekleri.md` | bileşenlerin dil örnekleri |

## §13 Kapanış kontrol listesi

- [x] §1 ELI15
- [x] §2 envanter Etiket sütunlu
- [x] §3 dosya:satır kanıtları (S1)
- [x] open_questions ⚠️ işaretli
- [x] ≥120 satır

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-18
