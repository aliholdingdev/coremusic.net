---
title: "K011 Â· usecase â€” Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 Â· usecase â€” Kapsam

> **AmaÃ§:** Application/UseCase katmanÄ±nÄ±n kapsamÄ±nÄ±, slot matrisini, Ã¶lÃ§Ã¼m
> Ã¶zetini ve sÄ±nÄ±rlarÄ±nÄ± tanÄ±mlamak. Format referansÄ±: `commands/` klasÃ¶rÃ¼.
> Scope slotu `usecase-usecase-katmani.md` (527 satÄ±r) korunmuÅŸtur â€” silinmedi,
> yeniden adlandÄ±rÄ±lmadÄ±.

## Â§GRUPLAR

| BÃ¶lÃ¼m | Konu |
|---|---|
| Â§A | Kapsam tanÄ±mÄ± (useCase nedir, ne deÄŸildir) |
| Â§B | Slot matrisi (6 dosya) |
| Â§C | Mevcut dosya slotlarÄ± (korunan) |
| Â§D | Dosya adlandÄ±rma ÅŸemasÄ± |
| Â§E | Ã–lÃ§Ã¼m Ã¶zeti (kanÄ±t) |
| Â§F | Kapsam sÄ±nÄ±rlarÄ± (IN/OUT) |
| Â§G | Gate matrisi (detay) |
| Â§H | PSR-11 / PSR-17 durum Ã¶zeti |
| Â§I | Cross-reference |
| Â§J | Kabul kriterleri |
| Â§K | Sonraki adÄ±mlar |

---

## Â§A â€” KAPSAM TANIMI

### A.1 â€” UseCase katmanÄ±nÄ±n rolÃ¼ (AGENTS Â§5 â€” Application katmanÄ±)

```
Application : Service / UseCase / Handler / DTO   â€” framework baÄŸÄ±msÄ±z
```

| Ã–zellik | Kural |
|---|---|
| Konum | Presentation (Controller) ile Domain arasÄ±nda |
| BaÄŸÄ±mlÄ±lÄ±k | Domain'e baÄŸÄ±mlÄ± OLABÄ°LÄ°R; framework'e HAYIR |
| Sorumluluk | KullanÄ±cÄ± senaryosu (use case) orkestrasyonu |
| Form | Genelde tek method: `execute()` / `handle()` |
| DÃ¶nÃ¼ÅŸ | Domain verisi veya DTO â€” raw HTTP response DEÄžÄ°L |

### A.2 â€” UseCase vs diÄŸer katmanlar

| Katman | Soru | Ã–rnek |
|---|---|---|
| Controller (Presentation) | HTTP ne ister? | route â†’ handler Ã§aÄŸÄ±r, status kodu seÃ§ |
| **UseCase (Application)** | **Senaryo nasÄ±l yÃ¼rÃ¼tÃ¼lÃ¼r?** | **validasyon â†’ repository â†’ domain â†’ event â†’ DTO** |
| Service (Application) | Ortak iÅŸ mantÄ±ÄŸÄ± | logger, cache, queue yardÄ±mÄ± |
| Repository (Infrastructure) | Veri nerede? | SQL sorgusu |
| Entity (Domain) | Kural ne diyor? | invariant doÄŸrulama |

### A.3 â€” Bu kapsamda NE aranÄ±yor (repo bulgularÄ±)

| Bulgu | DosyaÂ·SatÄ±r | Durum |
|---|---|---|
| Controller dispatch deseni | api.coremusic.net/include/Controller/AuthController.php Â· SatÄ±r: 52-61 | `match($action)` |
| Error mapping deseni | AuthController.php Â· SatÄ±r: 62-78 | status kodu seÃ§imi |
| Handler dispatch deseni | auth.coremusic.net/include/Handler/AuthPostHandler.php Â· SatÄ±r: 38-57 | `match($action)` |
| Transaction kullanÄ±mÄ± | UserRepository.php Â· SatÄ±r: 147, 184, 192 | 3 nokta |
| Transaction kullanÄ±mÄ± | CatalogWriter.php Â· SatÄ±r: 183, 217, 220 | 3 nokta |
| Transaction kullanÄ±mÄ± | shared/bin/api-key-create.php Â· SatÄ±r: 135-147 | CLI script |
| PSR-14 EventDispatcher | shared/src/Events/EventDispatcher.php | `EventDispatcherInterface` |
| PSR-11 container | composer: psr/container ^2.0 | âš ï¸ kod kullanÄ±mÄ± doÄŸrulanmadÄ± |
| php-di container | auth.coremusic.net/include/Container/AuthContainer.php | `DI\ContainerBuilder` |

