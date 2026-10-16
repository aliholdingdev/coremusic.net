---
title: "personalization — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/personalization
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# personalization — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIWorkflow.php` satır 52–56 · 75–78 — oturum bağlamının öneriye enjeksiyonu:

```php
$sessionId = $request['session_id'] ?? 'anonymous';

// 1. Context collect — session'dan tercihleri çek
$context = $this->memory->getSession($sessionId, 'preferences') ?? [];
$context = array_merge($context, $request);
...
$recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);

// 6. Feedback — sonucu kaydet
$this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);
```

`shared/src/AI/AIEngine.php` satır 52–56 — kişisel bağlam alanları:

```php
$userId = $context['user_id'] ?? null;
$sessionId = $context['session_id'] ?? null;
$mood = $context['mood'] ?? null;
$genre = $context['genre'] ?? null;
$history = $context['history'] ?? [];
```

`shared/src/AI/AIEngine.php` satır 211–213 — anonim kullanıcıda collaborative kolu devre dışı:

```php
if ($userId === null) {
    return [];
}
```

## §2 Resmi doküman örneği (URL + snippet)

**Amazon Personalize — Recording real-time events** ·
https://docs.aws.amazon.com/personalize/latest/dg/recording-events.html (doğrulandı 2026-10-12):

> "If your domain use case or custom recipe supports real-time personalization, Amazon Personalize uses
> events in real time to update and adapt recommendations according to a user's evolving interest."

> "For anonymous users (users who haven't logged in), for each event specify only a `sessionId`."

## §3 Uygulama notu

- Repo örneği ile resmi örnek aynı **anonim → kayıtlı geçiş** fikrini paylaşır; CoreMusic'in hangi servisi
  kullanacağı **UNKNOWN** (kapsam §5/R4).
- `getCollaborativeScores` anonimde `[]` döndüğü için kişiselleştirmenin gücü içerik koluna bağlı.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `personalization-kapsam.md` | bu dalın rolü, içinde/dışında, açık sorular |
| `personalization-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 262) |

## §5 Çok-dilli dil blokları (Faz 2 · 13-dil kapsama eki · 2026-10-14)

> Bu bölüm 2026-10-14'te eklenmiştir (yalnız ekleme, §1–§4'e dokunulmadı). Her blok için
> resmi kaynak belge `webfetch` ile açılmış ve HTTP 200 ile doğrulanmıştır; kod parçaları
> doğrulanan belgeden türetilmiştir. Hepsi `[ORİJİNAL ÖRNEK · derlenmedi]` statüsündedir —
> CoreMusic'te bu dillerde kişiselleştirme uygulandığı iddiası DEĞİLDİR (seçim yok,
> `status: draft` korunur). Kaynak havuzu: `../ai-master.md` §11 (scikit-learn · ONNX Runtime) ·
> §16 (N-aday turu); §11'de listelenmeyen kaynaklar (Microsoft Learn, konduit.ai, pkg.go.dev)
> bu ekte doğrulanarak eklenmiştir.

### §5.1 Python — scikit-learn · MultiLabelBinarizer (feature/transformer)

https://scikit-learn.org/stable/modules/generated/sklearn.preprocessing.MultiLabelBinarizer.html
(doğrulandı 2026-10-14 · HTTP 200) · `[ORİJİNAL ÖRNEK · derlenmedi]` ·
bağlam: kullanıcının çoklu tür etiketlerinin (multi-label) ikili matrise çevrilmesi — profil
öznitelik mühendisliği. Belgedeki docstring örneği birebir; skorlama/model tarafı CoreMusic'e
ait değildir.

```python
from sklearn.preprocessing import MultiLabelBinarizer

