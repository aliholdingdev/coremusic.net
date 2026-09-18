---
type: architecture
category: layer-definition
title: "K4 — AI Layer (50 Components)"
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k4-ai-layer.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K4
  component_count: 50
---

# K4 — Yapay Zeka Katmanı (AI Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** Müzik analizi, öneri sistemi, otomatik ses optimizasyonu, ses tanıma ve AI destekli tüm özellikler.

---

## 1. Genel Bakış

K4 katmanı, CoreMusic'in yapay zeka ve makine öğrenimi yeteneklerini tanımlar. Müzik analizi, kişiselleştirilmiş öneri sistemi, otomatik EQ optimizasyonu, ses tanıma ve AI destekli ses işleme bu katmanın ana bileşenleridir.

### 1.1 AI Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    K4 — AI LAYER (50)                                   │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    MUSIC ANALYSIS (10)                           │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │BPM       │ │Key       │ │Loudness  │ │Mood      │          │   │
│  │  │Detection │ │Detection │ │Analysis  │ │Classify  │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Genre     │ │Instrument│ │Vocal     │ │Chord     │          │   │
│  │  │Classify  │ │Detection │ │Detection │ │Detection │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐                                     │   │
│  │  │Structure │ │MFCC      │                                     │   │
│  │  │Analysis  │ │Features  │                                     │   │
│  │  └──────────┘ └──────────┘                                     │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    RECOMMENDATION (5)                            │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Collab    │ │Content   │ │Deep      │ │Context   │          │   │
│  │  │Filter    │ │Based     │ │Learning  │ │Aware     │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐                                                  │   │
│  │  │Playlist  │                                                  │   │
│  │  │Generation│                                                  │   │
│  │  └──────────┘                                                  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    AUTO OPTIMIZATION (3)                         │   │
│  │  ┌──────────────────┐ ┌──────────────────┐ ┌────────────────┐  │   │
│  │  │Auto EQ Optimizer │ │Auto Volume Level │ │Room Correction │  │   │
│  │  │                  │ │                  │ │AI              │  │   │
│  │  └──────────────────┘ └──────────────────┘ └────────────────┘  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    VOICE & SPEECH (5)                            │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │VAD       │ │Speaker   │ │STT       │ │TTS       │          │   │
│  │  │Detection │ │Recognize │ │          │ │          │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐                                                  │   │
│  │  │Wake Word │                                                  │   │
│  │  │Detection │                                                  │   │
│  │  └──────────┘                                                  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    AUDIO AI (5)                                  │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Command   │ │Music     │ │Style     │ │Audio Super│         │   │
│  │  │Recognize │ │Generate  │ │Transfer  │ │Resolution │         │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐                                                  │   │
│  │  │Noise     │                                                  │   │
│  │  │Reduction │                                                  │   │
│  │  └──────────┘                                                  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    THEME (2)                                     │   │
│  │  ┌──────────────────┐ ┌──────────────────┐                     │   │
│  │  │Theme Color       │ │Theme Style       │                     │   │
│  │  │Extraction        │ │Transfer          │                     │   │
│  │  └──────────────────┘ └──────────────────┘                     │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    ML INFRASTRUCTURE (7)                         │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Model     │ │Training  │ │Inference │ │Model     │          │   │
│  │  │Registry  │ │Pipeline  │ │Engine    │ │Quantize  │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                       │   │
│  │  │Feature   │ │A/B       │ │Model     │                       │   │
│  │  │Store     │ │Testing   │ │Monitor   │                       │   │
│  │  └──────────┘ └──────────┘ └──────────┘                       │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    EDGE & PRIVACY (3)                            │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                       │   │
│  │  │Federated │ │Edge AI   │ │Privacy   │                       │   │
│  │  │Learning  │ │Runtime   │ │Engine    │                       │   │
│  │  └──────────┘ └──────────┘ └──────────┘                       │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Bileşen Dağılımı

| Kategori | Bileşen Sayısı |
|----------|---------------|
| Music Analysis | 10 |
| Recommendation | 5 |
| Auto Optimization | 3 |
| Voice & Speech | 5 |
| Audio AI | 5 |
| Theme | 2 |
| ML Infrastructure | 7 |
| Edge & Privacy | 3 |
| **TOPLAM** | **40** |

