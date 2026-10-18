---
node_id: K021-recomenataion-bilesenler
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K006-yapay-zeka, K007-data, K015-AI, K020-discovery]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-master.md §4-§5 (S1-S6 + 3 alt sistem) ve §13/§24 (repo kanıtları — 2026-10-17 okundu)"
  - "shared/src/AI/AIEngine.php:30 (MAX_RECOMMENDATIONS=20) · :31 (MIN_SCORE=0.7) · :32 (DIVERSITY_TARGET=0.30) · :65 (0.6/0.4) · :94-100 (ses özellikleri)"
  - ".ai/architecture/K021-recomenataion/ (disk: index · master · kod-ornekleri · arastirma + ranking/ · personalization/ · evaluation/ — 2026-10-18 glob)"
validation: "Her bileşen disk/repo kanıtıyla; Faz-1 karşılığı OLMAYAN bileşen 'yok' olarak işaretlenir (B1-B4)."
open_questions:
  - "⚠️ S1 (aday üretimi) · S5 (geri bildirim) · S6 (ölçüm) repo'da YOK — boşluk B1/B2/B3"
  - "⚠️ applyDiversityFilter gerçek kullanım satırı 110+ (okuma bütçesi dışı — iddia yazılmadı, master §24.1/3)"
group: mimari
---

# Recommendation — Bileşenler

> K021'in envanteri: boru hattı bileşenleri (S1-S6), alt sistemler (3), repo
> bileşenleri (Faz-1 kanıtlı) ve dosya envanteri. Var olmayan bileşen "yok" olarak
> işaretlenir — uydurulmaz.

## §1. ELI15

Bileşen = öneri makinesinin parçası. Üç grup: (1) **boru hattı aşamaları** (S1-S6
— katalogdan kullanıcıya giden yol); (2) **alt sistemler** (ranking, personalization,
evaluation — kim neyi yapar); (3) **repo bileşenleri** (gerçek PHP kodunda var olan
parçalar). Üçüncüsü hariç hepsi **tasarım**dır; Faz-1'de çalışan kod S2→S3→S4
segmentidir (master §24).

## §2. Boru hattı bileşenleri (S1-S6 — kanonik)

