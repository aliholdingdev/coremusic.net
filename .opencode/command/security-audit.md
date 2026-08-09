---
description: "Guvenlik denetimi yap, aciklari tespit et, duzeltmeleri oner"
agent: security-engineer
---

# Security Audit Komutu

Guvenlik denetimi yapar, aciklari tespit eder.

## Nasil Calisir?

1. OWASP Top 10 kontrolü
2. CSRF/CSP/XSS taramasi
3. Auth mekanizmasi denetimi
4. Veritabani guvenligi
5. Rapor olustur

## Kullanim

```
/security-audit [denetim alani]
```

## Denetim Alanlari

| Alan | Kontroller |
|------|------------|
| Authentication | Login, register, session, password |
| Authorization | RBAC, yetki bypass, privilege escalation |
| CSRF | Token varligi, SameSite, Double Submit |
| CSP | Nonce, strict-dynamic, script-src |
| XSS | innerHTML, eval, document.write |
| SQL Injection | Prepared statements, parameterized queries |
| Encryption | AES-256-GCM, Argon2id, key management |

## Kontrol Listesi

```
✅ CSRF token her form'da var mi?
✅ CSP nonce her script'te var mi?
✅ Session timeout uygulaniyor mu?
✅ Password hash Argon2id mi?
✅ PDO prepared statement kullaniliyor mu?
✅ innerHTML kullanimi var mi? (YASAK)
✅ Hardcoded secret var mi? (YASAK)
✅ Rate limiting aktif mi?
✅ CORS dogru yapilandirilmis mi?
✅ Security headers mevcut mu?
```

## Vault Context

- `.ai/architecture/l1-security/` — Guvenlik mimarisi
- `.ai/decisions/accepted/ADR-010-csrf-protection-strategy`
- `.ai/decisions/accepted/ADR-011-session-management`
- `.ai/decisions/accepted/ADR-022-database-hardened-security`
- `.claude/rules/security-standards.md`

## Cikti Formati

```markdown
# Guvenlik Denetim Raporu

## Tarih: [tarih]
## Kapsam: [alan]

## Tespit Edilen Sorunlar
| # | Sorun | Onem | Dosya | Cozum |
|---|-------|------|-------|-------|
| 1 | [aciklama] | Kritik/Yuksek/Orta/Dusuk | [dosya] | [cozum] |

## Gecilen Kontroller
- [x] CSRF korumasi
- [x] CSP yapilandirmasi
- [ ] Rate limiting

## Oneriler
1. [oneri 1]
2. [oneri 2]
```
