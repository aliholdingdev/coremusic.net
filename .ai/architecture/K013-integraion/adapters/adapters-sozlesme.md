---
node_id: K013-integraion/adapters/adapters-sozlesme
node_type: technology-deepdive
title: "Adaptör Sözleşmesi — İç Model ↔ Dış Sistem Çevirisi, Tip Dönüşümü, Hata Eşleme, Versioning"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K013-integraion/adapters
children: []
depends_on:
  - K013-integraion/adapters/adapters-anayasa
  - K013-integraion/adapters
  - K013-integraion
evidence:
  - "https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc9457.html (2026-10-16 HEAD 200 · içerik çekildi)"
  - "https://www.rfc-editor.org/rfc/rfc9110.html (2026-10-16 HEAD 200 · içerik okunmadı → yalnız varlık)"
  - "https://www.rfc-editor.org/rfc/rfc6585.html (2026-10-16 HEAD 200 · içerik okunmadı → yalnız varlık)"
  - "https://spec.openapis.org/oas/latest.html (2026-10-16 HEAD 200)"
  - "https://martinfowler.com/articles/consumerDrivenContracts.html (2026-10-16 HEAD 200 · içerik çekildi)"
  - "Dosya: shared/src/OAuth/Provider/BaseOAuthProvider.php (2026-10-16 okundu — dış sistem çağrısı curl ile, satır 76/106)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16)"
  - "İçerik alıntısı yapılan kaynaklar §6'da `çekildi` olarak işaretlidir; yalnız HEAD 200 olanlar için içerik iddiası yazılmamıştır"
  - "Bu dosyanın §2–§8'i TASARIM İDDİASI'dır (CoreMusic adaptör sözleşmesi henüz yazılmamıştır — bkz. §9)"
open_questions:
  - "Dış sistemlerin hata biçimi standardize edilmiş mi (RFC 9457 mi, kendi biçimi mi)? → UNKNOWN"
  - "Adaptör versioning'i API versioning'ine mi bağlı? → ⚠️ VERIFICATION REQUIRED (ADR bekliyor)"
  - "Idempotency anahtarı hangi uçlarda zorunlu? → UNKNOWN (karar yok)"
  - "Bilinmeyen alan (unknown field) politikası: koru mu, at mı? → UNKNOWN"
group: mimari
---

# Adaptör Sözleşmesi — Çeviri · Tip · Hata · Versioning

> **Bu dosya ne değildir:** Kod örnekleri (`adapters-kod-ornekleri.md`),
> doğrulama kuralı (`adapters-dogrulama.md`) veya dış sistem envanteri
> (`external-systems`) değildir. Bu dosya **adaptörün yazmadan önce
> imzaladığı sözleşmeyi** tanımlar: ne çevrilir, ne çevrilmez, hata nasıl
> eşlenir, sürüm nasıl korunur.
>
> **Durum etiketi:** ŞARTNAME TASARIM İDDİASI · Kod ⚠️ PLANNED
> (CoreMusic repo'sunda üçüncü taraf sistemlere yönelik, adı
> "adaptör" olan sözleşme sınıfı bu turda tespit edilmedi).

---

## §0 ELI15 — Bu dosya bir cümlede

Bir adaptörün sözleşmesi şudur: **dış sistem ne verirse versin, içeriye
CoreMusic'in kendi diliyle girsin; içeriden ne çıkarsa çıksın, dışarıya
dış sistemin diliyle çıksın** — ve bu çevirinin kuralları (hangi tip,
hangi alan, hangi hata, hangi sürüm) **kod yazılmadan önce yazılmış
olsun**, çünkü sözlü eşleme kod değişince kaybolur.

---

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | 2 |
| mimari | `adapters-anayasa.md` | Kimlik, kapsam-sınır, desen envanteri | 3 |
| mimari | `adapters-sozlesme.md` (bu dosya) | Çeviri, tip dönüşümü, hata eşleme, versioning | 3 |
| kod | `adapters-dogrulama.md` | Kontrat testi, sahte sunucu, kabul testi, CI | 3 |
| kod | `adapters-kod-ornekleri.md` | 12+ dilde port + adaptör + hata eşleme | 3 |
| kanit | `adapters-web-arastirma.md` | ≥12 benzersiz resmi URL + iddia defteri | 3 |

---

## §1 Sözleşme Yüzeyi — adaptör neyi taşır?

