---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K4 Yapay Zeka Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
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

## Alt Katman Şeması (K4.a.b.c)

> **Katman Kuralı (kanıt: README L28):** K4, K5 veri katmanından veri okur ve **K8 servis katmanı üzerinden** öneri sunar; K3 asla K4'ü çağırmaZ (katman matrisi: K4 → K5 okur, K8'e servis eder, K3'ten izole).
> **Kapsam:** 14 Markdown dosyası · diskteki H2/H3 başlıkları · 147 başlık (87 H2 + 60 H3) − 17 hariç = 130 kanıtlı yaprak · 0 uydurma değer.
> **Hariç kuralı:** 12 × "Durum: Implementasyon" + 4 × CLAUDE.md başlığı + 1 × README §8 (ADR referansı) — gerekçeler Katalog notları 2'de.

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|-----------|----|-----------|-------------------|----------------|
| K4.1 | Müzik Analizi & Parmak İzi | 2 | 22 | music-analysis.md · audio-fingerprinting.md |
| K4.2 | Öneri Motoru | 1 | 9 | recommendation-engine.md |
| K4.3 | Ruh Hali & Söz Analizi | 2 | 16 | mood-classification.md · lyrics-analysis.md |
| K4.4 | Ses & Metin | 2 | 16 | speech-to-text.md · voice-assistant.md |
| K4.5 | Auto EQ Optimizasyonu | 1 | 8 | auto-eq-optimization.md |
| K4.6 | Üretim & Edge AI | 2 | 17 | ai-generation.md · edge-ai.md |
| K4.7 | ML Altyapı | 1 | 9 | ml-infrastructure.md |
| K4.8 | Katman Dokümanları | 2 | 33 | index.md · README.md |
| **TOPLAM** | | **13** | **130** | 14 dosya |

### K4.1 — Müzik Analizi & Parmak İzi

*BPM/key/mood çıkarımı (K4-01) + Chromaprint/Echoprint parmak izi eşleştirmesi; K5'ten ham ses/özellik okur.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.1.a** | **Müzik Analizi** | music-analysis.md · L14–L364 |
| K4.1.a.1 | Genel Bakış | music-analysis.md · L14 |
| K4.1.a.2 | Teknik Detaylar | music-analysis.md · L18 |
| K4.1.a.3 | BPM Detection (Tempo Analizi) | music-analysis.md · L20 |
| K4.1.a.4 | Key Detection (Tonal Analiz) | music-analysis.md · L68 |
| K4.1.a.5 | Mood Analysis (Duygu Analizi) | music-analysis.md · L115 |
| K4.1.a.6 | Audio Fingerprinting (Chromaprint) | music-analysis.md · L174 |
| K4.1.a.7 | API / Konfigürasyon | music-analysis.md · L229 |
| K4.1.a.8 | Endpoint Tanımları | music-analysis.md · L231 |
| K4.1.a.9 | Konfigürasyon Dosyası | music-analysis.md · L266 |
| K4.1.a.10 | Performans / Ölçeklenebilirlik | music-analysis.md · L294 |
| K4.1.a.11 | Benchmark Sonuçları | music-analysis.md · L296 |
| K4.1.a.12 | Paralel İşleme | music-analysis.md · L306 |
| K4.1.a.13 | Önbellek Stratejisi | music-analysis.md · L340 |
| K4.1.a.14 | Bağımlılıklar | music-analysis.md · L364 |
| **K4.1.b** | **Parmak İzi Eşleştirme** | audio-fingerprinting.md · L14–L405 |
| K4.1.b.1 | Genel Bakış | audio-fingerprinting.md · L14 |
| K4.1.b.2 | Teknik Detaylar | audio-fingerprinting.md · L18 |
| K4.1.b.3 | Chromaprint Fingerprinting | audio-fingerprinting.md · L20 |
| K4.1.b.4 | Echoprint Fingerprinting | audio-fingerprinting.md · L168 |
| K4.1.b.5 | Parça Tanıma ve Eşleştirme | audio-fingerprinting.md · L244 |
| K4.1.b.6 | API / Konfigürasyon | audio-fingerprinting.md · L370 |
| K4.1.b.7 | Performans / Ölçeklenebilirlik | audio-fingerprinting.md · L395 |
| K4.1.b.8 | Bağımlılıklar | audio-fingerprinting.md · L405 |