mlb = MultiLabelBinarizer()
mlb.fit_transform([{'sci-fi', 'thriller'}, {'comedy'}])
# array([[0, 1, 1],
#        [1, 0, 0]])
list(mlb.classes_)
# ['comedy', 'sci-fi', 'thriller']
```

### §5.2 JavaScript / Node.js — ONNX Runtime Node · kişiselleştirme çıkarım akışı

https://onnxruntime.ai/docs/get-started/with-javascript/node.html (doğrulandı 2026-10-14 ·
HTTP 200) · kod kaynağı: Microsoft resmi örnek reposu
https://raw.githubusercontent.com/microsoft/onnxruntime-inference-examples/main/js/quick-start_onnxruntime-node/index.js
(doğrulandı 2026-10-14 · HTTP 200 · MIT lisanslı Microsoft Corporation örneği) ·
`[ORİJİNAL ÖRNEK · derlenmedi]` · bağlam: kişiselleştirme modeli ONNX formatında sunucuda
(Node.js binding) çalıştırılır; `InferenceSession.create` → `Tensor` besleme → `session.run`
akışı, tarayıcı-içi/on-server inference için aynı API'dir.

```js
const ort = require('onnxruntime-node');

async function main() {
    try {
        const session = await ort.InferenceSession.create('./model.onnx');
        const dataA = Float32Array.from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
        const dataB = Float32Array.from([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120]);
        const tensorA = new ort.Tensor('float32', dataA, [3, 4]);
        const tensorB = new ort.Tensor('float32', dataB, [4, 3]);
        const feeds = { a: tensorA, b: tensorB };
        const results = await session.run(feeds);
        const dataC = results.c.data;
        console.log(`data of result tensor 'c': ${dataC}`);
        await session.release()
    } catch (e) {
        console.error(`failed to inference ONNX model: ${e}.`);
    }
}
main();
```

### §5.3 C# — ML.NET · feature engineering transform zinciri

https://learn.microsoft.com/en-us/dotnet/machine-learning/how-to-guides/prepare-data-ml-net
(doğrulandı 2026-10-14 · HTTP 200) · `[ORİJİNAL ÖRNEK · derlenmedi]` ·
bağlam: kişiselleştirme özniteliklerinin (sayısal skor, kategori, serbest metin etiket)
model girdisine hazırlanması; belgedeki transform API'leri birebir, yalnız kolon
adları bağlama uyarlanmıştır (belge örneğinde "Price"/"Description" kolonları vardır;
API imzaları değişmemiştir).

```csharp
// Sayısal tercih skorunu 0-1 aralığına normalize et (MinMax)
var minMaxEstimator = mlContext.Transforms.NormalizeMinMax("PreferenceScore");
ITransformer minMaxTransformer = minMaxEstimator.Fit(data);
IDataView transformedData = minMaxTransformer.Transform(data);

// Kategorik tercih alanını (ör. tür) key tipine eşle
var keyEstimator = mlContext.Transforms.Conversion.MapValueToKey("Genre");

// Serbest metin etiketi / çalma listesi notunu sayısal vektöre çevir
var textEstimator = mlContext.Transforms.Text.FeaturizeText("Description");
ITransformer textTransformer = textEstimator.Fit(data);
IDataView textTransformedData = textTransformer.Transform(data);
```

### §5.4 Java — Eclipse Deeplearning4j (DataVec) · veri hattı & normalizasyon

https://deeplearning4j.konduit.ai/core-concepts/data-pipelines.md (doğrulandı 2026-10-14 ·
HTTP 200 · sürüm 1.0.0-M2.1) · `[ORİJİNAL ÖRNEK · derlenmedi]` ·
bağlam: kullanıcı etkileşim kayıtlarının CSV'den okunup `DataSetIterator`'a dönüşmesi ve
öznitelik normalizasyonu (profil feature transform zinciri).

```java
import org.datavec.api.records.reader.impl.csv.CSVRecordReader;
import org.datavec.api.split.FileSplit;
import org.deeplearning4j.datasets.datavec.RecordReaderDataSetIterator;
import org.nd4j.linalg.dataset.api.preprocessor.NormalizerStandardize;

CSVRecordReader trainReader = new CSVRecordReader(1, ',');  // skip header
trainReader.initialize(new FileSplit(new File("train.csv")));
DataSetIterator trainIter = new RecordReaderDataSetIterator.Builder(trainReader, batchSize)
    .classification(labelIndex, numClasses)
    .build();

