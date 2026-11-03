---
title: "CoreMusic — .agents Context"
type: docs
category: domain
version: 1.0.0
status: active
authority: "SSOT: .agents/CONTEXT.md · companion: .agents/RAG.md · .agents/durum.md"
updated: 2026-11-01
---

# CoreMusic — .agents Context

**docType:** context · **Klasör:** `.agents/` · **Sorumlu:** Master Orchestrator (MO)

**Zorunlu Bağlantılar:** [[AGENTS.md]] · [[CLAUDE.md]] · [[RAG.md]] · [[durum.md]] · [[../CONTEXT.md]]

---

## 1. Amaç

`.agents/` dizini, CoreMusic otonom mühendislik ortamı için agent profillerini, rol tanımlarını, alt iş akışlarını ve yerel çalışma kurallarını barındırır. Multi-agent swarm (20 persona: 5 Expert, 5 Senior, 10 Junior) hiyerarşisinin çalışma alanıdır.

| Alt Alan | Amaç & İçerik | Kaynak |
|---|---|---|
| `rules/` | Ajan çalışma kuralları ve kısıtları (SSOT: [[../.ai/.rules/RULES.md]]). | `.agents/rules/` · `.ai/.rules/` |
| `workflows/` | Ajan görev akışları ve süreç otomasyonları. | `.agents/workflows/` |
| `durum.md` | Tek sayfalık agent orkestrasyon hafızası ve görev devri. | `.agents/durum.md` |
| `RAG.md` | Ajan profilleri ve kuralları için hızlı RAG erişim yönlendiricisi. | `.agents/RAG.md` |

---

## 2. Kapsam

- **Kapsam:** Agent persona tanımları, yetki matrisi, dispatch kuralları ve iş akışı şablonları.
- **Kapsam Dışı:** Üretim uygulama kaynak kodları (PHP, JS, C++).
- **Hiyerarşi:** Expert (5) > Senior (5) > Junior (10). Kararlar kanıt önceliklidir; oy çokluğu yoktur.

---

*.agents Context v1.0.0 — CoreMusic Master Agent Architecture — 2026-11-01*