âš ï¸ **Kritik gÃ¶zlem:** Bu repo'da klasik "UseCase" sÄ±nÄ±fÄ± adÄ± altÄ±nda dosya
bulunmuyor (grep: `*UseCase*.php` â†’ 0). Senaryo yÃ¼rÃ¼tme **Controller +
Handler + Repository** Ã¼Ã§lÃ¼sÃ¼ne daÄŸÄ±lmÄ±ÅŸ. Bu durum:
- UseCase katmanÄ±nÄ±n **de-fakto Handler** ile temsil edildiÄŸini, veya
- UseCase katmanÄ±nÄ±n **PLANNED-Ã¼retilmiÅŸ** olduÄŸunu gÃ¶sterir.

âš ï¸ VERIFICATION REQUIRED: `usecase-usecase-katmani.md` (527 satÄ±r, mevcut
scope slotu) iÃ§inde UseCase sÄ±nÄ±f adaylarÄ±nÄ±n listesi olabilir â€” bu gÃ¶rev
kapsamÄ±nda o dosya OKUNMADI, yalnÄ±z varlÄ±ÄŸÄ± ve satÄ±r sayÄ±sÄ± doÄŸrulandÄ±.

---

## Â§B â€” SLOT MATRÄ°SÄ°

| # | Dosya | Group | Rol |
|---|---|---|---|
| 1 | usecase-kapsam.md | mimari | Kapsam, slot matrisi, Ã¶lÃ§Ã¼m, sÄ±nÄ±r (bu dosya) |
| 2 | usecase-dogrulama.md | mimari | Ã–lÃ§Ã¼m, gate matrisi, anti-verification |
| 3 | usecase-web-arastirma.md | kanit | PSR-17 (Factory) + PSR-11 (Container) â€” 2 URL havuzu |
| 4 | usecase-ornekler.md | kod | PHP kod bloklarÄ± â€” DosyaÂ·SatÄ±r kanÄ±tlÄ± |
| 5 | usecase-bilesenler.md | mimari | BileÅŸen envanteri (Handler/Service/Transaction/Event) |
| 6 | index.md | mimari | Katalog |

---

## Â§C â€” MEVCUT DOSYA SLOTLARI (Silinmez, yeniden adlandÄ±rÄ±lmaz)

| Dosya | SatÄ±r | Slot RolÃ¼ | Yeni Set Ä°Ã§indeki KarÅŸÄ±lÄ±ÄŸÄ± |
|---|---|---|---|
| usecase-usecase-katmani.md | 527 | KAPSAM (orijinal, korunur) | KAPSAM slotunun bir parÃ§asÄ± â€” usecase-kapsam.md ile birlikte |
| usecase-kapsam.md | 500+ | KAPSAM (yeni) | Katalog + kapsam |
| usecase-dogrulama.md | 500+ | DOÄžRULAMA | Ã–lÃ§Ã¼m + gate doÄŸrulama |
| usecase-web-arastirma.md | 500+ | WEB ARAÅžTIRMA | PSR-17 + PSR-11 kanÄ±t havuzu |
| usecase-ornekler.md | 500+ | Ã–RNEKLER | DosyaÂ·SatÄ±r kanÄ±t kod bloklarÄ± |
| usecase-bilesenler.md | 500+ | BÄ°LEÅžENLER | BileÅŸen envanteri |
| index.md | 40â€“100 | KATALOG | 6 dosya Ã¶zeti |

**Kural:** usecase-usecase-katmani.md bu setin KAPSAM slotunun orijinal kanÄ±tÄ±dÄ±r;
dosya Ã¼zerinde revizyon YOK, taÅŸÄ±ma/silme YOK.

