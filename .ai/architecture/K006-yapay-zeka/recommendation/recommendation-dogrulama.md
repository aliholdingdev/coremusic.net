---
title: "recommendation — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/recommendation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# recommendation — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, algoritma, bibliyotek uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "recommendation" v2 §2.6 satır 261'de birebir geçer | `.ai/architecture/K006-yapay-zeka/recommendation/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `recommendation/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | Repo'da hybrid öneri akışı 6 adımda uygulanır | `shared/src/AI/AIEngine.php` satır 49–77 | 2026-10-12 | ✅ dosya okundu |
| 4 | Hybrid ağırlık 0.6 collaborative / 0.4 content-based | `shared/src/AI/AIEngine.php` satır 65 | 2026-10-12 | ✅ dosya okundu |
| 5 | Collaborative tarafı henüz boş — ALS yorumu var, kod yok | `shared/src/AI/AIEngine.php` satır 216–217 | 2026-10-12 | ✅ dosya okundu |
| 6 | Öneri sahipliği K006 · K008 · K020 arasında açık | `.ai/architecture/K006-yapay-zeka/ai-master.md` §7/2 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 7 | Hybrid öneri mimarisi "collaborative-filtering veya content-based" kullanımı sağlar (LibRecommender) | https://librecommender.readthedocs.io/en/stable/ | 2026-10-12 | ✅ doğrulandı |
| 8 | Microsoft Recommenders, ALS/SAR/LightGCN gibi CF algoritmalarını ve offline metrikleri içerir | https://github.com/recommenders-team/recommenders | 2026-10-12 | ✅ doğrulandı |
| 9 | Collaborative filtering (CF) tanımı ve kategorileri (memory-based / model-based) resmi ders kaynağındadır | http://www.d2l.ai/chapter_recommender-systems/recsys-intro.html | 2026-10-12 | ✅ doğrulandı |
| 10 | Amazon Personalize, öneri skorlarını 0–1 aralığında **göreli** üretir | https://docs.aws.amazon.com/personalize/latest/dg/recommendations.html | 2026-10-12 | ✅ doğrulandı |
| 11 | CoreMusic'in hangi CF algoritmasını (ALS vb. kullanacağı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | CoreMusic'in LibRecommender / Recommenders / Personalize'i kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 13 | Skor ağırlıkları 0.6/0.4'ün tasarım kararı olduğu | `shared/src/AI/AIEngine.php` satır 65 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 14 | K006 teknoloji ekosistemi (model formatı / runtime) | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |
| 15 | 9 çocuğun kademe-3 (bounded-context) yapısı | ADR-097 K4 | 2026-10-12 | ⚠️ BEKLİYOR |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| İşlenen benzersiz URL | 4 (7 · 8 · 9 · 10) |
| Gerçek dosya yolu satırı | 7 (1 · 2 · 3 · 4 · 5 · 6 · 13) |
| ✅ doğrulandı | 9 (1–5 · 7–10) |
| ⚠️ VERIFICATION REQUIRED | 3 (6 · 13 · 15) |
| UNKNOWN | 3 (11 · 12 · 14) |

**Geçiş kuralı:** satır 6 sahiplik ADR'siyle, 13 tasarım onayıyla, 15 ADR-097 K4 ile; satır 11–12 ve 14
teknoloji seçim kaydıyla (`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
