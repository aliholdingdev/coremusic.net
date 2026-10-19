---
node_id: "K008-recommendation-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-recommendation-kapsam"
  - "K008-recommendation-ornekler"
evidence:
  - "shared/src/AI/AIEngine.php satır 28–77 · shared/src/AI/AIWorkflow.php satır 49–90"
  - ".ai/.sql/mysql/coremusic_ai.sql satır 75–77 · 79–98"
  - "domain-master.md §8 satır 232 — Discovery/recommendation → AIEngine (Kanıtlı, C2 bağlantılı)"
validation: "2026-10-15 — §1 mevcut kademe (kod + 1 tablo) + §2 altı PLANNED satır"
open_questions:
  - "RecommendationSet aggregate + invariant → ADR-097 K4"
  - "bağlam sahipliği kararı → master §6 C2 (satır 188) → orkestratör"
group: mimari
---

# recommendation — Bileşenler (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> §1 mevcut olanı, §2 ise **PLANNED** kademe-3 planını taşır. Disk'te karşılığı
> olmayan sınıf/dosya adı "var" gibi sunulmaz.

## §1 MEVCUT KADEME

| # | Bileşen | Kanıt | Durum |
|---|---|---|---|
| 1 | `AIEngine` — skorlama zinciri (hibrit · çeşitlilik · eşik · limit) | `shared/src/AI/AIEngine.php` satır 28–77 | ✅ DISKTE (sahibi K008 değil — §4.3) |
| 2 | `AIWorkflow::recommendationWorkflow` — orkestrasyon | `shared/src/AI/AIWorkflow.php` satır 49–90 | ✅ DISKTE |
| 3 | `recommendation_history` — iz + geri bildirim tablosu | `.ai/.sql/mysql/coremusic_ai.sql` satır 79–98 | ✅ DISKTE (eski nesil `int unsigned`) |
| 4 | `model_versions` — `recommendation` model tipi | aynı dosya satır 75 | ✅ DISKTE |
| 5 | Öneri domain event'i (outbound) | workflow satır 77–78: memory cache'e yazar, event YOK | ❌ YOK — kod kanıtsız |
| 6 | Öneri PHP sunucusu/controller | — | ⚠️ VERIFICATION REQUIRED (tarama yok) |

## §2 KADEME-3 PLANI (PLANNED — ADR-097 K4 + C2 bekliyor)

| # | Bileşen (aday) | Sorumluluk | Dayanak | Durum |
|---|---|---|---|---|
| 1 | RecommendationSet (aggregate — ad master satır 94) | öneri listesi + meta (task_id, tokenCount) yaşam döngüsü | [[recommendation-kapsam]] §3 satır 2 | ⚠️ PLANNED |
| 2 | Feedback yazıcı | kullanıcı geri bildirimini `recommendation_history`'e yazma | satır 97 (feedback enum'u 5 değer) | ⚠️ PLANNED |
| 3 | Cold-start stratejisi | yeni kullanıcı/şarkı için içerik tabanlı ağırlığının artırılması | W-3 (soğuk başlangıç) + satır 65 (ağırlık sabit) | ⚠️ PLANNED |
| 4 | Öneri event'i (opsiyonel) | `recommendation.generated` — playback/audit akışı için | §1 satır 5: bugün YOK | ⚠️ PLANNED — needs ADR |
| 5 | Şema göçü (aday) | `recommendation_history` → `binary(16)` UUID v7 uyumu | D-RC9: eski nesil `int unsigned` | ⚠️ PLANNED — data-engine kararı |
| 6 | Bağlam sahiplik sınırı | recommendation/discovery K008'de mi, K006/K020'de mi | master §6 C2 (satır 188) | ⏸ BEKLEMEDE — orkestratör kararı |

**Yasak:** §2 adları **tasarım adayıdır**; disk'te karşılığı doğrulanmadan "mevcut"
gibi raporlanmaz. Geçiş: kod diske yazıldı + ADR-097 K4 kilitlendi → `active`.
`AIEngine`/`AIWorkflow` üzerinde K008 kaynaklı değişiklik yetkisi yoktur (§4.3).

**Kardeş dosyalar:** [[recommendation-kapsam]] · [[recommendation-dogrulama]] · [[recommendation-web-arastirma]] · [[recommendation-ornekler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
