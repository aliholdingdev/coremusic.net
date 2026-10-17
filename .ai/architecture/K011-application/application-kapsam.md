---
node_id: K011-application/application-kapsam
node_type: file
status: verify-required
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K008-domain
  - K012-services
  - K013-integraion
  - K009-security
  - K0100-identitiy
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.11 (K011) · §2.8 (K008) · §2.12 (K012) · §2.13 (K013)"
  - "coremusic-katmanli-mimari-agaci-v2.md §3 KURAL 1 (imla/numara korunumu)"
  - ".ai/architecture/index.md §6 (çakışma kayıtları) — kapsam dışı çakışmalar"
validation:
  - "2026-10-12 — sınır satırları v2 §2.8/§2.11/§2.12/§2.13 metinleriyle karşılaştırıldı"
  - "2026-10-12 — K011 dışına çıkan her satıra karşı düğüm atandı (K007/K008/K009/K010/K012/K013/K017/K018/K019)"
open_questions:
  - "K011 'application' (v2) ↔ diskte eski `K010-uygulama` adı — v2 §6 satır 1004 ID CONFLICT kaydı korunur → ⚠️ ID CONFLICT — VERIFY REQUIRED"
  - "K012 'background-jobs' ile K011 'zamanlamali-gorevler' sınırı v2'de tarif edilmemiş → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# application-kapsam — K011 Kapsam Sınırı

## §1 — Kapsam ilkesi

K011 **yalnızca** uygulama akışını sahiplenir: isteğin karşılanması, kullanılabilir
iş birimlerinin (use case · command · query · event · DTO · zamanlanmış görev)
çalıştırılması ve sonucun sunuma geri verilmesi. Aşağıdaki §2–§6 tabloları
**ne DEĞİL** olduğunu kanıtla eşliğinde kilitler.

## §2 — Kapsam İÇİNDE (IN)

| # | Yüzey | K011 içindeki karşılığı | Kanıt (disk) |
|---|---|---|---|
| 1 | Route eşleme ve yönlendirme | `PageRouter`, `RouteRegistry`, `RouteResult` | `shared/src/PageRouter/PageRouter.php` |
| 2 | Middleware zinciri yürütümü | `MiddlewarePipeline` + 11 middleware | `shared/src/Middleware/` |
| 3 | Command/query handler düzeni | Gateway handler yönlendirme | `shared/src/Api/Gateway.php` |
| 4 | Domain event yayını | `EventDispatcher` + 12 event | `shared/src/Events/` |
| 5 | DTO üretimi ve map | `ApiResponse`, `ApiRequest`, BFF zarfı | `shared/src/Api/` |
| 6 | Zamanlanmış görev **tanımı** | repo'da yok → PLANNED | envanter 2026-10-12 |
| 7 | Ödeme use case **akışı** | repo'da yok → PLANNED | envanter 2026-10-12 |
| 8 | Sayfa/API hata zarfı | `ErrorHandler`, `ResponseEmitter` | `shared/src/PageRouter/ErrorHandler.php` |

## §3 — Kapsam DIŞI (OUT) — komşu düğümler

| # | Ne | Neden K011 DEĞİL | Karşı düğüm | Dayanak |
|---|---|---|---|---|
| 1 | Aggregate · entity · value object · invariant | işin kuralları | **K008-domain** | v2 §2.8 |
| 2 | Dış servis adaptörü (ödeme sağlayıcı, e-posta, push) | dış dünya bağlantısı | **K012-services** | v2 §2.12 |
| 3 | API sözleşmesi/adaptör katmanı (protocol adapter) | köprüleştirme | **K013-integraion** | v2 §2.13 |
| 4 | Şifreleme, politika, CSRF/CSP kararı | kapıdaki bekçi | **K009-security** | v2 §2.9 |
| 5 | Kimlik deposu, rol/izin saklama | "kim olduğun" | **K0100-identitiy** | v2 §2.10 |
| 6 | MySQL şema, index, migration, N+1 | veritabanı | **K007-data** | v2 §2.7 |
| 7 | Cron/CI altyapısı, deploy, artefakt | kodun yayılma yolu | **K018-devops** | v2 §2.18 |
| 8 | Log/metrik/trace sahipliği (altyapı) | sistem gözetimi | **K017-observability** | v2 §2.17 |
| 9 | Test **altyapısı** (runner, pipeline) | her katmanın sınandığı yer | **K019-testing** | v2 §2.19 |
| 10 | SPA bileşen ağacı, ITCSS, mockup | arayüz kendisi | **UI (K011/web çocuğu kısıtlı)** | `.ai/AGENTS.md` §5 |
| 11 | Ses motoru, DSP zinciri, ASIO/WASAPI | realtime ses | **K003-audio · K004-dsp** | v2 §2.3/§2.4 |
| 12 | Ajan/orkestrasyon, RAG, model | yapay zekâ çalışma biçimi | **K015-AI** | v2 §2.16 |

## §4 — Sınır matrisi (giriş / çıkış)

| Sınır | Yön | Taşıdığı şey | K011 tutumu | Karşı taraf |
|---|---|---|---|---|
| S1 | K011 → K008 | aggregate çağrısı | yalnız `interface` üzerinden | Domain repository |
| S2 | K008 → K011 | domain event | dinler, yorumlar | `EventDispatcher` |
| S3 | K011 → K012 | event tetikli | senkron çağırmaz | servis adaptörü |
| S4 | K012 → K011 | sonuç event'i | idempotent işler | dinleyici |
| S5 | K011 → K009 | middleware çağrısı | kararı uygular | `CsrfMiddleware` vb. |
| S6 | K011 → K010 | oturum/rol okuma | deposuna dokunmaz | `AuthGuard` |
| S7 | K011 → K007 | repository okuma/yazma | SQL yazmaz | repository impl. |
| S8 | K011 → K013 | dış API çağrısı | adaptör kurmaz | `ApiClient` |
| S9 | K011 → K017 | olay/log gönderimi | şema sahibi değildir | `StructuredLogger` |
| S10 | K011 → K018 | görev zamanı tanımı | çalıştırıcı barındırmaz | scheduler |

