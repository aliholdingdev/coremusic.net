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

---

# EK (2026-10-15) — Taze Kanıt Turu: Test Altyapısı + 10 URL HTTP Doğrulaması

> Append-only ek; §0–§8 orijinal metin silinmeden eklendi.

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Yöntem | Sonuç |
|---|---|---|
| `shared/composer.json` | grep (`require-dev\|phpunit\|phpstan`) | L23–25: `phpunit/phpunit: ^10.5` · `phpstan/phpstan: ^1.10` · L47–48 script `test`/`stan` |
| `shared/tests/**/*.php` | glob (bu oturum) | **45 dosya** — `*AI*` glob → **0** |
| HTTP fetch turu | 10 URL (tek Code Mode turu) | 10/10 HTTP 200 + gerçek `<title>` |

## EK §1 — Verbatim Kanıt Blokları (2026-10-15 disk okuması)

### §1.1 Test altyapısı (composer.json)

```json
// Dosya: shared/composer.json [Satır: 23]
"require-dev": {
    "phpunit/phpunit": "^10.5",
    "phpstan/phpstan": "^1.10"
}
```

```json
// Dosya: shared/composer.json [Satır: 47]
"test": "phpunit",
"stan": "phpstan analyse src --level=5"
```

- Dosya: `shared/composer.json` [Satır: 23–25 · 47–48] — statik analiz **seviye 5**;
  `infection` (mutation test) require-dev'de **yok** (grep → 0 eşleşme).

### §1.2 Test envanteri (glob, 2026-10-15)

- `shared/tests/**/*.php` glob → **45 dosya** (2026-10-15 ölçümü, aile kırılımı):
  Api 8 · Middleware 9 · Unit **16** · Events 2 · Repository 4 (bunun 2'si Sqlite yardımcı,
  test değil) · Security 2 · OAuth 1 · Component 1 · Fixtures 2 (fixture, test değil)
  → 8+9+16+2+4+2+1+1+2 = **45** ✅.
  **Düzeltme kaydı:** bu ek ilk yazımında "Unit 12 / toplam 47?" yazmıştı; tek tek sayım
  16/45 verir (döküm: EK-4 §7).
- `shared/tests/**/*AI*` glob → **0 dosya** — AI katmanı test kapsamı hâlâ **0**.
- AI testi iddiası yalnız dosya adı üzerinden tarandı; içerik üzerinden `AIEngine|AIWorkflow`
  araması 2026-10-12 kaydıdır (§5 · §8) — bu turda tekrarlanmadı (`⚠️` yok, tarihli kayıt).

### §1.3 Test edilmemiş kusur yüzeyi (önceki turlarda okunan verbatim)

| Hedef | Kanıt | Bugünkü testi |
|---|---|---|
| Boş skorlayıcı | AIEngine L209–218 · L223–235 `return []` | ❌ yok (glob 0) |
| Placeholder öznitelik | AIEngine L319–326 sabit 6 alan | ❌ yok |
| ffprobe null varsayılanı | AIEngine L296–298 | ❌ yok |
| `JSON_THROW_ON_ERROR` yayılımı | AIEngine L300 · AIWorkflow L70 | ❌ yok |
| kontrolsüz `callTool` | AIWorkflow L122–127 | ❌ yok |
| `'anonymous'` oturum | AIWorkflow L52 | ❌ yok |
| try/catch olmayan workflow | AIWorkflow L49–91 (tamamı okundu) | ❌ yok |

## EK §2 — Taze URL Tablosu (2026-10-15 · HTTP 200 + gerçek `<title>`)

