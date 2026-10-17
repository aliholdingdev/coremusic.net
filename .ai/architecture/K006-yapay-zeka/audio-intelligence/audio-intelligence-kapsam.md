---
title: "audio-intelligence — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/audio-intelligence
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# audio-intelligence — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Sesi ve metriği analiz etmek"** sorumluluğuna bağlar —
ama master bu tabloyu açıkça *tasarım yorumu* olarak işaretler; **resmi çocuk kapsamı v2'de yoktur.**

Basitçe: ses dosyasından sayı üreten taraf — tempo (BPM), ton (key), enerji, mood.
`audio-intelligence` ile `music-analysis` arasındaki ayrım v2'de **tanımlı değildir**; bu kayıt ikisini birleştirmez,
ayrımı §3/R1'e sorar.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Ses dosyasından öznitelik (feature) çıkarımının planı | Özniteliklerin veritabanında saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Ses zekâsı modelinin K006 kapsamında çalıştırılması | DSP zinciri ve sinyal üretimi | K004-dsp (ai-master §2.1) |
| 3 | Çıktının (BPM/key/energy/mood) öneri akışına verilmesi | Öneri sıralamasının kendisi | recommendation (ai-master §3) |
| 4 | Ses zekâsı için K006 ↔ K016 tek ML çağrı noktası | Ajan çalışma zamanı, prompt yönetimi | K015-AI (ai-master §2.1) |
| 5 | Metrik **sözleşmesi** (alan adları, tipler) | "çal/beğen" iş kuralının yorumu | K008-domain (ai-master §2.1) |
| 6 | Model/format seçimi için kanıt toplama | Firmware seviyesinde ses işleme | K005-firmware (ai-master §2.1) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K015-AI ajan altyapısı
bu kapsama **taşınamaz**.

**Yasak:** yukarıdaki tablo resmi sınır değil, **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez;
kilit ADR-097 K4 (kademe-3)tedir.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | audio-intelligence ↔ music-analysis (ikisi de "ses analizi") | **UNKNOWN** — ayrım v2'de yok | ai-master §3 satır 263–264 |
| A2 | Feature çıkarma katmanı: PHP mi, C++ DSP mi | ⚠️ taslak — repo kodu C++'a işaret eder | `AIEngine.php` satır 317 yorumu |
| A3 | Ses zekâsı ↔ K004-dsp sinyal yolu | ⚠️ üst karar bekliyor | ai-master §2.1 |
| A4 | Metrik sözleşmesinin sahibi (K006 mı, K008 mi) | ⚠️ VERIFY REQUIRED | bagimliliklar §2/2 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 264'te birebir geçer | `.ai/architecture/K006-yapay-zeka/audio-intelligence/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `audio-intelligence/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Motor 5 modülden oluşur, 2'si ses tarafı (RecommendationEngine · AudioAnalyzer) | `shared/src/AI/AIEngine.php` satır 21–23 | ✅ dosya okundu |
| 4 | `analyzeAudio()` BPM/key/energy/mood döndürür | `shared/src/Contracts/AI/AIEngineInterface.php` satır 30–36 | ✅ dosya okundu |
| 5 | Özellik çıkarma henüz **placeholder** (bpm=120.0, key=C sabit) | `shared/src/AI/AIEngine.php` satır 315–327 | ✅ dosya okundu |
| 6 | "Gerçek implementasyonda C++ DSP engine ile yapılacak" | `shared/src/AI/AIEngine.php` satır 317 | ✅ dosya okundu |
| 7 | Ses analizi workflow'u sonuçları `tracks` tablosuna prepared statement ile yazar | `shared/src/AI/AIWorkflow.php` satır 117–127 | ✅ dosya okundu |
| 8 | Metadata çıkarma ffprobe ile yapılır | `shared/src/AI/AIEngine.php` satır 291 | ✅ dosya okundu |
| 9 | K006'nın resmi model/kütüphane seçimi | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | audio-intelligence ↔ music-analysis ayrımı hangi ölçütlerle? | ⚠️ VERIFICATION REQUIRED |
| R2 | Feature çıkarma hangi katmanda (PHP / C++ DSP / Python)? | ⚠️ ai-master §7/4 — **UNKNOWN** |
| R3 | Hangi ses analizi kütüphanesi/modeli seçilecek? (adaylar da doğrulanmadı) | ⚠️ **UNKNOWN** — seçim yok |
| R4 | Ses zekâsı ↔ K004-dsp sınırı ne zaman kilitlenecek? | ⚠️ ADR-097 K4 |

