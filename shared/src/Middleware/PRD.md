---
title: "CoreMusic — Middleware PRD (HTTP Middleware Katmanı Gereksinim Belgesi)"
type: prd
category: security
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Middleware — PRD

**docType:** prd · **Modül:** `shared/src/Middleware/` · **Yüzey:** #10 (aile indeksi §5) · **Sorumlu:** Security Engineer (+ Backend Architect)

**Bağlı platform gereksinimleri:** PRD-PLT-006

---

## 1. Amaç

Bu PRD, `Middleware/` modülünün **HTTP güvenlik/zincir** gereksinimlerini tanımlar: Auth, BypassAuth (ADR-008), CSRF (ADR-010), RateLimiter (ADR-013), SecurityHeaders, Session, CORS, Origin, Permission (10+ middleware) ve `MiddlewarePipeline` kaydı. Amaç, OWASP kapılarının her istekte **aynı sırayla** çalışmasıdır.

> **eli10 (basit):** Bu dosya, her isteğin geçtiği güvenlik kapılarının ve sırasının kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü middleware sırası güvenlik karardır (ADR-020); gereksinimi yoksa
> kapılar atlanabilir. Okunması, kapı eklerken/düzenlerken kontrolü verir.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Middleware/**` (10+ middleware + Pipeline) | Auth akışının iç mantığı → auth PRD (#4/#22) |
| Pipeline kaydı sırası | Route listesi → `config/routes.php` (#24) |
| BypassAuth kapsamı (yalnız ADR ile) | API'ye özel middleware → `src/Api/Middleware/` (#11) |

- **Ön koşul:** ADR-020 (middleware sırası), ADR-008/010/013.
- **Dokunulmaz:** BypassAuth kapsamı ADR'siz genişletilemez · frozen ADR metinleri.

> **eli10 (basit):** Kapsam ortak HTTP kapıları; auth içi ve API'ye özel kapılar ayrı PRD'de.
> **eli15 (detay):** Sınır, bypass'ın sessizce genişlemesini engeller — kapsam yalnız ADR ile değişir.

---

## 3. Gereksinimler (PRD-MDW-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-MDW-001 | Pipeline kaydı tam ve sıra ADR-020'ye uygun | Kayıtsız middleware devrede değil; sıra testi yeşil |
| PRD-MDW-002 | CSRF her POST/form'da (ADR-010) | Tokensiz istek 403 |
| PRD-MDW-003 | RateLimit her hassas uçta (ADR-013) | Sınır aşımı 429; bypass yok |
| PRD-MDW-004 | SecurityHeaders + CORS/Origin kapıları aktif | CSP/XFrame/Origin reddi davranışları testli |

> **eli10 (basit):** Dört kural: sıralı zincir, CSRF, rate limit, başlık/origin kapıları.
> **eli15 (detay):** Kimlikler ayrı çünkü kapı ihlali OWASP hedefine dönüşür; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | OWASP | PRD-MDW-002..004 — Security PASS (PRD-PLT-006) |
| 2 | Sıra | PRD-MDW-001 — ADR-020 |
| 3 | Test | `tests/Middleware` + `tests/Unit/Middleware` yeşil |
| 4 | Kanıt | `⚠️ VERIFICATION REQUIRED` disiplini (PRD-PLT-011) |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Middleware sırası | ADR-020 |
| BypassAuth kapsamı | ADR-008 |
| CSRF · RateLimit · Session | ADR-010 · ADR-013 · ADR-011 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Sıranın değişmesi | Kapı atlanması | PRD-MDW-001 + ADR-020 |
| 2 | BypassAuth kapsam genişletilmesi | Auth deliği | PRD §2 dokunulmazlık + ADR-008 |
| 3 | Tek uçta rate limit atlanması | Brute-force | PRD-MDW-003 |

---

## 7. Sonraki Adım

1. #11 (Api PRD) ile `src/Api/Middleware/` (6 API middleware) ilişkisi netleştirilir.
2. #12 (Security PRD) ile RateLimiter/SecurityHelper çapraz kontrol edilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
