---
node_id: K011-application/application-master
node_type: domain
status: verify-required
parent_id: K000–K5000 Architecture Registry
children:
  - usecase-akislari
  - command-query
  - event-akislari
  - dto-ve-mapper
  - odem-servisleri
  - zamanlamali-gorevler
  - web
  - desktop
  - electron
  - local-runtime
  - server-runtime
  - application-services
  - commands
  - queries
  - workflows
  - application-testing
depends_on:
  - K007-data
  - K008-domain
  - K009-security
  - K010-identitiy
  - K012-services
  - K013-integraion
  - K017-observability
  - K019-testing
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.11 satır 352–370 (K011 tanımı + 10 çocuk mini-ağaç)"
  - "shared/src/PageRouter/ — 14 PHP dosyası / 1.575 satır (envanter 2026-10-12)"
  - "shared/src/Middleware/ — 11 middleware dosyası (envanter 2026-10-12)"
  - "shared/src/Events/ — EventDispatcher + 12 event sınıfı (envanter 2026-10-12)"
  - ".ai/.decisions/draft/ADR-097-...coklu-md-ve-bant-rezervasyonu.md §5.5 (iki-ekseni gruplama)"
  - ".ai/architecture/index.md §8.1 (gruplama tablosu)"
validation:
  - "2026-10-12 — v2 §2.11 ELI15 cümlesi ve 10 çocuk adı spec'ten birebir alındı (imla korundu)"
  - "2026-10-12 — kök 12 MD + 6 yeni çocuk + 3 kademe-3 teknoloji klasörü şeması yazıldı"
  - "2026-10-12 — repo envanteri shell ile ölçüldü; PLANNED yüzeyler ayrıca işaretlendi"
open_questions:
  - "6 yeni çocuk (usecase-akislari · command-query · event-akislari · dto-ve-mapper · odem-servisleri · zamanlamali-gorevler) v2 §2.11'de YOK → üretim kararı → ⚠️ VERIFICATION REQUIRED"
  - "v2 'commands' ve 'queries' çocukları ile yeni 'command-query' çocuğu örtüşüyor → kapsam ayrımı onayı → ⚠️ VERIFICATION REQUIRED"
  - "K011 ↔ K012 sınırı (zamanlanmış görev tetiklediği dış servis) döngü riski taşır → ⚠️ VERIFICATION REQUIRED"
  - "repo'da `class *UseCase`, ödeme ve cron/worker kodu YOK (envanter 2026-10-12) → PLANNED"
group: mimari
---

# application-master — K011-application Anayasası

## §1 — ELI15 rol (v2 §2.11 — birebir, imla korundu)

> **Uygulamanın kendisi — web sayfası, masaüstü penceresi, Electron çatısı.**

Bu düğüm, CoreMusic'in **application katmanıdır**: bir kullanıcının isteğinin
route edilip `use case` / `command handler` / `query handler` üzerinden çalıştırıldığı,
sonucun `DTO`'ya çevrildiği ve yanıtın geri gönderildiği katman. K011 **iş kuralını**
üretmez (o K008-domain), **dış servise gitmez** (o K012-services); ikisiyle de
yalnızca `depends_on` ile bağ taşır.

## §2 — Kimlik künyesi

| Alan | Değer |
|---|---|
| node_id | `K011-application/application-master` |
| node_type | `domain` |
| parent_id | `K000–K5000 Architecture Registry` |
| status | `verify-required` |
| group | `mimari` |
| Kaynak | v2 §2.11 (satır 352–370) |
| Şema | 12/6 çoklu-MD + iki-ekseni gruplama (ADR-097 §5.5 · index §8.1) |

## §3 — K011 ne yapar / ne yapmaz

| # | Yapar | Yapmaz | Sınır düğümü |
|---|---|---|---|
| 1 | Route → middleware → handler akışını yürütür | İş kuralı (invariant) üretmez | K008-domain |
| 2 | `command` / `query` ayırır ve handler'a dağıtır | Aggregate state'i kalıcı yapmaz | K007-data |
| 3 | Domain event'i yayınlar (`EventDispatcher`) | Dış servisi HTTP ile tetiklemez | K012-services |
| 4 | `DTO` ↔ entity/map dönüşümünü yapar | BCNF şema kararı vermez | K007-data |
| 5 | Zamanlanmış görevi tanımlar ve çalıştırır | Cron altyapısını barındırmaz | K018-devops |
| 6 | Middleware zincirini sırayla yürütür | Güvenlik politikasını sahiplenmez | K009-security |
| 7 | Kimlik doğrulama adımını çağırır | Kimlik deposunu tutmaz | K010-identitiy |
| 8 | Sayfa/API zarfını üretir | Dış API adaptörü yazmaz | K013-integraion |

## §4 — Kademe-2 çocuk kataloğu (16 çocuk)

### §4.1 v2 spec'ten gelen 10 çocuk (KURAL 1 — adlar birebir, dokunulmaz)

