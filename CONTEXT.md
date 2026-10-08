---
title: "CoreMusic — Vault Context Pointer"
type: context-pointer
category: navigation
version: 1.0.0
status: active
authority: "SSOT: .ai/CONTEXT.md — vault envanteri ve boot ilişkisi tam metin `.ai/CONTEXT.md`'dedir"
updated: 2026-10-07
---

# CoreMusic — Vault Context Pointer

> Bu dosya bir **YÖNLENDİRİCİ (POINTER)**'dır (ADR-042).
> Vault envanteri (hangi klasörde ne var, boot okuma sırası, disk çelişkileri) **tek SSOT**'ta yaşar: **`.ai/CONTEXT.md`**.
> **BU DOSYAYI TALİMAT OKUMAK İÇİN KULLANMAYIN — AŞAĞIDAKİ BAĞLANTIYA GEÇİN.**

## 🔗 SSOT Bağlantıları

1. **[Vault Klasör Context (CONTEXT.md)](.ai/CONTEXT.md)** — tam metin `.ai/CONTEXT.md` 👈 *(§3.1 kök dosya envanteri · §3.2 dizin envanteri · §3.3 boot sırası · §3.5 disk çelişkileri)*
2. **[AI Anayasası (CLAUDE.md)](.ai/CLAUDE.md)** — 16 Hard Guardrail, boot §16
3. **[Agent Registry (AGENTS.md)](.ai/AGENTS.md)** — routing, handover, escalation
4. **[Workflow (WORKFLOW.md)](.ai/WORKFLOW.md)** — fazlar, kapılar
5. **[RAG Retrieval (RAG.md)](.ai/RAG.md)** — konu→dosya indeksi + pipeline
6. **[Master Katalog (index.md)](.ai/index.md)** · **[Keyword Haritası (keys.md)](.ai/keys.md)**

## Boot Okuma Sırası (özet)

```text
1) kök CLAUDE.md + AGENTS.md + README.md + WORKFLOW.md OKU
2) ilgili .ai/ dosyası — yalnız ihtiyaç anında @ ile (toplu okuma YASAK)
3) hedef dosya → kod/doküman → doğrulama → log.md (append)
```

---

*CoreMusic Context Pointer v1.0.0 — Authority: Bayram Ali / Vault Steward — Last Updated: 2026-10-07*
*Mode: Red Team · Human Mode · Truth Mode*
