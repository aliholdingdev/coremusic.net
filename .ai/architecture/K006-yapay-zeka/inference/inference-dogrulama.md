---
title: "inference — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# inference — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, API uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "inference" v2 §2.6 satır 260'ta birebir geçer | `.ai/architecture/K006-yapay-zeka/inference/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `inference/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | Inference çağrısının tek noktası: workflow → engine | `shared/src/AI/AIWorkflow.php` satır 75 | 2026-10-12 | ✅ dosya okundu |
| 4 | Çıktı sözleşmesi `array{id, title, artist, score, reason}` | `shared/src/Contracts/AI/AIEngineInterface.php` satır 26–28 | 2026-10-12 | ✅ dosya okundu |
| 5 | `shared/src/AI/` içinde ONNX/TFLite/torch runtime'ı yok (grep `onnx` 0 isabet) | `.ai/architecture/K006-yapay-zeka/inference/` (bu oturum taraması) | 2026-10-12 | ✅ oturum taraması |
| 6 | ONNX Runtime, eğitim framework'ünden bağımsız yüksek performanslı skoring motorudur | https://onnxruntime.ai/docs/api/python/index.html | 2026-10-12 | ✅ doğrulandı |
| 7 | `InferenceSession` ONNX/ORT modelini yükler; `run(output_names, input_feed)` tahmin üretir | https://onnxruntime.ai/docs/api/python/api_summary | 2026-10-12 | ✅ doğrulandı |
| 8 | Standart akış: train → ONNX'e dönüştür → ONNX Runtime ile çalıştır | https://onnxruntime.ai/docs/api/python/tutorial.html | 2026-10-12 | ✅ doğrulandı |
| 9 | ONNX, hesaplama grafiğini taşınabilir serialize formatında tanımlar (ModelProto) | https://onnx.ai/onnx/repo-docs/IR.html | 2026-10-12 | ✅ doğrulandı |
| 10 | CoreMusic'in ONNX Runtime kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 11 | Model formatı seçimi (ONNX · pickle · PMML · özel) | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |
| 12 | inference ↔ local-inference ↔ model-management sınırı | `ai-master.md` §3 satır 259–260 · 265 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | Mevcut PHP motorun yerine (veya yanına) harici model runtime geçişi | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 13 |
| İşlenen benzersiz URL | 4 (6 · 7 · 8 · 9) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) |
| ✅ doğrulandı | 9 (1–9) |
| ⚠️ VERIFICATION REQUIRED | 1 (12) |
| UNKNOWN | 3 (10 · 11 · 13) |

**Geçiş kuralı:** satır 12 ADR-097 K4 ile; satır 10–11 ve 13 teknoloji seçim kaydıyla
(`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
