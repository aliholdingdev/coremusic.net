---
title: "Evaluation — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/evaluation
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Evaluation — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde değerlendirme metrik kodu — yok:**

grep `ndcg|precision|recall|drift|map@` → 0 isabet; repo'da metrik hesaplayan sınıf yok. En yakın kanıt, `AIEngine`'in güven (confidence) döngüsüdür:

```php
// shared/src/AI/AIEngine.php (satır 163)
// confidenceBoost = 0.05 — 10. çağrıdan sonra "high" etiketi
```

**Yorum (kanıt temelli):** `confidenceBoost` bir **değerlendirme metriği değil**, mock bir artıştır (kapsam maddesi 1'in repo karşılığı henüz yok); gerçek ölçüm (NDCG · drift) henüz kodda yok.

## §2. Resmi doküman örneği

Evidently AI — öneri sistemi metrikleri (resmi doküman, 2026-10-12 doğrulandı):

> "Recommendation system metrics … accuracy of the model (precision, recall, F1, and more) … or the quality of recommendations (for example, in the form of a ranked list)."
> — https://docs.evidentlyai.com/metrics/explainer_recsys

## §3. Uygulama notu

Metrik seti seçimi (ai-master.md §4 satır 94 UNKNOWN) yapılmadan A/B/Guardian log tasarımı başlayamaz; önce domain→metrik eşlemesi sabitlenmeli.

## §4. İlişkili dosyalar

- [[evaluation-kapsam]]
- [[evaluation-dogrulama]]
- [[../index|K006-yapay-zeka index]]