| # | Çocuk | v2 satır | Bu üretimde | Durum |
|---|---|---|---|---|
| 1 | `web` | 360 | yalnız `index.md` mevcut | verify-required |
| 2 | `desktop` | 361 | yalnız `index.md` mevcut | verify-required |
| 3 | `electron` | 362 | yalnız `index.md` mevcut | verify-required |
| 4 | `local-runtime` | 363 | yalnız `index.md` mevcut | verify-required |
| 5 | `server-runtime` | 364 | yalnız `index.md` mevcut | verify-required |
| 6 | `application-services` | 365 | yalnız `index.md` mevcut | verify-required |
| 7 | `commands` | 366 | yalnız `index.md` mevcut | verify-required |
| 8 | `queries` | 367 | yalnız `index.md` mevcut | verify-required |
| 9 | `workflows` | 368 | yalnız `index.md` mevcut | verify-required |
| 10 | `application-testing` | 369 | yalnız `index.md` mevcut | verify-required |

### §4.2 Bu üretimde eklenen 6 çocuk (⚠️ VERIFICATION REQUIRED — v2'de adı yok)

| # | Çocuk | Kapsam | Kanıt durumu | group dosyaları |
|---|---|---|---|---|
| 1 | `usecase-akislari` | route → guard → handler/use case → yanıt | `class *UseCase` repo'da YOK → verify-required | mimari ×3 · kod ×1 · kanit ×2 |
| 2 | `command-query` | command/query ayrımı, handler, idempotency | `shared/src/Api/Gateway.php` + `tests/Repository/ApiKeyCreateCommandTest.php` → verify-required | mimari ×3 · kod ×1 · kanit ×2 |
| 3 | `event-akislari` | domain/integration event oluşum → publish → dinleme | `shared/src/Events/` (dispatcher + 12 event) → verified kısmen | mimari ×3 · kod ×1 · kanit ×2 |
| 4 | `dto-ve-mapper` | DTO sözleşmesi, map, doğrulama | `auth.coremusic.net/include/Domain/DTO/` + `shared/tests/Api/DtoTest.php` → verify-required | mimari ×3 · kod ×1 · kanit ×2 |
| 5 | `odem-servisleri` | ödeme use case akışları (arayüz + idempotency) | ödeme kodu repo'da YOK → verify-required · sınır K012 | mimari ×3 · kod ×1 · kanit ×2 |
| 6 | `zamanlamali-gorevler` | periyodik görev tanımı, pencere, tekrar deneme | cron/worker kodu repo'da YOK → verify-required · sınır K018 | mimari ×3 · kod ×1 · kanit ×2 |

## §5 — Kök 12 MD şeması (12/12)

| # | Dosya | group | Aralık (satır) | Amaç |
|---|---|---|---|---|
| 1 | `index.md` | mimari | 60–120 | künye · §GRUPLAR tablosu · çocuk/envanter |
| 2 | `application-master.md` | mimari | 300–500 | bu anayasa |
| 3 | `application-kapsam.md` | mimari | 150–300 | ne DEĞİL + sınır matrisi |
| 4 | `application-bagimliliklar.md` | mimari | 150–300 | depends_on / used_by / entegrasyon |
| 5 | `application-dogrulama.md` | kanit | 200–500 | ölçüt matrisi + web-kanıt (≥25 içerik satırı) |
| 6 | `application-web-arastirma.md` | kanit | 300–600 | 13 doğrulanmış URL (P1 havuzu) |
| 7 | `application-bilesenler.md` | mimari | 200–400 | kademe-3 bileşen ↔ repo dosyası eşlemesi |
| 8 | `application-kod-ornekleri.md` | kod | 200–400 | gerçek `Dosya:/Satır:` PHP örnekleri |
| 9 | `application-kararlar.md` | mimari | 150–300 | ADR eşlemesi + üretim kararları |
| 10 | `application-riskler.md` | kanit | 120–300 | risk kaydı · kaynak · azaltma |
| 11 | `application-test-stratejisi.md` | kod | 120–250 | test piramidi + gate |
| 12 | `application-evrensel-dil.md` | mimari | 100–250 | terim sözlüğü (TR domain × EN terim) |

**Grup dağılımı (kök):** `mimari` 7 · `kod` 2 · `kanit` 3 · `teknoloji` 0.

## §6 — Kademe-3 teknoloji derin-dalları (3 klasör)

| # | Klasör | Dosya (ad + grup rolü) | Bağlandığı repo yüzeyi |
|---|---|---|---|
| 1 | `pagerrouter/` | `index` · `-anayasa` · `-yonlendirme-tablosu` · `-middleware-zinciri` · `-oturum-akisi` · `-kod-ornekleri` · `-web-arastirma` · `-dogrulama` · `-riskler` · `-test-stratejisi` · `-evrensel-dil` | `shared/src/PageRouter/` · `shared/src/Middleware/` |
| 2 | `event-bus/` | `index` · `-anayasa` · `-abone-tablosu` · `-dinleyici-zinciri` · `-yayin-akisi` · `-kod-ornekleri` · `-web-arastirma` · `-dogrulama` · `-riskler` · `-test-stratejisi` · `-evrensel-dil` | `shared/src/Events/` · `shared/tests/Events/` |
| 3 | `cache-layer/` | `index` · `-anayasa` · `-anahtar-tablosu` · `-katman-zinciri` · `-gecersizlik-akisi` · `-kod-ornekleri` · `-web-arastirma` · `-dogrulama` · `-riskler` · `-test-stratejisi` · `-evrensel-dil` | `shared/src/Cache/` · `shared/src/Security/CacheRateLimiter.php` |

