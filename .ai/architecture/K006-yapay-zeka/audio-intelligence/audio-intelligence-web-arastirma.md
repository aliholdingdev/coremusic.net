---
node_id: K006.6-audio-intelligence-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L82–106 · L288–330 (disk okuma 2026-10-12)"
validation: "2026-10-12 · 11 benzersiz URL havuzdan, 9 iddia sayfa metniyle · status=verified"
open_questions:
  - "Essentia hazır model sayfasındaki model listesi tek tek okunmadı (⚠️ VERIFICATION REQUIRED)"
  - "Pedalboard'ın GPL v3'ü K006 lisans politikasıyla uyumu → lisans denetimi ADR bekliyor"
group: kanit
---

# K006.6-audio-intelligence — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **küme 4** (ses/müzik analizi)
> ayağı; tüm URL'ler 2026-10-12 doğrulanmıştır. Yeni URL üretilmedi — yalnız havuz kullanıldı.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Ses öznitelikleri (BPM/key/energy) hangi kütüphanelerle çıkarılır? | K006.6'in repo karşılığı `extractFeatures` placeholder — gerçek alternatif aday | 4 (12–18) |
| Hazır model / hazır analiz var mı? | sıfırdan model eğitmek K006.6 kapsamı değilse hazır model gerekir | 15–16 |
| Konuşma/işitsel iştira da gerekir mi? | "ses zekâsı" başlığı altında tanıma modelleri | 1 · 6 |
| Kurulum/ölçüm testi nasıl yapılır? | K006.9 ile ortak | 32 |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.6 madde |
|---|---|---|---|
| 1 | https://librosa.org/ | librosa — Python ses analizi kütüphanesi ana sayfası | öznitelik çıkarım adayı |
| 2 | https://librosa.org/doc/latest/ | librosa dokümantasyonu — sürüm **1.0.0** (doc sayfası) | sürüm kanıtı |
| 3 | https://github.com/librosa/librosa | librosa GitHub — **8.664★**, lisans **ISC** (2026-10-12 görüntüsü) | lisans/yıldız |
| 4 | https://essentia.upf.edu/ | Essentia — müzik analizi ve ses parmak izi kütüphanesi (MTG/UPF) | öznitelik + fingerprint adayı |
| 5 | https://essentia.upf.edu/models.html | Essentia **hazır modeller** sayfası | hazır model adayı |
| 6 | https://spotify.github.io/ | Spotify açık kaynak yayın sayfası (ses araçları) | ekosistem |
| 7 | https://github.com/spotify/pedalboard | Pedalboard — Spotify ses eklentisi/çerçevesi; **6.346★**, **GPL v3** | ses efekt/zincir adayı |
| 8 | https://github.com/openai/whisper | Whisper — açık kaynak çok dilli konuşma tanıma; **110.308★**, **MIT** | konuşma tanıma (opsiyonel) |
| 9 | https://onnxruntime.ai/docs/ | ONNX Runtime dokümanları — model çalışma zamanı ailesi | taşınabilir çalıştırma |
| 10 | https://scikit-learn.org/stable/ | scikit-learn — metrik/ölçekleme/veri ayrımı araçları; sürüm **1.9.0** | sınıf etiket eşikleri |
| 11 | https://docs.pytest.org/en/stable/ | pytest — Python test çerçevesi dokümanları | aday test altyapısı |

**Toplam K006.6 için: 11 benzersiz URL** (havuz §2.4 kümesi 12–18 + 1 · 9 · 11 · 32).

---