**Kapanış:** status: draft kalır. R1–R3 kapanmadan bu dosyada **teknoloji ya da resmi sınır iddiası** yazılmaz
(ai-master §4.1 · yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[audio-intelligence-dogrulama]]
- [[audio-intelligence-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

# EK — 2026-10-15 Derinleştirme Eki (append-only)

## EK §0 — Eklenti notu

Bu ek, 2026-10-12 tarihli kapsam kaydının **sonuna** eklenmiştir; L1–85 içeriği değişmemiş,
eski metin bu ekin tam öneki olarak korunmuştur (append-only · `.ai/AGENTS.md` §25.3 madde 1–2).

| Kural | Bu ek için durum |
|---|---|
| Append-only (eski satır silme/değiştirme) | ✅ uygulandı |
| Yeni URL eklenmesi yasak (yalnız 2026-10-12 URL'lerinin taşınması) | ✅ uygulandı — §14'te taşınanların kaynağı `audio-intelligence-web-arastirma.md` L41–51 + L95 |
| `index.md` · `-anayasa.md` · `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `*.php/js/css/sql` yazımı | ❌ dokunulmadı |
| `git commit` | ❌ atılmadı (orkestratöre ait) |
| `// ...` · `TBD` · doldurma satırı | ❌ yok — alıntılardaki `//` yalnızca kaynak kodun kendi satırıdır |
| Satır ölçümü | `read` aralık testi (`offset` 9999 → "Offset ... out of range for this file (X lines)") — `shell` bu oturumda izin dışı |

**Bu ek ne iddia eder, ne etmez:** iddiası yalnızca **okunabilir satır kanıtı**dır; R1–R3
kapanmadan bu dosyada teknoloji ya da resmi sınır iddiası yine yazılmaz (eski §3 kapanış cümlesi
L74–75 aynen bağlayıcıdır).

## EK §1 — Taze tarama (2026-10-15, `read` ile tam okuma)

| # | Ne tarandı | Dosya | Satır | Bulgu |
|---|---|---|---|---|
| T1 | Sınıf docblock — 5 modül | `shared/src/AI/AIEngine.php` | 18–27 | RecommendationEngine · AudioAnalyzer · EQOptimizer · HardwareAnalyzer · FaultPredictor |
| T2 | Sabitler | `shared/src/AI/AIEngine.php` | 30–32 | `MAX_RECOMMENDATIONS = 20` · `MIN_SCORE = 0.7` · `DIVERSITY_TARGET = 0.30` |
| T3 | `analyzeAudio()` gövdesi | `shared/src/AI/AIEngine.php` | 82–106 | 10 anahtar döner (bpm, key, energy, mood, danceability, acousticness, duration, sample_rate, bit_depth, channels) |
| T4 | `extractMetadata()` | `shared/src/AI/AIEngine.php` | 288–310 | `ffprobe ... -print_format json` kabuk çağrısı + `shell_exec` |
| T5 | `extractFeatures()` | `shared/src/AI/AIEngine.php` | 315–327 | placeholder — sabit 6 değer, dosya hiç okunmaz |
| T6 | Sözleşme | `shared/src/Contracts/AI/AIEngineInterface.php` | 30–36 | `analyzeAudio(string $filePath): array` — 6 anahtarlık `@return` şablonu |
| T7 | Workflow — analiz zinciri | `shared/src/AI/AIWorkflow.php` | 105–127 | dispatch → analyzeAudio → knowledge search → DB UPDATE |
| T8 | `genre` boşluğu | `shared/src/AI/AIWorkflow.php` | 112–113 | yorum satırı: "analyzeAudio() 'genre' anahtarını döndürmez" → `$genre = ''` |
| T9 | Dal indeksi children | `.ai/architecture/K006-yapay-zeka/audio-intelligence/index.md` | 6–12 | **6 dosya** (anayasa, bilesenler, kapsam, ornekler, dogrulama, web-arastirma) |
| T10 | Dal indeksi `»` kusuru | `.../audio-intelligence/index.md` | 37, 43–45 | kapanış `»` yerine düz tırnak — bilinen kusur, düzeltilmedi |
| T11 | Üst indeks children | `.ai/architecture/K006-yapay-zeka/index.md` | 7–15 | **9 çocuk** — audio-intelligence 6. sırada |
| T12 | Anayasa başlıkları | `.../audio-intelligence/anayasa.md` | 27–486 | **45 başlık** (`§1`–`§23`, §22/§23 ek usul) |
| T13 | ai-master eşlemeleri | `.../K006-yapay-zeka/ai-master.md` | 77, 152–153, 183, 198–199, 401–402, 523 | bkz. EK §9 |
| T14 | Bağımlılık envanteri | `.../K006-yapay-zeka/yapay-zeka-bagimliliklar.md` | 7, 137–158, 190, 193, 367–376 | bkz. EK §10 |

## EK §2 — Alıntı blokları (kaynak koddan birebir, 2026-10-15 `read`)

### EK §2.1 `analyzeAudio()` — Dosya: `shared/src/AI/AIEngine.php` [Satır: 82–106]

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

**Gözlem A1-1:** dosya yolu yalnız `file_exists` ile kontrol edilir; okuma/çözümleme
`extractMetadata`/`extractFeatures` içindedir. **A1-2:** `analyzeAudio` 10 anahtar döner;
sözleşme `@return` şablonu (AIEngineInterface L34) yalnız **6** anahtar sayar — danceability ve
acousticness sözleşmede var, **duration/sample_rate/bit_depth/channels ise sözleşmede yok** (EK §3/C4).

### EK §2.2 `extractFeatures()` — Dosya: `shared/src/AI/AIEngine.php` [Satır: 315–327]

```php
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

**Gözlem A2-1:** `$filePath` parametresi gövdede **hiç kullanılmaz** — her çağrı aynı 6 değeri
döner. **A2-2:** yorum satırı hedefi açıkça "C++ DSP engine"der; eski §3/İddia 6 (L60) bu satırı
doğru aktarmıştır. **A2-3:** C++ iddiası repo kanıtına bağlıdır — bu vault'ta `**/*.cpp` = 0,
`**/*.h` = 0 (K006 taraması) → "repo'da C++ var" **denmez**; yalnız yorumun *plan* olduğunu
yazılır.

### EK §2.3 `extractMetadata()` — Dosya: `shared/src/AI/AIEngine.php` [Satır: 288–310]

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

**Gözlem A3-1:** kaçış `escapeshellarg` ile yapılıyor (Satır: 292) — kabuk enjeksiyonu önlemi
mevcut. **A3-2:** `ffprobe` binary'si **sistemde kurulu varsayılır**; kurulu değilse `$output === null`
→ varsayılan 44100/16/2 döner (sessiz degrade). **A3-3:** `json_decode(..., JSON_THROW_ON_ERROR)`
bozuk JSON'ta **fırlatır** — `analyzeAudio`'da bu istisna yakalanmaz (Satır: 82–106'da try/catch yok).

### EK §2.4 `audioAnalysisWorkflow()` — Dosya: `shared/src/AI/AIWorkflow.php` [Satır: 105–139]

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

**Gözlem A4-1:** 4. adım **parameterized** SQL kullanır (`:bpm`, `:key_sig`, `:energy`, `:id`) —
eski §3/İddia 7 (L61) doğrulandı. **A4-2:** `$genre` bilinçli boş; sorgu `"key mood "` biçiminde
döner — knowledge araması **tür filtresi olmadan** çalışır. **A4-3:** `$this->toolCalling` ve
`$this->knowledge` bağımlılıkları K016 profiline ait sınıflardır (bağımlılık envanteri
`yapay-zeka-bagimliliklar.md` Satır: 55, 57–58: KnowledgeBase/PromptEngine/ToolCalling =
"**K016 (K006 DIŞI**)") → bu dalın workflow'u K016'sız çalışmaz.

### EK §2.5 Sözleşme — Dosya: `shared/src/Contracts/AI/AIEngineInterface.php` [Satır: 30–36]

```php
    /**
     * Ses dosyasını analiz eder (BPM, key, energy, mood).
     *
     * @param string $filePath Ses dosyası yolu
     * @return array{bpm: float, key: string, energy: float, mood: string, danceability: float, acousticness: float}
     */
    public function analyzeAudio(string $filePath): array;
```

**Gözlem A5-1:** docblock 4 metrik sayar (BPM, key, energy, mood), `@return` 6 anahtar verir;
gerçek dönüş 10 anahtardır (EK §2.1) → üç sayım arasında **3/6/10** farkı (EK §3/C4).

## EK §3 — Çelişki kaydı (append-only — eski satırlar değiştirilmedi, yenileri buraya eklendi)

| # | Eski iddia (dosya·satır) | 2026-10-15 kanıtı | Karar |
|---|---|---|---|
| C1 | EK §1 satır 18: "`ai-master.md` §1 sorumluluk tablosu (**satır 37–42**)" | `ai-master.md` ilgili tablo **Satır: 176–200** aralığındadır (S2 satırı 183; çocuk eşlemeleri 198–199); L37–47 aralığı başka bir bölümdür | ⚠️ BAYAT SATIR REFERANSI — eski metin düzeltilmedi, buraya kaydedildi |
| C2 | EK §3/`A1` satır 46: "ai-master §3 **satır 263–264**" | `ai-master.md` **Satır: 152–153** = `music-analysis/` → 263 · `audio-intelligence/` → 264 (bu sayılar v2 mini-ağaç satırıdır, ai-master'ın kendi satırı değil) | ⚠️ KARIŞIK REFERANS — 263/264 = kaynak ağacın satırı, ai-master'ın değil |
| C3 | EK §3/`A2` satır 47: "`AIEngine.php` satır **317** yorumu" | Doğru: `shared/src/AI/AIEngine.php` Satır: 317 = `// Gerçek implementasyonda C++ DSP engine ile yapılacak` | ✅ doğrulandı |
| C4 | ESKİ — (yeni bulgu) sözleşme 6 anahtar vs gerçek 10 anahtar | `AIEngineInterface.php` Satır: 34 (`@return` 6) ↔ `AIEngine.php` Satır: 94–105 (10 anahtar) | ⚠️ SÖZLEŞME/UYGULAMA FARKI — yeni kayıt |
| C5 | ESKİ — (yeni bulgu) `genre` anahtarı hiçbir yerde dönmez | `AIWorkflow.php` Satır: 112–113 yorumu bunu zaten kabul eder; `analyzeAudio` dönüşünde `genre` yok (Satır: 94–105) | ⚠️ BİLİNÇLİ BOŞLUK — yeni kayıt |
| C6 | index.md T9: `children:` **6 dosya** | `ai-master.md` §13.2 Satır: 402: `audio-intelligence/ | 6 | 563` → 6 sayısı tutarlı; **563 satır toplamı bayattır** (2026-10-15: `dogrulama` 657 + bu dosya ek sonrası = çok daha fazla) | ⚠️ ÖLÇÜM ÇELİŞKİSİ — eski 563 geçersiz |
| C7 | EK §4/kapanış L74–75: "status: draft kalır" | `index.md` Satır: 4 `status: draft` — hâlâ doğru | ✅ doğrulandı |
| C8 | index.md Satır: 37, 43–45: kapanış `»` (düz tırnak) | Aynı dosyada L37 ve L43–45'te `»` görünür; `[[../index|...]]` biçimi (L81) doğru | ℹ️ BİLGİ — yalnız raporlandı, düzeltilmedi |
| C9 | EK §3/İddia 9 satır 63: "model seçimi `ai-master.md` §4 → UNKNOWN" | `yapay-zeka-bagimliliklar.md` Satır: 144–148: librosa/Essentia/Spotify/Pedalboard/Whisper **"⚠️ aday"** olarak K006.1'e eşlenmiş — yani *aday listesi var, seçim yok* | ℹ️ ŞEKİL DÜZELTMESİ: "yok" değil "aday var, seçim yok" |

## EK §4 — Kapsam sınırı derinlemesine (eski §2 tablosunun kanıt genişletmesi)

### EK §4.1 Komşu K düğümleri — ne taşıdığını kanıt satırıyla

| Komşu | Eski §2'deki rolü | 2026-10-15 kanıt satırı | Durum |
|---|---|---|---|
| K007-data | feature'ların DB'de saklanması hariç (satır 29) | `yapay-zeka-bagimliliklar.md` Satır: 496–579: §21 "K006 → K007 gereksinim taslağı" — **şemayı K007 üretir, K006 üretmez** (Satır: 572, 578–579) | ✅ sınır net |
| K004-dsp | DSP zinciri/sinyal hariç (satır 30) | `yapay-zeka-bagimliliklar.md` Satır: 180: "Yön kuralı: K006 → K004 (iç)" | ✅ yön net |
| K015-AI | ajan çalışma zamanı/prompt hariç (satır 32) | `yapay-zeka-bagimliliklar.md` Satır: 55, 57–58: KnowledgeBase/PromptEngine/ToolCalling = K016 (K006 DIŞI); Satır: 181: "K016 → K006 bağımlılığı **kodda gözlenmedi**" | ✅ tek yön (K006 → K016 çağrısı) |
| K008-domain | "çal/beğen" kuralı hariç (satır 33) | `yapay-zeka-bagimliliklar.md` Satır: 193: "`MemorySystem` sahipliği (K006 personalization mı K016 mı) — sınır açık" | ⚠️ sınır açık |
| K005-firmware | firmware ses işleme hariç (satır 34) | K006 envanterinde firmware'e dair satır **görülmedi** (yapay-zeka-bagimliliklar.md taraması 2026-10-15) | ⚠️ VERIFICATION REQUIRED |
| recommendation | çıktı öneri akışına verilir (satır 31) | `ai-master.md` Satır: 198–199: `music-analysis` ve `audio-intelligence` → sorumlu `S2` (öneri değil) | ✅ ayrım korunuyor |

### EK §4.2 "İçinde / Dışında" sınırının kod tarafı karşılığı

| Sınır | Kod gerçeği (2026-10-15) | Sonuç |
|---|---|---|
| İçinde: "feature çıkımının planı" | Plan var: `AIEngine.php` Satır: 317 yorumu (C++ DSP hedefi) + `ai-master.md` Satır: 523 (librosa/Essentia pipeline şartnamesi) | ✅ plan evi doğru |
| Dışında: "feature'ların DB'de saklanması" | DB yazımı `AIWorkflow.php` Satır: 117–127'de ama **özellik alanlarını** (bpm/key_sig/energy) yazar; sahiplik K006.6/music-analysis'e ait (`yapay-zeka-bagimliliklar.md` Satır: 568) | ✟ gözlem: yazım var, sahiplik K006.6'da |
| İçinde: "K006 ↔ K016 tek ML çağrı noktası" | Çağrı noktası `AIWorkflow` (dispatchTask/search/callTool üçlüsü, Satır: 106–126) | ✅ tek nokta |
| Dışında: "ajan çalışma zamanı" | `dispatchTask` çağrısı K016'ya gider (`AIOrchestrator`, bağımlılık envanteri Satır: 53) | ✅ doğrulandı |

## EK §5 — Alt ayrım noktaları (eski A1–A4 + yeni A5–A7)

| # | Ayrım | 2026-10-15 durumu | Kanıt |
|---|---|---|---|
| A1 | audio-intelligence ↔ music-analysis | **UNKNOWN** (değişmedi) — ayrım hâlâ v2'de yok; ai-master ikisini ayrı satırlarda tanır: music-analysis = "özellik çıkarma", audio-intelligence = "sınıflandırma, etiketleme, anomali" | `ai-master.md` Satır: 152–153 |
| A2 | Feature çıkarma katmanı | ⚠️ taslak — hedef "C++ DSP engine", alternatif aday "librosa/Essentia" — **ikisi de uygulanmamış** | `AIEngine.php` Satır: 317 · `ai-master.md` Satır: 523 |
| A3 | Ses zekâsı ↔ K004-dsp sinyal yolu | ⚠️ üst karar bekliyor — yön kuralı yazılı (K006 → K004 iç), sinyal yolu tanımı yok | `yapay-zeka-bagimliliklar.md` Satır: 180 |
| A4 | Metrik sözleşmesinin sahibi | ⚠️ VERIFY REQUIRED — sözleşmede 6, uygulamada 10 anahtar (C4); sahiplik kararı yok | `AIEngineInterface.php` Satır: 34 ↔ `AIEngine.php` Satır: 94–105 |
| **A5 (yeni)** | `genre` metriğinin sahibi | ⚠️ yeni soru — workflow `genre` istiyor ama motor döndürmez; boş mu bırakılacak, K006.6'dan mı gelecek? | `AIWorkflow.php` Satır: 112–113 |
| **A6 (yeni)** | ffprobe bağımlılığının sahipliği (K006.1 mi, altyapı mı) | ⚠️ yeni soru — sistem binary'si, K006 envanterinde paket olarak geçmiyor | `AIEngine.php` Satır: 290–295 |
| **A7 (yeni)** | Bozuk JSON / fırlatma davranışının sahibi | ⚠️ yeni soru — `JSON_THROW_ON_ERROR` istisnası yakalanmaz; hata sınıfı bu dalın anayasasında (§17) tanımlı mı? | `AIEngine.php` Satır: 300 · anayasa §17 (Satır: 369) |

## EK §6 — Eski §3 "Disk kanıtları" tablosunun yeniden doğrulaması

| İddia (eski satır) | 2026-10-15 kontrol | Sonuç |
|---|---|---|
| 1 · L55 — ad v2 §2.6 satır 264'te geçer | `index.md` evidence Satır: 15 aynı iddiayı taşır | ✅ vault içi tutarlı |
| 2 · L56 — rol tanımı yok | `index.md` open_questions Satır: 19: "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED" | ✅ doğrulandı |
| 3 · L57 — 5 modül, 2'si ses | `AIEngine.php` Satır: 21–27 docblock birebir aynı | ✅ doğrulandı |
| 4 · L58 — analyzeAudio BPM/key/energy/mood | `AIEngineInterface.php` Satır: 31, 36 | ✅ doğrulandı |
| 5 · L59 — placeholder bpm=120.0, key=C | `AIEngine.php` Satır: 320–321 | ✅ doğrulandı (enerji/mood/dance/acoustic da sabit, Satır: 322–325) |
| 6 · L60 — "C++ DSP engine ile yapılacak" | `AIEngine.php` Satır: 317 | ✅ doğrulandı |
| 7 · L61 — prepared statement ile yazım | `AIWorkflow.php` Satır: 122–126 (`:bpm` parametreleri) | ✅ doğrulandı |
| 8 · L62 — metadata ffprobe ile | `AIEngine.php` Satır: 290–291 (`ffprobe ... -show_streams`) | ✅ doğrulandı |
| 9 · L63 — model seçimi UNKNOWN | bkz. C9 — aday var, **seçim yok** | ℹ️ düzeltme kaydı |

**Sayım notu:** eski §3'te 9 iddia → 2026-10-15'te 8 ✅ + 1 ℹ️ (C9) = **yanlış iddia 0**.

## EK §7 — Açık sorular (eski R1–R4 + yeni R5–R8)

| # | Soru | Durum | Yeni kanıt / ilk kayıt |
|---|---|---|---|
| R1 | audio-intelligence ↔ music-analysis ölçütü | ⚠️ VERIFICATION REQUIRED | ai-master satırları (152–153) iki ayrı tanım verir ama **ayrım ölçütünü** yazmaz |
| R2 | Feature çıkarma katmanı (PHP/C++/Python) | ⚠️ UNKNOWN | üç aday yazılı, hiçbiri seçilmemiş (A2) |
| R3 | Kütüphane/model seçimi | ⚠️ UNKNOWN | aday listesi var: `yapay-zeka-bagimliliklar.md` Satır: 144–148 (librosa, Essentia, Spotify, Pedalboard, Whisper) — hepsi "⚠️ aday" |
| R4 | K004-dsp sınırı ne zaman kilitlenir? | ⚠️ ADR-097 K4 | değişmedi |
| **R5 (yeni)** | `genre` metriği hangi katmandan gelecek (A5)? | ⚠️ VERIFICATION REQUIRED | `AIWorkflow.php` Satır: 112–113 |
| **R6 (yeni)** | Sözleşme 6 ↔ uygulama 10 anahtar farkı hangi yöne düzeltilecek? | ⚠️ VERIFICATION REQUIRED | C4 |
| **R7 (yeni)** | ffprobe kurulu değilse davranış (sessiz varsayılan) kabul edilebilir mi? | ⚠️ VERIFICATION REQUIRED | `AIEngine.php` Satır: 296–298 |
| **R8 (yeni)** | `JSON_THROW_ON_ERROR` fırlatması kime yakalanacak? | ⚠️ VERIFICATION REQUIRED | `AIEngine.php` Satır: 300 + anayasa §17 (Satır: 369) |

**Kapanış (yeni):** R1–R8 kapanmadan bu dosyada **teknoloji ya da resmi sınır iddiası yazılmaz** —
eski kapanış cümlesi (L74–75) bu ekle birlikte **genişlemiş** olarak uygulanır; eski metin silinmedi.

## EK §8 — Kardeş dosya ve indeks cross'ları (2026-10-15)

### EK §8.1 `audio-intelligence/index.md` children (Satır: 6–12)

| # | Dosya | Bu ekle ilişkisi |
|---|---|---|
| 1 | audio-intelligence-anayasa.md | 45 başlık (T12) — §1 Kapsam Sınırı (Satır: 35) bu dosyanın §2'siyle çapraz okunur |
| 2 | audio-intelligence-bilesenler.md | bileşen envanteri — bu eke **dokunulmadı** |
| 3 | audio-intelligence-kapsam.md | **bu dosya** (append hedefi) |
| 4 | audio-intelligence-ornekler.md | kod örnekleri — bu eke **dokunulmadı** |
| 5 | audio-intelligence-dogrulama.md | kanıt dosyası — 2026-10-15'te 657 satıra genişletildi (o dosyanın EK §11 ölçümü) |
| 6 | audio-intelligence-web-arastirma.md | §14 URL kaynağı (L41–51, 95) |

### EK §8.2 Üst `K006-yapay-zeka/index.md` cross (Satır: 7–15, 18–29)

| Kontrol | Değer |
|---|---|
| children 9/9 | model-management · inference · recommendation · personalization · music-analysis · **audio-intelligence** · local-inference · evaluation · testing (Satır: 7–15) |
| doğrulama satırı | Satır: 22: "children listesi v2 mini-ağaç ile karşılaştırıldı: 9/9 birebir" |
| kanıt satırı | Satır: 20: `shared/src/AI/ + shared/src/Contracts/AI/ — 13 dosya (disk ölçümü 2026-10-12)` |
| açık sorular | Satır: 26–28: registry adı çakışması · öneri sahipliği 3'lü çakışma · kademe-3 yapısı → hepsi ⚠️ |
| group | Satır: 29: `group: mimari` — bu dosyanın frontmatter'ı (L10) ile aynı |

## EK §9 — `ai-master.md` cross'ları (2026-10-15 satır kanıtı)

| # | ai-master satırı | İçerik | Bu dosyayla ilişki |
|---|---|---|---|
| M1 | 77 | "Dinler — BPM/ton/enerji çıkarır (`music-analysis` · `audio-intelligence`)" | Rol cümlesi (eski §1 L21) ile uyumlu |
| M2 | 152 | `music-analysis/` → v2 satır 263 → "Ses dosyasından özellik çıkarma" | A1 ayrımının tek yazılı tanımı |
| M3 | 153 | `audio-intelligence/` → v2 satır 264 → "Ses zekâsı üst katmanı — sınıflandırma, etiketleme, anomali" | bu dosyanın §1 rolü (L17) = ad aktarımı; **tanım yalnız ai-master'da** |
| M4 | 183 | S2 "Sesi/metriği analiz etmek" → children `music-analysis · audio-intelligence` → kanıt `AIEngine::analyzeAudio L82–106` | eski §1 satır 18'in "sorumluluk tablosu" dediği şey budur (C1'e bkz.: satır referansı bayat) |
| M5 | 198–199 | iki çocuk da sorumlu `S2` | öneri (S?) ile karışmaz |
| M6 | 401–402 | `music-analysis/ | 6 | 569` · `audio-intelligence/ | 6 | 563` (2026-10-13 ölçümü) | 6 = dosya sayısı ✅ · satır toplamları **bayat** (C6) |
| M7 | 523 | Faz-2 şartnamesi: `extractFeatures()` placeholder'ı librosa/Essentia ile değiştirilecek · hedef: 50 şarkılık ground-truth · T4: ±0 BPM · ±0 yarım ton | R2/R3 için **koşul** var, **seçim** yok |

## EK §10 — `yapay-zeka-bagimliliklar.md` cross'ları

| # | Satır | İçerik | K006.1 (bu dal) etkisi |
|---|---|---|---|
| B1 | 7 | `K006.1-audio-intelligence` = ilk çocuk | bu dosyanın node'u |
| B2 | 137–148 | aday ML/analiz araçları tablosu — K006.1'e eşlenenler: Satır 144 librosa · 145 Essentia · 146 Spotify Audio Tools · 147 Pedalboard · 148 Whisper | **hepsi "⚠️ aday"** — R3'ün kanıtı (URL'ler §14'te taşındı) |
| B3 | 149–151 | whisper.cpp / llama.cpp / Ollama → **K006.4-local-inference** | bu dalın dışında |
| B4 | 190 | "K006 eğitim verisi kaynağı (tablo/depo adı) → UNKNOWN" | R3'ü güçlendirir |
| B5 | 193 | MemorySystem sahipliği sınırı açık | EK §4.1 K008 satırı |
| B6 | 367–376 | 6 ölçüm tanımının **tamamı tanımsız** → "K006 üretimde sağlıklı" iddiası kanıtlanamaz | bu dal için L-satırı: "K006.4 yerel çalıştırma" (373) dışarıda; 376 ortak sonuç |
| B7 | 496–579 | §21 K006 → K007 gereksinim taslağı; şemayı **K007 üretir** (572), K006 uymak zorunda (578–579); D5 (568): BPM/key/energy sahibi K006.6/music-analysis | §2 satır 29 sınırının resmi dayanağı |

## EK §11 — Anayasa başlık cross'ı (45 başlık, `audio-intelligence-anayasa.md` 2026-10-15 `grep`)

| Başlık | Satır | Bu dosyayla çapraz |
|---|---|---|
| §1. Kapsam Sınırı | 35 | §2 kapsam tablosu ile **birincil** çapraz |
| §2. İlke ve Değerler | 55 | §1/§7 kapanışlarıyla |
| §2.1 Öncelik sırası | 57 | — |
| §2.2 Zero-hallucination bağlayıcılığı | 67 | EK §0 kural tablosu |
| §3. Mimari Roller ve Kararlar | 76 | — |
| §3.1 Katman ayrımı (RT/Non-RT) | 78 | — |
| §3.2 Sahiplik matrisi | 100 | A4/A5 sahiplik soruları |
| §4. Bileşen Kataloğu | 114 | — |
| §5. RT Bütçesi ve Köprü Sözleşmesi | 128 | — |
| §5.1 RT kuralları | 130 | — |
| §5.2 Köprü veri akışı | 141 | — |
| §5.3 Bütçe (sayısal durum) | 150 | — |
| §6. Hedef Üretim ve Güvenlik Kırpma | 156 | — |
| §6.1 Hedef üretim zinciri | 158 | R2/R3 |
| §6.2 Güvenlik kırpma (clamping) | 168 | — |
| §7. Rehberler (Guidelines) | 181 | — |
| §8. Ölçüm ve Doğrulama (evaluation ile ortak) | 194 | R7/R8 ölçüm boşluğu |
| §8.1 Birincil metrikler | 196 | — |
| §8.2 Kapılar | 206 | — |
| §9. Zorunlu Kurallar (MUST / MUST NOT) | 218 | — |
| §10. Karar Kaydı | 235 | — |
| §11. Bağımlılıklar | 256 | EK §10 |
| §11.1 Bağımlı olduğu alanlar | 258 | EK §4.1 |
| §11.2 Bağımlı olan alanlar | 268 | recommendation (EK §4.1 son satır) |
| §12. İlişkili Dosyalar ve Açık Sorular | 278 | EK §7 |
| §12.1 Kardeş dosyalar | 280 | EK §8.1 |
| §12.2 Vault referansları | 284 | — |
| §12.3 Açık sorular | 295 | R1–R8 |
| §13. İlgili Lint Maddeleri | 305 | — |
| §14. Bağlam ve Versiyon | 317 | — |
| §15. Bileşen Sözlüğü (TR↔EN) | 332 | — |
| §16. Durum Matrisi | 352 | — |
| §17. Hata Sınıfları ve Zincir Davranışı | 369 | **R8** (JSON_THROW_ON_ERROR) |
| §18. Güvenlik ve Dayanıklılık | 385 | A3-1 (`escapeshellarg`) |
| §19. Anti-Patternler | 395 | — |
| §20. Uç Nokta Sözleşmesi (giriş/çıkış) | 410 | A4 (sözleşme 6 ↔ uygulama 10) |
| §20.1 Giriş | 412 | — |
| §20.2 Çıkış | 421 | — |
| §21. Kaynaklar, Red-Team ve Revizyon | 432 | §14 |
| §21.1 Kaynaklar | 434 | §14 |
| §21.2 Red-Team soruları | 446 | R1–R8 |
| §21.3 Revizyon Kaydı | 459 | bu ek |
| §22. Ek Usul — Değişiklik Eşiği Tablosu | 470 | — |
| §23. Ek Usul — SSS | 486 | — |

## EK §12 — Korunan maddeler (kapı listesi)

| # | Madde | Durum |
|---|---|---|
| P-1 | Eski içerik L1–85 **tam önek** olarak korundu (append-only) | ✅ |
| P-2 | Frontmatter (`group: mimari` dahil, L1–11) değişmedi | ✅ |
| P-3 | `index.md` yazılmadı | ✅ |
| P-4 | `-anayasa.md` · `-ornekler.md` · `-bilesenler.md` · `-web-arastirma.md` · `-dogrulama.md` yazılmadı | ✅ |
| P-5 | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `.ai/architecture/index.md` yazılmadı | ✅ |
| P-6 | `*.php/js/css/sql` yazılmadı (yalnız okundu) | ✅ |
| P-7 | `git commit` atılmadı | ✅ |
| P-8 | Yeni URL yok — §14 yalnız 2026-10-12 kayıtlarını taşır | ✅ |
| P-9 | `// ...` · `TBD` · doldurma satırı yok; alıntılardaki `//` kaynak kodun kendi satırıdır | ✅ |
| P-10 | Mojibake yok; her iddia `Dosya: ... [Satır: n]` ya da taşınan URL ile kanıtlandı, kanıtsız `⚠️ VERIFICATION REQUIRED` | ✅ |

## EK §13 — Ölçüm, sayım ve sonuç

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi (2026-10-15) | `read` tam okuma (L1–85) | **85 satır** |
| Ek sonrası (2026-10-15) | `read` offset 9999 → "Offset 9999 is out of range for this file (**512** lines)" | **512 satır** |
| Eşik | ≥500 | **✅ GEÇTİ** (512 ≥ 500) · ek satırı = 512 − 85 = **427** |

### EK §13.1 Bu ek sayımı

| Metrik | Değer |
|---|---|
| EK bölüm sayısı | 14 (EK §0–§14) |
| Alıntı bloğu | 5 (tamamı PHP kaynak kodu) |
| Yeni görgü/gözlem satırı | A1 · A2 · A3 · A4 · A5 serileri + T1–T14 + C1–C9 + B1–B7 + M1–M7 + P1–P10 |
| Açık soru | 4 eski + 4 yeni = 8 (R1–R8) |
| Çelişki kaydı | 9 (C1–C9) |
| Taşınan URL | 12 (0 yeni) |
| Kapı ihlali | 0 |
| Beyazliste dışı yazım | 0 |
| `git commit` | yok |

## EK §14 — Taşınan URL'ler (2026-10-12 havuzu — 0 yeni URL)

Kaynak: `audio-intelligence-web-arastirma.md` [Satır: 41–51, 95] · aday eşlemeleri:
`yapay-zeka-bagimliliklar.md` [Satır: 137–158].

| # | URL (taşındı) | Satır kaynağı | K006.1 rolü |
|---|---|---|---|
| 1 | https://librosa.org/ | web-arastirma 41 | öznitelik çıkarım adayı (R3) |
| 2 | https://librosa.org/doc/latest/ | web-arastirma 42 · bagimliliklar 144 | sürüm kanıtı (1.0.0) |
| 3 | https://github.com/librosa/librosa | web-arastirma 43 | lisans/yıldız (ISC) |
| 4 | https://essentia.upf.edu/ | web-arastirma 44 | öznitelik + fingerprint adayı |
| 5 | https://essentia.upf.edu/models.html | web-arastirma 45 · bagimliliklar 145 | hazır model adayı |
| 6 | https://spotify.github.io/ | web-arastirma 46 · bagimliliklar 146 | ekosistem |
| 7 | https://github.com/spotify/pedalboard | web-arastirma 47 · bagimliliklar 147 | ses efekt/zincir adayı |
| 8 | https://github.com/openai/whisper | web-arastirma 48 · bagimliliklar 148 | konuşma tanıma (opsiyonel) |
| 9 | https://onnxruntime.ai/docs/ | web-arastirma 49 · bagimliliklar 137 | taşınabilir çalıştırma (K006.3/K006.4) |
| 10 | https://scikit-learn.org/stable/ | web-arastirma 50 · bagimliliklar 152 | sınıf etiket eşikleri |
| 11 | https://docs.pytest.org/en/stable/ | web-arastirma 51 · bagimliliklar 158 | aday test altyapısı (yalnız Python tarafı) |
| 12 | `https://mtg.github.io/essentia` | web-arastirma 95 | **CRAWL_NOT_FOUND düzeltme kaydı** — eski adres, `essentia.upf.edu` ile değiştirildi; yeni adresteki içerik bu eke kopyalanmadı |

**Not:** bagimliliklar tablosundaki diğer adayların (kütüphane adı geçen ama bu listede olmayanlar)
URL'leri bu eke **kopyalanmadı** — 2026-10-12 havuzu dışında kalmaları riskine karşı yalnızca
dosya·satır referansı verildi (EK §10/B2, EK §4.1).

---

**Ek Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-15 ·
**Eklenti:** EK §0–§14 (append-only · 2026-10-12 tabanı korunmuştur · 0 yeni URL)
