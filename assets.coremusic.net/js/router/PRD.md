---
title: "CoreMusic — JS Router PRD (Frontend SPA Router Gereksinim Belgesi)"
type: prd
category: frontend
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] (js çekirdek) · bağ: [[CLAUDE.md]]"
updated: 2026-10-23
---

# assets.coremusic.net/js/router — PRD

**docType:** prd · **Klasör:** `js/router/` · **Yüzey:** #19 (aile indeksi §5) · **Sorumlu:** UI Designer (+ Backend çapraz kontrol)

**Bağlı platform gereksinimleri:** PRD-PLT-008

---

## 1. Amaç

Bu PRD, `js/router/` çekirdeğinin gereksinimlerini tanımlar: **28 dosya** — SPA router çekirdeği (ADR-021/ADR-083) + `config/` (auth-routes, css-selectors, error-types, events, headers, navigate-utils, signal-utils). Amaç, frontend route akışının backend `PageRouter` (#9) ile **aynı desende** olmasıdır (PRD-PLT-008).

> **eli10 (basit):** Bu dosya, tarayıcı adres çubuğunun sayfa değişim kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü router, backend kernel ile el sıkışan frontend yüzüdür; gereksinimi yoksa hash/path sapması ve çift yönlü tutarsızlık doğar.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `js/router/**` (28 dosya + `config/`) | Backend kernel → `shared/src/PageRouter` (#9) |
| Route guard/sinyal/navigate yardımcıları | Sayfa özellik kodu → `features/` (#18) |

- **Dokunulmaz:** frozen ADR metinleri · CSP nonce uyumsuz inline.

> **eli10 (basit):** Kapsam frontend adres motoru; backend çekirdek ve sayfa içerikleri ayrı.
> **eli15 (detay):** Sınır, router'ın sayfa içeriği taşımasını engeller — motor taşır, içerik sayfadadır.

---

## 3. Gereksinimler (PRD-JRT-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-JRT-001 | Router deseni backend PageRouter ile aynı (PRD-PLT-008) | Hash/path sapması yok; çapraz test |
| PRD-JRT-002 | `config/` tek kaynak: auth-routes + css-selectors + events | Route/seçici kod içine gömülmez |
| PRD-JRT-003 | Korumalı route guard'sız render edilmez | Auth guard akışı PRD-PLT-004'e uyar |
| PRD-JRT-004 | `navigate-utils`/`signal-utils` genel kullanımlıdır | Sayfa-özel hack ile kullanılmaz |

> **eli10 (basit):** Dört kural: backend'le aynı desen, config tek kaynak, korumalı route, genel yardımcı.
> **eli15 (detay):** Kimlikler ayrı çünkü router ihlali gezinme/erişim kusuru üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Uyum | PRD-JRT-001 — backend çapraz kontrol |
| 2 | Guard | PRD-JRT-003 — auth |
| 3 | Yapı | PRD-JRT-002/004 — config tek kaynak |
| 4 | Kanıt | `⚠️ VERIFICATION REQUIRED` disiplini |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| SPA router deseni | ADR-021 · ADR-083 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Frontend/backend desen sapması | Çift router | PRD-JRT-001 + #9 |
| 2 | Route'ın kod içine gömülmesi | Config drift'i | PRD-JRT-002 |

---

## 7. Sonraki Adım

1. #9 (PageRouter) PRD'si ile sözleşmeler çapraz kontrol edilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
