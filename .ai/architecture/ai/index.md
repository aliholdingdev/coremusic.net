---
title: "CoreMusic — AI Architecture"
type: architecture
category: ai
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_ai_files: 12
---

# CoreMusic — AI Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]] · [[index.md]]

---

## 1. Amaç

CoreMusic AI altyapısının tam mimarisini tanımlar. Öneri motoru, ses işleme AI'ı, otomatik EQ, prompt sistemi, agent koordinasyonu, knowledge base, tool calling ve MCP entegrasyonu dahil.

---

## 2. AI Sistem Bileşenleri (12 Dosya)

| # | Bileşen | Dosya | Amaç |
|---|---------|-------|------|
| 1 | [[ai-engine]] | AI Engine | Ana AI işleme motoru — müzik önerileri, ses analizi, otomatik EQ |
| 2 | [[ai-orchestrator]] | AI Orchestrator | Görev dağıtımı, context yönetimi, workflow engine |
| 3 | [[agent-system]] | Agent System | 11 ajanlı agent sistemi — domain boundary, handover |
| 4 | [[knowledge-base]] | Knowledge Base | Bilgi bankası — semantic search, RAG, knowledge lifecycle |
| 5 | [[memory-system]] | Memory System | Session hafızası — persistent state, cache, session lifecycle |
| 6 | [[prompt-engine]] | Prompt Engine | Prompt üretim motoru — token management, template |
| 7 | [[tool-calling]] | Tool Calling | Dış servis çağrısı — API integration, fallback |
| 8 | [[mcp-integration]] | MCP Integration | Model Context Protocol — tool exposure, resource sharing |
| 9 | [[ai-workflow]] | AI Workflow | AI iş akışları — recommendation, analysis, optimization |
| 10 | [[ai-electronics-engine]] | AI Electronics Engine | Elektronik AI — donanım analizi, PCB optimizasyonu |
| 11 | [[ai-workflow-electronics]] | AI Workflow Electronics | Elektronik AI workflow — test, validasyon, iterasyon |

---

## 3. AI Katman Mimarisi

```
┌─────────────────────────────────────────────────────┐
│  L3 — AI Presentation (Chat, Recommendations)       │
├─────────────────────────────────────────────────────┤
│  L2 — AI Orchestration (Task Dispatch, Routing)      │
├─────────────────────────────────────────────────────┤
│  L1 — AI Security (Prompt Injection Guard, RBAC)     │
├─────────────────────────────────────────────────────┤
│  L0 — AI Infrastructure (LLM, Vector DB, Cache)      │
└─────────────────────────────────────────────────────┘
```

---

## 4. AI Servis Haritası

| Servis | Port | Protokol | Sorumluluk |
|--------|------|----------|------------|
| AI Service | — | Internal | Öneri motoru, auto-download, analiz |
| Prompt Service | — | Internal | Prompt üretimi, context yönetimi |
| Memory Service | — | Internal | Session persistence, cache |
| Knowledge Service | — | Internal | Semantic search, RAG, indexing |
| Tool Service | — | Internal | Dış servis çağrısı, fallback |

---

## 5. AI Entegrasyon Noktaları

| Entegrasyon | Kaynak | Hedef | Protokol |
|-------------|--------|-------|----------|
| Audio Analysis | [[ai-engine]] | Audio Service (9741) | REST |
| DSP Optimization | [[ai-engine]] | DSP Firmware | IPC |
| Hardware Analysis | [[ai-electronics-engine]] | Device Service | BLE/WiFi |
| Knowledge Query | [[knowledge-base]] | Vault (.ai/) | File System |
| Agent Dispatch | [[ai-orchestrator]] | 11 Agent | Internal |
| Prompt Generation | [[prompt-engine]] | LLM API | HTTP |
| Tool Execution | [[tool-calling]] | External APIs | REST/WS |
| MCP Resources | [[mcp-integration]] | MCP Clients | MCP Protocol |

---

## 6. İlgili ADR'ler

| ADR | Konu | Kategori |
|-----|------|----------|
| [[ADR-030-ai-strategy-core]] | AI stratejisi — temel AI kararları | AI |
| [[ADR-035-system-prompt-engineering]] | Prompt engineering standartları | AI |
| [[ADR-036-multi-project-prompt-maker]] | Multi-proje prompt üretimi | AI |
| [[ADR-049-startup-prompt-loader]] | Startup prompt loader — otomatik yükleme | AI |

---

## 7. AI Pipeline Genel Bakış

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Data Source │───▶│  AI Engine   │───▶│  AI Output   │
│  (Audio/DB)  │    │  (Pipeline)  │    │  (Rec/EQ)    │
└──────────────┘    └──────────────┘    └──────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Knowledge   │◀──│  Orchestrator│───▶│  Tool Calling│
│  Base        │    │  (Dispatch)  │    │  (External)  │
└──────────────┘    └──────────────┘    └──────────────┘
```

---

## 8. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| AI motoru | [[ai-engine]] |
| Agent sistemi | [[agent-system]] |
| Prompt üretimi | [[prompt-engine]] |
| AI workflow | [[ai-workflow]] |
| Bilgi bankası | [[knowledge-base]] |
| Orchestrasyon | [[ai-orchestrator]] |
| Tool calling | [[tool-calling]] |
| MCP entegrasyonu | [[mcp-integration]] |
| Elektronik AI | [[ai-electronics-engine]] |

---

## 9. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Bileşenler | [[AGENTS.md]] §4 | Agent tanımları |
| § 3 Katmanlar | [[brain.md]] §5 | L0-L3 mimarisi |
| § 6 ADR'ler | [[decisions/accepted/ADR-030-ai-strategy-core]] | AI stratejisi |
| § 7 Pipeline | [[tool-calling]] | Dış servisler |
| § 7 Pipeline | [[knowledge-base]] | Bilgi bankası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
