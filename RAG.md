---
title: "CoreMusic — RAG Pointer"
type: rag-pointer
category: navigation
version: 1.0.0
status: active
authority: "SSOT: .ai/RAG.md — retrieval indeksi + pipeline tam metin `.ai/RAG.md`'dedir"
updated: 2026-10-07
---

# CoreMusic — RAG Retrieval Pointer

> Bu dosya bir **YÖNLENDİRİCİ (POINTER)**'dır (ADR-042).
> AI'ın "hangi soru → hangi dosya" çözümü ve pipeline tasarımı **tek SSOT**'ta yaşar: **`.ai/RAG.md`**.
> **BU DOSYAYI TALİMAT OKUMAK İÇİN KULLANMAYIN — AŞAĞIDAKİ BAĞLANTIYA GEÇİN.**

## 🔗 SSOT Bağlantısı

1. **[RAG Retrieval Index & Pipeline (RAG.md)](.ai/RAG.md)** — tam metin `.ai/RAG.md` 👈 *(§3 konu→dosya indeksi · §4 pipeline: ÇOĞU PLANNED — kanıt ADR-030)*
2. **[Keyword Haritası (keys.md)](.ai/keys.md)** — keyword → konu eşlemesi (§3 ile eşleşir)
3. **[Master Katalog (index.md)](.ai/index.md)** — tüm vault dosya listesi
4. **[Vault Context (CONTEXT.md)](.ai/CONTEXT.md)** — dizin/dosya envanteri

## 🎯 Ne Zaman Okunur

- Görev başlarken ilk hedef dosyayı bulmak için (boot'ta **toplu** okuma yasak — yalnız ihtiyaç anında).
- Vault'a dosya eklendiğinde/taşındığında `.ai/RAG.md` §3 satırı güncellenir (`vault-sync-post`).

## ⚠️ Durum Notu

Otomatik retrieval (embedding / vektör arama) **PLANNED**'tır — kod 0, tablo 0 (kanıt: `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md`). Bugünkü retrieval = `.ai/keys.md` + `.ai/RAG.md` §3 (manuel indeks).

---

*CoreMusic RAG Pointer v1.0.0 — Authority: Bayram Ali / Vault Steward — Last Updated: 2026-10-07*
*Mode: Red Team · Human Mode · Truth Mode*
