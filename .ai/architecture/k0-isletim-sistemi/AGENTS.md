---
title: "k0 İşletim Sistemi — Agent Kapsamı"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/AGENTS.md (registry SSOT) — bu dosya katman kapsamı"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: false
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md", ".ai/AGENTS.md"]
---

# k0 — Agent Kapsamı ve Sınırları

**Sahip agent:** `win-sw` (Windows Software Engineer) — K0 katmanı envanteri, derinleştirme ve doğrulama.
**Yardımcı:** `devops-engineer` (container/CI kanıtı) · `security-engineer` (03-guvenlik-izolasyon politikaları).

| Agent | Bu katmanda | Yapamaz |
|---|---|---|
| win-sw | Platform çekirdek dokümanı, API envanteri, WASAPI/COM kanıtı | K6 auth politikası yazmak (security) |
| devops-engineer | Container/CI kanıtı, Dockerfile envanteri | Platform API kararı vermek |
| security-engineer | Sandbox/seccomp/capability politika onayı | Dosya isimlendirmesini değiştirmek |
| data-engineer | — | (bu katmanda yetkisi yok) |

**Domain boundary:** `.ai/architecture/k0-isletim-sistemi/**` yalnız sahip agent + MO tarafından düzenlenir; ihlalde R8 (öncelik) + `.ai/AGENTS.md` §6 devreye girer.
**Sınıflandırma:** A0 alanı (K0-K5 altyapı) — katman matrisi: [[architecture/00-enterprise-index]].
