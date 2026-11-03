---
title: "assets.coremusic.net — RAG (Retrieval Router & Bilgi Erişim İndeksi)"
type: domain-rag
category: navigation
version: 1.0.0
status: active
authority: "SSOT: assets.coremusic.net/CONTEXT.md · companion: RAG.md · durum.md"
updated: 2026-11-01
---

# assets.coremusic.net — RAG Retrieval Router (Akıllı Bilgi Erişim İndeksi)

> **Amaç:** `assets.coremusic.net` alanında çalışan AI agent'larının veya geliştiricilerin ITCSS 11 katman mimarisine, CSS tokenlerine, Vanilla JS coreplayer modüllerine ve Playwright E2E testlerine nokta atışı erişmesini sağlamak.

---

## ⚡ Hızlı RAG Rota Tablosu (Soru / Görev → Dosya)

| Görev / Soru Sınıfı | Doğrudan Hedef Dosya | İkincil / Tamamlayıcı |
|---|---|---|
| **Oturum Durumu & Nerede Kaldık?** | [`durum.md`](file:///c:/www/coremusic.net/assets.coremusic.net/durum.md) | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) |
| **Asset Alanı Bağlamı & Envanter** | [`CONTEXT.md`](file:///c:/www/coremusic.net/assets.coremusic.net/CONTEXT.md) | `package.json` |
| **Tasarım Tokenleri (01_Abstracts)** | `Css/01_Abstracts/a-tokens.css` | `Css/01_Abstracts/` |
| **Temel & Reset Stilleri (02_Base)** | `Css/02_Base/` | `Css/03_Layout/` |
| **Düzen & Grid Sistemleri (03_Layout)** | `Css/03_Layout/` | `Css/04_Components/_wide-sidebar.css` |
| **Bileşen Stilleri (04_Components)** | `Css/04_Components/` | `Css/04_Components/c-terminal.css` |
| **Sayfa Stilleri (05_Pages)** | `Css/05_Pages/p-browse.css` · `p-album-detail.css` | `Css/05_Pages/p-kesfet.css` · `p-wizard.css` |
| **JS Coreplayer & Ses Kontrolü** | `js/coreplayer/` | `js/coreplayer/coreplayer.volume.spec.js` |
| **Playwright E2E Test Süiti** | `tests/e2e/pages.spec.ts` | `playwright.config.ts` |

---

## 🎯 Keyword & Arama Etiketleri

`assets`, `css`, `itcss`, `tokens`, `01_abstracts`, `04_components`, `05_pages`, `light-mode`, `zero-raw-hex`, `coreplayer`, `playwright`, `e2e`

---

## ⚠️ Kritik Alan Kısıtları

1. **YALNIZCA LIGHT MODE (`HL-01`):** Tüm CSS stilleri kesinlikle Light Mode için tasarlanır; `@media (prefers-color-scheme: dark)` veya dark mod class'ları yazmak KESİNLİKLE YASAKTIR.
2. **0 RAW HEX / %100 TOKEN PURITY (`HL-12`):** Bileşen ve sayfa CSS'lerine sabit hex yazılamaz; renkler yalnızca `01_Abstracts/` içindeki `--cm-*` tokenlerinden okunur.
3. **CONTAINER QUERY KURALI (`HL-04`):** Flex sütunlar `minmax(0, ...)` ile tanımlanır, kapsayıcıya `container-type: inline-size` verilir.
4. **SUBAGENT COMMIT YASAĞI (`HL-03`):** Subagent `git commit` atamaz; commit yetkisi kullanıcıya / orkestratöre aittir.

---

*assets.coremusic.net RAG Router v1.0.0 — SSOT: [`assets.coremusic.net/CONTEXT.md`](file:///c:/www/coremusic.net/assets.coremusic.net/CONTEXT.md)*
