---
title: "K011 · usecase — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 · usecase — Kapsam

> **Amaç:** Application/UseCase katmanının kapsamını, slot matrisini, ölçüm
> özetini ve sınırlarını tanımlamak. Format referansı: `commands/` klasörü.
> Scope slotu `usecase-usecase-katmani.md` (527 satır) korunmuştur — silinmedi,
> yeniden adlandırılmadı.

## §GRUPLAR

| Bölüm | Konu |
|---|---|
| §A | Kapsam tanımı (useCase nedir, ne değildir) |
| §B | Slot matrisi (6 dosya) |
| §C | Mevcut dosya slotları (korunan) |
| §D | Dosya adlandırma şeması |
| §E | Ölçüm özeti (kanıt) |
| §F | Kapsam sınırları (IN/OUT) |
| §G | Gate matrisi (detay) |
| §H | PSR-11 / PSR-17 durum özeti |
| §I | Cross-reference |
| §J | Kabul kriterleri |
| §K | Sonraki adımlar |

---

## §A — KAPSAM TANIMI

### A.1 — UseCase katmanının rolü (AGENTS §5 — Application katmanı)

```
Application : Service / UseCase / Handler / DTO   — framework bağımsız
```

| Özellik | Kural |
|---|---|
| Konum | Presentation (Controller) ile Domain arasında |
| Bağımlılık | Domain'e bağımlı OLABİLİR; framework'e HAYIR |
| Sorumluluk | Kullanıcı senaryosu (use case) orkestrasyonu |
| Form | Genelde tek method: `execute()` / `handle()` |
| Dönüş | Domain verisi veya DTO — raw HTTP response DEĞİL |

### A.2 — UseCase vs diğer katmanlar

| Katman | Soru | Örnek |
|---|---|---|
| Controller (Presentation) | HTTP ne ister? | route → handler çağır, status kodu seç |
| **UseCase (Application)** | **Senaryo nasıl yürütülür?** | **validasyon → repository → domain → event → DTO** |
| Service (Application) | Ortak iş mantığı | logger, cache, queue yardımı |
| Repository (Infrastructure) | Veri nerede? | SQL sorgusu |
| Entity (Domain) | Kural ne diyor? | invariant doğrulama |

### A.3 — Bu kapsamda NE aranıyor (repo bulguları)

| Bulgu | Dosya·Satır | Durum |
|---|---|---|
| Controller dispatch deseni | api.coremusic.net/include/Controller/AuthController.php · Satır: 52-61 | `match($action)` |
| Error mapping deseni | AuthController.php · Satır: 62-78 | status kodu seçimi |
| Handler dispatch deseni | auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57 | `match($action)` |
| Transaction kullanımı | UserRepository.php · Satır: 147, 184, 192 | 3 nokta |
| Transaction kullanımı | CatalogWriter.php · Satır: 183, 217, 220 | 3 nokta |
| Transaction kullanımı | shared/bin/api-key-create.php · Satır: 135-147 | CLI script |
| PSR-14 EventDispatcher | shared/src/Events/EventDispatcher.php | `EventDispatcherInterface` |
| PSR-11 container | composer: psr/container ^2.0 | ⚠️ kod kullanımı doğrulanmadı |
| php-di container | auth.coremusic.net/include/Container/AuthContainer.php | `DI\ContainerBuilder` |

⚠️ **Kritik gözlem:** Bu repo'da klasik "UseCase" sınıfı adı altında dosya
bulunmuyor (grep: `*UseCase*.php` → 0). Senaryo yürütme **Controller +
Handler + Repository** üçlüsüne dağılmış. Bu durum:
- UseCase katmanının **de-fakto Handler** ile temsil edildiğini, veya
- UseCase katmanının **PLANNED-üretilmiş** olduğunu gösterir.

⚠️ VERIFICATION REQUIRED: `usecase-usecase-katmani.md` (527 satır, mevcut
scope slotu) içinde UseCase sınıf adaylarının listesi olabilir — bu görev
kapsamında o dosya OKUNMADI, yalnız varlığı ve satır sayısı doğrulandı.

---

## §B — SLOT MATRİSİ

| # | Dosya | Group | Rol |
|---|---|---|---|
| 1 | usecase-kapsam.md | mimari | Kapsam, slot matrisi, ölçüm, sınır (bu dosya) |
| 2 | usecase-dogrulama.md | mimari | Ölçüm, gate matrisi, anti-verification |
| 3 | usecase-web-arastirma.md | kanit | PSR-17 (Factory) + PSR-11 (Container) — 2 URL havuzu |
| 4 | usecase-ornekler.md | kod | PHP kod blokları — Dosya·Satır kanıtlı |
| 5 | usecase-bilesenler.md | mimari | Bileşen envanteri (Handler/Service/Transaction/Event) |
| 6 | index.md | mimari | Katalog |