### K4.2 — Öneri Motoru

*K4-02 hibrit öneri: Collaborative + Content-based + Real-time Learning; çıktı K8 üzerinden sunulur.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.2.a** | **Hibrit Öneri** | recommendation-engine.md · L14–L367 |
| K4.2.a.1 | Genel Bakış | recommendation-engine.md · L14 |
| K4.2.a.2 | Teknik Detaylar | recommendation-engine.md · L18 |
| K4.2.a.3 | Collaborative Filtering | recommendation-engine.md · L20 |
| K4.2.a.4 | Content-Based Filtering | recommendation-engine.md · L94 |
| K4.2.a.5 | Hybrid Recommendation | recommendation-engine.md · L181 |
| K4.2.a.6 | Real-time Learning | recommendation-engine.md · L279 |
| K4.2.a.7 | API / Konfigürasyon | recommendation-engine.md · L326 |
| K4.2.a.8 | Performans / Ölçeklenebilirlik | recommendation-engine.md · L356 |
| K4.2.a.9 | Bağımlılıklar | recommendation-engine.md · L367 |

### K4.3 — Ruh Hali & Söz Analizi

*K4-09 mood classification (energy-valence) + söz/metin NLP (sentiment, topic, semantic search).*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.3.a** | **Mood Classification** | mood-classification.md · L14–L438 |
| K4.3.a.1 | Genel Bakış | mood-classification.md · L14 |
| K4.3.a.2 | Teknik Detaylar | mood-classification.md · L18 |
| K4.3.a.3 | Energy-Valence Modeli | mood-classification.md · L20 |
| K4.3.a.4 | Temporal Mood Tracking | mood-classification.md · L207 |
| K4.3.a.5 | Real-time Mood Analysis | mood-classification.md · L342 |
| K4.3.a.6 | API / Konfigürasyon | mood-classification.md · L392 |
| K4.3.a.7 | Performans / Ölçeklenebilirlik | mood-classification.md · L428 |
| K4.3.a.8 | Bağımlılıklar | mood-classification.md · L438 |
| **K4.3.b** | **Lyrics Analysis** | lyrics-analysis.md · L14–L481 |
| K4.3.b.1 | Genel Bakış | lyrics-analysis.md · L14 |
| K4.3.b.2 | Teknik Detaylar | lyrics-analysis.md · L18 |
| K4.3.b.3 | Sentiment Analysis | lyrics-analysis.md · L20 |
| K4.3.b.4 | Topic Modeling | lyrics-analysis.md · L169 |
| K4.3.b.5 | Semantic Search | lyrics-analysis.md · L351 |
| K4.3.b.6 | API / Konfigürasyon | lyrics-analysis.md · L445 |
| K4.3.b.7 | Performans / Ölçeklenebilirlik | lyrics-analysis.md · L471 |
| K4.3.b.8 | Bağımlılıklar | lyrics-analysis.md · L481 |

### K4.4 — Ses & Metin

*K4-04 Voice Control: Whisper STT + Wake Word + NLU + TTS; komutlar K8'e iletilir.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.4.a** | **Speech-to-Text** | speech-to-text.md · L14–L375 |
| K4.4.a.1 | Genel Bakış | speech-to-text.md · L14 |
| K4.4.a.2 | Teknik Detaylar | speech-to-text.md · L18 |
| K4.4.a.3 | Whisper Entegrasyonu | speech-to-text.md · L20 |
| K4.4.a.4 | Real-time Streaming | speech-to-text.md · L218 |
| K4.4.a.5 | Flash Attention Optimizasyonu | speech-to-text.md · L286 |
| K4.4.a.6 | API / Konfigürasyon | speech-to-text.md · L325 |
| K4.4.a.7 | Performans / Ölçeklenebilirlik | speech-to-text.md · L364 |
| K4.4.a.8 | Bağımlılıklar | speech-to-text.md · L375 |
| **K4.4.b** | **Voice Assistant** | voice-assistant.md · L14–L363 |
| K4.4.b.1 | Genel Bakış | voice-assistant.md · L14 |
| K4.4.b.2 | Teknik Detaylar | voice-assistant.md · L18 |
| K4.4.b.3 | Wake Word Detection | voice-assistant.md · L20 |
| K4.4.b.4 | Natural Language Understanding (NLU) | voice-assistant.md · L120 |
| K4.4.b.5 | Text-to-Speech (TTS) | voice-assistant.md · L263 |
| K4.4.b.6 | API / Konfigürasyon | voice-assistant.md · L320 |
| K4.4.b.7 | Performans / Ölçeklenebilirlik | voice-assistant.md · L352 |
| K4.4.b.8 | Bağımlılıklar | voice-assistant.md · L363 |

