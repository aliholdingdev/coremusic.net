---
title: "encryption — Eski Şifreleme Dokümanı (stub-with-truth)"
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

# encryption — Şifreleme (stub)

**Durum:** `architecture/07-security/encryption` **diskte YOK** (eski ağaç silindi) — 4 link referansı.

## Bugünkü Karşılığı (gerçek kanıt)

| Konu | Kaynak | Durum |
|------|--------|-------|
| JWT RS256 imzalama (firebase/php-jwt ^7.2) | `shared/src/Security/JwtService.php` · ADR-095 | IMPLEMENTED |
| DB güvenlik sertleştirme kararı | `.ai/.decisions/accepted/ADR-022-database-hardened-security.md` | ADR (Frozen) |
| Vault / credential kararı | ADR-034 (credential-vault-normalization) | PLANNED (kod kanıtı yok) |
| AES-256-GCM / Argon2id iddiası | `.ai/CLAUDE.md` §12 | ⚠️ VERIFICATION REQUIRED — kod kanıtı doğrulanmadı |

**İlgili domain:** d05 → [[architecture/14-domain-d05-guvenlik-middleware]] (K227–K228)