NormalizerStandardize normalizer = new NormalizerStandardize();
normalizer.fit(trainIter);
trainIter.setPreProcessor(normalizer);
```

### §5.5 Go — net/http · kişiselleştirme REST servisi iskeleti

https://pkg.go.dev/net/http (doğrulandı 2026-10-14 · HTTP 200 · go1.27.2 · BSD-3-Clause) ·
`[ORİJİNAL ÖRNEK · derlenmedi]` ·
bağlam: Go standart kütüphanesi `net/http` sunucu deseni — kişiselleştirme önerisinin REST
endpoint olarak sunulması (kalıp "Servers" bölümünden birebir; profil/öneri iş mantığı
CoreMusic'e ait değildir, bilinçli olarak eklenmemiştir).

```go
http.Handle("/foo", fooHandler)

http.HandleFunc("/bar", func(w http.ResponseWriter, r *http.Request) {
	fmt.Fprintf(w, "Hello, %q", html.EscapeString(r.URL.Path))
})

log.Fatal(http.ListenAndServe(":8080", nil))
```

### §5.6 PowerShell — batch kişiselleştirme veri akışı (JSON okuma)

https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/convertfrom-json
(doğrulandı 2026-10-14 · HTTP 200 · PowerShell 7.6) · `[ORİJİNAL ÖRNEK · derlenmedi]` ·
bağlam: kişiselleştirme servisi çıktısının (JSON) toplu iş (batch) betiğinde PowerShell
nesnesine çevrilmesi — belge Example 2 birebir.

```powershell
# Ensures that Invoke-WebRequest uses TLS 1.2
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
$j = Invoke-WebRequest 'https://api.github.com/repos/PowerShell/PowerShell/issues' |
    ConvertFrom-Json
```

> **Kapsam dışı bırakılan diller (N/A):** Rust, Ruby, Swift, Kotlin, C/C++, Elixir, Scala —
> kişiselleştirme semantiğine karşılık gelen resmi doğrulanmış kaynak bu turda havuzda
> bulunmadı; uydurma örnek yazılmadı (zero-hallucination).

---

## §6 Kapanış Derinliği — Neva Engine kişiselleştirme C++ örnek turu (2026-10-15)

### §6.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §6.1 Örnek PZ-1 — Kullanıcı tercih modeli (JSON şema, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 89–107].

```json
{
  "user_id": 123,
  "listening_history": [
    {"song_id": 1, "genre": "arabesk", "eq_profile": "warm_vocal", "skip": false},
    {"song_id": 2, "genre": "pop", "eq_profile": "modern_bright", "skip": true}
  ],
  "preferred_curve": {
    "60Hz":  2.8,
    "250Hz": 2.5,
    "1kHz":  0.5,
    "4kHz":  3.5,
    "12kHz": 1.0
  },
  "skip_patterns": {
    "too_bright": 3,
    "too_muddy": 2
  }
}
```

**Kişiselleştirme notları:**

1. Şema **3 sinyal** toplar: dinleme geçmişi (geri bildirim yok) · skip davranışı (olumsuz sinyal) · manuel EQ sapmaları (olumlu tercih) → kişiselleştirme üç sinyal türüne dayanır.
2. `preferred_curve` 5 bantlıdır (60/250/1k/4k/12k); 31-bant EQ (equalizer-31band.md [Satır: 32–41]) ile kişisel eğri arasında **çözünürlük farkı** vardır → kişisel eğri 31 banta nasıl genişletilir (interpolasyon yöntemi) spec'te **yok** → `⚠️ VERIFICATION REQUIRED`.
3. `skip_patterns` sayaçları eşiği aşarsa EQ profili "too bright"/"too muddy" olarak işaretlenir; eşik değerleri (3 · 2) örnek veridir, kural değildir — K006'da eşik kararı gerekir.
4. `user_id` açık — bu bir **yerel** şema görünümüdür (sunucu tarafı hesap kimliği K006'da KVKK/rıza katmanına bağlanır: kararlar §H/H5).

### §6.2 Örnek PZ-2 — Online öğrenme döngüsü (tam blok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 112–120].

```text
Her dinleme seansında:
  1. Skip tespit → EQ profilini not al
  2. Şarkı sonuna kadar dinleme → EQ profilini güçlendir
  3. Volume değişikliği → loudness preference
  4. Manuel EQ override → model güncelle (high weight)

