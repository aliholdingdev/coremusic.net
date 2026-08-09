---
type: system
category: workflow-registry
title: "CoreMusic — Workflow Registry Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 3.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_workflows: 7
---

# CoreMusic — Workflow Registry Index

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Bu dosya, CoreMusic ekosistemindeki tüm iş akışlarının (workflow) merkezi indeksidir. Her iş akışının amacını, adımlarını ve sorumlu ajanları tanımlar.

## 2. Workflow List

| # | Workflow | Amaç | Dosya |
|---|----------|------|-------|
| 1 | Code Review | Çok eksenli inceleme | [[code-review]] |
| 2 | Bug Fix | Kök neden → düzeltme → regresyon | [[bug-fix]] |
| 3 | New Feature | Gereksinim → plan → uygulama → test | [[new-feature]] |
| 4 | Security Audit | OWASP Top 10 uyumluluk | [[security-audit]] |
| 5 | Deployment | Pre-flight → deploy → health check | [[deployment]] |
| 6 | Session Init | Boot protokolü → vault sync | [[session-init]] |
| 7 | Vault Sync | 5 soru → 6 adım | [[vault-sync]] |

## 3. Workflow Haritası

```text
┌─────────────────────────────────────────────────┐
│                Workflow Registry                 │
├─────────────────────────────────────────────────┤
│  Code Review → Bug Fix → New Feature            │
│       ↓              ↓              ↓            │
│  Security Audit → Deployment → Session Init     │
│       ↓              ↓              ↓            │
│  Vault Sync → Quality Report → Completion       │
└─────────────────────────────────────────────────┘
```

## 4. Seviye Bazlı Kullanım

| Seviye | Workflow | Kullanım |
|--------|----------|----------|
| **Günlük** | Code Review, Bug Fix | Her geliştirme döngüsü |
| **Haftalık** | New Feature, Security Audit | Periyodik kontrol |
| **Aylık** | Deployment, Session Init | Periyodik operasyon |
| **Sürekli** | Vault Sync | Değişiklik sonrası |

## 5. İlgili Dokümanlar

- [[WORKFLOW.md]] — Ana iş akışları dokümanı
- [[CLAUDE.md]] — Kanonik AI talimatı
- [[AGENTS.md]] — Agent kayıt defteri
- [[brain.md]] — Mimari kararlar
- [[index.md]] — Master katalog

## 6. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Toplam Workflow** | 7 |
| **Frontmatter** | ✅ Tamamlandı |
| **Cross-Reference** | ✅ Doğrulandı |

---

*Workflow Registry Index v3.0.0 — CoreMusic Workflow Registry*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-08*
*Mode: Red Team · Human Mode · Truth Mode*
