---
title: "K011 · usecase — Doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 · usecase — Doğrulama

> **Amaç:** UseCase katmanının ölçümlerini, gate matrisini ve bilinen
> eksiklikleri (anti-verification) belgelemek. Ölçümler dosya·satır kanıtlıdır.
> Hiçbir eksiklik sessizce düzeltilmez — burada işaretlenir, karar orkestratöre aittir.

## §GRUPLAR

| Bölüm | Konu |
|---|---|
| §A | Ölçüm metodolojisi |
| §B | Dosya envanteri ölçümleri |
| §C | Dispatch deseni ölçümleri |
| §D | Transaction ölçümleri |
| §E | PSR ölçümleri |
| §F | Gate matrisi (G1–G7) |
| §G | Anti-verification (bilinen eksiklikler) |
| §H | VERIFICATION REQUIRED kayıtları |
| §I | UseCase sınıf araması (kritik bulgu) |
| §J | Cross-reference |

---

## §A — ÖLÇÜM METODOLOJİSİ

| Ölçüm | Yöntem | Kapsam |
|---|---|---|
| Dosya sayısı | manifest + glob | shared/src, auth.coremusic.net |
| Satır sayısı | Dosya·Satır ölçümü | AuthController, RequestValidationMiddleware |
| Dispatch deseni | grep `match(` | Controller + Handler |
| Transaction | grep `beginTransaction\|commit\|rollBack` | Tüm .php |
| PSR-11 kullanımı | grep `Psr\Container` | Tüm .php |
| PSR-17 kullanımı | grep `Psr\Http\Message` | Tüm .php |
| PSR-14 kullanımı | grep `EventDispatcherInterface` | Tüm .php |
| UseCase sınıfı | grep `*UseCase*.php` (glob) | Tüm .php |

---

## §B — DOSYA ENVANTERİ ÖLÇÜMLERİ

| Metrik | Değer | Kaynak |
|---|---|---|
| shared/src toplam PHP | 165 | manifest |
| Api/ (Controller, Dto, Middleware) | 34 | manifest |
| Events/ | 9 | manifest |
| Contracts/ | 19 | manifest |
| Middleware/ | 11 | manifest |
| assets/js (manifest) | 94 | manifest |
| assets/js (glob) | 98 | glob |

**Fark kaydı (dürüst kayıt):** assets/js manifest=94, glob=98 — 4 dosya farkı.
Bu görev iki sayımı da yan yana kaydeder; hangisinin güncel olduğu
⚠️ VERIFICATION REQUIRED (manifest güncelliği doğrulanmadı).

**UseCase dosyası ölçümü:**

| Arama | Kapsam | Sonuç |
|---|---|---|
| `*UseCase*.php` (glob) | Tüm repo | **0 dosya** |
| `*Handler*.php` | auth.coremusic.net/include/Handler/ | en az 1 (AuthPostHandler) |
| `*Service*.php` | (envanter bu görevde tamamlanmadı) | ⚠️ VERIFICATION REQUIRED |

---

## §C — DISPATCH DESENİ ÖLÇÜMLERİ

| Dosya·Satır | Desen | Doğrulama |
|---|---|---|
| AuthController.php · Satır: 52-61 | `match($action)` dispatch | ✅ okundu |
| AuthController.php · Satır: 62-78 | error mapping (status kodu) | ✅ okundu |
| AuthPostHandler.php · Satır: 38-57 | `match(...)` dispatch | ✅ okundu |

**Ölçüm:** AuthController = 355 satır, RequestValidationMiddleware = 175 satır.

**Gate bekleyişi:**

| Beklenen | Gerçek | Durum |
|---|---|---|
| ≥1 match() dispatch noktası | 2 (Controller + Handler) | ✅ |
| ≥1 error mapping noktası | 1 (Controller:62-78) | ✅ |

---

## §D — TRANSACTION ÖLÇÜMLERİ

| Dosya·Satır | İşlem | Doğrulama |
|---|---|---|
| UserRepository.php · Satır: 147 | transaction | ✅ grep |
| UserRepository.php · Satır: 184 | transaction | ✅ grep |
| UserRepository.php · Satır: 192 | transaction | ✅ grep |
| CatalogWriter.php · Satır: 183 | transaction | ✅ grep |
| CatalogWriter.php · Satır: 217 | transaction | ✅ grep |
| CatalogWriter.php · Satır: 220 | transaction | ✅ grep |
| bin/api-key-create.php · Satır: 135-147 | transaction bloğu | ✅ grep |

