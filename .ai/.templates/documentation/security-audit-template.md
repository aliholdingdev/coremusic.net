---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Security Audit Template"
type: security-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Security Audit Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic güvenlik denetim raporunu standartlaştırmaktır: OWASP Top 10:2025 kontrol listesi, middleware pipeline doğrulaması, güvenlik parametreleri, sensitive data kontrolü, tespit edilen açık listesi ve sonuç formatını tek iskelette toplar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Güvenlik denetim raporu (Markdown) | Uygulama kodu değişikliği |
| OWASP Top 10:2025 + middleware pipeline denetimi | Performans testi |
| Kripto/session/CSRF/CSP parametre doğrulaması | Donanım denetimi (bkz. hardware-template) |

- **Dosya tipi:** Markdown güvenlik denetim raporu
- **Kullanan agent:** Security Engineer (birincil · AGENTS.md §6), QA Engineer / Backend Architect (ikincil)
- **Guardrail:** #16 (Template Mandatory) — yeni denetim raporu bu şablondan başlar

## 3. Mimari

Rapor iskeleti. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); placeholder ve tablolar birebir korunmuştur. Denetim kontrol listeleri (OWASP, Middleware, Sensitive Data) §4.1-§4.3'tedir.

### {{TITLE}}

**Denetim Tarihi:** {{DATE}}
**Denetçi:** {{AUTHOR}}
**Kapsam:** OWASP Top 10:2025

---

#### 3.1 Güvenlik Parametreleri

| Parametre | Beklenen Değer | Gerçek Değer | Durum |
|-----------|---------------|-------------|-------|
| CSRF Token Key | `csrf_token` | {{CSRF_KEY}} | ✅/❌ |
| CSP | strict-dynamic, nonce | {{CSP_VALUE}} | ✅/❌ |
| HSTS | max-age=31536000 | {{HSTS_VALUE}} | ✅/❌ |
| Argon2id Memory | 64MB | {{ARGON2_MEMORY}} | ✅/❌ |
| Argon2id Time | 4 iterations | {{ARGON2_TIME}} | ✅/❌ |
| AES-256-GCM IV | 96-bit (12 byte) | {{AES_IV}} | ✅/❌ |
| Session Timeout | 3600s | {{SESSION_TIMEOUT}} | ✅/❌ |
| Rate Limit | 60 req/60s | {{RATE_LIMIT}} | ✅/❌ |

---

#### 3.2 Tespit Edilen Açık Listesi

| # | Açıklama | Severity | Dosya | Öneri |
|---|----------|----------|-------|-------|
| 1 | {{FINDING_1}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_1}} | {{FIX_1}} |
| 2 | {{FINDING_2}} | CRITICAL/HIGH/MEDIUM/LOW | {{FILE_2}} | {{FIX_2}} |

---

#### 3.3 Sonuç

**Genel Durum:** ✅ GÜVENLİ / ⚠️ İYİLEŞTİRME GEREKLİ / ❌ KRİTİK AÇIK

**Öneriler:**
1. {{RECOMMENDATION_1}}
2. {{RECOMMENDATION_2}}

---

## 4. Kurallar

Denetim sırasında uygulanan zorunlu/yasak kurallar:

- **Zorunlu:** kapsam her denetimde OWASP Top 10:2025'tir; her satır ✅/⚠️/❌ ile işaretlenir (§4.1).
- **Zorunlu:** middleware sırası §4.2'deki 1-10 düzeni değişmez; ihlal → derhal revert + log ERROR (AGENTS.md §5, §17).
- **Zorunlu:** güvenlik parametreleri §3.1'deki *Beklenen Değer* sütununa birebir eşleşmelidir.
- **Zorunlu:** açık severity değerleri `CRITICAL/HIGH/MEDIUM/LOW` ile yazılır; sonuç üçlüsü ✅/⚠️/❌ kullanır (§3.2, §3.3).
- **Yasak:** hardcoded secret, `.env` commit'i, log'da sensitive data, error message'da stack trace (§4.3).
- **Yasak:** `{{DATE}}`, `{{AUTHOR}}`, `{{CSRF_KEY}}`… `{{RECOMMENDATION_2}}` placeholder'ları doldurulmadan rapor kapatılamaz.
- **Uyarı:** doğrulanamayan gerçek değer `⚠️ VERIFICATION REQUIRED` ile işaretlenir; tahmin yazmak yasaktır.

