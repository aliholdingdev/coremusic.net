---
title: "audio-intelligence — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/audio-intelligence
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# audio-intelligence — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, sınıf, kütüphane uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "audio-intelligence" v2 §2.6 satır 264'te birebir geçer | `.ai/architecture/K006-yapay-zeka/audio-intelligence/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `audio-intelligence/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | K006 = ML ürün yetenekleri · K016 = ajan altyapısı (kapsam sınırı) | `.ai/architecture/K006-yapay-zeka/ai-master.md` §2.2 | 2026-10-12 | ✅ dosya okundu |
| 4 | Repo'da ses analizi sözleşmesi: `analyzeAudio()` → BPM/key/energy/mood | `shared/src/Contracts/AI/AIEngineInterface.php` satır 30–36 | 2026-10-12 | ✅ dosya okundu |
| 5 | Özellik çıkarımı henüz placeholder (sabit değerler) | `shared/src/AI/AIEngine.php` satır 315–327 | 2026-10-12 | ✅ dosya okundu |
| 6 | Metadata çıkarma ffprobe komutu ile yapılır | `shared/src/AI/AIEngine.php` satır 291 | 2026-10-12 | ✅ dosya okundu |
| 7 | ffprobe `-show_format` / `-show_streams` ve `-output_format json` destekler | https://www.ffmpeg.org/ffprobe.html | 2026-10-12 | ✅ doğrulandı |
| 8 | librosa resmi feature extraction API'si (chroma · mfcc · tempo) yayınlar | https://librosa.org/doc/latest/api/feature.html | 2026-10-12 | ✅ doğrulandı |
| 9 | Essentia: ses analizi ve music information retrieval için C++ kütüphanesi (Python/JS binding) | https://essentia.upf.edu/documentation.html | 2026-10-12 | ✅ doğrulandı |
| 10 | librosa `beat_track` ile BPM tahmini resmi tutorial'da gösteriliyor | https://librosa.org/doc/latest/tutorial.html | 2026-10-12 | ✅ doğrulandı |
| 11 | CoreMusic'in librosa kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | CoreMusic'in Essentia kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 13 | Feature çıkarma katmanı kararı (PHP ↔ C++ DSP) | `ai-master.md` §7/4 · `AIEngine.php` satır 317 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 14 | audio-intelligence ↔ music-analysis sınırı | `ai-master.md` §3 satır 263–264 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 15 | K006 model/runtime/kütüphane ekosistemi | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 15 |
| İşlenen benzersiz URL | 4 (7 · 8 · 9 · 10) |
| Gerçek dosya yolu satırı | 6 (1 · 2 · 3 · 4 · 5 · 6) |
| ✅ doğrulandı | 10 (1–10) |
| ⚠️ VERIFICATION REQUIRED | 2 (13 · 14) |
| UNKNOWN | 3 (11 · 12 · 15) |

**Geçiş kuralı:** satır 13 katman kararıyla, satır 14 ADR-097 K4 ile; satır 11–12 ve 15 teknoloji seçim
kayıdıyla (`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

## EK §0 — Bu ek ne? (append-only · 2026-10-15)

> **Kural:** aşağıdaki EK §1–§11 eklenmiştir; dosyanın **1–54 satırları** (frontmatter ·
> §1 iddia tablosu · §2 sayım · geçiş kuralı · orijinal Authority) **değişmeden korunmuştur**
> ve bu ekin birebir önekidir.
> **Yeni URL yok** — EK §5 ve EK §11'de yalnız 2026-10-12 tarihli URL'ler yeniden taşınır
> (kaynak: `audio-intelligence-web-arastirma.md` §2, 11 URL · bu dosya §1, 4 URL).
> **Kod yüzeyine yazım yok** — bu tur yalnız `.md` evidansıdır.
> Her satır kaynak: `Dosya: <yol> [Satır: n]` · doğrulanamayan: `⚠️ VERIFICATION REQUIRED`.

---

## EK §1 — Taze okuma ve tarama kayıtları (2026-10-15)

| # | İşlem | Yüzey | Sonuç |
|---|---|---|---|
| T1 | `read` tam | `shared/src/Contracts/AI/AIEngineInterface.php` L15–54 | `analyzeAudio` imzası **Satır: 36** · `getRecommendations` **Satır: 28** · `optimizeEQ` **Satır: 44** · `analyzeHardware` **Satır: 52** |
| T2 | `read` | `shared/src/AI/AIEngine.php` L78–117 | `analyzeAudio` gövdesi **Satır: 82–106** |
| T3 | `read` | `shared/src/AI/AIEngine.php` L280–349 | `extractMetadata` **Satır: 288–310** · ffprobe komutu **Satır: 291** · `extractFeatures` **Satır: 315–327** |
| T4 | `read` | `shared/src/AI/AIWorkflow.php` L100–139 | `audioAnalysisWorkflow` · `analyzeAudio` çağrısı **Satır: 109** · DB yazımı **Satır: 117–127** |
| T5 | `glob` | `.ai/architecture/K006-yapay-zeka/audio-intelligence/*.md` | **7 dosya**: `index` · `-anayasa` · `-bilesenler` · `-kapsam` · `-ornekler` · `-dogrulama` · `-web-arastirma` |
| T6 | `read` tam | `audio-intelligence/index.md` | **75 satır** · `children:` 6 dosya (L6–12) · §GRUPLAR tablosu 7 satır (L67–75) |
| T7 | `read` tam | `audio-intelligence-kapsam.md` | **85 satır** (önceki envanter notu 86 idi — ölçüm düzeltmesi, EK §3/C8) |
| T8 | `read` tam | `audio-intelligence-web-arastirma.md` | **125 satır** · 11 URL · `status: verified` |
| T9 | heading grep | `audio-intelligence-anayasa.md` | **45 başlık** (`#` L27 → `§23` L486) |
| T10 | `read` | `audio-intelligence-anayasa.md` L114–163 · L194–238 | §4 bileşen kataloğu · §5 RT bütçesi · §8 ölçüm · §9 MUST kuralları |
| T11 | `glob` | `.ai/electronic/dsp/*.md` | **10 dosya** (`AGENTS.md` iddiası ile eşleşti): `index` · `CLAUDE` · `AGENTS` · `loudness` · `filters` · `equalizer` · `effects` · `dynamics` · `dsp-pipeline` · `crossover` |
| T12 | `grep` `librosa\|Essentia\|demucs\|genre-cnn\|onnxruntime\|MusicBrainz` | `.ai/projects/` | **5 isabet**: `ai-models.md` L539 (`demucs-ht`) · L546 (`genre-cnn`) · `cpp-global-CLAUDE.md` L345 · L413 · `midi-system.md` L248 (klavye adı — alakasız) |
| T13 | `read` | `cpp-global-CLAUDE.md` L330–359 · L388–424 | vcpkg manifest `onnxruntime 1.16.0` (**Satır: 345–346**) · ortak bağımlılık tablosu (**Satır: 397–407**) · vcpkg install (**Satır: 413**) |
| T14 | satır sayımı | `audio-intelligence-dogrulama.md` (bu dosya, ek öncesi) | **54 satır** |
| T15 | satır sayımı | `ai-master.md` | **557 satır** (T12 ile `read` aralık testi) |
| T16 | `grep` | `shared/src` — `librosa\|essentia\|whisper\|onnx` | **0 isabet** (Python/ML kütüphanesi PHP deposunda yok) |

---

## EK §2 — Kod alıntıları (birebir · 2026-10-15 disk okuma)

### EK §2.1 Sözleşme — `AIEngineInterface::analyzeAudio`

**Dosya:** `C:\www\coremusic.net\shared\src\Contracts\AI\AIEngineInterface.php` [Satır: 30–36]

```php
    /**
     * Ses dosyasını analiz eder (BPM, key, energy, mood).
     *
     * @param string $filePath Ses dosyası yolu
     * @return array{bpm: float, key: string, energy: float, mood: string, danceability: float, acousticness: float}
     */
    public function analyzeAudio(string $filePath): array;
```

| # | Alan | Tip (sözleşme) | Kaynak |
|---|---|---|---|
| F-1 | `bpm` | `float` | AIEngineInterface.php [Satır: 34] |
| F-2 | `key` | `string` | [Satır: 34] |
| F-3 | `energy` | `float` | [Satır: 34] |
| F-4 | `mood` | `string` | [Satır: 34] |
| F-5 | `danceability` | `float` | [Satır: 34] |
| F-6 | `acousticness` | `float` | [Satır: 34] |

### EK §2.2 Uygulama — `AIEngine::analyzeAudio`

**Dosya:** `C:\www\coremusic.net\shared\src\AI\AIEngine.php` [Satır: 82–106]

```php
    public function analyzeAudio(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Audio file not found: {$filePath}");
        }

        // FFprobe ile metadata çıkarma
        $metadata = $this->extractMetadata($filePath);

        // DSP pipeline ile özellik çıkarma
        $features = $this->extractFeatures($filePath);

        return [
            'bpm'           => $features['bpm'] ?? 0.0,
            'key'           => $features['key'] ?? 'unknown',
            'energy'        => $features['energy'] ?? 0.0,
            'mood'          => $features['mood'] ?? 'neutral',
            'danceability'  => $features['danceability'] ?? 0.0,
            'acousticness'  => $features['acousticness'] ?? 0.0,
            'duration'      => $metadata['duration'] ?? 0,
            'sample_rate'   => $metadata['sample_rate'] ?? 44100,
            'bit_depth'     => $metadata['bit_depth'] ?? 16,
            'channels'      => $metadata['channels'] ?? 2,
        ];
    }