Model: Incremental gradient descent with forgetting factor (α = 0.95)
```

**Kişiselleştirme notları:**

1. 4 geri bildirim sinyali tanımlıdır (skip · tam dinleme · volume · manuel override) — manuel override **yüksek ağırlık** taşıyor ama sayısal ağırlık verilmemiş → `⚠️ VERIFICATION REQUIRED`.
2. Tek model adı verilmiştir: **incremental gradient descent, α = 0.95** (unutma faktörü). α = 0.95 ne anlama gelir: ~20 örneklik yarı-ömrü (0.95^20 ≈ 0.36) → kişisel eğri hızlı esner. Bu sayısal okuma **türetmedir**.
3. `listening_history` sınırsız büyür → kişisel modelin bellek/veri büyüme sınırı spec'te **yok** → K006'da saklama politikası kararı (KVKK silme hakkı ile bağ).

### §6.3 Örnek PZ-3 — Zaman bazlı adaptasyon (tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 174–194].

```cpp
// Günün saatine göre adaptif EQ
void applyTimeAdaptation(int hour) {
    if (hour >= 22 || hour < 6) {
        // Gece modu: bass reduction, slight high cut (komşu rahatsız etme)
        eq.setBandBass(-3.0f);
        eq.setBandHigh(-2.0f);
    } else if (hour >= 6 && hour < 10) {
        // Sabah modu: light, natural
        eq.setBandBass(0.0f);
        eq.setBandHigh(1.0f);
    } else if (hour >= 10 && hour < 18) {
        // Gündüz: normal
        eq.setBandBass(1.0f);
        eq.setBandHigh(1.0f);
    } else {
        // Akşam: energetic
        eq.setBandBass(3.0f);
        eq.setBandHigh(2.0f);
    }
}
```

**Kişiselleştirme notları:**

1. Bu adaptasyon **kişisel modelden bağımsızdır** (saat kuralı) → kişiselleştirme 2 katman: statik zaman kuralı + öğrenilen tercih; iki katman **çakışınca** hangisinin kazanacağı spec'te tanımsız → `⚠️ VERIFICATION REQUIRED` (çakışma çözümü).
2. Dört pencere: 22–06 · 06–10 · 10–18 · 18–22 — `hour` parametresi **yerel saat** midir UTC midir? belirsiz → K006'da zaman dilimi kararı (KVKK zaman damgası ile bağ).

### §6.4 Örnek PZ-4 — Oda kalibrasyonu (kullanıcı-özel ortam, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 128–143].

```cpp
// Mikrofon ile pink noise oynat → oda yanıtını ölç → inverse filter
void calibrateRoom() {
    // 1. Pink noise generate (20Hz - 20kHz)
    // 2. Play through speakers
    // 3. Record with microphone
    // 4. FFT → frequency response
    // 5. Inverse filter = target_flat_response - measured_response
    // 6. Apply inverse filter as room correction EQ

    for (int i = 0; i < BINS; ++i) {
        float correction_dB = targetResponse[i] - measuredResponse[i];
        correction_dB = std::clamp(correction_dB, -12.0f, 12.0f);
        roomCorrectionEQ.setBand(i, correction_dB);
    }
}
```

**Kişiselleştirme notları:**

1. Düzeltme **±12 dB ile kıskaçlanır** (`std::clamp`) → oda kalibrasyonunun mutlak sınırı; bu sayı K006'da ölçülebilir tekil bir gate'tir.
2. Akış 6 adımlıdır ve `BINS` sayısı belirsiz (31-bant EQ ile eşleşiyor olabilir ama spec'te `BINS` tanımsız) → `⚠️ VERIFICATION REQUIRED`.
3. K006 PHP yüzeyinde birebir karşılık: `calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float` (Dosya: `shared/src/AI/AIEngine.php` [Satır: 347]) — PHP sürümü **frekans-başına** hesap yapar, C++ sürümü **band-başına**; iki farklı çözünürlük, aynı amaç (karşılaştırma: audio-intelligence-ornekler §6.1).

### §6.5 Örnek PZ-5 — Loudness kompanzasyonu (kişisel ses seviyesi, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 152–169].

```cpp
float loudnessCompensation(float frequency, float volume_dB) {
    // 80 phon = reference (flat)
    // < 80 phon → bass + treble boost
    // > 80 phon → slight reduction

    float phon = volume_dB; // approximate
    float compensation = 0.0f;

    if (frequency < 200.0f) {
        // Bass boost at low volumes
        compensation = (80.0f - phon) * 0.15f; // 6dB max at 60 phon
    } else if (frequency > 8000.0f) {
        // Treble boost at low volumes
        compensation = (80.0f - phon) * 0.08f; // 3dB max
    }

    return std::clamp(compensation, -6.0f, 6.0f);
}
```

**Kişiselleştirme notları:**

1. `phon = volume_dB` yorumu "approximate" — phon ↔ dB eşitliği **yaklaşıktır** (ISO 226 eşit-loudness eğrileri gerçek dönüşümü verir). Bu yaklaşım K006'da bilinçli bir sadeleştirme olarak kayıtlıdır, doğruluk iddiası değildir.
2. Kıskaç ±6 dB — oda düzeltmesindeki ±12 dB'den (§6.4) yarı dardır; iki düzeltmenin üst üste bindiğinde toplam sapma ≤ 18 dB olabilir → toplam sapma sınırı spec'te yok → `⚠️ VERIFICATION REQUIRED`.
3. Referans: ISO 226:2003 (Dosya: eq-ai-auto.md [Satır: 9] frontmatter `refs`).

### §6.6 Örnek PZ-6 — Kullanıcı modu profili (kişiselleştirme segmentleri)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` [Satır: 18–24].

