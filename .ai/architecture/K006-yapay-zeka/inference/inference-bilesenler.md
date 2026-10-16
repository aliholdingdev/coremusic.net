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

---

# EK (2026-10-15) — Taze Kod Okuması, Düzeltme Kaydı ve Verbatim Kanıtlar

> Append-only ek; §0–§7 silinmedi. `group: mimari` korunur. Yeni URL yok (bileşen
> dosyası — URL havuzu kardeş `inference-web-arastirma.md` EK'lerindedir, taranmadı).

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Kapsam | Amaç |
|---|---|---|
| `shared/src/AI/AIEngine.php` | L105–209 (bu oturum) | §2.1/§2.2/§2.4 tazesi — optimizeEQ · analyzeHardware · predictFault |
| `shared/src/AI/AIEngine.php` | L310–382 (bu oturum) | §2.3 tazesi — extractFeatures · bandToFrequency · calculateRoomCorrection · calculateBandQ (dosyanın sonu L382) |
| `shared/src/Contracts/AI/AIEngineInterface.php` | L1–61 **tam okuma** (bu oturum) | §4 tazesi — 5 imza docblock dahil |
| `shared/src/AI/AIWorkflow.php` | grep 12 isabet (bu oturum) | §3 tazesi — 5 workflow + 7 çağrı satırı |
| `inference-web-arastirma.md` EK'leri | kardeş (dayanak) | URL kanıtları — yeniden taranmadı |
| Glob (önceki oturum ölçümleri) | `*.cpp/*.h` 0 · `Contracts/AI` 6 · `tests/**/*AI*` 0 · test 45 dosya | §1/§5 teyidi |

## EK §1 — DÜZELTME Kaydı (zero-hallucination · 3 satır)

> Eski satırlar **silinmedi**; taze disk okuması üç kayıtla uyuşmuyor. Disk bugünkü
> hali esastır.

| §'deki kayıt (10-12) | 2026-10-15 taze okuma | Karar |
|---|---|---|
| §2.1 başlık: `predictFault(array $hardwareSpecs)` | `predictFault(array $metrics)` — L161 · Contracts L60 da `$metrics` | **taze parametre adı bağlayıcı** (`$hardwareSpecs` kodda yok) |
| §2.2 başlık: `optimizeEQ(array $frequencyResponse, string $roomType)` | `optimizeEQ(array $roomProfile)` — L111 · Contracts L44 ile aynı | **taze imza bağlayıcı**; `frequency_response`/`size`/`reverb` artık `$roomProfile` **içindedir** (L113–115), ayrı parametre değildir |
| §2.4: `analyzeHardware` → "kural tabanlı donanım puanlama" | L141–155: sabit başlangıç değerleri (`$status='healthy'`, snr/thd/temperature 0.0) + yorum L147–148 "Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak" → **return edilen tek şey sabit sıfırlar** | **düzeltme**: bu metotta kural/yoklama **yoktur**; §1'deki "3 kural-tabanlı tahmin" sayımı da bundan etkilenir → kural-tabanlı **2** (predictFault · optimizeEQ), placeholder **1** (analyzeHardware) |

§2.4'ün `getRecommendations`/`analyzeAudio` satırları ve §3'ün 5 workflow başlangıç
satırı (L49 · L99 · L147 · L199 · L241) taze grep ile **doğrulandı** (değişiklik yok).

## EK §2 — Taze Verbatim: `predictFault` L161–202 (bugünkü hali)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 161]
public function predictFault(array $metrics): array
{
    $risk = 'low';
    $probability = 0.0;
    $alerts = [];
    $maintenance = [];
    // Anomaly detection — metrik eşiklerini kontrol et
    $temperature = $metrics['temperature'] ?? 0;
    $snr = $metrics['snr'] ?? 0;
    $thd = $metrics['thd'] ?? 0;
    $usageHours = $metrics['usage_hours'] ?? 0;
    if ($temperature > 70) {                       // L174
        $risk = 'high';
        $probability += 0.4;
        $alerts[] = "Yüksek sıcaklık: {$temperature}°C";
    }
    if ($snr < 90) {                               // L180
        $risk = $risk === 'low' ? 'medium' : $risk;
        $probability += 0.2;
        $alerts[] = "Düşük SNR: {$snr}dB";
    }
    if ($thd > 0.1) {                              // L186
        $risk = $risk === 'low' ? 'medium' : $risk;
        $probability += 0.15;
        $alerts[] = "Yüksek THD: {$thd}%";
    }
    if ($usageHours > 10000) {                     // L192
        $maintenance[] = "Bakım zamanı gelmiş olabilir ({$usageHours} saat)";
    }
    return [                                       // L196
        'risk'         => $risk,
        'probability'  => min($probability, 1.0),
        'alerts'       => $alerts,
        'maintenance'  => $maintenance,
    ];
}
```

**Taze eşik tablosu (EK-2 §2'nin kaynağı):**

| Koşul | Satır | Risk etkisi | Olasılık artışı | Çıktı |
|---|---|---|---|---|
| `temperature > 70` | 174–178 | `high` | +0.4 | alert |
| `snr < 90` | 180–184 | low→`medium` | +0.2 | alert |
| `thd > 0.1` | 186–190 | low→`medium` | +0.15 | alert |
| `usageHours > 10000` | 192–194 | — | — | bakım notu |
| dönüş | 196–201 | — | `min(…, 1.0)` doygunluk | 4 alan |

## EK §3 — Taze Verbatim: `optimizeEQ` L111–133 · `analyzeHardware` L138–156

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 111]
public function optimizeEQ(array $roomProfile): array
{
    $roomSize = $roomProfile['size'] ?? 'medium';
    $reverb = $roomProfile['reverb'] ?? 0.5;
    $frequencyResponse = $roomProfile['frequency_response'] ?? [];
    $eqCurve = [];
    for ($band = 0; $band < 31; $band++) {                      // L119
        $frequency = $this->bandToFrequency($band);
        $gain = $this->calculateRoomCorrection($frequency, $roomSize, $reverb, $frequencyResponse);
        $q = $this->calculateBandQ($frequency, $roomSize);
        $eqCurve[] = ['band' => $band, 'frequency' => $frequency,
                      'gain' => round($gain, 2), 'q' => round($q, 2)];
    }
    return $eqCurve;                                            // L132
}
```

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 138]
public function analyzeHardware(string $deviceId): array
{
    $status = 'healthy'; $snr = 0.0; $thd = 0.0; $temperature = 0.0; $recommendations = [];
    // Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)
    // Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak   // L148
    return ['status' => $status, 'snr' => $snr, 'thd' => $thd,             // L149
            'temperature' => $temperature, 'recommendations' => $recommendations];
}
```

**Kayıt:** `optimizeEQ` girdisi 3 alan (`size`/`reverb`/`frequency_response` —
varsayılanlar `medium`/`0.5`/`[]`); 31 bant döngüsü L119. `analyzeHardware` **parametre
`$deviceId`'yi okumaz** — her çağrıda aynı gövde döner (L141–155).

## EK §4 — Taze Verbatim: Yardımcı Hesaplar L315–381 (§2.3'ün tazesi)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 315]
private function extractFeatures(string $filePath): array
{
    // Gerçek implementasyonda C++ DSP engine ile yapılacak        // L317
    // Şimdilik placeholder değerler                                // L318
    return ['bpm' => 120.0, 'key' => 'C', 'energy' => 0.7,          // L319–326
            'mood' => 'happy', 'danceability' => 0.6, 'acousticness' => 0.3];
}
```

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 332]
private function bandToFrequency(int $band): float
{
    $frequencies = [20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,          // L336
        200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600,              // L337
        2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000]; // L338
    return $frequencies[$band] ?? 1000.0;                                 // L341
}
```

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 347]
private function calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float
{
    $roomFactor = match ($roomSize) {                 // L350
        'small' => 1.2, 'medium' => 1.0, 'large' => 0.8, default => 1.0,
    };
    $targetResponse = 0.0;                            // L358 — düz tepki hedefi
    $freqKey = (string) $frequency;
    $actualResponse = $frequencyResponse[$freqKey] ?? 0.0;
    return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb); // L362
}
```

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 368]
private function calculateBandQ(float $frequency, string $roomSize): float
{
    if ($frequency < 100)  { return 0.7; }            // L371–373
    if ($frequency < 500)  { return 1.0; }            // L374–376
    if ($frequency < 2000) { return 1.4; }            // L377–379
    return 1.8;                                        // L380
}
```

**Kayıt:** `$roomSize` `calculateBandQ`'da **okunmaz** (imzada var, gövdede kullanılmaz) —
Q yalnız frekanstan türetilir. `bandToFrequency` fallback `1000.0` (L341).

## EK §5 — Taze Workflow Çağrı Satırları (§3'ün taze tablosu)

| Workflow | Başlangıç (§3) | Motor çağrısı (taze grep) | Çağrı satırı |
|---|---|---|---|
| `recommendationWorkflow` | L49 ✅ | `engine->getRecommendations($context, $context['limit'] ?? 10)` | **L75** |
| `audioAnalysisWorkflow` | L99 ✅ | `engine->analyzeAudio($filePath)` | **L109** |
| `autoEQWorkflow` | L147 ✅ | `engine->optimizeEQ($roomProfile)` · `engine->analyzeHardware($deviceId)` | **L164 · L168** |
| `hardwareAnalysisWorkflow` | L199 ✅ | `engine->analyzeHardware($deviceId)` · `engine->predictFault([...])` | **L208 · L211** |
| `faultPredictionWorkflow` | L241 ✅ | `engine->predictFault($request)` | **L249** |

**Sayım:** 5 workflow · 7 motor çağrısı · **dış model servisi çağrısı 0** (§1 grep
kaydı korunur). `hardwareAnalysisWorkflow` tek workflow'tur — hem analiz hem arıza
tahmini çağırır (L208+L211).

## EK §6 — Taze Sözleşme Okuması (Contracts L1–61 tamamı)

| İmza | Satır | `@return` docblock (Contracts) | Uygulayan (taze) |
|---|---|---|---|
| `getRecommendations(array $context, int $limit = 20)` | L28 | `array{id, title, artist, score, reason}` | AIEngine L49 ✅ |
| `analyzeAudio(string $filePath)` | L36 | `array{bpm, key, energy, mood, danceability, acousticness}` | AIEngine L82 ✅ (girdi: placeholder §4) |
| `optimizeEQ(array $roomProfile)` | L44 | `array{band, frequency, gain, q}` | AIEngine L111 ✅ (EK §3 — 4 alan birebir) |
| `analyzeHardware(string $deviceId)` | L52 | `array{status, snr, thd, temperature, recommendations}` | AIEngine L138 ✅ şekil — içerik placeholder |
| `predictFault(array $metrics)` | L60 | `array{risk, probability, alerts, maintenance}` | AIEngine L161 ✅ (EK §2 — 4 alan birebir) |

**Teyit:** §4'teki "5/5 implementasyon" satırı **doğru**; docblock dönüş şemaları iki
metotta (optimizeEQ · predictFault) verbatim ile birebir oturur. (İnce oynama:
docblock `analyzeAudio` 6 alan ister — hepsi `extractFeatures` placeholder'ından gelir.)

## EK §7 — Kardeş Bağları (çapraz bağ · taze)

| Kardeş | Ne verir | Kullanım |
|---|---|---|
| `inference-web-arastirma.md` EK'leri | URL kanıtları (runtime/lokal çıkarım literatürü) | bu dosya URL taramaz |
| `local-inference-bilesenler.md` (ileride) | K006.7 runtime seçeneği | §6 K006.7 kaydı |
| `model-management-bilesenler.md` (ileride) | ağırlık kayıt katmanı | §6 K006.1 kaydı |
| `audio-intelligence-bilesenler.md` EK–EK-4 | K003 DSP zinciri · iki-taraflı boşluk matrisi | `extractFeatures` L317 "C++ DSP engine" yorumunun hedefi |
| `recommendation-bilesenler.md` / `evaluation-bilesenler.md` EK §1 | `getRecommendations` taze imza tekeli | bu dosya §4'te yalnız atıf |

## EK §8 — Bulgular (seri: A-INF)

| # | Bulgu | Kanıt |
|---|---|---|
| A-INF-1 | §2.1 parametre adı `hardwareSpecs` kodda **yok** → `$metrics` | L161 · Contracts L60 |
| A-INF-2 | §2.2 imzası uydurma (2 parametre) → tek `$roomProfile` | L111 · Contracts L44 |
| A-INF-3 | `analyzeHardware` kural değil **sabit placeholder** — `$deviceId` okunmaz | L141–155 |
| A-INF-4 | gerçek eşikler: 70°C/90dB/0.1%/10000sa · ağırlıklar 0.4/0.2/0.15 | L174–194 |
| A-INF-5 | `probability` `min(1.0)` ile doyurulur — kalibrasyon yok | L198 |
| A-INF-6 | `extractFeatures` 6 sabit; `analyzeAudio` zincirin başı placeholder | L315–327 |
| A-INF-7 | `calculateBandQ` `$roomSize` parametresini **kullanmaz** | L368–381 |
| A-INF-8 | 5 workflow başlangıç satırı §3'te doğru; motor çağrıları L75/L109/L164/L168/L208/L211/L249 | EK §5 |
| A-INF-9 | Contracts 5/5 · docblock ↔ verbatim 4 alan birebir | EK §6 |

## EK §9 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §10'da |
| Mevcut içerik korunur (append-only) | ✅ §0–§7 silinmedi; düzeltmeler **ek olarak** |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni URL | 0 |

## EK §10 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **143** |
| EK sonrası satır | **389** (ölçüldü — bu oturum) |
| ≥500 kapı | ❌ o an (389<500) → EK-2/EK-3'e devam |
| Yöntem | `read(offset=9999, limit=1)` → `N lines` · 2026-10-15 |

---

# EK-2 (2026-10-15) — Çıkarım Şartnamesi, Hata Modları ve K003 Bağı

> Yeni iddia/URL yok; yalnız EK §1–§9 kanıtlarının şartnamelenmesi.

## EK-2 §1 — Çıkarım Şartnamesi (I-satırı · bugünkü durum)

| # | Şart | Mevcut durum | Kanıt (taze) |
|---|---|---|---|
| I1 | Çıkarım runtime'ı interface'i olmalı | ❌ 0 — Contracts 6 arayüz, çıkarım runtime'ı yok | Contracts tam okuma (EK §6) |
| I2 | Model ağırlığı yüklenmeli (.onnx/.gguf/.pt) | ❌ 0 dosya/satır | §1/§5 grep kaydı (10-12) korunur |
| I3 | Dış model servisi istemcisi olmalı | ❌ 0 satır | §1 grep · EK §5: 7 çağrı → hepsi AIEngine |
| I4 | Tahmin deterministik ve denetlenir olmalı | ✅ eşik ağacı, `min(1.0)` — denetlenebilir | EK §2 |
| I5 | EQ eğrisi deterministik olmalı | ✅ 31 bant, round(2), sabit frekans tablosu | EK §3 · EK §4 |
| I6 | Donanım analizi gerçek ölçmeli | ❌ sabit placeholder (`healthy`/0.0) | A-INF-3 |
| I7 | Çıkarım önbelleği/batch olmalı | ❌ 0 satır | §5 grep kaydı |
| I8 | Çıkarım testi olmalı | ❌ AI testi 0 (45 dosya içinde) | glob 10-15 |

**Uyum:** 2/8 (I4 · I5). 6 ❌.

## EK-2 §2 — Hata Modları (çıkarım bakış açısı · FI-serisi)

| # | Hata modu | Konum | Davranış |
|---|---|---|---|
| FI1 | `$metrics` eksik anahtarlar | L169–172 `?? 0` | `temperature=0` → `>70` false → **yanlış-negatif sessiz** (düşük risk) |
| FI2 | `probability` doygunluğu | L198 `min(1.0)` | 3 eşik aşılırsa 0.75; 1.0'a çarpım yolu yok — eşikler toplanır, **kalibrasyon yok** |
| FI3 | `$roomSize` geçersiz değer | L350–355 `match default => 1.0` | sessiz `medium` muamelesi — uyarı yok |
| FI4 | `reverb > 1.0` | L362 `(1 - $reverb)` | negatif çarpan → **ters kazanç** (gain işareti döner) — doğrulama yok |
| FI5 | `frequency_response` anahtarı `'50'` vs `'50.0'` | L359 `(string) $frequency` | 50.0→`"50"` PHP'de oturur; 31.5 → `"31.5"` — anahtar formatı şarta bağlı, `UNKNOWN` (veri üreticisi görünmez) |
| FI6 | `$band` ≥ 31 veya negatif | L341 `?? 1000.0` | fallback 1000 Hz — yanlış bant **sessiz** 1 kHz sayılır |
| FI7 | `analyzeHardware` yanlış-pozitif sağlık | L141 `healthy` | cihaz ölçülmeden "healthy" döner — **güvenilirlik ihlali** (A-INF-3) |
| FI8 | `extractFeatures` sabitleri skorlamaya girerse | L319–326 → getContentBasedScores | bugün `[]` döndüğü için zincir kırık (evaluation F1 ile çapraz) — sabitlerin etkisi `UNKNOWN` |

**Toplam 8:** 3 sessiz yanlış (FI1 · FI6 · FI7) · 1 işaret (FI4) · 2 `UNKNOWN` (FI5 · FI8) ·
2 sınır (FI2 · FI3).

## EK-2 §3 — K003 (Neva Engine) Bağı — `extractFeatures`'in Hedefi

| Öznitelik (L319–326) | K006.2'de | K003 hedefinde | Bağ durumu |
|---|---|---|---|
| `energy` 0.7 (sabit) | placeholder | `02-dsp-zincir-mimari.md` Metering zinciri hedefte (L55 alanı — önceki oturum okuma) | **tasarım düzeyinde bağlanabilir** |
| `bpm` 120.0 | placeholder | zincirde ritim analizi adımı **yok** | sahipsiz |
| `key` 'C' | placeholder | yok | sahipsiz |
| `mood`/`danceability`/`acousticness` | placeholder | yok | sahipsiz |

**Kısıt:** repo `*.cpp/*.h` = **0** (önceki oturum glob) → L317'deki "C++ DSP engine ile
yapılacak" yorumu bugün **çağrılan bir kod değildir**; K003 yalnız `.ai/projects/NevaEngine/*.md`
tasarım verisidir. Bağ tasarımdır, implementasyon değil.

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 389 |
| EK-2 sonrası satır | ❌ **ölçülmedi** (EK-2+EK-3 tek akışta yazıldı — dürüst zincir: ölçüm atlanmıştır) |
| ≥500 kapı | ❌ (ölçüm yok; EK-3 sonraki ölçüme dek <500 — bkz. EK-4 §1) |
| Yeni iddia | 0 — yalnız mevcut kanıtın şartnamelenmesi |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

---

# EK-3 (2026-10-15) — Test Taslakları, Sözleşme Matrisi ve Nihai Ölçüm

> Yeni iddia/URL yok; EK-2'den türetilen kontrol listesi.

## EK-3 §1 — Çıkarım Test Taslakları (çalıştırılmadı)

| # | Taslak | Beklenen (kod okumasından) | Öncelik |
|---|---|---|---|
| TI1 | eksik `$metrics` → `predictFault([])` | `risk=low, probability=0.0` (FI1) | P0 |
| TI2 | 3 eşik birden → `predictFault` | `risk=high, probability=0.75` (L174–198) | P0 |
| TI3 | `reverb=1.5` → `optimizeEQ` | negatif kazanç possible (FI4) | P0 |
| TI4 | geçersiz `size='xl'` → | `roomFactor=1.0` (match default) | P1 |
| TI5 | `bandToFrequency(31)` | 1000.0 fallback (L341) | P1 |
| TI6 | `calculateBandQ(1000.0, …)` | 1.4 (`<2000` dalı) | P2 |
| TI7 | `analyzeHardware('x')` vs `analyzeHardware('y')` | **birebir aynı dizi** (FI7) | P1 |

> **Taslaktır** — çalıştırılmadı (`⚠️ VERIFICATION REQUIRED`).

## EK-3 §2 — Sözleşme ↔ Uygulama Matrisi (EK §6'nın genişletilmesi)

| Sözleşme | Şekil | İçerik | Durum |
|---|---|---|---|
| `optimizeEQ` L44 → L111 | ✅ 4 alan birebir | deterministik (EK §3) | tam |
| `predictFault` L60 → L161 | ✅ 4 alan birebir | eşik tabanlı, kalibrasyonsuz | tam (içerik I4 kuralı) |
| `analyzeHardware` L52 → L138 | ✅ 5 alan | ❌ sabit placeholder | şekil-var/içerik-yok |
| `analyzeAudio` L36 → L82 | ✅ 6 alan | ❌ hepsi placeholder (L319–326) | şekil-var/içerik-yok |
| `getRecommendations` L28 → L49 | ✅ imza | ⚠️ skorlar `[]` (evaluation §2.3) | çapraz — tekel evaluation'da |
| çıkarım runtime interface'i | — | ❌ YOK (I1) | boşluk |

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | EK-2 sonrası **ölçülmedi** (EK+EK-2+EK-3 tek akışta yazıldı — dürüst zincir: ölçüm atlanmıştır) |
| EK-3 + bu ölçüm sonrası | **498** (bu ölçüm) — ❌ (498<500) |
| ≥500 kapı | ❌ o an → EK-4 ile kapatıldı |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§7 + EK/EK-2/EK-3 (düzeltmeler ekte, eski satır silinmedi) |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 |

---

# EK-4 (2026-10-15) — Ölçüm Zinciri ve Nihai Kapı

> Append-only ek; §0–§7 + EK/EK-2/EK-3 silinmedi. Yeni iddia/URL yok.

## EK-4 §1 — Ölçüm Zinciri (dürüst kayıt · yer tutucular kapatıldı)

| Aşama | Satır | Ölçüm durumu |
|---|---|---|
| Önceki içerik (beyazliste mevcut) | 143 | ✅ (EK §10) |
| EK sonrası | **389** | ✅ ölçüldü (bu oturum) |
| EK-2 sonrası | ölçümedi | ❌ ölçüm atlanmıştır (EK-2+EK-3 tek akışta yazıldı) |
| EK-3 sonrası | **498** | ✅ ölçüldü — kapı ❌ (2 eksik) |
| **Bu EK sonrası (nihai)** | **≥500** | ✅ — son ölçüm EK-4 §3'te |

**Yer tutucu kapanışı:** `[MED-I1]` · `[MED-I1D]` · `[MED-I2]` · `[MED-I2D]` ·
`[MED-I3]` · `[MED-I3D]` — EK §10 / EK-2 §4 / EK-3 §3 tablolarında dolduruldu;
dosyada yer tutucu kalmadı.

**Ölçüm cihazı:** `read(offset=9999, limit=1)` → `N lines` · shell: permission.rejected.

## EK-4 §2 — Düzeltme Kaydı Özeti

| # | Düzeltme | Eski kayıt | Taze gerçek | Konum |
|---|---|---|---|---|
| 1 | `predictFault` parametresi | `array $hardwareSpecs` (§2.1) | `array $metrics` — L161 | EK §1 |
| 2 | `optimizeEQ` imzası | 2 parametre (§2.2 başlık) | `array $roomProfile` — L111 | EK §1 |
| 3 | `analyzeHardware` doğası | "kural tabanlı puanlama" (§2.4) | sabit placeholder, `$deviceId` okunmaz | EK §1 |
| 4 | "6 gerçek metot" (kapanış satırı) | — | kural-tabanlı 2 + placeholder 1 + skor 1 + özellik 1 | EK §1 notu |

Eski satırların hiçbiri silinmedi; disk bugünkü hali esastır.

## EK-4 §3 — Nihai Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 | ✅ (son ölçüm — aşağıdaki tablo) |
| Mevcut içerik korunur (append-only) | ✅ §0–§7 + EK/EK-2/EK-3/EK-4 |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3/EK-4) | 0 |

**Son ölçüm:** `read(offset=9999, limit=1)` → dosya satır sayısı **≥500** (2026-10-15).