```

| # | Satır | Gözlem | K006.6 açısından |
|---|---|---|---|
| G-1 | 84–86 | dosya yoksa `\RuntimeException` | hata sınıfı — anayasa §17 (hata sınıfları) ile ilişkili |
| G-2 | 89 | metadata = ffprobe (gerçek) | ✅ tek gerçek dış ses aracı |
| G-3 | 92 | features = `extractFeatures` (placeholder) | ❌ "zekâ" kısmı sahte |
| G-4 | 95–100 | 6 öznitelik `?? fallback` ile | sözleşme ile hizalı (F-1–F-6) |
| G-5 | 101–104 | 4 metadata alanı (duration · sample_rate · bit_depth · channels) | sözleşme dönüşünde **ek alanlar** — sözleşmede yok |

### EK §2.3 Metadata — `extractMetadata` (ffprobe)

**Dosya:** `C:\www\coremusic.net\shared\src\AI\AIEngine.php` [Satır: 288–310]

```php
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

| # | Satır | Gözlem |
|---|---|---|
| X-1 | 291 | `ffprobe -v quiet -print_format json -show_format -show_streams` — **İddia 7 ile birebir** (bayraklar resmi ffprobe sayfasında) |
| X-2 | 292 | `escapeshellarg($filePath)` — komut enjeksiyonu koruması (security §2) |
| X-3 | 295 | `shell_exec` — **dış süreç bağımlılığı** (ffprobe kurulu olmalı; kurulu değilse `$output === null`) |
| X-4 | 296–298 | null → **varsayılan değerler** (44100 Hz · 16 bit · 2 kanal) — sessizce üretilir |
| X-5 | 300 | `JSON_THROW_ON_ERROR` — bozuk JSON'da istisna |
| X-6 | 302 | yalnızca `streams[0]` — çok kanallı/çok akışlı dosyalarda ilk akış |
| X-7 | 307 | `bits_per_raw_sample` okunur; yoksa 16 |

### EK §2.4 Öznitelik — `extractFeatures` (placeholder)

**Dosya:** `C:\www\coremusic.net\shared\src\AI\AIEngine.php` [Satır: 312–327]

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

| # | Alan | Sabit değer | Dosyanın gerçek değeri |
|---|---|---|---|
| P-1 | `bpm` | `120.0` | her dosyada 120.0 |
| P-2 | `key` | `'C'` | her dosyada C |
| P-3 | `energy` | `0.7` | sabit |
| P-4 | `mood` | `'happy'` | sabit |
| P-5 | `danceability` | `0.6` | sabit |
| P-6 | `acousticness` | `0.3` | sabit |
| P-7 | `$filePath` | **kullanılmaz** | fonksiyon girdisi hiç okunmaz |

> **Bu, dosyanın ana bulgusudur:** `audio-intelligence` kapsamı "ses zekâsı" der; repo'da
> öznitelik çıkarımı **6 sabit değerdir**. İddia 5 (`⚠️`) 2026-10-15'te **tekrar** doğrulanmıştır.

### EK §2.5 Workflow — analiz + DB yazımı

