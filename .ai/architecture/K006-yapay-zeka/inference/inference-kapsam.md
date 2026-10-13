---
title: "inference — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# inference — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Modeli barındırıp çalıştırmak"** sorumluluğuna bağlar
(`model-management · inference · local-inference` birlikte) — tablo *tasarım yorumudur*.

Basitçe: eğitilmiş modeli **çalıştırıp çıktı üreten** taraf (serving). Repo'da durum net: motor çağrıları
**vardır** (`engine->getRecommendations`) ama bunlar **kural tabanlı PHP** değildir — gerçek bir model
inference runtime'ı (ör. ONNX oturumu) repo'da **görülmedi** (bkz. §3/7).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Modelin K006 ürünü olarak çalıştırılması (tek ML çağrı noktası) | Ajan çalışma zamanı / prompt orkestrasyonu | K016-AI (ai-master §2.1 · bagimliliklar §4) |
| 2 | Inference girdi/çıktı sözleşmesi (context → skor/liste) | Girdi verisinin saklanması | K007-data (bagimliliklar §2/1) |
| 3 | Inference'ın çalıştırılacağı süreçle arayüz | Sürecin kendisi | K012-services (bagimliliklar §2/3) |
| 4 | Inference çıktısının UI'a verilmesi (sözleşme) | Kartın çizimi | K011-application (ai-master §2.1) |
| 5 | Inference performans/girdi doğrulaması (sözleşme) | Model kalitesi metrikleri | evaluation (ai-master §3) |
| 6 | Bulut mu yerel mi kararı için kanıt toplama | **Yerel** çalışma ayrıntıları | local-inference (ai-master §3) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan
altyapısına **taşınmaz** — ajanlar inference'ı **çağırır**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez; kilit ADR-097 K4'tedir.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | inference ↔ local-inference (aynı işin iki yüzü mü?) | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 260/265 |
| A2 | inference ↔ model-management (çalıştıran mı, saklayan mı?) | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 259/260 |
| A3 | Gerçek model runtime'ı repo'da yok — ne zaman eklenecek? | ⚠️ IMPLEMENTED değil | `shared/src/AI/` (bu oturum taraması) |
| A4 | Model formatı (ONNX vb.) seçimi | ⚠️ **UNKNOWN** | ai-master §4 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 260'ta birebir geçer | `.ai/architecture/K006-yapay-zeka/inference/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `inference/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Inference çağrısının tek noktası workflow → engine | `shared/src/AI/AIWorkflow.php` satır 75 | ✅ dosya okundu |
| 4 | Çıktı sözleşmesi: `array{id, title, artist, score, reason}` | `shared/src/Contracts/AI/AIEngineInterface.php` satır 26–28 | ✅ dosya okundu |
| 5 | Motor 5 modülü PHP içinde implement eder (AIEngine sınıfı) | `shared/src/AI/AIEngine.php` satır 28–32 | ✅ dosya okundu |
| 6 | Task tipi `recommendation` orchestrator'a düşer | `shared/src/AI/AIOrchestrator.php` satır 41 | ✅ dosya okundu |
| 7 | `shared/src/AI/` altında ONNX/TFLite/torch model runtime'ı **görülmedi** (bu oturum grep: `onnx` 0 isabet) | `.ai/…/inference/inference-dogrulama.md` §1/9 | ✅ oturum taraması |
| 8 | K006 model formatı / runtime seçimi | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | inference ↔ local-inference sınırı ne? | ⚠️ VERIFICATION REQUIRED |
| R2 | Model formatı ve runtime (ONNX · PMML ·pickle/joblib · özel)? | ⚠️ ai-master §4 — **UNKNOWN** |
| R3 | Inference süreci K012'de mi, K006'da mı? | ⚠️ bagimliliklar §2/3 |
| R4 | Mevcut PHP motor mu, harici model mi (geçiş planı)? | ⚠️ **UNKNOWN** — kanıt yok |

**Kapanış:** status: draft kalır. R1–R4 kapanmadan bu dosyada **runtime/model seçimi** iddiası yazılmaz
(ai-master §4.1 · yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[inference-dogrulama]]
- [[inference-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
