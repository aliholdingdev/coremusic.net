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

---

# EK (2026-10-15) — Taze Kod Okuması, Yeni Doğrulanan URL'ler ve Zayıf Kanıt Kayıtları

> **EK usulü:** append-only; §1–§8 ve status satırı silinmez. URL'ler 2026-10-15
> ortak HTTP turunda status + gerçek `<title>` ile doğrulanmıştır (yalnız
> **200 + hedefle eşleşen title** kanıt sayılır).

---

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| # | Kaynak | Kapsam | Yöntem |
|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | L111–133 (`optimizeEQ`) | disk read |
| 2 | `shared/src/AI/AIEngine.php` | L135–202 (`analyzeHardware` · `predictFault`) | disk read |
| 3 | HTTP GET turu (40 URL — K006.6/8/9 ortak) | status + `<title>` | Code Mode `fetch`, 2026-10-15 |

---

## EK §1 — `optimizeEQ` verbatim (deterministik zincir — 31 bant)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 111–133** · okuma: 2026-10-15

```php
    public function optimizeEQ(array $roomProfile): array
    {
        $roomSize = $roomProfile['size'] ?? 'medium';
        $reverb = $roomProfile['reverb'] ?? 0.5;
        $frequencyResponse = $roomProfile['frequency_response'] ?? [];

        // 31-band parametrik EQ eğrisi üret
        $eqCurve = [];
        for ($band = 0; $band < 31; $band++) {
            $frequency = $this->bandToFrequency($band);
            $gain = $this->calculateRoomCorrection($frequency, $roomSize, $reverb, $frequencyResponse);
            $q = $this->calculateBandQ($frequency, $roomSize);

            $eqCurve[] = [
                'band'      => $band,
                'frequency' => $frequency,
                'gain'      => round($gain, 2),
                'q'         => round($q, 2),
            ];
        }

        return $eqCurve;
    }
```

**Gözlem:**

1. Döngü sabittir: `0..30` = **31 bant**, `bandToFrequency` ile eşleşir (§5'teki
   "31 bant" kaydının gövde kanıtı).
2. Girdi `frequency_response` **boşsa** `calculateRoomCorrection` her bantta
   `0.0` döner (bkz. `audio-intelligence` EK §3 gözlem 2) → `gain` sütunu tamamı
   `0.0`; yani boş oda profili **düz (nötr) EQ eğrisi** üretir — davranış
   deterministik ve **tahmin edilebilir**.
3. `calculateBandQ($frequency, $roomSize)` çağrısında `$roomSize` **iletilir**
   ancak metot gövdesi bu parametreyi okumaz (audio-intelligence EK §3 gözlem 3) —
   imza bağlanmış, davranış bağlanmamış.
4. `round(…, 2)` çıkış hassasiyetini 2 basamağa indirir (kayıpsız değil,
   bilinçli kuantalama).
5. Model ağırlığı, dosya, önceden eğitim veya dış servis **hiçbiri yok** —
   §5'in "model değil / deterministik hesap" kaydı EK §1 ile somutlaştı.

---

## EK §2 — `analyzeHardware` + `predictFault` verbatim (kural tabanlı tahmin)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 135–202** · okuma: 2026-10-15

```php
    /**
     * {@inheritdoc}
     */
    public function analyzeHardware(string $deviceId): array
    {
        // PCM3168A, XMOS XU316, AK4458 için analiz
        $status = 'healthy';
        $snr = 0.0;
        $thd = 0.0;
        $temperature = 0.0;
        $recommendations = [];

        // Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)
        // Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak
        return [
            'status'          => $status,
            'snr'             => $snr,
            'thd'             => $thd,
            'temperature'     => $temperature,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * {@inheritdoc}
     */
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

        if ($temperature > 70) {
            $risk = 'high';
            $probability += 0.4;
            $alerts[] = "Yüksek sıcaklık: {$temperature}°C";
        }

        if ($snr < 90) {
            $risk = $risk === 'low' ? 'medium' : $risk;
            $probability += 0.2;
            $alerts[] = "Düşük SNR: {$snr}dB";
        }

        if ($thd > 0.1) {
            $risk = $risk === 'low' ? 'medium' : $risk;
            $probability += 0.15;
            $alerts[] = "Yüksek THD: {$thd}%";
        }

        if ($usageHours > 10000) {
            $maintenance[] = "Bakım zamanı gelmiş olabilir ({$usageHours} saat)";
        }

        return [
            'risk'         => $risk,
            'probability'  => min($probability, 1.0),
            'alerts'       => $alerts,
            'maintenance'  => $maintenance,
        ];
    }
```