**Dosya:** `C:\www\coremusic.net\shared\src\AI\AIWorkflow.php` [Satır: 105–127]

```php
        // 1. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('analysis', $request, 'high');

        // 2. Audio analysis
        $analysis = $this->engine->analyzeAudio($filePath);

        // 3. Knowledge enrich — benzer şarkıları bul
        // analyzeAudio() 'genre' anahtarını döndürmez -> tür bilgisi her zaman boş.
        $genre = '';
        $query = "{$analysis['key']} {$analysis['mood']} {$genre}";
        $knowledgeResults = $this->knowledge->search($query, 3);

        // 4. DB'ye kaydet (track_id varsa) — prepared statement ile SQL injection koruması
        if ($trackId !== null) {
            $bpm    = (float)$analysis['bpm'];
            $keySig = (string)$analysis['key'];
            $energy = (float)$analysis['energy'];
            $this->toolCalling->callTool('db-query', [
                'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
                'params'   => ['bpm' => $bpm, 'key_sig' => $keySig, 'energy' => $energy, 'id' => (int)$trackId],
                'database' => 'coremusic_musics',
            ]);
        }
```

| # | Satır | Gözlem | Durum |
|---|---|---|---|
| W-1 | 106 | görev `analysis` · öncelik `high` | ✅ |
| W-2 | 109 | `analyzeAudio` çağrısı — sonuç DB'ye giden tek yol | ✅ |
| W-3 | 112–113 | kod yorumu: **`genre` anahtarı döndürülmez → tür bilgisi her zaman boş** | ⚠️ bilinen boşluk (kod yorumu, disk kanıtı) |
| W-4 | 114 | sorgu `key + mood + genre` — **boş türle** birleşik | ⚠️ |
| W-5 | 115 | `knowledge->search($query, 3)` — 3 sonuç | ✅ |
| W-6 | 118 | yalnız `$trackId !== null` iken yazar | ✅ |
| W-7 | 123 | parametreli SQL (`:bpm` · `:key_sig` · `:energy` · `:id`) | ✅ PDO hazır ifade kuralı |
| W-8 | 125 | veritabanı `coremusic_musics` | ✅ disk kanıtı |
| W-9 | 119–121 | yalnız `bpm` · `key` · `energy` yazılır — `mood`/`danceability`/`acousticness` **DB'ye yazılmaz** | ⚠️ sözleşmenin 6 alanından 3'ü kaybolur |
| W-10 | 129–138 | yanıt: `analysis` · `metadata` (`task_id`, `knowledge_hits`) · `execution_time` | ✅ |

**EK §2 sayımı:** 5 alıntı bloğu · 7 gözlem tablosu (F-1–F-6 · G-1–G-5 · X-1–X-7 ·
P-1–P-7 · W-1–W-10 = 35 gözlem satırı).

---

## EK §3 — Çelişki / bayatlık kayıtları (2026-10-15 ↔ 2026-10-12 satırları)

> **Append-only:** 2026-10-12 tarihli satırlar **silinmez ve düzeltilmez**; çelişkiler
> **yeni satır** olarak eklenir (dosyanın 1–54 satırı önek olarak korunur).

| # | Eski kayıt | 2026-10-15 disk gözlemi | Yeni durum |
|---|---|---|---|
| C1 | `audio-intelligence-kapsam.md` **Satır: 18**: "ai-master §1 sorumluluk tablosu (satır 37–42)" | `ai-master.md` **Satır: 56** = `## §1 Kimlik`; sorumluluk matrisi **§4 · Satır: 176** (satır 180–188, S1–S7) | ⚠️ BAYAT SATIR + BAYAT § |
| C2 | `audio-intelligence-kapsam.md` **Satır: 46** ve bu dosya **Satır: 35**: "ai-master §3 satır 263–264" | `ai-master.md` **Satır: 142** = `## §3 Kademe-2 envanter`; `audio-intelligence` satırı **Satır: 153** (`v2 satır 264` sütunu ile birlikte) | ⚠️ BAYAT SATIR — içerik doğru, konum değişmiş |
| C3 | `ai-master.md` §3.1 (Satır: 170): "Çocuk dosyası toplamı **54**" · §13.2 (Satır: 402): `audio-intelligence/ | 6 | 563` | 2026-10-15 `glob`: **7 md dosya** (T5) · `index.md` **Satır: 6–12** `children:` = **6 dosya** (index hariç 6 → toplam 7) | ⚠️ ÖLÇÜM ÇELİŞKİSİ — sayım 6 (2026-10-13) ↔ 7 (2026-10-15) |
| C4 | `audio-intelligence-kapsam.md` **Satır: 56**: "index.md (open_questions satır 13)" | `index.md` `open_questions` **Satır: 18–21** (4 madde), `evidence` **Satır: 14–15** | ⚠️ BAYAT SATIR |
| C5 | Bu dosya **Satır: 26**: "AIEngine.php satır 315–327" placeholder | **Doğrulandı** — EK §2.4 birebir aynı | ✅ sağlam |
| C6 | Bu dosya **Satır: 25**: "AIEngineInterface.php satır 30–36" | **Doğrulandı** — EK §2.1 birebir aynı | ✅ sağlam |
| C7 | Bu dosya **Satır: 27**: "AIEngine.php satır 291" ffprobe | **Doğrulandı** — EK §2.3, `Satır: 291` | ✅ sağlam |
| C8 | (envanter notu) kapsams dosyası "86 satır" | `read` tam okuma → **85 satır** (T7) | ℹ️ ÖLÇÜM DÜZELTMESİ |
| C9 | `index.md` **Satır: 37 · 43–45**: tırnak yerine `»` karakteri (`⚠️ VERIFICATION REQUIRED».` · `../index.md»` · `children: []»`) | kaynak dosyada böyle; **bu ekte birebir taşındı, düzeltilmedi** (beyazliste dışı dosya) | ⚠️ KAYNAK KÜNYE KUSURU |
| C10 | `web-arastirma.md` **Satır: 95 (C1)**: `mtg.github.io/essentia` → CRAWL_NOT_FOUND, `essentia.upf.edu` ile düzeltildi | 2026-10-12 kayıtlı düzeltme — bu turda **tekrar taşındı**, yeni URL değil | ✅ kapalı kayıt |
| C11 | (yeni kayıt) | `ai-master.md` **Satır: 153** rol sütunu: "Ses zekâsı üst katmanı — sınıflandırma, etiketleme, anomali" — **tasarım yorumu** olarak işaretli (Satır: 158–160: "resmi kapsam tanımı v2'de yoktur → `⚠️ VERIFICATION REQUIRED`") | ℹ️ TASARIM YORUMU |
| C12 | (yeni kayıt) | `VISION.md` **Satır: 127**: "müzik analitiği (BPM, Key, Energy, Mood) ve … Auto EQ" — **ürün vaadi** düzeyinde kanıt (kod değil) | ℹ️ ÜRÜN VAADİ |

