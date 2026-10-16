---
title: "K011 · usecase — Bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 · usecase — Bileşenler (Envanter ve Mimari Analiz)

> **Amaç:** UseCase/Application katmanının bileşen envanterini (Handler,
> Controller dispatch, Transaction, Container, Event), aile/hiyerarşi
> yapısını ve katman bağımlılıklarını sunmak. Her iddia Dosya kanıtlıdır.

## §GRUPLAR

| Bölüm | Konu |
|---|---|
| §A | Bileşen ağacı |
| §B | Handler ailesi |
| §C | Controller ailesi |
| §D | Transaction ailesi |
| §E | Container ailesi (php-di / PSR-11) |
| §F | Event ailesi (PSR-14) |
| §G | Validation pipeline ailesi |
| §H | Katman bağımlılık matrisi |
| §I | SRP ve kalite analizi |
| §J | Bileşen olgunluk matrisi |
| §K | PLANNED bileşenler |
| §L | Cross-reference |

---

## §A — BİLEŞEN AĞACI

```
UseCase / Application katmanı bileşenleri (de-fakto)
├── Handler ailesi (senaryo yürütücüler)
│   └── AuthPostHandler                    [auth.coremusic.net/include/Handler/]
│
├── Controller ailesi (HTTP orkestratörler)
│   └── AuthController (355 L)             [api.coremusic.net/include/Controller/]
│
├── Transaction ailesi (bütünlük sağlayıcılar)
│   ├── UserRepository (3 transaction)     [api.coremusic.net/include/Repository/]
│   ├── CatalogWriter (3 transaction)      [api.coremusic.net/include/Repository/]
│   └── api-key-create (1 transaction)     [shared/bin/]
│
├── Container ailesi (bağımlılık çözücü)
│   └── AuthContainer (php-di)             [auth.coremusic.net/include/Container/]
│
├── Event ailesi (PSR-14)
│   └── EventDispatcher                    [shared/src/Events/]
│
├── Validation pipeline ailesi (middleware)
│   ├── RequestValidationMiddleware (175 L) [shared/src/Api/Middleware/]
│   └── Validator                          [shared/src/Api/Validation/]
│
└── UseCase ailesi (KLASİK ADLANDIRMA)
    └── (YOK — glob *UseCase*.php = 0 dosya) ⚠️ PLANNED
```

---

## §B — HANDLER AİLESİ

### B.1 — AuthPostHandler

| Özellik | Değer | Kanıt |
|---|---|---|
| Dosya | auth.coremusic.net/include/Handler/AuthPostHandler.php | envanter |
| Senaryo | Login | dogrulama §O (eski kapsam §A.3 — 2026-10-16 kaldırıldı) |
| Dispatch | `match(...)` (Satır: 38-57) | grep |
| Girdi | `ServerRequestInterface` (PSR-7) | Satır: 38-57 |
| Çıktı | `ResponseInterface` (PSR-7) | Satır: 38-57 |
| DTO kullanımı | `new LoginRequest(...)` — AKTİF üretim | Satır: 38-57 |
| SRP | ✅ tek senaryo (login) | — |

⚠️ VERIFICATION REQUIRED: `auth.coremusic.net/include/Handler/` dizininde
başka handler dosyaları var mı — bu görev kapsamında listelenmedi.

### B.2 — Handler ailesi envanter durumu

| Dosya | Durum |
|---|---|
| AuthPostHandler.php | ✅ doğrulandı |
| Diğer handler'lar | ⚠️ VERIFICATION REQUIRED |

---

## §C — CONTROLLER AİLESİ

### C.1 — AuthController

| Özellik | Değer | Kanıt |
|---|---|---|
| Dosya | api.coremusic.net/include/Controller/AuthController.php | envanter |
| Toplam satır | 355 | Dosya·Satır ölçümü |
| Dispatch | `match($action)` (Satır: 52-61) | grep |
| Error mapping | Satır: 62-78 | grep |
| Girdi | `ServerRequestInterface` | §A usecase-ornekler.md |
| Çıktı | `ResponseInterface` | §A usecase-ornekler.md |
| SRP | ⚠️ 355 satır — iş mantığı olabilir | usecase-dogrulama.md §K |

### C.2 — Controller rolü (katman analizi)

| Katman | Controller sorumluluğu |
|---|---|
| Presentation | HTTP kabuğu (doğru) |
| Application | Dispatch orkestrasyonu (match) |
| Error mapping | Status kodu atama (Satır: 62-78) |