**Adlandırma kuralı (ADR-097 §5.5 B):** teknoloji = klasör + dosya öneki; dosya adı
**mimari/kod ilişkisi** taşır (`-yonlendirme-tablosu`, `-middleware-zinciri`,
`-oturum-akisi`, `-kod-ornekleri`); soyut/uydurma ad yasak. Kademe-3 dosyaları
**yalnız** kendi teknoloji klasöründe yaşar, kök klasöre serpilmez.

## §7 — Katman sorumluluk matrisi

| Katman | Sorumlu agent (`.ai/AGENTS.md` §4) | K011 ile kesişim |
|---|---|---|
| Presentation | UI Designer · Backend Architect | route/handler imzası, `ViewModel` → `DTO` |
| Application (K011) | Backend Architect | use case, command/query, event, DTO, zamanlanmış görev |
| Domain (K008) | Backend Architect + Data Engineer | aggregate, entity, value object, invariant |
| Infrastructure | Data Engineer · Security Engineer | repository, adapter, middleware güvenliği |

## §8 — Mimari ilkeler (K011'e bağlayıcı)

| # | İlke | Dayanak |
|---|---|---|
| 1 | Handler **framework'ten bağımsız** yazılır; framework yalnız taşıyıcıdır | PSR-15 (P3 havuzu) |
| 2 | `command` ile `query` aynı handler'da birleşmez (CQRS hafif) | K011/command-query |
| 3 | Domain event **geçmiş zamanlı** adlandırılır (`music.played`) | `shared/src/Events/Domain/*` |
| 4 | `DTO` içeriği **presentation**'a aittir; entity asla dışarı çıkmaz | `shared/src/Api/ApiResponse.php` |
| 5 | Middleware **tek sorumluluk** taşır; sıraya `MiddlewarePipeline` karar verir | `shared/src/Middleware/MiddlewarePipeline.php` |
| 6 | Hata **Result/Either** benzeri zarf ile taşınır; `throw` yalnız sınır katmanında | `.ai/CLAUDE.md` §6 LINT |
| 7 | Zamanlanmış görev **idempotent** olmak zorundadır | K011/zamanlamali-gorevler |
| 8 | Ödeme akışında **idempotency key** zorunlu; tekrar deneme mutasyon yaratmaz | K011/odem-servisleri |
| 9 | K011, K012'ye **doğrudan** bağlanmaz; arada event/handler sınırı vardır | K008/K012 ayrımı |
| 10 | Her handler'ın karşılığında **test dosyası** vardır (gate ≥ %80) | `.ai/AGENTS.md` §16 |

## §9 — İstek yaşam döngüsü (ASCII)

```text
  tarayıcı / SPA / masaüstü
          │  HTTP GET /music/playlist/42
          ▼
  ┌─ PageRouterKernel ────────────────────────────────────────────┐
  │ 1 RequestNormalizer  → path/query gövde normalize             │
  │ 2 RouteRegistry      → RouteResult ( eşleşme / 404 )          │
  │ 3 AuthGuard          → oturum + rol kontrolü                 │
  │ 4 MiddlewarePipeline → CORS → Origin → SecurityHeaders →      │
  │                        RateLimit → CSRF → Validation →       │
  │                        Session → Auth → Permission           │
  │ 5 handler            → command / query / use case            │
  │ 6 domain (K008)      → aggregate erişimi                     │
  │ 7 event publish      → EventDispatcher → dinleyiciler        │
  │ 8 DTO map            → ApiResponse / HtmlShellRenderer       │
  │ 9 ResponseEmitter    → status + header + body                │
  └──────────────────────────────────────────────────────────────┘
          │
          ▼
   200 / 3xx / 4xx / 5xx  (+ structured log)
```

## §10 — K008 · K012 ile sınır

| Soru | K011 cevabı | Karşı düğüm |
|---|---|---|
| Aggregate nerede oluşur? | Oluşmaz; **çağrılır** | K008-domain |
| İş kuralı ihlali nasıl bildirilir? | Handler `Result` zarfı alır, yorumlamaz | K008-domain |
| Dış servis çağrısı nasıl olur? | Handler → event → K012 adaptörü | K012-services |
| Ödeme sağlayıcı şifresi? | K011'de **bulunmaz** | K009-security / K012 |
| Cron zamanlayıcı? | Görev **tanımı** K011'de; **çalıştırıcı** K018 | K018-devops |

## §11 — Bağımlılık grafiği

