---
title: ".workflows — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: .workflows/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# .workflows — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `.workflows` dizininde çalışan AI agent'larının veya geliştiricilerin süreç kılavuzlarına, denetim iş akışlarına ve otomasyon adımlarına nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/.workflows/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Süreç Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/.workflows/CONTEXT.md) | `../WORKFLOW.md` |
| **Orkestratör İş Akışı (Orchestrator Flow)** | `orchestrator-flow.md` | `AGENTS.md` |
| **Oturum Başlatma Süreci (Session Init)** | `session-init.md` | `../RAG-KILAVUZU.md` |
| **ADR Oluşturma & Karar Süreci** | `adr-creation.md` | `.ai/.rules/karar-kurallari.md` |
| **Güvenlik Denetim Akışı (Security Audit)** | `security-audit.md` | `.ai/.rules/hata-listesi.md` |
| **Halüsinasyon Önleme & Kanıt Süreci** | `hallucination-control.md` | `.ai/.rules/kurallar.md` (`KURAL-06`) |
| **Vault Senkronizasyon Süreci (Vault Sync)** | `vault-sync.md` | `CLAUDE.md` |
| **Dağıtım ve Sürüm Süreci (Deployment)** | `deployment.md` | `PRD.md` |
| **Kiro Teknik Tasarım Şartnamesi** | design.md | ../PRD.md |
| **Kiro Aşamalı Görev Şartnamesi** | 	asks.md | ../TODO.md |
| **Kiro 6 Adımlı Yürütme İş Akışı** | workflow.md | WORKFLOW.md |

---

## 🎯 Keyword & Arama Etiketleri

`workflows`, `orchestrator-flow`, `session-init`, `adr-creation`, `security-audit`, `hallucination-control`, `vault-sync`, `deployment`

---

## ⚠️ Kritik Alan Kısıtları

1. **HARD GATE GEÇİŞLERİ:** Onay kapısı (gate) geçilmeden bir sonraki faza atlanamaz.
2. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*.workflows RAG Router v1.0.0 — SSOT: [`.workflows/CONTEXT.md`](file:///c:/www/coremusic.net/.workflows/CONTEXT.md)*