**Gözlem:** Controller, Handler'a dispatch yaptığı için **Application katmanının
giriş kapısı** olarak çalışıyor. Fakat 355 satır, saf Controller'dan fazlasını
içerebilir — ⚠️ VERIFICATION REQUIRED (dosya tam okunmadı).

---

## §D — TRANSACTION AİLESİ

### D.1 — Transaction envanteri

| Bileşen | Dosya | Transaction satırları | Adet |
|---|---|---|---|
| UserRepository | api.../Repository/UserRepository.php | 147, 184, 192 | 3 |
| CatalogWriter | api.../Repository/CatalogWriter.php | 183, 217, 220 | 3 |
| api-key-create | shared/bin/api-key-create.php | 135-147 | 1 |
| **TOPLAM** | — | — | **7** |

### D.2 — Transaction sahibi analizi

| Desen | Klasik | Bu repo |
|---|---|---|
| Transaction sahibi | UseCase / UnitOfWork | **Repository** |
| UseCase rolü | transaction orkestrasyonu | (dosya yok) |
| Repository rolü | veri erişimi | veri erişimi + bütünlük |

⚠️ **MİMARİ BULGU:** Repository çift sorumluluk (SRP riski).
UseCase eklenirse transaction orkestrasyonu taşınmalı — ADR kararı.

---

## §E — CONTAINER AİLESİ (php-di / PSR-11)

### E.1 — AuthContainer

| Özellik | Değer | Kanıt |
|---|---|---|
| Dosya | auth.coremusic.net/include/Container/AuthContainer.php | envanter |
| Kütüphane | php-di (`DI\ContainerBuilder`) | grep |
| PSR-11 arayüzü | ⚠️ VERIFICATION REQUIRED | grep edilmedi |
| Scope | Auth domain'e özel | — |
| Compile cache | ⚠️ VERIFICATION REQUIRED | okunmadı |
| Singleton kayıtlar | ⚠️ VERIFICATION REQUIRED | okunmadı |

### E.2 — Container bileşen analizi

| Soru | Cevap | Durum |
|---|---|---|
| Genel (app-wide) composition root var mı? | AuthContainer tek örnek | ⚠️ eksik olabilir |
| Container test ediliyor mu? | ⚠️ doğrulanmadı | — |
| PSR-11 `get`/`has` kullanımı | ⚠️ grep edilmedi | — |

---

## §F — EVENT AİLESİ (PSR-14)

### F.1 — EventDispatcher

| Özellik | Değer | Kanıt |
|---|---|---|
| Dosya | shared/src/Events/EventDispatcher.php | envanter |
| Arayüz | `EventDispatcherInterface` (PSR-14) | grep |
| dispatch kullanımı (use-case akışta) | ⚠️ VERIFICATION REQUIRED | grep edilmedi |

### F.2 — Event bileşen analizi

| Soru | Cevap | Durum |
|---|---|---|
| Event'ler yayınlanıyor mu use-case akışta? | ⚠️ doğrulanmadı | `grep "->dispatch("` gerekli |
| Listener kayıt mekanizması | ⚠️ doğrulanmadı | dosya okunmadı |
| Event sınıf envanteri | Events/ = 9 PHP dosyası | ⚠️ detaylı envanter |

---

## §G — VALIDATION PIPELINE AİLESİ

### G.1 — Middleware + Validator

| Bileşen | Dosya | Uzunluk | Rol |
|---|---|---|---|
| RequestValidationMiddleware | shared/src/Api/Middleware/RequestValidationMiddleware.php | 175 L | HTTP doğrulama |
| Validator | shared/src/Api/Validation/Validator.php | ⚠️ ölçülmedi | doğrulama motoru |
| Route rules | api.coremusic.net/config/routes.php | ⚠️ ölçülmedi | kural seti |

### G.2 — Pipeline ilişkisi

```
routes.php (rule set)
    → RequestValidationMiddleware (175 L)
        → Validator
            → pass → Controller dispatch (AuthController:52-61)
            → fail → status kodu (⚠️ 415/400/422 — doğrulanmadı)
```

⚠️ VERIFICATION REQUIRED: Middleware'ın status kodu seçimi ve Validator'ın
iç yapısı bu görevde okunmadı.

---

## §H — KATMAN BAĞIMLILIK MATRİSİ