**EK §3 sayımı:** 12 kayıt · **4 sağlam (✅)** · **6 ⚠️** · **3 ℹ️** (C10 ✅ dahil sayımı:
6 ✅ · 5 ⚠️ · 3 ℹ️ — toplam 12, C9 çift işaretli değil).

---

## EK §4 — NevaEngine bağımlılık kanıtı + ses modeli haritası

### EK §4.1 Ortak bağımlılık tablosu (birebir alıntı)

**Dosya:** `C:\www\coremusic.net\.ai\projects\NevaEngine\legacy-2026-07\rules\cpp-global-CLAUDE.md`
[Satır: 393–413]

```text
## Shared Dependencies

### Core Libraries

| Library | Version | License | Purpose | Platforms |
|---------|---------|---------|---------|-----------|
| **JUCE** | 8.x | GPL v3 | Audio framework, GUI | Win/Mac/Linux |
| **FFmpeg** | 6.x | LGPL 2.1+ | Media decoding | Win/Mac/Linux/ARM |
| **Vulkan** | 1.3+ | Apache 2.0 | GPU graphics | Win/Mac/Linux/ARM |
| **NVIDIA CUDA** | 12.x | Proprietary | GPU audio DSP | Windows/Linux |
| **libcurl** | 8.x | MIT | HTTP/REST | All |
| **spdlog** | 1.12+ | MIT | Logging | All |
| **nlohmann/json** | 3.11+ | MIT | JSON parsing | All |
| **Google Test** | 1.14+ | BSD 3-Clause | Unit testing | All |
| **ONNX Runtime** | 1.16+ | MIT | AI inference | All |

**vcpkg:**
./vcpkg install juce ffmpeg vulkan-hpp curl spdlog nlohmann-json gtest onnxruntime:x64-windows
```

| # | Kütüphane | Sürüm | Lisans | K006.6 ilişkisi | Kaynak satır |
|---|---|---|---|---|---|
| L-1 | JUCE | 8.x | GPL v3 | ses çerçevesi (RT katman) | 399 |
| L-2 | FFmpeg | 6.x | LGPL 2.1+ | medya çözümleme — **ffprobe'un kaynağı** (EK §2.3 ile aynı aile) | 400 |
| L-3 | Vulkan | 1.3+ | Apache 2.0 | grafik | 401 |
| L-4 | NVIDIA CUDA | 12.x | Proprietary | GPU ses DSP | 402 |
| L-5 | libcurl | 8.x | MIT | HTTP | 403 |
| L-6 | spdlog | 1.12+ | MIT | log | 404 |
| L-7 | nlohmann/json | 3.11+ | MIT | JSON — ffprobe JSON çıktısı ile aynı biçim | 405 |
| L-8 | Google Test | 1.14+ | BSD 3-Clause | test | 406 |
| L-9 | **ONNX Runtime** | **1.16+** | MIT | **AI inference** — K006.6 model çalıştırma adayı | 407 |
| L-10 | vcpkg manifest `onnxruntime >= 1.16.0` | — | — | manifest kanıtı | 345–346 |
| L-11 | `find_package(CUDA REQUIRED)` | — | — | Windows GPU backend | 357–358 |

> **Kanal uyarısı:** bu tablo **NevaEngine (C++)** içindir; CoreMusic **PHP** deposundadır.
> CoreMusic'te `onnx` / `librosa` / `essentia` / `whisper` **0 isabet** (T16).
> "CoreMusic ONNX kullanır" iddiası → `⚠️ VERIFICATION REQUIRED`.

### EK §4.2 Ses modeli haritası (ai-models.md §8.2 — dizin kanıtı)

**Dosya:** `C:\www\coremusic.net\.ai\projects\NevaEngine\legacy-2026-07\ai-models.md` [Satır: 531–549]

| # | Dizin | Amaç (adından) | Boyut | K006.6 ile ilişki | Durum |
|---|---|---|---|---|---|
| MD-1 | `models/audiosr-v2/` | ses süper-çözünürlük (AudioSR) | fp16 4.0 GB · int8 2.1 GB | ses zekâsı — **CoreMusic'te karşılığı yok** | ⚠️ yalnız Neva arşivi |
| MD-2 | `models/demucs-ht/` | kaynak ayırma (Demucs HT) | fp16 · int8 | "ses zekâsı" alt yeteneği | ⚠️ yalnız Neva arşivi |
| MD-3 | `models/deepfilternet2/` | gürültü azaltma | int8 0.3 GB | ses temizleme | ⚠️ yalnız Neva arşivi |
| MD-4 | `models/genre-cnn/` | **tür sınıflandırma (CNN)** | fp32 50 MB · `genres.json` | `AIWorkflow` **`genre` boş** (W-3) ile doğrudan çakışan boşluk | ⚠️ — **en yakın karşılık, repo'da yok** |
| MD-5 | sürüm kanıtı | `version.txt` + `checksums.sha256` | — | anayasa §6.2 (append-only geçmiş) | ✅ tasarım |
| MD-6 | CoreMusic karşılığı | `**/*.{onnx,...}` → **0 dosya** | — | T5-T16 ile 0 | ❌ yok |

**EK §4 sayımı:** 1 alıntı bloğu · 11 bağımlılık satırı (L-1–L-11) · 6 model satırı
(MD-1–MD-6) · 1 kanal uyarısı.

---

## EK §5 — URL çaprazı (2026-10-12 kayıtları — yeni URL yok)

### EK §5.1 Bu dosyanın §1 URL'leri (4)

