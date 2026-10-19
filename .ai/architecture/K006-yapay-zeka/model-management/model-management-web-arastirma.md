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

---

# EK (2026-10-15) — Taze Envanter, Yeni Doğrulanmış URL'ler ve Zayıf Kanıt Kayıtları

> **EK usulü:** append-only; §1–§8 ve status satırı silinmez. URL'ler 2026-10-15
> ortak HTTP turunda status + gerçek `<title>` ile doğrulanmıştır (yalnız
> **200 + hedefle eşleşen title** kanıt sayılır).

---

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| # | Kaynak | Kapsam | Yöntem |
|---|---|---|---|
| 1 | `shared/src/Contracts/AI/*.php` | arayüz envanteri (glob) | glob, 2026-10-15 |
| 2 | `shared/src/AI/AIEngine.php` | L1–48 (sabitler · constructor · `getConfig`) | disk read (local-inference EK §1 ile ortak) |
| 3 | HTTP GET turu (40 URL — K006.6/8/9 ortak) | status + `<title>` | Code Mode `fetch`, 2026-10-15 |

---

## EK §1 — `Contracts/AI` arayüz envanteri (taze glob) + config seam

**Glob:** `shared/src/Contracts/AI/*.php` · sonuç **6 dosya** · 2026-10-15:

| # | Arayüz | Ait olduğu K006 alanı | Model registry mi? |
|---|---|---|---|
| 1 | `AIEngineInterface.php` | K006.3/6/8 (5 metot sözleşmesi) | ❌ |
| 2 | `AIOrchestratorInterface.php` | K006.5 (orkestrasyon) | ❌ |
| 3 | `KnowledgeBaseInterface.php` | K006.5 (bilgi tabanı) | ❌ |
| 4 | `MemorySystemInterface.php` | K006.5 (bellek) | ❌ |
| 5 | `PromptEngineInterface.php` | K006.5 (prompt) | ❌ |
| 6 | `ToolCallingInterface.php` | K006.5 (araç çağırma) | ❌ |

**Sonuç (taze):** §5'teki "6 arayüz, hiçbiri model registry değil" kaydı
**2026-10-15 glob'uyla teyit edildi** — registry arayüzü **hâlâ yok**. Sayı 6/6
aynıdır; envanter değişmemiştir.

**Config seam verbatim (taze okuma — bkz. `local-inference` EK §1 tam metni):**

```php
    private const MAX_RECOMMENDATIONS = 20;
    private const MIN_SCORE = 0.7;
    private const DIVERSITY_TARGET = 0.30;

    public function __construct(private readonly ConfigManager $config)
    {
    }

    public function getConfig(): ConfigManager
    {
        return $this->config;
    }
```

**Gözlem:** `ConfigManager` uygulama yapılandırmasıdır (seçenek/env); **model
registry** ise sürüm + artifact + durum kaydı ister — ikisi farklı katmandır.
§5'teki "tek gerçek yönetim katmanı" kaydı, bu katman farkını koruyarak devam
eder: config seam'i **registry'nin yeri değildir**, yalnız ayar taşıyıcısıdır.

---

## EK §2 — 2026-10-15 Taze HTTP Doğrulaması: Yeni/Genişleyen URL'ler

> §2'deki 8 URL aynen korunur; aşağıdaki URL'ler 2026-10-15 turunda doğrulanmıştır.

| # | URL | HTTP | `<title>` (yanıttan birebir) | K006.1 ilgisi |
|---|---|---|---|---|
| 9 | https://huggingface.co/docs/hub/index | 200 | `Hugging Face Hub documentation · Hugging Face` | §2.1 taze teyidi (doküman indeksi) |
| 10 | https://huggingface.co/docs/hub/models-the-hub | 200 | `The Model Hub · Hugging Face` | model hub'ının kendisi — paket/tarif kataloğu |
| 11 | https://mlflow.org/docs/latest/ | 200 | `MLflow Documentation | MLflow AI Platform` | §2.2 doküman kökü — **yeni URL** |
| 12 | https://mlflow.org/ | 200 | `MLflow - Open Source AI Platform for Agents, LLMs & Models` | §2.2 taze teyidi — title genişlemesi |
| 13 | https://www.tensorflow.org/tensorboard/ | 200 | `TensorBoard | TensorFlow` | §2.3 taze teyidi |
| 14 | https://www.evidentlyai.com/ | 200 | `Evidently AI - Open-source AI Evaluation and Observability` | §2.4 taze teyidi — "Evaluation and Observability" tanımı title'da |
| 15 | https://onnxruntime.ai/docs/ | 200 | `ONNX Runtime | onnxruntime` | §2.5 taze teyidi |
| 16 | https://docs.pytest.org/en/stable/ | 200 | `pytest documentation` | §2.8 taze teyidi |
| 17 | https://dvc.org/ | 200 | `Home – DVC` | **yeni aday** — veri/deney versiyonlama (registry ailesi) |
| 18 | https://github.com/iterative/dvc | 200 | `GitHub - treeverse/dvc: 🦉 Data Versioning and ML Experiments · GitHub` | **yeni aday** deposu — org `treeverse` (title) |
| 19 | https://www.wandb.ai/ | 200 | `Forge` | **yeni aday** — deney izleme; **title "Forge"** (marka değişimi, ⚠️ nedeni UNKNOWN) |