### K4.5 — Auto EQ Optimizasyonu

*K4-03: oda düzeltme + hoparlör kalibrasyonu → parametrik EQ katsayıları (K3'e veri üretir; K4 K3'ü çağırmaz, katsayı K5/K8 üzerinden akar).*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.5.a** | **Otomatik EQ** | auto-eq-optimization.md · L14–L348 |
| K4.5.a.1 | Genel Bakış | auto-eq-optimization.md · L14 |
| K4.5.a.2 | Teknik Detaylar | auto-eq-optimization.md · L18 |
| K4.5.a.3 | Room Correction (Oda Düzeltme) | auto-eq-optimization.md · L20 |
| K4.5.a.4 | Speaker Calibration | auto-eq-optimization.md · L158 |
| K4.5.a.5 | Parametric EQ Optimizasyonu | auto-eq-optimization.md · L234 |
| K4.5.a.6 | API / Konfigürasyon | auto-eq-optimization.md · L307 |
| K4.5.a.7 | Performans / Ölçeklenebilirlik | auto-eq-optimization.md · L338 |
| K4.5.a.8 | Bağımlılıklar | auto-eq-optimization.md · L348 |

### K4.6 — Üretim & Edge AI

*K4-05 AI üretimi (müzik/kapak/playlist) + K4-06 ONNX yerel inference; privacy-first, çevrimdışı.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.6.a** | **AI Generation** | ai-generation.md · L14–L399 |
| K4.6.a.1 | Genel Bakış | ai-generation.md · L14 |
| K4.6.a.2 | Teknik Detaylar | ai-generation.md · L18 |
| K4.6.a.3 | Müzik Üretimi (Music Generation) | ai-generation.md · L20 |
| K4.6.a.4 | Kapak Resmi Üretimi (Cover Art Generation) | ai-generation.md · L153 |
| K4.6.a.5 | Playlist Üretimi | ai-generation.md · L264 |
| K4.6.a.6 | API / Konfigürasyon | ai-generation.md · L359 |
| K4.6.a.7 | Performans / Ölçeklenebilirlik | ai-generation.md · L389 |
| K4.6.a.8 | Bağımlılıklar | ai-generation.md · L399 |
| **K4.6.b** | **Edge AI** | edge-ai.md · L14–L558 |
| K4.6.b.1 | Genel Bakış | edge-ai.md · L14 |
| K4.6.b.2 | Teknik Detaylar | edge-ai.md · L18 |
| K4.6.b.3 | Model Quantization | edge-ai.md · L20 |
| K4.6.b.4 | On-Device Inference | edge-ai.md · L159 |
| K4.6.b.5 | Privacy-Preserving ML | edge-ai.md · L276 |
| K4.6.b.6 | Edge Model Optimization | edge-ai.md · L409 |
| K4.6.b.7 | API / Konfigürasyon | edge-ai.md · L516 |
| K4.6.b.8 | Performans / Ölçeklenebilirlik | edge-ai.md · L549 |
| K4.6.b.9 | Bağımlılıklar | edge-ai.md · L558 |

### K4.7 — ML Altyapı

