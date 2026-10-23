---
title: "CoreMusic — Security PRD (Güvenlik Yardımcıları Gereksinim Belgesi)"
type: prd
category: security
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Security — PRD

**docType:** prd · **Modül:** `shared/src/Security/` · **Yüzey:** #12 (aile indeksi §5) · **Sorumlu:** Security Engineer

**Bağlı platform gereksinimleri:** PRD-PLT-004 · PRD-PLT-006

---

## 1. Amaç

Bu PRD, `Security/` modülünün **güvenlik yardımcıları** gereksinimlerini tanımlar: `CacheRateLimiter`, `ReturnUrlPolicy`, `SecurityHelper`, `SessionKeys`, `UuidV7`. Amaç, OWASP kapılarının (PRD-PLT-006) altındaki yardımcıların tutarlı, kriptografik olarak doğru ve sızıntısız olmasıdır.

> **eli10 (basit):** Bu dosya, güvenlik kapılarını besleyen küçük yardımcıların kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü yardımcı hata (ör. zayıf return-url politikası) tüm kapıları zayıflatır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Security/**` (5 sınıf) | Middleware sırası → #10 |
| ReturnUrl politikası (open-redirect savunması) | Auth/Session iç mantığı → #4/#17 |
| UuidV7 üretimi | DB şeması → #13/#25 |

- **Dokunulmaz:** key/anahtar içerikleri (REDACTED) · frozen ADR metinleri.

> **eli10 (basit):** Kapsam yardımcı sınıflar; kapılar ve oturum ayrı PRD'de.
> **eli15 (detay):** Sınır, yardımcının kapı yerine geçmesini engeller — kapı ayrı, yardımcı ayrı katmandır.

---

## 3. Gereksinimler (PRD-SEC-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-SEC-001 | ReturnUrlPolicy yalnız izinli yola izin verir | `//evil.com` gibi mutlak/çift-slash URL'ler reddedilir |
| PRD-SEC-002 | SessionKeys sabittir ve sızdırmaz | Key adları tek kayıt; değer log'a yazılmaz |
| PRD-SEC-003 | UuidV7 sıralı ve tahmin edilemez | Monotonik + 122-bit entropi; UUIDv4'e geri düşülmez |
| PRD-SEC-004 | CacheRateLimiter sayaçları TTL ile yaşar | Sonsuz sayaç yok; cache kaybında fail-safe (fail-open değil) |

> **eli10 (basit):** Dört kural: güvenli dönüş URL'i, sabit session anahtarları, güçlü ID, TTL'li sayaç.
> **eli15 (detay):** Kimlikler ayrı çünkü yardımcı ihlali güvenlik açığı üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | OWASP | PRD-SEC-001/004 — open-redirect + fail-safe |
| 2 | Kripto | PRD-SEC-003 — UuidV7 özelliği |
| 3 | Test | `tests/Security` + `tests/Unit/Security` yeşil |
| 4 | Denetim | Security ENGINEER denetimi; PASS kaydı |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| CSRF · RateLimit · Session | ADR-010 · ADR-013 · ADR-011 (bağ — kapılar) |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Zayıf ReturnUrl politikası | Open redirect / token sızıntısı | PRD-SEC-001 |
| 2 | Rate limit'in fail-open olması | Brute-force | PRD-SEC-004 |

---

## 7. Sonraki Adım

1. #10 (Middleware) ve #17 (Session) PRD'leri ile helper kullanım çapraz kontrol edilir.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