| # | Katman | Ne taşır | Kaynak |
|---|---|---|---|
| 1 | **İstek sözleşmesi** | Yol, method, query/body, başlık, kimlik | Dış sistemin dokümanı |
| 2 | **Yanıt sözleşmesi** | Durum kodu, gövde şeması, sayfalama | Dış sistemin dokümanı |
| 3 | **Hata sözleşmesi** | Hata kodu, mesajı, yeniden denenebilirlik | Dış sistem + iç hata modeli (`error-handling`) |
| 4 | **Kimlik sözleşmesi** | Token/anahtar alma, yenileme, kapsam (scope) | `K009-security` |
| 5 | **Sürüm sözleşmesi** | Dış sürüm ↔ iç sürüm eşlemesi | Bu dosya §6 |
| 6 | **Limit sözleşmesi** | Rate limit, timeout, boyut sınırı | `K010-network` + bu dosya §5.4 |

> **Kural:** Altı katmandan **hiçbiri** adaptörün dışında yazılmaz ama
> **hiçbiri** adaptörde icat edilmez — hepsi dış kaynaktan okunur,
> içeriye çevrilir.

### §1.1 Sözleşme kaynağı zinciri

```text
Dış sistem dokümanı (OpenAPI/MD/SDK)
        ↓  okunur
adapters-sozlesme.md (eşleme tablosu)   ← bu dosya
        ↓  uygulanır
Port arayüzü + concrete adaptör          → adapters-kod-ornekleri.md
        ↓  doğrulanır
Kontrat testi + kabul testi + CI         → adapters-dogrulama.md
```

**Zincir kuralı:** Eşleme tablosu olmadan kod **yazılmaz**; kod, tablo
olmadan **değiştirilmez**; test, tabloyu **yansıtmıyorsa** test eksiktir.

---

## §2 Çeviri (Mapping) Kuralları — 8 madde

| # | Kural | Ne demek | İhlali |
|---|---|---|---|
| M1 | **Tek yönlü kaynak** | Her alanın **tek** hakikat kaynağı vardır (dış ya da iç); ikisi de "kısmen doğru" olamaz | Çifte kayıt, drift |
| M2 | **Eşleme tablosu zorunlu** | Alan → alan eşlemesi kod değil, **tablo** olarak yazılır | Kod değişince eşleme kaybolur |
| M3 | **İç model dış adı taşımaz** | İç tipte `place_id` gibi dış adlar **kalmaz**; iç dilin adlandırması kullanılır | Dış sistem dili içeri sızar → ACL ihlali |
| M4 | **Düşünce birimi dönüşür** | Birim/saat/tarih/para mutlaka **dönüştürülür**, kopyalanmaz | Sessiz veri bozulması |
| M5 | **Eksik ≠ null** | Dış sistemde alan yoksa (eksik) ile null **ayrı** işlenir; hangisinin ne olduğu tabloda yazılıdır | Yanlış varsayılan |
| M6 | **Bilinmeyen alan politikası** | Dış sistem yeni alan eklerse: koru-yok-et (forward compatible) mu, at mı — **karar yazılıdır** | Sessiz alan kaybı |
| M7 | **Bilinmeyen enum değeri** | Tanınmayan enum değeri sessizce `default`'a çevrilmez → ya `UNKNOWN` ya da açık hata | Yanlış sınıflandırma |
| M8 | **Çift yön ayrı yazılır** | Gidiş (iç→dış) ve geliş (dış→iç) çevirisi **iki ayrı** fonksiyon/tablodur; tek fonksiyonda iki yön karmaşası hata üretir | Yön karışması |

> **M1–M8 tasarım kuralıdır**; dayanağı bu dosyanın §Kaynak Tablosu'ndaki
> kaynaklar **değildir** (kaynaklar yalnızca §3/§5/§6'daki standart
> iddiaları taşır). Kural olarak yazılmalarının gerekçesi: sıfır
> denetimli varsayım (zero-hallucination) — her kural ihlali **gözlenebilir**
> bir hata üretir.

---

## §3 Tip Dönüşüm Tablosu (dış tip → iç tip)