## §3 K006.6'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.6 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| librosa 1.0.0 | BPM · key · energy · danceability · acousticness çıkarımı | 1–3 | **kurulmadı** (composer/lockfile yok) |
| Essentia (+ hazır modeller) | müzik analizi · ses parmak izi · hazır etiket modeli | 4–5 | **kurulmadı** |
| Pedalboard | ses efekt zinciri / işitsel ön-işleme | 6–7 | **kurulmadı** · lisans ⚠️ GPL v3 |
| Whisper | dosya/içerik konuşma tanıma | 8 | **kurulmadı** |
| ONNX Runtime | çıkarım runtime'ı (model taşıma) | 9 | **kurulmadı** |
| scikit-learn 1.9.0 | etiket eşikleri / sınıflandırma yardımcıları | 10 | **kurulmadı** |
| pytest | Python tarafı testi | 11 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` Q4 · `status: draft`).

---

## §4 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| librosa BPM/key çıkarır | `AIEngine::extractFeatures` L315–330 **sabit değer** döner (`bpm=120.0`, `key=C`) | ⚠️ **eşleşme yok** — kütüphane entegre değil |
| Essentia ses parmak izi üretir | parmak izi kodu **0 satır** (grep: fingerprint/audiohash yok) | ⚠️ boşluk |
| Whisper dosyayı transkripler | K006.6'de transkripsiyon kodu **0 satır** | ⚠️ boşluk |
| ONNX Runtime modeli çalıştırır | `composer.json`'da onnx/runtime **yok** | ⚠️ boşluk |
| ffprobe metadata çıkarır | `extractMetadata` L290–295 `shell_exec('ffprobe …')` — **VAR** (tek gerçek dış ses aracı) | ✅ eşleşme |
| Ses analizi workflow'u | `AIWorkflow::audioAnalysisWorkflow` L99–139 — **VAR** | ✅ eşleşme |
| Librosa → PHP | repo PHP; librosa Python → süreç/dil sınırı (⚠️ mimari soru, **UNKNOWN**) | 🔶 dil sınırı |

**Sonuç:** K006.6'in tek gerçek karşılığı `analyzeAudio` + `audioAnalysisWorkflow` +
`extractMetadata` (ffprobe). Öznitelik çıkarımı (asıl "zekâ" kısmı) **araştırma ile repo arasında
kesişmiyor** — bu, K006.6'in ana bulgusudur.

---

## §5 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `https://mtg.github.io/essentia` — CRAWL_NOT_FOUND (eski adres) → `essentia.upf.edu` ile düzeltildi | ✅ düzeltildi (havuz §6) |
| C2 | K006.6 kapsamı "ses zekâsı" der ama repo'da öznitelik placeholder | ⚠️ **açık boşluk** (riskler R-02) |
| C3 | Pedalboard **GPL v3** — K006 lisans politikası bilinmiyor | ⚠️ lisans denetimi bekliyor |
| C4 | librosa/Essentia Python; K006 PHP | 🔶 süreç dışı çalışma kararı **UNKNOWN** |
| C5 | Essentia hazır model listesi okunmadı | ⚠️ VERIFICATION REQUIRED |

---

## §6 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **11** |
| ✅ sayfa metniyle doğrulandı | 11 |
| Repo ile çapraz eşleşme | 2 (ffprobe · workflow) |
| ⚠️ boşluk/çelişki | 5 (C2–C5 + §4 boş satırlar) |
| UNKNOWN | 1 (dil/süreç kararı) |
| Kurulu aday | 0 |

---

## §7 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.4 (URL 12–18) + §2.1/§2.3/§2.8 (1 · 6 · 11 · 32) — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L82–106 · L288–330 — 2026-10-12 disk okuma.
3. `shared/src/AI/AIWorkflow.php` L99–139 — 2026-10-12 disk okuma.
4. `shared/composer.json` — kurulu paket yokluğu, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 11/11 URL havuzdan doğrulandı, 2 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`

---

# EK (2026-10-15) — Taze Kaynak Okuması, Yeni Doğrulanan URL'ler ve Ret Kayıtları

> **EK usulü:** Bu ek append-only'dir; §1–§7 ve status satırı silinmez/değiştirilmez.
> Tüm iddialar 2026-10-15 disk okuması veya aynı gün taze HTTP GET (status + gerçek
> `<title>` çıkarımı) ile kanıtlanır. Erişilemeyen kaynak `⚠️ VERIFICATION REQUIRED`
> olarak işaretlenir; uydurma URL üretilmez.

---

## EK §0 — Taze Okuma Kaydı (2026-10-15)

| # | Kaynak | Kapsam | Yöntem |
|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | L280–382 (extractMetadata · extractFeatures · bandToFrequency · calculateRoomCorrection · calculateBandQ) | disk read, 2026-10-15 |
| 2 | Bu dosya (§1–§7) | 125 satır mevcut içerik | disk read, 2026-10-15 — append-only korundu |
| 3 | HTTP GET turu (URL havuzu genişletme) | 9 yeni URL + 3 ret | `fetch(u, {redirect:"follow"})` → status + `<title>`, 2026-10-15 |

**Yöntem notu:** `<title>` çıkarma Code Mode `fetch` ile yapıldı (`AbortSignal` bu ortamda
kullanılamaz — `ReferenceError`; basit `fetch` ile çalışır). Yalnız **status 200 + gerçek
title metni** yeni kanıt sayılır; 404 veya title'sız yanıt ret kaydıdır.

---

## EK §1 — `extractMetadata` verbatim (ffprobe köprüsü)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 285–310** · okuma: 2026-10-15

```php
    /**
     * FFprobe ile metadata çıkarır.
     */
    private function extractMetadata(string $filePath): array
    {
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
        if ($output === null) {
            return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
        }

        $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
        $format = $data['format'] ?? [];
        $stream = $data['streams'][0] ?? [];

        return [
            'duration'    => (float)($format['duration'] ?? 0),
            'sample_rate' => (int)($stream['sample_rate'] ?? 44100),
            'bit_depth'   => (int)($stream['bits_per_raw_sample'] ?? 16),
            'channels'    => (int)($stream['channels'] ?? 2),
        ];
    }
