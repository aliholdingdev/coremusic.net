---
node_id: "K008-recommendation-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/domain-master"
evidence:
  - "domain-master.md §3.1 satır 94 — Öneri | recommendation | RecommendationSet | AIEngine çıktısı (integration)"
  - "domain-master.md §4.3 satır 158–166 — K008 bu kodun sahibi değildir; sınır ADR olarak domain-kararlar.md §5"
  - "shared/src/AI/AIEngine.php satır 28–77 · shared/src/AI/AIWorkflow.php satır 49–90"
  - ".ai/.sql/mysql/coremusic_ai.sql satır 75–77 · 79–98"
validation: "2026-10-15 — kapsam sınırı kod + şema + master kanıtıyla çizildi; sahiplik çelişkisi açık (C2)"
open_questions:
  - "RecommendationSet aggregate → ADR-097 K4'te kilitlenmedi → taslak"
  - "öneri bağlamının sahibi: K008 mi K006/K020 mi → master §6 C2 (satır 188) → ORKESTRATÖR KARARI"
  - "öneri PHP sunucusu/controller → bu oturumda tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# recommendation — Kapsam (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> `recommendation`, **ne önerileceğini üretme** anıdır: işbirlikçi skor + içerik
> skoru → hibrit → çeşitlilik → eşik → sıralama → limit. Master §4.3 (satır
> 158–166) bu davranışın K008'de **yaşadığını ama K008'in kodun sahibi olmadığını**
> söyler — uygulama `shared/src/AI/` katmanındadır.

## §1 ROL (ELI15)

İki skor kaynağı vardır: benzer kullanıcıların tercihleri (işbirlikçi) ve şarkı
özellikleri (içerik tabanlı). `AIEngine` bunları **0.6 / 0.4 ağırlığıyla** birleştirir
(satır 65), **%30 farklı tür garantisi** uygular (satır 67–68), **0.7 altı skorları
eler** (satır 71), sıralar ve **en çok 20** sonuç döner (satır 51, 76). `AIWorkflow`
bunu orkestre eder: oturum tercihlerini okur (satır 55), görevi dağıtır (satır 59),
bilgi tabanında arar (satır 62–63), prompt üretir (satır 66–72), motoru çağırır
(satır 75) ve sonucu 3600 sn cache'ler (satır 78). `recommendation_history` tablosu
geri bildirim döngüsünün kaydıdır (coremusic_ai.sql satır 79–98).

## §2 KAPSAM SINIRI

| # | Bu bağlamın İÇİNDE | Bu bağlamın DIŞINDA | Sınır komşusu |
|---|---|---|---|
| 1 | Öneri skorlama zinciri (`AIEngine.php` satır 49–77) | Arama sorgusu + sonuç metrikleri (`search_logs`) | `discovery` |
| 2 | Öneri tarihi + geri bildirim (`recommendation_history` satır 79–98) | Benzerlik çiftleri şeması (`music_similar`, coremusic_musics.sql 221–234) | `music` |
| 3 | Workflow orkestrasyonu + cache (`AIWorkflow.php` satır 49–90) | Oynatma anının durumu | `playback` |
| 4 | `feedback` enum'unda `saved` sinyali (satır 97) | Koleksiyon CRUD | `library` |
| 5 | — | **Öneri davranışının UYGULAMA kodu sahipliği** | master §4.3 (satır 163–165): K008 kodun sahibi DEĞİL → K016/teknoloji katmanı |
| 6 | — | **Bağlamın sahibi: K008 mi, K006/K020 mi?** (recommendation + discovery) | SINIR AÇIK — master §6 C2 (satır 188) |

## §3 DDD MODEL NOTU (taslak — ADR-097 K4)

| # | Kalem | Ad | Kanıt |
|---|---|---|---|
| 1 | Bounded Context (aday) | Öneri (`recommendation`) | ✅ domain-master §3.1 satır 94 |
| 2 | Aggregate (aday) | RecommendationSet — "AIEngine çıktısı (integration)" | ⚠️ TASARIM İDDİASI — ADR-097 K4 |
| 3 | Entity / kayıt | `recommendation_history` satırı (algoritma · güven · geri bildirim · pozisyon) | ✅ coremusic_ai.sql satır 79–98 |
| 4 | Value Object (aday) | `confidence` decimal(3,2) (satır 85) · `position` tinyint (satır 87) · `feedback` enum (satır 97) | ✅ şema |
| 5 | Ubiquitous language | öneriler = AIEngine çıktısı · geri bildirim = `feedback` (none/liked/disliked/skipped/saved) · eşik = `MIN_SCORE` | ✅ kod + şema |
| 6 | Domain event | — (kanıtlanmadı; workflow sonucu memory cache'e yazar, satır 78) | ⚠️ tarama yok |
| 7 | Invariant | limit ≤ 20 (satır 51) · score ≥ 0.7 (satır 71) | ✅ KOD KANITLI |

## §4 DISK KANITLARI

| Dosya | Satır | Ne |
|---|---|---|
| `shared/src/AI/AIEngine.php` | 28 · 30–32 · 49–77 | sınıf · üç sabit · skorlama zinciri (hibrit 0.6/0.4) |
| `shared/src/AI/AIWorkflow.php` | 49–90 | recommendationWorkflow — memory → orkestratör → bilgi → prompt → engine → cache |
| `.ai/.sql/mysql/coremusic_ai.sql` | 75–77 | `model_versions` — model_type enum'u `recommendation` içerir |
| `.ai/.sql/mysql/coremusic_ai.sql` | 79–98 | `recommendation_history` — güven · geri bildirim · pozisyon · CHECK'ler |
| `.ai/architecture/K008-domain/domain-master.md` | 94 · 158–166 · 232 · 188 | RecommendationSet · §4.3 sahiplik sınırı · §8 bağımlılık · §6 C2 |

**Kardeş dosyalar:** [[recommendation-dogrulama]] · [[recommendation-web-arastirma]] · [[recommendation-ornekler]] · [[recommendation-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