## §5 — K011 İÇİNDEKİ ÇOCUK SINIRLARI

| Çocuk | Kapsam içi | Kapsam dışı (komşuya devredilen) |
|---|---|---|
| `usecase-akislari` | akış adımları, guard noktaları, geri dönüş kodları | iş kuralı (K008), dış adaptör (K012) |
| `command-query` | handler imzası, idempotency, yanıt zarfı | persistence (K007), şema |
| `event-akislari` | event şeması, publish sırası, dinleyici sözleşmesi | dinleyicinin yaptığı dış iş (K012) |
| `dto-ve-mapper` | DTO alanları, map kuralı, sürüm uyumu | entity tasarımı (K008), API sürümleme kararı (K013) |
| `odem-servisleri` | ödeme akış adımları, idempotency key, iptal/iade akışı | sağlayıcı bağlantısı, gizli anahtar (K012/K009) |
| `zamanlamali-gorevler` | görev tanımı, pencere, tekrar deneme, SLA | cron/altyapı (K018), kaynak kuyruk (K012) |

## §6 — v2 ile çakışma/örtüşme kayıtları (dokunulmaz — kayıt yalnız)

| # | Kayıt | Tür | Durum |
|---|---|---|---|
| 1 | v2 §2.11 `commands` + `queries` ↔ yeni `command-query` | kapsam örtüşmesi | `⚠️ VERIFICATION REQUIRED` |
| 2 | v2 §2.11 `application-services` ↔ K012 `services` | ad benzerliği | `⚠️ VERIFICATION REQUIRED` |
| 3 | v2 §2.11 `workflows` ↔ `usecase-akislari` | kapsam örtüşmesi | `⚠️ VERIFICATION REQUIRED` |
| 4 | v2 §6 satır 1004: `K011-application` ↔ disk `K010-uygulama` | ID CONFLICT (tarihsel) | `⚠️ ID CONFLICT — VERIFY REQUIRED` |
| 5 | v2 §6 satır 1005: `K012-services` ↔ disk `K012-izleme` | ID CONFLICT (tarihsel) | `⚠️ ID CONFLICT — VERIFY REQUIRED` |

> **KURAL 1:** yukarıdaki adlar/numaralar **değiştirilmez**; yalnız kayıt tutulur.

## §7 — Kapsam karar kapıları

| # | Kapı | Soru | Karar verici | Şu anki durum |
|---|---|---|---|---|
| 1 | Yeni 6 çocuk onayı | `usecase-akislari`… v2'ye eklensin mi? | Bayram Ali | açık → `verify-required` |
| 2 | `commands`/`queries` → `command-query` birleşimi | v2 çocukları silinsin mi? | Bayram Ali | açık → birlikte yaşıyor |
| 3 | Ödeme kapsamı | ödeme use case mi, dış adaptör mü? | Bayram Ali + backend | açık → K011 akış + K012 adaptör önerisi |
| 4 | Zamanlanmış görev | görev tanımı K011'de mi K018'de mi? | Bayram Ali + devops | açık → tanım K011, çalıştırıcı K018 önerisi |

## §8 — Kapsam DIŞI kanıt listesi (ne okunmadı)

| Yüzey | Durum |
|---|---|
| K012-services iç dosyaları | bu üretimde okunmadı (başka ajanın alanı) |
| K008-domain iç dosyaları | bu üretimde okunmadı — `depends_on` yalnız düğüm adı düzeyinde |
| K013-integraion iç dosyaları | bu üretimde okunmadı |
| `.ai/.sql/**` | bu üretimde okunmadı (K007 alanı) |

## §9 — Hızlı soru/cevap (kapsam tartışmaları için)

| # | Soru | Cevap | Dayanak |
|---|---|---|---|
| 1 | Middleware burada mı? | Evet — yürütme K011, politika K009 | §2/2 · §4/S5 |
| 2 | Repository burada mı? | Hayır — impl. K007 | §4/S7 |
| 3 | E-posta gönderimi burada mı? | Hayır — adaptör K012; yalnız tetikleyen olay K011'de | §3/2 |
| 4 | Cron betiği kimin? | Tanım K011, çalıştırıcı K018 | §4/S10 |
| 5 | DTO kimin? | Üretim K011; entity tasarımı K008 | §2/5 |
| 6 | Test dosyaları kimin? | Test altyapısı K019; K011 testleri K011'de | §3/9 |
| 7 | SPA bileşeni kimin? | K011/web çocuğu kısıtlı; ITCSS/mockup UI alanı | §3/10 |
| 8 | Ödemede gizli anahtar? | Asla K011'de — K009/K012 | §5 |
| 9 | Event şeması kimin? | Şema+yield K011; dinleyicinin dış işi K012 | §5 |
| 10 | Log altyapısı? | Gönderim var, şema sahibi K017 | §4/S9 |

## §10 — Kapsam dışı davranış testleri (negatif senaryolar)

| # | Senaryo | Beklenen tutum | Karşı düğüm |
|---|---|---|---|
| 1 | K011 kodunda ham `PDO`/`SELECT` görülürse | ihlal → revert | K007 (`.ai/AGENTS.md` §18/7) |
| 2 | K011'de sağlayıcı API anahtarı yazılırsa | ihlal → revert + log | K009/K012 |
| 3 | K011 kodunda ITCSS/mockup token'ı görülürse | ihlal → UI alanına taşı | UI |
| 4 | K011'de `sleep`/uzun döngü varsa | kuyruğa devret | K012/K018 |
| 5 | K011 doğrudan `mail()`/HTTP sağlayıcı çağırırsa | adaptöre devret | K012 |

> Bu testler **denetim testi**dir; gerçek test dosyaları K019 ile birlikte yazılır.

## §11 — Kapsam karar özeti (karar → dayanak)

