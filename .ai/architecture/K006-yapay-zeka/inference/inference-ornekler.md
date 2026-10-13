---
title: "inference — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# inference — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIWorkflow.php` satır 75 — inference çağrısının tek noktası:

```php
// 5. Recommendation engine
$recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);
```

`shared/src/Contracts/AI/AIEngineInterface.php` satır 26–28 — girdi/çıktı sözleşmesi:

```php
@return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
public function getRecommendations(array $context, int $limit = 20): array;
```

> **Doğruluk notu:** `shared/src/AI/` altında ONNX/TFLite/torch gibi bir **model runtime'ı yok**
> (bu oturum grep: `onnx` 0 isabet). Bu örnekler bir **modelin** değil, PHP kural motorunun
> çağrı noktasıdır — kapsam §4/7 ve dogrulama §1/5.

## §2 Resmi doküman örneği (URL + snippet)

**ONNX Runtime — Python tutorial** · https://onnxruntime.ai/docs/api/python/tutorial.html
(doğrulandı 2026-10-12):

```python
import numpy
import onnxruntime as rt

sess = rt.InferenceSession("logreg_iris.onnx", providers=["CPUExecutionProvider"])
input_name = sess.get_inputs()[0].name
pred_onx = sess.run(None, {input_name: X_test.astype(numpy.float32)})[0]
```

**API özeti** · https://onnxruntime.ai/docs/api/python/api_summary (doğrulandı 2026-10-12):

> "ONNX Runtime loads and runs inference on a model in ONNX graph format, or ORT format
> (for memory and disk constrained environments)."

## §3 Uygulama notu

- Örnek 2 bir **aday** mimaridir; CoreMusic'in ONNX kullanıp kullanmadığı **UNKNOWN** (kapsam §5/R2).
- Kademe-3'te "PHP motor ↔ model runtime" geçiş kararı ai-master §4/k4 kapsamında alınmalıdır.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `inference-kapsam.md` | bu dalın rolü, içinde/dışında, sınır soruları |
| `inference-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 260) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
