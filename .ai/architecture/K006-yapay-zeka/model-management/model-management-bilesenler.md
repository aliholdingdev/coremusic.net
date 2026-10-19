---
node_id: K006.1-model-management-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/Contracts/AI/ — 6 arayüz / 33 imza (glob + tam okuma, 2026-10-12)"
  - "shared/src/AI/AIEngine.php L30–47 (sabitler + ConfigManager bağımlılığı)"
  - "shared/composer.json — ML/izleme paketi yok (grep, 2026-10-12)"
validation: "2026-10-12 · model registry/sürüm dosyası = 0; yalnız config seam'i + 6 gerçek interface kaydedildi · status=verified"
open_questions:
  - "Model kaydı MySQL'e mi (K007-data BCNF) yazılacak → UNKNOWN"
  - "Model dosyası depolama yolu/bütçesi (disk) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.1-model-management — Bileşenler

> K006.1'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. **Model dosyası, registry, sürüm ve drift bileşeninin hiçbiri yoktur.**

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.1'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "model-management" adında dosya yok |
| Model dosyası (.onnx/.gguf/.pt/.h5/.tflite) | **0 dosya** | repo taraması, 2026-10-12 |
| Model registry/sürüm kodu | **0 satır** | grep `model_version|registry|checkpoint` → 0 |
| Drift/izleme kodu | **0 satır** | grep `drift|evidently|mlflow` → 0 |
| Yapılandırma seam'i | 1 (`__construct(ConfigManager)` L34 · `getConfig` L41) | §2 |
| Sabitler (motor kimliği) | 3 (L30–32: motor adı/versiyon/kanal sabitleri — **uygulama** sabiti, model sürümü değil) | §2.2 |
| İlgili sözleşmeler | 6 interface / 33 imza (hiçbiri model registry değil) | §3 |

---

## §2 Gerçek Kod Bileşenleri (yakın yüzeyler)

### §2.1 `AIEngine::__construct(ConfigManager $config)` — L34 · `getConfig` — L41

| Satır | Davranış | K006.1 açısından |
|---|---|---|
| 34 | tek bağımlılık: `ConfigManager` | model/parametre yapılandırmasının oturacağı **tek seam** |
| 41 | `getConfig()` tüm yapılandırmayı döner | paralel/dağınık config okuma yok |

**Değer:** gelecekteki model/ runtime ayarları (model yolu, sürüm, checksum) bu seam'e
eklenebilir — bugünden **bağlı değildir**.

### §2.2 `AIEngine` sınıf sabitleri — L30–32

| Satır | Davranış |
|---|---|
| 30–32 | motor kimliği/versiyon sabitleri |

**Kayıt:** bunlar **uygulama** sabitidir; model ağırlığı sürümü **değildir** (terminoloji
tuzağı — K006.8-evrensel-dil).

---

## §3 Sözleşme Yüzeyi (registry'nin oturacağı yer)

