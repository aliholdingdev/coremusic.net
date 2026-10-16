---
node_id: "K008-discovery-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 93 — Keşif | discovery | DiscoveryQuery (radar/trend) | MusicPlayedEvent (akış girdisi)"
  - ".ai/.sql/mysql/coremusic_logs.sql satır 398–414 (search_logs) · 416–431 (user_activity_logs)"
  - ".ai/.sql/mysql/coremusic_musics.sql satır 221–234 (music_similar)"
validation: "2026-10-15 — kapsam sınırı 3 SQL tablo + master satır kanıtıyla çizildi"
open_questions:
  - "DiscoveryQuery aggregate → ADR-097 K4'te kilitlenmedi → taslak"
  - "bağlam sahipliği: discovery/recommendation K008 mi K006/K020 mi → master §6 C2 → ORKESTRATÖR KARARI"
  - "Arama PHP servisi/controller → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# discovery — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `discovery`, **kullanıcının arayıp bulma** anıdır: sorgu, sonuç, seçim, tarama
> (radar/trend). "Ne önerilir" sorusu `recommendation`'ın, "ne var" sorusu `music`'in.

## §1 ROL (ELI15)

Kullanıcı arama kutusuna yazar → sistem sorguyu, sonuç sayısını, seçtiği sonucun
pozisyonunu ve yanıt süresini kaydeder (`search_logs`, satır 398–414). Ne tıklandığı
daha geniş aktivite akışına düşer (`user_activity_logs`, satır 416–431) — hem
analiz hem kişiselleştirme besidir. Benzer şarkı eşlemeleri `music_similar`'dadır
(satır 221–234). Master §3.1 satır 93 bu bağlamı `DiscoveryQuery (radar/trend)`
olarak adlandırır ve girdi event'ine `MusicPlayedEvent` der (akış girdisi).

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Arama sorgusu + sonuç metrikleri (`search_logs` satır 401–406) | Öneri skorlaması / AIEngine çıktısı | `recommendation` |
| 2 | Benzerlik çiftleri + benzerlik puanı (`music_similar` satır 225–226) | Öneri listesi üretimi (RecommendationSet) | `recommendation` |
| 3 | Aktivite akışı: arama/çalma/beğenme olayları (activity_type, satır 419) | Oynatma anının durumu | `playback` |
| 4 | `search_type` enum'unda `playlist`/`album`/`artist` sonuç tipleri (satır 402) | Çalma listesi CRUD | `playlist` |
| 5 | Kategori/öne çıkan içerik arayüzü (referans: [[discovery-web-arastirma]] §1 W-1, W-2) | Katalog tanımı (eser/şarkı) | `music` |
| 6 | — | **Bağlamın sahibi: discovery/recommendation K008 mi, K006/K020 mi?** | SINIR AÇIK — master §6 C2 |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Keşif (`discovery`) | ✅ domain-master §3.1 satır 93 |
| 2 | Aggregate (aday) | DiscoveryQuery (radar/trend) | ⚠️ TASARIM İDDİASI — ADR-097 K4 |
| 3 | Entity / kayıt | `search_logs` satırı (sorgu + tip + sonuç metrikleri) | ✅ coremusic_logs.sql satır 398–414 |
| 4 | Value Object (aday) | `search_type` enum: music · artist · album · playlist · podcast · radio · video (7) | ✅ satır 402 |
| 5 | Ubiquitous language | arama = `search_logs` · benzerlik = `music_similar` · aktivite = `user_activity_logs` | ✅ şema adları |
| 6 | Domain event | keşif event'i YOK; girdi = `MusicPlayedEvent` (master satır 93) | ✅ girdi event'i var (bkz [[playback-dogrulama]] D-PL7) |
| 7 | Invariant | tanımsız | ⚠️ ADR-097 K4 |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `.ai/.sql/mysql/coremusic_logs.sql` | 398–414 | `search_logs` — sorgu · tip · sonuç · yanıt süresi · FULLTEXT |
| `.ai/.sql/mysql/coremusic_logs.sql` | 416–431 | `user_activity_logs` — 14 değerli aktivite · json metadata · cihaz |
| `.ai/.sql/mysql/coremusic_musics.sql` | 221–234 | `music_similar` — benzerlik puanı · algoritma enum'u |
| `.ai/architecture/K008-domain/domain-master.md` | 93 | DiscoveryQuery + MusicPlayedEvent (akış girdisi) eşlemesi |
| `shared/src/Events/Domain/MusicPlayedEvent.php` | 20 | girdi event'i sınıfı (bağlam girdisi — §2 satır 6'ya bkz) |

**Kardeş dosyalar:** [[discovery-dogrulama]] · [[discovery-web-arastirma]] · [[discovery-ornekler]] · [[discovery-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
