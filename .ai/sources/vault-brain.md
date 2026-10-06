---
title: "Vault Kaynağı: brain.md (Engineering Brain)"
type: kaynak
raw_path: raw/brain.md
created: 2026-10-06
updated: 2026-10-06
sources: [vault-brain]
ingested: 2026-10-06 18:58
tags: [mimari, karar, mühendislik]
---

# Kaynak Özeti — brain.md (v26.1.4)

## Genel Özet

"Mühendislik Hafızası" — mimari kararların, donanım/yazılım kısıtlamalarının ve ses işleme spesifikasyonlarının tutulduğu SSOT. Kategorisi: architecture-decisions.

## Ana Fikirler

1. Offline-first mimari: FLAC/WAV/MP3 mülkiyeti, çevrimiçi akış olmadan çalışabilme.
2. Kapsam: C++20 Neva Engine (ASIO, WASAPI, ALSA, lock-free ring buffer, zero-allocation, 32-bit float PCM), 8.1 Surround (Class AB 8x50W, XMOS XU316, PCM3168A), PHP 8.4+ middleware pipeline (10 adım, Argon2id, AES-256-GCM Credential Vault), 18 BCNF DB (156 tablo), 10 panel/subdomain mimarisi, NovaSearchEngine downloader, 11 ajan, 21 katman (K0-K20).
3. Master Engineering System özeti (2026-10-01) gömülü.

## Önemli Alıntılar/Veriler

- "Ana Mühendislik Hafızasıdır (SSOT)" · "18 BCNF DB (156 tablo), 10 panel / subdomain mimarisi"
- Bağlantılar: [[vault-vision]], [[vault-projects]]

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[vault-brain]] (yeni)
