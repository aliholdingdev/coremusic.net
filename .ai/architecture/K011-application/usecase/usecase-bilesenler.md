---
title: "K011 Â· usecase â€” BileÅŸenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 Â· usecase â€” BileÅŸenler (Envanter ve Mimari Analiz)

> **AmaÃ§:** UseCase/Application katmanÄ±nÄ±n bileÅŸen envanterini (Handler,
> Controller dispatch, Transaction, Container, Event), aile/hiyerarÅŸi
> yapÄ±sÄ±nÄ± ve katman baÄŸÄ±mlÄ±lÄ±klarÄ±nÄ± sunmak. Her iddia Dosya kanÄ±tlÄ±dÄ±r.

## Â§GRUPLAR

| BÃ¶lÃ¼m | Konu |
|---|---|
| Â§A | BileÅŸen aÄŸacÄ± |
| Â§B | Handler ailesi |
| Â§C | Controller ailesi |
| Â§D | Transaction ailesi |
| Â§E | Container ailesi (php-di / PSR-11) |
| Â§F | Event ailesi (PSR-14) |
| Â§G | Validation pipeline ailesi |
| Â§H | Katman baÄŸÄ±mlÄ±lÄ±k matrisi |
| Â§I | SRP ve kalite analizi |
| Â§J | BileÅŸen olgunluk matrisi |
| Â§K | PLANNED bileÅŸenler |
| Â§L | Cross-reference |

---

## Â§A â€” BÄ°LEÅžEN AÄžACI

```
UseCase / Application katmanÄ± bileÅŸenleri (de-fakto)
â”œâ”€â”€ Handler ailesi (senaryo yÃ¼rÃ¼tÃ¼cÃ¼ler)
â”‚   â””â”€â”€ AuthPostHandler                    [auth.coremusic.net/include/Handler/]
â”‚
â”œâ”€â”€ Controller ailesi (HTTP orkestratÃ¶rler)
â”‚   â””â”€â”€ AuthController (355 L)             [api.coremusic.net/include/Controller/]
â”‚
â”œâ”€â”€ Transaction ailesi (bÃ¼tÃ¼nlÃ¼k saÄŸlayÄ±cÄ±lar)
â”‚   â”œâ”€â”€ UserRepository (3 transaction)     [api.coremusic.net/include/Repository/]
â”‚   â”œâ”€â”€ CatalogWriter (3 transaction)      [api.coremusic.net/include/Repository/]
â”‚   â””â”€â”€ api-key-create (1 transaction)     [shared/bin/]
â”‚
â”œâ”€â”€ Container ailesi (baÄŸÄ±mlÄ±lÄ±k Ã§Ã¶zÃ¼cÃ¼)
â”‚   â””â”€â”€ AuthContainer (php-di)             [auth.coremusic.net/include/Container/]
â”‚
â”œâ”€â”€ Event ailesi (PSR-14)
â”‚   â””â”€â”€ EventDispatcher                    [shared/src/Events/]
â”‚
â”œâ”€â”€ Validation pipeline ailesi (middleware)
â”‚   â”œâ”€â”€ RequestValidationMiddleware (175 L) [shared/src/Api/Middleware/]
â”‚   â””â”€â”€ Validator                          [shared/src/Api/Validation/]
â”‚
â””â”€â”€ UseCase ailesi (KLASÄ°K ADLANDIRMA)
    â””â”€â”€ (YOK â€” glob *UseCase*.php = 0 dosya) âš ï¸ PLANNED
```

---

## Â§B â€” HANDLER AÄ°LESÄ°

### B.1 â€” AuthPostHandler

| Ã–zellik | DeÄŸer | KanÄ±t |
|---|---|---|
| Dosya | auth.coremusic.net/include/Handler/AuthPostHandler.php | envanter |
| Senaryo | Login | Â§A.3 usecase-kapsam.md |
| Dispatch | `match(...)` (SatÄ±r: 38-57) | grep |
| Girdi | `ServerRequestInterface` (PSR-7) | SatÄ±r: 38-57 |
| Ã‡Ä±ktÄ± | `ResponseInterface` (PSR-7) | SatÄ±r: 38-57 |
| DTO kullanÄ±mÄ± | `new LoginRequest(...)` â€” AKTÄ°F Ã¼retim | SatÄ±r: 38-57 |
| SRP | âœ… tek senaryo (login) | â€” |

