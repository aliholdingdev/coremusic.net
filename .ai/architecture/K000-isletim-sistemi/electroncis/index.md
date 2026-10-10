---
type: architecture
category: layer-subindex
title: "K000 · electroncis — Yerleşim Notu (Sınır Referansı)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# electroncis — Yerleşim Notu (K000 altında, içerik K001'e ait)

> Üst indeks: `[[../index]]` · Asıl sahip: `[[../../K001-donanim/index]]` (L6 electronics, ADR-061)

## §1 Uyarı
Bu alt klasör **K000 (İşletim Sistemi)** altındadır, fakat elektronik içeriği
**K001 (Donanım)** katmanının sorumluluğundadır (ADR-061: L6 = hardware, firmware, driver, DSP, audio engine).
Dizin adı mevcut yapıdadır — **ad değiştirilmedi, taşınmadı** (onaysız taşıma/rename yasak; AGENTS §13).
İçerik bu dosyada **yazılmaz**; tek yetkili referans K001'dir (ikinci yazım → çelişki riski).

## §2 Sınır Referansları
| Konu | Yer |
|---|---|
| Elektronik mimarisi (L6, kart/modül hiyerarşisi) | `[[../../K001-donanim/index]]` · `.ai/.decisions/accepted/ADR-061-electronics-architecture.md` |
| Donanım tipi kırılımı (circuits/hdd/ssd) | `[[../../K001-donanim/donanaım-type/index]]` |
| OS ↔ donanım sınırı (K000'in tek elektronik teması) | K000 yalnız OS zeminini verir; sürücü/donanım → K001–K003 |

## §3 Rapor (aksiyon bekliyor)
- **Beklenen:** `electroncis/` muhtemelen K001 altında olmalıydı (veya hiç olmamalıydı).
- **Yapılan:** yalnız rapor; taşıma ADR/onay gerektirir → `⚠️ VERIFICATION REQUIRED` (kasıt UNKNOWN).

## §4 Durum
**NOT — içerik YOK (kasıtlı)**. Bu dosya bilgi değil, yerleşim kararı kaydıdır.