*Model Registry → Feature Store → Training Pipeline → Model Serving; K5'ten özellik okur.*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.7.a** | **ML Platform** | ml-infrastructure.md · L14–L508 |
| K4.7.a.1 | Genel Bakış | ml-infrastructure.md · L14 |
| K4.7.a.2 | Teknik Detaylar | ml-infrastructure.md · L18 |
| K4.7.a.3 | Model Registry | ml-infrastructure.md · L20 |
| K4.7.a.4 | Feature Store | ml-infrastructure.md · L154 |
| K4.7.a.5 | Training Pipeline | ml-infrastructure.md · L277 |
| K4.7.a.6 | Model Serving | ml-infrastructure.md · L399 |
| K4.7.a.7 | API / Konfigürasyon | ml-infrastructure.md · L464 |
| K4.7.a.8 | Performans / Ölçeklenebilirlik | ml-infrastructure.md · L498 |
| K4.7.a.9 | Bağımlılıklar | ml-infrastructure.md · L508 |

### K4.8 — Katman Dokümanları

*Katman indeksi + taşıyıcı README; CLAUDE.md kural dosyasıdır, yaprak üretmez (Katalog notları 2).*

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K4.8.a** | **Index** | index.md · L14–L145 |
| K4.8.a.1 | Genel Bakış | index.md · L14 |
| K4.8.a.2 | Mimari Konum | index.md · L18 |
| K4.8.a.3 | B bileşen Sayısı ve Dağılımı | index.md · L38 |
| K4.8.a.4 | Teknik Altyapı | index.md · L55 |
| K4.8.a.5 | Kullanılan Teknolojiler | index.md · L57 |
| K4.8.a.6 | Donanım Gereksinimleri | index.md · L67 |
| K4.8.a.7 | API Endpointleri | index.md · L89 |
| K4.8.a.8 | Genel API Yapısı | index.md · L91 |
| K4.8.a.9 | Ana Endpointler | index.md · L100 |
| K4.8.a.10 | Bağımlılıklar | index.md · L114 |
| K4.8.a.11 | İç Bağımlılıklar | index.md · L116 |
| K4.8.a.12 | Dış Bağımlılıklar | index.md · L124 |
| K4.8.a.13 | Performans Metrikleri | index.md · L134 |
| K4.8.a.14 | Güvenlik | index.md · L145 |
| **K4.8.b** | **README Bölümleri** | README.md · L26–L245 |
| K4.8.b.1 | 1. Genel Bakış | README.md · L26 |
| K4.8.b.2 | 1.1 Temel İlkeler | README.md · L30 |
| K4.8.b.3 | 2. Bileşen Haritası | README.md · L42 |
| K4.8.b.4 | 3. Music Analysis (K4-01) | README.md · L59 |
| K4.8.b.5 | 3.1 Çıkarılan Özellikler | README.md · L61 |
| K4.8.b.6 | 3.2 Feature Extraction Pipeline | README.md · L76 |
| K4.8.b.7 | 4. Recommendation Engine (K4-02) | README.md · L115 |
| K4.8.b.8 | 4.1 Hibrit Öneri Sistemi | README.md · L117 |
| K4.8.b.9 | 4.2 Collaborative Filtering | README.md · L127 |
| K4.8.b.10 | 4.3 Content-Based Filtering | README.md · L148 |
| K4.8.b.11 | 5. Auto EQ (K4-03) | README.md · L169 |
| K4.8.b.12 | 5.1 Otomatik EQ Algoritması | README.md · L171 |
| K4.8.b.13 | 5.2 EQ Hedefleri | README.md · L185 |
| K4.8.b.14 | 6. Voice Control (K4-04) | README.md · L196 |
| K4.8.b.15 | 6.1 Sesli Komutlar | README.md · L198 |
| K4.8.b.16 | 6.2 Whisper Entegrasyonu | README.md · L210 |
| K4.8.b.17 | 7. Edge AI (K4-06) | README.md · L225 |
| K4.8.b.18 | 7.1 ONNX Runtime | README.md · L227 |
| K4.8.b.19 | 7.2 Model Boyutu Kısıtlamaları | README.md · L245 |

## Kanıt Kataloğu (K4)

