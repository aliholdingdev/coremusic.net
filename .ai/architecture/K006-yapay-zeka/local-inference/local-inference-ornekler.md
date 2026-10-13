---
title: "Local Inference — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/local-inference
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Local Inference — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde yerel inference çağıran kod — yok:**

grep `onnx|onnxruntime|tflite|local_inference` → 0 isabet; repo'da yerel model çalıştırma katmanı henüz yok. En yakın kanıt, `AIEngine`'in çalışma zamanı etiketidir:

```php
// shared/src/AI/AIEngine.php (satır 19)
'runtime' => 'cpu', // yerel veya bulut — mock veri
```

**Yorum (kanıt temelli):** `runtime` alanı mock veride sabit `cpu`; yerel ↔ bulut mod seçimi henüz kodda temsil edilmez (kapsam maddesi 1'in repo karşılığı yok).

## §2. Resmi doküman örneği

ONNX Runtime — execution provider seçimi (resmi doküman, 2026-10-12 doğrulandı):

> "Execution Providers allow ONNX Runtime to take advantage of hardware acceleration … e.g. NVIDIA CUDA, DirectML, Apple CoreML, Intel OpenVINO."
> — https://onnxruntime.ai/docs/execution-providers/

## §3. Uygulama notu

Hedef donanım (DirectML · CUDA) kesinleşmeden EP seçimi yapılamaz — ai-master.md §7'deki donanım sorusu önceliklidir.

## §4. İlişkili dosyalar

- [[local-inference-kapsam]]
- [[local-inference-dogrulama]]
- [[../index|K006-yapay-zeka index]]