#### 4.1 OWASP Top 10:2025 Kontrol Listesi

| # | Risk | Durum | Not |
|---|------|-------|-----|
| A01 | Broken Access Control | ✅/⚠️/❌ | RBAC uygulanmış |
| A02 | Cryptographic Failures | ✅/⚠️/❌ | AES-256-GCM + Argon2id |
| A03 | Injection | ✅/⚠️/❌ | Prepared statement |
| A04 | Insecure Design | ✅/⚠️/❌ | Threat modeling yapılmış |
| A05 | Security Misconfiguration | ✅/⚠️/❌ | CSP nonce-based |
| A06 | Vulnerable Components | ✅/⚠️/❌ | `composer audit` |
| A07 | Auth Failures | ✅/⚠️/❌ | JWT + Session hybrid |
| A08 | Data Integrity Failures | ✅/⚠️/❌ | CSRF token |
| A09 | Logging Failures | ✅/⚠️/❌ | Structured logging |
| A10 | SSRF | ✅/⚠️/❌ | Whitelist only |

#### 4.2 Middleware Pipeline Doğrulaması

| # | Middleware | Sıra | Durum |
|---|-----------|------|-------|
| 1 | OriginCheckMiddleware | 1 | ✅/❌ |
| 2 | CorsMiddleware | 2 | ✅/❌ |
| 3 | RateLimiterMiddleware | 3 | ✅/❌ |
| 4 | SecurityHeadersMiddleware | 4 | ✅/❌ |
| 5 | SessionManagerMiddleware | 5 | ✅/❌ |
| 6 | CsrfMiddleware | 6 | ✅/❌ |
| 7 | BypassAuthMiddleware | 7 | ✅/❌ |
| 8 | AuthMiddleware | 8 | ✅/❌ |
| 9 | PermissionMiddleware | 9 | ✅/❌ |
| 10 | ValidationMiddleware | 10 | ✅/❌ |

#### 4.3 Sensitive Data Kontrolü

| Kontrol | Durum |
|---------|-------|
| Hardcoded secret kodda yok | ✅/❌ |
| `.env` dosyası gitignore'da | ✅/❌ |
| Log'da sensitive data yok | ✅/❌ |
| Error message'da stack trace yok | ✅/❌ |
| API key'ler hashlenmiş | ✅/❌ |

---

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/documentation/security-audit-template.md` (Guardrail #16).
2. **KOPYALA:** dosyayı denetim raporu konumuna kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** `{{TITLE}}`, `{{DATE}}`, `{{AUTHOR}}`; §3.1'de `{{CSRF_KEY}}`…`{{RATE_LIMIT}}` gerçek değerlerle; §3.2'de `{{FINDING_*}}`/`{{FILE_*}}`/`{{FIX_*}}`; §3.3'te `{{RECOMMENDATION_*}}`.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu; §4.1-§4.3 listeleri işaretli; AGENTS.md §24.5 kontrol listesi geçti.
5. **COMMIT:** raporu commit et; bulgular CRITICAL/HIGH ise AGENTS.md §10 eskalasyon protokolüne göre ilgili ajana handover yap.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §4.1 OWASP, §4.2 middleware sırası, §4.3 sensitive data kontrolleri işaretli

**REFACTOR REPORT:** FILE: security-audit-template.md · PURPOSE: Security Audit Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: security → Security Engineer), middleware sıra doğrulaması, eskalasyon §10
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*Security Audit Template v2.0.0 — CoreMusic Security Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