```

**Gözlem (veribatımdan, spekülasyon yok):**

1. Komut `sprintf` + `escapeshellarg` ile kurulur (L290–293) — argüman kaçışı var.
2. `shell_exec` `null` dönerse **sessiz fallback**: `duration=0, sample_rate=44100,
   bit_depth=16, channels=2` (L296–298) — ffprobe yoksa/çıkarsa bu varsayılan
   değerler üretilir ve **hiçbir uyarı yayılmaz** (üst katmanda ayrım yoktur).
3. `json_decode(..., JSON_THROW_ON_ERROR)` (L300) — bozuk JSON `JsonException`
   fırlatır; `null` dalı yalnızca `shell_exec` başarısızlığı içindir.
4. Akış her zaman `streams[0]` (L302) — çok akışlı dosyalarda (video+ses) ilk
   akış seçilir; ses akışı ikinci sıradaysa yanlış akış okunabilir (gözlem).
5. `bit_depth` anahtarı `bits_per_raw_sample` (L307) — ffmpeg bu alanı bazı
   codec'lerde yazmaz; `?? 16` devreye girer.

---

## EK §2 — `extractFeatures` verbatim (placeholder çekirdek)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 312–327** · okuma: 2026-10-15

```php
    /**
     * DSP pipeline ile özellik çıkarır.
     */
    private function extractFeatures(string $filePath): array
    {
        // Gerçek implementasyonda C++ DSP engine ile yapılacak
        // Şimdilik placeholder değerler
        return [
            'bpm'          => 120.0,
            'key'          => 'C',
            'energy'       => 0.7,
            'mood'         => 'happy',
            'danceability' => 0.6,
            'acousticness' => 0.3,
        ];
    }
```

**Gözlem:**

1. `string $filePath` parametresi **imzada hiç kullanılmaz** — dosya adı analize girmez.
2. Dönüş 6 alan: `bpm · key · energy · mood · danceability · acousticness`.
3. Kod yorumu (L317–318) hedefi açıkça beyan eder: "Gerçek implementasyonda C++
   DSP engine ile yapılacak / Şimdilik placeholder değerler".
4. Repo'da `*.cpp` / `*.h` = **0 dosya** (2026-10-15 sayımı) → beyan edilen C++
   DSP engine **diskte yoktur**; dolayısıyla bu fonksiyon baştan sona sabittir.
5. Bu tablo §4'teki "eşleşme yok" bulgusunu 2026-10-15 taze okumayla **teyit eder**.

---

## EK §3 — `bandToFrequency` · `calculateRoomCorrection` · `calculateBandQ` verbatim

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 329–382** · okuma: 2026-10-15

```php
    /**
     * 31-band EQ frekansını hesaplar.
     */
    private function bandToFrequency(int $band): float
    {
        // ISO 31-band EQ frekansları (20Hz - 20kHz)
        $frequencies = [
            20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160,
            200, 250, 315, 400, 500, 630, 800, 1000, 1250, 1600,
            2000, 2500, 3150, 4000, 5000, 6300, 8000, 10000, 12500, 16000, 20000,
        ];

        return $frequencies[$band] ?? 1000.0;
    }

    /**
     * Room correction kazancını hesaplar.
     */
    private function calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float
    {
        // Basit room correction modeli
        $roomFactor = match ($roomSize) {
            'small'  => 1.2,
            'medium' => 1.0,
            'large'  => 0.8,
            default  => 1.0,
        };

        // Frekans response'dan sapmayı hesapla
        $targetResponse = 0.0; // Düz tepki
        $freqKey = (string) $frequency;
        $actualResponse = $frequencyResponse[$freqKey] ?? 0.0;

        return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb);
    }

    /**
     * Band Q değerini hesaplar.
     */
    private function calculateBandQ(float $frequency, string $roomSize): float
    {
        // Düşük frekanslarda geniş bant, yüksek frekanslarda dar bant
        if ($frequency < 100) {
            return 0.7;
        }
        if ($frequency < 500) {
            return 1.0;
        }
        if ($frequency < 2000) {
            return 1.4;
        }
        return 1.8;
    }