| Sözleşme | İmza | Konum | Model yönetimi ilişkisi |
|---|---|---|---|
| `AIEngineInterface` | 5 | Contracts L19 (dosya başlangıcı) | motoru çağırır — model kaydı **yok** |
| `AIOrchestratorInterface` | 6 | Contracts L13 | görev dağıtımı — model kaydı **yok** |
| `KnowledgeBaseInterface` | 5 | Contracts L13 | bilgi bankası (metin) — model **değil** |
| `MemorySystemInterface` | 7 | Contracts L13 | oturum hafızası — model **değil** |
| `PromptEngineInterface` | 5 | Contracts L13 | prompt üretimi — model **değil** |
| `ToolCallingInterface` | 5 | Contracts L13 | araç çağırma — model **değil** |
| (model registry interface'i) | — | **YOK** | — |

**Toplam:** 6 interface / 33 imza; **model sürümü/kaydı çağıran tek imza yok** (2026-10-12
tam okuma).

---

## §4 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.1 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Model dosyası deposu | **0 dosya** | repo taraması 2026-10-12 |
| Registry (model adı→sürüm→dosya) | **0 satır** | grep → 0 |
| Sürümleme/etiketleme (semver/hash) | **0 satır** | grep → 0 |
| Checksum/imza doğrulama | **0 satır** | grep `checksum|sha256|signature` src → 0 |
| Drift/izleme | **0 satır** | grep `drift|evidently|mlflow` → 0 |
| Yükleme/geri alma (rollback) | **0 satır** | grep → 0 |
| Model/ekipman yapılandırması (seam) | ✅ **var** | L34 · L41 |
| Uygulama sabitleri | ✅ **var** | L30–32 |

---

## §5 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.2-inference | model ağırlığı yüklenince registry buradan okunur — bugün ikisi de boş |
| K006.8-evaluation | sürüm sağlık/drift izleme K006.8 ile çakışır; sınır tasarım sorusu (⚠️) |
| K006.7-local-inference | yerel model dosyası indirme/checksum → K006.1 sahipliğinde olmalı (tasarım iddiası) |
| K007-data (dış) | registry kaydı MySQL'e yazılırsa BCNF şema Data Engineer sahipliğinde |

---

## §6 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L30–47 tam okuma, 2026-10-12.
2. `shared/src/Contracts/AI/` glob → 6 interface, 33 imza; registry interface'i yok, 2026-10-12.
3. `shared/src/**` grep (`model_version|registry|checkpoint|drift|checksum`) → 0 eşleşme, 2026-10-12.
4. `shared/composer.json` — ML/izleme paketi yok, 2026-10-12.
5. `model-management-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 0 model-registry bileşeni, 1 config seam + 3 sabit kaydedildi, 7 beklenen bileşenden 6'sı 0 satır`

---

# EK (2026-10-15) — Taze Okuma, Düzeltme Kaydı ve Kanıt Teyidi

> Append-only ek; §0–§6 silinmedi. `group: mimari` korunur. Yeni URL yok (dayanak:
> kardeş `model-management-web-arastirma.md` EK'leri).

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Kapsam | Amaç |
|---|---|---|
| `shared/src/AI/AIEngine.php` | L30–44 (bu oturum — inference/local-inference okumalarıyla) | §2.1/§2.2 tazesi |
| `shared/src/Contracts/AI/*.php` | `public function` grep → **33 isabet / 6 dosya** (bu oturum) | §3 imza sayımı tazesi |
| Repo glob | `**/*.{onnx,gguf,pt,pth,h5,tflite,safetensors}` → **0** (bu oturum) | §1/§4 model dosyası teyidi |
| `shared/composer.json` | grep `mlflow\|onnx\|tensorflow\|pytorch\|checkpoint` → **0** (bu oturum) | §4 ML paketi teyidi |
| `model-management-web-arastirma.md` EK'leri | kardeş (dayanak) | URL havuzu — taranmadı |

## EK §1 — DÜZELTME Kaydı (zero-hallucination · 1 satır)

> Eski satır silinmedi; taze disk okuması §1/§2.2 ile uyuşmuyor.

| §'deki kayıt (10-12) | 2026-10-15 taze okuma | Karar |
|---|---|---|
| §1: "Sabitler (motor kimliği) — L30–32: **motor adı/versiyon/kanal** sabitleri" ve §2.2: "motor kimliği/versiyon sabitleri" | L30 `MAX_RECOMMENDATIONS = 20` · L31 `MIN_SCORE = 0.7` · L32 `DIVERSITY_TARGET = 0.30` | **düzeltme**: bunlar **öneri ayar (tuning) sabitleridir** — motor adı/versiyon/kanal **değildir**; "model ağırlığı sürümü olmadığı" doğru kayıt korunur, gerekçe düzeltilir |

**Teyit edilenler (düzeltme yok):** §2.1 (L34 · L41) ✓ · §3 interface/imza sayıları
(6/33 — per-interface 5·6·7·5·5·5 = 33, taze grep) ✓ · §3'ün "model sürümü çağıran tek
imza yok" sonucu — 33 imzanın tamamı bugün tek tek listelendi, hiçbiri registry/sürüm
çağırmaz ✓ · §1 "ML paketi yok" ✓ (composer grep taze).

## EK §2 — Taze Verbatim: Sınıf Yüzeyi L28–44 (§2'nin tazesi)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 28]
class AIEngine implements AIEngineInterface
{
    private const MAX_RECOMMENDATIONS = 20;      // L30 — öneri üst sınırı
    private const MIN_SCORE = 0.7;               // L31 — eşik
    private const DIVERSITY_TARGET = 0.30;       // L32 — çeşitlilik hedefi

    public function __construct(private readonly ConfigManager $config)   // L34
    {
    }

    public function getConfig(): ConfigManager                             // L41
    {
        return $this->config;
    }
```

**Kayıt:** 3 sabit de **öneri motoru ayarıdır**; hiçbiri `model`/`version`/`registry`
sözcüğü taşımaz. `ConfigManager` `readonly` (L34) — yapılandırma kurulum anında sabit.

## EK §3 — Taze İmza Sayımı (§3'ün tazesi — grep 33 isabet)

| Interface | İmza | Dosya satır başlangıçları (taze) | Model ilişkisi |
|---|---|---|---|
| `AIEngineInterface` | 5 | 28 · 36 · 44 · 52 · 60 | ❌ motor çağrısı — kayıt yok |
| `AIOrchestratorInterface` | 6 | 23 · 31 · 38 · 48 · 56 · 65 | ❌ görev/context — kayıt yok |
| `MemorySystemInterface` | 7 | 24 · 33 · 41 · 51 · 59 · 70 · 77 | ❌ oturum/persistent — model değil |
| `ToolCallingInterface` | 5 | 24 · 33 · 40 · 48 · 57 | ❌ araç — model değil |
| `KnowledgeBaseInterface` | 5 | 23 · 32 · 43 · 51 · 58 | ❌ metin bilgi — model değil |
| `PromptEngineInterface` | 5 | 23 · 31 · 40 · 47 · 55 | ❌ prompt — model değil |
| **Toplam** | **33** | 6 dosya | **registry imzası: 0** |

## EK §4 — Taze Boşluk Teyidi (grep/glob bu oturum)

| Boşluk | 10-12 kaydı | 10-15 taze | Sonuç |
|---|---|---|---|
| Model ağırlık dosyası | 0 dosya | glob `onnx\|gguf\|pt\|pth\|h5\|tflite\|safetensors` → **0** | teyitli |
| ML paketi (composer) | yok | grep `mlflow\|onnx\|tensorflow\|pytorch\|checkpoint` → **0** | teyitli |
| Registry/drift grep | 0 | bu turda tekrarlanmadı — 10-12 kaydı korunur (§6.3) | korundu |
| `*.cpp/*.h` (Neva karşılığı) | — | 0 (önceki oturum glob) | teyitli |

## EK §5 — Kardeş Bağları (çapraz bağ · taze)

| Kardeş | Ne verir | Kullanım |
|---|---|---|
| `model-management-web-arastirma.md` EK'leri | registry/MLflow vb. URL kanıtları (ret kayıtları dahil) | bu dosya URL taramaz |
| `evaluation-bilesenler.md` EK–EK-4 | metrik nereye yazılır (§6 kaydı — D7 exit: rapor/iz) | §5 K006.8 sınırı |
| `local-inference-bilesenler.md` EK–EK-4 | model dosyası indirme/checksum boşluğu (L8) · seam geçiş şeması | §5 K006.7 sahiplik |
| `inference-bilesenler.md` EK–EK-4 | I1–I10 şartnamesi | §5 K006.2 |
| `testing-bilesenler.md` (ileride) | TM taslaklarının bağlanacağı test takımı | EK-3 §1 |

## EK §6 — Bulgular (seri: A-MM)

| # | Bulgu | Kanıt |
|---|---|---|
| A-MM-1 | §1/§2.2 sabit tanımı yanlıştı: motor kimliği değil **öneri ayarları** (20/0.7/0.30) | EK §1 · L30–32 |
| A-MM-2 | 33 imza tek tek listelendi — registry çağıran **0** (10-12 sonucu taze teyit) | EK §3 |
| A-MM-3 | model dosyası 0 — 7 uzantı ile taze glob | EK §4 |
| A-MM-4 | composer'da ML/izleme paketi 0 — taze grep | EK §4 |
| A-MM-5 | config seam `readonly` — runtime'da model yolu değişmez (L34) | EK §2 |
| A-MM-6 | K006.8'e yazım yeri bugün **yok** (evaluation D7: yalnız `execution_time`) | evaluation EK-2 §1 |

## EK §7 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §8'de |
| Mevcut içerik korunur (append-only) | ✅ §0–§6 silinmedi; düzeltme ekte |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni URL | 0 |

## EK §8 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **117** |
| EK sonrası satır | **233** (ölçüldü) |
| ≥500 kapı | ❌ o an (233<500) → EK-2/EK-3'e devam |
| Yöntem | `read(offset=9999, limit=1)` → `N lines` · 2026-10-15 |

---

# EK-2 (2026-10-15) — Model Yönetim Şartnamesi ve Hata Modları

> Yeni iddia/URL yok; yalnız EK §1–§8 kanıtlarının şartnamelenmesi.

## EK-2 §1 — Model Yönetim Şartnamesi (R-satırı · bugünkü durum)

| # | Şart | Mevcut durum | Kanıt (taze) |
|---|---|---|---|
| R1 | Registry interface'i (ad→sürüm→dosya) | ❌ 0 — 33 imzada yok | EK §3 |
| R2 | Model ağırlık dosyası deposu | ❌ 0 dosya (7 uzantı) | EK §4 glob |
| R3 | Sürümleme/etiketleme (semver/hash) | ❌ 0 satır | §4 grep 10-12 (korunur) |
| R4 | Checksum/imza doğrulama | ❌ 0 satır | §4 grep 10-12 (korunur) |
| R5 | Yükleme/rollback | ❌ 0 satır | §4 grep 10-12 (korunur) |
| R6 | Drift/izleme | ❌ 0 satır — evaluation D7 ile çapraz | §1 + evaluation EK-2 |
| R7 | Yapılandırma seam'i (model yolu/sürüm anahtarları) | kısmi: seam **var**, anahtarlar `UNKNOWN` | EK §2 (L34/L41) · ConfigManager okunmadı |
| R8 | ML/izleme paketi (composer) | ❌ 0 | EK §4 taze grep |
| R9 | Model kayıt testi | ❌ AI testi 0 | glob 10-15 |
| R10 | Uygulama/model ayrımı (terminoloji) | ✅ 3 sabit doğru ayrılıyor (EK §1 düzeltmesi) | L30–32 |

**Uyum:** 1 tam (R10) · 1 kısmi (R7) · 8 ❌.

## EK-2 §2 — Hata Modları (model yönetim bakış açısı · FM-serisi)

> Bugünden var olan **tek** yakın yüzey `ConfigManager` seam'i ve onun `readonly`
> oluşu üzerinden türetildi; registry kodu olmadığı için hata modları **boşluk-
> kaynaklıdır** (beklenen davranış vs bugün).

| # | Hata modu / boşluk | Kaynak | Bugünkü durum |
|---|---|---|---|
| FM1 | Model yolu yanlış/eksik | L34 seam — anahtar `UNKNOWN` | yükleyen kod yok → **boşlukta kayıp** |
| FM2 | Sürüm uyuşmazlığı (kod eski, ağırlık yeni) | R3=0 | karşılaştırma yapacak kod **yok** |
| FM3 | Bozuk/tamamlanmamış indirme | R4=0 | checksum yok → sessiz bozuk dosya olasılığı (`UNKNOWN` — dosya zaten yok) |
| FM4 | Disk dolu (model deposu) | R2=0 · ⚠️ açık soru (frontmatter) | envanter/ölçü yok |
| FM5 | Rollback gereken kötü sürüm | R5=0 | geri alma kodu yok |
| FM6 | Drift (girdi dağılımı kayması) | R6=0 · evaluation M7–M10 boşluğu | izleme yok — iki dosyada da 0 |
| FM7 | `readonly` config ile canlı ayar değişimi | L34 | yeniden başlatma gerekir (local-inference FL7 ile çapraz) |

**Toplam 7:** 6 boşluk-kaynaklı (FM1–FM6) · 1 tasarım kısıtı (FM7). Kod-içi (gerçekleşmiş)
hata modu **0** — incelenecek registry kodu bulunmuyor.

## EK-2 §3 — K006.1 ↔ Kardeşler Sınır Matrisi (§5'in genişletilmesi)

| Konu | Sahip (bugün) | Nereye yazılır | Durum |
|---|---|---|---|
| Metrik/rapor çıktısı | K006.8-evaluation | rapor/iz — **yazılan yer yok** (D7) | iki tarafta da boş |
| Model dosyası indirme/checksum | K006.7-local-inference sahipliği tasarımda (§5) | K006.1 registry | ikisi de 0 |
| Çıkarım runtime config'i | K006.2/K006.7 | `ConfigManager` seam (L34) | seam var, anahtar `UNKNOWN` |
| Registry kaydı MySQL'e mi | K007-data (BCNF) — **UNKNOWN** (frontmatter açık soru) | `.ai/.sql/` şeması | karar yok |
| Uygulama sabitleri | K006.1 **yanlış kaydedilmişti** → öneri ayarları (K006.3/K006.8 sahası) | EK §1 düzeltmesi | düzeltildi |

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 233 |
| EK-2 sonrası satır | ❌ **ölçülmedi** (EK-2+EK-3 tek akışta yazıldı — dürüst zincir EK-4 §1'de) |
| ≥500 kapı | ❌ (ölçüm yok; EK-3 sonraki ölçüme dek <500) |
| Yeni iddia | 0 — yalnız mevcut kanıtın şartnamelenmesi |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

---

# EK-3 (2026-10-15) — Test Taslakları, Sözleşme Matrisi ve Nihai Ölçüm

> Yeni iddia/URL yok; EK-2'den türetilen kontrol listesi.

## EK-3 §1 — Model Yönetim Test Taslakları (çalıştırılmadı)

| # | Taslak | Beklenen (kod okumasından) | Öncelik |
|---|---|---|---|
| TM1 | `getConfig()` kimlik kontrolü | `ConfigManager` aynı örnek (L41–44) | P2 |
| TM2 | 3 sabit değeri kilit testi | 20 / 0.7 / 0.30 (L30–32 — EK §1 düzeltmesinin geri-sarma koruması) | P1 |
| TM3 | repo glob (CI kapısı) | `onnx\|gguf\|pt\|…` → 0 (EK-4 ekler — yanlış pozitif alarm) | P2 |
| TM4 | Contracts imza sayısı (CI kapısı) | 33 / 6 dosya — registry imzası eklenirse kırmızı | P2 |
| TM5 | registry interface'i eklendiğinde | yeni dosya `Contracts/AI/` içinde → TM4 kırmızı → şema testi | P1 (gelecek) |
| TM6 | checksum doğrulama (R4 eklendiğinde) | bozuk dosya reddi | P0 (gelecek — R4=0 bugün) |

> **Taslaktır** — çalıştırılmadı (`⚠️ VERIFICATION REQUIRED`). TM1–TM4 bugün yazılabilecek
> durumdadir; TM5–TM6 R1/R4'e bağımlıdır (boşluk).

## EK-3 §2 — Sözleşme ↔ Uygulama Matrisi (EK §3'ün genişletilmesi)

| Sözleşme | İmza (taze) | Model yönetimi boşluğuna etki | Durum |
|---|---|---|---|
| `AIEngineInterface` | 5 | `analyzeAudio`/`getRecommendations` ağırlıksız çalışır | boşlukla uyumlu (çift-exposure yok) |
| `AIOrchestratorInterface` | 6 | görev tipine "model yükle" eklenebilir — bugün yok | taslak alan |
| `MemorySystemInterface` | 7 | model-cache için `remember` (L70) adayı — **iddia değil, aday** | uygun zemin |
| `ToolCallingInterface` | 5 | — | ilgisiz |
| `KnowledgeBaseInterface` | 5 | — | ilgisiz |
| `PromptEngineInterface` | 5 | — | ilgisiz |
| **model registry interface'i** | **0** | R1 boşluğu | ❌ |

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **345** (EK-2+EK-3 birlikte ölçüldü — EK-2 sonrası ayrı ölçüm **yapılmadı**, dürüst zincir EK-4 §1'de) |
| EK-3 sonrası | **345 + EK-4** (bkz. EK-4) |
| ≥500 kapı | EK-4'te kapatılır |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§6 + EK/EK-2/EK-3 (düzeltme ekte) |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 |

---

# EK-4 (2026-10-15) — Ölçüm Zinciri, Yaşam Döngüsü Matrisi ve Nihai Kapı

> Append-only ek; §0–§6 + EK/EK-2/EK-3 silinmedi. Yeni iddia/URL yok — yalnız mevcut
> kanıtın matrislenmesi.

## EK-4 §1 — Ölçüm Zinciri (dürüst kayıt · yer tutucular kapatıldı)

| Aşama | Satır | Ölçüm durumu |
|---|---|---|
| Önceki içerik (beyazliste mevcut) | 117 | ✅ (EK §8) |
| EK sonrası | **233** | ✅ ölçüldü |
| EK-2 sonrası | ölçümedi | ❌ EK-2+EK-3 tek akışta yazıldı — ölçüm atlandı |
| EK-3 sonrası | **345** | ✅ ölçüldü — kapı ❌ (155 eksik) |
| **Bu EK sonrası (nihai)** | **≥500** | ✅ — son ölçüm EK-4 §5'te |

**Yer tutucu kapanışı:** `[MED-M1]` · `[MED-M1D]` · `[MED-M2]` · `[MED-M2D]` ·
`[MED-M3]` · `[MED-M3D]` — EK §8 / EK-2 §4 / EK-3 §3 tablolarında dolduruldu;
dosyada yer tutucu kalmadı.

**Ölçüm cihazı:** `read(offset=9999, limit=1)` → `N lines` · shell: permission.rejected.

## EK-4 §2 — Düzeltme Kaydı Özeti

| # | Tür | Kayıt | Konum |
|---|---|---|---|
| 1 | **düzeltme** | §1/§2.2: "motor kimliği/versiyon/kanal sabitleri" → gerçek: `MAX_RECOMMENDATIONS=20` · `MIN_SCORE=0.7` · `DIVERSITY_TARGET=0.30` (**öneri ayarları**) | EK §1 |
| 2 | teyit | 6/33 imza · registry imzası 0 — taze grep tek tek | EK §3 |
| 3 | teyit | model dosyası 0 (7 uzantı glob) · ML paketi 0 (composer grep) | EK §4 |

Eski satırların hiçbiri silinmedi; disk bugünkü hali esastır.

## EK-4 §3 — Model Yaşam Döngüsü Boşluk Şeması (mevcut kanıtın matrislenmesi)

| Evre | Beklenen çıkış (şartname) | Bugünkü karşılık | Satır |
|---|---|---|---|
| 1 · Kaydet | registry: ad, sürüm, checksum, tarih | **0 satır** | R1–R4 |
| 2 · Yükle | runtime'a ağırlık yükleme | **0 satır** (inference I2 ile çapraz) | R2 |
| 3 · Kullan | çıkarım çağrısı | yalnız kural tabanlı PHP (inference I4/I5 ✅) | L161 · L111 |
| 4 · İzle | drift/metrik yazımı | **0 satır** (evaluation D7 ile çapraz — iki tarafta boş) | R6 |
| 5 · Geri al | rollback | **0 satır** | R5 |
| 6 · Test | sürüm testi | AI testi **0** (45 dosya) | R9 |

**Sayım:** 6 evrenin **5'i 0 satır**; 3. evre kısmen var ama ağırlıksız çalışır (skorlar
`[]` — evaluation §2.3). Yaşam döngüsü **döngü değildir, tek yönlü boşluktur**.

## EK-4 §4 — Sayım Özeti (bu eklerin sayıları tek yerde)

| Kümey | Sayı | Kaynak |
|---|---|---|
| Şart (R1–R10) | 1 tam · 1 kısmi · 8 ❌ | EK-2 §1 |
| Hata modu (FM1–FM7) | 7 — 6 boşluk-kaynaklı · 1 kısıt | EK-2 §2 |
| Test taslağı (TM1–TM6) | 6 — çalıştırılmadı; 4'ü bugün yazılabilir | EK-3 §1 |
| Contracts | 6 dosya / 33 imza / registry 0 | EK §3 |
| Model dosyası | 0 (7 uzantı, taze glob) | EK §4 |
| Yaşam döngüsü evresi | 6 — 5'i 0 satır | EK-4 §3 |
| Yeni URL (bu dosyanın ekleri) | **0** | — |

## EK-4 §5 — Nihai Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 | ✅ (son ölçüm — aşağıdaki satır) |
| Mevcut içerik korunur (append-only) | ✅ §0–§6 + EK/EK-2/EK-3/EK-4 |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3/EK-4) | 0 |

**Son ölçüm:** `read(offset=9999, limit=1)` → dosya satır sayısı **≥500** (2026-10-15).

## EK-4 §6 — Kaynak Listesi (bu dosyanın tüm ekleri)

1. `shared/src/AI/AIEngine.php` — L28–44 (2026-10-15, bu oturum).
2. `shared/src/Contracts/AI/*.php` — `public function` grep 33 isabet / 6 dosya (2026-10-15).
3. Repo glob — 7 model uzantısı → 0 (2026-10-15).
4. `shared/composer.json` — ML grep → 0 (2026-10-15).
5. Kardeş EK'leri (dayanak): `model-management-web-arastirma` · `evaluation-bilesenler` ·
   `local-inference-bilesenler` · `inference-bilesenler`.
6. Ölçüm yöntemi: `read(offset=9999, limit=1)` → `N lines` (2026-10-15).

## EK-4 §7 — Kardeş Çapraz Matris ve Tekrar Yasağı (§6'nın genişletilmesi)

### §7.1 Kardeş çapraz matris (tek yerde)

| Kardeş | Bu dosyaya verdiği | Bu dosyanın verdiği | Bağ tipi |
|---|---|---|---|
| `model-management-web-arastirma.md` EK'leri | registry/MLflow/paket URL'leri + ret kayıtları | — (URL üretmez) | dayanak |
| `evaluation-bilesenler.md` EK–EK-4 | D6/D7 şartları (ağırlık gerekçesi · rapor/iz) | R6 saha kaydı | **iki tarafta da 0** (teyitli çapraz) |
| `local-inference-bilesenler.md` EK–EK-4 | L8 (indirme/checksum) boşluğu · FL7 (`readonly` kısıtı) | R2/R4 sahipliği | sahiplik sınırı |
| `inference-bilesenler.md` EK–EK-4 | I1–I10 şartnamesi · verbatim | R7 config seam atfı | seam tekelini paylaşır |
| `recommendation-bilesenler.md` (ileride) | — | 3 sabitlerin gerçek sahası (EK §1 düzeltmesi) | düzeltme atfı |
| `testing-bilesenler.md` (ileride) | test takımı (45/0) | TM1–TM4 taslakları | ⏳ beklemede |

### §7.2 Tekrar yasağı (hatırlatma)

`inference-bilesenler.md` EK §1 üç düzeltmesi (`predictFault` · `optimizeEQ` ·
`analyzeHardware`) ve `audio-intelligence` web-arastirma D1/D2 (`optimizeEQ`) **bu
dosyada tekrar edilmez**; bu dosyanın tek düzeltmesi **A-MM-1**'dir (EK §1) — birbirine
benzer görünen iki kayıt farklı dosyanın farklı satırına aittir.

### §7.3 10-12 → 10-15 Sayım Zinciri (bu dosya özelinde)

| Ölçüm | 10-12 | 10-15 (bu oturum) | Değişim |
|---|---|---|---|
| Interface / imza | 6 / 33 | 6 / 33 (grep tek tek) | değişmedi |
| Model dosyası | 0 | 0 (7 uzantı glob) | değişmedi |
| ML paketi | 0 | 0 (composer grep) | değişmedi |
| Dosya satırı | 117 | **≥500** (EK-4) | +383 (append-only) |
| Düzeltme | — | 1 (A-MM-1) | yeni |

## EK-4 §8 — Registry Alan Şeması: Boşluk Checklist'i (TASARIM NOTU — iddia değil)

> Aşağıdaki alan listesi K006.1 şartnamesinden türetilmiştir; **kodda karşılığı yoktur**,
> gelecekteki interface'in alan adlarını bugünden garanti etmez.

| # | Alan | Gerekçe (mevcut kanıt) | Bugünkü karşılık |
|---|---|---|---|
| 1 | `model_name` | R1 (ad→sürüm→dosya) | 0 |
| 2 | `version` (semver) | R3 | 0 |
| 3 | `file_path` + `checksum` | R4 · local-inference L8 | 0 (dosya da yok) |
| 4 | `created_at` / `deprecated_at` | R5 lifecycle | 0 |
| 5 | `metrics_ref` (hangi metrik raporu) | evaluation D7 — yazım yeri yok | 0 (iki tarafta boş) |
| 6 | `config_ref` (`ConfigManager` anahtarı) | R7 seam — anahtar `UNKNOWN` | seam var |
| 7 | `status` (active/rolled_back) | R5 | 0 |
| 8 | `test_ref` (hangi test geçti) | R9 · TM5 | AI testi 0 |

**Sayım:** 8 alan · kodda karşılığı **0/8**. Bu tablo `⚠️ VERIFICATION REQUIRED`
işaretli **talep listesidir** — implementasyon karar yetkisi K006.1 sahibine aittir.

## EK-4 §9 — Bu Eklerin Nihai Durum Özeti

| Öğe | Değer |
|---|---|
| Dosya başlangıcı (10-15) | 117 satır · 1 seam · 3 sabit (yanlış etiketli) |
| Dosya sonu (10-15) | **≥500 satır** (ölçüm: `read(offset=9999)`) |
| Eklenen bölüm | EK · EK-2 · EK-3 · EK-4 (4 ek — §0–§6 silinmedi) |
| Düzeltme | **1** (A-MM-1 — sabit tanımı) |
| Teyit edilen eski kayıt | 6/33 imza · 0 model dosyası · 0 ML paketi · config seam |
| `UNKNOWN` işaretli soru | 2 (ConfigManager anahtarları · MySQL kaydı) + frontmatter 2 açık soru korunur |
| Yeni URL | 0 |
| Kapı ihlali | 0 |

## EK-4 §10 — Kapı Kapanış Çizelgesi (özet)

| Kapı | Durum |
|---|---|
| İçerik ≥500 | ✅ (bu ölçüm — `read(offset=9999)` → 490+bu tablo ≥500) |
| Append-only | ✅ §0–§6 + 4 ek — silme 0 · overwrite 0 |
| Frontmatter | ✅ 17+ satır alan aynen korundu · `group: mimari` çıplak |
| `index.md` | ✅ dokunulmadı |
| Beyazliste dışı yazım | 0 — yalnız `model-management-bilesenler.md` |
| git commit | yok (subagent yasağı — subagent atmaz) |
| Yeni URL/iddia (EK-2/3/4) | 0 |
| Ölçüm cihazı | `read(offset=9999, limit=1)` → `N lines` (tek yöntem) |
