---
type: rules
category: core
title: "CoreMusic — Core Rules (Birlesik Kurallar)"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Core Rules

**See also:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]]

---

## 1. Amaç

Tüm agent'lar tarafından kullanılacak **birlesik kural seti**. opencode.json'daki `@.claude/rules/core-rules.md` referansını karşılar.

---

## 2. Hard Guardrails (16 Kural)

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert edilir |
| 2 | Vault First | Kod yazmadan önce vault'u oku | Kod geçersiz |
| 3 | Zero Hallucination | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` | İçerik silinir |
| 4 | In-Place Refactoring | Dosya adı/yolu değişmez | Dosya geri yüklenir |
| 5 | Single Source of Truth | Bilgi sadece `.ai/` vault'tan | Harici bilgi reddedilir |
| 6 | CSRF Token = `csrf_token` | `_csrf_token` yasak (2026-05-30) | Token reddedilir |
| 7 | Middleware Order Immutable | Sıra değişmez (10 katman) | Sistem durdurulur |
| 8 | Port 81 = music.coremusic.net | PHP 8.4 | Yanlış port yasak |
| 9 | No ORM | Raw PDO only (ADR-002) | ORM kullanımı reddedilir |
| 10 | No Frameworks | Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |
| 11 | Mockup Before Frontend | Frontend görevinde mockup okunmadan kod yazılamaz | Kod revert edilir |
| 12 | Contradiction Gate | Vault'ta çelişki varsa kullanıcıya sor, onay bekle | İşlem durur |
| 13 | Session Continuity | Her oturum başlangıcında geçmiş session'dan devam et | Bağlam kaybolur |
| 14 | Human Approval Gate | Mimari karar öncesi kullanıcı onayı zorunlu | Kod revert edilir |
| 15 | Vault-First Mandatory | Vault okumadan plan/kod/faaliyet başlatamaz | İşlem durdurulur |
| 16 | Template Mandatory | Yeni dosya oluştururken template seçilmek ZORUNLU | Dosya geçersiz |

---

## 3. Katman Kuralları (L0-L3)

```
L3 Presentation ──→ L2 Routing ──→ L1 Security ──→ L0 Infrastructure
    (ui-designer)    (backend)      (security)        (data/embedded)
```

| Kural | İzin | Yasak |
|-------|------|-------|
| L3 → L2 | ✅ | — |
| L2 → L1 | ✅ | — |
| L1 → L0 | ✅ | — |
| L0 → L2/L3 | ❌ | Layer Violation → revert |
| L1 → L3 | ❌ | Layer Violation → revert |
| L3 → L0 | ❌ | Layer Violation → revert |

---

## 4. Middleware Pipeline (Frozen — 10 Katman)

```
1.  OriginCheckMiddleware()      — Köken doğrulama
2.  CorsMiddleware()             — CORS header'ları
3.  RateLimiterMiddleware()      — APCu: 60 req/60s
4.  SecurityHeadersMiddleware()  — CSP, HSTS, X-Frame
5.  SessionManagerMiddleware()   — Session + CSP nonce
6.  CsrfMiddleware()             — csrf_token doğrulama
7.  BypassAuthMiddleware()       — Test bypass (prod'da devre dışı)
8.  AuthMiddleware()             — Auth bilgisi inject
9.  PermissionMiddleware()       — RBAC yetki kontrolü
10. ValidationMiddleware()       — Request/DTO validasyonu
→ Controller
```

**Kritik:** CSP nonce SessionManager'da üretilir. Sıra değiştirilirse CSP bozulur.

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `_csrf_token` | `csrf_token` |
| ORM (Eloquent, Doctrine) | Raw PDO |
| `SELECT *` | Explicit columns |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| React / Vue / Angular | Vanilla JS |
| Hardcoded secrets | `.env` / credential vault |
| `eval()` / `Function()` | Safe alternatives |
| `localStorage` for auth | HTTPOnly cookie |
| `var` | `const` / `let` |
| PCM5122 (8.1 surround) | PCM3168A / AK4458 |

---

## 6. Security Standartları

| Parametre | Değer |
|-----------|-------|
| AES-256-GCM IV | 96-bit (12 byte) |
| AES-256-GCM Tag | 16 byte |
| AES-256-GCM Key | 256-bit (32 byte) |
| Argon2id Memory | 64MB |
| Argon2id Time | 4 iterations |
| Argon2id Threads | 2 |
| CSRF Token Key | `csrf_token` |
| CSRF Doğrulama | `hash_equals()` (timing-safe) |
| CSP Nonce | `base64_encode(random_bytes(32))` |

---

## 7. Kodlama Standartları

### 7.1 PHP

| Kural | Açıklama |
|-------|----------|
| `declare(strict_types=1)` | Her dosyada zorunlu |
| PSR-12 | Kod stili standardı |
| Constructor injection | Bağımlılıklar constructor'dan |
| Final classes | Mümkünse final |
| Named arguments | 3+ parametreli call'larda |

### 7.2 JavaScript

| Kural | Açıklama |
|-------|----------|
| Vanilla JS ES6+ | Framework yasak (ADR-001) |
| `const` / `let` | `var` yasak |
| `async` / `await` | Callback hell yasak |
| DOMParser | `innerHTML` yasak |
| ES6 modules | `require()` yasak |

### 7.3 C++

| Kural | Açıklama |
|-------|----------|
| C++20 | Modern C++ |
| `noexcept` | Audio callback'lerde zorunlu |
| `constexpr` | Compile-time hesaplamalar |
| `alignas(64)` | Cache line alignment |
| Zero-allocation | Audio thread'de yasak |

---

## 8. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Ana anayasa | [[CLAUDE.md]] |
| Agent yetkileri | [[AGENTS.md]] |
| Süreçler | [[WORKFLOW.md]] |
| Mimari kararlar | [[brain.md]] |
| Keyword haritası | [[keys.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