| # | Karar | Dayanak |
|---|---|---|
| 1 | Middleware yürütme K011'de kalır | v2 §4.2 A2 alanı; `shared/src/Middleware/` 11 dosya / 758 satır |
| 2 | Repository impl. K007'ye devredilir | v2 §4.2 A0 alanı; K011'de ham SQL yasağı |
| 3 | Ödeme/kuyruk/cron adaptörleri K011 dışı | v2 §4.7 bant kuralı; boşluk `verify-required` |
| 4 | SPA bileşenleri yalnız UI alanı | v2 §3/10; ITCSS token kullanımı K011'e yasak |
| 5 | Kademe-3 teknoloji klasörleri K011 içinde | `.ai/architecture/index.md` §8.1 çoklu-MD kuralı |

## §A — Kapsam (ne kapsar · ne KAPSAMAZ · kabul kriteri · bağımlılıklar)

> Bu bölüm §1–§11'deki özetin **hücre bazlı derinleşmesidir**; aynı kararı tekrar etmez,
> her satıra ölçülebilir kabul kriteri ve disk kanıtı ekler. Ölçüm tarihi: 2026-10-13.

### §A.1 — Kapsam İÇİNDE: yüzey bazlı detay

| # | Yüzey | K011 davranışı | Repo kanıtı (Dosya / Satır) | Kabul kriteri | Bağımlılık |
|---|---|---|---|---|---|
| I-1 | Route çözümü | URI → rota nesnesi, koruma anahtarı okunur | `Dosya: shared/src/PageRouter/RouteRegistry.php / Satır: 35` | eşleşmeyen URI 404 döner | K010 |
| I-2 | Rota kaydı | çalışma zamanında rota kaydı ve dosya yükleme | `Dosya: shared/src/PageRouter/RouteRegistry.php / Satır: 10` | kayıt sonrası `resolve` çağrısı eşleşir | K011/routing |
| I-3 | Koruma anahtarları | korumalı rota anahtarları tek yerden okunur | `Dosya: shared/src/PageRouter/RouteRegistry.php / Satır: 61` | liste AuthGuard'a aynen geçer | K010 |
| I-4 | İstek normalizasyonu | path/query/header/body tek biçime indirgenir | `Dosya: shared/src/PageRouter/PageRouterKernel.php / Satır: 225` | aynı istek iki kez aynı çıktı üretir | — |
| I-5 | Çekirdek handler çağrısı | rota eşleşince çekirdek `callable` çalışır | `Dosya: shared/src/PageRouter/PageRouterKernel.php / Satır: 81` | handler yalnız request array alır | — |
| I-6 | Middleware pipeline | `pipe()` ile sıralı yığın, `run()` ile yürütüm | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 17` | sıra değiştirilirse davranış değişir (kayıtlı) | K009 |
| I-7 | Pipeline çekirdeği | yığının sonundaki çekirdek çağrı | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 23` | çekirdek yalnız 1 kez çalışır | — |
| I-8 | Varsayılan middleware seti | CORS → Origin → SecurityHeaders → RateLimit → CSRF → Validation → Session → Auth → Permission | `Dosya: shared/src/PageRouter/PageRouterKernel.php / Satır: 295` | sıradan sapma yok | K009 |
| I-9 | CSRF denetimi | token doğrulama; başarısızsa reddet | `Dosya: shared/src/Middleware/CsrfMiddleware.php / Satır: 20` | geçersiz token 4xx döner | K009 |
| I-10 | Hız sınırı | IP + proxy çözümlemesiyle sayaç | `Dosya: shared/src/Middleware/RateLimiterMiddleware.php / Satır: 50` | aşımda 429 | K009 |
| I-11 | Güvenilir proxy çözümü | `X-Forwarded-For` yalnız trusted proxy'de okunur | `Dosya: shared/src/Middleware/RateLimiterMiddleware.php / Satır: 124` | sahte IP ile bypass mümkün değil | K009 |
| I-12 | Gövde doğrulama | şema ihlalinde akış durur | `Dosya: shared/src/Middleware/ValidationMiddleware.php / Satır: 17` | hata listesi tek yanıtta döner | K008 |
| I-13 | AuthGuard | rol/izin/red akışı | `Dosya: shared/src/PageRouter/AuthGuard.php / Satır: 15` | izin yoksa fail-closed | K010 |
| I-14 | API gateway | route eşleştirme → handler çağrısı | `Dosya: shared/src/Api/Gateway.php / Satır: 50` | metod izni yoksa 405 | K013 |
| I-15 | Domain event yayımı | olay kaydı ve dinleyiciye ulaşım | `Dosya: shared/src/Events/EventDispatcher.php / Satır: 28` | olay dinleyicisi yoksa sessiz geç | K012 |
| I-16 | Dinleyici sıralama | öncelik numarasına göre sıralama | `Dosya: shared/src/Events/EventDispatcher.php / Satır: 50` | aynı öncelikte kayıt sırası korunur | — |
| I-17 | DTO yanıt zarfı | başarı yanıtı standart zarfta | `Dosya: shared/src/Api/ApiResponse.php / Satır: 26` | alan adları sürümden sürümüne uyumlu | K013 |
| I-18 | Sayfalama DTO'su | sayfa/limit/toplam tek zarfta | `Dosya: shared/src/Api/ApiResponse.php / Satır: 73` | `totalPages` = ceil(total/pageSize) | K007 |
| I-19 | Cache kapısı | adaptör seçimi tek noktadan | `Dosya: shared/src/Cache/CacheManager.php / Satır: 9` | adaptör değişimi davranışı değiştirmez | — |
| I-20 | APCu adaptörü | `set` + TTL yazımı | `Dosya: shared/src/Cache/ApcuAdapter.php / Satır: 17` | TTL 0 = kalıcı (APCu kuralı) | — |
| I-21 | Oturum başlatma | tek statik kapıdan oturum | `Dosya: shared/src/Session/SessionBootstrapper.php / Satır: 19` | ikinci çağrı yarım oturum yaratmaz | K010 |
| I-22 | Yanıt yayımı | status/header/body tek emitter | `Dosya: shared/src/PageRouter/ResponseEmitter.php / Satır: 16` | ikinci header yazımı engelli | — |
| I-23 | Hata sayfası | hata bilgisi + CSRF token'lı sayfa | `Dosya: shared/src/PageRouter/ErrorHandler.php / Satır: 11` | 4xx/5xx tek biçimde döner | — |
| I-24 | Zamanlanmış görev tanımı | görev tanımı + pencere (PLANNED) | repo'da cron/worker kodu YOK | tanım var, çalıştırıcı K018 | K018 |

