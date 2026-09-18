# K03: Ses Motoru Detayı

## 6 Ses Servisi

```
┌─────────────────────────────────────────────────────────────────────┐
│                    6 SES SERVİSİ                                     │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ Audio Engine     │  │ Media Service    │  │ Download Service│    │
│  │ (C++20 JUCE)    │  │ (PHP 8.4)       │  │ (Node.js + TS) │    │
│  │                 │  │                 │  │                 │    │
│  │ • Neva Engine   │  │ • Metadata      │  │ • Deezer API    │    │
│  │ • DSP Chain     │  │ • Cover Art     │  │ • YouTube API   │    │
│  │ • Mixer         │  │ • Lyrics        │  │ • Queue Manager │    │
│  │ • Effects       │  │ • Streaming     │  │ • Cache         │    │
│  │ • Analysis      │  │ • Library       │  │ • History       │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ Network Audio    │  │ AI Service      │  │ Device Service  │    │
│  │ (C++20)         │  │ (PHP + Python)  │  │ (C++20)         │    │
│  │                 │  │                 │  │                 │    │
│  │ • DLNA          │  │ • Recommendation│  │ • Bluetooth     │    │
│  │ • AirPlay       │  │ • Analysis      │  │ • WiFi          │    │
│  │ • Chromecast    │  │ • Auto EQ       │  │ • USB           │    │
│  │ • Multi-Room    │  │ • Voice         │  │ • Sync          │    │
│  │ • WebRTC        │  │ • Generation    │  │ • Pairing       │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## Media Pipeline

```
Müzik Dosyası → Metadata Çıkarma → DB Kaydı → İndeksleme → Arama → Oynatma
     │              │                  │            │          │        │
     ▼              ▼                  ▼            ▼          ▼        ▼
  FFmpeg        ID3/FLAC          MySQL 9      FULLTEXT    Search   Audio
  Transcode     Tag Parse         INSERT       Index       API      Engine
```

## Ses Formatları

| Format | Codec | Bit Derinliği | Örnekleme |
|--------|-------|---------------|-----------|
| FLAC | Free Lossless | 16/24/32-bit | 44.1-192kHz |
| MP3 | MPEG-1 Layer 3 | 16-bit | 44.1kHz |
| AAC | Advanced Audio | 16-bit | 44.1kHz |
| OGG | Vorbis | 16-bit | 44.1kHz |
| WAV | PCM | 16/24/32-bit | 44.1-192kHz |
| DSD | Direct Stream | 1-bit | 2.8/5.6MHz |

## DSP Zinciri

```
Sinyal Girişi → EQ → Dynamics → Effects → Bass Mgmt → Mix → Çıkış
                  │      │          │          │        │
                  ▼      ▼          ▼          ▼        ▼
              31-Band  Comp/Lim   Reverb    LR4 Crossover  8.1
              Parametric Gate    Delay     80Hz           Surround
```

## İlgili Dosyalar

- [[k3-audio-engine]] — Genel ses motoru
- [[electronics/amplifier-classab-circuit]] — Class AB çıkış
- [[electronics/power-supply-classab]] — ±35V güç kaynağı

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
