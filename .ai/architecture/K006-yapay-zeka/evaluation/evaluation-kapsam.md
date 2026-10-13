---
title: "Evaluation — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/evaluation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Evaluation — Kapsam

**ELI15:** Evaluation (değerlendirme), yapay zekanın **karnesini** çıkarmaktır: model iyi mi, kötü mü, nerede hata yapıyor, zamanla bozuluyor mu — bunu ölçülebilir metriklerle (Precision · Recall · NDCG · MAE · drift) söylersin. "Güzel çalışıyor" hissi sayıya dönüşmeden model üretime alınamaz. Bu alt alan, model ve öneri çıktısının **kalite ölçümünü**, veri/kavram drift'ini (zamanla değişen davranış) ve hype döngüsünün (döngüsel doğrulama) ölçüm tarafını kapsar. K006'nın çocuk tablosunda (ai-master.md §3) 8. sırada tanımlıdır; `evaluation/` klasörünün `index.md` (satır 1–47) kademe-2 kapsam taslağının olmadığı, üst düzey kapsamın v2 §2.4-8'de verildiğini belirtir.

## 1. Kapsam içi (İçinde)

1. **Öneri/çıktı metrikleri** — öneri sistemleri için Precision@k · Recall@k · NDCG · MAP · MRR; sınıflandırma/regresyon için F1 · MAE/RMSE; hangi metriğin hangi domain'de (M01–M12) kullanıldığı.
2. **Drift ve veri dağılımı ölçümü** — zamanla veri/kavram değişimi (data drift · concept drift); üretim ile eğitim verisi arasındaki farkın izlenmesi.
3. **A/B ve karşılaştırmalı değerlendirme** — eski model ↔ yeni model, yerel ↔ bulut çıktısının karşılaştırılması; kontrol grubu.
4. **Hype döngüsü ölçümü (döngüsel doğrulama)** — K006 hype loop'undaki doğrulama adımının metrik tarafı; `evaluation/` alt alanının ana işi.
5. **Değerlendirme veri seti** — test/hold-out verisi bölme, gizli sızıntıyı (leakage) önleme; ölçümün güvenilirliği.

## 2. Kapsam dışı (Dışında)

1. **Unit/integration test (yazılım testi)** — kod testi `testing/` alt alanındadır (v2 §2.4-10); burada model/çıktı **kalite metrikleri** ölçülür.
2. **Modelin eğitilmesi** — eğitim işlemi hype loop'un kendisidir; burada yalnız ölçüm.
3. **Model kayıt/dağıtım** — `model-management/` kapsamındadır (metrik buradan registry'e akar).
4. **Guardian/6-axis gate kararları** — `yapay-zeka-dogrulama.md`'deki gate; bu alt alan girdiyi hazırlar, kararı vermez (karar gate/insan).
5. **Ajan / zeka altyapısı (K016-AI)** — ajan orkestrasyonu K016'ya aittir (K006 sınırı, `v2.md §2.6`).

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Alt alan envanteri | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 71) | 8. satır · "Ölçüm & hype döngüsü"; çıktı "Metrik · drift · A/B ölçümü" |
| Kapsam sınırı (v2) | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-8` (satır 246) | "Minimal kapsam: A/B test framework · Guard · Guardian log" |
| Gate · ölçüm girdisi | `.ai/architecture/K006-yapay-zeka/yapay-zeka-dogrulama.md` | Guardian/6-axis gate — metrik bu alt alanın çıktısıdır |
| Envanter · ML domain | `.ai/architecture/K006-yapay-zeka/ai-master.md §1` (satır 47) | M12 · music-ML — model değerlendirme sorumluluğu |
| Test ile ayrım | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-10` (satır 252) | testing ayrı alt alan — kapsam ayrımı §2 maddesi 1–2 |
| Bağımlılık · ölçüm | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 22) | M13 · mem0-lancedb ... loop persist — döngüsel doğrulama verisi |

## 4. Açık sorular

1. **Metrik seti kesinleşmedi** — hangi domain hangi metrikle ölçülecek (NDCG · F1 · MAE) seçilmedi; `ai-master.md §4` "Model değerlendirme yöntemi UNKNOWN" (satır 94).
2. Drift eşiği (kaç σ/sıklıkta uyarı) ve otomatik tepki (modeli geri al mı, uyar mı) netleşmedi.
3. A/B altyapısı (trafik bölme · istatistiksel anlamlılık) tasarımsız.
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.4-8 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Hype döngüsü doğrulama adımının tekrar sıklığı ve otomatik mi manuel mi olacağı belirsiz.

## İlişkili dosyalar

- [[evaluation-dogrulama]]
- [[evaluation-ornekler]]
- [[../index|K006-yapay-zeka index]]
