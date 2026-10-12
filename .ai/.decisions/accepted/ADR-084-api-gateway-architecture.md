---
title: "CoreMusic — ADR-084: API Gateway Architecture (API-First · BFF · CQRS=PLANNED)"
type: "architecture-decision"
category: "architecture"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "accepted"
authority: "API istemcilerinin tek giriş noktası api.coremusic.net Gateway'dir; route/DTO/pipeline sözleşmesi kodda IMPLEMENTED, CQRS ve OpenAPI spec PLANNED'tir (2026-10-07 kullanıcı kararı: 'ADR'ları yaz + CQRS'i PLANNED yap')."
kaynak: "brain.md §13 ADR-084 metni + disk kanıtı (2026-10-07): api.coremusic.net/{index.php,config/routes.php,include}, shared/src/Api/{Gateway,Api/Registry,Api/Versioning,Api/Dto,Api/Bff,Api/Middleware} · şablon adr-template.md"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-084: API Gateway Architecture

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-10-07 (dosya boşluktan dolduruldu) · **Ağırlık:** 1 · **İlgili ADR:** 020, 083, 085, 094, 095
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `api-gateway-architecture` · **Dosya:** `ADR-084-api-gateway-architecture.md`
> **Not:** Bu numara kayıtlıydı, dosyası yoktu (`index.md`: "NO FILE on disk — brain.md only"). 2026-10-07'de disk gerçeğiyle dolduruldu; numara ATANMADI, mevcut 084 kullanıldı.

---

## §1 Bağlam (Context)

### §1.1 Disk Kanıtları (2026-10-07 ölçümü)

| Yüzey | Kanıt | Durum |
|-------|-------|-------|
| Gateway + dispatch | `shared/src/Api/Gateway.php` + `GatewayInterface.php` | ✅ IMPLEMENTED |
| Route table + versiyon | `Api/Routing/RouteTable`, `Api/Versioning/{ApiVersion,VersionRegistry,VersionResolver}`, `Api/Registry/ServiceRegistry` | ✅ IMPLEMENTED |
| Pipeline | `Api/Middleware/*` + `ApiMiddlewarePipeline` — ADR-094 ile **8 kapı** (ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication → CSRF·session → RequestValidation → Authorization) | ✅ IMPLEMENTED |
| DTO | `Api/Dto/` | ✅ IMPLEMENTED |
| BFF | `Api/Bff/`: **Desktop, Embedded, Mobile, Spa** (+BffLayer) = **4** | ✅ IMPLEMENTED (kısmi) |
| BFF Admin/Car | `grep AdminBff\|CarBff` → 0 | ❌ PLANNED (idddia 6, disk 4) |
| OpenAPI spec | `find openapi*\|swagger*` → 0 dosya | ❌ PLANNED (§6A.1 "önce OpenAPI" — spec yok) |
| CQRS | `grep CQRS\|CommandBus\|QueryBus` → 0 | ❌ **PLANNED** (kullanıcı kararı 2026-10-07) |
| ADR-020 rejim | API ayrı pipeline (§ADR-094 ile OriginCheck/CSRF eklendi) | ✅ IMPLEMENTED |

### §1.2 Kısıtlamalar

- Cookie (session) + Bearer (JWT/ADR-095) hybrid kimlik; public route önekleri `AuthenticationMiddleware::PUBLIC_ROUTE_PATTERNS`.
- JSON-only sözleşmesi: `{data,meta}` / `{error:{code,message}}`; exception mesajı asla dönmez.
- auth kodu KOPYALANMAZ — `CoreMusic\Auth\` composer autoload ile auth.coremusic.net'ten gelir (SSOT).

---

## §2 Karar (Decision)

### §2.1 Karar

1. **Gateway tekilliği:** tüm API istemcileri `api.coremusic.net` üzerinden; routing/versiyon/rate-limit/auth Gateway'de.
2. **BFF×4** (Desktop/Embedded/Mobile/Spa) implement; **Admin/Car BFF PLANNED**.
3. **CQRS PLANNED** — anayasa §6A.3 iddiası kodda yoktur; implemente edilene kadar CQRS'ten bahseden vault satırları bu ADR'ye atıfla "PLANNED" okunur.
4. **OpenAPI PLANNED** — sözleşmeden-kod kuralı spec dosyası yokken OpenAPI'ye bağlanana kadar askıda (sonraki tur: spec üretimi → kod).

### §2.2 Gerekçe

Kodda olan ile iddia edileni ayırmak (zero-hallucination); Gateway/BFF altyapısı fiilen çalışıyor ve ADR-094/095 ile güçlendirildi; CQRS/OpenAPI aceleye getirilmez — implemente olmadan "var" denmeyecek.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Neden reddedildi |
|---|-----------|------------------|
| 1 | Vault'tan ADR-084'ü tamamen silmek | Karar numarası kayıtlı (brain §13, prompt eşlemesi); silinmesi numara boşluğu + atıf kırılması yaratır. |
| 2 | CQRS'i şimdi implement etmek | Kod kanıtı yok, kapsam büyük (bus/handler/read-model); kullanıcı "PLANNED yap" dedi (2026-10-07). |
| 3 | OpenAPI'siz devam + kuralı kaldırmak | API-first anayasa kuralı (§6A) korunur; spec sonraki fazda üretilir. |

---

## §4 Sonuçlar (Consequences)

**Olumlu:** iddia-disk hizası · Gateway/BFF için tek referans · CQRS/OpenAPI artık açıkça PLANNED (yanlış "var" iddiası yok).

**Olumsuz / Risk:** anayasa §6A.3 hâlâ "CQRS" gibi konuşur → okuyucu bu ADR'ye atıf yapmalı (P9 vault senkronu notu) · BFF Admin/Car eksikliği UI/admin API ihtiyaçlarında ortaya çıkar.

**Risk:** Düşük (doküman kararı; kod değişikliği yok).

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar (2026-10-07)

Dosya oluşturuldu (brain metni + disk ölçümü); `index.md` satırı "NO FILE" yorumundan gerçek link'e çevrildi; log.md'ye kayıt. **Kod değişikliği yok** (ADR-094/095 ile ilgili pipeline/CLI değişiklikleri ayrı commit'lerde).

### §5.2 Rollback

Dosya + index satırı geri alınır; brain.md §13 metni zaten durur (çakışma yok).

---

## §6 İlgili Dokümanlar

`api.coremusic.net/` (index, routes, include) · `shared/src/Api/**` · [[ADR-020-api-public-security]] · [[ADR-083-spa-router]] · [[ADR-094-api-pipeline-origin-csrf]] · [[ADR-095-hybrid-jwt-rs256-access-token]] · [[../brain.md]] §13

---

## §7 Onay

| Tarih | Karar Veren | Durum | Not |
|-------|-------------|-------|-----|
| 2026-10-07 | Bayram Ali (Vault Steward) · Claude Code | ACCEPTED | Refactor P2-11 — kullanıcı kararı: "ADR'ları yaz + CQRS'i PLANNED yap" |
