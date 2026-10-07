---
name: skill-maker-example-02-coremusic-php-skill
description: "Example (not a skill) — CoreMusic PHP domain skill üretim örneği: middleware pipeline, raw PDO ve API-first kurallarıyla v3.0 SKILL.md çıktısı."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  category: example
  updated: 2026-10-07
  template: templates/php-skill-template.md
---

# Örnek 02 — CoreMusic PHP Domain Skill (girdi → çıktı)

> PHP/Backend domain skill'inde `templates/php-skill-template.md` + CoreMusic vault
> kuralları (CLAUDE §6 middleware, §21 yasaklı kalıplar, ADR-002) nasıl enjekte edilir.

## Girdi (kullanıcı isteği — ham)

```text
"PHP middleware zinciri hakkında bir skill oluştur: php-middleware-guard —
yeni middleware yazarken sırayı ve güvenlik kurallarını kontrol etsin."
```

## ADIM 1 Gereksinim

| Soru | Cevap |
|------|-------|
| Ad | `php-middleware-guard` (lowercase-hyphen ✓) |
| Domain | Backend / PHP 8.4 (strict_types) |
| Araç | Vault `.ai/` + php.net web research |
| Çıktı | Sıra doğrulama + ihlal raporu |

## ADIM 2 Web Research (Truth Mode)

| İddia | Kaynak | Sonuç |
|-------|--------|-------|
| PSR-15 middleware interface'leri PHP'de standart | php-fig.org psr-15 + php.net | ✅ Verified |
| PHP 8.4 `strict_types` davranışı | php.net/language.types | ✅ Verified |
| Projeye özel middleware sırası | Vault `.ai/CLAUDE.md` §6 (web değil, SSOT) | ✅ Vault |

## ADIM 3-4 Yapı + Enjeksiyon (üretilen SKILL.md çekirdeği)

```markdown
---
name: php-middleware-guard
description: "Use when writing or reviewing a CoreMusic PHP middleware — checks pipeline order and security invariants. Tetikleyici: 'middleware yaz', 'middleware sırası'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: backend-orchestration
  tags: [php, middleware, security, truth-mode]
  updated: 2026-10-07
---

# php-middleware-guard — Middleware Sıra & Güvenlik Muhafızı

§2 Zorunlu Okumalar: references/rules.md · references/anti-patterns.md
§3 Örnekler: examples/order-audit.md

## Bağlayıcı Sıra (DEĞİŞTİRİLEMEZ — CLAUDE §6 / ADR-010…022)
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
→ BypassAuth → Auth → Permission → Validation → Controller

## Sert Kurallar
- Sıra değişimi = reddet (CSP nonce üretimi SecurityHeaders #4 / SessionManager #5 bozulur).
- Token adı yalnız `csrf_token` (`_csrf_token` YASAK — Guardrail #6).
- Raw PDO (ADR-002); ORM, `SELECT *`, `innerHTML`, `var` YASAK (§21).
- API-first: endpoint önce OpenAPI sözleşmesi (ADR-084) → DTO → kod.
- Secret hardcode yok; `// ⚠️ VERIFICATION REQUIRED` teyitsiz iddiaya.

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 1.0.0 — Updated: 2026-10-07*
```

## ADIM 5 Kalite çıkışı

```text
[x] Vault çelişkisi yok (middleware sırası §6 ile birebir) ✓
[x] Frontmatter kökü: name/description/license/metadata (N1) ✓
[x] description "Use when" + Türkçe tetikleyici (N2) ✓
[x] Footer metadata.version ile hizalı (N9) ✓
[x] examples/ linkli (N4/N5) · CLAUDE.md sidecar yok (N8) ✓
```

---

*CoreMusic Skill v3.0 (örnek 02) — Updated: 2026-10-07*