### §A.2 — Kapsam DIŞI: ne KAPSAMAZ ve ihlal sonucu

| # | Ne KAPSAMAZ | Neden dışarıda | Kodda görülürse | Karşı düğüm |
|---|---|---|---|---|
| O-1 | Aggregate · invariant · value object | iş kuralının kaynağı | `O-1` ihlali → revert | K008-domain |
| O-2 | Ham SQL / `SELECT` / prepared statement | veritabanı sahibi | revert + `.ai/AGENTS.md` §18/7 | K007-data |
| O-3 | Şema · index · migration · BCNF kararı | şema sahibi | revert | K007-data |
| O-4 | Sağlayıcı API anahtarı · şifre · token saklama | sır sahibi | revert + log ERROR | K009-security |
| O-5 | Şifreleme algoritması seçimi (Argon2id vb.) | politika sahibi | revert | K009-security |
| O-6 | Kimlik deposu · rol/izin saklama | kimlik sahibi | revert | K0100-identitiy |
| O-7 | Dış servis adaptörü (HTTP client, sağlayıcı) | dış dünya bağlantısı | adaptöre devret | K013-integraion |
| O-8 | Ödeme sağlayıcı bağlantısı | sağlayıcı sözleşmesi | akış K011, bağlantı K012 | K012-services |
| O-9 | Kuyruk / worker / uzun iş çalıştırıcısı | kaynak yönetimi | kuyruğa devret | K012-services |
| O-10 | Cron/CI altyapısı · deploy adımı | yayım yolu | revert | K018-devops |
| O-11 | Log/metrik/trace altyapısı ve şeması | gözetim sahibi | yalnız gönderim | K017-observability |
| O-12 | Test runner · coverage altyapısı | test altyapısı | yalnız test yazar | K019-testing |
| O-13 | ITCSS katmanı · BEM · token · mockup | arayüz sahibi | UI alanına taşı | UI (ui-design) |
| O-14 | Ses motoru · DSP · ASIO/WASAPI | realtime ses | revert | K003-audio · K004-dsp |
| O-15 | Model · prompt · RAG · ajan orkestrasyonu | yapay zekâ çalışma biçimi | revert | K015-AI |
| O-16 | Ham `$_SERVER`/`$_GET` okuması (normalize dışında) | ortak girdi kapısı | `RequestNormalizer`'a taşı | K011/routing |
| O-17 | `sleep` / uzun senkron döngü | istek ömrü aşımı | kuyruğa devret | K012/K018 |
| O-18 | Doğrudan `mail()` / push / SMS çağrısı | sağlayıcı bağlantısı | event ile devret | K012-services |
| O-19 | Dosya sistemi kalıcılığı (config hariç) | depolama sahibi | repository'ye devret | K007-data |
| O-20 | Birim test harici `echo`/`var_dump` | gizli sızıntı | revert | — |
| O-21 | Global değişken ile durum paylaşma | test edilemezlik | kapsülle | K011 |
| O-22 | `catch` edilmeyen istisnanın kullanıcıya ham çıkması | sızıntı | `ErrorHandler`'a bağla | K011 |
| O-23 | Bellek önbelleğinde parola/PII tutma | veri minimizasyonu | redakt et + K009 | K009/K017 |
| O-24 | Sürüm/alan kırıcı DTO değişikliği | sözleşmenin bütünlüğü | sürümleme + K013 onayı | K013 |

### §A.3 — Kabul kriteri (giriş · süreç · çıkış)

| # | Kriter | Ölçülebilir eşik | Ölçüm | Durum |
|---|---|---|---|---|
| K-1 | Her içerik dosyası ≥500 tam satır | ≥500 (`index.md` hariç) | `[IO.File]::ReadAllLines()` | 2026-10-13 kapalı |
| K-2 | Frontmatter 10 alan eksiksiz | 10/10 | FM ayrıştırma | 2026-10-13 kapalı |
| K-3 | `group` değeri yalnız `mimari|kod|kanit` | 0 geçersiz değer | FM taraması | 2026-10-13 kapalı |
| K-4 | Yeni URL envanterle kesişmez | kesişim = 0 | envanter farkı | 2026-10-13 kapalı |
| K-5 | Web URL'si HTTP 200 döner | 200 | `curl.exe -L` | 2026-10-13 kapalı |
| K-6 | Kod iddiası satır numarası doğrulanır | `(Get-Content)[N-1]` | PowerShell | 2026-10-13 kapalı |
| K-7 | Yasak kalıp yok (doldurma etiketi · eksik gövde · boş yer tutucu) | 0 | regex tarama | 2026-10-13 kapalı |
| K-8 | Mojibake yok | 0 | UTF-8 tarama | 2026-10-13 kapalı |
| K-9 | Yeni URL dışındaki dosyalara URL yazılmaz | 0 | dosya türü kontrolü | 2026-10-13 kapalı |
| K-10 | Alt-modül dosyaları ≥500 | 4/4 | ölçüm | 2026-10-13 kapalı |
| K-11 | `use-case` implementasyon kanıtı | sınıf var mı | `class *UseCase` araması | 0 → açık ⚠️ |
| K-12 | Ödeme akışı kanıtı | kod var mı | envanter | 0 → açık ⚠️ |
| K-13 | Coverage ≥ %80 | %80 | PHPUnit | ⏳ (vendor yok) |
| K-14 | PHPStan level 5 temiz | 0 hata | `shared/composer.json / Satır: 48` | ⏳ |
| K-15 | CI workflow geçiyor | yeşil | `.github/workflows/ci.yml` | ⏳ |
| K-16 | Middleware sırası değişmezliği testi | sıra kaydı | `shared/tests/Middleware/` | ⏳ |
| K-17 | Cache geçersizlik tutarlılığı | anahtar kuralı | `caching/` dosyası | ⏳ |
| K-18 | DTO alan sürüm uyumu | 0 kırılma | `shared/tests/Api/DtoTest.php` | ⏳ |
| K-19 | Negatif kapsam testleri (§10) | 5/5 senaryo | denetim testi | ⏳ |
| K-20 | Kullanıcı onayı ile `status: active` | onay | vault kaydı | ❌ yok |