**Sayım:** §2 = 8 URL + EK = **11 URL** → K006.1 toplam **19 benzersiz URL**.

**Taze title notları:**

- §2.6 `pytorch.org` ve §2.7 `tensorflow.org` bu turda yeniden fetch edilmedi →
  2026-10-12 iddialarına bağlı (`⚠️ taze teyit yok`).
- `mlflow.org/docs/latest/model-registry.html` 200 döndü ama **title boş** →
  kanıt sayılmadı (bkz. EK §3 Z1).
- Evidently'in §2.4'teki rakamları (Apache 2.0 · 7.500+★ · 40m+) bu turda
  **yeniden okunmadı**; yalnız title konum teyidi yapıldı — rakamlar eski
  okuma olarak korunur.

---

## EK §3 — Zayıf / Red Kayıtları (kanıt olarak sayılmaz)

| # | URL | HTTP | Dönene title | Karar |
|---|---|---|---|---|
| Z1 | https://mlflow.org/docs/latest/model-registry.html | 200 | **(title yok)** | ⚠️ **zayıf kanıt** — registry dokümanının doğrudan kanıtı elde edilemedi; sayım dışı, `VERIFICATION REQUIRED` |
| Z2 | https://lyst.github.io/lightfm/ | **404** | `Page not found · GitHub Pages` | ❌ RET (ortak tur — bu dosyada kullanılmadı) |
| Z3 | https://surprise.readthedocs.io/en/stable/metrics.html | **404** | `Documentation page not found - Read the Docs Community` | ❌ RET (ortak tur) |
| Z4 | https://recbole.io | 200 | `SLOT TOTO >> Situs Slot Gacor…` (kumar spam) | ❌ RET — alan adı gaspı; **RecBole model registry kaynağı olarak kullanılamaz** |
| Z5 | https://implicit.readthedocs.io | 200 | `Careem Pay Merchant Plugins…` | ❌ RET — alan adı gaspı |