âš ï¸ VERIFICATION REQUIRED: `auth.coremusic.net/include/Handler/` dizininde
baÅŸka handler dosyalarÄ± var mÄ± â€” bu gÃ¶rev kapsamÄ±nda listelenmedi.

### B.2 â€” Handler ailesi envanter durumu

| Dosya | Durum |
|---|---|
| AuthPostHandler.php | âœ… doÄŸrulandÄ± |
| DiÄŸer handler'lar | âš ï¸ VERIFICATION REQUIRED |

---

## Â§C â€” CONTROLLER AÄ°LESÄ°

### C.1 â€” AuthController

| Ã–zellik | DeÄŸer | KanÄ±t |
|---|---|---|
| Dosya | api.coremusic.net/include/Controller/AuthController.php | envanter |
| Toplam satÄ±r | 355 | DosyaÂ·SatÄ±r Ã¶lÃ§Ã¼mÃ¼ |
| Dispatch | `match($action)` (SatÄ±r: 52-61) | grep |
| Error mapping | SatÄ±r: 62-78 | grep |
| Girdi | `ServerRequestInterface` | Â§A usecase-ornekler.md |
| Ã‡Ä±ktÄ± | `ResponseInterface` | Â§A usecase-ornekler.md |
| SRP | âš ï¸ 355 satÄ±r â€” iÅŸ mantÄ±ÄŸÄ± olabilir | usecase-dogrulama.md Â§K |

### C.2 â€” Controller rolÃ¼ (katman analizi)

| Katman | Controller sorumluluÄŸu |
|---|---|
| Presentation | HTTP kabuÄŸu (doÄŸru) |
| Application | Dispatch orkestrasyonu (match) |
| Error mapping | Status kodu atama (SatÄ±r: 62-78) |

**GÃ¶zlem:** Controller, Handler'a dispatch yaptÄ±ÄŸÄ± iÃ§in **Application katmanÄ±nÄ±n
giriÅŸ kapÄ±sÄ±** olarak Ã§alÄ±ÅŸÄ±yor. Fakat 355 satÄ±r, saf Controller'dan fazlasÄ±nÄ±
iÃ§erebilir â€” âš ï¸ VERIFICATION REQUIRED (dosya tam okunmadÄ±).

---

## Â§D â€” TRANSACTION AÄ°LESÄ°

### D.1 â€” Transaction envanteri

| BileÅŸen | Dosya | Transaction satÄ±rlarÄ± | Adet |
|---|---|---|---|
| UserRepository | api.../Repository/UserRepository.php | 147, 184, 192 | 3 |
| CatalogWriter | api.../Repository/CatalogWriter.php | 183, 217, 220 | 3 |
| api-key-create | shared/bin/api-key-create.php | 135-147 | 1 |
| **TOPLAM** | â€” | â€” | **7** |

### D.2 â€” Transaction sahibi analizi

| Desen | Klasik | Bu repo |
|---|---|---|
| Transaction sahibi | UseCase / UnitOfWork | **Repository** |
| UseCase rolÃ¼ | transaction orkestrasyonu | (dosya yok) |
| Repository rolÃ¼ | veri eriÅŸimi | veri eriÅŸimi + bÃ¼tÃ¼nlÃ¼k |

âš ï¸ **MÄ°MARÄ° BULGU:** Repository Ã§ift sorumluluk (SRP riski).
UseCase eklenirse transaction orkestrasyonu taÅŸÄ±nmalÄ± â€” ADR kararÄ±.

---

## Â§E â€” CONTAINER AÄ°LESÄ° (php-di / PSR-11)

### E.1 â€” AuthContainer

| Ã–zellik | DeÄŸer | KanÄ±t |
|---|---|---|
| Dosya | auth.coremusic.net/include/Container/AuthContainer.php | envanter |
| KÃ¼tÃ¼phane | php-di (`DI\ContainerBuilder`) | grep |
| PSR-11 arayÃ¼zÃ¼ | âš ï¸ VERIFICATION REQUIRED | grep edilmedi |
| Scope | Auth domain'e Ã¶zel | â€” |
| Compile cache | âš ï¸ VERIFICATION REQUIRED | okunmadÄ± |
| Singleton kayÄ±tlar | âš ï¸ VERIFICATION REQUIRED | okunmadÄ± |

### E.2 â€” Container bileÅŸen analizi