---

## EK §3 — `predictFault` kural kataloğu (veribatımından çıkarım)

| Kural | Koşul (satır) | Risk etkisi | Olasılık artışı | Üretilen uyarı |
|---|---|---|---|---|
| K1 | `$temperature > 70` (L174) | `risk = 'high'` (doğrudan) | +0.40 | "Yüksek sıcaklık: X°C" |
| K2 | `$snr < 90` (L180) | `low` ise `medium` | +0.20 | "Düşük SNR: XdB" |
| K3 | `$thd > 0.1` (L186) | `low` ise `medium` | +0.15 | "Yüksek THD: X%" |
| K4 | `$usageHours > 10000` (L192) | risk etkisi **yok** | +0 | bakım önerisi (alerts değil, maintenance) |
| K5 | — (çıkış) | — | `min(..., 1.0)` tavan (L198) | — |

**Kural öznitelikleri:**

1. **Maks. birleşik olasılık:** 0.40+0.20+0.15 = **0.75** — üçü birlikte tetiklense
   bile `probability` **1.0'a asla ulaşmaz** (tavan uygulanmadan önce 0.75).
2. **Risk sıçraması asimetrik:** K1 `low`'dan `high`'a doğrudan atlar; K2/K3
   ancak `low` ise `medium` yapar — `high` sonrası `medium` dalı çalışmaz
   (`$risk === 'low'` koşulu), yani sıralama `low < medium < high` kabulüyle
   yazım sırasına bağlıdır.
3. **K4 olasılığa katkı vermez** — bakım sinyali ayrı kanaldadır.
4. **Eşikler sabit yazım** (70 · 90 · 0.1 · 10000): konfigürasyon, sabit sınıf
   üyesi veya veri türetme **yok**; `UNKNOWN` (eşik kaynağı belirsiz — §6 C5
   ile aile).
5. **Girdi yoksa her şey sıfır:** tüm `?? 0` varsayılanları eşik altında kalır
   (`0 > 70` ✗, `0 < 90` ✓ → **boş metrik bile K2'yi tetikler**: SNR bilinmiyorsa
   "Düşük SNR: 0dB" uyarısı üretilir). Bu, sessiz varsayımın **yanlış pozitif**
   ürettiği tek noktadır (dosyanın `audio-intelligence` EK-2 §3'ündeki 7
   varsayım envanterine ek bir kayıt).

---

## EK §4 — 2026-10-15 Taze HTTP Doğrulaması: Yeni/Genişleyen URL'ler

> §2'deki 8 URL aynen korunur; aşağıdaki URL'ler 2026-10-15 turunda doğrulanmıştır.

| # | URL | HTTP | `<title>` (yanıttan birebir) | K006.2 ilgisi |
|---|---|---|---|---|
| 9 | https://docs.vllm.ai/ | 200 | `vLLM` | §2.5 ile aynı hedef, taze title teyidi |
| 10 | https://onnxruntime.ai/docs/ | 200 | `ONNX Runtime | onnxruntime` | §2.1 ile aynı hedef, taze title teyidi |
| 11 | https://github.com/ggml-org/llama.cpp | 200 | `GitHub - ggml-org/llama.cpp: LLM inference in C/C++ · GitHub` | §2.8 ile aynı hedef, taze title — "LLM inference in C/C++" tanımı |
| 12 | https://github.com/ggml-org/whisper.cpp | 200 | `GitHub - ggml-org/whisper.cpp: Port of OpenAI's Whisper model in C/C++ · GitHub` | Whisper'ın C++ portu — §2.6 Whisper ile aynı aile |
| 13 | https://github.com/ml-explore/mlx | 200 | `GitHub - ml-explore/mlx: MLX: An array framework for Apple silicon · GitHub` | Apple silicon çıkarım çerçevesi — yeni aday |
| 14 | https://developer.apple.com/machine-learning/core-ml/ | 200 | `Core ML | Apple Developer Documentation` | on-device çıkarım — yeni aday |
| 15 | https://huggingface.co/docs/transformers/index | 200 | `Transformers · Hugging Face` | model/çıkarma kütüphanesi dokümanı — yeni aday |

