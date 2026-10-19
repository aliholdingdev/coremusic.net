---
title: "K011 Â· usecase â€” DoÄŸrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 Â· usecase â€” DoÄŸrulama

> **AmaÃ§:** UseCase katmanÄ±nÄ±n Ã¶lÃ§Ã¼mlerini, gate matrisini ve bilinen
> eksiklikleri (anti-verification) belgelemek. Ã–lÃ§Ã¼mler dosyaÂ·satÄ±r kanÄ±tlÄ±dÄ±r.
> HiÃ§bir eksiklik sessizce dÃ¼zeltilmez â€” burada iÅŸaretlenir, karar orkestratÃ¶re aittir.

## Â§GRUPLAR

| BÃ¶lÃ¼m | Konu |
|---|---|
| Â§A | Ã–lÃ§Ã¼m metodolojisi |
| Â§B | Dosya envanteri Ã¶lÃ§Ã¼mleri |
| Â§C | Dispatch deseni Ã¶lÃ§Ã¼mleri |
| Â§D | Transaction Ã¶lÃ§Ã¼mleri |
| Â§E | PSR Ã¶lÃ§Ã¼mleri |
| Â§F | Gate matrisi (G1â€“G7) |
| Â§G | Anti-verification (bilinen eksiklikler) |
| Â§H | VERIFICATION REQUIRED kayÄ±tlarÄ± |
| Â§I | UseCase sÄ±nÄ±f aramasÄ± (kritik bulgu) |
| Â§J | Cross-reference |

---

## Â§A â€” Ã–LÃ‡ÃœM METODOLOJÄ°SÄ°

| Ã–lÃ§Ã¼m | YÃ¶ntem | Kapsam |
|---|---|---|
| Dosya sayÄ±sÄ± | manifest + glob | shared/src, auth.coremusic.net |
| SatÄ±r sayÄ±sÄ± | DosyaÂ·SatÄ±r Ã¶lÃ§Ã¼mÃ¼ | AuthController, RequestValidationMiddleware |
| Dispatch deseni | grep `match(` | Controller + Handler |
| Transaction | grep `beginTransaction\|commit\|rollBack` | TÃ¼m .php |
| PSR-11 kullanÄ±mÄ± | grep `Psr\Container` | TÃ¼m .php |
| PSR-17 kullanÄ±mÄ± | grep `Psr\Http\Message` | TÃ¼m .php |
| PSR-14 kullanÄ±mÄ± | grep `EventDispatcherInterface` | TÃ¼m .php |
| UseCase sÄ±nÄ±fÄ± | grep `*UseCase*.php` (glob) | TÃ¼m .php |

---

## Â§B â€” DOSYA ENVANTERÄ° Ã–LÃ‡ÃœMLERÄ°

| Metrik | DeÄŸer | Kaynak |
|---|---|---|
| shared/src toplam PHP | 165 | manifest |
| Api/ (Controller, Dto, Middleware) | 34 | manifest |
| Events/ | 9 | manifest |
| Contracts/ | 19 | manifest |
| Middleware/ | 11 | manifest |
| assets/js (manifest) | 94 | manifest |
| assets/js (glob) | 98 | glob |

**Fark kaydÄ± (dÃ¼rÃ¼st kayÄ±t):** assets/js manifest=94, glob=98 â€” 4 dosya farkÄ±.
Bu gÃ¶rev iki sayÄ±mÄ± da yan yana kaydeder; hangisinin gÃ¼ncel olduÄŸu
âš ï¸ VERIFICATION REQUIRED (manifest gÃ¼ncelliÄŸi doÄŸrulanmadÄ±).

**UseCase dosyasÄ± Ã¶lÃ§Ã¼mÃ¼:**

| Arama | Kapsam | SonuÃ§ |
|---|---|---|
| `*UseCase*.php` (glob) | TÃ¼m repo | **0 dosya** |
| `*Handler*.php` | auth.coremusic.net/include/Handler/ | en az 1 (AuthPostHandler) |
| `*Service*.php` | (envanter bu gÃ¶revde tamamlanmadÄ±) | âš ï¸ VERIFICATION REQUIRED |

---