| Soru | Cevap | Durum |
|---|---|---|
| Genel (app-wide) composition root var mÄ±? | AuthContainer tek Ã¶rnek | âš ï¸ eksik olabilir |
| Container test ediliyor mu? | âš ï¸ doÄŸrulanmadÄ± | â€” |
| PSR-11 `get`/`has` kullanÄ±mÄ± | âš ï¸ grep edilmedi | â€” |

---

## Â§F â€” EVENT AÄ°LESÄ° (PSR-14)

### F.1 â€” EventDispatcher

| Ã–zellik | DeÄŸer | KanÄ±t |
|---|---|---|
| Dosya | shared/src/Events/EventDispatcher.php | envanter |
| ArayÃ¼z | `EventDispatcherInterface` (PSR-14) | grep |
| dispatch kullanÄ±mÄ± (use-case akÄ±ÅŸta) | âš ï¸ VERIFICATION REQUIRED | grep edilmedi |

### F.2 â€” Event bileÅŸen analizi

| Soru | Cevap | Durum |
|---|---|---|
| Event'ler yayÄ±nlanÄ±yor mu use-case akÄ±ÅŸta? | âš ï¸ doÄŸrulanmadÄ± | `grep "->dispatch("` gerekli |
| Listener kayÄ±t mekanizmasÄ± | âš ï¸ doÄŸrulanmadÄ± | dosya okunmadÄ± |
| Event sÄ±nÄ±f envanteri | Events/ = 9 PHP dosyasÄ± | âš ï¸ detaylÄ± envanter |

---

## Â§G â€” VALIDATION PIPELINE AÄ°LESÄ°

### G.1 â€” Middleware + Validator

| BileÅŸen | Dosya | Uzunluk | Rol |
|---|---|---|---|
| RequestValidationMiddleware | shared/src/Api/Middleware/RequestValidationMiddleware.php | 175 L | HTTP doÄŸrulama |
| Validator | shared/src/Api/Validation/Validator.php | âš ï¸ Ã¶lÃ§Ã¼lmedi | doÄŸrulama motoru |
| Route rules | api.coremusic.net/config/routes.php | âš ï¸ Ã¶lÃ§Ã¼lmedi | kural seti |

### G.2 â€” Pipeline iliÅŸkisi

```
routes.php (rule set)
    â†’ RequestValidationMiddleware (175 L)
        â†’ Validator
            â†’ pass â†’ Controller dispatch (AuthController:52-61)
            â†’ fail â†’ status kodu (âš ï¸ 415/400/422 â€” doÄŸrulanmadÄ±)
```

âš ï¸ VERIFICATION REQUIRED: Middleware'Ä±n status kodu seÃ§imi ve Validator'Ä±n
iÃ§ yapÄ±sÄ± bu gÃ¶revde okunmadÄ±.

---

## Â§H â€” KATMAN BAÄžIMLILIK MATRÄ°SÄ°

| BileÅŸen | BaÄŸÄ±mlÄ±lÄ±klar (kod) | DÄ±ÅŸ paket | Framework baÄŸÄ±mlÄ±lÄ±ÄŸÄ± |
|---|---|---|---|
| AuthPostHandler | PSR-7 tipleri, LoginRequest | psr/http-message (transitif) | âš ï¸ Response Ã¼retimi (factory belirsiz) |
| AuthController | PSR-7 tipleri, Handler | psr/http-message | âš ï¸ (Presentation katmanÄ± â€” framework kabul) |
| UserRepository | DatabaseManager (DB) | â€” | âŒ |
| CatalogWriter | DatabaseManager (DB) | â€” | âŒ |
| AuthContainer | php-di | php-di/php-di | âŒ (container kÃ¼tÃ¼phanesi) |
| EventDispatcher | PSR-14 | psr/event-dispatcher | âŒ |
| RequestValidationMiddleware | Validator | â€” | âš ï¸ (Middleware â€” framework kabul) |

**Kural uyumu (AGENTS Â§5 â€” Application framework baÄŸÄ±msÄ±z):**

| BileÅŸen | Kural | Durum |
|---|---|---|
| AuthPostHandler | framework baÄŸÄ±msÄ±z olmalÄ± | âš ï¸ PSR-7 tipi alÄ±yor ( Kabul â€” PSR standart) |
| AuthController | Presentation â€” framework kabul | âœ… (Controller zaten HTTP) |
| Transaction (Repo) | framework baÄŸÄ±msÄ±z | âœ… |
| EventDispatcher | framework baÄŸÄ±msÄ±z | âœ… (PSR-14) |

