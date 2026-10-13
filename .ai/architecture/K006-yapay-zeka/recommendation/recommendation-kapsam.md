---
title: "recommendation — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/recommendation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# recommendation — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
Bu çocuğun adı cümlenin **ilk yarısını** ("şarkı öneren") karşılar; `ai-master.md` §1 sorumluluk tablosu
(satır 37–42) bunu **"Öneri üretmek"** sorumluluğuna bağlar — tablo master'da *tasarım yorumu* olarak işaretlidir.

Basitçe: kullanıcıya ne çalacağını puanlayıp sıralayan taraf. Repo'da karşılığı **vardır**
(`AIEngine::getRecommendations` — hybrid collaborative + content-based), ama bu dalın **resmi kapsam tanımı
v2'de yoktur**; ayrıntı §3'teki R sorularındadır.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Aday üretimi + hybrid skorlama (collaborative + content-based) | Etkileşim/katalog verisinin saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Çeşitlilik (diversity) filtresi ve minimum skor eşiği | "beğeni/öneri" iş kuralının tanımı | K008-domain (bagimliliklar §2/2) |
| 3 | Öneri sonucunun tek ML çağrı noktası olarak verilmesi | Öneri kartının UI'da çizimi | K011-application (ai-master §2.1) |
| 4 | Öneri modelinin K006'da çalıştırılması | Ajan çalışma zamanı / prompt yönetimi | K016-AI (ai-master §2.1) |
| 5 | Öneri kalitesinin ölçülmesi (evaluation ile birlikte) | Genel uygulama testi | K019-testing (bagimliliklar §3/3) |
| 6 | Öneri akışının arka plan süreciyle ilişkisi (sözleşme) | Sürecin kendisi | K012-services (ai-master §2.1) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan altyapısına
öneri yeteneği **taşınamaz**, ajanlar K006'yı **çağırır**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir**. `ai-master.md` §7/2 ve bagimliliklar §5/2'ye göre
öneri sahipliği üç düğüm arasında **açık** (K006 · K008 · K020-recomenataion) — sahiplik ADR'si gelene kadar
bu dal "önerinin sahibi" olduğunu **iddia edemez**.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | K006-recommendation ↔ K008-recommendation ↔ K020-recomenataion | ⚠️ **SINIR AÇIK** | ai-master §7/2 · bagimliliklar §5/2 |
| A2 | Collaborative filtering gerçekten uygulanacak mı (şu an boş)? | ⚠️ taslak — repo kodu `[]` döndürür | `AIEngine.php` satır 209–218 |
| A3 | Skor ağırlıkları (0.6/0.4) sabit mi, öğrenilen mi? | **UNKNOWN** — değer hardcoded | `AIEngine.php` satır 65 |
| A4 | Öneri verisi K007'de mi, önbellekte mi saklanır? | ⚠️ VERIFY REQUIRED | bagimliliklar §4/2 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 261'de birebir geçer | `.ai/architecture/K006-yapay-zeka/recommendation/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `recommendation/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Öneri motoru repo'da mevcut: hybrid 6 adım (collab → content → hybrid → diversity → filtre → sıralama) | `shared/src/AI/AIEngine.php` satır 49–77 | ✅ dosya okundu |
| 4 | Hybrid ağırlıkları 0.6 collaborative / 0.4 content-based (hardcoded) | `shared/src/AI/AIEngine.php` satır 65 | ✅ dosya okundu |
| 5 | Collaborative skorlar henüz boş `[]` ("matrix factorization (ALS) kullanılacak" yorumu) | `shared/src/AI/AIEngine.php` satır 209–218 | ✅ dosya okundu |
| 6 | Sabitler: MAX_RECOMMENDATIONS=20 · MIN_SCORE=0.7 · DIVERSITY_TARGET=0.30 | `shared/src/AI/AIEngine.php` satır 30–32 | ✅ dosya okundu |
| 7 | Workflow: session tercihi → dispatch → knowledge search → engine → feedback | `shared/src/AI/AIWorkflow.php` satır 49–91 | ✅ dosya okundu |
| 8 | Öneri sahipliği üç düğüm arasında açık | `ai-master.md` §7/2 | ⚠️ dosya okundu |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Öneri domain'inin sahibi kim (K006 · K008 · K020)? | ⚠️ SINIR AÇIK — VERIFY REQUIRED |
| R2 | Collaborative filtering / ALS ne zaman uygulanacak? | ⚠️ repo'da boş — IMPLEMENTED değil |
| R3 | Skor ağırlığı 0.6/0.4 kalıcı mı? | ⚠️ **UNKNOWN** |
| R4 | Hangi model formatı/runtime? | ⚠️ ai-master §4 — **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 kapanmadan bu dosyada "öneri sahibi bu düğümüdür" iddiası yazılmaz
(ai-master §7/2 · yapay-zeka-dogrulama §3/2).

## §4 İlişkili dosyalar

- [[recommendation-dogrulama]]
- [[recommendation-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