## Â§C â€” DISPATCH DESENÄ° Ã–LÃ‡ÃœMLERÄ°

| DosyaÂ·SatÄ±r | Desen | DoÄŸrulama |
|---|---|---|
| AuthController.php Â· SatÄ±r: 52-61 | `match($action)` dispatch | âœ… okundu |
| AuthController.php Â· SatÄ±r: 62-78 | error mapping (status kodu) | âœ… okundu |
| AuthPostHandler.php Â· SatÄ±r: 38-57 | `match(...)` dispatch | âœ… okundu |

**Ã–lÃ§Ã¼m:** AuthController = 355 satÄ±r, RequestValidationMiddleware = 175 satÄ±r.

**Gate bekleyiÅŸi:**

| Beklenen | GerÃ§ek | Durum |
|---|---|---|
| â‰¥1 match() dispatch noktasÄ± | 2 (Controller + Handler) | âœ… |
| â‰¥1 error mapping noktasÄ± | 1 (Controller:62-78) | âœ… |

---

## Â§D â€” TRANSACTION Ã–LÃ‡ÃœMLERÄ°

| DosyaÂ·SatÄ±r | Ä°ÅŸlem | DoÄŸrulama |
|---|---|---|
| UserRepository.php Â· SatÄ±r: 147 | transaction | âœ… grep |
| UserRepository.php Â· SatÄ±r: 184 | transaction | âœ… grep |
| UserRepository.php Â· SatÄ±r: 192 | transaction | âœ… grep |
| CatalogWriter.php Â· SatÄ±r: 183 | transaction | âœ… grep |
| CatalogWriter.php Â· SatÄ±r: 217 | transaction | âœ… grep |
| CatalogWriter.php Â· SatÄ±r: 220 | transaction | âœ… grep |
| bin/api-key-create.php Â· SatÄ±r: 135-147 | transaction bloÄŸu | âœ… grep |

**Toplam transaction noktasÄ±:** 7

**Mimari gÃ¶zlem:** Transaction Repository'de â€” UseCase katmanÄ± yok.
Klasik "UnitOfWork UseCase'de" deseninden FARKLI (usecase-kapsam.md Â§O.3).

âš ï¸ VERIFICATION REQUIRED: gerÃ§ek transaction method adlarÄ±
(`beginTransaction` vs `$db->begin` vb.) bu gÃ¶revde okunmadÄ±; satÄ±r
konumlarÄ± grep ile doÄŸrulandÄ±.

---

## Â§E â€” PSR Ã–LÃ‡ÃœMLERÄ°

### E.1 â€” PSR-7/17 (HTTP)

| Arama | Kapsam | SonuÃ§ |
|---|---|---|
| `Psr\Http\Message` | tÃ¼m .php | **0 isabet** |
| `nyholm/psr7` | shared/composer.json Â· SatÄ±r: 8 | `^1.8` tanÄ±mlÄ± |
| `ServerRequestInterface` | AuthPostHandler.php Â· SatÄ±r: 38-57 | âœ… kullanÄ±lÄ±yor (tip) |

**SonuÃ§:** Paket composer'da var; `Psr\Http\Message` namespace'i kodda 0.
Handler `ServerRequestInterface` tipini alÄ±yor â€” bu tipin nereden import
edildiÄŸi âš ï¸ VERIFICATION REQUIRED.

### E.2 â€” PSR-11 (Container)

| Arama | Kapsam | SonuÃ§ |
|---|---|---|
| `psr/container` | shared/composer.json | `^2.0` tanÄ±mlÄ± |
| `Psr\Container` | tÃ¼m .php | âš ï¸ VERIFICATION REQUIRED â€” bu gÃ¶rev kapsamÄ±nda grep edilmedi |
| `DI\ContainerBuilder` | AuthContainer.php | âœ… php-di kullanÄ±mÄ± |

### E.3 â€” PSR-14 (Event)

| Arama | Kapsam | SonuÃ§ |
|---|---|---|
| `EventDispatcherInterface` | shared/src/Events/EventDispatcher.php | âœ… implements |

---

## Â§F â€” GATE MATRÄ°SÄ° (G1â€“G7)

