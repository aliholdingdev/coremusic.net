---
type: technical-spec
project: Neva Engine
component: eq-presets
updated: 2026-06-03
---

# Neva Engine — EQ Presets (Genre + User + Auto)

---

## 1. Genre-Based EQ Presets (Rule-Based)

| Genre            | 60Hz | 150Hz | 400Hz | 1kHz | 2.4kHz | 6kHz | 15kHz | Curve              |
| ---------------- | ---- | ----- | ----- | ---- | ------ | ---- | ----- | ------------------ |
| **Arabesk**      | +2   | +3    | +1    | +2   | +4     | +2   | +1    | Warm vocal forward |
| **Arabesk Slow** | +3   | +2    | +2    | +1   | +3     | +1   | 0     | Deep emotional     |
| **Pop Turkish**  | +3   | +1    | 0     | +1   | +3     | +3   | +3    | Modern bright      |
| **Pop Dance**    | +5   | +2    | 0     | +1   | +2     | +3   | +4    | Bass heavy bright  |
| **Rock**         | +2   | 0     | -1    | -2   | +3     | +3   | +2    | Mid scoop          |
| **Metal**        | +2   | 0     | -2    | -3   | +4     | +4   | +3    | Aggressive         |
| **Electronic**   | +5   | +2    | +1    | 0    | +2     | +3   | +4    | Club               |
| **HipHop**       | +5   | +3    | +1    | +1   | +2     | +2   | +2    | Bass power         |
| **Jazz**         | +1   | +2    | +1    | 0    | -1     | -1   | -2    | Warm mellow        |
| **Classical**    | 0    | 0     | 0     | 0    | 0      | 0    | -1    | Natural flat       |
| **Folk**         | +1   | +2    | +1    | +1   | 0      | -1   | 0     | Natural warm       |
| **R&B**          | +4   | +2    | +1    | +1   | +1     | +2   | +2    | Smooth             |
| **Latin**        | +3   | +2    | +1    | +2   | +2     | +2   | +2    | Rhythmic           |
| **Reggae**       | +4   | +2    | +1    | +1   | 0      | +1   | +1    | Bass-line          |
| **Blues**        | +2   | +2    | +1    | +1   | 0      | -1   | -1    | Vintage warm       |
| **Ambient**      | +2   | +1    | 0     | 0    | -1     | 0    | +1    | Atmospheric        |

---

## 2. Special EQ Presets

| Preset            | 60Hz | 150Hz | 400Hz | 1kHz | 2.4kHz | 6kHz | 15kHz | Amaç            |
| ----------------- | ---- | ----- | ----- | ---- | ------ | ---- | ----- | --------------- |
| **Flat**          | 0    | 0     | 0     | 0    | 0      | 0    | 0     | Reference       |
| **Smiley Face**   | +4   | +2    | 0     | -1   | 0      | +2   | +4    | Classic V-shape |
| **Vocal Boost**   | -2   | 0     | +1    | +3   | +4     | +2   | 0     | Voice clarity   |
| **Bass Boost**    | +6   | +3    | +1    | 0    | 0      | 0    | 0     | Subwoofer       |
| **Treble Boost**  | 0    | 0     | 0     | +1   | +2     | +3   | +5    | Brilliance      |
| **Mid Scoop**     | +3   | +1    | -2    | -3   | -2     | +1   | +3    | "Disco smile"   |
| **Loudness**      | +3   | +2    | +1    | 0    | 0      | +1   | +2    | Low volume      |
| **Night Mode**    | -4   | -2    | 0     | +1   | +1     | 0    | -3    | Late night      |
| **Podcast/Voice** | -3   | -2    | 0     | +4   | +3     | 0    | -2    | Speech clarity  |

---

## 3. User EQ Presets

Kullanıcı kendi preset'lerini kaydedebilir:

```json
{
  "preset_id": "uuid-123",
  "name": "Benim Arabesk Ayarım",
  "created_by": "user_42",
  "eq_type": "31band",
  "bands": {
    "63": 2.5, "125": 3.0, "250": 2.0, "500": 1.5,
    "1k": 1.0, "2k": 3.5, "4k": 4.0, "8k": 2.0, "16k": 1.0
  },
  "fx_chain": {
    "compressor": {"enabled": true, "threshold": -20, "ratio": 3},
    "reverb": {"enabled": false}
  },
  "is_public": true,
  "tags": ["arabesk", "vocal", "warm"]
}
```

---

## 4. FX Sound Reference Presets

FX Sound (open source since 2023) referans alınarak:

| Preset | Bass | 3D Surround | Dynamic Boost | Clarity | Ambience |
|--------|------|-----------|---------------|---------|----------|
| **General** | 4 | 2 | 3 | 3 | 2 |
| **Music** | 5 | 3 | 4 | 4 | 3 |
| **Movies** | 6 | 5 | 3 | 3 | 4 |
| **Gaming** | 4 | 5 | 4 | 5 | 2 |
| **Voice** | 1 | 0 | 2 | 6 | 0 |
| **Transcription** | 0 | 0 | 1 | 8 | 0 |

---

*Neva Engine EQ Presets v1 — 2026-06-03*
