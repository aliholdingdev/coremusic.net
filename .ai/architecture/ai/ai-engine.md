---
title: "CoreMusic — AI Engine"
type: architecture
category: ai-engine
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — AI Engine

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

---

## 1. Amaç

CoreMusic'in merkezi AI işleme motorunu tanımlar. Müzik önerileri, ses analizi, otomatik EQ, donanım analizi, hata tahmini ve content-based recommendation system.

---

## 2. AI Engine Bileşenleri (5 Modül)

| # | Modül | Sorumluluk | Teknoloji |
|---|-------|------------|-----------|
| 1 | **Recommendation Engine** | Müzik önerileri — collaborative + content-based | Matrix Factorization, ALS |
| 2 | **Audio Analyzer** | Ses analizi — BPM, key, energy, mood | FFT, Chroma, RMS |
| 3 | **EQ Optimizer** | Otomatik EQ — room correction, preset | DSP chain + AI |
| 4 | **Hardware Analyzer** | Donanım analizi — DAC/ADC performans, termal | PCM3168A, XMOS metrics |
| 5 | **Fault Predictor** | Hata tahmini — donanım arıza, performans düşüşü | Anomaly detection |

---

## 3. AI Pipeline

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Veri        │───▶│  Önişleme    │───▶│  Model       │───▶│  Sonuç       │───▶│  Eylem       │
│  Toplama     │    │  (Feature)   │    │  Çalıştırma  │    │  Üretimi     │    │  (Uygulama)  │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
       │                   │                   │                   │                   │
   Audio Stream      FFT/Chroma/RMS      LLM/ML/DSP        Recommendation       Auto-EQ
   User History      Normalization       Inference         Fault Alert          HW Config
   DB Metadata       Vector Encoding     Batch/Realtime    EQ Curve             Fault Log
```

---

## 4. Recommendation Engine Detayları

### 4.1 Collaborative Filtering

| Özellik | Açıklama |
|---------|----------|
| User-User | Benzer kullanıcı tercihleri |
| Item-Item | Benzer şarkı özellikleri |
| Matrix Factorization | ALS (Alternating Least Squares) |
| Cold Start | Yeni kullanıcılar için content-based fallback |

### 4.2 Content-Based

| Feature | Kaynak | Ağırlık |
|---------|--------|---------|
| BPM | Audio analysis | 0.15 |
| Key | Audio analysis | 0.10 |
| Energy | Audio analysis | 0.20 |
| Danceability | Audio analysis | 0.15 |
| Valence | Audio analysis | 0.10 |
| Acousticness | Audio analysis | 0.10 |
| Genre | Metadata | 0.10 |
| Artist | Metadata | 0.10 |

---

## 5. Auto EQ System

```
Audio Input → Spectrum Analysis → Room Correction → EQ Curve → Output
     ↓              ↓                  ↓              ↓
  PCM Stream    FFT Analysis      Room Profile    31-band EQ
