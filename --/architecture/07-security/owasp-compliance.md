---
title: "owasp-compliance — Eski OWASP Uyum Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-security
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# owasp-compliance — OWASP Uyumu (stub)

**Durum:** `architecture/07-security/owasp-compliance` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| OWASP alanı | Karşılık | Durum |
|-------------|----------|-------|
| A01 Broken Access Control | RBAC PermissionMiddleware · ReturnUrlPolicy | IMPLEMENTED (`shared/src/Middleware/PermissionMiddleware.php`) |
| A02 Cryptographic Failure | JWT RS256 (JwtService) · şifreleme iddiaları | PARTIAL — ADR-095 IMPLEMENTED; AES/Argon2 ⚠️ VERIFICATION REQUIRED |
| A03 Injection | Raw PDO + prepared statement (ORM/SELECT * yasak) | IMPLEMENTED — ADR-002 · `.ai/CLAUDE.md` §21 |
| A04 Insecure Design | 10-adımlı middleware + ADR-010…022 | IMPLEMENTED — `shared/src/Middleware/` |
| A05 Security Misconfiguration | SecurityHeaders (CSP/HSTS), CORS, gitleaks | IMPLEMENTED — ADR-012 · `.gitleaks.toml` |
| A06 Vulnerable Components | composer audit / dep pins | ⚠️ VERIFICATION REQUIRED (denetim kaydı yok) |
| A07 Identification Failure | Hybrid session+JWT + revocation | IMPLEMENTED — ADR-052/095 |
| A08 Data Integrity | — | ⚠️ VERIFICATION REQUIRED |
| A09 Logging Failures | PSR-3 + 5 log akışı + securityEvent | IMPLEMENTED — `shared/src/Log/` |
| A10 SSRF | OriginCheck | IMPLEMENTED — `shared/src/Middleware/OriginCheckMiddleware.php` |

⚠️ **Ayrı OWASP denetim raporu diskte YOK** — bu tablo kod kanıtından türetilmiştir (2026-10-07).