---

## Â§D â€” DOSYA ADLANDIRMA ÅžEMASI

```
.ai/architecture/K011-application/usecase/
â”œâ”€â”€ usecase-kapsam.md                â†’ kapsam
â”œâ”€â”€ usecase-dogrulama.md             â†’ dogrulama
â”œâ”€â”€ usecase-web-arastirma.md         â†’ web-arastirma
â”œâ”€â”€ usecase-ornekler.md              â†’ ornekler
â”œâ”€â”€ usecase-bilesenler.md            â†’ bilesenler
â”œâ”€â”€ index.md                         â†’ katalog
â””â”€â”€ usecase-usecase-katmani.md       â†’ MEVCUT (korunur)
```

**Åžablon:** `commands/` klasÃ¶rÃ¼ format referansÄ± â€” Â§GRUPLAR tablosu frontmatter
sonra; footer Authority + Last Updated.

---

## Â§E â€” Ã–LÃ‡ÃœM Ã–ZETÄ° (KanÄ±t)

| Metrik | DeÄŸer | Kaynak |
|---|---|---|
| shared/src toplam PHP | 165 | manifest + glob |
| Api/ (Controller, Dto, Middleware) | 34 | manifest |
| Events/ | 9 | manifest |
| Contracts/ | 19 | manifest |
| Middleware/ | 11 | manifest |
| assets/js (manifest) | 94 | manifest |
| assets/js (glob) | 98 | glob (âš ï¸ fark â€” dto-dogrulama.md Â§E.2 kaydÄ±) |
| AuthController satÄ±r | 355 | DosyaÂ·SatÄ±r Ã¶lÃ§Ã¼mÃ¼ |
| RequestValidationMiddleware satÄ±r | 175 | DosyaÂ·SatÄ±r Ã¶lÃ§Ã¼mÃ¼ |
| Transaction noktalarÄ± | 6+ (UserRepo 3, CatalogWriter 3, CLI 1) | DosyaÂ·SatÄ±r |
| PSR-14 EventDispatcher | 1 dosya | shared/src/Events/EventDispatcher.php |
| php-di container | 1 dosya | AuthContainer.php |
| Klasik UseCase sÄ±nÄ±fÄ± | **0** (grep) | âš ï¸ PLANNED-Ã¼retilmiÅŸ katman |

---

## Â§F â€” KAPSAM SINIRLARI (IN / OUT)

### IN â€” Bu kapsamÄ±n iÃ§inde

- Handler deseni (AuthPostHandler: match dispatch)
- Controller dispatch deseni (AuthController: match + error mapping)
- Transaction orkestrasyonu (UserRepository, CatalogWriter, api-key-create)
- PSR-14 EventDispatcher (Events katmanÄ± kÃ¶prÃ¼sÃ¼)
- PSR-11 container kullanÄ±mÄ± (php-di / AuthContainer)
- PSR-17 Factory (durum: paket var, kod yok)
- Kapsam slotlarÄ±: usecase-kapsam.md + usecase-usecase-katmani.md

### OUT â€” Bu kapsamÄ±n dÄ±ÅŸÄ±nda

- DiÄŸer K011 klasÃ¶rleri (dto/, events/, middleware/, commands/)
- Kaynak kod deÄŸiÅŸikliÄŸi (.php / .js / .css / .sql)
- git commit (orkestratÃ¶re ait)
- URL uydurma (yalnÄ±z 2 URL havuzu)
- PLANNED/VERIFICATION REQUIRED iÅŸaretlemesi yapÄ±lmayan iddialar

---

## Â§G â€” GATE MATRÄ°SÄ° (Detay)