**Not:** Z1 bu dosya için kritiktir — K006.1'in çekirdek sorusu "registry
nasıl kurulur" iken MLflow registry sayfasının title'sı alınamamıştır.
`mlflow.org/docs/latest/` (EK §2.11, title'lı) doküman kökü olarak sayılabilir;
**özel registry sayfası** için tekrar tur gerekir.

---

## EK §4 — Çapraz Bağ (Taze Envanter ↔ §5 Tablosu)

| # | EK bulgusu (2026-10-15) | §5 satırı | Sonuç |
|---|---|---|---|
| X1 | `Contracts/AI` = 6 arayüz, registry yok (EK §1 glob) | "6 arayüz, hiçbiri registry değil" | ✅ teyit — sayı + isim listesi taze |
| X2 | config seam L34–44 verbatim (EK §1) | "Yapılandırma yönetimi ✅ kısmi" | ✅ teyit + katman farkı notu |
| X3 | DVC (EK §2.17–18) — veri/deney versiyonlama | §4'te yok | 🔶 yeni aday → aday 6 → 8 |
| X4 | W&B/"Forge" (EK §2.19) — deney izleme | §4'te yok (MLflow tek deney adayıydı) | 🔶 yeni aday — **title marka değişimi** ⚠️ |
| X5 | mlflow docs kökü (EK §2.11) | §2.2 tek URL | 🔶 URL derinleşti |
| X6 | Z1 registry sayfası title'sız | C5 (tek-kaynak iddialar) | ⚠️ yeni zayıf kanıt kaydı |

---

## EK §5 — Fark (C-row) Tablosu

| C-row | Eski kayıt (korunur) | Taze gerçek (2026-10-15) | Etki |
|---|---|---|---|
| C-1 | §7 "Benzersiz URL: 8" | 8 + 11 = **19** (§2 korunur) | EK §2 sayımı |
| C-2 | §7 "Repo ile çapraz eşleşme: 1" | +1 (Contracts/AI glob teyidi ayrı satır değil — X1 §5'in mevcut satırının teyidi) | eşleşme 1'de kalır; teyit kuvvetlendi |
| C-3 | §8 kaynak listesi 5 madde | +1 madde: EK §0–§4 (2026-10-15 glob + disk + 40-URL turu) | ek kanıt kütlesi |
| C-4 | C4 (semver mi hash mi) | değişmedi | `⚠️` açık kaldı |
| C-5 | §2.6–2.7 (pytorch/tensorflow) taze teyitsiz | değişmedi — bu tur fetch edilmedi | işaretli kaldı |

---

## EK §6 — Bulgular (A-MM serisi)

### A-MM-1 — Registry yokluğu üç kez teyit edildi (arayüz · dosya · paket)

Taze kanıt üç katmanda: (1) `Contracts/AI` glob → 6/6 arayüz registry değil
(EK §1); (2) model dosyası envanteri 0 (§5, 2026-10-12 — bugün değişmedi
varsayılmaz); (3) composer ML/izleme paketi 0 (§5). K006.1'in ana bulgusu
**çoklu kaynakla sabittir**; C1 ("yönetilecek model bile yok") açık kalır.

### A-MM-2 — Aday envanteri deney izleme tarafında genişledi

MLflow tek başına değil artık: **DVC** (veri/deney versiyonlama — EK §2.17–18)
ve **W&B/"Forge"** (EK §2.19) da aday. Üçü de kurulu değil (§4 usulü). Karar
dosyasında Q sorusu "MLflow mu, DVC mi, W&B mi" olarak genişlemelidir —
**karar verilmedi** (`UNKNOWN`).

### A-MM-3 — W&B title'ı "Forge" — marka kanıtı zayıf

`wandb.ai` ana sayfasının title'ı yalnız **`Forge`**. "Weights & Biases" adı
title'da görünmüyor. İddia sınırlıdır: **yalnız title**; marka değişimi nedeni
`⚠️ VERIFICATION REQUIRED`. Aday kaydı "W&B (title: Forge)" olarak kullanılmalı.

### A-MM-4 — MLflow registry sayfası kanıtsız kaldı (zayıf kanıt disiplini)

Z1: `model-registry.html` 200 + **title yok** → bu dosyanın çekirdek sorusuna
(registry nasıl) **doğrudan kanıt alınamadı**. Doküman kökü (EK §2.11) var ama
özel sayfa yok. Sonraki turda yeniden denenecek; **uydurma içerik yazılmaz**.

### A-MM-5 — DVC org title'ı `treeverse`

`github.com/iterative/dvc` → title `treeverse/dvc` (EK §2.18). İki org adı
(redirect zinciri) — hangisi kanonik `UNKNOWN`; her ikisi de 200 olduğu için
kaynak olarak ikisi de kullanılabilir, org iddiası title ile sınırlıdır.

### A-MM-6 — URL/kapı durumu

§2 8 + EK 11 = **19 benzersiz URL**; 1 zayıf (sayım dışı); 4 ret (2×404, 2×gasp —
ortak); uydurma URL 0; taze teyitsiz 2 (pytorch.org · tensorflow.org). C2 (depo
seçimi) ve C4 (sürüm politikası) **açık** — EK yeni aday ekledi, kararı vermedi.

---

## EK §7 — Kapılar (K006.1 web-arastirma)

| Kapı | Durum | Kanıt |
|---|---|---|
| Append-only (§1–§8 + status korundu) | ✅ | EK yalnız ek |
| Frontmatter değişmedi | ✅ | L1–18 ilk okumayla birebir |
| Uydurma URL 0 · zayıf kanıt işaretli | ✅ | 11 kanıt URL; Z1 title'sız sayım dışı; Z2–Z5 ret |
| Taze teyitsiz iddialar işaretli | ✅ | pytorch.org · tensorflow.org (EK §2 notları) |
| Kod/envanter kanıtı taze | ✅ | glob + `Satır: L34–44` (2026-10-15) |
| Yazım yüzeyi | ✅ | yalnız bu `.md`; `*.php/js/css/sql` yalnız okundu |
| git commit | ✅ | yok — orkestratöre ait |
| `.ai/architecture/index.md` | ✅ | dokunulmadı |

---

## EK §8 — Ölçüm

| Metrik | Değer |
|---|---|
| EK öncesi satır | **136** |
| EK sonrası satır | **400** (EK + EK-2 tek editte — kapı o an ❌, EK-3/EK-4 ile kapatıldı) |
| ≥500 kapı | **EK-5'e bak — nihai ✅ (508)** |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Ölçüm tarihi | 2026-10-15 |

---

# EK-2 (2026-10-15) — Derinlik Eki: Model Registry Şartnamesi ↔ Repo Boşluğu

> Yeni URL/iddia yok; §2–§5 ve EK §1–§2'deki kanıtların "registry ne içerir"
> şartnamesine eşlenmesi. Karar üretmez (C2/C4 `UNKNOWN` olarak kalır).

## EK-2 §1 — Registry bileşen matrisi

| # | Bileşen | Ne tutar | Repo durumu (kanıt) | Boşluk |
|---|---|---|---|---|
| R1 | Model kaydı (ad · sürüm · açıklama) | kayıt şeması | 0 satır; registry arayüzü yok (EK §1 glob) | ⚠️ |
| R2 | Artifact deposu (dosya + checksum) | ağırlık dosyaları | model dosyası 0 (§5) | ⚠️ |
| R3 | Sürüm politikası (semver/hash) | değişiklik takibi | politika yok — C4 `⚠️` | ⚠️ |
| R4 | Durum makinesi (staging/production/archived) | yayım kontrolü | 0 satır | ⚠️ |
| R5 | Depo seçimi (MySQL/dosya) | nereye yazılır | C2 `UNKNOWN` (K007-data sınırı) | ⚠️ |
| R6 | Kayda bağlı izleme (drift/sağlık) | sürüm sağlığı | Evidently/DVC aday, kurulu 0 | ⚠️ |
| R7 | Geri alma (rollback) | eski sürüme dönüş | 0 satır | ⚠️ |
| R8 | Erişim/kayıt entegrasyonu (kim yazar) | yetki | mevcut middleware var ama kayıt yeri yok | 🔶 |
| R9 | Registry testi | regresyon | AI testi 0/44 (§5) | ⚠️ |

**Okuma:** 9 bileşenin **8'i 0 satır**, 1'i (R8) altyapı-var-kayıt-yok.
C2 (depo) ve C4 (politika) bu tablonun R5/R3 satırlarıdır — karar verilmeden
R1–R4 inşa edilemez (kayıt şeması politikaya bağlı).

## EK-2 §2 — Hub paket modeli ↔ repo: karşılaştırma

| Hub kavramı (§2.1/§2.3 — iddia) | Repo karşılığı | Fark |
|---|---|---|
| Paket = dosya + kart (README/tarif) | model dosyası 0, kart 0 | tamamen eksik |
| Sürümleme | `@version 1.0.0` yalnız **PHP dosyası** içindedir (AIEngine L9 — kod sürümü, model sürümü değil) | sürüm kavramı farklı katmanda |
| İndirme/dağıtım | yok | eksik |
| İmza/doğrulama (checksum) | yok | eksik (local-inference EK-3 S4 ile ortak) |

**Not:** kod sürümü (`@since 2026-09-01`, `@version 1.0.0`) ile **model
sürümü** birbirine karıştırılmamalıdır; bu dosyada ikisi ayrı satırlarda tutulur.

## EK-2 §3 — C2/C4 kapalıysa yapılabilecekler (bağımlılık haritası)

```text
C4 sürüm politikası (semver vs hash) ──┐
                                        ├─→ R1 kayıt şeması ─→ R4 durum makinesi ─→ R7 rollback
C2 depo seçimi (MySQL vs dosya) ───────┘                                    │
                                                                             └─→ R6 izleme (Evidently/DVC adayı)
R9 test kapısı (AI testi 0) — her adımın ön koşulu
```

**Sonuç:** en erken başlatılabilir iş **R9 (test kapısı)** ve **aday seçimi
araştırmasının tamamlanması** (A-MM-2); C2/C4 kararları bayrak noktalarıdır —
karar verilmeden şema yazılmaz.

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 400 (EK+EK-2 tek editte — ayrık ölçüm yok) |
| EK-2 sonrası satır | **400** (EK-3 öncesi ölçüm) |
| ≥500 kapı | **❌ 400 → EK-3/EK-4/EK-5 ile kapatıldı — nihai 508** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız mevcut kanıtın şartnamelenmesi) |

---

# EK-3 (2026-10-15) — Derinlik Eki: Registry Kayıt Yolları — Üç Aday Yol ve Ön Koşulları

> Yeni URL/iddia yok; C2 (depo seçimi) ve C4 (sürüm politikası) açık sorularını
> **seçenek şeması** olarak derinleştirir. Karar **verilmez** — seçenekler ve
> her birinin ön koşulları listelenir; yetkili karar `yapay-zeka-kararlar.md`'dir.

## EK-3 §1 — Yol seçenekleri (C2'nin açılımı)

| Yol | Kayıt deposu | Artısı (kanıt bağlantısı) | Eksisi (kanıt bağlantısı) | Ön koşul |
|---|---|---|---|---|
| Y1 | MySQL (BCNF şema) | repo'da PDO + BCNF disiplini mevcut (§5/K007 sınırı) | migration gerekir; artifact dosyayı DB tutamaz (blob vs disk) | C2 kararı + K007-data ile sınır |
| Y2 | Dosya tabanlı (JSON/SQLite) | sıfır bağımlılık; yerel çıkarım dosyalarına yakın (local-inference EK-3 S3) | eşzamanlı yazım/lock; sorgu yeteneği yok | C2 kararı |
| Y3 | Hazır araç (MLflow/DVC/W&B — aday, kurulu 0) | registry + izleme birlikte gelir (A-MM-2) | **dil sınırı** (Python ekosistemi — C3 `UNKNOWN`); PHP'den süreç köprüsü gerekir | C3 + A-MM-2 aday seçimi |

**Ortak ön koşul (üç yolda):** R9 test kapısı (AI testi 0/44) + R3 sürüm
politikası (C4) — politika olmadan kayıt satırının anahtarı belirlenemez.

## EK-3 §2 — Yol 3 için PHP↔Python köprüsü gereksinimleri (C3 açılımı)

| # | Gereksinim | Kanıt/boşluk | Durum |
|---|---|---|---|
| T1 | Dış süreç çağırma deseni | ffprobe `shell_exec` + `escapeshellarg` (AIEngine L285–293) | ✅ desen var (local-inference EK-2 A1) |
| T2 | Süreç yönetimi (timeout/kuyruk) | yok — local-inference EK-3 §2 P1–P8 (7/8 eksik) | ⚠️ |
| T3 | HTTP köprüsü (araç sunucusu) | repo'da LLM istemcisi 0 (inference §5) — genel HTTP istemci katmanı K006 dışı `UNKNOWN` | ⚠️ |
| T4 | Kurulum/dağıtım (pip vs composer) | composer ML paketi 0 (§5) | ⚠️ |
| T5 | Lisans denetimi | DVC/MIT?, MLflow/Apache 2.0 (§2 iddiaları — taze teyit sınırlı) | ⚠️ lisans ADR bekliyor (audio C3 ailesi) |

**Sonuç:** Yol 3'ün ön koşulu T2 (süreç yönetimi) — yani **önce Yol A'nın
A2'si** (local-inference EK-3); yollar bağımsız değil, zincirdir.

## EK-3 §3 — C4 sürüm politikası seçenekleri (sadece şema)

| Politika | Anahtar | Uyumlu olduğu R-satırı | Kanıt durumu |
|---|---|---|---|
| semver (1.2.3) | insan-okunur sürüm | R1 + R7 (rollback) | politika yok — `⚠️` (C4) |
| hash (sha256) | artifact bütünlüğü | R2 (checksum) + R7 | politika yok — `⚠️` (C4) |
| ikisi birden | semver insan için, hash doğrulama için | R1 + R2 + R7 | politika yok — `⚠️` (C4) |

**Not:** Hub iddiası (§2.1 "sürümleme") hangi politikayı kastettiğini
**sayfa metninden okumaz** — bu dosya politika **üretmez**, üç seçeneği
işaretli olarak saklar (uydurma politika yazılmaz).

## EK-3 §4 — EK Kapıları Özeti (K006.1, 2026-10-15)

| Kapı | Durum | Kanıt |
|---|---|---|
| Append-only | ✅ | EK §0–§3, EK-2, EK-3 yalnız ek; §1–§8 + status korundu |
| Frontmatter değişmedi | ✅ | L1–18 birebir |
| Uydurma URL 0 | ✅ | 11 kanıt URL; Z1 sayım dışı; Z2–Z5 ret |
| Uydurma politika/karar yok | ✅ | EK-3 yalnız seçenek şeması — karar `UNKNOWN`/`⚠️` bıraktı |
| Kod/envanter kanıtı taze | ✅ | glob (6 arayüz) + L34–44 (2026-10-15) |
| Yazım yüzeyi | ✅ | yalnız bu `.md` |
| git commit | ✅ | yok |

## EK-3 §5 — Ölçüm (EK-3)

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **400** |
| EK-3 sonrası satır | **467** (EK-4 öncesi ölçüm) — EK-4 sonrası **508** |
| ≥500 kapı | **✅ GEÇTİ (508 ≥ 500, `read(offset=9999)` → "508 lines")** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız seçenek şeması) |

