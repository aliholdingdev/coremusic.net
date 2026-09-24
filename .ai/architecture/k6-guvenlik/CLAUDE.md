---
title: "CoreMusic — K6 Güvenlik CLAUDE.md"
type: layer-guide
folder: "architecture/k6-guvenlik"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K6 Güvenlik — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | CSRF = `csrf_token` (ADR-010) | CSRF bozulması |
| 2 | Hardcoded secret yasak | Veri sızıntısı |
| 3 | Prepared statement zorunlu | SQL injection |
| 4 | OWASP Top 10 uyumlu | Güvenlik açığı |

## 2. Yasaklı Örüntüler

```php
// ❌ YASAK
$secret = 'my-secret-key';                    // Hardcoded
$token = $_GET['csrf_token'];                 // URL'de
$_SESSION['token'] = $token;                  // Düz kaydet

// ✅ DOĞRU
$secret = $_ENV['JWT_SECRET'];                // .env
$token = $_POST['csrf_token'];                // POST body
$valid = hash_equals($_SESSION['csrf_token'], $token); // Timing-safe
```

## 3. Şifreleme Parametreleri

| Parametre | Değer |
|-----------|-------|
| Algorithm | AES-256-GCM |
| Key | 256-bit (32 byte) |
| IV | 96-bit (12 byte) |
| Tag | 16 byte |
| Password | Argon2id, 64MB, 4 iter |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-010 | csrf_token key zorunlu |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-022 | AES-256-GCM, Argon2id |
| ADR-034 | Credential vault |

---

*K6 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
