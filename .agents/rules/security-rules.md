---
title: "CoreMusic — Güvenlik Kuralları"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §6, §7, §21-§23 · .ai/AGENTS.md §16"
docType: claude
---

# CoreMusic — Güvenlik Kuralları (Security Rules)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** security-engineer (**veto yetkisi var**)

**Zorunlu Bağlantılar:** [[RULES]] · [[../../.ai/CLAUDE]] · [[../../.ai/AGENTS]] · [[architecture-rules]]

> **eli10:** Güvenlik yüzeyinde tek söz sahibi security agent'dır; bulgu düzeltmez, bildirir ve durdurur.

---

## 1. Amaç

OWASP Top 10:2025 kapsamındaki CoreMusic güvenlik kurallarını; şifreleme, kimlik, CSRF/CSP, secret yüzeyi ve yasak pattern'ler halinde vault SSOT'larından toplar. **K6 katmanı hard guardrail'dır: asla bypass edilemez.**

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Auth, session, CSRF, CSP, rate limit, şifreleme | Mimari sıralama → [[architecture-rules]] §3.3 |
| Secret/anahtar yüzeyi (.env, vault, CI) | Veri şeması → [[database-rules]] |
| Yasak pattern'ler (§3.4) ve edge case'ler | Donanım koruma rölesi → [[audio-hardware-rules]] |

---

## 3. Mimari

### 3.1 Kripto Standartları (`@.ai/CLAUDE.md` §12 + kök `AGENTS.md` §5)

| Alan | Zorunlu Değer |
|------|---------------|
| Şifreleme | **AES-256-GCM** (NIST SP 800-38D) / ChaCha20-Poly1305 |
| Parola hashing | **Argon2id** |
| Yasak algoritmalar | MD5 · SHA-1 · DES (üretimde asla) |
| Token/nonce | Her GCM işlemi benzersiz nonce — nonce yeniden kullanımı yasak |
| JWT | Session + JWT birlikte (middleware #8); rol RBAC (middleware #9) |

### 3.2 Middleware Güvenlik Zinciri (immutable — [[architecture-rules]] §3.3)

CSP nonce üretimi **SecurityHeaders (#4)** içindedir; **SessionManager (#5)** bu nonce'u session'a kaydedilir. Sıra değişimi = CSP bozulması = güvenlik açığı (`@.ai/CLAUDE.md` §23 #1).

- **CSRF:** token adı **`csrf_token`** — `_csrf_token` yasak (Guardrail #6, ADR-010). Multi-Tab: session-bound tek token.
- **Rate limit:** APCu 60 req/60s (RateLimiter #3).
- **BypassAuth (#7):** yalnız test ortamında aktif (Soft Constraint #4 — onay: Security Engineer); prod'da devre dışı.
- **Session:** 3600s idle timeout; timeout → otomatik yeniden auth (ADR-011).

### 3.3 Secret Yüzeyi (Zero-Hallucination + REDACTED)

| Kural | Değer |
|-------|-------|
| Üretim yeri | Secret yalnız `.env` / credential vault / env substitution |
| **Yasak yazım yerleri** | `.md` · `.json` · `.log` · `.yml` içine anahtar/sır asla yazılmaz |
| Vault'ta | `[REDACTED]` maskeleme (Edge Case #3) |
| Log'da hassas veri | Maskeleme zorunlu |
| Hardcoded secret | Kodda/log'da → veri sızıntısı, CRITICAL (`@.ai/CLAUDE.md` §23 #3) |
| CI | GitLeaks taraması temiz olmalı; secret `.yml`'e gömülmez |

### 3.4 Yasak Pattern'ler (`@.ai/CLAUDE.md` §21 — güvenlik kırılımı)

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| `_csrf_token` | `csrf_token` |
| Hardcoded secrets | `.env` / credential vault |
| `eval()` / `Function()` | Safe alternatives |
| `localStorage` / `sessionStorage` auth için | Session-based auth (HTTPOnly cookie) |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| `unserialize(user_input)` | Kök `AGENTS.md` §5 (PHP) |

### 3.5 Edge Cases & Uyarılar (`@.ai/CLAUDE.md` §22-§23 + `.ai/AGENTS.md` §17)

| Durum | Çözüm / Sonuç |
|-------|---------------|
| Middleware sırası değiştirme | CSP bozulur → güvenlik açığı |
| `SELECT *` | SQL injection riski |
| Network outage | Offline-First + SQLite queue |
| Vault corruption | `git checkout` + son commit |
| Sensitive data log'da | `[REDACTED]` |
| CSRF/CSP uyumsuzluğu | Escalation L1(Security)→L2, timeout 15s (`.ai/AGENTS.md` §10.1) |

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | K6 bypass edilemez | Derhal durdurma |
| 2 | Secret belgeye/koda/günlüğe sızmaz | CRITICAL + MO bildirimi (`opencode/AGENTS.md` §3.7 #9) |
| 3 | Güvenlik bulgusu agent tarafından **düzeltmez**, bildirir | Düzeltme security agent'a handover |
| 4 | Fail-safe: hata durumunda **fail-closed** (fail-open yasak) | Revert |
| 5 | OWASP denetimi yüksek riskli işlerde zorunlu (`security-review` / `security-hardening` skill) | Görev tamamlanmaz |
| 6 | Yetkisiz auth bypass (ADR-008 kapsamı dışı) | Revert + escalation |

---

## 5. Workflow

```text
Güvenlik etkili iş (auth/session/secret/middleware/_CRYPTO)
  → Skill: security-hardening (Skill Usage Mandate)
  → Vault: @.ai/CLAUDE.md §6 + §21-§23 + ADR-010/011 (frozen, okunur)
  → Uygulama (security agent)
  → OWASP + §3.4 pattern taraması
  → QA kapısı → MO: log.md append
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Kripto değerleri vault §12 ile eşleşme | AES-256-GCM · Argon2id |
| 3 | CSRF token adı tek | `csrf_token` (Guardrail #6) |
| 4 | Secret yasağı 3 yerde tekrarlı | §3.3 satırı mevcut |
| 5 | Veto yetkisi açıkça yazıyor | §1 header |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Anayasa §6 | [[../../.ai/CLAUDE]] | Middleware pipeline |
| Anayasa §21-§23 | [[../../.ai/CLAUDE]] | Yasak pattern + uyarılar |
| Registry §16-§18 | [[../../.ai/AGENTS]] | Quality/warnings/edge |
| CSRF ADR | [[../../.ai/.decisions/accepted/ADR-010-csrf-protection-strategy]] | Frozen |
| Session ADR | [[../../.ai/.decisions/accepted/ADR-011-session-management]] | Frozen |
| Master kurallar | [[RULES]] §4.4 | Özet |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