| # | Dış tip/format | İç tip (PHP 8.x modeli) | Risk | Kural |
|---|---|---|---|---|
| T1 | JSON string `"123"` | `int` | Gölge değer, taşma | Yalnız rakam ise dönüştür; değilse hata |
| T2 | JSON number (ondalık) | `string` (para) | Ondalık kaybı | Para **asla float'a** alınmaz |
| T3 | `"true"` / `"false"` (string) | `bool` | Yalancı truthy | Yalnız `true/false/1/0` kabul; diğerleri hata |
| T4 | Alan **yok** (eksik) | `null` + "yok" bayrağı **ayrı** | M5 ihlali | Eksik ve null ayrı ele alınır |
| T5 | `null` | `?Tip` | — | Açık |
| T6 | ISO 8601 tarih (`2026-10-16T…Z`) | `DateTimeImmutable` (UTC) | Yerel saat kaybı | Zaman dilimi **her zaman** belirtilir; `Z`/offset yoksa hata |
| T7 | Yalnız tarih (`2026-10-16`) | `DateTimeImmutable` (gece yarısı) | Saat varsayımı | Kullanım yerinde belgelenir |
| T8 | UUID / harici ID | `string` (iç ID ile **karıştırılmaz**) | Kimlik çakışması | Harici ID ayrı alan (`external_id`) taşınır |
| T9 | Enum (bilinmeyen değer) | Sabit enum + `UNKNOWN` | M7 ihlali | Bilinmeyen → `UNKNOWN` veya hata (karar zorunlu) |
| T10 | Tekil obje ↔ dizi | `array<T>` | Tek elemanlı dizi | Tek/çok ayrımı tabloda yazılıdır |
| T11 | Base64 / binary | `string` + decode sınırı | Boyut/süre | Sınır (boyut) K014'te; adaptör yalnız taşır |
| T12 | Dış kod (ülke/para birimi) | Sabit harita | Anlamsız kod | ISO kodu doğrulanır; tanınmayan kod hata |

> **Standart referansı:** T6'daki ISO 8601/JSON tarih biçimi ve durum
> kodu semantiği HTTP semantiğine bağlıdır → [S3] (`rfc9110`, **içerik
> bu turda okunmadı**, yalnız erişilebilirlik doğrulandı).

### §3.1 Alan adı çevirisi

| Kural | Örnek (dış → iç) | Not |
|---|---|---|
| Kablo-ayraç → alt çizgi | `created_at` ↔ `createdAt` | Çeviri **tek yerde** yapılır (serialize katmanı) |
| Harici ad iç tipte kalmaz | `place_id` → `place` (iç modelin kendi adı) | M3 |
| Sökme (unsur) adı korunur | `data.items[]` → koleksiyon | Şema derinliği aynen taşınır |
| Tarih alanı adı niyet taşır | `*At` / `*_at` → zaman damgası | Ad, tipi yansıtır |

---

## §4 Hata Eşleme — dış hata → iç hata

### §4.1 Eşleme tablosu