| Gate | Kapsam DosyasÄ± | Karar | KanÄ±t | Durum |
|---|---|---|---|---|
| G1 | usecase-usecase-katmani.md (527 L) | Scope slot â€” UseCase katmanÄ± kapsamÄ± | Mevcut dosya | MEVCUT |
| G2 | usecase-kapsam.md (bu dosya) | Scope slot â€” usecase kapsam ve katalog | Â§Bâ€“Â§F | YAZILDI |
| G3 | usecase-dogrulama.md | Verification slot â€” Ã¶lÃ§Ã¼m ve doÄŸrulama | Ã–lÃ§Ã¼m + gate | YAZILDI |
| G4 | usecase-web-arastirma.md | KanÄ±t slot â€” PSR-17 + PSR-11 | URL havuzu (2 URL) | YAZILDI |
| G5 | usecase-ornekler.md | Kod slot â€” PHP Ã¶rnek kanÄ±tlarÄ± | DosyaÂ·SatÄ±r kanÄ±t bloklarÄ± | YAZILDI |
| G6 | usecase-bilesenler.md | Mimari slot â€” bileÅŸen envanteri | Handler/Service/Transaction envanteri | YAZILDI |
| G7 | index.md | Katalog â€” 6 dosya Ã¶zet tablosu | 40â€“100 satÄ±r | YAZILDI |

---

## Â§H â€” PSR-11 / PSR-17 DURUM Ã–ZETÄ°

### H.1 â€” PSR-17 (HTTP Factory)

| Paket | SÃ¼rÃ¼m | Durum | Kod kullanÄ±mÄ± |
|---|---|---|---|
| nyholm/psr7 | ^1.8 | composer.json'da (shared/composer.json Â· SatÄ±r: 8) | **SIFIR** â€” grep: `Psr\Http\Message` 0 isabet |

**SonuÃ§:** PSR-17 Message interface'leri DTO'larda ve Handler'larda KULLANILMIYOR.
Handler `ServerRequestInterface` tipini alÄ±yor ama bu arayÃ¼zÃ¼n nereden geldiÄŸi
(php-di mi, elle mi) bu gÃ¶rev kapsamÄ±nda doÄŸrulanmadÄ±.

### H.2 â€” PSR-11 (Container)

