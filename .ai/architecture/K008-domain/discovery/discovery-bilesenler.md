---
node_id: "K008-discovery-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-discovery-kapsam"
  - "K008-discovery-ornekler"
evidence:
  - ".ai/.sql/mysql/coremusic_logs.sql satır 398–414 · 416–431"
  - ".ai/.sql/mysql/coremusic_musics.sql satır 221–234"
  - "shared/src/Events/Domain/ — 9 event; keşif event'i YOK (girdi MusicPlayedEvent)"
validation: "2026-10-15 — §1 mevcut kademe (3 tablo + 1 girdi event) + §2 altı PLANNED satır"
open_questions:
  - "DiscoveryQuery aggregate + invariant → ADR-097 K4"
  - "bağlam sahipliği: discovery/recommendation K008 mi K006/K020 mi → master §6 C2 → ORKESTRATÖR KARARI"
group: mimari
---

# discovery — Bileşenler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> §1 mevcut olanı, §2 ise **PLANNED** kademe-3 planını taşır. Disk'te karşılığı
> olmayan sınıf/dosya adı "var" gibi sunulmaz.

## §1 MEVCUT KADEME

| # | Bileşen | Kanıt | Durum |
|---|---|---|---|
| 1 | `search_logs` (arama ölçümü) | `.ai/.sql/mysql/coremusic_logs.sql` satır 398–414 | ✅ DISKTE |
| 2 | `user_activity_logs` (aktivite akışı) | aynı dosya satır 416–431 | ✅ DISKTE |
| 3 | `music_similar` (benzerlik çiftleri) | `.ai/.sql/mysql/coremusic_musics.sql` satır 221–234 | ✅ DISKTE |
| 4 | `MusicPlayedEvent` (girdi event'i) | `shared/src/Events/Domain/MusicPlayedEvent.php` satır 20 | ✅ DISKTE (bkz [[playback-dogrulama]] D-PL7) |
| 5 | Keşif event'i (outbound) | `shared/src/Events/Domain/` envanteri | ❌ YOK — keşif olay yaymaz |
| 6 | Arama PHP/JS modülü | — | ⚠️ VERIFICATION REQUIRED (tarama yok — D-DS13) |

## §2 KADEME-3 PLANI (PLANNED — ADR-097 K4 bekliyor)

| # | Bileşen (aday) | Sorumluluk | Dayanak | Durum |
|---|---|---|---|---|
| 1 | DiscoveryQuery (aggregate — ad master satır 93) | sorgu + sonuç metriği yaşam döngüsü | [[discovery-kapsam]] §3 satır 2 | ⚠️ PLANNED |
| 2 | Arama servisi (ad ADR-097 K4) | sorgu çalıştırma · tip filtresi · sonuç sayma | `search_logs` satır 401–406 | ⚠️ PLANNED — kod kanıtı yok (D-DS13) |
| 3 | Radar/trend üretici | aktivite akışından yükselen içerik | `user_activity_logs` satır 419 (activity_type) + master satır 93 | ⚠️ PLANNED |
| 4 | Benzerlik sorgu katmanı | `music_similar` üzerinden eş sorgusu | satır 229 (unique) · 231 (score sıralama) | ⚠️ PLANNED |
| 5 | Ölçüm yazıcı (search/selection) | sorgu + seçim pozisyonu + yanıt süresi kaydı | `search_logs` satır 403–406 | ⚠️ PLANNED |
| 6 | Bağlam sahiplik sınırı | discovery/recommendation K008'de mi, K006/K020'de mi | master §6 C2 (SINIR AÇIK) | ⏸ BEKLEMEDE — orkestratör kararı |

**Yasak:** §2 adları **tasarım adayıdır**; disk'te karşılığı doğrulanmadan "mevcut"
gibi raporlanmaz. Geçiş: kod diske yazıldı + ADR-097 K4 kilitlendi → `active`.

**Kardeş dosyalar:** [[discovery-kapsam]] · [[discovery-dogrulama]] · [[discovery-web-arastirma]] · [[discovery-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
