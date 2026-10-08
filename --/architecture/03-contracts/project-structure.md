---
title: "project-structure — Eski Proje Yapısı Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-contracts
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# project-structure — Proje Yapısı (stub)

**Durum:** `architecture/03-contracts/project-structure` **diskte YOK** (eski ağaç silindi).
Link: `.ai/ROLE.md` §8 · `.ai/index.md` · `.ai/keys.md`.

## Bugünkü Karşılığı — Gerçek Repo Yapısı (2026-10-07 glob)

```text
C:\www\coremusic.net\
├── auth.coremusic.net/      # Merkezi auth (PHP 8.4) — IMPLEMENTED
├── home.coremusic.net/      # Home panel (PHP + tests) — IMPLEMENTED
├── api.coremusic.net/       # API gateway — IMPLEMENTED
├── media.coremusic.net/     # Media servisi CLI (src/Media, bin) — IMPLEMENTED
├── assets.coremusic.net/    # Statik: Css (11 katman) + js (94) + Fonts — IMPLEMENTED
├── shared/                  # coremusic/shared-infrastructure v2.0.0 (165 PHP, 20 namespace)
├── bin/                     # api-key-create.php
├── .github/workflows/       # ci.yml · secret-scan.yml
└── .ai/                     # Vault (SSOT)
```

**Paneller YOK (hedef mimari):** music · admin · download · car · studio · pro · landing
(glob: kök `*/` listesi — 2026-10-07). Detay: [[architecture/16-domain-d07-uygulama-ux]] (K327–K333 = PLANNED).