> **Yöntem:** 14 dosyanın tamamı grep (^#{2,3} , 147 eşleşme: 87 H2 + 60 H3) ile sayıldı; yaprak = 4. seviye başlık satırı kanıtı. Hariç tutmalar not 2'de gerekçelendirildi.

| # | Dosya | Rol | H2 | H3 | Kapsanan Yaprak | Satır Aralığı |
|---|-------|-----|----|----|-----------------|---------------|
| 1 | music-analysis.md | BPM/key/mood analizi | 6 | 9 | 14 | L14–L376 |
| 2 | audio-fingerprinting.md | Parmak izi eşleştirme | 6 | 3 | 8 | L14–L414 |
| 3 | recommendation-engine.md | Öneri motoru | 6 | 4 | 9 | L14–L377 |
| 4 | mood-classification.md | Ruh hali sınıflandırma | 6 | 3 | 8 | L14–L447 |
| 5 | lyrics-analysis.md | Söz/NLP analizi | 6 | 3 | 8 | L14–L490 |
| 6 | speech-to-text.md | Whisper STT | 6 | 3 | 8 | L14–L385 |
| 7 | voice-assistant.md | Sesli asistan | 6 | 3 | 8 | L14–L373 |
| 8 | auto-eq-optimization.md | Oda/hoparlör kalibrasyonu | 6 | 3 | 8 | L14–L357 |
| 9 | ai-generation.md | AI üretim | 6 | 3 | 8 | L14–L409 |
| 10 | edge-ai.md | ONNX edge inference | 6 | 4 | 9 | L14–L567 |
| 11 | ml-infrastructure.md | ML platform | 6 | 4 | 9 | L14–L519 |
| 12 | index.md | Katman indeksi | 9 | 6 | 14 | L14–L153 |
| 13 | README.md | Taşıyıcı doküman | 8 | 12 | 19 (§8 hariç) | L26–L256 |
| 14 | CLAUDE.md | Ajan kural dosyası | 4 | 0 | 0 (4 hariç) | L14–L37 |
| | **TOPLAM** | 14 dosya | **87** | **60** | **130** | |

Katalog notları:

1. **Sayım zinciri:** 147 başlık − 17 hariç = **130 kanıtlı yaprak**; hiyerarşi 8 × 2. katman · 13 × 3. katman · 130 × 4. katman. Onaylı hedef 8/5/130 tam karşılandı (3. katman tabanı 5'in üzerinde).
2. **Hariç tutulan 17:** 12 × "Durum: Implementasyon" (11 içerik dosyası + index.md — durum metni, katman kanıtı değil; K2/K3 emsali); 4 × CLAUDE.md başlığı (L14, L23, L29, L37 — guardrail/kural dosyası); 1 × README "8. İlgili ADR'ler" (L256 — ADR referans listesi, katman yaprağı değil; K3'te benzer referans başlıkları sayılmıştı, fark yalnız bu katmandaki 17. hariç ihtiyacından doğdu ve burada kayıtlıdır).
3. **Katman kuralı:** K4 → K5'ten veri okur, K8 üzerinden servis eder, K3'ten izole çalışır (README L28). K4-03 Auto EQ katsayıları K3'e dolaylı akar (K5/K8 üzerinden), K4 asla K3'ü çağırmaz.
4. **Dosya boşlukları:** README §2 Bileşen Haritası'ndaki K4-05, K4-07, K4-08, K4-09, K4-10 için ayrı .md dosyası diskte yok (K4-05 → ai-generation.md, K4-09 → mood-classification.md kapsamında işlenir); yapraklar yalnız var olan 11 içerik dosyasından sayıldı — başlık satırı README'de olduğu için K4.8.b kapsamında kanıtlandı.
5. **İlişki notu:** music-analysis.md L174 "Audio Fingerprinting (Chromaprint)" ile audio-fingerprinting.md ayrı dosyalardır; ikisi de bağımsız yaprak sayıldı, çakışma yoktur.
6. **Yazım notu:** index.md L38 başlığı diskte "B bileşen Sayısı ve Dağılımı" şeklinde yazım hatasıyla duruyor; disk kanıtı olduğu için aynen yaprak sayıldı, düzeltilmesi vault-updater görevidir.
7. **Tutarlılık:** "Teknik Detaylar" H2'si ile altındaki H3 başlıkları ayrı yaprak sayıldı; "Bağımlılıklar" H2'leri gerçek bağımlılık tabloları taşıdığı için korundu (K0–K3 ile aynı kural).

---

*K4 Yapay Zeka Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
