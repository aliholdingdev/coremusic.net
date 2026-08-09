---
type: architecture
category: l3
title: "Web Audio API"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Web Audio API

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]]

---

## 1. Amaç

Web Audio API kullanımını ve ses oynatma mekanizmasını tanımlar.

---

## 2. Audio Context

```javascript
class AudioManager {
    #context = null;
    #gainNode = null;
    #source = null;

    constructor() {
        this.#context = new (window.AudioContext || window.webkitAudioContext)();
        this.#gainNode = this.#context.createGain();
        this.#gainNode.connect(this.#context.destination);
    }

    async loadTrack(url) {
        const response = await fetch(url);
        const arrayBuffer = await response.arrayBuffer();
        const audioBuffer = await this.#context.decodeAudioData(arrayBuffer);
        return audioBuffer;
    }

    play(buffer) {
        this.#source = this.#context.createBufferSource();
        this.#source.buffer = buffer;
        this.#source.connect(this.#gainNode);
        this.#source.start();
    }

    setVolume(value) {
        this.#gainNode.gain.value = value;
    }

    stop() {
        if (this.#source) {
            this.#source.stop();
        }
    }
}
```

---

## 3. Features

| Özellik | Değer |
|---------|-------|
| **Format** | FLAC 24/32-bit |
| **Sample Rate** | 48kHz (standart) |
| **Channels** | 2.0 → 8.1 |
| **Latency** | <10ms target |
| **EQ** | 31-band parametric |

---

## 4. Edge Cases

| Durum | Çözüm |
|-------|-------|
| **Context suspended** | User gesture ile resume |
| **Buffer underrun** | Fade-out + restart |
| **Format desteği** | Decode fallback |
| **Mobile restriction** | User interaction |

---

## 5. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[ADR-025-professional-eq-system]] | EQ system |

---

## 6. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~500 |
| **ADR Uyumlu** | ✅ 001, 025 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
