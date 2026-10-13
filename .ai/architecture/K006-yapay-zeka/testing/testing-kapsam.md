---
title: "Testing — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/testing
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Testing — Kapsam

**ELI15:** Testing, yapay zeka kodunun **test sürüşü**'dür: model servise bağlanmadan önce küçük örneklerle denenir — bozuk veri gelirse çöküyor mu, boş çıktı üretince nasıl davranıyor, aynı girdiye aynı cevabı veriyor mu (tutarlılık). Test, hatayı müşteriden önce bulmaktır. Bu alt alan, K006 AI kodunun **yazılım testini** (unit · integration · regresyon) ve yeniden üretilebilirlik (reproducibility) testini kapsar — model kalite metrikleri `evaluation/` alt alanındadır. K006'nın çocuk tablosunda (ai-master.md §3) 9. alt alan olarak testing, son sırada tanımlıdır; `testing/` klasörünün `index.md` (satır 1–47) kademe-2 kapsam taslağının olmadığı, üst düzey kapsamın v2 §2.4-10'da verildiğini belirtir.

## 1. Kapsam içi (İçinde)

1. **Unit test** — AI sınıfı/metot düzeyinde test (`AIEngine` · `AIWorkflow` · `PromptEngine`): her metot doğru tipte/dalıkta sonuç döndürüyor mu, hata (Result/Either) yolu çalışıyor mu.
2. **Integration test** — AI akışının diğer katmanlarla (DB · HTTP · auth) birleştiği yerde test; orchestrator → engine zincirinin uçtan uca davranışı.
3. **Regresyon testi** — model/prompt değişince eski davranışın bozulmadığını doğrulama; aynı fixture ile beklenen çıktı.
4. **Yeniden üretilebilirlik (reproducibility)** — aynı girdi + aynı model sürümü → aynı çıktı; determinizm (seed · sıcaklık) kontrolü.
5. **Test altyapısı** — test fixture'ları (örnek ses/prompt/metin), test verisi üretimi, CI'ye bağlama; `shared/tests` yapısı.

## 2. Kapsam dışı (Dışında)

1. **Model kalite metrikleri (NDCG · drift · A/B)** — bunlar `evaluation/` alt alanındadır (kapsam ayrımı §2 maddesi 1); burada yalnız kod testi.
2. **Model eğitimi ve hyperparameter arama** — hype loop'un kendisi.
3. **Guardian/6-axis gate kararı** — `yapay-zeka-dogrulama.md` kapsamıdır; test girdi üretir, kapı kararı vermez.
4. **K016-AI ajan testleri** — ajan altyapısı K016'ya aittir (K006 sınırı, `v2.md §2.6`).
5. **UI/frontend testi** — arayüz testi K006 dışıdır (AI çıktısını gösteren ekran testi ayrı sahiptir).

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Alt alan envanteri | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 72) | 9. satır · "AI testi · regresyon"; çıktı "AI unit · integration test" |
| Kapsam sınırı (v2) | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-10` (satır 252) | "Minimal kapsam: unit · integration · regression · fuzz" |
| Test altyapısı | `shared/tests/` | PHPUnit yapısı mevcut; **AI'a dair test dosyası grep ile 0 isabet** (negatif kanıt: `shared/tests` içinde `AI` geçen dosya yok) |
| Test ile evaluation ayrımı | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-8` (satır 246) | evaluation ayrı alt alan — §2 maddesi 1–2 |
| El yazımı regresyon riski | `shared/AGENTS.md` (satır 120–137) | `hash_hmac('sha256', …)` — değişmeyecek veri için elle yazıldı (regresyon testi çıkarımı) |
| Bağımlılık · test | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 24) | "AI test kütüphanesi: UNKNOWN" · testing bağımlılık satırı |

## 4. Açık sorular

1. **AI test kütüphanesi bilinmiyor** — `yapay-zeka-bagimliliklar.md` (satır 24) "AI test kütüphanesi: UNKNOWN"; PHPUnit mü yoksa özel fixture mı seçilecek belirsiz.
2. CI'ya bağlama (hangi step'te AI testleri koşar) ve hedef coverage oranı sabitlenmedi.
3. Determinizm politikası (seed · sıcaklık sabitleme) netleşmedi; LLM çıktısında tekrarlanabilirlik nasıl sağlanacak?
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.4-10 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Fuzz/bozuk veri testi kapsamı (hangi girdi sınırları) tanımsız.

## İlişkili dosyalar

- [[testing-dogrulama]]
- [[testing-ornekler]]
- [[../index|K006-yapay-zeka index]]