### §A.4 — Bağımlılıklar (bu üretimde)

| # | Düğüm | Bağ türü | Ne taşınır | K011 tutumu | Kanıt düzeyi |
|---|---|---|---|---|---|
| D-1 | K007-data | `depends_on` | repository impl. · SQL | arayüz üzerinden | ad düzeyi |
| D-2 | K008-domain | `depends_on` | aggregate çağrısı · event şeması | interface üzerinden | ad düzeyi |
| D-3 | K009-security | `depends_on` | CSRF/CSP/rate-limit politikası | kod K011, karar K009 | disk (11 middleware) |
| D-4 | K0100-identitiy | `depends_on` | oturum/rol okuma | deposuna dokunmaz | disk (`AuthGuard`) |
| D-5 | K012-services | `depends_on` | uzak iş devri | senkron çağrı yok | ad düzeyi |
| D-6 | K013-integraion | `depends_on` | dış API sözleşmesi | adaptör K013 | ad düzeyi |
| D-7 | K017-observability | `depends_on` | olay/log gönderimi | şema sahibi değil | disk (`StructuredLogger`) |
| D-8 | K018-devops | `depends_on` | görev çalıştırıcı | yalnız tanım | ad düzeyi |
| D-9 | K019-testing | `depends_on` | test altyapısı | testleri K011 yazar | disk (`shared/tests/`) |

## §B — Bileşen listesi

### §B.1 — K011 bileşen envanteri

| # | Bileşen | Sorumluluk | Repo karşılığı | group | Durum |
|---|---|---|---|---|---|
| B-1 | PageRouterKernel | istek yaşam döngüsü sahibi | `shared/src/PageRouter/PageRouterKernel.php` | routing | IMPLEMENTED |
| B-2 | RouteRegistry | rota kaydı ve çözümü | `shared/src/PageRouter/RouteRegistry.php` | routing | IMPLEMENTED |
| B-3 | RequestNormalizer | girdi normalizasyonu | `shared/src/PageRouter/RequestNormalizer.php` | routing | IMPLEMENTED |
| B-4 | AuthGuard | koruma kapıları | `shared/src/PageRouter/AuthGuard.php` | routing | IMPLEMENTED |
| B-5 | ResponseEmitter | yanıt yayımı | `shared/src/PageRouter/ResponseEmitter.php` | routing | IMPLEMENTED |
| B-6 | ErrorHandler | hata zarfı | `shared/src/PageRouter/ErrorHandler.php` | routing | IMPLEMENTED |
| B-7 | HtmlShellRenderer | HTML kabuk üretimi | `shared/src/PageRouter/HtmlShellRenderer.php` | routing | IMPLEMENTED |
| B-8 | StructuredLogger | yapılandırılmış log | `shared/src/PageRouter/StructuredLogger.php` | routing | IMPLEMENTED |
| B-9 | SessionInitializer | oturum tohumu | `shared/src/PageRouter/SessionInitializer.php` | routing | IMPLEMENTED |
| B-10 | MiddlewarePipeline | sıralı yığın yürütümü | `shared/src/Middleware/MiddlewarePipeline.php` | routing | IMPLEMENTED |
| B-11 | CorsMiddleware | kaynak köprüsü | `shared/src/Middleware/CorsMiddleware.php` | routing | IMPLEMENTED |
| B-12 | OriginCheckMiddleware | kaynak doğrulama | `shared/src/Middleware/OriginCheckMiddleware.php` | routing | IMPLEMENTED |
| B-13 | SecurityHeadersMiddleware | başlık seti | `shared/src/Middleware/SecurityHeadersMiddleware.php` | routing | IMPLEMENTED |
| B-14 | RateLimiterMiddleware | hız sınırı | `shared/src/Middleware/RateLimiterMiddleware.php` | routing | IMPLEMENTED |
| B-15 | CsrfMiddleware | token denetimi | `shared/src/Middleware/CsrfMiddleware.php` | routing | IMPLEMENTED |
| B-16 | ValidationMiddleware | gövde doğrulama | `shared/src/Middleware/ValidationMiddleware.php` | routing | IMPLEMENTED |
| B-17 | SessionManagerMiddleware | oturum yaşam döngüsü | `shared/src/Middleware/SessionManagerMiddleware.php` | routing | IMPLEMENTED |
| B-18 | AuthMiddleware | kimlik denetimi | `shared/src/Middleware/AuthMiddleware.php` | routing | IMPLEMENTED |
| B-19 | BypassAuthMiddleware | kapı bypass'ı (fail-closed) | `shared/src/Middleware/BypassAuthMiddleware.php` | routing | IMPLEMENTED |
| B-20 | PermissionMiddleware | izin denetimi | `shared/src/Middleware/PermissionMiddleware.php` | routing | IMPLEMENTED |
| B-21 | CacheManager | adaptör kapısı | `shared/src/Cache/CacheManager.php` | caching | IMPLEMENTED |
| B-22 | ApcuAdapter | APCu taşıyıcı | `shared/src/Cache/ApcuAdapter.php` | caching | IMPLEMENTED |
| B-23 | MemoryAdapter | test/yerel taşıyıcı | `shared/src/Cache/MemoryAdapter.php` | caching | IMPLEMENTED |
| B-24 | PageCacheAdapter | sayfa önbelleği | `shared/src/Cache/PageCacheAdapter.php` | caching | IMPLEMENTED |
| B-25 | CacheInterface | taşıyıcı sözleşmesi | `shared/src/Cache/CacheInterface.php` | caching | IMPLEMENTED |
| B-26 | PageCacheInterface | sayfa önbelleği sözleşmesi | `shared/src/Cache/PageCacheInterface.php` | caching | IMPLEMENTED |
| B-27 | ApiResponse | yanıt zarfı | `shared/src/Api/ApiResponse.php` | dto | IMPLEMENTED |
| B-28 | ApiRequest | istek zarfı | `shared/src/Api/ApiRequest.php` | dto | IMPLEMENTED |
| B-29 | Request DTO (6) | giriş taşıyıcıları | `shared/src/Api/Dto/Request/` | dto | IMPLEMENTED |
| B-30 | Response DTO (5) | çıkış taşıyıcıları | `shared/src/Api/Dto/Response/` | dto | IMPLEMENTED |
| B-31 | Gateway | API yönlendirici | `shared/src/Api/Gateway.php` | routing | IMPLEMENTED |
| B-32 | EventDispatcher | olay dağıtımı | `shared/src/Events/EventDispatcher.php` | event | IMPLEMENTED |
| B-33 | Domain event (12) | geçmiş zamanlı olaylar | `shared/src/Events/Domain/` | event | IMPLEMENTED |
| B-34 | SessionBootstrapper | oturum kapısı | `shared/src/Session/SessionBootstrapper.php` | oturum | IMPLEMENTED |
| B-35 | Exception hierarchy (8) | hata tipleri | `shared/src/Exception/` | hata | IMPLEMENTED |
| B-36 | ErrorHandler (PageRouter) | hata sayfası | `shared/src/PageRouter/ErrorHandler.php` | hata | IMPLEMENTED |
| B-37 | use-case katmanı | niyet işleyici | `class *UseCase` = 0 dosya | usecase | PLANNED ⚠️ |
| B-38 | Zamanlanmış görev tanımı | periyodik iş tanımı | cron/worker kodu = 0 | usecase | PLANNED ⚠️ |
| B-39 | Ödeme akış bileşeni | ödeme niyeti | ödeme kodu = 0 | usecase | PLANNED ⚠️ |
| B-40 | Repository (1) | API anahtarı deposu | `shared/src/Repository/ApiKeyRepository.php` | veri | kısmen IMPLEMENTED |

