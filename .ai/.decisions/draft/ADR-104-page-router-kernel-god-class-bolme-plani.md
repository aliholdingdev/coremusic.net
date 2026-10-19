---
title: "CoreMusic - ADR-104 (DRAFT): PageRouterKernel & SidebarManager God Class Bölme Planı"
type: "architecture-decision"
category: "architecture"
date: "2026-10-18"
updated: "2026-10-18"
version: "0.1.0"
status: "draft"
authority: "SSOT - god class bölme planı; KOD BÖLMESİ BU ADR İLE YAPILMAZ, ayrı gate oturumunda uygulanır"
kaynak: "P1 SOLID/Clean-Code debate kararı (2026-10-18): god class 'ADR + karakterizasyon test korumalı' maddesi"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic - ADR-104 (DRAFT): PageRouterKernel & SidebarManager God Class Bölme Planı

> **Durum:** 📝 **DRAFT** — Tarih: 2026-10-18 — Onay bekliyor (bölme KODU bu ADR'da yoktur)
> **Karar serisi:** `.ai/.decisions/draft/` — **Slug:** `ADR-104-page-router-kernel-god-class-bolme-plani`
> **İlgili kararlar:** [[accepted/ADR-010-csrf-middleware]] · [[accepted/ADR-021-spa-page-router]] (frozen middleware sırası ADR-010/011/012/013/022 bölmede DEĞİŞMEZ) · [[index.md]]
> **Numara gerekçesi:** draft/ dizininde 097, 098, 102, 103 dolu — **104 ilk boş draft slotu**.

---

## §1 Bağlam (Context)

### §1.1 İhlal

P1 debate (2026-10-18) iki god class tespit etti:

| Sınıf | Kanıt |
|---|---|
| `shared/src/PageRouter/PageRouterKernel.php` | **351 satır** · ctor **14 param** (L40-53: 2 zorunlu + 12 opsiyonel) · **10 private method** · `handle()` **70 satır** (L81-150) |
| `assets.coremusic.net/js/managers/SidebarManager.js` | `class SidebarManager` L606 · tek dosyada 3 sınıf (L258, L370 iç sınıflar) · çoklu sorumluluk |

### §1.2 PageRouterKernel sorumluluk envanteri (dosya:satır kanıtı)

| # | Sorumluluk | Satır |
|---|---|---|
| 1 | Request normalizasyonu (`normalizeRequest` + `extractHeaders`) | L252-279, L281-294 |
| 2 | Route meta çözümleme (`resolveRouteMeta`, B-F-13 contract) | L152-169 |
| 3 | Middleware birleştirme + çalıştırma (`buildDefaultMiddlewares` FROZEN sıra · `runMiddlewareStack`) | L322-342, L296-303 |
| 4 | Response şekillendirme / HTML shell (`wrapInHtmlShell` 4 dal · `collectSessionData` · `buildRedirectUrl`) | L171-226, L232-239, L241-250 |
| 5 | Fatal hata günlüğü (catch: Redactor + çift `file_put_contents` + `error_log`) | L128-149 |
| 6 | SPA tespiti (`isSpaRequest`: XHR + Accept header) | L305-319 |
| 7 | TraceId üretimi (UUID v4) | L344-350 |
| 8 | Orkestrasyon (`handle()`: trace → normalize → meta → middleware → emit) | L81-150 |

**Üretim çağıran yüzey (2 nokta):**

| Dosya | Satır |
|---|---|
| `home.coremusic.net/config/bootstrap.php` | 97 |
| `auth.coremusic.net/index.php` | 218 |

### §1.3 SidebarManager (faz 2 kapsamı)

Bu oturumda listener leak zaten kapatıldı (P1-4/P1-5): `#listeners` kaydı L619-620 · `#listen` L833-842 · `#unbindAll` L846-850 · `destroy()` L1077-1088 (`#unbindAll` + `resizeHandle.destroy` + `scroller.destroy`). Bölme tasarımı bu yeni registry semantiğini hesaba katmak zorundadır.

## §2 Karar (Decision) — PLAN; UYGULAMA AYRI GATE

### §2.1 PageRouterKernel 3 collaborator'a bölünür (kod bu oturumda YAZILMAZ)

| Yeni sınıf | Taşınacak methodlar (kaynak satır) |
|---|---|
| `shared/src/PageRouter/RequestContextFactory.php` | `normalizeRequest` (L252-279) · `extractHeaders` (L281-294) · `isSpaRequest` (L305-319) · `resolveRouteMeta` (L152-169) |
| `shared/src/PageRouter/ShellResponseFactory.php` | `wrapInHtmlShell` (L171-226) · `collectSessionData` (L232-239) · `buildRedirectUrl` (L241-250) — `HtmlShellRenderer` + `ErrorHandler` enjeksiyonu |
| `shared/src/PageRouter/RequestErrorLogger.php` | catch bloğu günlüğü (L128-137: Redactor + çift dosya + error_log) |

**Kernel'de kalan:** `handle()` orkestrasyon · `buildDefaultMiddlewares()` (FROZEN sıra — ADR-010/011/012/013/022) · `runMiddlewareStack()` · `generateTraceId()` · emitter çağrısı. Tahmini: 351 → ~150 satır.

**BC sözleşmesi:** ctor'a **3 opsiyonel nullable** parametre eklenir (mevcut default-construction deseni L55-61 ile aynı); 2 üretim çağran **değişmez**.

### §2.2 Korumа kapısı (gate)

1. `shared/tests/Unit/PageRouter/PageRouterKernelCharacterizationTest.php` — **25 test / 61 assertion, yeşil (2026-10-18)** — bölme ÖNCESİ ve SONRASI çalışır; tek kırmızı = bölme durur.
2. Full shared suite yeşil (373+).
3. Yeni 3 sınıf için birim testi eklenir.
4. Subdomain smoke: home + auth bootstrap'ları değişmeden yüklenir.

### §2.3 SidebarManager (faz 2 — kendi gate'inde)

Ayrı tasarım oturumu: `#listeners` registry + `destroy()` semantiği korunarak sorumluluk ayrıştırma. Kapı: vitest 133 yeşil + destroy sonrası listener sayacı 0 invariantı.

## §3 Alternatifler

| Alternatif | Karar | Gerekçe |
|---|---|---|
| (a) Bölme şimdi, bu oturumda inline | ❌ RED | En büyük riskli refactor, çok uzun oturumun sonunda; taze review yok |
| (b) Status quo, bölme yok | ❌ RED | P1 debate mandate; bakım maliyeti (70 satırlık handle, 14 param ctor) |
| (c) **Draft ADR + karakterizasyon şimdi; bölme ayrı gate'te** | ✅ SEÇİLDİ | Regresyon kapısı hazır, risk izole; karar kaydı oluşur |

## §4 Sonuçlar (Consequences)

- ✅ Karakterizasyon 25/25 yeşil — bölme regresyon kapısı hazır (2026-10-18).
- ✅ Frozen middleware sırası ADR-010/011/012/013/022 dokunulmaz — testte `testDefaultMiddlewareOrderIsFrozen` ile kilitli.
- ⚠️ Kernel ctor'u 17 parametreye çıkar (14 + 3 opsiyonel) — kabul edilen geçici maliyet; uzun vadede factory deseni adayı.
- ⚠️ Bölme kodu bu ADR ile DEĞİLDİR — uygulama ayrı oturumda, §2.2 kapılarıyla.
