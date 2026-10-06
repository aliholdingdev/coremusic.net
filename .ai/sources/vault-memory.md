---
title: "Vault Kaynağı: MEMORY.md (Memory System Index)"
type: kaynak
raw_path: raw/MEMORY.md
created: 2026-10-06
updated: 2026-10-06
sources: [vault-memory]
ingested: 2026-10-06 18:58
tags: [hafıza, session-state, persist]
---

# Kaynak Özeti — MEMORY.md

## Genel Özet

Oturumlar arası persistent state yönetimini standartlaştıran, vault ile kod arasındaki tutarlılığı koruyan bellek sistemi indeksi. Tüm ajanların oturum başında okuması zorunlu dosyalardan biri.

## Ana Fikirler

1. Audit trail ile izlenebilirlik garanti edilir.
2. §5 boot 20-adım listesi (bilinen varyant), §6 "5 soru" (CHECKLIST A2 ile eşleşir), §20 session state, §21 Last Updated.
3. Frontend Mimarisi bölümü (L149-232) kod detayı taşır → ayrıştırma adayı (log kaydı 2026-10-06).
4. Terminoloji bölümü vault/kod tutarlılık kurallarını tanımlar.

## Önemli Alıntılar/Veriler

- "tum AI ajanlarinin oturum baslangicinda okumasi gereken zorunlu dosyalardan biridir"

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[vault-memory]] (yeni)
