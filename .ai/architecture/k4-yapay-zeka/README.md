---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K4 Yapay Zeka Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K4: Yapay Zeka Layer

**Katman:** K4 (Yapay Zeka)
**Kapsam:** Music Analysis, Recommendation, Auto EQ, Voice, AI Gen, Edge AI
**Sorumlu Agent:** AI Engineer
**Bileşen Sayısı:** 50

---

## 1. Genel Bakış

K4 katmanı, CoreMusic'in akıllı özelliklerini sağlayan yapay zeka ve makine öğrenmesi bileşenlerini içerir. Bu katman, K5 veri katmanından veri okur ve K8 servis katmanına öneriler sunar.

### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Offline-First** | Çevrimdışı çalışabilme |
| **Edge AI** | Yerel ML inference |
| **Privacy** | Kullanıcı verisi yerel işlenir |
| **Real-time** | Gerçek zamanlı öneri |
| **Explainable** | Açıklayıcı öneriler |

---

## 2. Bileşen Haritası

| # | Bileşen | Amaç | Teknoloji |
|---|---------|------|-----------|
| K4-01 | Music Analysis | Şarkı analizi (BPM, key, energy) | Librosa, Essentia |
| K4-02 | Recommendation | Kişisel öneriler | Collaborative + Content-based |
| K4-03 | Auto EQ | Otomatik EQ ayarı | DSP + ML |
| K4-04 | Voice Control | Sesli komut | Whisper, Vosk |
| K4-05 | AI Theme | AI ile tema üretimi | LLM |
| K4-06 | Edge AI | Yerel inference | ONNX Runtime |
| K4-07 | Feature Extract | Ses özellikleri | FFT, MFCC |
| K4-08 | Similarity | Şarkı benzerliği | Cosine similarity |
| K4-09 | Mood Detection | Ruh hali tespiti | Audio features |
| K4-10 | Auto Mastering | Otomatik mastering | ML pipeline |

---

## 3. Music Analysis (K4-01)

### 3.1 Çıkarılan Özellikler

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| BPM | 60-200 | Tempo |
| Key | C-C# | Müzikal anahtar |
| Mode | Major/Minor | Major/Minor |
| Energy | 0-1 | Enerji seviyesi |
| Danceability | 0-1 | Dans edilebilirlik |
| Valence | 0-1 | Pozitiflik |
| Acousticness | 0-1 | Akustiklik |
| Instrumentalness | 0-1 | Enstrüman ağırlığı |
| Speechness | 0-1 | Konuşma oranı |
| Loudness | -60-0 LUFS | Ses seviyesi |

### 3.2 Feature Extraction Pipeline

```python
# Feature extraction pipeline
def extract_features(audio_file):
    # Load audio
    y, sr = librosa.load(audio_file, sr=48000)

    # Tempo and beat
    tempo, beats = librosa.beat.beat_track(y=y, sr=sr)

    # Key and mode
    chroma = librosa.feature.chroma_cqt(y=y, sr=sr)
    key, mode = estimate_key(chroma)

    # Energy features
    rms = librosa.feature.rms(y=y)
    energy = np.mean(rms)

    # Spectral features
    spectral_centroid = librosa.feature.spectral_centroid(y=y, sr=sr)
    spectral_rolloff = librosa.feature.spectral_rolloff(y=y, sr=sr)

    # MFCCs (for similarity)
    mfccs = librosa.feature.mfcc(y=y, sr=sr, n_mfcc=20)

    return {
        'tempo': tempo,
        'key': key,
        'mode': mode,
        'energy': energy,
        'mfccs': mfccs,
        'spectral_centroid': spectral_centroid,
        'spectral_rolloff': spectral_rolloff,
    }
```

---

## 4. Recommendation Engine (K4-02)

### 4.1 Hibrit Öneri Sistemi

```
User History → Collaborative Filtering → Candidate Set
                                              ↓
Audio Features → Content-based → Candidate Set → Merge & Rank → Final Recommendations
                                              ↑
User Context → Context-aware → Candidate Set
```

### 4.2 Collaborative Filtering

```python
# User-based collaborative filtering
def collaborative_filtering(user_id, user_item_matrix, k=10):
    # Find similar users
    user_vector = user_item_matrix[user_id]
    similarities = cosine_similarity(user_vector, user_item_matrix)

    # Get top-k similar users
    similar_users = np.argsort(similarities)[::-1][1:k+1]

    # Aggregate recommendations
    recommendations = np.zeros(user_item_matrix.shape[1])
    for sim_user in similar_users:
        weight = similarities[sim_user]
        recommendations += weight * user_item_matrix[sim_user]

    return recommendations
```

### 4.3 Content-Based Filtering

```python
# Content-based using audio features
def content_based(track_id, track_features, k=10):
    target_features = track_features[track_id]

    # Calculate similarity
    similarities = cosine_similarity(
        target_features.reshape(1, -1),
        track_features
    )[0]

    # Get top-k similar tracks
    similar_tracks = np.argsort(similarities)[::-1][1:k+1]

    return similar_tracks
```

---

## 5. Auto EQ (K4-03)

### 5.1 Otomatik EQ Algoritması

```
Room Analysis → Transfer Function → Target Curve → EQ Coefficients
                    ↓
            RT60 Measurement
                    ↓
            Frequency Response
                    ↓
            Difference Curve
                    ↓
            EQ Filters (parametric)
```

### 5.2 EQ Hedefleri

| Hedef | Eğri | Kullanım |
|-------|------|----------|
| Harman Target | Harman research | Genel dinleme |
| Diffuse Field | ISO 11904 | Stüdyo |
| Custom | Kullanıcı tanımı | Kişisel |
| Flat | ±0 dB | Referans |

---

## 6. Voice Control (K4-04)

### 6.1 Sesli Komutlar

| Komut | Eylem |
|-------|-------|
| "Play [song/artist]" | Müzik çal |
| "Next track" | Sonraki şarkı |
| "Previous track" | Önceki şarkı |
| "Volume up/down" | Ses ayarı |
| "Set EQ to [preset]" | EQ preset |
| "What's playing?" | Şarkı bilgisi |
| "Add to playlist" | Çalma listesine ekle |

### 6.2 Whisper Entegrasyonu

```python
# Whisper speech-to-text
import whisper

model = whisper.load_model("base")

def transcribe_command(audio_path):
    result = model.transcribe(audio_path)
    return result["text"]
```

---

## 7. Edge AI (K4-06)

### 7.1 ONNX Runtime

```cpp
// ONNX inference
#include <onnxruntime_cxx_api.h>

Ort::Env env(ORT_LOGGING_LEVEL_WARNING, "coremusic");
Ort::SessionOptions session_options;
session_options.SetIntraOpNumThreads(4);

Ort::Session session(env, "model.onnx", session_options);

// Run inference
Ort::RunOptions run_options;
session.Run(run_options, input_names, input_tensors,
            num_inputs, output_names, output_tensors, num_outputs);
```

### 7.2 Model Boyutu Kısıtlamaları

| Model | Boyut | Inference Time |
|-------|-------|----------------|
| Music Analysis | <50MB | <100ms |
| Recommendation | <100MB | <50ms |
| Voice Command | <200MB | <200ms |
| Auto EQ | <20MB | <10ms |

---

## 8. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-030 | AI öneri motoru |

---

*K4 Yapay Zeka Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
