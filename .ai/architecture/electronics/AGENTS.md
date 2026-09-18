# electronics — Agent Kuralları

## Domain Boundary

Bu klasör, CoreMusic'in elektronik donanım tasarımını kapsar:
- K16: Class AB amplifikatör
- K17: Güç kaynağı
- K18: Termal tasarım
- K19: PCB tasarımı
- K20: BOM ve üretim

## Erişim Yetkisi

| Agent | Erişim | Açıklama |
|-------|--------|----------|
| embedded-engineer | Tüm dosyalar | Donanım mühendisi |
| audio-hardware-engineer | amplifier, thermal | Amplifikatör ve soğutma |

## Kurallar

1. ADR-089 Class AB topolojisi zorunlu
2. 6S LiPo (22.2V) + LM5122 boost
3. Fischer SK53 heatsink zorunlu
4. Star ground topolojisi zorunlu

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
