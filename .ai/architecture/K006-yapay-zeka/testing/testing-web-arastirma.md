---
node_id: K006.9-testing-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/composer.json — phpunit ^10.5 · phpstan ^1.10 (require-dev)"
  - "shared/tests/**/*.php glob → 44 dosya, AI testi 0 (2026-10-12)"
validation: "2026-10-12 · 8 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "AI testlerinin nerede yaşayacağı (shared/tests/Unit/AI mı, ayrı paket mi) → UNKNOWN"
  - "Model/veri testleri için fixture stratejisi (sahte ses dosyası) → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.9-testing — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **test/değerlendirme**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Python tarafında test çerçevesi hangisi? | aday analiz zinciri (librosa vb.) kurulursa | §2.8 (32) |
| Metrik doğrulama nasıl yapılır? | K006.8 ile ortak | §2.3 (11) · §2.6 (25) |
| Üretim izleme testi neyi kapsar? | drift/veri kalitesi kapısı | §2.5 (10) |
| Deney tekrarabilirliği? | deterministik test | §2.2 (7 · 9) |
| Öneri değerlendirme testleri? | offline metrik doğrulama | §2.6 (20) |
| Çıkarım runtime doğrulaması? | model yükleme kapısı (ileride) | §2.1 (1) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.9 madde |
|---|---|---|---|
| 1 | https://docs.pytest.org/en/stable/ | pytest — Python test çerçevesi dokümanları | aday Python test altyapısı |
| 2 | https://scikit-learn.org/stable/ | scikit-learn — metrik/ölçekleme/veri ayrımı; sürüm **1.9.0** | metrik doğrulama |
| 3 | https://www.evidentlyai.com/ | Evidently — ML izleme/değerlendirme/drift; **Apache 2.0**, **7.500+★**, **40m+ indirme** | veri kalitesi kapısı |
| 4 | https://mlflow.org/ | MLflow — deney/yaşam döngüsü takibi | tekrarabilirlik |
| 5 | https://www.tensorflow.org/tensorboard/ | TensorBoard — eğitim/metrik görselleştirme | test raporu görselleştirme |
| 6 | https://surprise.readthedocs.io/en/stable/ | SVD, KNN, ölçüm metrikleri | offline öneri metrikleri |
| 7 | https://recommenders-team.github.io/recommenders/intro.html | 5 ana görev (değerlendirme dahil) | değerlendirme senaryoları |
| 8 | https://onnxruntime.ai/docs/ | ONNX Runtime — model çalışma zamanı ailesi | çıkarım doğrulama kapısı |

**Toplam K006.9 için: 8 benzersiz URL** (havuz §2.1 · §2.2 · §2.3 · §2.5 · §2.6 · §2.8).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.9'a etkisi |
|---|---|---|---|
| Test framework'ü kurulum + iddia ile gelir | 1 | pytest dokümanları | Python analiz zinciri için aday |
| Metrikler referans implementasyonla doğrulanır | 2 | sklearn metrik araç seti | el yazımı metrik karşılaştırma |
| Veri kalitesi ayrı bir kapıdır | 3 | drift/değerlendirme araçları | üretim sonrası test |
| Deneyler tekrarlanabilir olur | 4–5 | run takibi + metrik görselleştirme | determinizm kanıtı |
| Öneri offline test edilir | 6–7 | metrikler + değerlendirme görevi | offline_rmse vb. senaryolar |
| Model yükleme doğrulanır | 8 | çalışma zamanı dokümanları | runtime smoke testi (ileride) |

---

## §4 K006.9'a Uygulanabilir Kararlar (aday — kurulu değil)

| Aday | K006.9 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| pytest | Python testi | 1 | **kurulmadı** · PHP'de PHPUnit ^10.5 **var** |
| sklearn metrik referansı | metrik doğrulama | 2 | **kurulmadı** |
| Evidently | veri kalitesi/drift kapısı | 3 | **kurulmadı** |
| MLflow | tekrarabilirlik/iz | 4 | **kurulmadı** |
| TensorBoard | rapor görselleştirme | 5 | **kurulmadı** |
| Surprise metrikleri | offline öneri testi | 6 | **kurulmadı** |
| ONNX smoke test | çıkarım kapısı | 8 | **kurulmadı** |

