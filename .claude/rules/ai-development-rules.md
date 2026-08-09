# AI Development Rules — CoreMusic

**Authority:** ADR-042, ADR-007
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Zero Hallucination Policy

- NEVER fabricate API endpoints, classes, or database tables
- Unverified data MUST be marked: `⚠️ VERIFICATION REQUIRED`
- Always verify against vault documentation before coding
- When uncertain, ask the user
- WEB SEARCH for verification is FORBIDDEN — use vault, scripts, CI/CD

## 2. MSA Limit (Sparse Attention)

- Max 15 files per task (ADR-042/C5)
- Read only relevant files, not entire codebase
- Use `keys.md` for keyword → file mapping
- Fallback to `index.md` if needed

## 3. 10-Step Boot Protocol

Every new session MUST read these files in order:

| # | Dosya | Amaç |
|---|-------|------|
| 1 | `.ai/CLAUDE.md` | Kanonik AI talimatı |
| 2 | `.ai/AGENTS.md` | Agent kayıt defteri |
| 3 | `.ai/WORKFLOW.md` | Süreçler |
| 4 | `.ai/index.md` | Master katalog |
| 5 | `.ai/keys.md` | Anahtar kelime haritası |
| 6 | `.ai/AGENTS.md` | Agent yetkileri (tekrar) |
| 7 | `.ai/brain.md` | Mimari kararlar |
| 8 | `.ai/MEMORY.md` | Oturum hafızası |
| 9 | `.ai/log.md` | Aktivite günlüğü (son 20 satır) |
| 10 | `.claude/rules/*` | Tüm kurallar (core-rules, orchestration, vault, php, js, css, security, testing) |

## 4. Mandatory 5 Skills (ADR-042/C4)

Every agent must use these skills:

1. `/prompt-maker` — prompt generation
2. `/brainstorming` — idea exploration
3. `/vault-sync` — vault synchronization
4. `/hallucination-control` — verification
5. `Red Team · Truth Mode · Human Mode` — always-on protocol

## 5. Session Protocol

1. Read boot files (max 25s)
2. Understand task context
3. Plan before code (Zero Code Before Plan)
4. Execute with verification
5. Log to audit trail
6. Vault-sync if changes made

## 6. Forbidden

- Code before plan approval
- Guessing file paths or APIs
- Reading > 15 files without justification
- Creating documentation files without explicit request
- Modifying frozen ADRs (001-037)
- Hardcoded secrets in code or logs
- `SELECT *` in SQL queries
- ORM usage (ADR-002)
- Frameworks in frontend (ADR-001)

## 7. Layer Architecture (L0-L3)

```
L3 — Presentation  (Frontend, UI, DOM)          ← Vanilla JS, ITCSS
L2 — Routing       (Router, middleware, dispatch) ← PHP 8.4 PageRouter
L1 — Security      (Session, Auth, CSRF, CSP)   ← Middleware Pipeline
L0 — Infrastructure (Database, cache, fs)        ← PDO, APCu, Redis
```

**Dependency Rules:**
- ✅ L3 → L2, L2 → L1, L1 → L0: Allowed
- ❌ L0 → L2/L3, L1 → L3, L3 → L0: FORBIDDEN

## 8. Hard Guardrails

| Rule | Enforcement |
|------|-------------|
| Zero Code Before Plan | No code without approved design |
| No Hallucination | Unverified data → `VERIFICATION REQUIRED` |
| MSA Limit = 15 files | Max 15 files per task (ADR-042/C5) |
| In-Place Refactoring | No file rename/move without approval |
| Single Source of Truth | All info from `.ai/` vault only |
| CSRF Token = `csrf_token` | NOT `_csrf_token` (removed 2026-05-30) |
| Middleware Order Immutable | SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf |
| Port 81 = music.coremusic.net | PHP 8.4, not other ports |

## 9. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Mimari karar | `brain.md` → decisions/ |
| Güvenlik | `security-standards.md` |
| Veritabanı | `database-standards.md` |
| Frontend | `js-standards.md` + `css-standards.md` |
| Backend | `php-standards.md` |
| Test | `testing-standards.md` |
| Vault | `vault.md` |
| Orkestrasyon | `orchestration.md` |
| Temel kurallar | `core-rules.md` |

---

*AI Development Rules v3.0.0 — CoreMusic Enterprise Architecture*
*Last Updated: 2026-08-09*
