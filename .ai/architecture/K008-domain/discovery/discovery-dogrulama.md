---
node_id: "K008-discovery-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-discovery-kapsam"
  - "K008-discovery-web-arastirma"
evidence:
  - "satır D-DS1…D-DS13 — 2026-10-15 kaydı"
  - "repo kanıtları: coremusic_logs.sql 398–414/416–431 · coremusic_musics.sql 221–234"
validation: "2026-10-15 — 13 iddia: 8 Dosya/Satır, 3 web pointer (§1), 1 tasarım ⚠️, 1 negatif"
open_questions:
  - "Arama PHP servisi/controller → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED (D-DS13)"
  - "bağlam sahipliği: discovery/recommendation K008 mi K006/K020 mi → master §6 C2 → ORKESTRATÖR KARARI"
group: kanit
---

# discovery — Doğrulama Defteri (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Web kanıtları URL ile değil [[discovery-web-arastirma]] §1 pointer'ı ile verilir (C4).
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN**.

## §1 İDDİA TABLOSU

| # | İddia | Repo kanıtı (Dosya/Satır) | Web kanıtı | Tür | Durum |
|---|-------|---------------------------|-----------|-----|-------|
| D-DS1 | `search_logs` tablosu şemada var | `.ai/.sql/mysql/coremusic_logs.sql` satır 398–414 | — | T4 | ✅ |
| D-DS2 | `search_type` 7 değerli enum (`music,artist,album,playlist,podcast,radio,video`) | aynı dosya satır 402 | — | T4 | ✅ |
| D-DS3 | sonuç sayısı + seçim pozisyonu takibi var | aynı dosya satır 403–405 (`results_count`, `selected_result_id`, `selected_result_position`) | — | T4 | ✅ |
| D-DS4 | arama yanıt süresi metriği var | aynı dosya satır 406 (`response_time_ms`) | — | T4 | ✅ |
| D-DS5 | sorgu üzerinde FULLTEXT indeksi var | aynı dosya satır 413 (`ft_search_logs_query`) | — | T4 | ✅ |
| D-DS6 | `user_activity_logs` var: 14 değerli `activity_type` · json `metadata` · `device_type` | aynı dosya satır 416–431 (enum satır 419, json 422, cihaz 424) | — | T4 | ✅ |
| D-DS7 | `music_similar` var: puan `decimal(3,2)` 0.00–1.00 · algoritma enum'u (`collaborative/content_based/hybrid`, varsayılan `hybrid`) | `.ai/.sql/mysql/coremusic_musics.sql` satır 221–234 (225, 226, unique 229) | — | T4 | ✅ |
| D-DS8 | keşif bağlamının aggregate adayı `DiscoveryQuery (radar/trend)` | domain-master.md §3.1 satır 93 | — | tasarım | ⚠️ ADR-097 K4 |
| D-DS9 | keşif event'i yok; girdi `MusicPlayedEvent`'tir | domain-master.md §3.1 satır 93 + sınıf mevcut ([[playback-dogrulama]] D-PL7) | — | T4 | ✅ |
| D-DS10 | açımlı (faceted) arama modeli — `search_type` karşılığı | — | [[discovery-web-arastirma]] §1 W-3 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-DS11 | keşif daralması (filter bubble) riski — radar/trend uyarısı | — | aynı dosya §1 W-4, W-5 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-DS12 | müzik bilgi erişimi (MIR) — arama/benzerlik bilimsel temeli | — | aynı dosya §1 W-7, W-8, W-9 | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-DS13 | arama/keşif PHP servisi veya JS arama modülü | tarama yapılmadı | — | — | ⚠️ VERIFICATION REQUIRED |

## §2 NOTLAR

- D-DS9 negatiftir: `shared/src/Events/Domain/` envanterinde "keşif" adlı olay yoktur —
  keşif olay yaratmaz, akışı dinler.
- D-DS8 ve §6 C2 birlikte okunmalı: çelişki "discovery/recommendation sahibi
  K008 mi, K006/K020 mi?" sorusudur (v2 spec K008'e verir, eski disk notları
  K006/K020'ye) — kararı **orkestratör** verir; bu dosyanın konusu değildir.

**Kardeş dosyalar:** [[discovery-kapsam]] · [[discovery-web-arastirma]] · [[discovery-ornekler]] · [[discovery-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
