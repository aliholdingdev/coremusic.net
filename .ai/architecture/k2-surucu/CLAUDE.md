---
title: "CoreMusic — K2 Sürücü CLAUDE.md"
type: layer-guide
folder: "architecture/k2-surucu"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K2 Sürücü — CLAUDE.md

**Bu dosya K2 katmanı için özel AI talimatlarını içerir.**

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | ASIO Exclusive Lock — tek uygulama | Sürücü çökmesi |
| 2 | Audio thread blocking yasak | Ses takılması |
| 3 | Buffer underrun koruması zorunlu | Crackling |
| 4 | Sample rate mismatch önlem | Pitch shift |

## 2. Sürücü Öncelik Sırası

| Sıra | Sürücü | Platform | Kullanım |
|------|--------|----------|----------|
| 1 | ASIO | Windows | Low-latency |
| 2 | WASAPI Exclusive | Windows | Bit-perfect |
| 3 | CoreAudio | macOS | Native |
| 4 | ALSA | Linux | Kernel |
| 5 | PipeWire | Linux | Modern |
| 6 | WASAPI Shared | Windows | Fallback |

## 3. Latency Hesaplama

```
Buffer: 512 samples @ 48kHz = 10.67ms (tek yön)
Buffer: 256 samples @ 48kHz = 5.33ms (tek yön)
Buffer: 128 samples @ 48kHz = 2.67ms (tek yön)
Toplam: Çift yön = ~2x tek yön gecikme
```

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-017 | XMOS XU316 + PCM3168A DSP |

---

*K2 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