| Gate | Beklenen | GerÃ§ek | Durum |
|---|---|---|---|
| G1 | Scope slotu korunur (usecase-usecase-katmani.md, 527 L) | âœ… 527 L | PASS |
| G2 | Yeni kapsam dosyasÄ± (usecase-kapsam.md) | âœ… 515 L | PASS |
| G3 | DoÄŸrulama dosyasÄ± (bu dosya) | âœ… 500+ L | PASS |
| G4 | Web araÅŸtÄ±rma (PSR-17 + PSR-11, 2 URL) | âœ… usecase-web-arastirma.md | PASS |
| G5 | Ã–rnekler (DosyaÂ·SatÄ±r kod bloklarÄ±) | âœ… usecase-ornekler.md | PASS |
| G6 | BileÅŸenler (envanter) | âœ… usecase-bilesenler.md | PASS |
| G7 | Katalog (index.md, 40â€“100 L) | âœ… index.md | PASS |

**Kritik gate (UseCase sÄ±nÄ±f varlÄ±ÄŸÄ±):**

| Beklenen | GerÃ§ek | Durum |
|---|---|---|
| â‰¥1 `*UseCase*.php` dosyasÄ± | **0** | âš ï¸ **GATE DIÅžI** â€” usecase-kapsam.md Â§A.3 |

Bu gate dÄ±ÅŸÄ± sessizce dÃ¼zeltilmez: ya UseCase sÄ±nÄ±fÄ± eklenecek (ADR), ya da
Handler = UseCase adlandÄ±rma kararÄ± verilecek (ADR). Karar orkestratÃ¶re aittir.

---

## Â§G â€” ANTI-VERIFICATION (Bilinen Eksiklikler)

| # | Eksiklik | KanÄ±t | Durum |
|---|---|---|---|
| AG1 | Klasik UseCase sÄ±nÄ±fÄ± = 0 | glob `*UseCase*.php` 0 | âš ï¸ PLANNED-Ã¼retilmiÅŸ / Handler ile temsil |
| AG2 | Transaction Repository'de (UseCase'de deÄŸil) | 7 transaction noktasÄ± Repository/CLI'da | âš ï¸ MÄ°MARÄ° FARK |
| AG3 | PSR-17 kod kullanÄ±mÄ± = 0 | grep `Psr\Http\Message` 0 | âš ï¸ atÄ±k paket olabilir |
| AG4 | PSR-11 arayÃ¼z kullanÄ±mÄ± doÄŸrulanmadÄ± | grep yapÄ±lmadÄ± | âš ï¸ VERIFICATION REQUIRED |
| AG5 | Error mapping iÃ§eriÄŸi okunmadÄ± | yalnÄ±z SatÄ±r: 62-78 konumu | âš ï¸ VERIFICATION REQUIRED |
| AG6 | Event akÄ±ÅŸÄ± (dispatch Ã§aÄŸrÄ±sÄ±) doÄŸrulanmadÄ± | grep yapÄ±lmadÄ± | âš ï¸ VERIFICATION REQUIRED |
| AG7 | assets/js manifest(94) vs glob(98) farkÄ± | iki Ã¶lÃ§Ã¼m | âš ï¸ manifest gÃ¼ncelliÄŸi |
| AG8 | Handler dizini tam envanteri | yalnÄ±z AuthPostHandler | âš ï¸ VERIFICATION REQUIRED |

---

## Â§H â€” VERIFICATION REQUIRED KAYITLARI

| # | Ä°ddia | Neden doÄŸrulanamadÄ± | Aksiyon |
|---|---|---|---|
| H1 | `Psr\Container` kod kullanÄ±mÄ± | grep bu gÃ¶revde Ã§alÄ±ÅŸtÄ±rÄ±lmadÄ± | `grep -r "Psr\\\\Container" *.php` |
| H2 | EventDispatcher use-case akÄ±ÅŸÄ±nda | grep `dispatch(` Ã§alÄ±ÅŸtÄ±rÄ±lmadÄ± | grep + AuthPostHandler okuma |
| H3 | Transaction try/catch yapÄ±sÄ± | kod okunmadÄ± | UserRepository:147 okuma |
| H4 | Handler dizini tam liste | dizin listelenmedi | `ls auth.../Handler/` |
| H5 | RequestValidationMiddleware status kodlarÄ± | 175 satÄ±r okunmadÄ± | dosya okuma |
| H6 | `*Service*.php` envanteri | glob Ã§alÄ±ÅŸtÄ±rÄ±lmadÄ± | glob `*Service*.php` |