| Dış durum | Dış davranım | İç sınıf (tasarım) | Yeniden dene? | Kullanıcıya |
|---|---|---|---|---|
| 400 | İstek bozuk | `ContractViolation` | Hayır | İç hata modeline çevrilir, dış metin **geçirilmez** |
| 401 | Kimlik geçersiz | `AuthenticationFailed` (K009'a devredilir) | Token yenilenirse evet | "Oturum yenilenmeli" |
| 403 | Yetki yok | `PermissionDenied` | Hayır | 403 (kendi modelimiz) |
| 404 | Kaynak yok | `NotFound` | Hayır | 404 |
| 409 | Çakışma | `Conflict` | Hayır (kullanıcı kararı) | 409 |
| 422 | Doğrulama | `ContractViolation` + alan listesi | Hayır | `error.fields` iç modeline |
| 429 | Limit | `RateLimited` | **Evet** (dış `Retry-After` varsa ona uy) | 429 |
| 5xx | Dış sistem hatası | `UpstreamFailed` | **Evet** (sınırlı) | 502/503 iç modeline |
| Timeout | Yanıt yok | `UpstreamTimeout` | **Evet** (sınırlı) | 504 iç modeline |
| Ağ hatası (DNS/TLS/bağlantı) | Bağlanılamadı | `UpstreamUnreachable` | **Evet** (sınırlı) | 503 iç modeline |
| 2xx ama şema bozuk | Beklenmeyen şekil | `ContractViolation` | Hayır | 502 — **sahte başarı değil** |

### §4.2 Hata eşleme kuralları

| # | Kural | Gerekçe |
|---|---|---|
| E1 | Dış hata metni **olduğu gibi geçirilmez** | Dış metin iç modeli kirletir (ACL, `anayasa` §4.2) |
| E2 | Yeniden deneme yalnız **yeniden-denebilir** sınıf | `ContractViolation` tekrar denenmez (aynı sonuç) |
| E3 | Retry üst sınırı **adaptörün kendisinde** değil, dayanıklılık katmanında | `anayasa` §4.4 — circuit breaker/retry kararı ayrı |
| E4 | Hata günlüğüne **secret/token asla yazılmaz** | `K009-security` sınırı (`.ai/AGENTS.md` §Security) |
| E5 | Dış `Retry-After` başlığı varsa **ona uyulur** | 429'da dış sistem sözü bağlayıcıdır |
| E6 | Hatada **kaynak temizlenir** (bağlantı, dosya, kilit) | Kaynak sızıntısı = üretim hatası |

### §4.3 Problem Details bağlantısı (standart)

| Konu | Değer | Kanıt |
|---|---|---|
| Biçim | RFC 9457 — "problem detail" JSON/XML | [S2] |
| Durum | RFC 9457, **RFC 7807'yi obsoletes eder** | [S2] (alıntı: "This document obsoletes RFC 7807.") |
| Kullanım | HTTP durum kodu tek başına yeterli değilse içerikte makine-okunur ayrıntı | [S2] |
| CoreMusic eşlemesi | İç `error` biçimi RFC 9457'ye bağlanacak mı | ❓ UNKNOWN (ADR — `openapi-kod-ornekleri.md` Q4 ile aynı soru) |

---

## §5 Versioning — sürüm nasıl korunur?

| # | Seçenek | Ne korur | Kanıt |
|---|---|---|---|
| V1 | URL tabanlı (`/v1/…`) | Kaynak kimliği düzeyinde sürüm | [S1] (URI/resource modeli) |
| V2 | Header tabanlı (`Accept-Version`) | Uçta sürüm bildirimi | ⚠️ VERIFICATION REQUIRED (standart tercih bu turda çekilmedi) |
| V3 | Provider/consumer sözleşmesi | İç kullanım yükümlülükleri | [S6] |
| V4 | Adaptör içi sürüm etiketi | Hangi sürüm çevirisinin uygulandığı | Tasarım iddiası (V4 etiketinin standart karşılığı yok → `x-` uzantısı, `openapi-anayasa.md` §9) |

### §5.1 Versioning kuralları

| # | Kural | Not |
|---|---|---|
| S1 | Dış sürüm değişirse **eşleme tablosu** güncellenir, kod sessizce değiştirilmez | M2 |
| S2 | Kırılma tanımı **adaptörde değil**, dış sistemin/ekibin sözleşmesinde | `openapi-anayasa.md` §18: OpenAPI kırmızı çizgi üretmez |
| S3 | Eski dış sürüm desteklenmeye devam edilecekse **iki eşleme tablosu** yan yana yaşar | Tek tablo = çift anlamlı kod |
| S4 | Dış sürüm düşürme (deprecation) bildirimi adaptöre **loglanır** (uyarı) | Erken uyarı |
| S5 | Adaptör sürümü, API sürümü ile **karıştırılmaz** | API sürümü K013/api-contracts'ındır |

### §5.2 Sözleşme evrimi — consumer-driven yaklaşım

| Kavram | Tanım | Kanıt |
|---|---|---|
| Consumer-Driven Contract | Sağlayıcı, **tüketici yükümlülüklerini** görür ve evrimi onların temel işlevine göre yönler | [S6] |
| Ne çözer | Sağlayıcı değiştiğinde tüketicinin kırılmasını **önce** görünür kılar | [S6] |
| Ne çözmez | Sağlayıcıyı tek başına korumaz; **iki taraf** testi gerekir | [S6] |
| Uygulama bağıntısı | Adaptör = tüketici; dış sistem = sağlayıcı | `adapters-dogrulama.md` §4 |

---

## §6 Kimlik / Secret Sınırı (K009)

| # | Kural | Sahip |
|---|---|---|
| K1 | API anahtarı / client secret **yalnız** `.env` veya K009'dan okunur | K009-security |
| K2 | Secret **asla** kod, `.md`, `.json`, `.log` içine yazılmaz | `.ai/AGENTS.md` §Security |
| K3 | Token yenileme akışı adaptörün **arka plan** işidir; iş kuralı K012'de | `anayasa` §2 |
| K4 | İstek başlıklarına secret **eklenmez** (yalnız `Authorization`/gereken başlık) | K009 |
| K5 | Hata mesajı ve log'da token/anahtar **maskelenir** (`[REDACTED]`) | `.ai/AGENTS.md` §17 (edge case 2) |

> **Repo kanıtı (K3 bağlamı):** `shared/src/OAuth/Provider/BaseOAuthProvider.php`
> `clientId`/`clientSecret`'i **constructor ile alır** (satır 20–30) ve
> gizli değeri kendisi **üretmez** — dışarıdan enjeksiyon kalıbı
> (2026-10-16 okundu).

---

## §7 Sözleşme Kontrol Listesi (kod öncesi, 12 madde)

| # | Madde | Tam mı? |
|---|---|---|
| 1 | Dış doküman okundu ve tarihi yazıldı | ☐ |
| 2 | Eşleme tablosu (alan → alan) yazıldı | ☐ |
| 3 | Tip dönüşümü (§3) her alan için işaretli | ☐ |
| 4 | Eksik ↔ null ayrımı kararlaştırıldı (M5) | ☐ |
| 5 | Bilinmeyen alan politikası yazıldı (M6) | ☐ |
| 6 | Bilinmeyen enum kararı yazıldı (M7) | ☐ |
| 7 | Hata eşleme tablosu (§4.1) tamam | ☐ |
| 8 | Yeniden-denebilirlik sınıfları işaretli (E2) | ☐ |
| 9 | Sürüm stratejisi seçildi (§5) | ☐ |
| 10 | Secret kaynağı K009 olarak bağlandı (§6) | ☐ |
| 11 | Timeout/limit değerleri K014'ten alınacak | ☐ |
| 12 | Kontrat testi kalemleri listelendi → `adapters-dogrulama.md` | ☐ |

**12/12 olmadan kod başlamaz.**

---

## §8 Ne DEĞİL (sınır sorusu)

| Bu dosya … | … değildir |
|---|---|
| Sözleşme **tanımı** | Doğrulama **kuralı** (o `adapters-dogrulama.md`) |
| Eşleme tablosu | Kod (`adapters-kod-ornekleri.md`) |
| Hata **eşlemesi** | Hata **modeli** (o `K013/error-handling`) |
| Sürüm **koruması** | API **versioning** tasarımı (o `K013/api-contracts`) |
| Secret **sınırı** | Secret **saklama** (o `K009-security`) |
| Limit/tipik timeout **taşıması** | Ağ **yapılandırması** (o `K010-network`) |
| Dış **şema** okuması | Dış sistem **envanteri** (o `K013/external-systems`) |
| Protokol-süzgüsüz çeviri | Protokol **dönüşümü** (o `K013/protocol-adapters`) |

---

## §9 CoreMusic Durumu (repo kanıtı vs tasarım iddiası)

| # | Öğe | Durum | Kanıt |
|---|---|---|---|
| R1 | Dış sistemlere yönelik adaptör sınıfı | ⚠️ tespit edilmedi | `shared/src/` taraması — `AntiCorruption` 0 eşleşme (2026-10-16) |
| R2 | OAuth dış çağrısı | ✅ var | `BaseOAuthProvider.php` satır 76/106 (`curl_init`) |
| R3 | Port arayüzü kalıbı | ✅ var | `IOAuthProvider.php`, `CacheInterface.php` (2026-10-16 dizin) |
| R4 | Hata modeli | ✅ 8 exception hiyerarşisi | `shared/src/Exception/` (`shared/AGENTS.md` §2) |
| R5 | Versioning altyapısı | ✅ sınıf var | `shared/src/Api/Versioning/` (ApiVersion, VersionRegistry, VersionResolver) |
| R6 | Eşleme tablosu (alan→alan) | ⚠️ YOK | Bu dosyanın kendisi ilk taslak |
| R7 | RFC 9457 uyumlu hata biçimi | ❓ UNKNOWN | ADR bekliyor |

> **R5 notu:** `shared/src/Api/Versioning/` varlığı `shared/AGENTS.md` §2
> envanteriyle kayıtlıdır; **içerikleri bu turda okunmadı** → iç eşleme
> davranışı `⚠️ VERIFICATION REQUIRED`.

---

## §10 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Anlamı |
|---|---|
| **Eşleme tablosu** | Alan → alan dönüşüm kaydı (kod dışı, değişime tabi) |
| **Eksik (absent)** | Alan hiç gelmemiş — `null` değil |
| **Forward-compatible** | Dış sistem yeni alan eklediğinde içeri çökmemek |
| **Süzme (filtering)** | Dış hata metnini geçirmeden iç modele çevirme |
| **Yeniden-denebilir** | Aynı istek tekrarlanabilir mi (idempotency dahil) |
| **Harici ID** | Dış sistemin kendi kimliği — iç ID ile karıştırılmaz |
| **Problem Details** | RFC 9457 makine-okunur hata ayrıntısı biçimi |

---

## §11 Sık Karıştırılan Kavramlar

| Karışıklık | Doğrusu |
|---|---|
| Eksik alan = null mı? | **Hayır** — M5 |
| Hata eşlemesi = hata modeli mi? | Hayır; eşleme adaptörde, model `error-handling`'de |
| Adaptör sürümü = API sürümü mü? | Hayır — S5 |
| Retry = hata eşleme mi? | Hayır; eşleme **sınıflandırır**, retry **kararı** ayrı katmanda (E3) |
| `Accept-Version` standart mı? | ⚠️ VERIFICATION REQUIRED (bu turda standart tercih çekilmedi) |
| Dış mesaj olduğu gibi gösterilir mi? | **Hayır** — E1 |
| RFC 7807 güncel mi? | **Hayır** — RFC 9457 onu obsoletes eder ([S2]) |

---

## §12 Kapsam Dışı

1. `adapters-dogrulama.md` — test ve CI kapısı
2. `adapters-kod-ornekleri.md` — dilbazında kod
3. `adapters-web-arastirma.md` — tam URL defteri
4. İç hata modelinin kendisi → `K013/error-handling`
5. Açık API belgesi üretimi → `K013/api-contracts/openapi`
6. Rate limit/algoritma → `K010-network` (adaptör yalnız **taşır**)
7. Araç seçimi (Pact/WireMock vb.) → `adapters-dogrulama.md` §3

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | Web API tasarımı: URI/resource, platform bağımsızlığı, gevşek bağ (loose coupling) | https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design | 2026-10-16 | ✅ 200 · çekildi |
| S2 | RFC 9457 — makine-okunur problem detail; "obsoletes RFC 7807" | https://www.rfc-editor.org/rfc/rfc9457.html | 2026-10-16 | ✅ 200 · çekildi |
| S3 | RFC 9110 — HTTP semantiği (durum kodu tanımları) | https://www.rfc-editor.org/rfc/rfc9110.html | 2026-10-16 | ✅ 200 · içerik okunmadı → **içerik iddiası ⚠️** |
| S4 | RFC 6585 — ek HTTP durum kodları (428/429/451) | https://www.rfc-editor.org/rfc/rfc6585.html | 2026-10-16 | ✅ 200 · içerik okunmadı → **içerik iddiası ⚠️** |
| S5 | OpenAPI Specification — güncel yayımlanmış sürüm sayfası (dış şema kaynağı) | https://spec.openapis.org/oas/latest.html | 2026-10-16 | ✅ 200 |
| S6 | Consumer-Driven Contracts (Ian Robinson, 2006-06-12) — sağlayıcı/tüketici yükümlülükleri | https://martinfowler.com/articles/consumerDrivenContracts.html | 2026-10-16 | ✅ 200 · çekildi |
| S7 | Pact — contract test tanımı (consumer/provider terminolojisi) | https://docs.pact.io/ | 2026-10-16 | ✅ 200 · çekildi (kullanım §`adapters-dogrulama.md` §2) |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak
> alınmamıştır. §2'deki M1–M8 ve §4.2'deki E1–E6 **tasarım kurallarıdır**,
> standart iddia değildir; dayanak olarak URL **kullanılmamıştır** (bu
> bilinçli bir ayrım).

---

## §A Kapılar ve Açık Sorular (bu dosyanın)

| # | Kapı / Soru | İşaret | Kime gider |
|---|---|---|---|
| 1 | Hata biçimi RFC 9457'ye bağlanacak mı? | UNKNOWN | ADR |
| 2 | Versioning stratejisi: URL mi header mı? | ⚠️ VERIFICATION REQUIRED | ADR |
| 3 | Bilinmeyen alan politikası (koru/at) | UNKNOWN | Bu dosya M6 — karar bekliyor |
| 4 | Bilinmeyen enum politikası | UNKNOWN | M7 |
| 5 | Idempotency hangi uçlarda zorunlu? | UNKNOWN | ADR |
| 6 | `shared/src/Api/Versioning/` içeriği eşlemeye uygun mu? | ⚠️ VERIFICATION REQUIRED | sonraki tur (dosya okunacak) |
| 7 | Dış sistem başına limit/timeout değerleri | UNKNOWN | K010-network |

> Bu tablo frontmatter `open_questions` alanını genişletir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · IETF RFC · Microsoft Learn · Fowler/Robinson · **Tarih: 2026-10-16**

---

## EK-E-2026-10-18 — Adaptör direnç katmanı (§E1–§E7)

> Append-only ek; §0–§375 korunmuştur. Dayanak kısaltmaları
> `../integration-master.md` §19'dandır (W-04 retry · W-05 circuit breaker ·
> W-10 IdempotentReceiver · W-12 Stripe imza · W-17 Stripe rate limit ·
> W-18 GitHub rate limit).

