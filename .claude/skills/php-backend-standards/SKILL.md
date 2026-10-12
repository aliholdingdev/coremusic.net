---
name: php-backend-standards
description: "Use when writing or reviewing PHP 8.4 backend code, middleware, endpoints, or PDO queries in CoreMusic — Tetikleyiciler: 'PHP kodla', 'middleware ekle', 'endpoint yaz', 'PDO sorgusu'."
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: backend
  tags: [php, pdo, middleware, api-first, bcnf, coremusic]
  updated: 2026-10-07
---

# PHP Backend Standards (CoreMusic)

## §1 Genel Bakış

CoreMusic PHP 8.4 backend standartlarının tek yönlendiricisidir: immutable middleware
pipeline, raw PDO (ORM yasak), API-First sözleşme akışı, APCu rate limit, port/CSRF/session
sabitleri. Kaynak: `.ai/CLAUDE.md` §6 / §6A / §18 / §21 + ADR-002 / ADR-040. Kod yazımadan
önce bu skill'in referansları okunur; vault ile çelişirse vault kazanır (Contradiction Gate → DUR + sor).

## §2 When-to-use

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| 'PHP kodla' / 'backend yaz' | PHP 8.4 dosyası, sınıf, servis üretimi | ✅ |
| 'middleware ekle' | Pipeline'a eleman, sıralama, CSP/nonce işi | ✅ |
| 'endpoint yaz' | Yeni route/-controller/handler — API-First akışı | ✅ |
| 'PDO sorgusu' | SELECT/INSERT/UPDATE/JOIN, repository kodu | ✅ |
| Frontend CSS/JS bileşen görevi | Vault `.ai/ui-design/**` işi | ❌ → `ui-code-generator` / Guardrail #11 |
| Vault `.md` / prompt üretimi | Döküman iskeleti | ❌ → `.ai/.templates/` / `prompt-maker` |

## §3 Otonom Çalışma Protokolü

1. **Analiz:** İstek incelenir — endpoint mi, middleware mi, sorgu mu? Hangi DB/coremusic_* şeması?
2. **Doğrulama (Truth Mode):** Aşağıdaki Zorunlu Okumalar + ilgili ADR okunur; teyitsiz iddia
   → `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN` (Zero-Hallucination, Guardrail #3).
3. **Execution:** OpenAPI sözleşmesi → DTO → Validation → Use Case → raw PDO kodu (ADR-002
   bağlantı imzası + prepared statement) yazılır; middleware sırası değiştirilmez.
4. **Raporlama:** Değişen dosya, uygulanan kural (§/ADR), doğrulanamayan noktalar tek tek
   listelenir. Test/build yoksa "tamam" denmez.

## §4 Zorunlu Okumalar

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| [references/middleware-pipeline.md](references/middleware-pipeline.md) | 11 adımlı immutable pipeline, CSP nonce mekaniği, yeniden sıralama sonuçları | Middleware/route/CSP/session işine dokunmadan önce |
| [references/pdo-rules.md](references/pdo-rules.md) | Raw PDO, prepared statement, BCNF (18 DB / 156 tablo), yasaklı sorgular | Her PDO/repository kodu öncesi ve code review'de |
| [references/api-contract-flow.md](references/api-contract-flow.md) | OpenAPI→DTO→… akışı, Gateway, BFF×6, CQRS, Event Bus, SPA→ApiClient | Yeni endpoint/spec/BFF işinde |

## §5 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| [examples/add-endpoint.md](examples/add-endpoint.md) | Playlist endpoint'i: spec → DTO → validation → use case → prepared PDO repository + doğru middleware sırası (tam döngü) |

## §6 Truth Mode & Güvenlik

- **Zero Hallucination:** bu skill'deki her fact yukarıdaki vault kaynaklarından gelir;
  kaynaksız iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
- **H001 Reddi:** ORM, `SELECT *`, `_csrf_token`, concat-sorgu, middleware yeniden sıralama,
  hardcoded secret, localStorage/sessionStorage auth → reddedilir, kullanıcı uyarılır.
- **Güvenlik:** API key/secret kod yazılmaz (`.env` / credential vault); yıkıcı komut
  (silme/deploy) kullanıcı onayına bağlıdır; BypassAuth prod'da devre dışıdır.
- **Vault uyumu:** `.ai/` kurallarıyla (CLAUDE §7, AGENTS §5) çelişilemez → DUR + sor.

## §7 Otorite & Vault Bağlantıları

| Kaynak | Bağlantı |
|--------|----------|
| `.ai/CLAUDE.md` §6 | Middleware pipeline (immutable) + CSP nonce kuralı |
| `.ai/CLAUDE.md` §6A | API-First, Gateway, BFF×6, CQRS, Event Bus, SPA→ApiClient |
| `.ai/CLAUDE.md` §18 / §21 / §23 | 18 BCNF DB · Forbidden Patterns · Critical Warnings |
| `.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md` | PDO zorunlu, ORM yasak, bağlantı imzası |
| `.ai/.decisions/accepted/ADR-040-database-authority.md` | 18 BCNF otoritesi (156 tablo / 82 FK) |
| ADR-084 (API-First) / ADR-086 (Event Driven) | `⚠️ VERIFICATION REQUIRED` — tekil ADR dosyaları diskte YOK; içerik `.ai/CLAUDE.md` §6A/§6A.4'tedir |

Sabitler: `strict_types` · port 81 music/Control · 3001 download · 5000/6000 media · 80 admin ·
CSRF `csrf_token` (`_csrf_token` YASAK) · session timeout 3600s · rate limit APCu 60 req/60s ·
auth = session-based (localStorage/sessionStorage auth YASAK).

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*