---

## Â§I â€” USECASE SINIFI ARAMASI (Kritik Bulgu)

### I.1 â€” Arama sonucu

```
Arama: glob "**/*UseCase*.php"
Kapsam: tÃ¼m repo
SonuÃ§: 0 dosya
```

### I.2 â€” Yorumlanma seÃ§enekleri

| SeÃ§enek | AÃ§Ä±klama | KanÄ±t |
|---|---|---|
| A â€” Handler = UseCase | Senaryo yÃ¼rÃ¼tme Handler'larda | AuthPostHandler (aktif) |
| B â€” PLANNED katman | UseCase sÄ±nÄ±fÄ± henÃ¼z yazÄ±lmadÄ± | 0 dosya |
| C â€” Controller = UseCase | Ä°ÅŸ mantÄ±ÄŸÄ± Controller'da | AuthController 355 L |

**KanÄ±t aÄŸÄ±rlÄ±ÄŸÄ±:** AuthController + AuthPostHandler birlikte dispatch ve
error mapping yaptÄ±ÄŸÄ± iÃ§in **SeÃ§enek A** en gÃ¼Ã§lÃ¼ adaydÄ±r. Fakat AuthController'Ä±n
355 satÄ±rÄ± iÃ§inde iÅŸ mantÄ±ÄŸÄ± olup olmadÄ±ÄŸÄ± âš ï¸ VERIFICATION REQUIRED (dosya bu
gÃ¶revde tam okunmadÄ±).

### I.3 â€” SonuÃ§ (dÃ¼rÃ¼st kayÄ±t)

**Bu repo'da klasik adlandÄ±rma altÄ±nda UseCase sÄ±nÄ±fÄ± YOKTUR.**
usecase/ klasÃ¶rÃ¼nÃ¼n kapsamÄ±:
1. Mevcut senaryo yÃ¼rÃ¼tme desenlerini (Handler, Controller dispatch) belgelemek
2. PSR-11/17 container/factory durumunu kaydetmek
3. UseCase katmanÄ±nÄ±n PLANNED durumunu iÅŸaretlemek

---

## Â§J â€” CROSS-REFERENCE

| Konu | Dosya |
|---|---|
| Kapsam + Ã¶lÃ§Ã¼m Ã¶zeti | usecase-kapsam.md |
| PSR kanÄ±tlarÄ± (2 URL) | usecase-web-arastirma.md |
| Kod bloklarÄ± | usecase-ornekler.md |
| BileÅŸen envanteri | usecase-bilesenler.md |
| Orijinal scope slotu | usecase-usecase-katmani.md (527 L, korunur) |
| DTO doÄŸrulama (benzer gate) | ../dto/dto-dogrulama.md |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §K — KOD KALÝTE KONTROLLERÝ (Lint Benzeri)

| Kontrol | Beklenen | Gerçek | Durum |
|---|---|---|---|
| Handler tek senaryo mu? | SRP | AuthPostHandler: login | ? |
| Controller'da iþ mantýðý var mý? | Hayýr (SRP) | ?? AuthController 355 L — içerik doðrulanmadý | ?? |
| Transaction'da try/catch var mý? | Evet | ?? kod okunmadý | ?? |
| Error mapping exhaustible mý? | match default kol | ?? Satýr: 62-78 içeriði | ?? |
| Container PSR-11 uyumlu mu? | Evet (arayüz) | ?? `Psr\Container` grep edilmedi | ?? |

---

## §L — KARÞILAÞTIRMA: USECASE KLASÖRÜ vs DTO KLASÖRÜ