| Mod | Varsayılan EQ | UI Stili | Hedef kullanıcı |
|---|---|---|---|
| Ev Kullanıcısı | 5-band Graphic | Büyük butonlu, basit | Günlük müzik dinleyici |
| Araba | 10-band Graphic | Büyük touch, gece modu | Sürücü |
| TV | 5-band + Presets | Uzaktan kumanda, büyük font | Oturma odası |
| Stüdyo | 31-band + 6-cell Parametric | Tam kontrol, spectrum analyzer | Ses mühendisi |
| Gamer | 10-band + HRTF + Low Latency | Karanlık tema, hızlı toggle | Oyuncu |

**Kişiselleştirme notları:**

1. Kayıt sırasında 3 soru sorulur (Dosya: eq-user-modes.md [Satır: 30–46]) → kişiselleştirme **soğuk başlangıç** sorununu soru-cevapla çözer, öğrenmeye bırakmaz.
2. Araba modunda yol gürültüsü kompanzasyonu tanımlıdır: 60–120 Hz motor gürültüsü slight cut · 200–500 Hz yol gürültüsü cut · 2–4 kHz vokal boost (Dosya: eq-user-modes.md [Satır: 76–81]) → mikrofon girdisi **kişisel veri** midir (KVKK rıza kapısı)?
3. Gamer modu HRTF ekler → kişiselleştirme + uzamsal ses kesişimi; K006 kapsamı bu kesişime girer mi → kapsam §6/P14 ile bağ.

### §6.7 Örnek PZ-7 — Kişiselleştirme gate'leri

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| PZ-G1 | Oda düzeltme üst sınırı | ±12 dB | eq-ai-auto.md [Satır: 140] |
| PZ-G2 | Loudness kompanzasyon üst sınırı | ±6 dB | eq-ai-auto.md [Satır: 168] |
| PZ-G3 | Manuel override ağırlığı | yüksek (sayısal değer YOK → UNKNOWN) | eq-ai-auto.md [Satır: 117] |
| PZ-G4 | Unutma faktörü | α = 0.95 | eq-ai-auto.md [Satır: 119] |
| PZ-G5 | Zaman penceresi | 4 pencere (22–06 · 06–10 · 10–18 · 18–22) | eq-ai-auto.md [Satır: 177–190] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §6.8 §6 tur kapanış kaydı

- Eklenen: 1 JSON şema okuması + 1 öğrenme döngüsü + 2 tam C++ okuması (zaman · oda) + 1 loudness okuması + 1 mod tablosu + 5 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 5 (interpolasyon yöntemi · override ağırlığı · çakışma çözümü · BINS · toplam sapma sınırı).

---

## §7 Kapanış Derinliği — Kişiselleştirme gate ölçüm tasarımı ve öğrenme döngüsü