### §B.2 — Bileşen ↔ alt-modül eşlemesi

| Alt-modül | Sahip olduğu bileşenler | Sahip olmadığı |
|---|---|---|
| `routing/` | B-1 … B-20 (router + middleware) | cache taşıyıcıları, DTO |
| `caching/` | B-21 … B-26 | TTL politikası sahibi (K011 kararı) |
| `dto/` | B-27 … B-30 | entity tasarımı (K008) |
| `usecase/` | — (henüz yok) | tümü PLANNED |
| `usecase-akislari/` | akış + kapı anlatımı | uygulama kodu |

### §B.3 — Bileşen yoğunluk dağılımı

| group | Bileşen sayısı | Dosya yoğunluğu | Not |
|---|---|---|---|
| routing | 20 | PageRouter 14 PHP / 1843 satır (2026-10-13) | en yoğun |
| caching | 6 | Cache 6 PHP | dengeli |
| dto | 4 + 11 dosya | Api 34 PHP (içinde) | API ile paylaşımlı |
| event | 2 + 12 olay | Events 14 PHP | — |
| usecase | 0 | — | PLANNED |

## §C — Arayüzler

### §C.1 — Çekirdek arayüz/imza kayıtları

| # | Arayüz / imza | Sahip | Görev | Kanıt |
|---|---|---|---|---|
| A-1 | `handle(array $request, callable $core): array` | middleware | isteği geçirir | `Dosya: shared/src/Middleware/CsrfMiddleware.php / Satır: 20` |
| A-2 | `pipe(IMiddleware $middleware): self` | pipeline | yığına ekler | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 17` |
| A-3 | `run(array $request, callable $core): array` | pipeline | yığını yürütür | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 23` |
| A-4 | `resolve(string $uri): ?SpaRoute` | route registry | rota bulur | `Dosya: shared/src/PageRouter/RouteRegistry.php / Satır: 35` |
| A-5 | `getProtectedRouteKeys(): array` | route registry | koruma anahtarı | `Dosya: shared/src/PageRouter/RouteRegistry.php / Satır: 61` |
| A-6 | `check(string $uri, SpaRoute $route, bool $isSpaRequest): ?array` | AuthGuard | kapı sonucu | `Dosya: shared/src/PageRouter/AuthGuard.php / Satır: 15` |
| A-7 | `dispatch(array $request): array` | Gateway | API yönlendirme | `Dosya: shared/src/Api/Gateway.php / Satır: 50` |
| A-8 | `dispatch(object $event): object` | EventDispatcher | olay yayımı | `Dosya: shared/src/Events/EventDispatcher.php / Satır: 28` |
| A-9 | `ok(array $data = [], int $statusCode = 200): array` | ApiResponse | başarı zarfı | `Dosya: shared/src/Api/ApiResponse.php / Satır: 26` |
| A-10 | `paginated(...)` | ApiResponse | sayfalama zarfı | `Dosya: shared/src/Api/ApiResponse.php / Satır: 73` |
| A-11 | `set(string $key, mixed $value, int $ttl = 0): bool` | ApcuAdapter | önbellek yazımı | `Dosya: shared/src/Cache/ApcuAdapter.php / Satır: 17` |
| A-12 | `getAdapter(): CacheInterface` | CacheManager | taşıyıcı seçimi | `Dosya: shared/src/Cache/CacheManager.php / Satır: 9` |
| A-13 | `emit(array $response, ?string $traceId = null, ...): never` | ResponseEmitter | yanıt yayımı | `Dosya: shared/src/PageRouter/ResponseEmitter.php / Satır: 16` |
| A-14 | `buildErrorPage(array $errorInfo, string $csrfToken): string` | ErrorHandler | hata sayfası | `Dosya: shared/src/PageRouter/ErrorHandler.php / Satır: 11` |
| A-15 | `fromArray(array $data): self` | PaginationResponse | DTO kurulumu | `Dosya: shared/src/Api/Dto/Response/PaginationResponse.php / Satır: 70` |
| A-16 | `ensureStarted(): void` | SessionBootstrapper | oturum kapısı | `Dosya: shared/src/Session/SessionBootstrapper.php / Satır: 19` |

