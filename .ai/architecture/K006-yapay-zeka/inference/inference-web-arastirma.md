---
node_id: K006.2-inference-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L49–133 · L161–205 (disk okuma 2026-10-12)"
  - "shared/src/** grep onnx|pytorch|tensorflow|vllm|torch → 0 eşleşme (2026-10-12)"
validation: "2026-10-12 · 8 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Üretim çıkarım runtime'ı (ONNX vs vLLM vs yerel kütüphane) → UNKNOWN (karar yok)"
  - "vLLM'in LLM odaklı sunumu K006'daki hangi tahmin yüzeyine binecek → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.2-inference — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **çıkarma/inference**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Model hangi runtime'da çalıştırılır? | K006.2'nin asıl sorusu — repo'da runtime **0 satır** | §2.1 (1) · §2.2 (4–5) |
| Framework adayları neler? | eğitim-sonrası çıkarım tarafı | §2.2 (2–3) |
| Ses/işitsel model çıkarımı mümkün mü? | K006.5/K006.6 ile sınır | §2.1 (6) · §2.7 (26) |
| Taşınabilir/on-device seçenek? | yerel çıkarım (K006.7 ile ortak) | §2.7 (26) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.2 madde |
|---|---|---|---|
| 1 | https://onnxruntime.ai/docs/ | ONNX Runtime — model çalışma zamanı ailesi dokümanları | taşınabilir çıkarım adayı |
| 2 | https://pytorch.org/ | PyTorch — açık kaynak ML framework ana sayfası | framework adayı |
| 3 | https://www.tensorflow.org/ | TensorFlow — açık kaynak ML platform ana sayfası | framework adayı |
| 4 | https://vllm.ai/ | vLLM — yüksek verimli LLM tahmin/servis motoru | LLM sunum adayı |
| 5 | https://docs.vllm.ai/ | vLLM doküman sitesi (sürüm dokümanları) | sürüm kanıtı |
| 6 | https://github.com/openai/whisper | Whisper — açık kaynak çok dilli konuşma tanıma; **110.308★**, **MIT** | ses modeli çıkarım adayı |
| 7 | https://huggingface.co/docs/hub/ | Hugging Face Hub — model paketleme, dağıtma, sürümleme dokümanları | model kaynağı |
| 8 | https://github.com/ggml-org/llama.cpp | llama.cpp — **130.787★**, **MIT** | yerel/ONNX-dışı çıkarım adayı |

**Toplam K006.2 için: 8 benzersiz URL** (havuz §2.1 · §2.2 · §2.7).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.2'e etkisi |
|---|---|---|---|
| Çıkarma runtime'ı ayrı bir katmandır | 1 | ONNX Runtime dokümanları modeli çalışma zamanında çalıştırır | runtime soyutlama katmanı fikri |
| Framework ≠ runtime | 2–3 | PyTorch/TensorFlow ana sayfaları platformdur | eğitim/çıkarma ayrımı |
| LLM sunumu yüksek verim ister | 4–5 | vLLM kendini "high-throughput LLM serving" olarak tanımlar | K006'da LLM yüzeyi varsa aday |
| Konuşma tanıma modeli doğrudan çalıştırılabilir | 6 | Whisper MIT; 110.308★ | ses→metin tahmini adayı |
| Model Hub'dan sürümlemeyle inilir | 7 | paketleme/dağıtma/sürümleme dokümanları | model tedarik zinciri |
| CPU'da yerel çıkarım mümkündür | 8 | llama.cpp yerel çıkarım motoru; MIT | sunucu-yerel çıkarım adayı |

---

## §4 K006.2'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.2 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| ONNX Runtime | herhangi bir modelin PHP-dışı çıkarımı | 1 | **kurulmadı** (grep → 0) |
| PyTorch / TensorFlow | eğitim + çıkarım | 2–3 | **kurulmadı** (grep → 0) |
| vLLM | LLM sunum katmanı | 4–5 | **kurulmadı** (grep → 0) |
| Whisper | dosya/içerik konuşma çıkarımı | 6 | **kurulmadı** |
| Hugging Face Hub | model tedariki/sürümleme | 7 | **kurulmadı** |
| llama.cpp | yerel çıkarım | 8 | **kurulmadı** (K006.7 ile ortak) |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| ONNX Runtime model çalıştırır | `shared/src/**` grep `onnx` → **0 eşleşme**; composer.json'da paket yok | ⚠️ boşluk |
| PyTorch/TF çıkarım yapar | grep `pytorch|tensorflow|torch` → **0** | ⚠️ boşluk |
| vLLM LLM sunar | grep `vllm` → **0**; repo'da HTTP LLM istemcisi de **0** (2026-10-12 ölçüm) | ⚠️ boşluk |
| Whisper transkripler | grep `whisper|transcri` → **0** | ⚠️ boşluk |
| Tahmin (prediction) kodu | `AIEngine::predictFault` **L161** (public) — **kural tabanlı** tahmin, model değil | ✅ var / 🔶 model değil |
| EQ/odak hesabı | `optimizeEQ` L111–133 (31 bant) + `calculateRoomCorrection` L347 + `calculateBandQ` L368 — **deterministik hesap** | ✅ var / 🔶 model değil |
| Tavsiye skorlama | `getRecommendations` L49–77 — sabit ağırlıklı hibrit, **eğitim yok** | 🔶 "çıkarım" değil |

**Sonuç:** K006.2'de "model çıkarımı" anlamında **hiçbir bileşen yoktur**. Repo'daki "tahmin"
yüzeyleri (`predictFault` · `optimizeEQ` · skorlama) **kural/deterministik hesaptır** —
model ağırlığı yükleyen hiçbir kod satırı bulunmaz (grep kanıtı §5). Bu, K006.2'nin ana
bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `github.com/openai/whai` (typo) → `github.com/openai/whisper` düzeltildi | ✅ düzeltildi (havuz §6.2) |
| C2 | Domain "inference" der; repo'da model/ağırlık yükleme **0 satır** | ⚠️ **açık boşluk** (riskler R-02) |
| C3 | vLLM/llama.cpp C/C++ ağırlıklı; K006 PHP → süreç/dil sınırı | 🔶 UNKNOWN |
| C4 | `predictFault` adında "tahmin" var ama model değil — yanlış anlaşılabilir | ⚠️ terminoloji tuzağı (K006.8-evrensel-dil) |
| C5 | Hangi tahmin yüzeyinin gerçek modele taşınacağı (EQ vs fault vs skor) | ⚠️ VERIFICATION REQUIRED |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **8** |
| ✅ sayfa metniyle doğrulandı | 8 |
| Repo ile çapraz eşleşme | 3 (predictFault · optimizeEQ · skorlama — hepsi "model değil" kaydıyla) |
| ⚠️ boşluk/çelişki | 4 (C2–C5) |
| UNKNOWN | 2 (dil sınırı · üretim runtime seçimi) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.2 · §2.7 — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L49–133 · L161–205 · L347–382 — 2026-10-12 disk okuma.
3. `shared/src/**` grep (`onnx|pytorch|tensorflow|vllm|torch|whisper|transcri`) → 0 eşleşme, 2026-10-12.
4. `shared/composer.json` — ML runtime paketi yok, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 8/8 URL havuzdan doğrulandı, 3 çapraz kayıt (tamamı "model değil"), 4 işaretli boşluk, 0 kurulu aday`
