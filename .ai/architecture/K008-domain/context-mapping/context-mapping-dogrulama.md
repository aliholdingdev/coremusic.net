---
node_id: "K008-context-mapping-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-context-mapping-kapsam"
  - "K008-context-mapping-web-arastirma"
evidence:
  - "satır D-CM1…D-CM16 — 2026-10-15 kaydı"
  - "repo kanıtları: domain.php 1–19 · DomainConfig.php 1–132 · routes.php 1–30 · grep 27 isabet / 13 dosya · ContextMap grep 0 isabet"
validation: "2026-10-15 — 16 iddia: 11 Dosya/Satır, 1 grep-negatif, 2 tasarım/negatif ⚠️, 1 web pointer, 1 drift bulgusu"
open_questions:
  - "fallback drift (5 vs 7 subdomain) → kod düzeltmesi onay gerektirir → raporlandı"
  - "domain event → §3.4 boşluğu → ADR bekliyor"
group: kanit
---

# context-mapping — Doğrulama Defteri (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Web kanıtları URL ile değil [[context-mapping-web-arastirma]] §1 pointer'ı ile verilir (C4).
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN**.

## §1 İDDİA TABLOSU

| # | İddia | Repo kanıtı (Dosya/Satır) | Web kanıtı | Tür | Durum |
|---|-------|---------------------------|-----------|-----|-------|
| D-CM1 | Domain haritası 7 subdomain: auth, home, assets, music, admin, media, api | `shared/config/domain.php` satır 7–15 | — | T4 | ✅ |
| D-CM2 | primary `coremusic.net`; `primary_port` 80; `subdomain_port` 80; `home` ayrı port 81 | aynı dosya satır 4–6, 16–18 | — | T4 | ✅ |
| D-CM3 | Constructor iki aday yol dener: `dirname(__DIR__, 3)` ve `dirname(__DIR__, 4)` + `/config/domain.php` | `shared/src/Config/DomainConfig.php` satır 16–19 | — | T4 | ✅ |
| D-CM4 | **DRIFT:** fallback config yalnız 5 subdomain içerir (auth, home, assets, media, api) — `music` ve `admin` **YOK** | aynı dosya satır 29–40 vs domain.php satır 11–12 | — | T4 | ✅ (bulgu) |
| D-CM5 | Dosya bulunursa `require` edilir, harita `subdomains` anahtarından alınır | aynı dosya satır 41–44 | — | T4 | ✅ |
| D-CM6 | `setOverrides(scheme, host, port)` çalışma anını bindirir (`runtimeScheme/Host/Port`) | aynı dosya satır 47–52 | — | T4 | ✅ |
| D-CM7 | `getScheme()` → runtime → config['scheme'] → `'http'` zinciri | aynı dosya satır 54–57 | — | T4 | ✅ |
| D-CM8 | `getUrl()` bilinmeyen subdomain'de primary URL'e düşer (exception fırlatmaz) | aynı dosya satır 89–96 | — | T4 | ✅ |
| D-CM9 | `getSubdomainPortByName()` → `subdomain_ports[name]` → 80 (ör. home→81) | aynı dosya satır 69–72 + domain.php satır 16–18 | — | T4 | ✅ |
| D-CM10 | `buildUrl()` port 80/443'te URL'e eklenmez | aynı dosya satır 124–131 | — | T4 | ✅ |
| D-CM11 | `isDevelopment()` = primary port 80/443 değilse true | aynı dosya satır 113–117 | — | T4 | ✅ |
| D-CM12 | routes.php: "Tüm URL'ler DomainConfig'den dinamik üretilir. Hardcoded domain KULLANILMAZ." + `new DomainConfig(...)` | `shared/config/routes.php` satır 10–16 | — | T4 | ✅ |
| D-CM13 | DomainConfig tüketicileri: 7 üretim dosyası (routes, PageRouter, PageRouterKernel, HtmlShellRenderer, AuthUrlBuilder, SecurityHeadersMiddleware, AuthRouteConfig) + 5 test dosyası | grep `DomainConfig` = 27 isabet / 13 dosya (2026-10-15) | — | T4 | ✅ |
| D-CM14 | **ContextMap sınıfı ve şeması diskte YOK** — aggregate design-only | grep `ContextMap`/`context_map` → `.ai/.sql/mysql` 0 isabet · `shared/src` 0 isabet (2026-10-15) | — | negatif | ✅ (negatif kanıt) |
| D-CM15 | ContextMap (ilişki kaydı) = aggregate **adayı**; domain event **YOK** | domain-master.md §3.1 satır 96 · §3.4 satır 127 | — | tasarım | ⚠️ ADR-097 K4 + VERIFICATION REQUIRED |
| D-CM16 | stratejik modelleme & geçiş referansları: hexagonal · ContextMapper ×2 · strangler fig ×2 · DDD kitapları ×3 | — | [[context-mapping-web-arastirma]] §1 W-1…W-8 | T3 | ✅ HTTP 200 · 2026-10-15 |

## §2 NOTLAR

- **D-CM4 — en somut bulgu:** fallback listesi dar (5) tutulmuş; `domain.php`
  okunamazsa `getUrl('music')`/`getUrl('admin')` primary URL'e düşer —
  crash değil, sessiz degradation. Düzeltme kod değişikliğidir; bu görev
  salt-okunur vault yazımıdır → **rapor, düzeltme** (onay: backend + data-engine).
- D-CM14 + D-CM15 birlikte okunmalı: `ContextMap` ne sınıftır ne şema —
  yalnızca master §3.1'de aday olarak durur; "mevcut" gibi raporlanamaz.
- D-CM8'deki fail-safe, domain event üretmez (event zaten §3.4'te YOK) —
  eşleme sessizdir, gözlemlenebilirliği `UNKNOWN`.
- 7 subdomain'in yalnız `music`'i K008 alt alanlarıyla örtüşür; `auth/home/
  assets/admin/media/api` deployments bağlamıdır (K-space dışı).

**Kardeş dosyalar:** [[context-mapping-kapsam]] · [[context-mapping-web-arastirma]] · [[context-mapping-ornekler]] · [[context-mapping-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