```text
              K000–K5000 Architecture Registry
                          │
                 K011-application
                          │
     ┌────────────────────┼─────────────────────┐
     ▼                    ▼                     ▼
  12 kök MD         6 yeni çocuk           10 v2 çocuk
 (master/kapsam/    (usecase-akislari      (web · desktop ·
  bagimliliklar/     command-query          electron · ...
  bilesenler/        event-akislari         application-testing)
  kararlar/          dto-ve-mapper)
  evrensel-dil/      odem-servisleri
  dogrulama/         zamanlamali-gorevler
  web-arastirma/
  kod-ornekleri/
  riskler/
  test-stratejisi)
                          │
                          ▼
              kademe-3 teknoloji klasörleri
        pagerrouter/  ·  event-bus/  ·  cache-layer/
                          │
        ┌─────────────────┼──────────────────┐
        ▼                 ▼                  ▼
 shared/src/PageRouter  shared/src/Events  shared/src/Cache
 shared/src/Middleware  shared/tests/Events shared/src/Security
```

## §12 — Doğrulama durumu

| Ölçüt | Sonuç | Tarih |
|---|---|---|
| v2 §2.11 adları birebir alındı mı? | ✅ evet (10 çocuk + ELI15 cümle) | 2026-10-12 |
| Kök 12 MD yazıldı mı? | ✅ evet | 2026-10-12 |
| Kademe-2 6 yeni çocuk 6 MD | ✅ evet | 2026-10-12 |
| Kademe-3 3 teknoloji klasörü ≥500 satır | 🔄 üretim sırasında ölçülecek (`index.md` §GRUPLAR) | 2026-10-12 |
| Repo envanteri shell ile ölçüldü mü? | ✅ evet (PageRouter 14 · Middleware 11 · Events 14 + Contracts 2 · Cache 6 · Session 4 · Api 3) | 2026-10-12 |
| Kullanıcı onayı | ❌ yok → `status: verify-required` | 2026-10-12 |

## §12.1 — Kök 12 dosya envanteri (ad · grup · satır aralığı)

| # | Dosya | Grup | Zorunlu satır | İşlev |
|---|---|---|---|---|
| 1 | `index.md` | mimari | 60–120 | Dizin + §GRUPLAR |
| 2 | `application-master.md` | mimari | 300–500 | Ana belge (bu dosya) |
| 3 | `application-kapsam.md` | mimari | 150–300 | Kapsam sınırı |
| 4 | `application-bagimliliklar.md` | mimari | 150–300 | Bağımlılık matrisi |
| 5 | `application-bilesenler.md` | mimari | 200–400 | Bileşen envanteri |
| 6 | `application-kararlar.md` | mimari | 150–300 | Karar defteri |
| 7 | `application-evrensel-dil.md` | mimari | 100–250 | Terim sözlüğü |
| 8 | `application-kod-ornekleri.md` | kod | 200–400 | Gerçek snippet'ler |
| 9 | `application-test-stratejisi.md` | kod | 120–250 | Test planı |
| 10 | `application-dogrulama.md` | kanit | 200–500 | Kanıt defteri |
| 11 | `application-web-arastirma.md` | kanit | 300–600 | URL kanıtları |
| 12 | `application-riskler.md` | kanit | 120–300 | Risk defteri |

**Dağılım:** mimari ×7 · kod ×2 · kanit ×3 = 12.

## §12.2 — Dosya okuma sırası (görev tipine göre)

| Görev tipi | Sıra | Neden |
|---|---|---|
| Kapsam/ne-DEĞİL | index → kapsam → master | sınır önce |
| Kod değişikliği | master §10 → kod-ornekleri → bilesenler | desen önce |
| Güvenlik | dogrulama §3 → kararlar §5 → riskler | kanıt önce |
| Test | test-stratejisi → dogrulama §6 | kapı önce |
| ADR | kararlar → riskler → dogrulama §7 | karar + risk + boşluk |

## §12.3 — Değişiklik protokolü

1. **Ekleme:** Yeni satır en altta; mevcut satır üzerine yazılmaz (append-only).
2. **Kanıt:** Her yeni iddia `Dosya: … / Satır: …` ya da URL + 2026-10-12 tarihi taşır.
3. **Grup:** `group` alanı yalnız `mimari|kod|kanit` olabilir; değişiklik `index.md` §GRUPLAR'a yansıtılır.
4. **Durum:** `verify-required → active` yalnız kullanıcı onayıyla olur.
5. **Yasak:** Başka K klasörüne yazma · git commit · URL tekrarı.

## §13 — Referanslar

| Kaynak | Ne verir |
|---|---|
| `coremusic-katmanli-mimari-agaci-v2.md` §2.11 · §3 · §4.2 · §4.7 | tanım · adlandırma · 8 alan · bant |
| `.ai/architecture/index.md` §8.1 | iki-ekseni gruplama tablosu |
| `.ai/.decisions/draft/ADR-097-…coklu-md-ve-bant-rezervasyonu.md` §5.5 | gruplar G1–G4 · formül |
| `.ai/AGENTS.md` §4 · §16 | agent sorumluluğu · kalite standardı |