### §C.2 — Arayüz sahipliği (yazan / okuyan / değiştiren)

| # | Arayüz | Yazar (karar) | Okuyan | Değişiklik onayı |
|---|---|---|---|---|
| C-1 | middleware `handle` imzası | K011 | tüm middleware | ADR |
| C-2 | `CacheInterface` | K011/caching | cache kullanan her yer | ADR |
| C-3 | `ApiResponse` alanları | K011/dto | SPA · BFF · masaüstü | K013 sürüm onayı |
| C-4 | event sınıfı şeması | K011 + K008 | K012 dinleyicileri | ortak onay |
| C-5 | `SpaRoute` alanı | K011/routing | AuthGuard · renderer | ADR |
| C-6 | hata kodu kümesi | K011 + K017 | UI · API istemcileri | ortak onay |
| C-7 | rota anahtarı biçimi | K011/routing | güvenlik kapıları | K009 onayı |
| C-8 | oturum çerezi adı | K010 | K011 okur | K010 tekel |

### §C.3 — Arayüz sürüm/uyumluluk kuralları

| # | Kural | Sonuç | Kontrol |
|---|---|---|---|
| V-1 | DTO'ya alan ekleme uyumlu | eski istemci çalışmaya devam eder | `shared/tests/Api/DtoTest.php` |
| V-2 | DTO'dan alan silme kırıcı | sürüm zorunlu | K013 onayı |
| V-3 | Middleware sırası değişikliği davranış değiştirir | test + ADR | `shared/tests/Middleware/` |
| V-4 | Event alanı silme kırıcı | dinleyici uyumu zorunlu | `shared/tests/Events/` |
| V-5 | `CacheInterface` imza değişikliği | tüm taşıyıcı | ADR + caching dosyası |
| V-6 | Rota anahtarı değişikliği | koruma listesi güncellenmeli | `RouteRegistry` testi |
| V-7 | PHP sürümü ≥ 8.4 | `shared/composer.json / Satır: 8` | CI (`ci.yml / Satır: 22`) |

## §D — Bağımlılık grafiği

### §D.1 — Dış düğüm grafiği (ASCII)

```text
                    K000–K5000 Architecture Registry
                                 │
                        K011-application (bu dosya)
                                 │
   ┌─────────────┬───────────────┼───────────────┬─────────────┐
   ▼             ▼               ▼               ▼             ▼
K007-data   K008-domain    K009-security   K0100-identitiy  K012-services
 (repo)      (aggregate)    (politika)      (oturum/rol)    (uzak iş)
   ▲             ▲               ▲               ▲             ▲
   │             │               │               │             │
   └─────────────┴─────── K011 handler/middleware/event ───────┘
                                 │
                 ┌───────────────┴───────────────┐
                 ▼                               ▼
        K013-integraion                  K017/K018/K019
        (dış sözleşme)                 (log · cron · test)
```

### §D.2 — Bağımlılık tablosu (yön + koşul)

