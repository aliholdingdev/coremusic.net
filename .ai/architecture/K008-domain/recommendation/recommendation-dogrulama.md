---
node_id: "K008-recommendation-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-recommendation-kapsam"
  - "K008-recommendation-web-arastirma"
evidence:
  - "satır D-RC1…D-RC13 — 2026-10-15 kaydı"
  - "repo kanıtları: AIEngine.php 28–77 · AIWorkflow.php 49–90 · coremusic_ai.sql 75–77/79–98"
validation: "2026-10-15 — 13 iddia: 10 Dosya/Satır, 1 web pointer grubu, 2 tasarım/açık ⚠️"
open_questions:
  - "öneri PHP sunucusu/controller → tarama yapılmadı → ⚠️ VERIFICATION REQUIRED"
  - "bağlam sahipliği K008 mi K006/K020 mi → master §6 C2 → ORKESTRATÖR KARARI"
group: kanit
---

# recommendation — Doğrulama Defteri (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> Web kanıtları URL ile değil [[recommendation-web-arastirma]] §1 pointer'ı ile verilir (C4).
> Durumlar: ✅ doğrulandı · ⚠️ VERIFICATION REQUIRED · **UNKNOWN**.

## §1 İDDİA TABLOSU

| # | İddia | Repo kanıtı (Dosya/Satır) | Web kanıtı | Tür | Durum |
|---|-------|---------------------------|-----------|-----|-------|
| D-RC1 | `AIEngine` sınıfı `AIEngineInterface`'i uygular | `shared/src/AI/AIEngine.php` satır 28 | — | T4 | ✅ |
| D-RC2 | üç sabit: `MAX_RECOMMENDATIONS = 20` · `MIN_SCORE = 0.7` · `DIVERSITY_TARGET = 0.30` | aynı dosya satır 30–32 | — | T4 | ✅ |
| D-RC3 | `getRecommendations(array $context, int $limit = 20)` + limit `MAX_RECOMMENDATIONS`'e kırpılır | aynı dosya satır 49, 51 | — | T4 | ✅ |
| D-RC4 | context alanları: user_id · session_id · mood · genre · history | aynı dosya satır 52–56 | — | T4 | ✅ |
| D-RC5 | hibrit skorlama ağırlıklandırması **0.6 işbirlikçi / 0.4 içerik tabanlı** | aynı dosya satır 65 (`combineScores(..., 0.6, 0.4)`) | — | T4 | ✅ |
| D-RC6 | çeşitlilik filtresi "%30 farklı tür garantisi" | aynı dosya satır 67–68 (`applyDiversityFilter`) | — | T4 | ✅ |
| D-RC7 | min-skor filtresi (`>= MIN_SCORE`) + azalan sıralama + `array_slice(..., $limit)` | aynı dosya satır 71, 74, 76 | — | T4 | ✅ |
| D-RC8 | `AIWorkflow::recommendationWorkflow`: memory tercihleri (55) → `dispatchTask('recommendation', …, 'medium')` (59) → bilgi araması 5 sonuç (62–63) → prompt (66–72) → engine varsayılan limit 10 (75) → `last_recommendations` cache 3600 sn (78) → return (82–90) | `shared/src/AI/AIWorkflow.php` satır 49–90 | — | T4 | ✅ |
| D-RC9 | `recommendation_history` var: `int unsigned` ID'ler (**UUID binary(16) DEĞİL** — şema nesli farkı), `confidence` decimal(3,2), `position`, algorithm CHECK 4 değer (satır 96), feedback CHECK 5 değer (satır 97) | `.ai/.sql/mysql/coremusic_ai.sql` satır 79–98 | — | T4 | ✅ |
| D-RC10 | `model_versions` model_type enum'u `recommendation` içerir | aynı dosya satır 75 | — | T4 | ✅ |
| D-RC11 | aggregate adayı `RecommendationSet` = "AIEngine çıktısı (integration)" | domain-master.md §3.1 satır 94 | — | tasarım | ⚠️ ADR-097 K4 |
| D-RC12 | öneri bilimi temelleri: işbirlikçi filtreleme · soğuk başlangıç · matris faktörizasyonu · genel çerçeve · müzik incelemeleri | — | [[recommendation-web-arastirma]] §1 W-3…W-9 (+ W-1, W-2) | T3 | ✅ HTTP 200 · 2026-10-15 |
| D-RC13 | sahiplik: davranış `shared/src/AI/` içinde yaşar ama **K008 bu kodun sahibi değildir** (uygulama K016/teknoloji katmanı); bağlam sahibi K008 mi K006/K020 mi belirsiz | domain-master.md §4.3 satır 158–166 + §8 satır 232 + §6 C2 satır 188 | — | sınır | ⏸ AÇIK — orkestratör + ADR domain-kararlar §5 |

## §2 NOTLAR

- D-RC9 bir **şema tutarsızlığı bulgusudur**: `recommendation_history` eski nesil
  (`int unsigned` auto-increment) iken bu oturumda doğrulanan diğer tablolar
  (`playback_queue`, `user_preferences` — bkz [[playback-dogrulama]]) `binary(16)`
  UUID v7 kullanır. `algorithm` enum'u da `music_similar`'dan farklıdır: burada
  4 değer vardır (`ai_curated` fazladan — satır 96; music_similar satır 226'da 3).
- D-RC5/D-RC6/D-RC7 tek bir methodun **iç** adımlarıdır (satır 65 → 68 → 71 → 76);
  ayrı ayrı değil zincir olarak okunmalıdır.
- D-RC13 hem uygulama sahipliğini (§4.3) hem bağlam sahipliğini (§6 C2) kapsar —
  ikisi farklı sorulardır ve ikisi de açık.

**Kardeş dosyalar:** [[recommendation-kapsam]] · [[recommendation-web-arastirma]] · [[recommendation-ornekler]] · [[recommendation-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