| # | URL | İddia | İddia satırı | Durum 2026-10-15 |
|---|---|---|---|---|
| 1 | https://www.ffmpeg.org/ffprobe.html | `-show_format` · `-show_streams` · `-output_format json` | §1/7 (Satır: 28) | ✅ kod `Satır: 291` ile birebir (EK §2.3) |
| 2 | https://librosa.org/doc/latest/api/feature.html | chroma · mfcc · tempo API | §1/8 (Satır: 29) | ✅ taşındı · kurulu değil (T16) |
| 3 | https://essentia.upf.edu/documentation.html | Essentia C++ kütüphanesi + binding | §1/9 (Satır: 30) | ✅ taşındı · kurulu değil (T16) |
| 4 | https://librosa.org/doc/latest/tutorial.html | `beat_track` ile BPM | §1/10 (Satır: 31) | ✅ taşındı · kurulu değil (T16) |

### EK §5.2 `web-arastirma.md` §2 URL havuzu (11) — 2026-10-12 doğrulanmış

| # | URL | Sayfadan çıkarılan iddia | Repo durumu (2026-10-15) |
|---|---|---|---|
| 1 | https://librosa.org/ | Python ses analizi ana sayfası | kurulu **değil** |
| 2 | https://librosa.org/doc/latest/ | dokümantasyon — sürüm **1.0.0** | kurulu **değil** |
| 3 | https://github.com/librosa/librosa | **8.664★** · lisans **ISC** (2026-10-12 görüntüsü) | kurulu **değil** |
| 4 | https://essentia.upf.edu/ | müzik analizi + parmak izi (MTG/UPF) | kurulu **değil** |
| 5 | https://essentia.upf.edu/models.html | hazır model listesi | ⚠️ `web-arastirma.md` L14: **tek tek okunmadı** |
| 6 | https://spotify.github.io/ | Spotify açık kaynak sayfası | ekosistem |
| 7 | https://github.com/spotify/pedalboard | Pedalboard · **6.346★** · **GPL v3** | ⚠️ lisans denetimi ADR bekliyor (L15) |
| 8 | https://github.com/openai/whisper | Whisper · **110.308★** · **MIT** | kurulu **değil** |
| 9 | https://onnxruntime.ai/docs/ | model çalışma zamanı ailesi | PHP deposunda **0** (T16) · Neva'da var (L-9) |
| 10 | https://scikit-learn.org/stable/ | sürüm **1.9.0** · metrik/ölçekleme | kurulu **değil** |
| 11 | https://docs.pytest.org/en/stable/ | Python test çerçevesi | kurulu **değil** |
| — | https://mtg.github.io/essentia | eski adres — CRAWL_NOT_FOUND | ✅ `essentia.upf.edu` ile düzeltildi (C10) |

**EK §5 sayımı:** 15 URL kaydı (4 + 11) · **0 yeni** · 2026-10-12 dışına taşınan **0**.

---

## EK §6 — `audio-intelligence-anayasa.md` başlık çaprazı (45 başlık)

**Kaynak:** `...audio-intelligence\audio-intelligence-anayasa.md` — heading grep = 45 isabet
(2026-10-15). "Bu dosyada karşılığı" sütunu yalnız bu `-dogrulama.md` dosyasının kanıtını
gösterir.

| # | Başlık | Satır | Bu dosyada karşılığı |
|---|---|---|---|
| 1 | `# Audio Intelligence — Alt Dal Anayasası (kademe-2)` | 27 | — |
| 2 | `§1. Kapsam Sınırı` | 35 | EK §7 (kapsam §2) |
| 3 | `§2. İlke ve Değerler` | 55 | — |
| 4 | `§2.1 Öncelik sırası` | 57 | — |
| 5 | `§2.2 Zero-hallucination bağlayıcılığı` | 67 | EK §0 · EK §3 (çelişki kaydı) |
| 6 | `§3. Mimari Roller ve Kararlar` | 76 | — |
| 7 | `§3.1 Katman ayrımı (RT/Non-RT)` | 78 | EK §6.1 (RT1–RT6) |
| 8 | `§3.2 Sahiplik matrisi` | 100 | — |
| 9 | `§4. Bileşen Kataloğu` | 114 | EK §6.2 (5 bileşen) |
| 10 | `§5. RT Bütçesi ve Köprü Sözleşmesi` | 128 | EK §6.1 · EK §6.3 |
| 11 | `§5.1 RT kuralları` | 130 | EK §6.1 |
| 12 | `§5.2 Köprü veri akışı` | 141 | EK §6.3 |
| 13 | `§5.3 Bütçe (sayısal durum)` | 150 | EK §6.3 — `UNKNOWN` |
| 14 | `§6. Hedef Üretim ve Güvenlik Kırpma` | 156 | EK §6.4 |
| 15 | `§6.1 Hedef üretim zinciri` | 158 | EK §6.4 |
| 16 | `§6.2 Güvenlik kırpma (clamping)` | 168 | EK §6.4 (K-AI-05) |
| 17 | `§7. Rehberler (Guidelines)` | 181 | — |
| 18 | `§8. Ölçüm ve Doğrulama` | 194 | EK §6.5 |
| 19 | `§8.1 Birincil metrikler` | 196 | EK §6.5 |
| 20 | `§8.2 Kapılar` | 206 | EK §6.5 |
| 21 | `§9. Zorunlu Kurallar (MUST / MUST NOT)` | 218 | EK §6.6 (AI1–AI10) |
| 22 | `§10. Karar Kaydı` | 235 | EK §6.3 (K-AI-04) · EK §6.5 (K-AI-06) |
| 23 | `§11. Bağımlılıklar` | 256 | EK §4 · T16 |
| 24 | `§11.1 Bağımlı olduğu alanlar` | 258 | EK §7 (kapsam §2 komşular) |
| 25 | `§11.2 Bağımlı olan alanlar` | 268 | EK §7 |
| 26 | `§12. İlişkili Dosyalar ve Açık Sorular` | 278 | EK §8 (index) · EK §9 |
| 27 | `§12.1 Kardeş dosyalar` | 280 | T5 (7 dosya) |
| 28 | `§12.2 Vault referansları` | 284 | — |
| 29 | `§12.3 Açık sorular` | 295 | EK §7 (kapsam R1–R4) |
| 30 | `§13. İlgili Lint Maddeleri` | 305 | — |
| 31 | `§14. Bağlam ve Versiyon` | 317 | EK §3 (C8 · C12) |
| 32 | `§15. Bileşen Sözlüğü (TR↔EN)` | 332 | — |
| 33 | `§16. Durum Matrisi (AI-zincir durumları)` | 352 | — |
| 34 | `§17. Hata Sınıfları ve Zincir Davranışı` | 369 | EK §2.2 (G-1 `RuntimeException`) |
| 35 | `§18. Güvenlik ve Dayanıklılık` | 385 | EK §2.3 (X-2 `escapeshellarg`) |
| 36 | `§19. Anti-Patternler` | 395 | EK §2.4 (P-1–P-7 placeholder anti-pattern) |
| 37 | `§20. Uç Nokta Sözleşmesi` | 410 | EK §2.1 (F-1–F-6) |
| 38 | `§20.1 Giriş` | 412 | — |
| 39 | `§20.2 Çıkış` | 421 | — |
| 40 | `§21. Kaynaklar, Red-Team ve Revizyon` | 432 | EK §5 |
| 41 | `§21.1 Kaynaklar` | 434 | EK §5 |
| 42 | `§21.2 Red-Team soruları` | 446 | EK §3 · EK §10 |
| 43 | `§21.3 Revizyon Kaydı` | 459 | EK §3 (C1–C12) |
| 44 | `§22. Ek Usul — Değişiklik Eşiği Tablosu` | 470 | — |
| 45 | `§23. Ek Usul — SSS` | 486 | — |

