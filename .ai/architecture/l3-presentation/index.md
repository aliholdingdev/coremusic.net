---
type: architecture
category: l3
title: "L3 — Presentation Layer Index"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L3 — Presentation Layer Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[engine.md]]

---

## 1. Amaç

CoreMusic platformunun sunum katmanını tanımlar. Vanilla JS, ITCSS 7-layer CSS mimarisi, TrustedTypes, Web Audio API ve UI component'leri bu katmanda yönetilir. [[ADR-001-vanilla-js-itcss]] ile uyumludur.

---

## 2. Mimari Konum

```
L3 Presentation (Bu Katman)
  ↓ Vanilla JS, ITCSS, Web Audio
L2 Routing
  ↓ PHP PageRouter
L1 Security
  ↓ SessionManager → Csrf
L0 Infrastructure
```

**Bağımlılık:** ✅ L3 → L2 | ❌ L3 → L1, L3 → L0

---

## 3. Dosya Yapısı

| Dosya | Amaç |
|-------|------|
| [[itcss-architecture]] | ITCSS 7-layer CSS mimarisi |
| [[vanilla-js-rules]] | Vanilla JS kuralları ve yasaklar |
| [[web-audio]] | Web Audio API kullanımı |
| [[theme-engine]] | Dinamik tema motoru (ADR-044) |
| [[device-css]] | Cihaz bazlı responsive CSS |
| [[components]] | UI component'leri |

---

## 4. İlgili ADR'ler

| ADR | Konu | Durum |
|-----|------|-------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frozen |
| [[ADR-018-footer-player-vaporwave]] | Footer player | Frozen |
| [[ADR-044-dynamic-user-theme-engine]] | Dynamic theme | Active |
| [[ADR-045-multi-domain-view-mode-architecture]] | View modes | Active |
| [[ADR-046-cross-view-state-preservation]] | Cross-view state | Active |
| [[ADR-048-view-transition-api-integration]] | View Transition API | Active |

---

## 5. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | Framework yasak | ADR-001 | Bağımlılık artışı |
| 2 | innerHTML yasak | ADR-001 | XSS açığı |
| 3 | var yasak | ADR-001 | Scope sorunları |
| 4 | eval yasak | ADR-001 | Güvenlik açığı |
| 5 | ITCSS uyumlu olmalı | ADR-001 | CSS kaosu |

---

## 6. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Mimari | [[architecture/l2-routing]] | Routing layer |
| § 2 Mimari | [[architecture/l1-security]] | Security layer |
| § 4 ADR'ler | [[ADR-001-vanilla-js-itcss]] | Vanilla JS |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~120 |
| **Dosya Sayısı** | 6 alt dosya |
| **ADR Uyumlu** | ✅ 001, 018, 044, 045, 046, 048 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