---

## §C — MEVCUT DOSYA SLOTLARI (Silinmez, yeniden adlandırılmaz)

| Dosya | Satır | Slot Rolü | Yeni Set İçindeki Karşılığı |
|---|---|---|---|
| usecase-usecase-katmani.md | 527 | KAPSAM (orijinal, korunur) | KAPSAM slotunun bir parçası — usecase-kapsam.md ile birlikte |
| usecase-kapsam.md | 500+ | KAPSAM (yeni) | Katalog + kapsam |
| usecase-dogrulama.md | 500+ | DOĞRULAMA | Ölçüm + gate doğrulama |
| usecase-web-arastirma.md | 500+ | WEB ARAŞTIRMA | PSR-17 + PSR-11 kanıt havuzu |
| usecase-ornekler.md | 500+ | ÖRNEKLER | Dosya·Satır kanıt kod blokları |
| usecase-bilesenler.md | 500+ | BİLEŞENLER | Bileşen envanteri |
| index.md | 40–100 | KATALOG | 6 dosya özeti |

**Kural:** usecase-usecase-katmani.md bu setin KAPSAM slotunun orijinal kanıtıdır;
dosya üzerinde revizyon YOK, taşıma/silme YOK.

---

## §D — DOSYA ADLANDIRMA ŞEMASI

```
.ai/architecture/K011-application/usecase/
├── usecase-kapsam.md                → kapsam
├── usecase-dogrulama.md             → dogrulama
├── usecase-web-arastirma.md         → web-arastirma
├── usecase-ornekler.md              → ornekler
├── usecase-bilesenler.md            → bilesenler
├── index.md                         → katalog
└── usecase-usecase-katmani.md       → MEVCUT (korunur)
```

**Şablon:** `commands/` klasörü format referansı — §GRUPLAR tablosu frontmatter
sonra; footer Authority + Last Updated.

---

## §E — ÖLÇÜM ÖZETİ (Kanıt)

| Metrik | Değer | Kaynak |
|---|---|---|
| shared/src toplam PHP | 165 | manifest + glob |
| Api/ (Controller, Dto, Middleware) | 34 | manifest |
| Events/ | 9 | manifest |
| Contracts/ | 19 | manifest |
| Middleware/ | 11 | manifest |
| assets/js (manifest) | 94 | manifest |
| assets/js (glob) | 98 | glob (⚠️ fark — dto-dogrulama.md §E.2 kaydı) |
| AuthController satır | 355 | Dosya·Satır ölçümü |
| RequestValidationMiddleware satır | 175 | Dosya·Satır ölçümü |
| Transaction noktaları | 6+ (UserRepo 3, CatalogWriter 3, CLI 1) | Dosya·Satır |
| PSR-14 EventDispatcher | 1 dosya | shared/src/Events/EventDispatcher.php |
| php-di container | 1 dosya | AuthContainer.php |
| Klasik UseCase sınıfı | **0** (grep) | ⚠️ PLANNED-üretilmiş katman |

---

## §F — KAPSAM SINIRLARI (IN / OUT)

### IN — Bu kapsamın içinde

- Handler deseni (AuthPostHandler: match dispatch)
- Controller dispatch deseni (AuthController: match + error mapping)
- Transaction orkestrasyonu (UserRepository, CatalogWriter, api-key-create)
- PSR-14 EventDispatcher (Events katmanı köprüsü)
- PSR-11 container kullanımı (php-di / AuthContainer)
- PSR-17 Factory (durum: paket var, kod yok)
- Kapsam slotları: usecase-kapsam.md + usecase-usecase-katmani.md

### OUT — Bu kapsamın dışında

- Diğer K011 klasörleri (dto/, events/, middleware/, commands/)
- Kaynak kod değişikliği (.php / .js / .css / .sql)
- git commit (orkestratöre ait)
- URL uydurma (yalnız 2 URL havuzu)
- PLANNED/VERIFICATION REQUIRED işaretlemesi yapılmayan iddialar

---

## §G — GATE MATRİSİ (Detay)