---

## Â§I â€” SRP VE KALÄ°TE ANALÄ°ZÄ°

### I.1 â€” Tek sorumluluk deÄŸerlendirmesi

| BileÅŸen | Sorumluluk | SRP durumu |
|---|---|---|
| AuthPostHandler | Login senaryosu yÃ¼rÃ¼tme | âœ… |
| AuthController | Dispatch + error mapping + ??? | âš ï¸ 355 L â€” iÃ§eriÄŸe baÄŸlÄ± |
| UserRepository | Veri eriÅŸimi + transaction | âš ï¸ Ã§ift sorumluluk |
| CatalogWriter | Veri eriÅŸimi + transaction | âš ï¸ Ã§ift sorumluluk |
| AuthContainer | Container kurulumu (auth domain) | âœ… |
| EventDispatcher | Event yayÄ±nÄ± | âœ… |
| RequestValidationMiddleware | DoÄŸrulama | âœ… |

### I.2 â€” Ä°simlendirme tutarlÄ±lÄ±ÄŸÄ±

| KalÄ±p | Uygulayan | SayÄ± |
|---|---|---|
| `*Handler` | AuthPostHandler | 1 |
| `*Controller` | AuthController | 1 |
| `*Repository` | UserRepository | 1 |
| `*Writer` | CatalogWriter | 1 |
| `*Container` | AuthContainer | 1 |
| `*Dispatcher` | EventDispatcher | 1 |
| `*Middleware` | RequestValidationMiddleware | 1 |
| `*UseCase` | (YOK) | **0** |

âš ï¸ Ä°simlendirme tutarsÄ±zlÄ±ÄŸÄ±: `*Handler` ve `*UseCase` â€” iki farklÄ± adlandÄ±rma.
Bu repo `*Handler` kullanÄ±yor. ADR gerekli (D5 usecase-web-arastirma.md).

### I.3 â€” Miras ve kompozisyon

| BileÅŸen | Miras? | Kompozisyon? |
|---|---|---|
| AuthPostHandler | âŒ (final?) | âœ… baÄŸÄ±mlÄ±lÄ±k enjeksiyonu (beklenen) |
| AuthController | âŒ | âœ… Handler enjeksiyonu |
| EventDispatcher | âœ… PSR-14 arayÃ¼zÃ¼nÃ¼ implement | â€” |

âš ï¸ VERIFICATION REQUIRED: AuthPostHandler/AuthController final mi, extend
edilebilir mi â€” okunmadÄ±.

---

## Â§J â€” BÄ°LEÅžEN OLGUNLUK MATRÄ°SÄ°

| Olgunluk | TanÄ±m | BileÅŸenler | SayÄ± |
|---|---|---|---|
| Seviye 3 â€” Aktif Ã¼retim | production'da kullanÄ±lÄ±yor | AuthPostHandler, AuthController, UserRepository, CatalogWriter, EventDispatcher, RequestValidationMiddleware | 6 |
| Seviye 2 â€” Kurulu | kod var, kullanÄ±m kÄ±smi | AuthContainer, Validator | 2 |
| Seviye 1 â€” Belirsiz | envanter doÄŸrulanmadÄ± | DiÄŸer Handler'lar, DiÄŸer Controller'lar | âš ï¸ |
| Seviye 0 â€” PLANNED | repo'da yok | LoginUseCase, UnitOfWork, Genel Composition Root | 0 (kod) |

---

## Â§K â€” PLANNED BÄ°LEÅžENLER (Repo'da YOK)

| PLANNED bileÅŸen | GerekÃ§e | Karar kapÄ±sÄ± |
|---|---|---|
| `LoginUseCase` | Klasik UseCase katmanÄ± (glob 0) | D5 (P0) |
| `UnitOfWork` | Transaction orkestrasyonu UseCase'de | D5 (P1) |
| Genel Composition Root | App-wide container (AuthContainer tek) | D4 (P1) |
| PSR-11 tip hint (`ContainerInterface`) | Soyutlama (php-di API yerine) | D3 (P2) |
| PSR-17 factory (bootstrap) | Mesaj Ã¼retimi soyutlama | D1 (P1) |
| `ClockInterface` (PSR-20) | Zaman test edilebilirliÄŸi | Kapsam dÄ±ÅŸÄ± not |

---

## Â§L â€” CROSS-REFERENCE