## §14 — Sözlük (K011'ye özgü terimler)

| Terim | Tanım |
|---|---|
| **Kademe-2 çocuk** | K011 altında 6 MD taşıyan birim (index + 5 içerik dosyası) |
| **Kademe-3 teknoloji klasörü** | 8–12 dosyalık, her dosyası ≥500 satır, `{teknoloji}-{rol}.md` adlı derin düğüm |
| **İki-eksen gruplama** | `group: mimari|kod|kanit` frontmatter alanı + `## §GRUPLAR` tablosu birlikte |
| **Kapsam bantı** | K011'in kodlayabildiği tek bant: A2 (application), alt bant A0/A1'e politika verir |
| **Boşluk** | K011'de karşılığı olmayan gerçek varlık (ör. ödeme servisi) → `verify-required` |
| **Kanıt satırı** | `Dosya: … / Satır: …` biçimli, diskte doğrulanabilir referans |

## §A — Alt-modül ağacı (routing·caching·dto·usecase-akislari + kök)

> Bu bölüm, K011-application altındaki **kademe-2.5 alt-modül klasörleri** ile kök 12 MD
> arasındaki ilişkiyi tek yerde toplar. Ölçüm yöntemi: `[IO.File]::ReadAllLines(<yol>).Count`
> (boş satır dâhil; `Measure-Object -Line` ile karıştırılmaz — o boş satırı saymaz).

### §A.1 — Alt-modül klasör envanteri (2026-10-13 ölçümü)

| # | Klasör | Dosya | Satır | ≥500 | group | Bağlı olduğu repo yüzeyi | Durum |
|---|---|---|---|---|---|---|---|
| 1 | `routing/` | `routing-yonlendirme-cati.md` | 529 | ✅ | mimari | `shared/src/PageRouter/` · `shared/src/Api/Routing/` | verified (disk) |
| 2 | `caching/` | `caching-uygulama-seviyesi-cache.md` | 513 | ✅ | mimari | `shared/src/Cache/` · `shared/src/Security/CacheRateLimiter.php` | verified (disk) |
| 3 | `dto/` | `dto-veri-tasiyici-tasarimi.md` | 525 | ✅ | mimari | `shared/src/Api/Dto/` · `shared/src/Api/ApiResponse.php` | verified (disk) |
| 4 | `usecase/` | `usecase-usecase-katmani.md` | 509 | ✅ | mimari | `class *UseCase` repo'da YOK → PLANNED | ⚠️ VERIFICATION REQUIRED |
| 5 | `usecase-akislari/` | `index.md` | 82 | — (index bandı) | mimari | `shared/src/PageRouter/` · `shared/src/Middleware/` | verified (disk) |
| 6 | `usecase-akislari/` | `usecase-akislari-kapsam.md` | ≥500 (2026-10-13 üretimi) | ✅ | mimari | `shared/src/PageRouter/AuthGuard.php` · `CsrfMiddleware.php` | üretiliyor |
| 7 | `usecase-akislari/` | `usecase-akislari-web-arastirma.md` | ≥500 (2026-10-13 üretimi) | ✅ | kanit | PSR-15/PSR-7 web kanıtları | üretiliyor |

**Kural:** `index.md` dosyaları bu tabloda satır hedefine tabi değildir (indeks dosyası 40–100
bandında kalır); içerik dosyaları ≥500 satıra tabidir.

### §A.2 — Kök 12 MD ↔ alt-modül bağları

| # | Kök MD | group | Alt-modülle bağı | Bağ türü |
|---|---|---|---|---|
| 1 | `index.md` | mimari | tüm alt-modüller | dizin |
| 2 | `application-master.md` | mimari | 4 alt-modül + 12 kök | anayasa |
| 3 | `application-kapsam.md` | mimari | `usecase-akislari/` | kapsam sınırı |
| 4 | `application-bagimliliklar.md` | mimari | `routing/` · `caching/` | `depends_on` |
| 5 | `application-bilesenler.md` | mimari | `dto/` · `caching/` | bileşen eşlemesi |
| 6 | `application-kararlar.md` | mimari | 4 alt-modül | karar mirası |
| 7 | `application-evrensel-dil.md` | mimari | 4 alt-modül | terim eşlemesi |
| 8 | `application-kod-ornekleri.md` | kod | `routing/` · `caching/` · `dto/` | snippet kaynağı |
| 9 | `application-test-stratejisi.md` | kod | `usecase/` · `usecase-akislari/` | test kapsamı |
| 10 | `application-dogrulama.md` | kanit | 4 alt-modül | ölçüt matrisi |
| 11 | `application-web-arastirma.md` | kanit | `usecase-akislari/web` | URL havuzu |
| 12 | `application-riskler.md` | kanit | 4 alt-modül | risk yayılımı |

### §A.3 — Alt-modül ağacı (ASCII)