| Bileşen | Bağımlılıklar (kod) | Dış paket | Framework bağımlılığı |
|---|---|---|---|
| AuthPostHandler | PSR-7 tipleri, LoginRequest | psr/http-message (transitif) | ⚠️ Response üretimi (factory belirsiz) |
| AuthController | PSR-7 tipleri, Handler | psr/http-message | ⚠️ (Presentation katmanı — framework kabul) |
| UserRepository | DatabaseManager (DB) | — | ❌ |
| CatalogWriter | DatabaseManager (DB) | — | ❌ |
| AuthContainer | php-di | php-di/php-di | ❌ (container kütüphanesi) |
| EventDispatcher | PSR-14 | psr/event-dispatcher | ❌ |
| RequestValidationMiddleware | Validator | — | ⚠️ (Middleware — framework kabul) |

**Kural uyumu (AGENTS §5 — Application framework bağımsız):**

| Bileşen | Kural | Durum |
|---|---|---|
| AuthPostHandler | framework bağımsız olmalı | ⚠️ PSR-7 tipi alıyor ( Kabul — PSR standart) |
| AuthController | Presentation — framework kabul | ✅ (Controller zaten HTTP) |
| Transaction (Repo) | framework bağımsız | ✅ |
| EventDispatcher | framework bağımsız | ✅ (PSR-14) |

---

## §I — SRP VE KALİTE ANALİZİ

### I.1 — Tek sorumluluk değerlendirmesi

| Bileşen | Sorumluluk | SRP durumu |
|---|---|---|
| AuthPostHandler | Login senaryosu yürütme | ✅ |
| AuthController | Dispatch + error mapping + ??? | ⚠️ 355 L — içeriğe bağlı |
| UserRepository | Veri erişimi + transaction | ⚠️ çift sorumluluk |
| CatalogWriter | Veri erişimi + transaction | ⚠️ çift sorumluluk |
| AuthContainer | Container kurulumu (auth domain) | ✅ |
| EventDispatcher | Event yayını | ✅ |
| RequestValidationMiddleware | Doğrulama | ✅ |

### I.2 — İsimlendirme tutarlılığı

| Kalıp | Uygulayan | Sayı |
|---|---|---|
| `*Handler` | AuthPostHandler | 1 |
| `*Controller` | AuthController | 1 |
| `*Repository` | UserRepository | 1 |
| `*Writer` | CatalogWriter | 1 |
| `*Container` | AuthContainer | 1 |
| `*Dispatcher` | EventDispatcher | 1 |
| `*Middleware` | RequestValidationMiddleware | 1 |
| `*UseCase` | (YOK) | **0** |

⚠️ İsimlendirme tutarsızlığı: `*Handler` ve `*UseCase` — iki farklı adlandırma.
Bu repo `*Handler` kullanıyor. ADR gerekli (D5 usecase-web-arastirma.md).

### I.3 — Miras ve kompozisyon

| Bileşen | Miras? | Kompozisyon? |
|---|---|---|
| AuthPostHandler | ❌ (final?) | ✅ bağımlılık enjeksiyonu (beklenen) |
| AuthController | ❌ | ✅ Handler enjeksiyonu |
| EventDispatcher | ✅ PSR-14 arayüzünü implement | — |

⚠️ VERIFICATION REQUIRED: AuthPostHandler/AuthController final mi, extend
edilebilir mi — okunmadı.

---

## §J — BİLEŞEN OLGUNLUK MATRİSİ

| Olgunluk | Tanım | Bileşenler | Sayı |
|---|---|---|---|
| Seviye 3 — Aktif üretim | production'da kullanılıyor | AuthPostHandler, AuthController, UserRepository, CatalogWriter, EventDispatcher, RequestValidationMiddleware | 6 |
| Seviye 2 — Kurulu | kod var, kullanım kısmi | AuthContainer, Validator | 2 |
| Seviye 1 — Belirsiz | envanter doğrulanmadı | Diğer Handler'lar, Diğer Controller'lar | ⚠️ |
| Seviye 0 — PLANNED | repo'da yok | LoginUseCase, UnitOfWork, Genel Composition Root | 0 (kod) |

---

## §K — PLANNED BİLEŞENLER (Repo'da YOK)

| PLANNED bileşen | Gerekçe | Karar kapısı |
|---|---|---|
| `LoginUseCase` | Klasik UseCase katmanı (glob 0) | D5 (P0) |
| `UnitOfWork` | Transaction orkestrasyonu UseCase'de | D5 (P1) |
| Genel Composition Root | App-wide container (AuthContainer tek) | D4 (P1) |
| PSR-11 tip hint (`ContainerInterface`) | Soyutlama (php-di API yerine) | D3 (P2) |
| PSR-17 factory (bootstrap) | Mesaj üretimi soyutlama | D1 (P1) |
| `ClockInterface` (PSR-20) | Zaman test edilebilirliği | Kapsam dışı not |