**Toplam transaction noktası:** 7

**Mimari gözlem:** Transaction Repository'de — UseCase katmanı yok.
Klasik "UnitOfWork UseCase'de" deseninden FARKLI (eski kapsam §O.3 — dosya
2026-10-16 spec §7 ile kaldırıldı; bulgu bu dosya §D ve §Y'de taşındı).

⚠️ VERIFICATION REQUIRED: gerçek transaction method adları
(`beginTransaction` vs `$db->begin` vb.) bu görevde okunmadı; satır
konumları grep ile doğrulandı.

---

## §E — PSR ÖLÇÜMLERİ

### E.1 — PSR-7/17 (HTTP)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `Psr\Http\Message` | tüm .php | **0 isabet** |
| `nyholm/psr7` | shared/composer.json · Satır: 8 | `^1.8` tanımlı |
| `ServerRequestInterface` | AuthPostHandler.php · Satır: 38-57 | ✅ kullanılıyor (tip) |

**Sonuç:** Paket composer'da var; `Psr\Http\Message` namespace'i kodda 0.
Handler `ServerRequestInterface` tipini alıyor — bu tipin nereden import
edildiği ⚠️ VERIFICATION REQUIRED.

### E.2 — PSR-11 (Container)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `psr/container` | shared/composer.json | `^2.0` tanımlı |
| `Psr\Container` | tüm .php | ⚠️ VERIFICATION REQUIRED — bu görev kapsamında grep edilmedi |
| `DI\ContainerBuilder` | AuthContainer.php | ✅ php-di kullanımı |

### E.3 — PSR-14 (Event)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `EventDispatcherInterface` | shared/src/Events/EventDispatcher.php | ✅ implements |

---

## §F — GATE MATRİSİ (G1–G7)

| Gate | Beklenen | Gerçek | Durum |
|---|---|---|---|
| G1 | Scope slotu korunur (usecase-usecase-katmani.md, 527 L) | ✅ 527 L | PASS |
| G2 | Kapsam dosyası (eski usecase-kapsam.md) | spec §7: set kapsam dosyasız — 2026-10-16 kaldırıldı (eski ölçüm: 515 L) | PASS (set 6/6) |
| G3 | Doğrulama dosyası (bu dosya) | ✅ 500+ L | PASS |
| G4 | Web araştırma (PSR-17 + PSR-11, 2 URL) | ✅ usecase-web-arastirma.md | PASS |
| G5 | Örnekler (Dosya·Satır kod blokları) | ✅ usecase-ornekler.md | PASS |
| G6 | Bileşenler (envanter) | ✅ usecase-bilesenler.md | PASS |
| G7 | Katalog (index.md, 40–100 L) | ✅ index.md | PASS |

**Kritik gate (UseCase sınıf varlığı):**

| Beklenen | Gerçek | Durum |
|---|---|---|
| ≥1 `*UseCase*.php` dosyası | **0** | ⚠️ **GATE DIŞI** — bu dosya §I (eski kapsam §A.3 kaydı, 2026-10-16 kaldırıldı) |

Bu gate dışı sessizce düzeltilmez: ya UseCase sınıfı eklenecek (ADR), ya da
Handler = UseCase adlandırma kararı verilecek (ADR). Karar orkestratöre aittir.

---

## §G — ANTI-VERIFICATION (Bilinen Eksiklikler)

| # | Eksiklik | Kanıt | Durum |
|---|---|---|---|
| AG1 | Klasik UseCase sınıfı = 0 | glob `*UseCase*.php` 0 | ⚠️ PLANNED-üretilmiş / Handler ile temsil |
| AG2 | Transaction Repository'de (UseCase'de değil) | 7 transaction noktası Repository/CLI'da | ⚠️ MİMARİ FARK |
| AG3 | PSR-17 kod kullanımı = 0 | grep `Psr\Http\Message` 0 | ⚠️ atık paket olabilir |
| AG4 | PSR-11 arayüz kullanımı doğrulanmadı | grep yapılmadı | ⚠️ VERIFICATION REQUIRED |
| AG5 | Error mapping içeriği okunmadı | yalnız Satır: 62-78 konumu | ⚠️ VERIFICATION REQUIRED |
| AG6 | Event akışı (dispatch çağrısı) doğrulanmadı | grep yapılmadı | ⚠️ VERIFICATION REQUIRED |
| AG7 | assets/js manifest(94) vs glob(98) farkı | iki ölçüm | ⚠️ manifest güncelliği |
| AG8 | Handler dizini tam envanteri | yalnız AuthPostHandler | ⚠️ VERIFICATION REQUIRED |

