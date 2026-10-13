---
title: "Model Management — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/model-management
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Model Management — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde model yönetimine dair kod — kısıtlı (yalnız model adı referansı):**

```php
// shared/src/AI/AIEngine.php (satır 28–32)
$model = 'core-algorithm-orchestrator';
$confidence = 0.75;
$durationMs = 120;
$timestamp = new DateTimeImmutable();
```

**Yorum (kanıt temelli):** Model adı sabit string olarak tutulur; **sürüm alanı, artifact yolu, registry çağrısı yoktur** — kayıt defteri henüz kodda temsil edilmez (kapsam maddesi 1'in repo karşılığı henüz yok). `shared/src` grep'i `registry|artifact|mlflow` için 0 isabet verir.

**`shared/src/Contracts/AI` sözleşme yüzeyi (6 interface — disk grep 2026-10-12):** `AIEngineInterface` · `AIOrchestratorInterface` · `KnowledgeBaseInterface` · `MemorySystemInterface` · `PromptEngineInterface` · `ToolCallingInterface` (33 imza) — model yönetimine dair bir method imzası **yok** (envanter genişletilirken eklenebilir; kapsam maddesi 5). Not: yol `shared/Contracts/AI` değil, **`shared/src/Contracts/AI`**; `AIWorkflowInterface`/`AIQualityInterface`/`AIHealthInterface`/`AIUsageInterface` adlarında arayüz **repoda yoktur** (Düzeltme kaydı 2026-10-12).

## §2. Resmi doküman örneği

MLflow Model Registry — sürüm + aşama etiketleme (resmi doküman, 2026-10-12 doğrulandı):

> "MLflow Model Registry provides a centralized model store, set of APIs, and UI to collaboratively manage the full lifecycle of an MLflow Model. It provides model lineage … and model versioning."
> — https://mlflow.org/docs/latest/ml/model-registry/

## §3. Uygulama notu

Registry teknolojisi seçilmeden (ai-master.md §4 satır 87 UNKNOWN) deploy tasarımı yapılamaz; önce kayıt şeması (ad · sürüm · metrik · artifact yolu) sabitlenmeli.

## §4. İlişkili dosyalar

- [[model-management-kapsam]]
- [[model-management-dogrulama]]
- [[../index|K006-yapay-zeka index]]
