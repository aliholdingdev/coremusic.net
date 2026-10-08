---
title: "l2-routing/html-shell-renderer — Eski HTML Shell Renderer Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-routing
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# html-shell-renderer — HTML Shell Renderer (stub)

**Durum:** `architecture/l2-routing/html-shell-renderer(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

- **Kod:** `shared/src/PageRouter/HtmlShellRenderer.php` — IMPLEMENTED (shell render)
- **Şablonlar:** `shared/src/PageRouter/templates/` (dizin — find 2026-10-07)
- **CSP nonce zinciri:** `SecurityHeadersMiddleware` (#4) üretir → `SessionManagerMiddleware` (#5) session'a kaydeder (`.claude/CLAUDE.md` §6 — sıra değiştirilirse CSP bozulur)
- **Panel shell'leri:** `home.coremusic.net/header.php` · `footer.php` · `auth.coremusic.net/pages/*.php`

**Domain tablosu:** [[architecture/15-domain-d06-servis-api]] (K285)
