---
title: "K019-testing/security — Subdomain Kaydı"
node_id: K019-testing/security
node_type: subdomain
status: draft
parent_id: K019-testing
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K019-testing    # domain kökü
  - K009-security   # sınanan politika K009'un — kanıtlı (güvenlik middleware yüzeyi)
evidence:
  - "shared/tests/Security = 2 *Test.php (SessionKeysTest · ReturnUrlPolicyEdgeCaseTest) — glob 2026-10-12"
  - "shared/tests/Middleware = 9 *Test.php (Csrf · Auth · BypassAuth(+FailClosed) · RateLimiter · OriginCheck · SessionManager · Validation · Pipeline) — glob 2026-10-12"
  - "shared/tests/Api/ApiPipelineSecurityTest.php — glob 2026-10-12"
  - "Unit/Security = 2 (JwtServiceTest · ReturnUrlPolicyTest) — glob 2026-10-12"
  - "ci.yml composer-audit job (bağımlılık açığı taraması) + .gitleaks.toml (kök) + secret-scan.yml — okuma/glob 2026-10-12"
validation:
  - "glob + dosya okuma 2026-10-12 — güvenlik testi YÜZEYİ kanıtlı; OWASP kapsam derinliği bağımsız ölçülmedi"
open_questions:
  - "Güvenlik test kapsamı OWASP Top 10 ile hizalı mı? (bağımsız kapsam denetimi YOK — ⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K019-testing/security

## 1. Rol (ELI15)

Güvenlik **politikasının kendisi** K009'da; burada o politikanın gerçekten uygulandığını yoklarız: auth bypass, injection, middleware zinciri, CSRF/CSP otomatik kapıda.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K019-testing altındadır.
- Güvenlik politikası/kararı K009-security'nindir; burada yalnız o politikanın **sınanması** (registry §6 ayrımı).
- Kademe-3 bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıtlı — 14 güvenlik testi + 2 kapı) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
