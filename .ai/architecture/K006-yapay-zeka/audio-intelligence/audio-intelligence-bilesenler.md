---
node_id: K006.6-audio-intelligence-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L82–106 · L288–330 (2026-10-12 tam okuma)"
  - "shared/src/AI/AIWorkflow.php L99–139 (2026-10-12 tam okuma)"
  - "shared/src/Contracts/AI/AIEngineInterface.php L30–36 (analyzeAudio sözleşmesi)"
validation: "2026-10-12 · tüm metot/satır grep ile doğrulandı; ayrı ses servisi = 0 satır · status=verified"
open_questions:
  - "C++ DSP engine (Neva Engine, K003-audio) bağlantısı ne zaman yapılacak → UNKNOWN"
  - "ffprobe sunucuda kurulu mu (shell_exec bağımlılığı) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.6-audio-intelligence — Bileşenler

> K006.6'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. `analyzeAudio` dışındaki hiçbir ses-zekâsı servisi repoda **yoktur**.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.6'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, hiçbiri "audio-intelligence" adında değil |
| Doğrudan ait metotlar | 2 (`analyzeAudio`, `extractMetadata`) + 1 private (`extractFeatures`) | §2 |
| Workflow karşılığı | 1 (`audioAnalysisWorkflow`) | §3 |
| Sözleşme karşılığı | 1 imza (`AIEngineInterface::analyzeAudio` L36) | §4 |
| Ses aracı bağımlılığı | `ffprobe` (shell_exec) — tek dış ses aracı | §2.2 |
| Python/Kütüphane bağımlılığı | **0 kurulu** | `composer.json` + src grep (librosa/essentia/whisper/onnx/pedalboard → 0 eşleşme) |

---

## §2 Gerçek Kod Bileşenleri

### §2.1 `AIEngine::analyzeAudio(string $filePath): array` — L82–106 (public)

| Satır | Davranış |
|---|---|
| 84–86 | dosya yoksa `throw new \RuntimeException(...)` |
| 89 | `$metadata = $this->extractMetadata($filePath)` |
| 92 | `$features = $this->extractFeatures($filePath)` |
| 94–105 | 10 alanlı dönüş: `bpm, key, energy, mood, danceability, acousticness, duration, sample_rate, bit_depth, channels` |

**Sorumluluk:** K006.6'in tek dışa açılan analiz kapısı. Sözleşme 6 alan ister (Contracts L34);
10 alan döner — genişlik kaydı (riskler R-05).

### §2.2 `AIEngine::extractMetadata(string $filePath): array` — L288–313 (private)

| Satır | Davranış |
|---|---|
| 290–293 | `ffprobe -v quiet -print_format json -show_format -show_streams "%s"` komutu (`escapeshellarg` L292 — kaçış doğru) |
| 295 | `shell_exec($cmd)` |
| 296–298 | `$output === null` → varsayılan `duration=0, sample_rate=44100, bit_depth=16, channels=2` |
| 300 | `json_decode(..., JSON_THROW_ON_ERROR)` — bozuk JSON **fırlatır** |
| 304–309 | `duration` (float) · `sample_rate` · `bit_depth` · `channels` |

**Gerçek değer:** K006.6'in **tek gerçek** ses çıkışı buradan gelir (süre/format). Sessiz başarısızlık
durumu da vardır (L296 varsayılanlara düşer) — ayrıca L300 istisna riski (R-12 ile aile).

### §2.3 `AIEngine::extractFeatures(string $filePath): array` — L315–330 (private) — **PLACEHOLDER**

| Satır | Davranış |
|---|---|
| 317–318 | yorum: "Gerçek implementasyonda C++ DSP engine ile yapılacak / Şimdilik placeholder değerler" |
| 319–326 | sabit dönüş: `bpm=120.0, key='C', energy=0.7, mood='happy', danceability=0.6, acousticness=0.3` |
| — | `$filePath` **gövdede kullanılmaz** |

**Bulgu:** K006.6'in "zekâ" katmanı gerçek değil; tüm ses analizi çıktıları (BPM/key/energy/mood/
danceability/acousticness) her dosya için aynıdır. Bu, K006.6'in **birincil boşluğu** (R-02).

---

