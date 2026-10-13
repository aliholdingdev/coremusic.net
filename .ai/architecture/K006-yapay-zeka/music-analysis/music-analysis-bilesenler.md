---
node_id: K006.5-music-analysis-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L82–106 · L288–330 (2026-10-12 tam okuma)"
  - "shared/src/AI/AIWorkflow.php L99–139 (katalog kaydı L118–127, 2026-10-12)"
  - "shared/src/Contracts/AI/AIEngineInterface.php L30–36 (analyzeAudio sözleşmesi)"
validation: "2026-10-12 · tüm metot/satır grep ile doğrulandı; öznitelik çıkarımı = placeholder · status=verified"
open_questions:
  - "Katalog analizi batch (toplu) akışla mı yapılacak → UNKNOWN"
  - "DB yazımı başarısızsa analiz sonucu korunuyor mu (transaction) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.5-music-analysis — Bileşenler

> K006.5'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. Çekirdek, K006.6 (audio-intelligence) ile **paylaşılan** 3 metottur; buradaki
> ayırt edici unsur **katalog kaydı**dır (`db-query` → `tracks`).

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.5'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "music-analysis" adında dosya yok |
| Paylaşılan çekirdek metotlar | 3 (`analyzeAudio` L82–106 · `extractMetadata` L288–313 · `extractFeatures` L315–330) | §2 |
| Katalog kayıt adımı | 1 (`audioAnalysisWorkflow` L118–127 → `callTool('db-query')` + `UPDATE tracks`) | §3 |
| Kalıcı öznitelik algoritması | **0 satır** (`extractFeatures` placeholder) | §2.3 |
| Fingerprint / tür etiketleme | **0 satır** | grep → 0 (2026-10-12) |
| Söz içerik analizi | **0 satır** | grep `whisper|transcri` → 0 |
| Sözleşme karşılığı | 1 imza (`AIEngineInterface::analyzeAudio` L36) | §4 |

---

## §2 Gerçek Kod Bileşenleri (paylaşılan çekirdek)

### §2.1 `AIEngine::analyzeAudio(string $filePath): array` — L82–106 (public)

| Satır | Davranış |
|---|---|
| 84–86 | dosya yoksa `throw new \RuntimeException(...)` |
| 89 | `$this->extractMetadata($filePath)` |
| 92 | `$this->extractFeatures($filePath)` |
| 94–105 | 10 alanlı dönüş: `bpm, key, energy, mood, danceability, acousticness, duration, sample_rate, bit_depth, channels` |

### §2.2 `AIEngine::extractMetadata(string $filePath): array` — L288–313 (private)

| Satır | Davranış |
|---|---|
| 290–293 | `ffprobe … -print_format json -show_format -show_streams` (`escapeshellarg` L292) |
| 295 | `shell_exec($cmd)` |
| 296–298 | null → varsayılan `duration=0, sample_rate=44100, bit_depth=16, channels=2` |
| 300 | `json_decode(..., JSON_THROW_ON_ERROR)` — bozuk JSON fırlatır |

**Gerçek değer:** katalogdaki **tek gerçek** ses verisi (süre/format) buradan gelir.

### §2.3 `AIEngine::extractFeatures(string $filePath): array` — L315–330 (private) — **PLACEHOLDER**

| Satır | Davranış |
|---|---|
| 317–318 | yorum: "Gerçek implementasyonda C++ DSP engine ile yapılacak / Şimdilik placeholder" |
| 319–326 | sabit: `bpm=120.0, key='C', energy=0.7, mood='happy', danceability=0.6, acousticness=0.3` |
| — | `$filePath` gövdede **kullanılmaz** |

**Bulgu:** katalog tablosuna yazılan BPM/key/mood **her parça için aynıdır** — veri bütünlüğü
riski (R-02/R-03).

---

## §3 Katalog Kaydı — `AIWorkflow::audioAnalysisWorkflow(array $request)` L99–139

| Adım | Satır | Çağrı | K006.5 ilgisi |
|---|---|---|---|
| 1 · Task dispatch | 106 | `orchestrator->dispatchTask('analysis', $request, 'high')` | görev kaydı (K016) |
| 2 · Analiz | 109 | `engine->analyzeAudio($filePath)` | §2.1 |
| 3 · Bilgi zenginleştirme | 113–115 | `knowledge->search($query, 3)` · yorum L112: **`genre` daima `''`** | tür boşluğu (C2) |
| 4 · **Kataloga kaydet** | 118–127 | `toolCalling->callTool('db-query', …)` · `UPDATE tracks SET bpm=…` · `:bpm` placeholder + `params` · `database: coremusic_musics` | **ayırt edici adım** |
| çıkış | 131–138 | `analysis` + `metadata.task_id`/`knowledge_hits` + `execution_time` | — |

**İzlek:** SQL parametreli (`:bpm` + `params`) ✅ · `callTool` dönüşü **kontrol edilmez** (sessiz
başarısızlık) ⚠️ · transaction yok ⚠️.

---

## §4 Sözleşme Eşlemesi

| Sözleşme | İmza | Konum | Uygulayan |
|---|---|---|---|
| `AIEngineInterface::analyzeAudio(...)` | 2/5 | Contracts L36 · docblock L30–35 | `AIEngine` L82 |
| (katalog analizi için ayrı interface) | — | **YOK** | — |

`@return` docblock (Contracts L34) 6 alan ister; implementasyon 10 alan döner — genişlik kaydı
(R-05).

---

## §5 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.5 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| BPM/key çıkarım algoritması | **0 satır** (placeholder) | L315–330 |
| Tür/ruh hali etiketleme | **0 satır** (`genre` daima `''`, L112–113) | workflow yorumu |
| Ses parmak izi (fingerprint) | **0 satır** | grep → 0 |
| Söz içerik analizi | **0 satır** | grep → 0 |
| Toplu (batch) katalog taraması | **0 satır** | grep `batch|queue` src AI → 0 |
| Kalanma/transaction güvenliği | **0 satır** | L118–127 transaction yok |
| Metadata çıkarma (ffprobe) | ✅ **var** | L288–313 |
| Katalog kayıt deseni | ✅ **var** | L118–127 |

---

## §6 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.6-audio-intelligence | **aynı 3 metodu paylaşır**; ayrım (tasarım iddiası, v2'de tanımsız → ⚠️): burası katalog/kalıcı kayıt, .6 ses-içerik zekâsı |
| K006.3-recommendation | katalog öznitelikleri öneri girdisidir — bugün placeholder olduğu için öneri kalitesi düşer |
| K006.9-testing | placeholder (L319–326) + JSON_THROW_ON_ERROR (L300) + kontrolsüz callTool (L122–126) → karakteristik test önceliği |
| K003-audio (dış) | L317 yorumunun hedefi C++ DSP engine — K006 dışı sahiplik |

---

## §7 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L82–106 · L288–330 tam okuma, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L99–139 tam okuma, 2026-10-12.
3. `shared/src/Contracts/AI/AIEngineInterface.php` — L30–36, 2026-10-12.
4. `shared/src/**` grep (`librosa|essentia|pedalboard|fingerprint|whisper|transcri`) → 0 eşleşme, 2026-10-12.
5. `music-analysis-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 3 gerçek metot + 1 katalog kaydı satır aralıklarıyla doğrulandı, 8 beklenen bileşenden 6'sı 0 satır`