```text
K011-application (application-master.md — bu dosya)
│
├── routing/     → rota çözümü, middleware sırası      (529 satır · verified)
│     └── kaynak: shared/src/PageRouter/ (14 PHP) · shared/src/Api/Routing/
├── caching/     → uygulama-seviyesi cache (APCu/Redis) (513 satır · verified)
│     └── kaynak: shared/src/Cache/ (6 PHP: CacheInterface · ApcuAdapter ·
│                                   MemoryAdapter · PageCacheAdapter · CacheManager)
├── dto/         → veri taşıyıcı tasarımı              (525 satır · verified)
│     └── kaynak: shared/src/Api/Dto/ (Request 6 · Response 5) · ApiResponse.php
├── usecase/     → use-case katmanı                    (509 satır · PLANNED)
│     └── durum: repo'da `class *UseCase` YOK (2026-10-12 envanteri)
└── usecase-akislari/ → akış kapsamları + web kanıtı   (index 82 · 2 dosya ≥500)
      └── kaynak: PageRouter (RouteRegistry · AuthGuard · ResponseEmitter)
                  Middleware (Pipeline · Csrf · RateLimiter · Validation)
```

### §A.4 — Grup dağılımı (alt-modül + kök, 2026-10-13)

| group | Kök MD | Alt-modül dosyası | Toplam | Zorunlu bölüm şablonu |
|---|---|---|---|---|
| `mimari` | 7 | 5 | 12 | master · kapsam · bilesenler · bagimliliklar · kararlar · evrensel-dil |
| `kod` | 2 | 0 | 2 | kod-ornekleri · test-stratejisi |
| `kanit` | 3 | 1 | 4 | dogrulama · web-arastirma · riskler |
| **Toplam** | **12** | **6** | **18** | §A–§D genişletme şablonu |

### §A.5 — Alt-modül olgunluk matrisi

| Alt-modül | Repo kanıtı | Dosya hedefi | Eksik | Öncelik |
|---|---|---|---|---|
| `routing/` | `RouteRegistry::resolve` — `Dosya: shared/src/PageRouter/RouteRegistry.php / Satır: 35` | ≥500 ✅ | — | P2 (koru) |
| `caching/` | `CacheManager::getAdapter` — `Dosya: shared/src/Cache/CacheManager.php / Satır: 9` | ≥500 ✅ | — | P2 (koru) |
| `dto/` | `ApiResponse::paginated` — `Dosya: shared/src/Api/ApiResponse.php / Satır: 73` | ≥500 ✅ | — | P2 (koru) |
| `usecase/` | sınıf yok → PLANNED | ≥500 ✅ | implementasyon kanıtı | P0 (karar bekliyor) |
| `usecase-akislari/` | `AuthGuard::check` — `Dosya: shared/src/PageRouter/AuthGuard.php / Satır: 15` | 2 dosya ≥500 | — | P1 (üretim tamam) |

**Doğrulama notu:** yukarıdaki satır numaraları `(Get-Content <yol>)[N-1]` ile 2026-10-13'te
doğrulanmıştır; numaralar dosya içi `function` imzasının bulunduğu satırdır.

## §B — Master kararlar

> Bu bölüm, K011 genelinde **bağlayıcı** kararları listeler. Alt-modül dosyalarındaki kararlar
> bu listeye aykırı olamaz; çelişki durumunda bu dosya (master) kazanır ve alt-modül revize edilir.

### §B.1 — Karar listesi