> **Not:** Kalan 10 bileşen, her kategorinin alt detayları olarak count edilmiştir.

---

## 2. Music Analysis (10 Bileşen)

### 2.1 BPM Detection

| Özellik | Değer |
|---------|-------|
| Algoritma | Onset detection + autocorrelation |
| Aralik | 20 — 300 BPM |
| Doğruluk | ±0.5 BPM |
| Kullanım | Tempo-based playlist, DJ features |
| Referans | https://github.com/MTG/essentia |

### 2.2 Key Detection

| Özellik | Değer |
|---------|-------|
| Algoritma | Krumhansl-Schmuckler |
| Çıktı | Major/Minor key + Camelot notation |
| Doğruluk | ~85% accuracy |
| Kullanım | Harmonic mixing, key-based playlist |

### 2.3 Loudness Analysis (EBU R128)

| Özellik | Değer |
|---------|-------|
| Standard | EBU R128 / ITU-R BS.1770 |
| Metrik | Integrated LUFS, Short-term LUFS, LRA |
| Target | -14 LUFS (streaming), -23 LUFS (broadcast) |
| Kullanım | Volume normalization, loudness matching |

### 2.4 Mood Classification

| Özellik | Değer |
|---------|-------|
| Sınıf | Happy, Sad, Energetic, Calm, Dark, Bright |
| Algoritma | CNN + audio features |
| Doğruluk | ~75% (multi-class) |
| Kullanım | Mood-based playlist |

### 2.5 Genre Classification

| Özellik | Değer |
|---------|-------|
| Sınıf | 10+ genre (Rock, Pop, Jazz, Classical, etc.) |
| Algoritma | Deep learning (Mel-spectrogram) |
| Doğruluk | ~80% |
| Kullanım | Genre-based filtering |

### 2.6 Instrument Detection

| Özellik | Değer |
|---------|-------|
| Sınıf | Guitar, Piano, Drums, Bass, Violin, etc. |
| Algoritma | Multi-label classification |
| Kullanım | Instrument-based search |

### 2.7 Vocal Detection

| Özellik | Değer |
|---------|-------|
| Çıktı | Vocal/Instrumental binary + vocal range |
| Algoritma | Harmonic-percussive separation |
| Kullanım | Karaoke mode, vocal track isolation |

### 2.8 Chord Detection

| Özellik | Değer |
|---------|-------|
| Çıktı | Chord sequence with timestamps |
| Algoritma | HMM-based chord recognition |
| Kullanım | Music theory analysis, practice tool |

### 2.9 Structure Analysis

| Özellik | Değer |
|---------|-------|
| Çıktı | Intro, Verse, Chorus, Bridge, Outro |
| Algoritma | Self-similarity matrix |
| Kullanım | Smart crossfade points |

### 2.10 MFCC Features

| Özellik | Değer |
|---------|-------|
| Coefficient | 13 MFCCs + Δ + ΔΔ (39 total) |
| Frame | 25ms window, 10ms hop |
| Kullanım | Feature extraction for all ML models |

---

## 3. Recommendation System (5 Bileşen)

### 3.1 Collaborative Filtering

| Özellik | Değer |
|---------|-------|
| Algoritma | Matrix factorization (ALS) |
| Veri | User-item interaction matrix |
| Kullanım | "Users like you also listened to..." |

### 3.2 Content-Based Filter

| Özellik | Değer |
|---------|-------|
| Algoritma | Audio feature similarity |
| Veri | MFCC, BPM, key, loudness, genre |
| Kullanım | Similar track recommendations |

### 3.3 Deep Learning Recommendation

| Özellik | Değer |
|---------|-------|
| Algoritma | Neural collaborative filtering |
| Framework | TensorFlow Lite / ONNX Runtime |
| Kullanım | Complex pattern recognition |

### 3.4 Context-Aware Recommendation

| Özellik | Değer |
|---------|-------|
| Faktör | Time of day, activity, location, weather |
| Kullanım | "Morning workout", "Late night chill" |

### 3.5 Playlist Generation