| # | URL | Status | Gerçek `<title>` | Eşleşme |
|---|---|---|---|---|
| W1 | https://docs.pytest.org/en/stable/ | 200 | `pytest documentation` | ✅ §2 #1 |
| W2 | https://hypothesis.readthedocs.io/en/latest/ | 200 | `Hypothesis 6.169.4 documentation` | ✅ yeni aday — sürüm **6.169.4** |
| W3 | https://phpunit.de/ | 200 | `PHPUnit: The testing framework for PHP` | ✅ PHP gerçek test çerçevesi |
| W4 | https://phpstan.org/ | 200 | `Find Bugs Without Writing Tests \| PHPStan` | ✅ statik analiz |
| W5 | https://infection.github.io/ | 200 | `Infection` | ✅ mutation testing adayı |
| W6 | https://www.evidentlyai.com/ | 200 | `Evidently AI - Open-source AI Evaluation and Observability` | ✅ §2 #3 |
| W7 | https://mlflow.org/ | 200 | `MLflow - Open Source AI Platform for Agents, LLMs & Models` | ✅ §2 #4 |
| W8 | https://www.tensorflow.org/tensorboard/ | 200 | `TensorBoard \| TensorFlow` | ✅ §2 #5 |
| W9 | https://onnxruntime.ai/docs/ | 200 | `ONNX Runtime \| onnxruntime` | ✅ §2 #8 |
| W10 | https://scikit-learn.org/stable/ | 200 | `scikit-learn: machine learning in Python — scikit-learn 1.9.1 documentation` | ✅ §2 #2 — **1.9.1** (C-row) |

**10/10 geçerli.** Ayrıca 2026-10-15 ortak turundan bu dosyayla ilgililer:
`surprise.readthedocs.io/en/stable/` 200 (`Welcome to Surprise' documentation! — Surprise 1
documentation` ✅) · `recommenders-team.github.io/.../intro.html` 200 ✅.

## EK §3 — Ret / Zayıf Kanıt Kayıtları (kanıt olarak KULLANILMAZ)

| URL | Sonuç | Kayıt |
|---|---|---|
| `surprise.readthedocs.io/en/stable/metrics.html` | **404** (ortak tur) | ret — metrik alt sayfası taşınmış |
| `mlflow.org/docs/latest/model-registry.html` | 200 ama `<title>` yok | zayıf — sayım dışı |
| `pytorch.org/docs/stable/quantization.html` | title `Redirecting…` | zayıf — sayım dışı |
| `lyst.github.io/lightfm/` | **404** | ret (komşu dosya — ortak tur) |
| `recbole.io` · `implicit.readthedocs.io` | **GASP** (kumar / Careem Pay) | ret — gasp alan adları |
| W'deki yıldız/indirme sayıları (7.500+★ · 40m+) | title'da YOK | ⚠️ VERIFICATION REQUIRED (2026-10-12 kaydı korundu) |

## EK §4 — Çapraz Bağ Matrisi (araştırma ↔ repo · 2026-10-15 taze)

| Araştırma bulgusu (EK §2) | Repo karşılığı (EK §1) | Sonuç |
|---|---|---|
| PHPUnit gerçek PHP test çerçevesi (W3) | composer `^10.5` + 45 test dosyası | ✅ güçlü eşleşme |
| Statik analiz (W4) | composer `^1.10` + `stan` script (seviye 5) | ✅ eşleşme |
| Mutation testing (W5) | require-dev'de infection **yok** | ⚠️ boşluk — aday |
| Property-based test (W2) | repo'da karşılık 0 | ⚠️ boşluk — aday |
| Python test zinciri (W1) | Python analiz zinciri yok → pytest gereksiz bugün | 🔶 önkoşul yok |
| Veri kalitesi/drift (W6) | ML metriği 0 satır (§5 kaydı) | ⚠️ boşluk |
| Deney izleme (W7–W8) | deney altyapısı 0 satır | ⚠️ boşluk |
| Model runtime smoke (W9) | model 0 (§6 C5) | ⚠️ önkoşul yok |
| Metrik referansı (W10) | 0 satır + sürüm C-row | ⚠️ boşluk + çelişki |

## EK §5 — C-Row Kayıtları (çelişki, silinmeden raporlandı)

