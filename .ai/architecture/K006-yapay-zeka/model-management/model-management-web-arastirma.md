---
node_id: K006.1-model-management-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/** grep onnx|gguf|tensorflow|torch → 0 eşleşme (2026-10-12)"
  - "shared/src/AI/AIEngine.php L34 · L41 (ConfigManager tabanlı yapılandırma)"
validation: "2026-10-12 · 8 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Model kaydı (registry) hangi depoya yazılacak (MySQL/SQLite/dosya) → UNKNOWN"
  - "Model sürümleme politikası (semver mı, hash mı) → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.1-model-management — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **model yönetimi**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Model paketleme/dağıtma/sürümleme nasıl yapılır? | K006.1'in çekirdek sorusu — repo'da **0 satır** | §2.2 (8) |
| Yaşam döngüsü/izleme araçları hangileri? | kaynak→eğitim→çıkarma zinciri | §2.2 (7 · 9) |
| Model doğrulama/drift neyi kapsar? | sürüm sağlığı ölçümü | §2.5 (10) |
| Model çalışma zamanı hangi formatları tanır? | format kararı (ONNX ailesi) | §2.1 (1) |
| Eğitim framework'leri ne sunar? | eğitim→kayıt arayüzü | §2.2 (2–3) |
| Test altyapısı? | K006.9 ile ortak | §2.8 (32) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.1 madde |
|---|---|---|---|
| 1 | https://huggingface.co/docs/hub/ | Hub — model **paketleme, dağıtma, sürümleme** dokümanları | paket/sürüm modeli |
| 2 | https://mlflow.org/ | MLflow — ML **deneyi/yaşam döngüsü** takibi | kayıt/izleme adayı |
| 3 | https://www.tensorflow.org/tensorboard/ | TensorBoard — eğitim/metrik görselleştirme | izleme arayüzü |
| 4 | https://www.evidentlyai.com/ | Evidently — ML izleme, değerlendirme, drift; **Apache 2.0**, **7.500+★**, **40m+ indirme** | sürüm sağlığı/drift |
| 5 | https://onnxruntime.ai/docs/ | ONNX Runtime — model çalışma zamanı ailesi | taşınabilir format |
| 6 | https://pytorch.org/ | PyTorch — açık kaynak ML framework ana sayfası | eğitim→dışa aktarma |
| 7 | https://www.tensorflow.org/ | TensorFlow — açık kaynak ML platform ana sayfası | eğitim→dışa aktarma |
| 8 | https://docs.pytest.org/en/stable/ | pytest dokümanları | aday test altyapısı |

**Toplam K006.1 için: 8 benzersiz URL** (havuz §2.1 · §2.2 · §2.5 · §2.8).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.1'e etkisi |
|---|---|---|---|
| Model bir pakettir: sürüm + dosya + kart | 1 | Hub dokümanları paketleme/dağıtım/sürümleme anlatır | kayıt şeması fikri |
| Deneyler takip edilir | 2 | MLflow yaşam döngüsü takibi | parametre/sonuç kaydı |
| Model sağlığı ölçülür | 4 | drift/değerlendirme araç seti | sürüm sonrası izleme |
| Format taşınabilirliği ONNX ile olur | 5 | çalışma zamanı ailesi | format standardı adayı |
| Dışa aktarma eğitim framework'ünden gelir | 6–7 | PyTorch/TF ana sayfaları | kaynak→dönüşüm zinciri |

---

## §4 K006.1'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.1 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| Hub paket modeli | model kaydı: dosya + sürüm + kart | 1 | **kurulmadı** |
| MLflow | deney/yaşam döngüsü takibi | 2 | **kurulmadı** (composer grep → 0) |
| TensorBoard | metrik görselleştirme | 3 | **kurulmadı** |
| Evidently | drift/sağlık izleme | 4 | **kurulmadı** (composer grep → 0) |
| ONNX formatı | taşınabilir model paketi | 5 | **kurulmadı** (grep → 0) |
| PyTorch/TF | eğitim→export | 6–7 | **kurulmadı** (grep → 0) |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| Hub sürümleme modeli | repo'da model dosyası **0** (.onnx/.gguf/.pt/.h5 yok — 2026-10-12 kök tarama) | ⚠️ boşluk |
| MLflow kayıt tutar | composer.json'da mlflow **yok**; grep → 0 | ⚠️ boşluk |
| Drift izlenir | grep `drift|evidently` → **0** | ⚠️ boşluk |
| Yapılandırma yönetimi | `AIEngine::__construct(ConfigManager)` L34 · `getConfig` L41 — **VAR** (tek gerçek "yönetim" katmanı: uygulama yapılandırması) | ✅ kısmi eşleşme |
| Sürüm/registry interface | `shared/src/Contracts/AI/` — 6 arayüz, **hiçbiri model registry değil** | ⚠️ boşluk |
| Paket envanteri | `shared/composer.json` — ML/izleme paketi yok (grep `evidently|mlflow|onnx` → 0) | ✅ negatif kanıt |

**Sonuç:** K006.1'de model yönetimi **tamamen 0 satırdır**; tek mevcut "yönetim" katmanı
uygulama yapılandırmasıdır (ConfigManager). Model dosyası, sürüm, registry, drift — dördü de
yoktur. Bu, K006.1'in ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | Domain "model-management" der ama repo'da **yönetilecek model bile yok** | ⚠️ **açık boşluk** (riskler R-02) |
| C2 | Model kaydı hangi depoya yazılacak (MySQL BCNF mi, dosya mı) → K007-data ile sınır | ⚠️ VERIFICATION REQUIRED |
| C3 | MLflow/TensorBoard Python ekosistemidir; K006 PHP | 🔶 dil sınırı — UNKNOWN |
| C4 | Sürümleme: semver mi, hash mi → politika yok | ⚠️ VERIFICATION REQUIRED |
| C5 | Evidently/MLflow iddiaları sayfa metnidir; bağımsız denetim **değil** | ⚠️ tek-kaynak iddia |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **8** |
| ✅ sayfa metniyle doğrulandı | 8 |
| Repo ile çapraz eşleşme | 1 (ConfigManager seam'i — kısmi) |
| ⚠️ boşluk/çelişki | 5 (C1–C5) |
| UNKNOWN | 2 (depo seçimi · dil sınırı) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.2 · §2.5 · §2.8 — 2026-10-12.
2. `shared/src/**` grep (`onnx|gguf|tensorflow|torch|mlflow|drift`) → 0 eşleşme, 2026-10-12.
3. `shared/src/AI/AIEngine.php` L34 · L41 — 2026-10-12 disk okuma.
4. `shared/composer.json` — ML/izleme paketi yok, 2026-10-12.
5. `shared/src/Contracts/AI/` glob → 6 interface, registry interface'i yok, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 8/8 URL havuzdan doğrulandı, 1 kısmi çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`
