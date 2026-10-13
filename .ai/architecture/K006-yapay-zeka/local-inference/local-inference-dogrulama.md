---
title: "Local Inference — Doğrulama"
type: architecture-scope
node_id: K006-yapay-zeka/local-inference
node_type: subdomain-verification
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# Local Inference — Doğrulama

Amaç: `local-inference-kapsam.md` içindeki iddiaların kaynaklarını listelemek. Her satır bir iddia + kaynak + tarih + durum. Durum: ✅ doğrulandı · ⚠️ kısmi · UNKNOWN doğrulanamadı.

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | ONNX Runtime execution providers, donanım hızlandırıcı (CPU · GPU · DirectML · NPU) sıralamasını ve seçimini belgeler | https://onnxruntime.ai/docs/execution-providers/ | 2026-10-12 | ✅ |
| 2 | ONNX Runtime DirectML ExecutionProvider, Windows'ta donanımdan bağımsız GPU hızlandırması sunar (yerel Windows makinesi için temel EP) | https://onnxruntime.ai/docs/execution-providers/DirectML-ExecutionProvider.html | 2026-10-12 | ✅ |
| 3 | ONNX Runtime Python get-started (kurulum · yerel çalıştırma adımları) resmi dokümanda anlatılır | https://onnxruntime.ai/docs/get-started/with-python.html | 2026-10-12 | ✅ |
| 4 | ONNX Python API tutorial, yerel model yükleme ve inference akışını gösterir | https://onnxruntime.ai/docs/api/python/tutorial.html | 2026-10-12 | ✅ |
| 5 | ONNX IR (model dosya biçimi) `ir_version` ve opset alanlarını tanımlar — yerel model uyumluluğu için temel | https://onnx.ai/onnx/repo-docs/IR.html | 2026-10-12 | ✅ |
| 6 | K006 local-inference v2 kapsamı: "offline mod · yerel model cache · hafif SDK" | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-7 (satır 241) | 2026-10-12 | ✅ |
| 7 | ai-master.md §3'te local-inference çıktısı "Yerel model çalıştırma · offline mod" olarak kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §3 (satır 69) | 2026-10-12 | ✅ |
| 8 | ai-master.md §7'de hedef donanım ("Hangi hardware üzerinde AI çalışacak?") açık soru listesinde | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §7 (satır 116) | 2026-10-12 | ✅ |
| 9 | v2 §2.4-4'te cloud→SaaS fallback sinyali kayıtlıdır (yerel/bulut ikiliği komşu kayıt) | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-4 (satır 234) | 2026-10-12 | ✅ |
| 10 | CoreMusic'te yerel inference çağıran kod bulunamadı (ONNX/TF-Lite/yerel model yükleyen sınıf yok) | `C:\www\coremusic.net\shared\src` — grep `onnx\|onnxruntime\|tflite\|local_inference` (0 isabet) | 2026-10-12 | ⚠️ |
| 11 | Hedef donanım (CPU · GPU · NPU) kesinleştirilmemiştir (AIEngine mock'ta `cpu` sabit — satır 19) | `C:\www\coremusic.net\shared\src\AI\AIEngine.php` satır 19 (tahmini; dosya 17–21) | 2026-10-12 | ⚠️ |
| 12 | Yerel ↔ bulut fallback kuralı (geçiş tetikleyicisi) tanımlı değildir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 13 | Yerel model bellek/boyut bütçesi ve quantization eşiği sabitlenmemiştir | UNKNOWN | 2026-10-12 | UNKNOWN |

**Sayım:** ✅9 · ⚠️2 · UNKNOWN2 · Toplam13
