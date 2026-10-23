---
title: "CoreMusic — OAuth PRD (OAuth Katmanı Gereksinim Belgesi)"
type: prd
category: security
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · [[../../PRD.md]] · bağ: [[CONTEXT.md]] (shared envanter)"
updated: 2026-10-23
---

# shared/src/OAuth — PRD

**docType:** prd · **Modül:** `shared/src/OAuth/` · **Yüzey:** #14 (aile indeksi §5) · **Sorumlu:** Security Engineer (+ Backend Architect)

**Bağlı platform gereksinimleri:** PRD-PLT-004 · PRD-PLT-006

---

## 1. Amaç

Bu PRD, `OAuth/` modülünün gereksinimlerini tanımlar: `OAuthManager` + 12 provider (Base · Discord · Facebook · Instagram · LinkedIn · Pinterest · Reddit · Snapchat · TikTok, ...). Amaç, üçüncü taraf girişlerinin **tek yönetim noktasından**, token'lar sızmadan ve SSO (PRD-PLT-004) ile uyumlu yürütülmesidir.

> **eli10 (basit):** Bu dosya, "Facebook/Google ile giriş" gibi dış bağlantıların kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü OAuth token'ı en hassas sızıntı yüzeyidir; gereksinimi yoksa her provider elle bağlanır ve secret yönetimi dağılır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/OAuth/**` + `Provider/**` (12 provider) | Provider listesi config → `config/oauth-platforms.php` (#24) |
| Token alımı/değişimi/depolama akışı | SSO oturum içeriği → #17 (Session) |
| OAuth route'ları | `config/auth-routes.php` (#24) |

- **Dokunulmaz:** client secret/anahtar içerikleri (REDACTED) · frozen ADR metinleri.

> **eli10 (basit):** Kapsam OAuth akış kodu; sağlayıcı listesi ve oturum ayrı PRD'de.
> **eli15 (detay):** Sınır, secret'ın config dışına taşmasını engeller — secret `.env`/config'de kalır.

---

## 3. Gereksinimler (PRD-OAU-001..004)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-OAU-001 | Provider eklemek Base sınıfından türetilir | Provider başına ayrı copy-paste kod yok |
| PRD-OAU-002 | Token/refresh yalnız ilgili depolama üzerinden yazılır | Log/.md'ye token yazımı 0 (REDACTED) |
| PRD-OAU-003 | `state` doğrulaması zorunlu (CSRF) | State'siz callback reddedilir |
| PRD-OAU-004 | SSO akışı PRD-PLT-004'e uyar | Yeni yüzey OAuth'u kopyalamaz; akış auth'a bağlı |

> **eli10 (basit):** Dört kural: ortak temel, gizli token, state kontrollü, tek SSO.
> **eli15 (detay):** Kimlikler ayrı çünkü OAuth ihlali hesap ele geçmesi üretir; denetlenebilir olmalıdır.

---

## 4. Kabul Kriteri

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | OWASP | PRD-OAU-002/003 — secret + state |
| 2 | SSO | PRD-OAU-004 — PRD-PLT-004 uyumu |
| 3 | Test | `tests/OAuth` + `tests/Unit/...` yeşil |
| 4 | Denetim | Security PASS; secret denetimi |

---

## 5. ADR Bağları

| Karar | ADR |
|-------|-----|
| SSO/RBAC | PRD-PLT-004 (bağ) |
| Middleware kapıları | ADR-010 · ADR-013 (bağ) |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** |

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | `state`'in atlanması | OAuth CSRF | PRD-OAU-003 |
| 2 | Token'ın log/.md'ye sızması | Hesap ele geçirme | PRD-OAU-002 + REDACTED |
| 3 | Provider başına kod kopyası | Bakım/kırılganlık | PRD-OAU-001 |

---

## 7. Sonraki Adım

1. #22 (auth/include PRD) ile OAuth akışının auth'a bağlanışı doğrulanır.
2. #24 (config PRD) ile `oauth-platforms.php` listesi hizalanır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