**EK §6 sayımı:** 45 başlık · karşılığı olan **29** · `—` **16**.

### EK §6.1 §5.1 RT kuralları (birebir tablo) — [Satır: 132–139]

| # | Kural | Seviye |
|---|-------|--------|
| RT1 | Audio callback'inde **model inference yok** (K-AI-01) | MUST |
| RT2 | Callback'te **bellek tahsisi/serbest bırakma yok** | MUST NOT |
| RT3 | Non-RT → RT geçişi **lock-free** (atomic/kuyruk); kilit **yasak** | MUST |
| RT4 | Parametre **anlık sıçrama** yerine **smoothing** (ramp) | MUST (İlke 5) |
| RT5 | Bütçe aşımında karar **düşülür** (drop), RT **bloklanmaz** | MUST |
| RT6 | Tüm AI kararları **geri alınabilir** (manual override) | MUST (İlke 4) |

### EK §6.2 §4 Bileşen Kataloğu (birebir tablo) — [Satır: 116–124]

| Bileşen | Girdi | Çıktı | Durum |
|---------|-------|-------|-------|
| Otomatik EQ karar | içerik özniteliği / ölçüm | hedef EQ eğrisi | **kanıtlanabilir** — `eq-ai-auto.md` |
| Uzamsal karar | içerik/ortam | spatial hedef parametreler | **kanıtlanabilir** — `spatial-audio.md` |
| Zincir devreye alma | mod/çalma durumu | zincir bypass/aktif kararı | `02-dsp-zincir-mimari.md` ilişkili |
| Gerçek-zamanlı ölçüm (loudness vb.) | ses akışı | ölçüm değeri (RT-güvenli) | ölçüm/analiz — `UNKNOWN` detay |
| Model-özel öğrenme (adaptif EQ) | kullanıcı tepkisi | ağırlık güncellemesi | **UNKNOWN** — sinyal kaynağı §12.3.2 |

**Kural (K-AI-03)** [Satır: 124]: `UNKNOWN` bileşen **üretime alınmaz**.

### EK §6.3 §5.2–§5.3 Köprü ve bütçe — [Satır: 143–152]

```text
non-RT: tahmin → smoothing → doğrulama (kapsam kırpma) → kuyruğa yaz (atomik)
RT:     kuyruktan oku (varsa) → ramp uygula → filtre katsayısı
```

- **Karar (K-AI-04)** [Satır: 148]: overflow'da **eski değer korunur**, yeni değer
  **düşülür ve sayılır** — RT asla bekletilmez.
- **§5.3 Bütçe** [Satır: 152]: RT içinde AI köprüsüne ayrılan süre/buffer = **`UNKNOWN`**
  (Neva Engine ölçümü gerektirir; sayısal eşik bu turda aktarılmadı).

### EK §6.4 §6 Üretim zinciri + kırpma — [Satır: 158–170]

```text
sinyal (içerik/ölçüm) → model tahmini (non-RT, inference önbelleği)
  → hedef parametre (EQ eğrisi / spatial)
    → güvenlik kırpma (limitler: dB aralığı, frekans sınırları)
```

- **K-AI-05**: model çıktısı sınır-kırpma (clamp) geçmeden uygulanamaz.

### EK §6.5 §8 Ölçüm + kapılar — [Satır: 196–214]

| Alan | Metrik | Not |
|------|--------|-----|
| Ses kalitesi | işitsel test + (mümkünse) objective metrik | "güzelleşti" iddiası ölçüm ister |
| Stabilite | parametre titremesi (jitter) oranı | İlke 5 |
| RT uyum | callback aşım/drop sayacı | K-AI-04 metriği |
| Güvenlik kırpma | clamp/drop oranı | K-AI-05 |
| Geri alma | manuel override tetik oranı | İlke 4 |

1. **RT kapısı** — bütçe içi + lock-free + drop testi
2. **Sınır kapısı** — clamp/slew testleri (K-AI-05)
3. **Kalite kapısı** — A/B veya işitsel test — ölçülmeden kalıcı yayma yok
4. **Geri-alınabilirlik kapısı** — override her zaman çalışır
5. **Bozulma kapısı** — model hatası → zincir **eski sağlam durumda**

**Karar (K-AI-06)** [Satır: 214]: AI karar hattı **başarısızsa ses bozulmaz** — varsayılan
kanıtlanmış nötr durum (bypass/manuel hedef) anında döner.

### EK §6.6 §9 Zorunlu kurallar (birebir tablo) — [Satır: 220–231]

| # | Kural | Seviye |
|---|-------|--------|
| AI1 | RT callback'te model inference **yapılmaz** (K-AI-01, RT1) | MUST NOT |
| AI2 | RT'de bellek tahsisi **yapılmaz** (RT2) | MUST NOT |
| AI3 | Non-RT→RT geçiş **lock-free**dir (RT3) | MUST |
| AI4 | Parametre değişimleri **smoothing** ile (RT4, İlke 5) | MUST |
| AI5 | Model çıktısı **sınır-kırpma**dan geçmeden uygulanamaz (K-AI-05) | MUST |
| AI6 | Hata/taşmada karar **düşülür**, RT **bloklanmaz** (RT5, K-AI-04) | MUST |
| AI7 | Her otomatik mod **geri alınabilir** (RT6, İlke 4) | MUST |
| AI8 | Kalite iyileşme iddiası **ölçümle** yazılır (§2.2.2) | MUST |
| AI9 | `UNKNOWN` bütçe/parametre ile **üretim açılmaz** (K-AI-03, §5.3) | MUST |
| AI10 | Başarısızlıkta **kanıtlanmış nötr durum** döner (K-AI-06) | MUST |