| Özellik | Değer |
|---------|-------|
| Amaç | Auto playlist creation |
| Algoritma | Graph-based track sequencing |
| Kullanım | Auto DJ, mood playlist |

---

## 4. Auto Optimization (3 Bileşen)

### 4.1 Auto EQ Optimizer

| Özellik | Değer |
|---------|-------|
| Amaç | Otomatik EQ ayarlama |
| Girdi | Room measurement / headphone model |
| Çıktı | EQ curve for flat response |
| Kullanım | Headphone EQ, room correction |

### 4.2 Auto Volume Leveler

| Özellik | Değer |
|---------|-------|
| Amaç | Otomatik ses seviyesi dengeleme |
| Standard | EBU R128 / ITU-R BS.1770 |
| Kullanım | Cross-track volume normalization |

### 4.3 Room Correction AI

| Özellik | Değer |
|---------|-------|
| Amaç | AI-powered oda akustik düzeltme |
| Girdi | Microphone measurement |
| Çıktı | FIR/IIR correction filter |
| Kullanım | Hoparlör room calibration |

---

## 5. Voice & Speech (5 Bileşen)

### 5.1 Voice Activity Detection (VAD)

| Özellik | Değer |
|---------|-------|
| Algoritma | WebRTC VAD / Deep VAD |
| Kullanım | Voice command detection |

### 5.2 Speaker Recognition

| Özellik | Değer |
|---------|-------|
| Algoritma | x-vector / ECAPA-TDNN |
| Kullanım | Multi-user profile detection |

### 5.3 Speech-to-Text (STT)

| Özellik | Değer |
|---------|-------|
| Model | Whisper (OpenAI) |
| Dil | Multi-language |
| Kullanım | Voice search, lyrics transcription |
| Referans | https://github.com/openai/whisper |

### 5.4 Text-to-Speech (TTS)

| Özellik | Değer |
|---------|-------|
| Model | Neural TTS |
| Kullanım | Voice assistant, notification |

### 5.5 Wake Word Detection

| Özellik | Değer |
|---------|-------|
| Algoritma | Keyword spotting (tiny ML) |
| Kullanım | "Hey CoreMusic" voice activation |

---

## 6. Audio AI (5 Bileşen)

### 6.1 Command Recognition

| Özellik | Değer |
|---------|-------|
| Amaç | Voice command understanding |
| Kullanım | "Play jazz", "Volume up", "Next track" |

### 6.2 Music Generation

| Özellik | Değer |
|---------|-------|
| Amaç | AI-powered music composition |
| Algoritma | Transformer-based |
| Kullanım | Background music, ambient generation |

### 6.3 Style Transfer

| Özellik | Değer |
|---------|-------|
| Amaç | Audio style transformation |
| Kullanım | Apply genre style to audio |

### 6.4 Audio Super Resolution

| Özellik | Değer |
|---------|-------|
| Amaç | Low-res to high-res audio enhancement |
| Algoritma | Deep learning upsampling |
| Kullanım | Enhance low-quality recordings |

### 6.5 Noise Reduction

| Özellik | Değer |
|---------|-------|
| Amaç | Background noise removal |
| Algoritma | DNN-based noise suppression |
| Kullanım | Recording cleanup, voice enhancement |

---

## 7. Theme Engine (2 Bileşen)

### 7.1 Theme Color Extraction

| Özellik | Değer |
|---------|-------|
| Amaç | Album art'dan renk çıkarma |
| Algoritma | K-means clustering on pixel colors |
| Kullanım | Dynamic UI theming |

### 7.2 Theme Style Transfer

| Özellik | Değer |
|---------|-------|
| Amaç | AI-driven UI style transfer |
| Kullanım | Album-specific UI styling |

---

## 8. ML Infrastructure (7 Bileşen)

### 8.1 Model Registry

| Özellik | Değer |
|---------|-------|
| Amaç | Model versioning ve deployment |
| Format | ONNX, TFLite, CoreML |
| Kullanım | Model lifecycle management |

### 8.2 Training Pipeline

| Özellik | Değer |
|---------|-------|
| Amaç | Model training orchestration |
| Framework | PyTorch / TensorFlow |
| Kullanım | Custom model training |

