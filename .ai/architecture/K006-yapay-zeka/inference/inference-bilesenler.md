---
node_id: K006.2-inference-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L49–133 · L161–205 · L332–382 (2026-10-12 tam okuma)"
  - "shared/src/Contracts/AI/AIEngineInterface.php L28–60 (5 tahmin imzası)"
  - "shared/src/** grep onnx|torch|tensorflow|vllm|whisper → 0 eşleşme (2026-10-12)"
validation: "2026-10-12 · tüm metot/satır grep ile doğrulandı; model ağırlığı yükleyen kod = 0 satır · status=verified"
open_questions:
  - "predictFault/optimizeEQ yüzeyleri gerçek modele ne zaman taşınacak → UNKNOWN"
  - "Çıkarım süreci PHP içinde mi, ayrı servis mi olacak → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.2-inference — Bileşenler

> K006.2'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. **Model ağırlığı yükleyen / çıkarım yapan hiçbir kod satırı yoktur** —
> repo'daki tüm "tahmin" yüzeyleri kural tabanlıdır.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.2'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, hiçbiri "inference" adında değil |
| Kural-tabanlı "tahmin" metotları | 3 (`predictFault` L161 · `optimizeEQ` L111–133 · `analyzeHardware` L138) | §2 |
| Deterministik yardımcı hesaplar | 3 (`bandToFrequency` L332 · `calculateRoomCorrection` L347 · `calculateBandQ` L368) | §2.3 |
| Model/ağırlık yükleyen kod | **0 satır** | grep `onnx|torch|tensorflow|vllm|gguf` → 0 (2026-10-12) |
| Tahmin sözleşmeleri | 5 imza (`AIEngineInterface` L28–60: getRecommendations · analyzeAudio · optimizeEQ · analyzeHardware · predictFault) | §4 |
| Dış çıkarım istemcisi (HTTP/CLI) | **0 satır** | grep → 0 (LLM/model endpoint çağrısı yok) |

---

## §2 Gerçek Kod Bileşenleri

### §2.1 `AIEngine::predictFault(array $hardwareSpecs): array` — L161 (public)

| Satır | Davranış |
|---|---|
| 163–205 (yaklaşık bölge) | donanım özelliklerinden kural tabanlı arıza tahmini döner |

**Sorumluluk:** "tahmin" adını taşır ama **model değildir** — eşik/kural ağacı. Terminoloji
tuzağı kaydı (web-arastirma C4).

### §2.2 `AIEngine::optimizeEQ(array $frequencyResponse, string $roomType): array` — L111–133

| Satır | Davranış |
|---|---|
| 115–133 | 31 bantlı EQ düzeltmesi üretir; `calculateBandQ`/`calculateRoomCorrection` yardımıyla |

Deterministik DSP hesabı — eğitim yok, ağırlık yok.

### §2.3 Yardımcı hesaplar (private)

| Metot | Konum | Davranış |
|---|---|---|
| `bandToFrequency` | L332 | bant indeksi → frekans dönüşümü |
| `calculateRoomCorrection` | L347 | oda tipi → düzeltme eğrisi |
| `calculateBandQ` | L368 | bant başına Q faktörü |

### §2.4 `AIEngine::getRecommendations` L49–77 · `analyzeAudio` L82–106 · `analyzeHardware` L138

| Metot | "Çıkarım" değerlendirmesi |
|---|---|
| `getRecommendations` | sabit ağırlıklı (0.6/0.4, L65) skor birleştirme — **model değil** |
| `analyzeAudio` | `extractMetadata` (ffprobe) + `extractFeatures` (**placeholder**, L315–330) |
| `analyzeHardware` | kural tabanlı donanım puanlama |

---

## §3 Workflow Bileşenleri — `AIWorkflow` (tahmin çağıran akışlar)

| Workflow | Konum | Çağrı | Not |
|---|---|---|---|
| `recommendationWorkflow` | L49–91 | `engine->getRecommendations(...)` | §2.4 |
| `audioAnalysisWorkflow` | L99–139 | `engine->analyzeAudio(...)` | placeholder girdi |
| `autoEQWorkflow` | L147 | `engine->optimizeEQ(...)` | §2.2 |
| `hardwareAnalysisWorkflow` | L199 | `engine->analyzeHardware(...)` | kural tabanlı |
| `faultPredictionWorkflow` | L241 | `engine->predictFault(...)` | §2.1 |

**İzlek:** 5 workflow'un tamamı **aynı PHP motorunu** (AIEngine) çağırır; hiçbiri dış model
servisiyle konuşmaz (grep kanıtı — §1).

---

## §4 Sözleşme Eşlemesi

| Sözleşme | İmza | Konum | Uygulayan |
|---|---|---|---|
| `AIEngineInterface::getRecommendations(...)` | 1/5 | Contracts L28 | `AIEngine` L49 |
| `AIEngineInterface::analyzeAudio(...)` | 2/5 | Contracts L36 | `AIEngine` L82 |
| `AIEngineInterface::optimizeEQ(...)` | 3/5 | Contracts L44 | `AIEngine` L111 |
| `AIEngineInterface::analyzeHardware(...)` | 4/5 | Contracts L52 | `AIEngine` L138 |
| `AIEngineInterface::predictFault(...)` | 5/5 | Contracts L60 | `AIEngine` L161 |
| (çıkarım runtime'ı için interface) | — | **YOK** | — |

5/5 implementasyon tam — ama hiçbiri model tabanlı değildir (§1).

---

## §5 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.2 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Model yükleme/çalıştırma (runtime) | **0 satır** | grep `onnx|torch|tensorflow|vllm` → 0 |
| Model ağırlığı dosyası (.onnx/.gguf/.pt) | **0 dosya** | repo taraması (2026-10-12 kök ölçüm) |
| Dış model servisi istemcisi | **0 satır** | grep → 0 |
| Çıkarım önbelleği | **0 satır** | grep `cache|redis|memcached` composer.json → 0 |
| Batch/streaming çıkarım | **0 satır** | grep → 0 |
| Kural-tabanlı tahmin | ✅ **var** | L161 · L111–133 · L138 |
| Deterministik hesaplar | ✅ **var** | L332 · L347 · L368 |

---

## §6 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.7-local-inference | yerel çıkarım (llama.cpp/whisper.cpp ailesi) K006.2'nin "runtime" sorusunun bir alt seçeneği |
| K006.1-model-management | model ağırlığı yüklenince kayıt/sürüm katmanı gerekir — bugün 0 satır |
| K006.6-audio-intelligence | `analyzeAudio` girdisi placeholder (L315–330) — gerçek çıkarım zincirin başında kırık |
| K006.3-recommendation | skorlama (L49–77) bugün tek "çıkarım" izlenimi veren yüzey |

---

## §7 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L49–133 · L161–205 · L332–382 tam okuma, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L49–271 metod bölgeleri, 2026-10-12.
3. `shared/src/Contracts/AI/AIEngineInterface.php` — L28–60, 2026-10-12.
4. `shared/src/**` + `shared/composer.json` grep (`onnx|torch|tensorflow|vllm|gguf|whisper|transcri`) → 0 eşleşme, 2026-10-12.
5. `inference-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 6 gerçek metot + 5 workflow çağrısı satır aralıklarıyla doğrulandı, 7 beklenen bileşenden 5'i 0 satır`