## §3 Workflow Bileşeni — `AIWorkflow::audioAnalysisWorkflow(array $request)` L99–139

| Adım | Satır | Çağrı | Not |
|---|---|---|---|
| giriş | 101–103 | `$request['file_path']` · `['track_id']` | — |
| 1 · Task dispatch | 106 | `orchestrator->dispatchTask('analysis', $request, 'high')` | K016 yüzeyi |
| 2 · Ses analizi | 109 | `engine->analyzeAudio($filePath)` | §2.1 |
| 3 · Bilgi zenginleştirme | 113–115 | `knowledge->search($query, 3)` | yorum L112: **`genre` anahtarı döndürülmez → tür sorgusu daima boş** |
| 4 · DB'ye kaydet | 118–127 | `toolCalling->callTool('db-query', …)` · `UPDATE tracks SET bpm=…` · `params` prepared-style · `database: coremusic_musics` | `track_id` varsa |
| çıkış | 131–138 | `analysis` + `metadata.task_id`/`knowledge_hits` + `execution_time` | — |

**İzlekler:**
1. `$genre` L113 sabit `''` — bilinçli kaydedilmiş boşluk (yorum L112), uydurma değil **dokümante eksik**.
2. SQL string'i L123 `:bpm` placeholder + `params` L124 → parametreli; `escapeshellarg` L292 → kaçış doğru.
3. `callTool('db-query', …)` sonucu **kontrol edilmez** (L122–126 dönüş yok/okunmaz) — sessiz başarısızlık riski.

---

## §4 Sözleşme Eşlemesi

| Sözleşme | İmza | Konum | Uygulayan |
|---|---|---|---|
| `AIEngineInterface::analyzeAudio(string $filePath): array` | 1/5 | Contracts L36 · docblock L30–35 | `AIEngine` L82 |
| (ses-zekâsı için ayrı interface) | — | **YOK** | — |

`@return` docblock (Contracts L34): `array{bpm: float, key: string, energy: float, mood: string,
danceability: float, acousticness: float}` — implementasyon bunu **aşar** (10 alan, §2.1).

---

## §5 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.6 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Bağımsız `AudioIntelligence` servisi | **0 satır** | glob `shared/src/AI/*` — adında ses servisi yok |
| BPM/key çıkarım algoritması | **0 satır** (placeholder) | L315–330 |
| Ses parmak izi (fingerprint) | **0 satır** | grep `fingerprint|audiohash` → 0 (2026-10-12) |
| Transkripsiyon / konuşma tanıma | **0 satır** | grep `whisper|transcri` → 0 |
| Segmentasyon / etiketleme | **0 satır** | grep → 0 |
| Öznitelik kütüphanesi entegrasyonu | **0 satır** | grep `librosa|essentia|pedalboard|onnx` → 0 (2026-10-12) |
| ffprobe metadata | ✅ **var** | L288–313 |
| Analiz workflow | ✅ **var** | L99–139 |

---

## §6 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.5-music-analysis | `extractFeatures`/`extractMetadata` **paylaşılan** tek çekirdek — iki çocuk aynı 3 metoda dayanır; ayrım (tasarım iddiası, v2'de tanımlı değil → ⚠️): music-analysis = müzik katalog analizi, audio-intelligence = ses-içerik zekâsı |
| K006.2-inference | `analyzeAudio` tahmin boru hattının girdisi olabilir — **bağlantı kodda yok** (⚠️ tasarımda) |
| K006.9-testing | §2.2 `JSON_THROW_ON_ERROR` + §2.3 placeholder için karakteristik test önceliği |
| K003-audio (dış) | §2.3'ün hedefi C++ DSP engine (yorum L317) — K006 dışındaki sahiplik |

---

## §7 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L82–106 · L288–330 tam okuma, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L99–139 tam okuma, 2026-10-12.
3. `shared/src/Contracts/AI/AIEngineInterface.php` — L30–36, 2026-10-12.
4. `shared/src/**` grep (`fingerprint|librosa|essentia|whisper|onnx|pedalboard`) → 0 eşleşme, 2026-10-12.
5. `audio-intelligence-web-arastirma.md` §4 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 3 gerçek metot + 1 workflow satır aralıklarıyla doğrulandı, 7 beklenen bileşenden 6'sı 0 satır`