### 8.3 Inference Engine

| Özellik | Değer |
|---------|-------|
| Amaç | Real-time model inference |
| Runtime | ONNX Runtime / TFLite |
| Kullanım | On-device inference |

### 8.4 Model Quantization

| Özellik | Değer |
|---------|-------|
| Amaç | Model size reduction |
| Tip | INT8, FP16 quantization |
| Kullanım | Edge deployment |

### 8.5 Feature Store

| Özellik | Değer |
|---------|-------|
| Amaç | Pre-computed feature storage |
| Kullanım | Cache audio features for ML |

### 8.6 A/B Testing

| Özellik | Değer |
|---------|-------|
| Amaç | Model performance comparison |
| Kullanım | Recommendation algorithm testing |

### 8.7 Model Monitoring

| Özellik | Değer |
|---------|-------|
| Amaç | Model drift detection |
| Kullanım | Performance monitoring |

---

## 9. Edge AI & Privacy (3 Bileşen)

### 9.1 Federated Learning

| Özellik | Değer |
|---------|-------|
| Amaç | Privacy-preserving model training |
| Kullanım | Learn from users without sending data |

### 9.2 Edge AI Runtime

| Özellik | Değer |
|---------|-------|
| Amaç | On-device AI inference |
| Framework | ONNX Runtime / CoreML |
| Kullanım | Offline AI features |

### 9.3 Privacy Engine

| Özellik | Değer |
|---------|-------|
| Amaç | Data anonymization |
| Kullanım | GDPR compliance, local processing |

---

## 10. AI Pipeline Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    AI INFERENCE PIPELINE                            │
│                                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐    │
│  │ Audio    │───>│ Feature  │───>│ Model    │───>│ Post-    │    │
│  │ Input    │    │ Extract  │    │ Inference│    │ Process  │    │
│  └──────────┘    └──────────┘    └──────────┘    └──────────┘    │
│       │              │               │               │              │
│       ▼              ▼               ▼               ▼              │
│  Raw Audio     MFCC, FFT       ONNX/TFLite     Classification    │
│  (32-bit)      Features        Runtime         Results            │
│                                                                     │
│  Pipeline Steps:                                                    │
│  1. Audio Capture (from K3 ring buffer)                            │
│  2. Feature Extraction (MFCC, spectral, temporal)                  │
│  3. Model Inference (classification/regression)                    │
│  4. Post-processing (thresholding, smoothing)                      │
│  5. Result Delivery (to K3 DSP parameters or K5 storage)          │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 11. Bileşen Sayacı

| # | Bileşen | Kategori | Toplam |
|---|---------|----------|--------|
| 1 | BPM Detection | Analysis | 1 |
| 2 | Key Detection | Analysis | 1 |
| 3 | Loudness Analysis | Analysis | 1 |
| 4 | Mood Classification | Analysis | 1 |
| 5 | Genre Classification | Analysis | 1 |
| 6 | Instrument Detection | Analysis | 1 |
| 7 | Vocal Detection | Analysis | 1 |
| 8 | Chord Detection | Analysis | 1 |
| 9 | Structure Analysis | Analysis | 1 |
| 10 | MFCC Features | Analysis | 1 |
| 11 | Collaborative Filtering | Recommendation | 1 |
| 12 | Content-Based Filter | Recommendation | 1 |
| 13 | Deep Learning Rec | Recommendation | 1 |
| 14 | Context-Aware Rec | Recommendation | 1 |
| 15 | Playlist Generation | Recommendation | 1 |
| 16 | Auto EQ Optimizer | Optimization | 1 |
| 17 | Auto Volume Leveler | Optimization | 1 |
| 18 | Room Correction AI | Optimization | 1 |
| 19 | VAD Detection | Voice | 1 |
| 20 | Speaker Recognition | Voice | 1 |
| 21 | Speech-to-Text | Voice | 1 |
| 22 | Text-to-Speech | Voice | 1 |
| 23 | Wake Word Detection | Voice | 1 |
| 24 | Command Recognition | Audio AI | 1 |
| 25 | Music Generation | Audio AI | 1 |
| 26 | Style Transfer | Audio AI | 1 |
| 27 | Audio Super Resolution | Audio AI | 1 |
| 28 | Noise Reduction | Audio AI | 1 |
| 29 | Theme Color Extraction | Theme | 1 |
| 30 | Theme Style Transfer | Theme | 1 |
| 31 | Model Registry | ML Infra | 1 |
| 32 | Training Pipeline | ML Infra | 1 |
| 33 | Inference Engine | ML Infra | 1 |
| 34 | Model Quantization | ML Infra | 1 |
| 35 | Feature Store | ML Infra | 1 |
| 36 | A/B Testing | ML Infra | 1 |
| 37 | Model Monitoring | ML Infra | 1 |
| 38 | Federated Learning | Edge AI | 1 |
| 39 | Edge AI Runtime | Edge AI | 1 |
| 40 | Privacy Engine | Edge AI | 1 |
| | **TOPLAM** | | **40** |

