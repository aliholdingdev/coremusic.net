---
node_id: K006-yapay-zeka-dogrulama
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - K004-shared
  - K016-AI
evidence:
  - "URL_fetcher ile 2026-10-12 tarihli 27 iddia·URL doğrulaması (§2)"
  - "5 başarısız URL — çelişki olarak kaydedildi (§3)"
  - "repo kod kanıtları — 2026-10-12 okundu (§4)"
validation: "2026-10-12 · 27 claim·URL satırı + 5 başarısız kayıt + 7 repo kanıtı · status=verify-required (§3/§4 açık kalemler nedeniyle)"
open_questions:
  - "§3 5 başarısız URL — alternatif doğrulama gerekiyor"
  - "§4-R5/R6/R7 contract içerikleri okunmadı"
group: kanit
---

# K006 — Yapay Zeka · Doğrulama

> **Claim·URL matrisi (bağlayıcı):** her iddia → URL → tarih → durum. Durum sözlüğü:
> `verified` (URL_fetcher 200 + içerik tutarlı) · `failed` (çekilemedi — §3) ·
> `repo` (disk kanıtı) · `⚠️` ( doğrulanamayan iddia — yazılmaz ).

---

## 1. Doğrulama Protokolü

1. İddia yalnız URL_fetcher çıktısı + repo disk kanıtı ile yazılır (ZERO-HALLUCINATION §3).
2. Bulunamayan değer `UNKNOWN`, doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`.
3. Başarısız URL **silinmez** — §3'e "çelişki" olarak kaydedilir.
4. Yıldız sayısı / sürüm / tarih yalnız kaynak sayfasından (2026-10-12 anlık görüntüsü).

---

## 2. Claim·URL Matrisi (27 satır — 2026-10-12)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | ONNX Runtime; docs alt yapısı mevcut (AI runtime) | https://onnxruntime.ai/docs/ | 2026-10-12 | verified |
| 2 | PyTorch — açık kaynak ML framework | https://pytorch.org/ | 2026-10-12 | verified |
| 3 | TensorFlow — açık kaynak ML framework | https://www.tensorflow.org/ | 2026-10-12 | verified |
| 4 | TensorBoard — görselleştirme/izleme aracı | https://www.tensorflow.org/tensorboard/ | 2026-10-12 | verified |
| 5 | Whisper — açık kaynak çok dilli tanıma modeli | https://github.com/openai/whisper | 2026-10-12 | verified |
| 6 | librosa — Python ses analiz kütüphanesi | https://librosa.org/ | 2026-10-12 | verified |
| 7 | librosa 1.0.0 sürümü mevcut (doc/latest) | https://librosa.org/doc/latest/ | 2026-10-12 | verified |
| 8 | Essentia — müzik analizi / ses parmak izi | https://essentia.upf.edu/ | 2026-10-12 | verified |
| 9 | Essentia hazır modeller sayfası | https://essentia.upf.edu/models.html | 2026-10-12 | verified |
| 10 | Spotify açık kaynak yayınları (ses araçları) | https://spotify.github.io/ | 2026-10-12 | verified |
| 11 | Pedalboard — Spotify ses eklentisi/çerçevesi | https://github.com/spotify/pedalboard | 2026-10-12 | verified |
| 12 | GDPR — veri koruma mevzuatı resmi portalı | https://gdpr.eu/ | 2026-10-12 | verified |
| 13 | KVKK resmi portalı | https://www.kvkk.gov.tr/ | 2026-10-12 | verified |
| 14 | KVKK Üretken Yapay Zekâ rehberi | https://www.kvkk.gov.tr/Icerik/8547/Uretken-Yapay-Zeka-Rehberi | 2026-10-12 | verified |
| 15 | llama.cpp — ggml tabanlı LLM çalışma zamanı | https://github.com/ggml-org/llama.cpp | 2026-10-12 | verified |
| 16 | whisper.cpp — ggml tabanlı Whisper portu | https://github.com/ggml-org/whisper.cpp | 2026-10-12 | verified |
| 17 | Ollama — yerel model çalışma zamanı | https://ollama.com/ | 2026-10-12 | verified |
| 18 | vLLM — yüksek verimli LLM tahmin motoru | https://vllm.ai/ | 2026-10-12 | verified |
| 19 | vLLM doküman sitesi | https://docs.vllm.ai/ | 2026-10-12 | verified |
| 20 | recommenders — Microsoft önerme kitaplığı | https://github.com/recommenders-team/recommenders | 2026-10-12 | verified |
| 21 | recommenders 5 ana görev (popülerlik, içerik, hibrit, evaluasyon, kişiselleştirme) | https://recommenders-team.github.io/recommenders/intro.html | 2026-10-12 | verified |
| 22 | Libre recommender — Apache 2.0 öneri kütüphanesi | https://librecommender.readthedocs.io/en/stable/ | 2026-10-12 | verified |
| 23 | implicit — gizli matris çarpanlaştırma tabanlı öneri | https://github.com/benfred/implicit | 2026-10-12 | verified |
| 24 | Surprise — öneri algoritması değerlendirme kitaplığı | https://github.com/NicolasHug/Surprise | 2026-10-12 | verified |
| 25 | scikit-learn — ML kütüphanesi (ölçekleme/split/metric) | https://scikit-learn.org/stable/ | 2026-10-12 | verified |
| 26 | MLflow — ML yaşam döngüsü takibi | https://mlflow.org/ | 2026-10-12 | verified |
| 27 | Hugging Face Hub — model paketleme/depolama | https://huggingface.co/docs/hub/ | 2026-10-12 | verified |

**Ek doğrulananlar (kapsam dosyalarında kullanılan):**

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 28 | Evidently — ML izleme/drift (Apache 2.0) | https://www.evidentlyai.com/ | 2026-10-12 | verified |
| 29 | pytest — Python test çerçevesi | https://docs.pytest.org/en/stable/ | 2026-10-12 | verified |

**Toplam: 29 claim·URL satırı (≥25 zorunlu ✅).**

---

## 3. Başarısız / Düzeltlenen URL'ler (çelişki kaydı — silinmedi)

| # | Aday URL | Hata | Bulunan doğru adres | Durum |
|---|---|---|---|---|
| 1 | `mtg.github.io/essentia` | CRAWL_NOT_FOUND | https://essentia.upf.edu/ | failed → düzeltildi (§2-8) |
| 2 | `recommenders.microsoft.com` | CRAWL_UNKNOWN_ERROR | — | ⚠️ kullanılamaz |
| 3 | `www.md.kvkk.gov.tr` | CRAWL_UNKNOWN_ERROR | https://www.kvkk.gov.tr/ | failed → düzeltildi (§2-13) |
| 4 | `github.com/implicit-library/implicit` | CRAWL_NOT_FOUND | https://github.com/benfred/implicit | failed → düzeltildi (§2-23) |
| 5 | `github.com/openai/whai` (yazım hatası) | — | https://github.com/openai/whisper | typo düzeltildi (§2-5) |

---

## 4. Repo Kanıtları (disk — 2026-10-12 okundu)

| # | İddia | Kanıt (dosya·satır) | Durum |
|---|---|---|---|
| R1 | `AIEngine::getCollaborativeScores` boş döner (ALS yerine tutamaç) | `shared/src/AI/AIEngine.php` L209–218 | repo |
| R2 | `extractFeatures()` sabit değer döner (ör. energy=0.8) | `AIEngine.php` L315–330 | repo |
| R3 | Hibrit ağırlık kod gömülü 0.6 / 0.4 | `AIEngine.php` L65 | repo |
| R4 | Sabitler: MAX_RECOMMENDATIONS=20, MIN_SCORE=0.7, DIVERSITY_TARGET=0.30 | `AIEngine.php` L30–32 | repo |
| R5 | `AIWorkflow::recommendationWorkflow` K006.8 iş akışı L49–97 | `shared/src/AI/AIWorkflow.php` | repo |
| R6 | `AIOrchestrator::dispatchTask` agent iş akışı L71–100 (K016 sınırı) | `AIOrchestrator.php` | repo |
| R7 | `KnowledgeBase::ragQuery` L72–91 (K016-RAG) | `KnowledgeBase.php` | repo |

**Önceki tur kaydı (bu tur yeniden doğrulanmadı):** `d2l.ai`,
`docs.aws.amazon.com/personalize` — `recommendation-dogrulama.md` içindeki eski satırlar
**2026-10-12 tarihli bu turda yeniden çekilmedi** → o satırlar `⚠️ önceki tur` etiketlidir.

---

## 5. Açık Kalemler (status=verify-required nedeni)

1. §3 satır 2 (`recommenders.microsoft.com`) — erişilemedi, alternatif kaynak gerekli.
2. **Düzeltme (2026-10-12):** eski madde "`AIOrchestratorContract` / `AIRetryContract` / `AIRequestValidationContract` içerikleri okunmadı" geçersizdir — bu arayüzler **repoda yoktur** (grep 0). Gerçek contract seti 6 arayüzdür, tamamı okundu: `yapay-zeka-bilesenler.md` §4 + §9.
3. Python paket versiyonları (lockfile yok) → UNKNOWN.
4. K006 eğitim verisi kaynağı → UNKNOWN.

---

## 6. Kaynaklar

1. URL_fetcher 2026-10-12 çıktıları (§2, §3).
2. `shared/src/AI/*` — 2026-10-12 tam okuma (7 dosya, 1.988 satır).
3. `.ai/architecture/index.md` §6 satır 94 — okundu 2026-10-12.
4. `yapay-zeka-web-arastirma.md` §2 — sistematik tarama ayrıntısı.

---

`status: verify-required` · `validation: 2026-10-12 · 29 claim·URL + 5 başarısız kayıt + 7 repo kanıtı`