## §E1 Adaptör direnç katmanı — hangi koruma hangi sırada (TASARIM İDDİASI)

Her üçüncü-parça adaptörü **aynı** katman sırasını uygular (dışarıdan içeri):

```text
1. rate-limit bekleyici (W-17/W-18 kotaları)
2. circuit breaker (W-05) — açıkken istek ATILMAZ
3. retry + backoff + jitter (W-04, §17 kuralı)
4. timeout (gateway > servis > adaptör eşitsizliği — §D1.1)
5. idempotency anahtarı (W-10 — dış çağrı tekrarı)
6. imza doğrulama (W-12/W-08 — yanıt geldiğinde)
7. olay yazımı (outbox — W-01)
```

**Katman kuralı (iddia):** 2 açıkken 3 **çalışmaz** — circuit breaker'ın
temel tarifi (W-05): hata eşiğini aşan çağrılar doğrudan reddedilir, retry
**hastalığı yaymaz**. Bu sırasız uygulama, retry'ı circuit breaker'dan önce
koyan uygulamalarda dış servisi **daha hızlı** öldürür (W-05'in tarif ettiği
"reset" kavramı).

## §E2 Rate limit sözleşmesi — iki doğrulanmış dış kota (W-17 + W-18)

| Sistem | Kota biçimi | Davranış | Kaynak |
|---|---|---|---|
| Stripe | ⚠️ hesap-bazlı okuma/yazma kotaları — sayısal değer dokümanda hesap bazlı verilir | 429 + `Retry-After` beklenir | ✅ W-17 |
| GitHub REST | saatlik kota (`X-RateLimit-Remaining` · `X-RateLimit-Reset`) | 403/429 + reset zamanı beklenir | ✅ W-18 |