```

### 5.1 EQ Modları

| Mod | Kaynak | Uygulama |
|-----|--------|----------|
| Manual | Kullanıcı | 31-band parametrik EQ |
| Preset | System | Konser Salonu, Stüdyo, Oda |
| Auto AI | DSP + AI | Ortam analizi + otomatik ayar |
| Crossfeed | Crossover | Hoparlör dinleme deneyimi |

---

## 6. Model Türleri

| Model Tipi | Kullanım Alanı | Örnek |
|------------|---------------|-------|
| **Classification** | Tür, mood, donanım durumu | Genre Classifier, Fault Detector |
| **Regression** | BPM tahmini, SNR ölçümü | BPM Estimator, THD Predictor |
| **Clustering** | Kullanıcı segmentasyonu, benzer şarkı | User Clustering, Track Grouping |
| **Anomaly Detection** | Donanım arıza, performans sapması | Fault Predictor, thermal anomaly |

---

## 7. Audio Feature Extraction

| Feature | Hesaplama | Aralığı |
|---------|-----------|---------|
| BPM | Autocorrelation | 60-200 |
| Key | Chroma features | C-B |
| Energy | RMS amplitude | 0.0-1.0 |
| Danceability | Beat strength + tempo | 0.0-1.0 |
| Valence | Spectral centroid | 0.0-1.0 |
| Acousticness | Spectral flatness | 0.0-1.0 |
| Instrumentalness | Harmonic analysis | 0.0-1.0 |
| Speechiness | Zero-crossing rate | 0.0-1.0 |

---

## 8. ML Model Envanteri

| Model | Amaç | Boyut | Latency |
|-------|------|-------|---------|
| Genre Classifier | Tür tahmini | ~10MB | <10ms |
| Mood Detector | Ruh hali | ~5MB | <5ms |
| BPM Estimator | Tempo tahmini | ~2MB | <3ms |
| Key Detector | Ton tahmini | ~3MB | <5ms |
| Recommendation | Öneri motoru | ~50MB | <50ms |
| Fault Predictor | Donanım arıza | ~8MB | <10ms |
| Hardware Analyzer | DAC/ADC analiz | ~5MB | <15ms |

---

## 9. Entegrasyon Noktaları

| Servis | Entegrasyon | Protokol | Veri Akışı |
|--------|-------------|----------|------------|
| Audio Service | Ses analizi, EQ | REST (9741) | PCM stream → Feature vector |
| DSP Firmware | EQ optimizasyonu | IPC | EQ curve → DSP chain |
| Device Service | Donanım analizi | BLE/WiFi | HW metrics → Fault predictor |
| Download Service | Metadata extraction | Internal | File → Metadata |
| Knowledge Base | Bilgi sorgulama | File System | Query → Result |
| Memory System | Session persistence | Internal | Cache → DB |

---

## 10. AI Inference Modları

| Mod | Konum | Kullanım | Latency |
|-----|-------|----------|---------|
| **Local (Embedded)** | Cihaz üzerinde | Gerçek zamanlı EQ, DSP | <5ms |
| **Hybrid** | Yerel + bulut | Öneri motoru, analiz | <100ms |
| **Cloud** | Uzak sunucu | Büyük model eğitimi, batch | <1s |

---

## 11. Data Flow

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  User DB    │────▶│  AI Engine  │────▶│  Response   │
│  (History)  │     │  (Pipeline) │     │  (Recs)     │
└─────────────┘     └─────────────┘     └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Audio DB   │────▶│  Feature    │────▶│  Model      │
│  (Metadata) │     │  Extraction │     │  Inference  │
└─────────────┘     └─────────────┘     └─────────────┘
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  HW Metrics │────▶│  Hardware   │────▶│  Fault      │
│  (PCM3168A) │     │  Analyzer   │     │  Predictor  │
└─────────────┘     └─────────────┘     └─────────────┘
```

---

## 12. Performance Targets

| Metrik | Hedef | ADR |
|--------|-------|-----|
| TTFB | <200ms | ADR-006 |
| API Response | <100ms | ADR-006 |
| Recommendation Latency | <500ms | — |
| Audio Analysis | <1s per track | — |
| Model Inference | <50ms | — |
| EQ Optimization | <100ms | — |
| Fault Prediction | <200ms | — |

---

## 13. Security

| Kural | Açıklama |
|-------|----------|
| Prompt Injection | Input sanitization zorunlu |
| Model Poisoning | Training data validation |
| Data Privacy | Kullanıcı verisi anonimize |
| Rate Limiting | API abuse önleme |
| Model Access | RBAC ile kontrol |
| Inference Audit | Tüm tahminler loglanır |

---

## 14. Edge Cases

| Senaryo | Çözüm |
|---------|-------|
| Cold start user | Content-based fallback |
| No audio features | Metadata-only recommendation |
| Model failure | Rule-based fallback |
| High latency | Cache + precompute |
| Data sparsity | Hybrid approach |
| HW metric loss | Last known value + alert |
| Model drift | Periodic retraining |

---

## 15. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Bileşenler | [[ai-orchestrator]] | Görev dağıtımı |
| § 3 Pipeline | [[tool-calling]] | Dış servisler |
| § 5 Auto EQ | [[ADR-025-professional-eq-system]] | 31-band EQ |
| § 8 Modeller | [[knowledge-base]] | Bilgi bankası |
| § 9 Entegrasyon | [[ADR-039-7-service-platform-architecture]] | 7 servis |
| § 10 Inference | [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
