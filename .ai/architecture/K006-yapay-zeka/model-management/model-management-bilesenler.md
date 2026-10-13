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