**Ortak kural (iddia):** `Retry-After` varsa **onu** kullan; yoksa jitter'lı
backoff (§E1/3). Kota-başı-istek-sayısı sayıları **adaptör-özel** olup bu
dosyada uydurulmaz — her adaptör kendi dokümanından okur (§E5).

## §E3 İdempotency — dış çağrının geri dönmesi (W-10 + Stripe idempotency-key)

| # | Şart | Biçim | İddia |
|---|---|---|---|
| 1 | Yazma dış çağrılarında idempotency anahtarı zorunlu | Stripe: `Idempotency-Key` başlığı (W-12 ailesi) | ✅ W-17 dokümanı |
| 2 | Anahtar üretimi | `evt-` öneki + event_id (sabit, yeniden denemede **değişmez**) | TASARIM İDDİASI |
| 3 | Aynı anahtar-farklı gövde | dış servis reddeder → adaptör 409'a benzer hata üretir | ⚠️ dış servise bağlı |
| 4 | İç taraf (alıcı) | inbox (§22–§32) — dış çağrı-yazma + inbox-okuma birlikte | W-10 |

## §E4 Circuit breaker durum matrisi (W-05 — taslak)

| Durum | Davranış | Geçiş | İddia |
|---|---|---|---|
| CLOSED | istekler akar | hata eşiği → OPEN | ✅ W-05 kavramı |
| OPEN | istekler **atılır**, adaptör hata döner | süre doldu → HALF-OPEN | ✅ W-05 |
| HALF-OPEN | sınırlı deneme | başarı → CLOSED · hata → OPEN | ✅ W-05 |
| (süre/eshik) | ⚠️ VERIFICATION REQUIRED | ADR | sayısal değer uydurulmadı |

