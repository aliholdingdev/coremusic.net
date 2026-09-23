---
title: "CoreMusic — shared/src/AI Bağlam"
type: context
folder: "shared/src/AI"
category: layer4-ai
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# AI — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k4-yapay-zeka]]

---

## 1. Bağlam

Yapay zeka altyapısı (ADR-030). AIEngine, Orchestrator, KnowledgeBase, MemorySystem, PromptEngine ve ToolCalling bileşenleri.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Alt klasör | 2 (AI/, AI/Contracts/) |
| ADR | ADR-030 (AI Strategy Core) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `AIEngine.php` | Ana AI motoru |
| `Contracts/*.php` | AI sözleşme arayüzleri |

---

## 3. AI Bileşenleri

| Bileşen | Görev |
|---------|-------|
| AIEngine | Müzik önerileri, ses analizi, otomatik EQ |
| Orchestrator | Görev dağıtımı, context yönetimi |
| KnowledgeBase | Bilgi bankası, semantic search |
| MemorySystem | Session hafızası, persistence |
| PromptEngine | Prompt üretimi, token management |
| ToolCalling | Dış servis çağrısı |

---

## 4. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | AI'dan direkt DB erişimi | K5 harici veriye erişemez |
| 2 | AI'dan frontend erişimi | Katman ihlali |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