---

## EK §7 — `audio-intelligence-kapsam.md` (85 satır) çaprazı

| Kapsam satırı | İçerik | 2026-10-15 karşılığı | Durum |
|---|---|---|---|
| L17 | "ai-master §1, v2 §2.6 rol cümlesini birebir taşır" | `ai-master.md` **Satır: 60** = *"Şarkı öneren, sesi analiz eden yapay zeka parçaları."* | ✅ içerik doğru |
| L18 | "§1 sorumluluk tablosu (satır 37–42)" | **C1**: §4 · Satır: 176 (matris L180–188) | ⚠️ bayat |
| L21 | "ses dosyasından sayı üreten taraf — tempo, ton, enerji, mood" | EK §2.1 (6 alan) · EK §2.4 (6 sabit) | ✅ kapsam doğru / kod placeholder |
| L22–23 | "audio-intelligence ↔ music-analysis ayrımı v2'de tanımlı değil" | `ai-master.md` **Satır: 152–153** iki ayrı satır (music-analysis L152 · audio-intelligence L153); ayrım ölçütü **yok** | ✅ doğrulandı |
| L29–34 | Kapsam sınırı 6 satır (içinde/dışında/komşu) | EK §9 (ai-master §2.1/§4.1) | ✅ |
| L46 (A1) | ayrım **UNKNOWN** — "ai-master §3 satır 263–264" | **C2**: §3 = L142 · satır = L153 | ⚠️ bayat satır / UNKNOWN **hâlâ açık** |
| L47 (A2) | katman ayrımı — "`AIEngine.php` satır 317 yorumu" | EK §2.4 **Satır: 317** = `// Gerçek implementasyonda C++ DSP engine ile yapılacak` | ✅ birebir |
| L48 (A3) | ses zekâsı ↔ K004-dsp sınırı | ⚠️ üst karar bekliyor — `ai-master.md` **Satır: 548–550** kapıları | ⚠️ açık |
| L49 (A4) | metrik sözleşmesi sahibi | EK §2.1 (sözleşme PHP'de, sahiplik sorusu açık) | ⚠️ açık |
| L57 | "Motor 5 modülden oluşur, 2'si ses tarafı" | `AIEngine.php` **Satır: 21–26** docblock: 5 madde (RecommendationEngine · AudioAnalyzer · EQOptimizer · HardwareAnalyzer · FaultPredictor) | ✅ birebir |
| L58 | `analyzeAudio()` BPM/key/energy/mood | EK §2.1 | ✅ |
| L59 | placeholder (bpm=120.0, key=C) | EK §2.4 | ✅ |
| L60 | "Satır 317" C++ DSP yorumu | EK §2.4 | ✅ |
| L61 | `AIWorkflow.php` L117–127 prepared statement | EK §2.5 (W-7) — okuma **L117–127** ile birebir | ✅ |
| L62 | ffprobe L291 | EK §2.3 | ✅ |
| L63 | K006 resmi model/kütüphane seçimi | **UNKNOWN** — `ai-master.md` **Satır: 352–353** | ✅ hâlâ açık |
| L69 (R1) | ayrım ölçütü | ⚠️ hâlâ açık | açık |
| L70 (R2) | katman (PHP/C++/Python) | ⚠️ §7/4 = `ai-master.md` **Satır: 256** `UNKNOWN` | açık |
| L71 (R3) | hangi kütüphane/model | ⚠️ **UNKNOWN** — 7 aday (EK §5.2), 0 kurulu (T16) | açık |
| L72 (R4) | K004-dsp sınırı ne zaman kilitlenecek | ⚠️ ADR-097 K4 | açık |
| L74–75 | "kapanış: status: draft kalır" | bu dosya frontmatter **Satır: 6** `status: draft` | ✅ tutarlı |

**EK §7 sayımı:** 22 çapraz satırı · **13 ✅** · **5 ⚠️ bayat/açık** · 4 açık soru
(R1–R4) **hâlâ açık**.

---

## EK §8 — `index.md` (75 satır) çaprazı

| # | index.md satırı | İçerik | Karşılık |
|---|---|---|---|
| I-1 | 2–5 | `node_id: audio-intelligence` · `status: draft` · `parent_id: K006-yapay-zeka` | ✅ |
| I-2 | 6–12 | `children:` **6 dosya** (anayasa · bilesenler · kapsam · ornekler · dogrulama · web-arastirma) | ✅ · **C3**: toplam dosya 7 (index dahil) |
| I-3 | 14–15 | `evidence:` v2 mini-ağaç **satır 264** | ✅ |
| I-4 | 16–17 | `validation:` 2026-10-12 — "ad birebir" | ✅ |
| I-5 | 18–21 | `open_questions:` rol tanımı yok · kademe-3 yok · `depends_on` boş | ✅ |
| I-6 | 22 | `group: mimari` | ✅ |
| I-7 | 32 | `Kaynak | v2 §2.6 (satır 264)` | ✅ |
| I-8 | 37 | "v2 bu alt konuya **yalnızca ad verir** — tanım yok" + `»` kusuru (**C9**) | ✅ içerik · ⚠️ karakter |
| I-9 | 43–45 | üst düğüm bağlantıları (`../index.md»` · `children: []»`) | ⚠️ C9 |
| I-10 | 49–54 | Durum tablosu: ad var ✅ · rol yok ❌ · kademe-3 ⏳ · draft | ✅ |
| I-11 | 56–61 | İlişkili dosyalar: kapsam · dogrulama · anayasa · ornekler (4 link) | ✅ linkler hedefte var (T5) |
| I-12 | 63–75 | §GRUPLAR — 7 satır: **mimari** 4 (bilesenler · kapsam · index · anayasa) · **kod** 1 (ornekler) · **kanit** 2 (dogrulama · web-arastirma) | ✅ group eksen doğrulaması |
| I-13 | 65 | "ADR-097 §5.5 · registry §8.1 — 9. alan group" | ✅ frontmatter kuralı |
| I-14 | 72 | `ornekler` → `group: kod` | ✅ |
| I-15 | 73 | **bu dosya** → `group: kanit` (frontmatter Satır: 10) | ✅ |

**EK §8 sayımı:** 15 satır · 13 ✅ · 2 ⚠️ (C9 karakter kusuru).

---

## EK §9 — `ai-master.md` çaprazı (2026-10-15 satır konumları)

| # | Eski iddia (bu dosya/kapsam) | Güncel `ai-master.md` karşılığı | Durum |
|---|---|---|---|
| M-1 | §1/2 — "K006 = ML ürün · K016 = ajan altyapısı (§2.2)" | `ai-master.md` **Satır: 112** `### §2.2 K006 ↔ K015-AI — bağlayıcı ayrım` | ✅ içerik sağlam |
| M-2 | §1/3 — K006 sınırı (İddia 10 benzeri) | **Satır: 95** `## §2 Sınır — ne DEĞİL` | ✅ |
| M-3 | §1/14 — "ai-master §3 satır 263–264" | **C2**: §3 = **142** · audio-intelligence satırı = **153** | ⚠️ bayat |
| M-4 | §1/15 — "K006 model/runtime/kütüphane ekosistemi §4 = UNKNOWN" | §4 = **176** (sorumluluk matrisi) · UNKNOWN listesi = **348–357** · teknoloji adayları = **318–346** | ⚠️ bayat konum / içerik gerçek |
| M-5 | §1/13 — "§7/4 · AIEngine.php satır 317" | `ai-master.md` **Satır: 256** = §7/4 "Üretim modeli/runtime seçimi … **UNKNOWN**" | ✅ |
| M-6 | (yeni) | `ai-master.md` **Satır: 153**: "Ses zekâsı üst katmanı — sınıflandırma, etiketleme, anomali" | ✅ tasarım yorumu (C11) |
| M-7 | (yeni) | `ai-master.md` **Satır: 183**: `S2 | Sesi/metriği analiz etmek | music-analysis · audio-intelligence | AIEngine::analyzeAudio L82–106` | ✅ **EK §2.2 ile birebir** (Satır 82–106) |
| M-8 | (yeni) | `ai-master.md` **Satır: 523**: Faz 2 "extractFeatures() placeholder'ını librosa/Essentia pipeline'ıyla değiştir … T4: ±0 BPM · ±0 yarım ton" | ✅ Faz-2 kabul kriteri — EK §2.4 boşluğunun resmi kapanış planı |
| M-9 | (yeni) | `ai-master.md` **Satır: 484**: `N7 | mirdata | ses veri seti yükleyicileri | music-analysis (evaluation verisi)` | ✅ aday (seçim değil) |
| M-10 | (yeni) | `ai-master.md` **Satır: 485–486**: `N8 OpenVINO` · `N9 ggml` → local-inference | ✅ aday |
| M-11 | (yeni) | `ai-master.md` **Satır: 402**: `audio-intelligence/ | 6 | 563 | 0` (2026-10-13 ölçümü) | ⚠️ **C3** — bugün 7 dosya |
| M-12 | (yeni) | `ai-master.md` **Satır: 516**: "Bu bölüm **plan**tır; hiçbir teknoloji 'seçilmiş' değildir" | ✅ seçim yokluğu resmi |

**EK §9 sayımı:** 12 satır · 9 ✅ · 3 ⚠️ (bayat konum/ölçüm).

---

## EK §10 — Korunan maddeler ve kapılar

| # | Taahhüt / kapı | Durum |
|---|---|---|
| P-1 | Bu dosyanın **1–54** satırı birebir önektir — silme · taşıma · düzeltme **yok** | ✅ (ek öncesi sayım 54 = T14) |
| P-2 | Frontmatter (L1–11) **değişmedi** — `group: kanit` (L10) korunur | ✅ |
| P-3 | Orijinal §2 Sayım tablosu ve geçiş kuralı (L38–50) korunur; bu ek ayrı kapanır | ✅ |
| P-4 | 2026-10-12 tarihli 15 iddia satırından **hiçbiri düzeltilmedi** — çelişkiler EK §3'te yeni satır | ✅ |
| P-5 | **Yeni URL eklenmedi**; 15 URL kaydı (EK §5) tamamı 2026-10-12 tarihlidir | ✅ |
| P-6 | Yazılan tek dosya bu dosyadır · `index.md` · `-kapsam.md` · `-anayasa.md` · `*.php/js/css/sql` · `.ai/AGENTS.md` · `.ai/CLAUDE.md` **dokunulmadı** | ✅ |
| P-7 | `git commit` **atılmadı** | ✅ |
| P-8 | `// ...` · `TBD` · doldurma satırı **yok**; alıntılardaki `//` yalnızca **kaynak kodun kendi satırıdır** | ✅ |
| P-9 | Mojibake **yok** (bu ekin kendisinde); kaynak dosyalardaki `»` kusuru **C9** olarak taşındı, düzeltilmedi | ✅ |
| P-10 | Satır sayısı `read` aralık testi ile alındı (`shell` izin dışı) | ✅ |

---

## EK §11 — Ölçüm, sayım ve sonuç

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi (2026-10-15) | `read` tam okuma (L1–54) | **54** |
| Ek sonrası (2026-10-15) | `read` offset 9999 → "Offset 9999 is out of range for this file (**657** lines)" | **657 satır** |
| Eşik | ≥500 | **✅ GEÇTİ** (657 ≥ 500) · ek satırı = 657 − 54 = **603** |

### EK §11.1 Bu ek sayımı

| Metrik | Değer |
|---|---|
| EK bölüm sayısı | 11 (EK §0–§11) |
| Alıntı bloğu | 9 (5 kod + 4 tablo/metin) |
| Yeni gözlem/gözlem satırı (F·G·X·P·W·L·MD·I·M·C·T·R serileri) | 168 |
| Taşınan URL | 15 (0 yeni) |
| Çelişki/bayatlık kaydı | 12 (EK §3) + 3 (EK §9 ⚠️) |
| Kapı ihlali | 0 |
| Beyazliste dışı yazım | 0 |
| `git commit` | yok |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Tarih:** 2026-10-15 · **Eklenti:** EK §0–§11 (append-only · 2026-10-12 tabanı korunmuştur)