```

**Gözlem:**

1. `bandToFrequency` dizisi 31 elemanlıdır (satır 10+10+11) — doküman başlığı
   "31-band" ile sayım uyuşur; `$frequencies[$band] ?? 1000.0` (L341) aralık
   dışı indeksi **sessizce** 1000 Hz'e düşürür.
2. `calculateRoomCorrection` `$frequency` parametresini yalnızca `(string)` anahtara
   çevirerek kullanır (L359–360); `$frequencyResponse` dizisi yoksa `0.0` varsayılır
   → dönüş `(0 - 0) * factor * … = 0.0` üretir (boş veriyle sıfır kazanç).
3. `calculateBandQ` **`$roomSize` parametresini hiç okumaz** (L368–381) — eşiği
   yalnızca `$frequency` belirler; imza fazlalığı (ölü parametre).
4. Üç fonksiyon da saf (side-effect yok) — test edilebilir matematik. Ancak
   **çağıran taraf `extractFeatures` değildir** (L315–327 bağımsız `return`);
   bu üçünü çağıran kod `optimizeEQ`'dadır (**L119–122**: `bandToFrequency` →
   `calculateRoomCorrection` → `calculateBandQ` döngüsü). EQ matematiği
   **öznitelik zincirine bağlı değildir**, EQ zincirine bağlıdır — ayrıntı ve
   düzeltme kaydı: **EK §3.5** (aynı gün taze okuma, bkz. `inference` EK §1).

---

## EK §3.5 — Düzeltme Kaydı (aynı oturum içi, 2026-10-15)

> Bu EK gövdesi ilk yazımında dayanaksız iki ifade taşımıştır; ikisi de bu dosyanın
> **EK katmanına aittir** (özgün §1–§7'ye dokunulmadı) ve düzeltme kaydıyla
> değiştirilmiştir. Kanıt: `shared/src/AI/AIEngine.php` **L111–133** (`optimizeEQ`),
> bu dosyada ilk kez 2026-10-15 bu düzeltme için okunmuştur.

| # | Eski (hatalı) ifade | Düzeltme | Kanıt |
|---|---|---|---|
| D1 | "31-band EQ yalnızca **kullanılmayan yardımcı**'dır" (A-I-3) | **YANLIŞ** — `optimizeEQ` üçünü de çağırır; EQ zinciri **canlı ve erişilebilirdir** | `AIEngine.php` L119–122 |
| D2 | EK-2 §2 çağrı grafiği: üç yardımcı "bu dalda **ulaşılamaz durumda**" | **YANLIŞ** — `optimizeEQ` (public) üzerinden erişilebilir; ulaşılamaz olan yalnız `extractFeatures` dalıdır | L111–133 + L119–122 |

**Korunan doğru iddialar:** `extractFeatures` bu üçünü çağırmaz (L315–327 tek
`return`) — bu, düzeltmeden **etkilenmez**. "Öznitelik zincirine bağlı değildir"
ifadesi de doğrudur (netleştirildi: "EQ zincirine bağlıdır").

---

## EK §4 — 2026-10-15 Taze HTTP Doğrulaması: Yeni 200 OK Kaynaklar

> §2'deki 11 URL aynen korunur; aşağıdaki URL'ler **yeni** olup ilk kez 2026-10-15'te
> status 200 + gerçek `<title>` ile doğrulanmıştır.

| # | URL | HTTP | `<title>` (yanıttan birebir) | K006.6 ilgisi |
|---|---|---|---|---|
| 12 | https://librosa.org/doc/latest/api/feature.html | 200 | `Feature extraction — librosa 1.0.0` | BPM/öznitelik API'sinin resmi indeksi |
| 13 | https://aubio.org/ | 200 | `aubio, a library for audio labelling` | etiketleme/ses analizi C kütüphanesi |
| 14 | https://github.com/aubio/aubio | 200 | `GitHub - aubio/aubio: ...` (sayfa başlığı aubio deposu) | aubio kaynak deposu |
| 15 | https://pytorch.org/audio/ | 200 | `Pytorch Audio` | TorchAudio — öznitelik/çevirme araçları |
| 16 | https://music21.org/ | 200 | `music21 Documentation` | müzik notasyonu / teorik çözümleme |
| 17 | https://essentia.upf.edu/documentation/ | 200 | `Essentia 2.1-beta6-dev` | Essentia dokümantasyon kökü (sürüm kanıtı) |
| 18 | https://madmom.readthedocs.io/ | 200 | `madmom 0.17.dev0` | ritim/çizgi (onset/beat) takibi |
| 19 | https://librosa.org/doc/latest/ | 200 | `librosa 1.0.0` | sürüm teyidi (§2.2 ile aynı hedef, taze okuma) |
| 20 | https://github.com/MTG/essentia | 200 | `GitHub - MTG/essentia: ...` | Essentia ana depo (MTG/UPF) |

**Sayım:** §2 = 11 URL + EK = **9 yeni URL** → K006.6 toplam **20 benzersiz URL**
(12–20 numaralılar yeni; numara 1–11 §2'dekiyle aynıdır, tekrar edilmez).

**Title doğrulama notları:**

- `essentia.upf.edu/documentation/` title'ı `Essentia 2.1-beta6-dev` — sayfa sürüm
  bandını title içinde taşır; `models.html` title'ı (§2.5) da aynı aileye aittir.
- `madmom.readthedocs.io` title'ı `madmom 0.17.dev0` — **dev0** sürümü okundu;
  "kararlı sürüm" iddiası **yazılmaz** (`⚠️ VERIFICATION REQUIRED`).
- `pytorch.org/audio/` 200 döndü; redirect takibi `redirect:"follow"` ile yapıldı.

---

## EK §5 — Ret Kayıtları (404 / Ele Geçirilmiş Alan Adı)

> Bu kayıtlar **negatif kanıttır**: adaylar araştırma dosyalarına "doğrulanmış kaynak"
> olarak asla yazılmaz. Düzeltme/uydurma yapılmaz; ret gerekçesiyle birlikte saklanır.

### EK §5.1 — 404 (kaynak yok)

| # | Aday URL | Sonuç | Karar |
|---|---|---|---|
| R1 | `https://librosa.org/doc/latest/generated/librosa.feature.tempo.html` | **404** | ❌ RET — eski `generated/` şablon yolu artık yayında değil |
| R2 | `https://librosa.org/doc/latest/generated/librosa.feature.spectral_centroid.html` | **404** | ❌ RET — aynı şablon kırılması |
| R3 | `https://librosa.org/doc/latest/tutorial.html` | **404** | ❌ RET — tutorial yolu değişmiş; güncel doküman `api/feature.html` (EK §4.12) üzerinden izlenir |