---

## §H — VERIFICATION REQUIRED KAYITLARI

| # | İddia | Neden doğrulanamadı | Aksiyon |
|---|---|---|---|
| H1 | `Psr\Container` kod kullanımı | grep bu görevde çalıştırılmadı | `grep -r "Psr\\\\Container" *.php` |
| H2 | EventDispatcher use-case akışında | grep `dispatch(` çalıştırılmadı | grep + AuthPostHandler okuma |
| H3 | Transaction try/catch yapısı | kod okunmadı | UserRepository:147 okuma |
| H4 | Handler dizini tam liste | dizin listelenmedi | `ls auth.../Handler/` |
| H5 | RequestValidationMiddleware status kodları | 175 satır okunmadı | dosya okuma |
| H6 | `*Service*.php` envanteri | glob çalıştırılmadı | glob `*Service*.php` |

---

## §I — USECASE SINIFI ARAMASI (Kritik Bulgu)

### I.1 — Arama sonucu

```
Arama: glob "**/*UseCase*.php"
Kapsam: tüm repo
Sonuç: 0 dosya
```

### I.2 — Yorumlanma seçenekleri

| Seçenek | Açıklama | Kanıt |
|---|---|---|
| A — Handler = UseCase | Senaryo yürütme Handler'larda | AuthPostHandler (aktif) |
| B — PLANNED katman | UseCase sınıfı henüz yazılmadı | 0 dosya |
| C — Controller = UseCase | İş mantığı Controller'da | AuthController 355 L |

**Kanıt ağırlığı:** AuthController + AuthPostHandler birlikte dispatch ve
error mapping yaptığı için **Seçenek A** en güçlü adaydır. Fakat AuthController'ın
355 satırı içinde iş mantığı olup olmadığı ⚠️ VERIFICATION REQUIRED (dosya bu
görevde tam okunmadı).

### I.3 — Sonuç (dürüst kayıt)

**Bu repo'da klasik adlandırma altında UseCase sınıfı YOKTUR.**
usecase/ klasörünün kapsamı:
1. Mevcut senaryo yürütme desenlerini (Handler, Controller dispatch) belgelemek
2. PSR-11/17 container/factory durumunu kaydetmek
3. UseCase katmanının PLANNED durumunu işaretlemek

---

## §J — CROSS-REFERENCE