| Konu | Dosya |
|---|---|
| Ã–lÃ§Ã¼m + gate (UseCase=0) | usecase-dogrulama.md Â§F, Â§I |
| PSR-17 + PSR-11 kanÄ±tlarÄ± (2 URL) | usecase-web-arastirma.md Â§A-Â§B |
| Kod bloklarÄ± (desen Ã¶zeti) | usecase-ornekler.md Â§A-Â§H |
| Kapsam + katalog | usecase-kapsam.md, index.md |
| Orijinal scope slotu | usecase-usecase-katmani.md (korunur) |
| DTO bileÅŸen analizi (benzer) | ../dto/dto-bilesenler.md |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §M — BÝLEÞEN BAZLI ALAN ENVANTERÝ

### M.1 — AuthPostHandler (kýsmi — Satýr: 38-57)

| Alan/Parametre | Tip | Doðrulama |
|---|---|---|
| `$request` (handle parametresi) | ServerRequestInterface | ? okundu |
| `$body` (lokal) | array | ? `getParsedBody()` cast |
| `$loginRequest` (lokal) | LoginRequest | ? `new LoginRequest(...)` |

### M.2 — AuthController (kýsmi — Satýr: 52-78)

| Alan/Method | Not | Doðrulama |
|---|---|---|
| dispatch() | match($action) (Satýr: 52-61) | ? konum |
| error mapping bloðu | status kodu eþleme (Satýr: 62-78) | ? konum |
| diðer methodlar (1-51, 79-355) | ?? okunmadý | ?? |

### M.3 — Transaction envanteri

| Dosya | Transaction satýrý | Ýþlem (öngörü) | Doðrulama |
|---|---|---|---|
| UserRepository.php | 147 | begin veya commit | ?? satýr grep |
| UserRepository.php | 184 | begin/commit | ?? satýr grep |
| UserRepository.php | 192 | commit/rollBack | ?? satýr grep |
| CatalogWriter.php | 183 | begin | ?? satýr grep |
| CatalogWriter.php | 217 | begin/commit | ?? satýr grep |
| CatalogWriter.php | 220 | commit (son) | ?? satýr grep |
| bin/api-key-create.php | 135-147 | try/catch transaction | ?? satýr grep |

---

## §N — BÝLEÞEN ÝLÝÞKÝ GRAFÝÐÝ

```
HTTP Request
    -
    ¡
RequestValidationMiddleware (175 L) ¦¦(fail)¦¦> status 4xx/5xx (?? kod)
    - (pass)
    ¡
AuthController.dispatch (Satýr: 52-61, match($action))
    -
    ¡
AuthPostHandler.handle (Satýr: 38-57, match)
    -                       ^
    -                       - enjeksiyon (beklenen)
    -                       -
    -               AuthContainer (php-di, DI\ContainerBuilder)
    -
    +¦¦> LoginRequest (Domain DTO — aktif)
    +¦¦> UserRepository (transaction 147/184/192)
    +¦¦> EventDispatcher (PSR-14 — akýþta ??)
    -
    ¡
AuthResponse (Domain DTO — JsonSerialize 68-70)
    -
    ¡
AuthController error mapping (Satýr: 62-78)
    -
    ¡
HTTP Response (PSR-7 ResponseInterface)
```

**Grafik notu:** Oklar "veri akýþý beklentisi"dir; her okun üretim kanýtý
§A–§G tablolarýnda ayrý ayrý iþaretlenmiþtir. Container › Handler enjeksiyonu
beklentidir — ?? doðrulanmadý (AuthContainer içeriði okunmadý).

---

## §O — EÞÝTLÝK KONTROLÜ (Handler vs Controller)