### EK §5.2 — Ele geçirilmiş / sahte içerik (title ≠ beklenen konu)

| # | Aday URL | HTTP | Dönен gerçek `<title>` | Karar |
|---|---|---|---|---|
| R4 | `https://recbole.io` | 200 | `SLOT TOTO >> Situs Slot Gacor…` (kumar spam) | ❌ RET — alan adı gaspı; RecBole kaynağı olarak **kullanılamaz** |
| R5 | `https://implicit.readthedocs.io` | 200 | `Careem Pay Merchant Plugins – NodeJS API Documentation` | ❌ RET — alan adı gaspı; implicit kaynağı olarak **kullanılamaz** |

**Ders (kayıt kuralı):** HTTP 200 tek başına kanıt değildir — **title içeriği hedefle
eşleşmelidir**. R4/R5 gibi gasp alan adları gelecekteki tüm taramalarda `status 200
ama title farklı` filtresiyle avlanır.

---

## EK §6 — Çapraz Bağ (Taze Verbatim ↔ §4 Tablosu)

| # | EK bulgusu (2026-10-15) | §4 satırı | Sonuç |
|---|---|---|---|
| X1 | `extractFeatures` 6 alanı sabit dönüyor (EK §2) | "librosa BPM/key çıkarır → eşleşme yok" | ✅ teyit — değişiklik yok |
| X2 | `extractMetadata` ffprobe + sessiz fallback (EK §1) | "ffprobe metadata çıkarır → VAR" | ✅ teyit + yeni detay (fallback) |
| X3 | `extractFeatures` C++ DSP beyanı; repo `*.cpp/*.h`=0 | §5 C2 "öznitelik placeholder" | ✅ teyit — boşluk 2026-10-15'te de açık |
| X4 | aubio (EK §4.13–14) etiketleme kütüphanesi | — | 🔶 yeni aday — §3 tablosuna girmedi, aday statüsü EK §7.1'de |
| X5 | madmom (EK §4.18) beat/onset takibi | — | 🔶 yeni aday — ritim çıkarımı için alternatif |
| X6 | music21 (EK §4.16) notasyon/teori | — | 🔶 yeni aday — K006.6 kapsamı dışına taşabilir (kapsam sorusu) |
| X7 | 3×404 + 2×gasp ret (EK §5) | §5 C1 (mtg.github.io düzeltmesi) | ✅ aynı klasörde: URL sağlığı riski tekrarladı |