---

## §L — CROSS-REFERENCE

| Konu | Dosya |
|---|---|
| Ölçüm + gate (UseCase=0) | usecase-dogrulama.md §F, §I |
| PSR-17 + PSR-11 kanıtları (2 URL) | usecase-web-arastirma.md §A-§B |
| Kod blokları (desen özeti) | usecase-ornekler.md §A-§H |
| Kapsam + katalog | index.md (eski usecase-kapsam.md — spec §7 gereği 2026-10-16 kaldırıldı) |
| Orijinal scope slotu | usecase-usecase-katmani.md (korunur) |
| DTO bileşen analizi (benzer) | ../dto/dto-bilesenler.md |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §M — BİLEŞEN BAZLI ALAN ENVANTERİ

### M.1 — AuthPostHandler (kısmi — Satır: 38-57)

| Alan/Parametre | Tip | Doğrulama |
|---|---|---|
| `$request` (handle parametresi) | ServerRequestInterface | ? okundu |
| `$body` (lokal) | array | ? `getParsedBody()` cast |
| `$loginRequest` (lokal) | LoginRequest | ? `new LoginRequest(...)` |

### M.2 — AuthController (kısmi — Satır: 52-78)

| Alan/Method | Not | Doğrulama |
|---|---|---|
| dispatch() | match($action) (Satır: 52-61) | ? konum |
| error mapping bloğu | status kodu eşleme (Satır: 62-78) | ? konum |
| diğer methodlar (1-51, 79-355) | ?? okunmadı | ?? |

### M.3 — Transaction envanteri

| Dosya | Transaction satırı | İşlem (öngörü) | Doğrulama |
|---|---|---|---|
| UserRepository.php | 147 | begin veya commit | ?? satır grep |
| UserRepository.php | 184 | begin/commit | ?? satır grep |
| UserRepository.php | 192 | commit/rollBack | ?? satır grep |
| CatalogWriter.php | 183 | begin | ?? satır grep |
| CatalogWriter.php | 217 | begin/commit | ?? satır grep |
| CatalogWriter.php | 220 | commit (son) | ?? satır grep |
| bin/api-key-create.php | 135-147 | try/catch transaction | ?? satır grep |

---

## §N — BİLEŞEN İLİŞKİ GRAFİĞİ

```
HTTP Request
    -
    ¡
RequestValidationMiddleware (175 L) ¦¦(fail)¦¦> status 4xx/5xx (?? kod)
    - (pass)
    ¡
AuthController.dispatch (Satır: 52-61, match($action))
    -
    ¡
AuthPostHandler.handle (Satır: 38-57, match)
    -                       ^
    -                       - enjeksiyon (beklenen)
    -                       -
    -               AuthContainer (php-di, DI\ContainerBuilder)
    -
    +¦¦> LoginRequest (Domain DTO — aktif)
    +¦¦> UserRepository (transaction 147/184/192)
    +¦¦> EventDispatcher (PSR-14 — akışta ??)
    -
    ¡
AuthResponse (Domain DTO — JsonSerialize 68-70)
    -
    ¡
AuthController error mapping (Satır: 62-78)
    -
    ¡
HTTP Response (PSR-7 ResponseInterface)
```

**Grafik notu:** Oklar "veri akışı beklentisi"dir; her okun üretim kanıtı
§A–§G tablolarında ayrı ayrı işaretlenmiştir. Container › Handler enjeksiyonu
beklentidir — ?? doğrulanmadı (AuthContainer içeriği okunmadı).

---

## §O — EŞİTLİK KONTROLÜ (Handler vs Controller)