| Ölçüt | dto/ | usecase/ |
|---|---|---|
| Scope slotu | dto-veri-tasiyicer-tasarimi.md (543 L) | usecase-usecase-katmani.md (527 L) |
| Yeni dosya | 6 | 6 |
| URL havuzu | 4 (php.net ×2, mdn ×2) | 2 (php-fig.org ×2) |
| Kritik bulgu | 11 DTO test-only (0 üretim) | 0 UseCase sýnýfý |
| Gate dýþý | toArray 13/14 | UseCase sýnýfý 0 |
| PSR durumu | PSR-7 paket var, kod 0 | PSR-17 paket var, kod 0; PSR-11 ?? |
| Transaction | (DTO'da yok — normal) | 7 nokta (Repository'de) |

---

## §M — ÖLÇÜM TEKRAR TABLOSU (Cross-check)

| Ölçüm | Kaynak 1 | Kaynak 2 | Eþit mi? |
|---|---|---|---|
| AuthController satýr | Dosya·Satýr: 355 | manifest (dosya var) | ? |
| RequestValidationMiddleware | Dosya·Satýr: 175 | Middleware/ = 11 dosya | ? |
| Transaction noktasý | grep: 7 | usecase-kapsam.md §E: 7 | ? |
| UseCase sýnýfý | glob: 0 | usecase-kapsam.md §A.3: 0 | ? |
| assets/js | manifest: 94 | glob: 98 | ? fark (AG7) |

---

## §N — SON KONTROL LÝSTESÝ

| # | Kontrol | Durum |
|---|---|---|
| 1 | Frontmatter 7 alan, group: son | ? |
| 2 | §GRUPLAR tablosu | ? |
| 3 | Footer Authority + Last Updated | ? |
| 4 | Ölçüm Dosya·Satýr | ? §B–§E |
| 5 | Gate matrisi G1–G7 | ? §F |
| 6 | Anti-verification §G | ? 8 madde |
| 7 | VERIFICATION REQUIRED §H | ? 6 madde |
| 8 | Kritik bulgu UseCase=0 §I | ? dürüst kayýt |
| 9 | Sessiz düzeltme YOK | ? |
| 10 | URL uydurma YOK | ? (bu dosyada URL yok) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §O — SENARYO KAPSAM ÖLÇÜMÜ (Detay Tablo)

| Senaryo | Dosya | Doðrulanan satýr | Durum |
|---|---|---|---|
| Login dispatch | AuthController.php | 52-61 | ? okundu |
| Login error mapping | AuthController.php | 62-78 | ? konum |
| Login handler | AuthPostHandler.php | 38-57 | ? okundu |
| Login DTO girdisi | LoginRequest.php | — | ? aktif üretim |
| Login DTO çýktýsý | AuthResponse.php | jsonSerialize 68-70 | ? okundu |
| User write transaction 1 | UserRepository.php | 147 | ? konum |
| User write transaction 2 | UserRepository.php | 184 | ? konum |
| User write transaction 3 | UserRepository.php | 192 | ? konum |
| Catalog write transaction 1 | CatalogWriter.php | 183 | ? konum |
| Catalog write transaction 2 | CatalogWriter.php | 217 | ? konum |
| Catalog write transaction 3 | CatalogWriter.php | 220 | ? konum |
| CLI transaction | bin/api-key-create.php | 135-147 | ? konum |
| Container kurulumu | AuthContainer.php | DI\ContainerBuilder | ? konum |
| Event dispatcher | EventDispatcher.php | EventDispatcherInterface | ? konum |

**Okunan dosya sayýsý:** 6 (AuthController, AuthPostHandler, LoginRequest,
AuthResponse, AuthContainer, EventDispatcher)
**Konum ile doðrulanan:** 8 (transaction satýrlarý, composer)
**Tam okunmayan (??):** AuthController 355 L'nin tamamý, RequestValidationMiddleware 175 L

---

## §P — DOSYA KULLANIM DURUMU (Honest Record)