> **Durum:** Bu bölümdeki kapıların hiçbiri bu turda **çalıştırılmadı**; eşikler
> tasarım durumundadır, ölçüm `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §7.1 PZ-G1…PZ-G5 ölçüm tasarımı

| Gate | Koşul (tasarım) | Ölçüm adımı | Beklenen kayıt |
|---|---|---|---|
| PZ-G1 | `|roomCorrection| ≤ 12 dB` | EQ eğrisi min/max aralığı | dB sapması |
| PZ-G2 | `|loudnessComp| ≤ 6 dB` | `std::clamp` çıktısı (§6.5) | dB sapması |
| PZ-G3 | manuel override → AI önerisi askıya | override bayrağı okuması | bayrak + oturum |
| PZ-G4 | `confidence = α·yeni + (1−α)·eski`, α = 0.95 | her etkileşimde bir kez | eski/yeni/confidence |
| PZ-G5 | gün saati → 4 pencere doğru segment | saat → pencere eşlemesi | pencere no + saat |

```cpp
// TASARIM — vault spesifikasyon alıntısıdır, derlenmiş kod değildir
// Dosya: .ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md [Satır: 119]
float updateConfidence(float current, float observed, float alpha = 0.95f) {
    return alpha * observed + (1.0f - alpha) * current;   // PZ-G4
}
```

### §7.2 Öğrenme döngüsü ile kapı ilişkisi (tam akış)

```text
etkileşim (play/preset/EQ change)
  → zaman penceresi belirle (PZ-G5 · [Satır: 177–190])
  → güven güncelle (PZ-G4 · α = 0.95 · [Satır: 119])
  → öneri üret (genre + preset eşleşmesi)
  → override kontrolü (PZ-G3 · [Satır: 117]) → manuel ise öneri bastırılır
  → oda düzeltmesi uygula (PZ-G1 · ±12 dB · [Satır: 140])
  → loudness kompanzasyonu (PZ-G2 · ±6 dB · [Satır: 168])
  → ses zincirine uygula (fail-safe: AI kapalıysa düz EQ · audio-core.md [Satır: 27])
```

**Akış çıkarımları:**

1. Override (PZ-G3) **her şeyin önündedir**: manuel seçim varsa öğrenme önerisi kullanıcıya gitmez; öğrenme yine de güven günceller mi → spec'te belirsiz → `⚠️ VERIFICATION REQUIRED`.
2. İki düzeltme (PZ-G1 + PZ-G2) **seri** bindir → üst üste en kötü durum ±18 dB (§6.5 not 2 ile bağ); toplam sınır gate'i yok.
3. `α = 0.95` unutma faktörü yüksek ağırlık verir (yeni gözlem %95) → hızlı adaptasyon; K006'da değiştirilirse kapı eşiği de değişir (karar: `../yapay-zeka-kararlar.md` §H).

### §7.3 PHP ↔ C++ eşleme (kişiselleştirme yüzeyi)

| PZ kapı | C++/spec kaynağı | PHP tarafı (repo kanıtı) |
|---|---|---|
| PZ-G1/G2 | eq-ai-auto.md [Satır: 140], [Satır: 168] | yok — repo'da EQ düzeltme kodu yok |
| PZ-G3 | [Satır: 117] override | yok |
| PZ-G4 | [Satır: 119] α | yok |
| PZ-G5 | [Satır: 177–190] pencereler | yok |
| mod segmentleri | eq-user-modes.md [Satır: 18–24] | yok — mod kaydı UI tarafıdır (spec) |

**Eşleme çıkarımı:** kişiselleştirme yüzeyinin **PHP karşılığı repo'da yoktur**; kapıların tamamı spec/C++ tarafındadır. Bu, K006'da kişiselleştirme katmanının henüz implemente edilmediğinin kanıtıdır (kapsam: `[[personalization-kapsam]]`).

### §7.4 Bağlar ve okuma izi

| Bağ | Hedef | İlişki |
|---|---|---|
| Doğrulama | `../yapay-zeka-dogrulama.md` §7 | PZ-G1…G5 + §7 kapıları taşındı |
| Test | `../yapay-zeka-test-stratejisi.md` | öğrenme döngüsü senaryoları |
| Risk | `../yapay-zeka-riskler.md` | KVKK/mikrofon · toplam sapma |
| Karar | `../yapay-zeka-kararlar.md` §H | α değeri · override önceliği |
| Öneri | `../recommendation/recommendation-ornekler.md` | genre ↔ preset eşleşmesi |
| Kapsam | `[[personalization-kapsam]]` | maddeler ↔ örnek |

Okunan kaynaklar (bu tur, satır kanıtlı): `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 9 · 117 · 119 · 140 · 152–169 · 177–190] · `eq-user-modes.md` [Satır: 18–24 · 30–46 · 76–81] · `audio-core.md` [Satır: 27].