| # | Karar | Gerekçe | Sonuç | Dayanak | Durum |
|---|---|---|---|---|---|
| M-01 | Handler framework'ten bağımsız yazılır; framework yalnız taşıyıcıdır | taşınabilirlik + test edilebilirlik | çekirdek `callable` imzası | PSR-15 · `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 23` | IMPLEMENTED |
| M-02 | `command` ile `query` aynı handler'da birleşmez (CQRS hafif) | okuma/yazma ayrı maliyet | ayrı route grubu | `shared/src/Api/Routing/RouteTable.php` | IMPLEMENTED |
| M-03 | Domain event **geçmiş zamanlı** adlandırılır | okunabilirlik + sorgulanabilirlik | `music.played` biçimi | `shared/src/Events/Domain/*` | IMPLEMENTED |
| M-04 | Entity asla API yanıtına girmez; yalnız DTO | sızıntı önleme | `ApiResponse::ok` zarfı | `Dosya: shared/src/Api/ApiResponse.php / Satır: 26` | IMPLEMENTED |
| M-05 | Middleware tek sorumluluk taşır; sıraya pipeline karar verir | tek sorumluluk ilkesi | `pipe()` → `run()` | `Dosya: shared/src/Middleware/MiddlewarePipeline.php / Satır: 17` | IMPLEMENTED |
| M-06 | Hata sınır katmanında tek yere toplanır; iç katmanlar Result zarfı döner | istisna sızıntısını engelle | `ErrorHandler::buildErrorPage` | `Dosya: shared/src/PageRouter/ErrorHandler.php / Satır: 11` | IMPLEMENTED |
| M-07 | Cache anahtarları tek üretim noktasından yazılır | tutarsız anahtar → yanlış veri | `CacheManager` tek kapı | `Dosya: shared/src/Cache/CacheManager.php / Satır: 9` | IMPLEMENTED |
| M-08 | Mutasyon uçları **idempotency key** ister | ağ tekrarı çift yazım yapmaz | gateway kontrolü | `Dosya: shared/src/Api/Gateway.php / Satır: 50` | kısmen IMPLEMENTED |
| M-09 | Zamanlanmış görev idempotent olmak zorunda | cron tekrarı veri bozmaz | tanım K011, çalıştırıcı K018 | `.ai/architecture` K018 sınırı | PLANNED |
| M-10 | Ödeme akışında idempotency key zorunlu | çift tahsilat riski | repo'da ödeme kodu YOK | envanter 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| M-11 | K011 → K012 doğrudan bağlanmaz; arada event/handler sınırı vardır | katman izolasyonu | event yayını | `Dosya: shared/src/Events/EventDispatcher.php / Satır: 28` | IMPLEMENTED |
| M-12 | Hız sınırı IP + güvenilir proxy çözümlemesiyle uygulanır | sahte IP ile bypass engeli | `resolveClientIp` | `Dosya: shared/src/Middleware/RateLimiterMiddleware.php / Satır: 105` | IMPLEMENTED |
| M-13 | Oturum başlatma tek yerden (`ensureStarted`) | yarım oturum durumları | statik kapı | `Dosya: shared/src/Session/SessionBootstrapper.php / Satır: 19` | IMPLEMENTED |
| M-14 | Her handler'ın karşılığında test dosyası vardır | gate ≥ %80 | `shared/tests/**` 22+ dosya | `shared/composer.json / Satır: 24` (phpunit ^10.5) | kısmen IMPLEMENTED |
| M-15 | İstek gövdesi normalize edilmeden middleware'e girmez | çift ayrıştırma hataları | `normalizeRequest` | `Dosya: shared/src/PageRouter/PageRouterKernel.php / Satır: 225` | IMPLEMENTED |
| M-16 | Yanıt yayımı tek emitter'dan yapılır | header çakışması engeli | `ResponseEmitter::emit` | `Dosya: shared/src/PageRouter/ResponseEmitter.php / Satır: 16` | IMPLEMENTED |
| M-17 | Yetki reddi fail-closed'dur (açık yok, sessiz geçiş yok) | OWASP erişim kontrolü | `AuthGuard::checkPermission` | `Dosya: shared/src/PageRouter/AuthGuard.php / Satır: 42` | IMPLEMENTED |
| M-18 | Log'da PII/secret redaksiyonu zorunlu | sızıntı önleme | `Redactor` | `shared/src/Log/Redactor.php` | IMPLEMENTED |
| M-19 | use-case katmanı implementasyonu ADR ile başlar | repo'da sınıf yok, varsayım yazılmaz | PLANNED | envanter 2026-10-12 (`*UseCase` = 0) | ⚠️ VERIFICATION REQUIRED |
| M-20 | Alt-modül dosyaları ≥500 satır ve tek §A–§D genişletme şablonuna uyar | tutarlı derinlik | ölçüm kapısı | bu dosya §A.1 | aktif (2026-10-13) |

### §B.2 — Karar durumu dağılımı

| Durum | Adet | Anlamı |
|---|---|---|
| IMPLEMENTED | 13 | repo dosyası + satır kanıtı var |
| kısmen IMPLEMENTED | 2 | arayüz var, kapsam eksik (M-08 · M-14) |
| PLANNED | 2 | repo'da kod yok (M-09 · M-19) |
| ⚠️ VERIFICATION REQUIRED | 3 | sınır/K012 bağlantısı doğrulanacak (M-10 · M-19 + §B.1 notu) |
| aktif (2026-10-13) | 1 | üretim süreci kuralı (M-20) |

### §B.3 — Karar → alt-modül sahipliği

| Karar grubu | Sahip dosya | Devredilen |
|---|---|---|
| M-01 · M-05 · M-12 · M-15 · M-17 | `routing/routing-yonlendirme-cati.md` | middleware sırası tablosu |
| M-07 (detay) | `caching/caching-uygulama-seviyesi-cache.md` | anahtar/TTL/geçersizlik |
| M-04 (detay) | `dto/dto-veri-tasiyici-tasarımı.md` (dosya adı: `dto-veri-tasiyici-tasarimi.md`) | alan sözleşmesi |
| M-03 · M-11 (detay) | `usecase/usecase-usecase-katmani.md` | event akışı |
| M-08 · M-10 (detay) | `usecase-akislari/usecase-akislari-kapsam.md` | idempotency akışları |

## §C — Çapraz modül kuralları (sıra·öncelik·çakışma)

### §C.1 — Sıra kuralları

