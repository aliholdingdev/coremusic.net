---
title: "k0-os-layer/credential-vault — Eski Credential Vault Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-os
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# credential-vault — Credential Vault (stub)

**Durum:** `architecture/k0-k5-software/k0-os-layer/credential-vault.md` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Konu | Kaynak | Durum |
|------|--------|-------|
| Credential vault normalizasyon kararı | `.ai/.decisions/accepted/ADR-034-credential-vault-normalization.md` | ADR |
| Vault'ta tutulan sırlar | `auth.coremusic.net/config/.env` (gitignore'lu) · `.env.example` şablonu | IMPLEMENTED (şema) |
| API key saklama | `.ai/.sql/mysql/coremusic_auth.sql` (api_keys) · `bin/api-key-create.php` | IMPLEMENTED (veri) |
| JWT private key saklama | `.ai/log.md` (P1-9: "private.pem gitignore") | IMPLEMENTED |
| Kod tarafı vault sınıfı | ⚠️ VERIFICATION REQUIRED — dedicated vault sınıfı glob'da yok | PLANNED |

**Domain:** d04+d05 → [[architecture/13-domain-d04-veri-yonetimi]] (K182) · [[architecture/14-domain-d05-guvenlik-middleware]] (K228)
