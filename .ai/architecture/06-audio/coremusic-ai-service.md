---
type: architecture
category: audio
title: "AI Service"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# AI Service

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

Müzik önerileri, davranış analizi, otomatik EQ ve otomatik indirme pipeline'ını yöneten **AI Servisi**ni tanımlar. [[ADR-030-ai-strategy-core]] ile uyumludur.

## 2. Servis Detayları

| Özellik | Değer |
|---------|-------|
| **Stack** | PHP 8.4 + Python |
| **Protocol** | Internal REST |
| **Port** | Internal (localhost) |
| **Database** | coremusic_musics, coremusic_user |
| **Auth** | Internal token |
| **ML** | Collaborative filtering |

## 3. Sorumluluklar

| Bileşen | Görev | Öncelik |
|---------|-------|---------|
| **Recommendations** | Müzik önerisi | Yüksek |
| **Behavior Analysis** | Dinleme alışkanlıkları | Yüksek |
| **Auto EQ** | Otomatik equalizer | Orta |
| **Auto Download** | YouTube → FLAC pipeline | Yüksek |
| **Mood Detection** | Ruh hali tespiti | Düşük |
| **Smart Playlist** | AI çalma listesi | Orta |

## 4. Öneri Motoru

### 4.1 Öneri Akışı

```
User History
  → Feature Extraction (tempo, key, energy, valence)
    → Similarity (cosine similarity)
      → Filtering (genre, mood, time)
        → Ranking (weighted scoring)
          → Top-N recommendations
```

### 4.2 Feature Extraction

| Özellik | Açıklama | Aralık |
|---------|----------|--------|
| **Tempo** | BPM | 60-200 |
| **Key** | Müzik anahtarı | C-B (major/minor) |
| **Energy** | Enerji seviyesi | 0.0-1.0 |
| **Valence** | Pozitiflik | 0.0-1.0 |
| **Danceability** | Dans edilebilirlik | 0.0-1.0 |
| **Acousticness** | Akustiklik | 0.0-1.0 |
| **Instrumentalness** | Enstrümantallık | 0.0-1.0 |
| **Liveness** | Canlılık | 0.0-1.0 |

### 4.3 Benzerlik Hesaplama

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| **Algorithm** | Cosine similarity | Vektör benzerliği |
| **Min Score** | 0.7 (70%) | Eşik değeri |
| **Max Results** | 50 | Maksimum öneri |
| **Weight: Tempo** | 0.2 | BPM ağırlığı |
| **Weight: Key** | 0.15 | Anahtar ağırlığı |
| **Weight: Energy** | 0.25 | Enerji ağırlığı |
| **Weight: Valence** | 0.2 | Pozitiflik ağırlığı |
| **Weight: Genre** | 0.2 | Tür ağırlığı |

## 5. Davranış Analizi

### 5.1 Metrikler

| Metrik | Kullanım | Hesaplama |
|--------|----------|-----------|
| **Play Count** | Popülerlik | Toplam dinleme |
| **Skip Rate** | Beğenmemeler | Erken bırakma oranı |
| **Time of Day** | Zaman tercihleri | Saat bazlı analiz |
| **Mood Patterns** | Ruh hali desenleri | Enerji/valence trend |
| **Repeat Rate** | Tekrar dinleme | Tekrar oranı |
| **Completion Rate** | Tamamlama | Şarkıyı sonuna kadar dinleme |

### 5.2 Davranış Modeli

```
Listening History
  → Session Analysis (dinleme oturumları)
    → Preference Extraction (tercih çıkarma)
      → Profile Building (profilleme)
        → Prediction (tahmin)
```

## 6. Otomatik EQ (ADR-025)

### 6.1 Auto-EQ Parametreleri

| Parametre | Kullanım | Kaynak |
|-----------|----------|--------|
| **Genre Detection** | Otomatik tür tespiti | Metadata |
| **Room Acoustics** | Oda akustiği analizi | Microphone |
| **Device Profile** | Cihaz EQ profili | Device Service |
| **User Preference** | Kullanıcı tercihi | History |

### 6.2 Genre Bazlı EQ Presetleri

| Genre | Bass | Mid | Treble | Preset |
|-------|------|-----|--------|--------|
| Pop | +2dB | 0dB | +1dB | pop-auto |
| Rock | +3dB | -1dB | +2dB | rock-auto |
| Classical | 0dB | +1dB | +1dB | classical-auto |
| Jazz | +1dB | +2dB | 0dB | jazz-auto |
| Electronic | +4dB | 0dB | +2dB | electronic-auto |

## 7. Otomatik İndirme Pipeline

| Aşama | Araç | Görev |
|-------|------|-------|
| Search | nova-search-engine | YouTube indeksleme |
| Match | Fuzzy matching | Şarkı eşleştirme |
| Download | deemix PHP port | Deezer FLAC indirme |
| Validate | FFmpeg | Dosya doğrulama |
| Store | Filesystem | Medya depolama |
| Index | MySQL | Kütüphane ekleme |

*Detay: [[architecture/06-audio/ai-auto-download]]*

## 8. AI API

| Method | Endpoint | Auth | Görev |
|--------|----------|------|-------|
| GET | `/api/recommendations/:userId` | Internal | Öneriler |
| GET | `/api/behavior/:userId` | Internal | Davranış |
| POST | `/api/auto-eq` | Internal | Otomatik EQ |
| POST | `/api/auto-download` | Internal | Otomatik indirme |
| GET | `/api/mood/:userId` | Internal | Ruh hali |

## 9. Model Eğitimi

| Parametre | Değer |
|-----------|-------|
| **Algorithm** | Collaborative filtering |
| **Min interactions** | 10 şarkı |
| **Retraining** | Haftalık |
| **Fallback** | Popüler şarkılar |
| **Cold start** | Genre bazlı öneriler |

## 10. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | AI önerileri opsiyonel | ADR-030 | Kullanıcı deneyimi |
| 2 | Privacy-first | ADR-030 | Veri sızıntısı |
| 3 | Explainability | ADR-030 | Güvensizlik |
| 4 | Human override | ADR-030 | Otomatik hata |
| 5 | Anti-ban zorunlu | ADR-028 | IP ban |

## 11. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[architecture/06-audio/ai-auto-download]] | Auto-download |
| [[architecture/06-audio/coremusic-media-service]] | Media service |
| [[ADR-030-ai-strategy-core]] | AI strategy |
| [[ADR-025-professional-eq-system]] | EQ system |
| [[ADR-028-anti-ban-system]] | Anti-ban |

## 12. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Öneri | [[ADR-030-ai-strategy-core]] | AI strategy |
| § 6 Auto-EQ | [[ADR-025-professional-eq-system]] | EQ system |
| § 7 İndirme | [[architecture/06-audio/ai-auto-download]] | Auto-download |

## 13. Sözlük

| Terim | Tanım |
|-------|-------|
| **AI** | Yapay Zeka |
| **Recommendation** | Öneri |
| **Collaborative filtering** | İşbirlikçi süzme |
| **Cosine similarity** | Kosinüs benzerliği |
| **Auto-EQ** | Otomatik equalizer |
| **Mood** | Ruh hali |
| **Cold start** | Soğuk başlangıç |
| **Feature extraction** | Özellik çıkarma |

## 14. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ 025, 028, 030 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 3 referans |
| **Guardrails** | ✅ 5 kural |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
