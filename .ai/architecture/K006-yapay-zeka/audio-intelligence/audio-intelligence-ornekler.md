---
title: "audio-intelligence — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/audio-intelligence
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# audio-intelligence — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/Contracts/AI/AIEngineInterface.php` satır 30–36 — ses analizi sözleşmesi:

```php
/**
 * Ses dosyasını analiz eder (BPM, key, energy, mood).
 *
 * @param string $filePath Ses dosyası yolu
 * @return array{bpm: float, key: string, energy: float, mood: string, danceability: float, acousticness: float}
 */
public function analyzeAudio(string $filePath): array;
```

`shared/src/AI/AIEngine.php` satır 315–326 — özellik çıkarımı **henüz placeholder**:

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

`shared/src/AI/AIEngine.php` satır 291 — metadata çıkarma ffprobe ile:

```php
'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
```

> Örnek 2 ve 3 gerçek koddur; sabit değerli `extractFeatures` bir **eksiklik kanıtıdır**, çalışan özellik
> çıkarımı **yoktur**.

## §2 Resmi doküman örneği (URL + snippet)

**librosa — feature extraction** · https://librosa.org/doc/latest/api/feature.html (doğrulandı 2026-10-12):

```python
# 3. Run the default beat tracker
tempo, beat_frames = librosa.beat.beat_track(y=y, sr=sr)
print('Estimated tempo: {:.2f} beats per minute'.format(tempo))
```

**Essentia (MTG/UPF)** · https://essentia.upf.edu/documentation.html (doğrulandı 2026-10-12):

> "Essentia is an open-source C++ library with Python and JavaScript bindings for audio analysis and
> audio-based music information retrieval."

## §3 Uygulama notu

- Her iki kütüphane de **adaydır**; CoreMusic'in hangisini kullanacağı `UNKNOWN` (kapsam §5/R3).
- Repo'da çalışan özellik çıkarımı yok → kademe-3'te önce `extractFeatures` sahiplenilmelidir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `audio-intelligence-kapsam.md` | bu dalın rolü, içinde/dışında, açık sorular |
| `audio-intelligence-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 264) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
