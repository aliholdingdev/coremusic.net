---
title: "CoreMusic — Events PRD (Event Dispatcher Gereksinim Belgesi)"
type: prd
category: shared
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Events — PRD

**docType:** prd · **Modül:** `shared/src/Events/` · **Yüzey:** #15 (aile indeksi §5) · **Sorumlu:** Backend Architect

**Bağlı platform gereksinimleri:** PRD-PLT-005

---

## 1. Amaç

Bu PRD, `Events/` modülünün gereksinimlerini tanımlar: `EventDispatcher` + 9 domain event + 3 integration event (ADR-086), `Domain/` ve `Integration/` ayrımı. Amaç, subdomain'ler arası gevşek bağlı iletişimin **arayüz üzerinden, tek dispatcher** ile yürütülmesidir.

> **eli10 (basit):** Bu dosya, sitelerin birbirine "haber verme" sisteminin kurallarını söyler.
> **eli15 (detay):** Ayrı yazdı çünkü olay sistemi bağımlılığı gevşetir; gereksinimi yoksa subdomain'ler birbirini doğrudan çağırır (PRD-PLT-005 ihlali).

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Events/**` (Dispatcher + Domain + Integration) | Event abonelerinin iş mantığı → ilgili modül/subdomain |
| `DomainEventInterface` sözleşmesi | Şema/migration (#13/#23/#25) |

- **Dokunulmaz:** frozen ADR metinleri.

> **eli10 (basit):** Kapsam olay yayma mekanizması; olayları kimin işleyeceği ayrı yerde.
> **eli15 (detay):** Sınır, dispatcher'ın iş mantığı taşımasını engeller — yayar, işlemez.

---

## 3. Gereksinimler (PRD-EVT-001..003)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-EVT-001 | Yeni domain event `src/Events/Domain/` + `DomainEventInterface` ile gelir | Arayüzsüz event yok; test eklenir |
| PRD-EVT-002 | Domain / Integration ayrımı korunur (ADR-086) | Tek dosyada karışık event yok |
| PRD-EVT-003 | Abone hatası yayıcıyı düşürmez | Listener exception'ı loglanır, zincir kırılmaz |

> **eli10 (basit):** Üç kural: arayüzlü event, ayrılmış türler, dayanıklı yayılım.
> **eli15 (detay):** Kimlikler ayrı çünkü olay ihlali gizli bağımlılık üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Sözleşme | PRD-EVT-001/002 — arayüz + ayrım |
| 2 | Dayanıklılık | PRD-EVT-003 — hata izolasyonu |
| 3 | Test | `tests/Events` + `tests/Component` yeşil |
| 4 | Kanıt | `⚠️ VERIFICATION REQUIRED` disiplini |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Event modeli | ADR-086 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Event'siz doğrudan çağrı | Sıkı bağlanma | PRD-EVT-001 + PRD-PLT-005 |
| 2 | Listener hatasının yayılması | Zincir kırılması | PRD-EVT-003 |

---

## 7. Sonraki Adım

1. #11 (Api) ve #16 (Cache) PRD'leri ile event kullanım noktaları çapraz kontrol edilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