| # | Kaynak | Hedef | Yön | Taşıma | Koşul |
|---|---|---|---|---|---|
| G-1 | K011 | K007 | → | repository arayüzü | ham SQL yasak |
| G-2 | K011 | K008 | → | aggregate çağrısı | interface üzerinden |
| G-3 | K008 | K011 | ← | domain event | `EventDispatcher` |
| G-4 | K011 | K009 | → | middleware çağrısı | politika K009 |
| G-5 | K011 | K010 | → | oturum/rol okuma | yazma yok |
| G-6 | K011 | K012 | → | event tetikleme | senkron HTTP yok |
| G-7 | K012 | K011 | ← | sonuç event'i | idempotent |
| G-8 | K011 | K013 | → | dış çağrı arayüzü | adaptör K013 |
| G-9 | K011 | K017 | → | olay/log gönderimi | şema K017 |
| G-10 | K011 | K018 | → | görev tanımı | çalıştırıcı K018 |
| G-11 | K019 | K011 | ← | test tetikleyici | coverage kapısı |
| G-12 | K011/routing | K011/caching | → | cache-first okuma | miss repo |
| G-13 | K011/caching | K011/dto | → | DTO yanıtı önbellek | TTL |
| G-14 | K011/dto | K011/usecase | ← | niyet sonucu | PLANNED |
| G-15 | K011/usecase-akislari | K011/* | → | akış şeması | belge katmanı |

### §D.3 — Döngü ve örtüşme denetimi

| # | Şüphe | Denetim | Sonuç |
|---|---|---|---|
| N-1 | K011 ↔ K012 döngüsü | senkron çağrı var mı? | yok → event sınırı |
| N-2 | K011 ↔ K007 döngüsü | K007 K011'e bağımlı mı? | ad düzeyi bağımlılık yok |
| N-3 | `application-services` ↔ K012 ad çakışması | ad benzerliği | kayıt §6/2 → ⚠️ |
| N-4 | `workflows` ↔ `usecase-akislari` örtüşmesi | kapsam ayrımı | kayıt §6/3 → ⚠️ |
| N-5 | cache ↔ repo tutarsızlığı | geçersizlik olayı | `caching/` dosyası sahibi |
| N-6 | event → K012 → event döngüsü | idempotency | açık soru → ⚠️ |
| N-7 | oturum K010 ↔ AuthGuard K011 | okuma/yazma ayrımı | yazma yok |
| N-8 | DTO sürüm K011 ↔ K013 | kırıcı değişiklik | ortak onay |

### §D.4 — Devreye alma sırası ve bağımlılık riski

| # | Adım | Önce gelmesi gereken | Risk | Azaltma |
|---|---|---|---|---|
| R-1 | Route/middleware çekirdeği | — | temel yoksa her şey durur | B-1 … B-10 önce |
| R-2 | Kimlik kapıları (Auth/Permission/Bypass) | R-1 | açık kapı | fail-closed testi |
| R-3 | Cache taşıyıcıları | R-1 | yanlış veri servisi | MemoryAdapter ile başla |
| B-4 | DTO/Response sözleşmesi | R-1 | istemci kırılması | `DtoTest` kapısı |
| B-5 | Event dağıtımı | R-1 | olay kaybı | dinleyicisiz yayını test et |
| B-6 | Repository genişlemesi | R-1 + K007 | N+1 | sorgu bütçesi |
| B-7 | use-case katmanı (PLANNED) | B-1 … B-6 | varsayım hatası | ADR zorunlu (M-19) |
| B-8 | Zamanlanmış görev (PLANNED) | B-7 | çift çalıştırma | idempotency |
| B-9 | Ödeme akışı (PLANNED) | K012 sınırı | çift tahsilat | idempotency key |

### §B.4 — PLANNED bileşen kabul kriterleri (repo'ya girerken)

| # | Bileşen | Giriş kapısı | Kanıt gereği | Kapanış |
|---|---|---|---|---|
| P-1 | `*UseCase` sınıfı | ADR onayı | gerçek dosya + `Dosya:/Satır:` | M-19 kapanır |
| P-2 | Command/Query handler ayrımı | R-2 tamam | iki ayrı route testi | M-02 derinleşir |
| P-3 | Zamanlanmış görev tanımı | K018 ile ortak ADR | tanım dosyası + pencere testi | M-09 kapanır |
| P-4 | Ödeme akışı | K012 sınır ADR'si | idempotency testi | M-10 kapanır |
| P-5 | Redis taşıyıcısı | `CacheInterface` sabit | taşıyıcı testi | caching dosyası |
| P-6 | Outbox/anahtar-kelimeli geçersizlik | olay şeması | geçersizlik testi | cache tutarlılığı |

### §A.5 — Kapsam karar kayıt defteri (2026-10-13 genişletme)

| # | Karar | Dayanak | Sonuç | Durum |
|---|---|---|---|---|
| L-1 | Kapsam IN listesi disk kanıtına bağlandı | §A.1 (24 satır) | kanıtsız satır yok | ✅ |
| L-2 | Kapsam OUT listesi ihlal sonucuyla yazıldı | §A.2 (24 satır) | denetlenebilir | ✅ |
| L-3 | Kabul kriterleri ölçülebilir eşiklere bağlandı | §A.3 (20 satır) | 8 kriter kapalı | kısmen |
| L-4 | Bağımlılıklar ad düzeyi / disk ayrımıyla işaretlendi | §A.4 | doğrulanabilir | ✅ |
| L-5 | Bileşen envanteri 40 satıra çıkarıldı | §B.1 | eksik yok | ✅ |
| L-6 | use-case katmanı kapsam içinde, implementasyon dışı | M-19 | PLANNED | ⚠️ açık |
| L-7 | Ödeme akışı kapsam içinde, bağlantı dışında | M-10 | PLANNED | ⚠️ açık |
| L-8 | Kapsam dışı davranış testleri denetim testi sayıldı | §10 | K019 ile yazılacak | ⏳ |

### §C.4 — Arayüz hata sözleşmeleri

| # | Arayüz | Hata biçimi | Dönüş | Sahip |
|---|---|---|---|---|
| E-1 | middleware | `next` çağrısı kesilir | hata zarfı array | K011 |
| E-2 | handler | Result/exception sınırda | `ErrorHandler` | K011 |
| E-3 | DTO map | alan eksik | `ValidationException` | K011 |
| E-4 | cache | taşıyıcı hatası | miss'e düş (fail-open okuma) | K011/caching |
| E-5 | event | dinleyici hatası | log + akış devam | K011 |
| E-6 | repository | bağlantı hatası | üst sınıfa taşınır | K007 |
| E-7 | dış çağrı | timeout/5xx | retry + devre kesici | K013 |
| E-8 | rate-limit | sayaç aşımı | 429 | K009/K011 |

### §D.5 — Bağımlılık olgunluk ölçümü (2026-10-13)

| # | Bağımlılık | Kod düzeyi | Belge düzeyi | Ölçüm | Karar |
|---|---|---|---|---|---|
| M-1 | K007-data | repository 1 dosya | §4/S7 | `shared/src/Repository/` = 1 PHP | genişletilecek |
| M-2 | K008-domain | `Events/Domain/` 12 olay | §4/S1–S2 | `shared/src/Events/` = 14 PHP | yeterli |
| M-3 | K009-security | 11 middleware / 908 satır | §4/S5 | 2026-10-13 sayımı | yeterli |
| M-4 | K0100-identitiy | AuthGuard + AuthMiddleware | §4/S6 | `AuthGuard.php` 42 metot | yeterli |
| M-5 | K012-services | yalnız event sınırı | §4/S3–S4 | senkron çağrı 0 | doğrulanmış |
| M-6 | K013-integraion | Api/ 34 PHP | §4/S8 | `Gateway.php` | kısmi |
| M-7 | K017-observability | `StructuredLogger` + `Log/` 3 PHP | §4/S9 | `shared/src/Log/` | kısmi |
| M-8 | K018-devops | yok (tanım PLANNED) | §4/S10 | cron kodu 0 | açık ⚠️ |
| M-9 | K019-testing | `shared/tests/` 9 dizin | §3/9 | 22+ test dosyası | kısmi |

### §A.6 — Kapsam sorumluluk matrisi (kim neyi onaylar)

| # | İş | K011 | K007 | K008 | K009 | K012 | K019 |
|---|---|---|---|---|---|---|---|
| X-1 | Rota ekleme | yazar | — | — | okur | — | test |
| X-2 | Middleware ekleme | yazar | — | — | onay | — | test |
| X-3 | DTO alanı ekleme | yazar | — | — | — | — | test |
| X-4 | Event ekleme | yazar | — | onay | — | dinler | test |
| X-5 | Sorgu optimizasyonu | ister | yazar | — | — | — | test |
| X-6 | İş kuralı değişikliği | dinler | — | yazar | — | — | test |
| X-7 | Güvenlik politikası | uygular | — | — | yazar | — | test |
| X-8 | Uzak servis ekleme | tetikler | — | — | onay | yazar | test |
| X-9 | Coverage artırma | test yazar | test | test | test | test | gate |
| X-10 | Kapsam dışı kod tespiti | düzeltir | — | — | — | — | raporlar |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
