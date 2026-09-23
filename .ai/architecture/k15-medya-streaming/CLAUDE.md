---
title: "CoreMusic — K15 Medya CLAUDE.md"
type: layer-guide
folder: "architecture/k15-medya-streaming"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K15 Medya — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | FLAC ana format | Kalite düşüşü |
| 2 | Bit-perfect zorunlu | Ses bozulması |
| 3 | DRM-Free | Mülkiyet ihlali |

## 2. Format Öncelikleri

| Sıra | Format | Kullanım |
|------|--------|----------|
| 1 | FLAC 24/32-bit | Ana format |
| 2 | WAV | Ham ses |
| 3 | MP3 320kbps | Uyumluluk |
| 4 | AAC 256kbps | Streaming |

## 3. FFmpeg Kuralları

```bash
# ❌ YASAK — Kalite düşüşü
ffmpeg -i input.flac -b:a 128k output.mp3

# ✅ DOĞRU — Yüksek kalite
ffmpeg -i input.flac -b:a 320k output.mp3
```

---

*K15 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