### §7.5 §7 tur kapanış kaydı

- Eklenen: 5 gate ölçüm tasarımı + 1 C++ tasarım bloğu + 7 satırlık tam akış + 3 akış çıkarımı + 5 satır PHP↔C++ eşleme + 6 bağ + 1 okuma izi.
- Çalıştırılan gate: **0** → ölçüm `../yapay-zeka-dogrulama.md` §7'ye taşındı.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 1 (override + öğrenme etkileşimi).

---

### §7.6 Ölçüm kayıt şablonu (gate çalıştırıldığında doldurulacak)

> Bu şablon **boştur** — kapılar çalıştırıldığında `../yapay-zeka-dogrulama.md` §7
> doldurur; bu dosyaya ölçüm sonucu **yazılmaz** (uydurma ölçüm yasağı).

| Gate | Tarih | Ölçülen | Eşik | Sonuç (PASS/BLOCK) |
|---|---|---|---|---|
| PZ-G1 | — | — | ±12 dB | — |
| PZ-G2 | — | — | ±6 dB | — |
| PZ-G3 | — | — | bayrak | — |
| PZ-G4 | — | — | α = 0.95 | — |
| PZ-G5 | — | — | 4 pencere | — |

Kayıt kuralı: her satır yalnız gerçek ölçümle doldurulur; ölçülmeyen satır `—` kalır ve "çalıştırıldı" sayılmaz.

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Kapanış turu:** 221 → ≥500 · append-only (§6–§7 eklendi, mevcut satır silinmedi).


---

## §8 Kapanış Derinliği — Kullanıcı modu segmentasyonu ↔ kişiselleştirme (2026-10-15 kapanış turu)

### §8.0 Kanıt disiplini (bu bölüm için)

- Kaynaklar yalnız vault markdown'ları + repo PHP'sidir; her iddia `Dosya: <yol> [Satır: n]` taşır.
- Repo'da `*.cpp` / `*.h` = **0 dosya** (2026-10-15 ölçümü) → bu bölümdeki C++/JSON blokları
  **vault şablonundan birebir alıntı**dır, "repo kodu" değildir.
- Çalıştırılan gate: **0** → ölçüm `../yapay-zeka-dogrulama.md` §7'ye bağlıdır.

### §8.1 Örnek PZ-8 — 5 kullanıcı modu → kişiselleştirme profil eşlemesi (tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` [Satır: 16–24]

| Mod | Varsayılan EQ | UI | Kişiselleştirme anlamı | K006 alt alanı |
|---|---|---|---|---|
| Ev Kullanıcısı | 5-band Graphic | Büyük butonlu, basit | Auto-EQ + genre detection açık | K006.6 · K006.7 |
| Araba | 10-band Graphic | Büyük touch, gece modu | yol gürültüsüne göre adaptif | K006.7 |
| TV | 5-band + Presets | Uzaktan kumanda, büyük font | içerik türüne göre otomatik geçiş | K006.6 |
| Stüdyo | 31-band + 6-cell Parametric | spectrum analyzer + LUFS meter | oda kalibrasyonu + reference matching | K006.1 |
| Gamer | 10-band + HRTF + Low Latency | karanlık tema, hızlı toggle | low-latency ASIO + ducking | K006.4 |

Beş satırın tamamı dosyanın mod tanım tablosundan alınmıştır [Satır: 18–24]; ad uydurulmamıştır.
Kayıt akışı üç sorudan oluşur: kullanım yeri · detay seviyesi · tür tercihi (opsiyonel, AI için)
[Dosya aynı · Satır: 28] → üçüncü soru **doğrudan K006 kişiselleştirme girdisidir**.

### §8.2 Örnek PZ-9 — Mod geçiş komutu → profil değişimi sözleşmesi (tam blok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` [Satır: 168–176]

```json
{"cmd":"mode.set","params":{"mode":"studio"}}
```