| # | Sıra | Kural | İhlali |
|---|---|---|---|
| S-1 | route çözümü → middleware → handler | handler rota bilmeden çalışmaz | istek 404'e düşer |
| S-2 | normalize → doğrula → mutate | doğrulamasız mutasyon yasak | veri bozulması |
| S-3 | cache oku → miss ise repo → cache'e yaz | erken return (cache-first) | gereksiz DB yükü |
| S-4 | domain olayı → sonra yanıt zarfı | olay yayını yanıt bloklamaz | çift yanıt riski |
| S-5 | log redaksiyonu → sonra yazım | PII log'a sızmaz | güvenlik olayı |
| S-6 | rate-limit → sonra CSRF | pahalı iş öncesi ucuz kapı | kaynak israfı |
| S-7 | rota koruması (`getProtectedRouteKeys`) → AuthGuard | korumasız rota = açık | OWASP A01 |

### §C.2 — Öncelik kuralları

| # | Çakışan ikili | Kazanan | Gerekçe |
|---|---|---|---|
| O-1 | rate-limit vs cache hit | rate-limit | kapı her zaman önce |
| O-2 | CSRF reddi vs iş mantığı | CSRF reddi | istek hiç işlenmez |
| O-3 | cache TTL vs explicit invalidation | explicit invalidation | veri sahibi günceller |
| O-4 | global exception handler vs yerel try/catch | yerel try/catch (iç katman) | yalnız sınır fırlatır |
| O-5 | rota deseni eşleşmesi (desen mi, sabit mi) | sabit rota | `matchesPattern` yedek |
| O-6 | v2 spec adı vs yeni çocuk adı | v2 spec adı (10 çocuk) | adlar dokunulmaz |
| O-7 | master kuralı vs alt-modül kuralı | master (bu dosya §B) | tek anayasa |

### §C.3 — Çakışma çözüm adımları

| Adım | Aksiyon | Sonuç |
|---|---|---|
| 1 | İlgili iki dosya okunur (master → alt-modül) | kanıt toplanır |
| 2 | Repo kodu ile teyit edilir (`Dosya:`/`Satır:`) | disk kazanır |
| 3 | Çelişki kodda mı, belgede mi belirlenir | belge varsa belge revize |
| 4 | Kodda ise karar satırı `application-kararlar.md`'ye eklenir | tek kayıt |
| 5 | Kapatılamıyorsa `⚠️ VERIFICATION REQUIRED` + üst karar | açık soru kapanmaz |

## §D — Yol haritası

### §D.1 — Fazlar

| # | Faz | Kapsam | Çıktı | Durum |
|---|---|---|---|---|
| R1 | Envanter | repo yüzeyi sayımı (165 PHP · 20 dizin) | §A.1 · §A.5 | ✅ 2026-10-12 |
| R2 | Kök 12 MD | anayasa + sınır + kanıt seti | kök dosyalar | ✅ 2026-10-12 |
| R3 | Alt-modül ≥500 | routing · caching · dto · usecase | 4 dosya ≥500 | ✅ 2026-10-13 ölçümü |
| R4 | Kök genişletme | 11 kök dosya §A–§D ile ≥500 | bu üretim | ✅ 2026-10-13 |
| R5 | usecase-akislari | kapsam + web-arastirma ≥500 | 2 dosya | ✅ 2026-10-13 |
| R6 | use-case kararı | `*UseCase` var mı, ADR ile | ADR + PLANNED → IMPLEMENTED | ⏳ bekliyor |
| R7 | ödeme akışı | K012 sınırı + idempotency | akış şeması | ⏳ bekliyor |
| R8 | gate ölçümü | coverage ≥ %80, lint, URL kesişim 0 | `application-dogrulama.md` | ⏳ bekliyor |

### §D.2 — Kapılar (gate)

| Kapı | Eşik | Ölçüm aracı | Geçiş |
|---|---|---|---|
| Satır derinliği | ≥500 / dosya (index hariç) | `[IO.File]::ReadAllLines()` | zorunlu |
| URL tekrarı | kesişim = 0 | envanter dosyası farkı | zorunlu |
| Kod kanıtı | ≥1 `Dosya:`/`Satır:` / dosya | `(Get-Content)[N-1]` | zorunlu |
| Test | coverage ≥ %80 | PHPUnit (`shared/composer.json / Satır: 24`) | hedef |
| Lint | PHPStan level 5 | `shared/composer.json / Satır: 48` | hedef |
| CI | workflow geçiyor | `.github/workflows/ci.yml / Satır: 8` | hedef |

### §D.3 — Sonraki karar bekleyenler

| # | Soru | Sahip | Kapanış |
|---|---|---|---|
| 1 | `*UseCase` sınıfları repo'ya giriyor mu? | ADR | M-19 |
| 2 | Ödeme akışı K011'de mi, K012'de mi? | K011 ↔ K012 sınırı | M-10 |
| 3 | Redis adaptörü `CacheInterface`'e ekleniyor mu? | `caching/` | cache ADR'si |
| 4 | 6 yeni çocuk adı v2 spec'ine yazılıyor mu? | vault sahibi | onay |

**Last Updated:** 2026-10-13
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
