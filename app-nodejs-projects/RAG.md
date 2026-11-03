---
title: "app-nodejs-projects — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: app-nodejs-projects/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# app-nodejs-projects — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `app-nodejs-projects` alanında çalışan AI agent'larının veya geliştiricilerin Node.js araçlarına, otomasyon servislerine ve derleme scriptlerine nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/app-nodejs-projects/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Node.js Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/app-nodejs-projects/CONTEXT.md) | `package.json` |
| **Paket Tanımları & Bağımlılıklar** | `package.json` | `package-lock.json` |
| **Yardımcı Araç & Otomasyon Scriptleri** | `scripts/` | `src/` |
| **Servis Yapılandırması & Portlar** | `config/` | `.env.example` |

---

## 🎯 Keyword & Arama Etiketleri

`nodejs`, `automation`, `tools`, `build-scripts`, `package.json`, `npm`, `pnpm`

---

## ⚠️ Kritik Alan Kısıtları

1. **BAĞIMLILIK KORUMASI (`KURAL-07`):** Kullanıcı onayı olmadan yeni harici npm paketi eklenemez.
2. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*app-nodejs-projects RAG Router v1.0.0 — SSOT: [`app-nodejs-projects/CONTEXT.md`](file:///c:/www/coremusic.net/app-nodejs-projects/CONTEXT.md)*