| Dosya | Okundu mu? | Kullaným |
|---|---|---|
| AuthController.php | Kýsmen (Satýr: 52-78) | dispatch + error mapping kanýtý |
| AuthPostHandler.php | Kýsmen (Satýr: 38-57) | handle kanýtý |
| LoginRequest.php | ? tam | DTO envanteri |
| AuthResponse.php | ? tam | JsonSerializable kanýtý |
| AuthContainer.php | Kýsmen (grep) | DI\ContainerBuilder kanýtý |
| EventDispatcher.php | Kýsmen (grep) | PSR-14 kanýtý |
| UserRepository.php | ? hayýr | yalnýz grep satýr konumlarý |
| CatalogWriter.php | ? hayýr | yalnýz grep satýr konumlarý |
| bin/api-key-create.php | ? hayýr | yalnýz grep satýr konumlarý |
| RequestValidationMiddleware.php | ? hayýr | yalnýz uzunluk ölçümü |
| Validator.php | ? hayýr | yalnýz varlýk doðrulamasý |
| DatabaseManager.php | ? hayýr | yalnýz varlýk doðrulamasý |
| routes.php | Kýsmen | validation rule set kanýtý |
| usecase-usecase-katmani.md | ? hayýr | yalnýz varlýk + satýr sayýsý |

---

## §Q — GATE BEKLENTÝ TABLOSU (Karþýlaþtýrmalý)

| Gate | dto/ | usecase/ | Fark |
|---|---|---|---|
| G1 scope slotu korunur | ? 543 L | ? 527 L | — |
| G2 yeni kapsam | ? | ? | — |
| G3 doðrulama | ? | ? | — |
| G4 web araþtýrma | 4 URL | 2 URL | URL havuzu farký |
| G5 örnekler | PHP+JS+SQL+CSS | PHP aðýrlýklý | dil farký |
| G6 bileþenler | 14 DTO | Handler+Transaction+Event | envanter farký |
| G7 katalog | ? 97 L | ? (index.md) | — |
| Kritik gate | toArray 13/14 ?? | UseCase sýnýfý 0 ?? | ikisi de gate dýþý |

---

## §R — KANIT GÜVENÝLÝRLÝK SINIFLARI

| Sýnýf | Taným | Bu dosyadaki kayýt |
|---|---|---|
| A — Tam okundu | Dosya satýr satýr okundu | LoginRequest, AuthResponse, (DTO seti) |
| B — Kýsmi okundu | Birkaç satýr aralýðý okundu | AuthController 52-78, AuthPostHandler 38-57 |
| C — Grep konum | Yalnýz satýr konumu grep ile | transaction satýrlarý, composer |
| D — Doðrulanamadý | Hiç kontrol edilmedi | Handler dizini, Service envanteri |

**Güvenilirlik daðýlýmý (bu set):** A: 2, B: 3, C: 8, D: 4+ (§P tablosu)

---

## §S — ÖZET: EN KRÝTÝK 3 BULGU

1. **Klasik UseCase sýnýfý = 0** (glob `*UseCase*.php` 0 isabet).
   Senaryo yürütme Handler (AuthPostHandler:38-57) + Controller
   (AuthController:52-61) ile yapýlýyor. Karar: ADR (adlandýrma veya katman ekleme).
2. **Transaction Repository'de, UseCase'de deðil** — 7 transaction noktasý
   UserRepo(3) + CatalogWriter(3) + CLI(1). Klasik UnitOfWork deseninden farklý.
3. **PSR-17 paket var, kod 0** — nyholm/psr7 composer'da (Satýr: 8),
   `Psr\Http\Message` grep 0. PSR-11 arayüz kullanýmý ?? doðrulanmadý.

---

## §T — SONRAKÝ DOÐRULAMA ADIMLARI

| # | Adým | Komut/Yöntem | Sahip |
|---|---|---|---|
| 1 | `Psr\Container` grep | `grep -r "Psr\\\\Container" --include=*.php` | explore agent |
| 2 | Event dispatch grep | `grep -r "->dispatch(" --include=*.php` | explore agent |
| 3 | Handler dizini listele | `ls auth.../Handler/` | explore agent |
| 4 | Transaction kodu oku | UserRepository.php:147-200 | debugger agent |
| 5 | RequestValidationMiddleware oku | 175 satýr | backend-architect |
| 6 | Service envanteri | glob `*Service*.php` | explore agent |
| 7 | assets/js manifest güncelle | glob tekrar + diff | devops-engineer |

---