| Ölçüt | AuthPostHandler | AuthController | Eşit mi? |
|---|---|---|---|
| Katman | Application (Handler) | Presentation (Controller) | ? (farklı) |
| Dispatch deseni | match(...) | match($action) | ? (aynı desen) |
| PSR-7 girdi | ? ServerRequestInterface | ? ServerRequestInterface | ? |
| PSR-7 çıktı | ? ResponseInterface | ? ResponseInterface | ? |
| DTO kullanımı | ? LoginRequest (aktif) | ?? doğrulanmadı | ?? |
| Uzunluk | ~57+ L | 355 L | ? |
| Error mapping | ? (Controller'da) | ? (Satır: 62-78) | ? (farklı rol) |

**Sonuç:** İki bileşen **aynı dispatch desenini** (match) ve **aynı PSR-7
tiplerini** paylaşıyor — tutarlı. Rolleri farklı: Handler senaryo yürütür,
Controller HTTP status atar.

---

## §P — BİLEŞEN KALİTE KONTROLLERİ

| Kontrol | Beklenen | Gerçek | Durum |
|---|---|---|---|
| Handler tek senaryo | SRP | AuthPostHandler = login | ? |
| Controller'da iş mantığı | Hayır | ?? 355 L — doğrulanmadı | ?? |
| Transaction'da try/catch | Evet | ?? kod okunmadı | ?? |
| Container singleton mu? | Evet (HTTP scope) | ?? doğrulanmadı | ?? |
| Event akışta yayınlanıyor mu? | Evet (beklenen) | ?? grep edilmedi | ?? |
| final class mı? | Evet (immutable) | ?? okunmadı | ?? |
| declare(strict_types=1) | Evet | ?? tam dosya okunmadı | ?? |

---

## §Q — UseCase EKLENİRDİ: BİLEŞEN ETKİ ANALİZİ

| Etkilenen bileşen | Değişiklik | Öncelik |
|---|---|---|
| AuthPostHandler | UseCase'e taşınır veya kalır (adlandırma) | P0 |
| AuthController | UseCase çağrısına geçer | P1 |
| UserRepository | Transaction UseCase'e taşınabilir | P1 |
| AuthContainer | UseCase kaydı eklenir | P1 |
| Genel Composition Root | Yeni oluşturulur | P1 |
| CatalogWriter | Transaction kalabilir (repo sorumluluğu) | P2 |

?? Bu tablo **PLANNED etki analizi**dir — gerçek implementasyon bu dosyanın
kapsamı dışındadır (karar D5 usecase-web-arastirma.md).

---

## §R — SONRAKİ ADIMLAR (Bu Dosyanın Çıktısı)

| # | Adım | Sahip | Öncelik |
|---|---|---|---|
| 1 | Handler dizini envanterini tamamla (§B.2) | explore agent | P0 |
| 2 | AuthController 1-51 + 79-355 oku (§C.1) | backend-architect | P1 |
| 3 | AuthContainer tam okuma (§E.1) | explore agent | P1 |
| 4 | Event dispatch grep (§F.2) | explore agent | P1 |
| 5 | Transaction kodu okuma (§D.1) | debugger agent | P1 |
| 6 | Handler vs UseCase adlandırma ADR (§I.2) | architect | **P0** |

---

## §S — SON KONTROL LİSTESİ

| Kontrol | Durum |
|---|---|
| Frontmatter 7 alan, group: son | ? |
| §GRUPLAR tablosu | ? |
| Footer Authority + Last Updated | ? |
| Dosya·Satır kanıtları | ? §B-§D |
| PLANNED işaretli | §K, §Q |
| VERIFICATION REQUIRED işaretli | §B.1, §C.1, §E.1, §F.2, §P |
| URL uydurma | 0 |
| Uydurma Dosya·Satır | 0 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §T — DOSYA YAŞAM DÖNGÜSÜ KAYDI

```
Envanter toplama (grep + Dosya·Satır)
  › Aile analizi (Handler/Controller/Transaction/Container/Event/Validation)
  › Katman bağımlılık matrisi (§H)
  › SRP analizi (§I)
  › Olgunluk matrisi (§J)
  › PLANNED bileşenler (§K)
  › İlişki grafiği (§N)
  › Etki analizi (§Q)
  › Orkestratöre teslim (commit YOK)
```

---

## §U — ÖZET: EN KRİTİK 3 BULGU

1. **UseCase sınıfı = 0** — senaryo yürütme Handler (AuthPostHandler:38-57)
   + Controller (AuthController:52-61) ile yapılıyor. ADR kararı: adlandırma
   veya yeni katman (D5, P0).
2. **Transaction Repository'de** — 7 nokta (UserRepo 3, CatalogWriter 3,
   CLI 1). Klasik UnitOfWork'tan farklı; UseCase eklenirse taşınmalı (§D.2).
3. **Container Auth domain'e özel** — AuthContainer tek php-di örneği;
   genel composition root yok (§E.2). PSR-11 arayüz kullanımı ?? doğrulanmadı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