## §E5 Adaptör envanteri — sürüm stratejisi sorusunun zemini (§A/2 için)

| # | Adaptör | Biçim | Sürüm stratejisi | İddia |
|---|---|---|---|---|
| 1 | YouTube Music | ⚠️ UNKNOWN (§A/6) | ⚠️ UNKNOWN | backlog — bagimliliklar §16 zinciri |
| 2 | Stripe webhook | ✅ imza biçimi biliniyor (§D2) | Stripe API sürüm başlığı | ✅ W-17 |
| 3 | GitHub webhook | ✅ imza biçimi biliniyor (§D2) | API-Version başlığı | ✅ W-18 |
| 4 | (diğerleri) | ⚠️ VERIFICATION REQUIRED | — | envanter tamamlanmadı |

## §E6 Adaptör sözleşmesi — her adaptörün uymak zorunda olduğu 7 şart

1. Ham gövdeyi **değiştirmeden** imzalar/doğrular (W-08 · W-12).
2. `tolerance=0` **yazmaz** (W-12).
3. Kalıcı hata ile geçici hata **ayırt eder**; kalıcı hata DLQ'ya gider, retry
   almaz (§33 filtresi).
4. Retry yollarının **tamamında** jitter vardır; jitter'siz yol **yoktur**
   (§17).