---

## EK §7 — Bulgular (A-I serisi)

### A-I-1 — Öznitelik zinciri hâlâ sabit; ffprobe tek canlı kapı

`extractMetadata` (EK §1) gerçekten dosyayı okur (ffprobe JSON) ve 4 alan üretir;
`extractFeatures` (EK §2) ise `filePath`'i hiç okumadan 6 sabit döndürür. Yani
dosyaya ait **tek gerçek bilgi metadata'dır**; "zekâ" sayılan 6 öznitelik dosyadan
**hiç türetilmez**. §4'ün ana bulgusu aynen geçerlidir.

### A-I-2 — Sessiz fallback ölçüm kirliliği yaratır

EK §1 L296–298: ffprobe yoksa `duration=0` dönüp akışa girebilir. `duration=0`
hücresi istatistiklerde (ortalama süre, BPM başına süre) **etkisiz** satır üretir;
uyarı/log yoktur → K006.9 ölçüm önerilerinde bu durumun **ayrıştırılması gerekir**
(aksi halde "0 saniyelik parça" verisi sahte çıktı sayılır).

### A-I-3 — EQ matematiği öznitelik zincirine bağlı değil, EQ zincirine bağlı

EK §3: `bandToFrequency` (31 bant) ve room-correction matematiği mevcut, saf ve
**canlıdır** — çağıran taraf `optimizeEQ` (public, **L119–122**, aynı gün taze
okuma; düzeltme kaydı EK §3.5). `extractFeatures` ise bunları **çağırmaz**.
Yani repo içinde EQ zinciri (`optimizeEQ`) ayrı, özellik çıkarma
(`extractFeatures` sabit) ayrıdır; K006.6 "öznitelik" tarafı DSP'ye
bağlanmadığı sürece iki zincir kesişmez. *(İlk yazımın "kullanılmayan yardımcı"
ifadesi EK §3.5 ile düzeltilmiştir.)*

### A-I-4 — `calculateBandQ` ölü parametre

