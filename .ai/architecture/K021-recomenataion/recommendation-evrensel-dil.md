---
node_id: K021-recomenataion-evrensel-dil
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K006-yapay-zeka, K007-data, K020-discovery]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-master.md §26 (14 terimlik sözlük — 2026-10-18)"
  - "recommendation-web-arastirma.md §1 (evidentlyai/weaviate 2026-10-18 — metrik EN terimleri)"
  - "recommendation-arastirma.md §0-§1 (Google/TF/sklearn kaynakları — 2026-10-17)"
validation: "≥15 EN/TR terim; her terim K021 kanıt dosyasında geçer; eşik/skor terimleri uydurma sayı içermez."
open_questions:
  - "⚠️ 'K' karmaşası: metrikte K (top-K kesimi) ile K020/K021 düğüm adı aynı harf — sözlükte ayrıştırıldı"
  - "⚠️ 'Sinyal' terimi K021'de örtük/açık/bağlam verisi anlamındadır; K020'de TANIMSIZ sinyal sözleşmesiyle KARIŞTIRILMAZ"
group: mimari
---

# Recommendation — Evrensel Dil (EN ↔ TR)

> K021 sözlüğü: öneri sistemi, boru hattı ve metrik terminolojisi. Kaynak:
> `recommendation-master.md` §26 + 2026-10-17/18 web doğrulaması.

## §1. Temel terimler (20+)

| # | English | Türkçe | Bağlam |
|---|---|---|---|
| 1 | Recommendation system | Öneri sistemi | bu programın konusu |
| 2 | Candidate generation / retrieval | Aday üretimi / geri getirme (S1) | katalog → aday havuzu (⚠️ Faz-1'de YOK) |
| 3 | Scoring / ranking | Skorlama / sıralama (S2) | hibrit skor |
| 4 | Re-ranking | Yeniden sıralama (S3) | çeşitlilik + iş kuralı |
| 5 | Serving | Sunum (S4) | API + önbellek |
| 6 | Feedback loop | Geri bildirim döngüsü (S5) | olay akışı (⚠️ Faz-1'de YOK) |
| 7 | Evaluation & monitoring | Ölçüm ve izleme (S6) | offline + online + sürüklenme |
| 8 | Collaborative filtering | İşbirlikçi süzme | repo: collab skor (0.6) |
| 9 | Content-based filtering | İçerik-bazlı süzme | repo: content skor (0.4) |
| 10 | Hybrid score | Hibrit skor | ağırlıklı birleşim (repo 0.6/0.4 — ⚠️ doğrulanmadı) |
| 11 | Diversity | Çeşitlilik | `DIVERSITY_TARGET=0.30` (repo) |
| 12 | Cold-start | Soğuk başlangıç | verisi olmayan kullanıcı/içerik |
| 13 | Filter bubble | Filtre balonu | coverage/novelty ile denetlenir |
| 14 | Personalization | Kişiselleştirme | profil — K1 KVKK kapısı **önce** |
| 15 | Popularity bias | Popülerlik yanlılığı | ARP/Gini ile ölçülür |
| 16 | Serendipity | beklenmedik-buluşluluk | öngörüyü aşan öneri kalitesi |
| 17 | Novelty | yenilik | uzun-kuyruk ürün oranı |
| 18 | Catalog coverage | katalog kapsamı | listede bulunan benzersiz ürün oranı |
| 19 | Precision@K / Recall@K | K-üstü isabet / geri-çağırma | §1/2, §1/3 (2026-10-18 ✓) |
| 20 | NDCG@K | normalize indirgenmiş kümülatif kazanç | sıra-duyarlı metrik (sklearn kaynağı ✓) |
| 21 | MAP / MRR | ortalama isabet / ortalama ters sıra | ranking metrikleri (weaviate ✓) |
| 22 | A/B test | A/B deneyi | online — Evan Miller kaynağı ✓ |
| 23 | Drift | sürüklenme | S6 izleme sinyali |
| 24 | Ground truth | gerçek-zemin verisi | offline ölçümün dayanağı |
| 25 | Top-K (cutoff) | ilk-K (kesim noktası) | **metrikteki K — düğüm adı K0xx DEĞİL** |
| 26 | Fail-safe / fail-open | güvenli-düşüş / güvensiz-geçiş | bu programda fail-open **yasak** |
| 27 | Threshold | eşik (eşik sayısı) | **bu vaultta atanmadı — ADR-3** |
| 28 | Reason (explanation) | gerekçe | `items[].reason` — Faz-1'de YOK (B4) |

## §2. Boru hattı kısa adları (S1-S6 — kanonik)

| Kısa ad | EN | TR |
|---|---|---|
| S1 | candidate generation | aday üretimi |
| S2 | scoring | skorlama |
| S3 | re-rank | yeniden sıralama |
| S4 | serving | sunum |
| S5 | feedback | geri bildirim |
| S6 | evaluation | ölçüm |

## §3. Sözlük uyarıları (çelişki noktaları)

1. **K çift anlamlı:** `Precision@K`'deki K = metrik kesim noktası; `K020/K021` =
   düğüm adı. Aynı harf, farklı katman — karıştırılmaz.
2. **Ranking ≠ Ranking sistemi kod adı:** burada S2/S3 anlamında "sıralama";
   `ranking/` alt sistemi bu anlamdadır.
3. **Score (skor) ≠ threshold (eşik):** skor üretilir (repo `0.6/0.4`); eşik
   **atanmadı** (`MIN_SCORE=0.7` repo değeri — tasarım onayı yok, "onaylı eşik"
   diye sunulmaz).
4. **Retrieval (S1) vs K020-discovery "keşif":** retrieval sistem içi aday
   getirmedir; discovery **dış servis** keşfidir — TR'de ikisi "getirme/keşif"
   diye karışmasın.
5. **Explicit/implicit signal:** açık (favori) / örtük (dinleme süresi) — K020'nin
   tanımsız "sinyal sözleşmesi" ile **aynı şey değildir**.

## §4. Kaynaklar

- `recommendation-master.md` §26 (14 terim — bu dosyanın çekirdeği)
- `recommendation-web-arastirma.md` §1 (evidentlyai ×2 · weaviate · CRAN recometrics — 2026-10-18 ✓)
- `recommendation-arastirma.md` §0-§1 (Google · TF · scikit-learn — 2026-10-17 ✓)

---

**Authority:** Bayram Ali / Vault Steward