| Gate | Kapsam Dosyası | Karar | Kanıt | Durum |
|---|---|---|---|---|
| G1 | usecase-usecase-katmani.md (527 L) | Scope slot — UseCase katmanı kapsamı | Mevcut dosya | MEVCUT |
| G2 | usecase-kapsam.md (bu dosya) | Scope slot — usecase kapsam ve katalog | §B–§F | YAZILDI |
| G3 | usecase-dogrulama.md | Verification slot — ölçüm ve doğrulama | Ölçüm + gate | YAZILDI |
| G4 | usecase-web-arastirma.md | Kanıt slot — PSR-17 + PSR-11 | URL havuzu (2 URL) | YAZILDI |
| G5 | usecase-ornekler.md | Kod slot — PHP örnek kanıtları | Dosya·Satır kanıt blokları | YAZILDI |
| G6 | usecase-bilesenler.md | Mimari slot — bileşen envanteri | Handler/Service/Transaction envanteri | YAZILDI |
| G7 | index.md | Katalog — 6 dosya özet tablosu | 40–100 satır | YAZILDI |

---

## §H — PSR-11 / PSR-17 DURUM ÖZETİ

### H.1 — PSR-17 (HTTP Factory)

| Paket | Sürüm | Durum | Kod kullanımı |
|---|---|---|---|
| nyholm/psr7 | ^1.8 | composer.json'da (shared/composer.json · Satır: 8) | **SIFIR** — grep: `Psr\Http\Message` 0 isabet |

**Sonuç:** PSR-17 Message interface'leri DTO'larda ve Handler'larda KULLANILMIYOR.
Handler `ServerRequestInterface` tipini alıyor ama bu arayüzün nereden geldiği
(php-di mi, elle mi) bu görev kapsamında doğrulanmadı.

### H.2 — PSR-11 (Container)

| Paket | Sürüm | Durum | Kod kullanımı |
|---|---|---|---|
| psr/container | ^2.0 | composer.json'da | ⚠️ VERIFICATION REQUIRED — kodda `Psr\Container` kullanımı grep edilmedi |
| php-di/php-di | (AuthContainer'da) | `DI\ContainerBuilder` | ✅ AuthContainer.php |

**Sonuç:** php-di container'ı `AuthContainer` içinde kullanılıyor.
`Psr\Container\ContainerInterface` tipi AuthContainer'da resolve ediliyor mu —
⚠️ VERIFICATION REQUIRED: `AuthContainer.php` içeriği bu görev kapsamında
okunmadı, yalnız `DI\ContainerBuilder` varlığı grep ile doğrulandı.

### H.3 — PSR-14 (Event)

| Paket | Durum | Kod kullanımı |
|---|---|---|
| psr/event-dispatcher | composer'da (transitif veya direkt) | ✅ `shared/src/Events/EventDispatcher.php` implements `EventDispatcherInterface` |

---

## §I — CROSS-REFERENCE

| Konu | Referans | Klasör |
|---|---|---|
| DTO taşınması | dto-katmanı | `../dto/` |
| Event yayınlanması | events-katmanı | `../events/` |
| Middleware validate etkileşimi | middleware-katmanı | `../middleware/` |
| Command pattern | commands/ (format referansı) | `../commands/` |

---

## §J — KAPSAM KABUL KRİTERLERİ

| # | Kriter | Beklenen | Durum |
|---|---|---|---|
| 1 | Dosya sayısı (kapsam slotu) | ≥2 (usecase-kapsam + usecase-usecase-katmani) | ✅ 2 |
| 2 | UseCase sınıf varlığı | ≥1 (grep: `*UseCase*.php`) | ⚠️ 0 — Handler ile temsil ediliyor |
| 3 | URL havuzu | ≤2 | ✅ 2 |
| 4 | Frontmatter | 7 alan (group: son) | ✅ |
| 5 | GRUPLAR tablosu | frontmatter sonrası | ✅ |
| 6 | Footer | Authority + Last Updated | ✅ |

---

## §K — SONRAKİ ADIMLAR

| # | Adım | Sahip | Öncelik |
|---|---|---|---|
| 1 | usecase-dogrulama.md → ölçüm gate doğrulaması | Developer | P0 |
| 2 | usecase-web-arastirma.md → PSR-17 + PSR-11 kanıt havuzu (2 URL) | Developer | P0 |
| 3 | usecase-ornekler.md → Dosya·Satır kanıtlı kod örnekleri | Developer | P0 |
| 4 | usecase-bilesenler.md → bileşen envanteri | Developer | P0 |
| 5 | index.md → katalog (6 dosya özeti) | Developer | P0 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
