---
title: ".agents — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: .agents/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# .agents — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `.agents` çalışma alanında çalışan AI orkestratörünün veya geliştiricilerin agent profillerine, swarm rollerine ve iş akışlarına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/.agents/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Agent Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/.agents/CONTEXT.md) | `../AGENTS.md` |
| **Master Agent Kuralları & Registry** | `AGENTS.md` (kök) | `.ai/AGENTS.md` (tam SSOT) |
| **Ajan İş Akışları (Workflows)** | `workflows/` | `.workflows/` |
| **Ajan Kuralları (Rules)** | `rules/` | `.ai/.rules/` |
| **Persona Hiyerarşisi (20 Persona)** | `../AGENTS.md` (§6.1) | `.ai/.agents/` |

---

## 🎯 Keyword & Arama Etiketleri

`agents`, `multi-agent-swarm`, `orchestrator`, `architect`, `security-engineer`, `persona-hierarchy`, `dispatch`, `workflows`

---

## ⚠️ Kritik Alan Kısıtları

1. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` ATAMAZ; commit yetkisi yalnızca kullanıcıya / orkestratöre aittir.
2. **OY ÇOKLUĞU YOKTUR:** Kararlar seviyeye ve kanıt gücüne göredir (Expert > Senior > Junior); eşit kanıtta üst seviye kazanır.

---

*.agents RAG Router v1.0.0 — SSOT: [`.agents/CONTEXT.md`](file:///c:/www/coremusic.net/.agents/CONTEXT.md)*