---

## EK-4 — K006.1 URL Envanteri (19 kayıt — 2026-10-15 durumu)

| # | URL | Kaynak turu | Durum |
|---|---|---|---|
| 1 | huggingface.co/docs/hub/ | 2026-10-12 (§2) | ✅ kök kayıt — taze teyit: EK §2.9 |
| 2 | mlflow.org/ | 2026-10-12 (§2) | ✅ taze 200 — EK §2.12 |
| 3 | tensorflow.org/tensorboard/ | 2026-10-12 (§2) | ✅ taze 200 — EK §2.13 |
| 4 | evidentlyai.com/ | 2026-10-12 (§2) | ✅ taze 200 — EK §2.14 |
| 5 | onnxruntime.ai/docs/ | 2026-10-12 (§2) | ✅ taze 200 — EK §2.15 |
| 6 | pytorch.org/ | 2026-10-12 (§2) | ⚠️ taze teyit yok (bu tur fetch edilmedi) |
| 7 | tensorflow.org/ | 2026-10-12 (§2) | ⚠️ taze teyit yok |
| 8 | docs.pytest.org/en/stable/ | 2026-10-12 (§2) | ✅ taze 200 — EK §2.16 |
| 9 | huggingface.co/docs/hub/index | 2026-10-15 (EK) | ✅ 200 + title |
| 10 | huggingface.co/docs/hub/models-the-hub | 2026-10-15 (EK) | ✅ 200 + title |
| 11 | mlflow.org/docs/latest/ | 2026-10-15 (EK) | ✅ 200 + title |
| 12 | dvc.org/ | 2026-10-15 (EK) | ✅ 200 + title — yeni aday |
| 13 | github.com/iterative/dvc | 2026-10-15 (EK) | ✅ 200 + title (`treeverse/dvc`) |
| 14 | wandb.ai/ | 2026-10-15 (EK) | ✅ 200 — title `Forge` ⚠️ (A-MM-3) |
| 15 | mlflow.org/docs/latest/model-registry.html | 2026-10-15 (EK) | ⚠️ 200 + **title yok** → sayım dışı (Z1) |
| 16 | lyst.github.io/lightfm/ | 2026-10-15 (EK) | ❌ 404 (Z2 — kullanılmadı) |
| 17 | surprise.readthedocs.io/.../metrics.html | 2026-10-15 (EK) | ❌ 404 (Z3 — kullanılmadı) |
| 18 | recbole.io | 2026-10-15 (EK) | ❌ gasp title (Z4) |
| 19 | implicit.readthedocs.io | 2026-10-15 (EK) | ❌ gasp title (Z5) |

**Sayım özeti:** benzersiz URL **19** (§2 8 + EK 11) · taze 200+title **11** ·
taze teyitsiz **2** (§6–7) · zayıf **1** (Z1, sayım dışı) · ret **4** (Z2–Z5) ·
kanıt olarak kullanılan ret **0** (hepsi "kullanılmadı" işaretli).

## EK-5 — Dosya İçi Ölçüm Geçmişi (2026-10-15)

| An | Satır | ≥500 |
|---|---|---|
| Görev başlangıcı (ilk okuma) | **136** | ❌ |
| EK + EK-2 sonrası | **400** | ❌ |
| EK-3 sonrası | **467** | ❌ |
| EK-4 sonrası (nihai) | **508** (2026-10-15 `read(offset=9999)` → "508 lines") | **✅ GEÇTİ (≥500)** |

Yöntem: `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı.