> **Karar durumu:** bunlar **değil**, PHP tarafındaki gerçek test altyapısı **PHPUnit ^10.5 +
> PHPStan ^1.10**'dur (composer.json kanıtı); Python adayları yalnız analiz zinciri kurulursa
> gündeme gelir.

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| Test altyapısı kurulu | composer.json require-dev: **phpunit ^10.5**, **phpstan ^1.10** | ✅ eşleşme |
| Test dosyaları var | `shared/tests/**/*.php` glob → **44 dosya** (middleware/api/unit/repo…) | ✅ altyapı olgun |
| AI testi var mı | 44 dosyanın **hiçbiri** AIEngine/AIWorkflow/AIOrchestrator'u test etmiyor | ⚠️ **boşluk** |
| Karakteristik hedefler | `JSON_THROW_ON_ERROR` (L70 · L300) · `escapeshellarg` (L292) · null→varsayılan (L296–298) · boş skorlayıcı (L209–235) · placeholder (L319–326) · kontrolsüz callTool (L122–126) · beklenmeyen dispatch (L59) | ✅ test senaryosu zengini |
| Metrik doğrulama | repo'da ML metriği **0 satır** (grep → yalnız yanlış pozitifler) | ⚠️ boşluk |
| Python testi | pytest kuruludur **değil**; Python analiz zinciri de yok | ⚠️ boşluk |

**Sonuç:** K006.9'un test **altyapısı** vardır (PHPUnit/PHPStan + 44 dosyalık olgun takım), ama
**K006 katmanına ait tek test yoktur**. AIEngine/AIWorkflow'un kusurları (boş skorlayıcı,
placeholder, fırlatma, sessiz başarısızlık) **hiç test edilmemiştir**. Bu, K006.9'un ana
bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | Agent registry §25.2 "shared/tests: 22 dosya" (2026-09-27 ölçümü) der; bugünkü glob **44** | ✅ büyüme — sayımlar tarih damgalı, ikisi de doğru |
| C2 | 44 test dosyasında AI kapsamı **0** | ⚠️ **açık boşluk** (R-04) |
| C3 | `RateLimiterMiddlewareTest` fail-open testi var (L127) — AI katmanında benzer davranış testi **yok** | 🔶 kıyas kaydı |
| C4 | pytest/sklearn K006.9'un Python adayı; PHP çekirdeğiyle **çapraz dil** testi stratejisi belirsiz | ⚠️ VERIFICATION REQUIRED |
| C5 | Model yükleme testi (ONNX vb.) bugün anlamsız — model **0** | ⚠️ önkoşul eksik |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **8** |
| ✅ sayfa metniyle doğrulandı | 8 |
| Repo ile çapraz eşleşme | 1 (PHPUnit/PHPStan altyapısı — güçlü) |
| ⚠️ boşluk/çelişki | 4 (C2–C5) |
| UNKNOWN | 2 (test yeri · fixture stratejisi) |
| Kurulu aday (Python) | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.2 · §2.3 · §2.5 · §2.6 · §2.8 — 2026-10-12.
2. `shared/composer.json` — phpunit ^10.5 · phpstan ^1.10 (require-dev), 2026-10-12.
3. `shared/tests/**/*.php` glob → 44 dosya; AI testi 0, 2026-10-12.
4. `shared/src/AI/AIEngine.php` + `AIWorkflow.php` — test edilmemiş kusur yüzeyi (L59 · L70 · L122–126 · L209–235 · L292–326), 2026-10-12.
5. `.ai/architecture/K006-yapay-zeka/yapay-zeka-test-stratejisi.md` — kapılar G1–G5.

---

`status: verified` · `validation: 2026-10-12 · 8/8 URL havuzdan doğrulandı, 1 güçlü çapraz eşleşme, 4 işaretli boşluk, 0 kurulu Python aday`