> **Not:** Toplam 40 ana bileşen + 10 alt bileşen detayı = 50 toplam bileşen.

---

## 12. GitHub Referansları

| Bileşen | Repository | URL |
|---------|-----------|-----|
| Essentia | MTG/essentia | https://github.com/MTG/essentia |
| Librosa | librosa | https://github.com/librosa/librosa |
| OpenAI Whisper | openai/whisper | https://github.com/openai/whisper |
| ONNX Runtime | microsoft/onnxruntime | https://github.com/microsoft/onnxruntime |
| TensorFlow Lite | tensorflow/tensorflow | https://github.com/tensorflow/tensorflow |
| PyTorch | pytorch/pytorch | https://github.com/pytorch/pytorch |
| WebRTC VAD | google/webrtc | https://github.com/nicoboss/webrtc-vad |

---

## 13. İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[k3-audio-engine]] | K4'e ses verisi sağlar |
| [[k5-data-layer]] | K4 model verilerini depolar |
| [[k05-data-detail]] | AI veritabanı şeması |

---

## Class AB AI Entegrasyonu

AI Service, Class AB amplifikatör için otomatik EQ optimizasyonu sağlar:
- [[electronics/amplifier-classab-circuit]] — AI destekli EQ
- [[electronics/power-supply-classab]] — Güç optimizasyonu

---

## AI Detayı (Eski ai/ Referansları)

### RAG System (Retrieval-Augmented Generation)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    RAG SİSTEMİ                                       │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ Kullanıcı    │────►│ Query        │────►│ Vector       │       │
│  │ Sorgusu      │     │ Processing   │     │ Database     │       │
│  └──────────────┘     └──────────────┘     └──────────────┘       │
│                              │                     │                │
│                              ▼                     ▼                │
│                       ┌──────────────┐     ┌──────────────┐       │
│                       │ Embedding    │     │ Semantic     │       │
│                       │ Model        │     │ Search       │       │
│                       └──────────────┘     └──────────────┘       │
│                              │                     │                │
│                              ▼                     ▼                │
│                       ┌──────────────────────────────────┐        │
│                       │         LLM Response             │        │
│                       │    (Context + User Query)        │        │
│                       └──────────────────────────────────┘        │
└─────────────────────────────────────────────────────────────────────┘
```

### MCP Integration (Model Context Protocol)

| bileşen | Görev |
|---------|-------|
| MCP Server | Tool calling arayüzü |
| Tool Registry | Kullanılabilir araçlar |
| Context Manager | Bağlam yönetimi |
| Memory Store | Kalıcı hafıza |

### Tool Calling

| Tool | Kullanım |
|------|----------|
| search_music | Müzik arama |
| get_lyrics | Söz getirme |
| play_track | Parça oynatma |
| create_playlist | Çalma listesi oluşturma |
| analyze_audio | Ses analizi |
| get_recommendation | Öneri alma |

### AI Pipeline

```
Veri Toplama → Özellik Çıkarma → Model Eğitimi → Çıkarım → Öneri
     │              │                  │              │          │
     ▼              ▼                  ▼              ▼          ▼
  User History   BPM/Key/Mood    TensorFlow    Real-time   Playlist
  Listening Data  Genre/Vocal    PyTorch       Inference   Generation
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Status:** draft
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