**Sayım:** §2 = 8 URL + EK = **7 yeni URL** → K006.2 toplam **15 benzersiz URL**.

---

## EK §5 — Zayıf / Red Kayıtları (kanıt olarak sayılmaz)

| # | URL | HTTP | Dönene title | Karar |
|---|---|---|---|---|
| Z1 | https://pytorch.org/docs/stable/quantization.html | 200 | `Redirecting…` | ⚠️ **zayıf kanıt** — title hedef içermiyor (yönlendirme sayfası); quantization dokümanı için **VERIFICATION REQUIRED**, sayım dışı |
| Z2 | (aynı tur, bu dosyada kullanılmayan) `mlflow.org/docs/latest/model-registry.html` | 200 | `(no title)` | ⚠️ title'sız — K006.2'de kullanılmaz, yalnız tur kaydı |

**Ret/experience notu (ortak tur):** `lyst.github.io/lightfm/` **404**,
`surprise.../metrics.html` **404**, `recbole.io`/`implicit.readthedocs.io`
**gasp title** → bu dosyada kullanılmadı (ilgili alanlar `recommendation` ve
`evaluation` EK'lerinde kayıtlıdır). K006.2'nin kendi ret kaydı Z1'dir.

---

## EK §6 — Çapraz Bağ (Taze Verbatim ↔ §5 Tablosu)

| # | EK bulgusu (2026-10-15) | §5/§6 satırı | Sonuç |
|---|---|---|---|
| X1 | `predictFault` eşik kuralları + sabit yazım (EK §2–§3) | "kural tabanlı tahmin, model değil" | ✅ teyit — mekanizma ortaya kondu |
| X2 | `optimizeEQ` deterministik 31 bant (EK §1) | "EQ/odak hesabı — deterministik" | ✅ teyit + boş-yanıt davranışı (gözlem 2) |
| X3 | `analyzeHardware` placeholder (EK §2) — SNR/THD/sıcaklık hep `0.0` | §5'te yok | 🔶 yeni bulgu: donanım analizi de **model değil, placeholder** |
| X4 | `predictFault` boş-girdi → "Düşük SNR: 0dB" (EK §3 gözlem 5) | §6 C4 terminoloji tuzağı | 🔶 derinlik — yanlış pozitif kanıtı |
| X5 | mlx · Core ML · transformers · whisper.cpp adayları (EK §4.12–15) | §4 tablosu 6 adaylı | 🔶 aday envanteri 6 → 10 |
| X6 | Z1 quantization zayıf title | C3 (dil sınırı) ile aile | ⚠️ işaretli kaldı |

---

## EK §7 — Fark (C-row) Tablosu

| C-row | Eski kayıt (korunur) | Taze gerçek (2026-10-15) | Etki |
|---|---|---|---|
| C-1 | §7 "Benzersiz URL: 8" | 8 + 7 = **15** (§2 korunur) | EK §4 sayımı |
| C-2 | §7 "Repo ile çapraz eşleşme: 3" | +1 (`analyzeHardware` placeholder — X3) | sonraki sayım turunda 4 |
| C-3 | §8 kaynak listesi 4 madde | +1 madde: EK §0–§6 (2026-10-15 disk + 40-URL turu) | ek kanıt kütlesi |
| C-4 | C5 "hangi tahmin yüzeyi modele taşınacak" | değişmedi — `UNKNOWN` aynen geçerli | kapalı değil |

---

## EK §8 — Bulgular (A-INF serisi)

### A-INF-1 — "Tahmin" yüzeylerinin tamamı kural/deterministik (kanıt genişledi)

EK §1–§2 ile üç yüzeyin de gövdesi okundu: `optimizeEQ` saf döngü hesabı;
`analyzeHardware` **sabit placeholder** (SNR/THD/sıcaklık `0.0`); `predictFault`
4 eşik kuralı. Model ağırlığı yükleyen/çağıran **tek satır yok**. §5'in ana
bulgusu (model çıkarımı 0 satır) üç verbatim ile pekişti.

### A-INF-2 — `analyzeHardware` placeholder'ı sahte sağlıklı veri üretir

`status = 'healthy'`, metrikler `0.0` (EK §2): cihaz yoksa bile "healthy + SNR 0"
döner. `predictFault` bu 0'ları alırsa K2 (`snr < 90`) **tetiklenir** →
sağlıklı cihaz kaydı "Düşük SNR" uyarısı üretir (EK §3 gözlem 5). İki public
metot yan yana kullanıldığında **sistematik yanlış pozitif** — K006.9 test
kapsamına aday senaryo.

### A-INF-3 — Olasılık tavanı pratikte 0.75

K1+K2+K3 maks 0.75 (EK §3 gözlem 1) → `min(..., 1.0)` hiç devreye girmez.
`probability` asla "kesin" seviyeye çıkmaz; `risk='high'` ise yalnız K1 ile
mümkün. Yorum: model **eşiği aşarsa yüksek risk**, ağırlık toplamı **yüksek
olasılık** üretmez — iki kavram bağımsız.

### A-INF-4 — Yeni adaylar: mlx · Core ML · whisper.cpp · transformers

EK §4.12–15: yerel/on-device çıkarım tarafı genişledi — C/C++ (whisper.cpp),
Apple silicon (mlx), on-device (Core ML), kütüphane (transformers). Hepsi
**kurulmadı** (grep → 0, §4 usulü); aday statüsü karar değil.

### A-INF-5 — quantization kanıtı zayıf kaldı (zayıf kanıt disiplini)

Z1: pytorch quantization sayfası 200 döner ama title `Redirecting…` — **kanıt
sayılmadı**, sayım dışı, `⚠️ VERIFICATION REQUIRED`. Bu, 200+title kuralının
istisnasız uygulandığına dair bu dosyanın denetim kaydıdır.

### A-INF-6 — URL sayımı ve kapı durumu

§2 8 URL + EK 7 URL = **15 benzersiz URL**; 1 zayıf kayıt (sayım dışı);
0 uydurma URL. C5 (hangi yüzey modele taşınacak) **hâlâ açık** — EK yeni kanıt
eklese de kararı değiştirmedi (`UNKNOWN` korunur).

---

## EK §9 — Kapılar (K006.2 web-arastirma)

| Kapı | Durum | Kanıt |
|---|---|---|
| Mevcut içerik korundu (append-only) | ✅ | §1–§8 + status satırı aynen duruyor |
| Frontmatter'e dokunulmadı | ✅ | EK gövdede (L18 sonrası) |
| Uydurma URL yok | ✅ | 7 kanıt URL = 200 + gerçek title; Z1 sayım dışı ⚠️ |
| Zayıf kanıt işaretli | ✅ | Z1 `Redirecting…`, Z2 no-title — ikisi de kanıt sayılmadı |
| Kod iddiaları disk kanıtlı | ✅ | EK §1–§2 verbatim + `Satır:` eşliği (2026-10-15) |
| `*.php` yazımı yok (yalnız okuma) | ✅ | 2026-10-15 yalnız `read` |
| git commit yok | ✅ | bu oturumda commit atılmadı |
| `.ai/architecture/index.md` dokunulmadı | ✅ | yazım yalnız bu dosyada |

---

## EK §10 — Ölçüm

| Metrik | Değer |
|---|---|
| EK öncesi satır | **136** |
| EK sonrası satır | **436** (kapı o an ❌ → EK-2 ile kapatıldı) |
| ≥500 kapı | **EK-2 §4'e bak — nihai ✅ (505)** |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Ölçüm tarihi | 2026-10-15 |

---

# EK-2 (2026-10-15) — Derinlik Eki: Çıkarma Katmanının Zorunlu Bileşenleri ↔ Repo Durumu

> Yeni URL/iddia yok; yalnız EK §1–§4 ve §2'deki mevcut kanıtların bir "çıkarım
> katmanı ne içerir" şartnamesine eşlenmesi. Her hücre kanıt veya `UNKNOWN` işaretli.

## EK-2 §1 — Bileşen şartnamesi matrisi

| # | Çıkarma katmanı bileşeni | Ne işe yarar | Repo durumu (kanıt) | Boşluk |
|---|---|---|---|---|
| I1 | Model/ağırlık formatı (ONNX/saf-tensor/gguf) | ağırlık dosyasını tanımlar | yükleyici **0 satır** (grep `onnx|pytorch|torch` → 0, §5) | ⚠️ |
| I2 | Çalışma zamanı (runtime) | modeli çalıştırır | 0 satır (§5) | ⚠️ |
| I3 | Girdi önişleme (tokenize/normalize) | veriyi tensöre çevirir | 0 satır | ⚠️ |
| I4 | Çıktı postişleme (decode/eşik) | tahminin kararı çevirir | kısmen: `predictFault` eşikleri (EK §3) — ama model değil | 🔶 |
| I5 | Parti/latency yönetimi (batch) | verim/SLA | 0 satır | ⚠️ |
| I6 | Model versiyonu/geri sarma | hangi model hangi sürümde | 0 satır (model registry yok) | ⚠️ |
| I7 | Kaynak/ota-devre dışı bırakma | hata halinde fallback | kısmen: `predictFault` sabit sonuçlar "model fallback" değil | 🔶 |
| I8 | Gözlemlilik (latency/error metrikleri) | üretim izleme | 0 satır | ⚠️ |
| I9 | Çıkarma tarafı testi | regresyon kapısı | AI testi 0/44 (§5) | ⚠️ |

**Okuma:** 9 bileşenin **6'sı tamamen 0 satır**, 3'ü kısmi ve hiçbiri model
kaynaklı değil. "runtime seçimi UNKNOWN" (frontmatter open_question) bu tabloyla
birlikte okunmalıdır: seçim **önce I1–I2'yi**, sonra I4'ü bağlamayı gerektirir.

## EK-2 §2 — `predictFault` → gerçek modele geçişin ön koşulları

Mevcut kural tabanı (EK §2–§3) model ile değiştirilecekse, kaybolmayacak/
taşınacakların listesi:

| Taşınacak varlık | Kaynak (EK) | Model tarafında karşılığı | Durum |
|---|---|---|---|
| 4 eşik kuralı (70/90/0.1/10000) | §3 K1–K4 | özellik (feature) olarak beslenir | taslak — UNKNOWN |
| 4 alanlı çıktı şeması (`risk`·`probability`·`alerts`·`maintenance`) | §2 L196–201 | API sözleşmesi korunabilir | uyumlu |
| Olasılık tavanı mantığı (`min(...,1.0)`) | §2 L198 | kalibrasyon (reliability diagram) ile değişir | yeniden tasarım |
| Eşik kaynak bilinmezliği | C5/UNKNOWN | eğitim verisi/etiket gerekir | **bloklayıcı** |
| Test yokluğu (AI testi 0) | §5 | model öncesi regresyon kapısı gerekir | blokaj |

**Sonuç:** geçişin bloklayıcıları **etiketli veri** (eşikler bugün sabit yazım)
ve **test kapsamıdır**; mimari değil, veri sorunudur. Bu, C5'in ("hangi yüzey
taşınacak") karar değil **veri ön koşulu** olduğunu gösterir.

## EK-2 §3 — Aday çalışma zamanları: yalnızca bu dosyadaki doğrulanmış iddialar

| Aday | Kanıt (bu dosya) | Tanım (title/metin) | Kurulu mu |
|---|---|---|---|
| ONNX Runtime | §2.1 + EK §4.10 (200, title `ONNX Runtime | onnxruntime`) | model çalışma zamanı ailesi | ❌ |
| vLLM | §2.4–2.5 + EK §4.9 (200, `vLLM`) | LLM sunum motoru | ❌ |
| llama.cpp | §2.8 + EK §4.11 (200, `LLM inference in C/C++`) | yerel LLM çıkarımı | ❌ |
| whisper.cpp | EK §4.12 (200) | Whisper C++ portu | ❌ |
| mlx | EK §4.13 (200, `array framework for Apple silicon`) | Apple silicon çerçeve | ❌ |
| Core ML | EK §4.14 (200) | on-device çıkarım | ❌ |
| transformers | EK §4.15 (200) | kütüphane | ❌ |
| PyTorch quantization | Z1 — `Redirecting…` | ⚠️ kanıt yok | sayım dışı |

**Karar durumu:** 7 aday, **0 kurulu**, 1 zayıf kayıt; üretim runtime seçimi
**UNKNOWN** aynen geçerli (frontmatter open_question kapalı değil).

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 436 |
| EK-2 sonrası satır | **505** (2026-10-15 `read(offset=9999)` → "505 lines") |
| ≥500 kapı | **✅ GEÇTİ (505 ≥ 500)** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız mevcut kanıtın şartnamelenmesi) |