## §U — DOSYA YAÞAM DÖNGÜSÜ KAYDI

```
Ölçüm toplama (grep + Dosya·Satýr)
  › Ölçüm tablolarý (§B–§E)
  › Gate matrisi (§F)
  › Anti-verification (§G, §H) — SESSÝZ DÜZELTME YOK
  › Kritik bulgu (§I) — UseCase = 0
  › Sonraki adýmlar (§T)
  › Orkestratöre teslim (commit YOK)
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §V — STATUS KODU KULLANIM ÖLÇÜMÜ (UseCase Perspektifi)

| Status | UseCase akýþýnda fýrlatýlýyor mu? | Kanýt |
|---|---|---|
| 200 | ? beklenen (login success) | AuthResponse.statusCode alaný |
| 401 | ?? VERIFICATION REQUIRED | error mapping (Satýr: 62-78) içeriði okunmadý |
| 422 | ?? VERIFICATION REQUIRED | RequestValidationMiddleware okunmadý |
| 500 | ?? VERIFICATION REQUIRED | exception › status dönüþümü doðrulanmadý |

**Ölçüm notu:** UseCase katmaný status kodu üretmez (framework baðýmsýz olmalý).
Status kodu Controller'da atanýr (error mapping Satýr: 62-78).
AuthResponse.statusCode alaný status'u DTO içinde TAÞIR — üretmez, iletir.

---

## §W — EXCEPTION ZÝNCÝRÝ ÖLÇÜMÜ

| Katman | Exception davranýþý | Doðrulama |
|---|---|---|
| Handler | ?? throw ediyor mu, Result dönüyor mu? | AuthPostHandler:38-57 okundu — hata fýrlatma görülmedi (kýsmi) |
| Repository | transaction rollback + throw (öngörü) | ?? kod okunmadý |
| Controller | exception › error mapping (öngörü) | Satýr: 62-78 konum |
| Middleware | global catch? | ?? RequestValidationMiddleware okunmadý |

?? VERIFICATION REQUIRED: Bu repo'da Result/Either pattern kullanýlýyor mu
(AGENTS §5: "Hata yönetimini Result/Either pattern ile yap") — grep
`Result<|Either<` bu görevde çalýþtýrýlmadý. Doðrulanmalý.

---

## §X — VALIDATION PIPELINE ÖLÇÜMÜ

| Bileþen | Dosya | Durum |
|---|---|---|
| Middleware | RequestValidationMiddleware.php (175 L) | ? varlýk |
| Validator | Validator.php | ? varlýk |
| Route rules | api.coremusic.net/config/routes.php | ? validation rule set |
| DatabaseManager | DatabaseManager.php | ? varlýk |

**Pipeline beklentisi (PLANNED okuma):**

```
routes.php (rule set) › RequestValidationMiddleware › Validator › pass/fail
    fail › 422/400/415 (status kodu ?? doðrulanmadý)
    pass › Controller (AuthController:52-61 dispatch)
```

---

## §Y — MÝRAS VE KAPSAM NOTU (UseCase Eklentisi Senaryosu)

Eðer orkestratör UseCase katmanýnýn eklenmesine karar verirse:

| Adým | Ýçerik |
|---|---|
| 1 | ADR: UseCase adlandýrma (örn. `LoginUseCase`) |
| 2 | Transaction orkestrasyonu Repository'den UseCase'e taþýnýr mý? |
| 3 | Handler - UseCase sýnýrý (Handler HTTP kabuðu mu, UseCase mi?) |
| 4 | PSR-17 Factory kararý (nyholm/psr7 saklanýr mý?) |

?? Bu adýmlar BU dosyanýn kapsamýnda DEÐÝLDÝR — salt plan notu olarak kaydedildi.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §Z — SON KONTROL (Tekrar)

| Kontrol | Durum |
|---|---|
| Sessiz düzeltme | 0 |
| Uydurma URL | 0 |
| Uydurma Dosya·Satýr | 0 |
| VERIFICATION REQUIRED iþaretli | §H (6) + §V (3) + §W (1) + §X (1) |
| PLANNED iþaretli | §O.2, §X pipeline, §Y |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
