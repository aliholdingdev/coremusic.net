---
title: "CoreMusic — Session PRD (Oturum Katmanı Gereksinim Belgesi)"
type: prd
category: security
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/Session — PRD

**docType:** prd · **Modül:** `shared/src/Session/` · **Yüzey:** #17 (aile indeksi §5) · **Sorumlu:** Security Engineer

**Bağlı platform gereksinimleri:** PRD-PLT-004

---

## 1. Amaç

Bu PRD, `Session/` modülünün gereksinimlerini tanımlar: `SessionBootstrapper`, `Config`, `Initializer`, `Lifecycle` (ADR-011). Amaç, tüm subdomain'lerin **tek oturum mekanizması** üzerinden çalışmasıdır (PRD-PLT-004 SSO); subdomain oturumu kopyalamaz, bu modülü çağırır.

> **eli10 (basit):** Bu dosya, "giriş yapılmışlık" halinin tek ve güvenli tutulmasının kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü oturum ihlali doğrudan hesap ele geçmesi üretir; gereksinimi yoksa her panel kendi session'ını icat eder.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Session/**` (Bootstrapper, Config, Initializer, Lifecycle) | Subdomain oturum köprüsü → `HomeSessionManager` (#5) / auth (#4/#22) |
| Oturum yaşam döngüsü (başlatma, yenileme, sonlandırma) | Session middleware sırası → #10 |

- **Dokunulmaz:** oturum/cookie değerleri log'a yazılmaz · frozen ADR metinleri.

> **eli10 (basit):** Kapsam oturum çekirdeği; panel/auth köprüleri ayrı PRD'de.
> **eli15 (detay):** Sınır, çekirdeğin uygulama-özel oturum taşımasını engeller — çekirdek genel, köprü özeldir.

---

## 3. Gereksinimler (PRD-SES-001..003)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-SES-001 | Bootstrapper tek giriş noktası (ADR-011) | Modül dışı oturum başlatma yok |
| PRD-SES-002 | Lifecycle (başlat/yenile/sonlandır) eksiksiz | Zaman aşımı ve logout temiz kapanır |
| PRD-SES-003 | Oturum verisi sızdırmaz | Log/.md'ye session/cookie değeri 0 |

> **eli10 (basit):** Üç kural: tek başlangıç, tam döngü, sızıntısız veri.
> **eli15 (detay):** Kimlikler ayrı çünkü oturum ihlali OWASP hedefine dönüşür; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | SSO | PRD-SES-001 — tek mekanizma (PRD-PLT-004) |
| 2 | Yaşam döngüsü | PRD-SES-002 — timeout/logout temiz |
| 3 | Sızıntı | PRD-SES-003 — Security denetimi |
| 4 | Test | `tests/Unit/...` oturum testleri yeşil |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| Session modeli | ADR-011 |
| Middleware kapısı | ADR-010 · ADR-013 (bağ) |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Panelin kendi oturumu | SSO kırığı | PRD-SES-001 + PRD-PLT-004 |
| 2 | Zaman aşımının atlanması | Terk edilmiş oturum | PRD-SES-002 |
| 3 | Session değerinin log'a sızması | Oturum ele geçirme | PRD-SES-003 |

---

## 7. Sonraki Adım

1. #4/#22 (auth) ve #5 (home) PRD'leri ile köprü çapraz kontrol edilir.
2. #12 (Security PRD) ile SessionKeys kullanımı hizalanır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