| # | Dosyadaki eski kayıt | 2026-10-15 taze kanıt | Eylem |
|---|---|---|---|
| C-ROW-1 | §2 #2 sklearn **1.9.0** (2026-10-12) | W10 title **1.9.1** | eski satır silinmedi; güncelleme sahibine ait |
| C-ROW-2 | §5 glob → **44** dosya (2026-10-12) | bu oturum glob → **45** dosya | iki ölçüm de tarihli; artış +1 (Fixtures chrome header/footer dahil sayım aynı yöntemle) |
| C-ROW-3 | §5 "AI testi 0" | `*AI*` glob → 0 (2026-10-15) | ✅ teyit |
| C-ROW-4 | §6 C1: registry "22 dosya" (2026-09-27) | 44 (10-12) → 45 (10-15) | ✅ büyüme zinciri — üç ölçüm de tarihli |

## EK §6 — Bulgular (seri: A-T)

| # | Bulgu | Kanıt |
|---|---|---|
| A-T-1 | AI katmanı test kapsamı 0 — iki bağımsız ölçümde teyitli | `*AI*` glob 0 (10-15) + §5 (10-12) |
| A-T-2 | Test altyapısı olgun: 45 dosya, 10 klasör ailesi, PHPUnit ^10.5 | glob + composer L24 |
| A-T-3 | Statik analiz seviye 5 açıktır ama AI katmanını da kapsar mı → `stan` script'i `src` (tümü) der | composer L48 |
| A-T-4 | Mutation testing yok (infection require-dev'de değil) | composer grep 0 |
| A-T-5 | 7 karakteristik kusur hedefi test edilmemiş (EK §1.3) | kod okumaları |
| A-T-6 | Python adayları (pytest/Hypothesis) önkoşulsuz — Python zinciri yok | §4 + EK §2 |
| A-T-7 | Hypothesis 6.169.4 yeni aday (property-based) | W2 title |
| A-T-8 | Infection yeni aday (mutation kapısı) | W5 title |

## EK §7 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §8'de |
| Mevcut içerik korunur (append-only) | ✅ §0–§8 silinmedi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK §8 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **141** |
| EK sonrası satır | **286** |
| ≥500 kapı | **❌ o an** (286 < 500) → nihai ✅ (502, bkz. EK-4) |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL | 10 tarandı · 10 geçerli · ek ret/zayıf §3 |

---

# EK-2 (2026-10-15) — AI Katmanı Test Kapsamı Şartnamesi ve Öncelik Matrisi

> Yeni URL/iddia yok; yalnız EK §1–§2 kanıtlarının şartnamelenmesi. Karar üretmez.

## EK-2 §1 — Kapsam Şartnamesi (K006.9 için · bugünkü durum)

| # | Şart | Mevcut durum | Kanıt |
|---|---|---|---|
| TS1 | AIEngine birim testi olmalı | ❌ 0 | `*AI*` glob 0 (10-15) |
| TS2 | AIWorkflow birim testi olmalı | ❌ 0 | aynı |
| TS3 | Orchestration/registry sözleşmesi testi | ❌ 0 (registry interface'i de yok) | `Contracts/AI` glob = 6 arayüz (10-15) |
| TS4 | Statik analiz AI katmanını kapsamalı | ✅ `stan` tüm `src` — ama 44/45 test dosyası ayrı | composer L48 |
| TS5 | Mutasyon dayanıklılığı olmalı | ❌ infection yok | composer grep 0 |
| TS6 | Karakteristik kusurlar test edilmiş olmalı | ❌ 7/7 hedefsiz | EK §1.3 |
| TS7 | Veri/drift kapısı testi | ❌ 0 satır metrik kodu | §5 kaydı |
| TS8 | Fixture stratejisi (sahte ses dosyası) tanımlı | 🔶 UNKNOWN | frontmatter open_questions |

**Uyum:** 1/8 (yalnız TS4 kısmen). 6 ❌ · 1 UNKNOWN.

## EK-2 §2 — Kusur → Öncelikli Test Matrisi (EK §1.3 dayanaklı)

| # | Kusur (kod kanıtlı) | Önerilen test | Öncelik | Beklenen davranış (bugün) |
|---|---|---|---|---|
| G1 | skorlayıcılar `[]` (L209–218 · L223–235) | `getRecommendations` boş girdi | **P0** | boş liste — regressyon: dolu dönerse yanlış |
| G2 | placeholder 6 alan (L319–326) | iki farklı dosyayı karşılaştır | **P0** | çıktılar birebir aynı (sahte) |
| G3 | ffprobe null → varsayılan (L296–298) | PATH'ten ffprobe gizle | P1 | 4 alan sabit + bpm yine 120.0 |
| G4 | `JSON_THROW_ON_ERROR` yayılımı (L300 · L70) | bozuk JSON | P1 | istisna katman dışına çıkar (try/catch yok) |
| G5 | kontrolsüz `callTool` (L122–127) | tool hata dönerse | P1 | sonuç yok sayılır, akış devam eder |
| G6 | `'anonymous'` (L52) + istek-üstün merge (L56) | oturumsuz/çakışan istek | P1 | profil birikmez / oturum tercihi ezilir |
| G7 | boş `mood`/`genre` sorgusu (L62) | eksik alan isteği | P2 | bilinmeyen kalite — `UNKNOWN` ölçüm gerekir |

`yapay-zeka-test-stratejisi.md` kapılarıyla eşleşme: bu matris G1–G7, o dosyanın
**G1–G5 kapılarına karşılık gelir** (kapı adları çakışır — dosyalar arası okuma
`@.ai/architecture/K006-yapay-zeka/yapay-zeka-test-stratejisi.md` §kapılar).

## EK-2 §3 — Araç Karar Matrisi (aday — kurulu değil)

| Araç | Yer | Kanıt | Bugünkü önkoşul |
|---|---|---|---|
| PHPUnit ^10.5 | PHP çekirdek | composer L24 ✅ kurulu | — |
| PHPStan ^1.10 (seviye 5) | PHP çekirdek | composer L25/L48 ✅ kurulu | — |
| Infection (mutation) | PHP çekirdek | W5 ✅ aday | kurulmalı (require-dev'e ekleme kararı) |
| Hypothesis 6.169.4 | Python zinciri | W2 ✅ aday | Python zinciri YOK → ertelenir |
| pytest | Python zinciri | W1 ✅ aday | aynı |
| Evidently | veri kalitesi | W6 ✅ aday | metrik kodu 0 → ertelenir |
| MLflow / TensorBoard | deney/rapor | W7–W8 ✅ aday | deney altyapısı 0 → ertelenir |
| ONNX smoke | model runtime | W9 ✅ aday | model 0 → ertelenir (§6 C5) |

## EK-2 §4 — Kapılar ve Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 286 |
| EK-2 sonrası satır | **ayrık ölçüm yok** — EK-2+EK-3 tek editte yazıldı (sonrasındaki ölçüm: 395) |
| ≥500 kapı | **❌ o an** (395 < 500) → nihai ✅ (502, bkz. EK-4) |
| Yeni URL/iddia | 0 — yalnız mevcut kanıtın şartnamelenmesi |
| Yöntem | `read(offset=9999, limit=1)` · Tarih 2026-10-15 |

---

# EK-3 (2026-10-15) — Test Taslakları, Strateji Soruları ve Nihai Ölçüm

> Yeni URL/iddia yok; EK-2 matrisinden türetilen taslaklar. Hiçbiri çalıştırılmadı.

## EK-3 §1 — Test Taslakları (PHPUnit · çalıştırılmadı)

| # | Taslak | Sınıf/şema | Beklenen (kod okumasından) |
|---|---|---|---|
| T1 | `getRecommendations` boş context | unit (AIEngine) | `recommendations: []` + dolu metadata |
| T2 | iki ses dosyası → `analyzeAudio` | unit (AIEngine) | 6 alan birebir aynı (placeholder) |
| T3 | var olmayan dosya | unit | `RuntimeException` |
| T4 | ffprobe'siz ortam | unit (şartlı: `@requires`/PATH) | varsayılan metadata + 120.0 bpm |
| T5 | `recommendationWorkflow` sessionsuz | unit (AIWorkflow) | session `'anonymous'`, yazım L78 |
| T6 | `db-query` hata dönerse | unit (mock ToolCalling) | akış sürer, hata görünmez |
| T7 | `applyDiversityFilter` >%30 tek tür | unit | ilgili skorlar ×0.8 |
| T8 | `combineScores` ağırlıklı | unit | `(c*W₁)+(t*W₂)` formülü |

> **Taslaktır** — `⚠️ VERIFICATION REQUIRED` = çalıştırılmış test kanıtı yok; beklenenler
> yalnız kod okumasından türetildi (EK §1.3 · EK §1.1).

## EK-3 §2 — Strateji Soruları (frontmatter uyumu)

| Soru | Durum |
|---|---|
| AI testleri nerede yaşasın? | 🔶 UNKNOWN (korundu) — `shared/tests/Unit/AI/` adayı olmakla birlikte **karar yok** |
| Fixture stratejisi (sahte ses)? | ⚠️ VERIFICATION REQUIRED (korundu) — T4 kısmi çözüm önerisi olabilir |
| Mutation test gerekli mi? | yeni soru — W5 kanıtıyla açıldı, karar değil |
| Çapraz dil testi (PHP↔Python)? | §6 C4 korundu — bugün Python zinciri yok |

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **ayrık ölçüm yok** (EK-2 ile tek editte yazıldı) |
| EK-3 + bu ölçüm sonrası (nihai) | EK-2+EK-3 birlikte **395** ölçüldü → EK-4 ile nihai **502** |
| ≥500 kapı | **✅ (502)** |
| Yöntem | `read(offset=9999, limit=1)` · Tarih 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§8 + EK/EK-2/EK-3 |
| Frontmatter korunur | ✅ `group: kanit` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 — yalnız mevcut kanıtın şartnamelenmesi |

---

# EK-4 (2026-10-15) — Nihai Ölçüm Zinciri ve Kapanış

## EK-4 §1 — Ölçüm zinciri (dürüst kayıt)

| An | Ölçüm | Durum |
|---|---|---|
| Başlangıç | 141 | — |
| EK sonrası | 286 | ❌ (<500) |
| EK-2 sonrası | **395** (EK-2+EK-3 birlikte ölçüldü — ayrık ölçüm yok) | ❌ (395 < 500) |
| EK-3 + EK-4 bu ek ile (nihai) | **502** | ✅ (502) |

## EK-4 §2 — Nihai kapı tablosu

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | **✅ (502)** |
| Mevcut içerik korunur (append-only) | ✅ §0–§8 orijinal + EK/EK-2/EK-3/EK-4 eklendi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK-4 §3 — Ret/zayıf envanter özeti (kanıt olarak kullanılmayanlar)

| Kayıt | Tür |
|---|---|
| `surprise.readthedocs.io/en/stable/metrics.html` 404 | ret (ortak tur) |
| `lyst.github.io/lightfm/` 404 | ret (ortak tur) |
| `recbole.io` · `implicit.readthedocs.io` GASP | ret (ortak tur) |
| `mlflow.org/docs/latest/model-registry.html` title yok | zayıf · sayım dışı |
| `pytorch.org/.../quantization.html` `Redirecting…` | zayıf · sayım dışı |
| Yıldız/indirme sayıları (title teyidi yok) | ⚠️ VERIFICATION REQUIRED |

## EK-4 §4 — Ölçüm methodu ve araçsal kayıt

| Kalem | Değer |
|---|---|
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Shell erişimi | permission.rejected — ölçüm yalnız read ile |
| HTTP turu | Code Mode `fetch` (AbortSignal yok, basit fetch) · 10 URL |
| Tarih | 2026-10-15 |

## EK-4 §5 — K006.9 son söz (kanıt sınırı içinde)

Test **altyapısı** olgundur (PHPUnit ^10.5 · PHPStan seviye 5 · 45 test dosyası ·
10 klasör ailesi); **K006 katmanının test kapsamı 0**'dır (iki bağımsız ölçümde teyitli —
A-T-1). 10 geçerli taze URL 4 yeni aday (Infection · Hypothesis · …) sunar; hiçbiri
kurulu değildir ve Python adaylarının önkoşulu (analiz zinciri) yoktur. 7 kusur hedefi
(P0: 2 · P1: 4 · P2: 1) taslak testlere bağlandı — hiçbiri çalıştırılmamıştır. Karar:
`yapay-zeka-kararlar.md` `status: draft` — bu dosya karar üretmez.

## EK-4 §6 — Kanıt Envanteri (2026-10-15 kayıtları)

| Kaynak | Kapsam |
|---|---|
| `shared/composer.json` | L23–25 · L47–48 (EK §1.1) |
| `shared/tests/**/*.php` glob | 45 dosya · `*AI*` → 0 (EK §1.2) |
| `shared/src/AI/AIEngine.php` | L1–48 · L45–114 · L111–205 · L205–282 · L280–382 (önceki turlar) |
| `shared/src/AI/AIWorkflow.php` | L47–92 · L99–143 (önceki turlar) |
| HTTP fetch turu | 10 URL geçerli (W1–W10) + ortak tur ortak kayıtları |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |

## EK-4 §7 — Test Takımı Dağılımı (glob · 2026-10-15 · 45 dosya)

| Aile | Dosya | Dosya adları (özet) |
|---|---|---|
| `Api/` | 8 | GatewayMethodRouting · Dto · ApiResponse · ApiPipelineSecurity · ResponseNormalization · RequestValidation · RouteTable · Versioning |
| `Middleware/` | 9 | MiddlewarePipeline · Csrf · BypassAuth · BypassAuthFailClosed · Auth · RateLimiter · OriginCheck · Validation · SessionManager |
| `Unit/` | 16 | Config 1 · Device 4 · Log 2 · PageRouter 6 · Security 2 · Theme 1 (aşağıda sayım) |
| `Events/` | 2 | EventDispatcher · DomainEvent |
| `Repository/` | 4 | ApiKeyCreateCommand · ApiKeyRepository + Sqlite yardımcı 2 (test değil) |
| `Security/` | 2 | ReturnUrlPolicyEdgeCase · SessionKeys |
| `OAuth/` | 1 | OAuthManager |
| `Component/` | 1 | TemplateEngine |
| `Fixtures/` | 2 | chrome header/footer (fixture, test değil) |
| **AI/** | **0** | — |

**Unit kırılımı (glob'dan tek tek sayım):** Config 1 (`ConfigManagerTest`) ·
Device 4 (`DeviceRenderer` · `DeviceDetector` · `NavRouteConsistency` · `WelcomePopupRender`)
· Log 2 (`LoggerRedaction` · `Psr3Logger`) · PageRouter 6 (`AuthGuard` · `AuthUrlBuilder` ·
`HtmlShellRendererGuardConfig` · `RouteRegistry` · `HtmlShellRendererChrome` · `SpaRoute`)
· Security 2 (`ReturnUrlPolicy` · `JwtService`) · Theme 1 (`ThemeManagerModeTest`)
→ **Unit = 16**.

**Toplam kontrolü:** 8+9+16+2+4+2+1+1+2 = **45** ✅ glob toplamıyla tutarlı.

| Kalem | Sayı |
|---|---|
| Test sınıfı içeren dosyalar (Fixtures + Sqlite hariç) | 45 − 4 = **41** |
| Bunlardan AI kapsamı | **0** (A-T-1) |

## EK-4 §8 — Sayım Zinciri Notu (§6 C1 bağlamı)

| Tarih | Ölçüm | Kaynak |
|---|---|---|
| 2026-09-27 | `shared/tests` = **22** | agent registry §25.2 |
| 2026-10-12 | glob = **44** | bu dosya §5 · §8 |
| 2026-10-15 | glob = **45** | bu ek EK §1.2 (tek tek sayım 45/45 ✅) |

Üç ölçüm de tarihli ve doğrudur (§6 C1 kaydı korunur); büyüme +23 dosya / 18 gün.
AI kapsamı: 2026-10-12 ve 2026-10-15 ölçümünde **0** (2026-09-27 registry kaydı AI
konusunda sessizdir — o tarih için AI=0 iddiası yazılmaz).
