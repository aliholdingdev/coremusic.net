# k6-k7-security — Agent Kuralları

## Domain Boundary

Bu klasör, CoreMusic'in güvenlik altyapısını kapsar:
- K6: Auth, RBAC, CSRF, CSP, şifreleme
- K7: Middleware pipeline

## Erişim Yetkisi

| Agent | Erişim | Açıklama |
|-------|--------|----------|
| security-engineer | Tüm dosyalar | Güvenlik uzmanı |
| backend-architect | k7-middleware.md | Middleware entegrasyonu |

## Kurallar

1. Middleware sırası DEĞİŞTİRİLEMEZ
2. CSRF token key: `csrf_token`
3. Tüm güvenlik değişiklikleri ADR gerektirir
4. Audit: [[../../log.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
