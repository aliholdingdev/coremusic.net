# firmware — Agent Kuralları

## Domain Boundary

Bu klasör, CoreMusic'in gömülü yazılım altyapısını kapsar:
- Boot sırası
- RTOS entegrasyonu
- DSP firmware
- OTA güncelleme

## Erişim Yetkisi

| Agent | Erişim | Açıklama |
|-------|--------|----------|
| embedded-engineer | Tüm dosyalar | Gömülü yazılım |
| dsp-firmware-engineer | k-firmware-layer.md | DSP firmware |

## Kurallar

1. OTA güncelleme SHA-256 + RSA-2048 zorunlu
2. Boot sırası: ROM → XMOS → DSP → I2S → PCM3168A → USB
3. RTOS öncelikleri: Audio DSP (10, en yüksek)

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