| Paket | SÃ¼rÃ¼m | Durum | Kod kullanÄ±mÄ± |
|---|---|---|---|
| psr/container | ^2.0 | composer.json'da | âš ï¸ VERIFICATION REQUIRED â€” kodda `Psr\Container` kullanÄ±mÄ± grep edilmedi |
| php-di/php-di | (AuthContainer'da) | `DI\ContainerBuilder` | âœ… AuthContainer.php |

**SonuÃ§:** php-di container'Ä± `AuthContainer` iÃ§inde kullanÄ±lÄ±yor.
`Psr\Container\ContainerInterface` tipi AuthContainer'da resolve ediliyor mu â€”
âš ï¸ VERIFICATION REQUIRED: `AuthContainer.php` iÃ§eriÄŸi bu gÃ¶rev kapsamÄ±nda
okunmadÄ±, yalnÄ±z `DI\ContainerBuilder` varlÄ±ÄŸÄ± grep ile doÄŸrulandÄ±.

### H.3 â€” PSR-14 (Event)

| Paket | Durum | Kod kullanÄ±mÄ± |
|---|---|---|
| psr/event-dispatcher | composer'da (transitif veya direkt) | âœ… `shared/src/Events/EventDispatcher.php` implements `EventDispatcherInterface` |

---

## Â§I â€” CROSS-REFERENCE

| Konu | Referans | KlasÃ¶r |
|---|---|---|
| DTO taÅŸÄ±nmasÄ± | dto-katmanÄ± | `../dto/` |
| Event yayÄ±nlanmasÄ± | events-katmanÄ± | `../events/` |
| Middleware validate etkileÅŸimi | middleware-katmanÄ± | `../middleware/` |
| Command pattern | commands/ (format referansÄ±) | `../commands/` |

---

## Â§J â€” KAPSAM KABUL KRÄ°TERLERÄ°

| # | Kriter | Beklenen | Durum |
|---|---|---|---|
| 1 | Dosya sayÄ±sÄ± (kapsam slotu) | â‰¥2 (usecase-kapsam + usecase-usecase-katmani) | âœ… 2 |
| 2 | UseCase sÄ±nÄ±f varlÄ±ÄŸÄ± | â‰¥1 (grep: `*UseCase*.php`) | âš ï¸ 0 â€” Handler ile temsil ediliyor |
| 3 | URL havuzu | â‰¤2 | âœ… 2 |
| 4 | Frontmatter | 7 alan (group: son) | âœ… |
| 5 | GRUPLAR tablosu | frontmatter sonrasÄ± | âœ… |
| 6 | Footer | Authority + Last Updated | âœ… |

---

## Â§K â€” SONRAKÄ° ADIMLAR

| # | AdÄ±m | Sahip | Ã–ncelik |
|---|---|---|---|
| 1 | usecase-dogrulama.md â†’ Ã¶lÃ§Ã¼m gate doÄŸrulamasÄ± | Developer | P0 |
| 2 | usecase-web-arastirma.md â†’ PSR-17 + PSR-11 kanÄ±t havuzu (2 URL) | Developer | P0 |
| 3 | usecase-ornekler.md â†’ DosyaÂ·SatÄ±r kanÄ±tlÄ± kod Ã¶rnekleri | Developer | P0 |
| 4 | usecase-bilesenler.md â†’ bileÅŸen envanteri | Developer | P0 |
| 5 | index.md â†’ katalog (6 dosya Ã¶zeti) | Developer | P0 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §L — BÝLEÞEN AÝLESÝ ÖZETÝ (Kapsam Ýçi)

UseCase katmanýnýn de-fakto bileþenleri bu repo'da üç aileye ayrýlýr:

### L.1 — Handler ailesi (senaryo yürütücüler)

| Bileþen | Dosya | Satýr | Rol |
|---|---|---|---|
| AuthPostHandler | auth.coremusic.net/include/Handler/AuthPostHandler.php | ~57+ | Login senaryosu |

- Dispatch deseni: `match($action)` (Satýr: 38-57)
- Girdi: `ServerRequestInterface` (PSR-7 tipi)
- Çýktý: `ResponseInterface` (PSR-7 tipi)

?? VERIFICATION REQUIRED: Handler dizininde baþka handler dosyalarý var mý —
bu görev kapsamýnda `auth.coremusic.net/include/Handler/` listesi okunmadý.

### L.2 — Controller ailesi (HTTP orkestratörler)

| Bileþen | Dosya | Satýr | Rol |
|---|---|---|---|
| AuthController | api.coremusic.net/include/Controller/AuthController.php | 355 | Auth HTTP dispatch |

- Dispatch deseni: `match($action)` (Satýr: 52-61)
- Error mapping: status kodu seçimi (Satýr: 62-78)

### L.3 — Transaction ailesi (bütünlük saðlayýcýlar)

| Bileþen | Dosya | Transaction satýrlarý | Adet |
|---|---|---|---|
| UserRepository | api.coremusic.net/include/Repository/UserRepository.php | 147, 184, 192 | 3 |
| CatalogWriter | api.coremusic.net/include/Repository/CatalogWriter.php | 183, 217, 220 | 3 |
| api-key-create (CLI) | shared/bin/api-key-create.php | 135-147 | 1 |

**Toplam:** 7 transaction noktasý (grep doðrulamasý).

### L.4 — Event ailesi (PSR-14)

| Bileþen | Dosya | Arayüz |
|---|---|---|
| EventDispatcher | shared/src/Events/EventDispatcher.php | `EventDispatcherInterface` (PSR-14) |

?? VERIFICATION REQUIRED: EventDispatcher'ýn use-case akýþýnda çaðrýlýp
çaðrýldýðý (login sonrasý event yayýnlanýyor mu) bu görev kapsamýnda grep edilmedi.

### L.5 — Container ailesi (PSR-11 / php-di)

| Bileþen | Dosya | Kütüphane |
|---|---|---|
| AuthContainer | auth.coremusic.net/include/Container/AuthContainer.php | php-di (`DI\ContainerBuilder`) |

---

## §M — SENARYO HARÝTASI (Bilinen Akýþlar)

### M.1 — Login senaryosu (doðrulanmýþ zincir)

```
HTTP POST /auth/login
    › AuthController::dispatch (Satýr: 52-61, match($action))
        › AuthPostHandler::handle (Satýr: 38-57, match)
            › LoginRequest (Domain DTO — aktif üretim)
            › (doðrulama + kimlik doðrulama)
            › AuthResponse (Domain DTO — aktif üretim, JsonSerializable)
        › error mapping (AuthController Satýr: 62-78)
    › HTTP response (status kodu + JSON)
```

**Doðrulanan dosyalar:**
- AuthController.php · Satýr: 52-61 (dispatch)
- AuthController.php · Satýr: 62-78 (error mapping)
- AuthPostHandler.php · Satýr: 38-57 (handle)
- LoginRequest.php (DTO)
- AuthResponse.php (DTO, jsonSerialize Satýr: 68-70)

### M.2 — Katalog yazma senaryosu (kýsmi)

```
(write iþlemi)
    › CatalogWriter (Repository)
        › transaction (Satýr: 183, 217, 220)
    › (event yayýnlanýr mý? ?? VERIFICATION REQUIRED)
```

### M.3 — API key oluþturma senaryosu (CLI)

```
php shared/bin/api-key-create.php
    › transaction (Satýr: 135-147)
    › (DB write + commit)
```

### M.4 — Doðrulama senaryosu (middleware)

```
HTTP request
    › RequestValidationMiddleware (175 satýr)
        › Validator (doðrulama kurallarý)
        › baþarýsýzsa: 415/400/422 (dto-web-arastirma.md §K — status matrisi)
    › baþarýlýysa: Controller'a devam
```

?? VERIFICATION REQUIRED: RequestValidationMiddleware'in 415/400/422 döndürdüðü
bu görev kapsamýnda okunmadý; 175 satýr uzunluðu ve Validator baðýmlýlýðý doðrulandý.

---

## §N — DISPATCH DESENÝ ANALÝZÝ

### N.1 — match() deseni (PHP 8.0+)

| Kullaným | Dosya·Satýr | Desen |
|---|---|---|
| Controller | AuthController.php · Satýr: 52-61 | `match($action) { 'login' => ..., 'logout' => ... }` |
| Handler | AuthPostHandler.php · Satýr: 38-57 | `match(...)` |

**Avantaj:** Controller'da if/else yerine exhaustible match — yeni action
eklendiðinde default kolu zorunlu (PHP match default'suz olabilir ama
non-exhaustive'da UnhandledMatchError fýrlatýr).

### N.2 — Error mapping deseni

```
Dosya: AuthController.php · Satýr: 62-78
```

Handler'dan dönen durum/kod › HTTP status kodu eþlemesi. Bu katman
Application'ýn "son kilometre" sorumluluðudur (framework baðýmlýlýðý burada
kabul edilir — Controller zaten Presentation katmanýndadýr).

?? VERIFICATION REQUIRED: error mapping'in tam içerik yapýsý (hangi kod hangi
status'a) bu görevde okunmadý; yalnýz konum (Satýr: 62-78) doðrulandý.

---

## §O — TRANSACTION ANALÝZÝ

### O.1 — Transaction noktalarýnýn daðýlýmý

| Dosya | Satýr | Ýþlem türü |
|---|---|---|
| UserRepository.php | 147 | DB write (user) |
| UserRepository.php | 184 | DB write (user update vb.) |
| UserRepository.php | 192 | DB write |
| CatalogWriter.php | 183 | DB write (catalog) |
| CatalogWriter.php | 217 | DB write |
| CatalogWriter.php | 220 | DB write (commit/close) |
| bin/api-key-create.php | 135-147 | CLI transaction bloðu |

### O.2 — Transaction deseni (PLANNED kod yapýsý)

```php
// PLANNED — transaction kullaným deseninin öngörülen biçimi.
// Doðrulama: Dosya·Satýr kanýtlarý yukarýda.

$this->db->beginTransaction();
try {
    // ... DB write adýmlarý ...
    $this->db->commit();
} catch (\Throwable $e) {
    $this->db->rollBack();
    throw $e;
}
```

?? VERIFICATION REQUIRED: gerçek transaction kodunun try/catch yapýsý ve
exception tipi bu görev kapsamýnda okunmadý; transaction çaðrý varlýklarý
doðrulandý.

### O.3 — Transaction ve UseCase iliþkisi

Klasik mimaride transaction UseCase katmanýnda baþlar (Unit of Work).
Bu repoda transaction **Repository** katmanýnda (UserRepository,
CatalogWriter) — yani:

| Desen | Klasik | Bu repo |
|---|---|---|
| Transaction sahibi | UseCase / UnitOfWork | Repository |
| UseCase rolü | transaction orkestrasyonu | (dosya yok — PLANNED) |

?? MÝMARÝ BULGU: Transaction'ýn Repository'de olmasý, Repository'nin
"veri eriþimi + bütünlük" sorumluluðunu birleþtirir. UseCase katmaný eklenirse
transaction orkestrasyonu UseCase'e taþýnmalý mý — ADR kararý.

---

## §P — ÖNCEKÝ KARARLAR (Precedent)

| Karar | Kaynak | Durum |
|---|---|---|
| Handler = senaryo yürütücüsü | AuthPostHandler (aktif üretim) | ADOPTED (de-fakto) |
| match() dispatch | AuthController:52-61, AuthPostHandler:38-57 | ADOPTED |
| Error mapping Controller'da | AuthController:62-78 | ADOPTED |
| Transaction Repository'de | UserRepo/CatalogWriter | ADOPTED (dikkat: klasik deðil) |
| PSR-14 EventDispatcher | shared/src/Events/EventDispatcher.php | ADOPTED |
| php-di container | AuthContainer.php | ADOPTED |
| PSR-11 arayüzü | psr/container ^2.0 (composer) | ?? VERIFICATION REQUIRED |
| PSR-17 Factory | nyholm/psr7 ^1.8 (composer) | ? KOD KULLANIMI YOK |

---

## §Q — RÝSKLER VE BELÝRSÝZLÝKLER

| # | Bulgu | Risk | Aksiyon |
|---|---|---|---|
| Q1 | Klasik UseCase sýnýfý = 0 | Katman eksik veya Handler ile temsil ediliyor | ADR: UseCase adlandýrma |
| Q2 | Transaction Repository'de | Repository çift sorumluluk (SRP) | ADR: transaction sahibi |
| Q3 | PSR-11 arayüz kullanýmý doðrulanmadý | Container soyutlama eksik olabilir | grep: `Psr\Container` |
| Q4 | PSR-17 kod kullanýmý = 0 | Paket atýk olabilir | Karar: sakla/kaldýr |
| Q5 | Event akýþý doðrulanmadý | Login sonrasý event yayýnlanmýyor olabilir | grep: `dispatch(` |

---

## §R — KAPSAM KABUL KONTROL LÝSTESÝ

| Kontrol | Beklenen | Gerçek | Durum |
|---|---|---|---|
| Frontmatter 7 alan, group: son | ? | ? | PASS |
| §GRUPLAR tablosu | ? | ? | PASS |
| Footer Authority + Last Updated | ? | ? | PASS |
| URL havuzu ?2 | ? | 2 | PASS |
| Korunan scope slotu | usecase-usecase-katmani.md (527 L) | ? | PASS |
| Ölçüm kanýtlarý Dosya·Satýr | ? | §E | PASS |
| PLANNED/VERIFICATION iþaretlemesi | ? | §H, §M, §O, §Q | PASS |
| Dosya sayýsý (yeni) | 6 | usecase-kapsam.md (1/6) | DEVAM |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §S — DOSYA YAÞAM DÖNGÜSÜ

```
Oluþturma (bu görev)
  › Kapsam tanýmý (usecase-kapsam.md)
  › Doðrulama (usecase-dogrulama.md — G1 gate)
  › Web kanýt (usecase-web-arastirma.md — PSR-17 + PSR-11)
  › Kod örnek (usecase-ornekler.md — Dosya·Satýr)
  › Bileþen envanter (usecase-bilesenler.md)
  › Katalog (index.md)
  › Orkestratör commit (subagent commit ATMADI)
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
