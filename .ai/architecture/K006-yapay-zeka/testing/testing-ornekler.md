---
title: "Testing — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/testing
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Testing — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde test edilecek AI kodu mevcut, `shared/tests/` içinde AI testi YOK:**

```php
// shared/src/AI/AIEngine.php (satır 97–115) — test edilecek yüzey
public function planWorkflow(string $input, array $context = []): array
{
    // ... workflow planı üretir (deterministik mock)
    return ['status' => 'ok', 'workflow' => $workflow];
}
```

**Yorum (kanıt temelli):** `planWorkflow` gibi metotlar unit test için doğruday adaydır; ancak `shared/tests/` içinde `AIEngine|AIWorkflow|AIOrchestrator` grep'i **0 isabet** verir — AI testi henüz yazılmamıştır (kapsam maddesi 1–2'nin repo karşılığı yok; negatif kanıt).

## §2. Resmi doküman örneği

scikit-learn — cross-validation (resmi doküman, 2026-10-12 doğrulandı):

> "Learning the parameters of a prediction function and testing it on the same data is a methodological mistake: a model that would just repeat the labels of the samples that it has just seen would have a perfect score but would fail to predict anything useful on yet-unseen data."
> — https://scikit-learn.org/stable/modules/cross_validation.html

## §3. Uygulama notu

İlk test dalgası: `AIEngine::planWorkflow` + `AIWorkflow` deterministik akış (unit), ardından orchestrator→engine integration; AI test kütüphanesi kararı (UNKNOWN) öncelenmeli.

## §4. İlişkili dosyalar

- [[testing-kapsam]]
- [[testing-dogrulama]]
- [[../index|K006-yapay-zeka index]]