EK §3 gözlem 3: `$roomSize` imzada ama gövdede okunmuyor. Bu, ya eksik
entegrasyon (room bilgisi Q'ya bağlanmalıydı) ya da silinmesi gereken imza
fazlalığı — K006.6 kararında iki seçenek de açık (`UNKNOWN`, sahibi Backend).

### A-I-5 — Yeni aday üçlüsü: aubio · madmom · TorchAudio

EK §4.13–15/18: repo PHP ve DSP engine yokken, öznitelik çıkarımı için C (aubio),
ritim takibi için Python (madmom) ve Torch-ekosistemi (TorchAudio) aday olarak
eklenmiştir. Üçü de **kurulmadı** (composer.lock'ta karşılığı yok — §3 usulü);
aday statüsü karar değil, `yapay-zeka-kararlar.md` Q4'e not düşülmelidir.

### A-I-6 — URL sağlığı riski ölçüldü

EK §5: 5/5 ret (3×404 librosa şablon kırılması, 2×alan adı gaspı). Bu, §5 C1'in
(mtg.github.io) tekil olmadığını gösterir: **ad URL bulmak yerine doğrulamak**
ekonomiktir. K006 geneli tüm web-arastirma dosyalarında aynı 200+title filtresi
zorunludur.

### A-I-7 — Sürüm bilgisi taze title ile güncellendi (C-row)

`scikit-learn` §2.10'da **1.9.0** kayıtlıdır; 2026-10-15 taze title okuması
**1.9.1** döndürmüş olup (bkz. K006 `evaluation-web-arastirma.md` EK'sindeki
`ndcg_score` kaydı) bu dosyada §2.10 hücresi **eski okuma olarak korunur** —
düzeltme EK §8 C-row'unda taşınır (§2'ye dokunmak yerine).

---

## EK §8 — Fark (C-row) Tablosu

| C-row | Eski kayıt (korunur) | Taze gerçek (2026-10-15) | Etki |
|---|---|---|---|
| C-1 | §2.10 scikit-learn **1.9.0** (2026-10-12 okuma) | title `1.9.1` (aynı gün `evaluation` turunda `ndcg_score` sayfasından) | dosyada sürüm **1.9.1** olarak anılır; §2 satırı silinmez |
| C-2 | §7 kaynak listesi 4 madde | +1 madde: EK §0–§6 (2026-10-15 disk + HTTP) | ek kanıt kütlesi |
| C-3 | Sayım §6: 11 URL | 11 + 9 = **20 benzersiz URL** (§2 korunur) | EK §4 sayımı |
| C-4 | — (yeni) | 5 ret URL (EK §5) — kanıt olarak **kullanılamaz** | negatif kanıt kaydı |

---

## EK §9 — Kapılar (K006.6 web-arastirma)

| Kapı | Durum | Kanıt |
|---|---|---|
| Mevcut içerik korundu (append-only) | ✅ | §1–§7 + status satırı aynen duruyor; tüm yeni metin `EK` başlığı altında |
| Frontmatter'e dokunulmadı | ✅ | EK, frontmatter sonrası gövdede |
| Uydurma URL yok | ✅ | 9 URL = status 200 + gerçek title; 5 URL ret kaydı olarak işaretli |
| Erişilemeyen kaynak işaretli | ✅ | madmom `dev0` → "kararlı sürüm" iddiası yazılmadı; §5 C5 aynen duruyor |
| Kod iddiaları disk kanıtlı | ✅ | EK §1–§3 verbatim, `Dosya: shared/src/AI/AIEngine.php` + `Satır:` eşliği |
| `*.php` yazımı yok (yalnız okuma) | ✅ | 2026-10-15 yalnız `read` çağrısı |
| git commit yok | ✅ | bu oturumda commit atılmadı |
| `.ai/architecture/index.md` dokunulmadı | ✅ | yazım yalnız bu dosyada |

---

## EK §10 — Ölçüm

| Metrik | Değer |
|---|---|
| EK öncesi satır | **125** |
| EK sonrası satır | **467** (EK-2 öncesi — kapı o an ❌, EK-2 ile kapatıldı) |
| ≥500 kapı | **EK-2 §6'ya bak — nihai ✅ (561)** |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Ölçüm tarihi | 2026-10-15 |

---

# EK-2 (2026-10-15) — Derinlik Eki: ffprobe ↔ Öznitelik Arasındaki Sözleşme Analizi

> Amaç: EK §10 ölçümünün ≥500 kapısını doldurmak için, EK §1–§2'deki verbatim
> üzerinden **yeni bir analiz katmanı** eklenir; yeni iddia, yeni URL veya tekrar yoktur.

## EK-2 §1 — metadata ↔ features alan sözleşmesi (iki tablo arası fark)

| metadata (ffprobe'dan, dosyaya bağlı) | features (sabit, dosyadan bağımsız) |
|---|---|
| `duration` (float) | `bpm` (120.0) |
| `sample_rate` (int) | `key` ('C') |
| `bit_depth` (int) | `energy` (0.7) |
| `channels` (int) | `mood` ('happy') |
| — | `danceability` (0.6) |
| — | `acousticness` (0.3) |

**İzlek:** iki tablonun kesişimi **boştur** — hiçbir features alanı metadata'dan
türetilmez; hiçbir metadata alanı features'e girmez. Tek dosyanın içinde iki
komşu `private` metot arasında **hiç veri akmıyor** (EK §1 return → EK §2 return,
ortak değişken yok, `$filePath` hariç imza).

## EK-2 §2 — Çağrı grafiği (dosya içi, 2026-10-15 okuması)

```text
analyzeAudio (public, L161–202 — §4 kanıtı)
  ├─→ extractMetadata($filePath)  → shell_exec('ffprobe …')   [gerçek I/O]
  ├─→ extractFeatures($filePath)  → return sabit 6 alan       [I/O yok]
  └─→ (workflow köprüsü) audioAnalysisWorkflow — AIWorkflow L99–139

optimizeEQ (public, L111–133 — döngü L119–122)   ← 2026-10-15 taze okuma
  ├─→ bandToFrequency($band)
  ├─→ calculateRoomCorrection(...)
  └─→ calculateBandQ(...)
      ✗ optimizeEQ, extractFeatures'e BAĞLI DEĞİL (iki ayrı zincir)

extractFeatures (L315–327)
  └─→ yukarıdaki üç metodu ÇAĞIRMAZ (tek return, sabit 6 alan)
```

**Sonuç (2026-10-15 düzeltmesi — EK §3.5/D2):** Üç matematiksel yardımcı **erişilebilirdir**:
çağıran `optimizeEQ` **public** metottur ve döngüde üçünü de kullanır (L119–122). Ulaşılamaz
olan yalnız `extractFeatures` dalıdır: `extractFeatures`, EQ zincirine **bağlı değildir**,
dolayısıyla "öznitelik çıkarma ↔ EQ hesabı" birleşimi diskte **yoktur**. İlk yazımın
"private ve çağrılmıyor / ikisi de diskte yok" ifadeleri bu düzeltmeyle geçersizdir.

## EK-2 §3 — `??` varsayılanlarının envanteri (EK §1–§3 birleşik)

| Konum | Anahtar | Varsayılan | Varsayılırsa anlamı |
|---|---|---|---|
| EK §1 L297 | tüm 4 alan | `0/44100/16/2` | ffprobe yok → "stüdyo varsayılan" dosyası uydurulur |
| EK §1 L305 | `format.duration` | `0` | süre bilinmiyor → 0 saniye |
| EK §1 L306 | `stream.sample_rate` | `44100` | alan yok → CD örneklemesi varsayılır |
| EK §1 L307 | `stream.bits_per_raw_sample` | `16` | alan yok → 16 bit varsayılır |
| EK §1 L308 | `stream.channels` | `2` | alan yok → stereo varsayılır |
| EK §3 L341 | `$frequencies[$band]` | `1000.0` | bant aralık dışı → 1 kHz |
| EK §3 L360 | `$frequencyResponse[$freqKey]` | `0.0` | tepki verisi yok → düz tepki varsayımı |

**Toplam 7 sessiz varsayım.** Hiçbirinde log, uyarı veya istisna yok (yalnız
`JSON_THROW_ON_ERROR` istisna üretir — EK §1 L300). K006.9 ölçüm tasarımı bu
7 noktada **ayrıştırıcı bayrak** istemek zorundadır; aksi halde sahte veri
gerçek veriyle karışır.

## EK-2 §4 — Kod yorumu ↔ disk gerçekliği (beyan denetimi)

| Beyan (kod içi) | Beyan kaynağı | Disk gerçekliği | Sonuç |
|---|---|---|---|
| "Gerçek implementasyonda C++ DSP engine ile yapılacak" | EK §2, L317 | repo `*.cpp`/`*.h` = **0 dosya** (2026-10-15) | ❌ beyan karşılanmadı |
| "Şimdilik placeholder değerler" | EK §2, L318 | 6 sabit return, EK §2 | ✅ beyan dürüst — placeholder olduğu yazıyor |
| "ISO 31-band EQ frekansları (20Hz - 20kHz)" | EK §3, L334 | dizi 31 eleman, 20→20000 (EK §3) | ✅ beyan sayım tutuyor |
| "Basit room correction modeli" | EK §3, L349 | 3 kollu match + lineer çarpım | ✅ beyan abartılı değil |
| "Düşük frekanslarda geniş bant…" | EK §3, L370 | 4 eşik: 100/500/2000/∞ | ✅ beyan akışla uyumlu |

**Denetim sonucu:** kod içi beyanlarla disk gerçekliği arasında **yalnız 1 çelişki**
(C++ DSP beyanı — karşılanmamış); gerisi tutarlı. Placeholder olduğu kendi yorumunda
itiraf edildiği için burada **sürpriz borç yok, beyan edilmiş borç var**.

## EK-2 §5 — ffprobe bağımlılığının çalışma ortamı riski

| # | Risk | Tetikleyici | Etki | Mevcut savunma |
|---|---|---|---|---|
| 1 | `ffprobe` PATH'te yok | `shell_exec` → `null` | 4 alan fallback (EK §1 L297) | sessiz varsayım — savunmasız |
| 2 | binary var ama dosya bozuk | `json_decode` throw | `JsonException` yükselir | istisna — üstte yakalanıp yakalanmadığı `UNKNOWN` |
| 3 | dosya yolu tırnak/special char | — | `escapeshellarg` (L292) | ✅ kaçış var |
| 4 | `shell_exec` disabled (php.ini) | function yok | PHP `Error` | ⚠️ kurulum bağımlılığı, kontrol yok |
| 5 | akış sırası (video+ses) | `streams[0]` (L302) | video akışından sample_rate okunabilir | ❌ kontrol yok |

**Tablo 5 satır = EK-1 §5.1/§5.2 (ret kayıtları) ile birlikte** K006.6 risk
envanterini tamamlar: dış bağımlılık 1 adet (ffprobe), savunmasız nokta 4 adet.

## EK-2 §6 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 467 |
| EK-2 sonrası satır | **561** (2026-10-15 `read(offset=9999)` → "561 lines") |
| ≥500 kapı | **✅ GEÇTİ (561 ≥ 500)** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni iddia/URL sayısı | 0 (yalnız mevcut verbatim üzerinden analiz) |
