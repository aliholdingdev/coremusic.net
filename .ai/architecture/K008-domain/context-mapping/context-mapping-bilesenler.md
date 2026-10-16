---
node_id: "K008-context-mapping-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-context-mapping-kapsam"
  - "K008-context-mapping-ornekler"
evidence:
  - "grep DomainConfig = 27 isabet / 13 dosya (2026-10-15) — 7 üretim + 5 test + sınıfın kendisi"
  - "grep ContextMap = 0 isabet (.ai/.sql/mysql + shared/src, 2026-10-15)"
  - "domain-master.md §3.1 satır 96 · §3.4 satır 127"
validation: "2026-10-15 — §1 mevcut kademe (2 gerçek bileşen + 12 tüketicisi) + §2 altı PLANNED satır"
open_questions:
  - "ContextMap aggregate + invariant → ADR-097 K4"
  - "domain event → §3.4 boşluğu → ADR bekliyor"
group: mimari
---

# context-mapping — Bileşenler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> §1 mevcut olanı, §2 ise **PLANNED** kademe-3 planını taşır. Disk'te karşılığı
> olmayan sınıf/ad "var" gibi sunulmaz.

## §1 MEVCUT KADEME

| # | Bileşen | Kanıt | Durum |
|---|---|---|---|
| 1 | `domain.php` — 7 subdomain haritası + port eşlemesi | `shared/config/domain.php` satır 1–19 | ✅ DISKTE |
| 2 | `DomainConfig` — final class, 132 satır (constructor fallback, setOverrides, getUrl fail-safe, buildUrl) | `shared/src/Config/DomainConfig.php` satır 1–132 | ✅ DISKTE |
| 3 | `AuthRouteConfig::getAuthUrl/getHomeUrl` — URL üreticisi (DomainConfig parametreli) | `shared/src/Config/AuthRouteConfig.php` satır 33, 39, 52 (grep) | ✅ DISKTE (imza kanıtı) |
| 4 | Tüketiciler — `routes.php`, `PageRouter`, `PageRouterKernel`, `HtmlShellRenderer`, `AuthUrlBuilder`, `SecurityHeadersMiddleware` | grep 27 isabet / 13 dosya (2026-10-15) | ✅ DISKTE |
| 5 | Testler — AuthGuard, AuthUrlBuilder, HtmlShellRenderer ×2, MiddlewarePipeline | aynı grep (5 test dosyası) | ✅ DISKTE |
| 6 | `ContextMap` sınıfı / `context_map` şeması | grep 0 isabet (`.ai/.sql/mysql` + `shared/src`) | ❌ YOK — tasarım adayı |
| 7 | Domain event (outbound) | — | ❌ YOK — master §3.4 satır 127 |

## §2 KADEME-3 PLANI (PLANNED — ADR-097 K4 bekliyor)

| # | Bileşen (aday) | Sorumluluk | Dayanak | Durum |
|---|---|---|---|---|
| 1 | ContextMap (aggregate — ad master satır 96) | bağlamlar arası ilişkinin kaydı (upstream/downstream vb.) | [[context-mapping-kapsam]] §3 satır 2 | ⚠️ PLANNED — diskte 0 karşılık |
| 2 | Domain event (`ContextMapped` / `ContextRelationshipChanged`) | ilişki değişiminin yayılımı | master §3.4 satır 127 boşluğu | ⚠️ PLANNED — needs ADR |
| 3 | Fallback drift düzeltmesi | fallback'e `music` + `admin` eklenmesi | [[context-mapping-dogrulama]] D-CM4 | ⚠️ PLANNED — kod onayı: backend + data-engine |
| 4 | ContextMapper referans uygulaması | context map'inDSL ile modellenmesi | [[context-mapping-web-arastirma]] W-2, W-3 | ⚠️ PLANNED — araç diskte YOK |
| 5 | K-space strangler-fig geçişi | eski not/katmanlardan yenisine kademeli devir | W-4, W-5 | ⏸ BEKLEMEDE — orkestratör (C2 ile ilişkili) |
| 6 | Eşleme gözlemlenebilirliği | `getUrl()` fallback'inin loglanması (şu an sessiz) | [[context-mapping-dogrulama]] §2 | ⚠️ PLANNED — logging sahibi UNKNOWN |

**Yasak:** §2 adları **tasarım adayıdır**; disk'te karşılığı doğrulanmadan
"mevcut" gibi raporlanmaz. `domain.php` domain listesi `shared/AGENTS.md` §4
yasak #4 gereği **onaysız değiştirilmez**; geçiş: kod diske yazıldı +
ADR-097 K4 kilitlendi → `active`. `PageRouter`/`AuthRouteConfig` yalnız
tüketici konumundadır — bağlam eşlemenin sahibi değildir.

**Kardeş dosyalar:** [[context-mapping-kapsam]] · [[context-mapping-dogrulama]] · [[context-mapping-web-arastirma]] · [[context-mapping-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
