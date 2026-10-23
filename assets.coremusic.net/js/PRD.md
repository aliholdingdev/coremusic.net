---
title: "CoreMusic — JS Çekirdek PRD (Frontend JS Katmanı Gereksinim Belgesi)"
type: prd
category: frontend
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../PRD.md]] (assets) · bağ: [[CLAUDE.md]] (js kuralları)"
updated: 2026-10-23
---

# assets.coremusic.net/js — PRD

**docType:** prd · **Klasör:** `js/` · **Yüzey:** #18 (aile indeksi §5) · **Sorumlu:** UI Designer (+ Security CSP denetimi)

**Bağlı platform gereksinimleri:** PRD-PLT-008 · PRD-PLT-010

---

## 1. Amaç

Bu PRD, `js/` katmanının gereksinimlerini tanımlar: giriş noktaları (`main.js`, `device-loader`, `device-layout-updater`, `devices.config`, `oauth-manager`, `header-manager`, `music-spa-manager`), `core/` (CoreMusicApp, EventBus, footer.init, helper), `features/`, `managers/`, `auth/`, `components/`, `admin/`. Alt dizinler ayrı PRD'dir (#19 router · #20 coreplayer).

> **eli10 (basit):** Bu dosya, sitenin davranışı yazan JS dosyalarının genel kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü Vanilla JS çekirdeği tüm subdomain davranışının taşıyıcısıdır; gereksinimi yoksa framework/dağınık script doğar (ADR-001 ihlali).

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `js/` kök giriş noktaları (7 dosya) + `core/` `features/` `managers/` `auth/` `components/` `admin/` | Router çekirdeği → #19 · CorePlayer → #20 |
| EventBus üzerinden modüller arası iletişim | CSS → #21 · asset servis kuralı → assets PRD (#3) |

- **Ölçüm (disk kanıtı):** `js/` = 8 alt dizin + 8 kök dosya (2026-10-23).
- **Dokunulmaz:** `var`/`eval`/`innerHTML` yasak · CSP nonce uyumsuz inline üretim yasak · frozen ADR metinleri.

> **eli10 (basit):** Kapsam JS çekirdeği; router ve çalar ayrı dosyalarda.
> **eli15 (detay):** Sınır, çekirdeğin sayfa-özel script taşımasını engeller — çekirdek altyapı, sayfa özellik.

---

## 3. Gereksinimler (PRD-JSK-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-JSK-001 | Vanilla ES6+; framework/`var`/`eval`/`innerHTML` yasak | DOMParser + TrustedTypes; ADR-001 |
| PRD-JSK-002 | Modüller arası iletişim `EventBus` üzerinden | Doğrudan global erişim zinciri yok |
| PRD-JSK-003 | `devices.config.js` ↔ `DeviceCssMap.php` senkronu (PRD-AST-004) | İkisi de güncel; drift testle yakalanır |
| PRD-JSK-004 | `oauth-manager.js` token akışı CSP/Security denetiminden geçer | Token DOM'a/görünür log'a sızmaz |

> **eli10 (basit):** Dört kural: saf JS, olay otobüsü, senkron cihaz kaydı, güvenli token akışı.
> **eli15 (detay):** Kimlikler ayrı çünkü JS ihlali hem güvenlik hem davranış kusuru üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Yasaklar | PRD-JSK-001 — framework/eval/innerHTML 0 |
| 2 | Mimari | PRD-JSK-002 — EventBus |
| 3 | Senkron | PRD-JSK-003/004 — config + Security |
| 4 | Kanıt | Ölçülmeyen davranış iddiası `⚠️ VERIFICATION REQUIRED` |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Vanilla JS + ITCSS | ADR-001 |
| CSP | ADR-012 |
| Router | ADR-021 · ADR-083 (bağ → #19) |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Framework/script kütüphanesi girişi | Mimari ihlal | PRD-JSK-001 + review |
| 2 | Token'ın DOM'a sızması | Güvenlik açığı | PRD-JSK-004 |
| 3 | Device config drift'i | Cihazda yanlış layout | PRD-JSK-003 |

---

## 7. Sonraki Adım

1. #19/#20 (router/coreplayer) PRD'leri bu dosyaya bağlanır.
2. Mockup gate: yeni davranış öncesi `01-mockup-index` + PNG (Guardrail #11).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