| Bileşen | Aşama | Girdi → Çıktı | Faz-1 repo karşılığı |
|---|---|---|---|
| S1 aday üretimi | candidate generation | katalog + profil → aday havuzu | **YOK** (boşluk B1 — `AIEngine.php:58-62` doğrudan skorlar) |
| S2 skorlama | scoring | aday + sinyaller → skorlu liste | ✓ `AIEngine.php:59,62,65` (collab+content → 0.6/0.4) |
| S3 yeniden sıralama | re-rank | skorlu → nihai sıra | ✓ `AIEngine.php:68,71,74` (diversity + MIN_SCORE + sort) |
| S4 sunum | serving | nihai sıra → API yanıtı | ✓ `AIEngine.php:51,76` (limit + array_slice) |
| S5 geri bildirim | feedback loop | olay → S1 akışı | **YOK** (boşluk B2 — dosyada geri bildirim API'si yok, 382 satır) |
| S6 ölçüm/izleme | evaluation | olay + sıra → metrik | **YOK** (boşluk B3 — `evaluation/index.md` bekliyor) |

**Kapalı döngü:** `S1→S2→S3→S4→S5→S6 ─(akış)→ S1`; S6 tanımlanmadan S4 yayına
alınamaz (master §9 K4/K5).

## §3. Alt sistem bileşenleri

| Alt sistem | Sorumluluk | Sıra kuralı | Dosya |
|---|---|---|---|
| Ranking | adayları sıralamak (S2/S3) | kişiselleştirme olmadan yapılabilir | `ranking/index.md` |
| Personalization | kullanıcıya özel kılmak (profil) | KVKK K1 kapısı **önce** | `personalization/index.md` |
| Evaluation | ölçmek (S6) | sıralama yoksa ölçüm anlamsız; kapı en baştan tanımlı | `evaluation/index.md` |

## §4. Repo bileşenleri (Faz-1 — `shared/src/AI/AIEngine.php`)

| Bileşen | Kanıt (satır) | Rol |
|---|---|---|
| `MAX_RECOMMENDATIONS = 20` | :30 | öner limiti (istemci aşamaz — §24.1/4) |
| `MIN_SCORE = 0.7` | :31 | minimum skor eşiği (repo değeri — **tasarım onayı yok**) |
| `DIVERSITY_TARGET = 0.30` | :32 | çeşitlilik hedefi (kullanım satırı 110+ → ⚠️) |
| Bağlam ayrıştırma | :52-56 | `user_id`, `session_id`, `mood`, `genre`, `history` |
| `getCollaborativeScores()` | :59 | işbirlikçi skor (S2) |
| `getContentBasedScores()` | :62 | içerik skoru (S2) |
| `combineScores(..., 0.6, 0.4)` | :65 | hibrit ağırlık (S2) |
| `applyDiversityFilter()` | :68 | çeşitlilik (S3) |
| `array_filter(>= MIN_SCORE)` | :71 | eşik filtresi — sort **öncesi** (§24.1/2) |
| `usort` skora göre | :74 | sıralama (S3) |
| `array_slice(0, $limit)` | :76 | çıktı kesme (S4) |
| Ses özellikleri çıkarımı | :94-100 | bpm/key/energy/mood/danceability/acousticness |

**Misafir akışı bileşeni:** `context['user_id'] ?? null` (§24.1/5) — kimlik
opsiyonel → kişiselleştirme kapalı çalışabilir (fail-safe).

## §5. Sözleşme/ölçüm bileşenleri

| Bileşen | Tanım | Durum |
|---|---|---|
| API sözleşmesi (S4) | `GET /api/v1/recommendations` JSON şekli + hata kodları (master §18) | şekil — alanlar ADR-2 sonrası kesin |
| Ölçüm katmanları | offline (NDCG/Precision) · çeşitlilik (coverage/novelty) · online (A/B) · izleme (sürüklenme) | metrik **isimleri** — eşik YOK |
| Kapılar K1-K5 | veri kapsamı → veri kaynağı → yöntem → ölçüm → yayım | K1/K2 ⚠️ · K3-K5 ⏸ |
| Olay şeması (S5) | dinleme/atlama/favori alanları | **tanımsız** (⚠️) |
| İzleme sinyalleri | yanıt p50/p95 · aday havuzu · boş çıkma · skor kayması · önbellek · olay gecikmesi | eşikler Faz-4 sonrası |

## §6. Dosya envanteri (2026-10-18 glob)

| Dosya/dizin | İçerik |
|---|---|
| `index.md` | klasör kapısı + çocuk kataloğu |
| `recommendation-master.md` | 31 bölümlük omurga (v2.0.0) |
| `recommendation-kod-ornekleri.md` | 22 dil matrisi (S1-S6 örnekleri) |
| `recommendation-arastirma.md` | 28 URL kanıt tablosu (group: kanit) |
| `ranking/index.md` · `personalization/index.md` · `evaluation/index.md` | alt bölgeler |

## §7. Yok bileşenler (kanıtla — uydurulmaz)

| Yok | Kanıt |
|---|---|
| Aday havuzu katmanı (S1) | `AIEngine.php:58-62` — iki skorlayıcı doğrudan çağrılır (B1) |
| Geri bildirim toplama (S5) | 382 satırda geri bildirim API'si yok (B2) |
| Metrik/log hesabı (S6) | dosyada ölçüm kodu yok (B3) |
| `reason` gerekçe alanı | `:76` — çıktı `(item, score)` (B4) |
| Profil şeması | KVKK K1 kapalı — yazılmaz (master §7) |
| Model/feature store | K006'ya ait — bu K'da yok |

## §8. Açık sorular

1. ⚠️ S1 stratejisi (item-item CF / içerik / trend karışımı) — ADR-2 kapısı.
2. ⚠️ Ağırlık 0.6/0.4 veriyle doğrulanmadı — Faz-4 ölçümü.
3. ⚠️ Çeşitlilik kotasının gerçek uygulama satırı okunmadı (110+ — bütçe dışı).

---

**Authority:** Bayram Ali / Vault Steward