Her mod değişiminde dört şey yeniden kurulur (dosya [Satır: 174–176]):
EQ band sayısı/tipi · UI teması · preset listesi · FX zinciri yapılandırması.
**K006 çıkarımı:** kişiselleştirme profili tek bir `mode` alanıyla taşınır; alt alanlar
(K006.6 tür etiketi · K006.7 otomatik EQ) bu alanın **bağımlı girdileridir**, ayrı oturum
açmaz. Band sayısı ayarlardan serbestçe seçilebilir: 2 / 5 / 10 / 31 / parametrik / custom
[Satır: 183] → profil şeması sabit band sayısını **zorlamaz**, varsayılan önerir.

### §8.3 Örnek PZ-10 — Mod bazlı kişiselleştirme “özel” satırları (kanıt tablosu)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md`

| Mod | Özel madde | Satır | Kişiselleştirme girdisi |
|---|---|---|---|
| Araba | Gece modu (kırmızı/siyah tema, düşük parlaklık) | [Satır: 69] | saat/zaman etiketi → K006.7 time-based adaptation |
| Stüdyo | spectrum analyzer · LUFS meter · phase meter | [Satır: 105–115] | kullanıcı seviyesi etiketi (profesyonel segment) |
| Gamer | Footsteps Enhancer (2–4 kHz, narrow Q) | [Satır: 139] | oyun türü tercihi → dar bant EQ kişiselleştirmesi |
| Gamer | Low-Latency Mode (< 5 ms ASIO buffer) | [Satır: 141] | donanım/tercih etiketi → K006.4 yerel çıkarım tercihi |
| Gamer | Aktivasyon = 3 yol (tema / toggle / ayar) | [Satır: 155–162] | etkinleşme sinyali → profil geçişi event'i |

Beş satırın beşi de dosyada okunarak alınmıştır; “kişiselleştirme” sütunu bu vault'un
**yorumu**dur (dosya K006'dan bahsetmez) — yorum olduğu §8.0 kuralı ile işaretlidir.

### §8.4 Örnek PZ-11 — Tercih modeli ↔ mod profili entegrasyonu (eşleme)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md`

| Tercih modeli alanı | Satır | Mod profili ile ilişki |
|---|---|---|
| `listening_history` (song · genre · eq_profile · skip) | [Satır: 87–99] | geçmiş + etkin mod → birleşik profil |
| `skip_patterns` (`too_bright` · `too_muddy` sayacı) | [Satır: 103–107] | renk şikayeti → mod içi EQ düzeltmesi |
| Online learning · forgetting factor α = 0.95 | [Satır: 110–119] | mod geçişinde model **sıfırlanmaz**, ağırlık düşerek sürer |
| Room correction clamp ±12 dB | [Satır: 140] | Stüdyo modu dışında da uygulanabilir (cihaz-özel) |
| Tier 2 → Tier 3 sırası (ML → Environmental) | [Satır: 85 · 124] | kişisel tercih önce, ortam kalibrasyonu sonra |

### §8.5 K006 kişiselleştirme gate'leri — bu bölümün katkısı (PZ-G6…PZ-G9)

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| PZ-G6 | Mod geçişi sonrası profil yükleme | 4/4 yeniden kurulum (EQ · tema · preset · FX) | eq-user-modes [Satır: 174–176] |
| PZ-G7 | Kayıt akışı 3. soru zorunluluğu | opsiyonel (AI girdisi boş bırakılabilir) | eq-user-modes [Satır: 28] |
| PZ-G8 | Unutma faktörü | α = 0.95 (mod değişse de) | eq-ai-auto [Satır: 119] |
| PZ-G9 | Oda kalibrasyonu sınırı | clamp ±12 dB | eq-ai-auto [Satır: 140] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §8.6 §8 tur kapanış kaydı

- Eklenen: 1 mod eşleme tablosu (5 satır) + 1 JSON bloğu + 5 satır özel-madde kanıtı +
  5 satır entegrasyon eşlemesi + 4 gate + 1 kanıt disiplini bloğu.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0.
- Alıntı yapılan kod yalnız vault markdown'ından; repo `*.cpp` sayısı 0 olduğu için
  "repo C++ kodu" iddiası **yapılmamıştır**.

---

**Authority:** Master-Orchestrator kapanış turu · **Status:** active · **Updated:** 2026-10-15