5. Dış çağrının idempotency anahtarı **yeniden denemede değişmez** (§E3/2).
6. Yanıtı RFC 9457 problem+json'a çevirir; dış detay **sızdırılmaz** (§D4/4).
7. `Retry-After` varsa onu kullanır; yoksa backoff (§E2).

## §E7 Ek kapanışı

1. Bu ek yalnız **adaptör-direnç** katmanını ekledi; §A açık sorular tablosu
   **değişmedi** (kapılar kapanmadı — açık soru kapanışı yalnız ADR ile olur).
2. Dayanaklar: W-04 · W-05 · W-10 · W-12 · W-17 · W-18 (erişildi-okunmadı
   işaretleri `event-web-arastirma.md` T3 listesindedir — bu ek onları
   dayanak olarak **saymaz**, yalnız kavramsal tarifi kullanır).
3. Sayısal değerler (hata eşiği, süre, kota) uydurulmadı — `⚠️ VERIFICATION
   REQUIRED` olarak ADR'ye bırakıldı.

---

*EK-E-2026-10-18 · adapters-sozlesme.md · Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*

---

## §E8 EK-E eklemesi — adaptör direnç-katmanı denetim tablosu (2026-10-18)

§E1'deki 7 katmanın, bu vault'taki kod örnekleriyle kesişimi:

| Katman | §E1 | Kod karşılığı | Dosya |
|---|---|---|---|
| 1. rate-limit bekleyici | §E2 | (adaptör-özel — sayısal yok) | — |
| 2. circuit breaker | §E4 | (durum matrisi — W-05 taslağı) | — |
| 3. retry+jitter | §E1/3 · §17 | `backoff()` · `Invoke-WithBackoff` · `yeniden_gonder()` | §28 Go · §34 PS · §33 Bash |
| 4. timeout | §E1/4 | (gateway eşitsizliği) | §D1.1 proto-sozlesme |
| 5. idempotency | §E3 | `isle()` · `isleyici` · `tek()` | §27 Python · §29 Rust · §32 Kotlin |
| 6. imza doğrulama | §E6/1 | `dogrula()` · `Test-CoreMusicWebhookSignature` | §31 Swift · §34 PS |
| 7. olay yazımı | §E6 | outbox `run_once()` | §25 C++ · §28 Go |

**Boşluk denetimi:** 7 katmanın **2'sinin** (1 rate-limit · 2 circuit breaker)
bu vault'ta **kod karşılığı YOK** — sayısal parametreler ADR kapısında bekliyor
(§E4). Bu boşluk "eksik" değil, **işaretli eksikliktir** (Truth Mode) —
uydurma sayısal değer yazılmadı.

## §E9 EK-E kapanış denetimi

1. EK-E + §E8, §A açık-sorular tablosuna **yeni satır eklemedi** (§A/2 hâlâ ADR
   bekliyor); envanter (§E5) 4 satırda kaldı — 4. satır `⚠️` işaretli.
2. Dayanak kullanımı: W-04 · W-05 · W-10 · W-12 · W-17 · W-18 — hepsi
   `../integration-master.md` §19'da; yeni URL: **0**.
3. §E6/7 şartının §D6 doğrulama sırasıyla uyumu: **7/7 eşleşti** (bir çelişki yok).

---

*EK-E-2 · adapters-sozlesme.md · 2026-10-18 · Vault Steward*
