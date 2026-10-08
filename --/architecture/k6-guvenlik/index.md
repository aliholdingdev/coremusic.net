---
title: "Architecture — Güvenlik"
type: docs
category: architecture
version: 0.1.0
status: draft
authority: "SSOT: .ai/architecture/k6-guvenlik/index.md (katman girişi — içerik Faz 1+)"
updated: 2026-10-07
tier: 3
domain: k6-guvenlik
ssot: true
risk: medium
owner: "security"
depends-on: [".ai/architecture/00-enterprise-index.md", ".ai/architecture/rules.md"]
---

# Güvenlik — Katman Girişi

> **Durum:** 🅿️ STUB (Faz 0 iskeleti) — içerik **sıfırdan**, kod + web kanıtıyla Faz 1+ kapsamında yazılacak (Q8: kopyalama YOK · Q11: hibrit kaynak).

## 1. Kapsam

JWT, RBAC, CSRF (csrf_token), CSP nonce, rate limit (APCu 60/60s), AES-256-GCM vault, audit — asla bypass edilemez.

- **Sahip agent:** security
- **K-matrix referansı:** [[CLAUDE]] §5 (K-satırı ile hizalanacak)
- **Kurallar (SSOT):** [[architecture/rules]]
- **Katalog:** [[architecture/00-enterprise-index]]

## 2. Bu Katmanda Ne Yaşayacak (iskelet)

```text
k6-guvenlik/
├── index.md            # bu dosya — arc42-tam + vault 8-iskelet hibrit (Q25)
├── README.md · AGENTS.md · CLAUDE.md · WORKFLOW.md   # tam set (Q10)
├── 01-…/02-…/          # derinleşme — yalnız gerektiğinde (Q19)
└── inventory/          # tür-bazlı çoklu-md (min 500 satır/md, Q17/Q21)
```

## 3. Envanter

Henüz yok. Faz 1+ kapı onayı sonrası: repo taraması + web (deepwiki/exa) + K-matrix iskeletiyle doldurulur.
Bileşen ID'leri `Kx.yy.zzz` (Q22) · 12 alan (Q26) · doğrulanmayan satır = `UNKNOWN`.

## 4. Sonraki Adım

Faz planı: [[architecture/00-enterprise-index]] §5 · Tarama kapıları: [[architecture/rules]] R11.