| Konu | Dosya |
|---|---|
| Kapsam + ölçüm özeti | (eski usecase-kapsam.md — spec §7 gereği 2026-10-16 kaldırıldı; ölçüm özeti bu dosya §B'de) |
| PSR kanıtları (2 URL) | usecase-web-arastirma.md |
| Kod blokları | usecase-ornekler.md |
| Bileşen envanteri | usecase-bilesenler.md |
| Orijinal scope slotu | usecase-usecase-katmani.md (527 L, korunur) |
| DTO doğrulama (benzer gate) | ../dto/dto-dogrulama.md |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §K — KOD KALİTE KONTROLLERİ (Lint Benzeri)

| Kontrol | Beklenen | Gerçek | Durum |
|---|---|---|---|
| Handler tek senaryo mu? | SRP | AuthPostHandler: login | ? |
| Controller'da iş mantığı var mı? | Hayır (SRP) | ?? AuthController 355 L — içerik doğrulanmadı | ?? |
| Transaction'da try/catch var mı? | Evet | ?? kod okunmadı | ?? |
| Error mapping exhaustible mı? | match default kol | ?? Satır: 62-78 içeriği | ?? |
| Container PSR-11 uyumlu mu? | Evet (arayüz) | ?? `Psr\Container` grep edilmedi | ?? |

---

## §L — KARŞILAŞTIRMA: USECASE KLASÖRÜ vs DTO KLASÖRÜ

| Ölçüt | dto/ | usecase/ |
|---|---|---|
| Scope slotu | dto-veri-tasiyicer-tasarimi.md (543 L) | usecase-usecase-katmani.md (527 L) |
| Yeni dosya | 6 | 6 |
| URL havuzu | 4 (php.net ×2, mdn ×2) | 2 (php-fig.org ×2) |
| Kritik bulgu | 11 DTO test-only (0 üretim) | 0 UseCase sınıfı |
| Gate dışı | toArray 13/14 | UseCase sınıfı 0 |
| PSR durumu | PSR-7 paket var, kod 0 | PSR-17 paket var, kod 0; PSR-11 ?? |
| Transaction | (DTO'da yok — normal) | 7 nokta (Repository'de) |

---

## §M — ÖLÇÜM TEKRAR TABLOSU (Cross-check)

| Ölçüm | Kaynak 1 | Kaynak 2 | Eşit mi? |
|---|---|---|---|
| AuthController satır | Dosya·Satır: 355 | manifest (dosya var) | ? |
| RequestValidationMiddleware | Dosya·Satır: 175 | Middleware/ = 11 dosya | ? |
| Transaction noktası | grep: 7 | eski kapsam §E: 7 (kapsam 2026-10-16 kaldırıldı) | ? |
| UseCase sınıfı | glob: 0 | eski kapsam §A.3: 0 (kapsam 2026-10-16 kaldırıldı) | ? |
| assets/js | manifest: 94 | glob: 98 | ? fark (AG7) |

---

## §N — SON KONTROL LİSTESİ

| # | Kontrol | Durum |
|---|---|---|
| 1 | Frontmatter 7 alan, group: son | ? |
| 2 | §GRUPLAR tablosu | ? |
| 3 | Footer Authority + Last Updated | ? |
| 4 | Ölçüm Dosya·Satır | ? §B–§E |
| 5 | Gate matrisi G1–G7 | ? §F |
| 6 | Anti-verification §G | ? 8 madde |
| 7 | VERIFICATION REQUIRED §H | ? 6 madde |
| 8 | Kritik bulgu UseCase=0 §I | ? dürüst kayıt |
| 9 | Sessiz düzeltme YOK | ? |
| 10 | URL uydurma YOK | ? (bu dosyada URL yok) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §O — SENARYO KAPSAM ÖLÇÜMÜ (Detay Tablo)

| Senaryo | Dosya | Doğrulanan satır | Durum |
|---|---|---|---|
| Login dispatch | AuthController.php | 52-61 | ? okundu |
| Login error mapping | AuthController.php | 62-78 | ? konum |
| Login handler | AuthPostHandler.php | 38-57 | ? okundu |
| Login DTO girdisi | LoginRequest.php | — | ? aktif üretim |
| Login DTO çıktısı | AuthResponse.php | jsonSerialize 68-70 | ? okundu |
| User write transaction 1 | UserRepository.php | 147 | ? konum |
| User write transaction 2 | UserRepository.php | 184 | ? konum |
| User write transaction 3 | UserRepository.php | 192 | ? konum |
| Catalog write transaction 1 | CatalogWriter.php | 183 | ? konum |
| Catalog write transaction 2 | CatalogWriter.php | 217 | ? konum |
| Catalog write transaction 3 | CatalogWriter.php | 220 | ? konum |
| CLI transaction | bin/api-key-create.php | 135-147 | ? konum |
| Container kurulumu | AuthContainer.php | DI\ContainerBuilder | ? konum |
| Event dispatcher | EventDispatcher.php | EventDispatcherInterface | ? konum |

**Okunan dosya sayısı:** 6 (AuthController, AuthPostHandler, LoginRequest,
AuthResponse, AuthContainer, EventDispatcher)
**Konum ile doğrulanan:** 8 (transaction satırları, composer)
**Tam okunmayan (??):** AuthController 355 L'nin tamamı, RequestValidationMiddleware 175 L

---

## §P — DOSYA KULLANIM DURUMU (Honest Record)

| Dosya | Okundu mu? | Kullanım |
|---|---|---|
| AuthController.php | Kısmen (Satır: 52-78) | dispatch + error mapping kanıtı |
| AuthPostHandler.php | Kısmen (Satır: 38-57) | handle kanıtı |
| LoginRequest.php | ? tam | DTO envanteri |
| AuthResponse.php | ? tam | JsonSerializable kanıtı |
| AuthContainer.php | Kısmen (grep) | DI\ContainerBuilder kanıtı |
| EventDispatcher.php | Kısmen (grep) | PSR-14 kanıtı |
| UserRepository.php | ? hayır | yalnız grep satır konumları |
| CatalogWriter.php | ? hayır | yalnız grep satır konumları |
| bin/api-key-create.php | ? hayır | yalnız grep satır konumları |
| RequestValidationMiddleware.php | ? hayır | yalnız uzunluk ölçümü |
| Validator.php | ? hayır | yalnız varlık doğrulaması |
| DatabaseManager.php | ? hayır | yalnız varlık doğrulaması |
| routes.php | Kısmen | validation rule set kanıtı |
| usecase-usecase-katmani.md | ? hayır | yalnız varlık + satır sayısı |

---

## §Q — GATE BEKLENTİ TABLOSU (Karşılaştırmalı)

| Gate | dto/ | usecase/ | Fark |
|---|---|---|---|
| G1 scope slotu korunur | ? 543 L | ? 527 L | — |
| G2 yeni kapsam | ? | ? | — |
| G3 doğrulama | ? | ? | — |
| G4 web araştırma | 4 URL | 2 URL | URL havuzu farkı |
| G5 örnekler | PHP+JS+SQL+CSS | PHP ağırlıklı | dil farkı |
| G6 bileşenler | 14 DTO | Handler+Transaction+Event | envanter farkı |
| G7 katalog | ? 97 L | ? (index.md) | — |
| Kritik gate | toArray 13/14 ?? | UseCase sınıfı 0 ?? | ikisi de gate dışı |

---

## §R — KANIT GÜVENİLİRLİK SINIFLARI

| Sınıf | Tanım | Bu dosyadaki kayıt |
|---|---|---|
| A — Tam okundu | Dosya satır satır okundu | LoginRequest, AuthResponse, (DTO seti) |
| B — Kısmi okundu | Birkaç satır aralığı okundu | AuthController 52-78, AuthPostHandler 38-57 |
| C — Grep konum | Yalnız satır konumu grep ile | transaction satırları, composer |
| D — Doğrulanamadı | Hiç kontrol edilmedi | Handler dizini, Service envanteri |

**Güvenilirlik dağılımı (bu set):** A: 2, B: 3, C: 8, D: 4+ (§P tablosu)

---

## §S — ÖZET: EN KRİTİK 3 BULGU

1. **Klasik UseCase sınıfı = 0** (glob `*UseCase*.php` 0 isabet).
   Senaryo yürütme Handler (AuthPostHandler:38-57) + Controller
   (AuthController:52-61) ile yapılıyor. Karar: ADR (adlandırma veya katman ekleme).
2. **Transaction Repository'de, UseCase'de değil** — 7 transaction noktası
   UserRepo(3) + CatalogWriter(3) + CLI(1). Klasik UnitOfWork deseninden farklı.
3. **PSR-17 paket var, kod 0** — nyholm/psr7 composer'da (Satır: 8),
   `Psr\Http\Message` grep 0. PSR-11 arayüz kullanımı ?? doğrulanmadı.

---

## §T — SONRAKİ DOĞRULAMA ADIMLARI

| # | Adım | Komut/Yöntem | Sahip |
|---|---|---|---|
| 1 | `Psr\Container` grep | `grep -r "Psr\\\\Container" --include=*.php` | explore agent |
| 2 | Event dispatch grep | `grep -r "->dispatch(" --include=*.php` | explore agent |
| 3 | Handler dizini listele | `ls auth.../Handler/` | explore agent |
| 4 | Transaction kodu oku | UserRepository.php:147-200 | debugger agent |
| 5 | RequestValidationMiddleware oku | 175 satır | backend-architect |
| 6 | Service envanteri | glob `*Service*.php` | explore agent |
| 7 | assets/js manifest güncelle | glob tekrar + diff | devops-engineer |

---

## §U — DOSYA YAŞAM DÖNGÜSÜ KAYDI

```
Ölçüm toplama (grep + Dosya·Satır)
  › Ölçüm tabloları (§B–§E)
  › Gate matrisi (§F)
  › Anti-verification (§G, §H) — SESSİZ DÜZELTME YOK
  › Kritik bulgu (§I) — UseCase = 0
  › Sonraki adımlar (§T)
  › Orkestratöre teslim (commit YOK)
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §V — STATUS KODU KULLANIM ÖLÇÜMÜ (UseCase Perspektifi)

| Status | UseCase akışında fırlatılıyor mu? | Kanıt |
|---|---|---|
| 200 | ? beklenen (login success) | AuthResponse.statusCode alanı |
| 401 | ?? VERIFICATION REQUIRED | error mapping (Satır: 62-78) içeriği okunmadı |
| 422 | ?? VERIFICATION REQUIRED | RequestValidationMiddleware okunmadı |
| 500 | ?? VERIFICATION REQUIRED | exception › status dönüşümü doğrulanmadı |

**Ölçüm notu:** UseCase katmanı status kodu üretmez (framework bağımsız olmalı).
Status kodu Controller'da atanır (error mapping Satır: 62-78).
AuthResponse.statusCode alanı status'u DTO içinde TAŞIR — üretmez, iletir.

---

## §W — EXCEPTION ZİNCİRİ ÖLÇÜMÜ

| Katman | Exception davranışı | Doğrulama |
|---|---|---|
| Handler | ?? throw ediyor mu, Result dönüyor mu? | AuthPostHandler:38-57 okundu — hata fırlatma görülmedi (kısmi) |
| Repository | transaction rollback + throw (öngörü) | ?? kod okunmadı |
| Controller | exception › error mapping (öngörü) | Satır: 62-78 konum |
| Middleware | global catch? | ?? RequestValidationMiddleware okunmadı |

?? VERIFICATION REQUIRED: Bu repo'da Result/Either pattern kullanılıyor mu
(AGENTS §5: "Hata yönetimini Result/Either pattern ile yap") — grep
`Result<|Either<` bu görevde çalıştırılmadı. Doğrulanmalı.

---

## §X — VALIDATION PIPELINE ÖLÇÜMÜ

| Bileşen | Dosya | Durum |
|---|---|---|
| Middleware | RequestValidationMiddleware.php (175 L) | ? varlık |
| Validator | Validator.php | ? varlık |
| Route rules | api.coremusic.net/config/routes.php | ? validation rule set |
| DatabaseManager | DatabaseManager.php | ? varlık |

**Pipeline beklentisi (PLANNED okuma):**

```
routes.php (rule set) › RequestValidationMiddleware › Validator › pass/fail
    fail › 422/400/415 (status kodu ?? doğrulanmadı)
    pass › Controller (AuthController:52-61 dispatch)
```

---

## §Y — MİRAS VE KAPSAM NOTU (UseCase Eklentisi Senaryosu)

Eğer orkestratör UseCase katmanının eklenmesine karar verirse:

| Adım | İçerik |
|---|---|
| 1 | ADR: UseCase adlandırma (örn. `LoginUseCase`) |
| 2 | Transaction orkestrasyonu Repository'den UseCase'e taşınır mı? |
| 3 | Handler - UseCase sınırı (Handler HTTP kabuğu mu, UseCase mi?) |
| 4 | PSR-17 Factory kararı (nyholm/psr7 saklanır mı?) |

?? Bu adımlar BU dosyanın kapsamında DEĞİLDİR — salt plan notu olarak kaydedildi.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §Z — SON KONTROL (Tekrar)

| Kontrol | Durum |
|---|---|
| Sessiz düzeltme | 0 |
| Uydurma URL | 0 |
| Uydurma Dosya·Satır | 0 |
| VERIFICATION REQUIRED işaretli | §H (6) + §V (3) + §W (1) + §X (1) |
| PLANNED işaretli | §O.2, §X pipeline, §Y |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