| Ölçüt | AuthPostHandler | AuthController | Eþit mi? |
|---|---|---|---|
| Katman | Application (Handler) | Presentation (Controller) | ? (farklý) |
| Dispatch deseni | match(...) | match($action) | ? (ayný desen) |
| PSR-7 girdi | ? ServerRequestInterface | ? ServerRequestInterface | ? |
| PSR-7 çýktý | ? ResponseInterface | ? ResponseInterface | ? |
| DTO kullanýmý | ? LoginRequest (aktif) | ?? doðrulanmadý | ?? |
| Uzunluk | ~57+ L | 355 L | ? |
| Error mapping | ? (Controller'da) | ? (Satýr: 62-78) | ? (farklý rol) |

**Sonuç:** Ýki bileþen **ayný dispatch desenini** (match) ve **ayný PSR-7
tiplerini** paylaþýyor — tutarlý. Rolleri farklý: Handler senaryo yürütür,
Controller HTTP status atar.

---

## §P — BÝLEÞEN KALÝTE KONTROLLERÝ

| Kontrol | Beklenen | Gerçek | Durum |
|---|---|---|---|
| Handler tek senaryo | SRP | AuthPostHandler = login | ? |
| Controller'da iþ mantýðý | Hayýr | ?? 355 L — doðrulanmadý | ?? |
| Transaction'da try/catch | Evet | ?? kod okunmadý | ?? |
| Container singleton mu? | Evet (HTTP scope) | ?? doðrulanmadý | ?? |
| Event akýþta yayýnlanýyor mu? | Evet (beklenen) | ?? grep edilmedi | ?? |
| final class mý? | Evet (immutable) | ?? okunmadý | ?? |
| declare(strict_types=1) | Evet | ?? tam dosya okunmadý | ?? |

---

## §Q — UseCase EKLENÝRDÝ: BÝLEÞEN ETKÝ ANALÝZÝ

| Etkilenen bileþen | Deðiþiklik | Öncelik |
|---|---|---|
| AuthPostHandler | UseCase'e taþýnýr veya kalýr (adlandýrma) | P0 |
| AuthController | UseCase çaðrýsýna geçer | P1 |
| UserRepository | Transaction UseCase'e taþýnabilir | P1 |
| AuthContainer | UseCase kaydý eklenir | P1 |
| Genel Composition Root | Yeni oluþturulur | P1 |
| CatalogWriter | Transaction kalabilir (repo sorumluluðu) | P2 |

?? Bu tablo **PLANNED etki analizi**dir — gerçek implementasyon bu dosyanýn
kapsamý dýþýndadýr (karar D5 usecase-web-arastirma.md).

---

## §R — SONRAKÝ ADIMLAR (Bu Dosyanýn Çýktýsý)

| # | Adým | Sahip | Öncelik |
|---|---|---|---|
| 1 | Handler dizini envanterini tamamla (§B.2) | explore agent | P0 |
| 2 | AuthController 1-51 + 79-355 oku (§C.1) | backend-architect | P1 |
| 3 | AuthContainer tam okuma (§E.1) | explore agent | P1 |
| 4 | Event dispatch grep (§F.2) | explore agent | P1 |
| 5 | Transaction kodu okuma (§D.1) | debugger agent | P1 |
| 6 | Handler vs UseCase adlandýrma ADR (§I.2) | architect | **P0** |

---

## §S — SON KONTROL LÝSTESÝ

| Kontrol | Durum |
|---|---|
| Frontmatter 7 alan, group: son | ? |
| §GRUPLAR tablosu | ? |
| Footer Authority + Last Updated | ? |
| Dosya·Satýr kanýtlarý | ? §B-§D |
| PLANNED iþaretli | §K, §Q |
| VERIFICATION REQUIRED iþaretli | §B.1, §C.1, §E.1, §F.2, §P |
| URL uydurma | 0 |
| Uydurma Dosya·Satýr | 0 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §T — DOSYA YAÞAM DÖNGÜSÜ KAYDI

```
Envanter toplama (grep + Dosya·Satýr)
  › Aile analizi (Handler/Controller/Transaction/Container/Event/Validation)
  › Katman baðýmlýlýk matrisi (§H)
  › SRP analizi (§I)
  › Olgunluk matrisi (§J)
  › PLANNED bileþenler (§K)
  › Ýliþki grafiði (§N)
  › Etki analizi (§Q)
  › Orkestratöre teslim (commit YOK)
```

---

## §U — ÖZET: EN KRÝTÝK 3 BULGU

1. **UseCase sýnýfý = 0** — senaryo yürütme Handler (AuthPostHandler:38-57)
   + Controller (AuthController:52-61) ile yapýlýyor. ADR kararý: adlandýrma
   veya yeni katman (D5, P0).
2. **Transaction Repository'de** — 7 nokta (UserRepo 3, CatalogWriter 3,
   CLI 1). Klasik UnitOfWork'tan farklý; UseCase eklenirse taþýnmalý (§D.2).
3. **Container Auth domain'e özel** — AuthContainer tek php-di örneði;
   genel composition root yok (§E.2). PSR-11 arayüz kullanýmý ?? doðrulanmadý.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
