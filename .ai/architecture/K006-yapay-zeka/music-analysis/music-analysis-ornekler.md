---
title: "music-analysis — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/music-analysis
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# music-analysis — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIEngine.php` satır 82–106 — analiz çıktısı:

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
        ...
    ];
}
```

`shared/src/AI/AIWorkflow.php` satır 119–125 — analiz sonucunun persist edilmesi:

```php
$bpm    = (float)$analysis['bpm'];
$keySig = (string)$analysis['key'];
$energy = (float)$analysis['energy'];
$this->toolCalling->callTool('db-query', [
    'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
    ...
```

## §2 Resmi doküman örneği (URL + snippet)

**librosa — feature extraction (rhythm)** · https://librosa.org/doc/latest/api/feature.html
(doğrulandı 2026-10-12):

```python
tempo = librosa.feature.tempo(onset_envelope=onset_env, sr=sr)
print(f"Estimated tempo: {tempo[0]:.2f} BPM")
```

**ffprobe** · https://www.ffmpeg.org/ffprobe.html (doğrulandı 2026-10-12):

```bash
ffprobe -show_streams -select_streams a INPUT
```

## §3 Uygulama notu

- Repo'daki `extractMetadata` bu komutu birebir kullanır; `extractFeatures` ise **placeholder**dır —
  BPM/key değeri gerçeği yansıtmaz.
- Kademe-3'te metrik çıkarımı için araç seçimi ai-master §4 kararı gerektirir (kapsam §5/R2).

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `music-analysis-kapsam.md` | bu dalın rolü, içinde/dışında, açık sorular |
| `music-analysis-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 263) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
