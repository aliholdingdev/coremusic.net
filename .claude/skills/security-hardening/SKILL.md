---
name: security-hardening
description: "Use when changing auth, session, CSRF, CSP, rate-limit, encryption, or security headers in CoreMusic — Tetikleyiciler: 'güvenlik değişikliği', 'CSRF ekle', 'CSP ayarı', 'auth kodu'."
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: security
  tags: [security, csrf, csp, session, encryption, rbac, coremusic]
  updated: 2026-10-07
---

# security-hardening — CoreMusic Güvenlik Değişiklik Protokolü

## §1 Genel Bakış

Bu skill, CoreMusic'te **güvenlik yüzeyinde herhangi bir değişiklik** (auth, session, CSRF,
CSP, rate-limit, şifreleme, security header) uygulanırken izlenecek bağlayıcı protokolü
tanımlar. Kaynak: `.ai/CLAUDE.md` §6/§7/§21/§23 + ADR-010/011/012/013/022.

Üç mutlak gerçek (kaynaksız ihlal edilemez):

1. **Middleware sırası DEĞİŞTİRİLEMEZ:** `OriginCheck → Cors → RateLimiter → SecurityHeaders
   → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller`.
   CSP nonce üretimi SecurityHeaders (#4) içindedir, SessionManager (#5) bunu session'a
   kaydeder; sıra değişirse CSP + CSRF bozulur (Guardrail #7, CLAUDE §23-1, ADR-010/011/012/013/022).
2. **K6 güvenlik katmanı = hard guardrail — asla bypass edilemez.**
3. **Token adı `csrf_token`'dır; `_csrf_token` yasaktır** (Guardrail #6, 2026-05-30).

## §2 When-to-use

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| 'güvenlik değişikliği' | Auth/session/CSRF/CSP/rate-limit/header koduna dokunulacağı zaman | ✅ |
| 'CSRF ekle' / 'CSP ayarı' | Token veya politika eklenirken/değiştirilirken | ✅ |
| 'auth kodu' | Login/session/RBAC akışı yazılırken | ✅ |
| Vault `.md` / prompt işi | Belge üretimi | ❌ → `skill-maker` / `.ai/.templates/` |

## §3 Otonom Çalışma Protokolü (emniyet odaklı)

1. **Change tanımla:** Hangi güvenlik yüzeyi değişiyor (auth · session · CSRF · CSP ·
   rate-limit · şifreleme · header) — tek cümle.
2. **Threat check:** Bu değişiklik hangi saldırı sınıfını etkiler (XSS · CSRF · brute force ·
   sızıntı)? İlgili ADR'yi (§6) oku; tahmin yok, kaynak var.
3. **Order check:** Pipeline #1-#10 sırasına dokunuyor mu? **Dokunuyorsa DUR** — sıra
   değişikliği yalnız `MiddlewarePipelineTest` + ADR güncellemesiyle ve kullanıcı onayıyla
   (Guardrail #7/#14) yapılır. Nonce zinciri (#4 üretir → #5 kaydeder) bozulamaz.
4. **Verify (Truth Mode):** Kod kanıtı (dosya+satır) veya vault § ile doğrulanır; her iddia
   kaynaklı — doğrulanamayan → `⚠️ VERIFICATION REQUIRED`, bilinmeyen → `UNKNOWN`.
   Yasaklı kalıp taraması (§5) + test kapısı geçmeden "tamam" denmez (Guardrail #3).

## §4 Zorunlu Okumalar

| # | Dosya | İçerik | Ne zaman okunur |
|---|-------|--------|-----------------|
| 1 | `references/middleware-security-order.md` | Tam pipeline tablosu, immutability, CSP nonce zinciri, sıra bozulması sonuçları | Her güvenlik değişikliğinde (önce) |
| 2 | `references/crypto-standards.md` | AES-256-GCM, Argon2id, JWT+session hibrit, RBAC rolleri, session kuralları (3600s, HTTPOnly) | Auth/şifreleme/oturum işinde |
| 3 | `references/forbidden-patterns.md` | Yasaklı↔doğru kalıp tablosu (snippet'li) | Kod yazım öncesi + doğrulama adımında |

## §5 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/csrf-change-walkthrough.md` | CSRF'i/superclass'ünü doğru uygulama: `csrf_token`, session-bound tek token (multi-tab), middleware #6 konumu, doğrulama adımları + ters sıranma sonucu |

## §6 Truth Mode & Güvenlik

- **Zero Hallucination:** repository/class/ADR/satır kanıtsız yazılmaz → `⚠️ VERIFICATION REQUIRED`.
- **Secret yasak:** API key/token/şifre hiçbir skill dosyasına hardcode edilmez (`.env`/vault —
  ADR-015; REDACTED).
- **Yıkıcı komut / prod değişikliği** (sıra değişikliği, bypass genişletme, deploy) kullanıcı
  onayına bağlıdır (Guardrail #14). 3 ardışık başarısız düzeltme → DUR + 1 soru (MAX THINKING #5).
- **H001 Reddi:** mimariye ters/güvensiz yapı (async-scope, `_csrf_token`, `unsafe-inline`,
  fail-open genişletme) reddedilir ve kullanıcı uyarılır.

## §7 Otorite & Vault Bağlantıları

| Kaynak | Bağlantı |
|--------|----------|
| `.ai/CLAUDE.md` §6 | Middleware pipeline (immutable) + CSP nonce zinciri |
| `.ai/CLAUDE.md` §7 | 16 Hard Guardrail (#6 CSRF adı, #7 sıra, #9 no-ORM, #14 onay) |
| `.ai/CLAUDE.md` §21 | Forbidden Patterns |
| `.ai/CLAUDE.md` §23 | Critical Warnings (1-7) |
| `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` | Üç katmanlı CSRF, `csrf_token`, #6 sıra |
| `.ai/.decisions/accepted/ADR-011-session-management.md` | 3600s idle, HTTPOnly cookie, oturum hijyeni |
| `.ai/.decisions/accepted/ADR-012-csp-nonce-strict-dynamic.md` | CSP strict-dynamic + nonce, unsafe-* kalıcı kapalı |
| `.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md` | APCu 60 req/60s, brute force |
| `.ai/.decisions/accepted/ADR-022-database-hardened-security.md` | PII AES-256-GCM, prepared statement, K6 ile hizalı |

SSOT çelişkisi → DUR + kullanıcıya sor (Guardrail #12). Vault çelişirse öncelik:
`.ai/CLAUDE.md > .ai/AGENTS.md > .ai/WORKFLOW.md` (CLAUDE §2.1).

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*